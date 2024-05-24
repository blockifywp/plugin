<?php

declare( strict_types=1 );

namespace Blockify\Blocks;

use Blockify\Utilities\Path;
use WP_Block;
use function array_intersect;
use function array_keys;
use function array_map;
use function dirname;
use function explode;
use function file_exists;
use function function_exists;
use function get_option;
use function get_plugins;
use function in_array;
use function is_array;
use function is_user_logged_in;
use function trim;
use function wp_enqueue_script;
use function wp_get_current_user;
use function wp_list_pluck;
use function wp_localize_script;
use const ABSPATH;
use const DIRECTORY_SEPARATOR;
use const FILTER_SANITIZE_FULL_SPECIAL_CHARS;
use const INPUT_COOKIE;

/**
 * Conditional class.
 *
 * @since 1.0.0
 */
class Conditional extends AbstractBlock {

	/**
	 * Renders the block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block instance.
	 *
	 * @return void
	 */
	public function render( array $attributes, string $content, WP_Block $block ): string {
		return $this->is_hidden( $attributes['visibility'] ?? [] ) ? '' : $content;
	}

	/**
	 * Filter all blocks.
	 *
	 * @param string   $block_content Block HTML.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook render_block 11
	 * @hook blockify_tabs_tab_visibility
	 *
	 * @return string
	 */
	public function filter( string $block_content, array $block, WP_Block $instance ): string {
		return $this->is_hidden( $block['attrs']['visibility'] ?? [] ) ? '' : $block_content;
	}

	/**
	 * Add user roles to editor data.
	 *
	 * @hook enqueue_block_editor_assets
	 *
	 * @return void
	 */
	public function add_editor_data(): void {
		global $wp_roles;

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = array_map(
			static fn( $plugin_path ): string => explode( DIRECTORY_SEPARATOR, $plugin_path )[0] ?? '',
			array_keys( get_plugins() )
		);

		$options            = get_option( 'blockify', [] );
		$data['userRoles']  = $wp_roles->role_names;
		$data['plugins']    = $plugins;
		$data['postMeta']   = $options['postMetaKeys'] ?? [];
		$data['visibility'] = $options['visibility'] ?? true;
		$data['blocks']     = [
			'core/navigation-link',
			'blockify/tab',
		];

		wp_localize_script(
			'blockify-conditional-editor-script',
			'blockifyVisibility',
			$data
		);

		if ( $data['visibility'] ) {
			$package_dir = Path::get_package_dir( $this->data->dir,
				dirname( __DIR__ ) );
			$package_url = Path::get_package_url( $this->data->dir,
				dirname( __DIR__ ) );
			$asset       = $package_dir . 'public/visibility.asset.php';

			if ( file_exists( $asset ) ) {
				$asset = require $asset;
			}

			wp_enqueue_script(
				'blockify-visibility',
				$package_url . 'public/visibility.js',
				$asset['dependencies'] ?? [],
				$asset['version'] ?? '',
				true
			);
		}
	}

	/**
	 * Checks if the block is hidden.
	 *
	 * @param array $visibility Visibility settings.
	 *
	 * @return bool
	 */
	private function is_hidden( array $visibility ): bool {
		if ( empty( $visibility ) ) {
			return false;
		}

		$status = $visibility['status'] ?? 'all';

		if ( $status === 'logged-in' && ! is_user_logged_in() ) {
			return true;
		}

		if ( $status === 'logged-out' && is_user_logged_in() ) {
			return true;
		}

		$roles = [];

		foreach ( $visibility['roles'] ?? [] as $role ) {
			$roles[] = $role['value'];
		}

		if ( ! empty( $roles ) && is_array( $roles ) ) {
			$user = wp_get_current_user();

			if ( ! array_intersect( $roles, $user->roles ) ) {
				return true;
			}
		}

		$post_meta = wp_list_pluck( $visibility['postMeta'] ?? [], 'value' );

		if ( $post_meta ) {
			$post_id = get_the_ID();

			foreach ( $post_meta as $meta ) {
				if ( empty( trim( get_post_meta( $post_id, $meta, true ) ) ) ) {
					return true;
				}
			}
		}

		$plugins = wp_list_pluck( $visibility['plugins'] ?? [], 'value' );

		if ( $plugins ) {
			$active_plugin_slugs = array_map(
				static fn( string $path ): string => explode( DIRECTORY_SEPARATOR, $path )[0] ?? '',
				get_option( 'active_plugins', [] )
			);

			foreach ( $plugins as $plugin ) {

				if ( ! in_array( $plugin, $active_plugin_slugs, true ) ) {
					return true;
				}
			}
		}

		$cookie_name  = $visibility['cookieName'] ?? '';
		$cookie_value = $visibility['cookieValue'] ?? '';
		$cookie       = filter_input( INPUT_COOKIE, $cookie_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( $cookie_name && $cookie_value && $cookie === $cookie_value ) {
			return true;
		}

		return false;
	}

}
