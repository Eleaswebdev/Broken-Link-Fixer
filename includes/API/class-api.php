<?php

class BLF_API {
    public static function scan_site_for_broken_links() {
        global $wpdb;
        
        // Step 1: Get all the posts and pages on the site
        $posts = get_posts([
            'post_type'      => ['post', 'page', 'custom_post_type'], // Add your custom post types here
            'posts_per_page' => -1, // Get all posts/pages
            'post_status'    => 'publish' // Only get published posts/pages
        ]);
        
        // Step 2: Loop through each post/page and extract links
        foreach ($posts as $post) {
            // Step 2a: Extract links from the post content using regex
            $content = $post->post_content;
            preg_match_all('/https?:\/\/[a-zA-Z0-9\/?=%.&_:-]+/', $content, $matches);
            
            // Step 2b: Loop through each found link and check if it's broken
            foreach ($matches[0] as $url) {
                self::check_and_insert_broken_link($url);
            }
        }
    }

    // Step 3: Function to check if the link is broken and insert into DB if broken
    private static function check_and_insert_broken_link($url) {
        global $wpdb;

        // Step 3a: Make an HTTP request to check the link status
        $response = wp_remote_get($url, ['timeout' => 5]);
        
        // Step 3b: If there is an error or the response code is 400 or higher, consider it broken
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) >= 400) {
            $status = 'broken';
        } else {
            return; // Skip if the link is working
        }

        // Step 3c: Check if the link already exists in the database
        $existing_link = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}blf_broken_links WHERE url = %s",
            $url
        ));

        // Step 3d: Insert or update the broken link in the database
        if ($existing_link) {
            // Update the link if it already exists
            $wpdb->update(
                "{$wpdb->prefix}blf_broken_links",
                ['status' => $status, 'last_checked' => current_time('mysql')],
                ['url' => $url]
            );
        } else {
            // Insert the broken link if it doesn't exist
            $wpdb->insert(
                "{$wpdb->prefix}blf_broken_links",
                [
                    'url' => $url,
                    'status' => $status,
                    'last_checked' => current_time('mysql')
                ]
            );
        }
    }
}
