<?php

declare( strict_types=1 );

namespace Blockify\Framework\DesignSystem;

use Blockify\Framework\InlineAssets\Styleable;
use Blockify\Framework\InlineAssets\Styles;
use Blockify\Utilities\Debug;
use function function_exists;

/**
 * Base CSS.
 *
 * @since 1.0.0
 */
class BaseCss implements Styleable {

	/**
	 * Registers styles.
	 *
	 * @since 1.0.0
	 *
	 * @param Styles $styles Styles.
	 *
	 * @return void
	 */
	public function styles( Styles $styles ): void {
		$style_groups = $this->get_stylesheets();

		foreach ( $style_groups as $group => $stylesheets ) {
			foreach ( $stylesheets as $name => $strings ) {
				$styles->add_file( $group . '/' . $name . '.css', $strings );
			}
		}
	}

	/**
	 * Adds conditional stylesheets inline.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_stylesheets(): array {
		$styles = [];

		$styles['elements'] = [
			'all'        => [],
			'anchor'     => [ '<a' ],
			'big'        => [ '<big' ],
			'blockquote' => [ '<blockquote' ],
			'body'       => [],
			'button'     => [
				'<button',
				'type="button"',
				'type="submit"',
				'type="reset"',
				'nf-form',
				'wp-element-button',
			],
			'caption'    => [ 'wp-element-caption' ],
			'checkbox'   => [ 'type="checkbox"' ],
			'cite'       => [ '<cite' ],
			'code'       => [ '<code' ],
			'hr'         => [ '<hr' ],
			'form'       => [
				'<fieldset',
				'<form',
				'<input',
				'nf-form',
				'wp-block-search',
			],
			'heading'    => [],
			'html'       => [],
			'list'       => [ '<ul', '<ol' ],
			'mark'       => [ '<mark' ],
			'pre'        => [ '<pre' ],
			'radio'      => [ 'type="radio"' ],
			'small'      => [ '<small' ],
			'strong'     => [ '<strong' ],
			'sub'        => [ '<sub' ],
			'sup'        => [ '<sup' ],
			'svg'        => [ '<svg' ],
			'table'      => [ '<table' ],
		];

		$styles['block-styles'] = [
			'badge'            => [ 'is-style-badge' ],
			'button-outline'   => [ 'is-style-outline' ],
			'button-secondary' => [ 'is-style-secondary' ],
			'button-ghost'     => [ 'is-style-ghost' ],
			'check-circle'     => [ 'is-style-check-circle' ],
			'check-outline'    => [ 'is-style-check-outline', 'is-style-checklist-circle' ],
			'checklist'        => [ 'is-style-checklist' ],
			'curved-text'      => [ 'is-style-curved-text' ],
			'divider-angle'    => [ 'is-style-angle' ],
			'divider-curve'    => [ 'is-style-curve' ],
			'divider-fade'     => [ 'is-style-fade' ],
			'divider-round'    => [ 'is-style-round' ],
			'divider-wave'     => [ 'is-style-wave' ],
			'heading'          => [ 'is-style-heading', 'is-style-summary-heading', 'is-style-list-heading' ],
			'list-dash'        => [ 'is-style-dash' ],
			'list-heading'     => [ 'is-style-heading' ],
			'list-none'        => [ 'is-style-none' ],
			'notice'           => [ 'is-style-notice' ],
			'numbered-list'    => [ 'is-style-numbered' ],
			'search-toggle'    => [ 'is-style-toggle' ],
			'square-list'      => [ 'is-style-square' ],
			'sub-heading'      => [ 'is-style-sub-heading' ],
			'surface'          => [ 'is-style-surface' ],
		];

		$styles['block-variations'] = [
			'accordion'  => [ 'is-style-accordion' ],
			'counter'    => [ 'is-style-counter' ],
			'icon'       => [ 'is-style-icon' ],
			'marquee'    => [ 'is-marquee' ],
			'newsletter' => [ 'is-style-newsletter' ],
			'svg'        => [ 'is-style-svg' ],
		];

		// Placeholder handled by service.
		$styles['block-extensions'] = [
			'animation'         => [ 'has-animation', 'will-animate', 'has-scroll-animation' ],
			'aspect-ratio'      => [ 'has-aspect-ratio-' ],
			'box-shadow'        => [ 'has-box-shadow' ],
			'copy-to-clipboard' => [ 'copy-to-clipboard' ],
			'filter'            => [ '--filter-hover' ],
			'gradient-mask'     => [ '-gradient-background' ],
			'inline-image'      => [ 'wp-image-' ],
			'on-click'          => [ 'onclick="' ],
			'shadow'            => [ 'has-shadow', 'has-box-shadow', 'has-text-shadow' ],
			'transform'         => [ 'has-transform' ],
		];

		// Admin bar handled by service.
		$styles['components'] = [
			'border'             => [
				'border-width:',
				'border-top-width:',
				'border-right-width:',
				'border-bottom-width:',
				'border-left-width:',
			],
			'edit-link'          => [ 'edit-link' ],
			'screen-reader-text' => [],
			'site-blocks'        => [],
			'splide'             => [ 'splide' ],
		];

		if ( Debug::is_enabled() && function_exists( 'xdebug_is_debugger_active' ) ) {
			$styles['components']['xdebug'] = [];
		}

		if ( Debug::is_enabled() && function_exists( 'd' ) && function_exists( 's' ) ) {
			$styles['components']['kint'] = [];
		}

		$styles['text-formats'] = [
			'animation'  => [ 'has-text-animation', 'typewriter' ],
			'arrow'      => [ 'is-underline-arrow' ],
			'brush'      => [ 'is-underline-brush' ],
			'circle'     => [ 'is-underline-circle' ],
			'scribble'   => [ 'is-underline-scribble' ],
			'gradient'   => [ 'has-text-gradient' ],
			'highlight'  => [ 'has-inline-color' ],
			'underline'  => [ 'has-text-underline' ],
			'font-size'  => [ 'has-inline-font-size' ],
			'inline-svg' => [ 'inline-svg' ],
			'outline'    => [ 'has-text-outline' ],
		];

		$styles['utilities'] = [
			'align'     => [ 'vertical-align-top' ],
			'dark-mode' => [
				'is-style-light',
				'is-style-dark',
				'hide-light-mode',
				'hide-dark-mode',
			],
			'fade'      => [
				'fade-top',
				'fade-right',
				'fade-bottom',
				'fade-left',
				'fade-horizontal',
				'fade-vertical',
			],
			'flex'      => [
				'flex',
				'justify-center',
				'justify-space-between',
				'align-content-center',
				'align-stretch',
			],
			'height'    => [ 'height-100', 'height-auto' ],
			'margin'    => [
				'margin-auto',
				'margin-top-auto',
				'margin-left-auto',
				'margin-right-auto',
				'margin-bottom-auto',
				'no-margin',
			],
			'wrap'      => [ 'nowrap', 'wrap' ],
		];

		return $styles;
	}
}
