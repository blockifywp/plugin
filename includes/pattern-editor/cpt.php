<?php

declare( strict_types=1 );

namespace Blockify\PatternEditor;

use function __;
use function add_action;

add_action( 'init', NS . 'register_pattern_cpt' );
/**
 * Registers block pattern custom post type.
 *
 * @since 0.0.1
 *
 * @return void
 */
function register_pattern_cpt() {
	$labels = [
		'name'                  => _x( 'Block Patterns', 'Block Pattern General Name', 'blockify' ),
		'singular_name'         => _x( 'Block Pattern', 'Block Pattern Singular Name', 'blockify' ),
		'menu_name'             => __( 'Block Patterns', 'blockify' ),
		'name_admin_bar'        => __( 'Block Pattern', 'blockify' ),
		'archives'              => __( 'Item Archives', 'blockify' ),
		'attributes'            => __( 'Item Attributes', 'blockify' ),
		'parent_item_colon'     => __( 'Parent Item:', 'blockify' ),
		'all_items'             => __( 'All Items', 'blockify' ),
		'add_new_item'          => __( 'Add New Item', 'blockify' ),
		'add_new'               => __( 'Add New', 'blockify' ),
		'new_item'              => __( 'New Item', 'blockify' ),
		'edit_item'             => __( 'Edit Item', 'blockify' ),
		'update_item'           => __( 'Update Item', 'blockify' ),
		'view_item'             => __( 'View Item', 'blockify' ),
		'view_items'            => __( 'View Items', 'blockify' ),
		'search_items'          => __( 'Search Item', 'blockify' ),
		'not_found'             => __( 'Not found', 'blockify' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'blockify' ),
		'featured_image'        => __( 'Featured Image', 'blockify' ),
		'set_featured_image'    => __( 'Set featured image', 'blockify' ),
		'remove_featured_image' => __( 'Remove featured image', 'blockify' ),
		'use_featured_image'    => __( 'Use as featured image', 'blockify' ),
		'insert_into_item'      => __( 'Place into item', 'blockify' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'blockify' ),
		'items_list'            => __( 'Items list', 'blockify' ),
		'items_list_navigation' => __( 'Items list navigation', 'blockify' ),
		'filter_items_list'     => __( 'Filter items list', 'blockify' ),
	];

	$args = [
		'label'               => __( 'Block Pattern', 'blockify' ),
		'description'         => __( 'Block Pattern Description', 'blockify' ),
		'labels'              => $labels,
		'supports'            => [ 'title', 'editor' ],
		'taxonomies'          => [ 'block_pattern' ],
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => false,
		'menu_position'       => 5,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'show_in_rest'        => true,
		'capability_type'     => 'page',
	];

	register_post_type( 'block_pattern', $args );
}

add_action( 'init', NS . 'register_pattern_category_taxonomy' );
/**
 * Registers block pattern category taxonomy.
 *
 * @since 0.0.1
 *
 * @return void
 */
function register_pattern_category_taxonomy() {

	$labels = [
		'name'                       => _x( 'Pattern Category', 'Pattern Category General Name', 'blockify' ),
		'singular_name'              => _x( 'Pattern Category', 'Pattern Category Singular Name', 'blockify' ),
		'menu_name'                  => __( 'Pattern Category', 'blockify' ),
		'all_items'                  => __( 'All Items', 'blockify' ),
		'parent_item'                => __( 'Parent Item', 'blockify' ),
		'parent_item_colon'          => __( 'Parent Item:', 'blockify' ),
		'new_item_name'              => __( 'New Item Name', 'blockify' ),
		'add_new_item'               => __( 'Add New Item', 'blockify' ),
		'edit_item'                  => __( 'Edit Item', 'blockify' ),
		'update_item'                => __( 'Update Item', 'blockify' ),
		'view_item'                  => __( 'View Item', 'blockify' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'blockify' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'blockify' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'blockify' ),
		'popular_items'              => __( 'Popular Items', 'blockify' ),
		'search_items'               => __( 'Search Items', 'blockify' ),
		'not_found'                  => __( 'Not Found', 'blockify' ),
		'no_terms'                   => __( 'No items', 'blockify' ),
		'items_list'                 => __( 'Items list', 'blockify' ),
		'items_list_navigation'      => __( 'Items list navigation', 'blockify' ),
	];

	$args = [
		'labels'            => $labels,
		'hierarchical'      => false,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => false,
		'show_tagcloud'     => false,
		'show_in_rest'      => true,
	];

	register_taxonomy( 'pattern_category', [ 'block_pattern' ], $args );
}

add_filter( 'template_include', NS . 'single_block_pattern_template' );
/**
 * Filter pattern template.
 *
 * @since 0.4.0
 *
 * @param string $template Template slug.
 *
 * @return string
 */
function single_block_pattern_template( string $template ): string {
	global $post;

	if ( $post && $post->post_type === 'block_pattern' ) {
		$template = locate_block_template( get_template_directory() . '/templates/blank.html', 'blank', [] );
	}

	return $template;
}

