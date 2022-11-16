<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use WP_Post;
use function __;
use function add_action;
use function basename;
use function explode;
use function filemtime;
use function get_permalink;
use function get_template;
use function get_template_directory;
use function get_template_directory_uri;
use function get_the_terms;
use function in_array;
use function is_array;
use function plugin_dir_url;
use function preg_match_all;
use function str_replace;
use function ucwords;
use function wp_enqueue_style;
use function wp_mkdir_p;
use function wp_send_json_success;

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

add_filter( 'admin_menu', NS . 'patterns_link', 999 );
/**
 * Adds menu link for block pattern editor.
 *
 * @since 0.0.1
 *
 * @return void
 */
function patterns_link(): void {
	add_theme_page(
		__( 'Patterns', 'blockify' ),
		__( 'Patterns', 'blockify' ),
		'edit_theme_options',
		'edit.php?post_type=block_pattern&orderby=title&order=asc'
	);
}

add_action( 'wp_ajax_blockify_export_patterns', NS . 'export_patterns' );
/**
 * Handles export pattern AJAX request.
 *
 * @since 0.0.1
 *
 * @return void
 */
function export_patterns(): void {
	if ( ! wp_verify_nonce( $_POST['nonce'], 'blockify' ) ) {
		die;
	}

	$posts = get_posts( [
		'post_type'   => 'block_pattern',
		'numberposts' => -1,
		'order'       => 'ASC',
		'orderby'     => 'title',
	] );

	$dir = get_template_directory() . '/patterns/' . DS;

	if ( ! file_exists( $dir ) ) {
		mkdir( $dir );
	}

	foreach ( $posts as $post ) {
		export_pattern( $post->ID, $post, true );
	}

	wp_send_json_success( $posts );
}

add_action( 'save_post_block_pattern', NS . 'export_pattern', 10, 3 );
/**
 * Handles export pattern AJAX request.
 *
 * @since 0.0.1
 *
 * @param int     $post_ID
 * @param WP_Post $post
 * @param bool    $update
 *
 * @return int
 */
function export_pattern( int $post_ID, WP_Post $post, bool $update ): int {
	$dir = get_template_directory() . '/patterns/';

	if ( ! file_exists( $dir ) ) {
		wp_mkdir_p( $dir );
	}

	$categories = get_the_terms( $post_ID, 'pattern_category' );

	if ( ! $categories ) {
		return $post_ID;
	}

	$category    = $categories[0]->slug ?? '';
	$block_types = '';

	if ( $category === 'page' ) {
		$block_types = 'Block Types: core/post-content';
	}

	if ( in_array( $category, [ 'header', 'footer' ] ) ) {
		$block_types = 'Block Types: core/template-part/' . ( $categories[0]->slug ?? '' );
	}

	$title = ucwords( get_template() ) . ' ' . $post->post_title;
	$slug  = get_template();

	$headers = <<<EOF
<?php
/**
 * Title: $title
 * Slug: $slug/$post->post_name
 * Categories: $category
 * $block_types
 */
?>
EOF;

	file_put_contents(
		$dir . $post->post_name . '.php',
		$headers . replace_image_paths( $post->post_content )
	);

	return $post_ID;
}

function replace_image_paths( $html ): string {
	$regex = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";

	preg_match_all( $regex, $html, $matches );

	if ( isset( $matches[0] ) && is_array( $matches[0] ) ) {
		foreach ( $matches[0] as $url ) {
			$base    = basename( $url );
			$explode = explode( '.', $base );
			$type    = $explode[1] ?? null;
			$new     = get_template_directory_uri() . '/assets/' . $type . DS . $base;

			if ( $type ) {
//				$html = str_replace( $url, $new, trim( $html ) );
			}
		}
	}

	$html = str_replace(
		get_template_directory_uri(),
		'<?php echo esc_url( get_template_directory_uri() ); ?>',
		$html
	);

	$html = str_replace(
		get_home_url(),
		'<?php echo esc_url( get_home_url() ); ?>',
		$html
	);

	$html = str_replace(
		[ '{"ref":999}', '"ref":999,' ],
		'',
		$html
	);

	return $html;
}

