<?php

declare( strict_types=1 );

namespace Blockify\Framework\Integrations;

use Blockify\Container\Interfaces\Conditional;
use function class_exists;
use function file_exists;
use function get_stylesheet_directory;
use function get_template_directory;
use function is_bbpress;
use function locate_block_template;

/**
 * BbPress extension.
 *
 * @since 1.0.0
 */
class BbPress implements Conditional {

	/**
	 * Condition.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function condition(): bool {
		return class_exists( 'bbPress' );
	}

	/**
	 * Adds bbPress theme compatibility.
	 *
	 * @since 0.3.3
	 *
	 * @param string $template Template file.
	 *
	 * @hook  bbp_template_include_theme_compat
	 *
	 * @return string
	 */
	public function bbpress_template( string $template ): string {
		if ( ! is_bbpress() ) {
			return $template;
		}

		$child  = get_stylesheet_directory() . '/templates/page.html';
		$parent = get_template_directory() . '/templates/page.html';
		$file   = file_exists( $child ) ? $child : $parent;

		if ( file_exists( $file ) ) {
			$template = locate_block_template( $file, 'page', [] );
		}

		return $template;
	}
}

