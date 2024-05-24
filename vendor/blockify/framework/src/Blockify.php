<?php

declare( strict_types=1 );

use Blockify\Container\Container;
use Blockify\Framework\ServiceProvider;

if ( ! class_exists( 'Blockify' ) ) {

	/**
	 * Blockify singleton.
	 *
	 * @since 0.1.0
	 */
	class Blockify {

		/**
		 * Service provider instance.
		 *
		 * @var ?ServiceProvider
		 */
		private static ?ServiceProvider $provider = null;

		/**
		 * Registers container with service provider.
		 *
		 * @param string $file Main plugin or theme file.
		 *
		 * @return void
		 */
		public static function register( string $file ): void {
			if ( is_null( self::$provider ) ) {
				self::$provider = new ServiceProvider( $file );
				self::$provider->register( new Container() );
			}
		}
	}
}
