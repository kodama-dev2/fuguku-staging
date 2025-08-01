<?php
/**
 * Add element banner for VC.
 *
 * @since   1.0.0
 * @package Claue
 */

function jas_claue_vc_map_banner() {
	vc_map(
		array(
			'name'     => esc_html__( 'Image and Text', 'claue' ),
			'base'     => 'claue_addons_banner',
			'icon'     => 'pe-7s-photo',
			'category' => esc_html__( 'Content', 'claue' ),
			'params'   => array(
				array(
					'param_name' => 'image',
					'heading'    => esc_html__( 'Image', 'claue' ),
					'type'       => 'attach_image',
				),
				array(
					'param_name' => 'text',
					'heading'    => esc_html__( 'Text', 'claue' ),
					'type'       => 'textarea',
				),
				array(
					'param_name' => 'link',
					'heading'    => esc_html__( 'Link to', 'claue' ),
					'type'       => 'vc_link',
				),
				vc_map_add_css_animation(),
				array(
					'param_name'  => 'class',
					'heading'     => esc_html__( 'Extra class name', 'claue' ),
					'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'claue' ),
					'type' 	      => 'textfield',
				),
			)
		)
	);
}
add_action( 'vc_before_init', 'jas_claue_vc_map_banner' );