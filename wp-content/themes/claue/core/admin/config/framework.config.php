<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// FRAMEWORK SETTINGS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$type = 'add_submenu';
$settings = array(
	'menu_title'     => esc_html__( 'Theme Options', 'claue' ),
	'menu_parent'    => 'jas',
	'menu_type'      => $type . '_page',
	'menu_slug'      => 'jas-theme-options',
	'show_reset_all' => true,
	'ajax_save'      => true
);

// Get list all menu
$menus = wp_get_nav_menus();
$menu  = array();
if ( $menus ) {
	foreach ( $menus as $key => $value ) {
		$menu[$value->term_id] = $value->name;
	}
}

// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// FRAMEWORK OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$options = array();

// ----------------------------------------
// a option section for options layout    -
// ----------------------------------------
$options[] = array(
	'name'  => 'layout',
	'title' => esc_html__( 'General Layout', 'claue' ),
	'icon'  => 'fa fa-building-o',
	'fields' => array(
		array(
			'id'      => 'content-width',
			'type'    => 'text',
			'title'   => esc_html__( 'Content Width', 'claue' ),
			'desc'    => esc_html__( 'Set the maximum allowed width for content, include unit. Example for: px or %', 'claue' ),
			'default' => '1170px'
		),
		array(
			'id'    => 'boxed',
			'type'  => 'switcher',
			'title' => esc_html__( 'Enable Boxed Layout', 'claue' ),
		),
		array(
			'id'         => 'boxed-bg',
			'type'       => 'background',
			'title'      => esc_html__( 'Background', 'claue' ),
			'dependency' => array( 'boxed', '==', 'true' ),
		),
		array(
			'id'    => 'preloader',
			'type'  => 'switcher',
			'title' => esc_html__( 'Enable Preloader', 'claue' ),
		),
		array(
			'id'      => 'preloader-type',
			'type'    => 'select',
			'title'   => esc_html__( 'Animation type', 'claue' ),
			'options' => array(
				'css' => esc_html__( 'CSS', 'claue' ),
				'img' => esc_html__( 'Image', 'claue' )
			),
			'dependency' => array( 'preloader', '==', true ),
		),
		array(
			'id'         => 'preloader-img',
			'type'       => 'image',
			'title'      => esc_html__( 'Preloader Image', 'claue' ),
			'add_title'  => esc_html__( 'Upload Your Image', 'claue' ),
			'dependency' => array( 'preloader|preloader-type', '==', 'true|img' ),
		),
		array(
			'id'       => 'custom-css',
			'type'     => 'textarea',
			'title'    => esc_html__( 'Custom CSS Style', 'claue' ),
			'desc'     => esc_html__( 'Paste your CSS code here. Do not place any &lt;style&gt; tags in these areas as they are already added for your convenience', 'claue' ),
			'sanitize' => 'html'
		),
		array(
			'id'       => 'custom-js',
			'type'     => 'textarea',
			'title'    => esc_html__( 'Custom JS Code', 'claue' ),
			'desc'     => esc_html__( 'Paste your Javscript code here. You can add your Google Analytics Code here. Do not place any &lt;script&gt; tags in these areas as they are already added for your convenience.', 'claue' ),
			'sanitize' => 'html'
		),
	),
);


