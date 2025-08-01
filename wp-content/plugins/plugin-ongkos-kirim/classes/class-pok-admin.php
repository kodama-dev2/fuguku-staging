<?php

/**
 * POK Admin Class
 */
class POK_Admin {

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

	public $admin_tabs;

	/**
	 * Constructor
	 *
	 * @param Tonjoo_License_Handler_3 $license License handler.
	 */
	public function __construct( Tonjoo_License_Handler_3 $license ) {
		global $pok_helper;

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'parent_file', array( $this, 'highlight_submenu' ) );
		add_action( 'admin_init', array( $this, 'handle_actions' ) );
		add_action( 'admin_menu', array( $this, 'register_tabs' ), 5 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		add_action( 'admin_notices', array( $this, 'api_disabled' ) );
		add_action( 'admin_notices', array( $this, 'show_notices' ) );
		add_action( 'init', array( $this, 'front_debugger' ) );
		add_action( 'pokmv_admin', array( $this, 'unregister_old_supported_hooks' ) );

		$this->helper     = $pok_helper;
		$this->setting    = new POK_Setting();
		$this->core       = new POK_Core( $license );
		$this->license    = $license;
		
	}

	/**
	 * Validate current admin screen
	 *
	 * @return boolean Screen is Plugin Ongkos Kirim or not.
	 */
	private function validate_screen() {
		$screen = get_current_screen();
		if ( is_null( $screen ) ) {
			return false;
		}

		$allowed_screens = array(
			'ongkos-kirim_page_pok_setting',
			'ongkos-kirim_page_pok_about',
			'woocommerce_page_wc-settings',
			'woocommerce_page_wc-reports',
		);
		if ( in_array( $screen->id, $allowed_screens, true ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Enqueue Script Inventory Manager
	 */
	public function enqueue_scripts() {
		if ( $this->validate_screen() ) {
			wp_register_style( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css', array(), '4.0.5' );
			wp_enqueue_style( 'pok-admin', POK_PLUGIN_URL . '/assets/css/admin.css', array( 'select2' ), POK_VERSION );
			wp_enqueue_script( 'pok-admin', POK_PLUGIN_URL . '/assets/js/admin.js', array( 'jquery', 'select2', 'jquery-blockui' ), POK_VERSION, true );
			wp_localize_script( 'pok-admin', 'pok_settings', $this->setting->get_all() );
			wp_localize_script(
				'pok-admin', 'pok_translations', array(
					'confirm_change_base_api'       => __( 'Are you sure? Switching base API will delete all cached data and custom shipping costs. Also, you might need to re-set your store location.', 'pok' ),
					'switch_base_api_rajaongkir'    => __( 'To activate Rajaongkir, provide the API key and set the API type, then click Check Rajaongkir Status.', 'pok' ),
					'switch_base_api_rajaongkir2'   => __( 'To activate Rajaongkir V2, provide the API key then click Check Rajaongkir Status.', 'pok' ),
					'api_key_empty'                 => __( 'API key is empty', 'pok' ),
					'cant_connect_server'           => __( 'Can not connect server', 'pok' ),
					'connecting_server'             => __( 'Connecting server...', 'pok' ),
					'all_province'                  => __( 'All Province', 'pok' ),
					'all_city'                      => __( 'All City', 'pok' ),
					'all_district'                  => __( 'All District', 'pok' ),
					'all_service'                   => __( 'All Service', 'pok' ),
					'select_service'                => __( 'Select Service', 'pok' ),
					'delete'                        => __( 'Delete', 'pok' ),
					'add'                           => __( 'Add', 'pok' ),
					'select_city'                   => __( 'Select city', 'pok' ),
					'select_district'               => __( 'Select district', 'pok' ),
					'confirm_disable_api'           => __( 'Are you sure want to disable the API? This option is intended only for debugging, do not use this on the production site!', 'pok' ),
					'no_service_selected'           => __( 'No service selected, click here to select services', 'pok' ),
				)
			);
			wp_localize_script(
				'pok-admin', 'pok_urls', array(
					'switch_base_api_tonjoo'        => wp_nonce_url( admin_url( 'admin.php?page=pok_setting&base_api=nusantara' ), 'change_base_api', 'pok_action' ),
					'switch_base_api_rajaongkir'    => wp_nonce_url( admin_url( 'admin.php?page=pok_setting&base_api=rajaongkir' ), 'change_base_api', 'pok_action' ),
					'switch_base_api_rajaongkir2'   => wp_nonce_url( admin_url( 'admin.php?page=pok_setting&base_api=rajaongkir2' ), 'change_base_api', 'pok_action' ),
				)
			);
			wp_localize_script(
				'pok-admin', 'pok_nonces', array(
					'set_rajaongkir_api_key'    => wp_create_nonce( 'set_rajaongkir_api_key' ),
					'set_rajaongkir2_api_key'   => wp_create_nonce( 'set_rajaongkir2_api_key' ),
					'search_city'               => wp_create_nonce( 'search_city' ),
					'get_list_city'             => wp_create_nonce( 'get_list_city' ),
					'get_list_district'         => wp_create_nonce( 'get_list_district' ),
					'get_list_service'          => wp_create_nonce( 'get_list_service' ),
					'check_fixer_api'           => wp_create_nonce( 'check_fixer_api' ),
					'check_currencylayer_api'   => wp_create_nonce( 'check_currencylayer_api' ),
					'simulate_disable_api'      => wp_create_nonce( 'simulate_disable_api' ),
				)
			);
			wp_localize_script(
				'pok-admin', 'wc_currency', array(
					'currency'      => get_woocommerce_currency_symbol( get_option( 'woocommerce_currency' ) ),
					'currency_pos'  => get_option( 'woocommerce_currency_pos' ),
					'sep_thousand'  => get_option( 'woocommerce_price_thousand_sep' ),
					'sep_decimal'   => get_option( 'woocommerce_price_decimal_sep' ),
					'num_decimal'   => get_option( 'woocommerce_price_num_decimals' ),
				)
			);
			if ( isset( $_GET['tab'] ) && 'checker' === sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) {
				wp_enqueue_script( 'pok-checker', POK_PLUGIN_URL . '/assets/js/checker.js', array( 'jquery', 'select2' ), POK_VERSION, true );
				$localize = array(
					'base_api'			=> $this->setting->get('base_api'),
					'get_list_city'     => wp_create_nonce( 'get_list_city' ),
					'get_list_district' => wp_create_nonce( 'get_list_district' ),
					'get_checker'		=> wp_create_nonce( 'check_cost' ),
					'select_city'       => __( 'Select city', 'pok' ),
					'select_district'   => __( 'Select district', 'pok' ),
				);
				if ( 'nusantara' === $this->setting->get('base_api') ) {
					ob_start();
					include POK_PLUGIN_PATH . 'data/provinces.json';
					$provinces = ob_get_contents();
					ob_end_clean();
					ob_start();
					include POK_PLUGIN_PATH . 'data/cities.json';
					$cities = ob_get_contents();
					ob_end_clean();
					$localize['provinces'] 	= json_decode( $provinces );
					$localize['cities']		= json_decode( $cities );
				}
				wp_localize_script( 'pok-checker', 'checker', $localize );
			}
		}
	}

	/**
	 * Register admin menu
	 */
	public function admin_menu() {
    	global $submenu;
		add_menu_page( 'Ongkos Kirim', 'Ongkos Kirim', 'manage_woocommerce', 'plugin_ongkos_kirim', null, POK_PLUGIN_URL . '/assets/img/icon.png', 58 );
		foreach ( $this->admin_tabs as $key => $tab ) {
			$sub_url = 'setting' === $key ? 'pok_setting' : 'pok_setting&tab=' . $key;
			add_submenu_page( 'plugin_ongkos_kirim', $tab['label'], $tab['label'], 'manage_woocommerce', $sub_url, array( $this, 'render_admin_page' ) );
		}
		remove_submenu_page( 'plugin_ongkos_kirim', 'plugin_ongkos_kirim' );
	}

	/**
	 * Manually highlight the sub menu hack
	 */
	public function highlight_submenu( $parent_file ) {
		global $submenu_file;
		if ( isset( $_GET['tab'] ) && in_array( sanitize_text_field( wp_unslash( $_GET['tab'] ) ), array_keys( $this->admin_tabs ), true ) ) { // WPCS: Input var okay, CSRF ok.
			$submenu_file = 'pok_setting&tab=' . sanitize_text_field( wp_unslash( $_GET['tab'] ) ); // WPCS: Input var okay, CSRF ok.
		}
		return $parent_file;
	}

	public function register_tabs() {
		$this->admin_tabs = array(
			'setting'   => array(
				'label'     => __( 'Settings', 'pok' ),
				'callback'  => array( $this, 'render_subpage_setting' ),
			),
			'custom'    => array(
				'label'     => __( 'Custom Costs', 'pok' ),
				'callback'  => array( $this, 'render_subpage_custom' ),
			),
			'checker'   => array(
				'label'     => __( 'Cost Checker', 'pok' ),
				'callback'  => array( $this, 'render_subpage_checker' ),
			),
		);
		if ( 'yes' === $this->setting->get( 'debug_mode' ) ) {
			$this->admin_tabs['debugger'] = array(
				'label'     => __( 'Debugger', 'pok' ),
				'callback'  => array( $this, 'render_subpage_debugger' ),
			);
		}
		$this->admin_tabs['help'] = array(
			'label'     => __( 'Help Center', 'pok' ),
			'callback'  => array( $this, 'render_subpage_help' ),
		);
		$this->admin_tabs = apply_filters( 'pok_admin_tabs', $this->admin_tabs );
	}

	/**
	 * Render setting page
	 */
	public function render_admin_page() {
		if ( $this->license->is_license_active() ) {
			$tabs = $this->admin_tabs;
			if ( isset( $_GET['tab'] ) && in_array( sanitize_text_field( wp_unslash( $_GET['tab'] ) ), array_keys( $tabs ), true ) ) { // WPCS: Input var okay, CSRF ok.
				$tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) ); // WPCS: Input var okay, CSRF ok.
			} else {
				$tab = current( array_keys( $tabs ) );
			}
			include_once POK_PLUGIN_PATH . 'views/admin.php';
		} else {
			include_once POK_PLUGIN_PATH . 'views/admin-inactive.php';
		}
	}

	/**
	 * Render setting page, subpage setting
	 */
	public function render_subpage_setting() {
		global $pok_helper;
		$settings = $this->setting->get_all();
		$sections = apply_filters( 'pok_setting_sections', array(
			'basic'	=> array(
				'icon'		=> POK_PLUGIN_URL . '/assets/img/icon-basic.png',
				'title'		=> __( 'Basic', 'pok' ),
				'template'	=> POK_PLUGIN_PATH . 'views/admin-setting-basic.php'
			),
			'courier'	=> array(
				'icon'		=> POK_PLUGIN_URL . '/assets/img/icon-courier.png',
				'title'		=> __( 'Courier', 'pok' ),
				'template'	=> POK_PLUGIN_PATH . 'views/admin-setting-courier.php'
			),
			'weight'	=> array(
				'icon'		=> POK_PLUGIN_URL . '/assets/img/icon-weight.png',
				'title'		=> __( 'Weight', 'pok' ),
				'template'	=> POK_PLUGIN_PATH . 'views/admin-setting-weight.php'
			),
			'cost'	=> array(
				'icon'		=> POK_PLUGIN_URL . '/assets/img/icon-cost.png',
				'title'		=> __( 'Cost', 'pok' ),
				'template'	=> POK_PLUGIN_PATH . 'views/admin-setting-cost.php'
			),
			'checkout'	=> array(
				'icon'		=> POK_PLUGIN_URL . '/assets/img/icon-checkout.png',
				'title'		=> __( 'Checkout', 'pok' ),
				'template'	=> POK_PLUGIN_PATH . 'views/admin-setting-checkout.php'
			),
			'miscellaneous'	=> array(
				'icon'		=> POK_PLUGIN_URL . '/assets/img/icon-miscellaneous.png',
				'title'		=> __( 'Miscellaneous', 'pok' ),
				'template'	=> POK_PLUGIN_PATH . 'views/admin-setting-miscellaneous.php'
			),
			'vendors'	=> array(
				'icon'		=> POK_PLUGIN_URL . '/assets/img/icon-vendors.png',
				'title'		=> __( 'Multi Vendor', 'pok' ),
				'template'	=> POK_PLUGIN_PATH . 'views/admin-setting-vendors.php'
			),
			'advanced'	=> array(
				'icon'		=> POK_PLUGIN_URL . '/assets/img/icon-advanced.png',
				'title'		=> __( 'Advanced', 'pok' ),
				'template'	=> POK_PLUGIN_PATH . 'views/admin-setting-advanced.php'
			)
		) );

		if ( $pok_helper->is_admin_active() ) {
			$all_couriers = $this->core->get_all_couriers();
			$couriers = $this->core->get_courier( $settings['base_api'], $settings['rajaongkir_type'] );
			$services = $this->core->get_courier_service();
			if ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) {
				$cities = $this->core->get_all_city();
			} else {
				$origins = $this->core->get_origins();
			}
		} else {
			$sections['courier']['template'] = POK_PLUGIN_PATH . 'views/admin-setting-inactive.php';
			$sections['cost']['template'] = POK_PLUGIN_PATH . 'views/admin-setting-inactive.php';
		}
		include_once POK_PLUGIN_PATH . 'views/admin-setting.php';
	}

	/**
	 * Render setting page, subpage custom
	 */
	public function render_subpage_custom() {
		$settings   = $this->setting->get_all();
		$costs      = $this->setting->get_custom_costs();
		$provinces  = $this->core->get_province();
		$couriers   = $this->core->get_courier();
		include_once POK_PLUGIN_PATH . 'views/admin-custom.php';
	}

	/**
	 * Render setting page, subpage checker
	 */
	public function render_subpage_checker() {
		$settings = $this->setting->get_all();
		if ( $this->helper->is_admin_active() ) {
			$provinces  	= $this->core->get_province();
			$all_couriers 	= $this->core->get_all_couriers();
			$couriers 		= $this->core->get_courier( $settings['base_api'], $settings['rajaongkir_type'] );
			if ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) {
				$cities = $this->core->get_all_city();
			} else {
				$origins = $this->core->get_origins();
			}
		}
		include_once POK_PLUGIN_PATH . 'views/admin-checker.php';
	}

