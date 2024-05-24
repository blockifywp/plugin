<?php

declare( strict_types=1 );

namespace Blockify\Container\Interfaces;

interface Conditional {

	public static function condition(): bool;

}
