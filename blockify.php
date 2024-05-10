<?php
/**
 * Plugin Name:  Blockify
 * Plugin URI:   https://blockifywp.com/
 * Description:  Blockify full site editing theme toolkit.
 * Author:       Blockify
 * Author URI:   https://blockifywp.com/
 * Version:      1.5.0
 * License:      GPLv2-or-Later
 * Requires WP:  6.3
 * Requires PHP: 7.4
 * Tested up to: 6.5
 * Text Domain:  blockify
 */

declare( strict_types=1 );

namespace Blockify\Plugin;

use function add_action;
use function esc_html__;
use function get_template;
use function printf;
use function version_compare;
use function wp_get_theme;
use const DIRECTORY_SEPARATOR;

const DIR  = __DIR__ . DIRECTORY_SEPARATOR;
const FILE = __FILE__;

( static function (): void {
	$theme         = get_template();
	$theme_version = wp_get_theme( $theme )->get( 'Version' );
	$min_version   = '1.5.0';

	if ( $theme === 'blockify' && version_compare( $theme_version, $min_version, '<' ) ) {
		add_action(
			'admin_notices',
			static function () use ( $min_version ): void {
				printf(
					'<div class="notice notice-warning is-dismissible"><p>%s <strong>%s</strong> %s</p></div>',
					esc_html__( 'Blockify Pro requires Blockify theme version', 'blockify-pro'
					),
					$min_version,
					esc_html__( 'or higher. Please update to the latest version to enable Pro features.', 'blockify-pro' )
				);
			}
		);

		return;
	}

	require_once DIR . 'vendor/autoload.php';
	require_once DIR . 'includes/utility.php';
	require_once DIR . 'includes/blocks.php';
	require_once DIR . 'includes/code.php';
	require_once DIR . 'includes/fields.php';
	require_once DIR . 'includes/fonts.php';
	require_once DIR . 'includes/framework.php';
	require_once DIR . 'includes/icons.php';
	require_once DIR . 'includes/license.php';
	require_once DIR . 'includes/patterns.php';
	require_once DIR . 'includes/seo.php';
	require_once DIR . 'includes/settings.php';
} )();
