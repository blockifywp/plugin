<?php

declare( strict_types=1 );

namespace Blockify\Framework\BlockSettings;

use Blockify\Framework\Interfaces\Renderable;
use Blockify\Utilities\Str;
use WP_Block;
use WP_Block_Type_Registry;
use function add_filter;
use function apply_filters;
use function array_keys;
use function array_merge;
use function esc_html;
use function get_bloginfo;
use function get_post_field;
use function get_post_meta;
use function get_post_type;
use function get_post_type_object;
use function get_queried_object;
use function get_stylesheet;
use function get_the_archive_title;
use function get_the_ID;
use function get_the_title;
use function gmdate;
use function home_url;
use function in_array;
use function is_a;
use function is_archive;
use function is_author;
use function is_callable;
use function is_day;
use function is_home;
use function is_month;
use function is_null;
use function is_year;
use function preg_match_all;
use function shortcode_exists;
use function str_contains;
use function str_replace;
use function strip_tags;
use function strtolower;

/**
 * TemplateTags class.
 *
 * @since 1.0.0
 */
class TemplateTags implements Renderable {

	/**
	 * Allow custom data to be rendered in blocks.
	 *
	 * Runs before the block is rendered, so that the custom field
	 * string can be used in shortcode block attributes.
	 *
	 * @since 0.9.34
	 *
	 * @param string   $block_content Block HTML.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block context.
	 *
	 * @hook  render_block 8
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$block_name        = $block['blockName'] ?? '';
		$registered_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();

		if ( ! array_key_exists( $block_name, $registered_blocks ) ) {
			return $block_content;
		}

		$category     = $registered_blocks[ $block_name ]->category ?? '';
		$other_blocks = [
			'core/button',
			'core/image',
			'core/navigation-link',
			'core/post-excerpt',
		];

		if ( 'text' !== $category && ! in_array( $block_name, $other_blocks, true ) ) {
			return $block_content;
		}

		$block_content = str_replace(
			[ '&#123;', '&#125;', '%7B', '%7D' ],
			[ '{', '}', '{', '}' ],
			$block_content
		);

		if ( ! str_contains( $block_content, '{' ) || ! str_contains( $block_content, '}' ) ) {
			return $block_content;
		}

		preg_match_all( '#\{(.*?)}#', $block_content, $matches );

		$without_brackets = $matches[1] ?? [];

		if ( empty( $without_brackets ) ) {
			return $block_content;
		}

		$post_id      = $instance->context['postId'] ?? get_the_ID();
		$replacements = [];

		foreach ( $without_brackets as $tag ) {
			$tag         = strtolower( $tag );
			$replacement = '';

			if ( shortcode_exists( $tag ) ) {
				continue;
			}

			if ( ! is_null( $post_id ) ) {
				$post_field = get_post_field( $tag, $post_id );

				if ( $post_field ) {
					$replacement = $post_field;
				} else {
					$post_meta = get_post_meta( $post_id, $tag, true );

					if ( $post_meta ) {
						$replacement = $post_meta;
					}
				}
			}

			if ( ! $replacement ) {
				$tags = $this->get_template_tags( $post_id ?: null );

				if ( isset( $tags[ $tag ] ) ) {
					$replacement = is_callable( $tags[ $tag ] ) ? call_user_func( $tags[ $tag ] ) : $tags[ $tag ];
				}
			}

			if ( $replacement ) {
				$replacements[ '{' . $tag . '}' ] = esc_html( $replacement );
			}
		}

		return str_replace( array_keys( $replacements ), array_values( $replacements ), $block_content );
	}

	/**
	 * Get the archive title for the home page.
	 *
	 * @since 1.3.0
	 *
	 * @param string $title Archive title.
	 *
	 * @hook  get_the_archive_title
	 *
	 * @return string
	 */
	public function get_the_archive_title_home( string $title ): string {
		if ( is_home() ) {
			$title = get_the_title( get_option( 'page_for_posts', true ) );
		}

		return $title;
	}

