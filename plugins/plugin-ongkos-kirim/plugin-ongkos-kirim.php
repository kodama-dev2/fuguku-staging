<?php
/**
 * Plugin Name:     Plugin Ongkos Kirim
 * Plugin URI:      https://tonjoostudio.com/addons/woo-ongkir/
 * Description:     Hitung ongkos kirim seluruh Indonesia (JNE, POS, Tiki, JNT, Wahana, Lion Parcel, Sicepat, dll)
 * Version:         3.11.1
 * Author:          Tonjoo Studio
 * Author URI:      https://tonjoostudio.com
 * License:         GPL
 * Text Domain:     pok
 * Domain Path:     /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 9.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// constants.
define( 'POK_VERSION', '3.11.1' );
define( 'POK_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'POK_PLUGIN_URL', plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) );
define( 'POK_DEBUG', false );
define( 'POK_LOG_NAME', 'pok-error-logs' );

// load files.
require_once POK_PLUGIN_PATH . 'classes/class-pok-setting.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-helper.php';
require_once POK_PLUGIN_PATH . 'libs/tonjoo-license/tonjoo-license-manager.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-wizard.php';
require_once POK_PLUGIN_PATH . 'classes/api/class-pok-api.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-core.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-hooks-product.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-admin.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-ajax.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-report-table.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-report-table-hpos.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-hooks-admin.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-hooks-addresses.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-cpr.php';

if ( ! class_exists( 'Plugin_Ongkos_Kirim' ) ) {

	/**
	 * POK Main Class
	 */
	class Plugin_Ongkos_Kirim {

		/**
		 * POK Helper
		 * 
		 * @var POK_Helper
		 */
		protected $helper;

		/**
		 * POK License
		 * 
		 * @var POK_License
		 */
		protected $license;

		/**
		 * Constructor
		 */
		public function __construct() {
			global $pok_helper;
			global $pok_core;
			global $tonjoo_license_manager;
			$this->license 	= $tonjoo_license_manager->register_plugin( 'wooongkir-premium', __FILE__ );
			$pok_helper 	= new POK_Helper( $this->license );
			$pok_core   	= new POK_Core( $this->license );
			$this->helper 	= $pok_helper;

			// delete old crons.
			if ( $time = wp_next_scheduled( 'nusantara_plugin_license_check_hook' ) ) {
				wp_unschedule_event( $time, 'nusantara_plugin_license_check_hook' );
			}
			if ( $time = wp_next_scheduled( 'pok_scheduled_license_check' ) ) {
				wp_unschedule_event( $time, 'pok_scheduled_license_check' );
			}

			register_activation_hook( __FILE__, array( $this, 'on_plugin_activation' ) );
			add_action( 'admin_init', array( $this, 'on_admin_init' ) );
			add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
			add_action( 'woocommerce_shipping_init', array( $this, 'load_shipping_method' ) );
			add_filter( 'woocommerce_shipping_methods', array( $this, 'register_shipping_method' ) );
			add_action( 'before_woocommerce_init', array( $this, 'define_compatibility' ) );
		}

		/**
		 * Actions when plugin activated
		 */
		public function on_plugin_activation() {
			if ( ! $this->license->is_license_active() ) {
				set_transient( 'pok_wizard', 'active', HOUR_IN_SECONDS );
			}
		}

		/**
		 * Actions when admin initialized
		 */
		public function on_admin_init() {
			if ( false !== get_transient( 'pok_wizard' ) ) {
				delete_transient( 'pok_wizard' );
				wp_safe_redirect( admin_url( 'admin.php?page=pok_setting&wizard=introduction' ) );
				exit;
			}
			new POK_Hooks_Admin();
			new POK_Wizard( $this->license );
		}

		/**
		 * Actions when all plugins loaded
		 */
		public function on_plugins_loaded() {
			load_plugin_textdomain( 'pok', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
			new POK_Admin( $this->license );
			new POK_Ajax( $this->license );
			new POK_Hooks_Product();
			new POK_Hooks_Addresses();

			$installed_version = get_option( 'pok_version', '2.1.3' );
			if ( version_compare( $installed_version, POK_VERSION, '<' ) ) {
				$setting = new POK_Setting();
				if ( version_compare( $installed_version, '2.1.3', '<=' ) ) {
					global $pok_core;
					$pok_core->purge_cache();
					$setting->setting_migration();
					$status = get_option( 'nusantara_ongkir_license_status', array( false, '' ) );
					$this->license->set_status(
						array(
							'active'    => $status[0],
							'key'       => get_option( 'nusantara_ongkir_lisensi', '' ),
						)
					);
					$this->license->check_status( true );
				}
				if ( version_compare( $installed_version, '3.0.0', '<=' ) ) {
					global $pok_core;
					$pok_core->purge_cache();
				}
				if ( version_compare( $installed_version, '3.2.3', '<=' ) ) {
					if ( 'yes' === $setting->get('markup_fee') ) {
						$setting->set( 'markup', array(
							'from_old_setting'	=> array(
								'courier'	=> '',
								'service'	=> '',
								'amount'	=> $setting->get('markup_fee_amount')
							)
						) );
					}
				}
				update_option( 'pok_version', POK_VERSION, true );
			}
			if ( in_array( 'custom-price-rules/custom-price-rules.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
				new POK_CPR();
			}
		}

		/**
		 * Load POK Shipping method
		 */
		public function load_shipping_method() {
			require_once POK_PLUGIN_PATH . '/classes/class-pok-shipping-method.php';
		}

		/**
		 * Register POK Shipping Method
		 *
		 * @param  array $methods Currently registered methods.
		 * @return array          Registered methods.
		 */
		public function register_shipping_method( $methods ) {
			$methods['plugin_ongkos_kirim'] = 'POK_Shipping_Method';
			return $methods;
		}

		/**
		 * Define compatibility/incompatibility with WooCommerce features.
		 */
		public function define_compatibility() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, false );
		        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		    }
		}

	}

	// Initiate!.
	new Plugin_Ongkos_Kirim();

}
