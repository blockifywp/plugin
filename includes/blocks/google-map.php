<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use DOMElement;
use function add_action;
use function file_get_contents;
use function is_admin;
use function json_decode;
use function md5;
use function rand;
use function substr;
use function wp_enqueue_script;
use function wp_localize_script;

add_filter( 'render_block_blockify/google-map', NS . 'render_google_map_block', 10, 2 );
/**
 * Modifies front end HTML output of block.
 *
 * `render_block` runs just after `template_redirect`, before `wp_enqueue_scripts`.
 *
 * @since 0.0.2
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function render_google_map_block( string $content, array $block ): string {
	if ( is_admin() ) {
		return $content;
	}

	static $enqueued = null;

	$google_maps_api_key = $block['attrs']['apiKey'] ?? '';

	$map = [
		'zoom'   => $block['attrs']['zoom'] ?? 8,
		'center' => [
			'lat' => $block['attrs']['lat'] ?? -25.344,
			'lng' => $block['attrs']['lng'] ?? 131.031,
		],
		'styles' => json_decode( file_get_contents( DIR . 'src/blocks/google-map/styles/' . ( $block['attrs']['lightStyle'] ?? 'default' ) . '.json' ) ),
	];

	$dark = json_decode( file_get_contents( DIR . 'src/blocks/google-map/styles/' . ( $block['attrs']['darkStyle'] ?? 'night-mode' ) . '.json' ) );
	$hex  = substr( md5( (string) rand() ), 0, 6 );
	$id   = 'blockify-map-' . $hex;

	add_action( 'wp_enqueue_scripts', function () use ( $google_maps_api_key, $enqueued, $map, $id, $hex, $dark ) {

		if ( ! $enqueued ) {
			wp_enqueue_script(
				'blockify-google-maps',
				'//maps.googleapis.com/maps/api/js?key=' . $google_maps_api_key . '&libraries=places&callback=initMaps',
				[],
				null,
				true
			);
		}

		// Allows multiple maps on same page.
		wp_localize_script(
			'blockify-google-maps',
			'blockifyGoogleMap' . $hex,
			[
				'id'       => $id,
				'dark'     => $dark,
				'map'      => $map,
				'position' => $map['center'],
			]
		);
	} );

	$enqueued = true;

	$dom = dom( $content );

	/**
	 * @var $div DOMElement
	 */
	$div = $dom->firstChild;
	$div->setAttribute( 'data-id', $hex );

	return $dom->saveHTML();
}
