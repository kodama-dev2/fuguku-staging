<?php

/**
 * Customized checkout fields
 */
class POK_Hooks_Addresses {

	/**
	 * POK Core
	 *
	 * @var object
	 */
	protected $core;

	/**
	 * POK Setting
	 *
	 * @var object
	 */
	protected $setting;

	/**
	 * POK Helper
	 *
	 * @var object
	 */
	protected $helper;

	/**
	 * Field order
	 *
	 * @var array
	 */
	protected $field_order;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $pok_helper;
		global $pok_core;
		$this->core     = $pok_core;
		$this->setting  = new POK_Setting();
		$this->helper   = $pok_helper;
		$this->field_order = apply_filters(
			'pok_fields_priority', array(
				'first_name'    => 10,
				'last_name'     => 20,
				'company'       => 30,
				'country'       => 40,
				'state'         => 50,
				'city'          => 60,
				'district'      => 70,
				'address_1'     => 80,
				'address_2'     => 90,
				'postcode'      => 100,
				'phone'         => 110,
				'email'         => 120,
				'insurance'		=> 9999
			)
		);

		if ( $this->helper->is_plugin_active() ) {
			// checkout page.
			add_filter( 'woocommerce_states', array( $this, 'set_provinces' ) );
			add_filter( 'woocommerce_checkout_fields', array( $this, 'custom_checkout_fields' ) );
			add_filter( 'woocommerce_billing_fields', array( $this, 'custom_billing_fields' ) );
			add_filter( 'woocommerce_shipping_fields', array( $this, 'custom_shipping_fields' ) );
			add_filter( 'woocommerce_get_country_locale_default', array( $this, 'country_locale_default' ) );
			add_filter( 'woocommerce_get_country_locale', array( $this, 'country_locale' ) );
			add_filter( 'woocommerce_country_locale_field_selectors', array( $this, 'locale_field_selectors' ) );
			add_action( 'woocommerce_review_order_after_cart_contents', array( $this, 'show_additional_info_on_checkout' ) );
			add_filter( 'woocommerce_checkout_get_value', array( $this, 'set_default_checkout_value' ), 10, 2 );
			add_action( 'woocommerce_checkout_process', array( $this, 'validate_district' ) );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta' ), 20, 2 );
			add_action( 'woocommerce_checkout_update_order_review', array( $this, 'delete_wc_cache' ) );
			add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_custom_fee' ) );
			add_filter( 'woocommerce_cart_ready_to_calc_shipping', array( $this, 'remove_shipping_on_cart' ) );
			add_filter( 'woocommerce_cart_shipping_method_full_label', array( $this, 'custom_shipping_label' ), 10, 2 );
			add_filter( 'woocommerce_coupon_discount_amount_html', array( $this, 'change_coupon_discount_label' ), 10, 2 );

			// order.
			add_filter( 'woocommerce_order_formatted_billing_address', array( $this, 'custom_formatted_billing_address' ), 10, 2 );
			add_filter( 'woocommerce_order_formatted_shipping_address', array( $this, 'custom_formatted_shipping_address' ), 10, 2 );
			add_filter( 'woocommerce_localisation_address_formats', array( $this, 'custom_address_format' ) );
			add_filter( 'woocommerce_formatted_address_replacements', array( $this, 'custom_address_replacement' ), 30, 2 );
			add_action( 'woocommerce_get_order_address', array( $this, 'set_order_address_data' ), 10, 3 );

			// my account.
			add_filter( 'woocommerce_my_account_edit_address_field_value', array( $this, 'set_my_account_address_value' ), 10, 3 );
			add_filter( 'woocommerce_my_account_my_address_formatted_address', array( $this, 'format_myaccount_address' ), 10, 3 );
			add_action( 'woocommerce_customer_save_address', array( $this, 'update_customer_address' ), 10, 2 );

			// other.
			add_filter( 'woocommerce_shipping_settings', array( $this, 'modify_shipping_settings' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Let 3rd parties unhook the above via this hook.
			do_action( 'pok_hooks_addresses', $this );
		}
	}

	/**
	 * Set custom provinces
	 *
	 * @param array $states WC States.
	 */
	public function set_provinces( $states ) {
		if ( is_admin() && function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen && 'woocommerce_page_wc-settings' === $screen->id && ( ! isset( $_GET['tab'] ) || 'general' === sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) ) {
				return $states;
			}
		}
		if ( 'rajaongkir2' === $this->setting->get('base_api') ) {
			return $states;
		}
		$provinces = $this->core->get_province();
		if ( ! empty( $provinces ) ) {
			$states['ID'] = $provinces;
		} else {
			if ( function_exists( 'wc_add_notice' ) ) {
				wc_add_notice( __( 'Failed to load data. Please refresh the page.', 'pok' ), 'error' );
			}
		}
		return $states;
	}

	/**
	 * Custom checkout fields
	 *
	 * @param  array $fields Checkout fields.
	 * @return array         Checkout fields
	 */
	public function custom_checkout_fields( $fields ) {
		if ( WC()->cart->needs_shipping() || 'yes' === $this->setting->get('show_fields_on_virtual_product') ) {
			$fields['billing']  = $this->alter_fields( $fields['billing'], 'billing' );
			$fields['shipping'] = $this->alter_fields( $fields['shipping'], 'shipping' );
		}
		return $fields;
	}

	/**
	 * Custom billing fields
	 *
	 * @param  array $fields Billing fields.
	 * @return array         Billing fields
	 */
	public function custom_billing_fields( $fields ) {
		if ( ( ! is_null( WC()->cart ) && WC()->cart->needs_shipping() ) || 'yes' === $this->setting->get('show_fields_on_virtual_product') ) {
			$fields = $this->alter_fields( $fields, 'billing' );
		}
		return $fields;
	}

