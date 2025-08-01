<?php
/**
 * Register the required plugins for this theme.
 *
 * @since   1.0.0
 * @package Claue
 */
// Include the TGM_Plugin_Activation class.
include JAS_CLAUE_PATH . '/core/libraries/vendors/tgmpa/class-tgm-plugin-activation.php';

/**
 * Register the required plugins for this theme.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function jas_claue_register_required_plugins() {
	$plugins = array(
		array(
			'name'     => esc_html__( 'Claue Addons', 'claue' ),
			'slug'     => 'claue-addons',
			'source'   => 'http://janstudio.net/plugins/janstudio/claue/claue-addons.zip',
			'version' => '1.2.7',
			'required' => true,
		),
		array(
			'name'     => esc_html__( 'Claue Sample Data', 'claue' ),
			'slug'     => 'claue-sample',
			'source'   => 'http://janstudio.net/plugins/janstudio/claue/claue-sample.zip',
			'required' => false,
			'version'  => '1.0.0'
		),
		array(
			'name'     => esc_html__( 'Pin Maker', 'claue' ),
			'slug'     => 'pin-maker',
			'source'   => 'http://janstudio.net/plugins/vendors/pin-maker.zip',
			'version'  => '1.0.9',
		),
		array(
			'name'     => esc_html__( 'Envato Market', 'claue' ),
			'slug'     => 'envato-market',
			'source'   => 'https://goo.gl/pkJS33',
		),
		array(
			'name'     => esc_html__( 'WPA WooCommerce Product Bundle', 'claue' ),
			'slug'     => 'wpa-woocommerce-product-bundle',
			'source'   => 'http://janstudio.net/plugins/vendors/wpa-woocommerce-product-bundle.zip',
			'version'  => '1.2.4',
			'external_url' => true
		),
		array(
			'name'     => esc_html__( 'WooCommerce', 'claue' ),
			'slug'     => 'woocommerce',
			'required' => false,
		),
		array(
			'name'      => esc_html__( 'Instagram Shop', 'claue' ),
			'slug'      => 'shop-feed-for-instagram-by-snapppt',
			'required'  => false,
		),
		array(
			'name'      => esc_html__( 'MailChimp', 'claue' ),
			'slug'      => 'mailchimp-for-wp',
			'required'  => false,
			),
		array(
			'name'      => esc_html__( 'YITH WooCommerce Wishlist', 'claue' ),
			'slug'      => 'yith-woocommerce-wishlist',
			'required'  => false,
		),
		array(
			'name'      => esc_html__( 'YITH WooCommerce Newsletter Popup', 'claue' ),
			'slug'      => 'yith-woocommerce-popup',
			'required'  => false,
		),
		array(
			'name'      => esc_html__( 'YITH WooCommerce Ajax Product Filter', 'claue' ),
			'slug'      => 'yith-woocommerce-ajax-navigation',
			'required'  => false,
		),
		array(
			'name'      => esc_html__( 'YIKES Custom Product Tabs', 'claue' ),
			'slug'      => 'yikes-inc-easy-custom-woocommerce-product-tabs',
			'required'  => false,
		),
		array(
			'name'      => esc_html__( 'Smash Balloon Instagram Feed', 'claue' ),
			'slug'      => 'instagram-feed',
			'required'  => false,
		),
		array(
			'name'    	=> esc_html__( 'Elementor', 'claue' ),
			'slug'    	=> 'elementor',
			'required' 	=> false,
		),
		array(
			'name'     	=> esc_html__( 'Visual Composer', 'claue' ),
			'slug'     	=> 'js_composer',
			'source'   	=> 'http://janstudio.net/plugins/vendors/js_composer.zip',
			'version'  	=> '6.13.0'
		)

	);

	if ( ! class_exists( 'ColorSwatch' ) ) { 
		$plugins[] = array(
			'name'    => esc_html__( 'WooCommerce Variation Swatch', 'claue' ),
			'slug'    => 'wpa-woocommerce-variation-swatch',
			'source'  => 'http://janstudio.net/plugins/vendors/wpa-woocommerce-variation-swatch.zip',
			'version' => '1.1.7'
		);
	}
	if ( ! class_exists( 'MetaSliderPlugin' ) ) { 
		$plugins[] = array(
			'name'    => esc_html__( 'Smart Slider', 'claue' ),
			'slug'    => 'smart-slider-3',
			'required' => false,
		);
	}
	if ( ! class_exists( 'SmartSlider3' ) ) { 
		$plugins[] = array(
			'name'      => esc_html__( 'Meta Slider', 'claue' ),
			'slug'      => 'ml-slider',
			'required'  => false,
		);
	}
	if ( ! defined( 'WPCF7_VERSION' ) ) { 
		$plugins[] = array(
			'name'      => esc_html__( 'Contact Form by WPForms', 'claue' ),
			'slug'      => 'wpforms-lite',
			'required'  => false,
		);
	}
	if ( ! defined( 'WPFORMS_VERSION' ) ) { 
		$plugins[] = array(
			'name'      => esc_html__( 'Contact Form 7', 'claue' ),
			'slug'      => 'contact-form-7',
			'required'  => false,
		);
	}

	$config = array(
		'id'           => 'tgmpa',
		'menu'         => 'jas-install-plugins',
		'parent_slug'  => 'jas',
		'capability'   => 'edit_theme_options',
		'is_automatic' => true,
	);
	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'jas_claue_register_required_plugins' );