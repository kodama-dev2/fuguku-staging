<?php
/**
 * Add element member for VC.
 *
 * @since   1.0.0
 * @package Claue
 */

function jas_claue_vc_map_member() {
	vc_map(
		array(
			'name'     => esc_html__( 'Member', 'claue' ),
			'base'     => 'claue_addons_member',
			'icon'     => 'pe-7s-id',
			'category' => esc_html__( 'Content', 'claue' ),
			'params'   => array(
				array(
					'param_name' => 'avatar',
					'heading'    => esc_html__( 'Avatar', 'claue' ),
					'type'       => 'attach_image',
				),
				array(
					'param_name' => 'name',
					'heading'    => esc_html__( 'Name', 'claue' ),
					'type'       => 'textfield',
				),
				array(
					'param_name' => 'job',
					'heading'    => esc_html__( 'Job', 'claue' ),
					'type'       => 'textfield',
				),
				array(
					'param_name' => 'facebook',
					'heading'    => esc_html__( 'Facebook Link', 'claue' ),
					'type'       => 'textfield',
					'group'      => esc_html__( 'Social Network', 'claue' ),
				),
				array(
					'param_name' => 'twitter',
					'heading'    => esc_html__( 'Twitter Link', 'claue' ),
					'type'       => 'textfield',
					'group'      => esc_html__( 'Social Network', 'claue' ),
				),
				array(
					'param_name' => 'instagram',
					'heading'    => esc_html__( 'Instagram Link', 'claue' ),
					'type'       => 'textfield',
					'group'      => esc_html__( 'Social Network', 'claue' ),
				),
				array(
					'param_name' => 'dribbble',
					'heading'    => esc_html__( 'Dribbble Link', 'claue' ),
					'type'       => 'textfield',
					'group'      => esc_html__( 'Social Network', 'claue' ),
				),
				array(
					'param_name' => 'behance',
					'heading'    => esc_html__( 'Behance Link', 'claue' ),
					'type'       => 'textfield',
					'group'      => esc_html__( 'Social Network', 'claue' ),
				),
				array(
					'param_name' => 'linkedin',
					'heading'    => esc_html__( 'Linkedin Link', 'claue' ),
					'type'       => 'textfield',
					'group'      => esc_html__( 'Social Network', 'claue' ),
				),
				array(
					'param_name' => 'tumblr',
					'heading'    => esc_html__( 'Tumblr Link', 'claue' ),
					'type'       => 'textfield',
					'group'      => esc_html__( 'Social Network', 'claue' ),
				),
				array(
					'param_name' => 'pinterest',
					'heading'    => esc_html__( 'Pinterest Link', 'claue' ),
					'type'       => 'textfield',
					'group'      => esc_html__( 'Social Network', 'claue' ),
				),
				array(
					'param_name' => 'googleplus',
					'heading'    => esc_html__( 'Google Plus Link', 'claue' ),
					'type'       => 'textfield',
					'group'      => esc_html__( 'Social Network', 'claue' ),
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
add_action( 'vc_before_init', 'jas_claue_vc_map_member' );