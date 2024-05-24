<?php

declare( strict_types=1 );

namespace Blockify\Framework\CoreBlocks;

use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function str_replace;

/**
 * Shortcode class.
 *
 * @since 1.0.0
 */
class Shortcode implements Renderable {

	/**
	 * Fix shortcode block empty paragraph tags.
	 *
	 * @param string   $block_content The block content.
	 * @param array    $block         The block.
	 * @param WP_Block $instance      The block instance.
	 *
	 * @hook render_block_core/shortcode 1
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		return str_replace( [ '<p>', '</p>' ], '', $block_content );
	}

	/**
	 * Render the block shortcode.
	 *
	 * @param string $block_content The block content.
	 *
	 * @hook render_block_core/shortcode 11
	 *
	 * @return string
	 */
	public function render_block_shortcode( string $block_content ): string {
		return do_shortcode( $block_content );
	}
}
