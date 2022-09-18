<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use function add_filter;
use function array_map;
use function file_exists;
use function add_action;
use function array_flip;
use function basename;
use function file_get_contents;
use function glob;
use function is_a;
use function is_admin;
use function is_admin_bar_showing;
use function str_replace;
use function wp_add_inline_style;
use function wp_dequeue_style;
use function wp_get_global_styles;
use function wp_get_global_settings;

add_action( 'blockify_editor_scripts', __NAMESPACE__ . '\\add_dynamic_custom_properties' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\add_dynamic_custom_properties' );
/**
 * Adds custom properties. Returns CSS for wp-org preview generation.
 *
 * @since 0.0.19
 *
 * @return string
 */
function add_dynamic_custom_properties(): string {
	$settings             = wp_get_global_settings();
	$global_styles        = wp_get_global_styles();
	$element              = is_admin() ? '.editor-styles-wrapper' : 'body';
	$content_size         = $settings['layout']['contentSize'] ?? '800px';
	$wide_size            = $settings['layout']['wideSize'] ?? '1200px';
	$layout_unit          = is_admin() ? '%' : 'vw';
	$border_width         = $settings['custom']['border']['width'] ?? '1px';
	$border_style         = $settings['custom']['border']['style'] ?? 'solid';
	$border_color         = $settings['custom']['border']['color'] ?? '#ddd';
	$body_background      = $global_styles['color']['background'] ?? null;
	$body_color           = $global_styles['color']['text'] ?? null;
	$button               = $global_styles['blocks']['core/button'] ?? [];
	$button_text          = $button['color']['text'] ?? null;
	$button_background    = $button['color']['background'] ?? null;
	$button_border_radius = $button['border']['radius'] ?? null;
	$button_border_width  = $button['border']['width'] ?? null;
	$button_font_size     = $button['typography']['fontSize'] ?? null;
	$button_font_weight   = $button['typography']['fontWeight'] ?? null;
	$button_line_height   = $button['typography']['lineHeight'] ?? null;
	$button_padding       = $button['spacing']['padding'] ?? null;

	$all = [
		// var(--wp--style--block-gap) doesn't work here.
		'layout--content-size'   => "min(calc(100{$layout_unit} - 40px),{$content_size})",
		'layout--wide-size'      => "min(calc(100{$layout_unit} - 40px),{$wide_size})",
		'border'                 => "$border_width $border_style $border_color",
		'body--background'       => $body_background,
		'body--color'            => $body_color,

		// Gutenberg .wp-element-button issue workaround. Also used by input.
		'button--background'     => $button_background,
		'button--color'          => $button_text,
		'button--padding-top'    => $button_padding['top'] ?? null,
		'button--padding-right'  => $button_padding['right'] ?? null,
		'button--padding-bottom' => $button_padding['bottom'] ?? null,
		'button--padding-left'   => $button_padding['left'] ?? null,
		'button--border-radius'  => $button_border_radius,
		'button--border-width'   => $button_border_width,
		'button--font-size'      => $button_font_size,
		'button--font-weight'    => $button_font_weight,
		'button--line-height'    => $button_line_height,
	];

	$all = array_combine( array_map(
		fn( $key ) => '--wp--custom--' . $key,
		array_keys( $all )
	), $all );

	$css = $element . '{' . css_array_to_string( $all ) . '}';

	wp_add_inline_style(
		is_admin() ? 'blockify-editor' : 'global-styles',
		$css
	);

	return $css;
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_block_styles' );
/**
 * Enqueues front end scripts.
 *
 * @since 0.0.2
 *
 * @return void
 */
function enqueue_block_styles(): void {
	global $wp_styles;

	wp_dequeue_style( 'wp-block-library-theme' );

	if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
		return;
	}

	$handles = array_flip( $wp_styles->queue );

	foreach ( $wp_styles->registered as $handle => $style ) {
		if ( ! isset( $handles[ $handle ] ) ) {
			continue;
		}

		$slug = str_replace( 'wp-block-', '', $handle );
		$file = get_framework_dir( 'build/css/blocks/' . $slug . '.css' );

		if ( file_exists( $file ) ) {
			wp_add_inline_style(
				$handle,
				file_get_contents( $file )
			);
		}
	}
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\add_conditional_styles' );
/**
 * Adds split styles.
 *
 * @since 0.0.27
 *
 * @return void
 */
function add_conditional_styles(): void {
	$styles = '';

	$stylesheets = [
		...glob( get_framework_dir( 'build/css/elements/*.css' ) ),
		...glob( get_framework_dir( 'build/css/components/*.css' ) ),
		...glob( get_framework_dir( 'build/css/blocks/*.css' ) ),
		...glob( get_framework_dir( 'build/css/extensions/*.css' ) ),
	];

	// TODO: Condition checks.
	$conditions = [
		'admin-bar' => is_admin_bar_showing(),
	];

	foreach ( $stylesheets as $stylesheet ) {
		if ( $conditions[ basename( $stylesheet, '.css' ) ] ?? true ) {
			$styles .= trim( file_get_contents( $stylesheet ) );
		}
	}

	wp_add_inline_style(
		is_admin() ? 'blockify-editor' : 'global-styles',
		$styles
	);
}

add_filter( 'body_class', __NAMESPACE__ . '\\add_body_classes' );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @param array $classes
 *
 * @return array
 */
function add_body_classes( array $classes ): array {
	$classes[] = 'style-variation-' . get_style_variation();

	return $classes;
}

add_action( 'init', __NAMESPACE__ . '\\responsive_embeds' );
/**
 * Adds responsive embeds.
 *
 * @since 0.0.2
 *
 * @return void
 */
function responsive_embeds(): void {
	add_theme_support( 'responsive-embeds' );
}
