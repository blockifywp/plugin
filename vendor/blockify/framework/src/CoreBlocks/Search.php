<?php

declare( strict_types=1 );

namespace Blockify\Framework\CoreBlocks;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Icons\Icon;
use Blockify\Framework\Interfaces\Renderable;
use Blockify\Utilities\Str;
use WP_Block;
use function array_unique;
use function esc_attr;
use function explode;
use function trim;

/**
 * Search class.
 *
 * @since 1.0.0
 */
class Search implements Renderable {

	/**
	 * Modifies front end HTML output of block.
	 *
	 * @since 0.0.2
	 *
	 * @param string   $block_content Block HTML.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook  render_block_core/search
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$dom   = DOM::create( $block_content );
		$form  = DOM::get_element( 'form', $dom );
		$wrap  = DOM::get_element( 'div', $form );
		$input = DOM::get_element( 'input', $wrap );

		if ( ! $input ) {
			return $block_content;
		}

		$button              = DOM::get_element( 'button', $wrap );
		$use_icon            = $block['attrs']['buttonUseIcon'] ?? false;
		$button_position     = $block['attrs']['buttonPosition'] ?? 'button-outside';
		$show_icon           = ! $use_icon || ( $button_position === 'button-outside' || $button_position === 'no-button' );
		$padding             = $block['attrs']['style']['spacing']['padding'] ?? [];
		$margin              = $block['attrs']['style']['spacing']['margin'] ?? [];
		$text_color          = $block['attrs']['textColor'] ?? '';
		$text_custom         = $block['attrs']['style']['color']['text'] ?? '';
		$background_color    = $block['attrs']['backgroundColor'] ?? '';
		$background_custom   = $block['attrs']['style']['color']['background'] ?? '';
		$input_background    = $block['attrs']['inputBackgroundColor'] ?? '';
		$border              = $block['attrs']['style']['border'] ?? [];
		$border_color        = $block['attrs']['style']['border']['color'] ?? $block['attrs']['borderColor'] ?? '';
		$box_shadow          = $block['attrs']['style']['boxShadow'] ?? [];
		$shadow_preset       = esc_attr( $block['attrs']['shadowPreset'] ?? '' );
		$shadow_preset_hover = esc_attr( $block['attrs']['shadowPresetHover'] ?? '' );

		$input_styles  = CSS::string_to_array( $input->getAttribute( 'style' ) );
		$input_classes = explode( ' ', $input->getAttribute( 'class' ) );

		$button_styles = $button ? CSS::string_to_array( $button->getAttribute( 'style' ) ) : [];

		if ( ! $button || $button_position === 'button-inside' ) {
			if ( $background_color ) {
				$input_classes[] = "has-{$background_color}-background-color";
			}

			if ( $background_custom ) {
				$input_styles['background-color'] = CSS::format_custom_property( $background_custom );
			}
		}

		if ( $shadow_preset ) {
			$input_classes[] = "has-{$shadow_preset}-shadow";
		}

		if ( $shadow_preset_hover ) {
			$input_classes[] = "has-{$shadow_preset_hover}-shadow-hover";
		}

		if ( $box_shadow ) {
			$x      = $box_shadow['x'] ?? '0';
			$y      = $box_shadow['y'] ?? '0';
			$blur   = $box_shadow['blur'] ?? '0';
			$spread = $box_shadow['spread'] ?? '0';
			$color  = $box_shadow['color'] ?? '';

			$input_styles['box-shadow'] = "{$x}px {$y}px {$blur}px {$spread}px {$color}";
		}

		if ( $padding['top'] ?? '' ) {
			$input_styles['padding-top'] = CSS::format_custom_property( $padding['top'] ?? '' );
		}

		if ( $padding['right'] ?? '' ) {
			$input_styles['padding-right'] = CSS::format_custom_property( $padding['right'] ?? '' );
		}

		if ( $padding['bottom'] ?? '' ) {
			$input_styles['padding-bottom'] = CSS::format_custom_property( $padding['bottom'] ?? '' );
		}

		if ( $padding['left'] ?? '' ) {
			$padding_left = 'calc(' . CSS::format_custom_property( $padding['left'] ?? '' ) . ' * 2)';

			if ( $show_icon ) {
				$padding_left = 'calc(1em + (' . CSS::format_custom_property( $padding['left'] ?? '' ) . ' * 2))';
			}

			$input_styles['padding-left'] = $padding_left;
		}

		if ( $border['width'] ?? '' ) {
			$input_styles['border-width'] = CSS::format_custom_property( $border['width'] ?? '' );
		}

		if ( $border['style'] ?? '' ) {
			$input_styles['border-style'] = CSS::format_custom_property( $border['style'] ?? '' );
		}

		if ( $border_color ) {
			$input_styles['border-color'] = CSS::format_custom_property( $border_color );
		}

		if ( $border['radius'] ?? '' ) {
			$input_styles['border-radius'] = CSS::format_custom_property( $border['radius'] ?? '' );
		}

		if ( $text_color || $text_custom ) {
			$input_styles['color'] = $text_color ?? $text_custom;
		}

		if ( $input_background ) {
			foreach ( $input_classes as $index => $class ) {
				if ( Str::contains_any( $class, 'has-background', '-background-color' ) ) {
					unset( $input_classes[ $index ] );
				}
			}

			unset( $input_styles['color'] );

			$input_styles['background-color'] = CSS::format_custom_property( $input_background );
		}

		if ( $input_styles ) {
			$input_styles['height'] = 'auto';

			$input->setAttribute(
				'style',
				CSS::array_to_string( $input_styles )
			);
		}

		$input->setAttribute(
			'class',
			implode( ' ', array_unique( $input_classes ) )
		);

		if ( $button && $button_styles ) {
			$button->setAttribute(
				'style',
				CSS::array_to_string( $button_styles )
			);
		}

		$form_styles = CSS::string_to_array( $form->getAttribute( 'style' ) );
		$form_styles = CSS::add_shorthand_property( $form_styles, 'margin', $margin );

		if ( $form_styles ) {
			$form->setAttribute(
				'style',
				CSS::array_to_string( $form_styles )
			);
		}

		$wrap_styles = CSS::string_to_array( $wrap->getAttribute( 'style' ) );
		$gap         = $block['attrs']['style']['spacing']['blockGap'] ?? '';

		if ( $gap ) {
			$wrap_styles['gap'] = CSS::format_custom_property( $gap );
		}

		if ( $border['radius'] ?? '' ) {
			$wrap_styles['border-radius'] = CSS::format_custom_property( $border['radius'] ?? '' );
		}

		if ( $wrap_styles ) {
			$wrap->setAttribute(
				'style',
				CSS::array_to_string( $wrap_styles )
			);
		}

		if ( $show_icon ) {
			$svg_dom = DOM::create( Icon::get_svg( 'wordpress', 'search' ) );
			$svg     = DOM::get_element( 'svg', $svg_dom );

			if ( ! $svg ) {
				return $dom->saveHTML();
			}

			$svg_classes   = explode( ' ', $svg->getAttribute( 'class' ) );
			$svg_styles    = CSS::string_to_array( $svg->getAttribute( 'style' ) );
			$svg_classes[] = 'wp-block-search__icon';

			if ( $padding['left'] ?? '' ) {
				$left = CSS::format_custom_property( $padding['left'] );

				$svg_styles['left'] = 'calc(0.25em + (' . $left . ' / 2))';
			}

			$svg->setAttribute( 'class', trim( implode( ' ', $svg_classes ) ) );

			if ( $svg_styles ) {
				$svg->setAttribute( 'style', CSS::array_to_string( $svg_styles ) );
			}

			$imported_svg = $dom->importNode( $svg, true );
			$wrap->insertBefore( $imported_svg, $input );
		}

		$post_type = esc_attr( $block['attrs']['postType'] ?? '' );

		if ( $post_type ) {
			$form = DOM::get_element( 'form', $dom );

			if ( $form ) {
				$post_type_field = DOM::create_element( 'input', $dom );
				$post_type_field->setAttribute( 'type', 'hidden' );
				$post_type_field->setAttribute( 'name', 'post_type' );
				$post_type_field->setAttribute( 'value', $post_type );

				$form->insertBefore( $post_type_field, $form->firstChild );
			}
		}

		return $dom->saveHTML();
	}

}
