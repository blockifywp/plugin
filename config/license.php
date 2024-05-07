<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use ArrayPress\LemonSqueezy\Updater;
use WP_REST_Server;
use function esc_html__;
use function filter_input;
use function get_option;
use function is_null;
use function register_rest_route;
use const FILTER_SANITIZE_FULL_SPECIAL_CHARS;
use const INPUT_GET;

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
			'file'        => FILE,
			'api_url'     => 'https://cloud.blockifywp.com/wp-json/lsq/v1',
			'version'     => null,
			'store_id'    => 11544,
			'product_id'  => 95716,
			'variant_id'  => 0,
			'renewal_url' => 'https://blockify.lemonsqueezy.com/',
		];

		// $updater = new Updater( ...array_values( $args ) );
	}

	return $updater;
}

// add_action( 'admin_init', __NAMESPACE__ . '\\init_updater' );
/**
 * Returns the updater instance.
 *
 * @since 0.0.2
 *
 * @return void
 */
function init_updater(): void {
	get_updater_instance();
}

// add_action( 'rest_api_init', __NAMESPACE__ . '\\register_license_rest_route' );
/**
 * Registers the license REST route.
 *
 * @since 0.0.2
 *
 * @return void
 */
function register_license_rest_route(): void {
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
	$message = esc_html__( 'Invalid license key.', 'blockify-pro' );

	if ( $success ) {
		$status  = 'active';
		$message = $updater->get_license_message();
	}

	update_option( $updater->plugin_slug . '_license_message', $message );

	return [
		'license' => $status,
		'message' => $message,
	];
}
