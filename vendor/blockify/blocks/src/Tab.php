<?php

declare( strict_types=1 );

namespace Blockify\Blocks;

use WP_Block;

/**
 * Tab class.
 *
 * @since 1.0.0
 */
class Tab extends AbstractBlock {

	/**
	 * Renders the tab block.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block object.
	 *
	 * @return string
	 */
	public function render( array $attributes, string $content, WP_Block $block ): string {
		return $content;
	}
}
