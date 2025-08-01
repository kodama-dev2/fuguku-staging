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