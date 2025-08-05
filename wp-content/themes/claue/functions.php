<?php
/**
 * Theme constants definition and functions.
 *
 * @since   1.0.0
 * @package Claue
 */

// Constants definition
define( 'JAS_CLAUE_PATH', get_template_directory()     );
define( 'JAS_CLAUE_URL',  get_template_directory_uri() );
define( 'JAS_CLAUE_VERSION', '2.2.1' );

// Initialize core file
require JAS_CLAUE_PATH . '/core/init.php';

// Integrasi shop.com
add_action( 'woocommerce_thankyou', 'my_custom_tracking' );
function my_custom_tracking( $order_id ) {  
	$order = new WC_Order( $order_id );  
	$total = $order->get_subtotal();  
	$id = str_replace('#', '',
	$order->get_order_number());  
	echo '<iframe src=https://marktamerica.go2cloud.org/aff_l?offer_id=18902&amount=' . $total . '&adv_sub=' . $id . ' scrolling="no" frameborder="0" width="1" height="1"></iframe>';
}

/**
 * Enqueue Gifts CSS
 */
function claue_enqueue_gifts_styles() {
    if (is_post_type_archive('gifts') || is_singular('gifts') || is_tax('gift_category') || is_tax('gift_tag')) {
        wp_enqueue_style(
            'claue-gifts-styles',
            get_template_directory_uri() . '/assets/css/gifts.css',
            array(),
            JAS_CLAUE_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'claue_enqueue_gifts_styles');
/**
 * Enqueue FUGU Images CSS
 */
function claue_enqueue_fugu_images_styles() {
    if (is_page() || is_single() || is_archive()) {
        wp_enqueue_style('claue-fugu-images', get_template_directory_uri() . '/assets/css/fugu-images.css', array(), '1.0.0');
    }
}
add_action('wp_enqueue_scripts', 'claue_enqueue_fugu_images_styles');

// Enqueue product-revamp.css and JS for product revamp layout
add_action('wp_enqueue_scripts', function() {
    if (is_product()) {
        global $post;
        $options = get_post_meta($post->ID, '_custom_wc_options', true);
        $style = (is_array($options) && !empty($options['wc-single-style'])) ? $options['wc-single-style'] : (function_exists('cs_get_option') ? cs_get_option('wc-single-style') : '');
        if ($style === 'revamp') {
                               wp_enqueue_style('product-revamp', get_template_directory_uri() . '/assets/css/product-revamp.css', [], '2.3.6');
                   wp_enqueue_script('product-revamp-js', get_template_directory_uri() . '/assets/js/product-revamp.js', ['jquery'], '2.3.6', true);
        }
    }
});
