<?php
/**
 * Initialize woocommerce.
 *
 * @since   1.0.0
 * @package Claue
 */

if ( ! class_exists( 'WooCommerce' ) ) return;

// Add this theme support woocommerce
add_theme_support( 'woocommerce' );

// Add wc support lightbox
add_theme_support( 'wc-product-gallery-lightbox' );

// Remove WooCommerce default styles.
add_filter( 'woocommerce_enqueue_styles', '__return_false' );

/**
 * Locate a template and return the path for inclusion.
 *
 * @since 1.0.0
 */
function jas_claue_wc_locate_template( $template, $template_name, $template_path ) {
	global $woocommerce;

	$_template = $template;

	if ( ! $template_path ) $template_path = $woocommerce->template_url;

	$theme_path = JAS_CLAUE_PATH . '/core/libraries/vendors/woocommerce/templates/';

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Modification: Get the template from this folder, if it exists
	if ( ! $template && file_exists( $theme_path . $template_name ) )
	$template = $theme_path . $template_name;

	// Use default template
	if ( ! $template )
	$template = $_template;

	// Return what we found
	return $template;
}
function jas_claue_wc_template_parts( $template, $slug, $name ) {
	$theme_path  = JAS_CLAUE_PATH . '/core/libraries/vendors/woocommerce/templates/';
	if ( $name ) {
		$newpath = $theme_path . "{$slug}-{$name}.php";
	} else {
		$newpath = $theme_path . "{$slug}.php";
	}
	return file_exists( $newpath ) ? $newpath : $template;
}
add_filter( 'woocommerce_locate_template', 'jas_claue_wc_locate_template', 10, 3 );
add_filter( 'wc_get_template_part', 'jas_claue_wc_template_parts', 10, 3 );

/**
 * Change the breadcrumb separator.
 *
 * @since 1.0.0
 */
function jas_claue_wc_change_breadcrumb_delimiter( $defaults ) {
	$defaults['delimiter'] = '<i class="fa fa-angle-right"></i>';
	return $defaults;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'jas_claue_wc_change_breadcrumb_delimiter' );

/**
 * Change product image thumbnail size.
 *
 * @since 1.0.0
 */
function jas_claue_wc_change_image_thumbnail_size( $size ) {
	global $jassc;

	// Get product list style
	$style = $jassc ? $jassc['style'] : apply_filters( 'jas_claue_wc_style', cs_get_option( 'wc-style' ) );

	// Get image size
	$shop_catalog = wc_get_image_size( 'shop_catalog' );

	// Get product options
	$options = get_post_meta( get_the_ID(), '_custom_wc_thumb_options', true );

	if ( $style == 'metro' && ( isset( $options['wc-thumbnail-size'] ) && $options['wc-thumbnail-size'] && is_numeric($shop_catalog['width']) && is_numeric($shop_catalog['height'] ) ) ) {
		add_image_size( 'jas_claue_shop_metro', $shop_catalog['width'] * 2, $shop_catalog['height'] * 2, true );
		$size = 'jas_claue_shop_metro';
	} else {
		$size = 'woocommerce_thumbnail';
	}
	return $size;
}
add_filter( 'single_product_archive_thumbnail_size', 'jas_claue_wc_change_image_thumbnail_size' );

/**
 * Ordering and result count.
 *
 * @since 1.0.0
 */
function jas_claue_wc_result_count() {
	echo '<div class="result-count-order"><div class="flex between-xs middle-xs">';
}
function jas_claue_wc_catalog_ordering() {
	echo '</div></div>';
}
add_action( 'woocommerce_before_shop_loop', 'jas_claue_wc_result_count', 10 );
add_action( 'woocommerce_before_shop_loop', 'jas_claue_wc_catalog_ordering', 30);

function jas_claue_wc_product_title() {
	echo '<h3 class="product-title pr fs__14 mg__0 fwm"><a class="cd chp" href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a></h3>';
}
add_action( 'woocommerce_shop_loop_item_title', 'jas_claue_wc_product_title', 15 );
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

/**
 * Remove e-commerce function when enable catalog mode.
 *
 * @since 1.1
 */
$catalog_mode = cs_get_option( 'wc-catalog' );
if ( $catalog_mode ) {
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
}

/**
 * Change position of product tab.
 *
 * @since 1.0.0
 */

/**
 * Add extra link after single cart.
 *
 * @since 1.0.0
 */
function jas_claue_wc_add_extra_link_after_cart() {
	// Get page options
	$options = get_post_meta( get_the_ID(), '_custom_wc_options', true );

	// Get image to display size guide
	$size_guide = ( isset( $options['wc-single-size-guide'] ) && $options['wc-single-size-guide'] ) ? $options['wc-single-size-guide'] : cs_get_option( 'wc-single-size-guide' );

	// Get help content
	$message = cs_get_option( 'wc-single-shipping-return' );

	if ( !empty( $size_guide ) || !empty( $message ) ) {

		echo '<div class="extra-link mt__25 fwsb">';
			if ( ! empty( $size_guide ) ) {
				echo '<a class="cd chp jas-magnific-image  mr__20" href="' . esc_url( $size_guide ) . '">' . esc_html__( 'Size Guide', 'claue' ) . '</a>';
			}

			if ( ! empty( $message ) ) {
				echo '<a data-type="shipping-return" class="jas-wc-help cd chp" href="#">' . esc_html__( 'Delivery & Return', 'claue' ) . '</a>';
			}
		echo '</div>';
	}
}
add_action( 'woocommerce_single_product_summary', 'jas_claue_wc_add_extra_link_after_cart', 35 );

/**
 * Custom layout review and price.
 *
 * @since 1.0.0
 */
