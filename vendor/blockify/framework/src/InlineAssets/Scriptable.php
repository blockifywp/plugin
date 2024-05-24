<?php

declare( strict_types=1 );

namespace Blockify\Framework\InlineAssets;

/**
 * Scriptable interface.
 *
 * @since 1.0.0
 */
interface Scriptable {

	/**
	 * Register scripts.
	 *
	 * @since 1.0.0
	 *
	 * @param Scripts $scripts Inlinable service.
	 *
	 * @return void
	 */
	public function scripts( Scripts $scripts ): void;

}
