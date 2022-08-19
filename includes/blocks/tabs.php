<?php

namespace Blockify\Plugin;

use DOMElement;
use DomXPath;
use function add_filter;

add_filter( 'render_block', NS . 'render_tabs_block', 10, 2 );
/**
 * Modifies front end HTML output of block.
 *
 * @since 0.0.2
 *
 * @param string $content
 * @param array  $block
 *
 * @return string
 */
function render_tabs_block( string $content, array $block ): string {

	if ( 'blockify/tabs' === $block['blockName'] ) {
		$dom          = dom( $content );
		$finder       = new DomXPath( $dom );
		$tab_contents = $finder->query( "//*[contains(concat(' ', normalize-space(@class), ' '), 'blockify-tab-content')]" );
		$tab_titles   = $finder->query( "//*[contains(concat(' ', normalize-space(@class), ' '), 'blockify-tab-title')]" );
		$nav          = $dom->createElement( 'div' );
		$nav->setAttribute( 'class', 'blockify-tabs-nav' );

		/**
		 * @var $container DOMElement
		 */
		$container = $dom->getElementsByTagName( 'div' )->item( 0 );
		$styles    = css_rules_to_array( $container->getAttribute( 'style' ) );

		unset( $styles['padding'] );
		unset( $styles['padding-top'] );
		unset( $styles['padding-right'] );
		unset( $styles['padding-bottom'] );
		unset( $styles['padding-left'] );

		//$container->setAttribute( 'style', css_rules_to_string( $styles ) );

		foreach ( $tab_contents as $tab_content ) {
			/**
			 * @var $tab DOMElement
			 */
			$tab = $tab_content->parentNode;

			foreach ( $tab_content->childNodes as $child ) {
				$tab->insertBefore( $child->cloneNode( true ), $tab_content );
			}

			$tab->removeChild( $tab_content );
			$tab->setAttribute( 'class', $tab->getAttribute( 'class' ) . ' blockify-tab-content' );
		}

		/**
		 * @var DOMElement $tabTitle
		 */
		foreach ( $tab_titles as $tabTitle ) {
			$nav->appendChild( $tabTitle );
		}

		if ( $container ) {
			$container->insertBefore( $nav, $container->firstChild );
		}

		$content = $dom->saveHTML();
	}

	return $content;
}

