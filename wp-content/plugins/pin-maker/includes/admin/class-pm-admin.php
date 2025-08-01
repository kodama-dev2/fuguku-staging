<?php
/**
 * Class post types
 * Register post type and taxonomy.
 *
 * @package PinMaker
 * @version 1.0.0
 * @author  WPAddon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post Types Class.
 *
 * @version 1.0.0
 */
class WPA_PM_Admin {
	/**
	 * Initialize.
	 *
	 * @return  void
	 */
	public static function init() {
		// Check if post type pin is requested.
		global $pagenow, $post_type, $post;

		if ( in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ) ) ) {
			// Get current post type.
			if ( ! isset( $post_type ) ) {
				$post_type = isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : null;
			}

			if ( empty( $post_type ) && ( isset( $post ) || isset( $_REQUEST['post'] ) ) ) {
				$post_type = isset( $post ) ? $post->post_type : get_post_type( $_REQUEST['post'] );
			}

			if ( 'pins' == $post_type ) {
				if ( 'edit.php' == $pagenow ) {
					// Register necessary actions / filters to customize All Items screen.
					add_filter( 'bulk_actions-edit-pins', array( __CLASS__, 'bulk_actions' ) );

					add_filter( 'manage_pins_posts_columns', array( __CLASS__, 'register_columns' ) );
					add_action( 'manage_posts_custom_column' , array( __CLASS__, 'display_columns'  ), 10, 2 );
				}

				elseif ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
					if ( ! isset( $_REQUEST['action'] ) || 'trash' != $_REQUEST['action'] ) {
						// Register necessary actions / filters to override Item Details screen.
						add_action( 'admin_footer'  , array( __CLASS__, 'admin_editor' ) );
						add_action( 'save_post_pins', array( __CLASS__, 'save_post' ) );
					}
				}
			}
		}

		// Add extra field to pin category
		add_action( 'pins_cat_add_form_fields', array( __CLASS__, 'add_pin_cat_field' ) );
		add_action( 'pins_cat_edit_form_fields', array( __CLASS__, 'edit_pin_cat_field' ), 10 );
		add_action( 'created_term', array( __CLASS__, 'save_pin_cat_fields' ), 10, 3 );
		add_action( 'edit_term', array( __CLASS__, 'save_pin_cat_fields' ), 10, 3 );

		// Add custom column to pin category
		add_filter( 'manage_edit-pins_cat_columns', array( __CLASS__, 'add_column_pins_cat' ) );
		add_filter( 'manage_pins_cat_custom_column', array( __CLASS__, 'add_content_pins_cat' ), 10, 3 );

		// Enqueue assets
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ), 9999 );

		// Custom style
		add_action( 'admin_head', array( __CLASS__, 'custom_css_icon' ) );

		// Register Ajax actions / filters.
		add_filter( 'woocommerce_json_search_found_products', array( __CLASS__, 'search_products' ) );
	}

	/**
	 * Setup bulk actions for in pins screen.
	 *
	 * @param   array  $actions  Current actions.
	 *
	 * @return  array
	 */
	public static function bulk_actions( $actions ) {
		// Remove edit action.
		unset( $actions['edit'] );

		return $actions;
	}

	/**
	 * Register extra column for pins screen.
	 *
	 * @param   array  $columns  Current columns.
	 *
	 * @return  array
	 */
	public static function register_columns( $columns ) {
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'image'     => esc_html__( 'Thumbnail', 'pin-maker' ),
			'title'     => esc_html__( 'Title', 'pin-maker' ),
			'shortcode' => esc_html__( 'Shortcode', 'pin-maker' ),
			'date'      => esc_html__( 'Date', 'pin-maker' ),
		);

		return $columns;
	}

	/**
	 * Display extra column for pins screen.
	 *
	 * @param   array  $column   Column to display content for.
	 * @param   int    $post_id  Post ID to display content for.
	 *
	 * @return  array
	 */
	public static function display_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'image' :
				// Get current image.
				$attachment_id = get_post_meta( $post_id, 'wpa_pin_images', true );

				if ( $attachment_id ) {
					// Print image source.
					echo wp_get_attachment_image( $attachment_id, array( 100, 100 ) );
				} else {
					esc_html_e( 'No Thumb', 'pin-maker' );
				}
			break;

			case 'shortcode' :
				?>
				<input class="code" type="text" onfocus="this.select();" readonly="readonly" value='[pins id="<?php echo absint( $post_id ); ?>"]' />
				<?php
			break;
		}
	}

	/**
	 * Init admin editor.
	 *
	 * @return  void
	 */
	public static function admin_editor() {
		// Check if is pins post type
		global $pagenow, $post_type, $post;

		// Get current post type.
		if ( ! isset( $post_type ) ) {
			$post_type = isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : null;
		}

		if ( empty( $post_type ) && ( isset( $post ) || isset( $_REQUEST['post'] ) ) ) {
			$post_type = isset( $post ) ? $post->post_type : get_post_type( $_REQUEST['post'] );
		}

		if ( 'pins' == $post_type ) {
			if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
				if ( ! isset( $_REQUEST['action'] ) || 'trash' != $_REQUEST['action'] ) {
					// Load template file.
					include_once WPA_PM()->plugin_path() . '/includes/admin/pm-admin-editor.php';
				}
			}
		}
	}

	/**
	 * Save custom post type extra data.
	 *
	 * @param   int  $id  Current post ID.
	 *
	 * @return  void
	 */
	public static function save_post( $id ) {
		if ( isset( $_POST['wpa_pin_images'] ) ) {
			update_post_meta( $id, 'wpa_pin_images', absint( $_POST['wpa_pin_images'] ) );
		}

		if ( isset( $_POST['wpa_pin_settings'] ) && is_array( $_POST['wpa_pin_settings'] ) ) {
			// Sanitize input data.
			$wpa_pin_settings = array();

			foreach ( $_POST['wpa_pin_settings'] as $key => $value ) {
				$wpa_pin_settings[ $key ] = sanitize_text_field( $value );
			}

			update_post_meta( $id, 'wpa_pin_settings', $wpa_pin_settings );
		}

		if ( isset( $_POST['wpa_pin'] ) && is_array( $_POST['wpa_pin'] ) ) {
			$wpa_pin = array();

			foreach ( $_POST['wpa_pin'] as $k => $pin ) {
				// Sanitize input data.
				foreach ( $pin as $key => $value ) {
					if ( 'settings' == $key ) {
						foreach ( $value as $settings_key => $settings_value ) {
							if ( 'text' == $settings_key ) {
								$wpa_pin[ $k ][ $key ][ $settings_key ] = esc_sql(
									str_replace(
										array( "\r\n", "\r", "\n", '\\' ),
										array( '<br>', '<br>', '<br>', '' ),
										$settings_value
									)
								);
							} else {
								$wpa_pin[ $k ][ $key ][ $settings_key ] = sanitize_text_field( $settings_value );
							}

							if ( 'id' == $settings_key && empty( $settings_value ) ) {
								$wpa_pin[ $k ][ $key ][ $settings_key ] = wp_generate_password( 3, false, false );
							}
						}
					} else {
						$wpa_pin[ $k ][ $key ] = sanitize_text_field( $value );
					}
				}
			}

			update_post_meta( $id, 'wpa_pin', $wpa_pin );
		} else {
			delete_post_meta( $id, 'wpa_pin' );
		}

		// Publish post if needed.
		if ( ! defined( 'DOING_AUTOSAVE' ) || ! DOING_AUTOSAVE ) {
			$post = get_post( $id );

			if ( esc_html_e( 'Auto Draft' ) != $post->post_title && 'publish' != $post->post_status ) {
				wp_publish_post( $post );
			}
		}
	}

	/**
	 * Adding custom form field to pin_cat taxonomy.
	 *
	 * @return  string
	 */
	public static function add_pin_cat_field() { ?>
		<div class="form-field term-layout-wrap">
			<label for="display_type"><?php esc_html_e( 'Display Type', 'pin-maker' ); ?></label>
			<select name="display_type" id="display_type" class="postform">
				<option value=""><?php esc_html_e( 'Default', 'pin-maker' ); ?></option>
				<option value="masonry"><?php esc_html_e( 'Masonry', 'pin-maker' ); ?></option>
				<option value="slider"><?php esc_html_e( 'Slider', 'pin-maker' ); ?></option>
			</select>
		</div>
		<div class="form-field term-column-wrap">
			<label for="display_column"><?php esc_html_e( 'Columns', 'pin-maker' ); ?></label>
			<select name="display_column" id="display_column" class="postform">
				<option value="1"><?php esc_html_e( '1 Column', 'pin-maker' ); ?></option>
				<option value="2"><?php esc_html_e( '2 Columns', 'pin-maker' ); ?></option>
				<option value="3"><?php esc_html_e( '3 Columns', 'pin-maker' ); ?></option>
				<option value="4"><?php esc_html_e( '4 Columns', 'pin-maker' ); ?></option>
				<option value="5"><?php esc_html_e( '5 Columns', 'pin-maker' ); ?></option>
			</select>
		</div>
		<div class="form-field term-gutter-wrap">
			<label for="gutter_width"><?php esc_html_e( 'Gutter Width (Type Number Only)', 'pin-maker' ); ?></label>
			<input name="gutter_width" id="gutter_width" type="number" value="" />
		</div>

		<div class="form-field term-slider-setting-wrap hidden">
			<label for="slider_limit"><?php esc_html_e( 'Slider Settings', 'pin-maker' ); ?></label>
			
			<label for="slider_autoplay">
				<input name="slider_autoplay" id="slider_autoplay" type="checkbox" value="1" />
				<?php esc_html_e( 'Autoplay', 'pin-maker' ); ?>
			</label>
			<label for="slider_arrow">
				<input name="slider_arrow" id="slider_arrow" type="checkbox" value="1" />
				<?php esc_html_e( 'Enable Arrow', 'pin-maker' ); ?>
			</label>
			<label for="slider_dot">
				<input name="slider_dot" id="slider_dot" type="checkbox" value="1" />
				<?php esc_html_e( 'Enable Dot', 'pin-maker' ); ?>
			</label>
		</div>
		<style>
			.row-actions .view,
			.term-parent-wrap,
			.term-description-wrap,
			.column-description {
				display: none;
			}
		</style>
		<script type="text/javascript">
			(function( $ ) {
				$( document ).ready( function() {
					$( '#display_type' ).change( function() {
						if ( $( this ).val() == 'slider' ) {
							$( '.term-slider-setting-wrap' ).removeClass( 'hidden' );
						} else {
							$( '.term-slider-setting-wrap' ).addClass( 'hidden' );
						}
					});
				});
			})( jQuery );
		</script>
	<?php }

	/**
	 * Edit pin_cat field.
	 *
	 * @param mixed $term Term (category) being edited
	 */
	public static function edit_pin_cat_field( $term ) {
		$display_type    = get_term_meta( $term->term_id, 'display_type', true );
		$display_column  = get_term_meta( $term->term_id, 'display_column', true );
		$gutter_width    = get_term_meta( $term->term_id, 'gutter_width', true );
		$slider_limit    = get_term_meta( $term->term_id, 'slider_limit', true );
		$slider_autoplay = get_term_meta( $term->term_id, 'slider_autoplay', true );
		$slider_arrow    = get_term_meta( $term->term_id, 'slider_arrow', true );
		$slider_dot      = get_term_meta( $term->term_id, 'slider_dot', true );
	?>
		<tr class="form-field term-layout-wrap">
			<th scope="row"><label><?php esc_html_e( 'Display Type', 'pin-maker' ); ?></label></th>
			<td>
				<select id="display_type" name="display_type" class="postform">
					<option value="" <?php selected( '', $display_type ); ?>><?php esc_html_e( 'Default', 'pin-maker' ); ?></option>
					<option value="masonry" <?php selected( 'masonry', $display_type ); ?>><?php esc_html_e( 'Masonry', 'pin-maker' ); ?></option>
					<option value="slider" <?php selected( 'slider', $display_type ); ?>><?php esc_html_e( 'Slider', 'pin-maker' ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="form-field term-columns-wrap">
			<th scope="row"><label><?php esc_html_e( 'Columns', 'pin-maker' ); ?></label></th>
			<td>
				<select name="display_column" id="display_column" class="postform">
					<option value="1" <?php selected( '1', $display_column ); ?>><?php esc_html_e( '1 Column', 'pin-maker' ); ?></option>
					<option value="2" <?php selected( '2', $display_column ); ?>><?php esc_html_e( '2 Columns', 'pin-maker' ); ?></option>
					<option value="3" <?php selected( '3', $display_column ); ?>><?php esc_html_e( '3 Columns', 'pin-maker' ); ?></option>
					<option value="4" <?php selected( '4', $display_column ); ?>><?php esc_html_e( '4 Columns', 'pin-maker' ); ?></option>
					<option value="5" <?php selected( '5', $display_column ); ?>><?php esc_html_e( '5 Columns', 'pin-maker' ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="form-field term-gutter-wrap">
			<th scope="row"><label><?php esc_html_e( 'Gutter Width', 'pin-maker' ); ?></label></th>
			<td>
				<input name="gutter_width" id="gutter_width" type="number" value="<?php echo esc_attr( $gutter_width ); ?>" />
				<p class="description"><?php esc_html_e( 'Type number only', 'pin-maker' ); ?></p>

			</td>
		</tr>
		<tr class="form-field term-slider-setting-wrap hidden">
			<th scope="row"><label><?php esc_html_e( 'Slider Setting', 'pin-maker' ); ?></label></th>
			<td>
				<label for="slider_autoplay">
					<input name="slider_autoplay" id="slider_autoplay" type="checkbox" value="1" <?php checked( $slider_autoplay, 1 ); ?> />
					<?php esc_html_e( 'Autoplay', 'pin-maker' ); ?>
				</label>
				<label for="slider_arrow" style="margin: 0 10px;">
					<input name="slider_arrow" id="slider_arrow" type="checkbox" value="1" <?php checked( $slider_arrow, 1 ); ?> />
					<?php esc_html_e( 'Enable Arrow', 'pin-maker' ); ?>
				</label>
				<label for="slider_dot">
					<input name="slider_dot" id="slider_dot" type="checkbox" value="1" <?php checked( $slider_dot, 1 ); ?> />
					<?php esc_html_e( 'Enable Dot', 'pin-maker' ); ?>
				</label>
			</td>
		</tr>
		<style>
			.term-parent-wrap,
			.term-description-wrap {
				display: none;
			}
		</style>
		<script type="text/javascript">
			(function( $ ) {
				$( document ).ready( function() {
					$( '#display_type' ).change( function() {
						if ( $( this ).val() == 'slider' ) {
							$( '.term-slider-setting-wrap' ).removeClass( 'hidden' );
						} else {
							$( '.term-slider-setting-wrap' ).addClass( 'hidden' );
						}
					});
					if ( $( '#display_type' ).val() == 'slider' ) {
						$( '.term-slider-setting-wrap' ).removeClass( 'hidden' );
					}
				});
			})( jQuery );
		</script>
	<?php }

	/**
	 * save_pin_cat_fields function.
	 *
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param string $taxonomy
	 */
	public static function save_pin_cat_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( 'pins_cat' === $taxonomy ) {
			update_term_meta( $term_id, 'display_type', esc_attr( $_POST['display_type'] ) );

			update_term_meta( $term_id, 'display_column', esc_attr( $_POST['display_column'] ) );

			update_term_meta( $term_id, 'gutter_width', esc_attr( $_POST['gutter_width'] ) );

			update_term_meta( $term_id, 'slider_autoplay', esc_attr( $_POST['slider_autoplay'] ) );

			update_term_meta( $term_id, 'slider_arrow', esc_attr( $_POST['slider_arrow'] ) );

			update_term_meta( $term_id, 'slider_dot', esc_attr( $_POST['slider_dot'] ) );
		}
	}

	/**
	 * Add column to display shortcode for pin category.
	 *
	 * @return  string
	 */
	public static function add_column_pins_cat( $columns ) {
		$columns['shortcode'] = esc_html__( 'Shortcode', 'pin-maker' );
		return $columns;
	}
	public static function add_content_pins_cat( $content, $column_name, $term_id ) {
		if ( 'shortcode' == $column_name ) {
			$content = '<input class="code" type="text" onfocus="this.select();" readonly="readonly" value="[pins_cat id=\'' . $term_id . '\']">';
		}
		return $content;
	}

	/**
	 * Enqueue assets for admin.
	 *
	 * @return  string
	 */
	public static function enqueue_assets() {
		// Check if is pins post type
		global $pagenow, $post_type;

		if ( 'pins' == $post_type ) {
			if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
				// Enqueue media.
				wp_enqueue_media();

				// Enqueue custom color picker library.
				wp_enqueue_style( 'cs-wp-color-picker', WPA_PM()->plugin_url() . '/assets/vendors/wp-color-picker/wp-color-picker.min.css', array( 'wp-color-picker' ) );
				wp_enqueue_script( 'cs-wp-color-picker', WPA_PM()->plugin_url() . '/assets/vendors/wp-color-picker/wp-color-picker.min.js' , array( 'wp-color-picker' ) );

				// Select 2
				wp_dequeue_script( 'select2' );
				wp_dequeue_style( 'select2' );
				wp_enqueue_style(  'pm-select2', WPA_PM()->plugin_url() . '/assets/vendors/select2/select2.min.css' );
				wp_enqueue_script( 'pm-select2', WPA_PM()->plugin_url() . '/assets/vendors/select2/select2.min.js' );

				// Enqueue assets for admin.
				wp_enqueue_style(  'pm-admin', WPA_PM()->plugin_url() . '/assets/css/pm-admin.css' );
				wp_enqueue_script( 'pm-admin', WPA_PM()->plugin_url() . '/assets/js/pm-admin.js' );

				wp_localize_script( 'pm-admin', 'wpa_pin_maker', array(
					'text' => array(
						'button_label'           => esc_html__( 'Select', 'pin-maker' ),
						'modal_title'            => esc_html__( 'Select or upload an image', 'pin-maker' ),
						'ask_for_saving_changes' => esc_html__( 'Your changes on this page are not saved!', 'pin-maker' ),
						'confirm_removing_pin'   => esc_html__( 'Are you sure you want to remove this pin?', 'pin-maker' ),
						'please_input_a_title'   => esc_html__( 'Please input a title for this pin', 'pin-maker' ),
					),
					'product_selector' => array(
						'url'      => admin_url( 'admin-ajax.php?action=woocommerce_json_search_products_and_variations' ),
						'security' => wp_create_nonce( 'search-products' ),
					),
				) );
			}
		}
	}

	/**
	 * Edit style of icons.
	 *
	 * @return  void
	 */
	public static function custom_css_icon() {
		?>
		<style type="text/css">
			#menu-posts-pins .wp-menu-image img {
				width: 22px;
    			padding-top: 5px;
			}
		</style>
		<?php
	}

	/**
	 * Method to alter results of WooCommerce's product search function.
	 *
	 * @param   array  $found_products  Current search results.
	 *
	 * @return  array
	 */
	public static function search_products( $found_products ) {
		// Check if term is a number.
		$id = ( string ) wc_clean( stripslashes( $_GET['term'] ) );

		if ( preg_match( '/^\d+$/', $id ) ) {
			// Get product.
			$product = wc_get_product( ( int ) $id );

			$found_products = array(
				'id' => $id,
				'text' => rawurldecode( str_replace( '&ndash;', ' - ', $product->get_formatted_name() ) ),
			);
		}

		return $found_products;
	}
}

WPA_PM_Admin::init();