<?php

declare( strict_types=1 );

namespace Blockify\Dom;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Exception;
use WP_Error;
use function addslashes;
use function bin2hex;
use function current;
use function iconv;
use function is_a;
use function is_null;
use function libxml_clear_errors;
use function libxml_use_internal_errors;
use function ltrim;
use function preg_replace_callback;
use function sprintf;
use function strtoupper;
use const LIBXML_HTML_NODEFDTD;
use const LIBXML_HTML_NOIMPLIED;
use const XML_ELEMENT_NODE;

/**
 * DOM utility.
 *
 * @since 1.0.0
 */
class DOM {

	/**
	 * Returns a formatted DOMDocument object from a given string.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html HTML string to convert to DOM.
	 *
	 * @return DOMDocument
	 */
	public static function create( string $html ): DOMDocument {
		$dom = new DOMDocument();

		if ( ! $html ) {
			return $dom;
		}

		$dom->preserveWhiteSpace = false;
		$dom->formatOutput       = true;

		// Setting libxml options with bitwise operator.
		$options = 0;
		$options |= defined( 'LIBXML_HTML_NOIMPLIED' ) ? LIBXML_HTML_NOIMPLIED : 0;
		$options |= defined( 'LIBXML_HTML_NODEFDTD' ) ? LIBXML_HTML_NODEFDTD : 0;

		// @see https://stackoverflow.com/questions/13280200/convert-unicode-to-html-entities-hex.
		// @todo Check if all DOMs need this.
		$html = static::convert_unicode_to_html_entities( $html );

		libxml_use_internal_errors( true );

		$dom->loadHTML( $html, $options );

		libxml_clear_errors();
		libxml_use_internal_errors( false );

		return $dom;
	}

	/**
	 * Returns a formatted DOMElement object from a DOMDocument object.
	 *
	 * @since 1.0.0
	 *
	 * @param string $tag            HTML tag.
	 * @param mixed  $dom_or_element DOMDocument or DOMElement.
	 * @param int    $index          Index of element to return.
	 *
	 * @return ?DOMElement
	 */
	public static function get_element( string $tag, $dom_or_element, int $index = 0 ): ?DOMElement {
		if ( ! is_a( $dom_or_element, DOMDocument::class ) && ! is_a( $dom_or_element, DOMElement::class ) ) {
			return null;
		}

		$element = $dom_or_element->getElementsByTagName( $tag )->item( $index );

		if ( ! $element ) {
			return null;
		}

		return self::node_to_element( $element );
	}

	/**
	 * Casts a DOMNode to a DOMElement.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $node DOMNode to cast to DOMElement.
	 *
	 * @return ?DOMElement
	 */
	public static function node_to_element( $node ): ?DOMElement {
		if ( $node && $node->nodeType === XML_ELEMENT_NODE ) {
			/* @var DOMElement $node DOM Element node */
			return $node;
		}

		return null;
	}

	/**
	 * Returns an HTML element with a replaced tag.
	 *
	 * @since 1.0.0
	 *
	 * @param string     $name    Tag name, e.g: 'div'.
	 * @param DOMElement $element DOM Element to change.
	 *
	 * @return ?DOMElement
	 */
	public static function change_tag_name( string $name, DOMElement $element ): ?DOMElement {
		if ( ! $element->ownerDocument ) {
			return null;
		}

		$child_nodes = [];

		foreach ( $element->childNodes as $child ) {
			$child_nodes[] = $child;
		}

		$new_element = $element->ownerDocument->createElement( $name );

		foreach ( $child_nodes as $child ) {
			$child2 = $element->ownerDocument->importNode( $child, true );
			$new_element->appendChild( $child2 );
		}

		foreach ( $element->attributes as $attr_node ) {
			$attr_name  = $attr_node->nodeName;
			$attr_value = $attr_node->nodeValue;

			$new_element->setAttribute( $attr_name, $attr_value );
		}

		if ( $element->parentNode ) {
			$element->parentNode->replaceChild( $new_element, $element );
		}

		return $new_element;
	}

