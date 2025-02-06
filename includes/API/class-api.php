<?php

class BLF_API {
    public static function scan_site_for_broken_links() {
        global $wpdb;

        $post_types = get_post_types(['public' => true], 'names');
        
        // Step 1: Get all posts, pages, and custom post types
        $posts = get_posts([
            'post_type'      => $post_types,
            'posts_per_page' => -1, // Get all posts/pages
            'post_status'    => 'publish' // Only get published posts/pages
        ]);
        
        // Step 2: Loop through each post/page and extract links
        foreach ($posts as $post) {
            $content = $post->post_content; // Get post content
            
            // Check for Elementor content
            $elementor_data = get_post_meta($post->ID, '_elementor_data', true);
            if (!empty($elementor_data)) {
                $elementor_json = json_decode($elementor_data, true);
                if (is_array($elementor_json)) {
                    $content .= self::extract_text_from_elementor($elementor_json);
                }
            }
    
            // Extract links from content
            preg_match_all('/<a[^>]+href=["\'](.*?)["\'][^>]*>(.*?)<\/a>/i', $content, $matches, PREG_SET_ORDER);
            
            // Step 3: Loop through each found link and check if it's broken
            foreach ($matches as $match) {
                $url = $match[1]; // Extracted URL
                $anchor_text = isset($match[2]) ? trim(wp_strip_all_tags($match[2])) : ''; // Extracted anchor text
                
                self::check_and_insert_broken_link($url, $anchor_text, $post->ID, $post->post_title);
            }
        }
    }
    
    // Step 4: Function to extract content from Elementor JSON
    private static function extract_text_from_elementor($elements) {
        $html = '';
    
        foreach ($elements as $element) {
            if (isset($element['widgetType'])) {
                if ($element['widgetType'] === 'heading' || $element['widgetType'] === 'text-editor') {
                    if (!empty($element['settings']['title'])) {
                        $html .= $element['settings']['title'] . ' '; // Heading widget
                    }
                    if (!empty($element['settings']['editor'])) {
                        $html .= $element['settings']['editor'] . ' '; // Text Editor widget
                    }
                }
            }
    
            // If it's a container, check nested elements
            if (!empty($element['elements']) && is_array($element['elements'])) {
                $html .= self::extract_text_from_elementor($element['elements']);
            }
        }
    
        return $html;
    }
    
    // Step 5: Function to check if the link is broken and insert into DB
    private static function check_and_insert_broken_link($url, $anchor_text, $post_id, $post_title) {
        global $wpdb;
    
        // Make an HTTP request to check the link status
        $response = wp_remote_get($url, ['timeout' => 5]);
        
        // If there is an error or the response code is 400 or higher, consider it broken
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) >= 400) {
            $status = 'broken';
        } else {
            return; // Skip if the link is working
        }
    
        // Check if the link already exists in the database
        $existing_link = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}blf_broken_links WHERE url = %s",
            $url
        ));
    
        // Format source post URL
        $source_link = get_permalink($post_id);
        $source_html = '<a href="' . esc_url($source_link) . '" target="_blank">' . esc_html($post_title) . '</a>';
    
        // Insert or update the broken link in the database
        if ($existing_link) {
            // Update the link if it already exists
            $wpdb->update(
                "{$wpdb->prefix}blf_broken_links",
                [
                    'status'       => $status,
                    'last_checked' => current_time('mysql'),
                    //'broken_text'  => $anchor_text,
                    //'source'       => $source_html
                ],
                ['url' => $url]
            );
        } else {
            // Insert the broken link if it doesn't exist
            $wpdb->insert(
                "{$wpdb->prefix}blf_broken_links",
                [
                    'url'          => $url,
                    'status'       => $status,
                    'last_checked' => current_time('mysql'),
                   // 'broken_text'  => $anchor_text,
                   // 'source'       => $source_html
                ]
            );
        }
    }
    

    public static function unlink_broken_link($url) {
        global $wpdb;
        $post_types = get_post_types(['public' => true], 'names');
    
        // Get all posts/pages containing the broken link
        $posts = get_posts([
            'post_type'      => $post_types,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            's'              => $url, // Search for the broken link in content
        ]);
    
        // Loop through each post and remove the broken link
        foreach ($posts as $post) {
            $content = $post->post_content;
    
            // Remove the full <a> tag containing the broken URL
            $updated_content = preg_replace(
                '/<a[^>]*href=["\']' . preg_quote($url, '/') . '["\'][^>]*>(.*?)<\/a>/is',
                '$1', // Keep the anchor text, remove only the <a> tag
                $content
            );
    
            // Update the post with the new content only if changes were made
            if ($updated_content !== $content) {
                wp_update_post([
                    'ID'           => $post->ID,
                    'post_content' => $updated_content,
                ]);
            }
        }
          // Delete the broken link from the database
          $wpdb->delete("{$wpdb->prefix}blf_broken_links", ['url' => $url]);
    }

    public static function find_post_by_broken_link($url) {
        global $wpdb;
    
        // Get post where the URL exists
        $post = $wpdb->get_row($wpdb->prepare("
            SELECT ID, post_title 
            FROM {$wpdb->posts} 
            WHERE post_content LIKE %s 
            AND post_status = 'publish'
            LIMIT 1", '%' . $wpdb->esc_like($url) . '%'
        ));
    
        return $post;
    }

    public static function find_broken_text($post_id, $url) {
        if (!$post_id) {
            return '';
        }
    
        $post_content = get_post_field('post_content', $post_id);
    
        // Use regex to find the text inside the anchor tag of the broken link
        preg_match('/<a[^>]+href=["\']' . preg_quote($url, '/') . '["\'][^>]*>(.*?)<\/a>/i', $post_content, $matches);
    
        return isset($matches[1]) ? $matches[1] : '';  // Return anchor text if found, else return empty string
    }
    
    
    
}
