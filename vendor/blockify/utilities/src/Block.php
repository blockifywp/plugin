<?php

declare( strict_types=1 );

namespace Blockify\Utilities;

use Blockify\Dom\CSS;
use WP;
use function implode;
use function is_admin;
use function parse_blocks;
use function render_block;
use function serialize_block;
use function str_contains;
use function strip_core_block_namespace;

/**
 * Block utility.
 *
 * @since 1.0.0
 */
class Block {

	/**
	 * Recursively search blocks for a specific type.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $blocks The blocks.
	 * @param string $type   The block type.
	 *
	 * @return array
	 */
	public static function search_blocks( array $blocks, string $type ): array {
		$found = [];

		foreach ( $blocks as $block ) {
			if ( $block['blockName'] === $type ) {
				$found[] = $block;
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$found = array_merge( $found, self::search_blocks( $block['innerBlocks'], $type ) );
			}
		}

		return $found;
	}

	/**
	 * Determine if the current request is a block rendering request in the editor.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_rendering_preview(): bool {
		if ( is_admin() ) {
			return true;
		}

		if ( ! defined( 'REST_REQUEST' ) || ! is_user_logged_in() ) {
			return false;
		}

		global $wp;

		if ( ! $wp instanceof WP || empty( $wp->query_vars['rest_route'] ) ) {
			return false;
		}

		$route = $wp->query_vars['rest_route'];

		return str_contains( $route, '/block-renderer/' );
	}

	/**
	 * Get block HTML.
	 *
	 * @since 1.3.0
	 *
	 * @param array $block  Block.
	 * @param bool  $render Whether to render the block.
	 *
	 * @return string
	 */
	public static function get_html( array $block, bool $render = false ): string {
		$block['innerContent'] = $block['innerContent'] ?? [];
		$block['innerHTML']    = $block['innerHTML'] ?? '';
		$block['innerBlocks']  = $block['innerBlocks'] ?? [];
		$name                  = strip_core_block_namespace( $block['blockName'] ?? '' );

		if ( ! $name || empty( $block['innerBlocks'] ) ) {
			return serialize_block( $block );
		}

		$classes = array_filter( [
			'wp-block-' . $name,
			$block['attrs']['className'] ?? null,
			isset( $block['attrs']['fontSize'] ) ? 'has-' . $block['attrs']['fontSize'] . '-font-size' : null,
			isset( $block['attrs']['textColor'] ) ? 'has-' . $block['attrs']['textColor'] . '-color' : null,
			isset( $block['attrs']['backgroundColor'] ) ? 'has-' . $block['attrs']['backgroundColor'] . '-background-color' : null,
		] );

		$styles = array_filter( [
			'gap' => $block['attrs']['style']['spacing']['blockGap'] ?? null,
		] );

		$tag     = $block['tagName'] ?? $block['attrs']['tagName'] ?? 'div';
		$opening = sprintf(
			'<%s class="%s" style="%s">',
			$tag, implode( ' ', $classes ),
			CSS::array_to_string( $styles )
		);
		$closing = sprintf( '</%s>', $tag );

		$inner_content = $block['innerContent'];
		array_unshift( $inner_content, $opening );
		$inner_content[] = $closing;

		foreach ( $block['innerBlocks'] as $inner_block ) {
			$inner_content[] = static::get_html( $inner_block );
		}

		$block['innerContent'] = $inner_content;
		$block['innerHTML']    = implode( '', $inner_content );

		$serialized   = serialize_block( $block );
		$parsed_block = parse_blocks( $serialized )[0];

		return $render ? render_block( $parsed_block ) : $serialized;
	}
}
