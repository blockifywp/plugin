<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use function str_replace;
use function strlen;
use function strpos;
use function substr;

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


