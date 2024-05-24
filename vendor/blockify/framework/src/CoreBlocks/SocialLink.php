<?php

declare( strict_types=1 );

namespace Blockify\Framework\CoreBlocks;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Icons\Icon;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function esc_attr;

/**
 * SocialLink class.
 *
 * @since 1.0.0
 */
class SocialLink implements Renderable {


	/**
	 * Modifies front end HTML output of block.
	 *
	 * @since 0.0.24
	 *
	 * @param string   $block_content Block HTML.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook  render_block_core/social-link
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$textColor = esc_attr( $block['attrs']['textColor'] ?? '' );

		if ( $textColor ) {
			$dom       = DOM::create( $block_content );
			$list_item = DOM::get_element( 'li', $dom );

			if ( ! $list_item ) {
				return $block_content;
			}

			$styles          = CSS::string_to_array( $list_item->getAttribute( 'style' ) );
			$styles['color'] = "var(--wp--preset--color--$textColor)";

			$list_item->setAttribute( 'style', CSS::array_to_string( $styles ) );

			$classes = explode( ' ', $list_item->getAttribute( 'class' ) );

			$classes[] = 'has-text-color';

			$list_item->setAttribute( 'class', implode( ' ', $classes ) );

			$block_content = $dom->saveHTML();
		}

		$service = $block['attrs']['service'] ?? null;

		if ( $service === 'slack' ) {
			$dom         = DOM::create( $block_content );
			$li          = DOM::get_element( 'li', $dom );
			$a           = DOM::get_element( 'a', $li );
			$default_svg = DOM::get_element( 'svg', $a );

			if ( ! $default_svg ) {
				return $block_content;
			}

			// TODO: New svg location.
			$svg_dom = DOM::create( Icon::get_svg( 'social', 'slack' ) );
			$svg     = DOM::get_element( 'svg', $svg_dom );

			$svg->setAttribute( 'fill', 'currentColor' );
			$svg->setAttribute( 'width', '24' );
			$svg->setAttribute( 'height', '24' );
			$svg->setAttribute( 'aria-hidden', 'true' );
			$svg->setAttribute( 'focusable', 'false' );
			$svg->setAttribute( 'role', 'img' );

			$imported = $dom->importNode( $svg, true );

			$a->appendChild( $imported );
			$a->removeChild( $default_svg );

			$block_content = $dom->saveHTML( $li );
		}

		$url = $block['attrs']['url'] ?? null;

		if ( $url === '#' ) {
			$dom = DOM::create( $block_content );
			$li  = DOM::get_element( 'li', $dom );
			$a   = DOM::get_element( 'a', $li );

			$a->setAttribute( 'href', '#' );

			$block_content = $dom->saveHTML( $li );
		}

		return $block_content;
	}

}
