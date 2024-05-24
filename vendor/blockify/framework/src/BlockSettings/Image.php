<?php

declare( strict_types=1 );

namespace Blockify\Framework\BlockSettings;

use Blockify\Framework\InlineAssets\Scriptable;
use Blockify\Framework\InlineAssets\Scripts;
use function is_admin;

/**
 * Image settings.
 *
 * @since 1.0.0
 */
class Image implements Scriptable {

	public const SETTINGS = [
		'aspectRatio'    => [
			'property' => 'aspect-ratio',
			'label'    => 'Aspect Ratio',
			'options'  => [
				[
					'label' => '',
					'value' => '',
				],
				[
					'label' => '1/1',
					'value' => '1/1',
				],
				[
					'label' => '1/2',
					'value' => '1/2',
				],
				[
					'label' => '1/3',
					'value' => '1/3',
				],
				[
					'label' => '2/1',
					'value' => '2/1',
				],
				[
					'label' => '2/3',
					'value' => '2/3',
				],
				[
					'label' => '3/1',
					'value' => '3/1',
				],
				[
					'label' => '3/2',
					'value' => '3/2',
				],
				[
					'label' => '3/4',
					'value' => '3/4',
				],
				[
					'label' => '4/3',
					'value' => '4/3',
				],
				[
					'label' => '4/5',
					'value' => '4/5',
				],
				[
					'label' => '5/2',
					'value' => '5/2',
				],
				[
					'label' => '5/4',
					'value' => '5/4',
				],
				[
					'label' => '9/16',
					'value' => '9/16',
				],
				[
					'label' => '16/9',
					'value' => '16/9',
				],
			],
		],
		'height'         => [
			'property' => 'height',
			'label'    => 'Height',
		],
		'objectFit'      => [
			'property' => 'object-fit',
			'label'    => 'Object Fit',
			'options'  => [
				[
					'label' => '',
					'value' => '',
				],
				[
					'label' => 'Fill',
					'value' => 'fill',
				],
				[
					'label' => 'Contain',
					'value' => 'contain',
				],
				[
					'label' => 'Cover',
					'value' => 'cover',
				],
				[
					'label' => 'None',
					'value' => 'none',
				],
				[
					'label' => 'Scale Down',
					'value' => 'scale-down',
				],
			],
		],
		'objectPosition' => [
			'property' => 'object-position',
			'label'    => 'Object Position',
			'options'  => [
				[
					'label' => '',
					'value' => '',
				],
				[
					'label' => 'Top',
					'value' => 'top',
				],
				[
					'label' => 'Top Right',
					'value' => 'top right',
				],
				[
					'label' => 'Right',
					'value' => 'right',
				],
				[
					'label' => 'Bottom Right',
					'value' => 'bottom right',
				],
				[
					'label' => 'Bottom',
					'value' => 'bottom',
				],
				[
					'label' => 'Bottom Left',
					'value' => 'bottom left',
				],
				[
					'label' => 'Left',
					'value' => 'left',
				],
				[
					'label' => 'Top Left',
					'value' => 'top left',
				],
				[
					'label' => 'Center',
					'value' => 'center',
				],
				[
					'label' => 'None',
					'value' => 'none',
				],
			],
		],
	];

	/**
	 * Responsive instance.
	 *
	 * @var Responsive
	 */
	private Responsive $responsive;

	/**
	 * Image constructor.
	 *
	 * @param Responsive $responsive Responsive instance.
	 *
	 * @return void
	 */
	public function __construct( Responsive $responsive ) {
		$this->responsive = $responsive;
	}

	/**
	 * Adds data to the block editor.
	 *
	 * @param Scripts $scripts Scripts instance.
	 *
	 * @return void
	 */
	public function scripts( Scripts $scripts ): void {
		$scripts->add_data(
			'imageOptions',
			self::SETTINGS,
			[],
			is_admin()
		);
	}

	/**
	 * Render Image Compare block.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Block content.
	 * @param array  $block   Block data.
	 *
	 * @hook  render_block_blockify/image-compare
	 *
	 * @return string
	 */
	public function render_image_compare( string $content, array $block ): string {
		return $this->responsive->add_responsive_classes( $content, $block, self::SETTINGS );
	}
}
