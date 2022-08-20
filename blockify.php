<?php
/**
 * Plugin Name: Blockify
 * Plugin URI:  https://blockifywp.com/
 * Description: Lightweight block library for full site editing themes.
 * Author:      Blockify
 * Author URI:  https://blockifywp.com/about/
 * Version:     0.0.15
 * License:     GPLv2-or-Later
 * Text Domain: blockify
 * Domain Path: /assets/lang
 */

declare( strict_types=1 );

namespace Blockify\Plugin;

use const DIRECTORY_SEPARATOR;
use const PHP_VERSION;
use function add_action;
use function basename;
use function load_plugin_textdomain;
use function version_compare;

if ( ! version_compare( '7.4.0', PHP_VERSION, '<=' ) ) {
	return;
}

const SLUG = 'blockify';
const NS   = __NAMESPACE__ . '\\';
const DS   = DIRECTORY_SEPARATOR;
const DIR  = __DIR__ . DS;
const FILE = __FILE__;

add_action( 'after_setup_theme', NS . 'register' );
/**
 * Registers plugin after theme is loaded.
 *
 * @since 0.0.13
 *
 * @return void
 */
function register() {
	load_plugin_textdomain(
		'blockify', false,
		basename( DIR ) . '/assets/lang'
	);

	require_once DIR . 'includes/utility.php';
	require_once DIR . 'includes/blocks.php';
	require_once DIR . 'includes/blocks/accordion.php';
	require_once DIR . 'includes/blocks/breadcrumbs.php';
	require_once DIR . 'includes/blocks/google-map.php';
	require_once DIR . 'includes/blocks/icon.php';
	require_once DIR . 'includes/blocks/input.php';
	require_once DIR . 'includes/blocks/newsletter.php';
	require_once DIR . 'includes/blocks/popup.php';
	require_once DIR . 'includes/blocks/slider.php';
	require_once DIR . 'includes/blocks/tabs.php';
	require_once DIR . 'includes/blocks/toggle.php';
}
