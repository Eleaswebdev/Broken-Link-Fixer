<?php
/**
 * Plugin Name: Broken Link Fixer
 * Description: A lightweight plugin that scans your site for broken links and notifies you in the admin dashboard. It auto-replaces them with the closest available links from the Wayback Machine, custom-defined backup URLs, or related internal pages.
 * Version: 1.0.0
 * Author: Eleas Kanchon
 * Author URI: https://github.com/Eleaswebdev
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: broken-link-fixer
 */

defined('ABSPATH') || exit;

define('BLF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BLF_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load the loader class
require_once BLF_PLUGIN_DIR . 'includes/class-loader.php';

BLF_Loader::init();

// Register activation and deactivation hooks
register_activation_hook(__FILE__, ['BLF_Activator', 'activate']);
register_deactivation_hook(__FILE__, ['BLF_Deactivator', 'deactivate']);
