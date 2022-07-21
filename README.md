# Blockify Plugin

Full site editing theme framework and block toolkit. Full documentation coming soon!

Visit [https://wordpress.org/plugins/blockify](https://wordpress.org/plugins/blockify) for more information.

## Installation

1. Download and install Blockify theme from WordPress.org
2. Download and install Blockify plugin from WordPress.org
3. Navigate to Appearance > Editor to begin editing

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

Blockify was built for you! It should work out of the box with any standard FSE theme. To get started, add theme support
to your parent theme, child theme or custom plugin to begin configuring your settings:

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