// ----------------------------------------
// a option section for options header    -
// ----------------------------------------
$options[] = array(
	'name'  => 'header',
	'title' => esc_html__( 'Header', 'claue' ),
	'icon'  => 'fa fa-home',
	'fields' => array(
		array(
			'id'    => 'header-layout',
			'type'  => 'image_select',
			'title' => esc_html__( 'Layout', 'claue' ),
			'radio' => true,
			'options' => array(
				'1' => CS_URI . '/assets/images/layout/header-1.png',
				'2' => CS_URI . '/assets/images/layout/header-2.png',
				'3' => CS_URI . '/assets/images/layout/header-3.png',
				'4' => CS_URI . '/assets/images/layout/header-4.png',
				'5' => CS_URI . '/assets/images/layout/header-5.png',
			),
			'default'    => '3',
			'attributes' => array(
				'data-depend-id' => 'header-layout',
			),
		),
		array(
			'id'         => 'header-bg',
			'type'       => 'background',
			'title'      => esc_html__( 'Background', 'claue' ),
			'dependency' => array( 'header-layout', 'any', 5 ),
		),
		array(
			'id'         => 'header-sticky',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable Header Sticky', 'claue' ),
			'default'    => false,
			'dependency' => array( 'header-layout', '!=', 5 ),
		),
		array(
			'id'         => 'header-transparent',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable Header Transparent', 'claue' ),
			'default'    => false,
			'dependency' => array( 'header-layout', '!=', 5 ),
		),
		array(
			'id'         => 'header-boxed',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable Header Boxed', 'claue' ),
			'default'    => false,
			'dependency' => array( 'header-layout', '!=', 5 ),
		),
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Logo Settings', 'claue' ),
		),
		array(
			'id'        => 'logo',
			'type'      => 'image',
			'title'     => esc_html__( 'Logo', 'claue' ),
			'add_title' => esc_html__( 'Upload', 'claue' ),
			'dependency' => array( 'header-transparent', '==', 'false' ),
		),
		array(
			'id'        => 'logo-retina',
			'type'      => 'image',
			'title'     => esc_html__( 'Logo Retina', 'claue' ),
			'desc'      => esc_html__( 'Upload logo for retina devices, mobile devices', 'claue' ),
			'add_title' => esc_html__( 'Upload', 'claue' ),
			'dependency' => array( 'header-transparent', '==', 'false' ),
		),
		array(
			'id'         => 'logo-transparent',
			'type'       => 'image',
			'title'      => esc_html__( 'Transparent Header Logo', 'claue' ),
			'desc'       => esc_html__( 'Add logo for transparent header layout', 'claue' ),
			'add_title'  => esc_html__( 'Upload', 'claue' ),
			'dependency' => array( 'header-transparent', '==', 'true' ),
		),
		array(
			'id'         => 'logo-transparent-retina',
			'type'       => 'image',
			'title'      => esc_html__( 'Transparent Header Logo Retina', 'claue' ),
			'desc'       => esc_html__( 'Add logo for transparent header layout for retina devices, mobile devices', 'claue' ),
			'add_title'  => esc_html__( 'Upload', 'claue' ),
			'dependency' => array( 'header-transparent', '==', 'true' ),
		),
		array(
			'id'         => 'logo-sticky',
			'type'       => 'image',
			'title'      => esc_html__( 'Sticky Header Logo', 'claue' ),
			'desc'       => esc_html__( 'Add logo for sticky header. It work when you upload regular logo.', 'claue' ),
			'add_title'  => esc_html__( 'Upload', 'claue' ),
			'dependency' => array( 'header-sticky', '==', 'true' ),
		),
		array(
			'id'      => 'logo-max-width',
			'type'    => 'text',
			'title'   => esc_html__( 'Logo Max Width', 'claue' ),
			'default' => 200,
			'desc'    => esc_html__( 'Defined in pixels. Do not add the \'px\' unit.', 'claue' ),
		),
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Header Top Settings', 'claue' ),
		),
		array(
			'id'         => 'header-top-left',
			'type'       => 'textarea',
			'title'      => esc_html__( 'Header left content', 'claue' ),
			'desc'       => esc_html__( 'HTML, shortcode is allowed', 'claue' ),
			'dependency' => array( 'header-layout', 'any', '1,2,3,4,5' ),
		),
		array(
			'id'         => 'header-top-center',
			'type'       => 'textarea',
			'title'      => esc_html__( 'Center promo text', 'claue' ),
			'desc'       => esc_html__( 'HTML, shortcode is allowed', 'claue' ),
			'dependency' => array( 'header-layout', 'any', '1,2,3,4,5' ),
		),
		array(
			'id'         => 'header-top-right',
			'type'       => 'textarea',
			'title'      => esc_html__( 'Header right content', 'claue' ),
			'desc'       => esc_html__( 'HTML, shortcode is allowed', 'claue' ),
			'dependency' => array( 'header-layout', 'any', '1,2,3,4,5' ),
		),
		array(
			'id'    => 'header-currency',
			'type'  => 'switcher',
			'title' => esc_html__( 'Enable currency', 'claue' ),
			'default' => false,
		),
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Header main settings', 'claue' ),
		),
		array(
			'id'      => 'header-search-icon',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Enable search icon', 'claue' ),
			'default' => true,
		),
		array(
			'id'      => 'header-my-account-icon',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Enable my account icon', 'claue' ),
			'default' => true,
		),
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Mobile settings', 'claue' ),
		),
		array(
			'id'        => 'mobile-icon',
			'type'      => 'image',
			'title'     => esc_html__( 'Mobile icon', 'claue' ),
			'add_title' => esc_html__( 'Upload', 'claue' )
		),
	),
);

// ----------------------------------------
// a option section for options footer    -
// ----------------------------------------
$options[] = array(
	'name'  => 'footer',
	'title' => esc_html__( 'Footer', 'claue' ),
	'icon'  => 'fa fa-copyright',
	'fields' => array(
		array(
			'id'    => 'footer-layout',
			'type'  => 'image_select',
			'title' => esc_html__( 'Layout', 'claue' ),
			'options' => array(
				'1' => CS_URI . '/assets/images/layout/footer-5cols.png',
				'2' => CS_URI . '/assets/images/layout/footer-4cols.png',
				'3' => CS_URI . '/assets/images/layout/footer-3cols.png',
				'4' => CS_URI . '/assets/images/layout/footer-2cols.png',
				'5' => CS_URI . '/assets/images/layout/footer-1col.png'
			),
			'default' => '1'
		),
		array(
			'id'      => 'footer-copyright',
			'type'    => 'textarea',
			'title'   => esc_html__( 'Copyright Text', 'claue' ),
			'desc'    => esc_html__( 'HTML is allowed', 'claue' ),
			'default' => sprintf( wp_kses_post( 'Copyright 2017 <span class="cp">Claue</span> all rights reserved. Powered by <a href="%s">JanStudio</a>', 'claue' ), esc_url( home_url() ) )
		),
	),
);

