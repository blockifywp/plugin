<?php

declare( strict_types=1 );

namespace Blockify\Framework\CoreBlocks;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Dom\JS;
use Blockify\Framework\BlockSettings\Responsive;
use Blockify\Framework\BlockSettings\Transform;
use Blockify\Framework\Interfaces\Renderable;
use Blockify\Icons\Icon;
use Blockify\Utilities\Str;
use WP_Block;
use function array_unique;
use function esc_attr;
use function esc_html;
use function esc_url;
use function explode;
use function implode;
use function in_array;
use function str_contains;
use function str_replace;
use function wp_get_global_settings;

/**
 * Button block.
 *
 * @since 0.0.2
 */
class Button implements Renderable {

	/**
	 * Responsive settings.
	 *
	 * @var Responsive
	 */
	private Responsive $responsive;

	/**
	 * Transform settings.
	 *
	 * @var Transform
	 */
	private Transform $transform;

	/**
	 * Constructor.
	 *
	 * @param Responsive $responsive Block settings.
	 *
	 * @return void
	 */
	public function __construct( Responsive $responsive, Transform $transform ) {
		$this->responsive = $responsive;
		$this->transform  = $transform;
	}

	/**
	 * Modifies front end HTML output of block.
	 *
	 * @since 0.0.2
	 *
	 * @param string   $block_content The block content.
	 * @param array    $block         The full block, including name and attributes.
	 * @param WP_Block $instance      The block instance.
	 *
	 * @hook  render_block 9
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$block_name = $block['blockName'] ?? null;

		// Using render_block for earlier priority.
		if ( 'core/button' !== $block_name ) {
			return $block_content;
		}

		$attrs      = $block['attrs'] ?? [];
		$class_name = $attrs['className'] ?? '';
		$dom        = DOM::create( $block_content );
		$div        = DOM::get_element( 'div', $dom );

		if ( ! $div ) {
			$div = DOM::create_element( 'div', $dom );

			$div->setAttribute( 'class', 'wp-block-button ' . $class_name );

			$dom->appendChild( $div );
		}

		$link = DOM::get_element( 'a', $div );

		if ( ! $link ) {
			$link = DOM::create_element( 'a', $dom );

			$div->appendChild( $link );
		}

		$link_classes = DOM::get_classes( $link );
		$link_styles  = DOM::get_styles( $link );

		if ( empty( $link_classes ) ) {
			$link_classes[] = 'wp-element-button';
			$link_classes[] = 'wp-block-button__link';
		}

		if ( str_contains( $class_name, 'is-style-outline' ) ) {
			$text_color        = $attrs['textColor'] ?? null;
			$custom_text_color = $attrs['style']['color']['text'] ?? null;

			if ( $text_color || $custom_text_color ) {
				$link_classes[] = 'has-text-color';
			}

			if ( $text_color ) {
				$link_classes[] = 'has-' . $text_color . '-color';
			}

			if ( $custom_text_color ) {
				$link_styles['color'] = $custom_text_color;
			}
		}

		$div_classes = DOM::get_classes( $div );
		$div_styles  = DOM::get_styles( $div );

		if ( isset( $attrs['style']['border'] ) || isset( $attrs['borderColor'] ) ) {
			$global_settings = wp_get_global_settings();

			foreach ( $div_classes as $index => $class ) {
				if ( str_contains( $class, '-border-' ) ) {
					unset( $div_classes[ $index ] );
				}
			}

			foreach ( $div_styles as $key => $style ) {
				if ( str_contains( $key, 'border-' ) ) {
					unset( $div_styles[ $key ] );
				}
			}

			$border_width = $attrs['style']['border']['width'] ?? null;
			$border_color = $attrs['style']['border']['color'] ?? null;

			if ( $border_width || $border_color ) {
				$border_width = $border_width ?? $global_settings['custom']['border']['width'];

				$link_styles['line-height'] = "calc(1em - $border_width)";
			}
		}

		$size = esc_attr( $attrs['size'] ?? 'medium' );

		if ( in_array( $size, [ 'small', 'large' ] ) ) {
			$div_classes[] = "is-style-$size";
		}

		$gap = $attrs['style']['spacing']['blockGap'] ?? null;

		if ( $gap ) {
			$link_styles['gap'] = CSS::format_custom_property( $gap );
		}

		$padding = $attrs['style']['spacing']['padding'] ?? [];

		if ( $padding ) {
			$link_styles = CSS::add_shorthand_property( $link_styles, 'padding', $padding );
		}

		DOM::add_classes( $link, $link_classes );
		DOM::add_styles( $link, $link_styles );
		DOM::add_classes( $div, $div_classes );
		DOM::add_styles( $div, $div_styles );

		$url = esc_url( $attrs['url'] ?? '' );

		if ( ! $url ) {
			$href = $link->getAttribute( 'href' );

			if ( $href ) {
				$link->setAttribute( 'href', $href );
			} else {

				$on_click = $attrs['onclick'] ?? null;

				if ( ! $on_click ) {
					$link->setAttribute( 'href', '#' );
				} else {
					$link->setAttribute( 'href', 'javascript:void(0)' );
				}
			}
		}

		$aria_label = $attrs['ariaLabel'] ?? null;

		if ( $aria_label ) {
			$link->setAttribute( 'aria-label', esc_html( $aria_label ) );
		}

		$href = $link->getAttribute( 'href' );

		if ( ! $href || '#' === $href || 'javascript:void(0)' === $href ) {
			$button        = DOM::change_tag_name( 'button', $link );
			$button_styles = DOM::get_styles( $button );
			$padding       = $attrs['style']['spacing']['padding'] ?? [];
			$button_styles = CSS::add_shorthand_property( $button_styles, 'padding', $padding );

			DOM::add_styles( $button, $button_styles );
		}

		$block_content = $dom->saveHTML();
		$block_content = $this->transform->render( $block_content, $block, $instance );
		$icon_set      = $attrs['iconSet'] ?? '';
		$icon_name     = $attrs['iconName'] ?? '';
		$icon          = $icon_set && $icon_name ? Icon::get_svg( $icon_set, $icon_name ) : '';

		if ( $icon ) {
			$block_content = $this->render_icon( $block_content, $block, $icon );
		}

		return $this->correct_urls( $block, $block_content );
	}

	/**
	 * Renders button icon.
	 *
	 * @since 0.0.2
	 *
	 * @param string $block_content Block HTML.
	 * @param array  $block         Block attributes.
	 * @param string $icon          Icon SVG.
	 *
	 * @return string
	 */
	public function render_icon( string $block_content, array $block, string $icon ): string {
		$dom   = DOM::create( $block_content );
		$div   = DOM::get_element( 'div', $dom );
		$attrs = $block['attrs'] ?? [];

		if ( ! $div ) {
			$div   = DOM::create_element( 'div', $dom );
			$class = esc_attr( $attrs['className'] ?? '' );

			$div->setAttribute( 'class', 'wp-block-button ' . $class );

			$dom->appendChild( $div );
		}

		$div_styles = CSS::string_to_array( $div->getAttribute( 'style' ) );

		foreach ( $div_styles as $key => $style ) {
			if ( str_contains( $key, '--wp--custom--icon--' ) ) {
				unset( $div_styles[ $key ] );
			}
		}

		$div->setAttribute( 'style', CSS::array_to_string( $div_styles ) );

		$link = DOM::get_element( 'a', $div );

		if ( ! $link ) {
			$link = DOM::get_element( 'button', $div );
		}

		if ( ! $link ) {
			$link = DOM::create_element( 'a', $dom );

			$link->setAttribute( 'class', 'wp-block-button__link wp-element-button' );

			$div->appendChild( $link );
		}

		$svg_dom  = DOM::create( $icon );
		$svg      = DOM::get_element( 'svg', $svg_dom );
		$imported = DOM::node_to_element( $dom->importNode( $svg, true ) );
		$classes  = explode( ' ', $link->getAttribute( 'class' ) );
		$styles   = CSS::string_to_array( $link->getAttribute( 'style' ) );

		$text_color = $attrs['textColor'] ?? null;

		if ( $text_color ) {
			$styles['color'] = CSS::format_custom_property( $text_color );
		}

		$background_color = $attrs['backgroundColor'] ?? null;

		if ( $background_color ) {
			$styles['background-color'] = CSS::format_custom_property( $background_color );
			$classes[]                  = 'has-background';
		}

		$border_width = $attrs['style']['border']['width'] ?? null;
		$border_style = $attrs['style']['border']['style'] ?? null;
		$border_color = $attrs['style']['border']['color'] ?? null;

		if ( $border_width ) {
			$styles['border-width'] = CSS::format_custom_property( $border_width );
		}

		if ( $border_style ) {
			$styles['border-style'] = CSS::format_custom_property( $border_style );
		}

		if ( $border_color ) {
			$styles['border-color'] = CSS::format_custom_property( $border_color );
		}

		if ( $styles ) {
			$link->setAttribute( 'style', CSS::array_to_string( $styles ) );
		}

		$link->setAttribute( 'class', implode( ' ', array_unique( $classes ) ) );

		$on_click = $attrs['onclick'] ?? null;

		if ( $on_click ) {
			$link->setAttribute( 'onclick', JS::format_inline_js( $on_click ) );
		}

		$url = $attrs['url'] ?? $link->getAttribute( 'href' );

		if ( ! $url ) {
			if ( ! $on_click ) {
				$link->setAttribute( 'href', '#' );
			} else {
				DOM::change_tag_name( 'button', $link );

				$link->setAttribute( 'href', 'javascript:void(0)' );
			}
		}

		$size = esc_attr( $attrs['iconSize'] ?? null ) ?: '20';

		if ( str_contains( $size, 'var' ) ) {
			$svg_styles = CSS::string_to_array( $svg->getAttribute( 'style' ) );

			unset ( $svg_styles['enable-background'] );

			$svg_styles['height'] = CSS::format_custom_property( $size );
			$svg_styles['width']  = CSS::format_custom_property( $size );

			$imported->setAttribute( 'style', CSS::array_to_string( $svg_styles ) );

		} else {
			$imported->setAttribute( 'height', $size );
			$imported->setAttribute( 'width', $size );
		}

		$fill = $imported->getAttribute( 'fill' );

		if ( ! $fill ) {
			$imported->setAttribute( 'fill', 'currentColor' );
		}

		$icon_position = $attrs['iconPosition'] ?? 'end';

		if ( $icon_position === 'start' ) {
			$svg = $link->insertBefore( $imported, $link->firstChild );
		} else {
			$svg = $link->appendChild( $imported );
		}

		$title = $svg->insertBefore(
			DOM::create_element( 'title', $dom ),
			$svg->firstChild
		);

		$text = $dom->createTextNode( Str::title_case( $attrs['iconName'] ?? '' ) );

		if ( $text ) {
			$title->appendChild( $text );
		}

		return $this->responsive->add_responsive_classes(
			$dom->saveHTML(),
			$block,
			Responsive::SETTINGS
		);
	}