function jas_claue_wc_before_price() {
	// Get page options
	$options = get_post_meta( get_the_ID(), '_custom_wc_options', true );

	$style = ( is_array( $options ) && $options['wc-single-style'] ) ? $options['wc-single-style'] : ( cs_get_option( 'wc-single-style' ) ? cs_get_option( 'wc-single-style' ) : '1' );
	if ( $style == 3 ) {
		echo '<div class="flex column price-review">';
	} else {
		echo '<div class="flex between-xs middle-xs price-review">';
	}
}
function jas_claue_wc_after_rating() {
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'jas_claue_wc_before_price', 5 );
add_action( 'woocommerce_single_product_summary', 'jas_claue_wc_after_rating', 15 );

/**
 * Countdown sale.
 *
 * @since 2.1.6
 */
$single_countdown_sale = cs_get_option( 'single-countdown-sale' );
if ( $single_countdown_sale ) {
	function jas_claue_product_sales_timer() {
	    global $product;
	    if ( !$product->is_on_sale() ){
	        return;
	    }
	    $date_on_sale_to = null;
	    if( $product->is_type('variable') ) {
	        $variation_ids = $product->get_visible_children();
	        foreach( $variation_ids as $variation_id ) {
	            $variation = wc_get_product( $variation_id );
	            if ( $variation->is_on_sale() ) {
	                $variation_date_on_sale_to = $variation->get_date_on_sale_to();
	                if( !empty($variation_date_on_sale_to) ) {
	                    $date_on_sale_to = $variation_date_on_sale_to;
	                    break;
	                }
	            }
	        }
	    } else {
	        $product_date_on_sale_to = $product->get_date_on_sale_to();
	        if( !empty($product_date_on_sale_to) ) {
	            $date_on_sale_to = $product_date_on_sale_to;
	        }
	    }
	    if ( empty($date_on_sale_to) ){
	        return;
	    }
	    $date_on_sale_to->modify('+1 day');
	    $output = '<div class="countdown-time countdown-time-single-product">
	    				<h4 class="dib mb__10">'.apply_filters( 'jas_claue_product_sales_timer_title', __( 'Hurry Up. Limited time offer', 'claue' )).'</h4>
						<div class="jas-countdown flex tc" data-time=\'{"day": "'.$date_on_sale_to->format('d').'", "month": "'.$date_on_sale_to->format('m').'", "year": "'.$date_on_sale_to->format('Y').'"}\'></div>
					</div>';
	    echo apply_filters( 'jas_claue_product_sales_timer', $output, $date_on_sale_to);
	}
	add_action( 'woocommerce_after_add_to_cart_button', 'jas_claue_product_sales_timer', 25 );
}

/**
 * Register widget area for wc.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'jas_claue_wc_register_sidebars' ) ) {
	function jas_claue_wc_register_sidebars() {
		register_sidebar(
			array(
				'name'          => esc_html__( 'WooCommerce Categories Menu Sidebar', 'claue' ),
				'id'            => 'wc-categories',
				'description'   => esc_html__( 'The woocommerce categories menu sidebar, It will display in archive product page on top', 'claue' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title tu fwsb">',
				'after_title'   => '</h3>',
			)
		);
		register_sidebar(
			array(
				'name'          => esc_html__( 'WooCommerce Filter Sidebar', 'claue' ),
				'id'            => 'wc-filter',
				'description'   => esc_html__( 'The sidebar area for woocommerce, It will display on archive product page', 'claue' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title tu fwsb">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => esc_html__( 'WooCommerce Sidebar', 'claue' ),
				'id'            => 'wc-primary',
				'description'   => esc_html__( 'The woocommerce sidebar, It will display in archive product page on left or right', 'claue' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>',
			)
		);
	}
}
add_action( 'widgets_init', 'jas_claue_wc_register_sidebars' );
   
/**
 * Disable page title on archive product.
 *
 * @since 1.0.0
 */
function jas_claue_wc_disable_page_title() {
	return false;
}
add_filter( 'woocommerce_show_page_title', 'jas_claue_wc_disable_page_title' );

/**
 * Custom add to wishlist button in single product.
 *
 * @since 1.0.0
 */
function jas_claue_before_single_add_to_cart() {
	$ajax_btn        = get_option( 'woocommerce_enable_ajax_add_to_cart_single' );
	$redirect        = get_option( 'woocommerce_cart_redirect_after_add' );
	$stripe_settings = get_option( 'woocommerce_stripe_settings', '' );
	$atc_behavior    = cs_get_option( 'wc-atc-behavior' ) ? cs_get_option( 'wc-atc-behavior' ) : 'slide';

	$classes = array();

	if ( $ajax_btn == 'no' || $redirect == 'yes' ) {
		$classes[] = 'no-ajax';
	}

	if ( isset( $stripe_settings['enabled'] ) && $stripe_settings['enabled'] == 'yes' ) {
		$classes[] = 'stripe-enabled';
	}

	if ( $atc_behavior ) {
		$classes[] = 'atc-' . $atc_behavior;
	}

	echo '<div class="btn-atc ' . esc_attr( implode( ' ', $classes ) ) . '">';
}
function jas_claue_after_single_add_to_cart() {
	echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'jas_claue_before_single_add_to_cart', 25 );
add_action( 'woocommerce_single_product_summary', 'jas_claue_after_single_add_to_cart', 35 );
function jas_claue_return() {
	return;
}
add_filter( 'yith_wcwl_positions', 'jas_claue_return' );

/**
 * Custom add to wishlist button on product listing.
 *
 * @since 1.0.0
 */
