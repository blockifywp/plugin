<?php

declare( strict_types=1 );

namespace Blockify\Blocks;

use Blockify\Container\Container;
use Blockify\Container\Interfaces\Registerable;
use Blockify\Hooks\Hook;
use Blockify\Utilities\Data;

/**
 * Blocks service provider.
 *
 * @since 1.0.0
 */
class BlocksServiceProvider implements Registerable {

	/**
	 * Blocks.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private array $blocks = [
		Breadcrumbs::class,
		Conditional::class,
		DarkModeToggle::class,
		GoogleMap::class,
		ImageCompare::class,
		Slider::class,
		Slide::class,
		TableOfContents::class,
		Tabs::class,
		Tab::class,
	];

	/**
	 * Plugin or theme data.
	 *
	 * @since 1.0.0
	 *
	 * @var Data
	 */
	private Data $data;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file Main plugin or theme file.
	 *
	 * @return void
	 */
	public function __construct( string $file ) {
		$this->data = Data::from( $file );
	}

	/**
	 * Register the service.
	 *
	 * @since 1.0.0
	 *
	 * @param Container $container The container.
	 *
	 * @return void
	 */
	public function register( Container $container ): void {
		foreach ( $this->blocks as $block ) {
			$service = $container->make( $block );

			$service->set_data( $this->data );
			$service->register();

			Hook::annotations( $service );
		}
	}
}
