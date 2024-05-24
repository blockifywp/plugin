<?php

declare( strict_types=1 );

namespace Blockify\Utilities;

use function _wp_to_kebab_case;
use function is_array;
use function str_replace;
use function strtolower;

/**
 * JSON utility.
 *
 * @since 1.0.0
 */
class JSON {

	/**
	 * Given an array of settings, extracts the CSS Custom Properties
	 * for the custom values and adds them to the $declarations
	 * array following the format:
	 *
	 *     array(
	 *       'property_name' => 'property_value,
	 *     )
	 *
	 * This is slightly different from the implementation in
	 * wp-includes/class-wp-theme-json.php which is:
	 *
	 *     array(
	 *       'name'  => 'property_name',
	 *       'value' => 'property_value,
	 *     )
	 *
	 * @see   WP_Theme_JSON::compute_theme_vars()
	 *
	 * @since 5.8.0
	 *
	 * @param array $custom_values Settings to process.
	 *
	 * @return array The modified $declarations.
	 */
	public static function compute_theme_vars( array $custom_values ): array {
		$declarations = [];
		$css_vars     = self::flatten_tree( $custom_values );

		foreach ( $css_vars as $key => $value ) {
			$declarations[ '--wp--custom--' . $key ] = $value;
		}

		return $declarations;
	}

	/**
	 * Given a tree, it creates a flattened one
	 * by merging the keys and binding the leaf values
	 * to the new keys.
	 *
	 * It also transforms camelCase names into kebab-case
	 * and substitutes '/' by '-'.
	 *
	 * This is thought to be useful to generate
	 * CSS Custom Properties from a tree,
	 * although there's nothing in the implementation
	 * of this function that requires that format.
	 *
	 * For example, assuming the given prefix is '--wp'
	 * and the token is '--', for this input tree:
	 *
	 *     {
	 *       'some/property': 'value',
	 *       'nestedProperty': {
	 *         'sub-property': 'value'
	 *       }
	 *     }
	 *
	 * it'll return this output:
	 *
	 *     {
	 *       '--wp--some-property': 'value',
	 *       '--wp--nested-property--sub-property': 'value'
	 *     }
	 *
	 * @see   WP_Theme_JSON::flatten_tree()
	 *
	 * @since 5.8.0
	 *
	 * @param array  $tree   Input tree to process.
	 * @param string $prefix Optional. Prefix to prepend to each variable. Default
	 *                       empty string.
	 * @param string $token  Optional. Token to use between levels. Default '--'.
	 *
	 * @return array The flattened tree.
	 */
	public static function flatten_tree( array $tree, string $prefix = '', string $token = '--' ): array {
		$result = [];

		foreach ( $tree as $property => $value ) {
			$new_key = $prefix . str_replace(
					'/',
					'-',
					strtolower( _wp_to_kebab_case( $property ) )
				);

			if ( is_array( $value ) ) {
				$new_prefix        = $new_key . $token;
				$flattened_subtree = self::flatten_tree( $value, $new_prefix, $token );

				foreach ( $flattened_subtree as $subtree_key => $subtree_value ) {
					$result[ $subtree_key ] = $subtree_value;
				}

			} else {
				$result[ $new_key ] = $value;
			}
		}

		return $result;
	}
}
