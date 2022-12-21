<?php
/**
 * Plugin Name:  Blockify
 * Plugin URI:   https://blockifywp.com/
 * Description:  Blockify full site editing theme toolkit.
 * Author:       Blockify
 * Author URI:   https://blockifywp.com/
 * Version:      0.6.3
 * License:      GPLv2-or-Later
 * Requires WP:  6.1
 * Requires PHP: 7.4
 * Text Domain:  blockify
 */

declare( strict_types=1 );

namespace Blockify\Plugin;

use const DIRECTORY_SEPARATOR;
use const PHP_VERSION;
use function add_action;
use function get_template;
use function glob;
use function is_readable;
use function version_compare;

const SLUG = 'blockify';
const NAME = 'Blockify';
const NS   = __NAMESPACE__ . '\\';
const DS   = DIRECTORY_SEPARATOR;
const DIR  = __DIR__ . DS;
const FILE = __FILE__;

if ( ! version_compare( '7.4.0', PHP_VERSION, '<=' ) ) {
	return;
}

add_action( 'plugins_loaded', NS . 'load_textdomain' );
/**
 * Load textdomain.
 *
 * @since 0.6.0
 *
 * @return void
 */
function load_textdomain(): void {
	load_plugin_textdomain( SLUG, false, basename( DIR ) . '/languages' );
}

add_action( 'after_setup_theme', NS . 'framework', 7 );
/**
 * Load theme framework.
 *
 * @since 0.6.0
 *
 * @return void
 */
function framework(): void {
	if ( get_template() !== 'blockify' ) {
		require_once DIR . 'vendor/blockify/theme/functions.php';
	}
}

add_action( 'after_setup_theme', NS . 'setup', 9 );
/**
 * Load plugin files.
 *
 * @since 0.6.0
 *
 * @return void
 */
function setup(): void {
	foreach ( glob( DIR . 'includes/*.php' ) as $file ) {
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}
}
