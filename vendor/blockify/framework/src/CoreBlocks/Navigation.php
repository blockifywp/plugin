<?php

declare( strict_types=1 );

namespace Blockify\Framework\CoreBlocks;

use Blockify\Framework\InlineAssets\Styleable;
use Blockify\Framework\InlineAssets\Styles;
use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function array_keys;
use function array_search;
use function in_array;
use function is_array;
use function is_string;
use function str_contains;
use function str_replace;
use function trim;
use function wp_get_global_settings;
use function wp_get_global_styles;
use function wp_list_pluck;

/**
 * Navigation class.
 *
 * @since 1.0.0
 */
class Navigation implements Renderable, Styleable {

	/**
	 * Modifies front end HTML output of block.
	 *
	 * @since 0.0.2
	 *
	 * @param string   $block_content Block HTML.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook  render_block_core/navigation
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {

		// Replace invalid root relative URLs.
		$block_content = str_replace( 'http://./', './', $block_content );
		$dom           = DOM::create( $block_content );
		$nav           = DOM::get_element( 'nav', $dom );

		if ( ! $nav ) {
			return $block_content;
		}

		$styles       = DOM::get_styles( $nav );
		$classes      = DOM::get_classes( $nav );
		$attrs        = $block['attrs'] ?? [];
		$overlay_menu = $attrs['overlayMenu'] ?? $attrs['icon'] ?? true;
		$filter       = $attrs['style']['filter'] ?? null;

		if ( ! empty( $filter ) ) {
			$filter_value = '';

			foreach ( $filter as $property => $value ) {
				if ( $property === 'backdrop' ) {
					continue;
				}

				$value = CSS::format_custom_property( $value ) . 'px';

				$filter_value .= "$property($value) ";
			}

			$styles['--wp--custom--nav--filter'] = trim( $filter_value );

			if ( $filter['backdrop'] ?? null ) {
				$classes[] = 'has-backdrop-filter';
			}

			DOM::add_styles( $nav, $styles );
			DOM::add_classes( $nav, $classes );
		}

		if ( $overlay_menu ) {
			$overlay_background_color = $attrs['overlayBackgroundColor'] ?? $attrs['customOverlayBackgroundColor'] ?? '';

			$global_settings = wp_get_global_settings();
			$color_slugs     = wp_list_pluck(
				$global_settings['color']['palette']['theme'] ?? [], 'slug'
			);
			$color_values    = wp_list_pluck(
				$global_settings['color']['palette']['theme'] ?? [], 'color'
			);

			if ( $overlay_background_color === 'white' && in_array( '#ffffff', $color_values, true ) ) {
				$index = array_search( '#ffffff', $color_values, true );

				if ( $index ) {
					$overlay_background_color = $color_slugs[ $index ] ?? '';
				}
			}

			if ( in_array( $overlay_background_color, $color_slugs, true ) ) {
				$overlay_background_color = "var(--wp--preset--color--{$overlay_background_color})";
			}

			if ( $overlay_background_color ) {
				$styles['--wp--custom--nav--background-color'] = CSS::format_custom_property( $overlay_background_color );
			}

			DOM::add_styles( $nav, $styles );
		}

		$block_content = $dom->saveHTML();

		$spacing = $attrs['style']['spacing'] ?? null;

		if ( ! $spacing ) {
			return $block_content;
		}

		$padding = $spacing['padding'] ?? null;

		unset( $spacing['padding'] );

		foreach ( array_keys( $spacing ) as $attribute ) {
			$prop = $attribute === 'blockGap' ? 'gap' : $attribute;

			if ( is_string( $spacing[ $attribute ] ) ) {
				$styles[ $prop ] = CSS::format_custom_property( $spacing[ $attribute ] );
			}

			if ( is_array( $spacing[ $attribute ] ) ) {
				foreach ( array_keys( $spacing[ $attribute ] ) as $side ) {
					$styles["$prop-$side"] = CSS::format_custom_property( $spacing[ $attribute ][ $side ] );
				}
			}
		}

		if ( $padding ) {
			$padding_top    = CSS::format_custom_property( $padding['top'] ?? '' );
			$padding_bottom = CSS::format_custom_property( $padding['bottom'] ?? '' );

			$styles['--wp--custom--nav--padding'] = $padding_top ?: $padding_bottom;
		}

		if ( $styles ) {
			$nav->setAttribute( 'style', CSS::array_to_string( $styles ) );
		}

		$buttons = DOM::get_elements_by_class_name( 'wp-block-navigation-submenu__toggle', $dom );

		foreach ( $buttons as $button ) {
			$span = $button->nextSibling;

			if ( ! $span || $span->tagName !== 'span' ) {
				continue;
			}

			$span->parentNode->removeChild( $span );
			$button->appendChild( $span );
		}

		return $dom->saveHTML();
	}

	/**
	 * Adds CSS for submenu borders.
	 *
	 * @since 0.0.2
	 *
	 * @param Styles $styles Styles instance.
	 *
	 * @return void
	 */
	public function styles( Styles $styles ): void {
		$styles->add_file( 'core-blocks/navigation.css', [ 'wp-block-navigation__submenu-container' ] );
		$styles->add_callback( [ $this, 'get_submenu_styles' ] );
	}

	/**
	 * Returns submenu styles.
	 *
	 * @param string $template_html Template HTML.
	 * @param bool   $load_all      Load all styles.
	 *
	 * @return string
	 */
	public function get_submenu_styles( string $template_html, bool $load_all ): string {
		if ( ! $load_all && ! str_contains( $template_html, 'wp-block-navigation__submenu-container' ) ) {
			return '';
		}

		$global_styles = wp_get_global_styles();
		$border        = $global_styles['blocks']['core/navigation-submenu']['border'] ?? [];
		$styles        = [];

		foreach ( [ 'top', 'right', 'bottom', 'left' ] as $side ) {
			if ( ! isset( $border[ $side ] ) ) {
				continue;
			}

			if ( $border[ $side ]['width'] ?? '' ) {
				$styles["border-$side-width"] = $border[ $side ]['width'];
			}

			if ( $border[ $side ]['style'] ?? '' ) {
				$styles["border-$side-style"] = $border[ $side ]['style'];
			}

			if ( $border[ $side ]['color'] ?? '' ) {
				$styles["border-$side-color"] = CSS::format_custom_property( $border[ $side ]['color'] );
			}
		}

		$radius = $border['radius'] ?? null;

		if ( $radius ) {
			$styles['border-radius'] = CSS::format_custom_property( $radius );
		}

		$css = '';

		if ( $styles ) {
			$css = '.wp-block-navigation-submenu{border:0}.wp-block-navigation .wp-block-navigation-item .wp-block-navigation__submenu-container{' . CSS::array_to_string( $styles ) . '}';
		}

		return $css;
	}

}
