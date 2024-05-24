<?php

declare( strict_types=1 );

namespace Blockify\Blocks;

use Blockify\Utilities\Block;
use Blockify\Dom\DOM;
use WP_Block;
use WP_Block_Patterns_Registry;
use function _wp_to_kebab_case;
use function esc_attr;
use function esc_html;
use function get_the_content;
use function parse_blocks;
use function sprintf;

/**
 * Registers the table of contents block.
 *
 * @since 1.0.0
 */
class TableOfContents extends AbstractBlock {

	/**
	 * Table of Contents shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $attributes The attributes.
	 * @param string   $content    The content.
	 * @param WP_Block $block      The block.
	 *
	 * @return string
	 */
	public function render( array $attributes, string $content, WP_Block $block ): string {
		$content = get_the_content();

		if ( ! $content ) {
			return '';
		}

		$blocks   = parse_blocks( $content );
		$headings = Block::search_blocks( $blocks, 'core/heading' );

		if ( ! $headings ) {
			$patterns = Block::search_blocks( $blocks, 'blockify/pattern' );

			if ( $patterns ) {
				$registered = WP_Block_Patterns_Registry::get_instance()->get_registered( 'page-style-guide' );
				$blocks     = parse_blocks( $registered['content'] ?? '' );
				$headings   = Block::search_blocks( $blocks, 'core/heading' );

				if ( ! $headings ) {
					return '';
				}
			}
		}

		$tree = $this->sort_heading_blocks( $headings );
		$dom  = DOM::create( $this->generate_nested_list( $tree ) );
		$ul   = DOM::get_element( 'ul', $dom );
		$ul->setAttribute( 'class', 'wp-block-blockify-table-of-contents' );

		return $dom->saveHTML();
	}

	/**
	 * Sorts heading blocks into nested array.
	 *
	 * @param array $blocks
	 *
	 * @return array
	 */
	private function sort_heading_blocks( array $blocks ): array {
		$sorted = [];
		$path   = [ &$sorted ];

		foreach ( $blocks as $block ) {
			$level = $block['attrs']['level'] ?? 2;

			if ( $level === 1 ) {
				continue;
			}

			$heading = strip_tags( $block['innerHTML'] );

			while ( count( $path ) >= $level ) {
				array_pop( $path );
			}

			$current             = &$path[ count( $path ) - 1 ];
			$current[ $heading ] = [];
			$path[]              = &$current[ $heading ];
		}

		return $sorted;
	}

	/**
	 * Generates nested list from tree.
	 *
	 * @param array $tree
	 *
	 * @return string
	 */
	private function generate_nested_list( array $tree ): string {
		$html = '<ul>';

		foreach ( $tree as $heading => $sub_headings ) {
			$html .= '<li>';
			$html .= sprintf(
				'<a href="#%s">%s</a>',
				esc_attr( _wp_to_kebab_case( $heading ) ),
				esc_html( $heading )
			);

			if ( $sub_headings ) {
				$html .= $this->generate_nested_list( $sub_headings );
			}

			$html .= '</li>';
		}

		$html .= '</ul>';

		return $html;
	}

}
