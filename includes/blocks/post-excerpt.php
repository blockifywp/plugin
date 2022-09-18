<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use function add_filter;
use function get_option;
use function has_excerpt;
use function is_page;
use function str_replace;

add_filter( 'render_block_core/post-excerpt', __NAMESPACE__ . '\\render_excerpt_block', 10, 2 );
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
function render_excerpt_block( string $content, array $block ): string {
	if ( is_page() && ! has_excerpt() ) {
		$content = '';
	}

	return $content;
}

add_filter( 'excerpt_length', __NAMESPACE__ . '\\set_excerpt_length', 99 );
/**
 * Filters the excerpt length for posts.
 *
 * @since 0.2.0
 *
 * @return int
 */
function set_excerpt_length(): int {
	return (int) ( get_option( 'blockify' )['excerptLength'] ?? 33);
}

add_filter( 'excerpt_more', __NAMESPACE__ . '\\remove_brackets_from_excerpt' );
/**
 * Removes brackets from excerpt more string.
 *
 * @since 0.0.1
 *
 * @param string $more
 *
 * @return string
 */
function remove_brackets_from_excerpt( string $more ): string {
	return str_replace( [ '[', ']' ], '', $more );
}
