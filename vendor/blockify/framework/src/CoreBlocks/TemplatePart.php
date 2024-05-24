<?php

declare( strict_types=1 );

namespace Blockify\Framework\CoreBlocks;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function esc_attr;

/**
 * TemplatePart class.
 *
 * @since 1.0.0
 */
class TemplatePart implements Renderable {

	/**
	 * Modifies the template part block.
	 *
	 * @since 0.7.1
	 *
	 * @param string   $block_content Block HTML.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook  render_block_core/template-part
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$dom   = DOM::create( $block_content );
		$first = DOM::get_element( '*', $dom );

		if ( ! $first ) {
			return $block_content;
		}

		$attrs  = $block['attrs'] ?? [];
		$styles = CSS::string_to_array( $first->getAttribute( 'style' ) );
		$color  = $attrs['style']['color'] ?? [];

		if ( isset( $color['background'] ) ) {
			$styles['background'] = esc_attr( $color['background'] );
		}

		if ( isset( $attrs['backgroundColor'] ) ) {
			$styles['background'] = 'var(--wp--preset--color--' . esc_attr( $attrs['backgroundColor'] ) . ')';
		}

		if ( isset( $color['gradient'] ) ) {
			$styles['background'] = esc_attr( $color['gradient'] );
		}

		if ( isset( $attrs['gradient'] ) ) {
			$styles['background'] = 'var(--wp--preset--gradient--' . esc_attr( $attrs['gradient'] ) . ')';
		}

		if ( isset( $color['text'] ) ) {
			$styles['color'] = esc_attr( $color['text'] );
		}

		if ( isset( $attrs['textColor'] ) ) {
			$styles['color'] = 'var(--wp--preset--color--' . esc_attr( $attrs['textColor'] ) . ')';
		}

		$styles = CSS::array_to_string( $styles );

		if ( $styles ) {
			$first->setAttribute( 'style', $styles );
		} else {
			$first->removeAttribute( 'style' );
		}

		if ( $block['attrs']['slug'] === 'header' ) {
			$first->setAttribute( 'role', 'banner' );
		}

		if ( $block['attrs']['slug'] === 'main' ) {
			$first->setAttribute( 'role', 'main' );
		}

		if ( $block['attrs']['slug'] === 'footer' ) {
			$first->setAttribute( 'role', 'contentinfo' );
		}

		return $dom->saveHTML();
	}

}
