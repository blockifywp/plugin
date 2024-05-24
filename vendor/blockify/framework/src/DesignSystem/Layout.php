<?php

declare( strict_types=1 );

namespace Blockify\Framework\DesignSystem;

use Blockify\Framework\InlineAssets\Scriptable;
use Blockify\Framework\InlineAssets\Scripts;
use function array_merge;
use function is_admin;
use function str_replace;

/**
 * Layout.
 *
 * @since 0.4.2
 */
class Layout implements Scriptable {

	/**
	 * Changes layout size unit from vw to % in editor.
	 *
	 * @since 0.4.2
	 *
	 * @param mixed $theme_json WP_Theme_JSON_Data | WP_Theme_JSON_Data_Gutenberg.
	 *
	 * @hook  wp_theme_json_data_theme
	 *
	 * @return mixed
	 */
	public function fix_editor_layout_sizes( $theme_json ) {
		if ( is_admin() ) {
			return $theme_json;
		}


		$default      = $theme_json->get_data();
		$new          = [];
		$content_size = $default['settings']['layout']['contentSize'] ?? 'min(calc(100dvw - var(--wp--preset--spacing--xl,2rem)), 720px)';
		$wide_size    = $default['settings']['layout']['wideSize'] ?? 'min(calc(100dvw - var(--wp--preset--spacing--xl,2rem)), 1200px)';

		$new['settings']['layout']['contentSize'] = str_replace( '100%', '100dvw', $content_size );
		$new['settings']['layout']['wideSize']    = str_replace( '100%', '100dvw', $wide_size );

		$theme_json->update_with( array_merge(
			$default,
			$new
		) );

		return $theme_json;
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 1.6.0
	 */
	public function scripts( Scripts $scripts ): void {
		$scripts->add_file(
			'header-height.js',
			[ 'has-header-height' ]
		);
	}
}
