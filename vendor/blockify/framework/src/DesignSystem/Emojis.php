<?php

declare( strict_types=1 );

namespace Blockify\Framework\DesignSystem;

/**
 * Emojis extension.
 *
 * @since 0.9.10
 */
class Emojis {

	/**
	 * Adds editor only styles.
	 *
	 * @since 0.9.10
	 *
	 * @hook  init
	 *
	 * @return void
	 */
	public function remove_emoji_script(): void {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
	}

}
