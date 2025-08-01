<?php
/**
 * Class shortcode
 * Add shortcode for PinMaker.
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
class WPA_PM_Shortcodes {
	/**
	 * Initialize.
	 *
	 * @return  void
	 */
	public static function init() {
		$shortcodes = array(
			'pins'     => __CLASS__ . '::pins',
			'pins_cat' => __CLASS__ . '::pins_cat',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}

	/**
	 * Pins shortcode.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function pins( $atts ) {
		if ( empty( $atts ) ) {
			return '';
		}

		// Make shortcode attributes accessible from outside.
		global $wpapin;

		$args = array(
			'post_type'              => 'pins',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'post_status'            => 'publish',
			'cache_results'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false
		);

		$wpapin = $atts;

		ob_start();

		$pins = new WP_Query( apply_filters( 'wpa_pm_shortcode_pins_query', $args, $atts ) );

		if ( $pins->have_posts() ) :

			while ( $pins->have_posts() ) : $pins->the_post();

				wpa_pm_get_template_part( 'content', 'pin' );

			endwhile;

		endif;

		wp_reset_postdata();

		// Reset globally accessible shortcode attributes.
		$wpapin = NULL;

		return ob_get_clean();
	}

	/**
	 * Pins category shortcode.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function pins_cat( $atts ) {
		if ( ! $atts ) {
			return '';
		}

		$cat_attr = $classes = array();

		$query_args = array(
			'post_type'           => 'pins',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => 99,
			'tax_query' => array(
				array(
					'taxonomy' => 'pins_cat',
					'field'    => 'id',
					'terms'    => $atts['id'],
				),
			),
		);
		$display_type = get_term_meta( $atts['id'], 'display_type', true );
		$columns      = get_term_meta( $atts['id'], 'display_column', true );
		$gutter       = get_term_meta( $atts['id'], 'gutter_width', true );
		$autoplay     = get_term_meta( $atts['id'], 'slider_autoplay', true );
		$arrows       = get_term_meta( $atts['id'], 'slider_arrow', true );
		$dots         = get_term_meta( $atts['id'], 'slider_dot', true );

		if ( $display_type == 'masonry' ) {

			$cat_attr[] = 'data-masonry=\'{"selector": ".pin__wrapper", "columnWidth": ".pin__sizer", "gutterWidth": ' . esc_attr( $gutter ) . '}\'';

		} elseif ( $display_type == 'slider' ) {

			$cat_attr[] = 'data-slick=\'{"slidesToShow": ' . $columns . ', "slidesToScroll": 1, "autoplay": ' . ( $autoplay ? 'true' : 'false' ) . ', "arrows": ' . ( $arrows ? 'true' : 'false' ) . ', "dots": ' . ( $dots ? 'true' : 'false' ) . ', "fade": ' . ( $columns == 1 ? 'true' : 'false' ) . ', "responsive":[{"breakpoint": 960,"settings":{"slidesToShow": 1}},{"breakpoint": 480,"settings":{"slidesToShow": 1}}]}\'';
			if ( ! empty( $gutter ) ) {
				$cat_attr[] = 'data-grid=\'{"gutterWidth": ' . esc_attr( $gutter ) . '}\'';
			}
			$classes[]  = ' pm-slick';

		} else {

			$cat_attr[] = 'data-grid=\'{"gutterWidth": ' . esc_attr( $gutter ) . '}\'';

		}

		if ( $columns ) {
			$classes[] = ' pm-' . $columns . 'col';
		}

		ob_start();

		$pins_cat = new WP_Query( apply_filters( 'wpa_pm_shortcode_pins_cat_query', $query_args, $atts ) );

		echo '<div class="pin-maker' . implode( ' ', $classes ) . '" ' . implode( ' ', $cat_attr ) . '>';
			if ( $display_type == 'masonry' ) {
				echo '<div class="pin__sizer"></div>';
			}

			if ( $pins_cat->have_posts() ) :

				while ( $pins_cat->have_posts() ) : $pins_cat->the_post();

					echo do_shortcode( '[pins id="' . get_the_ID() . '"]' );

				endwhile;

			endif;

		echo '</div>';

		wp_reset_postdata();

		return ob_get_clean();
	}
}

WPA_PM_Shortcodes::init();
