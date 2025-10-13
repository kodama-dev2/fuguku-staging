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
class WPA_WCVS_Admin {
	/**
	 * Variable to hold supported custom attribute types.
	 *
	 * @var  array
	 */
	protected static $types;

	/**
	 * Initialize.
	 *
	 * @return  void
	 */
	public static function init() {
		// Define supported custom attribute types.
		self::$types = array(
			'color' => esc_html__( 'Color', 'wcvs' ),
			'label' => esc_html__( 'Label'  , 'wcvs' ),
		);

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

		// Register filter to add custom types for WooCommerce product attributes.
		add_filter( 'product_attributes_type_selector', array( __CLASS__, 'register' ) );

		// Register action to print values for custom attribute types in add/edit product screen.
		add_action( 'woocommerce_product_option_terms', array( __CLASS__, 'print_values' ), 10, 2 );

		// Register Ajax action to detect if Color Picker attribute is used for product variations.
		add_action( 'wp_ajax_wpa-detect-color-picker-attribute', array( __CLASS__, 'detect_color_picker_attribute' ) );

		// Register filter to get product image gallery (attachment IDs) for the selected color.
		add_filter( 'woocommerce_product_get_gallery_image_ids', array( __CLASS__, 'get_product_image_gallery' ), 10, 2 );

		// Register Ajax action to get product image gallery (HTML) for the selected color.
		add_action( 'wp_ajax_wpa-get-product-image-gallery', array( __CLASS__, 'print_product_image_gallery' ) );
		add_action( 'wp_ajax_nopriv_wpa-get-product-image-gallery', array( __CLASS__, 'print_product_image_gallery' ) );

		// Register action to show confirmation before deactivating plugin.
		add_action( 'plugin_action_links_' . WCVS_BASENAME, array( __CLASS__, 'confirm_deactivation' ) );

		// Register action to convert all custom attributes to <select> type when plugin is being deactivated.
		add_action( 'deactivate_' . WCVS_BASENAME, array( __CLASS__, 'deactivate_plugin' ) );

		// Check if screen to add/edit attribute values is requested.
		global $pagenow;

		if (
			in_array( $pagenow, array( 'edit-tags.php', 'term.php' ) )
			||
			( 'admin-ajax.php' == $pagenow && 'add-tag' == $_REQUEST['action'] )
		) {
			$taxonomy = isset( $_REQUEST['taxonomy' ] ) ? sanitize_text_field( $_REQUEST['taxonomy' ] ) : null;

			if ( $taxonomy && 'pa_' == substr( $taxonomy, 0, 3 ) ) {
				// Get custom attribute type.
				global $wpdb;

				$attribute = current(
					$wpdb->get_results(
						"SELECT attribute_type FROM {$wpdb->prefix}woocommerce_attribute_taxonomies " .
						"WHERE attribute_name = '" . esc_sql( substr( $taxonomy, 3 ) ) . "' LIMIT 0, 1;"
					)
				);

				if ( array_key_exists( $attribute->attribute_type, self::$types ) ) {
					// Add actions to print custom fields for add/edit attribute value form.
					add_action( "{$taxonomy}_add_form_fields" , array( __CLASS__, 'add'  ), 10    );
					add_action( "{$taxonomy}_edit_form_fields", array( __CLASS__, 'edit' ), 10, 2 );

					// Add action to save custom data for attribute value of custom types.
					add_action( "created_{$taxonomy}", array( __CLASS__, 'save' ), 10, 2 );
					add_action( "edited_{$taxonomy}" , array( __CLASS__, 'save' ), 10, 2 );
				}
			}
		}
	}

