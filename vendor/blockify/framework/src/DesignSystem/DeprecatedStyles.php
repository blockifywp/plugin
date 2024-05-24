<?php

declare( strict_types=1 );

namespace Blockify\Framework\DesignSystem;

use Blockify\Framework\InlineAssets\Styleable;
use Blockify\Framework\InlineAssets\Styles;
use Blockify\Utilities\Color;
use Blockify\Dom\CSS;
use function file_exists;
use function get_template_directory;
use function wp_get_global_settings;
use function wp_json_file_decode;

/**
 * Deprecated.
 *
 * @since 1.3.0
 */
class DeprecatedStyles implements Styleable {

	/**
	 * Styles.
	 *
	 * @since 1.3.0
	 *
	 * @param Styles $styles Styles.
	 *
	 * @return void
	 */
	public function styles( Styles $styles ): void {
		$css = CSS::array_to_string( $this->get_deprecated_color_palette() );
		$css .= CSS::array_to_string( $this->get_deprecated_typography() );

		$styles->add_string( "body{{$css}}", [], ! empty( $css ) );
	}

	/**
	 * Adds deprecated color palette to inline styles.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	private function get_deprecated_color_palette(): array {
		$colors = Color::get_deprecated_colors();
		$styles = [];

		foreach ( $colors as $slug => $value ) {
			if ( $value ) {
				$styles["--wp--preset--color--{$slug}"] = $value;
			}
		}

		return $styles;
	}

	/**
	 * Adds deprecated typography to inline styles.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	private function get_deprecated_typography(): array {
		$global_settings = wp_get_global_settings();
		$font_sizes      = $global_settings['typography']['fontSizes']['theme'] ?? [];

		$styles = [];

		if ( ! $font_sizes ) {
			return $styles;
		}

		$has_deprecated = false;
		$slugs          = [];

		foreach ( $font_sizes as $font_size ) {
			$slug = $font_size['slug'] ?? '';

			if ( $slug === '81' ) {
				$has_deprecated = true;
			}

			$slugs[ $slug ] = $font_size;
		}

		if ( ! $has_deprecated ) {
			return $styles;
		}

		$theme_json_file = get_template_directory() . '/theme.json';

		if ( ! file_exists( $theme_json_file ) ) {
			return $styles;
		}

		$theme_json            = wp_json_file_decode( $theme_json_file );
		$theme_json_font_sizes = (array) ( $theme_json->settings->typography->fontSizes ?? [] );

		if ( ! $theme_json_font_sizes ) {
			return $styles;
		}

		foreach ( $theme_json_font_sizes as $font_size ) {
			$slug = $font_size->slug ?? '';

			if ( isset( $slugs[ $slug ] ) ) {
				continue;
			}

			$styles["--wp--preset--font-size--{$slug}"] = $font_size->size ?? '';
		}

		return $styles;
	}

}

