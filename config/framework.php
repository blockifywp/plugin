<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use Blockify;
use function add_action;

add_action( 'after_setup_theme', __NAMESPACE__ . '\\setup_framework' );
/**
 * Set up the framework.
 *
 * @return void
 */
function setup_framework(): void {
	Blockify::register( FILE );
}
