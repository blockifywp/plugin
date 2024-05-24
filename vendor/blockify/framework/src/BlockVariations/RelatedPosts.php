<?php

declare( strict_types=1 );

namespace Blockify\Framework\BlockVariations;

use WP_Block;
use function array_replace_recursive;
use function get_object_taxonomies;
use function get_post_type;
use function get_the_ID;
use function get_the_terms;
use function is_front_page;
use function is_singular;
use function str_contains;

/**
 * RelatedPosts class.
 *
 * @since 1.0.0
 */
class RelatedPosts {

	/**
	 * Query block tax relation.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $query The query vars.
	 * @param WP_Block $block The block instance.
	 * @param int      $page  The current page.
	 *
	 * @hook  query_loop_block_query_vars
	 *
	 * @return array
	 */
	public function related_posts_query_block( array $query, WP_Block $block, int $page ): array {
		$class_name = $block->attributes['className'] ?? '';

		if ( ! str_contains( $class_name, 'is-related-posts' ) ) {
			return $query;
		}

		if ( ! is_singular() || is_front_page() ) {
			return $query;
		}

		$related_posts = str_contains( $block->inner_html, 'is-related-posts' );

		if ( ! $related_posts ) {
			return [];
		}

		$term_types = get_object_taxonomies( get_post_type() );
		$tax_query  = [
			'relation' => 'OR',
		];

		foreach ( $term_types as $term_type ) {
			$terms = get_the_terms( get_the_ID(), $term_type );

			if ( ! $terms ) {
				continue;
			}

			$tax_query[] = [
				'taxonomy'         => $term_type,
				'terms'            => wp_list_pluck( $terms, 'term_id' ),
				'include_children' => false,
			];
		}

		return array_replace_recursive(
			$query,
			[
				'post_type'    => get_post_type(),
				'order'        => 'DESC',
				'orderby'      => 'date',
				'post__not_in' => [ get_the_ID() ],
				'tax_query'    => $tax_query,
			]
		);
	}
}
