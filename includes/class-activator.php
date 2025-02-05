<?php

class BLF_Activator {
    public static function activate() {
        require_once BLF_PLUGIN_DIR . 'includes/Database/class-database-handler.php';
        BLF_Database_Handler::create_tables();
    }
}