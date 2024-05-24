<?php

declare( strict_types=1 );

namespace Blockify\Blocks;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use WP_Block;
use function apply_filters;
use function esc_attr;
use function esc_html;
use function esc_html__;
use function esc_url;
use function function_exists;
use function get_option;
use function get_permalink;
use function get_post;
use function get_post_type;
use function get_post_type_archive_link;
use function get_the_title;
use function home_url;
use function is_a;
use function is_archive;
use function is_front_page;
use function is_home;
use function is_search;
use function is_singular;
use function str_contains;
use function trailingslashit;
use function wp_get_post_parent_id;
use function yoast_breadcrumb;
use const DIRECTORY_SEPARATOR;

/**
 * Class Breadcrumbs
 *
 * @package Blockify\Blocks
 */
class Breadcrumbs extends AbstractBlock {

	/**
	 * Renders the breadcrumbs block.
	 *
	 * @since 0.0.2
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block object.
	 *
	 * @return string
	 */
	public function render( array $attributes, string $content, WP_Block $block ): string {
		if ( ! str_contains( $content, 'wp-block-blockify-breadcrumbs' ) ) {
			return $content;
		}

		if ( function_exists( 'yoast_breadcrumb' ) ) {
			return yoast_breadcrumb( '<p id="breadcrumbs"', '</p>', false );
		}

		$dom = DOM::create( $content );
		$div = DOM::get_element( 'div', $dom );

		$separator = esc_html( apply_filters(
			'blockify_breadcrumbs_separator',
			' ' . ( $attributes['separator'] ?? DIRECTORY_SEPARATOR ) . ' '
		) );

		$post = get_post();

		if ( $post && is_singular() ) {
			$span              = DOM::create_element( 'span', $dom );
			$span->textContent = esc_html( $post->post_title );

			$div->appendChild( $dom->createTextNode( $separator ) );
			$div->appendChild( $span );
		}

		$term = get_queried_object();

		if ( is_a( $term, 'WP_Term' ) && is_archive() ) {
			$span              = DOM::create_element( 'span', $dom );
			$span->textContent = esc_html( $term->name );

			$div->appendChild( $dom->createTextNode( $separator ) );
			$div->appendChild( $span );
		}

		$post_type   = $post ? $post->post_type : get_post_type();
		$archive_url = get_post_type_archive_link( $post_type );
		$is_search   = is_search();

		if ( $archive_url && ! $is_search ) {
			$archive_link = DOM::create_element( 'a', $dom );

			$archive_link->setAttribute( 'href', esc_attr( $archive_url ) );

			$post_type_object = get_post_type_object( $post_type );

			$archive_link->textContent = esc_html( $post_type_object->labels->name ?? $post_type_object->label ?? $post_type );

			$div->insertBefore( $archive_link, $div->firstChild );
			$div->insertBefore( $dom->createTextNode( $separator ), $div->firstChild );
		}

		if ( $is_search ) {
			$span              = DOM::create_element( 'span', $dom );
			$span->textContent = esc_html__( 'Search results', 'blockify-pro' );

			$div->appendChild( $dom->createTextNode( $separator ) );
			$div->appendChild( $span );
		}

		$parent = wp_get_post_parent_id();

		while ( $parent ) {
			$url   = get_permalink( $parent );
			$title = get_the_title( $parent );
			$a     = DOM::create_element( 'a', $dom );

			$a->setAttribute( 'href', esc_url( $url ) );
			$a->textContent = esc_html( $title );

			$div->insertBefore( $a, $div->lastChild );
			$div->insertBefore( $dom->createTextNode( $separator ), $div->lastChild );

			$parent = wp_get_post_parent_id( $parent );
		}

		if ( get_option( 'show_on_front' ) === 'page' ) {
			$home_url = get_permalink( get_option( 'page_on_front' ) );
		} else {
			$home_url = trailingslashit( home_url() );
		}

		$home_text = esc_html( apply_filters(
			'blockify_breadcrumbs_home',
			esc_html__( 'Home', 'blockify-pro' )
		) );

		if ( is_home() && is_front_page() ) {
			$div->insertBefore( $dom->createTextNode( $home_text ), $div->firstChild );
		} else {
			$home_link = DOM::create_element( 'a', $dom );

			$home_link->setAttribute( 'href', esc_url( $home_url ) );

			$home_link->textContent = $home_text;

			$div->insertBefore( $home_link, $div->firstChild );
		}

		$div_styles = CSS::string_to_array( $div->getAttribute( 'style' ) );

		$display = $attributes['layout']['type'] ?? '';
		$justify = $attributes['layout']['justifyContent'] ?? '';
		$gap     = $attributes['style']['spacing']['blockGap'] ?? '';

		if ( $display ) {
			$div_styles['display'] = esc_attr( $display );
		}

		if ( $justify ) {
			$div_styles['justify-content'] = esc_attr( $justify );
		}

		if ( $gap ) {
			$div_styles['gap'] = CSS::format_custom_property( $gap );
		}

		if ( $div_styles ) {
			$div->setAttribute( 'style', CSS::array_to_string( $div_styles ) );
		}

		return $dom->saveHTML();
	}
}
