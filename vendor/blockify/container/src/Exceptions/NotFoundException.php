<?php

declare( strict_types=1 );

namespace Blockify\Container\Exceptions;

use Exception;
use Psr\Container\NotFoundExceptionInterface;
use function __;
use function sprintf;

/**
 * Dependency Not Found Exception.
 *
 * @since 0.1.0
 */
class NotFoundException extends Exception implements NotFoundExceptionInterface {

	/**
	 * DependencyNotFoundException constructor.
	 *
	 * @param string $id Identifier of the entry to look for.
	 *
	 * @return void
	 */
	public function __construct( string $id ) {

		/* translators: %s: Dependency ID */
		$message = __( '%s not found.', 'blockify' );

		parent::__construct( sprintf( $message, $id ) );
	}

}
