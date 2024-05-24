<?php

declare( strict_types=1 );

namespace Blockify\Framework\CoreBlocks;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use DOMElement;
use WP_Block;
use function esc_attr;
use function trim;
use function wp_get_global_settings;

/**
 * SocialLinks class.
 *
 * @since 1.0.0
 */
class SocialLinks implements Renderable {

	/**
	 * Modifies front end HTML output of block.
	 *
	 * @since 0.0.24
	 *
	 * @param string   $block_content Block HTML.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook  render_block_core/social-links
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$dom = DOM::create( $block_content );
		$ul  = DOM::get_element( 'ul', $dom );

		if ( ! $ul || ! $ul->hasChildNodes() ) {
			return $block_content;
		}

		$global_settings = wp_get_global_settings();
		$color_palette   = $global_settings['color']['palette']['theme'] ?? [];

		foreach ( $ul->childNodes as $child ) {
			if ( ! $child instanceof DOMElement ) {
				continue;
			}

			if ( $child->nodeName === 'li' ) {
				$styles = CSS::string_to_array( $child->getAttribute( 'style' ) );

				if ( ! ( $styles['color'] ?? null ) ) {
					continue;
				}

				foreach ( $color_palette as $color ) {
					$hex = $color['color'] ?? '';

					if ( trim( $styles['color'] ) === trim( $hex ) ) {
						$slug = esc_attr( $color['slug'] ?? '' );

						if ( ! $slug ) {
							continue;
						}

						$styles['color'] = "var(--wp--preset--color--$slug)";
						$child->setAttribute( 'style', CSS::array_to_string( $styles ) );

						break;
					}
				}

				$child->setAttribute( 'style', CSS::array_to_string( $styles ) );
			}
		}

		return $dom->saveHTML();
	}
}