	/**
	 * Corrects URLs in block content.
	 *
	 * @since 0.0.2
	 *
	 * @param array  $block         Block attributes.
	 * @param string $block_content Block content.
	 *
	 * @return string
	 */
	private function correct_urls( array $block, string $block_content ): string {
		$inner_html = $block['innerHTML'] ?? $block['innerContent'] ?? $block_content;
		$back_urls  = [
			'javascript:history.go(-1)',
			'javascript: history.go(-1)',
		];

		foreach ( $back_urls as $back_url ) {
			if ( str_contains( $inner_html, $back_url ) ) {
				$block_content = str_replace( 'href="#"', 'href="' . $back_url . '"', $block_content );
			}
		}

		if ( str_contains( $block_content, 'javascript:void' ) ) {
			$block_content = str_replace(
				[
					'http://javascript:void',
					'target="_blank"',
				],
				[
					'javascript:void',
					'disabled',
				],
				$block_content
			);
		}

		if ( str_contains( $block_content, 'href="http://#"' ) ) {
			$block_content = str_replace(
				[
					'href="http://#"',
					'target="_blank"',
				],
				[
					'href="#"',
					'',
				],
				$block_content
			);
		}

		if ( str_contains( $block_content, 'http://http' ) ) {
			$block_content = str_replace( 'http://http', 'http', $block_content );
		}

		return $block_content;
	}
}
