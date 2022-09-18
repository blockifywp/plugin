<?php
/**
 * Plugin Name: Blockify
 * Plugin URI:  https://blockifywp.com/
 * Description: Block toolkit for WordPress full site editing.
 * Author:      Blockify
 * Author URI:  https://blockifywp.com/about/
 * Version:     0.4.0
 * License:     GPLv2-or-Later
 * Text Domain: blockify
 */

declare( strict_types=1 );

namespace Blockify\Plugin;

use const PHP_VERSION;
use function add_action;
use function array_map;
use function function_exists;
use function glob;
use function version_compare;

if ( ! version_compare( '7.4.0', PHP_VERSION, '<=' ) ) {
	return;
}

if ( ! function_exists( __NAMESPACE__ . '\\register' ) ) {
	add_action( 'after_setup_theme', __NAMESPACE__ . '\\register' );
	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function register() {
		array_map(
			fn( $file ) => require_once $file,
			[
				...glob( __DIR__ . '/includes/utility/*.php' ),
				...glob( __DIR__ . '/includes/*.php' ),
				...glob( __DIR__ . '/includes/blocks/*.php' ),
				...glob( __DIR__ . '/includes/extensions/*.php' ),
			]
		);
	}
}
