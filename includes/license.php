<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use ArrayPress\LemonSqueezy\Updater;
use WP_REST_Server;
use function add_action;
use function add_filter;
use function chmod;
use function defined;
use function esc_html__;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function filemtime;
use function filter_input;
use function function_exists;
use function get_option;
use function get_transient;
use function is_dir;
use function is_null;
use function is_writable;
use function json_decode;
use function mkdir;
use function register_rest_route;
use function set_transient;
use function str_contains;
use function time;
use function trailingslashit;
use function unlink;
use function unzip_file;
use function update_option;
use function WP_Filesystem;
use function wp_json_file_decode;
use function wp_remote_get;
use function wp_remote_retrieve_body;
use function wp_send_json_success;
use const ABSPATH;
use const BLOCKIFY_API_URL;
use const DAY_IN_SECONDS;
use const FILTER_SANITIZE_FULL_SPECIAL_CHARS;
use const HOUR_IN_SECONDS;
use const INPUT_GET;

/**
 * Returns the API URL.
 *
 * @since 0.0.2
 *
 * @return string
 */
function get_api_url(): string {
	$url = defined( 'BLOCKIFY_API_URL' ) ? BLOCKIFY_API_URL : 'https://cloud.blockifywp.com/';

	return trailingslashit( $url );
}

/**
 * Returns the updater instance.
 *
 * @since 0.0.2
 *
 * @param bool $new Whether to get a new instance.
 *
 * @return ?Updater
 */
function get_updater_instance( bool $new = false ): ?Updater {
	static $updater;

	if ( is_null( $updater ) || $new ) {
		$args = [
			'file'       => FILE,
			'api_url'    => get_api_url() . 'wp-json/lsq/v1',
			'version'    => null,
			'store_id'   => 11544,
			'product_id' => 95716,
			'variant_id' => 0,
		];

		$updater = new Updater( ...array_values( $args ) );
	}

	return $updater;
}

add_filter( 'blockify_editor_data', __NAMESPACE__ . '\\add_pro_license_data' );
/**
 * Adds Pro license data.
 *
 * @since 0.0.2
 *
 * @param array $data Editor data.
 *
 * @return array
 */
function add_pro_license_data( array $data ): array {
	$data['pro']['licenseActive'] = is_license_active();

	return $data;
}

add_action( 'rest_api_init', __NAMESPACE__ . '\\register_license_rest_routes' );
/**
 * Registers the license REST route.
 *
 * @since 0.0.2
 *
 * @return void
 */
function register_license_rest_routes(): void {
	register_rest_route(
		'blockify/v1',
		'/license',
		[
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => __NAMESPACE__ . '\\validate_license',
			'permission_callback' => static fn(): bool => current_user_can( 'manage_options' ),
			'args'                => [
				'key' => [
					'required' => true,
				],
			],
		]
	);
}

/**
 * Validates the license key.
 *
 * @since 0.0.2
 *
 * @return array
 */
