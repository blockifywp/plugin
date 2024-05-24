<?php

declare( strict_types=1 );

namespace Blockify\Framework\BlockSettings;

use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function is_array;
use function trim;

/**
 * Transform class.
 *
 * @since 1.0.0
 */
class Transform implements Renderable {

	private const UNITS = [
		'rotate'     => 'deg',
		'rotateX'    => 'deg',
		'rotateY'    => 'deg',
		'scale'      => '',
		'scaleX'     => '',
		'scaleY'     => '',
		'skew'       => 'deg',
		'skewX'      => 'deg',
		'skewY'      => 'deg',
		'translateX' => '',
		'translateY' => '',
		'translateZ' => '',
	];

	/**
	 * Transform settings.
	 *
	 * @param string   $block_content Block content.
	 * @param array    $block         Block settings.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$attrs           = $block['attrs'] ?? [];
		$transform       = $attrs['style']['transform'] ?? null;
		$transform_hover = $attrs['style']['transformHover'] ?? null;

		if ( ! $transform && ! $transform_hover ) {
			return $block_content;
		}

		$dom   = DOM::create( $block_content );
		$first = DOM::get_element( '*', $dom );

		if ( ! $first ) {
			return $block_content;
		}

		$classes   = DOM::get_classes( $first );
		$classes[] = 'has-transform';

		DOM::add_classes( $first, $classes );

		$styles = DOM::get_styles( $first );

		if ( is_array( $transform ) ) {
			$transform_value = '';

			foreach ( $transform as $key => $value ) {
				$unit            = self::UNITS[ $key ] ?? '';
				$transform_value .= "{$key}({$value}{$unit}) ";
			}

			$styles['--transform'] = trim( $transform_value );
		}

		if ( is_array( $transform_hover ) ) {
			$transform_hover_value = '';

			foreach ( $transform_hover as $key => $value ) {
				$unit                  = self::UNITS[ $key ] ?? '';
				$transform_hover_value .= "{$key}({$value}{$unit}) ";
			}

			$styles['--transform-hover'] = trim( $transform_hover_value );
		}

		DOM::add_styles( $first, $styles );

		return $dom->saveHTML();
	}

}
