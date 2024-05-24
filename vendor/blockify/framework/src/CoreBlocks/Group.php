<?php

declare( strict_types=1 );

namespace Blockify\Framework\CoreBlocks;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function array_diff;
use function esc_attr;
use function explode;
use function implode;
use function in_array;

/**
 * Group class.
 *
 * @since 1.0.0
 */
class Group implements Renderable {

	/**
	 * Modifies front end HTML output of block.
	 *
	 * @since 1.3.0
	 *
	 * @param string   $block_content Block HTML.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook  render_block_core/group
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$dom   = DOM::create( $block_content );
		$first = DOM::get_element( '*', $dom );

		if ( ! $first ) {
			return $block_content;
		}

		if ( $block['attrs']['minHeight'] ?? null ) {
			$first->setAttribute(
				'style',
				$first->getAttribute( 'style' ) . ';min-height:' . $block['attrs']['minHeight']
			);
		}

		$margin  = $block['attrs']['style']['spacing']['margin'] ?? [];
		$padding = $block['attrs']['style']['spacing']['padding'] ?? [];

		$div_styles = CSS::string_to_array( $first->getAttribute( 'style' ) );
		$div_styles = CSS::add_shorthand_property( $div_styles, 'margin', $margin );
		$div_styles = CSS::add_shorthand_property( $div_styles, 'padding', $padding );

		if ( $div_styles ) {
			$first->setAttribute( 'style', CSS::array_to_string( $div_styles ) );
		}

		$tag = esc_attr( $block['attrs']['tagName'] ?? 'div' );

		if ( $tag === 'main' ) {
			$first->setAttribute( 'role', $tag );

			$classes = explode( ' ', $first->getAttribute( 'class' ) );

			// Move `site-main` class to the start of the array.
			if ( in_array( 'site-main', $classes, true ) ) {
				$classes = [
					'site-main',
					...( array_diff( $classes, [ 'site-main' ] ) ),
				];
			}

			$first->setAttribute( 'class', implode( ' ', $classes ) );
		}

		return $dom->saveHTML();
	}
}
