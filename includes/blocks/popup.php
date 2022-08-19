<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use DOMElement;
use function add_filter;
use function explode;
use function implode;

add_filter( 'render_block', NS . 'render_popup_block', 10, 2 );
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
function render_popup_block( string $content, array $block ): string {
	if ( 'blockify/popup' !== $block['blockName'] ) {
		return $content;
	}

	$dom = dom( $content );

	/**
	 * @var $first DOMElement
	 */
	$first   = $dom->getElementsByTagName( 'div' )[0];
	$classes = explode( ' ', $first->getAttribute( 'class' ) );
	$classes = implode( ' ', \array_unique( $classes ) );

	$first->setAttribute( 'class', $classes );

	$content = $dom->saveHTML();


	return $content;
}
