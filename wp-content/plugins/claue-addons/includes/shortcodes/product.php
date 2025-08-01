<?php
/**
 * Product shortcode.
 *
 * @package ClaueAddons
 * @since   1.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'claue_addons_shortcode_product' ) ) {
	function claue_addons_shortcode_product( $atts, $content = null ) {
		$output = '';

		global $jassc;

		$atts = shortcode_atts( array(
			'id'            => '',
			'sku'           => '',
			'countdown'     => '',
			'css_animation' => '',
			'class'         => '',
			'flip'          => false,
			'style'         => 'grid',
			'filter'        => '',
			'columns'       => 12,
			'slider'        => '',
			'issc'          => true,
		), $atts );

		$jassc = $atts;

		$classes = array( 'jas-sc-product ' . $atts['class'] );

		if ( '' !== $atts['css_animation'] ) {
			wp_enqueue_script( 'waypoints' );
			$classes[] = 'wpb_animate_when_almost_visible wpb_' . $atts['css_animation'];
		}

		$meta_query = WC()->query->get_meta_query();

		$args = array(
			'post_type'              => 'product',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'post_status'            => 'publish',
			'cache_results'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_query'             => $meta_query
		);

		if ( $atts['sku'] !== '' )
			$args['meta_query'][] = array(
				'key' 		=> '_sku',
				'value' 	=> $atts['sku'],
				'compare' 	=> '='
			);

		if ( $atts['id'] !== '' )
			$args['p'] = $atts['id'];

		ob_start();

		$products = new WP_Query( $args );

		if ( $products->have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

		wp_reset_postdata();

		$output .= '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">' . ob_get_clean() . '</div>';

		// Reset jassc global variable to null for render shortcode after
		$jassc = NULL;

		// Return output
		return apply_filters( 'claue_addons_shortcode_product', force_balance_tags( $output ) );
	}
}