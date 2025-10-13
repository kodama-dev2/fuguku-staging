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
class WPA_WCPB_Frontend {
	/**
	 * Initialize.
	 *
	 * @return  void
	 */
	public static function init() {
		// Enqueue frontend assets
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

		// Add product to cart
		add_action( 'wp_ajax_wpa_wcpb_add_to_cart', array( __CLASS__, 'wpa_wcpb_add_to_cart' ) );
		add_action( 'wp_ajax_nopriv_wpa_wcpb_add_to_cart', array( __CLASS__, 'wpa_wcpb_add_to_cart' ) );

		// Update mini cart
		add_filter( 'wp_ajax_nopriv_wpa_wcpb_update_mini_cart', array( __CLASS__, 'wpa_wcpb_update_mini_cart' ) );
		add_filter( 'wp_ajax_wpa_wcpb_update_mini_cart', array( __CLASS__, 'wpa_wcpb_update_mini_cart' ) );

		// Change price of product bundle in cart
		add_action( 'woocommerce_before_calculate_totals', array( __CLASS__, 'wpa_wcpb_update_product_bundle_in_cart' ) );

		// Custom woocommerce template path
		add_filter( 'woocommerce_locate_template', array( __CLASS__, 'wpa_wcpb_woocommerce_locate_template' ), 10, 3 );

		// Change woocommerce checkout order detail
		add_action( 'woocommerce_add_order_item_meta', array( __CLASS__, 'wpa_wcpb_woocommerce_change_checkout_order_detail' ), 1, 3 );

		// add price after filter into variation data
		add_filter( 'woocommerce_available_variation', array( __CLASS__, 'wpa_wcpb_woocommerce_add_price_with_filter' ), 10, 3 );

		include_once( 'wcpb-template-hooks.php' );
	}

	/**
	 * Enqueue front assets.
	 *
	 * @return  void
	 */
	public static function enqueue_assets() {
		wp_enqueue_style( 'wpa-wcpb-frontend', WPA_WCPB_URL . 'assets/css/wcpb-frontend.css' );
		wp_enqueue_script( 'wpa-wcpb-frontend', WPA_WCPB_URL . 'assets/js/wcpb-frontend.js', array('jquery'), NULL, true );
		wp_localize_script( 'wpa-wcpb-frontend', 'wpa_wcpb', self::localize_script() );
	}

