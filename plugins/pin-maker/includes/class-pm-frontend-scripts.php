<?php
/**
 * Class front-end script
 * Enqueue stylesheets and scripts for front-end
 *
 * @package PinMaker
 * @version 1.0.0
 * @author  WPAddon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post Types Class.
 *
 * @version 1.0.0
 */
class WPA_PM_Frontend_Scripts {
	/**
	 * Initialize.
	 *
	 * @return  void
	 */
	public static function init() {
		// Enqueue frontend script
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 100 );
	}

	/**
	 * Enqueue assets for frontend.
	 *
	 * @return  string
	 */
	public static function enqueue_scripts() {
		global $post;
		if ( is_a( $post, 'WP_Post' ) && ! has_shortcode( $post->post_content, 'pins' ) && ! has_shortcode( $post->post_content, 'pins_cat' ) ) {
			return;
		}

		// PM Assets
		wp_enqueue_style( 'pm-style', WPA_PM()->plugin_url() . '/assets/css/pm-frontend.css' );
		wp_enqueue_script( 'pm-script', WPA_PM()->plugin_url() . '/assets/js/pm-frontend.js', array( 'jquery' ), false, true );

		// Slick slider
		wp_enqueue_script( 'slick', WPA_PM()->plugin_url() . '/assets/vendors/slick/slick.min.js', array(), false, true );

		// Masonry
		wp_enqueue_script( 'isotope', WPA_PM()->plugin_url() . '/assets/vendors/isotope/isotope.pkgd.min.js', array(), false, true );
		wp_enqueue_script( 'imagesloaded', WPA_PM()->plugin_url() . '/assets/vendors/masonry/imagesloaded.pkgd.min', array(), false, true );

		do_action( 'wpa_pm_frontend_scripts' );
	}
}
WPA_PM_Frontend_Scripts::init();