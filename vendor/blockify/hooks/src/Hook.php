<?php

declare( strict_types=1 );

namespace Blockify\Hooks;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use function add_filter;
use function explode;
use function is_string;
use function preg_match_all;
use function str_replace;
use function trim;

/**
 * Hook class.
 *
 * Based on Hook Annotations by Viktor Szépe.
 *
 * @link https://github.com/szepeviktor/SentencePress
 */
class Hook {

	/**
	 * Hook methods based on annotation.
	 *
	 * @param object|string $object_or_class Object or class name.
	 *
	 * @return void
	 */
	public static function annotations( $object_or_class ): void {
		try {
			$reflection = new ReflectionClass( $object_or_class );
		} catch ( ReflectionException $e ) {
			return;
		}

		$public_methods = $reflection->getMethods( ReflectionMethod::IS_PUBLIC );

		foreach ( $public_methods as $method ) {

			// Do not hook constructors.
			if ( $method->isConstructor() ) {
				continue;
			}

			// Do not hook non-static methods for non-object classes.
			if ( is_string( $object_or_class ) && $method->isStatic() ) {
				continue;
			}

			$annotations = self::get_annotations( (string) $method->getDocComment() );

			if ( ! $annotations ) {
				continue;
			}

			foreach ( $annotations as $annotation ) {
				add_filter(
					$annotation['tag'],
					[ $object_or_class, $method->name ],
					$annotation['priority'],
					$method->getNumberOfParameters()
				);
			}
		}
	}

	/**
	 * Read hook tag from docblock.
	 *
	 * @param string $doc_block Method doc block.
	 *
	 * @return ?array
	 */
	private static function get_annotations( string $doc_block ): ?array {
		$pattern = '/@hook\s+([^\s]+)(\s+[0-9]+)?/';

		preg_match_all( $pattern, $doc_block, $matches );

		if ( ! isset( $matches[0] ) ) {
			return null;
		}

		$annotations = [];

		foreach ( $matches[0] as $annotation ) {
			$annotation = str_replace( '@hook', '', $annotation );
			$parts      = explode( ' ', trim( $annotation ) );
			$tag        = trim( $parts[0] ?? '' );

			$annotations[] = [
				'tag'      => $tag,
				'priority' => $parts[1] ?? 10,
			];
		}

		return $annotations;
	}

}
