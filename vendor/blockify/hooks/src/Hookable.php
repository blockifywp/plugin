<?php

declare( strict_types=1 );

namespace Blockify\Hooks;

interface Hookable {

	/**
	 * Adds hooks based on annotations.
	 *
	 * @return void
	 */
	public function hook_annotations(): void;

}
