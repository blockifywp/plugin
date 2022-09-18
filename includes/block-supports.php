<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use function add_filter;

add_filter( 'blockify', __NAMESPACE__ . '\\add_block_supports' );
/**
 * Returns the final merged config.
 *
 * @since 0.0.9
 *
 * @param array $config
 *
 * @return mixed
 */
function add_block_supports( array $config ): array {
	$block_supports = [
		'core/buttons'             => [],
		'core/button'              => [
			'typography'           => [
				'fontSize'   => true,
				'fontWeight' => true,
				'fontFamily' => true,
			],
			'boxShadow'            => true,
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
		'core/code'                => [
			'boxShadow' => true,
		],
		'core/column'              => [
			'boxShadow'            => true,
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
			'position'             => true,
		],
		'core/columns'             => [
			'boxShadow'     => true,
			'typography'    => [
				'fontSize'   => true,
				'fontWeight' => true,
			],
			'reverseMobile' => true,
			'position'      => true,
		],
		'core/cover'               => [
			'position' => true,
		],
		'core/embed'               => [
			'spacing'              => [
				'margin' => true,
			],
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
		'core/gallery'             => [
			'spacing' => [
				'margin' => true,
			],
		],
		'core/group'               => [
			'boxShadow' => true,
			'position'  => true,
			'spacing'   => true,
			'minHeight' => true,
		],
		'core/heading'             => [
			'align'     => [
				'full',
				'wide',
				'none',
			],
			'alignWide' => true,
			'color'     => [
				'gradients'  => true,
				'background' => true,
				'text'       => true, // For SVG currentColor.
			],
			'spacing'   => [
				'margin'  => true,
				'padding' => true,
			],
		],
		'core/image'               => [
			'boxShadow'            => true,
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
			'color'                => [
				'gradients'  => true,
				'background' => true,
				'text'       => true, // For SVG currentColor.
			],
			'spacing'              => [
				'margin'  => true,
				'padding' => true,
			],
			'transform'            => true,
			'filter'               => true,
			'position'             => true,
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
		'core/navigation-submenu'  => [
			'spacing' => [
				'margin'   => true,
				'padding'  => true,
				'blockGap' => true,
			],
			'color'   => [
				'background' => true,
				'gradients'  => true,
				'link'       => true,
				'text'       => true,
			],
		],
		'core/paragraph'           => [
			'align'                => [
				'full',
				'wide',
				'left',
				'center',
				'right',
				'none',
			],
			'alignWide'            => true,
			'color'                => [
				'background' => true,
				'gradients'  => true,
				'link'       => true,
				'text'       => true,
			],
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
		'core/post-content'        => [
			'align'     => [
				'full',
				'wide',
				'left',
				'center',
				'right',
				'none',
			],
			'alignWide' => true,
			'spacing'   => [
				'margin'  => true,
				'padding' => true,
			],
		],
		'core/post-author'         => [
			// Border applied to avatar.
			'__experimentalBorder' => [
				'radius'                        => true,
				'width'                         => true,
				'color'                         => false,
				'style'                         => true,
				'__experimentalDefaultControls' => [
					'width' => true,
					'color' => true,
				],
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
		'core/post-date'           => [
			'spacing' => [
				'margin' => true,
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
			'color'     => [
				'background' => true,
			],
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
			'spacing'   => [
				'padding' => true,
				'margin'  => true,
			],
		],
		'core/post-title'          => [
			'spacing' => [
				'padding' => true,
				'margin'  => true,
			],
		],
		'core/query'               => [
			'spacing' => [
				'padding'  => true,
				'blockGap' => true,
			],
		],
		'core/quote'               => [
			'spacing'              => [
				'margin'   => true,
				'padding'  => true,
				'blockGap' => true,
			],
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
		'core/row'                 => [
			'boxShadow' => true,
		],
		'core/search'              => [
			'boxShadow' => true,
			'spacing'   => [
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
			'color'                => [
				'text'       => true,
				'background' => false,
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
		'core/site-logo'           => [
			'color' => [
				'background' => true,
				'gradients'  => true,
				'link'       => true,
				'text'       => true,
			],
		],
		'core/social-link'         => [
			'color' => [
				'background' => false,
				'text'       => true,
			],
		],
		'core/spacer'              => [
			'align'                => [
				'full',
				'wide',
				'left',
				'center',
				'right',
				'none',
			],
			'alignWide'            => true,
			'boxShadow'            => true,
			'color'                => [
				'gradients'  => true,
				'background' => true,
				'text'       => true,
			],
			'spacing'              => [
				'margin' => true,
			],
			'position'             => true,
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
		'core/tag-cloud'           => [
			'typography' => [
				'textTransform' => true, // Doesn't work
				'letterSpacing' => true, // Doesn't work
			],
		],
		'core/template-part'       => [
			'boxShadow' => true,
			'color'     => [
				'background' => true,
				'gradients'  => true,
				'link'       => true,
				'text'       => true,
			],
			'position'  => true,
		],
		'core/video'               => [
			'boxShadow' => true,
			'color'     => [
				'gradients'  => true,
				'background' => true,
				'text'       => true,
			],
			'spacing'   => [
				'margin' => true, // Doesn't work.
			],
		],
	];

	$config['blockSupports'] = $block_supports;

	return $config;
}