function jas_claue_wc_wishlist_button_simple() {
	global $product, $yith_wcwl;

	if ( ! class_exists( 'YITH_WCWL' ) || $product->is_type( 'variable' ) ) return;

	$url          = YITH_WCWL()->get_wishlist_url();
	$product_type = $product->get_type();
	$exists       = $yith_wcwl->is_product_in_wishlist( $product->get_id() );
	$classes      = 'class="add_to_wishlist cw"';
	$add          = get_option( 'yith_wcwl_add_to_wishlist_text' );
	$browse       = get_option( 'yith_wcwl_browse_wishlist_text' );
	$added        = get_option( 'yith_wcwl_product_added_text' );

	echo '<div class="yith-wcwl-add-to-wishlist ts__03 mg__0 ml__10 pr add-to-wishlist-' . esc_attr( $product->get_id() ) . '">
           <div class="yith-wcwl-add-button';
   
   if ($exists){
       echo ' hide" style="display:none;"';
   } else {
       echo ' show"';
   }
            
    echo '><a href="' . esc_url( htmlspecialchars( YITH_WCWL()->get_wishlist_url() ) ) . '" data-product-id="' . esc_attr( $product->get_id() ) . '" data-product-type="' . esc_attr( $product_type ) . '" ' . $classes . ' ><i class="fa fa-heart-o"></i></a>
               <i class="fa fa-spinner fa-pulse ajax-loading pa" style="visibility:hidden"></i>
           </div>
           <div class="yith-wcwl-wishlistaddedbrowse hide" style="display:none;"><a class="chp" href="' . esc_url( $url ) . '"><i class="fa fa-heart"></i></a></div>
              <div class="yith-wcwl-wishlistexistsbrowse ';
              
   if ($exists){
       echo 'show';
   } else {
       echo 'hide';
   }
              
   echo '" style="display:';
   
   if ($exists){
       echo 'block';
   } else {
       echo 'none';
   }
   
   echo '"><a href="' . esc_url( $url ) . '" class="chp"><i class="fa fa-heart"></i></a></div>
       </div>';
    
}
function jas_claue_wc_wishlist_button_variable() {
	global $product, $yith_wcwl;

	if ( ! class_exists( 'YITH_WCWL' ) || ! $product->is_type( 'variable' ) ) return;

	$url          = YITH_WCWL()->get_wishlist_url();
	$product_type = $product->get_type();
	$exists       = $yith_wcwl->is_product_in_wishlist( $product->get_id() );
	$classes      = 'class="add_to_wishlist cw"';
	$add          = get_option( 'yith_wcwl_add_to_wishlist_text' );
	$browse       = get_option( 'yith_wcwl_browse_wishlist_text' );
	$added        = get_option( 'yith_wcwl_product_added_text' );

	echo '<div class="yith-wcwl-add-to-wishlist ts__03 mg__0 ml__10 pr add-to-wishlist-' . esc_attr( $product->get_id() ) . '">
           <div class="yith-wcwl-add-button';
   
   if ($exists){
       echo ' hide" style="display:none;"';
   } else {
       echo ' show"';
   }
            
    echo '><a href="' . esc_url( htmlspecialchars( YITH_WCWL()->get_wishlist_url() ) ) . '" data-product-id="' . esc_attr( $product->get_id() ) . '" data-product-type="' . esc_attr( $product_type ) . '" ' . $classes . ' ><i class="fa fa-heart-o"></i></a>
               <i class="fa fa-spinner fa-pulse ajax-loading pa" style="visibility:hidden"></i>
           </div>
           <div class="yith-wcwl-wishlistaddedbrowse hide" style="display:none;"><a class="chp" href="' . esc_url( $url ) . '"><i class="fa fa-heart"></i></a></div>
              <div class="yith-wcwl-wishlistexistsbrowse ';
              
   if ($exists){
       echo 'show';
   } else {
       echo 'hide';
   }
              
   echo '" style="display:';
   
   if ($exists){
       echo 'block';
   } else {
       echo 'none';
   }
   
   echo '"><a href="' . esc_url( $url ) . '" class="chp"><i class="fa fa-heart"></i></a></div>
       </div>';
}
add_action( 'woocommerce_after_add_to_cart_button', 'jas_claue_wc_wishlist_button_simple', 0 );
add_action( 'woocommerce_before_shop_loop_item', 'jas_claue_wc_wishlist_button_simple', 11 );
add_action( 'woocommerce_before_shop_loop_item', 'jas_claue_wc_wishlist_button_variable', 11 );
add_action( 'woocommerce_after_add_to_cart_button', 'jas_claue_wc_wishlist_button_variable', 0 );

/**
 * Shopping cart.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'jas_claue_wc_my_account' ) ) {
	function jas_claue_wc_my_account() {
		$output = '';

		if ( cs_get_option( 'header-my-account-icon' ) ) {
			$output .= '<div class="jas-my-account hidden-xs ts__05 pr">';
				$output .= '<a class="cb chp db" href="' . esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '" title="' . esc_html__( 'Account', 'claue' ) . '"><i class="pe-7s-user"></i></a>';
				$output .= '<ul class="pa tc">';
					if ( is_user_logged_in() ) {
						$output .= '<li><a class="db cg chp" href="' . esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '">' . esc_html__( 'Dashboard', 'claue' ) . '</a></li>';
						$output .= '<li><a class="db cg chp" href="' . esc_url( wc_get_account_endpoint_url( 'orders' ) ) . '">' . esc_html__( 'My Orders', 'claue' ) . '</a></li>';
						$output .= '<li><a class="db cg chp" href="' . esc_url( wp_logout_url() ) . '">' . esc_html__( 'Logout', 'claue' ) . '</a></li>';
					} else {
						$output .= '<li><a class="db cg chp" href="' . esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '">' . esc_html__( 'Login / Register', 'claue' ) . '</a></li>';
					}
				$output .= '</ul>';
			$output .= '</div>';
		}

		return apply_filters( 'jas_claue_wc_my_account', $output );
	}
}

/**
 * Ensure cart contents update when products are added to the cart via AJAX.
 *
 * @since 1.0.0
 */