	/**
	 * Render setting page, subpage debugger
	 */
	public function render_subpage_debugger() {
		// $this->license->check_status();
		$log = new TJ_Logs( POK_LOG_NAME );
		$error = false;
		$is_writable = $log->is_writable();
		if ( $is_writable ) {
			$logs = $log->read( true );
			if ( is_array( $logs ) ) {
				$logs = array_reverse( $logs );
			} else {
				$logs = array();
				$error = true;
			}
		} else {
			$logs = array();
			$error = true;
		}
		include_once POK_PLUGIN_PATH . 'views/admin-debugger.php';
	}

	/**
	 * Render setting page, subpage help
	 */
	public function render_subpage_help() {
		require_once ABSPATH . 'wp-includes/rss.php';
		if ( false === ( $tutorials = get_transient( 'pok_tutorial_rss' ) ) ) {
			$tutorials = [];
			$rss = fetch_rss( 'https://tonjoostudio.com/tag/plugin-ongkos-kirim/feed/' );
			if ( $rss && ! empty( $rss->items ) ) {
				foreach ( $rss->items as $item ) {
					$tutorials[] = [
						'title' => $item['title'],
						'link'  => $item['link']
					];
				}
				set_transient( 'pok_tutorial_rss', $tutorials, WEEK_IN_SECONDS );
			}
		}
		include_once POK_PLUGIN_PATH . 'views/admin-help.php';
	}

