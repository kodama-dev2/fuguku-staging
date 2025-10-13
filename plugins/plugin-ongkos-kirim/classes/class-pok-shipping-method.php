<?php

/**
 * POK Shipping Method
 */
class POK_Shipping_Method extends WC_Shipping_Method {

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
	 * POK License
	 * 
	 * @var POK_License
	 */
	protected $license;

	/**
	 * License type
	 * 
	 * @var string
	 */
	protected $type;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $pok_helper;
		global $pok_core;
		$this->id                   = 'plugin_ongkos_kirim';
		$this->method_title         = __( 'Plugin Ongkos Kirim', 'pok' );
		$this->method_description   = __( 'Shipping Method for Indonesia Marketplace', 'pok' );
		$this->enabled              = 'yes';
		$this->title                = __( 'Plugin Ongkos Kirim', 'pok' );
		$this->core                 = $pok_core;
		$this->setting              = new POK_Setting();
		$this->helper               = $pok_helper;
		$this->type                 = $this->helper->get_license_type();
		add_action( 'pok_calculate_shipping', array( $this, 'pok_calculate_shipping' ), 30 );
	}

	/**
	 * Display admin options
	 */
	public function admin_options() {
		include_once POK_PLUGIN_PATH . 'views/setting-wc.php';
	}

	/**
	 * Calculate shipping cost
	 *
	 * @param  array $package Packages.
	 */
	public function calculate_shipping( $package = array() ) {
		global $woocommerce;

		$rates = array();
		$final_rates = array();

		if ( ! $this->helper->is_plugin_active() ) {
			return false;
		}

		if ( empty( $package ) ) {
			return false;
		}

		// clear all cached WC's shipping costs.
		$this->helper->clear_cached_costs();

		do_action( 'pok_calculate_shipping', $package, $this );

	}

	/**
	 * POK's Calculate Shipping cost
	 *
	 * @param  array $package Packages.
	 */
	public function pok_calculate_shipping( $package ) {
		$destination = $package['destination'];

		if ( $this->helper->is_let_user_decide_insurance() ) {
			$user_insurance = 0;
			if ( isset( $_POST['post_data'] ) ) { // checkout page.
				$user_insurance = $this->get_checkout_post_data( 'billing_insurance' );
			} elseif ( isset( $_POST['billing_insurance'] ) ) { // order detail (after checkout).
				$user_insurance = sanitize_text_field( wp_unslash( $_POST['billing_insurance'] ) );
			}
			if ( 1 === intval( $user_insurance ) ) {
				$enable_insurance = true;
			} else {
				$enable_insurance = false;
			}
		} else {
			$enable_insurance = $this->helper->is_enable_insurance( $package['contents'] );
		}
		$enable_timber_packing = $this->helper->is_enable_timber_packing( $package['contents'] );

		if ( ! isset( $package['weight'] ) ) {
			$weight = $this->helper->get_total_weight( $package['contents'] );
		} else {
			$weight = $package['weight'];
		}

		if ( 'ID' === $destination['country'] ) {
			// get destination.
			if ( 'pro' === $this->type ) { // get district (not provided by WC by default).
				if ( isset( $_POST['post_data'] ) ) { // checkout page.
					if ( '1' === $this->get_checkout_post_data( 'ship_to_different_address' ) ) {
						$city     = $this->get_checkout_post_data( 'shipping_pok_city' );
						$district = $this->get_checkout_post_data( 'shipping_pok_district' );
						$state    = $this->get_checkout_post_data( 'shipping_state' );
					} else {
						$city     = $this->get_checkout_post_data( 'billing_pok_city' );
						$district = $this->get_checkout_post_data( 'billing_pok_district' );
						$state    = $this->get_checkout_post_data( 'billing_state' );
					}
				} else { // order detail (after checkout).
					if ( $this->helper->is_use_simple_address_field() ) {
						if ( isset( $_POST['shipping_pok_city'] ) && ! empty( $_POST['shipping_pok_city'] ) ) {
							$exp = explode( '_', $_POST['shipping_pok_city'] );
							$district = $exp[0];
							$city     = $exp[1];
							$state    = $exp[2];
						} elseif ( isset( $_POST['billing_pok_city'] ) && ! empty( $_POST['billing_pok_city'] ) ) {
							$exp = explode( '_', $_POST['billing_pok_city'] );
							$district = $exp[0];
							$city     = $exp[1];
							$state    = $exp[2];
						}
					} else {
						if ( isset( $_POST['shipping_pok_district'] ) && ! empty( $_POST['shipping_pok_district'] ) ) {
							$city     = sanitize_text_field( wp_unslash( $_POST['shipping_pok_city'] ) );
							$district = sanitize_text_field( wp_unslash( $_POST['shipping_pok_district'] ) );
							$state    = sanitize_text_field( wp_unslash( $_POST['shipping_state'] ) );
						} elseif ( isset( $_POST['billing_pok_district'] ) && ! empty( $_POST['billing_pok_district'] ) ) {
							$city     = sanitize_text_field( wp_unslash( $_POST['billing_pok_city'] ) );
							$district = sanitize_text_field( wp_unslash( $_POST['billing_pok_district'] ) );
							$state    = sanitize_text_field( wp_unslash( $_POST['billing_state'] ) );
						}
					}
				}
				if ( ! empty( $state ) ) {
					$destination['state'] = intval( $state );
				}
				if ( ! empty( $city ) ) {
					$destination['city'] = intval( $city );
				}
				if ( ! empty( $district ) ) {
					$destination['district'] = intval( $district );
					$destination_id = intval( $district );
				}
				$destination_type = 'district';
				if ( 'rajaongkir2' === $this->setting->get('base_api') && ! empty( $city ) ) {
					$destination_id = intval( $city );
					$destination_type = 'city';
				}
			} else {
				$city = 0;
				if ( isset( $_POST['post_data'] ) ) { // checkout page.
					if ( '1' === $this->get_checkout_post_data( 'ship_to_different_address' ) ) {
						$city = $this->get_checkout_post_data( 'shipping_pok_city' );
					} else {
						$city = $this->get_checkout_post_data( 'billing_pok_city' );
					}
				} else { // order detail (after checkout).
					if ( isset( $_POST['shipping_pok_city'] ) && ! empty( $_POST['shipping_pok_city'] ) ) {
						$city = sanitize_text_field( wp_unslash( $_POST['shipping_pok_city'] ) );
					} elseif ( isset( $_POST['billing_pok_city'] ) && ! empty( $_POST['billing_pok_city'] ) ) {
						$city = sanitize_text_field( wp_unslash( $_POST['billing_pok_city'] ) );
					}
				}
				$destination_id = intval( $city );
				$destination_type = 'city';
			}
			// get costs.
			if ( isset( $destination_type ) && ! empty( $destination_id ) ) {
				$rates          = $this->core->get_cost( $destination_id, $weight, ( isset( $package['origin'] ) ? intval( $package['origin'] ) : 0 ), apply_filters( 'pok_calculate_cost_couriers', array(), $package ) );
				$custom_costs   = $this->core->get_custom_cost( $destination, $destination_type, $weight );
				if ( 'replace' === $this->setting->get( 'custom_cost_type' ) && ! empty( $custom_costs ) ) {
					$rates = $custom_costs;
				} else {
					if ( ! empty( $custom_costs ) ) {
						$rates = array_merge( $custom_costs, $rates );
					}
				}
			}
		} elseif ( 'rajaongkir' === $this->setting->get( 'base_api' ) && 'starter' !== $this->setting->get( 'rajaongkir_type' ) && 'yes' === $this->setting->get( 'international_shipping' ) ) { // international shipping.
			$country_name = WC()->countries->countries[ $destination['country'] ];
			$country_data = $this->core->get_all_country();
			$destination_id = array_search( $this->helper->rajaongkir_country_name( $country_name ), $country_data, true );
			if ( $destination_id ) {
				$rates = $this->core->get_cost_international( $destination_id, $weight, ( isset( $package['origin'] ) ? intval( $package['origin'] ) : 0 ), apply_filters( 'pok_calculate_cost_couriers', array(), $package ) );
			}
		}

		if ( ! empty( $rates ) ) {

			// allow 3rd parties to filter result form api.
			$rates = apply_filters( 'pok_rates', $rates, $package );

			foreach ( $rates as $rate ) {
				$final_rate = array();
				$meta = array(
					'created_by_pok' => true,
					'courier'   => $rate['courier'],
					'etd'       => $rate['time'],
					'weight'    => $weight,
					'service'   => isset( $rate['service'] ) ? $rate['service'] : $rate['service'],
				);

				// if filter courier active.
				if ( 'yes' === $this->setting->get( 'specific_service' ) && ( ! isset( $rate['source'] ) || 'custom' !== $rate['source'] ) && apply_filters( 'pok_rates_apply_filter', true, $rate, $package ) ) {
					if ( ! in_array( sanitize_title( $rate['class'] ), $this->setting->get( 'specific_service_option' ), true ) ) {
						continue;
					}
				}

				// add timber packing.
				if ( true === $enable_timber_packing ) {
					$meta['timber_packing'] = apply_filters( 'pok_timber_packing_fee', floatval( $this->setting->get( 'timber_packing_multiplier' ) ) * $rate['cost'], $rate['courier'] );
					$rate['cost'] += $meta['timber_packing'];
				}

				// add insurance fee.
				if ( true === $enable_insurance ) {
					$meta['insurance'] = $this->helper->get_insurance( $rate['courier'], array_sum( wp_list_pluck( $package['contents'], 'line_total' ) ) );
					$rate['cost'] += $meta['insurance'];
				}

				// cost markup.
				$markups = $this->setting->get( 'markup' );
				if ( ! empty( $markups ) && is_array( $markups ) ) {
					foreach ( $markups as $markup ) {
						if ( '' === $markup['courier'] || $rate['courier'] === $markup['courier'] ) {
							if ( ! isset( $markup['service'] ) || '' === $markup['service'] || ( $markup['service'] === sanitize_title( $rate['service'] ) ) ) {
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

				// coupon check.
				if ( ! empty( $package['applied_coupons'] ) ) {
					foreach ( $package['applied_coupons'] as $coupon_code ) {
						$coupon = new WC_Coupon( wc_get_coupon_id_by_code( $coupon_code ) );
						$coupon_id = $coupon->get_id();
						$coupon_type = get_post_meta( $coupon_id, 'discount_type', true );
						if ( 'ongkir' === $coupon_type ) {
							// validate restrictions.
							$restriction = get_post_meta( $coupon_id, 'shipping_restriction', true );
							if ( ! empty( $restriction ) ) {
								// validate min weight restriction.
								if ( isset( $restriction['min_weight'] ) && ! empty( $restriction['min_weight'] ) && $weight < floatval( $restriction['min_weight'] ) ) {
									continue;
								}
								// validate max weight restriction.
								if ( isset( $restriction['max_weight'] ) && ! empty( $restriction['max_weight'] ) && $weight > floatval( $restriction['max_weight'] ) ) {
									continue;
								}
								// validate courier restriction.
								if ( isset( $restriction['courier'] ) && ! empty( $restriction['courier'] ) && ! in_array( $rate['courier'], $restriction['courier'], true ) ) {
									continue;
								}
								// validate service restriction.
								if ( isset( $restriction['service'] ) && ! empty( $restriction['service'] ) && ! in_array( $rate['courier'] . '-' . sanitize_title( $rate['service'] ), $restriction['service'], true ) ) {
									continue;
								}
								// validate destination restriction.
								if ( isset( $restriction['destination'] ) && ! empty( $restriction['destination'] ) ) {
									foreach ( $restriction['destination'] as $res_destination ) {
										$valid_destination = true;
										if ( isset( $res_destination['province'] ) && ! empty( $res_destination['province'] ) && intval( $res_destination['province'] ) !== intval( $destination['state'] ) ) {
											$valid_destination = false;
										} else {
											if ( isset( $res_destination['city'] ) && ! empty( $res_destination['city'] ) && intval( $res_destination['city'] ) !== intval( $destination['city'] ) ) {
												$valid_destination = false;
											} else {
												if ( isset( $res_destination['district'] ) && ! empty( $res_destination['district'] ) && isset( $district ) && intval( $res_destination['district'] ) !== intval( $district ) ) {
													$valid_destination = false;
												}
											}
										}

										if ( true === $valid_destination ) {
											break;
										}
									}

									if ( false === $valid_destination ) {
										continue;
									}
								}
							}

							$discount_type = get_post_meta( $coupon_id, 'shipping_discount_type', true );

							// apply discount.
							if ( 'percent' === $discount_type ) {
								if ( ! isset( $meta['original_cost'] ) ) {
									$meta['original_cost'] = $rate['cost'];
								}
								$amount = floatval( get_post_meta( $coupon->get_id(), 'shipping_discount_amount', true ) );
								if ( 100 < $amount ) {
									$amount = 100;
								}
								$rate['cost'] = $rate['cost'] - ( ( $amount / 100 ) * $rate['cost'] );
								if ( 0 > $rate['cost'] ) {
									$rate['cost'] = 0;
								}
							} elseif ( 'fixed' === $discount_type ) {
								if ( ! isset( $meta['original_cost'] ) ) {
									$meta['original_cost'] = $rate['cost'];
								}
								$rate['cost'] = $rate['cost'] - floatval( get_post_meta( $coupon->get_id(), 'shipping_discount_amount', true ) );
								if ( 0 > $rate['cost'] ) {
									$rate['cost'] = 0;
								}
							} elseif ( 'free' === $discount_type ) {
								if ( ! isset( $meta['original_cost'] ) ) {
									$meta['original_cost'] = $rate['cost'];
								}
								$rate['cost'] = 0;
							}
						}
					}
				}

				$label = $this->helper->get_courier_name( $rate['courier'] );
				if ( ! empty( $rate['service'] ) && '-' !== $rate['service'] ) {
					$label .= ' - ';
					if ( isset( $rate['source'] ) && 'custom' === $rate['source'] ) {
						$label .= $rate['service'];
					} else {
						if ( $custom_name = $this->helper->get_custom_service_name( $rate['courier'], $rate['service'] ) ) {
							$label .= $custom_name;
						} else {
							$label .= $rate['service'];
						}
					}
				}

				$final_rate = apply_filters(
					'pok_rate', array(
						'id'        => 'pok-' . $rate['courier'] . '-' . sanitize_title( $rate['service'] ),
						'label'     => $label,
						'cost'      => $this->helper->currency_convert( $rate['cost'] ),
						'meta_data' => $meta,
					), $rate, $package
				);
				$this->add_rate( $final_rate );
			}
		}
	}

	/**
	 * Get checkout post data.
	 *
	 * @param  string $field Checkout field.
	 * @return mixed         Checkout field data.
	 */
	private function get_checkout_post_data( $field ) {
		if ( isset( $_POST['post_data'] ) ) {
			parse_str( $_POST['post_data'], $return );
			if ( 'nusantara' === $this->setting->get( 'base_api' ) && $this->helper->is_use_simple_address_field() ) {
				if ( ! empty( $return['billing_pok_city'] ) ) {
					$exp = explode( '_', $return['billing_pok_city'] );
					$return['billing_pok_district'] = $exp[0];
					$return['billing_pok_city']     = $exp[1];
					$return['billing_state']        = $exp[2];
				}
				if ( ! empty( $return['shipping_pok_city'] ) ) {
					$exp = explode( '_', $return['shipping_pok_city'] );
					$return['shipping_pok_district'] = $exp[0];
					$return['shipping_pok_city']     = $exp[1];
					$return['shipping_state']        = $exp[2];
				}
			}
			$return = array_map( function( $field ) {
				return str_replace( '+',' ', $field );
			}, $return );
			if ( isset( $return[ $field ] ) ) {
				return $return[ $field ];
			}
		}
		return false;
	}
}
