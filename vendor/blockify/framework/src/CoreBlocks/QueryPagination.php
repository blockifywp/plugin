<?php

declare( strict_types=1 );

namespace Blockify\Framework\CoreBlocks;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function str_contains;

/**
 * QueryPagination class.
 *
 * @since 1.0.0
 */
class QueryPagination implements Renderable {

	/**
	 * Modifies front end HTML output of block.
	 *
	 * @since 0.0.2
	 *
	 * @param string   $block_content Block HTML.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook  render_block_core/query-pagination
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$dom = DOM::create( $block_content );
		$nav = DOM::get_element( 'nav', $dom );

		if ( ! $nav ) {
			return $block_content;
		}

		$styles  = CSS::string_to_array( $nav->getAttribute( 'style' ) );
		$margin  = $block['attrs']['style']['spacing']['margin'] ?? null;
		$padding = $block['attrs']['style']['spacing']['padding'] ?? null;
		$styles  = CSS::add_shorthand_property( $styles, 'margin', $margin );
		$styles  = CSS::add_shorthand_property( $styles, 'padding', $padding );

		foreach ( $styles as $key => $value ) {
			if ( ! $value ) {
				continue;
			}

			// TODO: Which properties need formatting?
			if ( str_contains( $value, 'var:' ) ) {
				$styles[ $key ] = CSS::format_custom_property( $value );
			}
		}

		$border_radius = $block['attrs']['style']['border']['radius'] ?? null;

		if ( $border_radius ) {
			$styles['border-radius'] = $border_radius;
		}

		$nav->setAttribute( 'style', CSS::array_to_string( $styles ) );

		return $dom->saveHTML();
	}

}