	/**
	 * Custom shipping fields
	 *
	 * @param  array $fields Billing fields.
	 * @return array         Billing fields
	 */
	public function custom_shipping_fields( $fields ) {
		if ( ( ! is_null( WC()->cart ) && WC()->cart->needs_shipping() ) || 'yes' === $this->setting->get('show_fields_on_virtual_product') ) {
			$fields = $this->alter_fields( $fields, 'shipping' );
		}
		return $fields;
	}

	/**
	 * Alter checkout fields
	 *
	 * @param  array  $fields Checkout fields.
	 * @param  string $type   Billing/Shipping.
	 * @return array          Customized fields.
	 */
	private function alter_fields( $fields = array(), $type = 'billing' ) {
		if ( isset( $fields[ $type . '_email' ] ) ) {
			$fields[ $type . '_email' ]['label']    = __( 'Email Address', 'pok' );
			$fields[ $type . '_email' ]['priority'] = $this->field_order['email'];
		}

		if ( isset( $fields[ $type . '_phone' ] ) ) {
			$fields[ $type . '_phone' ]['label']    = __( 'Phone', 'pok' );
			$fields[ $type . '_phone' ]['priority'] = $this->field_order['phone'];
		}

		if ( isset( $fields[ $type . '_country' ] ) ) {
			$fields[ $type . '_country' ]['label']    = __( 'Country', 'pok' );
			$fields[ $type . '_country' ]['priority'] = $this->field_order['country'];
		}

		$existing_class = isset( $fields[ $type . '_city' ]['class'] ) ? $fields[ $type . '_city' ]['class'] : array();

		if ( ! $this->helper->is_use_simple_address_field() ) {
			$fields[ $type . '_pok_city' ] = array(
				'label'       => __( 'City', 'pok' ),
				'placeholder' => __( 'Select City', 'pok' ),
				'type'        => 'select',
				'class'       => array_merge( $existing_class, array( 'validate-required', 'init-select2' ) ),
				'options'     => array( '' => __( 'Select City', 'pok' ) ),
				'priority'    => $this->field_order['city'],
			);
		} else {
			$fields[ $type . '_pok_city' ] = array(
				'label'        		=> __( 'Town / City', 'pok' ),
				'placeholder'  		=> __( 'Search Town / City', 'pok' ),
				'type'         		=> 'select',
				'options'      		=> array( '' => __( 'Search Town / City', 'pok' ) ),
				'class'        		=> array_merge( $existing_class, array( 'update_totals_on_change', 'address-field', 'select2-ajax' ) ),
				'custom_attributes'	=> array(
					'data-action'	=> 'pok_search_simple_address',
					'data-nonce'	=> wp_create_nonce( 'search_city' )
				),
				'priority'          => $this->field_order['city'],
			);
			$fields[ $type . '_state' ]['required'] = false;
			$fields[ $type . '_city' ]['required']  = false;
		}

		if ( 'pro' === $this->helper->get_license_type() ) {
			$fields[ $type . '_pok_district' ] = array(
				'label'       => __( 'District', 'pok' ),
				'placeholder' => __( 'Select District', 'pok' ),
				'type'        => 'select',
				'options'     => array( '' => __( 'Select District', 'pok' ) ),
				'class'       => array_merge( $existing_class, array( 'update_totals_on_change', 'address-field', 'init-select2' ) ),
				'priority'    => $this->field_order['district'],
			);
		}

		// get returning user data.
		if ( is_user_logged_in() && ( is_account_page() || 'yes' === $this->setting->get( 'auto_fill_address' ) ) ) {
			$user_id = get_current_user_id();
			$default[ $type . '_state' ]      = $this->helper->get_address_id_from_user( $user_id, $type, 'state' );
			$default[ $type . '_city' ]       = $this->helper->get_address_id_from_user( $user_id, $type, 'city' );
			$default[ $type . '_district' ]   = $this->helper->get_address_id_from_user( $user_id, $type, 'district' );

			if ( ! $this->helper->is_use_simple_address_field() ) {
				if ( ! empty( $default[ $type . '_state' ] ) ) {
					$fields[ $type . '_pok_city' ]['options'] = $this->core->get_city( $default[ $type . '_state' ] );
				}
				if ( 'pro' === $this->helper->get_license_type() && ! empty( $default[ $type . '_city' ] ) ) {
					$fields[ $type . '_pok_district' ]['options'] = $this->core->get_district( $default[ $type . '_city' ] );
				}
			} else {
				if ( ! empty( $default[ $type . '_state' ] ) && ! empty( $default[ $type . '_city' ] ) && ! empty( $default[ $type . '_district' ] ) ) {
					$city_id = $default[ $type . '_district' ] . '_' . $default[ $type . '_city' ] . '_' . $default[ $type . '_state' ];
					$city = $this->core->get_simple_address( $city_id );
					if ( ! empty( $city ) ) {
						$fields[ $type . '_pok_city' ]['options'] = array();
						$fields[ $type . '_pok_city' ]['options'][ $city_id ] = $city;
					}
				}
			}
				
		}

		if ( 'billing' === $type && $this->helper->is_let_user_decide_insurance() && is_checkout() ){
			$fields[ $type . '_insurance' ]['label']	= __( 'Add shipping insurance to this order', 'pok' );
			$fields[ $type . '_insurance' ]['type']		= 'checkbox';
			$fields[ $type . '_insurance' ]['class']	= array( 'update_totals_on_change', 'form-row-wide' );
			$fields[ $type . '_insurance' ]['priority'] = $this->field_order['insurance'];
		}

		return $fields;
	}

