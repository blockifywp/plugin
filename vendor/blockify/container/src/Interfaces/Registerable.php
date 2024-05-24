<?php

declare( strict_types=1 );

namespace Blockify\Container\Interfaces;

use Blockify\Container\Container;

interface Registerable {

	public function register( Container $container ): void;

}
