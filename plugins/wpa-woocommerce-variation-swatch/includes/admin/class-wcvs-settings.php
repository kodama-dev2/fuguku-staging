<?php
/**
 * Description
 *
 * @package WPA_WCVS
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
class WPA_WCVS_Settings {
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
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'settings' ) );

		self::$options = get_option( 'wpa_wcvs_options' );
	}

	/**
	 * Get plugin datas.
	 *
	 * @return  string
	 */
	public static function data( $key ) {
		if ( ! empty( self::$options[$key] ) )
			return self::$options[$key];
		return '';
	}

	/**
	 * Register menu
	 */
	public static function menu() {
		add_menu_page(
			'wpaddon',
			esc_html__( 'WPAddon', 'wcvs' ),
			'nosuchcapability',
			'wpaddon',
			NULL,
			'dashicons-wordpress',
			57
		);

		add_submenu_page(
			'wpaddon',
			esc_html__( 'WooCommerce Variation Swatch', 'wcvs' ),
			esc_html__( 'Variation Swatch', 'wcvs' ),
			'manage_options',
			'wpa-advanced-attributes',
			array( __CLASS__, 'screen' )
		);
	}

	/**
	 * Add plugin screen
	 */
	public static function screen() { ?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Variation Swatch Settings', 'wcvs' ); ?></h1>

			<div class="card">
				<form method="post" action="options.php">
					<?php
						// This prints out all hidden setting fields
						settings_fields( 'wpa-wcvs-settings' );
						do_settings_sections( 'wpa_wcvs_settings_page' );
						submit_button();
					?>
				</form>
				<script>
					( function( $ ) {
						$( document ).ready( function() {
							var show_on_list = $( '#show_product_list' ),
							    _val         = show_on_list.val();

							if ( _val == 'yes' ) {
								show_on_list.parent().parent().next().css( 'display', 'table-row' );
							} else {
								show_on_list.parent().parent().next().css( 'display', 'none' );
							}
							show_on_list.on( 'change', function() {
								if ( $( this ).val() == 'yes' ) {
									$( this ).parent().parent().next().css( 'display', 'table-row' );
								} else {
									$( this ).parent().parent().next().css( 'display', 'none' );
								}
							});
						});
					})( jQuery );
				</script>
			</div>
		</div>
	<?php }

	/**
	 * Register and add settings
	 */
	public static function settings() {
		register_setting(
			'wpa-wcvs-settings',
			'wpa_wcvs_options',
			array( __CLASS__, 'sanitize' )
		);

		add_settings_section(
			'section_general_settings',
			esc_html__( 'General', 'wcvs' ),
			array(),
			'wpa_wcvs_settings_page'
		);

		add_settings_field (
			'wpa_wcvs_enable_tooltip',
			esc_html__( 'Enable Tooltip', 'wcvs' ), 
			array( __CLASS__, 'select_callback' ),
			'wpa_wcvs_settings_page',
			'section_general_settings',
			array(
				'id'      => 'enable_tooltip',
				'default' => 'yes',
				'val'     => array(
					'yes' => esc_html__( 'Yes', 'wcpb' ),
					'no'  => esc_html__( 'No', 'wcpb' ),
				)
			)
		);

		add_settings_field (
			'wpa_wcvs_show_product_list',
			esc_html__( 'Show on product list', 'wcvs' ), 
			array( __CLASS__, 'select_callback' ),
			'wpa_wcvs_settings_page',
			'section_general_settings',
			array(
				'id'      => 'show_product_list',
				'default' => 'no',
				'val'     => array(
					'yes' => esc_html__( 'Yes', 'wcpb' ),
					'no'  => esc_html__( 'No', 'wcpb' ),
				)
			)
		);

		add_settings_field (
			'wpa_wcvs_show_image_on_list',
			esc_html__( 'Show image swatch on product list', 'wcvs' ), 
			array( __CLASS__, 'select_callback' ),
			'wpa_wcvs_settings_page',
			'section_general_settings',
			array(
				'id'      => 'show_image_product_list',
				'default' => 'yes',
				'val'     => array(
					'yes' => esc_html__( 'Yes', 'wcpb' ),
					'no'  => esc_html__( 'No', 'wcpb' ),
				)
			)
		);

		add_settings_field (
			'wpa_wcvs_show_product_list_position',
			esc_html__( 'Position', 'wcvs' ), 
			array( __CLASS__, 'select_callback' ),
			'wpa_wcvs_settings_page',
			'section_general_settings',
			array(
				'id'      => 'show_product_list_position',
				'default' => 'after',
				'val'     => array(
					'before' => esc_html__( 'Before title', 'wcpb' ),
					'after'  => esc_html__( 'After title', 'wcpb' ),
				)
			)
		);
		add_settings_field (
			'wpa_wcvs_add_color_name_to_product_title',
			esc_html__( 'Add color name to product title on list page', 'wcvs' ), 
			array( __CLASS__, 'select_callback' ),
			'wpa_wcvs_settings_page',
			'section_general_settings',
			array(
				'id'      => 'add_color_name_to_product_title',
				'default' => 'no',
				'val'     => array(
					'yes' => esc_html__( 'Yes', 'wcpb' ),
					'no'  => esc_html__( 'No', 'wcpb' ),
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
		$data_key = array( 'enable_tooltip', 'show_product_list', 'show_product_list_position', 'show_image_product_list', 'add_color_name_to_product_title' );
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
	public static function select_callback( $args ) {
		extract( shortcode_atts( array(
			'id'	   => '',
			'default'  => '',
			'wpa_class' => '',
			'val'	   => array()
		), $args ) );
		$select_value = isset( self::$options[$id] ) ? esc_attr( self::$options[$id]) : $default;

		echo '<select id="'.$id.'" name="wpa_wcvs_options[' . $id . ']">';
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
	public static function text_callback( $args = array() ) {
		extract( shortcode_atts( array(
			'id'	   => '',
			'default'  => '',
			'wpa_class' => '',
		), $args ) );
		
		printf(
			'<input class="'. $wpa_class .' regular-text ltr" type="text" id="' . $id . '" name="wpa_wcvs_options[' . $id . ']" value="%s" />',
			isset( self::$options[$id] ) ? esc_attr( self::$options[$id] ) : $default
		);
	}

	/** 
	 * Get the settings option array and print one of its values
	 */
	public static function textarea_callback( $args = array() ) {
		extract( shortcode_atts( array(
			'id'	    => '',
			'default'   => '',
			'wpa_class' => '',
		), $args ) );
		
		printf(
			'<textarea style="min-height: 70px" class="'. $wpa_class .' regular-text ltr" id="'. $id .'" name="wpa_wcvs_options['. $id .']" />' . ' %s ' .'</textarea>',
			isset( self::$options[$id] ) ? esc_attr( self::$options[$id] ) : $default
		);
	}
}
$WPA_WCVS_Settings = new WPA_WCVS_Settings();