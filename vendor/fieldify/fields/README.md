# Fieldify

[Documentation](https://fieldifywp.github.io/)

Composer package for creating react-powered fields, blocks and settings in the
WordPress editor with only PHP.

## Installation

From your theme or plugin directory:

```bash
composer require fieldify/fields
```

Currently, this package is not available on Packagist. To install from GitHub,
add the following to your composer.json file:

```json
{
	"require": {
		"fieldify/fields": "dev-main"
	},
	"repositories": [
		{
			"type": "git",
			"url": "git@github.com:fieldifywp/fields.git"
		},
		{
			"type": "git",
			"url": "git@github.com:blockifywp/utilities.git"
		}
	]
}
```

## Configuration

To enable the Fieldify package, add the following to your theme or plugin:

```php
// Require the Composer autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// Configure main plugin file or theme functions.php.
Fieldify::register( __FILE__ );
```

## Usage

### Blocks

Block registration matches WordPress core block registration with the addition
of a 'panels' argument for grouping controls in the block sidebar. Attributes
must specify the 'panel' key to be grouped.

Block names must include a namespace and be in kebab-case in the following
format: `namespace/my-block`.

```php

register_custom_block( 'namespace/my-block', [
	'title'           => __( 'My Block', 'text-domain' ),
	'description'     => __( 'My custom block', 'text-domain' ),
	'category'        => 'custom',
	// Uncomment to use dashicons.
	// 'icon'		  => 'admin-site',
	'icon'            => [
		'src' => get_icon( 'wordpress', 'star-filled' ),
	],
	'keywords'        => [ 'my', 'block' ],
	'render_callback' => static function ( array $attributes, string $content ): string {
		return '<div class="my-block">' . ( $attributes['content'] ?? 'no content' ) . '</div>';
	},
	'style'           => plugin_dir_url( __FILE__ ) . '/assets/my-block.css',
	'supports'        => [
		'color'   => [
			'text'       => true,
			'background' => false,
		],
		'spacing' => [
			'blockGap' => true,
			'margin'   => true,
		],
	],
	'panels'          => [
		'conditional' => [
			'title' => 'Conditional',
		],
		'text'        => [
			'title' => 'Text',
		],
		'number'      => [
			'title' => 'Number',
		],
		'media'       => [
			'title' => 'Media',
		],
		'ui'          => [
			'title' => 'UI',
		],
		'custom'      => [
			'title' => 'Custom',
		],
	],
	// Uncomment to use inner blocks.
	//'template'      => [],
	//'template_lock' => false,
	'attributes'      => [
		'verticalAlign'      => [
			'type'    => 'string',
			'toolbar' => 'BlockVerticalAlignmentToolbar',
		],
		'horizontalAlign'    => [
			'type'    => 'string',
			'toolbar' => 'BlockAlignmentToolbar',
		],
		'hideContentSetting' => [
			'type'    => 'boolean',
			'label'   => 'Hide content setting',
			'control' => 'toggle',
			'default' => false,
			'panel'   => 'conditional',
		],
		'content'            => [
			'type'    => 'string',
			'control' => 'text',
			'default' => 'My block content',
			'panel'   => 'conditional',
			'show_if' => [
				[
					'attribute' => 'hideContentSetting',
					'operator'  => '!==',
					'value'     => true,
				],
			],
		],
		'checkbox'           => [
			'type'    => 'boolean',
			'label'   => __( 'Checkbox', 'text-domain' ),
			'control' => 'checkbox',
			'panel'   => 'ui',
		],
		'number'             => [
			'type'    => 'number',
			'label'   => __( 'Number', 'text-domain' ),
			'control' => 'number',
			'panel'   => 'number',
		],
		'unit'               => [
			'type'    => 'string',
			'label'   => __( 'Unit', 'text-domain' ),
			'control' => 'unit',
			'panel'   => 'number',
		],
		'range'              => [
			'type'    => 'number',
			'label'   => __( 'Range', 'text-domain' ),
			'control' => 'range',
			'min'     => 0,
			'max'     => 100,
			'step'    => 1,
			'panel'   => 'number',
		],
		'dropdown'           => [
			'type'    => 'string',
			'label'   => __( 'Dropdown', 'text-domain' ),
			'control' => 'select',
			'options' => [
				[
					'value' => 'option1',
					'label' => 'Option 1',
				],
				[
					'value' => 'option2',
					'label' => 'Option 2',
				],
				[
					'value' => 'option3',
					'label' => 'Option 3',
				],
			],
			'panel'   => 'ui',
		],
		'paragraphContent'   => [
			'type'    => 'string',
			'label'   => __( 'Paragraph content', 'text-domain' ),
			'control' => 'textarea',
			'panel'   => 'text',
		],
		'hiddenField'        => [
			'type'    => 'string',
			'label'   => __( 'Hidden field', 'text-domain' ),
			'control' => 'hidden',
			'panel'   => 'text',
		],
		'image'              => [
			'type'    => 'string',
			'label'   => __( 'Image picker', 'text-domain' ),
			'control' => 'image',
			'panel'   => 'media',
		],
		'youtubeUrl'         => [
			'type'    => 'string',
			'label'   => __( 'YouTube URL', 'text-domain' ),
			'control' => 'embed',
			'panel'   => 'media',
		],
		'galleryImages'      => [
			'type'    => 'array',
			'label'   => __( 'Gallery images', 'text-domain' ),
			'control' => 'gallery',
			'panel'   => 'media',
		],
		'iconPicker'         => [
			'type'    => 'object',
			'label'   => __( 'Select Icon', 'text-domain' ),
			'control' => 'icon',
			'panel'   => 'custom',
		],
		'colorOrGradient'    => [
			'type'    => 'string',
			'label'   => __( 'Color or Gradient', 'text-domain' ),
			'control' => 'color',
			'panel'   => 'ui',
		],
		'repeater'           => [
			'type'      => 'array',
			'label'     => __( 'Repeater', 'text-domain' ),
			'control'   => 'repeater',
			'panel'     => 'custom',
			'subfields' => [
				'item' => [
					'type'    => 'string',
					'label'   => __( 'Item', 'text-domain' ),
					'control' => 'text',
				],
			],
		],
	],
] );
```

*Attributes*

Block attributes are defined as an associative array with the attribute name as
the key and an array of options as the value.

### Meta Boxes

```php

register_custom_meta_box( 'my-meta-box', [
	'title'      => 'My Meta Box',
	'post_types' => [ 'post' ],
	'context'    => 'side',
	'priority'   => 'default',
	'fields'     => [
		'hideContentSetting' => [
			'default' => false,
			'control' => 'toggle',
		],
		'content' => [
			'label'   => 'Content',
			'control' => 'text',
			'default' => 'My meta box content',
			'show_if' => [
				[
					'field'    => 'hideContentSetting',
					'operator' => '!==',
					'value'    => true,
				],
			],
		],
	],
] );
```

*Fields*

Meta box fields are defined as an associative array with the field name as the
key and an array of options as the value.

### Settings

```php
register_custom_settings('my-settings', [
	'title' => 'My Settings',
	'fields' => [
		'content' => [
			'type' => 'string',
			'default' => 'My settings content',
		],
	],
]);
```

### Supported Controls

#### Core controls

Most WordPress core control component types are supported. Available props can
be found for each component in the WordPress block editor reference guide:

https://developer.wordpress.org/block-editor/reference-guides/components/

- text
- toggle
- checkbox
- number
- unit
- range
- textarea
- select - (with additional support
  for [React Select](https://react-select.com/home) props,
  e.g. `creatable`, `searchable`, `multiple`, etc.)

#### Custom controls

- image
- embed
- gallery
- icon
- color
- repeater
	- subfields: *array*
	- sortable: *boolean*
	- direction: *string* (row|column)

### Utility functions

- **register_block**: *string $id, array $args*
	- Registers a block with the given id and options.
- **register_meta_box**: *string $id, array $args*
	- Registers a meta box with the given id and options.
- **register_settings**: *string $id, array $args*
	- Registers a settings panel with the given id and options.
- **get_icon**: *string $set, string $name, $size = null*
	- Returns svg icon markup.
- **block_is_rendering_preview**
	- Used in block render callback to determine if block is being rendered in
	  the editor preview. 
