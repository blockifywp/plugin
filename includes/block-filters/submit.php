<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use function add_filter;
use function wp_enqueue_script;

add_filter( 'render_block_blockify/submit', NS . 'render_submit_block', 10, 2 );
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
function render_submit_block( string $content, array $block ): string {
	if ( $block['attrs']['recaptcha'] ?? null ) {
		wp_enqueue_script( 'blockify-google-recaptcha' );
	}

	return $content;
}