	/**
	 * Handle actions
	 */
	public function handle_actions() {
		if ( isset( $_REQUEST['pok_action'] ) ) { // Input var okay.

			// update setting.
			if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pok_action'] ) ), 'update_setting' ) ) { // Input var okay.
				$new_setting = array();
				if ( isset( $_POST['pok_setting'] ) && is_array( $_POST['pok_setting'] ) ) { // Input var okay.
					foreach ( wp_unslash( $_POST['pok_setting'] ) as $key => $value ) { // WPCS: Input var okay, CSRF ok, sanitization ok.
						if ( ! is_array( $value ) ) {
							$new_setting[ $key ] = sanitize_text_field( wp_unslash( $value ) );
						} else {
							if ( 'markup' !== $key && 'custom_service_name' !== $key ) {
								$new_setting[ $key ] = array_map( 'sanitize_text_field', wp_unslash( $value ) );
							} else {
								$new_setting[ $key ] = $value;
							}
						}
					}
				}

				$old_setting = $this->setting->get_all();
				$new_setting['base_api']        = $old_setting['base_api'];
				$new_setting['rajaongkir_type'] = $old_setting['rajaongkir_type'];
				$new_setting['rajaongkir_key']  = $old_setting['rajaongkir_key'];
				if ( ! isset( $new_setting['markup'] ) ) {
					$new_setting['markup'] = array();
				}
				if ( ! isset( $new_setting['custom_service_name'] ) ) {
					$new_setting['custom_service_name'][ $new_setting['base_api'] ] = array();
				} else {
					if ( isset( $new_setting['custom_service_name'][ $new_setting['base_api'] ] ) && is_array( $new_setting['custom_service_name'][ $new_setting['base_api'] ] ) ) {
						$temp = array();
						foreach ( $new_setting['custom_service_name'][ $new_setting['base_api'] ] as $key => $value ) {
							if ( ! empty( $value['courier'] ) && ! empty( $value['service'] ) && ! empty( $value['name'] ) ) {
								$temp[ $key ] = $value;
							}
						}
						$new_setting['custom_service_name'][ $new_setting['base_api'] ] = $temp;
					} else {
						$new_setting['custom_service_name'][ $new_setting['base_api'] ] = array();
					}
				}
				$old_setting['custom_service_name'][ $new_setting['base_api'] ] = $new_setting['custom_service_name'][ $new_setting['base_api'] ];
				$new_setting['custom_service_name'] = $old_setting['custom_service_name'];
				$new_setting = wp_parse_args( $new_setting, $old_setting );
				$this->setting->save( $new_setting );
				$this->core->purge_cache( 'cost' );
				$this->add_notice( __( 'Settings saved', 'pok' ) );
				wp_safe_redirect( admin_url( 'admin.php?page=pok_setting' ) );
				die;

				// update shipping costs.
			} elseif ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pok_action'] ) ), 'update_custom_costs' ) ) { // Input var okay.
				if ( isset( $_POST['custom_cost'] ) && ! empty( $_POST['custom_cost'] ) ) { // Input var okay.
					$custom = wp_unslash( $_POST['custom_cost'] ); // WPCS: Input var okay, CSRF ok, sanitization ok.
				} else {
					$custom = array();
				}
				$courier_name   = isset( $_POST['pok_setting']['custom_cost_courier'] ) ? sanitize_text_field( wp_unslash( $_POST['pok_setting']['custom_cost_courier'] ) ) : 'Custom'; // Input var okay.
				$custom_type    = isset( $_POST['pok_setting']['custom_cost_type'] ) ? sanitize_text_field( wp_unslash( $_POST['pok_setting']['custom_cost_type'] ) ) : 'append'; // Input var okay.
				$this->setting->set( 'custom_cost_courier', $courier_name );
				$this->setting->set( 'custom_cost_type', $custom_type );

				$custom = array_values( $custom );
				$this->setting->save_custom_costs( $custom );

				$this->add_notice( __( 'Custom costs saved', 'pok' ) );
				wp_safe_redirect( admin_url( 'admin.php?page=pok_setting&tab=custom' ) );
				die;

				// change base api.
			} elseif ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pok_action'] ) ), 'change_base_api' ) ) { // Input var okay.
				if ( isset( $_GET['base_api'] ) ) { // Input var okay.
					$old_store_location	= $this->helper->get_store_location();
					if ( 'nusantara' === sanitize_text_field( wp_unslash( $_GET['base_api'] ) ) ) { // Input var okay.
						$this->setting->set( 'base_api', 'nusantara' );
						$this->setting->set( 'couriers', $this->core->get_courier() );
						if ( 'no' === $this->setting->get( 'round_weight' ) ) {
							$this->setting->set( 'round_weight', 'auto' );
						}
						$new_city = $this->helper->get_alt_city_id( $old_store_location, 'nusantara' );
						if ( ! empty( $new_city ) ) {
							$new_store_location = array( $new_city );
						}
					} elseif ( 'rajaongkir' === sanitize_text_field( wp_unslash( $_GET['base_api'] ) ) ) { // Input var okay.
						$this->setting->set( 'base_api', 'rajaongkir' );
						$this->setting->set( 'couriers', $this->core->get_courier() );
						$this->setting->set( 'round_weight', 'no' );
						if ( $this->helper->is_rajaongkir_active() ) {
							$new_city = $this->helper->get_alt_city_id( $old_store_location, 'rajaongkir' );
							if ( ! empty( $new_city ) ) {
								$new_store_location = array( $new_city );
							}
						}
					} elseif ( 'rajaongkir2' === sanitize_text_field( wp_unslash( $_GET['base_api'] ) ) ) { // Input var okay.
						$this->setting->set( 'base_api', 'rajaongkir2' );
						$this->setting->set( 'couriers', $this->core->get_courier() );
						$this->setting->set( 'round_weight', 'no' );
						// if ( $this->helper->is_rajaongkir2_active() ) {
						// 	$new_city = $this->helper->get_alt_city_id( $old_store_location, 'rajaongkir' );
						// 	if ( ! empty( $new_city ) ) {
						// 		$new_store_location = array( $new_city );
						// 	}
						// }
					}
					$this->setting->set( 'store_location', $new_store_location );
					// delete custom costs.
					$this->setting->reset_custom_costs();
					// delete customer saved address data.
					global $wpdb;
					$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key IN ('billing_state','billing_city','billing_district','shipping_state','shipping_city','shipping_district','billing_state_id','billing_city_id','billing_district_id','shipping_state_id','shipping_city_id','shipping_district_id')" ); // WPCS: db call ok.
					// purge cache.
					$this->core->purge_cache();

					if ( empty( $new_store_location ) ) {
						$this->add_notice( __( 'Base API are switched. Please re-set your store location.', 'pok' ) );
					} else {
						$this->add_notice( __( 'Base API are switched.', 'pok' ) );
					}
				}
				wp_safe_redirect( admin_url( 'admin.php?page=pok_setting' ) );
				die;

