<?php

declare( strict_types=1 );

namespace Blockify;

use WP_Screen;
use function add_action;
use function basename;
use function get_file_data;
use function ob_get_clean;
use function ob_start;
use function str_replace;
use function wp_get_global_settings;
use function get_template_directory;
use function register_block_pattern;
use function register_block_pattern_category;

add_action( 'template_redirect', NS . 'register_patterns_front_end' );
/**
 * Registers patterns on front end to support pattern block.
 *
 * @since 1.0.0
 *
 * @return void
 */
function register_patterns_front_end() {
	$settings         = wp_get_global_settings();
	$active_variation = $settings['custom']['variation'] ?? 'default';

	register_block_patterns( 'default' );

	if ( $active_variation !== 'default' ) {
		register_block_patterns( $active_variation );
	}
}

/**
 * Conditionally registers block patterns depending on active style variation.
 *
 * @since 0.0.5
 *
 * @param string $style_variation Style variation patterns to register.
 *
 * @return void
 */
function register_block_patterns( string $style_variation = 'default' ): void {
	$patterns   = [];
	$categories = [];
	$dir        = get_template_directory() . '/patterns/' . $style_variation . DS;

	foreach ( glob( $dir . '*.php' ) as $file ) {
		$headers = get_file_data( $file, [
			'categories'  => 'Categories',
			'title'       => 'Title',
			'slug'        => 'Slug',
			'block_types' => 'Block Types',
		] );

		$slug     = $headers['slug'];
		$category = str_replace(
			'blockify/',
			'',
			$headers['categories']
		);

		ob_start();
		include $file;
		$content = ob_get_clean();

		$patterns[ $slug ] = [
			'title'      => $headers['title'],
			'content'    => $content,
			'categories' => [ $category ],
		];

		if ( $headers['block_types'] ) {
			$patterns[ $slug ]['blockTypes'] = $headers['block_types'];
		}

		$categories[ $category ] = [
			'label' => ucwords( $category ),
		];
	}

	foreach ( $categories as $category_name => $args ) {
		register_block_pattern_category( $category_name, $args );
	}

	foreach ( $patterns as $pattern_name => $args ) {
		register_block_pattern( $pattern_name, $args );
	}
}

add_action( 'current_screen', NS . 'register_block_patterns_editor' );
/**
 * Conditionally registers block patterns in site editor.
 *
 * @since 0.0.5
 *
 * @param WP_Screen $current_screen Current admin page to check.
 *
 * @return void
 */
function register_block_patterns_editor( WP_Screen $current_screen ): void {
	$site_editor = $current_screen->base === 'appearance_page_gutenberg-edit-site' || $current_screen->base === 'site-editor';

	// Registers all block patterns in site editor to allow switching.
	if ( $site_editor ) {
		foreach ( \glob( get_template_directory() . '/patterns/*', \GLOB_ONLYDIR ) as $dir ) {
			register_block_patterns( basename( $dir ) );
		}
	} else {
		$settings         = wp_get_global_settings();
		$active_variation = $settings['custom']['variation'] ?? 'default';

		register_block_patterns( $active_variation );
	}
}

add_action( 'after_setup_theme', NS . 'register_root_level_pattern_categories' );
/**
 * Generates categories for patterns automatically registered by core.
 *
 * @since 0.0.2
 *
 * @return void
 */
function register_root_level_pattern_categories(): void {
	$block_patterns = glob( get_template_directory() . '/patterns/*.php' );
	$categories     = [];

	foreach ( $block_patterns as $file ) {
		$category = get_file_data( $file, [
			'Categories' => 'Categories',
		] )['Categories'];

		$categories[ strtolower( $category ) ] = [
			'label' => ucfirst( $category ),
		];
	}

	foreach ( $categories as $category => $args ) {
		register_block_pattern_category( $category, $args );
	}
}
