<?php

declare( strict_types=1 );

namespace Fieldify\Fields;

use Blockify\Utilities\Str;
use function add_filter;
use function apply_filters;
use function array_merge;
use function esc_html;
use function register_taxonomy;
use function wp_parse_args;

/**
 * Taxonomies class.
 *
 * @since 1.0.0
 */
class Taxonomies {

	public const HOOK = 'fieldify_taxonomies';

	/**
	 * Registers a taxonomy.
	 *
	 * @param string       $id        Name.
	 * @param string|array $post_type Post types.
	 * @param array        $args      Arguments.
	 *
	 * @return void
	 */
	public static function register_taxonomy( string $id, $post_type, array $args ): void {
		$args['post_types'] = (array) $post_type;

		add_filter(
			static::HOOK,
			static fn( array $taxonomies ): array => array_merge( $taxonomies, [ $id => $args ] )
		);
	}

	/**
	 * Register custom taxonomies.
	 *
	 * @hook init 0
	 *
	 * @return void
	 */
	public function register_custom_taxonomies(): void {
		$taxonomies = $this->get_custom_taxonomies();

		foreach ( $taxonomies as $taxonomy => $args ) {
			register_taxonomy(
				$taxonomy,
				$args['post_types'],
				$args
			);
		}
	}

	/**
	 * Gets custom taxonomies.
	 *
	 * @return ?array
	 */
	private function get_custom_taxonomies(): ?array {
		$config     = apply_filters( static::HOOK, [] );
		$taxonomies = [];

		foreach ( $config as $taxonomy => $args ) {
			$singular = esc_html( $args['singular'] ?? Str::title_case( $taxonomy ) );
			$plural   = esc_html( $args['plural'] ?? $singular . 's' );

			$labels = [
				'name'                       => $plural,
				'singular_name'              => $singular,
				'menu_name'                  => $plural,
				'all_items'                  => __( 'All ', 'fieldify' ) . $plural,
				'parent_item'                => __( 'Parent ', 'fieldify' ) . $singular,
				'parent_item_colon'          => __( 'Parent ', 'fieldify' ) . $singular . ':',
				'new_item_name'              => __( 'New ', 'fieldify' ) . $singular . __( ' Name', 'fieldify' ),
				'add_new_item'               => __( 'Add New ', 'fieldify' ) . $singular,
				'edit_item'                  => __( 'Edit ', 'fieldify' ) . $singular,
				'update_item'                => __( 'Update ', 'fieldify' ) . $singular,
				'view_item'                  => __( 'View ', 'fieldify' ) . $singular,
				'separate_items_with_commas' => __( 'Separate ', 'fieldify' ) . $plural . __( ' with commas', 'fieldify' ),
				'add_or_remove_items'        => __( 'Add or remove ', 'fieldify' ) . $plural,
				'choose_from_most_used'      => __( 'Choose from the most used', 'fieldify' ),
				'popular_items'              => __( 'Popular ', 'fieldify' ) . $plural,
				'search_items'               => __( 'Search ', 'fieldify' ) . $plural,
				'not_found'                  => __( 'Not Found', 'fieldify' ),
				'no_terms'                   => __( 'No ', 'fieldify' ) . $plural,
				'items_list'                 => $plural . __( ' list', 'fieldify' ),
				'items_list_navigation'      => $plural . __( ' list navigation', 'fieldify' ),
			];

			$defaults = [
				'labels'            => $labels,
				'hierarchical'      => false,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'show_tagcloud'     => true,
				'show_in_rest'      => true,
				'post_types'        => [ 'post' ],
			];

			$taxonomies[ $taxonomy ] = wp_parse_args( $args, $defaults );
		}

		return $taxonomies;
	}
}
