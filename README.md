# Blockify Plugin

Full site editing theme framework and block toolkit. Full documentation coming soon!

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

## Features

- **Block Supports API:** Easy to use PHP API for modifying core block supports. This allows for conditional block supports, or extra settings for core blocks.
- **Block Styles API:** Easy to use PHP API for modifying core block styles that usually require JS. Conditional registration supported - for example, only register a "Secondary" block style if a secondary color is set in the theme or editor.
- **Block Library:** Unbranded, fully customizable, commonly needed UI components. Configurable through theme.json.
- **Block Extensions**: Additional appearance controls for all blocks. For example, box shadows, absolute positioning.
- **Full Site Editing:** CSS framework. Extra page, post and template part settings.
- **Google Fonts:** Automatically downloads and locally serves selected editor fonts.
- **Text Formats:** Additional text formats including gradients, font size and more.
- **Responsive Settings:** Reverse on mobile, hide on mobile and more.
- **Dark Mode (Pro):** Automatically enables dark mode for any supported theme.

## Requirements

- WordPress ^6.0
- PHP ^7.4

## Theme Developers

Blockify was built for you! It should work out of the box with any standard FSE theme. To add extra supports, copy and paste the code snippet below into your parent theme, child theme or custom plugin to begin configuring your settings:

```php
// Filter Blockify config.
add_theme_support( 'blockify', [

	// Modify default block supports.
	'blockSupports' => [
		'core/paragraph' => [
			'alignWide' => true,
		],
	],

	// Block styles to be registered correctly with JS.
	'blockStyles'   => [
		'unregister' => [
			[
				'type' => 'core/separator',
				'name' => [ 'wide', 'dots' ],
			],
		],
		'register'   => [
			[
				'type'  => 'core/button',
				'name'  => 'secondary',
				'label' => __( 'Secondary', 'blockify' ),
			],
		],
	],

	// Colors to swap (requires pro).
	'darkMode'      => [
		'black' => 'white',
		'white' => 'black',
	],
] );
```

Alternatively, you can completely overwrite the defaults and start blank by using the `blockify` filter. For example:

```php
namespace Custom\Theme;

add_filter( 'blockify', __NAMESPACE__ . '\\blockify_config' );
/**
 * Customize Blockify config.
 *
 * @since 1.0.0
 *
 * @param array $defaults Default Blockify config.
 *                       
 * @return array Custom config.
 */
function blockify_config( array $defaults ) : array {
    return [
        ...$defaults,
        'blockSupports' => [
            'core/paragraph' => [
                'alignWide' => true,
            ],
        ],
    ];
}
```

## Contributing

All contributions and questions are welcome. Please feel free to submit Github issues.
