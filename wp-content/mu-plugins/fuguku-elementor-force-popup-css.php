<?php
/**
 * Plugin Name: Fuguku — force Elementor CSS for country popup
 * Description: Memuat CSS file template Elementor (elementor_library) untuk popup di frontend. Mengatasi popup yang tampil “polos” di publish padahal di editor/preview benar — biasanya karena CSS post tidak ikut di-enqueue sampai kondisi tertentu.
 * Version: 1.0.0
 *
 * Ganti ID lewat filter: add_filter( 'fuguku_forced_elementor_css_post_id', fn() => 10504 );
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Post ID template Elementor: Popup “select-country-list” (elementor_library). */
const FUGUKU_ELEMENTOR_COUNTRY_POPUP_POST_ID = 10504;

add_action(
	'elementor/frontend/after_enqueue_styles',
	static function () {
		if ( is_admin() ) {
			return;
		}
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}
		if ( ! class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
			return;
		}

		$post_id = (int) apply_filters( 'fuguku_forced_elementor_css_post_id', FUGUKU_ELEMENTOR_COUNTRY_POPUP_POST_ID );
		if ( $post_id < 1 ) {
			return;
		}

		try {
			\Elementor\Core\Files\CSS\Post::create( $post_id )->enqueue();
		} catch ( \Throwable $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'fuguku-elementor-force-popup-css: ' . $e->getMessage() );
			}
		}
	},
	20
);
