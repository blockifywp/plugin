<?php
/**
 * Plugin Name: Blockify
 *
 * Plugin URI:  https://blockifywp.com/
 * Description: A lightweight (1kb) block library and toolkit that supercharges Full Site Editing themes.
 * Author:      Blockify
 * Author URI:  https://blockifywp.com/about/
 * Version:     0.0.8
 * License:     GPLv2-or-Later
 * Text Domain: blockify
 */

declare( strict_types=1 );

namespace Blockify;

use const DIRECTORY_SEPARATOR;
use const PHP_VERSION;
use function add_action;
use function array_map;
use function glob;
use function load_plugin_textdomain;
use function version_compare;

const SLUG = 'blockify';
const DIR  = __DIR__ . DIRECTORY_SEPARATOR;
const FILE = __FILE__;
const NS   = __NAMESPACE__ . '\\';

if ( ! version_compare( '7.4.0', PHP_VERSION, '<=' ) ) {
	return;
}

add_action( 'plugins_loaded', NS . 'load' );
/**
 * Loads plugin.
 *
 * @since 0.0.1
 *
 * @return void
 */
function load(): void {
	load_plugin_textdomain( SLUG );

	require_once DIR . 'vendor/autoload.php';
	require_once DIR . 'includes/utility.php';
	require_once DIR . 'includes/register.php';
	require_once DIR . 'includes/settings.php';
	require_once DIR . 'includes/enqueue.php';

	array_map(
		fn( $file ) => require_once $file,
		glob( DIR . 'includes/blocks/*.php' )
	);
}
