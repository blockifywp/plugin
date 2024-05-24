<?php

declare( strict_types=1 );

namespace Blockify\Framework\Integrations;

use Blockify\Container\Interfaces\Conditional;
use function add_filter;
use function add_theme_support;
use function class_exists;

/**
 * LifterLMS extension.
 *
 * @since 1.0.0
 */
class LifterLMS implements Conditional {

	/**
	 * Condition.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function condition(): bool {
		return class_exists( '\\LifterLMS' );
	}

	/**
	 * Adds theme support for LifterLMS course and lesson sidebars.
	 *
	 * @since 1.0.0
	 *
	 * @hook  after_setup_theme
	 *
	 * @return void
	 */
	public function add_lifterlms_support(): void {
		if ( class_exists( '\\LifterLMS' ) ) {
			add_theme_support( 'lifterlms-sidebars' );
			add_filter( 'llms_get_theme_default_sidebar', static fn() => null );
		}
	}
}
