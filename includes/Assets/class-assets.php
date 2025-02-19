<?php

class BROKLIFI_Assets {
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'load_assets'));
    }

    // Load JavaScript & CSS
    public function load_assets($hook) {
        if ($hook !== 'toplevel_page_broken-link-fixer') {
            return;
        }
        wp_enqueue_style('broken-link-fixer', BROKLIFI_PLUGIN_URL . '/assets/css/style.css');
        wp_enqueue_script('broken-link-fixer', BROKLIFI_PLUGIN_URL . '/assets/js/script.js', array('jquery'), '1.0', true);

    }
}