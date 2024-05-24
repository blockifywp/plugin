<?php

declare( strict_types=1 );

namespace Blockify\Blocks;

use Blockify\Container\Container;

/**
 * Blocks singleton.
 *
 * @since 1.0.0
 */
class Blocks {

	/**
	 * Service provider instance.
	 *
	 * @var ?BlocksServiceProvider
	 */
	private static ?BlocksServiceProvider $provider = null;

	/**
	 * Registers container with service provider.
	 *
	 * @param string $file Main plugin or theme file.
	 *
	 * @return void
	 */
	public static function register( string $file ): void {
		if ( is_null( self::$provider ) ) {
			self::$provider = new BlocksServiceProvider( $file );
			self::$provider->register( new Container() );
		}
	}

}
