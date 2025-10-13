<?php
/**
 * Plugin Name: Claue Addons
 * Plugin URI:  http://www.janstudio.net
 * Description: Extra addons for Claue theme.
 * Version:     1.2.7
 * Author:      JanStudio
 * Author URI:  http://www.janstudio.net
 * License:     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: claue-addons
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

// Define url to this plugin file.
if ( ! defined( 'CLAUE_ADDONS_URL' ) )
	define( 'CLAUE_ADDONS_URL', plugin_dir_url( __FILE__ ) );

// Define path to this plugin file.
if ( ! defined( 'CLAUE_ADDONS_PATH' ) )
	define( 'CLAUE_ADDONS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Class ClaueAddons
 *
 * @package  ClaueAddons
 * @since    1.2.7
 */
class Claue_Addons {
	/**
	 * Construct function.
	 *
	 * @return  void
	 */
	function __construct() {
		add_action( 'init',               array( $this, 'init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ), 100 );

		// Load plugin text domain
		load_plugin_textdomain( 'claue-addons', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Indicates if a multi-currency plugin is active. In such case, the currency
	 * addon should not be loaded.
	 *
	 * @return bool
	 * @author Aelia
	 */
	protected static function is_multi_currency_plugin_active() {
		return
		
		// WPML
		class_exists( 'woocommerce_wpml' ) ||
		// WooCommerce Prices Based on Country
		class_exists( 'WC_Product_Price_Based_Country' ) ||
		// Aelia Currency Switcher
		isset( $GLOBALS['woocommerce-aelia-currencyswitcher'] ) ||
		// WooComerce Wallet
		isset( $GLOBALS['woo_wallet'] ) ||
		// WooComerce Multi Currency
		defined( 'WOOCOMMERCE_MULTICURRENCY_VERSION' ) ||
		// WooComerce Currency Converter
		defined( 'WC_CURRENCY_CONVERTER_VERSION' ) ||
		// WooComerce Multi Currency Villa Theme
		defined( 'WOOMULTI_CURRENCY_F_VERSION' ) ||
		// WooComerce Currency Switcher
		isset( $GLOBALS['WOOCS'] ) ||
		// WooCommerce Payments
		isset( $GLOBALS['wcpay_activated'] );
	}

	/**
	 * Get list addon name.
	 *
	 * @return  array
	 */
	public static function addons( $addons = NULL ) {
		if ( ! self::is_multi_currency_plugin_active() ) {
			$addons = 'currency';
		} else {
			$addons = '';
		}
		return apply_filters( 'jas_addons_filter', $addons );
	}

	/**
	 * Get list shortcode name.
	 *
	 * @return  array
	 */
	public static function shortcodes( $shortcodes = NULL ) {
		$shortcodes = 'service, portfolio, member, blog, products, product, google_maps, wc_categories, banner, promotion, heading';
		return $shortcodes;
	}

	/**
	 * Include addon file.
	 *
	 * @since 1.0
	 */
	public static function init() {
		$addons     = array_map( 'trim', explode( ",", self::addons() ) );
		$shortcodes = array_map( 'trim', explode( ",", self::shortcodes() ) );

		// Include addon
		if ( ! self::is_multi_currency_plugin_active() ) {
			foreach ( $addons as $addon ) {
				include_once CLAUE_ADDONS_PATH . 'includes/' . $addon . '/init.php';
			}
		}

		// Include shortcode
		foreach ( $shortcodes as $shortcode ) {
			include_once CLAUE_ADDONS_PATH . 'includes/shortcodes/' . $shortcode . '.php';
			add_shortcode( 'claue_addons_' . $shortcode, 'claue_addons_shortcode_' . $shortcode );
		}
	}

	/**
	 * Enqueue stylesheet and scripts to frontend.
	 */
	public static function frontend_scripts() {
		if ( class_exists( 'Claue_Addons_Currency' ) ) {
			wp_enqueue_script( 'jas-vendor-jquery-cookies', CLAUE_ADDONS_URL . 'assets/js/3rd.js', array(), false, true );
		}

		if ( is_singular() ) {
			global $post;

			if ( has_shortcode( $post->post_content, 'claue_addons_google_maps' ) ) {
				wp_enqueue_script( 'google-map-api', 'https://maps.google.com/maps/api/js?key=AIzaSyBiyBHqKfGcCN1Pw0rzysj-vMSnJ0_GNgU' );
			}

			if ( has_shortcode( $post->post_content, 'jas_vertical_slide' ) ) {
				wp_enqueue_style( 'multiscroll', CLAUE_ADDONS_URL . '/assets/vendors/multiscroll/jquery.multiscroll.css' );
				wp_enqueue_script( 'easings', CLAUE_ADDONS_URL . 'assets/vendors/multiscroll/jquery.easings.min.js', array(), false, true );
				wp_enqueue_script( 'multiscroll', CLAUE_ADDONS_URL . 'assets/vendors/multiscroll/jquery.multiscroll.min.js', array(), false, true );
			}
		}
	}
}

$claueaddons = new Claue_Addons;

// Include custom post type
include CLAUE_ADDONS_PATH . 'includes/portfolio/init.php';


///////////////////////////////////////////////////////////////////////////////
/* backward compatibility for shortcodes created with WP Bakery PageBuilder, to run them without PageBuilder  */

/**
 * Parse string like "title:Hello world|weekday:Monday" to array('title' => 'Hello World', 'weekday' => 'Monday')
 *
 * @param $value
 * @param array $default
 *
 * @return array
 * @since 4.2
 */
function claue_vc_parse_multi_attribute( $value, $default = array() ) {
    $result = $default;
    $params_pairs = explode( '|', $value );
    if ( ! empty( $params_pairs ) ) {
        foreach ( $params_pairs as $pair ) {
            $param = preg_split( '/\:/', $pair );
            if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
                $result[ $param[0] ] = rawurldecode( $param[1] );
            }
        }
    }

    return $result;
}

/**
 * @param $value
 *
 * @return array
 */
function claue_vc_build_link( $value ) {

    $result = array(
        'url' => '',
        'title' => '',
        'target' => '',
        'rel' => '',
    );
    $params_pairs = explode( '|', $value );
    if ( ! empty( $params_pairs ) ) {
        foreach ( $params_pairs as $pair ) {
            $param = preg_split( '/\:/', $pair );
            if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
                $result[ $param[0] ] = rawurldecode( $param[1] );
            }
        }
    }

    return $result;
}
