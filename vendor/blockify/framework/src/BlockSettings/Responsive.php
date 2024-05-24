<?php

declare( strict_types=1 );

namespace Blockify\Framework\BlockSettings;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\InlineAssets\Scriptable;
use Blockify\Framework\InlineAssets\Scripts;
use Blockify\Framework\InlineAssets\Styleable;
use Blockify\Framework\InlineAssets\Styles;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function _wp_to_kebab_case;
use function array_merge;
use function is_admin;
use function sprintf;
use function str_contains;
use function str_replace;

/**
 * Responsive class.
 *
 * @since 1.0.0
 */
class Responsive implements Renderable, Scriptable, Styleable {

	public const SETTINGS = [
		'position'            => [
			'property' => 'position',
			'label'    => 'Position',
			'options'  => [
				[
					'label' => '',
					'value' => '',
				],
				[
					'label' => 'Relative',
					'value' => 'relative',
				],
				[
					'label' => 'Absolute',
					'value' => 'absolute',
				],
				[
					'label' => 'Sticky',
					'value' => 'sticky',
				],
				[
					'label' => 'Fixed',
					'value' => 'fixed',
				],
				[
					'label' => 'Static',
					'value' => 'static',
				],
			],
		],
		'top'                 => [
			'property' => 'top',
			'label'    => 'Top',
		],
		'right'               => [
			'property' => 'right',
			'label'    => 'Right',
		],
		'bottom'              => [
			'property' => 'bottom',
			'label'    => 'Bottom',
		],
		'left'                => [
			'property' => 'left',
			'label'    => 'Left',
		],
		'zIndex'              => [
			'property' => 'z-index',
			'label'    => 'Z-Index',
		],
		'display'             => [
			'property' => 'display',
			'label'    => 'Display',
			'options'  => [
				[
					'label' => '',
					'value' => '',
				],
				[
					'label' => 'None',
					'value' => 'none',
				],
				[
					'label' => 'Flex',
					'value' => 'flex',
				],
				[
					'label' => 'Inline Flex',
					'value' => 'inline-flex',
				],
				[
					'label' => 'Block',
					'value' => 'block',
				],
				[
					'label' => 'Inline Block',
					'value' => 'inline-block',
				],
				[
					'label' => 'Inline',
					'value' => 'inline',
				],
				[
					'label' => 'Grid',
					'value' => 'grid',
				],
				[
					'label' => 'Inline Grid',
					'value' => 'inline-grid',
				],
				[
					'label' => 'Contents',
					'value' => 'contents',
				],
			],
		],
		'order'               => [
			'property' => 'order',
			'label'    => 'Order',
		],
		'gridTemplateColumns' => [
			'property' => 'grid-template-columns',
			'label'    => 'Columns',
		],
		'gridTemplateRows'    => [
			'property' => 'grid-template-rows',
			'label'    => 'Rows',
		],
		'gridColumnStart'     => [
			'property' => 'grid-column-start',
			'label'    => 'Column Start',
		],
		'gridColumnEnd'       => [
			'property' => 'grid-column-end',
			'label'    => 'Column End',
		],
		'gridRowStart'        => [
			'property' => 'grid-row-start',
			'label'    => 'Row Start',
		],
		'gridRowEnd'          => [
			'property' => 'grid-row-end',
			'label'    => 'Row End',
		],
		'overflow'            => [
			'property' => 'overflow',
			'label'    => 'Overflow',
			'options'  => [
				[
					'label' => '',
					'value' => '',
				],
				[
					'label' => 'Hidden',
					'value' => 'hidden',
				],
				[
					'label' => 'Visible',
					'value' => 'visible',
				],
			],
		],
		'pointerEvents'       => [
			'property' => 'pointer-events',
			'label'    => 'Pointer Events',
			'options'  => [
				[
					'label' => '',
					'value' => '',
				],
				[
					'label' => 'None',
					'value' => 'none',
				],
				[
					'label' => 'All',
					'value' => 'all',
				],
			],
		],
		'width'               => [
			'property' => 'width',
			'label'    => 'Width',
		],
		'minWidth'            => [
			'property' => 'min-width',
			'label'    => 'Min Width',
		],
		'maxWidth'            => [
			'property' => 'max-width',
			'label'    => 'Max Width',
		],
	];

