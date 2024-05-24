<?php

declare( strict_types=1 );

namespace Blockify\Framework\CoreBlocks;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function explode;
use function implode;
use function is_array;

/**
 * Paragraph class.
 *
 * @since 1.0.0
 */
class Paragraph implements Renderable {

	/**
	 * Modifies front end HTML output of block.
	 *
	 * @since 0.0.2
	 *
	 * @param string   $block_content Block HTML.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook  render_block_core/paragraph
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$dom = DOM::create( $block_content );
		$p   = DOM::get_element( 'p', $dom );

		if ( ! $p ) {
			return $block_content;
		}

		$p->setAttribute(
			'class',
			implode(
				' ',
				[
					'wp-block-paragraph',
					...explode(
						' ',
						$p->getAttribute( 'class' )
					),
				]
			)
		);

		$p_styles = CSS::string_to_array( $p->getAttribute( 'style' ) );
		$padding  = $block['attrs']['style']['spacing']['padding'] ?? '';
		$margin   = $block['attrs']['style']['spacing']['margin'] ?? '';

		if ( is_array( $padding ) && ! empty( $padding ) ) {
			$p_styles = CSS::add_shorthand_property( $p_styles, 'padding', $padding );
		}

		if ( is_array( $margin ) && ! empty( $margin ) ) {
			$p_styles = CSS::add_shorthand_property( $p_styles, 'margin', $margin );
		}

		$p->setAttribute( 'style', CSS::array_to_string( $p_styles ) );

		$block_content = $dom->saveHTML();

		if ( str_contains( $block_content, 'http://http' ) ) {
			$block_content = str_replace( 'http://http', 'http', $block_content );
		}

		return $block_content;
	}
}
