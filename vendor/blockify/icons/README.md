# Blockify Icons

Blockify icons library.

## Installation

```bash
composer require blockify/icons
```

## Usage

### PHP

First, require Composer's autoloader:

```php
require_once __DIR__ . '/vendor/autoload.php';
```

Then, use the icons:

```php
use Blockify\Icons\Icon;

$icon = Icon::get_svg( 'wordpress', 'star' );
```
