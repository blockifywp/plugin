<?php

declare( strict_types=1 );

namespace Blockify\Framework\BlockVariations;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use Blockify\Utilities\Str;
use DOMElement;
use WP_Block;

/**
 * Accordion list block variation.
 *
 * @since 1.0.0
 */
class AccordionList implements Renderable {

	/**
	 * Renders list block as an accordion.
	 *
	 * @since 0.0.2
	 *
	 * @param string   $block_content Block HTML.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook  render_block_core/list 12
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		if ( ! str_contains( $block_content, 'is-style-accordion' ) ) {
			return $block_content;
		}

		$dom = DOM::create( $block_content );
		$ul  = DOM::get_element( 'ul', $dom );
		$ol  = DOM::get_element( 'ol', $dom );

		if ( ! $ul && ! $ol ) {
			return $block_content;
		}

		$list = $ul ?? $ol;

		$classes = explode( ' ', $list->getAttribute( 'class' ) );

		// Move `wp-block-list` class to the start of the array.
		$classes = [
			'wp-block-list',
			...( array_diff( $classes, [ 'wp-block-list' ] ) ),
		];

		$list->setAttribute( 'class', implode( ' ', $classes ) );

		$div = '<div>';

		foreach ( $list->getElementsByTagName( 'li' ) as $li ) {
			$inner = $dom->saveHTML( $li );

			if ( ! $li instanceof DOMElement ) {
				continue;
			}

			if ( ! str_contains( $inner, '<br>' ) ) {
				continue;
			}

			$details = DOM::create_element( 'details', $dom );

			foreach ( $li->attributes as $attribute ) {
				$details->setAttribute(
					esc_attr( $attribute->name ),
					esc_attr( $attribute->value )
				);
			}

			$summary = DOM::create_element( 'summary', $dom );
			$section = DOM::create_element( 'section', $dom );
			$explode = explode( '<br>', $inner );

			$details->textContent = '';

			$title_dom = DOM::create( $explode[0] );
			$list_item = DOM::get_element( 'li', $title_dom );

			foreach ( $list_item->childNodes as $child_node ) {
				$imported = $dom->importNode( $child_node, true );
				$summary->appendChild( $imported );
			}

			// If third arg present then second will be unused closing html tags.
			$section->textContent = strip_tags( $explode[2] ?? $explode[1], '' );
			$details->appendChild( $summary );

			$li_style = $li->getAttribute( 'style' );

			$has_border = Str::contains_any( $li_style, 'border-width', 'border-style', 'border-color' ) && ! str_contains( $li_style, 'border-width:0' );

			if ( $has_border ) {
				$details->appendChild( DOM::create_element( 'hr', $dom ) );
			}

			$details->appendChild( $section );

			$styles  = CSS::string_to_array( $details->getAttribute( 'style' ) );
			$padding = [];

			foreach ( $styles as $key => $value ) {
				if ( str_contains( $key, 'padding' ) ) {
					unset( $styles[ $key ] );

					$padding[ $key ] = $value;
				}
			}

			if ( $padding ) {
				$summary->setAttribute(
					'style',
					CSS::array_to_string(
						$padding
					)
				);

				if ( ! $has_border ) {
					unset( $padding['padding-top'] );
				}

				$section->setAttribute(
					'style',
					CSS::array_to_string(
						$padding
					)
				);
			}

			$details->setAttribute(
				'style',
				CSS::array_to_string(
					$styles
				)
			);

			if ( ! $styles ) {
				$details->removeAttribute( 'style' );
			}

			$icon = DOM::create_element( 'span', $dom );
			$icon->setAttribute( 'class', 'accordion-toggle' );
			$summary->appendChild( $icon );

			$div .= $dom->saveHTML( $details );
		}

		$div_dom  = DOM::create( $div . '</div>' );
		$imported = $dom->importNode( $div_dom->documentElement, true );

		foreach ( $list->attributes as $attribute ) {
			if ( ! method_exists( $imported, 'setAttribute' ) ) {
				continue;
			}

			$imported->setAttribute( $attribute->localName, $attribute->nodeValue );
		}

		$dom->removeChild( $list );
		$dom->appendChild( $imported );

		return $dom->saveHTML();
	}

}
