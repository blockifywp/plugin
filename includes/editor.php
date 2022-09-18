<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use WP_Screen;
use const DIRECTORY_SEPARATOR;
use function add_action;
use function do_action;
use function file_exists;
use function filemtime;
use function function_exists;
use function get_option;
use function get_site_url;
use function trailingslashit;
use function wp_enqueue_script;
use function wp_register_script;
use function add_editor_style;
use function basename;
use function dirname;
use function glob;

add_action( 'current_screen', __NAMESPACE__ . '\\maybe_load_editor_assets' );
/**
 * Conditionally changes which action hook editor assets are enqueued.
 *
 * @since 0.0.19
 *
 * @param WP_Screen $screen
 *
 * @return void
 */
function maybe_load_editor_assets( WP_Screen $screen ): void {
	$site_editor = $screen->base === 'appearance_page_gutenberg-edit-site' || $screen->base === 'site-editor';
	$hook_name   = $site_editor ? 'admin_enqueue_scripts' : 'enqueue_block_editor_assets';

	add_action( $hook_name, fn() => do_action( 'blockify_editor_scripts' ) );
}

add_action( 'blockify_editor_scripts', __NAMESPACE__ . '\\enqueue_editor_scripts' );
/**
 * Enqueues editor assets.
 *
 * @since 0.0.14
 *
 * @return void
 */
function enqueue_editor_scripts(): void {
	$asset  = get_framework_dir( 'build/js/editor.asset.php' );
	$script = get_framework_dir( 'build/js/editor.js' );

	if ( file_exists( $asset ) ) {
		$asset = require $asset;
	}

	if ( ! file_exists( $script ) ) {
		return;
	}

	$deps = $asset['dependencies'] ?? [];
	$slug = 'blockify';

	wp_register_script(
		$slug,
		get_framework_url( 'build/js/editor.js' ),
		$deps,
		filemtime( $script ),
		true
	);

	wp_enqueue_script( $slug );

	$config = apply_filters( 'blockify', [
		'url'                => get_framework_url(),
		'siteUrl'            => trailingslashit( get_site_url() ),
		'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
		'nonce'              => wp_create_nonce( $slug ),
		'icon'               => get_default_icon(),
		'darkMode'           => ( get_option( $slug )['darkMode'] ?? false ) === 'true',
		'removeEmojiScripts' => ( get_option( $slug )['removeEmojiScripts'] ?? null ) === 'true',
		'excerptLength'      => get_option( $slug )['excerptLength'] ?? 33,
		'pluginActive'       => function_exists( 'Blockify\Plugin\setup' ),
	] );

	wp_localize_script(
		$slug,
		$slug,
		$config,
	);
}

add_action( 'blockify_editor_scripts', __NAMESPACE__ . '\\enqueue_editor_only_styles' );
/**
 * Enqueues editor assets.
 *
 * @since 0.3.3
 *
 * @return void
 */
function enqueue_editor_only_styles(): void {
	wp_enqueue_style(
		'blockify-editor',
		get_framework_url( 'build/css/editor.css' ),
		[],
		filemtime( get_framework_dir( 'build/css/editor.css' ) )
	);

	wp_enqueue_style(
		'blockify-plugin-editor',
		get_framework_url( 'build/css/plugins/blockify-editor.css' ),
		[],
		filemtime( get_framework_dir( 'build/css/plugins/blockify-editor.css' ) )
	);

	$files = [
		...glob( get_framework_dir( 'build/css/plugins/*.css' ) ),
	];

	foreach ( $files as $file ) {
		wp_enqueue_style(
			'blockify-plugin-' . basename( $file, '.css' ),
			get_framework_url( 'build/css/plugins/' . basename( $file ) ),
			[],
			[],
			filemtime( $file )
		);
	}
}

add_action( 'init', __NAMESPACE__ . '\\add_editor_styles' );
/**
 * Always load all styles in editor.
 *
 * @since 0.0.2
 *
 * @return void
 */
function add_editor_styles(): void {
	$files = [
		// Load all block CSS when in editor.
		...glob( get_framework_dir( 'build/css/blocks/*.css' ) ),
		...glob( get_framework_dir( 'build/css/elements/*.css' ) ),
		...glob( get_framework_dir( 'build/css/components/*.css' ) ),
		...glob( get_framework_dir( 'build/css/extensions/*.css' ) ),
		...glob( get_framework_dir( 'build/css/plugins/*.css' ) ),
	];

	foreach ( $files as $file ) {
		add_editor_style( 'vendor/blockify/plugin/build/css/' . basename( dirname( $file ) ) . DIRECTORY_SEPARATOR . basename( $file ) );
	}
}
