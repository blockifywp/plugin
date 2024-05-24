<?php

declare( strict_types=1 );

namespace Blockify\Framework\DesignSystem;

use Blockify\Framework\InlineAssets\Styleable;
use Blockify\Framework\InlineAssets\Styles;
use Blockify\Dom\CSS;
use function apply_filters;
use function array_merge;
use function wp_get_global_settings;
use function wp_get_global_styles;

/**
 * Adds dynamic custom properties.
 *
 * @since 0.9.19
 */
class CustomProperties implements Styleable {

	/**
	 * Adds dynamic custom properties.
	 *
	 * @since 0.9.19
	 *
	 * @param Styles $styles Styles service.
	 *
	 * @return void
	 */
	public function styles( Styles $styles ): void {
		$inline = 'body{' . CSS::array_to_string( $this->get_custom_properties() ) . '}';

		$styles->add_string( $inline );
	}

	/**
	 * Gets dynamic custom properties.
	 *
	 * @since 0.9.19
	 *
	 * @return array
	 */
	public function get_custom_properties(): array {
		$global_settings     = wp_get_global_settings();
		$global_styles       = wp_get_global_styles();
		$custom              = $global_settings['custom'] ?? [];
		$transition_property = $custom['transition']['property'] ?? 'all';
		$transition_duration = $custom['transition']['duration'] ?? '0.3s';
		$transition_timing   = $custom['transition']['timingFunction'] ?? 'ease-in';
		$body_background     = $global_styles['color']['background'] ?? null;
		$body_color          = $global_styles['color']['text'] ?? null;
		$body_font_family    = $global_styles['typography']['fontFamily'] ?? null;
		$body_font_size      = $global_styles['typography']['fontSize'] ?? null;
		$body_font_weight    = $global_styles['typography']['fontWeight'] ?? null;
		$box_shadow          = $custom['boxShadow'] ?? [];
		$list_gap            = $global_styles['blocks']['core/list']['spacing']['blockGap'] ?? null;

		// Button.
		$button_block         = $global_styles['blocks']['core/button'] ?? [];
		$button_element       = $global_styles['elements']['button'] ?? [];
		$button_text          = $button_element['color']['text'] ?? $button_block['color']['text'] ?? null;
		$button_background    = $button_element['color']['background'] ?? $button_block['color']['background'] ?? null;
		$button_border_radius = $button_element['border']['radius'] ?? $button_block['border']['radius'] ?? null;
		$button_border_width  = $button_element['border']['width'] ?? $button_block['border']['width'] ?? null;
		$button_font_size     = $button_element['typography']['fontSize'] ?? $button_block['typography']['fontSize'] ?? null;
		$button_font_weight   = $button_element['typography']['fontWeight'] ?? $button_block['typography']['fontWeight'] ?? null;
		$button_line_height   = $button_element['typography']['lineHeight'] ?? $button_block['typography']['lineHeight'] ?? null;
		$button_padding       = $button_element['spacing']['padding'] ?? $button_block['spacing']['padding'] ?? null;

		// Also used by b, strong elements and legend element.
		$heading_font_family    = $global_styles['elements']['heading']['typography']['fontFamily'] ?? null;
		$heading_font_weight    = $global_styles['elements']['heading']['typography']['fontWeight'] ?? null;
		$heading_line_height    = $global_styles['elements']['heading']['typography']['lineHeight'] ?? null;
		$heading_letter_spacing = $global_styles['elements']['heading']['typography']['letterSpacing'] ?? null;
		$heading_color          = $global_styles['elements']['heading']['color']['text'] ?? null;

		// Also used by placeholder image.
		$image_border_radius = $global_styles['blocks']['core/image']['border']['radius'] ?? null;

		// Search gap.
		$search_gap = $global_styles['blocks']['core/search']['spacing']['blockGap'] ?? null;

		$link_hover_color = $global_styles['elements']['link'][':hover']['color']['text'] ?? null;

		$calendar_background = $global_styles['blocks']['core/calendar']['color']['background'] ?? null;

		$styles = [
			'--scroll'                              => '0',
			'--breakpoint'                          => '782px', // Only used by JS.
			'--wp--custom--border'                  => "var(--wp--custom--border--width,1px) var(--wp--custom--border--style,solid) var(--wp--custom--border--color,#ddd)",
			'--wp--custom--transition'              => "$transition_property $transition_duration $transition_timing",
			'--wp--custom--body--background'        => $body_background,
			'--wp--custom--body--color'             => $body_color,
			'--wp--custom--body--font-family'       => $body_font_family,
			'--wp--custom--body--font-size'         => $body_font_size,
			'--wp--custom--body--font-weight'       => $body_font_weight,
			'--wp--custom--heading--font-family'    => $heading_font_family,
			'--wp--custom--heading--font-weight'    => $heading_font_weight,
			'--wp--custom--heading--line-height'    => $heading_line_height,
			'--wp--custom--heading--letter-spacing' => $heading_letter_spacing,
			'--wp--custom--heading--color'          => $heading_color,

			// Used by .button.
			'--wp--custom--button--background'      => $button_background,
			'--wp--custom--button--color'           => $button_text,
			'--wp--custom--button--padding-top'     => $button_padding['top'] ?? null,
			'--wp--custom--button--padding-right'   => $button_padding['right'] ?? null,
			'--wp--custom--button--padding-bottom'  => $button_padding['bottom'] ?? null,
			'--wp--custom--button--padding-left'    => $button_padding['left'] ?? null,
			'--wp--custom--button--padding'         => 'var(--wp--custom--button--padding-top) var(--wp--custom--button--padding-right) var(--wp--custom--button--padding-bottom) var(--wp--custom--button--padding-left)',
			'--wp--custom--button--border-radius'   => $button_border_radius,
			'--wp--custom--button--border-width'    => $button_border_width,
			'--wp--custom--button--font-size'       => $button_font_size,
			'--wp--custom--button--font-weight'     => $button_font_weight,
			'--wp--custom--button--line-height'     => $button_line_height,

			// Image.
			'--wp--custom--image--border--radius'   => $image_border_radius,

			// Search.
			'--wp--custom--search--gap'             => $search_gap,

			// Link hover color used by navigation.
			'--wp--custom--link--hover--color'      => $link_hover_color,
		];

		if ( $list_gap ) {
			$styles['--wp--custom--list--gap'] = $list_gap;
		}

		if ( $calendar_background ) {
			$styles['--wp--custom--calendar--background'] = $calendar_background;
		}

		$inset      = $box_shadow['inset'] ?? ' ';
		$x          = $box_shadow['x'] ?? '0px';
		$y          = $box_shadow['y'] ?? '0px';
		$blur       = $box_shadow['blur'] ?? '0px';
		$spread     = $box_shadow['spread'] ?? '0px';
		$color      = $box_shadow['color'] ?? 'rgba(0,0,0,0)';
		$box_shadow = "$inset $x $y $blur $spread $color";

		$styles = array_merge(
			$styles,
			[
				'--wp--custom--box-shadow--inset'  => $inset,
				'--wp--custom--box-shadow--x'      => $x,
				'--wp--custom--box-shadow--y'      => $y,
				'--wp--custom--box-shadow--blur'   => $blur,
				'--wp--custom--box-shadow--spread' => $spread,
				'--wp--custom--box-shadow--color'  => $color,
				'--wp--custom--box-shadow'         => $box_shadow,
			]
		);

		// Fix for white and black colors.
		$styles['--wp--preset--color--white'] = 'var(--wp--preset--color--neutral-0,#ffffff)';
		$styles['--wp--preset--color--black'] = 'var(--wp--preset--color--neutral-950,#000000)';

		/**
		 * Filters the dynamic custom properties.
		 *
		 * @since 1.3.0
		 *
		 * @param array $styles        Dynamic custom properties.
		 * @param array $global_styles Global styles.
		 */
		return apply_filters( 'blockify_dynamic_custom_properties', $styles, $global_styles );
	}
}
