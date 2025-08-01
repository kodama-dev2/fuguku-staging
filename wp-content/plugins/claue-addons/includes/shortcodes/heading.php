<?php
/**
 * Service shortcode.
 *
 * @package ClaueAddons
 * @since   1.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'claue_addons_shortcode_heading' ) ) {
	function claue_addons_shortcode_heading( $atts, $content = null ) {
		$output = $icon_color_attr = $title_color_attr = $content_color_attr = '';

		extract( shortcode_atts( array(
			'title'         => '',
			'sub_title'   => '',
			'divider'    => '',
			'css_animation' => '',
			'class'         => '',
		), $atts ) );

		$classes = array();

		if ( '' !== $css_animation ) {
			wp_enqueue_script( 'waypoints' );
			$classes[] = 'wpb_animate_when_almost_visible wpb_' . $css_animation;
		}

		if ( ! empty( $class ) ) {
			$classes[] = $class;
		}

		$h_classes = $divider ? ' divider pr' : '';

		$output .= '<div class="jas-heading-wrapper ' . esc_attr( implode( ' ', $classes ) ) . '">';
			$output .= '<h3 class="jas-heading' . esc_attr( $h_classes ) . '">';
				$output .= esc_html($title);
            if ( $sub_title ) {
                $output .= '<span class="sub-title db">' . esc_html($sub_title) . '</span>';
            }
            $output .= '</h3>';
		$output .= '</div>';

		// Return output
		return apply_filters( 'claue_addons_shortcode_heading', force_balance_tags( $output ) );
	}
}