function jas_claue_wc_add_to_cart_fragment( $fragments ) {
	ob_start();
	?>
	<a class="cart-contents pr cb chp db" href="#" title="<?php esc_html_e( 'View your shopping cart', 'claue' ); ?>">
		<i class="pe-7s-shopbag"></i>
		<span class="pa count bgb br__50 cw tc"><?php echo sprintf ( wp_kses_post( '%d', '%d', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?></span>
	</a>
	<?php
	
	$fragments['a.cart-contents'] = ob_get_clean();
	
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'jas_claue_wc_add_to_cart_fragment' );

/**
 * Shopping cart in header.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'jas_claue_wc_shopping_cart' ) ) {
	function jas_claue_wc_shopping_cart() {
		global $woocommerce;
		
		// Catalog mode
		$catalog_mode = cs_get_option( 'wc-catalog' );

		if ( $catalog_mode ) return;

		$output = '';
		$output .= '<div class="jas-icon-cart pr">';
			$output .= '<a class="cart-contents pr cb chp db" href="#" title="' . esc_html( 'View your shopping cart', 'claue' ) . '">';
				$output .= '<div style="height: 32px;text-align: start;line-height: 32px; width: 46px;">Cart</div>';
				$output .= '<span class="pa count bgb br__50 cw tc" style="left: 36px;top: 6px;">' . esc_html( $woocommerce->cart->get_cart_contents_count() ) . '</span>';
			$output .= '</a>';
		$output .= '</div>';
		return apply_filters( 'jas_claue_wc_shopping_cart', $output );
	}
}

/**
 * Load mini cart on header.
 *
 * @since 1.0.0
 */
function jas_claue_wc_render_mini_cart() {
	$output = '';

	ob_start();
		$args['list_class'] = '';
		wc_get_template( 'cart/mini-cart.php' );
	$output = ob_get_clean();

	$result = array(
		'message'    => WC()->session->get( 'wc_notices' ),
		'cart_total' => WC()->cart->cart_contents_count,
		'cart_html'  => $output
	);
	echo json_encode( $result );
	exit;
}
add_action( 'wp_ajax_load_mini_cart', 'jas_claue_wc_render_mini_cart' );
add_action( 'wp_ajax_nopriv_load_mini_cart', 'jas_claue_wc_render_mini_cart' );

/**
 * Customize product quick view.
 *
 * @since  1.0
 */
function jas_claue_wc_quickview() {
	// Get product from request.
	if ( isset( $_POST['product'] ) && (int) $_POST['product'] ) {
		global $post, $product, $woocommerce;

		$id      = ( int ) $_POST['product'];
		$post    = get_post( $id );
		$product = wc_get_product( $id );

		if ( $product ) {
			// Get quickview template.
			include JAS_CLAUE_PATH . '/core/libraries/vendors/woocommerce/templates/content-quickview-product.php';
		}
	}

	exit;
}
add_action( 'wp_ajax_jas_quickview', 'jas_claue_wc_quickview' );
add_action( 'wp_ajax_nopriv_jas_quickview', 'jas_claue_wc_quickview' );

/**
 * WPML fix: multicurrency in quickshop Claue theme feature
 */


if ( class_exists( 'woocommerce_wpml' ) ) {
	add_filter( 'wcml_multi_currency_ajax_actions', 'add_action_to_multi_currency_ajax', 10, 1 );
	function add_action_to_multi_currency_ajax( $ajax_actions ) {
	    $ajax_actions[] = 'jas_quickview';
	    return $ajax_actions;
	}	
}
/**
 * Customize shipping & return content.
 *
 * @since  1.0
 */
function jas_claue_wc_shipping_return() {
	// Get help content
	$message = cs_get_option( 'wc-single-shipping-return' );
	if ( ! $message ) return;

	$output = '<div class="wc-content-help pr">' . do_shortcode( $message ) . '</div>';

	echo wp_kses_post($output);
	exit;
}
add_action( 'wp_ajax_jas_shipping_return', 'jas_claue_wc_shipping_return' );
add_action( 'wp_ajax_nopriv_jas_shipping_return', 'jas_claue_wc_shipping_return' );

/**
 * Customize WooCommerce image dimensions.
 *
 * @since  2.0.4
 */
// Update WooCommerce image dimensions.
add_filter( 'woocommerce_get_image_size_gallery_thumbnail', function( $size ) {
return array(
	'width' => 120,
	'height' => 150,
	'crop' => 0,
	);
});

/**
 * Add social sharing to single product.
 *
 * @since  1.0
 */
function jas_claue_wc_single_social_share() {
	if ( cs_get_option( 'wc-social-share' ) ) {
		jas_claue_social_share();
	}
}
add_action( 'woocommerce_single_product_summary', 'jas_claue_wc_single_social_share', 50 );

/**
 * Add page title to archive product.
 *
 * @since  1.0
 */
function jas_claue_wc_page_head() {
	if ( ! cs_get_option( 'wc-enable-page-title' ) || ( class_exists( 'WCV_Vendors' ) && WCV_Vendors::is_vendor_page() ) ) return;

	$title = cs_get_option( 'wc-page-title' );

	$output = '<div class="page-head pr tc"><div class="jas-container pr">';
		if ( is_search() ) {
			$output .= '<h1 class="mb__5 cw">' . sprintf(__( 'Search Results for: %s', 'claue' ), '<span>' . get_search_query() . '</span>' ) . '</h1>';
		} elseif ( is_shop() ) {
			$output .= '<h1 class="mb__5 cw">' . esc_html( cs_get_option( 'wc-page-title' ) ) . '</h1>';
			$output .= '<p class="mg__0">' . do_shortcode( cs_get_option( 'wc-page-desc' ) ) . '</p>';
		} else {
			// Remove old position of category description
			remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );

			$output .= '<h1 class="cw">' . single_cat_title( '', false ) . '</h1>';
			$output .= do_shortcode( category_description() );
		}
		ob_start();
		$output .= ob_get_clean();
	$output .= '</div></div>';

	echo wp_kses_post( $output );
}
add_action( 'woocommerce_before_main_content', 'jas_claue_wc_page_head', 15 );

/**
 * Woocommerce currency switch.
 *
 * @since 1.0.0
 */
function jas_claue_wc_currency() {
	if ( ! class_exists( 'Claue_Addons_Currency' ) || ! cs_get_option( 'header-currency' ) ) return;
 
	// Auto update currency
	$update_every_hours = get_option( 'jas_currency_auto_update_hours' );
	
	if ( isset( $update_every_hours ) && $update_every_hours > 0 ) {
		$last_time_update_cr = strtotime(get_option( 'jas_currency_auto_update_last_time' ) );
		if ( ( time() - $last_time_update_cr ) / 60 / 60 > $update_every_hours ) {
			// Update currency rate
			Claue_Addons_Currency::autoUpdateCurrencyRate();
			$time_format = get_option( 'time_format' );
			update_option( 'jas_currency_auto_update_last_time', date( $time_format, time() ) );
		}
	}
	
	$currencies = Claue_Addons_Currency::getCurrencies();
	$default    = Claue_Addons_Currency::woo_currency();

	$update_by_location = get_option( 'jas_currency_update_by_location', 0 );
	
	if ($update_by_location) {
		$result  = array( 'currency' => '' );
		$client  = WC_Geolocation::get_external_ip_address();
		$ip_data = @json_decode(wp_remote_get( 'http://www.geoplugin.net/json.gp?ip=' . $client ) );
		if ( $ip_data && $ip_data->geoplugin_currencyCode != null ) {
			$result['currency'] = $ip_data->geoplugin_currencyCode;
			if ( isset( $currencies[$result['currency']] ) ) {
				$default = $result;
			}
		}	
	}
	
	$current = isset($_COOKIE['jas_currency']) ? $_COOKIE['jas_currency'] : $default['currency'];
	$_COOKIE['jas_currency']  = $current;
	$output = '';
	if ( is_array( $currencies ) && count( $currencies ) > 0 ) :
		$woocurrency = Claue_Addons_Currency::woo_currency();
		$woocode = $woocurrency['currency'];
		if ( ! isset( $currencies[$woocode] ) ) {
			$currencies[$woocode] = $woocurrency;
		}
		$output .= '<div class="jas-currency dib pr cg">';
			$output .= '<span class="current dib">' . esc_html( $current ) . '<i class="fa fa-angle-down ml__5"></i></span>';
			$output .= '<ul class="pa ts__03 bgbl">';
				foreach( $currencies as $code => $val ) :
					$output .= '<li>';
						$output .= '<a class="currency-item cg db" href="javascript:void(0);" data-currency="' . esc_attr( $code ) . '">' . esc_html( $code ) . '</a>';
					$output .= '</li>';
				endforeach;
			$output .= '</ul>';
		$output .= '</div>';
	endif;

	return apply_filters( 'jas_claue_wc_currency', $output );
}

/**
 * Change number of products displayed per page.
 *
 * @since  1.0
 *
 * @return  number
 *
 */
function jas_claue_wc_change_product_per_page() {
	$number = cs_get_option( 'wc-number-per-page' );

	return $number;
}
add_filter( 'loop_shop_per_page', 'jas_claue_wc_change_product_per_page' );

/**
 * Change pagination position.
 *
 * @since  1.0
 */
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination' );
add_action( 'jas_pagination', 'woocommerce_pagination' );

/**
 * Ajax search.
 *
 * @since  1.0
 */
function jas_claue_wc_live_search() {
	$result = array();
	$args = array(
		's'              => urldecode( $_REQUEST['key'] ),
		'post_type'      => 'product',
		'posts_per_page' => 10
	);
	$query = new WP_Query( $args );
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			
			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), array( 60,60 ) );
			if ( ! empty( $thumb ) ) {
				$thumb = $thumb[0];
			} else {
				$thumb = '';
			}
			$result[] = array(
				'id'     => get_the_ID(),
				'label'  => get_the_title(),
				'value'  => get_the_title(),
				'thumb'  => $thumb,
				'url'    => get_the_permalink(),
				'except' => preg_replace( '/[\x00-\x1F\x7F-\xFF]/u', '' , mb_substr( strip_tags( get_the_excerpt() ), 0, 60 , 'UTF-8' ) ) . '...'
			);
		}
	}
	echo json_encode( $result );
	exit;
}
add_action( 'wp_ajax_jas_claue_live_search', 'jas_claue_wc_live_search' );
add_action( 'wp_ajax_nopriv_jas_claue_live_search', 'jas_claue_wc_live_search' );