	/**
	 * Gets responsive classes for a given property.
	 *
	 * @since 1.0.0
	 *
	 * @param string $block_content HTML content.
	 * @param array  $block         Block data.
	 * @param array  $options       Block options.
	 * @param bool   $image         Is an image block.
	 *
	 * @return string
	 */
	public static function add_responsive_classes( string $block_content, array $block, array $options, bool $image = false ): string {
		$dom   = DOM::create( $block_content );
		$first = DOM::get_element( '*', $dom );

		if ( ! $first ) {
			return $block_content;
		}

		$element = $first;

		if ( $image ) {
			$link    = DOM::get_element( 'a', $first );
			$element = $link ? DOM::get_element( 'img', $link ) : DOM::get_element( 'img', $first );
		}

		if ( ! $element ) {
			return $block_content;
		}

		$style   = $block['attrs']['style'] ?? [];
		$classes = explode( ' ', $element->getAttribute( 'class' ) );

		foreach ( $options as $key => $args ) {
			if ( ! isset( $style[ $key ] ) || $style[ $key ] === '' ) {
				continue;
			}

			$property = _wp_to_kebab_case( $key );

			if ( isset( $args['options'] ) ) {
				$both    = $style[ $key ]['all'] ?? '';
				$mobile  = $style[ $key ]['mobile'] ?? '';
				$desktop = $style[ $key ]['desktop'] ?? '';

				if ( $both ) {
					$classes[] = "has-{$property}-{$both}";
				}

				if ( $mobile ) {
					$classes[] = "has-{$property}-{$mobile}-mobile";
				}

				if ( $desktop ) {
					$classes[] = "has-{$property}-{$desktop}-desktop";
				}
			} else {
				$classes[] = "has-{$property}";
			}

			$element->setAttribute( 'class', implode( ' ', $classes ) );

			$block_content = $dom->saveHTML();
		}

		return $block_content;
	}

	/**
	 * Adds responsive styles to DOM.
	 *
	 * @since 1.0.0
	 *
	 * @param string $block_content HTML content.
	 * @param array  $block         Block data.
	 * @param array  $options       Block options.
	 *
	 * @return string
	 */
	public static function add_responsive_styles( string $block_content, array $block, array $options ): string {
		$style = $block['attrs']['style'] ?? [];

		if ( ! $style ) {
			return $block_content;
		}

		foreach ( $options as $key => $args ) {

			if ( ! isset( $style[ $key ] ) ) {
				continue;
			}

			// Has utility class.
			if ( isset( $args['options'] ) ) {
				continue;
			}

			$dom   = DOM::create( $block_content );
			$first = DOM::get_element( '*', $dom );

			if ( ! $first ) {
				continue;
			}

			$styles   = CSS::string_to_array( $first->getAttribute( 'style' ) );
			$property = _wp_to_kebab_case( $key );
			$both     = $style[ $key ]['all'] ?? '';
			$mobile   = $style[ $key ]['mobile'] ?? '';
			$desktop  = $style[ $key ]['desktop'] ?? '';

			if ( $both ) {
				$styles[ '--' . $property ] = $both;
			}

			if ( $mobile ) {
				$styles[ '--' . $property . '-mobile' ] = $mobile;
			}

			if ( $desktop ) {
				$styles[ '--' . $property . '-desktop' ] = $desktop;
			}

			$first->setAttribute( 'style', CSS::array_to_string( $styles ) );

			$block_content = $dom->saveHTML();
		}

		return $block_content;
	}

