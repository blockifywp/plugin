<?php

declare( strict_types=1 );

namespace Blockify\Plugin\Bin;

use function dirname;
use function file_put_contents;

$pattern_dir = dirname( __DIR__, 3 ) . '/cache/blockify/patterns';

if ( ! is_dir( $pattern_dir ) ) {
	echo 'Directory not found: ' . $pattern_dir;

	return;
}

$dirs = glob( $pattern_dir . '/*', GLOB_ONLYDIR );

if ( empty( $dirs ) ) {
	echo 'No directories found in: ' . $pattern_dir;

	return;
}

$patterns = [];

foreach ( $dirs as $dir ) {
	$theme              = basename( $dir );
	$patterns[ $theme ] = [];
	$categories         = glob( $dir . '/*', GLOB_ONLYDIR );

	foreach ( $categories as $category_dir ) {
		$category = basename( $category_dir );
		$files    = glob( $category_dir . '/*.php' );

		foreach ( $files as $file ) {
			$name = basename( $file, '.php' );

			$patterns[ $theme ][] = $category . '-' . $name;
		}
	}
}

file_put_contents(
	dirname( __DIR__ ) . '/patterns.json',
	json_encode( $patterns, JSON_PRETTY_PRINT )
);
