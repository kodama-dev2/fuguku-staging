<?php
/*
Plugin Name: WPA WooCommerce Product Bundle
Plugin URI: http://wpaddon.net/wc-product-bundle
Description: Boost your sale with bundle product, frequently bought together.
Author: WPAddon
Author URI: http://wpaddon.net
Version: 1.2.6
Text Domain: wcpb
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WPA_WCPB' ) ) :
	/**
	 * Main WCPB Class.
	 *
	 * @version 1.2.2
	 */
	final class WPA_WCPB {
		/**
		 * Define WCPB version.
		 *
		 * @var string
		 */
		public $version = '1.2.6';

		/**
		 * The single instance of the class.
		 *
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		/**
		 * WCPB instance.
		 *
		 * @since 1.0.0
		 */
		public static function instance() {
			if ( ! function_exists( 'WC' ) ) {

				add_action( 'admin_notices', array( __CLASS__, 'required_wc' ) );

			} elseif ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
	     * Print an admin notice if woocommerce is deactivated
	     *
	     * @return void
	     * @use admin_notices hooks
	     */
		public static function required_wc() {
			echo '
				<div class="error">
            		<p>' . esc_html__( 'WooCommerce Product Bundle is enabled but not effective. It requires WooCommerce in order to work.', 'wcpb' ) . '</p>
        		</div>
        	';
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param  string $name
		 * @param  string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * WCPB constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->constants();
			$this->includes();
			$this->hooks();

			do_action( 'wcpb_loaded' );
		}

		/**
		 * Define WCPB constants.
		 *
		 * @since 1.0.0
		 */
		private function constants() {
			$this->define( 'WCPB_VERSION', $this->version );
		}

		/**
		 * Include WCPB core file.
		 *
		 * @since 1.0.0
		 */
		public function includes() {
			include_once( 'includes/admin/class-wcpb-settings.php' );
			include_once( 'includes/admin/class-wcpb-admin.php' );
			include_once( 'includes/class-wcpb-frontend.php' );

			do_action( 'wcpb_core_includes' );
		}

		/**
		 * Include WCPB core file.
		 *
		 * @since 1.0.0
		 */
		public function hooks() {
			load_plugin_textdomain( 'wcpb', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Get the plugin url.
		 *
		 * @since 1.0.0
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @since 1.0.0
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get the template path.
		 *
		 * @since 1.0.0
		 */
		public function template_path() {
			return apply_filters( 'wcpb_template_path', 'wpa-woocommerce-product-bundle/' );
		}
	}
endif;

// Define path to plugin directory.
define( 'WPA_WCPB_PATH', plugin_dir_path( __FILE__ ) );

// Define URL to plugin directory.
define( 'WPA_WCPB_URL', plugin_dir_url( __FILE__ ) );

// Define plugin base file.
define( 'WPA_WCPB_BASENAME', plugin_basename( __FILE__ ) );

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

$wpa_wcpb_data = get_plugin_data( __FILE__ );

define( 'WPA_WCPB_VERSION', $wpa_wcpb_data['Version'] );

/**
 * Main instance of WCPB
 *
 * @since  1.0.0
 * @return WPA_WCPB
 */
function WPA_WCPB() {
	return WPA_WCPB::instance();
}

// Global for backwards compatibility.
$GLOBALS['wpa_wcpb'] = WPA_WCPB();