	/**
	 * Set default checkout value
	 * 
	 * @param mixed  $value Field value.
	 * @param string $input Field name.
	 */
	public function set_default_checkout_value( $value, $input ) {
		if ( is_user_logged_in() && ( is_account_page() || 'yes' === $this->setting->get( 'auto_fill_address' ) ) ) {
			$user_id = get_current_user_id();
			$default = array(
				'billing_state'         => $this->helper->get_address_id_from_user( $user_id, 'billing', 'state' ),
				'billing_pok_city'      => $this->helper->get_address_id_from_user( $user_id, 'billing', 'city' ),
				'billing_pok_district'  => $this->helper->get_address_id_from_user( $user_id, 'billing', 'district' ),
				'shipping_state'        => $this->helper->get_address_id_from_user( $user_id, 'shipping', 'state' ),
				'shipping_pok_city'     => $this->helper->get_address_id_from_user( $user_id, 'shipping', 'city' ),
				'shipping_pok_district' => $this->helper->get_address_id_from_user( $user_id, 'shipping', 'district' ),
			);
			if ( isset( $default[ $input ] ) && ! empty( $default[ $input ] ) ) {
				return $default[ $input ];
			}
		}
		return $value;
	}

	/**
	 * Get country locale
	 *
	 * @param  array $fields Fields.
	 * @return array         Fields.
	 */
	public function country_locale( $fields ) {
		if ( is_null( WC()->cart ) || WC()->cart->needs_shipping() || 'yes' === $this->setting->get('show_fields_on_virtual_product') ) {
			foreach ( $fields as $country => $locale ) {
				if ( 'ID' === $country ) {
					$fields['ID'] = array(
						'address_1' => array(
							'label'    => __( 'Address', 'pok' ),
							'priority' => $this->field_order['address_1']
						),
						'address_2' => array(
							'priority' => $this->field_order['address_2']
						),
						'state' => array(
							'label'       => __( 'Province', 'pok' ),
							'placeholder' => __( 'Select Province', 'pok' ),
							'priority'    => $this->field_order['state']
						),
						'city' => array(
							'hidden' => true,
							'required' => false
						),
						'postcode'  => array(
							'label'    => __( 'Postcode / ZIP', 'pok' ),
							'priority' => $this->field_order['postcode']
						),
						'pok_city' => array(
							'hidden'   => false,
							'required' => true,
						),
						'pok_district' => array(
							'hidden'   => false,
							'required' => true,
						)
					);
					if ( 'rajaongkir2' === $this->setting->get('base_api') ) {
						$fields['ID']['postcode'] = array(
							'hidden' => true,
							'required' => false
						);
					}
					if ( $this->helper->is_use_simple_address_field() ) {
						$fields['ID']['state']['hidden'] = true;
						$fields['ID']['state']['required'] = false;
						$fields['ID']['pok_district']['hidden'] = true;
						$fields['ID']['pok_district']['required'] = false;
					}
				} else {
					$fields[ $country ]['pok_city'] = array(
						'required' => false,
						'hidden'   => true,
					);
					$fields[ $country ]['pok_district'] = array(
						'required' => false,
						'hidden'   => true,
					);
					$fields[ $country ]['insurance'] = array(
						'required' => false,
						'hidden'   => true,
					);
				}
				if ( $this->helper->is_let_user_decide_insurance() && is_checkout() ){
					$fields[ $country ]['insurance'] = array(
						'required' => false,
						'hidden'   => false,
					);
				}
			}
		}
		return $fields;
	}

	/**
	 * Get country locale default
	 *
	 * @param  array $fields Fields.
	 * @return array         Fields.
	 */
	public function country_locale_default( $fields ) {
		if ( is_null( WC()->cart ) || WC()->cart->needs_shipping() || 'yes' === $this->setting->get('show_fields_on_virtual_product') ) {
			$fields['pok_city'] = array(
				'required' => false,
				'hidden'   => true,
			);
			$fields['pok_district'] = array(
				'required' => false,
				'hidden'   => true,
			);
			$fields['insurance'] = array(
				'required' => false,
				'hidden'   => true,
			);
		}
		return $fields;
	}

	/**
	 * Additional locale address fields
	 * 
	 * @param  array $fields Fields.
	 * @return array         Fields.
	 */
	public function locale_field_selectors( $fields ) {
		$fields['pok_city']     = '#billing_pok_city_field, #shipping_pok_city_field';
		$fields['pok_district'] = '#billing_pok_district_field, #shipping_pok_district_field';
		$fields['insurance']    = '#billing_insurance_field';
		return $fields;
	}

