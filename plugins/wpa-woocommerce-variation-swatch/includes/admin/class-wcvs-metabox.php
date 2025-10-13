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
class WPA_WCVS_Metabox {
	/**
	 * Initialize.
	 *
	 * @return  void
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add'  ) );
		add_action( 'save_post'     , array( __CLASS__, 'save' ) );
	}

	/**
	 * Register meta boxes for adding product variation gallery images.
	 *
	 * @return  void
	 */
	public static function add() {
		global $post;

		if ( 'product' == $post->post_type ) {
			// Get product details.
			$product = function_exists( 'wc_get_product' ) ? wc_get_product( $post ) : get_product( $post );

			// If product is variable, check if it has any variation using Color Picker attribute.
			if ( $product->is_type( 'variable' ) ) {
				// Get all product variation attributes.
				$attributes = $product->get_variation_attributes();
				$attribute  = maybe_unserialize( get_post_meta( $post->ID, '_product_attributes', true ) );
				
				// Loop thru attributes to check if Color Picker is used.
				foreach ($attribute as $key => $single_attribute) {
					$color = false;
					if (!$single_attribute['is_taxonomy']) {
						$display_type_name = '_display_type_' . sanitize_title($key);
						$color = get_post_meta($post->ID, $display_type_name, true) == 'color' ? true : false;
					} else {
						global $wpdb;
					
						$attr = current(
							$wpdb->get_results(
								"SELECT attribute_type FROM {$wpdb->prefix}woocommerce_attribute_taxonomies " .
								"WHERE attribute_name = '" . substr( $single_attribute['name'], 3 ) . "' LIMIT 0, 1;"
							)
						);

						$color = $attr && ( 'color' == $attr->attribute_type ) ? true : false;
					}

					if ( $color ) {
						// Color Picker attribute is used in product variation, add meta boxes for selecting variation images.
						if (isset($attributes[$single_attribute['name']])) {
							if (is_array($attributes[$single_attribute['name']])) {
								foreach ( $attributes[$single_attribute['name']] as $option ) {
									add_meta_box(
										sprintf( "product-gallery-color-%s", $option ),
										ucwords( sprintf( esc_html__( 'Product Gallery %s Color', 'wcvs' ), urldecode( $option ) ) ),
										array( __CLASS__, 'display' ),
										'product',
										'side',
										'low',
										array( 'name' => sanitize_title($single_attribute['name']), 'value' => sanitize_title($option) )
									);
								}
							}
						}
					}
				}

				add_filter( 'woocommerce_product_data_tabs', array( __CLASS__, 'tabs' ) );
				add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'panels' ) );
			}
		}
	}

	/**
	 * Display meta box to select gallery images for product variation.
	 *
	 * @param   WP_Post  $post    WordPress's post object.
	 * @param   object   $params  Meta box parameters.
	 *
	 * @return  void
	 */
	public static function display( $post, $params ) {
		// Generate meta key to get product variation gallery.
		$meta_key = strtolower("_product_image_gallery_{$params['args']['name']}-{$params['args']['value']}");
		// Print nonce field once.
		static $nonce_field_printed;

		if ( ! isset( $nonce_field_printed ) ) {
			wp_nonce_field( 'wpa_wcvs_save_product_variation_gallery_images', 'wpa_wcvs_save_product_variation_gallery_images' );

			$nonce_field_printed = true;
		}
		?>
		<div class="product_variation_images_container">
			<ul class="product_images">
				<?php
				// Get product variation gallery.
				$product_image_gallery = get_post_meta( $post->ID, $meta_key, true );
				$attachments           = array_filter( explode( ',', $product_image_gallery ) );
				$update_meta           = false;
				$updated_gallery_ids   = array();
				
				if ( ! empty( $attachments ) ) {
					foreach ( $attachments as $attachment_id ) {
						$attachment = wp_get_attachment_image( $attachment_id, 'thumbnail' );

						if ( empty( $attachment ) ) {
							$update_meta = true;
							continue;
						}
				?>
				<li class="image" data-attachment_id="<?php echo esc_attr( $attachment_id ); ?>">
					<?php echo '' . $attachment; ?>
					<ul class="actions">
						<li>
							<a href="#" class="delete tips" data-tip="<?php
								esc_attr_e( 'Delete image', 'wcvs' );
							?>">
								<?php esc_html_e( 'Delete', 'wcvs' ); ?>
							</a>
						</li>
					</ul>
				</li>
				<?php
						// Rebuild ids to be saved.
						$updated_gallery_ids[] = $attachment_id;
					}

					// Need to update product meta to set new gallery ids.
					if ( $update_meta ) {
						update_post_meta( $post->ID, $meta_key, implode( ',', $updated_gallery_ids ) );
					}
				}
				?>
			</ul>

			<input type="hidden" class="product_variation_image_gallery" name="<?php echo esc_attr( $meta_key ); ?>" value="<?php
				echo esc_attr( $product_image_gallery );
			?>" />
		</div>
		<p class="add_product_variation_images hide-if-no-js">
			<a href="#" data-choose="<?php
				echo esc_attr( sprintf(
					__( 'Add Images to Product Gallery %s', 'wcvs' ),
					urldecode( ucfirst( $params['args']['value'] ) )
				) );
			?>" data-update="<?php
				esc_attr_e( 'Add to gallery', 'wcvs' );
			?>" data-delete="<?php
				esc_attr_e( 'Delete image', 'wcvs' );
			?>" data-text="<?php
				esc_attr_e( 'Delete', 'wcvs' );
			?>">
				<?php _e( 'Add product gallery images', 'wcvs' ); ?>
			</a>
		</p>
		<?php
	}

	/**
	 * Save meta boxes.
	 *
	 * @param   int  $post_id  The ID of the post being saved.
	 *
	 * @return  void
	 */
	public static function save( $post_id ) {
		$attribute  = maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );
		
		if ( ! empty( $attribute ) ) {
			
			foreach ( $attribute as $key => $single_attribute ) {
				if ( isset( $single_attribute ) && ! $single_attribute['is_taxonomy'] ) {
					$custom_attrs = explode( ' | ', $single_attribute['value'] );
					$display_type_name = '_display_type_' . sanitize_title($single_attribute['name']);
					$display_type = $_POST[$display_type_name];
					
					if ( ! empty( $display_type ) ) {
						update_post_meta( $post_id, $display_type_name, esc_attr( $display_type ) );
					}
					
					foreach ( $custom_attrs as $custom_attr ) {
						$custom_attr = strtolower(sanitize_title($custom_attr));
						$custom_attr_color = $_POST['custom_attr_color_' . $custom_attr];

						if (isset($_POST['custom_attr_color_' . $custom_attr]) && ! empty( $_POST['custom_attr_color_' . $custom_attr] ) ) {
							$custom_attr_color = $_POST['custom_attr_color_' . $custom_attr];
							update_post_meta( $post_id, 'custom_attr_color_' . $custom_attr, esc_attr( $custom_attr_color ) );
						}

						if (isset($_POST['custom_attr_img_' . $custom_attr]) && ! empty( $_POST['custom_attr_img_' . $custom_attr] ) ) {
							$custom_attr_img = $_POST['custom_attr_img_' . $custom_attr];
							update_post_meta( $post_id, 'custom_attr_img_' . $custom_attr, esc_attr( $custom_attr_img ) );
						}

						if (isset($_POST['custom_attr_label_' . $custom_attr]) && ! empty( $_POST['custom_attr_label_' . $custom_attr] ) ) {
							$custom_attr_label = $_POST['custom_attr_label_' . $custom_attr];
							update_post_meta( $post_id, 'custom_attr_label_' . $custom_attr, esc_attr( $custom_attr_label ) );
						}
					}
				}
			}
		}

		// Check if our nonce is set.
		if ( ! isset( $_POST['wpa_wcvs_save_product_variation_gallery_images'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['wpa_wcvs_save_product_variation_gallery_images'], 'wpa_wcvs_save_product_variation_gallery_images' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( ! ( current_user_can( 'edit_post', $post_id ) || current_user_can( 'edit_posts' ) || current_user_can( 'manage_woocommerce' ) ) ) {
			return;
		}
		
		// Sanitize and save meta boxes.
		foreach ( $_POST as $k => $v ) {
			if ( 0 === strpos( $k, '_product_image_gallery_' ) ) {
				// Sanitize user input.
				$v = implode( ',', array_map( 'trim', explode( ',', $v ) ) );

				// Update the meta field in the database.
				update_post_meta( $post_id, $k, $v );
			}
		}

		if ( ! empty( $_POST['wpa_wcvs_attrs'] ) ) {
			update_post_meta( $post_id, 'wpa_wcvs_attrs', $_POST['wpa_wcvs_attrs'] );
		} else {
			update_post_meta( $post_id, 'wpa_wcvs_attrs', '' );
		}
	}

	/**
	 * Add product data tab.
	 *
	 * @param $tabs
	 *
	 * @return  array
	 */
	public static function tabs( $tabs ) {
		$tabs['wpa_wcvs'] = array(
			'label'    => esc_html__( 'Variation Swatch', 'wcpb' ),
			'target'   => 'wpa_wcvs_product_data',
			'class'    => 'show_if_variable',
			'priority' => 70
		);

		return $tabs;
	}

	/**
	 * Add product data panel.
	 *
	 * @param $tabs
	 *
	 * @return  string
	 */
	public static function panels() {
		global $post, $thepostid;

		$product    = function_exists( 'wc_get_product' ) ? wc_get_product( $post ) : get_product( $post );
		$attributes = $product->get_variation_attributes();
		$attribute  = maybe_unserialize( get_post_meta( $thepostid, '_product_attributes', true ) );
		
		if ( ! empty( $attributes ) ) {
			echo '<div id="wpa_wcvs_product_data" class="panel woocommerce_options_panel hidden">';
			if ( ! empty( $attribute ) ) {

				$empty_taxonomy = array_column( $attribute, 'is_taxonomy' );

				if ( in_array( 0, $empty_taxonomy, true )  ) {
					echo '<h3>' . esc_html__( 'Setting for attribute of this product', 'wcvs' ) . '</h3>';
					echo sprintf( '<span class="description">This setting affect for the attribute of this product only. By default it\'s select</span>', 'wcvs' );
				}

				foreach ( $attribute as $key => $single_attribute ) {
					if ( isset( $single_attribute ) && ! $single_attribute['is_taxonomy'] ) {
						$custom_attrs = explode( ' | ', $single_attribute['value'] );

							woocommerce_wp_select( 
								array( 
									'id'          => '_display_type_' . sanitize_title($single_attribute['name']),
									'label'       => esc_html__( 'Attribute type for ' . $single_attribute['name'], 'wcvs' ),
									'desc_tip'    => true,
									'description' => esc_html__( 'Click Update button to see the attributes.', 'wcvs' ),
									'options'     => array(
										'default' => esc_html__( 'Select', 'wcvs' ),
										'color'   => esc_html__( 'Color', 'wcvs' ),
										'label'   => esc_html__( 'Label', 'wcvs' )
									)
								)
							);
							
							$display_type = get_post_meta( $post->ID, '_display_type_' . sanitize_title($single_attribute['name']), true );

							if ( $display_type !== 'default' ) {
								foreach ( $custom_attrs as $custom_attr ) {
								$custom_attr_color  = get_post_meta( $post->ID, 'custom_attr_color_' . sanitize_title( $custom_attr ), true );
								$custom_attr_img_id = get_post_meta( $post->ID, 'custom_attr_img_' . sanitize_title( $custom_attr ), true );
								$custom_attr_label  = get_post_meta( $post->ID, 'custom_attr_label_' . sanitize_title( $custom_attr ), true );

								echo '<p class="form-field wcvs-default-field">';
									echo '<label for="custom_attr_color"><strong>' . $custom_attr . '</strong></label>';
									if ( $display_type !== 'label' ) {
										echo '<span class="wcvs-color-field">';
											echo '<input type="text" class="wcvs-color-picker" name="custom_attr_color_' . sanitize_title( $custom_attr ) . '" id="custom_attr_color_' . sanitize_title( $custom_attr ) . '" value="' . $custom_attr_color . '" />';

											if ( isset( $custom_attr_img_id ) && $custom_attr_img_id ) {
												$image = wp_get_attachment_thumb_url( $custom_attr_img_id );
												$hidden = '';
											} else {
												$image = wc_placeholder_img_src();
												$hidden = ' hidden';
											}

											echo '
												<span class="wpa-swatch">
													<a href="#" class="wpa-wcvs-btn-upload" data-choose="' . esc_html__( 'Add image', 'wcvs' ) . '" data-update="' . esc_html__( 'Update image', 'wcvs' ) . '" data-delete="' . esc_html__( 'Delete image', 'wcvs' ) . '" data-text="' . esc_html__( 'Delete', 'wcvs' ) . '">
														<img src="' . esc_url( $image ) . '" />
														<span data-thumb="' . esc_url( wc_placeholder_img_src() ) . '" class="wpa-wcvs-btn-remove dashicons dashicons-no-alt' . esc_attr( $hidden ) . '"></span>
													</a>
													<input type="hidden" class="wpa_wcvs_thumb_id" value="" name="custom_attr_img_' . sanitize_title( $custom_attr ) . '" />
												</span>
											';
										echo '</span>';
									} else {
										echo '
											<span class="wcvs-label-field">
												<input type="text" class="short" name="custom_attr_label_' . sanitize_title( $custom_attr ) . '" id="custom_attr_label_' . sanitize_title( $custom_attr ) . '" value="' . $custom_attr_label . '" placeholder="Label" />
											</span>
										';
									}

								echo '</p>';
							}
							?>
							<script type="text/javascript">
								(function($) {
									$(document).ready(function() {
										$( '.wcvs-color-picker' ).wpColorPicker();
									});
								})(jQuery);
							</script>

							<?php
						}
					}
				}
			}
			foreach ( $attributes as $attribute_name => $options ) {
				// Get custom attribute type.
				global $wpdb;

				$attr = current(
					$wpdb->get_results(
						"SELECT attribute_type FROM {$wpdb->prefix}woocommerce_attribute_taxonomies " .
						"WHERE attribute_name = '" . substr( $attribute_name, 3 ) . "' LIMIT 0, 1;"
					)
				);
				
				if ( $attr && ( 'color' == $attr->attribute_type ) && isset( $attribute[$attribute_name] ) ) {
					$terms = wc_get_product_terms( $product->get_id(), $attribute_name, array( 'fields' => 'all' ) );

					$attr_swatch = get_post_meta( $post->ID, 'wpa_wcvs_attrs', true );

					echo '<h3>' . esc_html__( 'Custom image for variation of global attribute', 'wcvs' ) . '</h3>';
					echo sprintf( '<span class="description">This setting will be overridden in global settings. <a target="_blank" href="%s">Click here</a> if you want to change global settings</span>', admin_url( 'edit-tags.php?taxonomy=' . $attribute_name . '&post_type=product' ) );

					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $options ) ) {
							if ( $attr_swatch ) {
								$thumb_id = $attr_swatch[esc_attr( $term->taxonomy )][$term->term_id];
							}

							if ( isset( $thumb_id ) && $thumb_id ) {
								$image = wp_get_attachment_thumb_url( $thumb_id );
								$hidden = '';
							} else {
								$image = wc_placeholder_img_src();
								$hidden = ' hidden';
							}
						?>
							<p class="form-field">
								<label><strong><?php echo sanitize_text_field( $term->name ) ; ?></strong></label>
								<span class="wpa-swatch">
									<a href="#" class="wpa-wcvs-btn-upload" data-choose="<?php esc_attr_e( 'Add image', 'wcvs' ); ?>" data-update="<?php esc_attr_e( 'Update image', 'wcvs' ); ?>" data-delete="<?php esc_attr_e( 'Delete image', 'wcvs' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'wcvs' ); ?>">
										<img src="<?php echo esc_url( $image ); ?>">
										<span data-thumb="<?php echo esc_url( wc_placeholder_img_src() ); ?>" class="wpa-wcvs-btn-remove dashicons dashicons-no-alt<?php echo esc_attr( $hidden ); ?>"></span>
									</a>
									<input type="hidden" name="is_attribute" value="1">
									<input type="hidden" class="wpa_wcvs_thumb_id" value="<?php echo isset( $thumb_id ) ? $thumb_id : ''; ?>" name="wpa_wcvs_attrs[<?php echo esc_attr( $term->taxonomy );?>][<?php echo absint( $term->term_id ); ?>]" />
								</span>
							</p>
							<?php
						}
					}
				}
			}
			echo '</div>';
		} else {
			echo '<div id="wpa_wcvs_product_data" class="panel woocommerce_options_panel hidden">';
				echo '<div class="inline notice woocommerce-message">';
					echo '<p>' . esc_html__( 'Before you can add a variation you need to add some variation attributes on the Attributes tab.', 'wcvs' ) . '</p>';
				echo '</div>';
			echo '</div>';
		}
	}
}

if ( is_admin() ) {
	WPA_WCVS_Metabox::init();
}