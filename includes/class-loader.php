<?php

class BLF_Loader {
    public static function init() {
        require_once BLF_PLUGIN_DIR . 'includes/Database/class-database-handler.php';
        require_once BLF_PLUGIN_DIR . 'includes/class-activator.php';
        require_once BLF_PLUGIN_DIR . 'includes/class-deactivator.php';

        require_once BLF_PLUGIN_DIR . 'includes/Admin/class-admin-settings.php';

        require_once BLF_PLUGIN_DIR . 'includes/Frontend/class-frontend.php';
        require_once BLF_PLUGIN_DIR . 'includes/API/class-api.php';

        new BLF_Admin_Settings();
        new BLF_Frontend();
    }
}