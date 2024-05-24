<?php

declare( strict_types=1 );

namespace Blockify\Framework\Integrations;

use Blockify\Container\Interfaces\Conditional;
use function class_exists;

/**
 * AffiliateWP extension.
 *
 * @since 1.0.0
 */
class AffiliateWP implements Conditional {

	/**
	 * Condition.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function condition(): bool {
		return class_exists( '\\Affiliate_WP' );
	}

	/**
	 * Hooks.
	 *
	 * @since 1.0.0
	 *
	 * @hook  affwp_enqueue_style_affwp-forms
	 *
	 * @return bool
	 */
	public function remove_styles(): bool {
		return false;
	}

}
