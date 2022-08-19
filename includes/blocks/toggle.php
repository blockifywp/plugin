<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use DOMElement;
use function add_filter;
use function explode;
use function str_contains;
use function trim;

add_filter( 'render_block', NS . 'render_toggle_block', 10, 2 );
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
function render_toggle_block( string $content, array $block ): string {
	if ( 'blockify/toggle' !== $block['blockName'] ) {
		return $content;
	}

	// TODO: Add ID.
	$id  = '123';
	$dom = dom( $content );

	/**
	 * @var $div DOMElement
	 */
	$div   = $dom->getElementsByTagName( 'div' )->item( 0 );
	$label = change_tag_name( $div, 'label' );
	$label->setAttribute( 'for', $id );
	$label->setAttribute( 'class', 'blockify-toggle-label ' . strip_block_editor_classes( $label->getAttribute( 'class' ) ) );
	$div->ownerDocument->appendChild( $label );

	/**
	 * @var $span DOMElement
	 */
	$span = $label->getElementsByTagName( 'span' )->item( 0 );

	foreach ( $span->childNodes as $child_node ) {
		$label->appendChild( $child_node );
	}

	$label->removeChild( $span );

	/**
	 * @var $input DOMElement
	 */
	$input = $label->getElementsByTagName( 'input' )->item( 0 );
	$input->setAttribute( 'class', 'blockify-toggle-input' );
	$input->setAttribute( 'id', $id );
	$input->setAttribute( 'name', $id );

	return $dom->saveHTML();
}

/**
 * Removes all block editor class names from element.
 *
 * @since 0.0.2
 *
 * @param string $classes
 *
 * @return string
 */
function strip_block_editor_classes( string $classes ): string {
	$class_array  = explode( ' ', $classes );
	$class_string = '';

	foreach ( $class_array as $class_name ) {
		if ( ! str_contains( $class_name, '-block' ) ) {
			$class_string .= ' ' . $class_name;
		}
	}

	return trim( $class_string );
}
