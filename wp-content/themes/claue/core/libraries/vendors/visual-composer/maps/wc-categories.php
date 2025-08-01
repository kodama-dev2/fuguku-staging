<?php
/**
 * Add element woocommerce categories for visual composer.
 *
 * @since   1.0.0
 * @package Claue
 */

function jas_claue_vc_map_wc_categories() {
	vc_remove_element( 'product_categories' );
	vc_map(
		array(
			'name'     => esc_html__( 'Product Categories', 'claue' ),
			'base'     => 'claue_addons_wc_categories',
			'category' => esc_html__( 'WooCommerce', 'claue' ),
			'icon'     => 'pe-7s-shopbag',
			'params'   => array(
				array(
					'param_name' => 'layout',
					'heading'    => esc_html__( 'Categories Layout', 'claue' ),
					'type'       => 'dropdown',
					'value' => array(
						esc_html__( 'Grid', 'claue' )    => 'grid',
						esc_html__( 'Masonry', 'claue' ) => 'masonry',
					),
				),
				array(
					'param_name' => 'columns',
					'heading'    => esc_html__( 'Columns', 'claue' ),
					'type'       => 'dropdown',
					'value'      => array(
						esc_html__( '2 columns', 'claue' ) => 6,
						esc_html__( '3 columns', 'claue' ) => 4,
						esc_html__( '4 columns', 'claue' ) => 3,
						esc_html__( '6 columns', 'claue' ) => 2,
					),
					'std' => 4
				),
				array(
					'param_name'  => 'exclude',
					'heading'     => esc_html__( 'Exclude', 'claue' ),
					'description' => esc_html__( 'Enter category id to exclude (Note: separate values by commas ",").', 'claue' ),
					'type'        => 'textfield',
				),
				array(
					'param_name'  => 'large',
					'heading'     => esc_html__( 'Item Large', 'claue' ),
					'description' => esc_html__( 'Number of item you want to set larger (Note: separate values by commas ",")', 'claue' ),
					'type'        => 'textfield',
					'dependency' => array(
						'element' => 'layout',
						'value'   => 'metro',
					),
				),
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
add_action( 'vc_before_init', 'jas_claue_vc_map_wc_categories' );