<?php

declare( strict_types=1 );

namespace Blockify\Utilities;

use function in_array;
use function is_array;
use function is_string;

/**
 * Class Arr
 *
 * @since 1.0.0
 */
class Arr {

	/**
	 * Check if any of the given values in needles exist in the haystack array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $haystack The array to search in.
	 * @param array $needles  The values to search for.
	 *
	 * @return bool
	 */
	public static function contains_any( array $haystack, array $needles ): bool {
		foreach ( $needles as $needle ) {
			if ( in_array( $needle, $haystack, true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Recursively converts all array keys to camel case.
	 *
	 * @since 1.0.0
	 *
	 * @param array $array The array to convert.
	 *
	 * @return array
	 */
	public static function keys_to_camel_case( array $array ): array {
		$converted = [];

		foreach ( $array as $key => $value ) {
			if ( is_string( $key ) ) {
				$key = Str::to_camel_case( $key );
			}

			$converted[ $key ] = is_array( $value ) ? static::keys_to_camel_case( $value ) : $value;
		}

		return $converted;
	}
}