/**
 * Add setting enable AJAX add to cart buttons on product detail
 *
 * @since  1.3.4
 */
function jas_claue_setting_ajax_btn( $settings ) {
	$data = array();

	if ( $settings ) {
		foreach( $settings as $val ) {
			if ( isset( $val['id'] ) && $val['id'] == 'woocommerce_enable_ajax_add_to_cart' ) {

				$val['checkboxgroup'] = '';

				$data[] = $val;

				$data[] = array(
					'desc'          => esc_html__( 'Enable AJAX add to cart buttons on product detail', 'claue' ),
					'id'            => 'woocommerce_enable_ajax_add_to_cart_single',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => 'end'
				);
			} else {
				$data[] = $val;
			}
		}

	}

	return $data;
}
add_filter( 'woocommerce_product_settings' , 'jas_claue_setting_ajax_btn' );

/**
 * Change number of related products output
 *
 * @since  1.1.3
 */
if ( ! function_exists( 'jas_claue_related_products_limit' ) ) {
	function jas_claue_related_products_limit( $args ) {
		$limit = cs_get_option( 'wc-other-product-limit' ) ? cs_get_option( 'wc-other-product-limit' ) : 4;

		$args['posts_per_page'] = $limit;
		return $args;
	}
	add_filter( 'woocommerce_output_related_products_args', 'jas_claue_related_products_limit' );
}



/**
 * Extra HTML content below add to cart button.
 *
 * @since  1.2.0
 */
function jas_claue_wc_extra_content() {
	// Get extra content
	$extra_content = cs_get_option( 'wc-extra-content' );

	if ( $extra_content ) {
		$output = '<div class="wc-extra-content dib w__100 mt__30">' . do_shortcode( $extra_content ) . '</div>';

		echo apply_filters( 'jas_claue_wc_extra_content', $output );
	}
}
add_action( 'woocommerce_single_product_summary', 'jas_claue_wc_extra_content', 35 );

/**
 * Extra HTML content below cart total.
 *
 * @since  1.2.0
 */
