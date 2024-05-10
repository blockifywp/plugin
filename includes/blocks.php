<?php

declare( strict_types=1 );

namespace Blockify\Plugin;

use Blockify\Blocks\Blocks;
use function add_action;

add_action( 'plugins_loaded', __NAMESPACE__ . '\\register_blocks' );
/**
 * Register blocks.
 *
 * @since 1.0.0
 *
 * @return void
 */
function register_blocks(): void {
	Blocks::register( FILE );
}
