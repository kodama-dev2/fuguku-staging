<?php
/**
 * Add element banner for VC.
 *
 * @since   1.0.0
 * @package Claue
 */

function jas_claue_vc_map_promotion() {
	vc_map(
		array(
			'name'     => esc_html__( 'Promotion Banner', 'claue' ),
			'base'     => 'claue_addons_promotion',
			'icon'     => 'pe-7s-cash',
			'category' => esc_html__( 'Content', 'claue' ),
			'params'   => array(
				array(
					'param_name' => 'image',
					'heading'    => esc_html__( 'Image', 'claue' ),
					'type'       => 'attach_image'
				),
				array(
					'param_name' => 'link',
					'heading'    => esc_html__( 'Link to', 'claue' ),
					'type'       => 'vc_link'
				),
				array(
					'param_name' => 'v_align',
					'heading'    => esc_html__( 'Text vertical align', 'claue' ),
					'type'       => 'dropdown',
					'value'      => array(
						esc_html__( 'Top', 'claue' )    => 'top',
						esc_html__( 'Middle', 'claue' ) => 'middle',
						esc_html__( 'Bottom', 'claue' ) => 'bottom'
					)
				),
				array(
					'param_name' => 'h_align',
					'heading'    => esc_html__( 'Text horizontal align', 'claue' ),
					'type'       => 'dropdown',
					'value'      => array(
						esc_html__( 'Left', 'claue' )   => 'left',
						esc_html__( 'Center', 'claue' ) => 'center',
						esc_html__( 'Right', 'claue' )  => 'right'
					)
				),
				vc_map_add_css_animation(),
				array(
					'param_name'  => 'class',
					'heading'     => esc_html__( 'Extra class name', 'claue' ),
					'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'claue' ),
					'type' 	      => 'textfield',
				),
				array(
					'param_name' => 'text_1',
					'heading'    => esc_html__( 'Text', 'claue' ),
					'type'       => 'textfield',
					'group'      => esc_html__( 'Text 1', 'claue' )
				),
				array(
					'param_name'  => 'text_1_font_size',
					'heading'     => esc_html__( 'Font size', 'claue' ),
					'type'        => 'textfield',
					'group'       => esc_html__( 'Text 1', 'claue' ),
					'description' => esc_html__( 'Defined in pixels. Do not add the \'px\' unit.', 'claue' ),
				),
				array(
					'param_name' => 'text_1_line_height',
					'heading'    => esc_html__( 'Line height', 'claue' ),
					'type'       => 'textfield',
					'group'      => esc_html__( 'Text 1', 'claue' )
				),
				array(
					'param_name' => 'text_1_color',
					'heading'    => esc_html__( 'Color', 'claue' ),
					'type'       => 'colorpicker',
					'group'      => esc_html__( 'Text 1', 'claue' )
				),
				array(
					'param_name'  => 'text_1_margin',
					'heading'     => esc_html__( 'Margin bottom', 'claue' ),
					'type'        => 'textfield',
					'group'       => esc_html__( 'Text 1', 'claue' ),
					'description' => esc_html__( 'Defined in pixels. Do not add the \'px\' unit.', 'claue' ),
				),
				array(
					'param_name' => 'text_2',
					'heading'    => esc_html__( 'Text', 'claue' ),
					'type'       => 'textfield',
					'group'      => esc_html__( 'Text 2', 'claue' )
				),
				array(
					'param_name'  => 'text_2_font_size',
					'heading'     => esc_html__( 'Font size', 'claue' ),
					'type'        => 'textfield',
					'group'       => esc_html__( 'Text 2', 'claue' ),
					'description' => esc_html__( 'Defined in pixels. Do not add the \'px\' unit.', 'claue' ),
				),
				array(
					'param_name' => 'text_2_line_height',
					'heading'    => esc_html__( 'Line height', 'claue' ),
					'type'       => 'textfield',
					'group'      => esc_html__( 'Text 2', 'claue' )
				),
				array(
					'param_name' => 'text_2_color',
					'heading'    => esc_html__( 'Color', 'claue' ),
					'type'       => 'colorpicker',
					'group'      => esc_html__( 'Text 2', 'claue' )
				),
				array(
					'param_name'  => 'text_2_margin',
					'heading'     => esc_html__( 'Margin bottom', 'claue' ),
					'type'        => 'textfield',
					'group'       => esc_html__( 'Text 2', 'claue' ),
					'description' => esc_html__( 'Defined in pixels. Do not add the \'px\' unit.', 'claue' ),
				),
				array(
					'param_name' => 'text_3',
					'heading'    => esc_html__( 'Text', 'claue' ),
					'type'       => 'textfield',
					'group'      => esc_html__( 'Text 3', 'claue' )
				),
				array(
					'param_name'  => 'text_3_font_size',
					'heading'     => esc_html__( 'Font size', 'claue' ),
					'type'        => 'textfield',
					'group'       => esc_html__( 'Text 3', 'claue' ),
					'description' => esc_html__( 'Defined in pixels. Do not add the \'px\' unit.', 'claue' ),
				),
				array(
					'param_name' => 'text_3_line_height',
					'heading'    => esc_html__( 'Line height', 'claue' ),
					'type'       => 'textfield',
					'group'      => esc_html__( 'Text 3', 'claue' )
				),
				array(
					'param_name' => 'text_3_color',
					'heading'    => esc_html__( 'Color', 'claue' ),
					'type'       => 'colorpicker',
					'group'      => esc_html__( 'Text 3', 'claue' )
				),
				array(
					'param_name' => 'text_3_button',
					'heading'    => esc_html__( 'Make it as button', 'claue' ),
					'type'       => 'checkbox',
					'group'      => esc_html__( 'Text 3', 'claue' )
				),
			)
		)
	);
}
add_action( 'vc_before_init', 'jas_claue_vc_map_promotion' );