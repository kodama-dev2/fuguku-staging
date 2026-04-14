<?php
/**
 * Plugin Name: Fuguku — force Elementor CSS for templates (popup / off-canvas / theme parts)
 * Description: Memuat file CSS template Elementor (elementor_library) di frontend. Mengatasi tampilan “polos” saat publish (popup negara, off-canvas menu, dll.) padahal di editor benar — biasanya CSS post tidak di-enqueue sampai kondisi tertentu / optimasi asset.
 * Version: 1.2.0
 *
 * Tambah/ubah ID lewat filter:
 *   add_filter( 'fuguku_forced_elementor_css_post_ids', function ( $ids ) {
 *       return array_merge( $ids, [ 12345 ] );
 *   } );
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Default: template Elementor (popup / theme parts) yang CSS-nya dipaksa di frontend. */
const FUGUKU_ELEMENTOR_DEFAULT_FORCED_CSS_IDS = [
	10504, // select-country-list
	10626, // off-canvas menu
	10670,
	10678,
	11972,
	11974,
	11976,
	11978,
	11980,
	12030,
];

/**
 * @return int[]
 */
function fuguku_forced_elementor_css_post_ids(): array {
	$defaults = FUGUKU_ELEMENTOR_DEFAULT_FORCED_CSS_IDS;
	/** @deprecated Gunakan fuguku_forced_elementor_css_post_ids */
	$legacy = (int) apply_filters( 'fuguku_forced_elementor_css_post_id', 0 );
	if ( $legacy > 0 ) {
		$defaults = array_merge( $defaults, [ $legacy ] );
	}

	$ids = apply_filters(
		'fuguku_forced_elementor_css_post_ids',
		$defaults
	);
	if ( ! is_array( $ids ) ) {
		return [];
	}
	$out = [];
	foreach ( $ids as $id ) {
		$id = (int) $id;
		if ( $id > 0 ) {
			$out[] = $id;
		}
	}
	return array_values( array_unique( $out ) );
}

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

		foreach ( fuguku_forced_elementor_css_post_ids() as $post_id ) {
			try {
				\Elementor\Core\Files\CSS\Post::create( $post_id )->enqueue();
			} catch ( \Throwable $e ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
					error_log( 'fuguku-elementor-force-css post ' . $post_id . ': ' . $e->getMessage() );
				}
			}
		}
	},
	20
);
