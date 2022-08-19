<?php
/**
 * Plugin Name: Blockify
 *
 * Plugin URI:  https://blockifywp.com/
 * Description: A lightweight block library and toolkit that supercharges Full Site Editing themes.
 * Author:      Blockify
 * Author URI:  https://blockifywp.com/about/
 * Version:     0.0.14
 * License:     GPLv2-or-Later
 * Text Domain: blockify
 */

declare( strict_types=1 );

namespace Blockify\Plugin;

use const DIRECTORY_SEPARATOR;
use const PHP_VERSION;
use function add_action;
use function array_map;
use function glob;
use function version_compare;

if ( ! version_compare( '7.4.0', PHP_VERSION, '<=' ) ) {
	return;
}

const SLUG = 'blockify';
const NS   = __NAMESPACE__ . '\\';
const DS   = DIRECTORY_SEPARATOR;
const DIR  = __DIR__ . DS;
const FILE = __FILE__;

add_action('after_setup_theme', NS . 'register');
/**
 * Registers plugin after theme is loaded.
 *
 * @since 0.0.13
 *
 * @return void
 */
function register() {
	require_once DIR . 'includes/utility.php';
	require_once DIR . 'includes/blocks.php';

	array_map(
		fn( $file ) => require_once $file,
		glob( DIR . 'includes/blocks/*.php' )
	);
}