// ----------------------------------------
// a option section for options woocommerce-
// ----------------------------------------
if ( class_exists( 'WooCommerce' ) ) {
	$attributes = array();
	$attributes_tax = wc_get_attribute_taxonomies();
	foreach ( $attributes_tax as $attribute ) {
		$attributes[ $attribute->attribute_name ] = $attribute->attribute_label;
	}
	$options[]  = array(
		'name'  => 'woocommerce',
		'title' => esc_html__( 'WooCommerce', 'claue' ),
		'icon'  => 'fa fa-shopping-basket',
		'sections' => array(

			// General Setting
			array(
				'name'   => 'wc_general_setting',
				'title'  => esc_html__( 'General Settings', 'claue' ),
				'icon'   => 'fa fa-minus',
				'fields' => array(
					array(
						'type'    => 'heading',
						'content' => esc_html__( 'General Setting', 'claue' ),
					),
					array(
						'id'      => 'wc-enable-page-title',
						'type'    => 'switcher',
						'title'   => esc_html__( 'Enable Page Title', 'claue' ),
						'default' => true,
					),
					array(
						'id'         => 'wc-page-title',
						'type'       => 'text',
						'title'      => esc_html__( 'Page Title', 'claue' ),
						'default'    => esc_html__( 'claue' , 'claue' ),
						'dependency' => array( 'wc-enable-page-title', '==', true ),
					),
					array(
						'id'         => 'wc-page-desc',
						'type'       => 'textarea',
						'title'      => esc_html__( 'Description', 'claue' ),
						'default'    => esc_html__( 'Online fashion, furniture, handmade... store', 'claue' ),
						'dependency' => array( 'wc-enable-page-title', '==', true ),
					),
					array(
						'id'         => 'wc-pagehead-bg',
						'type'       => 'background',
						'title'      => esc_html__( 'Page Title Background', 'claue' ),
						'dependency' => array( 'wc-enable-page-title', '==', true ),
					),
					array(
						'id'      => 'wc-catalog',
						'type'    => 'switcher',
						'title'   => esc_html__( 'Enable Catalog Mode', 'claue' ),
						'default' => false,
					),
					array(
						'id'    => 'wc-badge-type',
						'type'  => 'select',
						'title' => esc_html__( 'Sale Badge Type', 'claue' ),
						'desc'  => esc_html__( 'Apply for product simple only', 'claue' ),
						'options' => array(
							'text'    => esc_html__( 'Text', 'claue' ),
							'percent' => esc_html__( 'Discount percent', 'claue' ),
						),
						'default' => 'text'
					)
				)
			),

			// Sub category layout
			array(
				'name'   => 'wc_sub_cat_setting',
				'title'  => esc_html__( 'Sub Category Settings', 'claue' ),
				'icon'   => 'fa fa-minus',
				'fields' => array(
					array(
						'type'    => 'heading',
						'content' => esc_html__( 'Sub Category', 'claue' ),
					),
					array(
						'id'    => 'wc-sub-cat-layout',
						'type'  =>'image_select',
						'title' => esc_html__( 'Sub-Categories Layout', 'claue' ),
						'desc'  => esc_html__( 'Display sub-categories as grid or masonry', 'claue' ),
						'radio' => true,
						'options' => array(
							'grid'    => CS_URI . '/assets/images/layout/grid.png',
							'masonry' => CS_URI . '/assets/images/layout/masonry.png'
						),
						'default' => 'masonry'
					),
					array(
						'id'    => 'wc-sub-cat-column',
						'type'  =>'image_select',
						'title' => esc_html__( 'Columns', 'claue' ),
						'desc'  => esc_html__( 'Display number of sub-category per row', 'claue' ),
						'radio' => true,
						'options' => array(
							'6' => CS_URI . '/assets/images/layout/2cols.png',
							'4' => CS_URI . '/assets/images/layout/3cols.png',
							'3' => CS_URI . '/assets/images/layout/4cols.png',
							'2' => CS_URI . '/assets/images/layout/6cols.png',
						),
						'default' => '3'
					)
				)
			),

			// Product Listing Setting
			array(
				'name'   => 'wc_list_setting',
				'title'  => esc_html__( 'Product Listing Settings', 'claue' ),
				'icon'   => 'fa fa-minus',
				'fields' => array(
					array(
						'type'    => 'heading',
						'content' => esc_html__( 'Product Listing', 'claue' ),
					),
					array(
						'id'      => 'wc-layout-full',
						'type'    => 'switcher',
						'title'   => esc_html__( 'Enable Full-Width', 'claue' ),
						'default' => false,
					),
					array(
						'id'    => 'wc-style',
						'type'  => 'image_select',
						'title' => esc_html__( 'Style', 'claue' ),
						'desc'  => esc_html__( 'Display product listing as grid or masonry or metro', 'claue' ),
						'radio' => true,
						'options' => array(
							'grid'    => CS_URI . '/assets/images/layout/grid.png',
							'masonry' => CS_URI . '/assets/images/layout/masonry.png',
							'metro'   => CS_URI . '/assets/images/layout/metro.png'
						),
						'default' => 'grid',
					),
					array(
						'id'         => 'wc-pagination',
						'type'       => 'select',
						'title'      => esc_html__( 'Pagination Style', 'claue' ),
						'options' => array(
							'number'   => esc_html__( 'Number', 'claue' ),
							'loadmore' => esc_html__( 'Load More', 'claue' ),
						),
						'default' => 'number'
					),
					array(
						'id'         => 'wc-scroll',
						'type'       => 'switcher',
						'title'      => esc_html__( 'Enable Infinite Scroll', 'claue' ),
						'dependency' => array( 'wc-pagination', '==', 'loadmore' ),
						'default'	 => false,
					),
					array(
						'id'      => 'wc-flip-thumb',
						'type'    => 'switcher',
						'title'   => esc_html__( 'Flip Product Thumbnail', 'claue' ),
						'default' => false,
					),
					array(
						'id'      => 'wc-atc-on-product-list',
						'type'    => 'switcher',
						'title'   => esc_html__( 'Enable add to cart button', 'claue' ),
						'default' => false,
					),
					array(
						'id'      => 'wc-quick-view-btn',
						'type'    => 'switcher',
						'title'   => esc_html__( 'Enable quick shop button', 'claue' ),
						'default' => true,
					),
					array(
						'id'    => 'wc-column',
						'type'  =>'image_select',
						'title' => esc_html__( 'Number Of Column', 'claue' ),
						'desc'  => esc_html__( 'Display number of product per row', 'claue' ),
						'radio' => true,
						'options' => array(
							'6' => CS_URI . '/assets/images/layout/2cols.png',
							'4' => CS_URI . '/assets/images/layout/3cols.png',
							'3' => CS_URI . '/assets/images/layout/4cols.png',
							'2' => CS_URI . '/assets/images/layout/6cols.png',
						),
						'default' => '4'
					),
					array(
						'id'      => 'wc-number-per-page',
						'type'    => 'number',
						'title'   => esc_html__( 'Per Page', 'claue' ),
						'desc'    => esc_html__( 'How much items per page to show (-1 to show all products)', 'claue' ),
						'default' => '12',
					),
					array(
						'id'    => 'wc-layout',
						'type'  => 'image_select',
						'title' => esc_html__( 'Layout', 'claue' ),
						'radio' => true,
						'options' => array(
							'left-sidebar'  => CS_URI . '/assets/images/layout/left-sidebar.png',
							'no-sidebar'    => CS_URI . '/assets/images/layout/no-sidebar.png',
							'right-sidebar' => CS_URI . '/assets/images/layout/right-sidebar.png',
						),
						'default' => 'no-sidebar'
					),
					array(
						'id'         => 'wc-sidebar',
						'type'       => 'select',
						'title'      => esc_html__( 'Select Sidebar', 'claue' ),
						'options'    => jas_claue_get_sidebars(),
						'dependency' => array( 'wc-layout_no-sidebar', '==', false ),
					),
					array(
						'id'      => 'wc-sticky-sidebar',
						'type'    => 'switcher',
						'title'   => esc_html__( 'Enable Sticky Sidebar', 'claue' ),
						'desc'    => esc_html__( 'When enable this option the sidebar will sticky when you scoll down browser', 'claue' ),
						'default' => false
					),
					array(
						'id'      => 'wc-sidebar-filter',
						'type'    => 'switcher',
						'title'   => esc_html__( 'Enable Sidebar Filter', 'claue' ),
						'default' => false,
						'desc'    => sprintf( wp_kses_post( 'Edit content in sidebar <a target="_blank" href="%s">WooCommerce Filter Sidebar</a>', 'claue' ), esc_url( admin_url( 'widgets.php' ) ) )
					),
					array(
						'id'         => 'wc-sidebar-filter-position',
						'type'       => 'select',
						'title'      => esc_html__( 'Sidebar Filter Position', 'claue' ),
						'options' => array(
							'left'  => esc_html__( 'Left', 'claue' ),
							'right' => esc_html__( 'Right', 'claue' ),
						),
						'default'    => 'left',
						'dependency' => array( 'wc-sidebar-filter', '==', true ),
					),
					array(
						'id'      => 'wc-col-switch',
						'type'    => 'switcher',
						'title'   => esc_html__( 'Enable Column Switcher', 'claue' ),
						'default' => true
					),
					array(
						'id'      => 'wc-attr',
						'type'    => 'checkbox',
						'title'   => esc_html__( 'Enable Products Attribute On Product List', 'claue' ),
						'options' => $attributes,
					),
					array(
						'id'      => 'wc-attr-background',
						'type'    => 'color_picker',
						'title'   => esc_html__( 'Products Attribute Background Color when hover', 'claue' ),
						'default' => 'transparent',
					),
				)
			),
			// Product Detail Setting
			array(
				'name'   => 'wc_detail_setting',
				'title'  => esc_html__( 'Product Detail Settings', 'claue' ),
				'icon'   => 'fa fa-minus',
				'fields' => array(
					array(
						'type'    => 'heading',
						'content' => esc_html__( 'Product detail', 'claue' ),
					),
					array(
						'id'      => 'wc-detail-full',
						'type'    => 'switcher',
						'title'   => esc_html__( 'Enable full width', 'claue' ),
						'default' => false,
					),
					array(
						'id'      => 'wc-single-style',
						'type'    => 'image_select',
						'title'   => esc_html__( 'Product detail style', 'claue' ),
						'radio'   => true,
						'options' => array(
							'1' => CS_URI . '/assets/images/layout/product-1.png',
							'2' => CS_URI . '/assets/images/layout/product-2.png',
							'3' => CS_URI . '/assets/images/layout/product-3.png',
							'4' => CS_URI . '/assets/images/layout/product-4.png',
						),
						'default' => '1'
					),
					array(
						'id'         => 'wc-thumbnail-position',
						'type'       => 'select',
						'title'      => esc_html__( 'Thumbnail position', 'claue' ),
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
						'type'    => 'subheading',
						'content' => esc_html__( 'Extra infomation', 'claue' ),
					),
					array(
						'id'      => 'wc-extra-layout',
						'type'    => 'select',
						'title'   => esc_html__( 'Layout', 'claue' ),
						'options' => array(
							'tab'       => esc_html__( 'Tab', 'claue' ),
							'accordion' => esc_html__( 'Accordion', 'claue' ),
						),
					),
					array(
						'id'      => 'wc-extra-position',
						'type'    => 'select',
						'title'   => esc_html__( 'Position', 'claue' ),
						'options' => array(
							'1' => esc_html__( 'Above extra products', 'claue' ),
							'2' => esc_html__( 'Below product description', 'claue' ),
						),
						'dependency' => array( 'wc-single-style_3', '==', false ),
					),
					
					array(
						'type'    => 'subheading',
						'content' => esc_html__('Product Detail Sidebar','claue'),
					),
					array(
						'id' 	  => 'product-detail-layout',
						'type'    => 'image_select',
						'title'   =>  esc_html__('Layout','claue'),
						'desc'    =>  esc_html__('Apply for product detail layout 1-2-4','claue'),
						'radio'   => true,
						'options' => array(
							'left-sidebar'  => CS_URI . '/assets/images/layout/left-sidebar.png',
							'no-sidebar'    => CS_URI . '/assets/images/layout/no-sidebar.png',
							'right-sidebar' => CS_URI . '/assets/images/layout/right-sidebar.png',
						),
						'default'    => 'no-sidebar',

					),
					array(
						'id'         => 'product-detail-sidebar',
						'type'       => 'select',
						'title'      => esc_html__( 'Select Sidebar', 'claue' ),
						'options'    => jas_claue_get_sidebars(),
						'dependency' => array( 'product-detail-layout_no-sidebar', '==', false ),
					),

					array(
						'type'    => 'subheading',
						'content' => esc_html__( 'Miscellaneous', 'claue' ),
					),
					array(
						'id'         => 'wc-atc-behavior',
						'type'       => 'select',
						'title'      => esc_html__( 'Add to cart behavior', 'claue' ),
						'options' => array(
							'slide' => esc_html__( 'Slide sidebar', 'claue' ),
							'popup' => esc_html__( 'Popup included upsell products', 'claue' ),
						),
						'default' => 'slide'
					),
					array(
						'id'      => 'wc-sticky-atc',
						'type'    => 'switcher',
						'title'   => esc_html__( 'Enable sticky add to cart', 'claue' ),
						'default' => false
					),
					array(
						'id'      => 'wc-single-zoom',
						'type'    => 'switcher',
						'title'   => esc_html__( 'Enable zoom image', 'claue' ),
						'default' => false,
					),
					array(
						'id'      => 'single-countdown-sale',
						'type'    => 'switcher',
						'title'   => esc_html__( 'Enable countdown sale', 'claue' ),
						'default' => false,
					),
					array(
						'title' => esc_html__( 'Extra Content','claue'),
						'id'    => 'wc-extra-content',
						'type'  => 'textarea',
						'desc'  => esc_html__( 'This text will be displayed right below add to cart button, HTML allowed.', 'claue' )
					),
					array(
						'id'    => 'wc-single-size-guide',
						'title' => esc_html__( 'Size guide image', 'claue' ),
						'type'  => 'upload',
					),
					array(
						'id'    => 'wc-single-shipping-return',
						'title' => esc_html__( 'Shipping & Return content', 'claue' ),
						'type'  => 'textarea',
						'desc'  => esc_html__( 'HTML is allowed', 'claue' ),
					),
					array(
						'type'    => 'subheading',
						'content' => esc_html__( 'Other products', 'claue' ),
					),
					array(
						'id'    => 'wc-other-product-show',
						'type'  => 'slider',
						'title' => esc_html__( 'Number of product want to show on a slide', 'claue' ),
						'desc'  => esc_html__( 'Apply for related products and up-sell products', 'claue' ),
						'choices' => array(
							'min'  => 2,
							'max'  => 6,
							'step' => 1,
							'unit' => '',
						),
						'default' => 4
					),
					array(
						'id'      => 'wc-other-product-limit',
						'type'    => 'number',
						'title'   => esc_html__( 'Number of product want to show', 'claue' ),
						'desc'    => esc_html__( 'Type number only, the number should be equal or bigger than number of product per slider', 'claue' ),
						'default' => '6',
					),
				)
			),
			// Cart page
			array(
				'name'   => 'wc_cart_checkout_setting',
				'title'  => esc_html__( 'Cart & Checkout Settings', 'claue' ),
				'icon'   => 'fa fa-minus',
				'fields' => array(
					array(
						'type'    => 'heading',
						'content' => esc_html__( 'Mini cart Setting', 'claue' ),
					),
					array(
						'id'    => 'wc-minicart-content',
						'title' => esc_html__( 'MiniCart Content', 'claue' ),
						'type'  => 'textarea',
						'desc'  => esc_html__( 'This text will be displayed right below checkout button, HTML allowed.', 'claue' )
					),
					array(
						'type'    => 'heading',
						'content' => esc_html__( 'Cart Setting', 'claue' ),
					),
					array(
						'id'    => 'wc-cart-content',
						'title' => esc_html__( 'Cart Content', 'claue' ),
						'type'  => 'textarea',
						'desc'  => esc_html__( 'This text will be displayed right below cart total, HTML allowed.', 'claue' )
					),
					array(
						'type'    => 'heading',
						'content' => esc_html__( 'Checkout Setting', 'claue' ),
					),
					array(
						'id'    => 'wc-checkout-content',
						'title' => esc_html__( 'Checkout Content', 'claue' ),
						'type'  => 'textarea',
						'desc'  => esc_html__( 'This text will be displayed right below checkout button, HTML allowed.', 'claue' )
					),
				)
			),
		),
	);
}