function jas_claue_cart_extra_content() {
	// Get extra cart content
	$cart_content = cs_get_option( 'wc-cart-content' );

	if ( $cart_content ) {
		$output = '<div class="wc-cart-extra-content dib w__100  mt__30">' . do_shortcode( $cart_content ) . '</div>';

		echo apply_filters( 'jas_claue_cart_extra_content', $output );
	}
}
add_action( 'woocommerce_after_cart_totals', 'jas_claue_cart_extra_content' );

/**
 * Extra HTML content below checkout button.
 *
 * @since  1.2.0
 */
function jas_claue_checkout_extra_content() {
	// Get extra checkout content
	$checkout_content = cs_get_option( 'wc-checkout-content' );

	if ( $checkout_content ) {
		$output = '<div class="wc-extra-content dib w__100 mt__30">' . do_shortcode( $checkout_content ) . '</div>';

		echo apply_filters( 'jas_claue_checkout_extra_content', $output );
	}
}
add_action( 'woocommerce_review_order_after_submit', 'jas_claue_checkout_extra_content', 35 );

/**
 * Extra HTML content below checkout button minicart.
 *
 * @since  1.2.0
 */
function jas_claue_minicart_extra_content() {
	// Get extra checkout content
	$minicart_content = cs_get_option( 'wc-minicart-content' );

	if ( $minicart_content ) {
		$output = '<div class="wc-extra-content dib w__100">' . do_shortcode( $minicart_content ) . '</div>';

		echo apply_filters( 'jas_claue_minicart_extra_content', $output );
	}
}

/**
 * Sticky add to cart
 *
 * @since  1.2.2
 */
if ( ! function_exists( 'jas_claue_sticky_add_to_cart' ) ) {
	function jas_claue_sticky_add_to_cart() {
		if ( cs_get_option( 'wc-sticky-atc' ) && ! cs_get_option( 'wc-catalog' ) ) {
			$atc_behavior = cs_get_option( 'wc-atc-behavior' );
			if ( $atc_behavior == 'popup' ) {
				$atc_behavior = ' atc-popup';
			} else {
				$atc_behavior = ' atc-slide';
			}

			echo '<div class="jas-sticky-atc pf bgb' . $atc_behavior . '">';
				echo woocommerce_template_single_add_to_cart();
			echo '</div>';
		}
	}
}

/**
 * Get Ajax refreshed fragments
 *
 * @since  1.2.2
 */
function jas_claue_popup_ajax_fragments() {
	ob_start();

	woocommerce_mini_cart();

	$mini_cart = ob_get_clean();

	// Fragments and mini cart are returned
	$data = array(
		'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
				'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
			)
		),
		'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() )
	);
	return $data;
}

/**
 * Allow multicurrency on ajax
 */
add_filter( 'wcml_load_multi_currency_in_ajax', 'claue_load_multi_currency_in_ajax', 10, 1 );
if (!function_exists('claue_load_multi_currency_in_ajax')){
    function claue_load_multi_currency_in_ajax( $load ) {
        if (!is_admin()){
            $load = true;
        }
        return $load;
    }
}

/**
 * Get popup cart content
 *
 * @since  1.2.2
 */
