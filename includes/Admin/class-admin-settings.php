<?php

class BLF_Admin_Settings {
    public function __construct() {
        add_action("admin_menu", array( $this,"add_admin_menu") );
        add_action("admin_init", array( $this,"register_settings") );
    }

    public function add_admin_menu() {
        add_menu_page(
            "Broken Link Fixer",
            "Broken Link Fixer",
            "manage_options",
            "broken-link-fixer",
            array($this, 'settings_page'),
            "dashicons-admin-links"
        );
    }

    public function register_settings() {
        register_setting('blf_settings_group', 'blf_check_interval');
        register_setting('blf_settings_group', 'blf_auto_replace');

        add_settings_section(
            'blf_main_settings',
            __('General Settings', 'broken-link-fixer'),
            null,
            'blf-settings'
        );

        add_settings_field(
            'blf_check_interval',
            __('Check Interval (hours)', 'broken-link-fixer'),
            [$this, 'check_interval_callback'],
            'blf-settings',
            'blf_main_settings'
        );

        add_settings_field(
            'blf_auto_replace',
            __('Auto-Replace Broken Links', 'broken-link-fixer'),
            [$this, 'auto_replace_callback'],
            'blf-settings',
            'blf_main_settings'
        );
    }

    public function check_interval_callback() {
        $value = get_option('blf_check_interval', 24);
        echo "<input type='number' name='blf_check_interval' value='" . esc_attr($value) . "' min='1' />";
    }

    public function auto_replace_callback() {
        $value = get_option('blf_auto_replace', 'yes');
        echo "<select name='blf_auto_replace'>
                <option value='yes' " . selected($value, 'yes', false) . ">" . __('Yes', 'broken-link-fixer') . "</option>
                <option value='no' " . selected($value, 'no', false) . ">" . __('No', 'broken-link-fixer') . "</option>
              </select>";
    }

    public function settings_page() {
        global $wpdb;
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Broken Link Fixer Settings', 'broken-link-fixer'); ?></h1>

        <form method="post" action="">
            <input type="hidden" name="blf_run_check" value="1">
            <input type="submit" value="<?php esc_attr_e('Check for Broken Links', 'broken-link-fixer'); ?>" class="button button-primary">
        </form>

        <?php
        if (isset($_POST['blf_run_check'])) {
            // Trigger the broken link check
            BLF_API::scan_site_for_broken_links();
            echo '<p>' . esc_html__('Broken link check completed!', 'broken-link-fixer') . '</p>';
        }
        ?>
    </div>
            
            <form method="post" action="options.php">
                <?php
                // settings_fields('blf_settings_group');
                // do_settings_sections('blf-settings');
                // submit_button();
                ?>
            </form>
    
            <h2><?php esc_html_e('Broken Links', 'broken-link-fixer'); ?></h2>
            
            <?php
            // Query broken links from the database
            $broken_links = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}blf_broken_links WHERE status = 'broken'");
    
            // Check if there are broken links
            if ($broken_links) {
                echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
                echo '<table class="widefat fixed" cellspacing="0">';
                echo '<thead>';
                echo '<tr>';
                echo '<th><input type="checkbox" id="select_all" /></th>'; // Select All checkbox
                echo '<th>' . esc_html__('ID', 'broken-link-fixer') . '</th>';
                echo '<th>' . esc_html__('URL', 'broken-link-fixer') . '</th>';
                echo '<th>' . esc_html__('Last Checked', 'broken-link-fixer') . '</th>';
                echo '<th>' . esc_html__('Status', 'broken-link-fixer') . '</th>';
                echo '<th>' . esc_html__('Action', 'broken-link-fixer') . '</th>'; // Unlink column
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                
                // Display each broken link
                foreach ($broken_links as $link) {
                    echo '<tr>';
                    echo '<td><input type="checkbox" name="select_links[]" value="' . esc_attr($link->id) . '" /></td>';
                    echo '<td>' . esc_html($link->id) . '</td>';
                    echo '<td><a href="' . esc_url($link->url) . '" target="_blank">' . esc_url($link->url) . '</a></td>';
                    echo '<td>' . esc_html($link->last_checked) . '</td>';
                    echo '<td>' . esc_html($link->status) . '</td>';
                    echo '<td><a href="' . esc_url(admin_url('admin-post.php?action=blf_unlink_link&link_id=' . $link->id)) . '" class="button button-secondary">Unlink</a></td>';
                    echo '</tr>';
                }
                
                echo '</tbody>';
                echo '</table>';
                
                // Bulk Unlink Button
                echo '<p><input type="submit" name="bulk_unlink" value="Bulk Unlink" class="button button-primary" /></p>';
                echo '</form>';
            } else {
                echo '<p>' . esc_html__('No broken links found.', 'broken-link-fixer') . '</p>';
            }
            ?>
        </div>
        <?php
    }
    
}