<?php

declare( strict_types=1 );

namespace Blockify;

use WP_REST_Request;
use WP_REST_Server;
use WP_Theme_JSON_Resolver;
use function add_filter;
use function str_replace;
use function apply_filters;
use function array_keys;
use function preg_replace;

add_filter( 'render_block', NS . 'render_icon_block', 10, 2 );
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
function render_icon_block( string $content, array $block ): string {
	if ( 'blockify/icon' !== $block['blockName'] ) {
		return $content;
	}

	if ( ! $content ) {
		return $content;
	}

	$dom = dom( $content );

	/**
	 * @var DOMElement $div
	 */
	$div       = $dom->getElementsByTagName( 'div' )->item( 0 );
	$container = $div->firstChild;
	$classes   = $div->getAttribute( 'class' );
	$classes   .= ' ' . $container->getAttribute( 'class' );

	if ( isset( $block['attrs']['layout']['justifyContent'] ) ) {
		$classes .= ' items-justified-' . $block['attrs']['layout']['justifyContent'];
	}

	$div->setAttribute( 'class', trim( $classes ) );
	$div->setAttribute( 'style', $container->getAttribute( 'style' ) );

	$mask = $container->firstChild;

	if ( ! $mask ) {
		return $content;
	}

	$style        = $mask->getAttribute( 'style' );
	$css          = css_rules_to_array( $style );
	$theme_json   = WP_Theme_JSON_Resolver::get_merged_data( '' );
	$palette      = $theme_json->get_settings()['color']['palette'];
	$mask_classes = $mask->getAttribute( 'class' );

	if ( isset( $css['background'] ) ) {
		$hex = $css['background'];

		foreach ( $palette as $color ) {
			if ( isset( $color['color'] ) && $hex === $color['color'] ) {
				$mask_classes .= ' has-' . $color['slug'] . '-background-color';
			}
		}
	}

	$mask->setAttribute( 'class', $mask_classes );
	$div->appendChild( $mask );
	$div->removeChild( $container );

	return str_replace( 'fill="currentColor"', ' ', $dom->saveHTML() );
}


add_action( 'rest_api_init', NS . 'register_icons_rest_route' );

function register_icons_rest_route(): void {
	register_rest_route( SLUG . '/v1', '/icons/', [
		'permission_callback' => '__return_true',
		'methods'             => WP_REST_Server::READABLE,
		[
			'args' => [
				'sets' => [
					'required' => false,
					'type'     => 'string',
				],
				'set'  => [
					'required' => false,
					'type'     => 'string',
				],
			],
		],
		'callback'            => function ( $request ) {
			$icon_data = get_icon_data();

			/**
			 * @var WP_REST_Request $request
			 */
			if ( $request->get_param( 'set' ) ) {
				$set = $request->get_param( 'set' );

				if ( $request->get_param( 'icon' ) ) {
					return $icon_data[ $set ][ $request->get_param( 'icon' ) ];
				}

				return $icon_data[ $set ];
			}

			if ( $request->get_param( 'sets' ) ) {
				return array_keys( $icon_data );
			}

			return $icon_data;
		},
	] );
}

function get_icon_data(): array {
	$icon_data = [];
	$icon_sets = apply_filters( 'blockify_icon_sets', [
		'dashicons' => DIR . 'assets/svg/dashicons',
		'wordpress' => DIR . 'assets/svg/wordpress',
		'social'    => DIR . 'assets/svg/social',
	] );

	foreach ( $icon_sets as $icon_set => $set_dir ) {
		$icons = glob( $set_dir . '/*.svg' );

		foreach ( $icons as $icon ) {
			$name = basename( $icon, '.svg' );
			$icon = file_get_contents( $icon );

			if ( $icon_set === 'wordpress' ) {
				$icon = str_replace(
					[ '<svg ', 'fill="none"' ],
					[ '<svg fill="currentColor" ', '' ],
					$icon
				);
			}

			// Remove comments.
			$icon = preg_replace( '/<!--(.|\s)*?-->/', '', $icon );

			$icon_data[ $icon_set ][ $name ] = $icon;
		}
	}

	return $icon_data;
}
