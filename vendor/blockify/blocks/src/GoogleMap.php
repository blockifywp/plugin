<?php

declare( strict_types=1 );

namespace Blockify\Blocks;

use Blockify\Dom\DOM;
use Blockify\Utilities\Path;
use WP_Block;
use function add_action;
use function apply_filters;
use function dirname;
use function esc_attr;
use function file_exists;
use function file_get_contents;
use function filemtime;
use function floatval;
use function get_option;
use function intval;
use function is_admin;
use function json_decode;
use function uniqid;
use function untrailingslashit;
use function wp_enqueue_script;
use function wp_localize_script;
use function wp_register_script;

/**
 * GoogleMap class.
 *
 * @since 1.0.0
 */
class GoogleMap extends AbstractBlock {

	/**
	 * Modifies front end HTML output of block.
	 *
	 * `render_block` runs just after `template_redirect`, before
	 * `wp_enqueue_scripts`.
	 *
	 * @since 0.0.2
	 *
	 * @param array    $attributes Block data.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block object.
	 *
	 * @return string
	 */
	public function render( array $attributes, string $content, WP_Block $block ): string {
		if ( is_admin() ) {
			return $content;
		}

		$package_dir = Path::get_package_dir( untrailingslashit( $this->data->dir ), dirname( __DIR__ ) );

		$options       = get_option( 'blockify', [] );
		$api_key       = $options['googleMaps'] ?? '';
		$dir           = $package_dir . 'public/google-map/styles/';
		$light_file    = $dir . esc_attr( $attributes['lightStyle'] ?? 'default' ) . '.json';
		$dark_file     = $dir . esc_attr( $attributes['darkStyle'] ?? 'night-mode' ) . '.json';
		$show_controls = $attributes['showControls'] ?? true;
		$custom_style  = $attributes['customStyle'] ?? '';

		$map = [
			'zoom'   => intval( $attributes['zoom'] ?? 8 ),
			'center' => [
				'lat' => floatval( $attributes['lat'] ?? -25.344 ),
				'lng' => floatval( $attributes['lng'] ?? 131.031 ),
			],
		];

		if ( ! $show_controls ) {
			$map['disableDefaultUI'] = true;
		}

		if ( $custom_style ) {
			$map['styles'] = json_decode( $custom_style );
		} else {
			if ( file_exists( $light_file ) ) {
				$map['styles'] = json_decode( file_get_contents( $light_file ) );
			}
		}

		$dark = null;

		if ( file_exists( $dark_file ) ) {
			$dark = json_decode( file_get_contents( $dark_file ) );
		}

		$hex    = uniqid();
		$id     = $this->data->slug . '-map-' . $hex;
		$handle = $this->data->slug . '-google-maps';

		static $enqueued = null;

		add_action(
			'wp_enqueue_scripts',
			static function () use ( $handle, $api_key, $enqueued, $map, $id, $hex, $dark ): void {
				if ( ! $enqueued ) {
					wp_enqueue_script(
						$handle,
						'//maps.googleapis.com/maps/api/js?key=' . $api_key . '&libraries=places&callback=initMaps',
						[],
						filemtime( __FILE__ ),
						true
					);
				}

				$data = [
					'id'       => $id,
					'map'      => $map,
					'position' => $map['center'],
				];

				if ( $dark ) {
					$data['dark'] = $dark;
				}

				wp_localize_script(
					$handle,
					'blockifyGoogleMap' . $hex,
					$data
				);
			}
		);

		$enqueued = true;
		$dom      = DOM::create( $content );
		$div      = DOM::get_element( 'div', $dom );

		if ( ! $div ) {
			return $content;
		}

		$div->setAttribute( 'data-id', esc_attr( $hex ) );

		return $dom->saveHTML();
	}

	/**
	 * Add map styles editor data.
	 *
	 * @hook enqueue_block_editor_assets
	 *
	 * @return void
	 */
	public function add_editor_data(): void {
		wp_register_script(
			self::class,
			'',
			[],
			'',
			false
		);

		wp_localize_script(
			self::class,
			'blockifyGoogleMaps',
			apply_filters( 'blockify_google_map_styles', [] )
		);

		wp_enqueue_script( self::class );
	}
}