	/**
	 * Adds post tags not included in post fields.
	 *
	 * @since 1.3.0
	 *
	 * @param array $tags    Template tags.
	 * @param ?int  $post_id Post ID.
	 *
	 * @hook  blockify_template_tags
	 *
	 * @return array
	 */
	private function get_post_template_tags( ?int $post_id ): array {
		$tags = [];

		if ( ! $post_id ) {
			return $tags;
		}

		$tags['post_id']   = $post_id;
		$tags['permalink'] = get_permalink( $post_id );

		$post_type        = get_post_type( $post_id );
		$post_type_object = get_post_type_object( $post_type );

		if ( $post_type_object ) {
			$tags['post_type_label'] = $post_type_object->label;
		}

		return $tags;
	}

	/**
	 * Adds archive tags.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	private function get_archive_template_tags(): array {
		$tags = [];

		if ( is_archive() || is_home() ) {
			$tags['archive_title'] = static function (): string {
				add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

				$title = get_the_archive_title();

				remove_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

				return $title;
			};

			$tags['archive_name'] = static function (): string {
				$queried_object = get_queried_object();
				$name           = '';

				if ( is_a( $queried_object, 'WP_Term' ) ) {
					$name = $queried_object->name;
				}

				if ( is_home() ) {
					$name = get_post_field( 'post_name', get_option( 'page_for_posts', true ) );
				}

				if ( is_month() ) {
					$name = strip_tags( get_the_date( 'F Y' ) );
				}

				if ( is_year() ) {
					$name = strip_tags( get_the_date( 'Y' ) );
				}

				if ( is_day() ) {
					$name = strip_tags( get_the_date( 'F j, Y' ) );
				}

				if ( is_author() ) {
					$name = get_the_author();
				}

				return $name;
			};
		}

		return $tags;
	}

	/**
	 * Additional template tags.
	 *
	 * @since 1.3.0
	 *
	 * @param ?int $post_id Post ID.
	 *
	 * @return array
	 */
	private function get_extra_template_tags( ?int $post_id ): array {
		$tags                = [];
		$tags['logout']      = esc_url( wp_logout_url() );
		$tags['logout_home'] = esc_url( wp_logout_url( home_url() ) );

		$queried_object = get_queried_object();
		$taxonomy       = null;

		if ( is_a( $queried_object, 'WP_Term' ) ) {
			$tags['term_id']          = esc_html( $queried_object->term_id );
			$tags['term_name']        = esc_html( $queried_object->name );
			$tags['term_slug']        = esc_html( $queried_object->slug );
			$tags['term_description'] = esc_html( $queried_object->description );

			$taxonomy = get_taxonomy( $queried_object->taxonomy );
		}

		if ( $taxonomy->labels ?? null ) {
			foreach ( $taxonomy->labels as $key => $label ) {
				$tags[ 'taxonomy_' . $key ] = esc_html( $label );
			}
		}

		if ( $post_id ) {
			$tags['read_time'] = static function () use ( $post_id ): string {
				$per_minute = apply_filters( 'blockify_words_per_minute', 200 );
				$words      = str_word_count( strip_tags( get_post_field( 'post_content', $post_id ) ) );
				$minutes    = floor( $words / $per_minute );

				return (string) $minutes;
			};
		}

		return $tags;
	}

	/**
	 * Get template tags.
	 *
	 * @since 0.9.34
	 *
	 * @param ?int $post_id Extra tags.
	 *
	 * @return array
	 */
	private function get_template_tags( ?int $post_id ): array {
		$year       = gmdate( 'Y' );
		$site_name  = get_bloginfo( 'name', 'display' );
		$stylesheet = get_stylesheet();
		$tags       = array_merge(
			[
				'year'         => $year,
				'current_year' => $year, // Backwards compatibility.
				'date'         => gmdate( 'm/d/Y' ),
				'home_url'     => home_url(),
				'site_title'   => $site_name,
				'site_name'    => $site_name,
				'stylesheet'   => $stylesheet,
				'theme_name'   => Str::title_case( $stylesheet ),
			],
			$this->get_post_template_tags( $post_id ),
			$this->get_archive_template_tags(),
			$this->get_extra_template_tags( $post_id )
		);

		/**
		 * Filter template tags.
		 *
		 * @since 0.9.34
		 *
		 * @param array $tags    Template tags.
		 * @param int   $post_id Post ID.
		 *
		 * @return array
		 */
		return apply_filters( 'blockify_template_tags', $tags, $post_id );
	}

}