// ----------------------------------------
// a option section for options blog      -
// ----------------------------------------
$options[] = array(
	'name'  => 'blog',
	'title' => esc_html__( 'Blog', 'claue' ),
	'icon'  => 'fa fa-file-text-o',
	'fields' => array(
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Blog listing layout', 'claue' ),
		),
		array(
			'id'      => 'blog-latest-slider',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Enable latest post slider', 'claue' ),
			'default' => false,
		),
		array(
			'id'      => 'blog-content-display',
			'type'    => 'select',
			'title'   => esc_html__( 'Content display on blog list', 'claue' ),
			'options' => array(
				'content' => esc_html__( 'Content', 'claue' ),
				'excerpt' => esc_html__( 'Excerpt', 'claue' ),
			),
		),
		array(
			'id'    => 'blog-style',
			'type'  => 'image_select',
			'title' => esc_html__( 'Style', 'claue' ),
			'radio' => true,
			'options' => array(
				'grid'    => CS_URI . '/assets/images/layout/right-sidebar.png',
				'masonry' => CS_URI . '/assets/images/layout/masonry.png',
			),
			'default' => 'masonry'
		),
		array(
			'id'    => 'blog-masonry-column',
			'type'  =>'image_select',
			'title' => esc_html__( 'Columns', 'claue' ),
			'desc'  => esc_html__( 'Display number of post per row', 'claue' ),
			'radio' => true,
			'options' => array(
				'6' => CS_URI . '/assets/images/layout/2cols.png',
				'4' => CS_URI . '/assets/images/layout/3cols.png',
			),
			'default'    => '6',
			'dependency' => array( 'blog-style_masonry', '==', true ),
		),
		array(
			'id'    => 'blog-layout',
			'type'  => 'image_select',
			'title' => esc_html__( 'Layout', 'claue' ),
			'radio' => true,
			'options' => array(
				'left-sidebar'  => CS_URI . '/assets/images/layout/left-sidebar.png',
				'no-sidebar'    => CS_URI . '/assets/images/layout/no-sidebar.png',
				'right-sidebar' => CS_URI . '/assets/images/layout/right-sidebar.png',
			),
			'default'    => 'right-sidebar',
		),
		array(
			'id'         => 'blog-sidebar',
			'type'       => 'select',
			'title'      => esc_html__( 'Select Sidebar', 'claue' ),
			'options'    => jas_claue_get_sidebars(),
			'dependency' => array( 'blog-layout_no-sidebar', '==', false ),
		),
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Blog detail layout', 'claue' ),
		),
		array(
			'id'    => 'blog-detail-layout',
			'type'  => 'image_select',
			'title' => esc_html__( 'Layout', 'claue' ),
			'radio' => true,
			'options' => array(
				'left-sidebar'  => CS_URI . '/assets/images/layout/left-sidebar.png',
				'no-sidebar'    => CS_URI . '/assets/images/layout/no-sidebar.png',
				'right-sidebar' => CS_URI . '/assets/images/layout/right-sidebar.png',
			),
			'default'    => 'right-sidebar',
		),
		array(
			'id'         => 'blog-detail-sidebar',
			'type'       => 'select',
			'title'      => esc_html__( 'Select Sidebar', 'claue' ),
			'options'    => jas_claue_get_sidebars(),
			'dependency' => array( 'blog-detail-layout_no-sidebar', '==', false ),
		),
	),
);

