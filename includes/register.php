<?php

declare( strict_types=1 );

namespace Blockify;

use const GLOB_ONLYDIR;
use WP_Block_Pattern_Categories_Registry;
use WP_Block_Patterns_Registry;
use function __;
use function add_action;
use function apply_filters;
use function array_merge;
use function basename;
use function glob;
use function in_array;
use function register_block_type;
use function register_block_pattern_category;
use function ucwords;
use function wp_list_pluck;

add_action( 'init', NS . 'register_block_types' );
/**
 * Registers all block types.
 *
 * @since 0.0.2
 *
 * @return void
 */
function register_block_types(): void {
	$block_types = [];

	foreach ( glob( DIR . 'build/blocks/*', GLOB_ONLYDIR ) as $dir ) {
		$block_types[] = basename( $dir );
	}

	$block_types = apply_filters( 'blockify_blocks', $block_types );

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
			'slug'  => 'blockify-newsletter',
			'title' => __( 'Newsletter', 'blockify' ),
		],
	], $categories );

	return array_merge(
		$categories,
		[
			[
				'slug'  => SLUG,
				'title' => ucwords( SLUG ),
			],
		]
	);
}

add_action( 'init', NS . 'register_root_level_pattern_categories', 11 );
/**
 * Generates categories for patterns automatically registered by core.
 *
 * @since 0.0.2
 *
 * @return void
 */
function register_root_level_pattern_categories(): void {
	$block_patterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();

	foreach ( $block_patterns as $block_pattern ) {
		if ( ! isset( $block_pattern['categories'] ) ) {
			continue;
		}

		foreach ( $block_pattern['categories'] as $category ) {
			$categories = wp_list_pluck( WP_Block_Pattern_Categories_Registry::get_instance()->get_all_registered(), 'name' );

			if ( in_array( $category, $categories ) ) {
				continue;
			}

			register_block_pattern_category( $category, [
				'label' => ucfirst( $category ),
			] );
		}
	}
}
