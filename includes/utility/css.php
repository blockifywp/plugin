<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use function sprintf;
use function explode;

/**
 * Converts array of CSS rules to string.
 *
 * @since 0.0.22
 *
 * @param array  $styles
 * @param string $selector
 *
 * @return string
 */
function css_array_to_string( array $styles, string $selector = '' ): string {
	$css = '';

	foreach ( $styles as $property => $value ) {
		if ( ! $value ) {
			continue;
		}

		$css .= "$property:$value;";
	}

	if ( $selector ) {
		return sprintf( "$selector{%s}", $css );
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


