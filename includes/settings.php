<?php

declare( strict_types=1 );

namespace Blockify;

use function add_action;
use function add_filter;
use function load_plugin_textdomain;
use function register_rest_field;
use function get_post;
use function wp_update_post;

add_action( 'plugins_loaded', NS . 'load_textdomain' );
/**
 * Loads plugin text domain.
 *
 * @since 0.0.2
 *
 * @return void
 */
function load_textdomain(): void {
	load_plugin_textdomain( SLUG );
}


add_action( 'after_setup_theme', NS . 'theme_supports' );
/**
 * Handles theme supports.
 *
 * @since 0.0.2
 *
 * @return void
 */
function theme_supports(): void {
	remove_theme_support( 'core-block-patterns' );
}

add_action( 'after_setup_theme', NS . 'post_type_supports' );
/**
 * Handles post type supports.
 *
 * @since 0.0.2
 *
 * @return void
 */
function post_type_supports(): void {
	add_post_type_support( 'page', 'excerpt' );
}

add_action( 'init', NS . 'rest_fields' );
/**
 * Registers rest fields.
 *
 * @since 0.0.2
 *
 * @return void
 */
function rest_fields(): void {
	register_rest_field(
		'blockify-title',
		'title',
		[
			'get_callback'    => function ( $params ) {
				$post_id = $params['id'];
				$post    = get_post( $post_id );

				return $post->post_title;
			},
			'update_callback' => function ( $value, $object ) {
				wp_update_post(
					[
						'ID'         => $object->ID,
						'post_title' => $value,
					]
				);
			},
		]
	);
}

add_filter( 'blockify_index_data', NS . 'block_supports', 10, 1 );
/**
 * Adds extra block supports.
 *
 * @since 0.0.2
 *
 * @param array $data
 *
 * @return array
 */
function block_supports( array $data ): array {
	$data['block_supports'] = [
		'core/button'              => [
			'__experimentalBorder' => [
				'radius'                        => true,
				'width'                         => true,
				'color'                         => true,
				'style'                         => true,
				'__experimentalDefaultControls' => [
					'width' => true,
					'color' => true,
				],
			],
		],
		'core/column'              => [
			'__experimentalBorder' => [
				'radius'                        => true,
				'width'                         => true,
				'color'                         => true,
				'style'                         => true,
				'__experimentalDefaultControls' => [
					'width' => true,
					'color' => true,
				],
			],
		],
		'core/columns'             => [
			'typography' => [
				'fontSize' => true,
			],
		],
		'core/embed'               => [
			'spacing' => [
				'margin' => true,
			],
		],
		'core/gallery'             => [
			'spacing' => [
				'margin' => true,
			],
		],
		'core/heading'             => [
			'spacing' => [
				'margin'  => true,
				'padding' => true,
			],
		],
		'core/image'               => [
			'__experimentalBorder' => [
				'radius' => true,
			],
			'color'                => [
				'gradients'  => true,
				'background' => true,
				'text'       => true, // For SVG currentColor.
			],
			'spacing'              => [
				'margin'  => true,
				'padding' => true,
			],
		],
		'core/list'                => [
			'__experimentalLayout' => [
				'allowSwitching'  => false,
				'allowInheriting' => false,
				'default'         => [
					'type'        => 'flex',
					'orientation' => 'vertical',
				],
			],
			'spacing'              => [
				'padding'  => true,
				'margin'   => true,
				'blockGap' => true,
			],
		],
		'core/media-text'          => [
			'__experimentalBorder' => [
				'radius' => true,
			],
			'spacing'              => [
				'margin' => true,
			],
		],
		'core/navigation'          => [
			'spacing' => [
				'margin'   => true,
				'padding'  => true,
				'blockGap' => true,
			],
		],
		'core/paragraph'           => [
			'__experimentalBorder' => [
				'radius'                        => true,
				'width'                         => true,
				'color'                         => true,
				'style'                         => true,
				'__experimentalDefaultControls' => [
					'width' => true,
					'color' => true,
				],
			],
			'spacing'              => [
				'margin'  => true,
				'padding' => true,
			],
		],
		'core/post-excerpt'        => [
			'__experimentalLayout' => [
				'allowSwitching'  => false,
				'allowInheriting' => false,
				'default'         => [
					'type' => 'flex',
				],
			],
		],
		'core/post-featured-image' => [
			'align'     => [
				'full',
				'wide',
				'left',
				'center',
				'right',
				'none',
			],
			'alignWide' => true,
		],
		'core/post-terms'          => [
			'align'     => [
				'full',
				'wide',
				'left',
				'center',
				'right',
				'none',
			],
			'alignWide' => true,
		],
		'core/query'               => [
			'spacing' => [
				'padding'  => true,
				'blockGap' => true,
			],
		],
		'core/search'              => [
			'spacing' => [
				'padding' => true,
				'margin'  => true,
			],
		],
		'core/separator'           => [
			'align'                => [
				'full',
				'wide',
				'left',
				'center',
				'right',
				'none',
			],
			'alignWide'            => true,
			'__experimentalBorder' => [
				'radius'                        => false,
				'width'                         => true,
				'color'                         => false,
				'style'                         => true,
				'__experimentalDefaultControls' => [
					'width' => true,
					'color' => true,
				],
			],
			'spacing'              => [
				'margin'  => true,
				'padding' => false,
			],
		],
		'core/spacer'              => [
			'color'   => [
				'gradients'  => true,
				'background' => true,
				'text'       => false,
			],
			'spacing' => [
				'margin' => true,
			],
		],
		'core/video'               => [
			'color'   => [
				'gradients'  => true,
				'background' => true,
				'text'       => true,
			],
			'spacing' => [
				'margin' => true, // Doesn't work.
			],
		],
	];

	return $data;
}
