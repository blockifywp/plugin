<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use DOMElement;
use function add_filter;
use function str_contains;

add_filter( 'render_block_core/image', __NAMESPACE__ . '\\render_image_block', 10, 2 );
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
function render_image_block( string $content, array $block ): string {
	$url   = $blockp['attrs']['url'] ?? '';
	$class = $block['attrs']['className'] ?? '';

	// Add support for image placeholders on front end.
	if ( ! $url && ! str_contains( 'is-style-icon', $class ) ) {
		$styles = css_array_to_string( add_block_support_color( [], $block['attrs'] ) );

		$content = '<figure class="wp-block-image is-placeholder"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60" preserveAspectRatio="none" class="wp-block-image__placeholder" style="' . $styles . '"><path vector-effect="non-scaling-stroke" d="M60 60 0 0"></path></svg></figure>';
	}

	return $content;
}
