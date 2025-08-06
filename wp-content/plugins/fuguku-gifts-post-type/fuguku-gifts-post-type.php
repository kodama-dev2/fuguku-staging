<?php
/**
 * Plugin Name: Fuguku Gifts Post Type
 * Plugin URI: https://fuguku.com/
 * Description: Custom post type untuk gifts/hadiah di website Fuguku dengan meta fields lengkap dan layout Louis Vuitton inspired. Features: Post type registration, custom taxonomies (categories & tags), meta fields (price, brand, availability, featured), responsive grid layout, admin interface, Elementor compatibility dengan simple GIFT SOLO widget untuk loop grid integration, FUGU IMAGES-ITEM widget dengan enhanced features: multiple images per item, gap control, width control, minimal navigation icons, text field, full-width container, fixed object-fit, hybrid approach (LV-style archive template + Elementor pages), clean CSS, dan simplified widget system untuk better performance dan compatibility. Fixed product revamp layout: removed scroll bar internal, image gaps, border radius, zoom hover effects, multiple lightbox issue dengan simple modal, updated button styling (black color, smaller size), pill shape buttons dengan text center, dan enhanced layout spacing dengan padding besar pada kolom kanan dan price styling tipis. This plugin is updated on 2025-01-28 01:00, version 2.4.1
 * Version: 2.4.1
 * Author: Fuguku Development Team
 * License: GPL v2 or later
 * Text Domain: fuguku-gift
 * Last Updated: 2025-01-28 01:00
 *
 * Version History:
 * v1.0.0 - Initial plugin creation with basic post type
 * v1.1.0 - Added meta fields and basic templates
 * v1.1.1 - Enhanced layout with Louis Vuitton inspired design
 * v1.1.2 - Enhanced description and documentation
 * v1.2.0 - Added Elementor integration with custom widgets
 * v1.3.0 - Added advanced features (AJAX filtering, wishlist, social sharing)
 * v1.4.0 - Implemented hybrid approach (LV-style archive template + Elementor pages)
 * v1.4.1 - Added LV mixed grid layout for Elementor widgets
 * v1.4.3 - Removed demo functionality and simplified widget
 * v1.4.4 - Implemented correct 14-card layout with proper positioning (portrait-large, regular, landscape-wide, portrait-tall)
 * v1.4.5 - Implemented permanent memory system to prevent basic mistakes
 * v1.4.6 - Added proper descriptions and auto attachments to rule files
 * v1.4.7 - Fixed CSS grid override for 14-card layout with proper 4-column grid
 * v1.4.8 - Removed all old CSS and created clean 14-card layout only
 * v1.5.0 - Added Section 1 (4 items: 30% 20% 20% 30%) and Section 2 (5 items: 15% 15% 40% 15% 15%) layouts with proper border radius controls and fixed widget settings
 * v1.5.1 - Completely rebuilt CSS with clean implementation to fix layout conflicts and ensure proper grid display for all layout types
 * v1.5.2 - Fixed widget controls to prevent CSS conflicts, removed columns/gap controls for special layouts, added Compact Grid (4 columns no gap) layout with show/hide info option
 * v1.5.3 - Implemented force override CSS with !important declarations and specific Elementor widget selectors to ensure layouts work properly
 * v1.6.0 - Removed all complex widgets and created simple GIFT SOLO widget for Elementor loop grid integration
 * v1.7.0 - Added FUGU IMAGES-ITEM widget with overlay, navigation, multiple image support, and responsive grid layout
 * v1.8.0 - Enhanced FUGU IMAGES-ITEM widget: multiple images per item, gap control, width control, minimal navigation icons, text field, full-width container, fixed object-fit
 * v1.9.0 - Fixed FUGU IMAGES-ITEM widget: object-fit functionality, full-width container, added text field, simplified navigation icons
 * v1.10.0 - Enhanced FUGU IMAGES-ITEM widget: working object-fit with data attributes, removed backdrop blur from navigation
 * v2.0.0 - Major Release: Added product single revamp layout (layout-revamp.php), modern CSS styling (product-revamp.css), conditional CSS enqueue, dual layout system for single products
 * v2.1.0 - Added revamp layout option to theme configs (framework.config.php, metabox.config.php), created product-revamp.png preview image
 * v2.2.0 - Enhanced product revamp layout: moved Description/Additional Info/Reviews to section 1 right column below categories, implemented full-height scrollable images in left column with internal scroll, updated CSS for better UX
 * v2.3.0 - Cloned layout 4 structure for revamp layout: implemented sticky full-height scrollable images container, moved Description/Additional Info/Reviews to right column with proper tabs, enhanced CSS with layout 4 styling and responsive design
 * v2.3.3 - Fixed product revamp layout: removed scroll bar internal dari kolom kiri, menghilangkan jarak antar image, removed border radius pada image, dan menghilangkan zoom hover effects
 * v2.3.4 - Fixed multiple lightbox issue dengan simple modal approach, disabled fullscreen, added product revamp thumbnail image untuk admin preview
 * v2.3.5 - Updated button styling: changed add to cart dan quick shop buttons to black color dan smaller size
 * v2.3.6 - Updated button shape: changed buttons to pill shape dengan border radius full dan text center alignment
 * v2.3.7 - Added hover icons: eye icon untuk quick view dan cart icon untuk add to cart button di shop page
 * v2.3.8 - Replaced text buttons with circular icon buttons di pojok kanan bawah product image
 * v2.3.9 - Enhanced layout spacing: added large padding pada kolom kanan dan updated price styling dengan font tipis
 * v2.4.0 - Added theme option to hide review tab on product detail pages
 * v2.4.1 - CURRENT - Added KODAMA ADMIN plugin integration for clean admin interface
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
 * Add Meta Boxes for Gift Fields
 */
