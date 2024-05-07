# Blockify Container

Simple PHP dependency injection container with autowiring for WordPress plugins and themes.

## Installation

```bash
composer require blockify/container
```

## Usage

### PHP

First, require Composer's autoloader and then register the container:

```php
require_once __DIR__ . '/vendor/autoload.php';

namespace MyNamespace;

use Blockify\Container\Container;
use Blockify\Container\Registerable;

$container = new Blockify\Container\Container();

$service_providers = [
	MyServiceProvider::class      => [ __FILE__ ],
	AnotherServiceProvider::class => [],
];

foreach ( $service_providers as $service_provider => $args ) {
	$instance = $container->make( $service_provider, ...$args );

	if ( $instance instanceof Registerable ) {
		$instance->register();
	}
}
```

Dependencies will be automatically resolved and injected into the constructor:

```php
namespace MyNamespace;

class MyServiceProvider {

	private string $file;

	public function __construct( string $file ) {
		$this->dependency = $dependency;
	}
	
	public function getFile(): string {
		return $this->file;
	}
}

class AnotherServiceProvider implements Registerable {

	private MyServiceProvider $my_service_provider;

	public function __construct( MyServiceProvider $my_service_provider ) {
		$this->my_service_provider = $my_service_provider;
	}
	
	public function register() {
		echo $this->my_service_provider->getFile();
	}
}
```


