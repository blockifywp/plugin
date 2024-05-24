<?php

declare( strict_types=1 );

namespace Blockify\Framework\CoreBlocks;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function implode;

/**
 * NavigationSubmenu class.
 *
 * @since 1.0.0
 */
class NavigationSubmenu implements Renderable {

	/**
	 * Modifies front end HTML output of block.
	 *
	 * @todo  Doesn't work.
	 *
	 * @since 0.0.2
	 *
	 * @param string   $block_content Block HTML.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook  render_block_core/navigation-submenu
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$dom     = DOM::create( $block_content );
		$attrs   = $block['attrs'] ?? [];
		$style   = $attrs['style'] ?? [];
		$spacing = $style['spacing'] ?? [];
		$padding = $spacing['padding'] ?? [];
		$margin  = $spacing['margin'] ?? [];
		$color   = $style['color'] ?? [];
		$styles  = [];

		if ( isset( $color['background'] ) ) {
			$styles['--wp--custom--submenu--background'] = $color['background'];
		}

		if ( isset( $attrs['backgroundColor'] ) ) {
			$styles['--wp--custom--submenu--background'] = 'var(--wp--preset--color--' . $attrs['backgroundColor'] . ')';
		}

		if ( isset( $color['text'] ) ) {
			$styles['--wp--custom--submenu--color'] = $color['text'];
		}

		if ( isset( $attrs['textColor'] ) ) {
			$styles['--wp--custom--submenu--color'] = 'var(--wp--preset--color--' . $attrs['textColor'] . ')';
		}

		$padding = implode(
			' ',
			[
				$padding['top'] ?? 0,
				$padding['right'] ?? 0,
				$padding['bottom'] ?? 0,
				$padding['left'] ?? 0,
			]
		);

		if ( $padding !== '0 0 0 0' ) {
			$styles['--wp--custom--submenu--padding'] = CSS::format_custom_property( $padding );
		}

		$margin = implode(
			' ',
			[
				$margin['top'] ?? 0,
				$margin['right'] ?? 0,
				$margin['bottom'] ?? 0,
				$margin['left'] ?? 0,
			]
		);

		if ( $margin !== '0 0 0 0' ) {
			$styles['--wp--custom--submenu--margin'] = CSS::format_custom_property( $margin );
		}

		$block_gap = $spacing['blockGap'] ?? null;

		if ( $block_gap ) {
			$styles['--wp--custom--submenu--gap'] = CSS::format_custom_property( $block_gap );
		}

		$submenu = DOM::get_element( 'ul', $dom );

		if ( ! $submenu ) {
			return $block_content;
		}

		$submenu_style = $submenu->getAttribute( 'style' );
		$css           = $submenu_style ? $submenu_style . ';' : '';

		foreach ( $styles as $property => $value ) {
			$css .= $value ? "$property:$value;" : '';
		}

		$submenu->setAttribute( 'style', $css );

		return $dom->saveHTML();
	}

}
