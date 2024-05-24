<?php

declare( strict_types=1 );

namespace Blockify\Framework\Integrations;

use Blockify\Container\Interfaces\Conditional;
use Blockify\Framework\InlineAssets\Styleable;
use Blockify\Framework\InlineAssets\Styles;
use function defined;
use function wp_get_global_settings;

/**
 * Syntax Highlighting Code Block extension.
 *
 * @since 1.0.0
 */
class SyntaxHighlightingCodeBlock implements Conditional, Styleable {

	/**
	 * Condition.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function condition(): bool {
		return defined( '\\Syntax_Highlighting_Code_Block\\PLUGIN_VERSION' );
	}

	/**
	 * Set syntax highlighting colors defined in theme.json.
	 *
	 * @since 1.0.0
	 *
	 * @param string $theme The theme to use.
	 *
	 * @hook  syntax_highlighting_code_block_style
	 *
	 * @return string
	 */
	public function set_syntax_highlighting_code_theme( string $theme ): string {
		$global_settings = wp_get_global_settings();

		return $global_settings['custom']['highlightJs'] ?? 'atom-one-dark';
	}

	/**
	 * Register styles.
	 *
	 * @since 1.0.0
	 *
	 * @param Styles $styles The styles instance.
	 *
	 * @return void
	 */
	public function styles( Styles $styles ): void {
		$styles->add_file(
			'plugins/syntax-highlighting-code-block.css',
			[ 'wp-block-code' ],
			static::condition()
		);
	}

}