	/**
	 * Adds inline block positioning classes.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $block_content Block content.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook  render_block 11
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$style = $block['attrs']['style'] ?? [];

		if ( ! $style ) {
			return $block_content;
		}

		$block_content = $this->add_responsive_classes(
			$block_content,
			$block,
			self::SETTINGS
		);

		$block_content = $this->add_responsive_styles(
			$block_content,
			$block,
			self::SETTINGS
		);

		return $block_content;
	}

	/**
	 * Add default block supports.
	 *
	 * @since 0.9.10
	 *
	 * @param Scripts $scripts Scripts service.
	 *
	 * @return void
	 */
	public function scripts( Scripts $scripts ): void {
		$scripts->add_data(
			'responsiveOptions',
			self::SETTINGS,
			[],
			is_admin()
		);
	}

	/**
	 * Conditionally adds CSS for utility classes
	 *
	 * @since 0.9.19
	 *
	 * @param Styles $styles Styles service.
	 *
	 * @return void
	 */
	public function styles( Styles $styles ): void {
		$styles->add_callback( [ $this, 'get_styles' ] );
	}

	/**
	 * Returns inline styles for responsive classes.
	 *
	 * @since 0.9.19
	 *
	 * @param string $template_html Template HTML.
	 * @param bool   $load_all      Load all assets.
	 *
	 * @return string
	 */
	public function get_styles( string $template_html, bool $load_all ): string {
		$options = array_merge(
			self::SETTINGS,
			Image::SETTINGS,
		);
		$both    = '';
		$mobile  = '';
		$desktop = '';

		foreach ( $options as $key => $args ) {
			$property       = _wp_to_kebab_case( $key );
			$select_options = $args['options'] ?? [];

			foreach ( $select_options as $option ) {
				$value = $option['value'] ?? '';

				if ( ! $value ) {
					continue;
				}

				$formatted_value = $value;

				if ( 'aspect-ratio' === $property ) {
					$formatted_value = str_replace( '/', '\/', $formatted_value );
				}

				if ( $load_all || str_contains( $template_html, " has-{$property}-{$value}" ) ) {
					$both .= sprintf(
						'.has-%1$s-%3$s{%1$s:%2$s !important}',
						$property,
						$value,
						$formatted_value,
					);
				}

				if ( $load_all || str_contains( $template_html, " has-{$property}-{$value}-mobile" ) ) {
					$mobile .= sprintf(
						'.has-%1$s-%3$s-mobile{%1$s:%2$s !important}',
						$property,
						$value,
						$formatted_value,
					);
				}

				if ( $load_all || str_contains( $template_html, " has-{$property}-{$value}-desktop" ) ) {
					$desktop .= sprintf(
						'.has-%1$s-%3$s-desktop{%1$s:%2$s !important}',
						$property,
						$value,
						$formatted_value,
					);
				}
			}

			// Has custom value.
			if ( ! $select_options ) {

				if ( $load_all || str_contains( $template_html, " has-$property" ) ) {
					$both .= sprintf(
						'.has-%1$s{%1$s:var(--%1$s)}',
						$property
					);
				}

				if ( $load_all || str_contains( $template_html, "--$property-mobile" ) ) {
					$mobile .= sprintf(
						'.has-%1$s{%1$s:var(--%1$s-mobile,var(--%1$s))}',
						$property
					);
				}

				if ( $load_all || str_contains( $template_html, "--$property-desktop" ) ) {
					$desktop .= sprintf(
						'.has-%1$s{%1$s:var(--%1$s-desktop,var(--%1$s))}',
						$property
					);
				}
			}
		}

		$css = '';

		if ( $both ) {
			$css .= $both;
		}

		if ( $mobile ) {
			$css .= sprintf( '@media(max-width:781px){%s}', $mobile );
		}

		if ( $desktop ) {
			$css .= sprintf( '@media(min-width:782px){%s}', $desktop );
		}

		return $css;
	}

}
