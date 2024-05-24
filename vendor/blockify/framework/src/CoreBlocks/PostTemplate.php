<?php

declare( strict_types=1 );

namespace Blockify\Framework\CoreBlocks;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function is_null;

/**
 * PostTemplate class.
 *
 * @since 1.0.0
 */
class PostTemplate implements Renderable {

	/**
	 * Modifies front end HTML output of block.
	 *
	 * @since 1.3.2
	 *
	 * @param string   $block_content Block content.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook  render_block_core/post-template
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$attrs     = $block['attrs'] ?? [];
		$block_gap = $attrs['style']['spacing']['blockGap'] ?? null;
		$layout    = $attrs['layout']['type'] ?? null;
		$columns   = $attrs['layout']['columnCount'] ?? null;

		if ( $columns ) {
			$dom   = DOM::create( $block_content );
			$first = DOM::get_element( '*', $dom );

			if ( $first ) {
				$first_styles = CSS::string_to_array( $first->getAttribute( 'style' ) );

				$first_styles['--columns'] = $columns;

				$first->setAttribute( 'style', CSS::array_to_string( $first_styles ) );

				$block_content = $dom->saveHTML();
			}
		}

		if ( ! is_null( $block_gap ) && $layout !== 'grid' ) {
			$dom   = DOM::create( $block_content );
			$first = DOM::get_element( '*', $dom );

			if ( $first ) {
				$first_styles = CSS::string_to_array( $first->getAttribute( 'style' ) );

				$first_styles['gap']       = CSS::format_custom_property( $block_gap );
				$first_styles['display']   = 'flex';
				$first_styles['flex-wrap'] = 'wrap';

				$first->setAttribute( 'style', CSS::array_to_string( $first_styles ) );

				$block_content = $dom->saveHTML();
			}
		}

		return $block_content;
	}
}
