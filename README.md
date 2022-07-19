# Blockify Plugin

Full site editing theme framework and block toolkit. Full documentation coming soon!

Visit [https://wordpress.org/plugins/blockify](https://wordpress.org/plugins/blockify) for more information.

## Installation

1. Download and install Blockify theme from WordPress.org
2. Download and install Blockify plugin from WordPress.org
3. Navigate to Appearance > Editor to begin editing

## Theme Developers

Blockify was built for you! It should work out of the box with any standard FSE theme. Just add the code snippet below to your parent theme, child theme or custom plugin to begin configuring your settings:

```php
// Filter Blockify config.
add_theme_support( 'blockify', [

    // Remove all blocks except icon.
    'blocks' => [
        'icon',
    ],   

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
