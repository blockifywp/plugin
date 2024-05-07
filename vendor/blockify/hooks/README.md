# Blockify Hooks

Hook methods by comment annotation in docblock.

Based on Hook Annotations by Viktor Szépe - [https://github.com/szepeviktor/SentencePress](https://github.com/szepeviktor/SentencePress)

## Installation

```bash
composer require blockify/hooks
```

## Usage

### PHP

First, require Composer's autoloader and then register the hooks with
WordPress:

```php
require_once __DIR__ . '/vendor/autoload.php';
```

Add hook tag and optional priority to method docblock:

```php
class MyPlugin
{
	/**
	 * Enqueue scripts.
	 * 
	 * @hook wp_enqueue_scripts 12
	 */
	public function enqueueScripts()
	{
		wp_enqueue_script('my-script', 'path/to/my-script.js', [], null, true);
	}
}
```

Then, call the `annotations` method on the class:

```php
use Blockify\Hooks\Hook;

$my_class = new MyPlugin();

Hook::annotations( $my_class );
```
