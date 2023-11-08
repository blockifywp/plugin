<?php

declare( strict_types=1 );

namespace Blockify\Theme;

use function add_filter;
use function array_diff;
use function array_map;
use function array_replace;
use function array_search;
use function file_exists;
use function filter_input;
use function is_null;
use function is_numeric;
use function is_string;
use function wp_get_global_settings;
use function wp_json_file_decode;
use const FILTER_SANITIZE_FULL_SPECIAL_CHARS;
use const INPUT_COOKIE;
use const INPUT_GET;

add_filter( 'body_class', NS . 'add_dark_mode_body_class' );
/**
 * Sets default body class.
 *
 * @since 0.9.10
 *
 * @param array $classes Body classes.
 *
 * @return array
 */
function add_dark_mode_body_class( array $classes ): array {
	$cookie         = filter_input( INPUT_COOKIE, 'blockifyDarkMode', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$url_param      = filter_input( INPUT_GET, 'dark_mode', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	$stylesheet_dir = get_stylesheet_directory();
	$default_mode   = file_exists( $stylesheet_dir . '/styles/light.json' ) ? 'dark' : 'light';
	$both_classes   = [ 'is-style-light', 'is-style-dark' ];

	$classes[] = 'default-mode-' . $default_mode;

	if ( ! $cookie ) {
		$classes[] = 'is-style-' . $default_mode;
	}

	if ( $cookie === 'true' ) {
		$classes[] = 'is-style-dark';
	} else if ( $cookie === 'false' ) {
		$classes[] = 'is-style-light';
	} else if ( $cookie === 'auto' ) {
		$classes = array_diff( $classes, $both_classes );

		$classes[] = 'default-mode-auto';
	}

	if ( $url_param ) {
		$classes = array_diff( $classes, $both_classes );

		$classes[] = $url_param === 'true' ? 'is-style-dark' : 'is-style-light';
	}

	return $classes;
}

add_filter( 'blockify_inline_css', NS . 'add_dark_mode_styles', 10, 3 );
/**
 * Adds dark mode styles.
 *
 * @since 0.0.24
 *
 * @param string $css       Inline CSS.
 * @param string $content   Page content.
 * @param bool   $is_editor Is Editor.
 *
 * @return string
 */
function add_dark_mode_styles( string $css, string $content, bool $is_editor ): string {
	$stylesheet_dir  = get_stylesheet_directory();
	$template_dir    = get_template_directory();
	$light_style     = $stylesheet_dir . '/styles/light.json';
	$dark_style      = $stylesheet_dir . '/styles/dark.json';
	$has_light_style = file_exists( $light_style );
	$has_dark_style  = file_exists( $dark_style );

	if ( ! $has_light_style && ! $has_dark_style ) {
		$light_style     = $template_dir . '/styles/light.json';
		$has_light_style = file_exists( $light_style );
	}

	$default_mode         = $has_light_style ? 'dark' : 'light';
	$opposite_mode        = $default_mode === 'light' ? 'dark' : 'light';
	$default_json_file    = $stylesheet_dir . '/theme.json';
	$opposite_json_file   = $stylesheet_dir . '/styles/' . $opposite_mode . '.json';
	$has_default          = file_exists( $default_json_file );
	$has_opposite         = file_exists( $opposite_json_file );
	$parent_default_file  = $template_dir . '/theme.json';
	$parent_has_default   = file_exists( $parent_default_file );
	$parent_opposite_file = $template_dir . '/styles/' . $opposite_mode . '.json';
	$parent_has_opposite  = file_exists( $parent_opposite_file );

	if ( ! ( $has_default || $parent_has_default ) ) {
		return $css;
	}

	$default_json         = [];
	$opposite_json        = [];
	$parent_default_json  = [];
	$parent_opposite_json = [];

	if ( $has_default ) {
		$default_json = wp_json_file_decode( $default_json_file );
	}

	if ( $has_opposite ) {
		$opposite_json = wp_json_file_decode( $opposite_json_file );
	}

	if ( $parent_has_default ) {
		$parent_default_json = wp_json_file_decode( $parent_default_file );
	}

	if ( $parent_has_opposite ) {
		$parent_opposite_json = wp_json_file_decode( $parent_opposite_file );
	}

	$settings           = wp_get_global_settings();
	$parent_colors      = get_color_values( (array) ( $parent_default_json->settings->color->palette ?? [] ) );
	$child_colors       = get_color_values( (array) ( $default_json->settings->color->palette ?? [] ) );
	$user_colors        = get_color_values( $settings['color']['palette']['theme'] ?? [] );
	$default_colors     = array_replace( $parent_colors, $child_colors, $user_colors );
	$parent_opposite    = get_color_values( (array) ( $parent_opposite_json->settings->color->palette ?? [] ) );
	$child_opposite     = get_color_values( (array) ( $opposite_json->settings->color->palette ?? [] ) );
	$theme_opposite     = array_replace( $parent_opposite, $child_opposite );
	$system_colors      = get_system_colors();
	$changed_colors     = array_diff( $user_colors, $child_colors, $parent_colors );
	$replacement_colors = get_replacement_colors( $settings );
	$user_opposite      = reverse_color_values( $default_colors, $changed_colors, $theme_opposite, $replacement_colors );

	if ( empty( $user_opposite ) ) {
		$user_opposite = reverse_color_values( $default_colors );
	}

	$opposite_colors = array_replace( $parent_opposite, $child_opposite, $user_opposite );

	// Gradients.
	$parent_gradients          = get_color_values( $parent_default_json->settings->color->gradients ?? [], 'gradient' );
	$child_gradients           = get_color_values( $default_json->settings->color->gradients ?? [], 'gradient' );
	$user_gradients            = get_color_values( $settings['color']['gradients']['theme'] ?? [], 'gradient' );
	$default_gradients         = array_replace( $parent_gradients, $child_gradients, $user_gradients );
	$parent_opposite_gradients = get_color_values( $parent_opposite_json->settings->color->gradients ?? [], 'gradient' );
	$child_opposite_gradients  = get_color_values( $opposite_json->settings->color->gradients ?? [], 'gradient' );
	$opposite_gradients        = array_replace( $parent_opposite_gradients, $child_opposite_gradients );

	if ( ! $default_colors && ! $opposite_colors && ! $default_gradients && ! $opposite_gradients ) {
		return $css;
	}

	foreach ( $default_colors as $slug => $value ) {
		if ( array_contains_any( $system_colors, [ $slug, $value ] ) ) {
			unset( $default_colors[ $slug ] );
			continue;
		}

		$new_slug  = array_search( $slug, $replacement_colors );
		$new_value = $default_colors[ $new_slug ] ?? '';

		if ( ! $new_slug || ! $new_value ) {
			continue;
		}

		$default_colors[ $slug ] = $new_value;
	}

	foreach ( $opposite_colors as $slug => $value ) {
		if ( array_contains_any( $system_colors, [ $slug, $value ] ) ) {
			unset( $opposite_colors[ $slug ] );
		}
	}

	$light_colors    = $default_mode === 'light' ? $default_colors : $opposite_colors;
	$dark_colors     = $default_mode === 'light' ? $opposite_colors : $default_colors;
	$light_gradients = $default_mode === 'light' ? $default_gradients : $opposite_gradients;
	$dark_gradients  = $default_mode === 'light' ? $opposite_gradients : $default_gradients;

	$light_styles = [];

	foreach ( $light_colors as $slug => $color ) {
		$light_styles[ '--wp--preset--color--' . $slug ] = $color;
	}

	foreach ( $light_gradients as $slug => $gradient ) {
		$light_styles[ '--wp--preset--gradient--' . $slug ] = $gradient;
	}

	$dark_styles = [];

	foreach ( $dark_colors as $slug => $color ) {
		$dark_styles[ '--wp--preset--color--' . $slug ] = $color;
	}

	foreach ( $dark_gradients as $slug => $gradient ) {
		$dark_styles[ '--wp--preset--gradient--' . $slug ] = $gradient;
	}

	$light_mode_property = (array) ( $settings['custom']['lightMode'] ?? [] );
	$dark_mode_property  = (array) ( $settings['custom']['darkMode'] ?? [] );

	foreach ( $light_mode_property as $property => $value ) {
		if ( is_string( $value ) ) {
			$light_styles[ $property ] = $value;
		}
	}

	foreach ( $dark_mode_property as $property => $value ) {
		if ( is_string( $value ) ) {
			$dark_styles[ $property ] = $value;
		}
	}

	$light_css = css_array_to_string( $light_styles );
	$dark_css  = css_array_to_string( $dark_styles );

	$css .= ".is-style-light:not(.default-mode-light){{$light_css}}";
	$css .= ".is-style-dark:not(.default-mode-dark){{$dark_css}}";

	if ( ! $is_editor ) {
		$css .= "@media(prefers-color-scheme:dark){body:not(.default-mode-dark):not(.is-style-light){{$dark_css}}}";
		$css .= "@media(prefers-color-scheme:light){body:not(.default-mode-light):not(.is-style-dark){{$light_css}}}";
	}

	$file = get_dir() . 'assets/css/extensions/dark-mode.css';

	if ( file_exists( $file ) ) {
		$css .= file_get_contents( $file );
	}

	return $css;
}

/**
 * Reverses color values for dark mode.
 *
 * @param array $colors       Key value pairs of colors.
 * @param array $changed      (Optional) Key value pairs of changed colors.
 * @param array $theme        (Optional) Key value pairs of theme colors.
 * @param array $replacements (Optional) Key value pairs of deprecated color
 *                            slugs.
 *
 * @return array
 */
function reverse_color_values( array $colors, array $changed = [], array $theme = [], array $replacements = [] ): array {
	$reversed      = [];
	$shade_scales  = get_shade_scales();
	$shade_strings = array_map( 'strval', $shade_scales['neutral'] ?? [] );

	foreach ( $colors as $slug => $value ) {
		if ( ! isset( $changed[ $slug ] ) ) {
			$theme_value = $theme[ $slug ] ?? null;

			if ( $theme_value ) {
				$reversed[ $slug ] = $theme_value;
			}

			if ( ! empty( $changed ) ) {
				continue;
			}
		}

		$original_slug = $slug;

		if ( isset( $replacements[ $slug ] ) ) {
			$slug = $replacements[ $slug ];
		}

		$explode = explode( '-', $slug );
		$color   = $explode[0] ?? null;
		$amount  = $explode[1] ?? null;

		if ( is_null( $color ) || ! is_numeric( $amount ) ) {
			continue;
		}

		$amount         = (int) $amount;
		$reverse_amount = $shade_scales[ $color ][ $amount ] ?? null;

		if ( is_null( $reverse_amount ) ) {
			continue;
		}

		$opposite_slug = $color . '-' . $reverse_amount;

		if ( str_contains_any( $original_slug, ...$shade_strings ) ) {
			$reversed[ $original_slug ] = $colors[ $opposite_slug ] ?? null;

			continue;
		}

		$new_slug  = array_search( $opposite_slug, $replacements );
		$new_value = $colors[ $new_slug ] ?? $colors[ $original_slug ] ?? null;

		if ( $new_value ) {
			$reversed[ $original_slug ] = $new_value;
		}
	}

	return $reversed;
}
