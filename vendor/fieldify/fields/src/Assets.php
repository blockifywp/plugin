<?php

declare( strict_types=1 );

namespace Fieldify\Fields;

use Blockify\Icons\Icon;
use RuntimeException;
use function array_values;
use function esc_html;
use function filemtime;
use function get_option;
use function get_post_type;
use function glob;
use function is_readable;
use function wp_enqueue_media;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_localize_script;
use function wp_register_script;
use function wp_register_style;
use const GLOB_ONLYDIR;

/**
 * Assets.
 *
 * @since 0.0.14
 */
class Assets {

	/**
	 * @var Config $config
	 */
	private Config $config;

	/**
	 * @var Blocks $blocks
	 */
	private Blocks $blocks;

	/**
	 * @var MetaBoxes $meta_boxes
	 */
	private MetaBoxes $meta_boxes;

	/**
	 * @var Settings $settings
	 */
	private Settings $settings;

	/**
	 * Constructor.
	 *
	 * @since 0.0.14
	 *
	 * @param Config    $config     Config.
	 * @param Blocks    $blocks     Blocks.
	 * @param MetaBoxes $meta_boxes Meta boxes.
	 * @param Settings  $settings   Settings.
	 */
	public function __construct(
		Config    $config,
		Blocks    $blocks,
		MetaBoxes $meta_boxes,
		Settings  $settings
	) {
		$this->config     = $config;
		$this->blocks     = $blocks;
		$this->meta_boxes = $meta_boxes;
		$this->settings   = $settings;
	}

	/**
	 * Enqueues editor assets.
	 *
	 * @since 1.0.0
	 *
	 * @throws RuntimeException If asset file is not readable.
	 *
	 * @hook  enqueue_block_editor_assets
	 * @hook  admin_enqueue_scripts
	 *
	 * @return void
	 */
	public function enqueue_editor_assets(): void {
		global $current_screen;

		$is_block_editor = $current_screen && $current_screen->is_block_editor();

		$settings = $this->settings->get_settings();

		// Load for settings pages and block editor only.
		if ( ! $current_screen || ! $is_block_editor ) {
			$is_settings_page = false;

			foreach ( $settings as $id => $args ) {
				$settings_page = $args['page'] ?? null;

				if ( ! $settings_page ) {
					continue;
				}

				if ( $current_screen->id === 'settings_page_' . $settings_page ) {
					$is_settings_page = true;
					break;
				}
			}

			if ( ! $is_settings_page ) {
				return;
			}
		}

		$dir        = $this->config->dir;
		$asset_file = $dir . 'public/js/index.asset.php';

		if ( ! is_readable( $asset_file ) ) {
			throw new RuntimeException( static::class . ' asset file is not readable. File path: ' . $asset_file );
		}

		$asset = require $asset_file;
		$slug  = $this->config->slug;
		$url   = $this->config->url;

		wp_register_style( ...array_values( [
			'handle' => $slug,
			'src'    => $url . 'public/css/index.css',
			'deps'   => [],
			'ver'    => filemtime( $dir . 'public/css/index.css' ),
			'media'  => 'all',
		] ) );

		wp_enqueue_style( $slug );

		wp_register_script( ...array_values( [
			'handle'    => $slug,
			'src'       => $url . 'public/js/index.js',
			'deps'      => $asset['dependencies'] ?? [],
			'ver'       => $asset['version'] ?? filemtime( $dir . 'public/js/index.js' ),
			'in_footer' => true,
		] ) );

		wp_enqueue_script( $slug );

		$args = [
			'slug'        => $slug,
			'postType'    => esc_html( get_post_type() ),
			'siteEditor'  => $current_screen && $current_screen->base === 'site-editor',
			'blockEditor' => $is_block_editor,
			'blocks'      => $this->blocks->get_blocks(),
			'settings'    => $settings,
		];

		if ( ! $is_block_editor ) {
			foreach ( $settings as $id => $settings_args ) {
				$args['options'][ $id ] = get_option( $id, [] );
			}
		}

		$meta_boxes = $this->meta_boxes->get_meta_boxes();

		if ( ! empty( $meta_boxes ) ) {
			$args['metaBoxes'] = $meta_boxes;
		}

		wp_localize_script( $slug, $slug, $args );

		// Enqueue CodeMirror assets.
		wp_enqueue_style( 'wp-codemirror' );

		if ( ! $is_block_editor ) {
			foreach ( $asset['dependencies'] as $dependency ) {
				wp_enqueue_script( $dependency );
			}

			wp_enqueue_media();

			wp_enqueue_style( 'wp-components' );
			wp_enqueue_style( 'wp-edit-post' );
			wp_enqueue_style( 'wp-format-library' );
		}
	}

	/**
	 * Registers icons rest route.
	 *
	 * @since 1.0.0
	 *
	 * @hook  after_setup_theme
	 *
	 * @return void
	 */
	public function register_icons(): void {
		$icon_sets = glob( $this->config->dir . 'public/icons/*', GLOB_ONLYDIR );

		foreach ( $icon_sets as $icon_set ) {
			$icon_set = basename( $icon_set );

			Icon::register_icon_set( $icon_set, $this->config->dir . "public/icons/$icon_set" );
		}

		Icon::register_rest_route( 'fieldify/v1' );
	}

}
