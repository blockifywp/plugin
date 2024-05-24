<?php

declare( strict_types=1 );

namespace Blockify\Framework\BlockSettings;

use Blockify\Framework\InlineAssets\Styleable;
use Blockify\Framework\InlineAssets\Styles;
use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function array_diff;
use function esc_attr;
use function in_array;
use function str_contains;
use function wp_get_global_settings;

/**
 * Shadow class.
 *
 * @since 1.0.0
 */
class BoxShadow implements Renderable, Styleable {

	/**
	 * Adds box shadow to blocks.
	 *
	 * @param string   $block_content The block content.
	 * @param array    $block         The block.
	 * @param WP_Block $instance      The block instance.
	 *
	 * @hook render_block 13
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$nested_element_blocks = [
			'core/button',
		];

		$shadow_preset = $block['attrs']['shadowPreset'] ?? null;
		$hover_preset  = $block['attrs']['shadowPresetHover'] ?? null;

		if ( $shadow_preset ) {

			if ( in_array( $block['blockName'], $nested_element_blocks, true ) ) {
				$dom    = DOM::create( $block_content );
				$first  = DOM::get_element( '*', $dom );
				$nested = DOM::get_element( '*', $first );

				if ( ! $first || ! $nested ) {
					return $block_content;
				}

				$first_classes  = explode( ' ', $first->getAttribute( 'class' ) );
				$nested_classes = explode( ' ', $nested->getAttribute( 'class' ) );

				foreach ( $first_classes as $index => $class ) {
					$exploded = explode( '-', $class );
					$has      = 'has' === ( $exploded[0] ?? null );
					$shadow   = in_array( 'shadow', [ $exploded[1] ?? '', $exploded[2] ?? '' ], true );

					if ( $has && $shadow ) {
						unset( $first_classes[ $index ] );
						$nested_classes[] = $class;
					}
				}

				$first->setAttribute( 'class', implode( ' ', $first_classes ) );
				$nested->setAttribute( 'class', implode( ' ', $nested_classes ) );

				$block_content = $dom->saveHTML();
			}
		}

		if ( $hover_preset && ! $shadow_preset ) {
			$dom       = DOM::create( $block_content );
			$first     = DOM::get_element( '*', $dom );
			$classes   = explode( ' ', $first->getAttribute( 'class' ) );
			$classes   = array_diff( $classes, [ 'has-shadow' ] );
			$classes[] = 'has-shadow-hover';

			$first->setAttribute( 'class', implode( ' ', $classes ) );

			$block_content = $dom->saveHTML();
		}

		$custom_shadow = $block['attrs']['style']['boxShadow'] ?? null;

		if ( ! $custom_shadow ) {
			return $block_content;
		}

		$dom   = DOM::create( $block_content );
		$first = DOM::get_element( '*', $dom );

		if ( ! $first ) {
			return $block_content;
		}

		$first_classes = explode( ' ', $first->getAttribute( 'class' ) );

		if ( ! in_array( 'has-box-shadow', $first_classes, true ) ) {
			$first_classes[] = 'has-box-shadow';
		}

		$first->setAttribute( 'class', implode( ' ', $first_classes ) );

		$styles = CSS::string_to_array( $first->getAttribute( 'style' ) );

		$inset       = $custom_shadow['inset'] ?? null;
		$inset_hover = $custom_shadow['hover']['inset'] ?? null;

		if ( $inset ) {
			$styles['--wp--custom--box-shadow--inset'] = 'inset';
		}

		if ( $inset_hover ) {
			$styles['--wp--custom--box-shadow--hover--inset'] = 'inset';
		}

		foreach ( [ 'x', 'y', 'blur', 'spread' ] as $property ) {

			if ( $custom_shadow[ $property ] ?? '' ) {
				$styles[ '--wp--custom--box-shadow--' . $property ] = esc_attr( $custom_shadow[ $property ] ) . 'px';
			}

			if ( $custom_shadow['hover'][ $property ] ?? '' ) {
				$styles[ '--wp--custom--box-shadow--hover--' . $property ] = esc_attr( $custom_shadow['hover'][ $property ] ) . 'px';
			}

		}

		$color       = $custom_shadow['color'] ?? null;
		$hover_color = $custom_shadow['hover']['color'] ?? null;
		$palette     = wp_get_global_settings()['color']['palette']['theme'] ?? [];

		foreach ( $palette as $theme_color ) {
			if ( $theme_color['color'] === $color ) {
				$styles['--wp--custom--box-shadow--color'] = "var(--wp--preset--color--{$theme_color['slug']})";
			}

			if ( $theme_color['color'] === $hover_color ) {
				$styles['--wp--custom--box-shadow--hover--color'] = "var(--wp--preset--color--{$theme_color['slug']})";
			}
		}

		$first->setAttribute( 'style', CSS::array_to_string( $styles ) );

		return $dom->saveHTML();
	}

	/**
	 * Adds box shadow custom properties.
	 *
	 * @param Styles $styles The styles.
	 *
	 * @return void
	 */
	public function styles( Styles $styles ): void {
		$styles->add_callback( [ $this, 'get_inline_css' ] );
	}

	/**
	 * Gets inline CSS.
	 *
	 * @param string $template_html The template HTML.
	 * @param bool   $load_all      Whether to load all.
	 *
	 * @return string
	 */
	public function get_inline_css( string $template_html, bool $load_all ): string {
		$settings = wp_get_global_settings();
		$presets  = $settings['shadow']['presets']['theme'] ?? [];
		$style    = [];

		foreach ( $presets as $preset ) {
			$slug   = $preset['slug'] ?? null;
			$shadow = $preset['shadow'] ?? null;

			if ( ! $slug || ! $shadow ) {
				continue;
			}

			if ( ! $load_all && ! str_contains( $template_html, "has-{$slug}" ) ) {
				continue;
			}

			$style[ '--wp--preset--shadow--' . $slug . '--hover' ] = esc_attr( $preset['shadow'] );
		}

		$css = '';

		if ( ! empty( $style ) ) {
			$css = 'body{' . CSS::array_to_string( $style ) . '}';
		}

		return $css;
	}
}
