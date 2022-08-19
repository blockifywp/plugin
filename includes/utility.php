<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use const PHP_INT_MAX;
use function add_action;
use function defined;
use function explode;
use function libxml_clear_errors;
use function libxml_use_internal_errors;
use function mb_convert_encoding;
use function preg_replace;
use function trim;
use DOMDocument;
use DOMElement;

/**
 * Returns a formatted DOMDocument object from a given string.
 *
 * @since 0.0.2
 *
 * @param string $html
 *
 * @return string
 */
function dom( string $html ): DOMDocument {
	$dom = new DOMDocument();

	if ( ! $html ) {
		return $dom;
	}

	$libxml_previous_state   = libxml_use_internal_errors( true );
	$dom->preserveWhiteSpace = true;

	if ( defined( 'LIBXML_HTML_NOIMPLIED' ) && defined( 'LIBXML_HTML_NODEFDTD' ) ) {
		$options = LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD;
	} else if ( defined( 'LIBXML_HTML_NOIMPLIED' ) ) {
		$options = LIBXML_HTML_NOIMPLIED;
	} else if ( defined( 'LIBXML_HTML_NODEFDTD' ) ) {
		$options = LIBXML_HTML_NODEFDTD;
	} else {
		$options = 0;
	}

	$dom->loadHTML( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' ), $options );

	$dom->formatOutput = true;

	libxml_clear_errors();
	libxml_use_internal_errors( $libxml_previous_state );

	return $dom;
}

/**
 * Returns an HTML element with a replaced tag.
 *
 * @since 0.0.20
 *
 * @param DOMElement $node
 * @param string     $name
 *
 * @return DOMElement
 */
function change_tag_name( DOMElement $node, string $name ): DOMElement {
	$child_nodes = [];

	foreach ( $node->childNodes as $child ) {
		$child_nodes[] = $child;
	}

	$new_node = $node->ownerDocument->createElement( $name );

	foreach ( $child_nodes as $child ) {
		$child2 = $node->ownerDocument->importNode( $child, true );
		$new_node->appendChild( $child2 );
	}

	foreach ( $node->attributes as $attr_node ) {
		$attr_name  = $attr_node->nodeName;
		$attr_value = $attr_node->nodeValue;

		$new_node->setAttribute( $attr_name, $attr_value );
	}

	$node->parentNode->replaceChild( $new_node, $node );

	return $new_node;
}

/**
 * Converts array of CSS rules to string.
 *
 * @since 0.0.22
 *
 * @param array $styles
 *
 * @return string
 */
function css_array_to_string( array $styles ): string {
	$css = '';

	foreach ( $styles as $property => $value ) {
		$css .= $value ? "$property:$value;" : '';
	}

	return $css;
}

/**
 * Converts string of CSS rules to an array.
 *
 * @since 0.0.2
 *
 * @param string $css
 *
 * @return array
 */
function css_string_to_array( string $css ): array {
	$array    = [];
	$elements = explode( ';', $css );

	foreach ( $elements as $element ) {
		$parts = explode( ':', $element, 2 );

		if ( isset( $parts[1] ) ) {
			$property = $parts[0];
			$value    = $parts[1];

			$array[ $property ] = $value;
		}
	}

	return $array;
}
