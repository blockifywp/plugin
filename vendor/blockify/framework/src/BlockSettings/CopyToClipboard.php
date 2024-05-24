<?php

declare( strict_types=1 );

namespace Blockify\Framework\BlockSettings;

use Blockify\Framework\InlineAssets\Scriptable;
use Blockify\Framework\InlineAssets\Scripts;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function esc_html__;
use function explode;
use function html_entity_decode;
use function implode;
use function is_admin;
use function trim;
use function wp_strip_all_tags;

/**
 * CopyToClipboard class.
 *
 * @since 1.0.0
 */
class CopyToClipboard implements Renderable, Scriptable {

	/**
	 * Renders the code block.
	 *
	 * @param string   $block_content The block content.
	 * @param array    $block         The block.
	 * @param WP_Block $instance      The block instance.
	 *
	 * @hook render_block_core/code
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		if ( is_admin() ) {
			return $block_content;
		}

		$enabled = $block['attrs']['copyToClipboard'] ?? true;

		if ( ! $enabled ) {
			return $block_content;
		}

		$show_line_numbers = $block['attrs']['showLineNumbers'] ?? false;
		$content           = trim( html_entity_decode( wp_strip_all_tags( $block['innerHTML'] ) ) );
		$label             = esc_html__( 'Copy to clipboard', 'blockify' );
		$copied            = esc_html__( 'Copied!', 'blockify' );
		$svg               = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" stroke="currentcolor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" fill="none">
  <path d="M12.9975 10.7499L11.7475 10.7499C10.6429 10.7499 9.74747 11.6453 9.74747 12.7499L9.74747 21.2499C9.74747 22.3544 10.6429 23.2499 11.7475 23.2499L20.2475 23.2499C21.352 23.2499 22.2475 22.3544 22.2475 21.2499L22.2475 12.7499C22.2475 11.6453 21.352 10.7499 20.2475 10.7499L18.9975 10.7499Z"></path>
  <path d="M17.9975 12.2499L13.9975 12.2499C13.4452 12.2499 12.9975 11.8022 12.9975 11.2499L12.9975 9.74988C12.9975 9.19759 13.4452 8.74988 13.9975 8.74988L17.9975 8.74988C18.5498 8.74988 18.9975 9.19759 18.9975 9.74988L18.9975 11.2499C18.9975 11.8022 18.5498 12.2499 17.9975 12.2499Z"></path>
  <path d="M13.7475 16.2499L18.2475 16.2499"></path>
  <path d="M13.7475 19.2499L18.2475 19.2499"></path>
</svg>
SVG;

		$copy_to_clipboard = "<div class='copy-to-clipboard'><span >$copied</span><button class='click-to-copy-button' title='$label'>$svg</button><textarea>$content</textarea></div>";
		$dom               = DOM::create( $block_content );
		$pre               = DOM::get_element( 'pre', $dom );
		$code              = DOM::get_element( 'code', $dom );
		$element           = $pre ?? $code ?? null;

		if ( $element && $show_line_numbers ) {
			$classes   = explode( ' ', $element->getAttribute( 'class' ) );
			$classes[] = 'show-line-numbers';

			$element->setAttribute( 'class', implode( ' ', $classes ) );
		}

		$block_content = $dom->saveHTML( $element );

		if ( ! $element ) {
			return $block_content;
		}

		$copy_dom = DOM::create( $copy_to_clipboard );
		$div      = DOM::get_element( 'div', $copy_dom );

		$imported = $element->ownerDocument->importNode( $div, true );

		$element->insertBefore( $imported, $element->firstChild );

		return $dom->saveHTML();
	}

	/**
	 * Add click to copy JS.
	 *
	 * @since 0.9.34
	 *
	 * @param Scripts $scripts Scripts instance.
	 *
	 * @return void
	 */
	public function scripts( Scripts $scripts ): void {
		$scripts->add_file( 'copy-to-clipboard.js', [ 'copy-to-clipboard' ] );
	}

}
