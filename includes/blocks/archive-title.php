<?php

declare( strict_types=1 );

namespace Blockify;

use function add_filter;
use function apply_filters;
use function is_search;
use function sprintf;

add_filter( 'render_block', __NAMESPACE__ . '\\render_archive_title_block', 10, 2 );
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
function render_archive_title_block( $content, $block ) {

	if ( 'core/query-title' !== $block['blockName'] ) {
		return $content;
	}

	if ( is_search() ) {

		$style = '';

		if ( isset( $block['attrs']['textAlign'] ) ) {
			$style .= 'text-align:' . $block['attrs']['textAlign'];
		}

		$content = sprintf(
			'<h1 class="%s" style="%s">%s</h1>',
			'wp-block-query-title',
			$style,
			apply_filters( 'blockify_search_results_title', __( 'Search results', 'blockify' ) )
		);
	}

	return $content;
}
