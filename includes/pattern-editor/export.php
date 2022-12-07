<?php

declare( strict_types=1 );

namespace Blockify\PatternEditor;

use const WP_CONTENT_DIR;
use function __;
use function add_action;
use function admin_url;
use function apply_filters;
use function basename;
use function explode;
use function file_exists;
use function file_put_contents;
use function get_home_url;
use function home_url;
use function in_array;
use function is_array;
use function preg_match_all;
use function str_contains;
use function str_replace;
use function trailingslashit;
use function trim;
use function wp_mkdir_p;
use function wp_parse_url;
use function wp_safe_redirect;
use WP_Post;

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

function get_pattern_dir( bool $full = true ): string {
	$stylesheet = get_stylesheet();
	$export_dir = apply_filters( 'pattern_editor_dir', "themes/$stylesheet/patterns" );

	if ( $full ) {
		$export_dir = WP_CONTENT_DIR . DS . trailingslashit( $export_dir );
	}

	return $export_dir;
}


if ( apply_filters( 'pattern_export_save', true ) ) {
	add_action( 'save_post_block_pattern', NS . 'export_pattern', 10, 3 );
}

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

	[ $category ] = explode( '-', $post->post_name );

	if ( ! $category ) {
		return $post_ID;
	}

	$pattern_dir = get_pattern_dir();
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
			$html = str_replace( $match, '', $html );
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
	$types       = [ 'jpg', 'jpeg', 'png', 'gif', 'svg', 'mp4' ];
	$upload_dir  = wp_upload_dir();
	$pattern_dir = implode( DS, explode( DS, get_pattern_dir( false ) ) );
	$img_dir     = $pattern_dir . '/assets/img/';
	$img_dir     = apply_filters( 'pattern_editor_img_dir', $img_dir, $pattern_dir );

	$img_content_dir = WP_CONTENT_DIR . DS . $img_dir;

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

		if ( $type === 'mp4' ) {
			$img_dir = str_replace( '/img', '/video', $img_dir );
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

		if ( ! file_exists( $img_content_dir ) ) {
			wp_mkdir_p( $img_content_dir );
		}

		$new = $img_content_dir . $basename;

		copy( $original, $new );

		$html = str_replace( $url, $new, trim( $html ) );
	}

	$html = str_replace(
		$img_content_dir,
		'<?php echo content_url( "/' . $img_dir . '" ) ?>',
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

