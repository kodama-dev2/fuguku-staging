<?php
/*
Plugin Name: WooCommerce Variation Swatch
Plugin URI: http://wpaddon.net/wc-variation-swatch
Description: This plugin will help you to replace dropdown fields on your variable products with Color and Image Swatches.
Author: WPAddon
Author URI: http://wpaddon.net
Version: 1.1.7
Text Domain: wcvs
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WPA_WCVS' ) ) :
	/**
	 * Main WCVS Class.
	 *
	 * @version 1.1.2
	 */
	final class WPA_WCVS {
		/**
		 * Define WCVS version.
		 *
		 * @var string
		 */
		public $version = '1.1.7';

		/**
		 * The single instance of the class.
		 *
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		/**
		 * WCVS instance.
		 *
		 * @since 1.0.0
		 */
		public static function instance() {
			self::$_instance = new self();
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
            		<p>' . esc_html__( 'WooCommerce Product Bundle is enabled but not effective. It requires WooCommerce in order to work.', 'wcvs' ) . '</p>
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
		 * WCVS constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->constants();
			$this->includes();
			$this->hooks();

			do_action( 'wcvs_loaded' );
		}

		/**
		 * Define WCVS constants.
		 *
		 * @since 1.0.0
		 */
		private function constants() {
			$this->define( 'WCVS_VERSION', $this->version );
			$this->define( 'WCVS_BASENAME', plugin_basename( __FILE__ ) );
		}

		/**
		 * Include WCVS core file.
		 *
		 * @since 1.0.0
		 */
		public function includes() {
			require_once( 'includes/admin/class-wcvs-settings.php' );
			require_once( 'includes/admin/class-wcvs-admin.php' );
			require_once( 'includes/admin/class-wcvs-metabox.php' );
			require_once( 'includes/class-wcvs-frontend.php' );

			do_action( 'wcvs_core_includes' );
		}

		/**
		 * Include WCVS core file.
		 *
		 * @since 1.0.0
		 */
		public function hooks() {
			load_plugin_textdomain( 'wcvs', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
			return apply_filters( 'wcvs_template_path', 'wpa-woocommerce-variation-swatch/' );
		}
	}
endif;

/**
 * Main instance of WCVS
 *
 * @since  1.0.0
 * @return WPA_WCVS
 */
function WPA_WCVS() {
	return WPA_WCVS::instance();
}

// Global for backwards compatibility.
$GLOBALS['wpa_wcvs'] = WPA_WCVS();