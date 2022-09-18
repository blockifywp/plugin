<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use function add_filter;

add_filter( 'render_block_core/post-featured-image', __NAMESPACE__ . '\\render_featured_image_block', 10, 2 );
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
function render_featured_image_block( string $content, array $block ): string {

	if ( ! $content ) {
		$attrs = $block['attrs'];

		$css = '';

		if ( $attrs['style']['spacing']['margin']['bottom'] ?? null ) {
			$css .= 'margin-bottom:' . $attrs['style']['spacing']['margin']['bottom'] . ';';
		}

		$content = '<figure class="wp-block-image is-placeholder" style="' . $css . '"><svg class="wp-block-image__placeholder" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60" preserveAspectRatio="none"><path vector-effect="non-scaling-stroke" d="M60 60 0 0"></path></svg></figure>';
	}

	return $content;
}
