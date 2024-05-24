<?php

declare( strict_types=1 );

namespace Blockify\Framework\BlockSettings;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function array_unique;
use function esc_attr;
use function wp_get_global_settings;

/**
 * Shadow class.
 *
 * @since 1.0.0
 */
class TextShadow implements Renderable {

	/**
	 * Adds text shadow to blocks.
	 *
	 * @param string   $block_content The block content.
	 * @param array    $block         The block.
	 * @param WP_Block $instance      The block instance.
	 *
	 * @hook render_block
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$text_shadow = $block['attrs']['style']['textShadow'] ?? [];

		if ( ! $text_shadow ) {
			return $block_content;
		}

		$dom   = DOM::create( $block_content );
		$first = DOM::get_element( '*', $dom );

		if ( ! $first ) {
			return $block_content;
		}

		$first_classes = explode( ' ', $first->getAttribute( 'class' ) );

		$text_classes = array_unique( [
			...$first_classes,
			'has-text-shadow',
		] );

		$first->setAttribute( 'class', implode( ' ', $text_classes ) );

		$first_styles = CSS::string_to_array( $first->getAttribute( 'style' ) );

		$x     = $text_shadow['x'] ?? null;
		$y     = $text_shadow['y'] ?? null;
		$blur  = $text_shadow['blur'] ?? null;
		$color = $text_shadow['color'] ?? null;

		if ( $x ) {
			$first_styles['--wp--custom--text-shadow--x'] = esc_attr( $x ) . 'px';
		}

		if ( $y ) {
			$first_styles['--wp--custom--text-shadow--y'] = esc_attr( $y ) . 'px';
		}

		if ( $blur ) {
			$first_styles['--wp--custom--text-shadow--blur'] = esc_attr( $blur ) . 'px';
		}

		if ( $color ) {
			$palette = wp_get_global_settings()['color']['palette']['theme'] ?? [];

			$first_styles['--wp--custom--text-shadow--color'] = esc_attr( $color );

			foreach ( $palette as $theme_color ) {
				if ( $theme_color['color'] === $color ) {
					$first_styles['--wp--custom--text-shadow--color'] = "var(--wp--preset--color--{$theme_color['slug']})";
				}
			}
		}

		$first->setAttribute( 'style', CSS::array_to_string( $first_styles ) );

		return $dom->saveHTML();
	}

}
