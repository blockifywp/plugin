# Blockify Plugin

Lightweight block library for full site editing themes.

Visit [https://wordpress.org/plugins/blockify](https://wordpress.org/plugins/blockify) for more information.

## Installation

This plugin will work with any FSE theme, but a free starter theme is also available for download from the WordPress.org repository.

### Standard

1. (Optionally) download and install the Blockify starter theme from WordPress.org
2. Download and install Blockify plugin from WordPress.org
3. Navigate to Appearance > Editor to begin editing

### Developers

Blockify can be installed in a number of ways with Composer:

- As a standard WordPress plugin
- As a Composer package
- Separate Composer packages
- Separate NPM packages

By separating packages, you can install only the functionality you need on a per project basis.

#### Composer

Blockify can be installed as a Composer package in any theme, child theme or plugin. It is automatically loaded and ready to use. More documentation for how to use Composer coming soon.
  
Composer details can be found in the `composer.json` file of this plugin.

#### NPM

Blockify provides a set of node packages (including the entire plugin) which allows installation via npm. Install only the parts you need, or the entire plugin. 

All npm details can be found in the `package.json` file of this plugin. 

## Requirements

- WordPress ^6.0
- PHP ^7.4

## Developers

To enable/disable blocks, use the `blockify_block_types` filter:

```php
namespace Custom\Theme;

add_filter( 'blockify_block_types', __NAMESPACE__ . '\\custom_block_types' );
/**
 * Customize Blockify config.
 *
 * @since 1.0.0
 *
 * @param array $defaults Default Blockify config.
 *                       
 * @return array Custom config.
 */
function custom_block_types( array $defaults ) : array {
    return [
        'accordion',
        'icon',
    ];
}
```

## Contributing

All contributions and questions are welcome. Please feel free to submit Github issues.
