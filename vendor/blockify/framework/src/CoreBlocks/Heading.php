<?php

declare( strict_types=1 );

namespace Blockify\Framework\CoreBlocks;

use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use Blockify\Utilities\Str;
use WP_Block;
use function esc_html;
use function esc_html__;
use function explode;
use function get_search_query;
use function implode;
use function in_array;
use function intval;
use function sanitize_title_with_dashes;
use function sprintf;

/**
 * Heading class.
 *
 * @since 1.0.0
 */
class Heading implements Renderable {

	/**
	 * Modifies front end HTML output of block.
	 *
	 * @since 0.0.2
	 *
	 * @param string   $block_content Block HTML.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block object.
	 *
	 * @hook  render_block_core/heading
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$dom     = DOM::create( $block_content );
		$level   = intval( $block['attrs']['level'] ?? 2 );
		$heading = DOM::get_element( 'h' . $level, $dom );

		if ( ! $heading ) {
			return $block_content;
		}

		$classes = explode( ' ', $heading->getAttribute( 'class' ) );

		if ( ! in_array( 'wp-block-heading', $classes, true ) ) {
			$classes[] = 'wp-block-heading';
		}

		$styles = CSS::string_to_array( $heading->getAttribute( 'style' ) );

		$gap = $block['attrs']['style']['spacing']['blockGap'] ?? null;

		if ( $gap ) {
			$styles['gap'] = CSS::format_custom_property( $gap );
		}

		$styles = CSS::add_shorthand_property( $styles, 'margin', $block['attrs']['style']['spacing']['margin'] ?? [] );

		$heading->setAttribute(
			'class',
			implode( ' ', $classes )
		);

		if ( $styles ) {
			$heading->setAttribute(
				'style',
				CSS::array_to_string( $styles )
			);
		}

		$id = $heading->getAttribute( 'id' );

		if ( ! $id ) {
			$heading->setAttribute(
				'id',
				Str::remove_non_alphanumeric(
					sanitize_title_with_dashes(
						$heading->textContent
					)
				)
			);
		}

		if ( ! $heading->getAttribute( 'style' ) ) {
			$heading->removeAttribute( 'style' );
		}

		$search_query = get_search_query();
		$default      = esc_html__( 'Search Results', 'blockify' );

		if ( $level === 1 && $search_query && $heading->textContent === $default ) {
			$heading->textContent = sprintf(
				esc_html__( 'Search results for: ', 'blockify' ) . '%s',
				esc_html( $search_query )
			);
		}

		return $dom->saveHTML();
	}

}
