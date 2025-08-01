<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/komerce
 * @since             1.0.0
 * @package           cekongkir
 *
 * @wordpress-plugin
 * Plugin Name:       Rajaongkir Official
 * Plugin URI:        https://docs.komship.id/
 * Description:       Rajaongkir Official is a plugin that integrates directly with Woocommerce for checking shipping costs both from within Indonesia, or from Indonesia to abroad. We provide up-to-date information regarding every shipping cost from every courier.
 * Version:           1.0.0
 * Author:            PT. Kampung Marketerindo Berdaya (Komerce)
 * Author URI:        https://docs.komship.id/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       Rajaongkir Official
 * Domain Path:       /languages
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 8.6.0
 */


// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'CEKONGKIR_METHOD_ID', 'cekongkir' );
define( 'CEKONGKIR_FILE', __FILE__ );
define( 'CEKONGKIR_PATH', plugin_dir_path( CEKONGKIR_FILE ) );
define( 'CEKONGKIR_URL', plugin_dir_url( CEKONGKIR_FILE ) );

// Load the helpers.
require_once CEKONGKIR_PATH . 'includes/helpers.php';

// Register the class auto loader.
if ( function_exists( 'cekongkir_autoload' ) ) {
	spl_autoload_register( 'cekongkir_autoload' );
}

/**
 * Boot the plugin
 */
if ( cekongkir_is_plugin_active( 'woocommerce/woocommerce.php' ) && class_exists( 'Cekongkir' ) ) {
	// Initialize the woongkir class.
	Cekongkir::get_instance();
}