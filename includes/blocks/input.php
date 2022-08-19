<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use function add_filter;
use function str_contains;
use function str_replace;

add_filter( 'render_block', NS . 'render_input_block', 10, 2 );
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
function render_input_block(  string $content, array $block ): string {
	if ( ! isset( $block['blockName'] ) || ! str_contains( $block['blockName'], 'blockify/' ) ) {
		return $content;
	}

	$required = isset( $block['attrs']['isRequired'] ) && $block['attrs']['isRequired'];

	if ( $required ) {
		$content = str_replace(
			'data-required',
			'required',
			$content
		);
	}

	return $content;
}
