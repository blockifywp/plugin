<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use WP_Block_Patterns_Registry;
use WP_Post;
use function get_template_directory;
use function locate_block_template;
use const WP_CONTENT_DIR;
use function __;
use function add_action;
use function add_filter;
use function add_theme_page;
use function admin_url;
use function apply_filters;
use function basename;
use function esc_url_raw;
use function explode;
use function file_exists;
use function file_put_contents;
use function filemtime;
use function get_current_user_id;
use function get_home_url;
use function get_page_by_path;
use function get_page_by_title;
use function get_permalink;
use function get_stylesheet;
use function get_stylesheet_directory;
use function home_url;
use function in_array;
use function is_array;
use function preg_match_all;
use function register_meta;
use function rest_url;
use function str_contains;
use function str_replace;
use function term_exists;
use function trailingslashit;
use function trim;
use function ucwords;
use function wp_create_nonce;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_insert_term;
use function wp_localize_script;
use function wp_mkdir_p;
use function wp_parse_url;
use function wp_safe_redirect;

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

	$file = get_template_directory() . '/templates/blank.html';

	if ( ! file_exists( $file ) ) {
		$file = get_stylesheet_directory() . '/templates/blank.html';
	}

	if ( ! file_exists( $file ) ) {
		file_put_contents( $file, '<!-- wp:post-content {"layout":{"inherit":true}} /-->' );
	}

	if ( $post && $post->post_type === 'block_pattern' ) {
		$template = locate_block_template( $file, 'blank', [] );
	}

	return $template;
}

add_action( 'admin_enqueue_scripts', NS . 'enqueue_pattern_admin' );
/**
 * Enqueues editor pattern styles.
 *
 * @since 0.0.1
 *
 * @return void
 */
function enqueue_pattern_admin(): void {
	$current_screen = get_current_screen();

	if ( 'block_pattern' !== ( $current_screen->post_type ?? '' ) ) {
		return;
	}

	if ( 'edit' !== $current_screen->base ) {
		return;
	}

	$asset_path = DIR . 'build/patterns.asset.php';
	$asset      = file_exists( $asset_path ) ? require $asset_path : [
		'dependencies' => [],
		'version'      => filemtime( DIR ),
	];

	wp_enqueue_style(
		'blockify-pattern-editor',
		plugin_dir_url( FILE ) . 'build/patterns.css',
		[],
		$asset['version'],
	);

	wp_enqueue_script(
		'blockify-pattern-editor',
		plugin_dir_url( FILE ) . 'build/patterns.js',
		[
			...$asset['dependencies'],
			'wp-block-editor',
		],
		$asset['version'],
		true
	);

	wp_localize_script(
		'blockify-pattern-editor',
		'blockifyPatternEditor',
		[
			'nonce'         => wp_create_nonce( 'wp_rest' ),
			'restUrl'       => esc_url_raw( rest_url() ),
			'adminUrl'      => esc_url_raw( admin_url() ),
			'currentUser'   => get_current_user_id() ?? false,
			'stylesheet'    => get_stylesheet(),
			'stylesheetDir' => get_stylesheet_directory(),
			'isChildTheme'  => is_child_theme(),
		]
	);
}

add_filter( 'admin_menu', NS . 'patterns_link' );
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
		'edit.php?post_type=block_pattern',
		null,
		99,
	);
}

add_filter( 'manage_block_pattern_posts_columns', NS . 'set_custom_edit_book_columns' );
/**
 * Adds preview column to patterns list screen.
 *
 * @since 1.0.0
 *
 * @param $columns
 *
 * @return mixed
 */
function set_custom_edit_book_columns( $columns ) {
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
	$url = get_permalink( $post_id );

	switch ( $column ) {
		case 'preview' :
			echo '<div class="pattern-preview"><iframe loading="lazy" scrolling="no" src=\'' . $url . '\' seamless></iframe></div>';
			break;
	}
}

add_action( 'rest_api_init', NS . 'register_pattern_user_meta' );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function register_pattern_user_meta(): void {
	register_meta(
		'user',
		'blockify_show_patterns',
		[
			'description'  => 'Blockify Show Patterns',
			'type'         => 'string',
			'show_in_rest' => true,
			'single'       => true,
		]
	);
}

