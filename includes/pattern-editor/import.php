<?php

declare( strict_types=1 );

namespace Blockify\PatternEditor;

use WP_Block_Patterns_Registry;
use function admin_url;
use function explode;
use function get_page_by_title;
use function get_page_by_path;
use function str_replace;
use function ucwords;
use function wp_insert_term;
use function term_exists;
use function wp_safe_redirect;

add_action( 'admin_post_blockify_import_patterns', NS . 'import_patterns', 11 );
/**
 * Imports all registered patterns as posts.
 *
 * @since 1.0.0
 *
 * @return void
 */
function import_patterns() {
	$registered = WP_Block_Patterns_Registry::get_instance()->get_all_registered();

	foreach ( $registered as $pattern ) {
		$pattern = (object) $pattern;

		$category = $pattern->categories[0] ?? '';

		if ( $category === 'template' ) {
			continue;
		}

		if ( ! $category ) {
			[ $prefix ] = explode( '-', $pattern->name );
			$category = $prefix;
		}

		$args = [
			'post_name'    => $pattern->name,
			'post_title'   => $pattern->title,
			'post_content' => $pattern->content,
			'post_status'  => 'publish',
			'post_type'    => 'block_pattern',
			'tax_input'    => [
				'pattern_category' => [ $category ],
			],
		];

		if ( get_page_by_path( $pattern->name, OBJECT, 'block_pattern' ) ) {
			continue;
		}

		if ( get_page_by_title( $pattern->title, OBJECT, 'block_pattern' ) ) {
			continue;
		}

		if ( ! term_exists( $category ) ) {
			wp_insert_term(
				ucwords( str_replace( '-', ' ', $category ) ),
				'pattern_category',
				[
					'slug' => $category,
				]
			);
		}

		wp_insert_post( $args );
	}

	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	update_option( 'rewrite_rules', false );
	$wp_rewrite->flush_rules( true );

	wp_safe_redirect( admin_url( 'edit.php?post_type=block_pattern&blockify_import_patterns=true' ) );
}


add_action( 'admin_notices', NS . 'pattern_import_success_notice' );
/**
 * Admin notice for pattern import.
 *
 * @since 1.0.0
 *
 * @return void
 */
function pattern_import_success_notice() {
	$imported  = (bool) ( $_GET['blockify_import_patterns'] ?? false );
	$post_type = (string) ( $_GET['post_type'] ?? '' );

	if ( ! $imported || 'block_pattern' !== $post_type ) {
		return;
	}

	?>
	<div class="notice notice-success is-dismissible">
		<p><?php _e( 'Patterns successfully imported.', 'blockify-pro' ); ?></p>
	</div>
	<?php
}
