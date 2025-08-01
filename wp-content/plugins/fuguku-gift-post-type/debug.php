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
        
        echo '</div>';
    }
}

// Add debug to admin footer
add_action('admin_footer', 'fuguku_gift_debug'); 