	/**
	 * Load scripts
	 */
	public function enqueue_scripts() {
		if ( $this->helper->is_woocommerce_active() ) {
			if ( is_checkout() || is_account_page() || apply_filters( 'pok_is_checkout', false ) ) {
				wp_enqueue_style( 'pok-checkout', POK_PLUGIN_URL . '/assets/css/checkout.css', array( 'select2' ), POK_VERSION );
				wp_enqueue_script( 'pok-checkout', POK_PLUGIN_URL . '/assets/js/checkout.js', array( 'jquery', 'select2' ), POK_VERSION, true );
				$localize = array(
					'ajaxurl'               => admin_url( 'admin-ajax.php' ),
					'labelFailedCity'       => __( 'Failed to load city list. Try again?', 'pok' ),
					'labelFailedDistrict'   => __( 'Failed to load district list. Try again?', 'pok' ),
					'labelSelectCity'       => __( 'Select City', 'pok' ),
					'labelLoadingCity'      => __( 'Loading city options...', 'pok' ),
					'labelSelectDistrict'   => __( 'Select District', 'pok' ),
					'labelLoadingDistrict'  => __( 'Loading district options...', 'pok' ),
					'loadReturningUserData' => is_account_page() ? 'yes' : $this->setting->get( 'auto_fill_address' ),
					'is_my_account'			=> is_account_page(),
					'is_checkout'			=> is_checkout(),
					'billing'               => array(
						'country'           => $this->helper->get_country_session( 'billing' ),
						'state'             => 0,
						'city'              => 0,
						'district'          => 0
					),
					'shipping'              => array(
						'country'           => $this->helper->get_country_session( 'shipping' ),
						'state'             => 0,
						'city'              => 0,
						'district'          => 0
					),
					'nonce_change_country'  => wp_create_nonce( 'change_country' ),
					'nonce_get_list_city'   => wp_create_nonce( 'get_list_city' ),
					'nonce_get_list_district' => wp_create_nonce( 'get_list_district' ),
					'enableDistrict'        => ( 'pro' === $this->helper->get_license_type() ? true : false ),
					'useSimpleAddress'		=> $this->helper->is_use_simple_address_field()
				);
				// get returning user data.
				if ( is_user_logged_in() && ( is_account_page() || $this->setting->get( 'auto_fill_address' ) ) ) {
					$user_id = get_current_user_id();
					$localize['billing']['state']     = $this->helper->get_address_id_from_user( $user_id, 'billing', 'state' );
					$localize['billing']['city']      = $this->helper->get_address_id_from_user( $user_id, 'billing', 'city' );
					$localize['billing']['district']  = $this->helper->get_address_id_from_user( $user_id, 'billing', 'district' );
					$localize['shipping']['state']    = $this->helper->get_address_id_from_user( $user_id, 'shipping', 'state' );
					$localize['shipping']['city']     = $this->helper->get_address_id_from_user( $user_id, 'shipping', 'city' );
					$localize['shipping']['district'] = $this->helper->get_address_id_from_user( $user_id, 'shipping', 'district' );
				}
				wp_localize_script( 'pok-checkout', 'pok_checkout_data', $localize );
			}
		}
	}

	/**
	 * Custom formatted billing address
	 *
	 * @param  array    $formats Address formats.
	 * @param  WC_Order $order   Order object.
	 * @return array             Address formats.
	 */
	public function custom_formatted_billing_address( $address, $order ) {
		if ( 'rajaongkir2' === $order->get_meta('_pok_data_api') ) {
			$address['ro2_destination'] = $order->get_meta('_billing_pok_ro2_destination');
			if ( isset( $address['ro2_destination'] ) && ! empty( $address['ro2_destination'] ) ) {
				[ $village, $district, $city, $state, $postcode ] = explode( ', ', $address['ro2_destination'] );
				$address['state']    = $state;
				$address['postcode'] = $postcode;
			}
		}
		return $address;
	}

	/**
	 * Custom formatted shipping address
	 *
	 * @param  array    $formats Address formats.
	 * @param  WC_Order $order   Order object.
	 * @return array             Address formats.
	 */
	public function custom_formatted_shipping_address( $address, $order ) {
		if ( 'rajaongkir2' === $order->get_meta('_pok_data_api') ) {
			$address['ro2_destination'] = $order->get_meta('_shipping_pok_ro2_destination');
			if ( isset( $address['ro2_destination'] ) && ! empty( $address['ro2_destination'] ) ) {
				[ $village, $district, $city, $state, $postcode ] = explode( ', ', $address['ro2_destination'] );
				$address['state']    = $state;
				$address['postcode'] = $postcode;
			}
		}
		return $address;
	}

	/**
	 * Custom address format
	 *
	 * @param  array $formats Address formats.
	 * @return array          Address formats.
	 */
	public function custom_address_format( $formats ) {
		$formats['ID'] = "{name}\n{company}\n{address_1}\n{address_2}\n{pok_district}{pok_city}\n{pok_state}\n{country}\n{postcode}";
		return $formats;
	}

	/**
	 * Custom address format replacements
	 *
	 * @param  array $replacements Replacement fields.
	 * @param  array $args         Address args.
	 * @return array               Replacement fields.
	 */
	public function custom_address_replacement( $replacements, $args ) {
		// only for rajaongkir v2
		if ( isset( $args['ro2_destination'] ) && ! empty( $args['ro2_destination'] ) ) {
			[ $village, $district, $city, $state, $postcode ] = explode( ', ', $args['ro2_destination'] );
			$replacements['{pok_district}'] = $village . ', ' . $district . "\n";
			$replacements['{pok_city}']     = $city;
			$replacements['{pok_state}']    = $state;
		} else {
			// set state name.
			$province = isset( $args['state'] ) ? $args['state'] : '';
			if ( isset( $args['state'] ) ) {
				if ( 0 !== intval( $args['state'] ) ) {
					$province = $this->core->get_single_province( intval( $args['state'] ) );
				} else {
					$province = $args['state'];
				}
			}

			// set city name.
			$city = isset( $args['city'] ) ? $args['city'] : '';
			if ( isset( $args['city'] ) ) {
				if ( 0 !== intval( $args['city'] ) ) {
					$city = $this->core->get_single_city_without_province( intval( $args['city'] ) );
				} else {
					$city = $args['city'];
				}
			}

			// set district name.
			$district = '';
			if ( isset( $args['district'] ) ) {
				if ( 0 !== intval( $args['city'] ) && 0 !== intval( $args['city'] ) && 'pro' === $this->helper->get_license_type() && 0 !== intval( $args['district'] ) ) {
					$district = 'Kec. ' . $this->core->get_single_district( intval( $args['city'] ), intval( $args['district'] ) ) . "\n";
				} elseif ( ! empty( $args['district'] ) ) {
					$district = 'Kec. ' . $args['district'] . "\n";
				} else {
					$district = "";
				}
			}

			$replacements['{pok_district}'] = $district;
			$replacements['{pok_city}']     = $city;
			$replacements['{pok_state}']    = $province;
		}

		return $replacements;
	}

