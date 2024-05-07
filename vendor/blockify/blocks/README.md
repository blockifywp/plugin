# Blockify Blocks

Composer package for creating custom WordPress blocks using only PHP.

## Installation

From your theme or plugin directory:

```bash
composer require blockify/blocks
```

## Usage

### PHP

Register service providers with the Blockify Container:

```php
namespace My\Namespace;

use Blockify\Blocks\Blocks;
use Blockify\Core\Container;
use Blockify\Core\Providers\Icons;
use function do_action;

do_action(
	'my/namespace/container',
	static function ( Container $container ): void {
		$container->set( Blocks::class );
		$container->set( Icons::class );
	}
);

```

Creating a standalone instance in your main plugin or theme file:

```php
namespace My\Namespace;

use Blockify\Blocks\Blocks;

new Blocks();
```

Registering blocks:

```php
add_filter('blockify_blocks', function ($blocks) {
	$blocks['my-block'] = [
		'title' => 'My Block',
		'description' => 'My block description',
		'category' => 'common',
		'icon' => 'admin-site',
		'keywords' => ['my', 'block'],
		'render_callback' => function ($attributes, $content) {
			return '<div class="my-block">' . $content . '</div>';
		},
		'attributes' => [
			'content' => [
				'type' => 'string',
				'default' => 'My block content',
			],
		],
	];

	return $blocks;
});
```

