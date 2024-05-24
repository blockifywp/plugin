<?php

declare( strict_types=1 );

namespace Blockify\Framework\BlockSettings;

use Blockify\Framework\InlineAssets\Styleable;
use Blockify\Framework\InlineAssets\Styles;
use Blockify\Utilities\Block;
use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Icons\Icon;
use Blockify\Framework\Interfaces\Renderable;
use Blockify\Utilities\Str;
use DOMElement;
use WP_Block;
use function array_merge;
use function esc_attr;
use function esc_url;
use function explode;
use function implode;
use function in_array;
use function is_archive;
use function property_exists;
use function str_replace;

/**
 * Placeholder class.
 *
 * @since 1.0.0
 */
class Placeholder implements Renderable, Styleable {

	/**
	 * Conditionally adds placeholder styles.
	 *
	 * @since 0.9.10
	 *
	 * @param Styles $styles Styles instance.
	 *
	 * @return void
	 */
	public function styles( Styles $styles ): void {
		$styles->add_file(
			'block-extensions/placeholder-image.css',
			[ 'is-placeholder' ],
			is_archive() || Block::is_rendering_preview()
		);
	}

	/**
	 * Returns placeholder HTML element string.
	 *
	 * @since 0.9.10
	 *
	 * @param string   $block_content Block content.
	 * @param array    $block         Block attributes.
	 * @param WP_Block $instance      Block object.
	 *
	 * @hook  render_block 11
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$name = $block['blockName'] ?? '';

		if ( ! Str::contains_any( $name, 'image' ) ) {
			return $block_content;
		}

		$attrs           = $block['attrs'] ?? [];
		$id              = $attrs['id'] ?? '';
		$has_icon        = ( $attrs['iconSet'] ?? '' ) && ( $attrs['iconName'] ?? '' ) || ( $attrs['iconSvgString'] ?? '' );
		$style           = $attrs['style'] ?? [];
		$has_svg         = $style['svgString'] ?? '';
		$use_placeholder = $attrs['usePlaceholder'] ?? true;

		if ( $id || $has_icon || $has_svg ) {
			return $block_content;
		}

		if ( ! $use_placeholder || $use_placeholder === 'none' ) {
			return $block_content;
		}

		if ( Str::contains_any( $block_content, 'is-style-icon', 'is-style-svg' ) ) {
			return $block_content;
		}

		$dom    = DOM::create( $block_content );
		$figure = DOM::get_element( 'figure', $dom );
		$img    = DOM::get_element( 'img', $figure );
		$link   = DOM::get_element( 'a', $figure );
		$svg    = DOM::get_element( 'svg', $link ?? $figure );

		if ( $svg ) {
			return $block_content;
		}

		if ( $img && $img->getAttribute( 'src' ) ) {
			return $block_content;
		}

		$block_name = str_replace(
			'core/',
			'',
			$block['blockName'] ?? ''
		);

		$block_content = $block_content ?: "<figure class='wp-block-{$block_name}'></figure>";
		$dom           = DOM::create( $block_content );
		$figure        = DOM::get_element( 'figure', $dom );

		if ( ! $figure instanceof DOMElement ) {
			return $block_content;
		}

		$img = DOM::get_element( 'img', $figure );

		if ( $img instanceof DOMElement ) {
			$figure->removeChild( $img );
		}

		$classes = explode( ' ', $figure->getAttribute( 'class' ) );

		if ( ! in_array( 'is-placeholder', $classes, true ) ) {
			$classes[] = 'is-placeholder';
		}

		if ( $block['align'] ?? null ) {
			$classes[] = 'align' . $block['align'];
		}

		$is_link     = $block['attrs']['isLink'] ?? false;
		$placeholder = Icon::get_placeholder( $dom );

		if ( $placeholder->tagName === 'svg' ) {
			$classes[] = 'has-placeholder-icon';
		}

		if ( $is_link ) {
			$context = (object) ( property_exists( $instance, 'context' ) ? $instance->context : null );
			$link    = DOM::create_element( 'a', $dom );
			$id_key  = 'postId';

			if ( property_exists( $context, $id_key ) ) {
				$post_id = $context->$id_key ?? null;
				$href    = get_permalink( $post_id );

				if ( $href ) {
					$link->setAttribute( 'href', esc_url( $href ) );
				}
			}

			$link_target = $block['linkTarget'] ?? '';

			if ( $link_target ) {
				$link->setAttribute( 'target', $link_target );
			}

			$rel = esc_attr( $block['rel'] ?? '' );

			if ( $rel ) {
				$link->setAttribute( 'rel', $rel );
			}

			$link_classes = explode( ' ', $link->getAttribute( 'class' ) );

			if ( ! in_array( 'wp-block-image__link', $link_classes, true ) ) {
				$link_classes[] = 'wp-block-image__link';
			}

			if ( ! in_array( 'is-placeholder', $classes, true ) && ! in_array( 'is-placeholder', $link_classes, true ) ) {
				$link_classes[] = 'is-placeholder';
			}

			$link->setAttribute(
				'class',
				implode( ' ', $link_classes )
			);
			$link->appendChild( $placeholder );
			$figure->appendChild( $link );
		} else {
			$figure->appendChild( $placeholder );
		}

		$style            = $block['attrs']['style'] ?? [];
		$spacing          = $style['spacing'] ?? [];
		$margin           = $spacing['margin'] ?? [];
		$padding          = $spacing['padding'] ?? [];
		$border           = $style['border'] ?? [];
		$radius           = $border['radius'] ?? [];
		$aspect_ratio     = $block['attrs']['aspectRatio'] ?? null;
		$background_color = $block['attrs']['backgroundColor'] ?? null;

		$styles = [
			'width'                      => $block['width'] ?? null,
			'height'                     => $block['height'] ?? null,
			'border-width'               => $border['width'] ?? null,
			'border-style'               => $border['style'] ?? ( ( $border['width'] ?? null ) ? 'solid' : null ),
			'border-color'               => $border['color'] ?? null,
			'border-top-left-radius'     => $radius['topLeft'] ?? null,
			'border-top-right-radius'    => $radius['topRight'] ?? null,
			'border-bottom-left-radius'  => $radius['bottomLeft'] ?? null,
			'border-bottom-right-radius' => $radius['bottomRight'] ?? null,
			'position'                   => $style['position']['all'] ?? null,
			'top'                        => $style['top']['all'] ?? null,
			'right'                      => $style['right']['all'] ?? null,
			'bottom'                     => $style['bottom']['all'] ?? null,
			'left'                       => $style['left']['all'] ?? null,
			'z-index'                    => $style['zIndex']['all'] ?? null,
		];

		$styles = CSS::add_shorthand_property( $styles, 'margin', $margin );
		$styles = CSS::add_shorthand_property( $styles, 'padding', $padding );

		if ( $aspect_ratio && $aspect_ratio !== 'auto' ) {
			$styles['aspect-ratio'] = $aspect_ratio;
		}

		if ( $background_color === 'transparent' ) {
			$classes[] = 'has-transparent-background-color';
		} else {
			$styles['background-color'] = $background_color;
		}

		$css = CSS::array_to_string(
			array_merge(
				CSS::string_to_array(
					$figure->getAttribute( 'style' )
				),
				$styles,
			)
		);

		if ( $css ) {
			$figure->setAttribute( 'style', $css );
		}

		$figure->setAttribute( 'class', implode( ' ', $classes ) );

		return $dom->saveHTML();
	}

}
