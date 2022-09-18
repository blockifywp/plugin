<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use DOMElement;
use function add_filter;

add_filter( 'render_block_core/group', __NAMESPACE__ . '\\render_block_layout', 10, 2 );
/**
 * Modifies front end HTML output of block.
 *
 * @since 0.0.20
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function render_block_layout( string $content, array $block ): string {
	$dom = dom( $content );

	/**
	 * @var $first_child DOMElement
	 */
	$first_child= $dom->firstChild;

	if ( $first_child->tagName === 'main' ) {
		$first_child->setAttribute(
			'class',
			'wp-site-main ' . $first_child->getAttribute( 'class' )
		);
	}

	if ( $block['attrs']['minHeight'] ?? null ) {
		$first_child->setAttribute(
			'style',
			$first_child->getAttribute( 'style' ) . 'min-height:' . $block['attrs']['minHeight']
		);
	}

	return $dom->saveHTML();
}
