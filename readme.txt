=== Blockify - Lightweight Full Site Editing Block Library ===
Contributors: blockify
Requires at least: 6.0
Tested up to: 6.0
Requires PHP: 7.4
Stable tag: 0.0.15
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

**Please Note:** This plugin works best with Gutenberg. Gutenberg is an experimental plugin which may cause breaking changes when updating to newer versions.

Extremely lightweight block library and toolkit that enhances the full site editing experience to make creating WordPress sites with blocks more enjoyable. Perfect for designers and developers needing a little bit extra from the WordPress editor. Take your block themes to the next level with the Blockify plugin! [Launching soon](https://blockifywp.com/) 🚀

== Frequently Asked Questions ==

= What themes does this plugin work with? =

This plugin is designed to work with any Full Site Editing block theme.

= Where is the settings page? =

This plugin provides no settings page. All settings are available in the block editor.

= How do I add icons to the editor? =

SVG Icons can be created by inserting an Image block anywhere in the block editor and then selecting the "SVG Icon" block style from the right sidebar settings. The original image is not loaded and the selected SVG icon is displayed instead. To add a gradient to an icon, select a gradient from the Background Color setting and clear any colors from the Text Color setting.

= How do I add a custom icon set to the editor? =

Custom icon sets can be added by passing the path to the icons to the Blockify config. Please see the default Blockify theme for an example.

= What version of PHP do I need? =

PHP 7.4 or higher is required. Lower versions are no longer supported.

= How to enable/disable specific blocks? =

Copy and paste the code snippet below to get started:

`
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
`

= How do I add code snippets for filter and action hooks? =

Parent themes, child themes and plugins can all be used to modify the default behaviour of Blockify. Every function is either added with a filter or an action which provides developers more control.

== Screenshots ==

1. Block collection library
2. Block extensions and settings
3. Additional text formats
4. Google maps with dark mode

== Installation ==

This plugin can be installed directly from your site.

1. Log in and navigate to Plugins → Add New.
2. Type "Blockify" into the search and hit Enter.
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

= 0.0.15 - 20 August, 2022 =
* Remove: Divider block, moved to spacer block
* Add: Missing pot file

= 0.0.14 - 20 August, 2022 =
* Fix: Calls to undefined functions

= 0.0.13 - 19 August, 2022 =
* Remove: Theme related features

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