	/**
	 * Returns an array of DOM elements by class name.
	 *
	 * @since 0.9.26
	 *
	 * @param string      $class_name Element class name.
	 * @param DOMDocument $dom        DOM document or element.
	 * @param string      $tag        Element tag name (optional).
	 *
	 * @return array
	 */
	public static function get_elements_by_class_name( string $class_name, DOMDocument $dom, string $tag = '*' ): array {
		$xpath    = new DOMXPath( $dom );
		$query    = sprintf( "//%s[contains(concat(' ', normalize-space(@class), ' '), ' %s ')]", $tag, $class_name );
		$nodes    = $xpath->query( $query );
		$elements = [];

		if ( $nodes !== false ) {
			foreach ( $nodes as $node ) {
				if ( $node instanceof DOMElement ) {
					$elements[] = $node;
				}
			}
		}

		return $elements;
	}

	/**
	 * Creates a DOMElement to avoid unhandled exceptions.
	 *
	 * @since 1.3.0
	 *
	 * @param string      $tag HTML tag.
	 * @param DOMDocument $dom DOM object.
	 *
	 * @return ?DOMElement
	 */
	public static function create_element( string $tag, DOMDocument $dom ): ?DOMElement {
		$element = null;

		try {
			$element = $dom->createElement( $tag );
		} catch ( Exception $e ) {
			new WP_Error( 'invalid_dom_tag', $e->getMessage() );
		}

		if ( is_null( $element ) ) {
			return null;
		}

		return self::node_to_element( $element );
	}

	/**
	 * Adds CSS classes to a DOM element.
	 *
	 * @since 1.0.0
	 *
	 * @param DOMElement $element DOM element.
	 * @param array      $classes Classes to add.
	 *
	 * @return void
	 */
	public static function add_classes( DOMElement $element, array $classes ): void {
		$element->setAttribute(
			'class',
			trim(
				implode(
					' ',
					array_unique(
						array_merge(
							self::get_classes( $element ),
							$classes
						)
					)
				)
			)
		);
	}

	/**
	 * Gets classes from a DOM element.
	 *
	 * @since 1.0.0
	 *
	 * @param DOMElement $element DOM element.
	 *
	 * @return array
	 */
	public static function get_classes( DOMElement $element ): array {
		$classes = explode( ' ', $element->getAttribute( 'class' ) );

		return array_filter( $classes );
	}

	/**
	 * Adds CSS styles to a DOM element.
	 *
	 * @since 1.0.0
	 *
	 * @param DOMElement $element DOM element.
	 * @param array      $styles  Styles to add.
	 *
	 * @return void
	 */
	public static function add_styles( DOMElement $element, array $styles ): void {
		$element->setAttribute(
			'style',
			CSS::array_to_string(
				array_merge(
					self::get_styles( $element ),
					$styles
				)
			)
		);
	}

	/**
	 * Gets styles from a DOM element.
	 *
	 * @since 1.0.0
	 *
	 * @param DOMElement $element DOM element.
	 *
	 * @return array
	 */
	public static function get_styles( DOMElement $element ): array {
		return CSS::string_to_array( $element->getAttribute( 'style' ) );
	}

	/**
	 * Returns an array of DOM elements that contain the specified text content.
	 *
	 * @since 1.0.0
	 *
	 * @param DOMDocument $dom  The DOM document to search within.
	 * @param string      $text Text to search for in elements.
	 * @param string      $tag  Optional. The tag name to limit the search; default is '*' (all elements).
	 *
	 * @return DOMElement[]
	 */
	public static function get_elements_by_content( DOMDocument $dom, string $text, string $tag = '*' ): array {
		$xpath    = new DOMXPath( $dom );
		$query    = sprintf( "//%s[contains(., '%s')]", $tag, addslashes( $text ) );
		$nodes    = $xpath->query( $query );
		$elements = [];

		if ( ! $nodes ) {
			return $elements;
		}

		foreach ( $nodes as $node ) {
			if ( $node instanceof DOMElement ) {
				$elements[] = $node;
			}
		}

		return $elements;
	}

	/**
	 * Returns a formatted HTML string from a DOMDocument object.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html HTML string to convert to DOM.
	 *
	 * @return string
	 */
	private static function convert_unicode_to_html_entities( string $html ): string {
		return preg_replace_callback(
			'/[\x{80}-\x{10FFFF}]/u',
			static fn( array $matches ): string => sprintf(
				'&#x%s;',
				ltrim(
					strtoupper(
						bin2hex(
							iconv(
								'UTF-8',
								'UCS-4',
								current( $matches )
							)
						)
					),
					'0'
				)
			),
			$html
		);
	}
}