function jas_claue_get_popup_cart() {
	$cart_data = WC()->cart->get_cart();

	$output = '';

	if ( $cart_data ) {
		$output .= '<h3 class="cart__popup-title center-xs">' . esc_html__( 'Your order', 'claue' ) . '</h3>';

		foreach ( $cart_data as $cart_item_key => $cart_item ) {
			$_product          = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id        = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
			$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );             
			$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
			$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );

			$product_subtotal  = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );

			if ( ! $product_permalink ) {
				$product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;';
			} else {
				$product_name = apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key );
			}


			$output .= '<div class="cart__popup-item flex middle-xs" data-cart-item="' . htmlentities( json_encode( array( 'key' => $cart_item_key, 'pid' => $product_id , 'pname' => $product_name ) ) ) . '">';
				$output .= '<div class="cart__popup-thumb">' . $thumbnail . ' </div>';
				$output .= '<div class="cart__popup-title grow">';

					if ( ! $product_permalink ) {
						$output .= apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;';
					} else {
						$output .= apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key );
					}

					// Meta data
					if ( version_compare( WC_VERSION, '3.3.0', '<' ) ) {
						$output .= WC()->cart->get_item_data( $cart_item );
					} else {
						$output .= wc_get_formatted_cart_item_data( $cart_item );
					}

					// Backorder notification
					if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
						$output .= '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'claue' ) . '</p>';
					}

					$bundles       = get_post_meta( $product_id, 'wpa_wcpb', true );
					$bundles_added = explode( ',', ( isset( $cart_item['bundle-products'] ) ? $cart_item['bundle-products'] : '' ) );
					
					if ( ! empty( $cart_item['bundle-products'] ) ){
						if ( $bundles ) {
							$custom_variable = $cart_item['bundle-variable'];

							$output .= '<ul class="product-bundle pd__0">';
							foreach( $bundles as $key => $val ) {
								if ( in_array( $val['product_id'], $bundles_added ) ) {
									$product_item = wc_get_product( intval( $val['product_id'] ) );
									$output .= '<li class="pr">';
										$output .= '<a href="'. $product_item->get_permalink() .'" title="'. $product_item->get_name() .'">'. $product_item->get_name() .'</a>';
										// Get variable
										if ( ! empty( $val['variable'] ) ) {
											$variable = wp_unslash( $val['variable'] );

											if ( isset( $custom_variable[$val['product_id']] ) && count( $custom_variable[$val['product_id']] ) > 0 ) {
												// Custom variable before add produt bundle to cart
												$output .= '<span class="db" style="text-transform: capitalize;">';
													$output .= $custom_variable[$val['product_id']]['variable'];
												$output .= '</span>';
											} else {
												if ( ! empty( $val['variable'] ) ) {
													foreach ( $val['variable'] as $key => $value ) {
														$output .= '<span class="db" style="text-transform: capitalize;">';
															$output .= substr( $key, 13 ) . ': ' . $value;
														$output .= '</span>';
													}
												}
											}
										}
									$output .= '</li>';
								}
							}
							$output .= '</ul>';
						}
					}

				$output .= '</div>';
				// var_dump($cart_item);
				if ( isset( $cart_item['bundle-products'] ) && $cart_item['bundle-products'] ) {
					$output .= '<div class="cart__popup-price">' . get_woocommerce_currency_symbol().round( $cart_item['custom-price-with-filter'], 5 ) . '</div>';
				} else {
					$output .= '<div class="cart__popup-price">' . $product_price . '</div>';
				}
				$output .= '<div class="cart__popup-quantity">';

				if ( $_product->is_sold_individually() ) {
					$output .= sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
				} else {
					$max_value = apply_filters( 'woocommerce_quantity_input_max', $_product->get_max_purchase_quantity(), $_product );
					$min_value = apply_filters( 'woocommerce_quantity_input_min', $_product->get_min_purchase_quantity(), $_product );
					$step      = apply_filters( 'woocommerce_quantity_input_step', 1, $_product );

					$output .= '<div class="quantity pr flex">';
						$output .= '<a class="cart__popup-qty cart__popup-qty--minus tc" href="javascript:void(0);">-</a>';
						$output .= '<input type="number" class="cart__popup-qty--input tc" max="'. esc_attr( 0 < $max_value ? $max_value : '' ).'" min="' . esc_attr( $min_value ) .'" step="' . esc_attr( $step ) . '" value="' . $cart_item['quantity'] . '">';
						$output .= '<a class="xcp-plus cart__popup-qty cart__popup-qty--plus tc" href="javascript:void(0);">+</a>';
					$output .= '</div>';
				}

				$output .= '</div>';
				if ( isset( $cart_item['bundle-products'] ) && $cart_item['bundle-products'] ) {
					$output .= '<div class="cart__popup-total fwsb cb">' . get_woocommerce_currency_symbol().round( $cart_item['custom-price-with-filter'], 5 ) * $cart_item['quantity'] . '</div>';
				} else {
					$output .= '<div class="cart__popup-total fwsb cb">' . $product_subtotal . '</div>';
				}
				$output .= '<div class="cart__popup-remove"><i class="fa fa-trash"></i></div>';
			$output .= '</div>';
		}

		$output .= '<div class="flex end-md end-sm center-xs middle-xs cb fs__20 mt__10 pb__10"><span class="mr__10">' . esc_html__( 'Subtotal', 'claue' ) . ': </span><span class="cart__popup-stotal fwb ml__10">' . WC()->cart->get_cart_subtotal() . '</span></div>';

		$output .= '<div class="flex between-xs tc cart__popup-action">';
			$output .= '<a href="javascript:void(0)" class="button mt__20 mfp-close">';
				$output .= esc_html__( 'Continue shopping', 'claue' );
			$output .= '</a>';
			$output .= '<a href="' . esc_url( wc_get_page_permalink( 'checkout' ) ) . '" class="checkout-button button mt__20">';
				$output .= esc_html__( 'Proceed to checkout', 'claue' );
			$output .= '</a>';
		$output .= '</div>';

		$upsells = $cart_product_ids = $args2 = array();

		foreach ( $cart_data as $item ) {
			$cart_product_ids[] = $item['product_id'];
		}

		foreach ( $cart_product_ids as $product_id ) {
			$product = new WC_product( $product_id );
			$upsells = array_merge( $upsells, $product->get_upsell_ids() );
		}

		if ( $upsells ) {
			$upsells = array_diff( $upsells, $cart_product_ids );
			$args2 = array(
				'ignore_sticky_posts' => 1,
				'no_found_rows'       => 1,
				'post__in'            => $upsells,
				'meta_query'          => WC()->query->get_meta_query()
			);
		}
		$args = array(
			'orderby'        => 'post__in',
			'posts_per_page' => 4,
			'post_type'      => 'product',
 			'post_status'    => 'publish',  
			'post__not_in'   => $cart_product_ids,
		);

		$args = array_merge( $args, $args2 );

		$p_upsell = new WP_Query( $args );

		if ( $p_upsell->have_posts() ) :
			$output .= '<h3 class="cart__popup-related-title center-xs">' . esc_html__( 'You might also like', 'claue' ) . '</h3>';
			$output .= '<div class="jas-row">';
				while ( $p_upsell->have_posts() ) : $p_upsell->the_post();
					global $product;
					$output .= '<div class="jas-col-xs-6 jas-col-md-3">';
						$output .= '<div class="popup__cart-product center-xs">';
							if ( has_post_thumbnail() ) {
								$props = wc_get_product_attachment_props( get_post_thumbnail_id(), get_the_ID() );
								$output .= get_the_post_thumbnail( get_the_ID(), array( 150, 150 ), array(
									'title'	 => $props['title'],
									'alt'    => $props['alt'],
								) );
							} elseif ( wc_placeholder_img_src() ) {
								$output .= wc_placeholder_img( array( 150, 150 ) );
							}

							$output .= '<h4 class="ls__0"><a href="' . get_the_permalink() . '">';
								$output .= get_the_title();
							$output .= '</a></h4>';

							ob_start();
								wc_get_template( 'loop/price.php' );
							$output .= ob_get_clean();

							if ( $product->get_type() == 'variable' ) {
								$output .= apply_filters( 'woocommerce_loop_add_to_cart_link',
									sprintf( '<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>',
										esc_url( $product->add_to_cart_url() ),
										esc_attr( isset( $quantity ) ? $quantity : 1 ),
										esc_attr( $product->get_id() ),
										esc_attr( $product->get_sku() ),
										esc_attr( isset( $class ) ? $class : 'button' ),
										esc_html( $product->add_to_cart_text() )
									),
								$product );
							} else {
								$output .= apply_filters( 'woocommerce_loop_add_to_cart_link',
									sprintf( '<button data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</button>',
										esc_attr( isset( $quantity ) ? $quantity : 1 ),
										esc_attr( $product->get_id() ),
										esc_attr( $product->get_sku() ),
										esc_attr( isset( $class ) ? $class : 'modal_btn_add_to_cart ls__0' ),
										esc_html( $product->add_to_cart_text() )
									),
								$product );
							}

						$output .= '</div>';
					$output .= '</div>';
				endwhile;
			$output .= '</div>';
		endif;
		wp_reset_postdata();

		return apply_filters( 'jas_claue_get_popup_cart', $output );
	}
}

