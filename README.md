# Blockify Plugin

Full site editing theme framework and block toolkit.
Visit [https://wordpress.org/plugins/blockify](https://wordpress.org/plugins/blockify)

## Installation

1. Download and install Blockify theme from WordPress.org
2. Download and install Blockify plugin from WordPress.org
3. Navigate to Appearance > Editor to begin editing

## Theme Developers

Blockify was built for you! Add the code snippet below to your parent or child theme to configure your settings:

```php
// Configure block toolkit.
add_theme_support( 'blockify', [

	// Modify default block supports.
	'blockSupports' => [
		'core/paragraph' => [
			'alignWide' => true,
		],
	],

	// Block styles to be registered with JS.
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
