<?php

declare( strict_types=1 );

namespace Blockify\Framework\InlineAssets;

/**
 * Styleable interface.
 *
 * @since 1.0.0
 */
interface Styleable {

	/**
	 * Register styles.
	 *
	 * @since 1.0.0
	 *
	 * @param Styles $styles Inlinable service.
	 *
	 * @return void
	 */
	public function styles( Styles $styles ): void;

}
