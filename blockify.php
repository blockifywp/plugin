<?php
/**
 * Plugin Name: Blockify
 * Plugin URI:  https://blockifywp.com/
 * Description: Full site editing theme framework, patterns, blocks, extensions and more.
 * Author:      Blockify
 * Author URI:  https://blockifywp.com/about/
 * Version:     0.0.3
 * License:     GPLv2-or-Later
 * Text Domain: blockify
 */

declare( strict_types=1 );

namespace Blockify;

use const DIRECTORY_SEPARATOR;
use const PHP_VERSION;
use function version_compare;

const SLUG    = 'blockify';
const VERSION = '0.0.3';
const NAME    = __NAMESPACE__;
const DIR     = __DIR__ . DIRECTORY_SEPARATOR;
const FILE    = __FILE__;
const NS      = __NAMESPACE__ . '\\';
const DS      = DIRECTORY_SEPARATOR;

if ( version_compare( '7.4.0', PHP_VERSION, '<=' ) ) {
	require_once DIR . 'vendor/autoload.php';

	array_map( fn( $file ) => require_once $file, glob( DIR . 'includes/*.php' ) );
	array_map( fn( $file ) => require_once $file, glob( DIR . 'includes/blocks/*.php' ) );
}
