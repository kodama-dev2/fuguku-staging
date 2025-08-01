<?php
/**
 * Display the vendor store ratings 
 * 
 * Override this template by copying it to yourtheme/wc-vendors/store
 *
 * @package    WCVendors_Pro
 * @version    1.2.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$vendor_shop 		= urldecode( get_query_var( 'vendor_shop' ) );
$vendor_id   		= WCV_Vendors::get_vendor_id( $vendor_shop ); 
$vendor_feedback 	= WCVendors_Pro_Ratings_Controller::get_vendor_feedback( $vendor_id );
$vendor_shop_url	= WCV_Vendors::get_vendor_shop_page( $vendor_id ); 

get_header( 'shop' ); ?>

<?php do_action( 'woocommerce_before_main_content' ); ?>	
	<div class="jas-container vendor-rating">
		<div class="jas-row">
		<div class="jas-col-md-9 jas-col-sm-8 jas-col-xs-12">

			<h1 class="page-title"><?php _e( 'Customer Ratings', 'claue' ); ?></h1>
				<?php if ( $vendor_feedback ) { 

					foreach ( $vendor_feedback as $vf ) {

						$customer 		= get_userdata( $vf->customer_id ); 
						$rating 		= $vf->rating; 
						$rating_title 	= $vf->rating_title; 
						$comment 		= $vf->comments;
						$post_date		= date_i18n( get_option( 'date_format' ), strtotime( $vf->postdate ) );  
						$customer_name 	= ucfirst( $customer->display_name ); 
						$product_link	= get_permalink( $vf->product_id );
						$product_title	= get_the_title( $vf->product_id ); 

						// This outputs the star rating 
						$stars = ''; 
						for ($i = 1; $i<=stripslashes( $rating ); $i++) { $stars .= "<i class='fa fa-star'></i>"; } 
						for ($i = stripslashes( $rating ); $i<5; $i++) { $stars .=  "<i class='fa fa-star-o'></i>"; }
						?> 
						
						<div class="vendor-product-rating">
						<h3><?php if ( ! empty( $rating_title ) ) { echo esc_attr($rating_title).' :: '; } ?> <?php echo esc_attr($stars); ?></h3>

						<p class="vendor-product-title"><?php _e( 'Product:', 'claue'); ?><a href="<?php echo esc_attr($product_link); ?>" target="_blank"><?php echo esc_attr($product_title); ?></a></p>
						<p class="rating-meta"><span><?php __( 'Posted on', 'claue'); ?> <?php echo esc_attr($post_date); ?></span> <?php __( 'by', 'claue'); echo esc_attr($customer_name); ?></p>
						<p class="vendor-product-comment"><?php echo esc_attr($comment); ?></p>
						<hr />	
						</div>		

				<?php } 
				} else {  echo __( 'No ratings have been submitted for this vendor yet.', 'claue' ); }  ?>	

				<h3 class="return-store"><a href="<?php echo esc_attr($vendor_shop_url); ?>"><?php _e( '<span>&larr;</span> Return to store', 'claue' ); ?></a></h3>
			</div>
			<div class="jas-col-md-3 jas-col-sm-4 jas-col-xs-12 vendor-sidebar">
				<?php do_action( 'woocommerce_sidebar' ); ?>
			</div>	
		</div>
	</div>
<?php do_action( 'woocommerce_after_main_content' ); ?>
<?php get_footer( 'shop' ); ?>