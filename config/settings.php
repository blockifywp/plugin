<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use Blockify\Icons\Icon;
use function add_action;
use function current_user_can;
use function esc_html__;
use function get_option;
use function register_custom_settings;

add_action( 'after_setup_theme', __NAMESPACE__ . '\\register_settings' );
/**
 * Register settings.
 *
 * @return void
 */
function register_settings(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$options         = get_option( 'blockify', [] );
	$updater         = get_updater_instance();
	$license_key     = get_option( $updater->license_name_key ?? '' ) ?? '';
	$license_status  = ( $updater ?? null ) ? $updater->get_license_status() : '';
	$license_message = esc_html__( 'Please enter your license key to enable updates and connect Blockify design library.', 'blockify-pro' );
	$message_option  = get_option( ( $updater->plugin_slug ?? '' ) . '_license_message' );
	$admin_role      = 'administrator';

	if ( $message_option ) {
		$license_message = $message_option;
	}

	register_custom_settings(
		'blockify',
		[
			'icon'   => Icon::get_svg( 'social', 'blockify' ),
			'title'  => 'Blockify',
			'panels' => [
				'license'       => [
					'title'        => esc_html__( 'License', 'blockify-pro' ),
					'initial_open' => true,
				],
				'api_keys'      => [
					'title' => esc_html__( 'API Keys', 'blockify-pro' ),
				],
				'site_identity' => [
					'title' => esc_html__( 'Site Identity', 'blockify-pro' ),
				],
				'custom_code'   => [
					'title' => esc_html__( 'Custom Code', 'blockify-pro' ),
				],
				'google_fonts'  => [
					'title' => esc_html__( 'Google Fonts', 'blockify-pro' ),
				],
				'optimization'  => [
					'title' => esc_html__( 'Optimization', 'blockify-pro' ),
				],
			],
			'fields' => [
				'licenseKey'         => [
					'type'        => 'license',
					'panel'       => 'license',
					'label'       => esc_html__( 'License Key', 'blockify-pro' ),
					'description' => $license_message,
					'default'     => [
						'license'       => $license_key,
						'licenseStatus' => $license_status,
					],
					'endpoint'    => 'blockify/v1/license?key={license_key}',
					'permission'  => $admin_role,
				],
				'title'              => [
					'type'        => 'text',
					'panel'       => 'site_identity',
					'option'      => 'site',
					'label'       => esc_html__( 'Site Title', 'blockify-pro' ),
					'value'       => get_option( 'blogname' ),
					'description' => esc_html__( 'Enter your site title.', 'blockify-pro' ),
					'permission'  => $admin_role,
				],
				'description'        => [
					'type'        => 'text',
					'panel'       => 'site_identity',
					'option'      => 'site',
					'label'       => esc_html__( 'Site Description', 'blockify-pro' ),
					'value'       => get_option( 'blogdescription' ),
					'description' => esc_html__( 'Enter your site description.', 'blockify-pro' ),
					'permission'  => $admin_role,
				],
				'site_icon'          => [
					'type'        => 'image',
					'panel'       => 'site_identity',
					'option'      => 'site',
					'label'       => esc_html__( 'Site Icon', 'blockify-pro' ),
					'value'       => get_option( 'site_icon' ),
					'description' => esc_html__( 'Site Icons are what you see in browser tabs, bookmark bars, and within the WordPress mobile apps. Upload one here! Site Icons should be square and at least 512 × 512 pixels.', 'blockify-pro' ),
					'permission'  => $admin_role,
				],
				'googleMaps'         => [
					'type'        => 'text',
					'panel'       => 'api_keys',
					'label'       => esc_html__( 'Google Maps', 'blockify-pro' ),
					'description' => esc_html__( 'Enter your Google Maps API key.', 'blockify-pro' ),
					'permission'  => $admin_role,
				],
				'googleAnalytics'    => [
					'type'        => 'text',
					'panel'       => 'api_keys',
					'label'       => esc_html__( 'Google Analytics', 'blockify-pro' ),
					'description' => esc_html__( 'Enter your Google Analytics API key.', 'blockify-pro' ),
					'permission'  => $admin_role,
				],
				'additionalCss'      => [
					'type'        => 'code',
					'language'    => 'css',
					'panel'       => 'custom_code',
					'value'       => $options['additionalCss'] ?? '',
					'label'       => esc_html__( 'Additional CSS', 'blockify-pro' ),
					'description' => esc_html__( 'Add additional CSS to your site.', 'blockify-pro' ),
					'placeholder' => '',
					'rows'        => 12,
					'permission'  => $admin_role,
				],
				'additionalJs'       => [
					'type'        => 'code',
					'language'    => 'javascript',
					'panel'       => 'custom_code',
					'placeholder' => '',
					'value'       => $options['additionalJs'] ?? '',
					'label'       => esc_html__( 'Additional JS', 'blockify-pro' ),
					'description' => esc_html__( 'Add additional JavaScript to your site.', 'blockify-pro' ),
					'rows'        => 12,
					'permission'  => $admin_role,
				],
				'googleFonts'        => [
					'type'        => 'select',
					'multiple'    => true,
					'searchable'  => true,
					'panel'       => 'google_fonts',
					'placeholder' => esc_html__( 'Select fonts', 'blockify-pro' ),
					'options'     => get_font_options(),
					'permission'  => $admin_role,
				],
				'removeEmojiScripts' => [
					'type'        => 'toggle',
					'panel'       => 'optimization',
					'label'       => esc_html__( 'Remove Emoji Scripts', 'blockify-pro' ),
					'description' => esc_html__( 'Remove emoji scripts from your site.', 'blockify-pro' ),
					'permission'  => $admin_role,
				],
			],
		]
	);
}
