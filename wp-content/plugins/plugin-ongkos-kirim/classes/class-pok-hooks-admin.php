<?php

/**
 * Admin hooks
 */
class POK_Hooks_Admin {

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
	 * Costructor
	 */
	public function __construct() {
		global $pok_helper;
		global $pok_core;
		$this->core     = $pok_core;
		$this->setting  = new POK_Setting();
		$this->helper   = $pok_helper;
		if ( $this->helper->is_plugin_active() ) {

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			// order page.
			add_filter( 'woocommerce_admin_billing_fields', array( $this, 'custom_admin_billing_fields' ) );
			add_filter( 'woocommerce_admin_shipping_fields', array( $this, 'custom_admin_shipping_fields' ) );
			add_action( 'woocommerce_order_item_add_line_buttons', array( $this, 'add_button_ongkir' ) );
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_item_meta' ) );
			add_action( 'woocommerce_after_order_itemmeta', array( $this, 'add_switch_button' ), 10, 2 );
			add_action( 'woocommerce_admin_order_totals_after_discount', array( $this, 'add_weight_info' ) );
			add_filter( 'woocommerce_ajax_get_customer_details', array( $this, 'get_district_from_meta' ), 10, 3 );
			add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_addresses_meta' ), 50, 2 );
			if ( $this->helper->compare_wc_version( '>=', '3.1.0' ) ) {
				add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'change_meta_keys' ), 10, 3 );
				add_filter( 'woocommerce_order_item_display_meta_value', array( $this, 'change_meta_values' ), 10, 3 );
			}
			add_filter( 'woocommerce_shipping_address_map_url_parts', array( $this, 'change_address_map_url' ), 10, 2 );

			// report page.
			add_filter( 'woocommerce_admin_reports', array( $this, 'shipping_report' ) );

			// coupons.
			add_filter( 'woocommerce_coupon_discount_types', array( $this, 'new_coupon_types' ) );
			add_filter( 'woocommerce_coupon_data_tabs', array( $this, 'coupon_shipping_restriction_tab' ) );
			add_action( 'woocommerce_coupon_data_panels', array( $this, 'coupon_shipping_restriction_panel' ), 10, 2 );
			add_action( 'woocommerce_coupon_options', array( $this, 'new_coupon_data_field' ), 10, 2 );
			add_action( 'woocommerce_coupon_options_save', array( $this, 'shipping_coupon_save' ), 10, 2 );

