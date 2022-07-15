<?php

declare( strict_types=1 );

namespace Blockify;

use function add_action;
use function add_editor_style;
use function apply_filters;
use function array_flip;
use function basename;
use function end;
use function explode;
use function file_exists;
use function file_get_contents;
use function function_exists;
use function glob;
use function implode;
use function in_array;
use function is_a;
use function sprintf;
use function str_contains;
use function str_replace;
use function ucwords;
use function wp_add_inline_style;
use function wp_enqueue_style;
use function wp_get_global_styles;
use function wp_get_global_settings;
use const DIRECTORY_SEPARATOR;

add_action( 'enqueue_block_editor_assets', NS . 'enqueue_editor_scripts_styles' );
/**
 * Enqueues editor assets.
 *
 * @since 0.0.2
 *
 * @return void
 */
function enqueue_editor_scripts_styles(): void {
	wp_dequeue_style( 'wp-block-library-theme' );

	enqueue_asset( 'index.js' );
	enqueue_asset( 'editor.css' );
	enqueue_asset( 'script.js' );
}

add_action( 'after_setup_theme', NS . 'add_editor_styles' );
/**
 * Adds editor styles.
 *
 * @since 0.0.2
 *
 * @return void
 */
function add_editor_styles(): void {
	$plugin_dir = '../../plugins/' . basename( DIR ) . DIRECTORY_SEPARATOR;

	add_editor_style( $plugin_dir . 'build/style.css' );

	foreach ( glob( DIR . 'build/styles/*.css' ) as $core_block_style ) {
		add_editor_style( 'build/styles/' . basename( $core_block_style ) );
	}

	add_editor_style( 'style.css' );
}

add_action( 'wp_enqueue_scripts', NS . 'enqueue_scripts_styles' );
/**
 * Enqueues front end scripts.
 *
 * @since 0.0.2
 *
 * @return void
 */
function enqueue_scripts_styles(): void {
	global $wp_styles;

	wp_dequeue_style( 'wp-block-library-theme' );
	enqueue_asset( 'style.css' );
	enqueue_asset( 'script.js' );

	// Block styles.
	if ( is_a( $wp_styles, 'WP_Styles' ) ) {
		foreach ( $wp_styles->registered as $handle => $style ) {
			if ( isset( array_flip( $wp_styles->queue )[ $handle ] ) ) {
				$slug = str_replace( 'wp-block-', '', $handle );
				$file = DIR . 'build/styles/' . $slug . '.css';

				if ( file_exists( $file ) ) {
					wp_add_inline_style(
						$handle,
						file_get_contents( $file )
					);
				}
			}
		}
	}
}

add_action( 'enqueue_block_editor_assets', NS . 'enqueue_google_fonts' );
add_action( 'wp_enqueue_scripts', NS . 'enqueue_google_fonts' );
/**
 * Enqueues google fonts.
 *
 * @since 0.0.2
 *
 * @return void
 */
function enqueue_google_fonts(): void {
	if ( ! function_exists( 'wptt_get_webfont_url' ) ) {
		return;
	}

	$script        = require DIR . 'build/script.asset.php';
	$global_styles = wp_get_global_styles();

	if ( isset( $global_styles['typography']['fontFamily'] ) ) {
		$setting = $global_styles['typography']['fontFamily'];

		if ( str_contains( $setting, 'var(--' ) ) {
			$explode = explode( '--', str_replace( ')', '', $setting ) );
		} else {
			$explode = explode( '|', $setting );
		}

		$slug = end( $explode );

		/**
		 * Allows optimization of font weight resources.
		 *
		 * @var string $slug
		 */
		$font_weights = apply_filters( 'blockify_font_weights', [
			100,
			200,
			300,
			400,
			500,
			600,
			700,
			800,
			900,
		], $slug );

		if ( ! in_array( $slug, [ 'sans-serif', 'serif', 'monospace' ] ) ) {
			$name = ucwords( str_replace( '-', '+', $slug ) );

			wp_enqueue_style(
				'blockify-' . $slug,
				wptt_get_webfont_url( sprintf(
					'https://fonts.googleapis.com/css2?family=%s:wght@%s&display=swap',
					$name,
					implode( ';', $font_weights )
				) ),
				[ SLUG ],
				$script['version']
			);
		}
	}
}

add_action( 'admin_enqueue_scripts', NS . 'admin_scripts_styles' );
/**
 * Conditionally enqueues admin scripts and styles.
 *
 * @since 0.0.2
 *
 * @return void
 */
function admin_scripts_styles() {
	$current_screen = get_current_screen();
	$conditions     = isset( $current_screen->post_type ) && 'block_pattern' === $current_screen->post_type;

	if ( $conditions ) {
		enqueue_asset( 'index.js' );
		enqueue_asset( 'editor.css' );
	}
}

add_filter( 'blockify_index_data', NS . 'add_editor_data' );
/**
 * Adds plugin data for editor scripts.
 *
 * @since 0.0.2
 *
 * @param array $data
 *
 * @return array
 */
function add_editor_data( array $data ): array {
	$data['ajaxUrl'] = admin_url( 'admin-ajax.php' );
	$data['nonce']   = wp_create_nonce( 'blockify' );

	return $data;
}

add_filter( 'blockify_style_inline', NS . 'add_default_custom_properties', 99 );
/**
 * Adds custom properties.
 *
 * @since 0.0.2
 *
 * @param string $css
 *
 * @return void
 */
function add_default_custom_properties( string $css ): string {
	$settings = wp_get_global_settings();

	$css .= ':root{' . css_rules_to_string( [
			'--wp--custom--font-stack--sans-serif' => '-apple-system, BlinkMacSystemFont, avenir next, avenir, segoe ui, helvetica neue, helvetica, Cantarell, Ubuntu, roboto, noto, arial, sans-serif',
			'--wp--custom--font-stack--serif'      => 'Iowan Old Style, Apple Garamond, Baskerville, Times New Roman, Droid Serif, Times, Source Serif Pro, serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol',
			'--wp--custom--font-stack--monospace'  => 'Menlo, Consolas, Monaco, Liberation Mono, Lucida Console, monospace',
		] ) . '}';

	$css .= 'body{' . css_rules_to_string( [
			'--wp--custom--layout--content-size' => $settings['layout']['contentSize'] ?? '768px',
			'--wp--custom--layout--wide-size'    => $settings['layout']['wideSize'] ?? '1280px',
		] ) . '}';

	return $css;
}
