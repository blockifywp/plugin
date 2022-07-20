<?php

declare( strict_types=1 );

namespace Blockify;

use function add_action;
use function get_post;
use function register_post_meta;
use function register_rest_field;
use function wp_update_post;

add_action( 'after_setup_theme', NS . 'theme_supports' );
/**
 * Handles theme supports.
 *
 * @since 0.0.2
 *
 * @return void
 */
function theme_supports(): void {
	remove_theme_support( 'core-block-patterns' );
}

add_action( 'after_setup_theme', NS . 'post_type_supports' );
/**
 * Handles post type supports.
 *
 * @since 0.0.2
 *
 * @return void
 */
function post_type_supports(): void {
	add_post_type_support( 'page', 'excerpt' );
	add_post_type_support( 'block_pattern', 'excerpt' );
	add_post_type_support( 'page', 'custom-fields' );
}

add_action( 'init', NS . 'rest_fields' );
/**
 * Registers rest fields.
 *
 * @since 0.0.2
 *
 * @return void
 */
function rest_fields(): void {
	register_rest_field(
		'blockify-page-title',
		'title',
		[
			'get_callback'    => function ( $params ) {
				$post_id = $params['id'];
				$post    = get_post( $post_id );

				return $post->post_title;
			},
			'update_callback' => function ( $value, $object ) {
				wp_update_post(
					[
						'ID'         => $object->ID,
						'post_title' => $value,
					]
				);
			},
		]
	);

	register_post_meta( 'page', 'template_part_header', [
		'show_in_rest' => true,
		'single'       => true,
		'type'         => 'boolean',
	] );

	register_post_meta( 'page', 'template_part_footer', [
		'show_in_rest' => true,
		'single'       => true,
		'type'         => 'string',
	] );
}