	/**
	 * Enqueue backend assets.
	 *
	 * @return  void
	 */
	public static function enqueue_assets() {
		global $pagenow;

		$post_type = get_post_type();

		if ( ( ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) && $post_type == 'product' ) || $pagenow == 'options-general.php' ) {
			wp_enqueue_script( 'wpa-wcvs-backend', WPA_WCVS()->plugin_url() . '/assets/js/wcvs-backend.js', array(), NULL, true );
			wp_enqueue_style( 'wpa-wcvs-backend', WPA_WCVS()->plugin_url() . '/assets/css/wcvs-backend.css' );

			wp_localize_script( 'wpa-wcvs-backend', 'wpa_wcvs', self::localize_script() );
		}
	}

	/**
	 * Embed baseline script.
	 *
	 * @return  array
	 */
	public static function localize_script() {
		return array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'_nonce'  => wp_create_nonce( 'wpa-wcvs-nonce' ),
			'refresh_tip' => __(
				"If you don't see meta boxes for selecting product variation images at the right side, click to refresh the page.",
				'wcvs'
			)
		);
	}

	/**
	 * Method to add custom types for WooCommerce product attributes.
	 *
	 * @param   array  $types  Current attribute types.
	 *
	 * @return  array
	 */
	public static function register( $types ) {
		return array_merge( $types, self::$types );
	}

	/**
	 * Method to print form fields for adding attribute value for custom attribute types.
	 *
	 * @param   string  $taxonomy  Current taxonomy slug.
	 *
	 * @return  void
	 */
	public static function add( $taxonomy ) {
		// Get custom attribute type.
		global $wpdb;

		$attribute = current(
			$wpdb->get_results(
				"SELECT attribute_type FROM {$wpdb->prefix}woocommerce_attribute_taxonomies " .
				"WHERE attribute_name = '" . substr( $taxonomy, 3 ) . "' LIMIT 0, 1;"
			)
		);

		if ( $attribute && array_key_exists( $attribute->attribute_type, self::$types ) ) {
			// Load template to print form fields for the custom attribute type.
			include_once WPA_WCVS()->plugin_path() . '/includes/admin/views/html-' . $attribute->attribute_type . '-new.php';
		}
	}

	/**
	 * Method to print form fields for editing attribute value for custom attribute types.
	 *
	 * @param object $tag      Current taxonomy term object.
	 * @param string $taxonomy Current taxonomy slug.
	 *
	 * @return  void
	 */
	public static function edit( $tag, $taxonomy ) {
		// Get custom attribute type.
		global $wpdb;

		$attribute = current(
			$wpdb->get_results(
				"SELECT attribute_type FROM {$wpdb->prefix}woocommerce_attribute_taxonomies " .
				"WHERE attribute_name = '" . substr( $taxonomy, 3 ) . "' LIMIT 0, 1;"
			)
		);

		if ( $attribute && array_key_exists( $attribute->attribute_type, self::$types ) ) {
			// Load template to print form fields for the custom attribute type.
			include_once WPA_WCVS()->plugin_path() . '/includes/admin/views/html-' . $attribute->attribute_type . '-edit.php';
		}
	}

	/**
	 * Method to save custom data for attribute value of custom types.
	 *
	 * @param   int  $term_id  Term ID.
	 * @param   int  $tt_id    Term taxonomy ID.
	 *
	 * @return  void
	 */
	public static function save( $term_id, $tt_id ) {
		// Save custom data.
		foreach ( $_POST as $key => $value ) {
			if ( 'wpa_' == substr( $key, 0, 4 ) ) {
				update_term_meta( $term_id, sanitize_key( $key ), sanitize_text_field( $value ) );
			}
		}
	}

	/**
	 * Method to print values for custom attribute types in add/edit product screen.
	 *
	 * @param   object  $attribute  Attribute data.
	 * @param   int     $i          Current attribute index.
	 *
	 * @return  void
	 */
	public static function print_values( $attribute, $i ) {
		// Verify attribute type.
		if ( array_key_exists( $attribute->attribute_type, self::$types ) ) {
			if ( isset( $_POST['taxonomy'] ) ) {
				$taxonomy = sanitize_text_field( $_POST['taxonomy'] );
			} else {
				$taxonomy = wc_attribute_taxonomy_name( $attribute->attribute_name );
			}
			?>
			<select name="attribute_values[<?php echo esc_attr( $i ); ?>][]" multiple="multiple" data-placeholder="<?php
				esc_attr_e( 'Select terms', 'wcvs' );
			?>" class="multiselect attribute_values wc-enhanced-select">
				<?php
				$all_terms = get_terms( $taxonomy, 'orderby=name&hide_empty=0' );

				if ( $all_terms ) :

				foreach ( $all_terms as $term ) :
				?>
				<option value="<?php
					echo esc_attr( version_compare(WC_VERSION, '3.0.0', 'lt') ? $term->slug : $term->term_id );
				?>" <?php
					selected( has_term( absint( $term->term_id ), $taxonomy, isset($_POST['post_id']) ? $_POST['post_id'] : 0 ), true );
				?>>
					<?php echo esc_html( $term->name ); ?>
				</option>
				<?php
				endforeach;

				endif;
				?>
			</select>
			<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'wcvs' ); ?></button>
			<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'wcvs' ); ?></button>
			<button class="button fr plus add_new_attribute"><?php esc_html_e( 'Add new', 'wcvs' ); ?></button>
			<?php
		}
	}

	/**
	 * Detect if Color Picker attribute is used for product variations.
	 *
	 * @return  void
	 */
	public static function detect_color_picker_attribute() {
		if ( isset( $_REQUEST['attributes'] ) ) {
			global $wpdb;

			foreach ( array_keys( $_REQUEST['attributes'] ) as $attribute_name ) {
				// Get custom attribute type.
				$attr = current(
					$wpdb->get_results(
						"SELECT attribute_type FROM {$wpdb->prefix}woocommerce_attribute_taxonomies " .
						"WHERE attribute_name = '" . substr( sanitize_key( $attribute_name ), 3 ) . "' LIMIT 0, 1;"
					)
				);

				if ( $attr && $attr->attribute_type == 'color' ) {
					wp_send_json_success( $attribute_name );
				}
			}
		}

		wp_send_json_error();
	}

	/**
	 * Method to get product image gallery for the selected color.
	 *
	 * @param   array       $attachment_ids  Current product gallery attachment IDs.
	 * @param   WC_Product  $product         Current product object.
	 *
	 * @return  array
	 */
	public static function get_product_image_gallery( $attachment_ids, $product ) {
		if ( $product->is_type( 'variable' ) ) {
			global $wpdb;

			// Prepare variation attributes.
			$attributes = $product->get_variation_attributes();

			// Alter variations form to support custom attribute types.
			foreach ( $attributes as $attribute_name => $options ) {
				// Get custom attribute type.
				$attr = current(
					$wpdb->get_results(
						"SELECT attribute_type FROM {$wpdb->prefix}woocommerce_attribute_taxonomies " .
						"WHERE attribute_name = '" . substr( $attribute_name, 3 ) . "' LIMIT 0, 1;"
					)
				);

				if ( $attr && ( $attr->attribute_type == 'color' ) ) {
					// Check if certain attribute value is requested.
					$key = 'attribute_' . sanitize_title( $attribute_name );

					if ( isset( $_REQUEST[ $key ] ) && in_array( $_REQUEST[ $key ], $options ) ) {
						// Get term data.
						$term = get_term_by( 'slug', wc_clean( $_REQUEST[ $key ] ), $attribute_name );

						if ( $term ) {
							// Get image gallery for the selected color.
							$meta_key      = "_product_image_gallery_{$term->taxonomy}-{$term->slug}";
							$image_gallery = get_post_meta( $product->get_id(), $meta_key, true );

							if ( $image_gallery ) {
								$attachment_ids = array_filter(
									array_filter( ( array ) explode( ',', $image_gallery ) ),
									'wp_attachment_is_image'
								);
							}
						}
					}
				}
			}
		}

		return $attachment_ids;
	}

	/**
	 * Print product image gallery for the selected color.
	 *
	 * @return  void
	 */
	public static function print_product_image_gallery() {
		// Initialize necessary global variables..
		$GLOBALS['post']    = get_post( $_REQUEST['product'] );
		$GLOBALS['product'] = function_exists( 'wc_get_product' ) ? wc_get_product( $_REQUEST['product'] ) : get_product( $_REQUEST['product'] );

		// Print HTML for product image gallery.
		woocommerce_show_product_images();

		exit;
	}

	/**
	 * Confirm before deactivating plugin.
	 *
	 * @param   string  $links  Current plugin's action links.
	 *
	 * @return  array
	 */
	public static function confirm_deactivation( $links ) {
		$links['deactivate'] = preg_replace(
			'/(href=["\'][^"\']+["\'])/',
			'onclick="if ( ! wpa_wcvs_confirm_deactivation() ) return false;" \\1',
			$links['deactivate']
		) . '
		<script type="text/javascript">
			window.wpa_wcvs_confirm_deactivation = function() {
				return confirm("' . __(
					'If this plugin is deactivated, custom attributes created using the plugin will no longer work. So, the plugin will automatically convert all custom attributes to <select> type before deactivating. Are you sure you want to continue?',
					'wcvs'
				) . '");
			};
		</script>
		';

		return $links;
	}

	/**
	 * Convert all custom attributes to <select> type before deactivating plugin.
	 *
	 * @return  void
	 */
	public static function deactivate_plugin() {
		// Get all custom attributes.
		global $wpdb;

		$attributes = $wpdb->get_results(
			"SELECT attribute_id FROM {$wpdb->prefix}woocommerce_attribute_taxonomies " .
			"WHERE attribute_type IN ('" . implode( "', '", array_keys( self::$types ) ) . "')",
			ARRAY_N
		);

		if ( count( $attributes ) ) {
			// Convert all custom attributes to <select> type.
			$wpdb->query(
				"UPDATE {$wpdb->prefix}woocommerce_attribute_taxonomies SET attribute_type = 'select'" .
				"WHERE attribute_id IN (" . implode( ', ', array_map( 'current', $attributes ) ) . ")"
			);
		}

		delete_transient( 'wc_attribute_taxonomies' );
	}
}

if ( is_admin() ) {
	WPA_WCVS_Admin::init();
}