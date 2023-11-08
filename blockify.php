<?php
/**
 * Plugin Name:  Blockify
 * Plugin URI:   https://blockifywp.com/
 * Description:  Blockify full site editing theme toolkit.
 * Author:       Blockify
 * Author URI:   https://blockifywp.com/
 * Version:      1.3.0
 * License:      GPLv2-or-Later
 * Requires WP:  6.3
 * Requires PHP: 7.4
 * Text Domain:  blockify
 */

declare( strict_types=1 );

namespace Blockify\Plugin;

use function add_action;
use function deactivate_plugins;
use function file_exists;
use function function_exists;
use function get_template;
use function get_template_directory;
use function plugin_basename;
use function printf;
use function wp_get_theme;
use const ABSPATH;

( static function ( string $file, string $dir ): void {
	$installed = false;

	if ( get_template() === 'blockify' ) {
		$installed = true;
	}

	if ( file_exists( get_template_directory() . '/vendor/blockify/theme' ) ) {
		$installed = true;
	}

	if ( ! $installed && wp_get_theme()->get( 'Name' ) === 'Blockify' ) {
		$installed = true;
	}

	if ( $installed ) {

		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		deactivate_plugins( plugin_basename( $file ) );

		add_action( 'admin_notices', static fn() => printf(
			'<div class="notice notice-error"><p>%s</p></div>',
			__( 'The Blockify plugin has been deactivated because the Blockify theme is already active. The Blockify plugin is only required when using a non-Blockify theme.', 'blockify' )
		) );

		return;
	}

	require_once $dir . '/vendor/autoload.php';

	load_plugin_textdomain(
		basename( $file, '.php' ),
		false,
		basename( $dir ) . '/languages'
	);

} )( __FILE__, __DIR__ );
