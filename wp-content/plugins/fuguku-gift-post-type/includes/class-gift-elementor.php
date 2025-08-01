<?php
/**
 * Elementor Support for Gift Post Type
 */

class FugukuGiftPostType_Elementor {
    
    public function __construct() {
        add_action('elementor/widgets/register', array($this, 'register_widgets'));
        add_action('elementor/elements/categories_registered', array($this, 'add_widget_categories'));
        add_action('elementor/init', array($this, 'init_elementor_support'));
    }
    
    /**
     * Initialize Elementor support
     */
    public function init_elementor_support() {
        // Add support for gift post type in Elementor
        add_post_type_support('gift', 'elementor');
    }
    
    /**
     * Register custom widgets
     */
    public function register_widgets($widgets_manager) {
        // For now, we'll just add basic support
        // Custom widgets can be added later if needed
    }
    
    /**
     * Add widget categories
     */
    public function add_widget_categories($elements_manager) {
        $elements_manager->add_category(
            'fuguku-gift',
            [
                'title' => __('Fuguku Gifts', 'fuguku-gift'),
                'icon' => 'fa fa-gift',
            ]
        );
    }
}
