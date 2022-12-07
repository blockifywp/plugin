<?php

declare( strict_types=1 );

namespace Blockify\PatternEditor;

use function __;
use function add_action;
use function add_filter;
use function add_theme_page;
use function esc_url_raw;
use function file_exists;
use function filemtime;
use function get_current_user_id;
use function get_permalink;
use function get_stylesheet_directory;
use function register_meta;
use function rest_url;
use function wp_create_nonce;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_localize_script;

add_filter( 'admin_menu', NS . 'patterns_link' );
/**
 * Adds menu link for block pattern editor.
 *
 * @since 0.0.1
 *
 * @return void
 */
function patterns_link(): void {
	add_theme_page(
		__( 'Patterns', 'blockify' ),
		__( 'Patterns', 'blockify' ),
		'edit_theme_options',
		'edit.php?post_type=block_pattern',
		null,
		99,
	);
}

add_filter( 'manage_block_pattern_posts_columns', NS . 'set_custom_edit_book_columns' );
/**
 * Adds preview column to patterns list screen.
 *
 * @since 1.0.0
 *
 * @param $columns
 *
 * @return mixed
 */
function set_custom_edit_book_columns( $columns ) {
	$columns['preview'] = __( 'Preview', 'blockify' );

	return $columns;
}

add_action( 'admin_enqueue_scripts', NS . 'enqueue_pattern_admin' );
/**
 * Enqueues editor pattern styles.
 *
 * @since 0.0.1
 *
 * @return void
 */
function enqueue_pattern_admin(): void {
	$current_screen = get_current_screen();

	if ( 'block_pattern' !== ( $current_screen->post_type ?? '' ) ) {
		return;
	}

	if ( 'edit' !== $current_screen->base ) {
		return;
	}

	$asset_path = DIR . 'build/index.asset.php';
	$asset      = file_exists( $asset_path ) ? require $asset_path : [
		'dependencies' => [],
		'version'      => filemtime( DIR ),
	];

	wp_enqueue_style(
		'pattern-editor',
		plugin_dir_url( FILE ) . 'build/index.css',
		[],
		$asset['version'],
	);

	wp_enqueue_script(
		'pattern-editor',
		plugin_dir_url( FILE ) . 'build/index.js',
		[
			...$asset['dependencies'],
			'wp-block-editor',
		],
		$asset['version'],
		true
	);

	wp_localize_script(
		'pattern-editor',
		'blockifyPatternEditor',
		[
			'nonce'         => wp_create_nonce( 'wp_rest' ),
			'restUrl'       => esc_url_raw( rest_url() ),
			'adminUrl'      => esc_url_raw( admin_url() ),
			'currentUser'   => get_current_user_id() ?? false,
			'stylesheet'    => get_stylesheet(),
			'stylesheetDir' => get_stylesheet_directory(),
			'isChildTheme'  => is_child_theme(),
		]
	);
}

add_action( 'manage_block_pattern_posts_custom_column', NS . 'pattern_preview_column', 10, 2 );
/**
 * Adds pattern iframe preview to admin columns.
 *
 * @since 0.0.1
 *
 * @param string $column
 * @param int    $post_id
 *
 * @return void
 */
function pattern_preview_column( string $column, int $post_id ): void {
	$url = get_permalink( $post_id );

	$show_patterns = get_user_option( 'blockify_show_patterns', get_current_user_id() );

	if ( ! $show_patterns ) {

	}

	switch ( $column ) {
		case 'preview' :
			echo '<div class="pattern-preview"><iframe loading="lazy" scrolling="no" src=\'' . $url . '\' seamless></iframe></div>';
			break;
	}
}

add_action( 'rest_api_init', NS . 'register_pattern_user_meta' );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function register_pattern_user_meta(): void {
	register_meta(
		'user',
		'blockify_show_patterns',
		[
			'description'  => 'Blockify Show Patterns',
			'type'         => 'string',
			'show_in_rest' => true,
			'single'       => true,
		]
	);
}

add_filter( 'admin_body_class', NS . 'add_show_patterns_body_class' );
/**
 * Conditionally  add show patterns class by default.
 *
 * @since 1.0.0
 *
 * @param string $classes
 *
 * @return string
 */
function add_show_patterns_body_class( string $classes ): string {
	$show_patterns = get_user_option( 'blockify_show_patterns', get_current_user_id() );

	if ( $show_patterns ) {
		$classes .= ' show-patterns';
	}

	return $classes;
}