add_filter( 'admin_body_class', NS . 'add_show_patterns_body_class' );
/**
 * Conditionally  add show patterns class by default.
 *
 * @since 1.0.0
 *
 * @param string $classes
 *
 * @return string
 */
function add_show_patterns_body_class( string $classes ): string {
	$show_patterns = get_user_option( 'blockify_show_patterns', get_current_user_id() );

	if ( $show_patterns ) {
		$classes .= ' show-patterns';
	}

	return $classes;
}


add_action( 'admin_post_blockify_import_patterns', NS . 'import_patterns', 11 );
/**
 * Imports all registered patterns as posts.
 *
 * @since 1.0.0
 *
 * @return void
 */
function import_patterns() {
	$registered = WP_Block_Patterns_Registry::get_instance()->get_all_registered();

	foreach ( $registered as $pattern ) {
		$pattern = (object) $pattern;

		$category = $pattern->categories[0] ?? '';

		if ( $category === 'template' ) {
			continue;
		}

		if ( ! $category ) {
			[ $prefix ] = explode( '-', $pattern->name );
			$category = $prefix;
		}

		$args = [
			'post_name'    => $pattern->name,
			'post_title'   => $pattern->title,
			'post_content' => $pattern->content,
			'post_status'  => 'publish',
			'post_type'    => 'block_pattern',
			'tax_input'    => [
				'pattern_category' => [ $category ],
			],
		];

		if ( get_page_by_path( $pattern->name, OBJECT, 'block_pattern' ) ) {
			continue;
		}

		if ( get_page_by_title( $pattern->title, OBJECT, 'block_pattern' ) ) {
			continue;
		}

		if ( ! term_exists( $category ) ) {
			wp_insert_term(
				ucwords( str_replace( '-', ' ', $category ) ),
				'pattern_category',
				[
					'slug' => $category,
				]
			);
		}

		wp_insert_post( $args );
	}

	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	update_option( 'rewrite_rules', false );
	$wp_rewrite->flush_rules( true );

	wp_safe_redirect( admin_url( 'edit.php?post_type=block_pattern&blockify_import_patterns=true' ) );
}


add_action( 'admin_notices', NS . 'pattern_import_success_notice' );
/**
 * Admin notice for pattern import.
 *
 * @since 1.0.0
 *
 * @return void
 */
function pattern_import_success_notice() {
	$imported  = (bool) ( $_GET['blockify_import_patterns'] ?? false );
	$post_type = (string) ( $_GET['post_type'] ?? '' );

	if ( ! $imported || 'block_pattern' !== $post_type ) {
		return;
	}

	?>
	<div class="notice notice-success is-dismissible">
		<p><?php _e( 'Patterns successfully imported.', 'blockify-pro' ); ?></p>
	</div>
	<?php
}


add_action( 'admin_post_blockify_export_patterns', NS . 'export_patterns' );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return void
 */
function export_patterns() {
	$block_patterns = get_posts(
		[
			'post_type'      => 'block_pattern',
			'posts_per_page' => -1,
		]
	);

	foreach ( $block_patterns as $block_pattern ) {
		export_pattern( $block_pattern->ID, $block_pattern, true );
	}

	wp_safe_redirect( admin_url( 'edit.php?post_type=block_pattern&blockify_export_patterns=true' ) );
}

add_action( 'admin_notices', NS . 'pattern_export_success_notice' );
/**
 * Admin notice for pattern export.
 *
 * @since 1.0.0
 *
 * @return void
 */
function pattern_export_success_notice() {
	$exported  = (bool) ( $_GET['blockify_export_patterns'] ?? false );
	$post_type = (string) ( $_GET['post_type'] ?? '' );

	if ( ! $exported || 'block_pattern' !== $post_type ) {
		return;
	}

	?>
	<div class="notice notice-success is-dismissible">
		<p>
			<?php echo __( 'Patterns successfully exported.', 'blockify-pro' ); ?>
		</p>
	</div>
	<?php
}

