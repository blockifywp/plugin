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

	$admin_role      = 'administrator';
	$license_key     = get_option( 'blockify_license_key' ) ?? '';
	$license_status  = is_license_active() ? 'active' : 'inactive';
	$license_message = get_option( 'blockify_license_message' ) ?? esc_html__( 'Please enter your license key to enable updates and connect Blockify design library.', 'blockify-plugin' );

	register_custom_settings(
		'blockify',
		[
			'icon'   => Icon::get_svg( 'social', 'blockify' ),
			'title'  => 'Blockify',
			'panels' => [
				'license'       => [
					'title'        => esc_html__( 'License', 'blockify-plugin' ),
					'initial_open' => true,
				],
				'site_identity' => [
					'title' => esc_html__( 'Site Identity', 'blockify-plugin' ),
				],
				'dark_mode'     => [
					'title' => esc_html__( 'Dark Mode', 'blockify-plugin' ),
				],
				'integrations'  => [
					'title' => esc_html__( 'Integrations', 'blockify-plugin' ),
				],
				'custom_code'   => [
					'title' => esc_html__( 'Custom Code', 'blockify-plugin' ),
				],
				'google_fonts'  => [
					'title' => esc_html__( 'Google Fonts', 'blockify-plugin' ),
				],
				'optimization'  => [
					'title' => esc_html__( 'Optimization', 'blockify-plugin' ),
				],
			],
			'fields' => [
				'licenseKey'         => [
					'type'        => 'license',
					'panel'       => 'license',
					'label'       => esc_html__( 'License Key', 'blockify-plugin' ),
					'description' => $license_message,
					'help'        => [
						'active'   => [
							'message' => esc_html__( 'Manage license activations from your ', 'blockify-plugin' ),
							'label'   => esc_html__( 'customer portal', 'blockify-plugin' ) . ' ↗',
							'url'     => 'https://blockify.lemonsqueezy.com/billing',
						],
						'inactive' => [
							'message' => esc_html__( 'Access premium block patterns, icon packs, fonts and block extensions with ', 'blockify-plugin' ),
							'label'   => 'Blockify Pro ↗',
							'url'     => 'https://blockifywp.com/pricing/',
						],
					],
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
					'label'       => esc_html__( 'Site Title', 'blockify-plugin' ),
					'value'       => get_option( 'blogname' ),
					'description' => esc_html__( 'Enter your site title.', 'blockify-plugin' ),
					'permission'  => $admin_role,
				],
				'description'        => [
					'type'        => 'text',
					'panel'       => 'site_identity',
					'option'      => 'site',
					'label'       => esc_html__( 'Site Description', 'blockify-plugin' ),
					'value'       => get_option( 'blogdescription' ),
					'description' => esc_html__( 'Enter your site description.', 'blockify-plugin' ),
					'permission'  => $admin_role,
				],
				'site_icon'          => [
					'type'        => 'image',
					'panel'       => 'site_identity',
					'option'      => 'site',
					'label'       => esc_html__( 'Site Icon', 'blockify-plugin' ),
					'value'       => get_option( 'site_icon' ),
					'description' => esc_html__( 'Site Icons are what you see in browser tabs, bookmark bars, and within the WordPress mobile apps. Upload one here! Site Icons should be square and at least 512 × 512 pixels.', 'blockify-plugin' ),
					'permission'  => $admin_role,
				],
				'disableDarkMode'    => [
					'type'        => 'toggle',
					'panel'       => 'dark_mode',
					'label'       => esc_html__( 'Disable Dark Mode', 'blockify-plugin' ),
					'description' => esc_html__( 'Disable dark mode on your site.', 'blockify-plugin' ),
					'permission'  => $admin_role,
					'default'     => false,
				],
				'defaultMode'        => [
					'type'       => 'button_group',
					'panel'      => 'dark_mode',
					'label'      => esc_html__( 'Default Mode', 'blockify-plugin' ),
					'default'    => 'system',
					'show_if'    => [
						[
							'setting'  => 'disableDarkMode',
							'operator' => '!==',
							'value'    => true,
						],
					],
					'options'    => [
						[
							'value' => 'system',
							'label' => esc_html__( 'System', 'blockify-plugin' ),
						],
						[
							'value' => 'light',
							'label' => esc_html__( 'Light', 'blockify-plugin' ),
						],
						[
							'value' => 'dark',
							'label' => esc_html__( 'Dark', 'blockify-plugin' ),
						],
					],
					'permission' => $admin_role,
				],
				'googleMaps'         => [
					'type'        => 'text',
					'panel'       => 'integrations',
					'label'       => esc_html__( 'Google Maps', 'blockify-plugin' ),
					'description' => esc_html__( 'Enter your Google Maps API key.', 'blockify-plugin' ),
					'permission'  => $admin_role,
				],
				'googleAnalytics'    => [
					'type'        => 'text',
					'panel'       => 'integrations',
					'label'       => esc_html__( 'Google Analytics', 'blockify-plugin' ),
					'description' => esc_html__( 'Enter your Google Analytics API key.', 'blockify-plugin' ),
					'permission'  => $admin_role,
				],
				'additionalCss'      => [
					'type'        => 'code',
					'language'    => 'css',
					'panel'       => 'custom_code',
					'value'       => $options['additionalCss'] ?? '',
					'label'       => esc_html__( 'Additional CSS', 'blockify-plugin' ),
					'description' => esc_html__( 'Add additional CSS to your site.', 'blockify-plugin' ),
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
					'label'       => esc_html__( 'Additional JS', 'blockify-plugin' ),
					'description' => esc_html__( 'Add additional JavaScript to your site.', 'blockify-plugin' ),
					'rows'        => 12,
					'permission'  => $admin_role,
				],
				'googleFonts'        => [
					'type'        => 'select',
					'multiple'    => true,
					'searchable'  => true,
					'panel'       => 'google_fonts',
					'placeholder' => esc_html__( 'Select fonts', 'blockify-plugin' ),
					'options'     => get_font_options(),
					'permission'  => $admin_role,
				],
				'removeEmojiScripts' => [
					'type'        => 'toggle',
					'panel'       => 'optimization',
					'label'       => esc_html__( 'Remove Emoji Scripts', 'blockify-plugin' ),
					'description' => esc_html__( 'Remove emoji scripts from your site.', 'blockify-plugin' ),
					'permission'  => $admin_role,
				],
			],
		]
	);
}
