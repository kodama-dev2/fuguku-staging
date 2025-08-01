<?php
/**
 * Debug file untuk cek plugin loading
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Debug function
function fuguku_gift_debug() {
    if (current_user_can('administrator')) {
        echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc;">';
        echo '<h3>Fuguku Gift Plugin Debug</h3>';
        
        // Check if plugin is active
        if (is_plugin_active('fuguku-gift-post-type/fuguku-gift-post-type.php')) {
            echo '<p style="color: green;"><strong>✅ Plugin is ACTIVE!</strong></p>';
        } else {
            echo '<p style="color: red;"><strong>❌ Plugin is NOT ACTIVE!</strong></p>';
        }
        
        // Check if post type exists
        $post_types = get_post_types(array(), 'names');
        echo '<p><strong>Registered Post Types:</strong> ' . implode(', ', $post_types) . '</p>';
        
        // Check if gift post type exists
        if (post_type_exists('gift')) {
            echo '<p style="color: green;"><strong>✅ Gift post type is registered!</strong></p>';
        } else {
            echo '<p style="color: red;"><strong>❌ Gift post type is NOT registered!</strong></p>';
        }
        
        // Check if taxonomies exist
        $taxonomies = get_taxonomies(array(), 'names');
        echo '<p><strong>Registered Taxonomies:</strong> ' . implode(', ', $taxonomies) . '</p>';
        
        if (taxonomy_exists('gift_category')) {
            echo '<p style="color: green;"><strong>✅ Gift category taxonomy is registered!</strong></p>';
        } else {
            echo '<p style="color: red;"><strong>❌ Gift category taxonomy is NOT registered!</strong></p>';
        }
        
        if (taxonomy_exists('gift_tag')) {
            echo '<p style="color: green;"><strong>✅ Gift tag taxonomy is registered!</strong></p>';
        } else {
            echo '<p style="color: red;"><strong>❌ Gift tag taxonomy is NOT registered!</strong></p>';
        }
        
        // Check if classes exist
        if (class_exists('FugukuGiftPostType_Register')) {
            echo '<p style="color: green;"><strong>✅ FugukuGiftPostType_Register class exists!</strong></p>';
        } else {
            echo '<p style="color: red;"><strong>❌ FugukuGiftPostType_Register class NOT found!</strong></p>';
        }
        
        // Check if method exists
        if (method_exists('FugukuGiftPostType_Register', 'register_post_type')) {
            echo '<p style="color: green;"><strong>✅ register_post_type method exists!</strong></p>';
        } else {
            echo '<p style="color: red;"><strong>❌ register_post_type method NOT found!</strong></p>';
        }
        
        // Check if post type is being registered
        global $wp_post_types;
        if (isset($wp_post_types['gift'])) {
            echo '<p style="color: green;"><strong>✅ Gift post type is in global $wp_post_types!</strong></p>';
        } else {
            echo '<p style="color: red;"><strong>❌ Gift post type is NOT in global $wp_post_types!</strong></p>';
        }
        
        // Check current hook
        echo '<p><strong>Current Hook:</strong> ' . current_filter() . '</p>';
        
        echo '</div>';
    }
}

// Add debug to admin footer
add_action('admin_footer', 'fuguku_gift_debug'); 