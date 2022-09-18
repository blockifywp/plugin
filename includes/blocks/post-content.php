<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use DOMElement;
use function add_filter;
use function explode;
use function implode;
use function is_a;

add_filter( 'render_block_core/post-content', __NAMESPACE__ . '\\render_post_content_block', 10, 2 );
/**
 * Modifies front end HTML output of block.
 *
 * @since 0.0.1
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function render_post_content_block( string $content, array $block ): string {
	$margin  = $block['attrs']['style']['spacing']['margin'] ?? [];
	$padding = $block['attrs']['style']['spacing']['padding'] ?? [];

	if ( ! empty( $margin ) || ! empty( $padding ) ) {
		$dom = dom( $content );

		/** @var DOMElement $first_child */
		$first_child = $dom->firstChild;

		if ( ! is_a( $first_child, DOMElement::class ) ) {
			return $content;
		}

		$styles   = [];
		$original = $first_child->getAttribute( 'style' );

		if ( $original ) {
			foreach ( explode( ';', $original ) as $rule ) {
				$styles[] = $rule;
			}
		}

		foreach ( $margin as $key => $value ) {
			$styles[] = "margin-$key:$value";
		}

		foreach ( $padding as $key => $value ) {
			$styles[] = "padding-$key:$value";
		}

		$first_child->setAttribute( 'style', implode( ';', $styles ) );

		$content = $dom->saveHTML();

	}

	return $content;
}

