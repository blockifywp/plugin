<?php

declare( strict_types=1 );

namespace Blockify;

use DOMElement;
use function str_contains;

add_filter( 'render_block', NS . 'render_accordion_block', 10, 2 );
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
function render_accordion_block( string $content, array $block ): string {
	if ( 'blockify/accordion' !== $block['blockName'] ) {
		return $content;
	}

	$dom = dom( $content );

	/**
	 * @var $div DOMElement
	 */
	$div = $dom->getElementsByTagName( 'div' )->item( 0 );

	$div->setAttribute( 'class', 'wp-block-blockify-accordion' );

	return $dom->saveHTML();
}


add_filter( 'render_block', NS . 'render_accordion_item_block', 10, 2 );
/**
 * Modifies front end HTML output of child blocks.
 *
 * @since 0.0.2
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function render_accordion_item_block( string $content, array $block ): string {
	if ( 'blockify/accordion-item' !== $block['blockName'] ) {
		return $content;
	}

	if ( isset( $block['attrs']['className'] ) && str_contains( $block['attrs']['className'], 'is-style-open' ) ) {

		$dom = dom( $content );

		/**
		 * @var $details DOMElement
		 */
		$details = $dom->firstChild;

		$details->setAttribute( 'open', '' );

		$content = $dom->saveHTML();
	}

	return $content;
}

