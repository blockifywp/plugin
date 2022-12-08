<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use Requests_Utility_CaseInsensitiveDictionary;
use WP_Screen;
use const GLOB_ONLYDIR;
use function add_action;
use function add_editor_style;
use function apply_filters;
use function basename;
use function do_action;
use function filemtime;
use function function_exists;
use function get_current_screen;
use function glob;
use function home_url;
use function trailingslashit;
use function wp_dequeue_style;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_register_script;
use function wp_register_style;

add_action( 'current_screen', NS . 'add_editor_scripts_hook', 10, 1 );
/**
 * Conditionally changes which action hook editor assets are enqueued.
 *
 * @since 0.0.19
 *
 * @param WP_Screen $screen Current screen.
 *
 * @return void
 */
function add_editor_scripts_hook( WP_Screen $screen ): void {
	$site_editor = $screen->base === 'site-editor';

	if ( ! $site_editor && function_exists( 'is_gutenberg_page' ) && ! is_gutenberg_page() ) {
		return;
	}

	if ( ! $site_editor && ! $screen->is_block_editor() ) {
		return;
	}

	add_action(
		$site_editor ? 'admin_enqueue_scripts' : 'enqueue_block_editor_assets',
		static fn() => do_action( 'blockify_editor_scripts', $screen )
	);
}

add_action( 'blockify_editor_scripts', NS . 'enqueue_editor_scripts' );
/**
 * Enqueues editor assets.
 *
 * @since 0.0.14
 *
 * @return void
 */
function enqueue_editor_scripts(): void {
	$asset = require DIR . 'assets/js/editor.asset.php';
	$deps  = $asset['dependencies'];

	wp_register_script(
		'blockify-editor',
		get_uri() . 'assets/js/editor.js',
		$deps,
		filemtime( DIR . 'assets/js/editor.js' ),
		true
	);

	wp_enqueue_script( SLUG . '-editor' );

	wp_localize_script(
		'blockify-editor',
		SLUG,
		get_editor_data()
	);
}

/**
 * Returns filtered editor data.
 *
 * @since 0.9.10
 *
 * @return mixed|void
 */
function get_editor_data() {
	$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	return apply_filters(
		'blockify_editor_data',
		[
			'url'        => get_uri(),
			'siteUrl'    => trailingslashit(
				home_url()
			),
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'adminUrl'   => trailingslashit( admin_url() ),
			'nonce'      => wp_create_nonce( SLUG ),
			'icon'       => get_icon( 'social', SLUG ),
			'isPlugin'   => is_plugin(),
			'siteEditor' => $current_screen && $current_screen->base === 'site-editor',
		]
	);
}

add_action( 'blockify_editor_scripts', NS . 'enqueue_editor_only_styles' );
/**
 * Enqueues editor assets.
 *
 * @since 0.3.3
 *
 * @return void
 */
function enqueue_editor_only_styles(): void {
	wp_dequeue_style( 'wp-block-library-theme' );

	wp_register_style(
		'blockify-editor',
		get_uri() . 'assets/css/editor.css',
		[],
		filemtime( DIR . 'assets/css/editor.css' )
	);

	wp_enqueue_style( 'blockify-editor' );
}

add_action( 'admin_init', NS . 'add_editor_stylesheets' );
/**
 * Adds editor only styles.
 *
 * @since 0.9.10
 *
 * @return void
 */
function add_editor_stylesheets() {
	$dirs = glob( DIR . 'assets/css/*', GLOB_ONLYDIR );
	$path = get_asset_path();

	foreach ( $dirs as $dir ) {

		if ( basename( $dir ) === 'abstracts' ) {
			continue;
		}

		$files = glob( $dir . '/*.css' );

		foreach ( $files as $file ) {
			$stylesheet = "{$path}assets/css/" . basename( $dir ) . DS . basename( $file );

			add_editor_style( $stylesheet );
		}
	}

	add_editor_style( 'https://blockify-dynamic-styles' );
}

add_filter( 'pre_http_request', NS . 'generate_dynamic_styles', 10, 3 );
/**
 * Generates dynamic editor styles.
 *
 * @since 0.9.23
 *
 * @param array|bool $response    HTTP response.
 * @param array      $parsed_args Response args.
 * @param string     $url         Response URL.
 *
 * @return array|bool
 */
function generate_dynamic_styles( $response, array $parsed_args, string $url ) {
	if ( $url === 'https://blockify-dynamic-styles' ) {
		$response = [
			'body'     => get_inline_styles( '', true ),
			'headers'  => new Requests_Utility_CaseInsensitiveDictionary(),
			'response' => [
				'code'    => 200,
				'message' => 'OK',
			],
			'cookies'  => [],
			'filename' => null,
		];
	}

	return $response;
}
