=== Blockify - Lightweight Block Library and Toolkit ===
Contributors: blockify
Requires at least: 6.0
Tested up to: 6.0
Requires PHP: 7.4
Stable tag: 0.0.12
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

**Please Note:** This plugin is currently in Beta. It has been tested thoroughly however due to WordPress and Gutenberg being under rapid development we cannot guarantee that all settings work 100% correctly all of the time.

Extremely lightweight block toolkit that enhances the full site editing experience. Perfect for designers and developers needing a little bit extra from the WordPress editor. Take your block themes to the next level with the Blockify plugin! [Launching soon](https://blockifywp.com/) 🚀

Every block and extension included has been carefully chosen and built from scratch, in order to extend WordPress' functionality, rather than replacing it. Blockify is designed to work with any standard Full Site Editing block theme, however some may need to add extra theme support. For any development related issues, please submit an issue at [https://github.com/blockifywp/plugin](https://github.com/blockifywp/plugin).

### Block Extensions

Block extensions are additional controls added to core blocks. They are helpful with site building, allowing you finer control over each blocks appearance. Almost every control and block has been built with 100% native WordPress components, and PHP filters are provided for developers.

- **Box Shadow:** Create box shadows on almost any type of block.

- **Absolute Positioning:** Change the positioning of almost any block, giving you almost unlimited control over your design.

- **CSS Transform:** Create modern effects by applying CSS Transforms to commonly used blocks such as images or groups.

- **CSS Filters:** Add CSS filter effects to blocks of any kind. Easily change the opacity, increase brightness, or even invert the colors of entire blocks.

- **List Styles:** Blockify adds the missing numbered list, checklist list and square list styles to the core List block. All Block Styles added by Blockify can be removed or modified with easy to use PHP filters.

- **Size (Width & Height):** Sometimes you need to fine tune the specific width of a block. This can be done easily with one click from the editor.

- **Reverse Columns:** Blockify provides a setting which enables the columns block to be reversed on smaller device screens.

- **Menu Icon:** Customize the appearance of the mobile menu icon from the default double lines.

### Text Format Effects

- **Gradient:** Add custom text gradients to paragraphs, headings, button text or any block that supports rich text formats.

- **Clear Formatting:** Removes all formatting from selected text.

- **Font Size:** Set custom font sizes on selection of text.

- **Underlines:** Set custom underline styles on selection of text.

### Full Site Editing

- **Google Fonts:** Automatically downloads and locally serves Google Fonts selected in the site editor. The Blockify theme provides an example selection of variable Google fonts for you to choose from and select from the site editor.

- **Template Parts:** Easily control the display of template parts on a per page level. This reduces the amount of templates required by themes.

### Extra Features

- **Block Supports:** Additional block support is added by default to allow a wider range of controls and settings on all core blocks, which provides finer control over your entire site. A PHP filter is provided for developers to modify block support settings instantly from within their own theme, child theme or plugin, without needing to know JS or React.

- **Hide Page Title:** Hides the default placement of the page title in the editor and moves it out of the way to the sidebar on pages and selected custom post types.

- **SVG Support:** Extra code has been included to enable SVG support in the image, gallery and site logo blocks.

- **Custom Icons:** Custom icons can be added directly in the editor with the Icon block. Alternatively, developers can provide complete SVG icon sets from within themes, child themes or custom plugins.

- **Optimized CSS & JS:** Optionally load a 1kb base CSS normalize/reset file to ensure consistent styling across core blocks, and fix minor styling issues. All CSS and JS assets are separated and conditionally loaded only when required by a page.

- **Hook System:** All functions can be added/removed/modified with action and filter hooks.

- **No Settings Page:** Blockify adds no settings page to the admin in order to keep the dashboard clean. All settings are available in the block editor.

### Modern Code

- **No jQuery:** - No blocks or extensions require the jQuery library, saving approx ~200kb

- **Block.json:** Every block uses a block.json file and is automatically loaded by WordPress in an optimized way.

- **Code Splitting:** All assets are automatically separated and conditionally loaded only when a block or extension is used on a page.

- **Modern PHP:** Only PHP 7.4 and up is supported. Dropping support for old and unsafe versions of PHP allows for extra clean, secure and fast code.

- **100% TypeScript:** Every line of code has been obsessed over and built following the best modern coding practices.

### Pro (coming soon!)

- **Dark Mode:** Install Blockify Pro and dark mode will be instantly activated. When a users operating system is set to dark mode, the dark version of your site will be displayed on both the front and back end. Themes can easily add support for dark mode by passing a simple array of colors to Blockify. An optional dark mode toggle block is also included which can be placed inside the site header.

- **WooCommerce:** Additional block support and controls for WooCommerce blocks, products and pages. All styles are inherited 100% from theme.json, so there's no need to do any extra work. Everything just works.

- **White Label:** Completely rebrand the entire Blockify plugin with your own company name and logo. Perfect for theme designers and agencies.

== Frequently Asked Questions ==

= Another block library plugin? =

There's already more than enough block library plugins available for free, why do we need another one?

Blockify is far more than a block library, however it made sense to include some commonly needed blocks. They can all be easily deactivated if not needed, plus all block assets are loaded separately, so if the block is not used on a page then nothing is loaded.

The WordPress editor has made significant progress over the past year, which has enabled core blocks and patterns to remove the need for many custom blocks. All blocks included in this library meet strict criteria:

- Commonly required website UI components (e.g. icons, accordions, tabs)
- Not possible, and most likely never possible with WordPress core blocks

Ultimately, including this library means you don't need to have 10+ active plugins for some basic functionality.

= What is Blockify? =

A site building toolkit for modern WordPress development.

= What version of PHP do I need? =

PHP 7.4 and up is required. Sites on older versions of PHP will need to upgrade.

= Can I run this on a live site? =

Blockify, like Gutenberg, is partly experimental. It is currently in Beta,

= How can I add support for the Blockify plugin in my theme? =

Copy and paste the code snippet below to get started:

`
// Filter Blockify config.
add_theme_support( 'blockify', [

    // Only allow selected blocks.
    'blocks' => [
        'icon',
        'newsletter'
    ],

	// Modify default block supports.
	'blockSupports' => [
		'core/paragraph' => [
			'alignWide' => true,
		],
	],

	// Block styles to be registered with JS in the editor.
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
`

= How do I add code snippets for filter and action hooks? =

Parent themes, child themes and plugins can all be used to modify the default behaviour of Blockify. Every function is either added with a filter or an action which provides developers maximum control.

== Screenshots ==

1. Example of block library
2. Example of block extensions
3. Example of text formats
4. Google maps with dark mode pro add on

== Installation ==

This plugin can be installed directly from your site.

1. Log in and navigate to _Plugins → Add New.
2. Type "Blockify" into the Search and hit Enter.
3. Locate the Blockify plugin in the list of search results and click Install Now.
4. Once installed, click the Activate link.

It can also be installed manually:

1. Download the Blockify plugin from WordPress.org.
2. Unzip the package and move to your plugins directory.
3. Log into WordPress and navigate to the Plugins screen.
4. Locate Blockify in the list and click the Activate link.

== Copyright ==

This plugin, like WordPress, is licensed under the GPL.

© Copyright 2022 BlockifyWP.

== Changelog ==

= 0.0.12 - 25 July, 2022 =
* Fix: Reverse on mobile
* Fix: Navigation block font size

= 0.0.11 - July 24, 2022=
* Fix: Google fonts loading
* Remove: Theme related functions

= 0.0.10 - July 22, 2022=
* Update: Move icons to config

= 0.0.9 - July 21, 2022=
* Fix: Reverse on mobile settings
* Update: Improve box shadows

= 0.0.8 - July 20, 2022=
* Fix: Google map block
* Fix: Site editor scripts

= 0.0.7 - July 19, 2022=
* Add: Accordion block description
* Update: Add blocks to config

= 0.0.6 - July 19, 2022=
* Add: Block descriptions

= 0.0.5 - July 19, 2022=
* Add: Screenshots

= 0.0.4 - July 19, 2022=
* Remove: Pattern functions

= 0.0.3 - July 15, 2022=
* Update: Readme

= 0.0.2 - July 12, 2022 =
* Remove: Framework CSS

= 0.0.1 - June 20, 2022=
* Initial commit:
