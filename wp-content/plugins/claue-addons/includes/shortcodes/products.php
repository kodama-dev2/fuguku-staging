<?php
/**
 * Products shortcode.
 *
 * @package FXAddons
 * @since   1.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'claue_addons_shortcode_products' ) ) {
	function claue_addons_shortcode_products( $atts, $content = null ) {
		$output = '';

		global $jassc;

		$atts = shortcode_atts( array(
			'style'         => 'grid',
			'id'            => '',
			'sku'           => '',
			'display'       => 'all',
			'orderby'       => 'title',
			'order'         => 'ASC',
			'cat_id'        => '',
			'limit'         => 12,
			'slider'        => '',
			'items'         => 4,
			'autoplay'      => '',
			'arrows'        => '',
			'dots'          => '',
			'columns'       => 4,
			'filter'        => false,
			'flip'          => false,
			'css_animation' => '',
			'class'         => '',
			'issc'          => true,
		), $atts );

		$jassc = $atts;

		$options = array();

		$classes = array( 'jas-sc-products ' . $atts['class'] );

		if ( '' !== $atts['css_animation'] ) {
			wp_enqueue_script( 'waypoints' );
			$classes[] = 'wpb_animate_when_almost_visible wpb_' . $atts['css_animation'];
		}

		$args = array(
			'post_type'              => 'product',
			'posts_per_page'         => (int) $atts['limit'],
			'no_found_rows'          => true,
			'post_status'            => 'publish',
			'cache_results'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'orderby'                => $atts['orderby'],
			'order'                  => $atts['order'],
			'meta_query'             => WC()->query->get_meta_query(),
			'tax_query'              => WC()->query->get_tax_query()
		);

		switch ( $atts['display'] ) {
			case 'all':

				if ( $atts['sku'] !== '' )
					$args['meta_query'][] = array(
						'key'     => '_sku',
						'value'   => array_map( 'trim', explode( ',', $atts['sku'] ) ),
						'compare' => 'IN'
					);

				if ( $atts['id'] !== '' )
					$args['post__in'] = array_map( 'trim', explode( ',', $atts['id'] ) );

				break;

			case 'recent':

				$args['orderby'] = 'date';
				$args['order']   = 'desc';

				break;

			case 'featured':

				$args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'featured',
					'operator' => 'IN',
				);

				break;

			case 'sale':

				$args['no_found_rows'] = 1;
				$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );

				break;

			case 'best_selling_products':

				$args['meta_key'] = 'total_sales';
				$args['orderby']  = 'meta_value_num';
				$args['order'] 	  = 'desc';

				break;

			case 'top_rated':

				add_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );

				break;

			case 'cat':
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'id',
						'terms'    => $atts['cat_id'],
					),
				);
		}

		ob_start();

		$products = new WP_Query( $args );

		if ( $products->have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template( 'content-product.php' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

		if ( 'top_rated' == $atts['display'] )
			remove_filter( 'posts_clauses', array( __CLASS__, 'order_by_rating_post_clauses' ) );

		$output .= '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';
			$output .= ob_get_clean();
		$output .= '</div>';

		wp_reset_postdata();

		// Reset jassc global variable to null for render shortcode after
		$jassc = NULL;

		// Return output
		return apply_filters( 'claue_addons_shortcode_products', force_balance_tags( $output ) );
	}
}