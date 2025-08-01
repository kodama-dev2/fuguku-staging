<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cekongkir {
	
	private $base_country = 'ID';

	private static $instance = null;


	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Cekongkir();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		// Hook to load plugin textdomain.
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Hook to add plugin action links.
		add_action( 'plugin_action_links_' . plugin_basename( CEKONGKIR_FILE ), array( $this, 'plugin_action_links' ) );

		// Hook to register the integration.
		add_filter("woocommerce_integrations", [$this, "register_integrations"]);

		// Hook to check if this shipping method is available for current order.
		add_filter( 'woocommerce_shipping_' . CEKONGKIR_METHOD_ID . '_is_available', array( $this, 'check_is_available' ), 10, 2 );

		// Hook to modify the default country selections after a country is chosen.
		add_filter( 'woocommerce_get_country_locale', array( $this, 'get_country_locale' ) );

		// Hook to  print hidden element for the hidden address 2 field after the shipping calculator form.
		// add_action( 'woocommerce_after_shipping_calculator', array( $this, 'after_shipping_calculator' ) );

		add_filter('woocommerce_shipping_calculator_enable_city', '__return_false');
		add_filter('woocommerce_shipping_calculator_enable_postcode', '__return_false');
		add_filter('woocommerce_shipping_calculator_enable_state', '__return_false');

		add_filter('woocommerce_checkout_fields', array( $this, 'customize_checkout_fields' ));

		// add_filter('woocommerce_checkout_fields',  array( $this, 'add_checkout_destination_dropdown' ));

		// Hook to enqueue scripts & styles assets in backend area.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_assets' ), 999 );

		// Hook to enqueue scripts & styles assets in frontend area.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ), 999 );

		// Enqueue scripts
		add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts'], 999);
		add_action('wp_enqueue_scripts',[$this, 'enqueue_checkout_destination_script'], 999 );

		// Add dropdown to Cart
		add_action('woocommerce_after_shipping_calculator', [$this, 'add_cart_destination_dropdown']);
		add_action('woocommerce_after_checkout_billing_form', [$this, 'add_cart_destination_dropdown']);

		// AJAX search destination
		add_action('wp_ajax_cart_search_destination', [$this, 'cart_search_destination']);
		add_action('wp_ajax_nopriv_cart_search_destination', [$this, 'cart_search_destination']);
		

		// AJAX update destination
		add_action('wp_ajax_update_destination', [$this, 'update_destination']);
		add_action('wp_ajax_nopriv_update_destination', [$this, 'update_destination']);

		add_action('wp_ajax_search_origin_locations', array($this, 'search_origin_locations_callback'));
		add_action('wp_ajax_nopriv_search_origin_locations',  array($this, 'search_origin_locations_callback'));	
		
		add_action('wp_ajax_save_origin_location', [$this, 'save_origin_location']); 
		add_action('wp_ajax_nopriv_save_origin_location', [$this, 'save_origin_location']);
		add_action('wp_ajax_get_saved_origin_location', [$this, 'get_saved_origin_location'] );

		add_action('wp_ajax_recalculate_shipping', [$this, 'recalculate_shipping']);
		add_action('wp_ajax_nopriv_recalculate_shipping', [$this, 'recalculate_shipping']);


		

		// Hook to declare compatibility with the High-Performance Order Storage.
		add_action(
			'before_woocommerce_init',
			function() {
				if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', CEKONGKIR_FILE, true );
				}
			}
		);

		// Hook to declare incompatibility with the Cart and Checkout Blocks.
		add_action(
			'before_woocommerce_init',
			function() {
				if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', CEKONGKIR_FILE, false );
				}
			}
		);
	}


	function get_saved_origin_location() {
		check_ajax_referer('cekongkir_nonce', 'security');
	
		$saved_id = get_option('origin_location_destination', '');
		$saved_label = get_option('origin_location_label', '');
	
		if (!$saved_id) {
			wp_send_json_error('No saved location found');
		}
	
		wp_send_json_success(array(
			'id'   => $saved_id,
			'text' => $saved_label
		));
	}
	


	function save_origin_location() {
		check_ajax_referer('cekongkir_nonce', 'security');

		if (!isset($_POST['origin_location_id']) || !isset($_POST['origin_location_label'])) {
			wp_send_json_error('Missing data');
		}

		update_option('origin_location_destination', sanitize_text_field($_POST['origin_location_id']));
		update_option('origin_location_label', sanitize_text_field($_POST['origin_location_label']));

		wp_send_json_success('Origin location saved successfully');
	}
	

	function search_origin_locations_callback() {
		if (!isset($_GET['term'])) {
			wp_send_json([]);
		}
	
		$search_term = sanitize_text_field($_GET['term']);

		$api = Cekongkir_API::get_instance();
		$locations = $api->search_destination_api($search_term);
	
		if (!is_array($locations)) {
			error_log("API did not return an array.");
			wp_send_json_error(['error' => 'Invalid API response format']);
		}
	
		$results = [];
		foreach ($locations as $location) {
			if (!isset($location['id']) || !isset($location['text'])) {
				continue;
			}
	
			$results[] = [
				'id'   => (string) $location['id'],
				'text' => $location['text']
			];
		}
	
		wp_send_json_success($results);
		wp_die();
	}
	

	/**
	 * Check if this method available
	 *
	 * @since 1.0.0
	 * @param boolean $available Current status is available.
	 * @param array   $package Current order package data.
	 * @return bool
	 */
	public function check_is_available( $available, $package ) {
		if ( WC()->countries->get_base_country() !== $this->base_country ) {
			return false;
		}

		if ( empty( $package ) || empty( $package['contents'] ) || empty( $package['destination'] ) ) {
			return false;
		}

		return $available;
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'cekongkir', false, basename( CEKONGKIR_PATH ) . '/languages' );
	}

	/**
	 * Add plugin action links.
	 *
	 * Add a link to the settings page on the plugins.php page.
	 *
	 * @since 1.1.3
	 *
	 * @param  array $links List of existing plugin action links.
	 * @return array         List of modified plugin action links.
	 */
	public function plugin_action_links( $links ) {
		$zone_id = 0;

		if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
			return $links;
		}

		foreach ( WC_Shipping_Zones::get_zones() as $zone ) {
			if ( empty( $zone['shipping_methods'] ) || empty( $zone['zone_id'] ) ) {
				continue;
			}

			foreach ( $zone['shipping_methods'] as $zone_shipping_method ) {
				if ( $zone_shipping_method instanceof Cekongkir ) {
					$zone_id = $zone['zone_id'];
					break;
				}
			}

			if ( $zone_id ) {
				break;
			}
		}

		$links = array_merge(
			array(
				'<a href="' . esc_url(
					add_query_arg(
						array(
							'page'              => 'wc-settings',
							'tab'               => 'shipping',
							'zone_id'           => $zone_id,
							'cekongkir_settings' => true,
						),
						admin_url( 'admin.php' )
					)
				) . '">' . __( 'Settings', 'cekongkir' ) . '</a>',
			),
			$links
		);

		return $links;
	}


	/**
	 * Enqueue backend scripts.
	 *
	 * @since 1.0.0
	 * @param string $hook Passed screen ID in admin area.
	 */
	public function enqueue_backend_assets( $hook = null ) {
		if ( ! is_admin() ) {
			return;
		}
	
		$is_dev_env = cekongkir_is_dev();
	
		if ( 'woocommerce_page_wc-settings' === $hook ) {
			// Define the styles URL.
			$css_url = CEKONGKIR_URL . 'assets/css/cekongkir-backend.min.css';
			if ( $is_dev_env ) {
				$css_url = add_query_arg( array( 't' => time() ), str_replace( '.min', '', $css_url ) );
			}
	
			// Enqueue admin styles.
			wp_enqueue_style(
				'cekongkir-backend',
				$css_url,
				array(),
				cekongkir_get_plugin_data( 'Version' ),
				false
			);
	
			// Register lockr.js scripts.
			$lockr_url = CEKONGKIR_URL . 'assets/js/lockr.min.js';
			if ( $is_dev_env ) {
				$lockr_url = add_query_arg( array( 't' => time() ), str_replace( '.min', '', $lockr_url ) );
			}
	
			wp_register_script(
				'lockr.js',
				$lockr_url,
				array( 'jquery' ),
				cekongkir_get_plugin_data( 'Version' ),
				true
			);
	
			// Define the scripts URL.
			$js_url = CEKONGKIR_URL . 'assets/js/cekongkir-backend.min.js';
			if ( $is_dev_env ) {
				$js_url = add_query_arg( array( 't' => time() ), str_replace( '.min', '', $js_url ) );
			}
	
			wp_enqueue_script(
				'cekongkir-backend',
				$js_url,
				array( 'jquery', 'accordion', 'wp-util', 'selectWoo', 'lockr.js' ),
				cekongkir_get_plugin_data( 'Version' ),
				true
			);
	
			wp_localize_script(
				'cekongkir-backend',
				'cekongkir_params',
				cekongkir_scripts_params(
					array(
						'method_id'    => CEKONGKIR_METHOD_ID,
						'method_title' => cekongkir_get_plugin_data( 'Name' ),
					)
				)
			);
	
			$select2_js_url = CEKONGKIR_URL . 'assets/js/cekongkir-select2.js';
			if ( $is_dev_env ) {
				$select2_js_url = add_query_arg( array( 't' => time() ), str_replace( '.min', '', $select2_js_url ) );
			}
	
			wp_enqueue_script(
				'cekongkir-select2', 
				$select2_js_url,
				array( 'jquery', 'selectWoo' ), 
				cekongkir_get_plugin_data( 'Version' ),
				true
			);
	
			// Kirim data ke JavaScript jika diperlukan
			wp_localize_script(
				'cekongkir-select2',
				'cekongkir_select2_params',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				)
			);
		}
	}
	
	

	/**
	 * Enqueue frontend scripts.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_frontend_assets() {
		if ( is_admin() || ! cekongkir_instances() ) {
			return;
		}

		$is_enqueue_assets = apply_filters( 'cekongkir_enqueue_frontend_assets', ( is_cart() || is_checkout() || is_account_page() ) );

		if ( ! $is_enqueue_assets ) {
			return;
		}

		$is_dev_env = cekongkir_is_dev();

		// Register lockr.js scripts.
		$lockr_url = CEKONGKIR_URL . 'assets/js/lockr.min.js';
		if ( $is_dev_env ) {
			$lockr_url = add_query_arg( array( 't' => time() ), str_replace( '.min', '', $lockr_url ) );
		}

		wp_register_script(
			'lockr.js', // Give the script a unique ID.
			$lockr_url, // Define the path to the JS file.
			array(), // Define dependencies.
			cekongkir_get_plugin_data( 'Version' ), // Define a version (optional).
			true // Specify whether to put in footer (leave this true).
		);

		// Enqueue main scripts.
		$js_url = CEKONGKIR_URL . 'assets/js/cekongkir-frontend.js';
		if ( $is_dev_env ) {
			$js_url = add_query_arg( array( 't' => time() ), str_replace( '.min', '', $js_url ) );
		}

		wp_enqueue_script(
			'cekongkir-frontend', // Give the script a unique ID.
			$js_url, // Define the path to the JS file.
			array( 'jquery', 'wp-util', 'selectWoo', 'lockr.js' ), // Define dependencies.
			cekongkir_get_plugin_data( 'Version' ), // Define a version (optional).
			true // Specify whether to put in footer (leave this true).
		);

		wp_localize_script( 'cekongkir-frontend', 'cekongkir_params', cekongkir_scripts_params() );
	}

	/**
	 * Modify the default country selections after a country is chosen.
	 *
	 * @since 1.3
	 *
	 * @param array $locale Default locale data.
	 *
	 * @return array
	 */
	public function get_country_locale( $locale ) {
		if ( ! cekongkir_instances() || ! isset( $locale['ID'] ) ) {
			return $locale;
		}

		$custom_fields = cekongkir_custom_address_fields();

		foreach ( $custom_fields as $key => $value ) {
			if ( isset( $locale['ID'][ $key ] ) ) {
				$locale['ID'][ $key ] = array_merge( $locale['ID'][ $key ], $value );
			} else {
				$locale['ID'][ $key ] = $value;
			}
		}

		return $locale;
	}

	/**
	 * Inject cart packages to calculate shipping for address 2 field.
	 *
	 * @since 1.1.4
	 * @param array $packages Current cart contents packages.
	 * @return array
	 */
	public function inject_cart_shipping_packages( $packages ) {
		if ( ! cekongkir_instances() ) {
			return $packages;
		}

		$nonce_action    = 'woocommerce-shipping-calculator';
		$nonce_name      = 'woocommerce-shipping-calculator-nonce';
		$address_2_field = 'calc_shipping_address_2';

		if ( isset( $_POST[ $nonce_name ], $_POST[ $address_2_field ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) ), $nonce_action ) ) {
			$address_2 = sanitize_text_field( wp_unslash( $_POST[ $address_2_field ] ) );

			if ( empty( $address_2 ) ) {
				return $packages;
			}

			foreach ( array_keys( $packages ) as $key ) {
				WC()->customer->set_billing_address_2( $address_2 );
				WC()->customer->set_shipping_address_2( $address_2 );
				$packages[ $key ]['destination']['address_2'] = $address_2;
			}
		}

		return $packages;
	}

	/**
	 * Hook to enable city field in the shipping calculator form.
	 *
	 * @since 1.3.2
	 *
	 * @param bool $is_enable Current status is city enabled.
	 *
	 * @return bool
	 */
	public function shipping_calculator_enable_city( $is_enable ) {
		if ( ! cekongkir_instances() ) {
			return $is_enable;
		}

		return true;
	}

	/**
	 * Print hidden element for the hidden address 2 field value
	 * in shipping calculator form.
	 *
	 * @since 1.2.4
	 * @return void
	 */
	public function after_shipping_calculator() {
		if ( ! cekongkir_instances() ) {
			return;
		}

		$enable_address_2 = apply_filters( 'woocommerce_shipping_calculator_enable_address_2', true ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

		if ( ! $enable_address_2 ) {
			return;
		}

		$address_2 = WC()->cart->get_customer()->get_shipping_address_2();
		?>
<input type="hidden" id="cekongkir_calc_shipping_address_2" value="<?php echo esc_attr( $address_2 ); ?>" />
<?php
	}

	/**
	 * Register shipping method to WooCommerce.
	 *
	 * @since 1.0.0
	 *
	 * @param array $methods Registered shipping methods.
	 */



	 public function register_integrations( $methods ) {
		if ( class_exists( 'Cekongkir_Integration' ) ) {
			$methods[ CEKONGKIR_METHOD_ID ] = 'Cekongkir_Integration';
		}

		return $methods;
	}
	

	public function update_destination() {
		check_ajax_referer('cart_destination_nonce', 'nonce');
	
		if (!isset($_POST['country']) || empty($_POST['country'])) {
			error_log('❌ Missing country in request');
			wp_send_json_error(['message' => 'Invalid request: Missing country']);
			return;
		}
	
		$country = sanitize_text_field($_POST['country']);
		$skip_totals = isset($_POST['update_totals']) && $_POST['update_totals'] === 'no';
	
		if ($country === 'ID') {
			if (!isset($_POST['destination_id'], $_POST['destination_label']) || empty($_POST['destination_id']) || empty($_POST['destination_label'])) {
				wp_send_json_error(['message' => 'Destination is required for Indonesia']);
				return;
			}
	
			$destination_id = sanitize_text_field($_POST['destination_id']);
			$destination_label = sanitize_text_field($_POST['destination_label']);
	
			error_log('📍 Destination ID: ' . $destination_id);
			error_log('🏷 Destination Label: ' . $destination_label);
	
			WC()->session->set('selected_destination_id', $destination_id);
			WC()->session->set('selected_destination_label', $destination_label);
		} else {
			error_log('✈️ International shipping: No custom destination needed.');
			WC()->session->__unset('selected_destination_id');
			WC()->session->__unset('selected_destination_label');
		}
	
		WC()->customer->set_shipping_country($country);
		WC()->customer->set_billing_country($country);
	
		error_log('✅ Country updated successfully in WooCommerce');
	
		// If we're skipping totals calculation, just save the session data
		if ($skip_totals) {
			error_log('⏩ Skipping totals calculation as requested');
			wp_send_json_success([
				'success' => true,
				'session_updated' => true,
				'country' => $country,
				'destination_id' => $country === 'ID' ? $destination_id : null,
				'destination_label' => $country === 'ID' ? $destination_label : null
			]);
			return;
		}
	
		// Clear the shipping cache for faster recalculation
		delete_transient('cekongkir_shipping_cache_' . WC()->session->get_customer_id());
		
		// Force WooCommerce to recalculate shipping
		WC()->session->set('shipping_for_package_0', false);
		WC()->cart->calculate_shipping();
		WC()->cart->calculate_totals();
		WC()->cart->set_session();
		WC()->session->set('reload_checkout', true);
	
		error_log('🚀 Shipping recalculated & checkout reloaded');
	
		wp_send_json_success([
			'success' => true,
			'country' => $country,
			'destination_id' => $country === 'ID' ? $destination_id : null,
			'destination_label' => $country === 'ID' ? $destination_label : null
		]);
	}

	public function recalculate_shipping() {
		check_ajax_referer('cart_destination_nonce', 'nonce');
		
		if (!isset($_POST['country']) || empty($_POST['country'])) {
			wp_send_json_error(['message' => 'Invalid request: Missing country']);
			return;
		}
		
		$country = sanitize_text_field($_POST['country']);
		
		delete_transient('cekongkir_shipping_cache_' . WC()->session->get_customer_id());
		
		WC()->session->set('shipping_for_package_0', false);
		WC()->cart->calculate_shipping();
		WC()->cart->calculate_totals();
		WC()->cart->set_session();
		WC()->session->set('reload_checkout', true);
		
		error_log('🚀 Shipping recalculated independently');
		
		wp_send_json_success([
			'success' => true,
			'shipping_recalculated' => true,
			'country' => $country
		]);
	}
		
	
	
	
	
	
		public function enqueue_scripts() {
			if (is_cart() || is_checkout() || is_account_page()) {
				wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
				wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['jquery'], null, true);


				$js_url = CEKONGKIR_URL . 'assets/js/cart-destination.js';
				wp_enqueue_script('cart-destination-dropdown', $js_url, ['jquery', 'select2'], '1.1', true);

				wp_localize_script('cart-destination-dropdown', 'cartDestination', [
					'ajax_url' => admin_url('admin-ajax.php'),
					'nonce'    => wp_create_nonce('cart_destination_nonce'),
				]);
			}
		}

	
		public function customize_checkout_fields($fields) {
			unset($fields['billing']['billing_address_2']);
			unset($fields['billing']['billing_state']); 
			unset($fields['billing']['billing_city']); 
			unset($fields['billing']['billing_province']);
			// unset($fields['billing']['billing_postcode']);

			unset($fields['shipping']['shipping_address_2']);
			unset($fields['shipping']['shipping_state']); 
			unset($fields['shipping']['shipping_city']); 
			unset($fields['shipping']['shipping_province']);
			// unset($fields['shipping']['shipping_postcode']); 
			
			return $fields;
		}


		
		public function add_cart_destination_dropdown() {
			$selected_destination_id = WC()->session->get('selected_destination_id', ''); 
			$selected_destination_label = WC()->session->get('selected_destination_label', ''); 
		
			if (is_checkout()) {
				// 🛒 Checkout Page
				echo '<div id="cart-destination-field" class="form-row form-row-wide validate-required" style="display: none;">';
				echo '<label>' . esc_html__('Select Destination', 'cekongkir') . ' <abbr class="required" title="required">*</abbr></label>';
				echo '<select id="cart-destination" name="cart_destination" class="select2" style="width: 100%;" required>';
				echo '<option value="">' . esc_html__('Search and select location...', 'cekongkir') . '</option>';
		
				if (!empty($selected_destination_id) && !empty($selected_destination_label)) {
					echo '<option value="' . esc_attr($selected_destination_id) . '" selected="selected">' . esc_html($selected_destination_label) . '</option>';
				}
		
				echo '</select>';
				echo '<input type="hidden" name="cart_destination_label" id="cart-destination-label" value="' . esc_attr($selected_destination_label) . '" />';
				echo '</div>';
			} else {
				// 🛍️ Cart Page
				echo '<div id="cart-destination-wrapper" style="display: none;">';
				echo '<select id="cart-destination" name="cart_destination" class="select2" required>';
				echo '<option value="">' . esc_html__('Search and select location...', 'cekongkir') . '</option>';
		
				if (!empty($selected_destination_id) && !empty($selected_destination_label)) {
					echo '<option value="' . esc_attr($selected_destination_id) . '" selected>' . esc_html($selected_destination_label) . '</option>';
				}
		
				echo '</select>';
				echo '<input type="hidden" id="cart-destination-label" name="cart_destination_label" value="' . esc_attr($selected_destination_label) . '">';
				echo '</div>';
			}
		}
		
		public function enqueue_checkout_destination_script() {
			if (is_checkout()) {
				wp_enqueue_script('checkout-destination', plugin_dir_url(__FILE__) . 'assets/js/checkout-destination.js', ['jquery', 'select2'], null, true);
		
				wp_localize_script('checkout-destination', 'cartDestination', [
					'ajax_url' => admin_url('admin-ajax.php'),
					'nonce'    => wp_create_nonce('cart_destination_nonce')
				]);
			}
		}
		


		public function cart_search_destination() {
			check_ajax_referer('cart_destination_nonce', 'nonce');
		
			$search_query = sanitize_text_field($_GET['query'] ?? '');
			$country = sanitize_text_field($_GET['country'] ?? 'ID'); 
			if (empty($search_query)) {
				wp_send_json_error(__('Masukkan lokasi tujuan.', 'cekongkir'));
			}
		
			$api = Cekongkir_API::get_instance();
			$zone = strtolower(trim($api->get_zone_by_country($country)));
		
			if ($zone === 'international') {
				$results = $api->search_international_destination_api($search_query);
			} else {
				$results = $api->search_destination_api($search_query);
			}
	
		
			wp_send_json_success($results);
		}
		
		
}