/**
 * Update cart
 *
 * @since  1.2.2
 */
function jas_claue_popup_update_cart() {
	$product_data = json_decode( stripslashes( $_POST['product_data'] ), true );
	$product_id   = intval( $product_data['product_id'] );
	$variation_id = intval( $product_data['variation_id'] );
	$quantity     = empty( $product_data['quantity'] ) ? 1 : wc_stock_amount( $product_data['quantity'] );
	$product      = wc_get_product( $product_id );
	$variations   = array();
	$product_image = false;

	if ( $variation_id ) {
		$attributes        = $product->get_attributes();
		$variation_data    = wc_get_product_variation_attributes( $variation_id );
		$chosen_attributes = json_decode( stripslashes( $product_data['attributes'] ), true );

		foreach ( $attributes as $attribute ) {

			if ( ! $attribute['is_variation'] ) {
				continue;
			}

			$taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

			if ( isset( $chosen_attributes[ $taxonomy ] ) ) {
				// Get value from post data
				if ( $attribute['is_taxonomy'] ) {
					// Don't use wc_clean as it destroys sanitized characters
					$value = sanitize_title( stripslashes( $chosen_attributes[ $taxonomy ] ) );

				} else {
					$value = wc_clean( stripslashes( $chosen_attributes[ $taxonomy ] ) );
				}

				// Get valid value from variation
				$valid_value = isset( $variation_data[ $taxonomy ] ) ? $variation_data[ $taxonomy ] : '';

				// Allow if valid or show error.
				if ( '' === $valid_value || $valid_value === $value ) {
					$variations[ $taxonomy ] = $value;
				} 
			}

		}
		$cart_success  = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations );
		$variation     = new WC_product_variation( $variation_id );
		$product_image = $variation->get_image();

	} elseif ( $variation_id === 0 ) {
		$cart_success = WC()->cart->add_to_cart( $product_id, $quantity );
	}

	if ( ! $product_image ) {
		$product_image = $product->get_image( $product_id );
	}

	if ( $cart_success ) {
		$cart_data       = WC()->cart->get_cart();
		$added_cart_key  = $cart_success;
		$added_item_data = $cart_data[$added_cart_key];
		$added_cart_qty  = $added_item_data['quantity'];
		$added_title     = $added_item_data['data']->get_title();
		$output          = jas_claue_get_popup_cart();
		$ajax_fragm      = jas_claue_popup_ajax_fragments();
		$items_count     = WC()->cart->get_cart_contents_count();

		wp_send_json(
			array(
				'pname'       => $added_title,
				'output'      => $output,
				'pimg'        => $product_image ,
				'ajax_fragm'  => $ajax_fragm ,
				'items_count' => $items_count
			)
		);
	} else {
		if ( wc_notice_count( 'error' ) > 0 ) {
			echo wc_print_notices();
		}
	}
	die();
}
add_action( 'wp_ajax_jas_claue_popup_update_cart', 'jas_claue_popup_update_cart' );
add_action( 'wp_ajax_nopriv_jas_claue_popup_update_cart', 'jas_claue_popup_update_cart' );

/**
 * Update cart in ajax
 *
 * @since  1.2.2
 */
function jas_claue_popup_update_ajax() {
	$cart_item_key = sanitize_text_field( $_POST['cart_key'] );
	$new_qty       = (int) $_POST['new_qty'];
	$undo          = sanitize_text_field ($_POST['undo_item'] );
	$updated       = '';

	if ( $new_qty === 0 ) {
		$removed = WC()->cart->remove_cart_item( $cart_item_key );
	} elseif ( $undo == 'true' ) {
		$updated = WC()->cart->restore_cart_item( $cart_item_key );
	} else {
		$updated = WC()->cart->set_quantity( $cart_item_key, $new_qty, true );  
	}

	$cart_data = WC()->cart->get_cart();

	if ( $removed ) {
		$ptotal = $quantity = 0;
	}

	if ( $updated ) {
		$cart_item = $cart_data[$cart_item_key];
		$_product  = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$ptotal    = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
		$quantity  = $cart_item['quantity'];
		
	}
	
	if ( $updated || $removed ) {
		$items_count = count( $cart_data );
		$ajax_fragm  = jas_claue_popup_ajax_fragments();
		$data = array(
			'ptotal'      => $ptotal ,
			'quantity'    => $quantity,
			'cart_total'  => WC()->cart->get_cart_subtotal(),
			'ajax_fragm'  => $ajax_fragm ,
			'items_count' => $items_count
		);
		wp_send_json( $data );
	} else {
		if ( wc_notice_count( 'error' ) > 0 ) {
			echo wc_print_notices();
		}
	}
	die();
}
add_action( 'wp_ajax_jas_claue_popup_update_ajax', 'jas_claue_popup_update_ajax' );
add_action( 'wp_ajax_nopriv_jas_claue_popup_update_ajax', 'jas_claue_popup_update_ajax' );

/**
 * detect add to cart behaviour by class on body tag. 
 *
 * @since  1.2.2
 */
function jas_claue_popup_content_ajax() {
	echo jas_claue_get_popup_cart(); 
	die();
}
add_action( 'wp_ajax_jas_claue_popup_content_ajax', 'jas_claue_popup_content_ajax' );
add_action( 'wp_ajax_nopriv_jas_claue_popup_content_ajax', 'jas_claue_popup_content_ajax' );



/**
 * add prev and next button to single product page 
 *
 * @since  2.1.6
 */

function claue_prev_next_product() {
// Show only products in the same category?
}
 add_action( 'claue_prev_next_product_button', 'claue_prev_next_product', 20 );