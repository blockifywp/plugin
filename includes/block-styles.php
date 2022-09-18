<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use function add_filter;

add_filter( 'blockify', __NAMESPACE__ . '\\add_block_styles' );
/**
 * Adds custom block styles to editor (registered with JS).
 *
 * @since 0.4.0
 *
 * @param array $config
 *
 * @return array
 */
function add_block_styles( array $config ): array {
	$register = [
		[
			'type'  => 'core/image',
			'name'  => 'icon',
			'label' => __( 'SVG Icon', 'blockify' ),
		],
		[
			'type'  => 'core/site-logo',
			'name'  => 'icon',
			'label' => __( 'Icon', 'blockify' ),
		],
		[
			'type'  => 'core/list',
			'name'  => 'numbered',
			'label' => __( 'Numbered', 'blockify' ),
		],
		[
			'type'  => 'core/list',
			'name'  => 'checklist',
			'label' => __( 'Checklist', 'blockify' ),
		],
		[
			'type'  => 'core/list',
			'name'  => 'checklist',
			'label' => __( 'Check Circle', 'blockify' ),
		],
		[
			'type'  => 'core/list',
			'name'  => 'square',
			'label' => __( 'Square', 'blockify' ),
		],
		[
			'type'  => 'core/navigation-submenu',
			'name'  => 'mega-menu',
			'label' => __( 'Mega Menu', 'blockify' ),
		],
		[
			'type'  => 'core/search',
			'name'  => 'toggle',
			'label' => __( 'Toggle', 'blockify' ),
		],
		[
			'type'  => 'core/spacer',
			'name'  => 'angle',
			'label' => __( 'Angle', 'blockify' ),
		],
		[
			'type'  => 'core/spacer',
			'name'  => 'curve',
			'label' => __( 'Curve', 'blockify' ),
		],
		[
			'type'  => 'core/spacer',
			'name'  => 'round',
			'label' => __( 'Round', 'blockify' ),
		],
		[
			'type'  => 'core/spacer',
			'name'  => 'wave',
			'label' => __( 'Wave', 'blockify' ),
		],
		[
			'type'  => 'core/spacer',
			'name'  => 'fade',
			'label' => __( 'Fade', 'blockify' ),
		],
	];

	$unregister = [
		[
			'type' => 'core/image',
			'name' => [ 'rounded' ],
		],
		[
			'type' => 'core/site-logo',
			'name' => [ 'rounded' ],
		],
		[
			'type' => 'core/separator',
			'name' => [ 'wide', 'dots' ],
		],
	];

	$config['blockStyles'] = [
		'register'   => $register,
		'unregister' => $unregister,
	];

	return $config;
}
