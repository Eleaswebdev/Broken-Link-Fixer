=== Broken Link Fixer ===
Contributors: eleaswp
Tags: broken links, link fixer, 404 fixer, SEO, elementor, wordpress, custom post types  
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: broken-link-fixer

The **Broken Link Fixer** plugin helps you automatically detect and unlink broken links in your WordPress content.

== Description ==

The **Broken Link Fixer** plugin helps you automatically detect and unlink broken links in your WordPress content, including posts, pages, and custom post types. This plugin works with Elementor content as well. The plugin provides an intuitive interface in the admin dashboard for easy detection, management, and un-linking of broken links.

Key Features:
- **Automatic Detection of Broken Links**: Detects broken links across all posts, pages, and custom post types.
- **Supports Elementor Content**: Scans broken links within Elementor widgets, including heading links.
- **Unlink Broken Links**: Unlink or remove broken links from your content with just one click.
- **Bulk Unlink**: Allows you to select multiple broken links and unlink them in bulk.
- **Link Source and Broken Text Display**: Displays the original post/page where the broken link is used, along with the anchor text that was broken.
- **Supports Custom Post Types**: Automatically detects broken links in all custom post types registered in your WordPress site.
- **Easy-to-use Admin Interface**: Provides an easy-to-use interface to manage broken links directly from the WordPress admin panel.

== Installation ==

1. Upload the `broken-link-fixer` plugin folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **Broken Link Fixer** menu , then scan to check for broken links on your site and manage them.

== Frequently Asked Questions ==

= How do I use the plugin? =
1. After activating the plugin, go to **Broken Link Fixer** Menu.
2. Click the "Check for Broken Links" button to start scanning your site.
3. Review the list of broken links and click "Unlink" to remove them from posts/pages.
4. You can also perform a bulk unlink by selecting multiple links and clicking "Bulk Unlink."

= Does the plugin work with Elementor? =
Yes, the plugin detects broken links inside Elementor widgets, including heading links and other content added by Elementor.

= Can I see where the broken link is used? =
Yes, the plugin displays the original post/page title and a link to it for each broken link, so you know exactly where it is being used.

= How does the plugin detect broken links? =
The plugin checks each URL against the live web, and if it returns an error (HTTP status 400 or higher), it marks the link as broken.

== Screenshots ==

1. Scan Page – Start scanning your site for broken links.
2. Results Page – View the list of detected broken links.

== Changelog ==

= 1.0.0 =
* Initial release.
* Added functionality to scan all post types (including custom post types) for broken links.
* Integrated support for detecting broken links inside Elementor content.
* Added UI for managing broken links directly from the admin dashboard.
* Introduced bulk unlink functionality.

== Upgrade Notice ==

= 1.0.0 =
This is the initial version of the Broken Link Fixer plugin. No previous versions exist.

== Acknowledgements ==

- WordPress.org documentation
- Elementor Community

