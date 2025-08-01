<?php
/**
 * Add element blog for visual composer.
 *
 * @since   1.0.0
 * @package Claue
 */

function jas_claue_vc_map_blog() {
	vc_map(
		array(
			'name'        => esc_html__( 'Blog', 'claue' ),
			'description' => esc_html__( 'Show blog post by ID or title', 'claue' ),
			'base'        => 'claue_addons_blog',
			'icon'        => 'pe-7s-news-paper',
			'category'    => esc_html__( 'Content', 'claue' ),
			'params'      => array(
				array(
					'param_name'  => 'id',
					'heading'     => esc_html__( 'Include special posts', 'claue' ),
					'description' => esc_html__( 'Enter posts title to display only those records.', 'claue' ),
					'type'        => 'autocomplete',
					'settings'    => array(
						'multiple'      => true,
						'sortable'      => true,
						'unique_values' => true,
					),
				),
				array(
					'param_name' => 'style',
					'heading'    => esc_html__( 'Style', 'claue' ),
					'type'       => 'dropdown',
					'value'      => array(
						esc_html__( 'Content Outside Thumbnail', 'claue' ) => 'outside',
						esc_html__( 'Content Inside Thumbnail', 'claue' )  => 'inside',
					),
					'std' => 'outside',
				),
				array(
					'param_name'  => 'thumb_size',
					'heading'     => esc_html__( 'Thumbnail size', 'claue' ),
					'description' => esc_html__( 'width x height, example: 570x320', 'claue' ),
					'type'        => 'textfield'
				),
				array(
					'param_name' => 'columns',
					'heading'    => esc_html__( 'Columns', 'claue' ),
					'type'       => 'dropdown',
					'value'      => array(
						esc_html__( '2 columns', 'claue' ) => '6',
						esc_html__( '3 columns', 'claue' ) => '4',
						esc_html__( '4 columns', 'claue' ) => '3',
					),
					'std' => '4',
				),
				array(
					'param_name'  => 'limit',
					'heading'     => esc_html__( 'Per Page', 'claue' ),
					'description' => esc_html__( 'How much items per page to show (-1 to show all posts)', 'claue' ),
					'type'        => 'textfield',
					'value'       => 3,
				),
				array(
					'param_name' => 'slider',
					'heading'    => esc_html__( 'Enable Slider', 'claue' ),
					'type'       => 'checkbox'
				),
				array(
					'param_name' => 'autoplay',
					'heading'    => esc_html__( 'Enable Auto play', 'claue' ),
					'type'       => 'checkbox',
					'dependency' => array(
						'element' => 'slider',
						'value'   => 'true'
					),
				),
				array(
					'param_name' => 'arrows',
					'heading'    => esc_html__( 'Enable Navigation', 'claue' ),
					'type'       => 'checkbox',
					'dependency' => array(
						'element' => 'slider',
						'value'   => 'true'
					),
				),
				array(
					'param_name' => 'dots',
					'heading'    => esc_html__( 'Enable Pagination', 'claue' ),
					'type'       => 'checkbox',
					'dependency' => array(
						'element' => 'slider',
						'value'   => 'true'
					),
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
add_action( 'vc_before_init', 'jas_claue_vc_map_blog' );