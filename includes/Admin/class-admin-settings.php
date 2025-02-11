<?php

class BLF_Admin_Settings {
    public function __construct() {
        add_action("admin_menu", array( $this,"add_admin_menu") );
        add_action("admin_init", array( $this,"register_settings") );
        add_action('admin_post_unlink_broken_link', [$this, 'handle_single_unlink']);
        add_action('admin_post_bulk_unlink_broken_links', [$this, 'handle_bulk_unlink']);
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
        register_setting('blf_settings_group', 'blf_check_interval', [
            'sanitize_callback' => 'absint'
        ]);
        
        register_setting('blf_settings_group', 'blf_auto_replace', [
            'sanitize_callback' => 'sanitize_text_field'
        ]);

        add_settings_section(
            'blf_main_settings',
            __('General Settings', 'broken-link-fixer'),
            '__return_null',
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
                <option value='yes' " . selected($value, 'yes', false) . ">" . esc_html__('Yes', 'broken-link-fixer') . "</option>
                <option value='no' " . selected($value, 'no', false) . ">" . esc_html__('No', 'broken-link-fixer') . "</option>
              </select>";
    }

    public function settings_page() {
        global $wpdb;
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Broken Link Fixer', 'broken-link-fixer'); ?></h1>

            <form method="post" action="">
            <?php wp_nonce_field('blf_run_check_action', 'blf_run_check_nonce'); ?>
                <input type="hidden" name="blf_run_check" value="1">
                <input type="submit" value="<?php esc_attr_e('Check for Broken Links', 'broken-link-fixer'); ?>" class="button button-primary">
            </form>

            <?php
            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blf_run_check'])) {
    
                // Check if the nonce field is set before using it
                if (!isset($_POST['blf_run_check_nonce'])) {
                    wp_die(esc_html__('Security check failed. Nonce is missing.', 'broken-link-fixer'));
                }
            
                // Sanitize and verify nonce
                $nonce = sanitize_text_field(wp_unslash($_POST['blf_run_check_nonce']));
                if (!wp_verify_nonce($nonce, 'blf_run_check_action')) {
                    wp_die(esc_html__('Security check failed. Invalid nonce.', 'broken-link-fixer'));
                }
                BLF_API::scan_site_for_broken_links();
                echo '<p>' . esc_html__('Broken link check completed!', 'broken-link-fixer') . '</p>';
            }
            ?>

            <h2><?php esc_html_e('Broken Links', 'broken-link-fixer'); ?></h2>

            <?php
            // Query broken links from the database
            $broken_links = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}blf_broken_links WHERE status = 'broken'");

            if ($broken_links) {
                echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
                echo '<input type="hidden" name="action" value="bulk_unlink_broken_links">';
                echo '<table class="widefat fixed">';
                echo '<thead>';
                echo '<tr>';
                echo '<th><input type="checkbox" id="select_all"></th>';
                echo '<th>' . esc_html__('Broken URL', 'broken-link-fixer') . '</th>';
                echo '<th>' . esc_html__('Broken Text', 'broken-link-fixer') . '</th>';
                echo '<th>' . esc_html__('Source', 'broken-link-fixer') . '</th>';
                echo '<th>' . esc_html__('Last Checked', 'broken-link-fixer') . '</th>';
                echo '<th>' . esc_html__('Status', 'broken-link-fixer') . '</th>';
                echo '<th>' . esc_html__('Actions', 'broken-link-fixer') . '</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                foreach ($broken_links as $link) {
                    $post = BLF_API::find_post_by_broken_link($link->url);
                    $broken_text = BLF_API::find_broken_text($post->ID, $link->url);
                    echo '<tr>';
                    echo '<td><input type="checkbox" class="link_checkbox" name="link_urls[]" value="' . esc_attr($link->url) . '"></td>';
                    echo '<td><a href="' . esc_url($link->url) . '" target="_blank">' . esc_url($link->url) . '</a></td>';
                    // Display broken text
                    echo '<td>' . (!empty($broken_text) ? esc_html($broken_text) : esc_html__('N/A', 'broken-link-fixer')) . '</td>';

                    // Display source (Post/Page title with edit link)
                    if ($post) {
                        echo '<td><a href="' . esc_url(get_edit_post_link($post->ID)) . '" target="_blank">' . esc_html($post->post_title) . '</a></td>';
                    } else {
                        echo '<td>' . esc_html__('Unknown', 'broken-link-fixer') . '</td>';
                    }
                    echo '<td>' . esc_html($link->last_checked) . '</td>';
                    echo '<td class="not-found">' . esc_html('404 Not Found') . '</td>';
                    echo '<td>';
                    echo '<a href="' . esc_url(admin_url('admin-post.php?action=unlink_broken_link&url=' . urlencode($link->url))) . '" class="button">' . esc_html__('Unlink', 'broken-link-fixer') . '</a>';
                    echo '</td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
                echo '<input type="submit" name="bulk_unlink" value="' . esc_attr__('Bulk Unlink', 'broken-link-fixer') . '" class="button button-secondary button-bulk-unlink">';
                echo '</form>';
            } else {
                echo '<p>' . esc_html__('No broken links found.', 'broken-link-fixer') . '</p>';
            }
            ?>
        </div>
        <?php
    }

    // Handle Unlink (single) request
    public function handle_single_unlink() {
        if (isset($_GET['url'])) {
            $url = urldecode($_GET['url']);
            BLF_API::unlink_broken_link($url);
            wp_redirect(admin_url('admin.php?page=broken-link-fixer'));
            exit;
        }
    }

    // Handle bulk unlink request
    public function handle_bulk_unlink() {
        if (isset($_POST['link_urls']) && is_array($_POST['link_urls'])) {
            foreach ($_POST['link_urls'] as $url) {
                BLF_API::unlink_broken_link(urldecode($url));
            }
        }
        wp_redirect(admin_url('admin.php?page=broken-link-fixer'));
        exit;
    }
    
}