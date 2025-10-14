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
        require_once(__DIR__ . '/widgets/gift-solo-widget.php');
        require_once(__DIR__ . '/widgets/fugu-images-item-widget.php');
        require_once(__DIR__ . '/widgets/fugu-productshow-item-widget.php');
        require_once(__DIR__ . '/widgets/fugu-catalog-form-widget.php');
        require_once(__DIR__ . '/widgets/fugu-catalog-table-widget.php');

        // Register widgets
        $widgets_manager->register(new \Fuguku_Gift_Solo_Widget());
        $widgets_manager->register(new \Fugu_Images_Item_Widget());
        $widgets_manager->register(new \Fugu_ProductShow_Item_Widget());
        $widgets_manager->register(new \Fugu_Catalog_Form_Widget());
        $widgets_manager->register(new \Fugu_Catalog_Table_Widget());
    }
}

// Initialize Elementor widgets
new Fuguku_Elementor_Gifts_Widgets(); 