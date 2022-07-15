<?php

declare( strict_types=1 );

namespace Blockify;

use DOMDocument;
use DOMElement;
use function add_filter;
use function apply_filters;
use function array_values;
use function basename;
use function dirname;
use function file_exists;
use function filemtime;
use function get_stylesheet_directory;
use function get_stylesheet_directory_uri;
use function get_template_directory;
use function get_template_directory_uri;
use function is_admin;
use function json_encode;
use function libxml_clear_errors;
use function libxml_use_internal_errors;
use function plugin_dir_url;
use function str_contains;
use function trim;
use function explode;
use function str_replace;
use function in_array;
use function implode;
use function defined;
use function mb_convert_encoding;
use function preg_match;
use function preg_replace;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_parse_args;
use const DIRECTORY_SEPARATOR;

const CAMEL_CASE    = 'camel';
const PASCAL_CASE   = 'pascal';
const SNAKE_CASE    = 'snake';
const ADA_CASE      = 'ada';
const MACRO_CASE    = 'macro';
const KEBAB_CASE    = 'kebab';
const TRAIN_CASE    = 'train';
const COBOL_CASE    = 'cobol';
const LOWER_CASE    = 'lower';
const UPPER_CASE    = 'upper';
const TITLE_CASE    = 'title';
const SENTENCE_CASE = 'sentence';
const DOT_CASE      = 'dot';

/**
 * Convert string case.
 *
 * camel    myNameIsBond
 * pascal   MyNameIsBond
 * snake    my_name_is_bond
 * ada      My_Name_Is_Bond
 * macro    MY_NAME_IS_BOND
 * kebab    my-name-is-bond
 * train    My-Name-Is-Bond
 * cobol    MY-NAME-IS-BOND
 * lower    my name is bond
 * upper    MY NAME IS BOND
 * title    My Name Is Bond
 * sentence My name is bond
 * dot      my.name.is.bond
 *
 * @since 0.0.2
 *
 * @param string $string
 * @param string $case
 *
 * @return string
 */
function convert_case( string $string, string $case ): string {
	$delimiters = 'sentence' === $case ? [ ' ', '-', '_' ] : [ ' ', '-', '_', '.' ];
	$lower      = trim( str_replace( $delimiters, $delimiters[0], strtolower( $string ) ), $delimiters[0] );
	$upper      = trim( ucwords( $lower ), $delimiters[0] );
	$pieces     = explode( $delimiters[0], $lower );

	$cases = [
		CAMEL_CASE    => lcfirst( str_replace( ' ', '', $upper ) ),
		PASCAL_CASE   => str_replace( ' ', '', $upper ),
		SNAKE_CASE    => strtolower( implode( '_', $pieces ) ),
		ADA_CASE      => str_replace( ' ', '_', $upper ),
		MACRO_CASE    => strtoupper( implode( '_', $pieces ) ),
		KEBAB_CASE    => strtolower( implode( '-', $pieces ) ),
		TRAIN_CASE    => lcfirst( str_replace( ' ', '-', $upper ) ),
		COBOL_CASE    => strtoupper( implode( '-', $pieces ) ),
		LOWER_CASE    => strtolower( $string ),
		UPPER_CASE    => strtoupper( $string ),
		TITLE_CASE    => $upper,
		SENTENCE_CASE => ucfirst( $lower ),
		DOT_CASE      => strtolower( implode( '.', $pieces ) ),
	];

	$string = $cases[ $case ] ?? $string;
	$string = in_array( $string, [ 'Wordpress' ] ) ? 'WordPress' : $string;

	return apply_filters( 'blockify_convert_case', $string );
}

/**
 * Returns part of string between two strings.
 *
 * @since 0.0.2
 *
 * @param string $start
 * @param string $end
 * @param string $string
 * @param bool   $omit
 *
 * @return string
 */
function str_between( string $start, string $end, string $string, bool $omit = false ): string {
	$string = ' ' . $string;
	$ini    = strpos( $string, $start );

	if ( $ini == 0 ) {
		return '';
	}

	$ini    += strlen( $start );
	$len    = strpos( $string, $end, $ini ) - $ini;
	$string = $start . substr( $string, $ini, $len ) . $end;

	if ( $omit ) {
		$string = str_replace( [ $start, $end ], '', $string );
	}

	return $string;
}

/**
 * Replaces first occurrence of a string within a string.
 *
 * @since 0.0.2
 *
 * @param string $haystack
 * @param string $needle
 * @param string $replace
 *
 * @return string
 */
function str_replace_first( string $haystack, string $needle, string $replace ): string {
	$pos = strpos( $haystack, $needle );

	if ( $pos !== false ) {
		$haystack = substr_replace( $haystack, $replace, $pos, strlen( $needle ) );
	}

	return $haystack;
}

/**
 * Replaces last occurrence of a string within a string.
 *
 * @since 0.0.2
 *
 * @param string $haystack
 * @param string $needle
 * @param string $replace
 *
 * @return string
 */
