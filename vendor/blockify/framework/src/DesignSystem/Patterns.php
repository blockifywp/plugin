<?php

declare( strict_types=1 );

namespace Blockify\Framework\DesignSystem;

use Blockify\Framework\Interfaces\Renderable;
use Blockify\Utilities\Debug;
use Blockify\Utilities\Pattern;
use WP_Block;
use WP_Block_Patterns_Registry;
use function apply_filters;
use function array_unique;
use function basename;
use function do_blocks;
use function get_stylesheet_directory;
use function glob;
use function in_array;
use function is_dir;
use function ksort;
use function register_block_pattern_category;
use function remove_theme_support;
use function str_replace;
use function strtoupper;
use function trailingslashit;
use function ucwords;

/**
 * Patterns extension.
 *
 * @since 0.0.2
 */
class Patterns implements Renderable {

	/**
	 * Removes core block patterns.
	 *
	 * @since 0.0.2
	 *
	 * @hook  init 9
	 *
	 * @return void
	 */
	function remove_core_patterns(): void {
		remove_theme_support( 'core-block-patterns' );
	}

	/**
	 * Manually registers default patterns to avoid loading in child themes.
	 *
	 * @since 1.0.1
	 *
	 * @hook  init
	 *
	 * @return void
	 */
	public function register_default_patterns(): void {
		$all_dirs   = $this->get_pattern_dirs();
		$categories = [];

		foreach ( $all_dirs as $dir ) {
			$files    = glob( $dir . '/*.php' );
			$category = basename( $dir );

			foreach ( $files as $file ) {
				$pattern = basename( $file, '.php' );

				if ( ! isset( $categories[ $category ] ) ) {
					$categories[ $category ] = [];
				}

				$categories[ $category ][ $pattern ] = $file;
			}
		}

		$categories = apply_filters( 'blockify_patterns', $categories );

		ksort( $categories );

		$registered_categories = [];
		$registered_slugs      = [];
		$all_patterns          = WP_Block_Patterns_Registry::get_instance()->get_all_registered() ?? [];

		foreach ( $all_patterns as $pattern ) {
			$registered_slugs[] = $pattern['slug'] ?? '';
		}

		foreach ( $categories as $category => $patterns ) {

			if ( ! in_array( $category, $registered_categories, true ) ) {

				if ( in_array( $category, [ 'cta', 'faq' ], true ) ) {
					$label = strtoupper( $category );
				} else {
					$label = ucwords( str_replace( '-', ' ', $category ) );
				}

				register_block_pattern_category(
					$category,
					[
						'label' => $label,
					]
				);

				$registered_categories[ $category ] = [];
			}

			foreach ( $patterns as $pattern => $file ) {
				$basename = basename( $file, '.php' );

				if ( in_array( $basename, $registered_categories[ $category ], true ) ) {
					continue;
				}

				$registered_categories[ $category ][] = $basename;

				$slug = $category . '-' . $basename;

				if ( in_array( $slug, $registered_slugs, true ) ) {
					continue;
				}

				Pattern::register_from_file( $file );
			}
		}
	}

	/**
	 * Renders the block.
	 *
	 * @param string   $block_content Block content.
	 * @param array    $block         Block attributes.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook render_block_core/pattern
	 * @hook render_block_blockify/pattern
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$slug = $block['attrs']['slug'] ?? '';
		$name = $block['blockName'] ?? '';

		if ( ! $block_content && $name === 'blockify/pattern' ) {
			$block_content = do_blocks( "<!-- wp:pattern {\"slug\":\"$slug\"} /-->" );
		}

		if ( ! Debug::is_enabled() || ! $slug ) {
			return $block_content;
		}

		return <<<HTML
<!-- $slug -->
	$block_content
<!-- /$slug -->
HTML;
	}

	/**
	 * Returns array of pattern directories.
	 *
	 * @since 0.0.2
	 *
	 * @return array
	 */
	private function get_pattern_dirs(): array {
		$template_dir   = get_template_directory();
		$stylesheet_dir = get_stylesheet_directory();
		$default_dirs   = [];
		$default_dirs[] = $stylesheet_dir . '/patterns';

		$dirs = apply_filters( 'blockify_pattern_dirs', $default_dirs );

		$category_dirs = [];

		// Register parent template patterns.
		if ( $template_dir !== $stylesheet_dir ) {
			$category_dirs[] = $template_dir . '/patterns/template';
		}

		foreach ( $dirs as $dir ) {
			$dir = trailingslashit( $dir );

			if ( ! is_dir( $dir ) ) {
				continue;
			}

			$category_dirs = [
				...$category_dirs,
				...glob( $dir . '*', GLOB_ONLYDIR ),
			];
		}

		return array_unique( $category_dirs );
	}

}
