<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use function add_action;
use function esc_html;
use function get_bloginfo;
use function is_archive;
use function is_plugin_active;
use function is_singular;
use function printf;

add_action( 'wp_head', __NAMESPACE__ . '\fallback_meta_description', 2 );
/**
 * Fallback meta description if no seo plugins are active.
 *
 * @since 1.0.0
 *
 * @return void
 */
function fallback_meta_description() {
	$seo_plugins = [
		'wordpress-seo/wp-seo.php',
		'all-in-one-seo-pack/all_in_one_seo_pack.php',
		'seo-by-rank-math/rank-math.php',
		'autodescription/autodescription.php', // The SEO Framework.
		'wp-seopress/seopress.php',
	];

	foreach ( $seo_plugins as $plugin ) {
		if ( is_plugin_active( $plugin ) ) {
			return;
		}
	}

	$description = get_bloginfo( 'description' );

	if ( ! $description ) {
		$description = get_bloginfo( 'name' );
	}

	if ( is_singular() ) {
		$excerpt     = get_the_excerpt();
		$description = $excerpt ?: $description;
	}

	if ( is_archive() ) {
		$archive_description = get_the_archive_description();
		$description         = $archive_description ?: $description;
	}

	printf(
		'<meta name="description" content="%s">',
		esc_html( $description )
	);
}
