<?php

declare( strict_types=1 );

namespace Blockify\Container;

class ContainerFactory {

	/**
	 * Creates a new container.
	 *
	 * @param string $id The container ID.
	 *
	 * @return Container
	 */
	public static function create( string $id ): Container {
		static $containers = [];

		if ( ! isset( $containers[ $id ] ) ) {
			$containers[ $id ] = new Container();
		}

		return $containers[ $id ];
	}

}
