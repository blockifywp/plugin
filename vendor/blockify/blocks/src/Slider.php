<?php

declare( strict_types=1 );

namespace Blockify\Blocks;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Utilities\Str;
use WP_Block;
use function gettype;

/**
 * Register the slider block.
 *
 * @since 1.0.0
 */
class Slider extends AbstractBlock {

	/**
	 * Modifies front end HTML output of block.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $attributes Block data.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block object.
	 *
	 * @return string
	 */
	public function render( array $attributes, string $content, WP_Block $block ): string {
		$dom = DOM::create( $content );
		$div = DOM::get_element( 'div', $dom );

		if ( ! $div ) {
			return $content;
		}

		$defaults = [
			'type'         => 'slider',
			'perPage'      => 3,
			'perMove'      => 1,
			'loop'         => 'loop',
			'autoplay'     => 'true',
			'breakpoints'  => 'true',
			'pauseOnHover' => 'true',
			'showArrows'   => 'true',
			'showDots'     => 'true',
			'drag'         => 'true',
			'speed'        => 400,
			'interval'     => 5000,
			'direction'    => 'ltr',
			'height'       => 'auto',
			'gap'          => '0',
		];

		$attributes['gap'] = CSS::format_custom_property( $attributes['style']['spacing']['blockGap'] ?? $defaults['gap'] );

		foreach ( $defaults as $key => $default ) {
			$type = gettype( $default );

			if ( $type === 'integer' ) {
				$value = (string) ( $attributes[ $key ] ?? $default );
			} else {
				$value = (string) ( $attributes[ $key ] ?? $default );
				$value = $value === '1' ? 'true' : ( $value === '0' ? 'false' : $value );
			}

			if ( $key === 'height' && ! ( $attributes['height'] ?? '' ) ) {
				continue;
			}

			$qualified_name = 'data-' . Str::camel_to_kebab( $key );

			$div->setAttribute( $qualified_name, $value );
		}

		return $dom->saveHTML();
	}
}


