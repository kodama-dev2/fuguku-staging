<?php
/**
 * PinMaker Core Functions
 * General core functions available on both the front-end and admin.
 *
 * @package PinMaker
 * @version 1.0.0
 * @author  WPAddon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get template part
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 */
function wpa_pm_get_template_part( $slug, $name = '' ) {
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/pin-maker/slug-name.php
	if ( $name ) {
		$template = locate_template( array( "{$slug}-{$name}.php", WPA_PM()->template_path() . "{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( WPA_PM()->plugin_path() . "/views/{$slug}-{$name}.php" ) ) {
		$template = WPA_PM()->plugin_path() . "/views/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/pin-maker/slug.php
	if ( ! $template ) {
		$template = locate_template( array( "{$slug}.php", WPA_PM()->template_path() . "{$slug}.php" ) );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'wpa_pm_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Get other templates passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
function wpa_pm_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	$located = wpa_pm_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$located = apply_filters( 'wpa_pm_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'wpa_pm_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'wpa_pm_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * @return string
 */
function wpa_pm_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = WPA_PM()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = WPA_PM()->plugin_path() . '/views/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template/
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'wpa_pm_locate_template', $template, $template_name, $template_path );
}

/**
 * Get pin ID.
 *
 * @return string
 */
if ( ! function_exists( 'wpa_pm_pin_id' ) ) {
	/**
	 * Output the pin with type icon.
	 *
	 * @since 1.0.0
	 */
	function wpa_pm_pin_id() {
		global $wpapin;
		
		return $wpapin['id'];
	}
}