function str_replace_last( string $haystack, string $needle, string $replace ): string {
	$pos = strrpos( $haystack, $needle );

	if ( $pos !== false ) {
		$haystack = substr_replace( $haystack, $replace, $pos, strlen( $needle ) );
	}

	return $haystack;
}

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
 * Quick and dirty way to mostly minify CSS.
 *
 * @author Gary Jones
 *
 * @since  0.0.2
 *
 * @param string $css CSS to minify
 *
 * @return string
 */
function minify_css( string $css ): string {
	$css = preg_replace( '/\s+/', ' ', $css );
	$css = preg_replace( '/(\s+)(\/\*(.*?)\*\/)(\s+)/', '$2', $css );
	$css = preg_replace( '~/\*(?![!|*])(.*?)\*/~', '', $css );
	$css = preg_replace( '/;(?=\s*})/', '', $css );
	$css = preg_replace( '/(,|:|;|\{|}|\*\/|>) /', '$1', $css );
	$css = preg_replace( '/ (,|;|\{|}|\(|\)|>)/', '$1', $css );
	$css = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css );
	$css = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css );
	$css = preg_replace( '/0 0 0 0/', '0', $css );
	$css = preg_replace( '/#([a-f0-9])\\1([a-f0-9])\\2([a-f0-9])\\3/i', '#\1\2\3', $css );

	return trim( $css );
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
function css_rules_to_array( string $css ): array {
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

/**
 * Converts
 *
 * @since 0.0.2
 *
 * @param array $rules
 *
 * @return string
 */
function css_rules_to_string( array $rules ): string {
	$css = '';

	foreach ( $rules as $property => $value ) {
		$css .= $property . ':' . $value . ';';
	}

	return $css;
}

/**
 * Returns an attribute value from a HTML element string, with fallback.
 *
 * @since 0.0.2
 *
 * @param string $name
 * @param string $html
 * @param string $default
 *
 * @return string
 */
function get_attr( string $name, string $html, string $default = '' ): string {
	preg_match( '/' . $name . '="(.+?)"/', $html, $matches );

	return $matches[1] ?? $default;
}

/**
 * Removes HTML comments from string.
 *
 * @since 0.0.2
 *
 * @param string $content
 *
 * @return string
 */
function remove_html_comments( string $content = '' ): string {
	return preg_replace( '/<!--(.|\s)*?-->/', '', $content );
}

/**
 * Replaces a HTML elements tag.
 *
 * @since 0.0.2
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
 * Returns a random hex code.
 *
 * @since 0.0.2
 *
 * @param bool $hashtag
 *
 * @return string
 */
function random_hex( bool $hashtag = true ): string {
	return ( $hashtag ? '#' : '' ) . str_pad( dechex( mt_rand( 0, 0xFFFFFF ) ), 6, '0', STR_PAD_LEFT );
}

/**
 * Attempts to log WordPress PHP data to console.
 *
 * @since    0.0.2
 *
 * @param mixed $data
 *
 * @return void
 */
function console_log( $data ): void {
	$data   = json_encode( $data );
	$script = "<script class='console-log'>console.log($data);</script>";

	print $script;
}

/**
 * Enqueues a compiled asset from build directory.
 *
 * @since 0.0.2
 *
 * @param string $base File basename relative to build directory. E.g style.css.
 *
 * @return void
 */
function enqueue_asset( string $base ): void {
	$build   = DIR . 'build/';
	$file    = $build . $base;
	$explode = explode( '.', $base );
	$name    = $explode[0];
	$type    = str_replace( '.', '', $explode[1] ) ?? '';
	$asset   = file_exists( $file ) ? require $build . $name . '.asset.php' : [
		'dependencies' => [],
		'version'      => filemtime( $file ),
	];

	$args = [
		'handle'  => 'blockify-' . $name,
		'src'     => plugin_dir_url( FILE ) . 'build/' . $base,
		'deps'    => $asset['dependencies'],
		'version' => $asset['version'],
	];

	if ( $type === 'css' ) {
		wp_enqueue_style( ...array_values( $args ) );

		$inline = apply_filters( "blockify_{$name}_inline", '' );

		if ( $inline ) {
			wp_add_inline_style( "blockify-$name", $inline );
		}

	} else if ( $type === 'js' ) {
		wp_enqueue_script( ...array_values( $args ) );

		$localize = [
			'handle' => $args['handle'],
			'object' => SLUG,
			'data'   => apply_filters( "blockify_{$name}_data", [] ),
		];

		if ( ! empty( $localize['data'] ) ) {
			wp_localize_script( ...array_values( $localize ) );
		}
	}
}

/**
 * Returns active style variation.
 *
 * @since 0.0.2
 *
 * @return string
 */
function get_style_variation(): string {
	return wp_get_global_settings()['custom']['variation'] ?? 'default';
}
