<?php

declare( strict_types=1 );

namespace Blockify\Framework\Integrations;

use Blockify\Container\Interfaces\Conditional;
use Blockify\Framework\InlineAssets\Styleable;
use Blockify\Framework\InlineAssets\Styles;

/**
 * GravityForms integration.
 *
 * @since 0.0.1
 */
class GravityForms implements Conditional, Styleable {

	/**
	 * Condition.
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	public static function condition(): bool {
		return class_exists( 'GFForms' );
	}

	/**
	 * Register styles.
	 *
	 * @since 0.0.1
	 *
	 * @param Styles $styles Styles instance.
	 *
	 * @return void
	 */
	public function styles( Styles $styles ): void {
		$styles->add_file(
			'plugins/gravity-forms.css',
			[
				'gform-body',
			]
		);
	}

	/**
	 * Remove the default Gravity Forms styles.
	 *
	 * @since 1.0.0
	 *
	 * @hook  init
	 *
	 * @return void
	 */
	public function remove_default_styles() {
		//add_filter( 'gform_disable_form_theme_css', '__return_true' );
	}
}
