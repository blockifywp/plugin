<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use function dirname;
use function get_template_directory;
use function is_null;
use function json_decode;
use function trailingslashit;

/**
 * Returns path to cache dir.
 *
 * @since 1.5.0
 *
 * @param string $sub_dir Subdirectory.
 *
 * @return string
 */
function get_cache_dir( string $sub_dir = '' ): string {
	static $dir = null;

	if ( is_null( $dir ) ) {
		$dir = dirname( get_template_directory(), 2 ) . '/cache/blockify/';
	}

	return $sub_dir ? $dir . trailingslashit( $sub_dir ) : $dir;
}

/**
 * Checks if license is active.
 *
 * @since 1.5.0
 *
 * @return bool
 */
function is_license_active(): bool {
	static $is_active = null;

	if ( is_null( $is_active ) ) {
		$options   = json_decode( get_option( 'blockify_license_data', '' ), true );
		$is_active = 'active' === ( $options['data']['license_key']['status'] ?? 'inactive' );
	}

	return $is_active;
}