	/**
	 * Set address value on my account page
	 * 
	 * @param string $value        Address value.
	 * @param string $key          Field key.
	 * @param string $load_address Address type.
	 */
	public function set_my_account_address_value( $value, $key, $load_address ) {
		if ( in_array( $key, array( 'billing_state', 'billing_city', 'billing_district', 'shipping_state', 'shipping_city', 'shipping_district' ) ) ) {
			$user_id = get_current_user_id();
			$exp = explode( '_', $key );
			$address_value = $this->helper->get_address_id_from_user( $user_id, $exp[0], $exp[1] );
			if ( ! empty( $address_value ) ) {
				$value = $address_value;
			}
		}
		return $value;
	}

	/**
	 * Fix name formatting on myaccount page
	 *
	 * @param  array  $address     Address data.
	 * @param  int    $customer_id Customer ID.
	 * @param  string $name        Billing/Shipping.
	 * @return array               Address data.
	 */
	public function format_myaccount_address( $address, $customer_id, $name ) {
		$address['district'] = get_user_meta( $customer_id, $name . '_district', true );
		return $address;
	}

	/**
	 * Validate district on checkout
	 */
	public function validate_district() {
		if ( WC()->cart->needs_shipping() || 'yes' === $this->setting->get('show_fields_on_virtual_product') ) {
			if ( 'pro' === $this->helper->get_license_type() ) {
				if (
					isset( $_POST['billing_country'] ) &&
					'ID' === $_POST['billing_country'] &&
					! $this->helper->is_use_simple_address_field() &&
					( ! isset( $_POST['billing_pok_district'] ) || empty( $_POST['billing_pok_district'] ) )
				) { // WPCS: Input var okay. CSRF okay.
					wc_add_notice( __( '<b>Billing district</b> is required', 'pok' ), 'error' );
				}

				if (
					isset( $_POST['ship_to_different_address'] ) &&
					! empty( $_POST['ship_to_different_address'] ) &&
					isset( $_POST['shipping_country'] ) &&
					'ID' === $_POST['shipping_country'] &&
					! $this->helper->is_use_simple_address_field() &&
					( ! isset( $_POST['shipping_pok_district'] ) || empty( $_POST['shipping_pok_district'] ) )
				) { // WPCS: Input var okay. CSRF okay.
					wc_add_notice( __( '<b>Shipping district</b> is required', 'pok' ), 'error' );
				}
			}
		}
	}

