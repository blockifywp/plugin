<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use Blockify\Dom\CSS;
use Blockify\Utilities\Debug;
use Exception;
use JShrink\Minifier;
use function add_action;
use function add_filter;
use function apply_filters;
use function array_diff;
use function class_exists;
use function get_option;
use function remove_action;
use function remove_filter;
use function wp_enqueue_script;

add_filter( 'blockify_inline_css', __NAMESPACE__ . '\\add_custom_css' );
/**
 * Adds custom CSS.
 *
 * @since 1.0.0
 *
 * @param string $css CSS.
 *
 * @return string
 */
function add_custom_css( string $css ): string {
	$options    = get_option( 'blockify', [] );
	$custom_css = $options['additionalCss'] ?? '';

	if ( $custom_css ) {
		$css .= CSS::minify( $custom_css );
	}

	return $css;
}

add_filter( 'blockify_inline_js', __NAMESPACE__ . '\\add_custom_js' );
/**
 * Adds custom JS.
 *
 * @since 1.0.0
 *
 * @param string $js JS.
 *
 * @return string
 */
function add_custom_js( string $js ): string {
	$options   = get_option( 'blockify', [] );
	$custom_js = $options['additionalJs'] ?? '';

	if ( $custom_js ) {
		try {
			$js .= Minifier::minify( $custom_js );
		} catch ( Exception $e ) {
			$js .= $custom_js;
		}
	}

	return $js;
}

add_filter( 'blockify_format_inline_js', __NAMESPACE__ . '\\minify_js' );
/**
 * Minify JS.
 *
 * @since 1.0.0
 *
 * @param string $js JS.
 *
 * @return string
 */
function minify_js( string $js ): string {
	if ( ! class_exists( 'JShrink\Minifier' ) ) {
		return $js;
	}

	try {
		$js = Minifier::minify( $js );
	} catch ( Exception $e ) {
		if ( Debug::is_enabled() ) {
			Debug::console_log( $e->getMessage() );
		}
	}

	return $js;
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\add_google_analytics_scripts' );
/**
 * Adds Google Analytics frontend script.
 *
 * @since 1.0.0
 *
 * @return void
 */
function add_google_analytics_scripts() {
	$id = get_option( 'blockify' )['googleAnalytics'] ?? '';

	if ( ! $id ) {
		return;
	}

	wp_enqueue_script(
		'blockify-google-analytics',
		'https://www.googletagmanager.com/gtag/js?id=' . $id,
		[],
		'1.0.0',
		false
	);

	$inline = <<<JS
window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','$id');
JS;

	wp_add_inline_script(
		'blockify-google-analytics',
		$inline
	);
}

add_action( 'admin_init', __NAMESPACE__ . '\\remove_emoji_scripts' );
add_action( 'after_setup_theme', __NAMESPACE__ . '\\remove_emoji_scripts' );
/**
 * Removes unused emoji scripts.
 *
 * @since 0.2.0
 *
 * @return void
 */
function remove_emoji_scripts(): void {
	$options = get_option( 'blockify' );
	$enabled = ( $options['removeEmojiScripts'] ?? true ) === true;

	if ( ! $enabled ) {
		return;
	}

	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );
	add_filter(
		'tiny_mce_plugins',
		static fn( array $plugins = [] ) => array_diff(
			$plugins,
			[ 'wpemoji' ]
		)
	);
	add_filter(
		'wp_resource_hints',
		function ( array $urls, string $relation_type ): array {
			if ( $relation_type === 'dns-prefetch' ) {
				$urls = array_diff( $urls, [
					apply_filters(
						'emoji_svg_url',
						'https://s.w.org/images/core/emoji/2/svg/'
					),
				] );
			}

			return $urls;
		},
		10,
		2
	);
}