				// flush cache.
			} elseif ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pok_action'] ) ), 'flush_cache' ) ) { // Input var okay.
				$this->core->purge_cache();
				$this->add_notice( __( 'All caches has been purged.', 'pok' ) );
				wp_safe_redirect( admin_url( 'admin.php?page=pok_setting' ) );
				die;

				// reset.
			} elseif ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pok_action'] ) ), 'reset' ) ) { // Input var okay.
				$this->setting->reset();
				$this->core->purge_cache();
				$this->add_notice( __( 'All settings has been reset.', 'pok' ) );
				wp_safe_redirect( admin_url( 'admin.php?page=pok_setting' ) );
				die;

				// switch to classic checkout
			} elseif ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pok_action'] ) ), 'switch_classic_checkout' ) ) { // Input var okay.
				$checkout_page_id = get_option( 'woocommerce_checkout_page_id' );
				wp_update_post( array(
					'ID' => $checkout_page_id,
					'post_content' => '<!-- wp:woocommerce/classic-shortcode {"shortcode":"checkout"} /-->'
				) );
				if ( isset( $_REQUEST['context'] ) && 'wizard' === sanitize_text_field( wp_unslash( $_REQUEST['context'] ) ) ) {
					wp_safe_redirect( admin_url( 'admin.php?page=pok_setting&wizard=next_steps' ) );
					die;
				}
				$this->add_notice( __( "Your site's checkout page was switched to classic successfully.", 'pok' ) );
				wp_safe_redirect( admin_url( 'admin.php?page=pok_setting' ) );
				die;
			}

			do_action( 'pok_admin_handle_action', sanitize_text_field( wp_unslash( $_REQUEST['pok_action'] ) ), $this ); // Input var okay.
		}
	}

	/**
	 * Add notice
	 *
	 * @param string  $message Message.
	 * @param string  $type    Type.
	 * @param boolean $p       Using paragraph?.
	 */
	public function add_notice( $message = '', $type = 'success', $p = true ) {
		$old_notice = get_option( 'pok_notices', array() );
		$old_notice[] = array(
			'type'      => $type,
			'message'   => $p ? '<p>' . $message . '</p>' : $message,
		);
		update_option( 'pok_notices', $old_notice, false );
	}

	/**
	 * Show all notices
	 */
	public function show_notices() {
		$notices = get_option( 'pok_notices', array() );
		foreach ( $notices as $notice ) {
			echo '
				<div class="notice is-dismissible notice-' . esc_attr( $notice['type'] ) . '">
					' . wp_kses_post( $notice['message'] ) . '
				</div>';
		}
		update_option( 'pok_notices', array() );
	}

	/**
	 * Admin notice
	 */
	public function admin_notice() {
		$errors = array();

		if ( ! $this->helper->is_woocommerce_active() ) {
			$errors[] = __( 'Woocommerce not active', 'pok' );
		}

		if ( ! function_exists( 'curl_version' ) ) {
			$errors[] = __( 'Plugin Ongkos Kirim needs active CURL', 'pok' );
		}

		if ( 'yes' === $this->setting->get( 'enable' ) && $this->helper->is_license_active() ) {
			if ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) {
				$rajaongkir_status = $this->setting->get( 'rajaongkir_status' );
				if ( ! $rajaongkir_status[0] ) {
					$errors[] = __( 'RajaOngkir API Key is not active.', 'pok' );
				}
			}
			if ( $this->helper->is_admin_active() ) {
				$store_location = $this->setting->get( 'store_location' );
				if ( empty( $store_location ) || empty( $store_location[0] ) ) {
					$errors[] = __( 'Store Location is empty.', 'pok' );
				}
				$courier = $this->setting->get( 'couriers' );
				if ( empty( $courier ) ) {
					$errors[] = __( 'Selected Couriers is empty.', 'pok' );
				}
			}
		}

		$errors = apply_filters( 'pok_admin_errors', $errors );

		if ( ! empty( $errors ) ) {
			?>
			<div class="notice notice-error">
				<p><?php echo wp_kses_post( __( '<strong>Plugin Ongkos Kirim</strong> is disabled due to the following errors:', 'pok' ) ); ?></p>
				<?php foreach ( $errors as $e ) : ?>
					<p style="margin:0;">- <?php echo esc_html( $e ); ?></p>
				<?php endforeach; ?>
				<p style="margin-top: 10px;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=pok_setting' ) ); ?>" class="button"><?php esc_html_e( 'Go to Settings', 'pok' ); ?></a></p>
			</div>
			<?php
			return;
		}

		if ( $this->helper->is_using_checkout_block() ) {
			?>
			<div class="notice notice-error">
				<p><?php echo wp_kses_post( __( '<strong>Plugin Ongkos Kirim</strong> is not working properly because your site uses block-based checkout page. Please consider using classic checkout to get our plugin work on your checkout page.', 'pok' ) ); ?></p>
				<p style="margin-top: 10px;"><a href="<?php echo esc_url( add_query_arg( [ 'page' => 'pok_setting', 'pok_action' => wp_create_nonce( 'switch_classic_checkout' ) ], admin_url( 'admin.php' ) ) ); ?>" class="button"><?php esc_html_e( 'Switch to classic checkout', 'pok' ); ?></a></p>
			</div>
			<?php
		}
	}

	/**
	 * Notice when API disabled
	 */
	public function api_disabled() {
		if ( ( 'nusantara' === $this->setting->get( 'base_api' ) && 'yes' === $this->setting->get( 'temp_disable_api_tonjoo' ) ) || ( 'rajaongkir' === $this->setting->get( 'base_api' ) && 'yes' === $this->setting->get( 'temp_disable_api_rajaongkir' ) ) ) {
			?>
			<div class="notice notice-error">
				<p><?php printf( __( '<strong>Plugin Ongkos Kirim</strong> API currently disabled by debugger, you can re-enable it on <a href="%s">this page</a>', 'pok' ), esc_url( admin_url( 'admin.php?page=pok_setting&tab=debugger' ) ) ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Display debug status on the front side
	 */
	public function front_debugger() {
		if ( isset( $_GET['ongkir_status'] ) && 'yes' === $this->setting->get( 'debug_mode' ) && 'yes' === $this->setting->get( 'front_debug_mode' ) && sanitize_text_field( wp_unslash( $_GET['ongkir_status'] ) ) === $this->setting->get( 'front_debug_mode_passphrase' ) ) { // WPCS: CSRF ok, Input var okay.
			// $this->license->check_status();
			if ( $this->helper->is_license_active() ) {
				echo 'LICENSE OK';
			} else {
				echo 'LICENSE NOT OK';
			}
			echo ' ';
			if ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) {
				if ( true === $this->helper->get_api_status() ) {
					echo 'API RAJAONGKIR OK';
				} else {
					echo 'API RAJAONGKIR NOT OK';
				}
			} else {
				if ( true === $this->helper->get_api_status() ) {
					echo 'API TONJOO OK';
				} else {
					echo 'API TONJOO NOT OK';
				}
			}
			die;
		}
	}

	/**
	 * Unregister old supported hooks from multi vendor
	 * 
	 * @param  POKMV_Admin $pokmv_admin Multi vendor admin class.
	 */
	public function unregister_old_supported_hooks( $pokmv_admin ) {
		remove_action( 'pok_setting_miscellaneous', array( $pokmv_admin, 'setting_html' ) );
	}

}
