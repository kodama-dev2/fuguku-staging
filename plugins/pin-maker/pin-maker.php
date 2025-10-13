<?php
/*
Plugin Name: Pin Maker
Plugin URI: http://wpaddon.net/pin-maker
Description: A simple and easy way to make a pin for images or woocommerce product.
Author: WPAddon
Author URI: http://wpaddon.net
Version: 1.0.9
Text Domain: pin-maker
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WPA_PinMaker' ) ) :
	/**
	 * Main Pin Maker Class.
	 *
	 * @version 1.0.9
	 */
	final class WPA_PinMaker {
		/**
		 * Define PinMaker version.
		 *
		 * @var string
		 */
		public $version = '1.0.9';

		/**
		 * The single instance of the class.
		 *
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		/**
		 * PinMaker instance.
		 *
		 * @since 1.0.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
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
		 * PinMaker constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->constants();
			$this->includes();
			$this->hooks();

			do_action( 'wpa_pm_loaded' );
		}

		/**
		 * Define PinMaker constants.
		 *
		 * @since 1.0.0
		 */
		private function constants() {
			$this->define( 'WPA_PM_VERSION', $this->version );
		}

		/**
		 * Include PinMaker core file.
		 *
		 * @since 1.0.0
		 */
		public function includes() {
			include_once( 'includes/pm-core-functions.php' );
			include_once( 'includes/class-pm-post-types.php' );
			include_once( 'includes/class-pm-frontend-scripts.php' );

			if ( is_admin() ) {
				include_once( 'includes/admin/class-pm-admin.php' );
			}

			if ( ! is_admin() ) {
				include_once( 'includes/pm-template-hooks.php' );
				include_once( 'includes/pm-template-functions.php' );
				include_once( 'includes/class-pm-shortcodes.php' );
			}

			do_action( 'wpa_pm_core_includes' );
		}

		/**
		 * Include PinMaker core file.
		 *
		 * @since 1.0.0
		 */
		public function hooks() {
			load_plugin_textdomain( 'pin-maker', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
			return apply_filters( 'wpa_pm_template_path', 'pin-maker/' );
		}
	}
endif;

/**
 * Main instance of Pin Maker.
 *
 * @since  1.0.0
 * @return WPA_PinMaker
 */
function WPA_PM() {
	return WPA_PinMaker::instance();
}

// Global for backwards compatibility.
$GLOBALS['wpa_pm'] = WPA_PM();