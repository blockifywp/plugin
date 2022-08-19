<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use DOMElement;
use function add_filter;

add_filter( 'render_block', NS . 'render_slider_block', 10, 2 );
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
function render_slider_block( string $content, array $block ): string {
	if ( 'blockify/slider' !== $block['blockName'] ) {
		return $content;
	}

	$dom = dom( $content );

	/**
	 * @var $div DOMElement
	 */
	$div = $dom->firstChild;

	$div->setAttribute( 'data-per-view', isset( $block['attrs']['perView'] ) ? (string) $block['attrs']['perView'] : '3' );

	$content = $dom->saveHTML();

	return $content;
}

