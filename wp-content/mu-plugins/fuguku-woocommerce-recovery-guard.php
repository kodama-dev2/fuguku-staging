<?php
/**
 * Plugin Name: Fuguku WooCommerce Recovery Guard
 * Description: Prevents fatal errors when WooCommerce core files are incomplete/corrupted by auto-disabling WooCommerce loading.
 */

defined('ABSPATH') || exit;

/**
 * Validate required WooCommerce files before plugin bootstrap.
 *
 * @return array<int,string>
 */
function fuguku_wc_required_files() {
	$base = WP_CONTENT_DIR . '/plugins/woocommerce';

	return array(
		$base . '/woocommerce.php',
		$base . '/src/Container.php',
	);
}

/**
 * Determine whether WooCommerce installation has required bootstrap files.
 * Woo 10 uses ExtendedContainer; Woo 11+ uses RuntimeContainer.
 */
function fuguku_wc_is_installation_valid() {
	foreach (fuguku_wc_required_files() as $file) {
		if (!file_exists($file)) {
			return false;
		}
	}

	$legacy = WP_CONTENT_DIR . '/plugins/woocommerce/src/Internal/DependencyManagement/ExtendedContainer.php';
	$current = WP_CONTENT_DIR . '/plugins/woocommerce/src/Internal/DependencyManagement/RuntimeContainer.php';

	return file_exists($legacy) || file_exists($current);
}

/**
 * Remove WooCommerce from active plugins list when installation is broken.
 *
 * @param mixed $plugins Active plugins option value.
 * @return mixed
 */
function fuguku_wc_guard_active_plugins($plugins) {
	if (!is_array($plugins)) {
		return $plugins;
	}

	$wc_plugin = 'woocommerce/woocommerce.php';
	if (!in_array($wc_plugin, $plugins, true)) {
		return $plugins;
	}

	if (fuguku_wc_is_installation_valid()) {
		return $plugins;
	}

	$filtered = array_values(array_diff($plugins, array($wc_plugin)));

	if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
		error_log('[FUGUKU WC Recovery] WooCommerce auto-disabled: required core file missing. Reinstall WooCommerce plugin files.');
	}

	return $filtered;
}

/**
 * Multisite variant: active sitewide plugins are stored as associative array.
 *
 * @param mixed $plugins Sitewide active plugins option value.
 * @return mixed
 */
function fuguku_wc_guard_sitewide_plugins($plugins) {
	if (!is_array($plugins)) {
		return $plugins;
	}

	$wc_plugin = 'woocommerce/woocommerce.php';
	if (!isset($plugins[$wc_plugin])) {
		return $plugins;
	}

	if (fuguku_wc_is_installation_valid()) {
		return $plugins;
	}

	unset($plugins[$wc_plugin]);

	if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
		error_log('[FUGUKU WC Recovery] WooCommerce network auto-disabled: required core file missing. Reinstall WooCommerce plugin files.');
	}

	return $plugins;
}

add_filter('option_active_plugins', 'fuguku_wc_guard_active_plugins', 1);
add_filter('site_option_active_sitewide_plugins', 'fuguku_wc_guard_sitewide_plugins', 1);
