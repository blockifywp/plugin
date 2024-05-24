<?php

declare( strict_types=1 );

namespace Blockify\Container\Exceptions;

use Exception;
use Psr\Container\ContainerExceptionInterface;

/**
 * Container Exception.
 *
 * @since 0.1.0
 */
class ContainerException extends Exception implements ContainerExceptionInterface {

	/**
	 * ContainerException constructor.
	 *
	 * @param string     $message  Error message.
	 * @param ?int       $code     Optional. Error code. Defaults to 0.
	 * @param ?Exception $previous Optional. Previous exception used for the exception chaining. Defaults to null.
	 *
	 * @return void
	 */
	public function __construct( string $message, ?int $code = 0, ?Exception $previous = null ) {
		parent::__construct( $message, $code, $previous );
	}
}
