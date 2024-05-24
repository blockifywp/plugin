<?php

declare( strict_types=1 );

namespace Blockify\Framework\BlockVariations;

use Blockify\Framework\BlockSettings\Onclick;
use Blockify\Dom\CSS;
use Blockify\Dom\DOM;
use Blockify\Icons\Icon;
use Blockify\Framework\Interfaces\Renderable;
use Blockify\Utilities\Str;
use DOMDocument;
use DOMElement;
use WP_Block;
use function esc_attr;
use function explode;
use function implode;
use function rawurlencode;
use function str_contains;
use function str_replace;
use function trim;

/**
 * Svg class.
 *
 * @since 1.0.0
 */
class Svg implements Renderable {

	/**
	 * Onclick instance.
	 *
	 * @var Onclick
	 */
	private Onclick $onclick;

	/**
	 * Constructor.
	 *
	 * @param Onclick $onclick Onclick instance.
	 *
	 * @return void
	 */
	public function __construct( Onclick $onclick ) {
		$this->onclick = $onclick;
	}

	/**
	 * Render SVG block variation.
	 *
	 * @since 0.9.10
	 *
	 * @param string   $block_content Block html content.
	 * @param array    $block         Block data.
	 * @param WP_Block $instance      Block instance.
	 *
	 *
	 * @hook  render_block_core/image 9
	 *
	 * @return string
	 */
	public function render( string $block_content, array $block, WP_Block $instance ): string {
		$attrs      = $block['attrs'] ?? [];
		$svg_string = Icon::sanitize_svg( $attrs['style']['svgString'] ?? '' );

		if ( ! $svg_string ) {
			return $block_content;
		}

		if ( ! str_contains( $block_content, 'is-style-svg' ) ) {
			return $block_content;
		}

		$dom      = DOM::create( $block_content );
		$figure   = DOM::get_element( 'figure', $dom );
		$link     = DOM::get_element( 'a', $figure );
		$img      = DOM::get_element( 'img', $link ?? $figure );
		$svg      = DOM::get_element( 'svg', $link ?? $figure );
		$width    = esc_attr( $attrs['width'] ?? '' );
		$height   = esc_attr( $attrs['height'] ?? '' );
		$mask     = (bool) ( $attrs['style']['maskSvg'] ?? false );
		$on_click = $attrs['onclick'] ?? '';

		if ( $mask ) {
			//return $this->render_mask( $img, $svg_string, $dom, $width, $height );
		}

		if ( $on_click ) {
			( $link ?? $figure ?? $img )->setAttribute( 'onclick', $on_click );
			$block_content = $dom->saveHTML();
		}

		if ( $svg ) {
			return $block_content;
		}

		if ( $img ) {
			$img->parentNode->removeChild( $img );
		}

		$svg_dom     = DOM::create( $svg_string );
		$svg_element = DOM::get_element( 'svg', $svg_dom );

		if ( ! $svg_element ) {
			return $block_content;
		}

		$imported = $dom->importNode( $svg_element, true );
		$imported = DOM::node_to_element( $imported );

		if ( $width ) {
			$imported->setAttribute( 'width', $width );
		}

		if ( $height ) {
			$imported->setAttribute( 'height', $height );
		}

		( $link ?? $figure )->appendChild( $imported );

		if ( $link ) {
			$link->appendChild( $imported );
		} else {
			$figure->appendChild( $imported );
		}

		return $dom->saveHTML();
	}

	/**
	 * Renders masked SVG.
	 *
	 * @param DOMElement  $img        Image element.
	 * @param string      $svg_string SVG string.
	 * @param DOMDocument $dom        DOM document.
	 * @param string      $width      Image width.
	 * @param string      $height     Image height.
	 *
	 * @return string
	 */
	public function render_mask( DOMElement $img, string $svg_string, DOMDocument $dom, string $width, string $height ): string {
		$span    = DOM::change_tag_name( 'span', $img );
		$styles  = CSS::string_to_array( $span->getAttribute( 'style' ) );
		$encoded = rawurlencode(
			str_replace(
				'"',
				"'",
				trim( $svg_string )
			)
		);

		$styles['-webkit-mask-image'] = 'url("data:image/svg+xml;utf8,' . $encoded . '")';

		if ( $width ) {
			$unit = Str::contains_any( $width, 'px', 'em', 'rem', 'vh', 'vw', '%' ) ? '' : 'px';

			$styles['width'] = $width . $unit;

			$span->removeAttribute( 'width' );
		}

		if ( $height ) {
			$unit = Str::contains_any( $height, 'px', 'em', 'rem', 'vh', 'vw', '%' ) ? '' : 'px';

			$styles['height'] = $height . $unit;

			$span->removeAttribute( 'height' );
		}

		$alt = $img->getAttribute( 'alt' );

		if ( $alt ) {
			$span->setAttribute( 'aria-label', esc_attr( $alt ) );
			$span->removeAttribute( 'alt' );
		}

		$classes = explode( ' ', $span->getAttribute( 'class' ) );

		$classes[] = 'wp-block-image__svg';

		$span->setAttribute( 'class', implode( ' ', $classes ) );
		$span->setAttribute( 'role', 'img' );
		$span->removeAttribute( 'style' );
		$span->setAttribute( 'style', CSS::array_to_string( $styles ) );
		$span->removeAttribute( 'src' );

		return $dom->saveHTML();
	}

}
