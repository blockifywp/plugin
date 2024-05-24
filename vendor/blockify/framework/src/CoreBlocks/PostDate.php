<?php

declare( strict_types=1 );

namespace Blockify\Framework\CoreBlocks;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;

/**
 * PostDate class.
 *
 * @since 1.0.0
 */
class PostDate implements Renderable {

	/**
	 * Adds block supports to the core post date block.
	 *
	 * @since 0.0.1
	 *
	 * @param string   $block_content The block content.
	 * @param array    $block         The block.
	 * @param WP_Block $instance      The block instance.
	 *
	 * @hook  render_block_core/post-date
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$margin = $block['attrs']['style']['spacing']['margin'] ?? null;

		if ( $margin ) {
			$dom = DOM::create( $block_content );
			$div = DOM::get_element( 'div', $dom );

			if ( ! $div ) {
				return $block_content;
			}

			$styles = CSS::string_to_array( $div->getAttribute( 'style' ) );
			$styles = CSS::add_shorthand_property( $styles, 'margin', $margin );

			$div->setAttribute( 'style', CSS::array_to_string( $styles ) );

			$block_content = $dom->saveHTML();
		}

		return $block_content;
	}

}
