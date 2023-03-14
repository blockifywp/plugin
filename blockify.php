<?php
/**
 * Plugin Name:  Blockify
 * Plugin URI:   https://blockifywp.com/
 * Description:  Blockify full site editing theme toolkit.
 * Author:       Blockify
 * Author URI:   https://blockifywp.com/
 * Version:      0.7.0
 * License:      GPLv2-or-Later
 * Requires WP:  6.1
 * Requires PHP: 7.4
 * Text Domain:  blockify
 */

// Installed as parent theme.
if ( get_template() === 'blockify' ) {
	return;
}

// Installed as framework.
if ( file_exists( get_template_directory() . '/vendor/blockify/theme' ) ) {
	return;
}

require_once __DIR__ . '/vendor/autoload.php';
