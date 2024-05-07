# Blockify Framework

This package contains the Blockify block theme framework for WordPress. It is a
collection of PHP, JS, and CSS files that extend the block editor.

## Installation

```bash
composer require blockify/framework
```

If you only need the JS and CSS files, this package can also be installed with
NPM:

```bash
npm install @blockifywp/framework
```

## Usage

### PHP

First, require Composer's autoloader and then register the framework with
WordPress:

```php
require_once __DIR__ . '/vendor/autoload.php';

Blockify::register( __FILE__ );
```
