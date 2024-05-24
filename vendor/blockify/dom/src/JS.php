<?php

declare( strict_types=1 );

namespace Blockify\Dom;

use function apply_filters;
use function preg_replace;
use function rtrim;
use function str_replace;
use function trim;

/**
 * JS Utility.
 *
 * @since 1.0.0
 */
class JS {

    /**
     * Formats inline JS.
     *
     * @since 1.0.0
     *
     * @param string $js JS.
     *
     * @return string
     */
    public static function format_inline_js( string $js ): string {

        // Correct double quotes to single quotes.
        $js = str_replace( '"', "'", $js );

        // Trim trailing semicolon.
        $js = trim( rtrim( $js, ';' ) );

        // Remove whitespace.
        $js = preg_replace( '/\s+/', ' ', $js );

        // Remove zero width spaces and other invisible characters.
        $js = preg_replace( '/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $js );

        // Replace line breaks.
        $js = str_replace( [ "\r", "\n", PHP_EOL, ], '', $js
        );

        /**
         * Allows additional minification of inline JS. (Eg JShrink).
         *
         * @var string $js Formatted JS.
         */
        $js = apply_filters( 'blockify_format_inline_js', $js );

        return $js;
    }
}
