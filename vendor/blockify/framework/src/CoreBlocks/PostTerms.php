<?php

declare( strict_types=1 );

namespace Blockify\Framework\CoreBlocks;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function array_key_last;
use function array_unique;
use function esc_attr;
use function esc_html;
use function esc_html__;
use function esc_url;
use function explode;
use function get_post_type;
use function get_term_link;
use function get_terms;
use function in_array;
use function is_front_page;
use function is_singular;
use function is_string;
use function trim;

/**
 * PostTerms class.
 *
 * @since 1.0.0
 */
class PostTerms implements Renderable {


	/**
	 * Modifies front end HTML output of block.
	 *
	 * @since 0.0.1
	 *
	 * @param string   $block_content Block HTML.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook  render_block_core/post-terms
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$attrs = $block['attrs'] ?? [];

		if ( $attrs['align'] ?? null ) {
			$dom = DOM::create( $block_content );
			$div = DOM::get_element( 'div', $dom );

			if ( $div ) {
				$classes = array_unique( [
					'wp-block-post-terms',
					'flex',
					'wrap',
					'justify-' . esc_attr( $attrs['align'] ?? 'left' ),
					...( explode( ' ', $div->getAttribute( 'class' ) ) ),
				] );

				$div->setAttribute( 'class', trim( implode( ' ', $classes ) ) );
			}

			$block_content = $dom->saveHTML();
		}

		$term = $attrs['term'] ?? '';

		if ( ! $term ) {
			return $block_content;
		}

		// Remove empty separator elements.
		$separator = $attrs['separator'] ?? null;

		if ( $separator === '' ) {
			$dom = DOM::create( $block_content );
			$div = DOM::get_element( 'div', $dom );

			if ( $div ) {

				foreach ( $div->childNodes as $child ) {

					if ( $child->nodeName === 'span' && ! trim( $child->nodeValue ) ) {
						$div->removeChild( $child );
					}
				}

				$block_content = $dom->saveHTML();
			}
		}

		$show_all = $attrs['showAll'] ?? false;

		if ( ! $block_content || $show_all ) {
			$dom         = DOM::create( '<div></div>' );
			$div         = DOM::get_element( 'div', $dom );
			$div_classes = [
				...( explode( ' ', $attrs['className'] ?? '' ) ),
				'wp-block-post-terms',
				'taxonomy-' . $term,
			];

			$text_align = esc_attr( $attrs['textAlign'] ?? '' );

			if ( $text_align ) {
				$div_classes[] = 'has-text-align-' . $text_align;
				$div_classes[] = 'justify-' . $text_align;
			}

			$text_color = esc_attr( $attrs['textColor'] ?? '' );

			if ( $text_color ) {
				$div_classes[] = 'has-' . $text_color . '-color';
			}

			$taxonomy  = get_taxonomy( $term );
			$post_type = $taxonomy->object_type[0] ?? get_post_type();

			if ( ( is_singular() && ! is_front_page() && ! $show_all ) || ! $show_all ) {
				$p            = DOM::create_element( 'p', $dom );
				$p->nodeValue = esc_html( $taxonomy->labels->not_found ?? '' );

				$p->setAttribute( 'class', 'margin-top-auto margin-bottom-auto' );

				$div->appendChild( $p );

			} else {
				$a            = DOM::create_element( 'a', $dom );
				$archive_link = get_post_type_archive_link( $post_type );

				if ( ! is_string( $archive_link ) ) {
					return '';
				}

				$a->setAttribute( 'href', esc_url( $archive_link ) );
				$a->setAttribute( 'class', 'wp-block-post-terms__link' );
				$a->setAttribute( 'rel', 'tag' );
				$a->nodeValue = esc_html__( 'All', 'blockify' );

				$div->appendChild( $a );

				if ( ! in_array( 'is-style-badges', $div_classes, true ) ) {
					$div->appendChild(
						$dom->createTextNode( $separator ?? '' )
					);
				}

				$terms = get_terms(
					[
						'taxonomy'   => $term,
						'hide_empty' => true,
					]
				);

				foreach ( $terms as $index => $term_object ) {
					$a         = DOM::create_element( 'a', $dom );
					$term_link = get_term_link( $term_object );

					if ( ! is_string( $term_link ) ) {
						continue;
					}

					$a->setAttribute( 'href', esc_url( $term_link ) );
					$a->setAttribute( 'class', 'wp-block-post-terms__link' );
					$a->setAttribute( 'rel', 'tag' );
					$a->nodeValue = esc_html( $term_object->name ?? '' );

					$div->appendChild( $a );

					if ( ! in_array( 'is-style-badges', $div_classes, true ) && $index !== array_key_last( $terms ) ) {
						$div->appendChild(
							$dom->createTextNode( $separator ?? '' )
						);
					}
				}
			}

			$styles = [];
			$margin = $attrs['style']['spacing']['margin'] ?? null;
			$styles = CSS::add_shorthand_property( $styles, 'margin', $margin );

			$text_decoration = esc_attr( $attrs['style']['typography']['textDecoration'] ?? '' );

			if ( $text_decoration ) {
				$styles['text-decoration'] = $text_decoration;
			}

			$font_size = esc_attr( $attrs['fontSize'] ?? '' );

			if ( $font_size ) {
				$div_classes[] = 'has-font-size';
				$div_classes[] = 'has-' . $font_size . '-font-size';
			}

			$font_size_custom = esc_attr( $attrs['style']['typography']['fontSize'] ?? '' );

			if ( $font_size_custom ) {
				$styles['font-size'] = CSS::format_custom_property( $font_size_custom );
				$div_classes[]       = 'has-font-size';
			}

			$padding = $attrs['style']['spacing']['padding'] ?? null;
			$styles  = CSS::add_shorthand_property( $styles, 'padding', $padding );

			$div->setAttribute( 'class', trim( implode( ' ', array_unique( $div_classes ) ) ) );

			if ( $styles ) {
				$div->setAttribute( 'style', CSS::array_to_string( $styles ) );
			}

			$block_content = $dom->saveHTML();
		}

		$dom = DOM::create( $block_content );

		// First child either div or ul.
		$first = DOM::get_element( '*', $dom );

		if ( $first ) {
			$styles = CSS::string_to_array( $first->getAttribute( 'style' ) );

			$gap = $attrs['style']['spacing']['blockGap'] ?? '';

			if ( $gap ) {
				$styles['gap'] = CSS::format_custom_property( $gap );
			}

			$font_weight = esc_attr( $attrs['style']['typography']['fontWeight'] ?? '' );

			if ( $font_weight ) {
				$styles['font-weight'] = $font_weight;
			}

			$border = $attrs['style']['border'] ?? null;
			$radius = $border['radius'] ?? null;

			if ( $radius ) {
				$styles['--wp--custom--border--radius'] = esc_attr( $radius );
			}

			if ( $styles ) {
				$first->setAttribute( 'style', CSS::array_to_string( $styles ) );
			}
		}

		$class_names = explode( ' ', $attrs['className'] ?? '' );

		if ( in_array( 'is-style-badges', $class_names, true ) ) {
			$padding = $attrs['style']['spacing']['padding'] ?? null;

			if ( $padding && $first ) {
				$styles = CSS::string_to_array( $first->getAttribute( 'style' ) );
				unset( $styles['padding'] );

				$styles = CSS::add_shorthand_property( $styles, '--wp--custom--badge--padding', $padding );

				$first->setAttribute( 'style', CSS::array_to_string( $styles ) );
			}
		}

		return $dom->saveHTML();
	}

}