add_action( 'save_post_block_pattern', NS . 'export_pattern', 10, 3 );
/**
 * Handles export pattern request.
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
	if ( ! $update ) {
		return $post_ID;
	}

	if ( $post->post_status === 'trash' ) {
		return $post_ID;
	}

	$category = explode( '-', $post->post_name )[0] ?? null;

	if ( ! $category ) {
		return $post_ID;
	}

	$stylesheet  = get_stylesheet();
	$default_dir = apply_filters( 'blockify_pattern_export_dir', "themes/$stylesheet/patterns" );
	$pattern_dir = WP_CONTENT_DIR . DS . trailingslashit( $default_dir );
	$content     = replace_nav_menu_refs( $post->post_content );
	$content     = replace_image_paths( $content );
	$block_types = '';

	if ( 'page' === $category ) {
		$block_types .= 'core/post-content,';
	}

	if ( 'header' === $category ) {
		$block_types .= 'core/template-part/header,';
	}

	if ( 'footer' === $category ) {
		$block_types .= 'core/template-part/footer,';
	}

	if ( $block_types ) {
		$block_types = 'Block Types: ' . rtrim( $block_types, ',' );
	}

	$data = <<<EOF
<?php
/**
 * Title: $post->post_title
 * Slug: $post->post_name
 * Categories: $category
 * $block_types
 */
?>
$content
EOF;

	if ( ! file_exists( $pattern_dir ) ) {
		wp_mkdir_p( $pattern_dir );
	}

	file_put_contents(
		$pattern_dir . $post->post_name . '.php',
		$data
	);

	return $post_ID;
}

/**
 * Removes nav menu references from pattern content.
 *
 * @since 1.0.0
 *
 * @param string $html
 *
 * @return string
 */
function replace_nav_menu_refs( string $html ): string {
	preg_match( '/"ref":(\d+),/i', $html, $matches );

	if ( $matches ) {
		foreach ( $matches as $match ) {
			//$html = str_replace( $match, '', $html );
		}
	}

	return $html;
}

/**
 * Replaces image paths with theme URI.
 *
 * @since 1.0.0
 *
 * @param string $html
 *
 * @return string
 */
function replace_image_paths( string $html ): string {
	$regex       = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
	$types       = [ 'jpg', 'jpeg', 'png', 'gif', 'mp4' ];
	$upload_dir  = wp_upload_dir();
	$content_dir = trailingslashit( WP_CONTENT_DIR );
	$stylesheet  = get_stylesheet();
	$setting     = apply_filters( 'blockify_image_export_dir', "themes/$stylesheet/assets/img" );
	$setting     = implode( DS, explode( DS, $setting ) );
	$img_dir     = $content_dir . $setting . DS;

	preg_match_all( $regex, $html, $matches );

	if ( ! isset( $matches[0] ) || ! is_array( $matches[0] ) ) {
		return $html;
	}

	foreach ( $matches[0] as $url ) {
		$basename = basename( $url );

		if ( ! str_contains( $basename, '.' ) ) {
			continue;
		}

		[ $file, $type ] = explode( '.', basename( $url ) );

		if ( ! in_array( $type, $types ) ) {
			continue;
		}

		// Limit to current site.
		$host = wp_parse_url( get_home_url() )['host'] ?? '';

		if ( ! str_contains( $url, $host ) ) {
			continue;
		}

		$original = str_replace(
			$upload_dir['baseurl'],
			$upload_dir['basedir'],
			$url
		);

		if ( ! file_exists( $original ) ) {
			continue;
		}

		if ( ! file_exists( $img_dir ) ) {
			wp_mkdir_p( $img_dir );
		}

		$new = $img_dir . $basename;

		copy( $original, $new );

		$html = str_replace( $url, $new, trim( $html ) );
	}

	$html = str_replace(
		$img_dir,
		'<?php echo content_url( "/' . $setting . '/" ) ?>',
		$html
	);

	$html = str_replace(
		get_stylesheet_directory_uri(),
		'<?php echo get_stylesheet_directory_uri() ?>',
		$html
	);

	$html = str_replace(
		home_url(),
		'<?php echo home_url() ?>',
		$html
	);

	return $html;
}
