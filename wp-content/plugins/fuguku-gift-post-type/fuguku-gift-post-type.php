<?php
/**
 * Plugin Name: Fuguku Gift Post Type
 * Plugin URI: https://fuguku.com/
 * Description: Custom post type untuk gift/hadiah di website Fuguku dengan Elementor dan Gutenberg support
 * Version: 1.0.0
 * Author: Fuguku Development Team
 * License: GPL v2 or later
 * Text Domain: fuguku-gift
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4.5
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('FUGUKU_GIFT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FUGUKU_GIFT_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('FUGUKU_GIFT_PLUGIN_VERSION', '1.0.0');
define('FUGUKU_GIFT_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class FugukuGiftPostType {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Get single instance of this class
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'init'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
        
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        // Include required files
        require_once FUGUKU_GIFT_PLUGIN_PATH . 'includes/class-gift-post-type.php';
        require_once FUGUKU_GIFT_PLUGIN_PATH . 'includes/class-gift-taxonomies.php';
        require_once FUGUKU_GIFT_PLUGIN_PATH . 'includes/class-gift-meta-boxes.php';
        require_once FUGUKU_GIFT_PLUGIN_PATH . 'includes/class-gift-elementor.php';
        require_once FUGUKU_GIFT_PLUGIN_PATH . 'includes/class-gift-gutenberg.php';
        
        // Include debug file for troubleshooting
        require_once FUGUKU_GIFT_PLUGIN_PATH . 'debug.php';
    }
    
    /**
     * Load textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain('fuguku-gift', false, dirname(FUGUKU_GIFT_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Initialize components immediately
        new FugukuGiftPostType_Register();
        new FugukuGiftPostType_Taxonomies();
        new FugukuGiftPostType_MetaBoxes();
        
        // Initialize Elementor support
        if (class_exists('\Elementor\Plugin')) {
            new FugukuGiftPostType_Elementor();
        }
        
        // Initialize Gutenberg support
        new FugukuGiftPostType_Gutenberg();
    }
    
    /**
     * Admin scripts
     */
    public function admin_scripts($hook) {
        global $post_type;
        
        if ($post_type === 'gift') {
            wp_enqueue_style('fuguku-gift-admin', FUGUKU_GIFT_PLUGIN_URL . 'assets/css/admin.css', array(), FUGUKU_GIFT_PLUGIN_VERSION);
            wp_enqueue_script('fuguku-gift-admin', FUGUKU_GIFT_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), FUGUKU_GIFT_PLUGIN_VERSION, true);
            
            // Localize script
            wp_localize_script('fuguku-gift-admin', 'fuguku_gift_admin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('fuguku_gift_nonce'),
            ));
        }
    }
    
    /**
     * Frontend scripts
     */
    public function frontend_scripts() {
        if (is_post_type_archive('gift') || is_singular('gift') || is_tax('gift_category') || is_tax('gift_tag')) {
            wp_enqueue_style('fuguku-gift-frontend', FUGUKU_GIFT_PLUGIN_URL . 'assets/css/frontend.css', array(), FUGUKU_GIFT_PLUGIN_VERSION);
            wp_enqueue_script('fuguku-gift-frontend', FUGUKU_GIFT_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), FUGUKU_GIFT_PLUGIN_VERSION, true);
            
            // Localize script
            wp_localize_script('fuguku-gift-frontend', 'fuguku_gift_frontend', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('fuguku_gift_nonce'),
            ));
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Create default terms
        $this->create_default_terms();
        
        // Set default options
        $this->set_default_options();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create default terms
     */
    private function create_default_terms() {
        // Default gift categories
        $default_categories = array(
            'Gifts for Her' => array(
                'description' => 'Beautiful gifts perfect for women',
                'slug' => 'gifts-for-her'
            ),
            'Gifts for Him' => array(
                'description' => 'Stylish gifts for men',
                'slug' => 'gifts-for-him'
            ),
            'Luxury Gifts' => array(
                'description' => 'Premium and luxury gift items',
                'slug' => 'luxury-gifts'
            ),
            'Personalized Gifts' => array(
                'description' => 'Custom and personalized gift items',
                'slug' => 'personalized-gifts'
            ),
            'Occasion Gifts' => array(
                'description' => 'Gifts for special occasions',
                'slug' => 'occasion-gifts'
            )
        );
        
        foreach ($default_categories as $name => $args) {
            if (!term_exists($name, 'gift_category')) {
                wp_insert_term($name, 'gift_category', $args);
            }
        }
        
        // Default gift tags
        $default_tags = array('Featured', 'New Arrival', 'Best Seller', 'Limited Edition', 'Premium');
        
        foreach ($default_tags as $tag) {
            if (!term_exists($tag, 'gift_tag')) {
                wp_insert_term($tag, 'gift_tag');
            }
        }
    }
    
    /**
     * Set default options
     */
    private function set_default_options() {
        $default_options = array(
            'gifts_per_page' => 12,
            'show_price' => true,
            'show_brand' => true,
            'show_availability' => true,
            'enable_quick_view' => true,
            'enable_wishlist' => true,
            'archive_layout' => 'grid',
            'single_layout' => 'standard'
        );
        
        add_option('fuguku_gift_options', $default_options);
    }
}

// Initialize the plugin
FugukuGiftPostType::get_instance();
