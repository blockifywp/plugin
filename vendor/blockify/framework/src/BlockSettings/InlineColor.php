<?php

declare( strict_types=1 );

namespace Blockify\Framework\BlockSettings;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use DOMElement;
use WP_Block;
use function explode;
use function in_array;
use function str_contains;

/**
 * InlineColor class.
 *
 * @since 1.0.0
 */
class InlineColor implements Renderable {

	/**
	 * Renders custom properties for inline colors.
	 *
	 * @since 0.9.25
	 *
	 * @param string   $block_content Block HTML content.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook  render_block
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		if ( ! str_contains( $block_content, 'has-inline-color' ) ) {
			return $block_content;
		}

		$dom   = DOM::create( $block_content );
		$first = DOM::get_element( '*', $dom );

		if ( ! $first ) {
			return $block_content;
		}

		$global_settings = wp_get_global_settings();
		$color_palette   = $global_settings['color']['palette']['theme'] ?? [];

		foreach ( $first->childNodes as $child ) {
			if ( ! $child instanceof DOMElement ) {
				continue;
			}

			$classes = explode( ' ', $child->getAttribute( 'class' ) );

			if ( ! in_array( 'has-inline-color', $classes, true ) ) {
				continue;
			}

			$styles = CSS::string_to_array( $child->getAttribute( 'style' ) );

			foreach ( $color_palette as $color ) {
				$hex_value   = $styles['color'] ?? '';
				$color_value = $color['color'] ?? '';

				if ( ! $hex_value || ! $color_value ) {
					continue;
				}

				if ( $hex_value === $color_value ) {
					$styles['color'] = "var(--wp--preset--color--{$color['slug']})";
					$child->setAttribute( 'style', CSS::array_to_string( $styles ) );

					break;
				}
			}

			$block_content = $dom->saveHTML();
		}

		return $block_content;
	}

}
