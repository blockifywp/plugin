<?php

declare( strict_types=1 );

namespace Blockify\Container;

use Blockify\Container\Exceptions\ContainerException;
use Blockify\Utilities\Debug;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use function class_exists;
use function is_callable;
use function is_object;
use function uniqid;

/**
 * Simple auto-wiring dependency injection container.
 *
 * @since 0.1.0
 */
class Container implements ContainerInterface {

	/**
	 * Instances.
	 *
	 * @var array
	 */
	private array $instances = [];

	/**
	 * Error log.
	 *
	 * @var array
	 */
	private array $log = [];

	/**
	 * Retrieves an instance.
	 *
	 * @param string $id Abstract class name.
	 *
	 * @return mixed
	 */
	public function get( string $id ) {
		if ( ! $this->has( $id ) ) {
			$this->log( "Class {$id} not found in container." );
		}

		return $this->instances[ $id ];
	}

	/**
	 * Checks if instance exists.
	 *
	 * @param string $id Abstract class name.
	 *
	 * @return bool
	 */
	public function has( string $id ): bool {
		return isset( $this->instances[ $id ] );
	}

	/**
	 * Creates a new instance or returns an existing one.
	 *
	 * @param string $id   Abstract class name.
	 * @param mixed  $args Optional arguments.
	 *
	 * @return mixed
	 */
	public function make( string $id, ...$args ) {
		if ( ! $this->has( $id ) ) {
			$this->instances[ $id ] = null;
		}

		if ( is_object( $this->instances[ $id ] ) ) {
			return $this->instances[ $id ];
		}

		try {
			$reflector = new ReflectionClass( $id );
		} catch ( ReflectionException $e ) {
			$this->log( "Class {$id} does not exist.", $e );

			return null;
		}

		if ( ! $reflector->isInstantiable() ) {
			$this->log( "Class {$id} is not instantiable." );

			return null;
		}

		$condition = true;

		if ( $reflector->hasMethod( 'condition' ) ) {
			$method = $reflector->getMethod( 'condition' );

			if ( $method->isStatic() ) {
				try {
					$condition = $method->invoke( null );
				} catch ( ReflectionException $e ) {
					$this->log( "Cannot invoke condition method for {$id}.", $e );

					return null;
				}
			}
		}

		if ( ! $condition ) {
			return null;
		}

		if ( is_callable( $this->instances[ $id ] ) ) {
			return $this->instances[$id]();
		}

		$constructor = $reflector->getConstructor();

		try {
			if ( $args ) {
				$instance = $reflector->newInstanceArgs( $args );
			} elseif ( $constructor ) {
				$parameters   = $constructor->getParameters();
				$dependencies = $this->resolve_parameters( $parameters );
				$instance     = $reflector->newInstanceArgs( $dependencies );
			} else {
				$instance = $reflector->newInstance();
			}
		} catch ( ReflectionException | ContainerException $e ) {
			$this->log( "Cannot instantiate class {$id}.", $e );

			return null;
		}

		if ( ! is_object( $instance ) ) {
			$this->log( "Class {$id} is not an object." );

			return null;
		}

		$this->instances[ $id ] = $instance;

		return $instance;
	}

	/**
	 * Resolves dependencies for class constructor.
	 *
	 * @throws ContainerException If a dependency cannot be resolved.
	 *
	 * @param ReflectionParameter[] $parameters Constructor parameters.
	 *
	 * @return array
	 */
	private function resolve_parameters( array $parameters ): array {
		$dependencies = [];

		foreach ( $parameters as $parameter ) {
			if ( ! $parameter instanceof ReflectionParameter ) {
				continue;
			}

			$type = $parameter->getType();

			if ( ! $type || $type->isBuiltin() ) {
				if ( $parameter->isDefaultValueAvailable() ) {
					$dependencies[] = $parameter->getDefaultValue();
				} else {
					$type_name  = $type ? $type->getName() : 'mixed';
					$class_name = $parameter->getDeclaringClass()->name;

					// TODO: Add support for primitive types.
					throw new ContainerException( "Cannot auto-resolve primitive type: '{$type_name}' for {$class_name}." );
				}
			} else {
				$resolved = $this->make( $type->getName() );

				if ( $resolved ) {
					$dependencies[] = $resolved;
				}
			}
		}

		return $dependencies;
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param string $message   Message to log.
	 * @param ?mixed $exception Optional exception to log.
	 *
	 * @return void
	 */
	private function log( string $message, $exception = null ): void {
		$id               = uniqid( static::class );
		$this->log[ $id ] = [ $message, $exception ];

		if ( class_exists( '\\Blockify\\Utilities\\Debug' ) && Debug::is_enabled() ) {
			Debug::console_log( $this->log[ $id ] );
		}
	}
}
