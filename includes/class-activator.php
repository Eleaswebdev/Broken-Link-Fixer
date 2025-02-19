<?php

class BROKLIFI_Activator {
    public static function activate() {
        require_once BROKLIFI_PLUGIN_DIR . 'includes/Database/class-database-handler.php';
        BROKLIFI_Database_Handler::create_tables();
    }
}