	/**
	 * Update user meta on checkout
	 *
	 * @param  integer $order_id Order ID.
	 * @param  array   $data     Input data.
	 */
	public function update_order_meta( $order_id, $data ) {
		$order          = wc_get_order( $order_id );
		$user_id        = version_compare( WC()->version, '3.0', '>=' ) ? $order->get_user_id() : $order->user_id;
		$simple_address = $this->helper->is_use_simple_address_field();
		$base_api       = $this->setting->get('base_api');

		// update order meta & user meta.
		if ( 'rajaongkir2' === $base_api ) {
			if ( 'ID' === $data['billing_country'] && ! empty( $data['billing_city'] ) ) {
				[ $billing_village, $billing_district, $billing_city, $billing_state, $billing_postcode ] = explode( ', ', $data['billing_city'] );
				$order->update_meta_data( 'billing_state', $billing_state );
				$order->update_meta_data( 'billing_city', $billing_city );
				$order->update_meta_data( 'billing_postcode', $billing_postcode );
				$order->update_meta_data( '_billing_pok_city', $billing_city );
				$order->update_meta_data( '_billing_pok_district', $billing_village . ', ' . $billing_district );
				$order->update_meta_data( '_billing_pok_ro2_destination', $data['billing_city'] );
				$order->update_meta_data( '_billing_pok_ro2_destination_id', $data['billing_pok_city'] );
			}
			if ( 'ID' === $data['shipping_country'] && ! empty( $data['shipping_city'] ) ) {
				[ $shipping_village, $shipping_district, $shipping_city, $shipping_state, $shipping_postcode ] = explode( ', ', $data['shipping_city'] );
				$order->update_meta_data( 'shipping_state', $shipping_state );
				$order->update_meta_data( 'shipping_city', $shipping_city );
				$order->update_meta_data( 'shipping_postcode', $shipping_postcode );
				$order->update_meta_data( '_shipping_pok_city', $shipping_city );
				$order->update_meta_data( '_shipping_pok_district', $shipping_village . ', ' . $shipping_district );
				$order->update_meta_data( '_shipping_pok_ro2_destination', $data['shipping_city'] );
				$order->update_meta_data( '_shipping_pok_ro2_destination_id', $data['shipping_pok_city'] );
			}
		} else {

			if ( 'ID' === $data['billing_country'] ) {
				if ( $simple_address && ! empty( $data['billing_pok_city'] ) ) {
					$exp = explode( '_', $data['billing_pok_city'] );
					$data['billing_pok_district'] = $exp[0];
					$data['billing_pok_city']     = $exp[1];
					$data['billing_state']        = $exp[2];
					$order->update_meta_data( '_billing_pok_city', $data['billing_pok_city'] );
					$order->update_meta_data( '_billing_pok_district', $data['billing_pok_district'] );
					update_user_meta( $user_id, 'billing_pok_city', $data['billing_pok_city'] );
					update_user_meta( $user_id, 'billing_pok_district', $data['billing_pok_district'] );
				}
				$province = $this->core->get_single_province( intval( $data['billing_state'] ) );
				$order->update_meta_data( '_billing_state', ( isset( $province ) && ! empty( $province ) ? $province : $data['billing_state'] ) );
				$order->update_meta_data( '_billing_pok_state', $data['billing_state'] );
				update_user_meta( $user_id, 'billing_state', ( isset( $province ) && ! empty( $province ) ? $province : $data['billing_state'] ) );
				update_user_meta( $user_id, 'billing_pok_state', $data['billing_state'] );

				$city = $this->core->get_single_city_without_province( intval( $data['billing_pok_city'] ) );
				$order->update_meta_data( '_billing_city', ( isset( $city ) && ! empty( $city ) ? $city : $data['billing_city'] ) );
				update_user_meta( $user_id, 'billing_city', ( isset( $city ) && ! empty( $city ) ? $city : $data['billing_city'] ) );

				if ( isset( $data['billing_pok_district'] ) ) {
					$district = $this->core->get_single_district( intval( $data['billing_pok_city'] ), intval( $data['billing_pok_district'] ) );
					$order->update_meta_data( '_billing_district', ( isset( $district ) && ! empty( $district ) ? $district : $data['billing_pok_district'] ) );
					update_user_meta( $user_id, 'billing_district', ( isset( $district ) && ! empty( $district ) ? $district : $data['billing_pok_district'] ) );
				}
			}
			if ( 'ID' === $data['shipping_country'] ) {
				if ( $simple_address && ! empty( $data['shipping_pok_city'] ) ) {
					$exp = explode( '_', $data['shipping_pok_city'] );
					$data['shipping_pok_district'] = $exp[0];
					$data['shipping_pok_city']     = $exp[1];
					$data['shipping_state']        = $exp[2];
					$order->update_meta_data( '_shipping_pok_city', $data['shipping_pok_city'] );
					$order->update_meta_data( '_shipping_pok_district', $data['shipping_pok_district'] );
					update_user_meta( $user_id, 'shipping_pok_city', $data['shipping_pok_city'] );
					update_user_meta( $user_id, 'shipping_pok_district', $data['shipping_pok_district'] );
				}
				$province = $this->core->get_single_province( intval( $data['shipping_state'] ) );
				$order->update_meta_data( '_shipping_state', ( isset( $province ) && ! empty( $province ) ? $province : $data['shipping_state'] ) );
				$order->update_meta_data( '_shipping_state_id', $data['shipping_state'] );
				update_user_meta( $user_id, 'shipping_state', ( isset( $province ) && ! empty( $province ) ? $province : $data['shipping_state'] ) );
				update_user_meta( $user_id, 'shipping_state_id', $data['shipping_state'] );

				$city = $this->core->get_single_city_without_province( intval( $data['shipping_pok_city'] ) );
				$order->update_meta_data( '_shipping_city', ( isset( $city ) && ! empty( $city ) ? $city : $data['shipping_city'] ) );
				update_user_meta( $user_id, 'shipping_city', ( isset( $city ) && ! empty( $city ) ? $city : $data['shipping_city'] ) );

				if ( isset( $data['shipping_pok_district'] ) ) {
					$district = $this->core->get_single_district( intval( $data['shipping_pok_city'] ), intval( $data['shipping_pok_district'] ) );
					$order->update_meta_data( '_shipping_district', ( isset( $district ) && ! empty( $district ) ? $district : $data['shipping_pok_district'] ) );
					update_user_meta( $user_id, 'shipping_district', ( isset( $district ) && ! empty( $district ) ? $district : $data['shipping_pok_district'] ) );
				}
			}
		}

		// set data API.
		$order->update_meta_data( '_pok_data_api', $base_api );

		// save the meta
		$order->save();

		// unset random number.
		if ( WC()->session->__isset( 'pok_random_number' ) ) {
			WC()->session->__unset( 'pok_random_number' );
		}
	}

	/**
	 * Add district to order address
	 *
	 * @param array  $address Address data.
	 * @param string $type    Billing/shipping.
	 * @param object $order   Order object.
	 */
	public function set_order_address_data( $address, $type, $order ) {
		$source_api = $order->get_meta( '_pok_data_api', true );
		
		$state = $order->get_meta( '_' . $type . '_pok_state', true );
		if ( empty( $state ) ) { // backward compatibility ( < 3.8.0 ).
			$state = $order->get_meta( '_' . $type . '_state_id', true );
		}
		if ( ! empty( $state ) && ( '' === $source_api || $source_api === $this->setting->get( 'base_api' ) ) ) {
			$address['state'] = $state;
		}

		$city = $order->get_meta( '_' . $type . '_pok_city', true );
		if ( empty( $city ) ) { // backward compatibility ( < 3.8.0 ).
			$city = $order->get_meta( '_' . $type . '_city_id', true );
		}
		if ( ! empty( $city ) && ( '' === $source_api || $source_api === $this->setting->get( 'base_api' ) ) ) {
			$address['city'] = $city;
		}

		$district = $order->get_meta( '_' . $type . '_pok_district', true );
		if ( empty( $district ) ) { // backward compatibility ( < 3.8.0 ).
			$district = $order->get_meta( '_' . $type . '_district_id', true );
		}
		// backward compatibility ( < 3.0.3 ).
		if ( '' === $district || ( '' !== $source_api && $source_api !== $this->setting->get( 'base_api' ) ) ) {
			$district = $order->get_meta( '_' . $type . '_district', true );
		}
		if ( ! empty( $district ) ) {
			$address['district'] = $district;
		}
		if ( 'rajaongkir2' === $source_api ) {
			$ro2_destination = $order->get_meta( '_' . $type . '_pok_ro2_destination', true );
			if ( ! empty( $ro2_destination ) ) {
				[ $village, $district, $city, $state, $postcode ] = explode( ', ', $ro2_destination );
				$address['state']    = $state;
				$address['postcode'] = $postcode;
			}
		}
		return $address;
	}