// ----------------------------------------
// a option section for options portfolio -
// ----------------------------------------
$options[] = array(
	'name'  => 'portfolio',
	'title' => esc_html__( 'Portfolio', 'claue' ),
	'icon'  => 'fa fa-flask',
	'fields' => array(
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Portfolio Listing', 'claue' ),
		),
		array(
			'id'      => 'portfolio-page-title',
			'type'    => 'text',
			'title'   => esc_html__( 'Page Title', 'claue' ),
			'default' => esc_html__( 'Portfolio', 'claue' ),
		),
		array(
			'id'      => 'portfolio-sub-title',
			'type'    => 'text',
			'title'   => esc_html__( 'Sub Title', 'claue' ),
			'default' => esc_html__( 'See our recent projects', 'claue' ),
		),
		array(
			'id'    => 'portfolio-pagehead-bg',
			'type'  => 'background',
			'title' => esc_html__( 'Page Title Background', 'claue' ),
		),
		array(
			'id'    => 'portfolio-column',
			'type'  =>'image_select',
			'title' => esc_html__( 'Columns', 'claue' ),
			'desc'  => esc_html__( 'Display number of post per row', 'claue' ),
			'radio' => true,
			'options' => array(
				'6' => CS_URI . '/assets/images/layout/2cols.png',
				'4' => CS_URI . '/assets/images/layout/3cols.png',
				'3' => CS_URI . '/assets/images/layout/4cols.png',
			),
			'default' => 4,
		),
		array(
			'id'      => 'portfolio-number-per-page',
			'type'    => 'number',
			'title'   => esc_html__( 'Per Page', 'claue' ),
			'desc'    => esc_html__( 'How much items per page to show (-1 to show all portfolio)', 'claue' ),
			'default' => 10,
		),		
	),
);


