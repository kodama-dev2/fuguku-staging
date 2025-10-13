<?php
/**
 * Description
 *
 * @package WPA_WCPB
 * @version 1.0.0
 * @author  WPAddon
 */
if ( ! defined('ABSPATH' ) ) {
	exit;
}

/**
 * Class description.
 *
 * @version 1.0.0
 */
class WPA_WCPB_Settings {
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private static $options;

	/**
	 * Initialize.
	 *
	 * @return  void
	 */
	public function __construct() {
		add_action( 'admin_menu', array( __CLASS__, 'add_plugin_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'page_init' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'px_add_color_picker' ) );

		self::$options = get_option( 'px_bundle_options' );
	}

	/**
	 * Get product bundle tyle.
	 *
	 * @return  string
	 */
	public static function get_product_bundle_type() {
		if ( ! empty( self::$options['type'] ) )
			return self::$options['type'];
		return 'total-discount';
	}

	/**
	 * Get product bundle data.
	 *
	 * @return  string
	 */
	public static function get_product_bundle_data( $key ) {
		if ( ! empty( self::$options[$key] ) )
			return self::$options[$key];
		return '';
	}


	/**
	 * Enqueue color picker jquery
	 *
	 * @return  string
	 */
	public static function px_add_color_picker( $hook ) {
		if ( is_admin() ) { 
			// Add the color picker css file       
			wp_enqueue_style( 'wp-color-picker' ); 

			// Include our custom jQuery file with WordPress Color Picker dependency
			wp_enqueue_script( 'wp-color-picker' ); 
		}
	}

	/**
	 * Add options page
	 */
	public static function add_plugin_page() {
		// This page will be under "Settings"
		add_options_page(
			'Product Bundle Settings', 
			'Product Bundle Settings', 
			'manage_options', 
			'product_bundle_settings_page', 
			array( __CLASS__, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public static function create_admin_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Product Bundle Settings', 'wcpb' );?></h1>
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'px-bundle_group' );
				do_settings_sections( 'product_bundle_settings_page' );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public static function page_init()
	{        
		register_setting(
			'px-bundle_group', // Option group
			'px_bundle_options', // Option name
			array( __CLASS__, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'px_bundle_section_general_settings',
			esc_html__( 'General Settings', 'wcpb' ),
			array(),
			// array( __CLASS__, 'print_section_info' ),
			'product_bundle_settings_page'
		);  

		add_settings_field (
			'px_bundle_widget_title',
			esc_html__( 'Bundle title', 'wcpb' ), 
			array( __CLASS__, 'px_text_callback' ),
			'product_bundle_settings_page',
			'px_bundle_section_general_settings',
			array(
				'id'      => 'bundles_widget_title',
				'default' => __( 'Buy this bundle and get 25% off', 'wcpb' )
			)
		);

		add_settings_field (
			'px_bundles_promo_text',
			esc_html__( 'Bundle description', 'wcpb' ), 
			array( __CLASS__, 'px_textarea_callback' ),
			'product_bundle_settings_page',
			'px_bundle_section_general_settings',
			array(
				'id'      => 'bundles_promo_text',
				'default' => __( 'Buy more save more. Save 15% off when you purchase 4 products, save 10% off when you purchase 3 products', 'wcpb' )
			)
		);

		add_settings_field(
			'px_bundle_button_label',
			esc_html__( 'Button label', 'wcpb' ), 
			array( __CLASS__, 'px_text_callback' ),
			'product_bundle_settings_page',
			'px_bundle_section_general_settings',
			array(
				'id'      => 'button_label',
				'default' => __( 'Add all to cart', 'wcpb' )
			)
		);

		add_settings_field(
			'px_bundle_button_background_color',
			esc_html__( 'Button background', 'wcpb' ), 
			array( __CLASS__, 'px_text_callback' ),
			'product_bundle_settings_page',
			'px_bundle_section_general_settings',
			array(
				'id'       => 'button_bg_color',
				'px_class' => 'px-color-picker'
			)
		);

		add_settings_field(
			'px_bundle_button_background_hover_color',
			esc_html__( 'Button background hover', 'wcpb' ), 
			array( __CLASS__, 'px_text_callback' ),
			'product_bundle_settings_page',
			'px_bundle_section_general_settings',
			array(
				'id'       => 'button_bg_hover_color',
				'px_class' => 'px-color-picker'
			)
		);

		add_settings_field(
			'px_bundle_button_text_color',
			esc_html__( 'Button text', 'wcpb' ), 
			array( __CLASS__, 'px_text_callback' ),
			'product_bundle_settings_page',
			'px_bundle_section_general_settings',
			array(
				'id'       => 'button_text_color',
				'px_class' => 'px-color-picker'
			)
		);

		add_settings_field(
			'px_bundle_button_text_hover_color',
			esc_html__( 'Button text hover', 'wcpb' ), 
			array( __CLASS__, 'px_text_callback' ),
			'product_bundle_settings_page',
			'px_bundle_section_general_settings',
			array(
				'id'       => 'button_text_hover_color',
				'px_class' => 'px-color-picker'
			)
		);

		add_settings_field(
			'px_bundle_product_image_size',
			esc_html__( 'Product image size', 'wcpb' ), 
			array( __CLASS__, 'px_image_size_callback' ),
			'product_bundle_settings_page',
			'px_bundle_section_general_settings',
			array(
				'id'      => 'product_image_size',
				'default' => '70x70'
			)
		);

		add_settings_field(
			'px_bundle_position_display_setting',
			esc_html__( 'Position', 'wcpb' ), 
			array( __CLASS__, 'px_select_callback' ),
			'product_bundle_settings_page',
			'px_bundle_section_general_settings',
			array(
				'id'      => 'position_display_setting',
				'default' => 'below-product-summary',
				'val'     => array(
					'below-product-summary' => esc_html__( 'Below product summary', 'wcpb' ),
					'above-product-tabs'    => esc_html__( 'Above product tab', 'wcpb' ),
					'below-product-tabs'    => esc_html__( 'Below product tab', 'wcpb' )
				)
			)
		);

		add_settings_field(
			'px_bundle_display_bundle_save',
			esc_html__( 'Display bundle save', 'wcpb' ), 
			array( __CLASS__, 'px_select_callback' ),
			'product_bundle_settings_page',
			'px_bundle_section_general_settings',
			array(
				'id'      => 'display_bundle_save',
				'default' => 'percent_off',
				'val'     => array(
					'percent_off' => esc_html__( 'Percent off (%)', 'wcpb' ),
					'amount_off'  => esc_html__( 'Amount off', 'wcpb' )
				)
			)
		);

		add_settings_field(
			'px_bundle_discount_type',
			esc_html__( 'Type of discount', 'wcpb' ), 
			array( __CLASS__, 'px_select_callback' ),
			'product_bundle_settings_page',
			'px_bundle_section_general_settings',
			array(
				'id'      => 'type',
				'default' => 'total-discount',
				'val'     => array(
					'total-discount' 		=> esc_html__( 'Discount for total', 'wcpb' ),
					//'discount-per-item' 	=> esc_html__( 'Discount per item' )
				)
			)
		);  

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		$new_input = array();
		$data_key = array( 'bundles_widget_title','bundles_promo_text', 'button_label', 'button_bg_color', 'button_bg_hover_color', 'button_text_color', 'button_text_hover_color', 'product_image_size', 'position_display_setting', 'display_bundle_save', 'type' );
		foreach ( $data_key as $key => $value ) {
			if ( isset( $input[$value] ) ) {
				$new_input[$value] = sanitize_text_field( $input[$value] );
			}
		}
		
		return $new_input;
	}
	
	/** 
	 * Get the settings option array and print one of its values
	 */
	public static function px_select_callback( $args ) {
		extract( shortcode_atts( array(
			'id'	   => '',
			'default'  => '',
			'px_class' => '',
			'val'	   => array()
		), $args ) );
		$select_value = isset( self::$options[$id] ) ? esc_attr( self::$options[$id]) : $default;

		echo '<select id="'.$id.'" name="px_bundle_options[' . $id . ']">';
		if ( count( $val ) > 0 ) {
			foreach ( $val as $key => $value ) {
				echo '<option value="' . $key . '" ' . selected( $select_value, $key, false ) . '>' . $value . '</option>';
			}
		}
		echo '</select>';
	}

	/** 
	 * Get the settings option array and print one of its values
	 */
	public static function px_text_callback( $args = array() ) {
		extract( shortcode_atts( array(
			'id'	   => '',
			'default'  => '',
			'px_class' => '',
		), $args ) );
		
		printf(
			'<input class="'. $px_class .' regular-text ltr" type="text" id="' . $id . '" name="px_bundle_options[' . $id . ']" value="%s" />',
			isset( self::$options[$id] ) ? esc_attr( self::$options[$id] ) : $default
		);
	}

	/** 
	 * Get the settings option array and print one of its values
	 */
	public static function px_textarea_callback( $args = array() ) {
		extract( shortcode_atts( array(
			'id'	   => '',
			'default'  => '',
			'px_class' => '',
		), $args ) );
		
		printf(
			'<textarea style="min-height: 70px" class="'. $px_class .' regular-text ltr" id="'. $id .'" name="px_bundle_options['. $id .']" />' . ' %s ' .'</textarea>',
			isset( self::$options[$id] ) ? esc_attr( self::$options[$id] ) : $default
		);
	}

	/** 
	 * Get the settings option array and print one of its values
	 */
	public static function px_image_size_callback( $args = array() ) {
		extract( shortcode_atts( array(
			'id'	   => '',
			'default'  => '',
			'px_class' => '',
		), $args ) );
		
		printf(
			'<input class="' . $px_class . ' regular-text ltr" type="text" id="' . $id . '" name="px_bundle_options['. $id .']" value="%s" /><p class="description">'.esc_html__( 'Ex: 70x70 or can use product size defalut: shop_thumbnail, shop_catalog, shop_single', 'wcpb' ).'</p>',
			isset( self::$options[$id] ) ? esc_attr( self::$options[$id] ) : $default
		);
	}	
}
$WPA_WCPB_Settings = new WPA_WCPB_Settings();