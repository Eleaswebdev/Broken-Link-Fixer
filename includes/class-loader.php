<?php

class BROKLIFI_Loader {
    public static function init() {
        require_once BROKLIFI_PLUGIN_DIR . 'includes/Database/class-database-handler.php';
        require_once BROKLIFI_PLUGIN_DIR . 'includes/class-activator.php';
        require_once BROKLIFI_PLUGIN_DIR . 'includes/class-deactivator.php';

        require_once BROKLIFI_PLUGIN_DIR . 'includes/Admin/class-admin-settings.php';

        require_once BROKLIFI_PLUGIN_DIR . 'includes/Assets/class-assets.php';
        require_once BROKLIFI_PLUGIN_DIR . 'includes/API/class-api.php';

        new BROKLIFI_Admin_Settings();
        new BROKLIFI_Assets();
    }
}