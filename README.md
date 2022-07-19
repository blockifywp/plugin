# Blockify Plugin

Full site editing theme framework and block toolkit.

## Installation

1. Download and install Blockify theme from WordPress.org
2. Download and install Blockify plugin from WordPress.org
3. Navigate to Appearance > Editor to begin editing

## Theme Developers

Follow the steps below to enable Blockify support and configure your settings.

1. Add theme support:

```php
add_theme_support( 'blockify', [
    'blockSupports' => [
        'core/paragraph' => [
            'alignWide' => true,
        ],   
    ],
    'blockStyles' => [
        'unregister' => [
            [
		        'type' => 'core/separator',
		        'name' => [ 'wide', 'dots' ],
	        ]
        ],
        'register' => [
            [
			    'type'  => 'core/button',
			    'name'  => 'secondary',
			    'label' => __( 'Secondary', 'blockify' ),
		    ]
        ]
    ]  
] );
```
