=== Blockify ===
Contributors: blockify
Requires at least: 6.0
Tested up to: 6.0
Requires PHP: 7.4
Stable tag: 0.0.7
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate Link:

== Description ==

**Please Note:** This plugin is currently in Beta. It has been tested thoroughly however due to WordPress and Gutenberg being under rapid development we cannot guarantee that all settings work 100% correctly all of the time.

Lightweight (1kb), yet powerful block library and toolkit that enhances the WordPress site building experience. Launching soon! 🚀

Blockify aims to simplify the WordPress site building process, by making small and unobtrusive enhancements to the block editor. Perfect for theme designers and developers who need additional functionality without having to learn React or configure build tools.

Every block and extension included has been carefully chosen, in order to extend WordPress' functionality, rather than replacing it. Blockify is designed to work with any standard Full Site Editing block theme. There is also a free starter theme available to use as a quick wireframe for your next project - ([https://wordpress.org/themes/blockify](https://wordpress.org/themes/blockify)).

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

- **NEW! Gradients:** Add custom text gradients to paragraphs, headings, button text or any block that supports Rich Text editing.

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

- **Base CSS:** Optionally load a 1kb base CSS normalize/reset file to ensure consistent styling across core blocks, and fix minor styling issues.

- **Code Splitting:** All CSS and JS assets are separated and conditionally loaded only when required by a page.

- **Hook System:** Every line of code in Blockify can be modified with hooks. There are plenty of actions and filters provided for developers to customize almost every aspect of the editor with plain old PHP.

### Coming Soon

- **Mailchimp:** Mailchimp integration with the newsletter block is almost ready.

### Pro (coming soon!)

- **Dark Mode:** Install Blockify Pro and dark mode will be instantly activated. When a users operating system is set to dark mode, the dark version of your site will be displayed on both the front and back end. Themes can easily add support for dark mode by passing a simple array of colors to Blockify. An optional dark mode toggle block is also included which can be placed inside the site header.

- **WooCommerce Support:** Additional block support and controls for WooCommerce blocks, products and pages. All styles are inherited 100% from theme.json, so there's no need to do any extra work. Everything just works.

- **Premium Icons:** 1000+ premium icons to choose from.

- **Pattern Editor:** Edit, import and export block patterns like a Pro. The pattern editor extension gives you the tools to develop professional, 100% automated design systems from WordPress themes. This is how the Blockify theme demos are created.

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

Blockify is currently in BETA testing, however it is currently running on multiple live websites successfully. It is recommended to treat all WordPress Full Site Editing and block editing as experimental, including this plugin.

= How can I add support for the Blockify plugin in my theme? =

Glad you asked! It couldn't be easier, simply copy and paste the code snippet below as an example:


`
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
`

= How do I add code snippets for filter and action hooks? =

Parent themes, child themes and plugins can all be used to modify the default behaviour of Blockify. Every function is either added with a filter or an action which provides developers maximum control.

== Screenshots ==

1. Example of block library
2. Example of block extensions
3. Example of text formats

== Copyright ==

This plugin, like WordPress, is licensed under the GPL.

© Copyright 2022 BlockifyWP.

== Changelog ==

= 0.0.7 - July 19, 2022=
* Add: Accordion bloc description
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