function validate_license(): array {
	$key      = filter_input( INPUT_GET, 'key', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$updater  = get_updater_instance( true );
	$existing = get_option( $updater->license_name_key, null );
	$instance = $updater->get_instance_id();

	$updater->cache_allowed = false;

	$updater->set_license_key( $key );

	if ( $existing && $instance ) {
		$updater->api_request->deactivate_key( $existing, $instance );
	}

	$request = $updater->api_request->validate_key( $key, null, true );
	$success = isset( $request->data->license_key->status ) && $request->data->license_key->status === 'active';
	$status  = 'invalid';
	$message = esc_html__( 'Invalid license key.', 'blockify-plugin' );

	if ( $success ) {
		$status  = 'active';
		$message = $updater->get_license_message() . esc_html__( ' Cloud content is up to date.', 'blockify-plugin' );
	}

	update_option( 'blockify_license_message', $message );

	if ( content_needs_updating() ) {
		$fetched = fetch_cloud_content( $key );
		$success = $fetched['success'] ?? false;
		$message = $fetched['message'] ?? esc_html__( 'An error occurred while updating cloud content.', 'blockify-plugin' );

		if ( $success ) {
			$message = esc_html__( 'License activated. ', 'blockify-plugin' ) . $message;
		}
	}

	$data = [
		'license' => $status,
		'message' => $message,
	];

	wp_send_json_success( $data );

	return $data;
}

add_action( 'admin_init', __NAMESPACE__ . '\\maybe_sync_cloud_content' );
/**
 * Maybe sync cloud content on admin init.
 *
 * @return void
 */
function maybe_sync_cloud_content(): void {
	$option = get_option( 'blockify_license_key', null );

	if ( content_needs_updating() && $option ) {
		fetch_cloud_content( $option );
	}
}

/**
 * Fetches cloud content.
 *
 * @param ?string $key License key.
 *
 * @return array
 */
function fetch_cloud_content( ?string $key = null ): array {
	$key = $key ?? filter_input( INPUT_GET, 'key', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	if ( ! $key ) {
		return [
			'success' => false,
			'message' => esc_html__( 'No license key provided.', 'blockify-plugin' ),
		];
	}

	$cache_dir     = get_cache_dir();
	$transient_key = 'blockify_cloud';

	if ( ! content_needs_updating() ) {
		return [
			'success' => true,
			'message' => '',
		];
	}

	$license_data = json_decode(
		get_option( 'blockify_license_data', [] ),
		true
	);

	if (
		! isset( $license_data['data']['valid'] ) ||
		! isset( $license_data['data']['license_key']['status'] ) ||
		! isset( $license_data['data']['license_key']['key'] ) ||
		$license_data['data']['valid'] !== true ||
		$license_data['data']['license_key']['status'] !== 'active' ||
		$license_data['data']['license_key']['key'] !== $key
	) {
		return [
			'success' => false,
			'message' => esc_html__( 'Invalid license key. Please re-enter your key and confirm it is correct.', 'blockify-plugin' ),
		];
	}

	$remote = wp_remote_get(
		get_api_url() . "wp-json/blockify/v1/cloud?license_key={$key}",
		[
			'timeout' => 10,
		]
	);

	$body = wp_remote_retrieve_body( $remote );
	$json = [];

	if ( ! empty( $body ) ) {
		$json = json_decode( $body, true );
	}

	if ( ! $json ) {
		return [
			'success' => false,
			'message' => esc_html__( 'No data returned from cloud request. Please contact Blockify support.', 'blockify-plugin' ),
		];
	}

	if ( ( $json['code'] ?? '' ) === 'no_s3_credentials' ) {
		return [
			'success' => false,
			'message' => esc_html__( 'No S3 credentials found. Please contact Blockify support.', 'blockify-plugin' ),
		];
	}

	if ( ! function_exists( 'WP_Filesystem' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}

	WP_Filesystem();

	$permissions = 0777;

	// Delete existing cache.
	if ( is_dir( $cache_dir ) ) {
		unlink( $cache_dir );
	}

	// Create cache directory.
	if ( ! is_dir( $cache_dir ) ) {
		mkdir( $cache_dir, $permissions, true );
	}

	// Attempt to set permissions.
	if ( ! is_writable( $cache_dir ) ) {
		chmod( $cache_dir, $permissions );
	}

	// Failed to set permissions.
	if ( ! is_writable( $cache_dir ) ) {
		return [
			'success' => false,
			'message' => esc_html__( 'Cache directory is not writable (wp-content/cache). Please contact Blockify or your hosting provider for support assistance.', 'blockify-plugin' ),
		];
	}

	$objects = [];
	$time    = time();

	foreach ( $json as $url_data ) {
		$object_key = $url_data['key'] ?? '';
		$signed_url = $url_data['url'] ?? '';

		if ( ! $object_key || ! $signed_url ) {
			continue;
		}

		if ( ! str_contains( $object_key, '.zip' ) ) {
			continue;
		}

		$file_path = $cache_dir . $object_key;

		if ( file_exists( $file_path ) ) {
			$file_time = filemtime( $file_path ) ?? 0;

			if ( $file_time > $time - HOUR_IN_SECONDS ) {
				continue;
			}
		}

		$file = file_put_contents(
			$file_path,
			file_get_contents( $signed_url ),
		);

		if ( ! $file ) {
			continue;
		}

		unzip_file( $file_path, $cache_dir );
		unlink( $file_path );

		$objects[] = $object_key;
	}

	if ( empty( $objects ) ) {
		return [
			'success' => false,
			'message' => esc_html__( 'No downloadable objects found.', 'blockify-plugin' ),
		];
	}

	set_transient( $transient_key, true, DAY_IN_SECONDS );

	return [
		'success' => true,
		'message' => esc_html__( ' Please reload the page to access premium features.', 'blockify-plugin' ),
	];
}

/**
 * Checks if cloud content needs updating.
 *
 * @since 1.5.0
 *
 * @return bool
 */
function content_needs_updating(): bool {
	$needs_update       = false;
	$transient_key      = 'blockify_cloud';
	$local_version_file = get_cache_dir() . 'version.json';
	$local_version      = null;

	if ( ! file_exists( $local_version_file ) ) {
		return true;
	}

	$local_data = wp_json_file_decode( $local_version_file, [
		'associative' => true,
	] );

	if ( $local_data['version'] ?? '' ) {
		$local_version = $local_data['version'];
	}

	// Local file not formatted correctly, needs update.
	if ( ! $local_version ) {
		return true;
	}

	// Transient set, no need to update.
	if ( get_transient( $transient_key ) ) {
		return false;
	}

	$remote_version = null;
	$request        = wp_remote_get( get_api_url() . 'wp-json/blockify/v1/cloud-version' );
	$response       = wp_remote_retrieve_body( $request );

	if ( $response ) {
		$remote_data    = json_decode( $response, true );
		$remote_version = $remote_data['version'] ?? null;
	}

	if ( $remote_version ) {
		$remote_timestamp = strtotime( $remote_version );
		$local_timestamp  = strtotime( $local_version );

		$needs_update = $remote_timestamp > $local_timestamp;
	}

	set_transient(
		$transient_key,
		$needs_update,
		DAY_IN_SECONDS
	);

	return $needs_update;
}