	/**
	 * Delete shipping cache
	 */
	public function delete_wc_cache() {
		$packages = WC()->cart->get_shipping_packages();
		foreach ( $packages as $key => $value ) {
			$shipping_session = "shipping_for_package_$key";
			WC()->session->__unset( $shipping_session );
		}
	}

	/**
	 * Add custom fee to checkout
	 */
	public function add_custom_fee() {
		// unique number.
		if ( 'yes' === $this->setting->get( 'unique_number' ) ) {
			if ( WC()->session->__isset( 'pok_random_number' ) ) {
				$number = WC()->session->get( 'pok_random_number' );
			} else {
				$number = $this->helper->random_number( $this->setting->get( 'unique_number_length' ) );
				WC()->session->set( 'pok_random_number', $number );
			}
			WC()->cart->add_fee( __( 'Unique Number', 'pok' ), $number );
		}
	}

	/**
	 * Show shipping weight on checkout
	 */
	public function show_additional_info_on_checkout() {
		if ( 'rajaongkir2' !== $this->setting->get( 'base_api' ) && 'yes' === $this->setting->get( 'show_origin_on_checkout' ) && WC()->cart->needs_shipping() && ! $this->helper->is_multi_vendor_addon_active() ) {
			$origin = $this->setting->get( 'store_location' );
			if ( ! empty( $origin ) && isset( $origin[0] ) && $this->core->get_single_city( intval( $origin[0] ) ) ) {
				?>
				<tr>
					<td class="product-name">
						<?php echo esc_html( apply_filters( 'pok_show_origin_label', __( 'Your items will be shipped from', 'pok' ) ) ); ?>
					</td>
					<td class="product-total">
						<?php echo esc_html( $this->core->get_single_city( intval( $origin[0] ) ) ); ?>
					</td>
				</tr>
				<?php
			}
		}
		if ( 'yes' === $this->setting->get( 'show_weight_on_checkout' ) && WC()->cart->needs_shipping() && ! $this->helper->is_multi_vendor_addon_active() ) {
			if ( count( WC()->cart->get_cart() ) > 0 ) {
				$weight = $this->helper->get_total_weight( WC()->cart->get_cart() );
				if ( floor( $weight ) < $weight ) {
					$weight = number_format( $weight, 1 );
				}
				if( $weight > 0 ):
				?>
				<tr>
					<td class="product-name">
						<?php echo esc_html( apply_filters( 'pok_show_weight_label', __( 'Total shipping weight', 'pok' ) ) ); ?>
					</td>
					<td class="product-total">
						<?php echo esc_html( $weight ); ?>
						Kg
					</td>
				</tr>
				<?php
				endif;
			}
		}
	}

	/**
	 * Modify woocommerce setting page
	 *
	 * @param  array $fields Setting fields.
	 * @return array         Setting fields.
	 */
	public function modify_shipping_settings( $fields ) {
		if ( function_exists( 'array_column' ) ) {
			$key = array_search( 'woocommerce_enable_shipping_calc', array_column( $fields, 'id' ), true );
			if ( false !== $key ) {
				update_option( 'woocommerce_enable_shipping_calc', 'no' );
				$fields[ $key ]['custom_attributes']['disabled'] = 'disabled';
				$fields[ $key ]['desc'] .= ' (' . esc_html__( 'disabled by Plugin Ongkos Kirim', 'pok' ) . ')';
			}
		}
		return $fields;
	}

	/**
	 * Remove shipping from cart page
	 *
	 * @param  boolean $show_shipping Show shipping or not.
	 * @return boolean                Show shipping or not.
	 */
	public function remove_shipping_on_cart( $show_shipping ) {
		if ( is_cart() ) {
			return false;
		}
		return $show_shipping;
	}

