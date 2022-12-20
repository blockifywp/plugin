<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use function add_action;
use function register_setting;

add_action( 'admin_init', NS . 'register_settings' );
add_action( 'rest_api_init', NS . 'register_settings' );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function register_settings() {
	register_setting(
		'options',
		SLUG,
		[
			'description'  => __( 'Blockify Settings.', 'blockify' ),
			'type'         => 'object',
			'show_in_rest' => [
				'schema' => [
					'type'       => 'object',
					'properties' => [
						'apiKey'          => [
							'type' => 'string',
						],
						'apiKeyStatus'    => [
							'type' => 'string',
						],
						'autoDarkMode'    => [
							'type' => 'boolean',
						],
						'additionalCss'   => [
							'type' => 'string',
						],
						'googleAnalytics' => [
							'type' => 'string',
						],
						'siteIconUrl'     => [
							'type' => 'string',
						],
						'googleFonts'     => [
							'type' => 'array',
						],
						'iconSets'        => [
							'type' => 'array',
						],
					],
				],
			],
		]
	);
}

add_action( 'blockify_editor_scripts', NS . 'enqueue_editor_scripts' );
/**
 * Enqueues editor assets.
 *
 * @since 0.0.14
 *
 * @return void
 */
function enqueue_editor_scripts(): void {
	$asset_file = DIR . 'build/settings.asset.php';

	// Installed as framework.
	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset = require DIR . 'build/settings.asset.php';
	$deps  = $asset['dependencies'];

	wp_register_script(
		'blockify-settings',
		plugin_dir_url( FILE ) . 'build/settings.js',
		$deps,
		filemtime( DIR . 'build/settings.js' ),
		true
	);

	wp_enqueue_script( SLUG . '-settings' );
}
