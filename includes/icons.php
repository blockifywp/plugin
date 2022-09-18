<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use function add_action;
use function add_filter;
use function apply_filters;
use function array_keys;
use function basename;
use function current_user_can;
use function file_get_contents;
use function glob;
use function preg_replace;
use function str_contains;
use function str_replace;
use function explode;
use function in_array;
use function trim;
use WP_REST_Request;
use WP_REST_Server;
use DOMElement;

add_action( 'rest_api_init', __NAMESPACE__ . '\\register_icons_rest_route' );
/**
 * Registers endpoint for icon data.
 *
 * @since 0.0.1
 *
 * @return void
 */
function register_icons_rest_route(): void {
	register_rest_route( 'blockify/v1', '/icons/', [
		'permission_callback' => fn() => current_user_can( 'edit_posts' ),
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
		'callback'            => function ( WP_REST_Request $request ): array {
			$icon_data = [];
			$icon_sets = apply_filters( 'blockify_icon_sets', [
				'wordpress' => get_framework_dir( 'assets/svg/wordpress' ),
				'social'    => get_framework_dir( 'assets/svg/social' ),
			] );

			foreach ( $icon_sets as $icon_set => $set_dir ) {
				$icons = glob( $set_dir . '/*.svg' );

				foreach ( $icons as $icon ) {
					$name = basename( $icon, '.svg' );
					$icon = file_get_contents( $icon );

					if ( $icon_set === 'wordpress' ) {
						$icon = str_replace(
							[ 'fill="none"' ],
							[ 'fill="currentColor"' ],
							$icon
						);
					}

					// Remove comments.
					$icon = preg_replace( '/<!--(.|\s)*?-->/', '', $icon );

					$icon_data[ $icon_set ][ $name ] = trim( $icon );
				}
			}

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

add_filter( 'render_block', __NAMESPACE__ . '\\render_image_icon', 10, 2 );
/**
 * Renders image icon styles on front end.
 *
 * @since 0.2.0
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function render_image_icon( string $content, array $block ): string {
	$class_name = $block['attrs']['className'] ?? null;

	if ( $class_name && str_contains( $class_name, 'is-style-icon' ) ) {
		$dom = dom( $content );

		if ( ! $dom->firstChild ) {
			return $content;
		}

		/** @var DOMElement $first_child */
		$first_child = $dom->firstChild;

		$div  = change_tag_name( $first_child, 'div' );
		$span = change_tag_name( $div->firstChild, 'span' );

		$allowed_classes = [
			'wp-block-image',
			'wp-block-site-logo',
			'is-style-icon',
			'aligncenter',
			'alignleft',
			'alignright',
			'aligncenter',
			'aligncenter',
		];

		$all_classes  = explode( ' ', $div->getAttribute( 'class' ) );
		$div_classes  = '';
		$span_classes = 'blockify-icon';

		foreach ( $all_classes as $class ) {
			if ( in_array( $class, $allowed_classes ) ) {
				$div_classes .= ' ' . $class;
			}
		}

		if ( str_contains( $div->getAttribute( 'class' ), 'has-box-shadow' ) ) {
			$span_classes .= ' has-box-shadow';
		}

		$span_styles = [];
		$default_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9.706 8.646a.25.25 0 01-.188.137l-4.626.672a.25.25 0 00-.139.427l3.348 3.262a.25.25 0 01.072.222l-.79 4.607a.25.25 0 00.362.264l4.138-2.176a.25.25 0 01.233 0l4.137 2.175a.25.25 0 00.363-.263l-.79-4.607a.25.25 0 01.072-.222l3.347-3.262a.25.25 0 00-.139-.427l-4.626-.672a.25.25 0 01-.188-.137l-2.069-4.192a.25.25 0 00-.448 0L9.706 8.646zM12 7.39l-.948 1.921a1.75 1.75 0 01-1.317.957l-2.12.308 1.534 1.495c.412.402.6.982.503 1.55l-.362 2.11 1.896-.997a1.75 1.75 0 011.629 0l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39z" clip-rule="evenodd"> </path></svg>';

		if ( isset( $block['attrs']['width'] ) ) {
			$span_styles['--wp--custom--icon--size'] = $block['attrs']['width'] . 'px';
		}

		if ( isset( $block['attrs']['height'] ) ) {
			$span_styles['--wp--custom--icon--height'] = $block['attrs']['height'] . 'px';
		}

		if ( isset( $block['attrs']['backgroundColor'] ) ) {
			$background = 'var(--wp--preset--color--' . $block['attrs']['backgroundColor'] . ')';

			$span_styles['--wp--custom--icon--color'] = $background;
		}

		if ( isset( $block['attrs']['gradient'] ) ) {
			$gradient = 'var(--wp--preset--gradient--' . $block['attrs']['gradient'] . ')';

			$span_styles['--wp--custom--icon--color'] = $gradient;
		}

		if ( isset( $block['attrs']['textColor'] ) ) {
			$span_styles['--wp--custom--icon--color'] = 'var(--wp--preset--color--' . $block['attrs']['textColor'] . ')';
			$span_styles['background']                = $gradient ?? $background ?? 'transparent';
		}

		if ( isset( $block['attrs']['style']['color']['gradient'] ) && ! isset( $block['attrs']['textColor'] ) ) {
			$span_styles['--wp--custom--icon--color'] = $block['attrs']['style']['color']['gradient'];
		}

		if ( isset( $block['attrs']['style']['transform'] ) ) {
			// TODO.
		}

		foreach ( explode( ';', $div->getAttribute( 'style' ) ) as $rule ) {
			$explode  = explode( ':', $rule );
			$property = $explode[0] ?? null;
			$value    = $explode[1] ?? null;

			if ( ! str_contains( $property, 'background' ) && $value ) {
				$span_styles[ $property ] = $value;
			}
		}

		$span_styles['--wp--custom--icon--url'] = "url('data:image/svg+xml;utf8," . ( $block['attrs']['svgString'] ?? $default_svg ) . "')";

		$css = css_array_to_string( $span_styles );

		if ( $span->getAttribute( 'href' ) ) {
			$span = change_tag_name( $span, 'a' );
		}

		$dom->removeChild( $dom->firstChild );
		$dom->appendChild( $div );
		$div->removeChild( $dom->firstChild->firstChild );
		$div->appendChild( $span );
		$div->removeAttribute( 'style' );
		$span->removeAttribute( 'src' );
		$span->removeAttribute( 'width' );
		$span->removeAttribute( 'height' );
		$span->removeAttribute( 'alt' );
		$div->setAttribute( 'class', trim( $div_classes ) );
		$span->setAttribute( 'class', trim( $span_classes ) );
		$span->setAttribute( 'style', $css );

		if ( $span->firstChild ) {
			$span->removeChild( $span->firstChild );
		}

		if ( isset( $block['attrs']['icon'] ) ) {
			$span->setAttribute( 'title', str_replace( '-', ' ', $block['attrs']['icon'] ) );
		}

		$content = $dom->saveHTML();
	}

	return $content;
}
