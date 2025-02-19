<?php

class BROKLIFI_Database_Handler {
    public static function create_tables() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'broklifi_broken_links';
        $charset_collate = $wpdb->get_charset_collate();

        // Check if the table already exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            // If the table doesn't exist, create it
            $sql = "CREATE TABLE $table_name (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                url TEXT NOT NULL,
                status VARCHAR(20) NOT NULL,
                replacement_url TEXT NULL,
                last_checked DATETIME DEFAULT CURRENT_TIMESTAMP
            ) $charset_collate;";
            
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);
        } else {
            // If the table exists, alter it if necessary (in case the primary key isn't set correctly)
            $sql = "ALTER TABLE $table_name
                    CHANGE COLUMN `id` `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY;";
            
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);
        }
    }
}
