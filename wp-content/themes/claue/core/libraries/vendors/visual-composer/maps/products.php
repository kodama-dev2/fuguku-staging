<?php
/**
 * Add element product list for visual composer.
 *
 * @since   1.0.0
 * @package Claue
 */

function jas_claue_vc_map_products() {
	// Get all terms of woocommerce
	$product_cat = array();
	$terms = get_terms( 'product_cat' );
	if ( $terms && ! isset( $terms->errors ) ) {
		foreach ( $terms as $key => $value ) {
			$product_cat[$value->name] = $value->term_id;
		}
	}
	vc_map(
		array(
			'name'        => esc_html__( 'Products', 'claue' ),
			'description' => esc_html__( 'Show multiple products by ID or SKU.', 'claue' ),
			'base'        => 'claue_addons_products',
			'icon'        => 'pe-7s-shopbag',
			'category'    => esc_html__( 'WooCommerce', 'claue' ),
			'params'      => array(
				array(
					'param_name' => 'style',
					'heading'    => esc_html__( 'List product style', 'claue' ),
					'type'       => 'dropdown',
					'value' => array(
						esc_html__( 'Grid', 'claue' )    => 'grid',
						esc_html__( 'Masonry', 'claue' ) => 'masonry',
						esc_html__( 'Metro', 'claue' )   => 'metro',
					),
					'edit_field_class' => 'vc_col-xs-6'
				),
				array(
					'param_name' => 'display',
					'heading'    => esc_html__( 'Display', 'claue' ),
					'type' 	     => 'dropdown',
					'value'      => array(
						esc_html__( 'All products', 'claue' ) 		   => 'all',
						esc_html__( 'Recent products', 'claue' ) 	   => 'recent',
						esc_html__( 'Featured products', 'claue' ) 	   => 'featured',
						esc_html__( 'Sale products', 'claue' ) 		   => 'sale',
						esc_html__( 'Best selling products', 'claue' ) => 'best_selling_products',
						esc_html__( 'Top Rated Products', 'claue' )    => 'rated',
						esc_html__( 'Products by category', 'claue' )  => 'cat',
					),
					'edit_field_class' => 'vc_col-xs-6 pt__0',
					'admin_label'      => true,
				),
				array(
					'param_name'  => 'orderby',
					'heading'     => esc_html__( 'Order By', 'claue' ),
					'description' => sprintf( wp_kses_post( 'Select how to sort retrieved products. More at %s. Default by Title', 'claue' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Title', 'claue' )         => 'title',
						esc_html__( 'Date', 'claue' )          => 'date',
						esc_html__( 'ID', 'claue' )            => 'ID',
						esc_html__( 'Author', 'claue' )        => 'author',
						esc_html__( 'Modified', 'claue' )      => 'modified',
						esc_html__( 'Random', 'claue' )        => 'rand',
						esc_html__( 'Comment count', 'claue' ) => 'comment_count',
						esc_html__( 'Menu order', 'claue' )    => 'menu_order',
					),
					'dependency'  => array(
						'element' => 'display',
						'value' => array( 'all', 'featured', 'sale', 'rated', 'cat' ),
					),
					'edit_field_class' => 'vc_col-xs-6 vc_column pt__15',
				),
				array(
					'param_name'  => 'order',
					'heading'     => esc_html__( 'Order', 'claue' ),
					'description' => sprintf( __( 'Designates the ascending or descending order. More at %s. Default by ASC', 'claue' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Ascending', 'claue' ) => 'ASC',
						esc_html__( 'Descending', 'claue' ) => 'DESC',
					),
					'dependency' => array(
						'element' => 'display',
						'value'   => array( 'all', 'featured', 'sale', 'rated', 'cat' ),
					),
					'edit_field_class' => 'vc_col-xs-6 vc_column pt__15',
				),
				array(
					'param_name'  => 'id',
					'heading'     => esc_html__( 'Products', 'claue' ),
					'description' => esc_html__( 'Input product ID or product SKU or product title to see suggestions', 'claue' ),
					'type'        => 'autocomplete',
					'settings'    => array(
						'multiple'      => true,
						'sortable'      => true,
						'unique_values' => true,
					),
					'save_always' => true,
					'dependency'  => array(
						'element' => 'display',
						'value'   => 'all',
					),
				),
				array(
					'param_name' => 'sku',
					'type'       => 'hidden',
					'dependency' => array(
						'element' => 'display',
						'value'   => 'all',
					),
				),
				array(
					'heading'    => esc_html__( 'Product Category', 'claue' ),
					'param_name' => 'cat_id',
					'type'       => 'dropdown',
					'value'      => $product_cat,
					'dependency' => array(
						'element' => 'display',
						'value'   => 'cat',
					),
					'edit_field_class' => 'vc_col-xs-12 vc_column pt__15',
				),
				array(
					'param_name'  => 'limit',
					'heading'     => esc_html__( 'Per Page', 'claue' ),
					'description' => esc_html__( 'How much items per page to show (-1 to show all products)', 'claue' ),
					'type'        => 'textfield',
					'value'       => 12,
				),
				array(
					'param_name' => 'slider',
					'heading'    => esc_html__( 'Enable Slider', 'claue' ),
					'type'       => 'dropdown',
					'value'      => array(
						esc_html__( 'No', 'claue' ) => 'no',
						esc_html__( 'Yes', 'claue' )  => 'yes',
					),
					'dependency' => array(
						'element' => 'style',
						'value'   => 'grid'
					),
				),
				array(
					'param_name'  => 'items',
					'heading'     => esc_html__( 'Items (Number only)', 'claue' ),
					'group'       => esc_html__( 'Slider Settings', 'claue' ),
					'description' => esc_html__( 'Set the maximum amount of items displayed at a time with the widest browser width', 'claue' ),
					'type'        => 'textfield',
					'value'       => 4,
					'dependency' => array(
						'element' => 'slider',
						'value'   => 'yes'
					),
				),
				array(
					'param_name' => 'autoplay',
					'heading'    => esc_html__( 'Enable Auto play', 'claue' ),
					'group'      => esc_html__( 'Slider Settings', 'claue' ),
					'type'       => 'checkbox',
					'dependency' => array(
						'element' => 'slider',
						'value'   => 'yes'
					),
				),
				array(
					'param_name' => 'arrows',
					'heading'    => esc_html__( 'Enable Navigation', 'claue' ),
					'group'      => esc_html__( 'Slider Settings', 'claue' ),
					'type'       => 'checkbox',
					'dependency' => array(
						'element' => 'slider',
						'value'   => 'yes'
					),
				),
				array(
					'param_name' => 'dots',
					'heading'    => esc_html__( 'Enable Pagination', 'claue' ),
					'group'      => esc_html__( 'Slider Settings', 'claue' ),
					'type'       => 'checkbox',
					'dependency' => array(
						'element' => 'slider',
						'value'   => 'yes'
					),
				),
				array(
					'param_name'  => 'columns',
					'heading'     => esc_html__( 'Columns', 'claue' ),
					'description' => esc_html__( 'This parameter will not working if slider has enabled', 'claue' ),
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( '2 columns', 'claue' ) => 6,
						esc_html__( '3 columns', 'claue' ) => 4,
						esc_html__( '4 columns', 'claue' ) => 3,
						esc_html__( '6 columns', 'claue' ) => 2,
					),
					'std' => 3
				),
				array(
					'param_name'  => 'filter',
					'heading'     => esc_html__( 'Enable Isotope Category Filter', 'claue' ),
					'type' 	      => 'checkbox',
					'dependency' => array(
						'element' => 'style',
						'value'   => 'masonry'
					),
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
add_action( 'vc_before_init', 'jas_claue_vc_map_products' );