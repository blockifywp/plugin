<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use function add_filter;
use function basename;
use function get_option;
use function glob;
use function wp_json_file_decode;

add_filter( 'blockify_google_map_styles', __NAMESPACE__ . '\\add_google_maps_styles' );
/**
 * Add Google Maps styles.
 *
 * @since 1.0.0
 *
 * @param array $data Data.
 *
 * @return array
 */
function add_google_maps_styles( array $data ): array {
	$files = glob( get_cache_dir( 'maps' ) . '*.json' );

	if ( empty( $files ) ) {
		return $data;
	}

	$data['apiKey'] = get_option( 'blockify', [] )['googleMaps'] ?? '';

	if ( ! isset( $data['styles'] ) ) {
		$data['styles'] = [];
	}

	foreach ( $files as $file ) {
		$data['styles'][ basename( $file, '.json' ) ] = wp_json_file_decode( $file );
	}

	return $data;
}
