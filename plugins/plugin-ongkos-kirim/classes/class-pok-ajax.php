<?php

/**
 * POK Ajax class
 */
class POK_Ajax {

	/**
	 * POK core
	 * 
	 * @var POK_Core
	 */
	protected $core;

	/**
	 * POK Helper
	 * 
	 * @var POK_Helper
	 */
	protected $helper;

	/**
	 * POK Setting
	 * 
	 * @var POK_Setting
	 */
	protected $setting;

	/**
	 * POK Logs
	 * 
	 * @var POK_Logs
	 */
	protected $logs;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $pok_core;
		global $pok_helper;
		$this->core = $pok_core;
		$this->helper = $pok_helper;
		$this->setting = new POK_Setting();

		add_action( 'wp_ajax_pok_get_list_city', array( $this, 'get_list_city' ) );
		add_action( 'wp_ajax_nopriv_pok_get_list_city', array( $this, 'get_list_city' ) );

		add_action( 'wp_ajax_pok_get_list_district', array( $this, 'get_list_district' ) );
		add_action( 'wp_ajax_nopriv_pok_get_list_district', array( $this, 'get_list_district' ) );

		add_action( 'wp_ajax_pok_get_list_service', array( $this, 'get_list_service' ) );
		add_action( 'wp_ajax_nopriv_pok_get_list_service', array( $this, 'get_list_service' ) );

		add_action( 'wp_ajax_pok_change_country', array( $this, 'change_country' ) );
		add_action( 'wp_ajax_nopriv_pok_change_country', array( $this, 'change_country' ) );

		add_action( 'wp_ajax_pok_get_cost', array( $this, 'get_cost' ) );
		add_action( 'wp_ajax_nopriv_pok_get_cost', array( $this, 'get_cost' ) );
		add_action( 'wp_ajax_pok_get_estimated_cost', array( $this, 'get_estimated_cost' ) );
		add_action( 'wp_ajax_nopriv_pok_get_estimated_cost', array( $this, 'get_estimated_cost' ) );

		add_action( 'wp_ajax_pok_checker', array( $this, 'checker' ) );

		add_action( 'wp_ajax_pok_search_city', array( $this, 'search_city' ) );
		add_action( 'wp_ajax_nopriv_pok_search_city', array( $this, 'search_city' ) );
		add_action( 'wp_ajax_pok_search_simple_address', array( $this, 'search_simple_address' ) );
		add_action( 'wp_ajax_nopriv_pok_search_simple_address', array( $this, 'search_simple_address' ) );

		add_action( 'wp_ajax_pok_set_rajaongkir_api_key', array( $this, 'set_rajaongkir_api_key' ) );
		add_action( 'wp_ajax_pok_nopriv_set_rajaongkir_api_key', array( $this, 'set_rajaongkir_api_key' ) );
		add_action( 'wp_ajax_pok_set_rajaongkir2_api_key', array( $this, 'set_rajaongkir2_api_key' ) );
		add_action( 'wp_ajax_pok_nopriv_set_rajaongkir2_api_key', array( $this, 'set_rajaongkir2_api_key' ) );

		add_action( 'wp_ajax_pok_insert_order_shipping', array( $this, 'add_order_shipping' ) );
		add_action( 'wp_ajax_pok_change_order_shipping', array( $this, 'switch_order_shipping' ) );

		add_action( 'wp_ajax_pok_check_fixer_api', array( $this, 'check_fixer_api' ) );
		add_action( 'wp_ajax_pok_check_currencylayer_api', array( $this, 'check_currencylayer_api' ) );

		add_action( 'wp_ajax_pok_simulate_api', array( $this, 'simulate_api' ) );

		add_action( 'wp_ajax_pok_change_profile_country', array( $this, 'change_profile_country' ) );

		add_action( 'wp_ajax_pok_check_ip', array( $this, 'check_my_ip' ) );

