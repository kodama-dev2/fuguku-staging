<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// TAXONOMY OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$options     = array();

// -----------------------------------------
// Taxonomy Options                        -
// -----------------------------------------
$options[]   = array(
	'id'       => '_custom_product_cat_options',
	'taxonomy' => 'product_cat',
	'fields'   => array(
		array(
			'id'      => 'product-cat-layout',
			'type'    => 'image_select',
			'title'   => esc_html__( 'Product Category Layout', 'claue' ),
			'options' => array(
				'left-sidebar'  => CS_URI . '/assets/images/layout/left-sidebar.png',
				'no-sidebar'    => CS_URI . '/assets/images/layout/no-sidebar.png',
				'right-sidebar' => CS_URI . '/assets/images/layout/right-sidebar.png',
			),
		),
		array(
			'id'         => 'product-cat-sidebar',
			'type'       => 'select',
			'title'      => esc_html__( 'Select Sidebar', 'claue' ),
			'options'    => jas_claue_get_sidebars(),
			'dependency' => array( 'product-cat-layout_no-sidebar', '==', false ),
		),
	),
);
CSFramework_Taxonomy::instance( $options );
