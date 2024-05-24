<?php

declare( strict_types=1 );

namespace Blockify\Utilities;

use function add_action;
use function debug_backtrace;
use function defined;
use function json_encode;
use const SCRIPT_DEBUG;
use const WP_DEBUG;

/**
 * Class Debug.
 *
 * @since 1.0.0
 */
class Debug {

	/**
	 * Check if debug mode is enabled.
	 *
	 * @return bool
	 */
	public static function is_enabled(): bool {
		$wp_debug     = defined( 'WP_DEBUG' ) && WP_DEBUG;
		$script_debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

		return $wp_debug || $script_debug;
	}

	/**
	 * Log data to the console.
	 *
	 * @param mixed $data  Data to log.
	 * @param bool  $trace Whether to log the stacktrace.
	 *
	 * @return void
	 */
	public static function console_log( $data, bool $trace = false ): void {
		add_action( 'wp_footer', static fn() => static::render_log( $data, $trace ) );
		add_action( 'admin_footer', static fn() => static::render_log( $data, $trace ) );
	}

	/**
	 * Log data to the console.
	 *
	 * @return void
	 */
	public static function stacktrace(): array {
		$backtrace  = debug_backtrace();
		$stacktrace = [];

		foreach ( $backtrace as $index => $trace ) {
			if ( ! isset( $trace['file'] ) || ! isset( $trace['line'] ) ) {
				continue;
			}

			if ( 0 === $index ) {
				continue;
			}

			$stacktrace[] = $trace['file'] . ': ' . $trace['line'] . "\n";
		}

		return $stacktrace;
	}

	/**
	 * Render the log.
	 *
	 * @param mixed $data  Data to log.
	 * @param bool  $trace Whether to log the stacktrace.
	 *
	 * @return void
	 */
	private static function render_log( $data, bool $trace = true ): void {
		$stacktrace = self::stacktrace();

		echo '<script>';
		echo 'console.log(' . json_encode( $data ) . ');';

		if ( $trace && $stacktrace ) {
			foreach ( $stacktrace as $trace ) {
				echo 'console.log(' . json_encode( $trace ) . ');';
			}
		}

		echo '</script>';
	}

}
