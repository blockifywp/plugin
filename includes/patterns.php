<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use function _cleanup_header_comment;
use function add_action;
use function explode;
use function file_exists;
use function get_file_data;
use function ob_get_clean;
use function ob_start;
use function preg_match;
use function register_block_pattern;
use function register_block_pattern_category;
use function str_contains;
use function str_replace;
use function ucwords;
use function get_template_directory;

add_action( 'init', __NAMESPACE__ . '\\register_patterns' );

function register_patterns(): void {
	$patterns = \glob( get_template_directory() . '/patterns/*.php' );

	foreach ( $patterns as $file ) {
		register_block_pattern_from_file( $file );
	}
}

/**
 * Parses and registers block pattern from PHP file with header comment.
 *
 * @since 0.0.8
 *
 * @param string $file Path to PHP file.
 *
 * @return void
 */
function register_block_pattern_from_file( string $file ): void {
	$content = $file;

	if ( file_exists( $file ) ) {
		$headers = get_file_data( $file, [
			'categories'  => 'Categories',
			'title'       => 'Title',
			'slug'        => 'Slug',
			'block_types' => 'Block Types',
		] );

		ob_start();
		include $file;
		$content = ob_get_clean();

	} else if ( str_contains( $file, 'Title: ' ) ) {
		$headers = [
			'title'       => 'Title',
			'slug'        => 'Slug',
			'categories'  => 'Categories',
			'block_types' => 'Block Types',
		];

		// @see get_file_data().
		foreach ( $headers as $field => $regex ) {
			if ( preg_match( '/^(?:[ \t]*<\?php)?[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file, $match ) && $match[1] ) {
				$headers[ $field ] = _cleanup_header_comment( $match[1] );
			} else {
				$headers[ $field ] = '';
			}
		}
	}

	$categories = explode( ',', $headers['categories'] );

	$pattern = [
		'title'      => $headers['title'],
		'content'    => str_replace(
			str_between( '<?php', '?>', $content ),
			'',
			$content
		),
		'categories' => [ ...$categories ],
	];

	if ( $headers['block_types'] ) {
		$pattern['blockTypes'] = $headers['block_types'];
	}

	foreach ( $categories as $category ) {
		register_block_pattern_category( $category, [
			'label' => ucwords( $category ),
		] );
	}

	register_block_pattern( $headers['slug'], $pattern );
}

add_action( 'template_redirect', __NAMESPACE__ . '\\register_patterns_front_end' );
/**
 * Registers block patterns on front end.
 *
 * @since 0.0.1
 *
 * @return void
 */
function register_patterns_front_end(): void {
	foreach ( glob( get_template_directory() . '/patterns/*.php' ) as $file ) {
		register_block_pattern_from_file( $file );
	}
}

add_filter( 'template_include', __NAMESPACE__ . '\\archive_block_pattern_template' );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @param $template
 *
 * @return mixed|string
 */
function archive_block_pattern_template( string $template ): string {
	if ( \is_post_type_archive( 'block_pattern' ) ) {
		$template = locate_block_template( get_dir() . 'templates/patterns.html', 'patterns', [] );
	}

	return $template;
}

add_filter( 'template_include', __NAMESPACE__ . '\\single_block_pattern_template' );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @param $template
 *
 * @return mixed|string
 */
function single_block_pattern_template( string $template ): string {
	if ( is_singular( 'block_pattern' ) ) {
		$template = locate_block_template( get_dir() . 'templates/blank.html', 'blank', [] );
	}

	return $template;
}
