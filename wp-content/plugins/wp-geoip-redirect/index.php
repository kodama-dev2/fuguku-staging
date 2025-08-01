<?php
/*
Plugin Name: WP GeoIP Country Redirect
Description: WP GeoIP Country Redirect let's you drive away unwanted traffic.
Version: 4.0
Author: Crivion
Author URI: https://crivion.com
*/

update_option( 'wpgeoip-functionality', 'Yes' );

/* Logging utilities */
function wpgeoip_add_log($action, $message)
{
  if (get_option("wpgeoip_logging", "No") == "Yes") {
    global $wpdb;
    $action = $wpdb->escape($action);
    $message = $wpdb->escape($message);
    $wpdb->query(
      "INSERT INTO wpgeoip_log VALUES (null, '$action', '$message')"
    );
  }
}

/**
 * Install/Remove Database Table
 */
require_once "wpgeoip_install.php";
register_activation_hook(__FILE__, "wpgeoip_install");
register_deactivation_hook(__FILE__, "wpgeoip_uninstall");

/**
 * Add CSS/JS
 */
add_action("admin_enqueue_scripts", "wpgeoip_resources");
function wpgeoip_resources()
{
  wp_register_style(
    "geoip_wp_admin_chosenCSS",
    plugin_dir_url(__FILE__) . "/assets/resources/chosen.min.css",
    false,
    "1.0.0"
  );
  wp_enqueue_style("geoip_wp_admin_chosenCSS");

  wp_register_style(
    "geoip_wp_admin_CSS",
    plugin_dir_url(__FILE__) . "/assets/wpgeoip.css",
    false,
    "1.0.0"
  );
  wp_enqueue_style("geoip_wp_admin_CSS");

  wp_register_script(
    "geoip_wp_admin_chosenJS",
    plugin_dir_url(__FILE__) . "/assets/resources/chosen.jquery.min.js",
    false,
    "1.0.0"
  );
  wp_enqueue_script("geoip_wp_admin_chosenJS");

  wp_register_script(
    "geoip_wp_admin_pluginJS",
    plugin_dir_url(__FILE__) . "/assets/resources/wpgeoip.js",
    false,
    "1.0.0"
  );
  wp_enqueue_script("geoip_wp_admin_pluginJS");

  wp_localize_script("geoip_wp_admin_pluginJS", "wpgeoipajax", [
    "ajax_url" => admin_url("admin-ajax.php"),
  ]);
}

/**
 * Add WP-Admin Pages
 */

// Add settings link on plugin page
function wpgeoip_settings_link($links)
{
  $url = admin_url("admin.php?page=wpgeoip-admin");
  $settings_link = '<a href="' . $url . '">Settings</a>';
  $mylinks = [$settings_link];
  return array_merge($links, $mylinks);
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", "wpgeoip_settings_link");

require_once "wpgeoip_rules.php";
require_once "wpgeoip-mass.php";
require_once "wpgeoip.log.php";
require_once "wpgeoip_noredirect.php";
require_once "wpgeoip_ipexclusion.php";
require_once "wpgeoip_once_redirect.php";
require_once "wpgeoip_autoupdate.php";

function geoip_admin_menu()
{
  add_menu_page(
    "WP GeoIP",
    "WP GeoIP",
    "manage_options",
    "wpgeoip-admin",
    "wpgeoip_admin",
    plugin_dir_url(__FILE__) . "/assets/images/icon16x16.png"
  );
  add_submenu_page(
    "wpgeoip-admin",
    "WPGeoIP Rules",
    "WPGeoIP Rules",
    "manage_options",
    "wpgeoip-admin",
    "wpgeoip_admin"
  );
  add_submenu_page(
    "wpgeoip-admin",
    "WPGeoIP Validation",
    "WPGeoIP Validation",
    "manage_options",
    "wpgeoip-license",
    "wpgeoip_autoupdate"
  );
  add_submenu_page(
    "wpgeoip-admin",
    "WPGeoIP Once Redirect",
    "WPGeoIP Once Redirect",
    "manage_options",
    "wpgeoip-once-redirect",
    "wpgeoip_once_redirect"
  );
  add_submenu_page(
    "wpgeoip-admin",
    "WPGeoIP Log",
    "WPGeoIP Log",
    "manage_options",
    "wpgeoip-log",
    "wpgeoip_log"
  );
  add_submenu_page(
    "wpgeoip-admin",
    "WPGeoIP Mass Redirect",
    "WPGeoIP Mass Redirect",
    "manage_options",
    "wpgeoip-admin-mass",
    "wpgeoip_mass_redirect"
  );
  add_submenu_page(
    "wpgeoip-admin",
    "WPGeoIP NO Redirect",
    "WPGeoIP NO Redirect",
    "manage_options",
    "wpgeoip-noredirect",
    "wpgeoip_noredirect"
  );
  add_submenu_page(
    "wpgeoip-admin",
    "WPGeoIP IP Exclusion",
    "WPGeoIP IP Exclusion",
    "manage_options",
    "wpgeoip-ipexclusion",
    "wpgeoip_ipexclusion"
  );
}

add_action("admin_menu", "geoip_admin_menu");

/**
 * Add Redirect Function
 */
require_once "wpgeoip_redirect.php";
add_action("wp_head", "wpgeoip_redirect");

/**
 * Add Cookie Support
 */
// add_action('init', 'WPGeoIPStartSession', 1);
// function WPGeoIPStartSession() {
//     if(!session_id()) {
//         session_start();
//     }
//     ob_start();
// }
