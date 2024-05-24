<?php

namespace Blockify\Blocks;

use Blockify\Dom\DOM;
use DOMElement;
use WP_Block;
use function apply_filters;
use function count;
use function esc_attr;
use function esc_url;
use function explode;
use function filter_input;
use function implode;
use function str_contains;
use function trim;
use function wp_kses_post;
use const FILTER_SANITIZE_NUMBER_INT;

/**
 * Registers the Tabs block.
 *
 * @since 1.0.0
 */
class Tabs extends AbstractBlock {

	/**
	 * Modifies front end HTML output of block.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $attributes Block data.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block instance.
	 *
	 * @return string
	 */
	public function render( array $attributes, string $content, WP_Block $block ): string {
		$dom = DOM::create( $content );
		$div = DOM::get_element( 'div', $dom );

		if ( ! $div ) {
			return $content;
		}

		$nav = DOM::get_element( 'div', $div );

		if ( ! $nav ) {
			return $content;
		}

		$ul = DOM::change_tag_name( 'ul', $nav );
		$ul->setAttribute( 'role', 'tablist' );
		$ul->setAttribute( 'data-tabs', '' );

		$inner_blocks = $block->inner_blocks ?? [];

		if ( ! $inner_blocks ) {
			return $content;
		}

		static $static_id = 0;

		$static_id++;

		$class_name    = $attributes['className'] ?? '';
		$is_buttons    = str_contains( $class_name, 'is-style-buttons' );
		$cookie        = (int) filter_input( INPUT_COOKIE, 'blockify-tabs-' . $static_id, FILTER_SANITIZE_NUMBER_INT );
		$section_index = 1;

		foreach ( $div->childNodes as $section ) {
			if ( ! $section instanceof DOMElement ) {
				continue;
			}

			if ( $section->nodeName !== 'section' ) {
				continue;
			}

			$section->setAttribute( 'id', 'tab-' . $static_id . '-' . ( $section_index++ ) );
			$section->setAttribute( 'role', 'tabpanel' );
		}

		$tabs = [];

		$link_color = $attributes['style']['elements']['link']['color']['text'] ?? '';

		foreach ( $inner_blocks as $index => $tab ) {
			$index      = $index + 1;
			$id         = 'tab-' . $static_id . '-' . $index;
			$section    = $dom->getElementById( $id );
			$visibility = apply_filters( 'blockify_tabs_tab_visibility', $section->nodeValue ?? '', $tab->parsed_block, $block );

			if ( ! $visibility || $section && empty( trim( $section->nodeValue ) ) ) {
				if ( $section ) {
					$div->removeChild( $section );
				}

				continue;
			}

			$tabs[ $id ] = $tab;
		}

		$index = 0;

		foreach ( $tabs as $id => $tab ) {
			$index = $index + 1;
			$li    = DOM::create_element( 'li', $dom );
			$a     = DOM::create_element( 'a', $dom );

			$li_classes   = explode( ' ', $li->getAttribute( 'class' ) );
			$li_classes[] = 'wp-block-blockify-tabs__nav-item';

			if ( $cookie && $cookie === $index ) {
				$li_classes[] = 'is-active';
				$a->setAttribute( 'data-tabby-default', '' );
			} else {
				if ( $cookie > count( $tabs ) && $index === 1 ) {
					$li_classes[] = 'is-active';
					$a->setAttribute( 'data-tabby-default', '' );
				} else {
					if ( ! $cookie && $index === 1 ) {
						$li_classes[] = 'is-active';
						$a->setAttribute( 'data-tabby-default', '' );
					}
				}
			}

			$a_classes = [
				'wp-block-blockify-tabs__nav-link',
			];

			if ( $is_buttons ) {
				$a_classes[] = 'wp-element-button';

				if ( $index !== 1 ) {
					$a_classes[] = 'is-style-ghost';
				}
			}

			$li->setAttribute( 'class', implode( ' ', $li_classes ) );
			$li->setAttribute( 'role', 'presentation' );
			$a->setAttribute( 'class', implode( ' ', $a_classes ) );
			$a->setAttribute( 'role', 'tab' );
			$a->setAttribute( 'aria-controls', $id );
			$a->setAttribute( 'href', '#' . $id );

			$li_styles = DOM::get_styles( $li );

			if ( $link_color ) {
				$li_styles['color'] = $link_color;
			}

			DOM::add_styles( $li, $li_styles );

			/**
			 * @var WP_Block $tab The tab block.
			 */
			$tab_attrs = $tab->attributes ?? [];

			$custom_url = esc_url( $tab_attrs['url'] ?? '' );

			if ( $custom_url ) {
				$a->setAttribute( 'onclick', "window.location='{$custom_url}';return false;" );
			}

			$title = wp_kses_post( $tab_attrs['title'] ?? ( __( 'Tab ', 'blockify-pro' ) . $index ) );

			$title_dom = DOM::create( "<div>$title</div>" );

			// Prevent DOMDocument wrapping text in <p> tags.
			foreach ( $title_dom->firstChild->childNodes as $node ) {
				$a->appendChild(
					$dom->importNode( $node, true )
				);
			}

			$icon_set  = esc_attr( $tab_attrs['iconSet'] ?? '' );
			$icon_name = esc_attr( $tab_attrs['iconName'] ?? '' );
			$icon_size = esc_attr( $tab_attrs['iconSize'] ?? '' );

			if ( $icon_set && $icon_name ) {
				$icon = get_icon( $icon_set, $icon_name, ! empty( $icon_size ) ? $icon_size : 20 );

				if ( $icon ) {
					$icon_dom      = DOM::create( $icon );
					$icon_svg      = DOM::get_element( 'svg', $icon_dom );
					$imported_icon = $dom->importNode( $icon_svg, true );
					$icon_position = esc_attr( $tab_attrs['iconPosition'] ?? 'end' );

					if ( $icon_position === 'start' ) {
						$a->insertBefore( $imported_icon, $a->firstChild );
					} else {
						$a->appendChild( $imported_icon );
					}
				}
			}

			$li->appendChild( $a );
			$ul->appendChild( $li );
		}

		return $dom->saveHTML();
	}
}
