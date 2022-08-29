<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use const GLOB_ONLYDIR;
use function __;
use function add_action;
use function apply_filters;
use function array_map;
use function array_merge;
use function basename;
use function register_block_type;

add_action( 'init', NS . 'register_block_types' );
/**
 * Registers all block types.
 *
 * @since 0.0.2
 *
 * @return void
 */
function register_block_types(): void {
	$block_types = apply_filters( 'blockify_block_types', array_map(
		fn( $dir ) => basename( $dir ),
		glob( DIR . 'build/blocks/*', GLOB_ONLYDIR )
	) );

	foreach ( $block_types as $block_type ) {
		register_block_type( DIR . 'build/blocks/' . $block_type );
	}
}

add_filter( 'block_categories_all', NS . 'register_block_categories' );
/**
 * Registers block categories.
 *
 * @since 0.0.2
 *
 * @param array $categories
 *
 * @return array
 */
function register_block_categories( array $categories ): array {
	$categories = array_merge( [
		[
			'slug'  => 'blockify-form',
			'title' => __( 'Form', 'blockify' ),
		],
	], $categories );

	return array_merge(
		$categories,
		[
			[
				'slug'  => 'blockify',
				'title' => 'Blockify',
			],
		]
	);
}
