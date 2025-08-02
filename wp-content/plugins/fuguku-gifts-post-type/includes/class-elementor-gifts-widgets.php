<?php
/**
 * Elementor Gifts Widgets
 * 
 * @package Fuguku_Gifts
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Elementor Gifts Widgets Class
 */
class Fuguku_Elementor_Gifts_Widgets {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('elementor/widgets/register', array($this, 'register_widgets'));
        add_action('elementor/elements/categories_registered', array($this, 'add_widget_categories'));
    }

    /**
     * Register Widget Categories
     */
    public function add_widget_categories($elements_manager) {
        $elements_manager->add_category(
            'fuguku-gifts',
            [
                'title' => __('Fuguku Gifts', 'fuguku-gift'),
                'icon' => 'fa fa-gift',
            ]
        );
    }

    /**
     * Register Widgets
     */
    public function register_widgets($widgets_manager) {
        // Include widget files
        require_once(__DIR__ . '/widgets/gift-grid-widget.php');
        require_once(__DIR__ . '/widgets/gift-slider-widget.php');
        require_once(__DIR__ . '/widgets/gift-filter-widget.php');
        require_once(__DIR__ . '/widgets/gift-single-widget.php');

        // Register widgets
        $widgets_manager->register(new \Fuguku_Gift_Grid_Widget());
        $widgets_manager->register(new \Fuguku_Gift_Slider_Widget());
        $widgets_manager->register(new \Fuguku_Gift_Filter_Widget());
        $widgets_manager->register(new \Fuguku_Gift_Single_Widget());
    }
}

// Initialize Elementor widgets
new Fuguku_Elementor_Gifts_Widgets(); 