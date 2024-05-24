<?php

declare( strict_types=1 );

use Blockify\Container\ContainerFactory;
use Blockify\Hooks\Hook;
use Fieldify\Fields\Assets;
use Fieldify\Fields\Blocks;
use Fieldify\Fields\Config;
use Fieldify\Fields\MetaBoxes;
use Fieldify\Fields\PostTypes;
use Fieldify\Fields\Settings;
use Fieldify\Fields\Taxonomies;
use Fieldify\Fields\UserInterface;

/**
 * Fieldify facade.
 *
 * @since 1.0.0
 */
final class Fieldify {

	/**
	 * Services.
	 *
	 * @var array
	 */
	private const SERVICES = [
		Assets::class,
		Blocks::class,
		MetaBoxes::class,
		PostTypes::class,
		Settings::class,
		Taxonomies::class,
		UserInterface::class,
	];

	/**
	 * Registers instance.
	 *
	 * @param string $file Main plugin or theme file.
	 *
	 * @return void
	 */
	public static function register( string $file ): void {
		static $container = null;

		if ( ! is_null( $container ) || ! file_exists( $file ) ) {
			return;
		}

		$container = ContainerFactory::create( self::class );

		$container->make( Config::class, $file );

		foreach ( self::SERVICES as $id ) {
			Hook::annotations( $container->make( $id ) );
		}
	}
}
