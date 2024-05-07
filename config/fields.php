<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use Fieldify;
use function add_action;

add_action( 'plugins_loaded', __NAMESPACE__ . '\\register_fields' );
/**
 * Register fields.
 *
 * @since 1.0.0
 *
 * @return void
 */
function register_fields(): void {
	Fieldify::register( FILE );
}
