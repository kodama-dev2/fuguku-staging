<?php
/**
 * Plugin Name: KODAMA ADMIN
 * Plugin URI: https://fuguku.com/
 * Description: KDM-PL Custom WordPress admin styling dengan background putih clean, tombol minimalis, font minimalis, dan icon berwarna ungu. Features: Clean white background, minimal button styling, minimal font design, purple colored icons, enhanced admin interface untuk better user experience. This plugin is updated on 2025-01-28 01:00, version 1.0.0
 * Version: 1.0.0
 * Author: Fuguku Development Team
 * License: GPL v2 or later
 * Text Domain: kodama-admin
 * Last Updated: 2025-01-28 01:00
 *
 * Version History:
 * v1.0.0 - Initial plugin creation with clean white background, minimal buttons, minimal font, purple icons
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue KODAMA ADMIN Styles
 */
function kodama_admin_enqueue_styles() {
    // Only load on admin pages
    if (!is_admin()) {
        return;
    }

    wp_enqueue_style(
        'kodama-admin-styles',
        plugin_dir_url(__FILE__) . 'assets/css/kodama-admin.css',
        array(),
        '1.0.0'
    );
}
add_action('admin_enqueue_scripts', 'kodama_admin_enqueue_styles');

/**
 * Add custom admin head styles
 */
function kodama_admin_head_styles() {
    if (!is_admin()) {
        return;
    }
    ?>
    <style type="text/css">
        /* KODAMA ADMIN - Clean White Background */
        body.wp-admin {
            background-color: #ffffff !important;
        }
        
        /* KODAMA ADMIN - Minimal Font */
        body.wp-admin,
        body.wp-admin * {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif !important;
            font-weight: 400 !important;
        }
        
        /* KODAMA ADMIN - Purple Icons */
        .dashicons,
        .dashicons-before::before {
            color: #8B5CF6 !important;
        }
        
        /* KODAMA ADMIN - Minimal Buttons */
        .button,
        .button-primary,
        .button-secondary,
        input[type="submit"],
        input[type="button"] {
            background-color: #f8f9fa !important;
            border: 1px solid #e9ecef !important;
            border-radius: 4px !important;
            color: #495057 !important;
            font-size: 13px !important;
            font-weight: 400 !important;
            padding: 6px 12px !important;
            text-decoration: none !important;
            transition: all 0.2s ease !important;
        }
        
        .button:hover,
        .button-primary:hover,
        .button-secondary:hover,
        input[type="submit"]:hover,
        input[type="button"]:hover {
            background-color: #e9ecef !important;
            border-color: #dee2e6 !important;
            color: #212529 !important;
        }
        
        .button-primary {
            background-color: #8B5CF6 !important;
            border-color: #8B5CF6 !important;
            color: #ffffff !important;
        }
        
        .button-primary:hover {
            background-color: #7C3AED !important;
            border-color: #7C3AED !important;
            color: #ffffff !important;
        }
        
        /* KODAMA ADMIN - Clean Admin Bar */
        #wpadminbar {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e9ecef !important;
        }
        
        #wpadminbar .ab-item {
            color: #495057 !important;
        }
        
        #wpadminbar .ab-item:hover {
            color: #8B5CF6 !important;
        }
        
        /* KODAMA ADMIN - Clean Sidebar */
        #adminmenu {
            background-color: #ffffff !important;
            border-right: 1px solid #e9ecef !important;
        }
        
        #adminmenu li a {
            color: #495057 !important;
            border-bottom: 1px solid #f8f9fa !important;
        }
        
        #adminmenu li a:hover {
            background-color: #f8f9fa !important;
            color: #8B5CF6 !important;
        }
        
        #adminmenu li.current a {
            background-color: #8B5CF6 !important;
            color: #ffffff !important;
        }
        
        /* KODAMA ADMIN - Clean Content Area */
        #wpcontent {
            background-color: #ffffff !important;
        }
        
        /* KODAMA ADMIN - Clean Tables */
        .wp-list-table {
            background-color: #ffffff !important;
            border: 1px solid #e9ecef !important;
        }
        
        .wp-list-table th {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #e9ecef !important;
            color: #495057 !important;
        }
        
        .wp-list-table td {
            border-bottom: 1px solid #f8f9fa !important;
            color: #495057 !important;
        }
        
        /* KODAMA ADMIN - Clean Forms */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="url"],
        input[type="number"],
        textarea,
        select {
            background-color: #ffffff !important;
            border: 1px solid #e9ecef !important;
            border-radius: 4px !important;
            color: #495057 !important;
            font-size: 13px !important;
            padding: 6px 8px !important;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="url"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            border-color: #8B5CF6 !important;
            box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.1) !important;
            outline: none !important;
        }
        
        /* KODAMA ADMIN - Clean Notices */
        .notice {
            background-color: #ffffff !important;
            border: 1px solid #e9ecef !important;
            border-radius: 4px !important;
            color: #495057 !important;
        }
        
        .notice-success {
            border-left-color: #10B981 !important;
        }
        
        .notice-error {
            border-left-color: #EF4444 !important;
        }
        
        .notice-warning {
            border-left-color: #F59E0B !important;
        }
        
        .notice-info {
            border-left-color: #8B5CF6 !important;
        }
        
        /* KODAMA ADMIN - Clean Meta Boxes */
        .postbox {
            background-color: #ffffff !important;
            border: 1px solid #e9ecef !important;
            border-radius: 4px !important;
        }
        
        .postbox .hndle {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #e9ecef !important;
            color: #495057 !important;
        }
        
        /* KODAMA ADMIN - Clean Tabs */
        .nav-tab {
            background-color: #ffffff !important;
            border: 1px solid #e9ecef !important;
            color: #495057 !important;
        }
        
        .nav-tab:hover {
            background-color: #f8f9fa !important;
            color: #8B5CF6 !important;
        }
        
        .nav-tab-active {
            background-color: #8B5CF6 !important;
            border-color: #8B5CF6 !important;
            color: #ffffff !important;
        }
        
        /* KODAMA ADMIN - Clean Pagination */
        .tablenav-pages a {
            background-color: #ffffff !important;
            border: 1px solid #e9ecef !important;
            color: #495057 !important;
        }
        
        .tablenav-pages a:hover {
            background-color: #f8f9fa !important;
            color: #8B5CF6 !important;
        }
        
        .tablenav-pages .current {
            background-color: #8B5CF6 !important;
            border-color: #8B5CF6 !important;
            color: #ffffff !important;
        }
    </style>
    <?php
}
add_action('admin_head', 'kodama_admin_head_styles');

/**
 * Plugin Activation Hook
 */
function kodama_admin_activate() {
    // Add activation timestamp
    update_option('kodama_admin_activated', current_time('timestamp'));
    
    // Flush rewrite rules if needed
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'kodama_admin_activate');

/**
 * Plugin Deactivation Hook
 */
function kodama_admin_deactivate() {
    // Clean up if needed
    delete_option('kodama_admin_activated');
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'kodama_admin_deactivate');

/**
 * Plugin Uninstall Hook
 */
function kodama_admin_uninstall() {
    // Clean up all plugin data
    delete_option('kodama_admin_activated');
}
register_uninstall_hook(__FILE__, 'kodama_admin_uninstall');

/**
 * Debug Function (for development)
 */
function kodama_admin_debug() {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('KODAMA ADMIN Plugin Debug: Plugin loaded successfully');
    }
}
add_action('init', 'kodama_admin_debug'); 