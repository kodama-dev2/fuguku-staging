<?php
/**
 * Add element meta slider for VC.
 *
 * @since   1.0.0
 * @package Claue
 */

function jas_claue_vc_map_meta_slider() {
	if ( ! class_exists( 'MetaSliderPlugin' ) ) return;
	$sliders = $slide = array();

	// list the tabs
	$args = array(
		'post_type'        => 'ml-slider',
		'post_status'      => 'publish',
		'suppress_filters' => 1, // wpml, ignore language filter
		'order'            => 'ASC',
		'posts_per_page'   => -1
	);

	$all_sliders = get_posts( $args );

	foreach( $all_sliders as $slideshow ) {
		$sliders[] = array(
			'title'  => $slideshow->post_title,
			'id'     => $slideshow->ID
		);
	}
	foreach ( $sliders as $value ) {
		$slide[$value['title']] = $value['id'];
	}
	vc_map(
		array(
			'name'     => esc_html__( 'Meta Slider', 'claue' ),
			'base'     => 'metaslider',
			'icon'     => 'pe-7s-albums',
			'category' => esc_html__( 'Content', 'claue' ),
			'params'   => array(
				array(
					'param_name' => 'id',
					'heading'    => esc_html__( 'Choose slide', 'claue' ),
					'type'       => 'dropdown',
					'value'      => $slide,
				)
			)
		)
	);
}
add_action( 'vc_before_init', 'jas_claue_vc_map_meta_slider' );