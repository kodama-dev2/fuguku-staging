<?php
/**
 * Add element service for VC.
 *
 * @since   1.0.0
 * @package Claue
 */

function jas_claue_vc_map_service() {
	vc_map(
		array(
			'name'     => esc_html__( 'Icon and Text', 'claue' ),
			'base'     => 'claue_addons_service',
			'icon'     => 'pe-7s-display2',
			'category' => esc_html__( 'Content', 'claue' ),
			'params'   => array(
				array(
					'param_name' => 'icon',
					'heading'    => esc_html__( 'Icon', 'claue' ),
					'type'       => 'iconpicker',
					'settings'   => array(
						'emptyIcon'    => true,
						'iconsPerPage' => 4000,
						'type'         => 'stroke'
					) ,
				),
				array(
					'param_name' => 'icon_style',
					'heading'    => esc_html__( 'Icon Style', 'claue' ),
					'type'       => 'dropdown',
					'value' => array(
						esc_html__( 'Default', 'claue' ) => '',
						esc_html__( 'Square', 'claue' )  => 'square',
						esc_html__( 'Circle', 'claue' )  => 'circle',
					),
				),
				array(
					'param_name' => 'icon_size',
					'heading'    => esc_html__( 'Icon Size', 'claue' ),
					'type'       => 'dropdown',
					'value' => array(
						esc_html__( 'Small', 'claue' )  => 'small',
						esc_html__( 'Medium', 'claue' ) => 'medium',
						esc_html__( 'Large', 'claue' )  => 'large',
					),
				),
				array(
					'param_name' => 'icon_position',
					'heading'    => esc_html__( 'Icon Position', 'claue' ),
					'type'       => 'dropdown',
					'value' => array(
						esc_html__( 'Top', 'claue' )   => 'tc',
						esc_html__( 'Right', 'claue' ) => 'tr',
						esc_html__( 'Left', 'claue' )  => 'tl',
					),
				),
				array(
					'param_name'  => 'title',
					'heading'     => esc_html__( 'Title', 'claue' ),
					'type'        => 'textfield',
					'admin_label' => true,
				),
				array(
					'param_name' => 'entry',
					'heading'    => esc_html__( 'Content', 'claue' ),
					'type'       => 'textarea'
				),
				array(
					'param_name'       => 'icon_color',
					'heading'          => esc_html__( 'Icon Color', 'claue' ),
					'type'             => 'colorpicker',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'param_name'       => 'title_color',
					'heading'          => esc_html__( 'Title Color', 'claue' ),
					'type'             => 'colorpicker',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'param_name'       => 'content_color',
					'heading'          => esc_html__( 'Content Color', 'claue' ),
					'type'             => 'colorpicker',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
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
add_action( 'vc_before_init', 'jas_claue_vc_map_service' );