		$this->logs = new TJ_Logs( POK_LOG_NAME );
	}

	/**
	 * Set rajaongkir API key
	 */
	public function set_rajaongkir_api_key() {
		check_ajax_referer( 'set_rajaongkir_api_key', 'pok_action' );
		$api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : ''; // Input var okay.
		$api_type = isset( $_POST['api_type'] ) ? sanitize_text_field( wp_unslash( $_POST['api_type'] ) ) : ''; // Input var okay.
		$check = $this->core->get_rajaongkir_status( $api_key, $api_type );
		if ( true === $check ) {
			$this->core->delete_cache( 'courier' );
			$this->setting->set( 'rajaongkir_key', $api_key );
			$this->setting->set( 'rajaongkir_type', $api_type );
			$this->setting->set( 'rajaongkir_status', array( true, 'API Key Active' ) );
			$this->core->purge_cache( 'cost' );
			$this->setting->set( 'base_api', 'rajaongkir' );
			$this->setting->set( 'store_location', array() );
			$this->setting->set( 'couriers', $this->core->get_courier() );
			echo 'success';
		} else {
			if ( '' === $api_key ) {
				esc_html_e( 'API key is empty', 'pok' );
			} elseif ( ! in_array( $api_type, array( 'starter', 'basic', 'pro' ), true ) ) {
				esc_html_e( 'API type is not valid', 'pok' );
			} elseif ( false !== $check ) {
				echo esc_html( $check );
			} else {
				esc_html_e( 'API Key is not valid', 'pok' );
			}
		}
		die;
	}

	/**
	 * Set rajaongkir v2 API key
	 */
	public function set_rajaongkir2_api_key() {
		check_ajax_referer( 'set_rajaongkir2_api_key', 'pok_action' );
		$api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : ''; // Input var okay.
		$check = $this->core->get_rajaongkir2_status( $api_key );
		if ( true === $check ) {
			$this->core->delete_cache( 'courier' );
			$this->setting->set( 'rajaongkir2_key', $api_key );
			$this->setting->set( 'rajaongkir2_status', array( true, 'API Key Active' ) );
			$this->core->purge_cache( 'cost' );
			$this->setting->set( 'base_api', 'rajaongkir2' );
			$this->setting->set( 'store_location', array() );
			$this->setting->set( 'couriers', $this->core->get_courier() );
			echo 'success';
		} else {
			if ( '' === $api_key ) {
				esc_html_e( 'API key is empty', 'pok' );
			} elseif ( false !== $check ) {
				echo esc_html( $check );
			} else {
				esc_html_e( 'API Key is not valid', 'pok' );
			}
		}
		die;
	}

	/**
	 * Search city
	 */
	public function search_city() {
		check_ajax_referer( 'search_city', 'pok_action' );
		$search = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : ''; // Input var okay.
		if ( 'nusantara' === $this->setting->get( 'base_api' ) ) {
			$result = $this->core->search_city( $search );
			$return = array();
			if ( isset( $result ) && ! empty( $result ) ) {
				foreach ( $result as $res ) {
					$return[] = array(
						'id'    => $res->id,
						'text'  => $res->type . ' ' . $res->nama . ', ' . $res->provinsi,
					);
				}
			}
		} elseif ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) {
			$cities = $this->core->get_all_city();
		}
		echo wp_json_encode( $return );
		exit();
	}

	/**
	 * Search city for simple address field
	 */
	public function search_simple_address() {
		check_ajax_referer( 'search_city', 'pok_action' );
		$search = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : ''; // Input var okay.
		$result = $this->core->search_simple_address( $search );
		$return = array();
		if ( isset( $result ) && ! empty( $result ) ) {
			foreach ( $result as $id => $res ) {
				$return[] = array(
					'id'    => $id,
					'text'  => $res,
				);
			}
		}
		echo wp_json_encode( $return );
		exit();
	}

	/**
	 * Change country on checkout page
	 */
	public function change_country() {
		check_ajax_referer( 'change_country', 'pok_action' );
		$new_value  = isset( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : 'ID'; // Input var okay.
		$context    = isset( $_POST['context'] ) ? sanitize_text_field( wp_unslash( $_POST['context'] ) ) : 'billing'; // Input var okay.
		$customer = maybe_unserialize( WC()->session->get( 'customer' ) );
		if ( 'billing' === $context ) {
			$session_name = 'country';
		} else {
			$session_name = 'shipping_country';
		}
		$old_value  = isset( $customer[ $session_name ] ) ? $customer[ $session_name ] : 'ID';
		if ( $old_value !== $new_value ) {
			$customer[ $session_name ] = $new_value;
			WC()->session->set( 'customer', maybe_serialize( $customer ) );
			if ( 'ID' === $old_value || 'ID' === $new_value ) {
				echo 'reload';
			}
		}
		die();
	}

	/**
	 * Get list city
	 */
	public function get_list_city() {
		check_ajax_referer( 'get_list_city', 'pok_action' );
		$province_id = isset( $_POST['province_id'] ) ? sanitize_text_field( wp_unslash( $_POST['province_id'] ) ) : 0; // Input var okay.
		$city = $this->core->get_city( $province_id );
		$r_city = array();

		if ( is_array( $city ) ) {
			foreach ( $city as $key => $value ) {
				$r_city[ $key ] = $value;
			}
		}

		wp_send_json( $r_city );
		wp_die();
	}

	/**
	 * Get list district
	 */
	public function get_list_district() {
		check_ajax_referer( 'get_list_district', 'pok_action' );
		$city_id    = isset( $_POST['city_id'] ) ? sanitize_text_field( wp_unslash( $_POST['city_id'] ) ) : 0; // Input var okay.
		$city       = $this->core->get_district( $city_id );
		$r_city     = array();

		if ( is_array( $city ) ) {
			foreach ( $city as $key => $value ) {
				$r_city[ $key ] = $value;
			}
		}

		wp_send_json( $r_city );
		wp_die();
	}

	/**
	 * Get list courier service
	 */
	public function get_list_service() {
		check_ajax_referer( 'get_list_service', 'pok_action' );
		$courier    = isset( $_POST['courier'] ) ? sanitize_text_field( wp_unslash( $_POST['courier'] ) ) : ''; // Input var okay.
		$services 	= array();
		$api_services = $this->core->get_courier_service();
		if ( isset( $api_services[ $courier ] ) ) {
			foreach ( $api_services[ $courier ] as $key => $service ) {
				$services[ $key ] = $service['name'];
			}
		}

		echo wp_json_encode( $services );
		wp_die();
	}

	/**
	 * Get list district
	 */
	public function get_cost() {
		check_ajax_referer( 'get_cost', 'pok_action' );
		$destination    = isset( $_POST['destination'] ) ? sanitize_text_field( wp_unslash( $_POST['destination'] ) ) : 0; // Input var okay.
		$order_id       = isset( $_POST['order_id'] ) ? intval( $_POST['order_id'] ) : 0; // Input var okay.
		$order          = wc_get_order( $order_id );
		$weight         = isset( $_POST['weight'] ) ? floatval( $_POST['weight'] ) : $this->helper->get_order_weight( $order ); // Input var okay.
		$origin         = isset( $_POST['origin'] ) ? intval( $_POST['origin'] ) : 0; // Input var okay.
		$courier        = isset( $_POST['courier'] ) ? explode( ':', $_POST['courier'] ) : array(); // Input var okay.

		$result         = $this->core->get_cost( $destination, $weight, $origin, $courier );

		$enable_insurance       = $this->helper->is_enable_insurance( $order );
		$enable_timber_packing  = $this->helper->is_enable_timber_packing( $order );

		$costs = array();
		if ( $result ) {
			foreach ( $result as $cost ) {
				$meta = array(
					'created_by_pok' => true,
					'courier'   => $cost['courier'],
					'etd'       => $cost['time'],
				);

				// add timber packing.
				if ( true === $enable_timber_packing ) {
					$meta['timber_packing'] = apply_filters( 'pok_timber_packing_fee', floatval( $this->setting->get( 'timber_packing_multiplier' ) ) * $cost['cost'], $cost['courier'] );
					$cost['cost'] += $meta['timber_packing'];
				}

				// add insurance fee.
				if ( true === $enable_insurance ) {
					$meta['insurance'] = $this->helper->get_insurance( $cost['courier'], $order->get_subtotal() );
					$cost['cost'] += $meta['insurance'];
				}

				// cost markup.
				$markups = $this->setting->get( 'markup' );
				if ( ! empty( $markups ) && is_array( $markups ) ) {
					foreach ( $markups as $markup ) {
						if ( '' === $markup['courier'] || $cost['courier'] === $markup['courier'] ) {
							if ( 'rajaongkir' === $this->setting->get('base_api') || ! isset( $markup['service'] ) || '' === $markup['service'] || ( $markup['service'] === sanitize_title( $cost['service'] ) ) ) {
								if ( ! isset( $markup['amount'] ) || empty( $markup['amount'] ) ) {
									$markup['amount'] = 0;
								}
								$cost['cost'] += apply_filters( 'pok_custom_markup', floatval( $markup['amount'] ), $cost );
								if ( 0 > $cost['cost'] ) {
									$cost['cost'] = 0;
								}
							}
						}
					}
				}

				$meta['service']        = $cost['service'];
				$cost['cost']           = $this->helper->currency_convert( $cost['cost'] );
				$cost['courier_name']   = $this->helper->get_courier_name( $cost['courier'] );
				$cost['cost_display']   = wc_price( $this->helper->currency_convert( $cost['cost'] ) );
				$cost['label']          = $cost['courier_name'] . ' - ' . $cost['service'];
				$cost['meta']           = $meta;
				$costs[]                = $cost;
			}
		}
		echo wp_json_encode( $costs );
		wp_die();
	}

	/**
	 * Get list district
	 */
	public function get_estimated_cost() {
		check_ajax_referer( 'get_cost', 'pok_action' );
		$destination    = isset( $_POST['destination'] ) ? intval( $_POST['destination'] ) : 0; // Input var okay.
		$origin    		= isset( $_POST['origin'] ) ? intval( $_POST['origin'] ) : 0; // Input var okay.
		$qty 			= isset( $_POST['qty'] ) ? intval( $_POST['qty'] ) : 0; // Input var okay.
		$product_id 	= isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0; // Input var okay.
		$product 		= wc_get_product( $product_id );

		$weight         = $qty * $this->helper->get_product_weight( $product );
		$courier        = apply_filters( 'pok_shipping_estimation_courier', array(), $product );
		
		$rates         	= $this->core->get_cost( $destination, $weight, $origin, $courier );
		$custom_costs   = $this->core->get_custom_cost( $destination, 'district', $weight );
		if ( 'replace' === $this->setting->get( 'custom_cost_type' ) && ! empty( $custom_costs ) ) {
			$rates = $custom_costs;
		} else {
			if ( ! empty( $custom_costs ) ) {
				$rates = array_merge( $custom_costs, $rates );
			}
		}

		$costs = array();
		if ( $rates ) {
			$html = '<table class="pok-shipping-estimation-result">';
			$result = array();
			foreach ( $rates as $rate ) {
				if ( 'nusantara' === $this->setting->get( 'base_api' ) && 'yes' === $this->setting->get( 'specific_service' ) && ( ! isset( $rate['source'] ) || 'custom' !== $rate['source'] ) && apply_filters( 'pok_rates_apply_filter', true, $rate, $product ) ) {
					if ( ! in_array( sanitize_title( $rate['class'] ), $this->setting->get( 'specific_service_option' ), true ) ) {
						continue;
					}
				}
				// cost markup.
				$markups = $this->setting->get( 'markup' );
				if ( ! empty( $markups ) && is_array( $markups ) ) {
					foreach ( $markups as $markup ) {
						if ( '' === $markup['courier'] || $rate['courier'] === $markup['courier'] ) {
							if ( 'rajaongkir' === $this->setting->get( 'base_api' ) || ! isset( $markup['service'] ) || '' === $markup['service'] || ( $markup['service'] === sanitize_title( $rate['service'] ) ) ) {
								if ( ! isset( $markup['amount'] ) || empty( $markup['amount'] ) ) {
									$markup['amount'] = 0;
								}
								$rate['cost'] += apply_filters( 'pok_custom_markup', floatval( $markup['amount'] ), $rate );
								if ( 0 > $rate['cost'] ) {
									$rate['cost'] = 0;
								}
							}
						}
					}
				}
				if ( ! isset( $result[ $rate['courier'] ] ) ) {
					$result[ $rate['courier'] ] = array();
				}
				$result[ $rate['courier'] ][] = $rate;
			}
			foreach ( $result as $courier => $costs ) {
				$html .= '<tr>
					<th class="pok-logo">';
				if ( isset( $costs[0]['source'] ) && 'custom' === $costs[0]['source'] ) {
					$html .= esc_attr( $courier );
				} else {
					$html .= '<img src="'. $this->helper->get_courier_logo( $courier ) . '" alt="' . esc_attr( $courier ) . '">';
				}
				$html .= '</th>
					<td class="pok-services">
						<table>';
							foreach ( $costs as $cost) {
								if ( $custom_name = $this->helper->get_custom_service_name( $cost['courier'], $cost['service'] ) ) {
									$service = $custom_name;
								} else {
									$service = $cost['service'];
								}

								if( 'Q9 Barang' === $cost['service'] ) {
									$cost['time'] = 1;
								}

								$html .= '<tr>
									<td class="pok-service">' . esc_html( $service ) . '</td>
									<td class="pok-cost">' . wc_price( $this->helper->currency_convert( $cost['cost'] ) ) . '<h5>' . $this->helper->format_etd( $cost['time'] ) . '</h5></td>
								</tr>';
							}
				$html .= '</table>
					</td>
				</tr>';
			}
			$html .= '</table>';
			wp_send_json( array(
				'found'		=> count( $result ),
				'html'		=> $html
			) );
		} else {
			wp_send_json( array(
				'found'		=> 0,
				'html'		=> '<p>' . __( "Sorry, no shipping cost found for the destination.", 'pok' ) . '</p>'
			) );
		}
	}

	/**
	 * Get list district
	 */
	public function checker() {
		check_ajax_referer( 'check_cost', 'pok_action' );
		$params = array();
		parse_str( $_POST['data'], $params );
		if ( 'pro' === $params['license_type'] ) {
			$destination    	= isset( $params['district'] ) ? intval( $params['district'] ) : 0; // Input var okay.
			$destination_type 	= 'district';
		} else {
			$destination    = isset( $params['city'] ) ? intval( $params['city'] ) : 0; // Input var okay.
			$destination_type 	= 'city';
		}
		$origin 		= isset( $params['origin'] ) ? intval( $params['origin'] ) : 0; // Input var okay.
		$weight 		= isset( $params['weight'] ) ? intval( $params['weight'] ) : 0; // Input var okay.
		$courier 		= isset( $params['courier'] ) ? $params['courier'] : array( 'jne' ); // Input var okay.
		
		$rates         	= $this->core->get_cost( $destination, $weight, $origin, $courier );
		$custom_costs   = $this->core->get_custom_cost( $destination, $destination_type, $weight );
		if ( 'replace' === $this->setting->get( 'custom_cost_type' ) && ! empty( $custom_costs ) ) {
			$rates = $custom_costs;
		} else {
			if ( ! empty( $custom_costs ) ) {
				$rates = array_merge( $custom_costs, $rates );
			}
		}

		$costs = array();
		if ( $rates ) {
			$html = '<table class="pok-checker-result">';
			$html .= '<thead><th class="pok-logo">Courier</th><th class="pok-services"><table><tbody><td class="pok-service">Service</td><td class="pok-cost">Cost</td><td class="pok-etd">Etd</td></tbody></table></th></thead><tbody>';
			$result = array();
			foreach ( $rates as $rate ) {

				// cost markup.
				$markups = $this->setting->get( 'markup' );
				if ( ! empty( $markups ) && is_array( $markups ) ) {
					foreach ( $markups as $markup ) {
						if ( '' === $markup['courier'] || $rate['courier'] === $markup['courier'] ) {
							if ( 'rajaongkir' === $this->setting->get( 'base_api' ) || ! isset( $markup['service'] ) || '' === $markup['service'] || ( $markup['service'] === sanitize_title( $rate['service'] ) ) ) {
								if ( ! isset( $markup['amount'] ) || empty( $markup['amount'] ) ) {
									$markup['amount'] = 0;
								}
								$rate['cost'] += apply_filters( 'pok_custom_markup', floatval( $markup['amount'] ), $rate );
								if ( 0 > $rate['cost'] ) {
									$rate['cost'] = 0;
								}
							}
						}
					}
				}
				if ( ! isset( $result[ $rate['courier'] ] ) ) {
					$result[ $rate['courier'] ] = array();
				}
				$result[ $rate['courier'] ][] = $rate;
			}
			foreach ( $result as $courier => $costs ) {
				$html .= '<tr>
					<th class="pok-logo">';
				if ( isset( $costs[0]['source'] ) && 'custom' === $costs[0]['source'] ) {
					$html .= esc_attr( $courier );
				} else {
					$html .= '<img src="'. $this->helper->get_courier_logo( $courier ) . '" alt="' . esc_attr( $courier ) . '">';
				}
				$html .= '</th>
					<td class="pok-services">
						<table>';
							foreach ( $costs as $cost) {
								if ( $custom_name = $this->helper->get_custom_service_name( $cost['courier'], $cost['service'] ) ) {
									$service = $custom_name;
								} else {
									$service = $cost['service'];
								}

								if( 'Q9 Barang' === $cost['service'] ) {
									$cost['time'] = 1;
								}

								$html .= '<tr>
									<td class="pok-service">' . esc_html( $service ) . '</td>
									<td class="pok-cost">' . wc_price( $this->helper->currency_convert( $cost['cost'] ) ) . '</td>
									<td class="pok-etd">' . ( ! empty( $cost['time'] ) ? $this->helper->format_etd( $cost['time'] ) : '' ) . '</td>
								</tr>';
							}
				$html .= '</table>
					</td>
				</tr>';
			}
			$html .= '</tbody></table>';
			wp_send_json( array(
				'found'		=> count( $result ),
				'html'		=> $html
			) );
		} else {
			wp_send_json( array(
				'found'		=> 0,
				'html'		=> '<p class="no-result">' . __( "Sorry, no shipping cost found for the destination.", 'pok' ) . '</p>'
			) );
		}
		exit;
	}

	/**
	 * Add order shipping on admin
	 */
	public function add_order_shipping() {
		check_ajax_referer( 'set_order_shipping', 'pok_action' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}
		try {
			$order_id   = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0; // Input var okay.
			$order      = wc_get_order( $order_id );
			$label      = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : 'Shipping'; // Input var okay.
			$cost       = isset( $_POST['cost'] ) ? floatval( $_POST['cost'] ) : 0; // Input var okay.
			$meta       = isset( $_POST['meta'] ) ? wp_unslash( $_POST['meta'] ) : array(); // Input var okay.

			$order_taxes      = $order->get_taxes();
			$shipping_methods = WC()->shipping() ? WC()->shipping->load_shipping_methods() : array();

			// Add new shipping.
			$item = new WC_Order_Item_Shipping();
			$item->set_shipping_rate( new WC_Shipping_Rate( 'pok-' . uniqid(), $label, $cost ) );
			$item->set_order_id( $order_id );
			foreach ( $meta as $key => $value ) {
				$item->add_meta_data( $key, $value );
			}
			$item_id = $item->save();

			ob_start();
			include WC()->plugin_path() . '/includes/admin/meta-boxes/views/html-order-shipping.php';

			wp_send_json_success(
				array(
					'html' => ob_get_clean(),
				)
			);

		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 * Add order shipping on admin
	 */
	public function switch_order_shipping() {
		check_ajax_referer( 'set_order_shipping', 'pok_action' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}
		try {
			$order_id   = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0; // Input var okay.
			$order      = wc_get_order( $order_id );
			$item_id    = isset( $_POST['item_id'] ) ? absint( $_POST['item_id'] ) : 0; // Input var okay.
			$label      = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : 'Shipping'; // Input var okay.
			$cost       = isset( $_POST['cost'] ) ? floatval( $_POST['cost'] ) : 0; // Input var okay.
			$meta       = isset( $_POST['meta'] ) ? wp_unslash( $_POST['meta'] ) : array(); // Input var okay.

			// remove old shipping.
			wc_delete_order_item( $item_id );

			$order_taxes      = $order->get_taxes();
			$shipping_methods = WC()->shipping() ? WC()->shipping->load_shipping_methods() : array();

			// Add new shipping.
			$item = new WC_Order_Item_Shipping();
			$item->set_shipping_rate( new WC_Shipping_Rate( 'pok-' . uniqid(), $label, $cost ) );
			$item->set_order_id( $order_id );
			foreach ( $meta as $key => $value ) {
				$item->add_meta_data( $key, $value );
			}
			$item_id = $item->save();

			ob_start();
			include WC()->plugin_path() . '/includes/admin/meta-boxes/views/html-order-shipping.php';

			wp_send_json_success(
				array(
					'html' => ob_get_clean(),
				)
			);

		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 * Get Fixer API status
	 *
	 * @return mixed API response
	 */
	public function check_fixer_api() {
		check_ajax_referer( 'check_fixer_api', 'pok_action' );
		$api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : ''; // Input var okay.
		$check = $this->core->get_fixer_status( $api_key );
		if ( true === $check ) {
			echo 'API active';
		} else {
			echo esc_html( $check );
		}
		die;
	}

	/**
	 * Get Currency Layer API status
	 *
	 * @return mixed API response
	 */
	public function check_currencylayer_api() {
		check_ajax_referer( 'check_currencylayer_api', 'pok_action' );
		$api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : ''; // Input var okay.
		$check = $this->core->get_currencylayer_status( $api_key );
		if ( true === $check ) {
			echo 'API active';
		} else {
			echo esc_html( $check );
		}
		die;
	}

	/**
	 * Change country on profile page
	 */
	public function change_profile_country() {
		check_ajax_referer( 'change_country', 'pok_action' );
		$new_value  = isset( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : 'ID'; // Input var okay.
		$context    = isset( $_POST['context'] ) ? sanitize_text_field( wp_unslash( $_POST['context'] ) ) : 'billing'; // Input var okay.
		$user_id    = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0; // Input var okay.

		update_user_meta( $user_id, $context . '_country', $new_value );
		echo 'reload';
		die();
	}

	/**
	 * Simulate disable API
	 */
	public function simulate_api() {
		check_ajax_referer( 'simulate_disable_api', 'pok_action' );
		$do = isset( $_POST['do'] ) ? sanitize_text_field( wp_unslash( $_POST['do'] ) ) : 'disable_tonjoo'; // Input var okay.
		$explode = explode( '_', $do );
		if ( 'disable' === $explode[0] ) {
			$set = 'yes';
		} else {
			$set = 'no';
		}
		if ( in_array( $explode[1], array( 'tonjoo', 'rajaongkir' ), true ) ) {
			$this->setting->set( 'temp_disable_api_' . $explode[1], $set );
			echo 'temp_disable_api_' . $explode[1];
		}
		echo 'success';
		die();
	}

	/**
	 * Check My IP
	 */
	public function check_my_ip() {
		$response = wp_remote_get( 'https://pluginongkoskirim.com/response.php' );
		if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
			echo wp_remote_retrieve_body( $response );
		} else {
			echo "Error " . wp_remote_retrieve_response_code( $response );
		}
		die();
	}

}
