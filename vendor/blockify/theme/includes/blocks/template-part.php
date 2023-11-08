<?php

declare( strict_types=1 );

namespace Blockify\Theme;

use function add_filter;
use function explode;
use function implode;
use function in_array;

add_filter( 'render_block_core/template-part', NS . 'render_block_template_part', 10, 2 );
/**
 * Modifies the template part block.
 *
 * @since 0.7.1
 *
 * @param string $html  Block HTML.
 * @param array  $block Block data.
 *
 * @return string
 */
function render_block_template_part( string $html, array $block ): string {
	$dom   = dom( $html );
	$first = get_dom_element( '*', $dom );

	if ( ! $first ) {
		return $html;
	}

	$attrs  = $block['attrs'] ?? [];
	$styles = css_string_to_array( $first->getAttribute( 'style' ) );
	$color  = $attrs['style']['color'] ?? [];

	if ( isset( $color['background'] ) ) {
		$styles['background'] = $color['background'];
	}

	if ( isset( $attrs['backgroundColor'] ) ) {
		$styles['background'] = 'var(--wp--preset--color--' . $attrs['backgroundColor'] . ')';
	}

	if ( isset( $color['gradient'] ) ) {
		$styles['background'] = $color['gradient'];
	}

	if ( isset( $attrs['gradient'] ) ) {
		$styles['background'] = 'var(--wp--preset--gradient--' . $attrs['gradient'] . ')';
	}

	if ( isset( $color['text'] ) ) {
		$styles['color'] = $color['text'];
	}

	if ( isset( $attrs['textColor'] ) ) {
		$styles['color'] = 'var(--wp--preset--color--' . $attrs['textColor'] . ')';
	}

	$styles = css_array_to_string( $styles );

	if ( $styles ) {
		$first->setAttribute( 'style', $styles );
	} else {
		$first->removeAttribute( 'style' );
	}

	$body_classes = get_body_class();
	$is_blank     = false;

	if ( in_array( 'page-template-blank', $body_classes, true ) ) {
		$is_blank = true;
	}

	$classes = explode( ' ', $first->getAttribute( 'class' ) );

	if ( $block['attrs']['slug'] === 'header' ) {
		if ( $is_blank ) {
			return '';
		}

		$first->setAttribute( 'role', 'banner' );
		$first->setAttribute( 'id', 'top' );
		$classes = [
			...$classes,
			'site-header',
		];
	}

	if ( $block['attrs']['slug'] === 'footer' ) {
		if ( $is_blank ) {
			return '';
		}

		$first->setAttribute( 'role', 'contentinfo' );
		$classes = [
			'site-footer',
			...$classes,
		];
	}

	$first->setAttribute( 'class', implode( ' ', $classes ) );

	return $dom->saveHTML();
}