	/**
	 * Handle save customer address
	 *
	 * @param  integer $user_id      User ID.
	 * @param  string  $load_address Billing/shipping.
	 */
	public function update_customer_address( $user_id, $load_address ) {
		if ( isset( $_POST['billing_country'] ) && 'ID' === sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) ) { // WPCS: Input var okay. CSRF okay.
			$_state = sanitize_text_field( $_POST['billing_state'] );
			$_city  = sanitize_text_field( $_POST['billing_pok_city'] );
			$_district = isset( $_POST['billing_pok_district'] ) ? sanitize_text_field( $_POST['billing_pok_district'] ) : '';
			if ( $this->helper->is_use_simple_address_field() && ! empty( $_city ) ) {
				$exp = explode( '_', $_city );
				$_district = $exp[0];
				$_city     = $exp[1];
				$_state    = $exp[2];
			}
			if ( isset( $_state ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_state ) ) { // WPCS: Input var okay. CSRF okay.
					$province = $this->core->get_single_province( intval( $_state ) ); // WPCS: Input var okay. CSRF okay.
					update_user_meta( $user_id, 'billing_state', ( isset( $province ) && ! empty( $province ) ? $province : $_state ) );
					update_user_meta( $user_id, 'billing_pok_state', $_state );
				}
			}
			if ( isset( $_city ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_city ) ) { // WPCS: Input var okay. CSRF okay.
					$city = $this->core->get_single_city_without_province( intval( $_city ) ); // WPCS: Input var okay. CSRF okay.
					update_user_meta( $user_id, 'billing_city', ( isset( $city ) && ! empty( $city ) ? $city : $_city ) );
					update_user_meta( $user_id, 'billing_pok_city', $_city );
				}
				if ( isset( $_district ) ) { // WPCS: Input var okay. CSRF okay.
					if ( 0 !== intval( $_district ) ) { // WPCS: Input var okay. CSRF okay.
						$district = $this->core->get_single_district( intval( $_city ), intval( $_district ) ); // WPCS: Input var okay. CSRF okay.
						update_user_meta( $user_id, 'billing_district', ( isset( $district ) && ! empty( $district ) ? $district : $_district ) );
						update_user_meta( $user_id, 'billing_pok_district', $_district );
					}
				}
			}
		}
		if ( isset( $_POST['shipping_country'] ) && 'ID' === sanitize_text_field( wp_unslash( $_POST['shipping_country'] ) ) ) { // WPCS: Input var okay. CSRF okay.
			$_state = sanitize_text_field( $_POST['shipping_state'] );
			$_city  = sanitize_text_field( $_POST['shipping_pok_city'] );
			$_district = isset( $_POST['shipping_pok_district'] ) ? sanitize_text_field( $_POST['shipping_pok_district'] ) : '';
			if ( $this->helper->is_use_simple_address_field() && ! empty( $_city ) ) {
				$exp = explode( '_', $_city );
				$_district = $exp[0];
				$_city     = $exp[1];
				$_state    = $exp[2];
			}
			if ( isset( $_state ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_state ) ) { // WPCS: Input var okay. CSRF okay.
					$province = $this->core->get_single_province( intval( $_state ) ); // WPCS: Input var okay. CSRF okay.
					update_user_meta( $user_id, 'shipping_state', ( isset( $province ) && ! empty( $province ) ? $province : $_state ) );
					update_user_meta( $user_id, 'shipping_pok_state', $_state );
				}
			}
			if ( isset( $_city ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_city ) ) { // WPCS: Input var okay. CSRF okay.
					$city = $this->core->get_single_city_without_province( intval( $_city ) ); // WPCS: Input var okay. CSRF okay.
					update_user_meta( $user_id, 'shipping_city', ( isset( $city ) && ! empty( $city ) ? $city : $_city ) );
					update_user_meta( $user_id, 'shipping_pok_city', $_city );
				}
				if ( isset( $_district ) ) { // WPCS: Input var okay. CSRF okay.
					if ( 0 !== intval( $_district ) ) { // WPCS: Input var okay. CSRF okay.
						$district = $this->core->get_single_district( intval( $_city ), intval( $_district ) ); // WPCS: Input var okay. CSRF okay.
						update_user_meta( $user_id, 'shipping_district', ( isset( $district ) && ! empty( $district ) ? $district : $_district ) );
						update_user_meta( $user_id, 'shipping_pok_district', $_district );
					}
				}
			}
		}
	}

	/**
	 * Change shipping method label if it has discount
	 * 
	 * @param  string $label  Shipping label.
	 * @param  object $method Method object.
	 * @return string         New shipping label.
	 */
	public function custom_shipping_label( $label, $method ) {
		if ( 'plugin_ongkos_kirim' === $method->get_method_id() ) {
			$shipping_meta = $method->get_meta_data();
			$etd = '';
			if ( 'yes' === $this->setting->get( 'show_shipping_etd' ) && ! empty( $shipping_meta['etd'] ) && '-' !== $shipping_meta['etd'] ) {
				if( 'Q9 Barang' === $shipping_meta['service'] ) {
					$shipping_meta['etd'] = 1;
				}
				$etd = " <span class='pok-etd'>(" . $this->helper->format_etd( $shipping_meta['etd'] ) . ")</span>";
			}
			$original_cost = isset( $shipping_meta['original_cost'] ) ? floatval( $shipping_meta['original_cost'] ) : floatval( $method->get_cost() );

			if ( isset( $shipping_meta['original_cost'] ) && floatval( $shipping_meta['original_cost'] ) > floatval( $method->get_cost() ) ) {
				$label = "<span class='pok-label'>" . $method->get_label() . ':</span> <span class="pok-price"><del>' . wc_price( $original_cost ) . '</del> <ins>' . wc_price( $method->get_cost() ) . '</ins></span>' . $etd;
			} else {
				$label = "<span class='pok-label'>" . $method->get_label() . ':</span> <span class="pok-price">' . wc_price( $original_cost ) . "</span>" . $etd;
			}
		}
		return $label;
	}

	/**
	 * Change default coupon label
	 * 
	 * @param  string $label  Coupon label.
	 * @param  object $coupon Coupon data.
	 * @return string         Coupon label.
	 */
	public function change_coupon_discount_label( $label, $coupon ) {
		if ( 'ongkir' === get_post_meta( $coupon->get_id(), 'discount_type', true ) ) {
			$label = $coupon->get_description();
			if ( empty( $label ) ) {
				$label = __( 'Shipping discount coupon', 'pok' );
			}
		}
		return $label;
	}

}
