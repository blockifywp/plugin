<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use WP_Post;
use function add_filter;
use function array_merge;
use function get_stylesheet;
use function get_the_terms;
use function in_array;
use function str_contains;
use function wp_list_pluck;

/**
 * Returns a list of child theme slugs.
 *
 * @since 1.5.0
 *
 * @return string[]
 */
function get_child_themes(): array {
	return [
		'agencify',
		'blockify',
		'brandify',
		'codeify',
		'creatify',
		'launchify',
		'mintify',
		'saasify',
	];
}

add_filter( 'blockify_pattern_export_dir', __NAMESPACE__ . '\\patterns_export_dir', 10, 3 );
/**
 * Adds pro pattern export directory.
 *
 * @since 1.0.0
 *
 * @param string   $default_dir Filtered pattern directory.
 * @param ?WP_Post $post        Post object (optional).
 * @param string   $content     Replaced content (optional).
 *
 * @return string
 */
function patterns_export_dir( string $default_dir, ?WP_Post $post = null, string $content = '' ): string {
	$themes     = get_child_themes();
	$stylesheet = get_stylesheet();

	if ( ! in_array( $stylesheet, $themes, true ) ) {
		return $default_dir;
	}

	$pro_dir    = DIR . "patterns/$stylesheet";
	$categories = get_the_terms( $post, 'wp_pattern_category' );

	if ( in_array( 'pro', wp_list_pluck( $categories, 'slug' ), true ) ) {
		$default_dir = $pro_dir;
	}

	if ( str_contains( $content, 'wp:blockify/' ) ) {
		$default_dir = $pro_dir;
	}

	if ( str_contains( $post->post_title, 'Utility Dark Mode Toggle' ) ) {
		$default_dir = DIR . "patterns";
	}

	return $default_dir;
}

add_filter( 'blockify_pattern_dirs', __NAMESPACE__ . '\\add_pro_patterns' );
/**
 * Add Pro patterns.
 *
 * @since 1.0.0
 *
 * @param string[] $dirs The pattern directories.
 *
 * @return string[]
 */
function add_pro_patterns( array $dirs ): array {
	$dirs = array_merge(
		[
			CACHE_DIR . 'patterns/blockify',
		],
		$dirs
	);

	$dirs[] = CACHE_DIR . 'patterns/' . get_stylesheet();

	return $dirs;
}

add_filter( 'blockify_patterns', __NAMESPACE__ . '\\add_utility_patterns' );
/**
 * Adds utility patterns.
 *
 * @since 1.0.0
 *
 * @param array $categories The patterns.
 *
 * @return array
 */
function add_utility_patterns( array $categories ): array {
	$categories['utility']['dark-mode-toggle']          = DIR . 'patterns/utility/dark-mode-toggle.php';
	$categories['utility']['dark-mode-toggle-switch']   = DIR . 'patterns/utility/dark-mode-toggle-switch.php';
	$categories['utility']['dark-mode-toggle-dropdown'] = DIR . 'patterns/utility/dark-mode-toggle-dropdown.php';

	return $categories;
}