// ----------------------------------------
// a option section for options typography-
// ----------------------------------------
$options[] = array(
	'name'  => 'typography',
	'title' => esc_html__( 'Typography', 'claue' ),
	'icon'  => 'fa fa-font',
	'fields' => array(
		array(
			'id'      => 'enable-google-font',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Enable Google font', 'claue' ),
			'default' => true,
		),
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Body Font Settings', 'claue' ),
		),
		array(
			'id'        => 'body-font',
			'type'      => 'typography',
			'title'     => esc_html__( 'Font Family', 'claue' ),
			'default'   => array(
				'family'  => 'Poppins',
				'font'    => 'google',
				'variant' => 'regular',
			),
		),
		array(
			'id'      => 'body-font-size',
			'type'    => 'number',
			'title'   => esc_html__( 'Font Size', 'claue' ),
			'after'   => ' <i class="cs-text-muted">px</i>',
			'default' => 14
		),
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Heading Font Settings', 'claue' ),
		),
		array(
			'id'        => 'heading-font',
			'type'      => 'typography',
			'title'     => esc_html__( 'Font Family', 'claue' ),
			'default'   => array(
				'family'  => 'Poppins',
				'font'    => 'google',
				'variant' => '600',
			),
		),
		array(
			'id'      => 'h1-font-size',
			'type'    => 'number',
			'title'   => esc_html__( 'H1 Font Size', 'claue' ),
			'after'   => ' <i class="cs-text-muted">px</i>',
			'default' => '48'
		),
		array(
			'id'      => 'h2-font-size',
			'type'    => 'number',
			'title'   => esc_html__( 'H2 Font Size', 'claue' ),
			'after'   => ' <i class="cs-text-muted">px</i>',
			'default' => '36'
		),
		array(
			'id'      => 'h3-font-size',
			'type'    => 'number',
			'title'   => esc_html__( 'H3 Font Size', 'claue' ),
			'after'   => ' <i class="cs-text-muted">px</i>',
			'default' => '24'
		),
		array(
			'id'      => 'h4-font-size',
			'type'    => 'number',
			'title'   => esc_html__( 'H4 Font Size', 'claue' ),
			'after'   => ' <i class="cs-text-muted">px</i>',
			'default' => '21'
		),
		array(
			'id'      => 'h5-font-size',
			'type'    => 'number',
			'title'   => esc_html__( 'H5 Font Size', 'claue' ),
			'after'   => ' <i class="cs-text-muted">px</i>',
			'default' => '18'
		),
		array(
			'id'      => 'h6-font-size',
			'type'    => 'number',
			'title'   => esc_html__( 'H6 Font Size', 'claue' ),
			'after'   => ' <i class="cs-text-muted">px</i>',
			'default' => '16'
		),
	),
);

