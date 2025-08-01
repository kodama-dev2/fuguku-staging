<?php
/**
 * Plugin Name: Fuguku Gifts Post Type
 * Plugin URI: https://fuguku.com/
 * Description: Custom post type untuk gifts/hadiah di website Fuguku
 * Version: 1.0.0
 * Author: Fuguku Development Team
 * License: GPL v2 or later
 * Text Domain: fuguku-gift
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Gifts Post Type
 */
function fuguku_register_gifts_post_type() {
    $labels = array(
        'name'               => _x('Gifts', 'post type general name', 'fuguku-gift'),
        'singular_name'      => _x('Gift', 'post type singular name', 'fuguku-gift'),
        'menu_name'          => _x('Gifts', 'admin menu', 'fuguku-gift'),
        'name_admin_bar'     => _x('Gift', 'add new on admin bar', 'fuguku-gift'),
        'add_new'            => _x('Add New', 'gift', 'fuguku-gift'),
        'add_new_item'       => __('Add New Gift', 'fuguku-gift'),
        'new_item'           => __('New Gift', 'fuguku-gift'),
        'edit_item'          => __('Edit Gift', 'fuguku-gift'),
        'view_item'          => __('View Gift', 'fuguku-gift'),
        'all_items'          => __('All Gifts', 'fuguku-gift'),
        'search_items'       => __('Search Gifts', 'fuguku-gift'),
        'parent_item_colon'  => __('Parent Gifts:', 'fuguku-gift'),
        'not_found'          => __('No gifts found.', 'fuguku-gift'),
        'not_found_in_trash' => __('No gifts found in Trash.', 'fuguku-gift'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'gifts'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-gift',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions'),
        'show_in_rest'       => true,
    );

    register_post_type('gifts', $args);
}
add_action('init', 'fuguku_register_gifts_post_type');

/**
 * Register Gift Taxonomies
 */
function fuguku_register_gift_taxonomies() {
    // Gift Category
    $category_labels = array(
        'name'              => _x('Gift Categories', 'taxonomy general name', 'fuguku-gift'),
        'singular_name'     => _x('Gift Category', 'taxonomy singular name', 'fuguku-gift'),
        'search_items'      => __('Search Gift Categories', 'fuguku-gift'),
        'all_items'         => __('All Gift Categories', 'fuguku-gift'),
        'parent_item'       => __('Parent Gift Category', 'fuguku-gift'),
        'parent_item_colon' => __('Parent Gift Category:', 'fuguku-gift'),
        'edit_item'         => __('Edit Gift Category', 'fuguku-gift'),
        'update_item'       => __('Update Gift Category', 'fuguku-gift'),
        'add_new_item'      => __('Add New Gift Category', 'fuguku-gift'),
        'new_item_name'     => __('New Gift Category Name', 'fuguku-gift'),
        'menu_name'         => __('Categories', 'fuguku-gift'),
    );

    register_taxonomy('gift_category', array('gifts'), array(
        'hierarchical'      => true,
        'labels'            => $category_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'gift-category'),
        'show_in_rest'      => true,
    ));

    // Gift Tags
    $tag_labels = array(
        'name'              => _x('Gift Tags', 'taxonomy general name', 'fuguku-gift'),
        'singular_name'     => _x('Gift Tag', 'taxonomy singular name', 'fuguku-gift'),
        'search_items'      => __('Search Gift Tags', 'fuguku-gift'),
        'all_items'         => __('All Gift Tags', 'fuguku-gift'),
        'edit_item'         => __('Edit Gift Tag', 'fuguku-gift'),
        'update_item'       => __('Update Gift Tag', 'fuguku-gift'),
        'add_new_item'      => __('Add New Gift Tag', 'fuguku-gift'),
        'new_item_name'     => __('New Gift Tag Name', 'fuguku-gift'),
        'menu_name'         => __('Tags', 'fuguku-gift'),
    );

    register_taxonomy('gift_tag', array('gifts'), array(
        'hierarchical'      => false,
        'labels'            => $tag_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'gift-tag'),
        'show_in_rest'      => true,
    ));
}
add_action('init', 'fuguku_register_gift_taxonomies');

/**
 * Flush rewrite rules on activation
 */
function fuguku_gifts_activate() {
    fuguku_register_gifts_post_type();
    fuguku_register_gift_taxonomies();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'fuguku_gifts_activate');

/**
 * Flush rewrite rules on deactivation
 */
function fuguku_gifts_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'fuguku_gifts_deactivate');

/**
 * Debug function
 */
function fuguku_gifts_debug() {
    if (current_user_can('administrator')) {
        echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc;">';
        echo '<h3>Fuguku Gifts Plugin Debug</h3>';
        
        // Check if plugin is active
        if (is_plugin_active('fuguku-gifts-post-type/fuguku-gifts-post-type.php')) {
            echo '<p style="color: green;"><strong>✅ Plugin is ACTIVE!</strong></p>';
        } else {
            echo '<p style="color: red;"><strong>❌ Plugin is NOT ACTIVE!</strong></p>';
        }
        
        // Check if post type exists
        $post_types = get_post_types(array(), 'names');
        echo '<p><strong>Registered Post Types:</strong> ' . implode(', ', $post_types) . '</p>';
        
        // Check if gifts post type exists
        if (post_type_exists('gifts')) {
            echo '<p style="color: green;"><strong>✅ Gifts post type is registered!</strong></p>';
        } else {
            echo '<p style="color: red;"><strong>❌ Gifts post type is NOT registered!</strong></p>';
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
add_action('admin_footer', 'fuguku_gifts_debug'); 