function fuguku_add_gift_meta_boxes() {
    add_meta_box(
        'gift_details',
        __('Gift Details', 'fuguku-gift'),
        'fuguku_gift_details_callback',
        'gifts',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'fuguku_add_gift_meta_boxes');

/**
 * Meta Box Callback Function
 */
function fuguku_gift_details_callback($post) {
    // Add nonce for security
    wp_nonce_field('fuguku_gift_meta_box', 'fuguku_gift_meta_box_nonce');

    // Get existing values
    $gift_price = get_post_meta($post->ID, '_gift_price', true);
    $gift_brand = get_post_meta($post->ID, '_gift_brand', true);
    $gift_availability = get_post_meta($post->ID, '_gift_availability', true);
    $featured_gift = get_post_meta($post->ID, '_featured_gift', true);
    ?>

    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="gift_price"><?php _e('Price (IDR)', 'fuguku-gift'); ?></label>
            </th>
            <td>
                <input type="number" id="gift_price" name="gift_price" value="<?php echo esc_attr($gift_price); ?>" class="regular-text" />
                <p class="description"><?php _e('Enter the price in Indonesian Rupiah', 'fuguku-gift'); ?></p>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="gift_brand"><?php _e('Brand', 'fuguku-gift'); ?></label>
            </th>
            <td>
                <input type="text" id="gift_brand" name="gift_brand" value="<?php echo esc_attr($gift_brand); ?>" class="regular-text" />
                <p class="description"><?php _e('Enter the brand name', 'fuguku-gift'); ?></p>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="gift_availability"><?php _e('Availability', 'fuguku-gift'); ?></label>
            </th>
            <td>
                <select id="gift_availability" name="gift_availability">
                    <option value=""><?php _e('Select Availability', 'fuguku-gift'); ?></option>
                    <option value="in_stock" <?php selected($gift_availability, 'in_stock'); ?>><?php _e('In Stock', 'fuguku-gift'); ?></option>
                    <option value="limited" <?php selected($gift_availability, 'limited'); ?>><?php _e('Limited Stock', 'fuguku-gift'); ?></option>
                    <option value="out_of_stock" <?php selected($gift_availability, 'out_of_stock'); ?>><?php _e('Out of Stock', 'fuguku-gift'); ?></option>
                </select>
                <p class="description"><?php _e('Select the availability status', 'fuguku-gift'); ?></p>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="featured_gift"><?php _e('Featured Gift', 'fuguku-gift'); ?></label>
            </th>
            <td>
                <input type="checkbox" id="featured_gift" name="featured_gift" value="1" <?php checked($featured_gift, '1'); ?> />
                <label for="featured_gift"><?php _e('Mark as featured gift', 'fuguku-gift'); ?></label>
                <p class="description"><?php _e('Featured gifts will be highlighted in the grid', 'fuguku-gift'); ?></p>
            </td>
        </tr>
    </table>

    <?php
}

/**
 * Save Meta Box Data
 */
function fuguku_save_gift_meta_box($post_id) {
    // Check if nonce is valid
    if (!isset($_POST['fuguku_gift_meta_box_nonce']) || !wp_verify_nonce($_POST['fuguku_gift_meta_box_nonce'], 'fuguku_gift_meta_box')) {
        return;
    }

    // Check if user has permissions to save data
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check if not an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Save the data
    if (isset($_POST['gift_price'])) {
        update_post_meta($post_id, '_gift_price', sanitize_text_field($_POST['gift_price']));
    }

    if (isset($_POST['gift_brand'])) {
        update_post_meta($post_id, '_gift_brand', sanitize_text_field($_POST['gift_brand']));
    }

    if (isset($_POST['gift_availability'])) {
        update_post_meta($post_id, '_gift_availability', sanitize_text_field($_POST['gift_availability']));
    }

    $featured_gift = isset($_POST['featured_gift']) ? '1' : '';
    update_post_meta($post_id, '_featured_gift', $featured_gift);
}
add_action('save_post', 'fuguku_save_gift_meta_box');

/**
 * Elementor Integration
 */
function fuguku_elementor_integration() {
    // Check if Elementor is installed and activated
    if (!did_action('elementor/loaded')) {
        return;
    }

    // Include Elementor widgets
    require_once(__DIR__ . '/includes/class-elementor-gifts-widgets.php');
}
add_action('plugins_loaded', 'fuguku_elementor_integration');

/**
 * Advanced Features Integration
 */
function fuguku_advanced_features_integration() {
    // Include AJAX handler
    require_once(__DIR__ . '/includes/class-ajax-handler.php');

    // Include share handler
    require_once(__DIR__ . '/includes/class-share-handler.php');

    // Enqueue scripts and styles
    add_action('wp_enqueue_scripts', 'fuguku_enqueue_advanced_scripts');
}
add_action('plugins_loaded', 'fuguku_advanced_features_integration');

/**
 * Enqueue Advanced Scripts and Styles
 */
function fuguku_enqueue_advanced_scripts() {
    // Enqueue jQuery UI for slider and autocomplete
    wp_enqueue_script('jquery-ui-slider');
    wp_enqueue_script('jquery-ui-autocomplete');

    // Enqueue custom scripts
    wp_enqueue_script(
        'fuguku-gifts-ajax',
        plugin_dir_url(__FILE__) . 'assets/js/gifts-ajax.js',
        array('jquery', 'jquery-ui-slider', 'jquery-ui-autocomplete'),
        '1.0.0',
        true
    );

    // Localize script
    wp_localize_script('fuguku-gifts-ajax', 'fuguku_gifts_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('fuguku_gifts_nonce'),
    ));

    // Enqueue advanced styles
    wp_enqueue_style(
        'fuguku-gifts-advanced',
        plugin_dir_url(__FILE__) . 'assets/css/gifts-advanced.css',
        array(),
        '1.0.0'
    );
}

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