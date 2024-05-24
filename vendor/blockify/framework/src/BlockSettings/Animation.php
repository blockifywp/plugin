<?php

declare( strict_types=1 );

namespace Blockify\Framework\BlockSettings;

use Blockify\Framework\InlineAssets\Scriptable;
use Blockify\Framework\InlineAssets\Scripts;
use Blockify\Framework\InlineAssets\Styleable;
use Blockify\Framework\InlineAssets\Styles;
use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Framework\Interfaces\Renderable;
use WP_Block;
use function array_diff;
use function array_keys;
use function array_unique;
use function esc_attr;
use function explode;
use function file_exists;
use function file_get_contents;
use function is_admin;
use function str_contains;

/**
 * Animation class.
 *
 * @since 1.0.0
 */
class Animation implements Renderable, Styleable, Scriptable {

	/**
	 * CSS dir.
	 *
	 * @since 1.0.0
	 */
	private string $css_dir;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Styles $styles ) {
		$this->css_dir = $styles->dir;
	}

	/**
	 * Adds animation attributes to block.
	 *
	 * @since 0.9.10
	 *
	 * @param string   $block_content The block content.
	 * @param array    $block         The block.
	 * @param WP_Block $instance      The block instance.
	 *
	 * @hook  render_block
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$attrs     = $block['attrs'] ?? [];
		$animation = $attrs['animation'] ?? [];

		if ( empty( $animation ) ) {
			return $block_content;
		}

		$infinite = ( $animation['iterationCount'] ?? null ) === '-1' || ( $animation['event'] ?? null ) === 'infinite';

		$dom   = DOM::create( $block_content );
		$first = DOM::get_element( '*', $dom );

		if ( ! $first ) {
			return $block_content;
		}

		$classes = explode( ' ', $first->getAttribute( 'class' ) );
		$classes = array_unique( $classes );
		$styles  = CSS::string_to_array( $first->getAttribute( 'style' ) );

		unset( $styles['animation-play-state'] );

		if ( $infinite ) {
			unset( $styles['--animation-event'] );

			$styles['animation-iteration-count'] = 'infinite';

		} else {
			unset( $styles['animation-name'] );

			$styles['--animation-name'] = esc_attr( $animation['name'] ?? '' );
		}

		$event = $animation['event'] ?? '';

		if ( $event === 'scroll' ) {
			$classes[] = 'animate';
			$classes[] = 'has-scroll-animation';

			$classes = array_diff( $classes, [ 'has-animation' ] );

			$styles['animation-delay']      = 'calc(var(--scroll) * -1s)';
			$styles['animation-play-state'] = 'paused';
			$styles['animation-duration']   = '1s';
			$styles['animation-fill-mode']  = 'both';

			unset( $styles['--animation-event'] );

			$offset = $animation['offset'] ?? '0';

			if ( $offset === '0' ) {
				$offset = '0.01';
			}

			if ( $offset ) {
				$first->setAttribute( 'data-offset', esc_attr( $offset ) );
			}
		}

		if ( $styles ) {
			$first->setAttribute( 'style', CSS::array_to_string( $styles ) );
		}

		$first->setAttribute( 'class', implode( ' ', $classes ) );

		return $dom->saveHTML();
	}

	/**
	 * Adds animation styles.
	 *
	 * @since 1.0.0
	 *
	 * @param Styles $styles Inlinable service.
	 *
	 * @return void
	 */
	public function styles( Styles $styles ): void {
		$styles->add_callback( [ $this, 'get_animation_styles' ] );
	}

	/**
	 * Adds animation scripts.
	 *
	 * @since 1.0.0
	 *
	 * @param Scripts $scripts Inlinable service.
	 *
	 * @return void
	 */
	public function scripts( Scripts $scripts ): void {
		$scripts->add_data(
			'animations',
			array_keys( $this->get_animations() ),
			[],
			is_admin()
		);

		$scripts->add_file(
			'animation.js',
			[ 'has-animation', 'has-scroll-animation' ]
		);

		$scripts->add_file(
			'scroll.js',
			[ 'animation-event:scroll', 'has-scroll-animation' ]
		);

		$scripts->add_file(
			'packery.js',
			[ 'packery' ]
		);

		$scripts->add_file(
			'typewriter.js',
			[ 'has-typewriter-animation' ]
		);
	}

	/**
	 * Returns inline styles for animations.
	 *
	 * @since 0.9.19
	 *
	 * @param string $template_html The template HTML.
	 * @param bool   $load_all      Whether to load all styles.
	 *
	 * @return string
	 */
	public function get_animation_styles( string $template_html, bool $load_all ): string {
		$animations = $this->get_animations();
		$css        = '';

		foreach ( $animations as $name => $animation ) {
			if ( $load_all || str_contains( $template_html, "animation-name:{$name}" ) ) {
				$css .= "@keyframes $name" . trim( $animation );
			}
		}

		return $css;
	}

	/**
	 * Gets animations from stylesheet.
	 *
	 * @since 0.9.18
	 *
	 * @return array
	 */
	private function get_animations(): array {
		$file = $this->css_dir . 'block-extensions/animations.css';

		if ( ! file_exists( $file ) ) {
			return [];
		}

		$parts      = explode( '@keyframes', file_get_contents( $file ) );
		$animations = [];

		unset( $parts[0] );

		foreach ( $parts as $animation ) {
			$name = trim( explode( '{', $animation )[0] ?? '' );

			$animations[ $name ] = trim( str_replace( $name, '', $animation ) );
		}

		return $animations;
	}
}
