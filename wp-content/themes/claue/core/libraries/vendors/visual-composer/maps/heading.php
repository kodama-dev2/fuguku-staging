<?php
/**
 * Custom heading for visual composer.
 *
 * @since   1.0.0
 * @package Claue
 */

function jas_claue_vc_add_params_to_custom_heading() {
	vc_add_params(
		'vc_custom_heading',
		array(
			array(
				'heading'     => esc_html__( 'Enable divider?', 'claue' ),
				'description' => esc_html__( 'Add a thin line to left and right of heading.', 'claue' ),
				'type'        => 'checkbox',
				'param_name'  => 'divider',
				'weight'      => 1,
				'value'       => array(
					esc_html__( 'Yes', 'claue' ) => 'yes'
				),
			),
			array(
				'param_name'  => 'sub_title',
				'heading'     => esc_html__( 'Small text', 'claue' ),
				'description' => esc_html__( 'It shows below the Text', 'claue' ),
				'type'        => 'textarea',
				'weight'      => 1,
			),
		)
	);
}
add_action( 'vc_after_init', 'jas_claue_vc_add_params_to_custom_heading' );