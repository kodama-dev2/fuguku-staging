<?php
/**
 * Description
 *
 * @package WPA_WCPB
 * @version 1.0.0
 * @author  WPAddon
 */
if ( ! defined('ABSPATH' ) ) {
	exit;
}

/**
 * Class description.
 *
 * @version 1.0.0
 */
class WPA_WCPB_Template_Hooks {
	/**
	 * Initialize.
	 *
	 * @return  void
	 */
	public static function init() {
		$position = WPA_WCPB_Settings::get_product_bundle_data( 'position_display_setting' );
		switch ( $position ) {
			case 'above-product-tabs':
				add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'list_product_bunle' ), 1 );
				break;

			case 'below-product-tabs':
				add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'list_product_bunle' ), 11 );
				break;

			default:
				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'list_product_bunle' ), 41 );		
				break;
		}
	}
	/**
	 * find matching product variation
	 *
	 * @return  product id
	 */
	public static function find_matching_product_variation( $product, $attributes ) {
		foreach( $attributes as $key => $value ) {
		    if( strpos( $key, 'attribute_' ) === 0 ) {
			    continue;
		    }
		    unset( $attributes[ $key ] );
		    $attributes[ sprintf( 'attribute_%s', $key ) ] = $value;
	    }

	    if( class_exists('WC_Data_Store') ) {
			$data_store = WC_Data_Store::load( 'product' );
	        return $data_store->find_matching_product_variation( $product, $attributes );
		} else {
		    return $product->get_matching_variation( $attributes );
		}
	}

	/**
	 * Show list product bundle.
	 *
	 * @return  string
	 */
	public static function list_product_bunle() {
		global $product;

		$data 					= get_post_meta( $product->get_id(), 'wpa_wcpb', true );
		$widget_edit 			= get_post_meta( $product->get_id(), 'wpa_ebw', true );
		$list_bunle 		   	= array();
		$toal_discount_percent 	= array( 0 );
		$product_image_size    	= WPA_WCPB_Settings::get_product_bundle_data( 'product_image_size' );
		$main_variation_id 			= 0;

		if ( ! empty( $product_image_size ) ) {
			$img_size_arr = explode( 'x', $product_image_size );
			if ( count( $img_size_arr ) >= 2 ) {
				$product_image_size = array( $img_size_arr[0], $img_size_arr[1] );
			}
		} else {
			$product_image_size = array( 70, 70 );
		}

		// Check variable of main product
		$main_variable_class = $main_variable_attr = '';
		if ( $product->is_type( 'variable' ) ) {
			$main_product_attributes = $product->get_variation_attributes();
			$main_product_variations = $product->get_available_variations();
			$main_default_variations = $product->get_default_attributes();

			$main_variation_id = WPA_WCPB_Template_Hooks::find_matching_product_variation($product, $main_default_variations);

			if ($main_variation_id) {
				$product = wc_get_product($main_variation_id);
			}

			if ( count( $main_product_attributes ) ) {
				foreach ( $main_product_attributes as $key => $value ) {
					$main_variable_attr .= ' attribute_' . $key .'=""';
				}
			}
			$main_variable_class = ' wc-variation-selection-needed disabled';	
		}
		
		if ( $data ) {
			$percent_arrange = 0;
			foreach ( $data as $key => $val) {
				if ( isset( $val['percent'] ) && $val['percent'] > $percent_arrange ){
					$percent_arrange = $val['percent'];
				}
			}
			// Get value of product bundle
			foreach( $data as $key => $val ) {
				$product_id   = ( isset( $val['product_id'] ) ) ? intval( $val['product_id'] ) : 0;
				$product_item = wc_get_product( $product_id );

				if ( $product_item ) {
					$toal_discount_percent[$key] = $val['percent'];
					if ( $product_item->is_type( 'variable' ) && ! empty( $val['variable'] ) ) {
						$variable     = wp_unslash( $val['variable'] );
						if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
							$variation_id = $product_item->get_matching_variation( $variable );
						} else {
							$data_store   = WC_Data_Store::load( 'product' );
							$variation_id = $data_store->find_matching_product_variation( $product_item, $variable );
						}

						$available_variation = new WC_Product_Variable( $product_item->get_id() );
						$list_bunle[ $key ]['id']      		= $product_item->get_id();
						$list_bunle[ $key ]['image']        = $product_item->get_image( $product_image_size );
						$list_bunle[ $key ]['url']          = get_permalink( $product_item->get_id() );
						$list_bunle[ $key ]['title']        = $product_item->get_title();
						$list_bunle[ $key ]['price']    	= $available_variation->get_price();
						$list_bunle[ $key ]['percent']      = $percent_arrange;
						$list_bunle[ $key ]['variable']     = $val['variable'];
						$list_bunle[ $key ]['variation_id'] = $variation_id;
						
					} else {
						$list_bunle[ $key ]['id']      = $product_item->get_id();
						$list_bunle[ $key ]['image']   = $product_item->get_image( $product_image_size );
						$list_bunle[ $key ]['url']     = get_permalink( $product_item->get_id() );
						$list_bunle[ $key ]['title']   = $product_item->get_title();
						$list_bunle[ $key ]['price']   = $product_item->get_price();
						$list_bunle[ $key ]['percent'] = $percent_arrange;
					}
				}
			}
		}

		// Sorting total discount array
		sort( $toal_discount_percent );
		if ( $list_bunle ) {
			$bundles_added 					= array();
			$main_product_price_discount 	= $main_product_price = $total = $product->get_price();
			$bundles_widget_title 			= WPA_WCPB_Settings::get_product_bundle_data( 'bundles_widget_title' );
			$bundles_promo_text 			= WPA_WCPB_Settings::get_product_bundle_data( 'bundles_promo_text' );
			if ( isset( $widget_edit['check_enable'] ) && $widget_edit['check_enable'] == 'on' ) {
				$bundles_widget_title 		= $widget_edit['title'];
				$bundles_promo_text 		= $widget_edit['description'];
			}
			echo '
			<div class="wpa-wcpb-list">
					<h4 class="wpa-title">' . $bundles_widget_title . '</h4>
					<p class="wpa-bundle-promo-text">' . $bundles_promo_text . '</p>
					<div class="list-image flxwr">
						<div class="item flx alc">
							<div class="image">' . $product->get_image( $product_image_size ) . '</div>
							<span class="plus">+</span>
						</div>';

						foreach( $list_bunle as $key => $val ){
							// Get Product by id
							$product_bund = wc_get_product( $val['id'] );
							if ( $product_bund->is_in_stock() ) {
								$bundles_added[] = $val['id'];
								echo
								'<div class="item flx alc">
									<div class="image"><a href="' . esc_url( $val['url'] ) . '">' . $val['image'] . '</a></div>
									<span class="plus">+</span>
								</div>';
							}
						}
				$main_product_price_html 			= '<span class="price">' . wc_price( $main_product_price ) . '</span>';
				$input_check_onchange_func 			= 'wpa_wcpb_onchange_input_check_discount_per_item()';
				$data_total_discount 				= 0;

				// Change main product price
				$percent = $i = 0;
				foreach ( $list_bunle as $key => $value ) { 
					$percent = $value['percent'];
					break;
				}

				$main_product_price_discount = $main_product_price - $main_product_price * $percent / 100;
				$main_product_price_html = '<span class="price">'. wc_price( $main_product_price_discount ) .' / <del>'. wc_price( $main_product_price ) .'</del></span>';

				$data_total_discount = implode( ',', $toal_discount_percent );
				$input_check_onchange_func = 'wpa_wcpb_onchange_input_check_total_discount()';
				echo '
					</div>
					<div class="list-select px-product-bundles" data-total-discount="'. $data_total_discount .'">
						<div class="item item-main" data-product-id="'. $product->get_id() .'" data-item-price="'. $main_product_price .'" data-item-price-with-filter="'.WPA_WCPB_Template_Hooks::raw_price_with_filter($main_product_price).'" data-item-percent="0">
							<div class="info-item">
								<input type="checkbox" checked="checked" disabled="disabled" />
								<span class="name">' . esc_html__( 'This product', 'wcpb' ) . ': ' . $product->get_title() . '</span> - 
								'. $main_product_price_html .'
							</div>';
							
							if ($product->is_type('variable') || $product->is_type('variation')) {
								$main_attribute_html = '<div data-variation="'.htmlspecialchars( wp_json_encode($main_product_variations)).'" class="plt-variations-form main-product" data-product_id="'. $product->get_id() .'">';
								foreach ($main_product_attributes as $att_key => $att_data) {
									$main_attribute_html .= '<div class="select-wrap"><select data-default="" data-attribute="'.sanitize_title($att_key).'">';
									foreach($att_data as $value) {
										$selected = isset($main_default_variations[$att_key]) && $main_default_variations[$att_key] == $value ? 'selected' : '';
										$option_term = get_term_by( 'slug', $value, $att_key );
										$name = $option_term ? $option_term->name : $value;
										$main_attribute_html .= '<option '.$selected.' value="'.$value.'">'.$name.'</option>';
									}
									$main_attribute_html .= '</select></div>';
								}
								$main_attribute_html .= '</div>';
								echo $main_attribute_html;
							}

				echo	'</div>';

						$total_bundle = $main_product_price_discount;

						foreach( $list_bunle as $key => $val ){
							// Get Product by id
							$product = wc_get_product( $val['id'] );
							// Get price of product bundle
							if ( WPA_WCPB_Settings::get_product_bundle_type() == 'total-discount' ) {
								$price_bundle = $val['price'] - $val['price'] * $percent / 100;
							} else {
								$price_bundle = $val['price'] - $val['price'] * $val['percent'] / 100;
							}

							echo '<div class="item" data-product-id="' . $val['id'] . '" data-item-price="'. $val['price'] .'" data-item-price-with-filter="'.WPA_WCPB_Template_Hooks::raw_price_with_filter($val['price']).'" data-item-percent="'. $val['percent'] .'">';
							if ( $product->is_in_stock() ) {
								$total += $val['price'];
								$total_bundle += $price_bundle;

								echo    '<div class="info-item in-of-stock">
											<input type="checkbox" onchange="'. $input_check_onchange_func .'" checked="checked" />
											<span class="name">' . $val['title'] . '</span> - 
											<span class="price">' . wc_price( $price_bundle ) . ' / <del>'. wc_price( $val['price'] ) .'</del></span>
										</div>';
							} else {
								echo 	'<div class="info-item out-of-stock">
											<input type="checkbox" disabled />
											<span class="name">' . $val['title'] . '</span> - 
											<span class="price">' . wc_price( $price_bundle ) . ' / <del>'. wc_price( $val['price'] ) .'</del> ('. esc_html__( 'Out of stock', 'wcpb' ) .')</span>
										</div>';
							}
							
							if ( ! empty( $val['variable'] ) ) {
								// Get product bundle Variations
								$product_attributes = $product->get_variation_attributes();
								$product_variations = $product->get_available_variations();
								// var_dump($val['variable']);
								$attribute_html = '<div data-variation="'.htmlspecialchars( wp_json_encode($product_variations)).'" class="plt-variations-form" data-product_id="'. $product->get_id() .'">';
								foreach ($product_attributes as $att_key => $att_data) {

								    $data_default = isset($val['variable']['attribute_' . $att_key]) ? $val['variable']['attribute_' . $att_key] : '';
									$attribute_html .= '<div class="select-wrap"><select data-default="'.$data_default.'" data-attribute="'.$att_key.'">';
									foreach($att_data as $value) {
										$selected = $data_default == $value ? 'selected' : '';
										$option_term = get_term_by( 'slug', $value, $att_key );
										$name = $option_term ? $option_term->name : $value;
										$attribute_html .= '<option '.$selected.' value="'.$value.'">'.$name.'</option>';
									}
									$attribute_html .= '</select></div>';
								}
								$attribute_html .= '</div>';
								echo $attribute_html;
							}
							echo '</div>';
						}
				$button_inline_css = '<style>';
				if ( WPA_WCPB_Settings::get_product_bundle_data( 'button_bg_color' ) ) {
					$button_inline_css .= '.wpa_wcpb_add_to_cart { background-color: ' . WPA_WCPB_Settings::get_product_bundle_data( 'button_bg_color' ) . ';}';
				}
				if ( WPA_WCPB_Settings::get_product_bundle_data( 'button_text_color' ) ) {
					$button_inline_css .= '.wpa_wcpb_add_to_cart { color: ' . WPA_WCPB_Settings::get_product_bundle_data( 'button_text_color' ) . ';}';
				}
				if ( WPA_WCPB_Settings::get_product_bundle_data( 'button_bg_hover_color' ) ) {
					$button_inline_css .= '.wpa_wcpb_add_to_cart:hover { background-color: ' . WPA_WCPB_Settings::get_product_bundle_data( 'button_bg_hover_color' ) . ';}';
				}
				if ( WPA_WCPB_Settings::get_product_bundle_data( 'button_text_hover_color' ) ) {
					$button_inline_css .= '.wpa_wcpb_add_to_cart:hover { color: ' . WPA_WCPB_Settings::get_product_bundle_data( 'button_text_hover_color' ) . ';}';
				}
				$button_inline_css .= '</style>';

				// Check display Bundle Save
				$saved = ( WPA_WCPB_Settings::get_product_bundle_data( 'display_bundle_save' ) == 'amount_off' ) ? '<span class="save-price">' . wc_price( $total - $total_bundle ) . '</span>' : '<span class="save-percent">' . $percent .'</span>%';
				
				echo '
					</div>

					<div class="total price">
						<strong>' . esc_html__( 'Price for all', 'wcpb' ) . '</strong>: <span class="current-price">' . wc_price( $total_bundle ) .'</span> / <del class="old-price">'. wc_price( $total ) . '</del> 
						(' . esc_html__( 'save', 'wcpb' ) . ' ' . $saved . ')
					</div>
						<button class="btn-wpa wpa_wcpb_add_to_cart single_add_to_cart_button button'. $main_variable_class .'" type="submit" onclick="wpa_wcpb_add_to_cart( jQuery(this) )"'. $main_variable_attr .'>' . WPA_WCPB_Settings::get_product_bundle_data( 'button_label' ) . '</button>
						<div class="showbox">
						  <div class="loader">
						    <svg viewBox="25 25 50 50">
						      <circle class="loader_background" cx="50" cy="50" r="20" stroke-width="3"/>
						      <circle class="loader_rotation" cx="50" cy="50" r="20" fill="none" stroke-width="4"/>
						      <path class="loader_path" d="m48,58l11,-16" stroke-dasharray="23" stroke-dashoffset="23"/>
						      <path class="loader_path" d="m48,58l-8,-6" stroke-dasharray="10" stroke-dashoffset="10"/>
						    </svg>
						  </div>
						</div>
					<div class="wpa-error">'. esc_html__( 'Please select some product options before adding this product to your cart.', 'wcpb' ) .'</div>
					<div class="wpa-message">'. esc_html__( 'Product bundle already add to cart, ', 'wcpb' ) .'<a href="'. wc_get_cart_url() .'" class="wc-forward">'. esc_html__( 'View cart', 'wcpb' ) .'</a></div>
					'. $button_inline_css .'
			</div>';
		}
		wp_reset_query();
	}


	/**
	 * get price after added filters
	 *
	 * @return  price number
	 */
	public static function raw_price_with_filter($singlePrice) {
		return str_replace(get_woocommerce_currency_symbol(), '', strip_tags(wc_price($singlePrice)));
	}
}

WPA_WCPB_Template_Hooks::init();