	/**
	 * Embed baseline script.
	 *
	 * @return  array
	 */
	public static function localize_script() {
		return array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'decimal_separator'  => wc_get_price_decimal_separator(),
			'thousand_separator' => wc_get_price_thousand_separator(),
			'currency_pos' => get_option( 'woocommerce_currency_pos' ),
			'_nonce' => wp_create_nonce( 'wpa-wcpb-nonce' ),
		);
	}

	/**
	 * Return my plugin path
	 *
	 * @return json
	 */
	public static function wpa_wcpb_plugin_path() {
		// Gets the absolute path to this plugin directory
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Message error
	 *
	 * @return
	 */
	public static function wpa_wcpb_message_error( $message = '' ) {
		if ( ! empty( $message ) ) {
			exit( json_encode( array( 'message' => $message ) ) );
		} else {
			exit( json_encode( array( 'message' => __( 'Data not isset.', 'wcpb' ) ) ) );
		}
	}

	/**
	 * Add product to cart
	 *
	 * @return
	 */
	public static function wpa_wcpb_add_to_cart() {
		// Check nonce
		if ( ! ( isset( $_POST['_nonce'] ) && wp_verify_nonce( $_POST['_nonce'], 'wpa-wcpb-nonce') ) ) {
			$message = __( 'The nonce check wrong.', 'wcpb' );
			self::wpa_wcpb_message_error( $message );
		}

		// Check isset data
		if ( ! isset( $_POST['list_product_id'] ) ) {
			self::wpa_wcpb_message_error();
		}

		$list_product_id 			= $_POST['list_product_id'];
		$main_pro_variable 			= $_POST['variable'];
		$main_pro_meta				= $_POST['main_pro_meta'];
		$custom_bundle_variable 	= $_POST['bundle_variable'];
		$arr_product_id  			= explode( ',', $list_product_id );

		if ( count( $arr_product_id ) > 0 && is_numeric( $arr_product_id[0] ) ) {
			$product_parent = wc_get_product( $arr_product_id[0] );
			if ( $product_parent ) {
				$parent_id = $product_parent->get_parent_id();
				$parent_id = $parent_id ? $parent_id : $product_parent->get_id();
				$product_bundle = get_post_meta( $parent_id, 'wpa_wcpb', true );
				if ($product_bundle) {
					self::wpa_wcpb_add_to_cart_total_discount( $arr_product_id, $product_bundle, $main_pro_variable, $custom_bundle_variable, $main_pro_meta );
				}
			}
		}
		die;
	}

	/**
	 * Add product to cart with total discount type
	 *
	 * @return
	 */
	public static function wpa_wcpb_add_to_cart_total_discount( $arr_product_id, $product_bundle, $variation_id, $custom_bundle_variable, $main_pro_meta ) {
		$main_product_id = $main_product_custom_price = $bundle_variation_id = 0;
		$bundle_product_added = $variable = $bundle_variable = $percent_arr = array();

		// Get main product
		if ( ! empty( $arr_product_id[0] ) ) {
			$mainProductID = $variation_id > 0 ? $variation_id : $arr_product_id[0];
			$main_product = wc_get_product( $mainProductID );
			if ( $main_product && $main_product->is_in_stock() ) {
				$main_product_id = $main_product->get_id();
				$main_product_custom_price = $main_product->get_price();
				if ( 'variation' === $main_product->get_type() ) {
					$variable  = $main_pro_meta;
				}
			}
		}

		if (is_array($product_bundle)) {
			foreach( $product_bundle as $key => $val ) {
				$percent_arr[] 			= $val['percent'];
				if ( in_array( $val['product_id'], $arr_product_id ) ) {
					$product 					= wc_get_product( $val['product_id'] );
                    $variation_product_id = $product->get_id();

					if ( $product && $product->is_in_stock() ) {
						$price 					= 0;
						$bundle_product_added[] = $variation_product_id;

                        if ( $product->is_type( 'variable' ) && ! empty( $val['variable'] ) && is_array($val['variable']) ) {

                            $variation_product 		= new WC_Product_Variable( $variation_product_id );
                            $price 						= $variation_product->get_price();

                            $variations = $variation_product->get_available_variations();

                            if ( !empty($variations) && !empty( $custom_bundle_variable[$variation_product_id]['variable'] ) ){
                                $variation_price_found = false;
                                foreach ($variations as $single_variation){

                                    foreach( $single_variation['attributes'] as $single_variation_attr_key => $single_variation_attr_value ){
                                       if ( !empty($custom_bundle_variable[$variation_product_id][$single_variation_attr_key]) && $custom_bundle_variable[$variation_product_id][$single_variation_attr_key] == $single_variation_attr_value){
                                           $price = (float)$single_variation['display_price'];
                                           $variation_price_found = true;
                                           break;
                                       }
                                    }
                                    if ($variation_price_found){
                                        break;
                                    }
                                }
                            }
                        } else {
                            $price 						= $product->get_price();
                        }
                        if ( isset( $custom_bundle_variable[$variation_product_id]['price'] ) && count( $custom_bundle_variable[$variation_product_id] ) > 0 ) {
                            $custom_bundle_variable[$variation_product_id]['price'] = $price;
                        }

						$main_product_custom_price 		= $main_product_custom_price + $price;
					}
				}
			}
		}
		if ( ! empty( $main_product_id ) ) {
			// Get percent of discount total price
			$percent = 0;
			sort( $percent_arr );
			for ( $i = count( $bundle_product_added ); $i >= 0 ; $i-- ) {
				if ( !empty( $percent_arr[$i-1] ) ) {
					$percent = $percent_arr[$i-1];
					break;
				} else {
					$percent = 0;
				}
			}

			// Set discount
			if ( count( $bundle_product_added ) > 0 && count( $bundle_product_added ) <= count( $product_bundle ) ) {
				$main_product_custom_price = $main_product_custom_price - $main_product_custom_price * $percent / 100;
			}

			$cart_item_data = array(
				'bundle-products' => implode( ",", $bundle_product_added ),
				'custom-price' => $main_product_custom_price,
				'custom-price-with-filter' =>  WPA_WCPB_Template_Hooks::raw_price_with_filter($main_product_custom_price),
				'bundle-variable' => $custom_bundle_variable,
			);

			try {
				$data = WC()->cart->add_to_cart( $main_product_id, '1', $variation_id, $variable, $cart_item_data );
			} catch (Exception $e) {
				if ( $e->getMessage() ) {
					wc_add_notice( $e->getMessage(), 'error' );
				}
				return false;
			}
		}

	}

	/**
	 * Add product to cart with discount per item type
	 *
	 * @return
	 */
	public static function wpa_wcpb_add_to_cart_discount_per_item( $arr_product_id, $product_bundle ) {
		$variation_id = 0;
		$variable = array();

		foreach ( $arr_product_id as $key => $product_id ) {
			$product = wc_get_product( $product_id );

			if ( $product && $product->is_in_stock() ) {
				$price = 0;
				foreach( $product_bundle as $key2 => $val ) {
					if ( $product_id == $val['product_id'] ) {
						if ( $product->is_type( 'variable' ) && ! empty( $val['variable'] ) ) {
							$variable = wp_unslash( $val['variable'] );
							if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
								$variation_id = $product->get_matching_variation( $variable );
							} else {
								$data_store   = WC_Data_Store::load( 'product' );
								$variation_id = $data_store->find_matching_product_variation( $product, $variable );
							}
							$available_variation = $product->get_available_variation( $variation_id );
							$price = $available_variation['display_price'];
						} else {
							$price = $product->get_price();
						}
						$price = $price - $price * $percent_arrange / 100;
					}
				}

				if( $key != 0 ) {
					$cart_item_data = array(
						'bundle-parent' => $arr_product_id[0],
						'custom-price' => $price,
						'custom-price-with-filter' => WPA_WCPB_Template_Hooks::raw_price_with_filter($price)
					);
				}

				try {
					WC()->cart->add_to_cart( $product->get_id(), '1', $variation_id, $variable, $cart_item_data );
				} catch (Exception $e) {
					if ( $e->getMessage() ) {
						wc_add_notice( $e->getMessage(), 'error' );
					}
					return false;
				}
			}
		}
	}

	/**
	 * Update product bundle in cart
	 *
	 * @return json
	 */
	public static function wpa_wcpb_update_product_bundle_in_cart( $cart_object ) {
		if ( count( $cart_object->cart_contents ) > 0 ) {
			if ( WPA_WCPB_Settings::get_product_bundle_type() == 'discount-per-item' ) {
				foreach ( $cart_object->cart_contents as $key => $cart_item ) {
					if ( ! empty( $cart_item['bundle-parent'] ) ) {
						$cart_item['data']->set_price( $cart_item['custom-price'] );
					}
				}
			} else {
				foreach ( $cart_object->cart_contents as $key => $cart_item ) {
					if ( ! empty( $cart_item['bundle-products'] ) ) {
						$cart_item['data']->set_price( $cart_item['custom-price'] );
					}
				}
			}
		}
	}

	/**
	 * Custom woocommerce template path
	 *
	 * @return string
	 */
	public static function wpa_wcpb_woocommerce_locate_template( $template, $template_name, $template_path ) {

		global $woocommerce;
		$_template = $template;

		if ( ! $template_path ) $template_path = $woocommerce->template_url;
		$plugin_path  = self::wpa_wcpb_plugin_path() . '/woocommerce/';

		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				$template_path . $template_name,
				$template_name
			)
		);

		// Modification: Get the template from this plugin, if it exists
		if ( ! $template && file_exists( $plugin_path . $template_name ) )
			$template = $plugin_path . $template_name;

		// Use default template
		if ( ! $template )
			$template = $_template;

		// Return what we found
		return $template;
	}

	/**
	 * Change checkout order detail
	 *
	 * @return
	 */
	public static function wpa_wcpb_woocommerce_change_checkout_order_detail( $item_id, $values, $cart ) {

		$custom_variable 	= $values['bundle-variable'];
		$bundles 			= get_post_meta( $values['product_id'], 'wpa_wcpb', true );
		$bundles_added 		= explode( ',', $values['bundle-products'] );
		$order_extra_meta_data 	= '';
		if ( $bundles && count( $bundles_added ) > 0 ) {
			foreach( $bundles as $key => $val ){

				if ( in_array( $val['product_id'], $bundles_added ) ) {

					$product_item 			= wc_get_product( intval( $val['product_id'] ) );
					if ( $product_item ) {
						$order_extra_meta_data .= '<li><a href="'. $product_item->get_permalink() .'" title="'. $product_item->get_title() .'">' . $product_item->get_title() . '</a> ';

						// Get variable
						if ( isset( $custom_variable[$val['product_id']] ) && count( $custom_variable[$val['product_id']] ) > 0 ) {
							// Get variable default of product bundle
							$order_extra_meta_data .= '<span class="db" style="text-transform: capitalize;">' . $custom_variable[$val['product_id']]['variable'] . '</span> ';
						} else {
							if ( ! empty( $val['variable'] ) ) {
								$i = 0;
								foreach ( $val['variable'] as $key => $value ) { $i++;
									if ( $i == count( $val['variable'] ) ) {
										$order_extra_meta_data .= '<span class="db" style="text-transform: capitalize;">' . substr( $key, 13 ) . ': ' . $value . '</span>';
									}else {
										$order_extra_meta_data .= '<span class="db" style="text-transform: capitalize;">' . substr( $key, 13 ) . ': ' . $value . '</span> + ';
									}
								}
							}
						}

						$order_extra_meta_data .= '</li>';
					}
				}
			}
			if ($order_extra_meta_data != '') {
				wc_add_order_item_meta( $item_id, esc_html__( 'Product Bundles', 'wcpb' ), '<ul style="clear: both;">'.$order_extra_meta_data.'</ul>' );
			}
		}
	}

	/**
	 * Update mini cart
	 *
	 * @return
	 */
	public static function wpa_wcpb_update_mini_cart() {
		echo wc_get_template( 'cart/mini-cart.php' );
		die();
	}

	/**
	 * add price after run filter
	 *
	 * @return data array with price after run filter
	 */
	public static function wpa_wcpb_woocommerce_add_price_with_filter($datas, $parent, $variation) {
		$datas['price_with_filter'] = WPA_WCPB_Template_Hooks::raw_price_with_filter($datas['display_price']);
		return $datas;
	}
}
WPA_WCPB_Frontend::init();
