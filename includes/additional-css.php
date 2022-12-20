<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

add_filter( 'blockify_inline_css', NS . 'add_additional_css', 10, 3 );
/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @param string $css       CSS string.
 * @param string $content   Page content.
 * @param bool   $is_editor Is editor.
 *
 * @return string
 */
function add_additional_css( string $css, string $content, bool $is_editor ): string {
	$additional_css = get_option( SLUG )['additionalCss'] ?? '';

	if ( $additional_css && ! $is_editor ) {
		$css .= $additional_css . $css;
	}

	return strip_tags( $css );
}
