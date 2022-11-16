<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use DOMElement;
use WP_Theme_JSON_Resolver;
use function add_filter;
use function str_replace;

add_filter( 'render_block_blockify/icon', NS . 'render_icon_block', 10, 2 );
/**
 * Modifies front end HTML output of block.
 *
 * @since 0.0.2
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function render_icon_block( string $content, array $block ): string {
	if ( ! $content ) {
		return $content;
	}

	$dom = dom( $content );

	/**
	 * @var DOMElement $div
	 */
	$div       = $dom->getElementsByTagName( 'div' )->item( 0 );
	$container = $div->firstChild;
	$classes   = $div->getAttribute( 'class' );
	$classes   .= ' ' . $container->getAttribute( 'class' );

	if ( isset( $block['attrs']['layout']['justifyContent'] ) ) {
		$classes .= ' items-justified-' . $block['attrs']['layout']['justifyContent'];
	}

	$div->setAttribute( 'class', trim( $classes ) );
	$div->setAttribute( 'style', $container->getAttribute( 'style' ) );

	$mask = $container->firstChild;

	if ( ! $mask ) {
		return $content;
	}

	$style        = $mask->getAttribute( 'style' );
	$css          = css_string_to_array( $style );
	$theme_json   = WP_Theme_JSON_Resolver::get_merged_data( '' );
	$palette      = $theme_json->get_settings()['color']['palette'];
	$mask_classes = $mask->getAttribute( 'class' );

	if ( isset( $css['background'] ) ) {
		$hex = $css['background'];

		foreach ( $palette as $color ) {
			if ( isset( $color['color'] ) && $hex === $color['color'] ) {
				$mask_classes .= ' has-' . $color['slug'] . '-background-color';
			}
		}
	}

	$mask->setAttribute( 'class', $mask_classes );
	$div->appendChild( $mask );
	$div->removeChild( $container );

	return str_replace( 'fill="currentColor"', ' ', $dom->saveHTML() );
}
