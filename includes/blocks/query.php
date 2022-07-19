<?php

declare( strict_types=1 );

namespace Blockify;

use function add_filter;
use function get_option;
use function get_post;
use function is_home;
use function str_contains;
use function str_replace;
use function strip_tags;

add_filter( 'render_block', NS . 'render_query_block', 10, 2 );
/**
 * Modifies front end HTML output of block.
 *
 * @since 0.0.2
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function render_query_block( string $content, array $block ): string {
	if ( $block['blockName'] !== 'core/query' ) {
		return $content;
	}

	if ( isset( $block['attrs']['style']['spacing']['blockGap'] ) ) {

		$dom = dom( $content );

		/**
		 * @var $div \DOMElement
		 */
		$div = $dom->firstChild;

		$style = $div->getAttribute( 'style' ) ? $div->getAttribute( 'style' ) . ';' : '';

		$div->setAttribute( 'style', $style . '--wp--style--block-gap:' . $block['attrs']['style']['spacing']['blockGap'] );

		$content = $dom->saveHTML();
	}

	return $content;
}

add_filter( 'render_block', NS . 'query_block_filters', 10, 2 );
/**
 * Description of expected behavior.
 *
 * @since 0.0.1
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function query_block_filters( string $content, array $block ): string {

	if ( 'core/post-title' === $block['blockName'] ) {

		if ( is_home() && str_contains( $content, '<h1' ) ) {
			$text           = strip_tags( $content );
			$page_for_posts = get_post( get_option( 'page_for_posts' ) );
			$content        = str_replace( $text, $page_for_posts->post_title, $content );
		}
	}

	if ( 'core/post-author' === $block['blockName'] ) {
		$content = str_replace(
			[ '<p ', '</p>' ],
			[ '<span ', '</span>' ],
			$content
		);
	}

	if ( 'core/post-terms' === $block['blockName'] && isset( $block['attrs']['align'] ) ) {
		$content = str_replace(
			[
				'wp-block-post-terms',
				'rel="tag"',
			],
			[
				'wp-block-post-terms flex justify-' . $block['attrs']['align'],
				'class="wp-block-post-terms__link" rel="tag"',
			],
			$content
		);
	}

	return $content;
}

