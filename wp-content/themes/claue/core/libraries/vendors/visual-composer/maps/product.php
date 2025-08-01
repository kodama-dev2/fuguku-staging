<?php
/**
 * Add element product for visual composer.
 *
 * @since   1.0.0
 * @package Claue
 */

function jas_claue_vc_map_product() {
	vc_remove_element( 'product' );
	vc_map(
		array(
			'name'        => esc_html__( 'Product', 'claue' ),
			'description' => esc_html__( 'Show a single product by ID or SKU', 'claue' ),
			'base'        => 'claue_addons_product',
			'icon'        => 'pe-7s-shopbag',
			'category'    => esc_html__( 'WooCommerce', 'claue' ),
			'params'      => array(
				array(
					'param_name'  => 'id',
					'heading'     => esc_html__( 'Select identificator', 'claue' ),
					'type'        => 'autocomplete',
					'description' => esc_html__( 'Input product ID or product SKU or product title to see suggestions', 'claue' ),
				),
				array(
					'param_name' => 'sku',
					'type'       => 'hidden',
					'dependency' => array(
						'element' => 'order',
						'value'   => 'all',
					),
				),
				array(
					'param_name'  => 'countdown',
					'heading'     => esc_html__( 'Enable countdown for sale product', 'claue' ),
					'description' => esc_html__( 'Setup sale schedule in product page first. Only work with product type simple', 'claue' ),
					'type'        => 'checkbox',
					'edit_field_class' => 'vc_col-xs-12 vc_column pt__15',
				),
				array(
					'param_name' => 'flip',
					'heading'    => esc_html__( 'Enable Flip Product Thumbnail', 'claue' ),
					'type' 	     => 'checkbox'
				),
				vc_map_add_css_animation(),
				array(
					'param_name'  => 'class',
					'heading'     => esc_html__( 'Extra class name', 'claue' ),
					'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'claue' ),
					'type' 	      => 'textfield',
					'edit_field_class' => 'vc_col-xs-12 vc_column pt__15',
				),
				array(
					'param_name' => 'issc',
					'type'       => 'hidden',
					'value'      => true,
				),
			)
		)
	);
}
add_action( 'vc_before_init', 'jas_claue_vc_map_product' );