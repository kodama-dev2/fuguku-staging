<?php
/**
 * Demo Page Handler
 * 
 * @package Fuguku_Gifts
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Demo Page Handler Class
 */
class Fuguku_Gifts_Demo_Page {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'create_demo_page'));
        add_filter('template_include', array($this, 'load_demo_template'));
    }

    /**
     * Create demo page if it doesn't exist
     */
    public function create_demo_page() {
        // Check if demo page already exists
        $demo_page = get_page_by_path('demo-luxury-gifts');
        
        if (!$demo_page) {
            // Create demo page
            $page_data = array(
                'post_title' => 'Demo Luxury Gifts',
                'post_name' => 'demo-luxury-gifts',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '',
                'page_template' => 'demo-luxury-gifts.php'
            );
            
            $page_id = wp_insert_post($page_data);
            
            if ($page_id) {
                // Add custom field to identify demo page
                update_post_meta($page_id, '_is_demo_page', 'yes');
                update_post_meta($page_id, '_demo_template', 'demo-luxury-gifts.php');
            }
        }
    }

    /**
     * Load demo template
     */
    public function load_demo_template($template) {
        global $post;
        
        if ($post && get_post_meta($post->ID, '_is_demo_page', true) === 'yes') {
            $demo_template = plugin_dir_path(__FILE__) . '../templates/demo-page.php';
            
            if (file_exists($demo_template)) {
                return $demo_template;
            }
        }
        
        return $template;
    }

    /**
     * Get demo page URL
     */
    public static function get_demo_url() {
        $demo_page = get_page_by_path('demo-luxury-gifts');
        if ($demo_page) {
            return get_permalink($demo_page->ID);
        }
        return '';
    }
}

// Initialize demo page handler
new Fuguku_Gifts_Demo_Page(); 