<?php
/**
 * WPA_WCVS Core Functions
 * General core functions available on both the front-end and admin.
 *
 * @package WPA_WCVS
 * @version 1.0.0
 * @author  WPA_WCVS
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
function wpa_wcvs_get_template_part( $slug, $name = '' ) {
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/woocommerce-product-bundle/slug-name.php
	if ( $name ) {
		$template = locate_template( array( "{$slug}-{$name}.php", WPA_WCVS()->template_path() . "{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( WPA_WCVS()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = WPA_WCVS()->plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/woocommerce-product-bundle/slug.php
	if ( ! $template ) {
		$template = locate_template( array( "{$slug}.php", WPA_WCVS()->template_path() . "{$slug}.php" ) );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'wpa_wcvs_get_template_part', $template, $slug, $name );

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
function wpa_wcvs_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	$located = wpa_wcvs_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$located = apply_filters( 'wpa_wcvs_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'wpa_wcvs_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'wpa_wcvs_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * @return string
 */
function wpa_wcvs_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = WPA_WCVS()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = WPA_WCVS()->plugin_path() . '/templates/';
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
	return apply_filters( 'wpa_wcvs_locate_template', $template, $template_name, $template_path );
}