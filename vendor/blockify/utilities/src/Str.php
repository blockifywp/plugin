<?php

declare( strict_types=1 );

namespace Blockify\Utilities;

use function _deprecated_function;
use function capital_P_dangit;
use function esc_html;
use function implode;
use function lcfirst;
use function ltrim;
use function preg_replace;
use function str_contains;
use function str_replace;
use function strlen;
use function strpos;
use function trim;
use function ucwords;
use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

/**
 * String utility.
 *
 * @since 1.0.0
 */
class Str {

	/**
	 * Checks if any of the given needles are in the haystack.
	 *
	 * @since 0.9.10
	 *
	 * @param string $haystack   The string to search.
	 * @param mixed  ...$needles The strings to search for.
	 *
	 * @return bool
	 */
	public static function contains_any( string $haystack, ...$needles ): bool {
		foreach ( $needles as $needle ) {
			if ( str_contains( $haystack, $needle ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if all given needles are in the haystack.
	 *
	 * @since 1.0.0
	 *
	 * @param string $haystack   The string to search.
	 * @param mixed  ...$needles The strings to search for.
	 *
	 * @return bool
	 */
	public static function contains_all( string $haystack, ...$needles ): bool {
		foreach ( $needles as $needle ) {
			if ( ! str_contains( $haystack, $needle ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Replaces multiple whitespace with single.
	 *
	 * @since 0.9.10
	 *
	 * @param string $string The string to search.
	 *
	 * @return string
	 */
	public static function reduce_whitespace( string $string ): string {
		return preg_replace( '/\s+/', ' ', $string );
	}

	/**
	 * Removes line breaks from a string.
	 *
	 * @since 1.0.1
	 *
	 * @param string $string The string to search.
	 *
	 * @return string
	 */
	public static function remove_line_breaks( string $string ): string {

		// Remove zero width spaces and other invisible characters.
		$string = preg_replace( '/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $string );

		// Replace line breaks.
		str_replace( [ "\r", "\n", PHP_EOL, ], '', $string
		);

		return trim( $string );
	}

	/**
	 * Returns parts of a string between two strings using regular expressions.
	 *
	 * @since 0.1.2
	 *
	 * @param string $start  Start string.
	 * @param string $end    End string.
	 * @param string $string String content.
	 * @param bool   $omit   Omit start and end strings.
	 * @param bool   $all    Return all occurrences.
	 *
	 * @return string|array
	 */
	public static function between( string $start, string $end, string $string, bool $omit = false, bool $all = false ) {
		$ds      = '/'; // Cannot use DIRECTORY_SEPARATOR because of Windows.
		$pattern = $ds . preg_quote( $start, $ds ) . '(.*?)' . preg_quote( $end, $ds ) . '/s';
		preg_match_all( $pattern, $string, $matches );

		$selected_matches = $omit ? $matches[1] : $matches[0];
		$first_match      = $selected_matches[0] ?? '';

		return $all ? $selected_matches : $first_match;
	}

	/**
	 * Removes non-alphanumeric characters from string.
	 *
	 * @since 1.0.0
	 *
	 * @param string $string String to sanitize.
	 *
	 * @return string
	 */
	public static function remove_non_alphanumeric( string $string ): string {
		return preg_replace( '/[^A-Za-z0-9\-]/', '', $string );
	}

	/**
	 * Replace first occurrence of a string.
	 *
	 * @since 1.2.9
	 *
	 * @param string $needle      The string to search for.
	 * @param string $replacement The string to replace with.
	 * @param string $haystack    The string to search.
	 *
	 * @return string
	 */
	public static function replace_first( string $needle, string $replacement, string $haystack ): string {
		if ( ! $needle || ! $haystack ) {
			return $haystack;
		}

		$position = strpos( $haystack, $needle );

		if ( $position !== false ) {
			$haystack = substr_replace( $haystack, $replacement, $position, strlen( $needle ) );
		}

		return $haystack;
	}

	/**
	 * Converts a string to title case.
	 *
	 * @param string   $string The string to convert.
	 * @param string[] $search Characters to replace (optional).
	 *
	 * @return string
	 */
	public static function title_case( string $string, array $search = [ '-', '_' ] ): string {
		$title_case = trim( ucwords( str_replace( $search, ' ', $string ) ) );

		return esc_html( capital_P_dangit( $title_case ) );
	}

	/**
	 * Converts a camelCase string to kebab case.
	 *
	 * @param string $string camelCase string to convert.
	 *
	 * @return string
	 */
	public static function camel_to_kebab( string $string ): string {
		$strings = preg_split( '/(?=[A-Z])/', lcfirst( $string ) );

		return strtolower( implode( '-', $strings ) );
	}

	/**
	 * Converts kebab-case string to camelCase.
	 *
	 * @deprecated Use `Str::to_camel_case` instead.
	 *
	 * @param string $string kebab-case string to convert.
	 *
	 * @return string
	 */
	public static function kebab_to_camel( string $string ): string {
		_deprecated_function(
			__METHOD__,
			'1.0.0',
			static::class . '::to_camel_case'
		);

		return static::to_camel_case( $string );
	}

	/**
	 * Converts a string to camelCase.
	 *
	 * @param string $string The string to convert.
	 *
	 * @return string
	 */
	public static function to_camel_case( string $string ): string {
		return lcfirst(
			str_replace(
				' ', '',
				ucwords(
					str_replace(
						[ '-', '_' ],
						' ',
						$string
					)
				)
			)
		);
	}

	/**
	 * Prepends a leading slash.
	 *
	 * Will remove leading forward and backslashes if it exists already before adding
	 * a leading forward slash. This prevents double slashing a string or path.
	 *
	 * The primary use of this is for paths and thus should be used for paths. It is
	 * not restricted to paths and offers no specific path support.
	 *
	 * @param string $string What to add the leading slash to.
	 *
	 * @return string String with leading slash added.
	 */
	public static function leadingslashit( string $string ): string {
		return DIRECTORY_SEPARATOR . self::unleadingslashit( $string );
	}

	/**
	 * Removes leading forward slashes and backslashes if they exist.
	 *
	 * The primary use of this is for paths and thus should be used for paths. It is
	 * not restricted to paths and offers no specific path support.
	 *
	 * @param string $string What to remove the leading slashes from.
	 *
	 * @return string String without the leading slashes.
	 */
	public static function unleadingslashit( string $string ): string {
		return ltrim( $string, '/\\' );
	}

}
