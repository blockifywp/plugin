<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use Blockify\Utilities\Str;
use function add_filter;
use function apply_filters;
use function array_merge;
use function basename;
use function get_option;
use function in_array;
use function wp_get_global_settings;
use function wp_list_pluck;

add_filter( 'wp_theme_json_data_theme', __NAMESPACE__ . '\\add_fonts' );
/**
 * Add all fonts to the editor.
 *
 * @param mixed $theme_json Theme JSON.
 *
 * @return mixed
 */
function add_fonts( $theme_json ) {
	$data = $theme_json->get_data();

	$data['settings']['typography']['fontFamilies']['theme'] = array_merge(
		$data['settings']['typography']['fontFamilies']['theme'] ?? [],
		get_selected_fonts(),
	);

	$theme_json->update_with( $data );

	return $theme_json;
}

/**
 * Returns array of user selected font families.
 *
 * @since 0.4.0
 *
 * @return array
 */
function get_selected_fonts(): array {
	$font_families  = get_option( 'blockify', [] )['googleFonts'] ?? [];
	$selected_fonts = [];
	$basename       = basename( DIR );

	if ( empty( $font_families ) ) {
		return $selected_fonts;
	}

	foreach ( $font_families as $font_family ) {
		$slug = $font_family['value'] ?? '';
		$name = $font_family['label'] ?? '';

		if ( ! $slug || ! $name ) {
			continue;
		}

		if ( in_array( $slug, [ 'sans-serif', 'serif', 'monospace', 'inherit', 'initial' ], true ) ) {
			continue;
		}

		$selected_fonts[] = [
			'fontFamily' => $name,
			'name'       => $name,
			'slug'       => $slug,
			'fontFace'   => [
				[
					'fontFamily'  => $name,
					'fontStyle'   => 'normal',
					'fontStretch' => 'normal',
					'fontDisplay' => 'swap',
					'fontWeight'  => '100 900',
					'src'         => [
						"file:./../../plugins/$basename/assets/fonts/$slug.woff2",
					],
				],
			],
		];
	}

	return $selected_fonts;
}

/**
 * Returns an array of all available local fonts.
 *
 * @since 0.4.0
 *
 * @return array
 */
function get_all_fonts(): array {
	$font_families = [];
	$font_slugs    = wp_list_pluck( $font_families, 'slug' );
	$fonts_dir     = get_cache_dir( 'fonts' );
	$font_files    = glob( $fonts_dir . '*.woff2' );
	$basename      = basename( $fonts_dir );

	foreach ( $font_files as $font_file ) {
		$slug = basename( $font_file, '.woff2' );

		if ( in_array( $slug, $font_slugs, true ) ) {
			continue;
		}

		$font_slugs[] = $slug;
		$name         = Str::title_case( $slug );

		$font_families[] = [
			'fontFamily' => $name,
			'name'       => $name,
			'slug'       => $slug,
			'fontFace'   => [
				[
					'fontFamily'  => $name,
					'fontStyle'   => 'normal',
					'fontStretch' => 'normal',
					'fontDisplay' => 'swap',
					'fontWeight'  => '100 900',
					'src'         => [
						"file:./../../plugins/$basename/assets/fonts/$slug.woff2",
					],
				],
			],
		];
	}

	return apply_filters( 'blockify_font_families', $font_families );
}

add_filter( 'blockify_editor_data', __NAMESPACE__ . '\\add_font_family_data', 11 );
/**
 * Add default block supports.
 *
 * @since 0.9.10
 *
 * @param array $config Blockify editor config.
 *
 * @return array
 */
function add_font_family_data( array $config ): array {
	$selected_fonts = wp_get_global_settings()['typography']['fontFamilies']['theme'] ?? [];

	$config['fontFamilies']  = wp_list_pluck( get_all_fonts(), 'slug' );
	$config['selectedFonts'] = wp_list_pluck( $selected_fonts, 'slug' );

	return $config;
}

/**
 * Returns an array of font options.
 *
 * @since 0.6.0
 *
 * @return array
 */
function get_font_options(): array {
	$slugs   = wp_list_pluck( get_all_fonts(), 'slug' );
	$options = [];

	foreach ( $slugs as $slug ) {
		$options[] = [
			'label' => Str::title_case( $slug ),
			'value' => $slug,
		];
	}

	return $options;
}
