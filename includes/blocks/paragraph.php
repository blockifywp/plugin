<?php

declare( strict_types=1 );

namespace Blockify;

add_filter( 'render_block', NS . 'render_paragraph_block', 10, 2 );
/**
 * Modifies front end HTML output of block.
 *
 * @since 0.0.2
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function render_paragraph_block( string $content, array $block ): string {
	if ( $block['blockName'] !== 'core/paragraph' ) {
		return $content;
	}

	$shortcodes = [
		'[year]' => \date('Y'),
		'[user]' => wp_get_current_user()->display_name,
	];

	foreach ($shortcodes as $shortcode => $value) {
		$content = \str_replace( $shortcode, $value, $content );
	}

	return $content;
}
