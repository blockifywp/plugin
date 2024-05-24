<?php

declare( strict_types=1 );

namespace Blockify\Framework\CoreBlocks;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function array_unique;
use function count;
use function explode;

/**
 * Columns class.
 *
 * @since 1.0.0
 */
class Columns implements Renderable {

	/**
	 * Modifies front end HTML output of block.
	 *
	 * @since 0.0.2
	 *
	 * @param string   $block_content Block HTML.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook  render_block_core/columns
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$attrs = $block['attrs'] ?? [];
		$dom   = DOM::create( $block_content );
		$div   = DOM::get_element( 'div', $dom );

		if ( ! $div ) {
			return $block_content;
		}

		$classes = explode( ' ', $div->getAttribute( 'class' ) );
		$styles  = CSS::string_to_array( $div->getAttribute( 'style' ) );

		$margin = $attrs['style']['spacing']['margin'] ?? null;

		if ( $margin ) {
			$styles = CSS::add_shorthand_property( $styles, 'margin', $margin );
		}

		$stacked   = $attrs['isStackedOnMobile'] ?? true;
		$classes[] = $stacked ? 'is-stacked-on-mobile' : 'is-not-stacked-on-mobile';

		$column_count = (string) count( $block['innerBlocks'] ?? 0 );

		$div->setAttribute( 'data-columns', $column_count );

		$styles['--columns'] = $column_count;

		$div->setAttribute( 'style', CSS::array_to_string( $styles ) );
		$div->setAttribute( 'class', implode( ' ', array_unique( $classes ) ) );

		return $dom->saveHTML();
	}

}
