<?php

declare( strict_types=1 );

use Blockify\Icons\Icon;
use Blockify\Utilities\Block;
use Fieldify\Fields\Blocks;
use Fieldify\Fields\MetaBoxes;
use Fieldify\Fields\PostTypes;
use Fieldify\Fields\Settings;
use Fieldify\Fields\Taxonomies;

if ( ! function_exists( 'register_custom_block' ) ) {

	/**
	 * Registers a block.
	 *
	 * @param string $id   The block name.
	 * @param array  $args The block arguments.
	 *
	 * @return void
	 */
	function register_custom_block( string $id, array $args ): void {
		Blocks::register_block( $id, $args );
	}
}

if ( ! function_exists( 'register_custom_post_type' ) ) {

	/**
	 * Registers a custom post type.
	 *
	 * @param string $id   The post type ID.
	 * @param array  $args (Optional). The post type arguments.
	 *
	 * @return void
	 */
	function register_custom_post_type( string $id, array $args = [] ): void {
		PostTypes::register_post_type( $id, $args );
	}
}

if ( ! function_exists( 'register_custom_taxonomy' ) ) {

	/**
	 * Registers a custom taxonomy.
	 *
	 * @param string       $id        The taxonomy ID.
	 * @param string|array $post_type Post type string or array of strings.
	 * @param array        $args      The taxonomy arguments.
	 *
	 * @return void
	 */
	function register_custom_taxonomy( string $id, $post_type, array $args ): void {
		Taxonomies::register_taxonomy( $id, $post_type, $args );
	}
}

if ( ! function_exists( 'register_custom_meta_box' ) ) {

	/**
	 * Registers a meta box.
	 *
	 * @param string $id   The meta box ID.
	 * @param array  $args The meta box arguments.
	 *
	 * @return void
	 */
	function register_custom_meta_box( string $id, array $args ): void {
		MetaBoxes::register_meta_box( $id, $args );
	}
}

if ( ! function_exists( 'register_custom_settings' ) ) {

	/**
	 * Registers settings.
	 *
	 * @param string $id   The settings ID.
	 * @param array  $args The settings.
	 *
	 * @return void
	 */
	function register_custom_settings( string $id, array $args ): void {
		Settings::register_settings( $id, $args );
	}
}

if ( ! function_exists( 'get_icon' ) ) {

	/**
	 * Returns svg string for given icon.
	 *
	 * @since 0.9.10
	 *
	 * @param string          $set  Icon set.
	 * @param string          $name Icon name.
	 * @param string|int|null $size Icon size.
	 *
	 * @return string
	 */
	function get_icon( string $set, string $name, $size = null ): string {
		return Icon::get_svg( $set, $name, $size );
	}
}

if ( ! function_exists( 'block_is_rendering_preview' ) ) {

	/**
	 * Checks if a block is currently rendering in the editor.
	 *
	 * @return bool
	 */
	function block_is_rendering_preview(): bool {
		return Block::is_rendering_preview();
	}
}
