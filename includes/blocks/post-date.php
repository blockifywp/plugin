<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use DOMElement;
use function add_filter;

add_filter( 'render_block_core/post-date', __NAMESPACE__ . '\\render_post_date', 10, 2 );
/**
 * Adds block supports to the core post date block.
 *
 * @since 0.0.1
 *
 * @param string $block_content The block content.
 * @param array  $block         The block.
 *
 * @return string
 */
function render_post_date( string $content, array $block ): string {
	$margin = $block['attrs']['style']['spacing']['margin'] ?? null;

	if ( $margin ) {
		$dom = dom( $content );

		/* @var DOMElement $first_child*/
		$first_child= $dom->firstChild;

		$styles = [
			'margin-top'    => $margin['top'] ?? null,
			'margin-right'  => $margin['right'] ?? null,
			'margin-bottom' => $margin['bottom'] ?? null,
			'margin-left'   => $margin['left'] ?? null,
		];

		$first_child->setAttribute( 'style', css_array_to_string( $styles ) . $first_child->getAttribute( 'style' ) );

		$content = $dom->saveHTML();
	}

	return $content;
}
