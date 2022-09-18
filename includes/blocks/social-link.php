<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use DOMElement;
use function add_filter;

add_filter( 'render_block_core/social-link', __NAMESPACE__ . '\\render_social_link_block', 10, 2 );
/**
 * Modifies front end HTML output of block.
 *
 * @since 0.0.24
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function render_social_link_block( string $content, array $block ): string {
	$textColor = $block['attrs']['textColor'] ?? null;

	if ( $textColor ) {
		$dom = dom( $content );

		/* @var $first_child DOMElement */
		$first_child= $dom->firstChild;

		if ( ! $first_child) {
			return $content;
		}

		$styles          = css_string_to_array( $first_child->getAttribute( 'style' ) );
		$styles['color'] = "var(--wp--preset--color--$textColor)";

		$first_child->setAttribute( 'style', css_array_to_string( $styles ) );

		$classes = explode( ' ', $first_child->getAttribute( 'class' ) );

		$classes[] = 'has-text-color';

		$first_child->setAttribute( 'class', implode( ' ', $classes ) );

		$content = $dom->saveHTML();
	}

	return $content;
}

