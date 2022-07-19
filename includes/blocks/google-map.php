<?php

declare( strict_types=1 );

namespace Blockify;

use DOMElement;
use function add_action;
use function file_get_contents;
use function is_admin;
use function json_decode;
use function json_encode;
use function sprintf;
use function wp_enqueue_script;

add_filter( 'render_block', NS . 'render_google_map_block', 10, 2 );
/**
 * Modifies front end HTML output of block.
 *
 * @since 0.0.2
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function render_google_map_block( string $content, array $block ): string {
	if ( 'blockify/google-map' !== $block['blockName'] || is_admin() ) {
		return $content;
	}

	$google_maps_api_key = 'google_maps_api_key';

	wp_enqueue_script(
		'blockify-google-maps',
		'//maps.googleapis.com/maps/api/js?key=' . $google_maps_api_key . '&libraries=places',
		[],
		'',
		true
	);

	$id       = 'map-' . random_hex( false );
	$api_key  = ''; // TODO: Add default key.
	$callback = 'initMap';
	$zoom     = 8;
	$center   = [
		'lat' => -25.344,
		'lng' => 131.031,
	];

	$styles = json_decode( file_get_contents( DIR . 'src/blocks/google-map/styles/subtle-greyscale.json' ) );

	$map = [
		'zoom'   => $zoom,
		'center' => $center,
		'styles' => $styles,
	];

	$script = '<script>function initMap() {';
	$script .= sprintf( 'const map = new google.maps.Map(document.getElementById("%s"), %s);', $id, json_encode( $map ) );
	$script .= sprintf( 'new google.maps.Marker({ position: %s, map: map });}', json_encode( $center ) );
	$script .= 'window.initMap = initMap;</script>';

	$dom                = dom( $content );
	$first              = $dom->firstChild;
	$first->textContent = '';

	/**
	 * @var $div DOMElement
	 */
	$div = $dom->getElementsByTagName( 'div' )->item( 0 );
	$div->setAttribute( 'id', $id );

	$content = $dom->saveHTML();
	$content = $script . $content;
	$content = $content . sprintf(
			'<script async defer src="//maps.googleapis.com/maps/api/js?key=%1$s&callback=%2$s" ></script>',
			$api_key,
			$callback
		);

	return $content;
}
