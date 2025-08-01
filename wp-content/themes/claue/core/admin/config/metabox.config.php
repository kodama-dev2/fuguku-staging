<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// METABOX OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$options = array();

if ( isset( $_GET['post'] ) && $_GET['post'] == get_option( 'page_for_posts' ) ) return;

// -----------------------------------------
// Page Metabox Options                    -
// -----------------------------------------
$options[] = array(
	'id'        => '_custom_page_options',
	'title'     => esc_html__( 'Page Layout Options','claue'),
	'post_type' => 'page',
	'context'   => 'normal',
	'priority'  => 'high',
	'sections'  => array(
		array(
			'name'  => 's1',
			'fields' => array(
				array(
					'id'      => 'page-layout',
					'type'    => 'image_select',
					'title'   => esc_html__( 'Page Layout', 'claue' ),
					'radio'   => true,
					'options' => array(
						'left-sidebar'  => CS_URI . '/assets/images/layout/left-sidebar.png',
						'no-sidebar'    => CS_URI . '/assets/images/layout/no-sidebar.png',
						'right-sidebar' => CS_URI . '/assets/images/layout/right-sidebar.png',
					),
					'default' => 'no-sidebar',
				),
				array(
					'id'         => 'page-sidebar',
					'type'       => 'select',
					'title'      => esc_html__( 'Select Sidebar', 'claue' ),
					'options'    => jas_claue_get_sidebars(),
					'dependency' => array( 'page-layout_no-sidebar', '==', false ),
					'default'    => 'primary-sidebar'
				),
				array(
					'id'      => 'pagehead',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Enable page title', 'claue' ),
					'default' => true
				),
				array(
					'id'         => 'subtitle',
					'type'       => 'switcher',
					'title'      => esc_html__( 'Enable sub-title', 'claue' ),
					'default'    => true,
					'dependency' => array( 'pagehead', '==', 'true' ),
				),
				array(
					'id'         => 'title',
					'type'       => 'text',
					'title'   		=> esc_html__( 'Sub Title', 'claue' ),
					'attributes' => array(
						'placeholder' => esc_html__( 'Do Stuff', 'claue' )
					),
					'dependency' => array( 'pagehead|subtitle', '==|==', 'true|true' ),
				),
				array(
					'id'      => 'breadcrumb',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Enable breadcrumb', 'claue' ),
					'default' => false
				),
			),
		),
	),
);

// -----------------------------------------
// Post Metabox Options                    -
// -----------------------------------------

// -----------------------------------------
// Product Metabox Options                    -
// -----------------------------------------
$options[] = array(
	'id'        => '_custom_wc_options',
	'title'     => esc_html__( 'Product Detail Layout Options', 'claue'),
	'post_type' => 'product',
	'context'   => 'normal',
	'priority'  => 'high',
	'sections'  => array(
		array(
			'name'  => 's2',
			'fields' => array(
				array(
					'id'    => 'wc-single-style',
					'type'  => 'image_select',
					'title' => esc_html__( 'Product Detail Style', 'claue' ),
					'info'  => sprintf( __( 'Change layout for only this product. You can setup global for all product page layout at <a target="_blank" href="%1$s">here</a> (WooCommerce section).', 'claue' ), esc_url( admin_url( 'admin.php?page=jas-theme-options' ) ) ),
					'options' => array(
						'1' => CS_URI . '/assets/images/layout/product-1.png',
						'2' => CS_URI . '/assets/images/layout/product-2.png',
						'3' => CS_URI . '/assets/images/layout/product-3.png',
						'4' => CS_URI . '/assets/images/layout/product-4.png',
					),
				),
				array(
					'id'      => 'wc-thumbnail-position',
					'type'    => 'select',
					'title'   => esc_html__( 'Thumbnail Position', 'claue' ),
					'options' => array(
						'left'    => esc_html__( 'Left', 'claue' ),
						'bottom'  => esc_html__( 'Bottom', 'claue' ),
						'right'   => esc_html__( 'Right', 'claue' ),
						'outside' => esc_html__( 'Outside', 'claue' )
					),
					'default'    => 'left',
					'dependency' => array( 'wc-single-style_1', '==', true ),
				),
				array(
					'title'	  => esc_html__( 'Video Thumbnail', 'claue'),
					'info'    => esc_html__( 'Support Vimeo & Youtube', 'claue'),
					'id'      => 'wc-single-video',
					'type'    => 'select',
					'options' => array(
						'url'    => esc_html__( 'Url', 'claue' ),
						'upload' => esc_html__( 'Self Hosted', 'claue' )					),
					'default' => 'url',
				),
				array(
					'id'         => 'wc-single-video-upload',
					'type'       => 'upload',
					'title'      => esc_html__( 'Upload Video Thumbnail', 'claue' ),
					'dependency' => array( 'wc-single-video', '==', 'upload' ),
					'settings'   => array(
						'upload_type'  => 'video',
						'button_title' => esc_html__( 'Video', 'claue' ),
						'frame_title'  => esc_html__( 'Select a video', 'claue' ),
						'insert_title' => esc_html__( 'Use this video', 'claue' ),
					),
				),
				array(
					'id'         => 'wc-single-video-url',
					'type'       => 'text',
					'title'      => esc_html__( 'Video Thumbnail Link', 'claue' ),
					'dependency' => array( 'wc-single-video', '==', 'url' ),
				),
				array(
					'title'   => esc_html__( 'New Arrival Product', 'claue'),
					'id'      => 'wc-single-new-arrival',
					'type'    => 'number',
					'default' => 5,
					'info'    => esc_html__( 'Set number of days display new arrivals badge for product.', 'claue' ),
				),
				array(
					'title' => esc_html__( 'Size Guide Image','claue'),
					'id'    => 'wc-single-size-guide',
					'type'  => 'upload',
					'info'  => sprintf( __( 'Upload size guide image for only this product. You can use image size guide for all product at <a target="_blank" href="%1$s">here</a> (WooCommerce section).', 'claue' ), esc_url( admin_url( 'admin.php?page=jas-theme-options' ) ) ),
				),
			),
		),
	),
);
$options[] = array(
	'id'        => '_custom_wc_thumb_options',
	'title'     => esc_html__( 'Thumbnail Size', 'claue'),
	'post_type' => 'product',
	'context'   => 'side',
	'priority'  => 'default',
	'sections'  => array(
		array(
			'name'  => 's3',
			'fields' => array(
				array(
					'id'      => 'wc-thumbnail-size',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Enable Large Thumbnail', 'claue' ),
					'desc'    => esc_html__( 'Apply for Product Layout Metro only', 'claue' ),
					'default' => false
				),
			),
		),
	),
	);

CSFramework_Metabox::instance( $options );