// ------------------------------------------
// a option section for options color_scheme-
// ------------------------------------------
$options[] = array(
	'name'  => 'color_scheme',
	'title' => esc_html__( 'Color Scheme', 'claue' ),
	'icon'  => 'fa fa-paint-brush',
	'fields' => array(
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'General Color', 'claue' ),
		),
		array(
			'id'      => 'primary-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Primary Color', 'claue' ),
			'desc'    => esc_html__( 'Main Color Scheme', 'claue' ),
			'default' => '#56cfe1',
		),
		array(
			'id'      => 'secondary-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Secondary Color', 'claue' ),
			'desc'    => esc_html__( 'Secondary Color Scheme', 'claue' ),
			'default' => '#222',
		),
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Section Color', 'claue' ),
		),
		array(
			'id'      => 'body-background-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Body Background Color', 'claue' ),
			'default' => '#fff',
		),
		array(
			'id'      => 'body-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Body Color', 'claue' ),
			'default' => '#878787',
		),
		array(
			'id'      => 'heading-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Heading Color', 'claue' ),
			'default' => '#222',
		),
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Header Color', 'claue' ),
		),
		array(
			'id'    => 'header-background',
			'type'  => 'color_picker',
			'title' => esc_html__( 'Header Background Color', 'claue' ),
		),
		array(
			'id'    => 'header-top-background',
			'type'  => 'color_picker',
			'title' => esc_html__( 'Header Top Background Color', 'claue' ),
		),
		array(
			'id'    => 'header-top-color',
			'type'  => 'color_picker',
			'title' => esc_html__( 'Header Top Color', 'claue' ),
			'default' => '#878787',
		),
		array(
			'id'      => 'main-menu-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Main Menu Color', 'claue' ),
			'default' => '#222'
		),
		array(
			'id'      => 'main-menu-hover-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Main Menu Hover Color', 'claue' ),
			'default' => '#56cfe1'
		),
		array(
			'id'      => 'sub-menu-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Sub Menu Color', 'claue' ),
			'default' => '#878787'
		),
		array(
			'id'      => 'sub-menu-hover-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Sub Menu Hover Color', 'claue' ),
			'default' => '#222'
		),
		array(
			'id'      => 'sub-menu-background-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Sub Menu Background Color', 'claue' ),
			'default' => 'rgba(255, 255, 255, 0.95)'
		),
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Header Transparent Color', 'claue' ),
		),
		array(
			'id'      => 'transparent-main-menu-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Main Menu Color', 'claue' ),
			'default' => '#222'
		),
		array(
			'id'      => 'transparent-main-menu-hover-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Main Menu Hover Color', 'claue' ),
			'default' => '#56cfe1'
		),
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Header Sticky Color', 'claue' ),
		),
		array(
			'id'      => 'header-sticky-background',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Header Sticky Background', 'claue' ),
			'default' => '#fff'
		),
		array(
			'id'      => 'sticky-main-menu-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Header Sticky Main Menu Color', 'claue' ),
			'default' => '#222'
		),
		array(
			'id'      => 'sticky-main-menu-hover-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Header Sticky Main Menu Hover Color', 'claue' ),
			'default' => '#56cfe1'
		),
		array(
			'id'      => 'sticky-sub-menu-background-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Header Sticky Sub Menu Background Color', 'claue' ),
			'default' => 'rgba(255, 255, 255, 0.95)'
		),
		array(
			'id'      => 'sticky-sub-menu-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Header Sticky Sub Menu Color', 'claue' ),
			'default' => '#222'
		),
		array(
			'id'      => 'sticky-sub-menu-color-hover',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Header Sticky Sub Menu Hover Color', 'claue' ),
			'default' => '#56cfe1'
		),
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Footer Color', 'claue' ),
		),
		array(
			'id'      => 'footer-background',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Footer Background Color', 'claue' ),
			'default' => '#f6f6f8'
		),
		array(
			'id'      => 'footer-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Footer Primary Color', 'claue' ),
			'default' => '#878787'
		),
		array(
			'id'      => 'footer-widget-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Footer Widget Title Color', 'claue' ),
			'default' => '#222'
		),
		array(
			'id'      => 'footer-link-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Footer Link Color', 'claue' ),
			'default' => '#878787'
		),
		array(
			'id'      => 'footer-link-hover-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Footer Link Hover Color', 'claue' ),
			'default' => '#56cfe1'
		),
		array(
			'type'	  => 'subheading',
			'content' => esc_html__('Product Badge Color', 'claue'),
		),
		array(
			'id'	  => 'product-sale-color',
			'type'    => 'color_picker',
			'title'   => esc_html__( 'Product Sale Badge Background Color', 'claue'),
			'default' => '#fe9931'
		),
		array(
			'id'      => 'product-new-color',
			'type'    => 'color_picker',
			'title'   => esc_html__('Product New Badge Background Color' ,'claue'),
			'default' => '#56cfe1'
		),
		array(
			'id'      => 'product-text-color',
			'type'    => 'color_picker',
			'title'   => esc_html__('Product Badge Text Color' ,'claue'),
			'default' => '#fff'
		),
		
	),
);

// ----------------------------------------
// a option section for options social    -
// ----------------------------------------
$options[] = array(
	'name'  => 'social',
	'title' => esc_html__( 'Social Network', 'claue' ),
	'icon'  => 'fa fa-globe',
	'fields' => array(
		array(
			'id'              => 'social-network',
			'type'            => 'group',
			'title'           => esc_html__( 'Social Account', 'claue' ),
			'button_title'    => esc_html__( 'Add New', 'claue' ),
			'accordion_title' => esc_html__( 'Add New Social Network', 'claue' ),
			'fields'          => array(
				array(
					'id'    => 'link',
					'type'  => 'text',
					'title' => esc_html__( 'URL', 'claue' ),
				),
				array(
					'id'    => 'icon',
					'type'  => 'icon',
					'title' => esc_html__( 'Icon', 'claue' ),
				),
			)
		),
		array(
			'type'	  => 'subheading',
			'content' => esc_html__('Enable Social Share', 'claue'),
		),
		array(
			'id'      => 'wc-social-share',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Enable Social Share', 'claue' ),
			'default' => true,
		),
		array(
			'id'         => 'facebook-share',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable Facebook Share', 'claue' ),
			'default'    => true,
			'dependency' => array( 'wc-social-share', '==', 'true' ),
		),
		array(
			'id'      	 => 'twitter-share',
			'type'    	 => 'switcher',
			'title'      => esc_html__( 'Enable Twitter Share', 'claue' ),
			'default'    => true,
			'dependency' => array( 'wc-social-share', '==', 'true' ),
		),
		array(
			'id'       	 => 'pinterest-share',
			'type'    	 => 'switcher',
			'title'    	 => esc_html__( 'Enable Pinterest Share', 'claue' ),
			'default' 	 => true,
			'dependency' => array( 'wc-social-share', '==', 'true' ),
		),
		array(
			'id'         => 'tumblr-share',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable Tumblr Share', 'claue' ),
			'default'  	 => true,
			'dependency' => array( 'wc-social-share', '==', 'true' ),
		),
		array(
			'id'         => 'vk-share',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable VK Share', 'claue' ),
			'default'  	 => false,
			'dependency' => array( 'wc-social-share', '==', 'true' ),
		),
		array(
			'id'         => 'linkedin-share',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable LinkedIn Share', 'claue' ),
			'default'  	 => false,
			'dependency' => array( 'wc-social-share', '==', 'true' ),
		),
		array(
			'id'         => 'whatsapp-share',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable WhatsApp Share', 'claue' ),
			'default'  	 => false,
			'dependency' => array( 'wc-social-share', '==', 'true' ),
		),
		array(
			'id'         => 'telegram-share',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Enable Telegram Share', 'claue' ),
			'default'  	 => false,
			'dependency' => array( 'wc-social-share', '==', 'true' ),
		),
	),
);

