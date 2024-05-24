<?php

declare( strict_types=1 );

namespace Blockify\Blocks;

use Blockify\Dom\DOM;
use Blockify\Utilities\Block;
use WP_Block;
use function dirname;
use function do_blocks;
use function esc_attr;
use function explode;
use function file_exists;
use function ob_get_clean;
use function ob_start;
use function str_replace;

/**
 * Dark mode toggle block.
 *
 * @since 1.0.0
 */
class DarkModeToggle extends AbstractBlock {

	/**
	 * Renders the dark mode toggle block.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block object.
	 *
	 * @return string
	 */
	public function render( array $attributes, string $content, WP_Block $block ): string {
		$dom              = DOM::create( $content ?: '<div>' );
		$div              = DOM::get_element( 'div', $dom );
		$div_classes      = explode( ' ', esc_attr( $attributes['className'] ?? '' ) );
		$block_style      = 'default';
		$pattern_dir      = dirname( __DIR__ ) . '/resources/patterns/';
		$default_pattern  = $pattern_dir . 'dark-mode-toggle-default.php';
		$dropdown_pattern = $pattern_dir . 'dark-mode-toggle-dropdown.php';
		$switch_pattern   = $pattern_dir . 'dark-mode-toggle-switch.php';
		$block_variations = [];

		if ( Block::is_rendering_preview() ) {
			$div->setAttribute( 'style', 'pointer-events:none' );
		}

		foreach ( $div_classes as $class ) {
			if ( in_array( $class, [ 'is-style-dropdown', 'is-style-switch' ] ) ) {
				$block_style = str_replace( 'is-style-', '', $class );
				break;
			}
		}

		if ( file_exists( $default_pattern ) ) {
			ob_start();
			include $default_pattern;

			$block_variations['default'] = ob_get_clean();
		}

		if ( file_exists( $dropdown_pattern ) && $block_style === 'dropdown' ) {
			ob_start();
			include $dropdown_pattern;

			$block_variations['dropdown'] = ob_get_clean();
		}

		if ( file_exists( $switch_pattern ) && $block_style === 'switch' ) {
			ob_start();
			include $switch_pattern;

			$block_variations['switch'] = ob_get_clean();
		}

		$pattern_blocks = do_blocks( $block_variations[ $block_style ] ?: $block_variations['default'] );
		$inner_dom      = DOM::create( "<div>$pattern_blocks</div>" );
		$inner_div      = DOM::get_element( 'div', $inner_dom );

		foreach ( $inner_div->childNodes as $child ) {
			$div->appendChild( $dom->importNode( $child, true ) );
		}

		return $dom->saveHTML();
	}

}

