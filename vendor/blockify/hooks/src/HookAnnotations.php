<?php

declare( strict_types=1 );

namespace Blockify\Hooks;

trait HookAnnotations {

	/**
	 * Adds hooks based on annotations.
	 *
	 * @return void
	 */
	public function hook_annotations(): void {
		Hook::annotations( $this );
	}
}
