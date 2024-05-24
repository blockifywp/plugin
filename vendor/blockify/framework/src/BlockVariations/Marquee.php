<?php

declare( strict_types=1 );

namespace Blockify\Framework\BlockVariations;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function is_array;

/**
 * Marquee class.
 *
 * @since 1.0.0
 */
class Marquee implements Renderable {

	/**
	 * Render marquee block variation.
	 *
	 * @since 1.0.0
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
		if ( ( $block['attrs']['layout']['orientation'] ?? '' ) !== 'marquee' ) {
			return $block_content;
		}

		$dom   = DOM::create( $block_content );
		$first = DOM::get_element( '*', $dom );

		if ( ! $first ) {
			return $block_content;
		}

		$repeat  = $block['attrs']['repeatItems'] ?? 2;
		$wrap    = DOM::create_element( 'div', $dom );
		$styles  = CSS::string_to_array( $first->getAttribute( 'style' ) );
		$classes = explode( ' ', $first->getAttribute( 'class' ) );
		$classes = array_diff( $classes, [ 'is-marquee' ] );

		$gap = $block['attrs']['style']['spacing']['blockGap'] ?? null;

		if ( $gap || $gap === '0' ) {
			if ( is_array( $gap ) ) {
				$gap = $gap['horizontal'] ?? $gap['left'] ?? $gap['right'] ?? null;
			}

			if ( $gap ) {
				$styles['--marquee-gap'] = CSS::format_custom_property( $gap );
			}
		}

		$first->setAttribute( 'class', implode( ' ', $classes ) );
		$first->setAttribute( 'style', CSS::array_to_string( $styles ) );
		$wrap->setAttribute( 'class', 'is-marquee' );

		$count = $first->childNodes->count();

		for ( $i = 0; $i < $count; $i++ ) {
			$item = $first->childNodes->item( $i );

			if ( ! $item || ! method_exists( $item, 'setAttribute' ) ) {
				continue;
			}

			$wrap->appendChild( $item );

			for ( $j = 0; $j < $repeat; $j++ ) {
				$clone = DOM::node_to_element( $item->cloneNode( true ) );

				if ( ! $clone ) {
					continue;
				}

				$classes   = explode( ' ', $clone->getAttribute( 'class' ) );
				$classes[] = 'is-cloned';
				$clone->setAttribute( 'class', implode( ' ', $classes ) );
				$wrap->appendChild( $clone );
			}
		}

		$first->insertBefore( $wrap, $first->firstChild );

		return $dom->saveHTML();
	}

}
