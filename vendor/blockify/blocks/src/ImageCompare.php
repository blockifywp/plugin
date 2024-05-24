<?php

declare( strict_types=1 );

namespace Blockify\Blocks;

use WP_Block;

/**
 * Image compare block.
 *
 * @since 1.0.0
 */
class ImageCompare extends AbstractBlock {

	/**
	 * Renders the image compare block.
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
