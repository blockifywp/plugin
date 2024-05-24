<?php

declare( strict_types=1 );

namespace Blockify\Framework\Interfaces;

use WP_Block;

interface Renderable {

	/**
	 * Render the object.
	 *
	 * @since 0.1.0
	 *
	 * @param string   $block_content The block content.
	 * @param array    $block         The full block, including name and attributes.
	 * @param WP_Block $instance      The block instance.
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string;

}
