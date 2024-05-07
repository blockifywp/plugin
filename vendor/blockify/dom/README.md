# Blockify DOM

DOM manipulation library for PHP.

## Installation

```bash
composer require blockify/dom
```

## Usage

### PHP

First, require Composer's autoloader:

```php
require_once __DIR__ . '/vendor/autoload.php';
```

Then, use the library:

```php
use Blockify\Dom\DOM;

$html = '<h1>Hello, World!</h1>';

$dom = DOM::parse( $html );

$h1 = $dom->getElementsByTagName('h1')[0];

$h1->setAttribute('class', 'heading');

echo $dom->saveHTML();
```
