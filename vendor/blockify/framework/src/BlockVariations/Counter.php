<?php

declare( strict_types=1 );

namespace Blockify\Framework\BlockVariations;

use Blockify\Framework\InlineAssets\Scriptable;
use Blockify\Framework\InlineAssets\Scripts;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function esc_attr;
use function esc_html;
use function trim;

/**
 * Counter block variation.
 *
 * @since 0.9.10
 */
class Counter implements Renderable, Scriptable {

	/**
	 * Render counter block markup.
	 *
	 * @since 0.9.10
	 *
	 * @param string   $block_content Block html content.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 * @hook  render_block_core/paragraph
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$counter = $block['attrs']['style']['counter'] ?? '';

		if ( ! $counter ) {
			return $block_content;
		}

		$dom = DOM::create( $block_content );
		$p   = DOM::get_element( 'p', $dom );

		if ( ! $p ) {
			return $block_content;
		}

		foreach ( $counter as $attribute => $value ) {
			$p->setAttribute( "data-$attribute", esc_attr( $value ) );
		}

		$p->textContent = esc_html( trim( $p->textContent ) );

		return $dom->saveHTML();
	}

	/**
	 * Conditionally add counter JS.
	 *
	 * @since 0.9.10
	 *
	 * @param Scripts $scripts The scripts instance.
	 *
	 * @return void
	 */
	public function scripts( Scripts $scripts ): void {
		$scripts->add_file( 'counter.js', [ 'is-style-counter' ] );
	}
}

