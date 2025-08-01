<?php

/**

 * Plugin Name:             WooCommerce Direct Checkout PRO
 * Description:             Simplifies the checkout process to improve your sales rate.
 * Version:                 3.1.1
 * Text Domain:             woocommerce-direct-checkout-pro
 * Author:                  QuadLayers
 * Author URI:              https://quadlayers.com
 * License:                 Copyright
 * Domain Path:             /languages
 * Request at least:        4.7.0
 * Tested up to:            6.3
 * Requires PHP:            5.6
 * WC requires at least:    4.0
 * WC tested up to:         8.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

define( 'QLWCDC_PRO_PLUGIN_NAME', 'WooCommerce Direct Checkout PRO' );
define( 'QLWCDC_PRO_PLUGIN_VERSION', '3.1.1' );
define( 'QLWCDC_PRO_PLUGIN_FILE', __FILE__ );
define( 'QLWCDC_PRO_PLUGIN_DIR', __DIR__ . DIRECTORY_SEPARATOR );
define( 'QLWCDC_PRO_DOMAIN', 'qlwcdc' );
define( 'QLWCDC_PRO_SUPPORT_URL', 'https://quadlayers.com/account/support/?utm_source=qlwcdc_admin' );
define( 'QLWCDC_PRO_LICENSES_URL', 'https://quadlayers.com/account/licenses/?utm_source=qlwcdc_admin' );

/**
 * Load composer autoload
 */
require_once __DIR__ . '/vendor/autoload.php';
/**
 * Load vendor_packages packages
 */
require_once __DIR__ . '/vendor_packages/wp-i18n-map.php';
require_once __DIR__ . '/vendor_packages/wp-dashboard-widget-news.php';
require_once __DIR__ . '/vendor_packages/wp-notice-plugin-required.php';
require_once __DIR__ . '/vendor_packages/wp-plugin-table-links.php';
require_once __DIR__ . '/vendor_packages/wp-license-client.php';
/**
 * Load plugin classes
 */
require_once __DIR__ . '/lib/class-plugin.php';

/**
 * Plugin activation hook
 */
register_activation_hook(
	__FILE__,
	function() {
		do_action( 'wcdc_pro_activation' );
	}
);

/**
 * Plugin activation hook
 */
register_deactivation_hook(
	__FILE__,
	function() {
		do_action( 'wcdc_pro_deactivation' );
	}
);

/**
 * Declare compatibility with WooCommerce Custom Order Tables.
 */
add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);