// ----------------------------------------
// a option section for options other     -
// ----------------------------------------
$options[] = array(
	'name'  => 'other',
	'title' => esc_html__( 'Maintenance Mode', 'claue' ),
	'icon'  => 'fa fa-power-off',
	'fields' => array(
		array(
			'id'    => 'maintenance',
			'type'  => 'switcher',
			'title' => esc_html__( 'Enable Maintenance Mode', 'claue' ),
			'desc'  => esc_html__( 'Put your site is undergoing maintenance, only admin can see the front end', 'claue' ),
		),
		array(
			'id'         => 'maintenance-bg',
			'type'       => 'background',
			'title'      => esc_html__( 'Background', 'claue' ),
			'dependency' => array( 'maintenance', '==', 'true' ),
		),
		array(
			'id'         => 'maintenance-title',
			'type'       => 'text',
			'title'      => esc_html__( 'Title', 'claue' ),
			'default'    => esc_html__( 'Sorry, we down for maintenance.', 'claue' ),
			'dependency' => array( 'maintenance', '==', 'true' ),
		),
		array(
			'id'         => 'maintenance-message',
			'type'       => 'wysiwyg',
			'title'      => esc_html__( 'Message', 'claue' ),
			'default'    => esc_html__( 'Fortunately only for a short while.', 'claue' ),
			'dependency' => array( 'maintenance', '==', 'true' ),
		),
		array(
			'id'         => 'maintenance-content',
			'type'       => 'textarea',
			'title'      => esc_html__( 'Extra Content', 'claue' ),
			'desc'       => esc_html__( 'This content will be put at right bottom, HTML is allowed', 'claue' ),
			'dependency' => array( 'maintenance', '==', 'true' ),
		),
		array(
			'id'    => 'maintenance-countdown',
			'type'  => 'switcher',
			'title' => esc_html__( 'Enable Countdown', 'claue' ),
			'dependency' => array( 'maintenance', '==', 'true' ),
		),
		array(
			'id'      => 'maintenance-date',
			'type'    => 'select',
			'title'   => esc_html__( 'Remaining Time - Date', 'claue' ),
			'options' => array(
				'01' => '01',
				'02' => '02',
				'03' => '03',
				'04' => '04',
				'05' => '05',
				'06' => '06',
				'07' => '07',
				'08' => '08',
				'09' => '09',
				'10' => '10',
				'11' => '11',
				'12' => '12',
				'13' => '13',
				'14' => '14',
				'15' => '15',
				'16' => '16',
				'17' => '17',
				'18' => '18',
				'19' => '19',
				'20' => '20',
				'21' => '21',
				'22' => '22',
				'23' => '23',
				'24' => '24',
				'25' => '25',
				'26' => '26',
				'27' => '27',
				'28' => '28',
				'29' => '29',
				'30' => '30',
				'31' => '31'
			),
			'class'      => 'chosen',
			'dependency' => array( 'maintenance-countdown', '==', 'true' ),
		),
		array(
			'id'    => 'maintenance-month',
			'type'  => 'select',
			'title' => esc_html__( 'Remaining Time - Month', 'claue' ),
			'options' => array(
				'January'   => 'January',
				'Febuary'   => 'Febuary',
				'March'     => 'March',
				'April'     => 'April',
				'May'       => 'May',
				'June'      => 'June',
				'July'      => 'July',
				'August'    => 'August',
				'September' => 'September',
				'October'   => 'October',
				'November'  => 'November',
				'December'  => 'December'
			),
			'class'      => 'chosen',
			'dependency' => array( 'maintenance-countdown', '==', 'true' ),
		),
		array(
			'id'    => 'maintenance-year',
			'type'  => 'select',
			'title' => esc_html__( 'Remaining Time - Year', 'claue' ),
			'options' => array(
				'2019' => '2019',
				'2020' => '2020',
				'2021' => '2021',
				'2022' => '2022',
				'2023' => '2023'
			),
			'class'      => 'chosen',
			'dependency' => array( 'maintenance-countdown', '==', 'true' ),
		),
	),
);

// ------------------------------
// backup                       -
// ------------------------------
$options[]   = array(
	'name'   => 'backup_section',
	'title'  => esc_html__( 'Backup', 'claue' ),
	'icon'   => 'fa fa-shield',
	'fields' => array(
		array(
			'type'    => 'notice',
			'class'   => 'warning',
			'content' => esc_html__( 'You can save your current options. Download a Backup and Import.', 'claue' ),
		),
		array(
	  		'type' => 'backup',
		),
	)
);
CSFramework::instance( $settings, $options );
