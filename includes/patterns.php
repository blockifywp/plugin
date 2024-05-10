<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use Blockify\Utilities\Str;
use WP_Post;
use function add_filter;
use function array_merge;
use function get_stylesheet;
use function get_the_terms;
use function in_array;
use function register_block_pattern;
use function sprintf;
use function str_contains;
use function wp_json_file_decode;
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
	if ( ! is_license_active() ) {
		return $dirs;
	}

	$patterns_dir = get_cache_dir( 'patterns' );

	$dirs = array_merge(
		[
			$patterns_dir . 'blockify',
		],
		$dirs
	);

	$dirs[] = $patterns_dir . get_stylesheet();

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


add_action( 'init', __NAMESPACE__ . '\\add_placeholder_patterns', 11 );
/**
 * Adds placeholder patterns.
 *
 * @since 1.5.0
 *
 * @return void
 */
function add_placeholder_patterns(): void {
	if ( is_license_active() ) {
		return;
	}

	$patterns_json_file = DIR . 'patterns.json';

	if ( ! file_exists( $patterns_json_file ) ) {
		return;
	}

	$html = <<<HTML
<!-- wp:group {"align":"full","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull">
<!-- wp:image {"sizeSlug":"full","align":"full","style":{"minWidth":{"all":"100vw"}}} -->
<figure class="wp-block-image alignfull size-full">
<img src="https://blockifywp.com/wp-content/themes/blockifywp/screenshots/%s/pro/%s.webp" alt="%s"/>
</figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
HTML;

	$stylesheet    = get_stylesheet();
	$patterns_json = wp_json_file_decode( $patterns_json_file );

	foreach ( $patterns_json as $theme => $patterns ) {
		if ( $theme !== $stylesheet ) {
			continue;
		}

		foreach ( $patterns as $pattern ) {
			$category = explode( '-', $pattern )[0] ?? 'text';

			$content = sprintf(
				$html,
				$theme,
				$pattern,
				Str::title_case( $theme . ' ' . $pattern )
			);

			register_block_pattern(
				"$theme/$pattern-pro",
				[
					'title'       => Str::title_case( $theme . ' ' . $pattern ) . ' (Pro)',
					'content'     => $content,
					'categories'  => [ $category ],
					'description' => 'This is a placeholder pattern. Activate your license to unlock the full pattern.',
				]
			);
		}
	}
}