add_filter( 'manage_block_pattern_posts_columns', NS . 'set_custom_edit_book_columns' );
function set_custom_edit_book_columns( $columns ) {
	unset( $columns['date'] );

	$columns['preview'] = __( 'Preview', 'blockify' );

	return $columns;
}

add_action( 'manage_block_pattern_posts_custom_column', NS . 'pattern_preview_column', 10, 2 );
/**
 * Adds pattern iframe preview to admin columns.
 *
 * @since 0.0.1
 *
 * @param string $column
 * @param int    $post_id
 *
 * @return void
 */
function pattern_preview_column( string $column, int $post_id ): void {
	switch ( $column ) {
		case 'preview' :
			echo '<div class="pattern-preview"><iframe scrolling="no" src=\'' . get_permalink( $post_id ) . '\'></iframe></div>';
			break;
	}
}

add_filter( 'show_admin_bar', NS . 'hide_admin_bar_pattern' );
/**
 * Hide admin bar on singular block patterns.
 *
 * @since 1.0.0
 *
 * @param bool $show
 *
 * @return bool
 */
function hide_admin_bar_pattern( bool $show ): bool {
	return is_singular( 'block_pattern' ) ? false : $show;
}

add_action( 'admin_enqueue_scripts', NS . 'enqueue_pattern_styles' );
/**
 * Enqueues editor pattern styles.
 *
 * @since 0.0.1
 *
 * @return void
 */
function enqueue_pattern_styles(): void {
	$current_screen = get_current_screen();

	if ( isset( $current_screen->post_type ) && 'block_pattern' === $current_screen->post_type ) {
		wp_enqueue_style(
			'blockify-pattern-editor',
			plugin_dir_url( FILE ) . 'assets/css/patterns.css',
			[],
			filemtime( DIR . 'assets/css/patterns.css' )
		);
	}
}

add_action( 'template_redirect', NS . 'register_patterns_front_end' );
/**
 * Registers block patterns on front end.
 *
 * @since 0.0.1
 *
 * @return void
 */
function register_patterns_front_end(): void {
	foreach ( glob( get_template_directory() . '/patterns/*.php' ) as $file ) {
		register_block_pattern_from_file( $file );
	}
}

//add_action( 'init', NS . 'register_dark_patterns' );
/**
 * Register dark patterns.
 *
 * @since 0.0.21
 *
 * @return void
 */
function register_dark_patterns(): void {
	$patterns = \WP_Block_Patterns_Registry::get_instance()->get_all_registered();

	foreach ( $patterns as $pattern ) {
		$number = substr( $pattern['slug'], -1 );
		$name   = str_replace( $number, '', $pattern['slug'] ) . 'dark-' . $number;
		$title  = str_replace( $number, '', $pattern['title'] ) . __( 'Dark ', 'blockify' ) . $number;

		if ( isset( $pattern['slug'] ) ) {
			unset( $pattern['slug'] );
		}

		if ( isset( $pattern['slug'] ) ) {
			unset( $pattern['title'] );
		}

		register_block_pattern( $name, array_merge( $pattern, [
			'slug'  => $name,
			'title' => $title,
		] ) );
	}
}

/**
 * Parses and registers block pattern from PHP file with header comment.
 *
 * @since 0.0.8
 *
 * @param string $file
 *
 * @return void
 */
function register_block_pattern_from_file( string $file ): void {
	$headers = get_file_data( $file, [
		'categories'  => 'Categories',
		'title'       => 'Title',
		'slug'        => 'Slug',
		'block_types' => 'Block Types',
	] );

	$categories = explode( ',', $headers['categories'] );

	ob_start();
	include $file;
	$content = ob_get_clean();

	$pattern = [
		'title'      => $headers['title'],
		'content'    => $content,
		'categories' => [ ...$categories ],
	];

	if ( $headers['block_types'] ) {
		$pattern['blockTypes'] = $headers['block_types'];
	}

	foreach ( $categories as $category ) {
		register_block_pattern_category( $category, [
			'label' => ucwords( $category ),
		] );
	}

	register_block_pattern( $headers['slug'], $pattern );
}


function get_pro_patterns() : array {
	$pattern_slugs = [
		'blog-featured-post',
	];

	foreach ($pattern_slugs as $pattern_slug ) {
		$pattern = \wp_remote_get('');
	}
}
