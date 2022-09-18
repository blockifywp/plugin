<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use function add_filter;
use function str_replace;

add_filter( 'render_block_core/columns', __NAMESPACE__ . '\\render_columns_block', 10, 2 );
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
function render_columns_block( string $content, array $block ): string {
	$class = 'is-stacked-on-mobile';

	if ( isset( $block['attrs']['isStackedOnMobile'] ) && $block['attrs']['isStackedOnMobile'] === false ) {
		$class = 'is-not-stacked-on-mobile';
	}

	if ( 'is-stacked-on-mobile' === $class ) {
		$content = str_replace( 'wp-block-columns', 'wp-block-columns is-stacked-on-mobile', $content );
	}

	return $content;
}
