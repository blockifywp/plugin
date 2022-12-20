<?php

declare( strict_types=1 );

namespace Blockify\Theme;

use function add_filter;
use function str_replace;

add_filter( 'render_block_core/post-terms', NS . 'render_post_terms_block', 10, 2 );
/**
 * Modifies front end HTML output of block.
 *
 * @since 0.0.1
 *
 * @param string $html Block HTML.
 * @param array  $block   Block data.
 *
 * @return string
 */
function render_post_terms_block( string $html, array $block ): string {
	if ( $block['attrs']['align'] ?? null ) {
		$html = str_replace(
			[
				'wp-block-post-terms',
				'rel="tag"',
			],
			[
				'wp-block-post-terms flex justify-' . $block['attrs']['align'],
				'class="wp-block-post-terms__link" rel="tag"',
			],
			$html
		);
	}

	return $html;
}

