<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use DOMElement;
use function add_filter;
use function file_exists;
use function file_get_contents;

add_filter( 'render_block_core/site-logo', __NAMESPACE__ . '\\render_site_logo_block', 10, 2 );
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
function render_site_logo_block( string $content, array $block ): string {
	if ( ! $content ) {
		$dom = dom( get_default_icon() );

		/** @var DOMElement $svg */
		$svg = $dom->firstChild;

		$svg->setAttribute( 'class', 'blockify-icon' );
		$svg->setAttribute( 'width', '30' );
		$svg->setAttribute( 'height', '30' );
		$svg->setAttribute( 'title', 'blockify' );

		$paths = $svg->getElementsByTagName( 'path' );

		if ( $paths->item( 0 ) ) {

			/** @var DOMElement $path */
			$path = $paths->item( 0 );

			$path->setAttribute( 'fill', 'currentColor' );
		}

		$styles = css_string_to_array( $svg->getAttribute( 'style' ) );
		$styles = add_block_support_color( $styles, $block['attrs'] );
		$svg->setAttribute( 'style', css_array_to_string( $styles ) );

		$content = $dom->saveHTML();
	}


	return $content;
}