			// Let 3rd parties unhook the above via this hook.
			do_action( 'pok_hooks_admin', $this );
		}
	}

	/**
	 * Custom admin billing fields
	 *
	 * @param  array $fields Billing fields.
	 * @return array         Custom billing fields.
	 */
	public function custom_admin_billing_fields( $fields ) {
		return $this->custom_admin_fields( 'billing', $fields );
	}

	/**
	 * Custom admin shipping fields
	 *
	 * @param  array $fields Shipping fields.
	 * @return array         Custom shipping fields.
	 */
	public function custom_admin_shipping_fields( $fields ) {
		return $this->custom_admin_fields( 'shipping', $fields );
	}

	/**
	 * Custom admin fields
	 *
	 * @param  string $context Context.
	 * @param  array  $fields  Original fields.
	 * @return array           Custom fields.
	 */
	private function custom_admin_fields( $context, $fields ) {
		if ( isset( $_GET['page'] ) && 'wc-orders' === sanitize_text_field( $_GET['page'] ) && isset( $_GET['id'] ) ) {
			$thepostid = absint( $_GET['id'] );
		} else {
			global $thepostid, $post;
			$thepostid = empty( $thepostid ) && ! is_null( $post ) ? $post->ID : $thepostid;
		}
		$custom_fields = array();
		foreach ( $fields as $key => $value ) {
			if ( 'city' === $key ) {
				$custom_fields['country']  = $fields['country'];
				$custom_fields['state']    = $fields['state'];
				$custom_fields['city']     = $fields['city'];
				$custom_fields['pok_city'] = array(
					'label'   => __( 'City', 'pok' ),
					'class'   => 'select select2',
					'type'    => 'select',
					'options' => array( '' => __( 'Select City', 'pok' ) ),
					'show'    => false,
				);
				if ( 'pro' === $this->helper->get_license_type() ) {
					$custom_fields['pok_district'] = array(
						'label'   => __( 'District', 'pok' ),
						'class'   => 'select select2',
						'type'    => 'select',
						'options' => array( '' => __( 'Select District', 'pok' ) ),
						'show'    => false,
					);
				}
			} elseif ( 'country' !== $key && 'state' !== $key ) {
				$custom_fields[ $key ] = $value;
			}
		}
		$state = $this->helper->get_address_id_from_order( $thepostid, $context, 'state' );
		$custom_fields['state']['value'] = $state;
		$cities = $this->core->get_city( $state );
		if ( is_array( $cities ) ) {
			foreach ( $cities as $city_id => $city ) {
				$custom_fields['pok_city']['options'][ $city_id ] = $city;
			}
		}
		$city = $this->helper->get_address_id_from_order( $thepostid, $context, 'city' );
		$custom_fields['pok_city']['value'] = $city;
		$districts = $this->core->get_district( $city );
		if ( is_array( $districts ) ) {
			$custom_fields['pok_district']['options'] = $districts;
		}
		return $custom_fields;
	}

	/**
	 * Load scripts
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		if ( is_null( $screen ) ) {
			return;
		}
		if ( ( 'post' === $screen->base && 'shop_order' === $screen->post_type ) || 'woocommerce_page_wc-orders' === $screen->id ) {
			if ( 'woocommerce_page_wc-orders' === $screen->id ) {
				$thepostid = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
			} else {
				global $thepostid, $post;
				$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
			}
			wp_register_style( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css', array(), '4.0.5' );
			wp_enqueue_style( 'pok-order', POK_PLUGIN_URL . '/assets/css/order.css', array( 'select2', 'woocommerce_admin_styles' ), rand() );
			wp_enqueue_script( 'pok-order', POK_PLUGIN_URL . '/assets/js/order.js', array( 'jquery', 'select2' ), POK_VERSION, true );
			$localize = array(
				'labelFailedCity'       => __( 'Failed to load city list. Try again?', 'pok' ),
				'labelFailedDistrict'   => __( 'Failed to load district list. Try again?', 'pok' ),
				'labelSelectState'      => __( 'Select State', 'pok' ),
				'labelLoadingState'     => __( 'Loading state options...', 'pok' ),
				'labelSelectCity'       => __( 'Select City', 'pok' ),
				'labelLoadingCity'      => __( 'Loading city options...', 'pok' ),
				'labelSelectDistrict'   => __( 'Select District', 'pok' ),
				'labelLoadingDistrict'  => __( 'Loading district options...', 'pok' ),
				'labelNoDistrict'       => __( "You need to set customer's shipping district to get the costs", 'pok' ),
				'labelNoCity'           => __( "You need to set customer's shipping city to get the costs", 'pok' ),
				'labelSelectShipping'   => __( 'Select shipping service', 'pok' ),
				'labelOnlyIndonesia'    => __( 'Currently this feature only support shipping to Indonesia.', 'pok' ),
				'billing'               => array(
					'country'  => $this->helper->get_address_id_from_order( $thepostid, 'billing', 'country' ),
					'state'    => $this->helper->get_address_id_from_order( $thepostid, 'billing', 'state' ),
					'city'     => $this->helper->get_address_id_from_order( $thepostid, 'billing', 'city' ),
					'district' => $this->helper->get_address_id_from_order( $thepostid, 'billing', 'district' ),
				),
				'shipping'              => array(
					'country'  => $this->helper->get_address_id_from_order( $thepostid, 'shipping', 'country' ),
					'state'    => $this->helper->get_address_id_from_order( $thepostid, 'shipping', 'state' ),
					'city'     => $this->helper->get_address_id_from_order( $thepostid, 'shipping', 'city' ),
					'district' => $this->helper->get_address_id_from_order( $thepostid, 'shipping', 'district' ),
				),
				'nonce_change_country'  => wp_create_nonce( 'change_country' ),
				'nonce_get_list_city'   => wp_create_nonce( 'get_list_city' ),
				'nonce_get_list_district' => wp_create_nonce( 'get_list_district' ),
				'enableDistrict'        => ( 'pro' === $this->helper->get_license_type() ? true : false ),
			);
			if ( 'ID' === $localize['billing']['country'] ) {
				$localize['billing']['state_options'] = $this->core->get_province();
				if ( 0 !== intval( $localize['billing']['state'] ) ) {
					$localize['billing']['city_options'] = $this->core->get_city( intval( $localize['billing']['state'] ) );
				}
				if ( $localize['enableDistrict'] && 0 !== intval( $localize['billing']['city'] ) ) {
					$localize['billing']['district_options'] = $this->core->get_district( intval( $localize['billing']['city'] ) );
				}
			}
			if ( 'ID' === $localize['shipping']['country'] ) {
				$localize['shipping']['state_options'] = $this->core->get_province();
				if ( 0 !== intval( $localize['shipping']['state'] ) ) {
					$localize['shipping']['city_options'] = $this->core->get_city( intval( $localize['shipping']['state'] ) );
				}
				if ( $localize['enableDistrict'] && 0 !== intval( $localize['shipping']['city'] ) ) {
					$localize['shipping']['district_options'] = $this->core->get_district( intval( $localize['shipping']['city'] ) );
				}
			}
			wp_localize_script( 'pok-order', 'pok_order_data', $localize );
			wp_localize_script(
				'pok-order', 'pok_nonces', array(
					'get_cost'              => wp_create_nonce( 'get_cost' ),
					'set_order_shipping'    => wp_create_nonce( 'set_order_shipping' ),
				)
			);
		} elseif ( 'shop_coupon' === $screen->id ) {
			wp_enqueue_style( 'select2', POK_PLUGIN_URL . '/assets/css/select2.min.css', array() );
			wp_enqueue_style( 'pok-coupon', POK_PLUGIN_URL . '/assets/css/coupon.css', array( 'select2', 'woocommerce_admin_styles' ), POK_VERSION );
			wp_enqueue_script( 'pok-coupon', POK_PLUGIN_URL . '/assets/js/coupon.js', array( 'jquery', 'select2' ), POK_VERSION, true );
			wp_localize_script(
				'pok-coupon', 'pok_translations', array(
					'all_province'                  => __( 'All Province', 'pok' ),
					'all_city'                      => __( 'All City', 'pok' ),
					'all_district'                  => __( 'All District', 'pok' ),
					'delete'                        => __( 'Delete', 'pok' ),
					'add'                           => __( 'Add', 'pok' ),
					'select_city'                   => __( 'Select city', 'pok' ),
					'select_district'               => __( 'Select district', 'pok' ),
				)
			);
			wp_localize_script(
				'pok-coupon', 'pok_nonces', array(
					'get_list_city'             => wp_create_nonce( 'get_list_city' ),
					'get_list_district'         => wp_create_nonce( 'get_list_district' ),
				)
			);
			wp_localize_script(
				'pok-coupon', 'pok_data', array(
					'base_api' => $this->setting->get( 'base_api' ),
					'couriers' => $this->setting->get( 'couriers' ),
					'services' => $this->core->get_courier_service()
				)
			);
		}
	}

	/**
	 * Add add ongkir button
	 *
	 * @param object $order Order object.
	 */
	public function add_button_ongkir( $order ) {
		add_thickbox();
		?>
		<div id="pok-switch-shipping" style="display:none;width:300px;">
			<div class="pok-order-shipping-result">
				<div class="loading">
					<img src="<?php echo POK_PLUGIN_URL . '/assets/img/wpspin-2x.gif'; ?>" alt="loading">
				</div>
				<div class="results hidden">
					<table cellspacing="0" cellpadding="0">
						<thead>
							<th><?php esc_html_e( 'Courier', 'pok' ); ?></th>
							<th><?php esc_html_e( 'Service', 'pok' ); ?></th>
							<th><?php esc_html_e( 'Etd', 'pok' ); ?></th>
							<th><?php esc_html_e( 'Cost', 'pok' ); ?></th>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<div class="no-result hidden">
					<p><?php esc_html_e( 'No shipping service found or something wrong happens. Please check your setting.', 'pok' ); ?></p>
				</div>
			</div>
		</div>
		<a class="button add-order-ongkir" data-order-id="<?php echo esc_attr( $order->get_id() ); ?>"><?php esc_html_e( 'Add Shipping (Ongkir)', 'pok' ); ?></a>
		<?php
	}

	/**
	 * Add courier switch button
	 *
	 * @param integer $item_id Item ID.
	 * @param object  $item    Order item object.
	 */
	public function add_switch_button( $item_id, $item ) {
		if ( 'shipping' === $item->get_type() && $item->meta_exists( 'created_by_pok' ) ) {
			if ( $item->meta_exists( 'weight' ) ) {
				$weight = $item->get_meta( 'weight', true );
			} else {
				$order = wc_get_order( $item->get_order_id() );
				$weight = $this->helper->get_order_weight( $order );
			}
			$origin = apply_filters( 'pok_admin_switch_courier_origin', $this->setting->get( 'store_location' )[0], $item );
			?>
			<div class="pok-switch-courier"><a data-id="<?php echo esc_attr( $item_id ); ?>" data-order-id="<?php echo esc_attr( $item->get_order_id() ); ?>" data-weight="<?php echo esc_attr( $weight ); ?>" data-origin="<?php echo esc_attr( $origin ); ?>" class="switch-order-ongkir"><?php esc_html_e( 'Change Service', 'pok' ); ?></a></div>
			<?php
		}
	}

	/**
	 * Hide custom item meta from order
	 *
	 * @param  array $metas Item metas.
	 * @return array        Item metas.
	 */
	public function hide_item_meta( $metas ) {
		$metas[] = 'created_by_pok';
		$metas[] = 'courier';
		$metas[] = 'service';
		return $metas;
	}

	/**
	 * Show shipping weight on order
	 *
	 * @param integer $order_id Order ID.
	 */
	public function add_weight_info( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $order->get_shipping_methods() ) {
			?>
			<tr>
				<td class="label"><?php esc_html_e( 'Shipping weight:', 'pok' ); ?></td>
				<td width="1%"></td>
				<td class="total">
					<span class="amount"><?php echo esc_html( $this->helper->get_order_weight( $order ) . ' kg' ); ?></span>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * Change meta keys
	 *
	 * @param  string $meta_key Original meta key.
	 * @param  object $meta     Meta object.
	 * @param  object $item     Item object.
	 * @return string           Changed meta key.
	 */
	public function change_meta_keys( $meta_key, $meta, $item ) {
		if ( 'etd' === $meta->key ) {
			$meta_key = __( 'Estimated', 'pok' );
		} elseif ( 'insurance' === $meta->key ) {
			$meta_key = __( 'Insurance fee', 'pok' );
		} elseif ( 'timber_packing' === $meta->key ) {
			$meta_key = __( 'Timber packing fee', 'pok' );
		} elseif ( 'markup' === $meta->key ) {
			$meta_key = __( 'Timber packing fee', 'pok' );
		} elseif ( 'weight' === $meta->key ) {
			$meta_key = __( 'Shipping weight', 'pok' );
		} elseif ( 'original_cost' === $meta->key ) {
			$meta_key = __( 'Shipping discount', 'pok' );
		}
		return $meta_key;
	}

	/**
	 * Change meta values
	 *
	 * @param  string $display_value Original value.
	 * @param  object $meta          Meta object.
	 * @param  object $item          Item object.
	 * @return string                Changed value.
	 */
	public function change_meta_values( $display_value, $meta, $item ) {
		if ( 'etd' === $meta->key ) {
			if ( ! empty( $meta->value ) ) {
				$display_value = $meta->value . ' ' . __( 'day(s)', 'pok' );
			} else {
				$display_value = '-';
			}
		} elseif ( 'insurance' === $meta->key ) {
			$display_value = wc_price( $meta->value );
		} elseif ( 'timber_packing' === $meta->key ) {
			$display_value = wc_price( $meta->value );
		} elseif ( 'markup' === $meta->key ) {
			$display_value = wc_price( $meta->value );
		} elseif ( 'weight' === $meta->key ) {
			$display_value = $meta->value . ' kg';
		} elseif ( 'original_cost' === $meta->key ) {
			$display_value = wc_price( floatval( $item->get_total() ) - floatval( $meta->value ) );
		}
		return $display_value;
	}

	/**
	 * Add district to customer data
	 *
	 * @param  array  $data     Customer data.
	 * @param  object $customer Customer object.
	 * @param  int    $user_id  User ID.
	 * @return array            Customer data.
	 */
	public function get_district_from_meta( $data, $customer, $user_id ) {
		$data['billing']['state']         = $this->helper->get_address_id_from_user( $user_id, 'billing', 'state' );
		$data['billing']['pok_city']      = $this->helper->get_address_id_from_user( $user_id, 'billing', 'city' );
		$data['billing']['pok_district']  = $this->helper->get_address_id_from_user( $user_id, 'billing', 'district' );
		$data['shipping']['state']        = $this->helper->get_address_id_from_user( $user_id, 'shipping', 'state' );
		$data['shipping']['pok_city']     = $this->helper->get_address_id_from_user( $user_id, 'shipping', 'city' );
		$data['shipping']['pok_district'] = $this->helper->get_address_id_from_user( $user_id, 'shipping', 'district' );
		if ( 'ID' === $data['billing']['country'] ) {
			$data['billing']['state_options'] = $this->core->get_province();
			if ( 0 !== intval( $data['billing']['state'] ) ) {
				$data['billing']['city_options'] = $this->core->get_city( intval( $data['billing']['state'] ) );
			}
			if ( 'pro' === $this->helper->get_license_type() && 0 !== intval( $data['billing']['pok_city'] ) ) {
				$data['billing']['district_options'] = $this->core->get_district( intval( $data['billing']['pok_city'] ) );
			}
		}
		if ( 'ID' === $data['shipping']['country'] ) {
			$data['shipping']['state_options'] = $this->core->get_province();
			if ( 0 !== intval( $data['shipping']['state'] ) ) {
				$data['shipping']['city_options'] = $this->core->get_city( intval( $data['shipping']['state'] ) );
			}
			if ( 'pro' === $this->helper->get_license_type() && 0 !== intval( $data['shipping']['pok_city'] ) ) {
				$data['shipping']['district_options'] = $this->core->get_district( intval( $data['shipping']['pok_city'] ) );
			}
		}
		return $data;
	}

	/**
	 * Handle save address data to order meta
	 *
	 * @param  integer $order_id Order ID.
	 * @param  array   $data     Order data.
	 */
	public function save_addresses_meta( $order_id, $data ) {
		$order = wc_get_order( $order_id );
		if ( isset( $_POST['_billing_country'] ) && 'ID' === sanitize_text_field( wp_unslash( $_POST['_billing_country'] ) ) ) { // WPCS: Input var okay. CSRF okay.
			if ( isset( $_POST['_billing_state'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['_billing_state'] ) ) { // WPCS: Input var okay. CSRF okay.
					$province = $this->core->get_single_province( intval( $_POST['_billing_state'] ) ); // WPCS: Input var okay. CSRF okay.
					$order->update_meta_data( '_billing_state', ( isset( $province ) && ! empty( $province ) ? $province : sanitize_text_field( wp_unslash( $_POST['_billing_state'] ) ) ) ); // WPCS: Input var okay. CSRF okay.
					$order->update_meta_data( '_billing_pok_state', sanitize_text_field( wp_unslash( $_POST['_billing_state'] ) ) ); // WPCS: Input var okay. CSRF okay.
				}
			}
			if ( isset( $_POST['_billing_pok_city'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['_billing_pok_city'] ) ) { // WPCS: Input var okay. CSRF okay.
					$city = $this->core->get_single_city_without_province( intval( $_POST['_billing_pok_city'] ) ); // WPCS: Input var okay. CSRF okay.
					$order->update_meta_data( '_billing_city', ( isset( $city ) && ! empty( $city ) ? $city : sanitize_text_field( wp_unslash( $_POST['_billing_pok_city'] ) ) ) ); // WPCS: Input var okay. CSRF okay.
					$order->update_meta_data( '_billing_pok_city', sanitize_text_field( wp_unslash( $_POST['_billing_pok_city'] ) ) ); // WPCS: Input var okay. CSRF okay.
				}
			}
			if ( isset( $_POST['_billing_pok_district'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['_billing_pok_district'] ) ) { // WPCS: Input var okay. CSRF okay.
					$district = $this->core->get_single_district( intval( $_POST['_billing_pok_city'] ), intval( $_POST['_billing_pok_district'] ) ); // WPCS: Input var okay. CSRF okay.
					$order->update_meta_data( '_billing_district', ( isset( $district ) && ! empty( $district ) ? $district : sanitize_text_field( wp_unslash( $_POST['_billing_pok_district'] ) ) ) ); // WPCS: Input var okay. CSRF okay.
					$order->update_meta_data( '_billing_pok_district', sanitize_text_field( wp_unslash( $_POST['_billing_pok_district'] ) ) ); // WPCS: Input var okay. CSRF okay.
				}
			}
		}
		if ( isset( $_POST['_shipping_country'] ) && 'ID' === sanitize_text_field( wp_unslash( $_POST['_shipping_country'] ) ) ) { // WPCS: Input var okay. CSRF okay.
			if ( isset( $_POST['_shipping_state'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['_shipping_state'] ) ) { // WPCS: Input var okay. CSRF okay.
					$province = $this->core->get_single_province( intval( $_POST['_shipping_state'] ) ); // WPCS: Input var okay. CSRF okay.
					$order->update_meta_data( '_shipping_state', ( isset( $province ) && ! empty( $province ) ? $province : sanitize_text_field( wp_unslash( $_POST['_shipping_state'] ) ) ) ); // WPCS: Input var okay. CSRF okay.
					$order->update_meta_data( '_shipping_pok_state', sanitize_text_field( wp_unslash( $_POST['_shipping_state'] ) ) ); // WPCS: Input var okay. CSRF okay.
				}
			}
			if ( isset( $_POST['_shipping_pok_city'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['_shipping_pok_city'] ) ) { // WPCS: Input var okay. CSRF okay.
					$city = $this->core->get_single_city_without_province( intval( $_POST['_shipping_pok_city'] ) ); // WPCS: Input var okay. CSRF okay.
					$order->update_meta_data( '_shipping_city', ( isset( $city ) && ! empty( $city ) ? $city : sanitize_text_field( wp_unslash( $_POST['_shipping_pok_city'] ) ) ) ); // WPCS: Input var okay. CSRF okay.
					$order->update_meta_data( '_shipping_pok_city', sanitize_text_field( wp_unslash( $_POST['_shipping_pok_city'] ) ) ); // WPCS: Input var okay. CSRF okay.
				}
			}
			if ( isset( $_POST['_shipping_pok_district'] ) ) { // WPCS: Input var okay. CSRF okay.
				if ( 0 !== intval( $_POST['_shipping_pok_district'] ) ) { // WPCS: Input var okay. CSRF okay.
					$district = $this->core->get_single_district( intval( $_POST['_shipping_pok_city'] ), intval( $_POST['_shipping_pok_district'] ) ); // WPCS: Input var okay. CSRF okay.
					$order->update_meta_data( '_shipping_district', ( isset( $district ) && ! empty( $district ) ? $district : sanitize_text_field( wp_unslash( $_POST['_shipping_pok_district'] ) ) ) ); // WPCS: Input var okay. CSRF okay.
					$order->update_meta_data( '_shipping_pok_district', sanitize_text_field( wp_unslash( $_POST['_shipping_pok_district'] ) ) ); // WPCS: Input var okay. CSRF okay.
				}
			}
		}
		$order->save();
	}

	/**
	 * Change Google Maps URL on orders table
	 * 
	 * @param  array $address Old address parts.
	 * @param  object $order   WC_Order object.
	 * @return array          New address parts.
	 */
	public function change_address_map_url( $address, $order ) {
		if ( '' !== ( $pok_api = $order->get_meta( '_pok_data_api', true ) ) ) {
			$address['state'] = $order->get_meta( '_shipping_state', true );
			$address['city'] = $order->get_meta( '_shipping_city', true );
			if ( isset( $address['district'] ) ) {
				$address['city'] = $order->get_meta( '_shipping_district', true ) . ', ' . $address['city'];
				unset( $address['district'] );
			}
		}
		return $address;
	}

	/**
	 * Register report tab
	 *
	 * @param  array $tabs Report tabs.
	 * @return array       Report tabs.
	 */
	public function shipping_report( $tabs ) {
		$tabs['pok_shipping'] = array(
			'title'   => __( 'Shipping Couriers', 'pok' ),
			'reports' => array(
				'all' => array(
					'title'       => __( 'All Courier', 'pok' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( $this, 'get_report' ),
				),
			),
		);
		foreach ( $this->core->get_courier( $this->setting->get( 'base_api' ), $this->helper->get_license_type() ) as $courier ) {
			$tabs['pok_shipping']['reports'][ $courier ] = array(
				'title'       => $this->helper->get_courier_name( $courier ),
				'description' => '',
				'hide_title'  => true,
				'callback'    => array( $this, 'get_report' ),
			);
		}
		return $tabs;
	}

	/**
	 * Display report
	 *
	 * @param  string $courier Courier name.
	 */
	public function get_report( $courier ) {
		$courier = in_array( $courier, $this->core->get_courier( $this->setting->get('base_api'), $this->helper->get_license_type() ), true ) ? $courier : 'all';
		if ( class_exists('Automattic\WooCommerce\Utilities\OrderUtil') && Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$report = new POK_Report_Table_HPOS( $courier );
		} else {
			$report = new POK_Report_Table( $courier );
		}
		$report->output_report();
	}

	/**
	 * Add new coupon types
	 * 
	 * @param  array $coupon_types Coupon types.
	 * @return array               Coupon types.
	 */
	public function new_coupon_types( $coupon_types ) {
		$coupon_types['ongkir'] = __( 'Shipping discount (by Plugin Ongkos Kirim)', 'pok' );
		return $coupon_types;
	}

	/**
	 * Add new coupon restriction tab
	 * 
	 * @param  array $tabs Coupon data tab.
	 * @return array       Coupon data tab.
	 */
	public function coupon_shipping_restriction_tab( $tabs ) {
		$new_tabs = array_slice( $tabs, 0, 1, true ) +
			array( "shipping_restriction" => array(
				'label'  => __( 'Shipping restriction', 'pok' ),
				'target' => 'shipping_restriction_coupon_data',
				'class'  => 'tab-shipping-restriction',
			) ) +
			array_slice( $tabs, 1, count($tabs) - 1, true );
		return $new_tabs;
	}

	/**
	 * Add new coupon restriction panel
	 * 
	 * @param  integer $coupon_id Coupon ID.
	 * @param  object  $coupon    Coupon data.
	 */
	public function coupon_shipping_restriction_panel( $coupon_id, $coupon ) {
		$ship_res 		= get_post_meta( $coupon_id, 'shipping_restriction', true );
		$res_couriers 	= isset( $ship_res['courier'] ) ? $ship_res['courier'] : array();
		$res_services 	= isset( $ship_res['service'] ) ? $ship_res['service'] : array();
		$couriers 		= $this->setting->get( 'couriers' );
		$services 		= $this->core->get_courier_service();
		$provinces  	= $this->core->get_province();
		?>
		<div id="shipping_restriction_coupon_data" class="panel woocommerce_options_panel">
			<div class="options_group">
				<p class="form-field">
					<label for="shipping_restriction_min_weight"><?php _e( 'Minimum weight', 'pok' ); ?> (<?php echo esc_attr( get_option('woocommerce_weight_unit') ) ?>)</label>
					<input type="number" min="0" id="shipping_restriction_min_weight" name="shipping_restriction[min_weight]" style="width: 50%;" data-placeholder="<?php esc_attr_e( 'Minimum shipping weight', 'pok' ); ?>" value="<?php echo isset( $ship_res['min_weight'] ) ? esc_attr( $ship_res['min_weight'] ) : '' ?>">
					<?php echo wc_help_tip( __( 'Left this field empty if the coupon does not need a minimum shipping weight.', 'pok' ) ); ?>
				</p>
				<p class="form-field">
					<label for="shipping_restriction_max_weight"><?php _e( 'Maximum weight', 'pok' ); ?> (<?php echo esc_attr( get_option('woocommerce_weight_unit') ) ?>)</label>
					<input type="number" min="0" id="shipping_restriction_max_weight" name="shipping_restriction[max_weight]" style="width: 50%;" data-placeholder="<?php esc_attr_e( 'Maximum shipping weight', 'pok' ); ?>" value="<?php echo isset( $ship_res['min_weight'] ) ? esc_attr( $ship_res['max_weight'] ) : '' ?>">
					<?php echo wc_help_tip( __( 'Left this field empty if the coupon does not need a maximum shipping weight.', 'pok' ) ); ?>
				</p>
				<p class="form-field">
					<label for="shipping_restriction_courier"><?php _e( 'Couriers', 'pok' ); ?></label>
					<select id="shipping_restriction_courier" name="shipping_restriction[courier][]" style="width: 90%;"  class="init-select2" multiple="multiple" data-placeholder="<?php esc_attr_e( 'All couriers', 'pok' ); ?>">
						<?php
						foreach ( $couriers as $courier ) {
							echo '<option value="' . esc_attr( $courier ) . '"' . ( in_array( $courier, $res_couriers, true ) ? 'selected' : '' ) . '>' . esc_html( $this->helper->get_courier_name( $courier ) ) . '</option>';
						}
						?>
					</select>
					<?php echo wc_help_tip( __( 'Left this field empty if the coupon is valid for all shipping couriers.', 'pok' ) ); ?>
				</p>
				<?php if ( 'nusantara' === $this->setting->get('base_api') ) : ?>
					<p class="form-field">
						<label for="shipping_restriction_service"><?php _e( 'Services', 'pok' ); ?></label>
						<select id="shipping_restriction_service" name="shipping_restriction[service][]" style="width: 90%;"  class="init-select2" multiple="multiple" data-placeholder="<?php esc_attr_e( 'All services', 'pok' ); ?>">
							<?php
							foreach ( $services as $cou => $ser ) {
								if ( empty( $res_couriers ) || in_array( $cou, $res_couriers ) ) {
									foreach ( $ser as $ser_slug => $service ) {
										echo '<option value="' . esc_attr( $cou . '-' . $ser_slug ) . '"' . ( in_array( $cou . '-' . $ser_slug, $res_services, true ) ? 'selected' : '' ) . '>' . esc_html( $this->helper->get_courier_name( $cou ) ) . ' - ' . $service['name'] . '</option>';
									}
								}
							}
							?>
						</select>
					</p>
				<?php endif; ?>
				<div class="form-field">
					<label for="shipping_restriction_destination"><?php _e( 'Destination', 'pok' ); ?></label>
					<div class="pok-coupon-destination">
						<table class="">
							<tbody>
								<tr class="repeater">
									<td>
										<select class="select_province">
											<option value=""><?php esc_html_e( 'All Province', 'pok' ); ?></option>
											<?php if ( ! empty( $provinces ) ) : ?>
												<?php foreach ( $provinces as $province_id => $province ) : ?>
													<option value="<?php echo esc_attr( $province_id ); ?>"><?php echo esc_html( $province ); ?></option>
												<?php endforeach; ?>
											<?php endif; ?>
										</select>
									</td>
									<td>
										<select class="select_city">
											<option value=""><?php esc_html_e( 'All City', 'pok' ); ?></option>
										</select>
									</td>
									<td>
										<select class="select_district">
											<option value=""><?php esc_html_e( 'All District', 'pok' ); ?></option>
										</select>
									</td>
									<td style="width: 1%;">
										<a class="button remove-manual"><?php esc_html_e( 'Delete', 'pok' ); ?></a>
									</td>
								</tr>
								<?php if ( isset( $ship_res['destination'] ) ) : ?>
									<?php foreach ( $ship_res['destination'] as $key => $destination ) : ?>
										<tr class="base">
											<td>
												<select class="select_province" name="shipping_restriction[destination][<?php echo esc_attr( $key ) ?>][province]">
													<option value=""><?php esc_html_e( 'All Province', 'pok' ); ?></option>
													<?php if ( ! empty( $provinces ) ) : ?>
														<?php foreach ( $provinces as $province_id => $province ) : ?>
															<option <?php echo isset( $destination['province'] ) && $province_id == $destination['province'] ? 'selected' : '' ?> value="<?php echo esc_attr( $province_id ); ?>"><?php echo esc_html( $province ); ?></option>
														<?php endforeach; ?>
													<?php endif; ?>
												</select>
											</td>
											<td>
												<select class="select_city" name="shipping_restriction[destination][<?php echo esc_attr( $key ) ?>][city]">
													<option value=""><?php esc_html_e( 'All City', 'pok' ); ?></option>
													<?php
														if ( isset( $destination['province'] ) && in_array( $destination['province'], array_keys( $provinces ) ) ) {
															$cities 	= $this->core->get_city( $destination['province'] );
														} else {
															$cities 	= array();
														}
													?>
													<?php if ( ! empty( $cities ) ) : ?>
														<?php foreach ( $cities as $city_id => $city ) : ?>
															<option <?php echo isset( $destination['city'] ) && $city_id == $destination['city'] ? 'selected' : '' ?> value="<?php echo esc_attr( $city_id ); ?>"><?php echo esc_html( $city ); ?></option>
														<?php endforeach; ?>
													<?php endif; ?>
												</select>
											</td>
											<td>
												<select class="select_district" name="shipping_restriction[destination][<?php echo esc_attr( $key ) ?>][district]">
													<option value=""><?php esc_html_e( 'All District', 'pok' ); ?></option>
													<?php
														if ( 'pro' === $this->helper->get_license_type() && isset( $destination['city'] ) && in_array( $destination['city'], array_keys( $cities ) ) ) {
															$districts 	= $this->core->get_district( $destination['city'] );
														} else {
															$districts 	= array();
														}
													?>
													<?php if ( ! empty( $districts ) ) : ?>
														<?php foreach ( $districts as $district_id => $district ) : ?>
															<option <?php echo isset( $destination['district'] ) && $district_id == $destination['district'] ? 'selected' : '' ?> value="<?php echo esc_attr( $district_id ); ?>"><?php echo esc_html( $district ); ?></option>
														<?php endforeach; ?>
													<?php endif; ?>
												</select>
											</td>
											<td style="width: 1%;">
												<a class="button remove-manual"><?php esc_html_e( 'Delete', 'pok' ); ?></a>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr class="base">
										<td>
											<select class="select_province" name="shipping_restriction[destination][0][province]">
												<option value=""><?php esc_html_e( 'All Province', 'pok' ); ?></option>
												<?php if ( ! empty( $provinces ) ) : ?>
													<?php foreach ( $provinces as $province_id => $province ) : ?>
														<option value="<?php echo esc_attr( $province_id ); ?>"><?php echo esc_html( $province ); ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
										</td>
										<td>
											<select class="select_city" name="shipping_restriction[destination][0][city]">
												<option value=""><?php esc_html_e( 'All City', 'pok' ); ?></option>
											</select>
										</td>
										<td>
											<select class="select_district" name="shipping_restriction[destination][0][district]">
												<option value=""><?php esc_html_e( 'All District', 'pok' ); ?></option>
											</select>
										</td>
										<td style="width: 1%;">
											<a class="button remove-manual"><?php esc_html_e( 'Delete', 'pok' ); ?></a>
										</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
						<button class="add-destination button" type="button"><?php esc_html_e( 'Add Destination', 'pok' ) ?></button>
					</div>
					<?php
						echo wc_help_tip( __( 'Specify the shipping destination where the discount should apply.', 'pok' ) );
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Create new field to coupon data
	 * 
	 * @param  integer $coupon_id Coupon ID.
	 * @param  object  $coupon    Coupon data.
	 */
	public function new_coupon_data_field( $coupon_id, $coupon ) {
		woocommerce_wp_select(
			array(
				'id'      => 'shipping_discount_type',
				'label'   => __( 'Shipping discount type', 'pok' ),
				'options' => array(
					'free'		=> __( 'Free shipping', 'pok' ),
					'fixed'		=> __( 'Fixed discount', 'pok' ),
					'percent'	=> __( 'Percentage discount', 'pok' )
				),
				'value'   => get_post_meta( $coupon_id, 'shipping_discount_type', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => 'shipping_discount_amount',
				'label'       => __( 'Discount amount', 'pok' ),
				'placeholder' => wc_format_localized_price( 0 ),
				'description' => __( 'Value of the coupon.', 'pok' ),
				'data_type'   => 'price',
				'desc_tip'    => true,
				'value'       => get_post_meta( $coupon_id, 'shipping_discount_amount', true ),
			)
		);
	}

	/**
	 * Handle save coupon
	 * 
	 * @param  integer $coupon_id Coupon ID.
	 * @param  object  $coupon    Coupon data.
	 */
	public function shipping_coupon_save( $coupon_id, $coupon ) {
		if ( $coupon->is_type( 'ongkir' ) ) {
			if ( isset( $_POST['shipping_discount_type'] ) && ! empty( $_POST['shipping_discount_type'] ) ) {
				update_post_meta( $coupon_id, 'shipping_discount_type', sanitize_text_field( wp_unslash( $_POST['shipping_discount_type'] ) ) );
			}
			if ( isset( $_POST['shipping_discount_amount'] ) && ! empty( $_POST['shipping_discount_amount'] ) ) {
				update_post_meta( $coupon_id, 'shipping_discount_amount', floatval( $_POST['shipping_discount_amount'] ) );
			}
			if ( isset( $_POST['shipping_restriction'] ) ) {
				$input = $_POST['shipping_restriction'];
				$coupon_data = array(
					'min_weight'	=> isset( $input['min_weight'] ) && is_numeric( $input['min_weight'] ) ? floatval( $input['min_weight'] ) : '',
					'max_weight'	=> isset( $input['max_weight'] ) && is_numeric( $input['max_weight'] ) ? floatval( $input['max_weight'] ) : '',
					'courier'		=> isset( $input['courier'] ) && ! empty( $input['courier'] ) ? $input['courier'] : array(),
					'service'		=> isset( $input['service'] ) && ! empty( $input['service'] ) ? $input['service'] : array(),
					'destination'	=> isset( $input['destination'] ) && ! empty( $input['destination'] ) ? $input['destination'] : array(),
				);
				update_post_meta( $coupon_id, 'shipping_restriction', $coupon_data );
			}
			$coupon->set_amount( 0 );
			$coupon->save();
		}
	}

}
