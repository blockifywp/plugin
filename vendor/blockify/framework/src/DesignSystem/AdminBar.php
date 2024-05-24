<?php

declare( strict_types=1 );

namespace Blockify\Framework\DesignSystem;

use Blockify\Framework\InlineAssets\Styleable;
use Blockify\Framework\InlineAssets\Styles;
use function is_admin_bar_showing;

/**
 * Admin bar.
 *
 * @since 1.0.0
 */
class AdminBar implements Styleable {

	/**
	 * Registers service with access to provider.
	 *
	 * @since 1.0.0
	 *
	 * @param Styles $styles Styles service.
	 *
	 * @return void
	 */
	public function styles( Styles $styles ): void {
		$styles->add_file(
			'components/admin-bar.css',
			[],
			is_admin_bar_showing()
		);
	}

	/**
	 * Removes the default callback for the admin bar.
	 *
	 * @since 1.0.0
	 *
	 * @hook  after_setup_theme
	 *
	 * @return void
	 */
	public function remove_default_callback() {
		add_theme_support( 'admin-bar', [
			'callback' => '__return_false',
		] );
	}
}
