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
class WPA_WCPB_Admin {
	/**
	 * Initialize.
	 *
	 * @return  void
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

		// Search product
		add_action( 'wp_ajax_wpa_wcpb_search_product', array( __CLASS__, 'search_product' ) );

		// Add product bundle
		add_action( 'wp_ajax_wpa_wcpb_add_bundle', array( __CLASS__, 'add_bundle' ) );

		// Save data
		add_action( 'save_post_product', array( __CLASS__, 'save_post' ), 10, 1 );

		self::add_product_data();
	}

	/**
	 * Save product metadata when a product is saved.
	 *
	 * @param int $post_id The product ID.
	 */
	public static function save_post( $post_id ) {
		if ( ! empty( $_POST['wpa_wcpb'] ) ) {
			update_post_meta( $post_id, 'wpa_wcpb', $_POST['wpa_wcpb'] );
		} else {
			update_post_meta( $post_id, 'wpa_wcpb', '' );
		}

		if ( ! empty( $_POST['wpa_ebw'] ) ) {
			update_post_meta( $post_id, 'wpa_ebw', $_POST['wpa_ebw'] );
		} else {
			update_post_meta( $post_id, 'wpa_ebw', '' );
		}
	}

	/**
	 * Add product bundle
	 *
	 * @return json
	 */
	public static function add_bundle() {
		// Check nonce
		if ( ! ( isset($_POST['_nonce'] ) && wp_verify_nonce( $_POST['_nonce'], 'wpa-wcpb-nonce') ) ) {
			exit( json_encode( array( 'message' => __( 'The nonce check wrong.', 'wcpb' ) ) ) );
		}

		// Check isset data
		if ( empty( $_POST['product_id'] ) ) {
			exit( json_encode( array( 'message' => __( 'Product not isset.', 'wcpb' ) ) ) );
		}

		$product = wc_get_product( intval( $_POST['product_id'] ) );

		$link_id = intval( $_POST['link_id'] );

		if ( $product ) {
			$price_html = '';
			$price = 0;

			if ( $product->is_type( 'variable' ) ) {
				$attributes = $product->get_variation_attributes();
				$available_variations = $product->get_available_variations();
				$selected_variations = isset( $available_variations[0]['attributes'] ) ? $available_variations[0]['attributes'] : array();

				if ( $attributes ) {
					$attribute_html = '<div class="variations_form" data-product_variations="' . htmlspecialchars( json_encode( $available_variations ) ) . '" data-product_id="' . $product->get_id() . '"><div class="variations">';
						foreach ( $attributes as $attribute => $options ) {
							if ( ! empty( $options ) ) {
								$selected = isset( $selected_variations[ 'attribute_' . $attribute ] ) ? $selected_variations[ 'attribute_' . $attribute ] : '';

								$attribute_html .= '<select data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '"' . '" name="wpa_wcpb[' . $link_id . '][variable][attribute_' . esc_attr( $attribute ) . ']">';

								// $attribute_html .= '<option value="">' . __( 'Choose an option', 'wcpb' ) . '</option>';

								if ( $product && taxonomy_exists( $attribute ) ) {
									// Get terms if this is a taxonomy - ordered. We need the names too.
									$terms = wc_get_product_terms($product->get_id(), $attribute, array('fields' => 'all'));

									foreach ($terms as $term) {
										if ( in_array( $term->slug, $options ) ) {
											$attribute_html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $selected ), $term->slug, false ) . '>' . esc_html($term->name) . '</option>';
										}
									}
								} else {
									foreach ( $options as $option ) {
										// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
										$selected = sanitize_title( $selected ) === $selected ? selected( $selected, sanitize_title( $option ), false ) : selected( $selected, $option, false );

										$attribute_html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $option ) . '</option>';
									}
								}
								$attribute_html .= '</select>';
							}
						}
					$attribute_html .= '</div></div>';
				}

				if ( isset( $available_variations[0]['display_price'] ) ) {
					$price = $available_variations[0]['display_price'];
					$price_html = wc_price( $price );
				}

			} else {
				$price = $product->get_price();
				$price_html = wc_price( $product->get_price() );
			}

			$data = '
				<tr data-link="' . $link_id . '">
					<td class="name">' . esc_html( $product->get_title() ) . '</td>
					<td class="image">' . $product->get_image( array( 70, 70 ) ) . '</td>
					<td class="attrs"> ' . $attribute_html . '</td>
					<td class="price">' . $price_html . '</td>
					<td class="remove"><span>' . __( 'Remove', 'wcpb' ) . '</span><input type="hidden" name="wpa_wcpb[' . $link_id . '][product_id]" value="' . $product->get_id() . '" /></td>
				</tr>
			';

			exit( json_encode( array( 'status' => 'true', 'data' => $data, 'price' => $price ) ) );
		}

		exit( json_encode( array( 'message' => __( 'Wrong.', 'wcpb' ) ) ) );

		die;
	}

	/**
	 * Search products
	 *
	 * @return json
	 */
	public static function search_product() {
		// Check nonce
		if ( ! ( isset( $_POST['_nonce'] ) && wp_verify_nonce( $_POST['_nonce'], 'wpa-wcpb-nonce') ) ) {
			exit( json_encode( array( 'message' => __( 'The nonce check wrong.', 'wcpb' ) ) ) );
		}

		// Check isset data
		if ( ! isset($_POST['keyword'] ) ) {
			exit( json_encode( array( 'message' => __( 'Data not isset.', 'wcpb' ) ) ) );
		}

		$keyword = $_POST['keyword'];

		$products = new WP_Query(
			array(
				'post_type'        => 'product',
				'post_status'      => 'publish',
				's'                => $keyword,
				'orderby'          => 'post_title',
				'order'            => 'DESC',
				'posts_per_page'   => 10,
				'suppress_filters' => true,
			)
		);

		$data_return = array();

		if ( $products->have_posts() ) {

			foreach ( $products->posts as $product ) {
				$product = wc_get_product( $product );

				$data_return['list_product'][] = array(
					'id'    => $product->get_id(),
					'title' => $product->get_title(),
					'image' => $product->get_image( array( 50, 50 ) ),
					'price' => $product->get_price_html(),
				);
			}

			exit( json_encode( $data_return ) );
		}

		exit( json_encode( array( 'message' => __( 'No results.', 'wcpb' ) ) ) );

		die;
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
			wp_enqueue_script( 'wpa-wcpb-variation', WPA_WCPB_URL . 'assets/vendors/add-to-cart-variation.js', array(), NULL, true);

			wp_enqueue_script( 'wpa-wcpb-backend', WPA_WCPB_URL . 'assets/js/wcpb-backend.js', array(), NULL, true );
			wp_enqueue_style( 'wpa-wcpb-backend', WPA_WCPB_URL . 'assets/css/wcpb-backend.css' );

			wp_localize_script( 'wpa-wcpb-backend', 'wpa_wcpb', self::localize_script() );
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
			'_nonce'  => wp_create_nonce( 'wpa-wcpb-nonce' ),
		);
	}

	/**
	 * Add product data tab and panel.
	 *
	 * @return  void
	 */
	public static function add_product_data() {
		add_filter( 'woocommerce_product_data_tabs', array( __CLASS__, 'tabs' ) );
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'panels' ) );
	}

	/**
	 * Add product data tab.
	 *
	 * @param $tabs
	 *
	 * @return  array
	 */
	public static function tabs($tabs) {
		$tabs['wpa_wcpb'] = array(
			'label'  => __( 'Product Bundles', 'wcpb' ),
			'target' => 'wpa_wcpb_product_data',
			'class'  => array(),
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
		global $post;

		$data = get_post_meta( $post->ID, 'wpa_wcpb', true );
		$widget_edit = get_post_meta( $post->ID, 'wpa_ebw', true );

		$check_enable = isset( $widget_edit['check_enable'] ) ? 'on' : 'off';
		?>

		<div id="wpa_wcpb_product_data" class="panel woocommerce_options_panel hidden">

			<div class="group">
				<div class="wrap">
					<div class="search-product">
						<input type="text" class="txt-search" placeholder="<?php esc_html_e( 'Enter product name', 'wcpb' ); ?>" />
					</div>
				</div>
			</div>

			<div class="group <?php if ( ! $data ) { echo 'hidden'; } ?>" >
				<div class="title"><?php esc_html_e( 'Select products to include in the bundle', 'wcpb'); ?></div>
				<div class="wrap">
					<table class="list-prouduct"><tbody>
						<?php
							$saved = array();
							if ( $data ) {
								foreach( $data as $link_id => $val ){
									$product_id = ( isset( $val['product_id'] ) ) ? intval( $val['product_id'] ) : 0;
									$product = wc_get_product( $product_id );
									if ( $product ) {
										$price_html = $attribute_html = $available_variations = '';
										$price = 0;

										if ( $product->is_type( 'variable' ) ) {
											$attributes = $product->get_variation_attributes();
											$variable   = wp_unslash( $val['variable'] );
											if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
												$variation_id = $product->get_matching_variation( $variable );
											} else {
												$data_store   = WC_Data_Store::load( 'product' );
												$variation_id = $data_store->find_matching_product_variation( $product, $variable );
											}

											if ( $variation_id ) {
												$available_variation = $product->get_available_variation( $variation_id );
												$selected_variations = $variable;
											}

											if ( $attributes ) {
												$available_variations = $product->get_available_variations();

												$attribute_html = '<div class="variations_form" data-product_id="' . $product->get_id() . '" data-product_variations="' . htmlspecialchars( json_encode( $available_variations ) ) . '"><div class="variations">';

												foreach ( $attributes as $attribute => $options ) {
													if ( !empty( $options ) ) {
														$selected = isset( $selected_variations[ 'attribute_' . $attribute ] ) ? $selected_variations[ 'attribute_' . $attribute ] : '';

														$attribute_html .= '<select data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '"' . '" name="wpa_wcpb[' . $link_id . '][variable][attribute_' . esc_attr( $attribute ) . ']">';

														// $attribute_html .= '<option value="">' . __( 'Choose an option', 'wcpb' ) . '</option>';

														if ( $product && taxonomy_exists( $attribute ) ) {
															// Get terms if this is a taxonomy - ordered. We need the names too.
															$terms = wc_get_product_terms($product->get_id(), $attribute, array('fields' => 'all'));

															foreach ($terms as $term) {
																if ( in_array( $term->slug, $options ) ) {
																	$attribute_html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $selected ), $term->slug, false ) . '>' . esc_html($term->name) . '</option>';
																}
															}
														} else {
															foreach ( $options as $option ) {
																// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
																$selected = sanitize_title( $selected ) === $selected ? selected( $selected, sanitize_title( $option ), false ) : selected( $selected, $option, false );

																$attribute_html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $option ) . '</option>';
															}
														}
														$attribute_html .= '</select>';
													}
												}
												$attribute_html .= '</div></div>';
											}

											if( isset( $available_variation['display_price'] ) ) {
												$price = $available_variation['display_price'];
												$price_html = wc_price( $price );
											}

										} else {
											$price = $product->get_price();
											$price_html = wc_price( $price );
										}

										$image = $product->get_image( array( 50, 50 ) );

										$saved[ $link_id ][ 'name' ] = $product->get_title();
										$saved[ $link_id ][ 'image' ] = $image;
										$saved[ $link_id ][ 'price' ] = $price;
										$saved[ $link_id ][ 'percent' ] = $val['percent'];

										$data = '
											<tr data-link="' . $link_id . '">
												<td class="name">' . esc_html( $product->get_title() ) . '</td>
												<td class="image">' . $image . '</td>
												<td class="attrs"> ' . $attribute_html . '</td>
												<td class="price">' . $price_html . '</td>
												<td class="remove"><span>' . __( 'Remove', 'wcpb' ) . '</span><input type="hidden" name="wpa_wcpb[' . $link_id . '][product_id]" value="' . $product->get_id() . '" /></td>
											</tr>
										';
										echo $data;
									}
								}
							}else {
								$main_product = wc_get_product( $post->ID );
								// var_dump($main_product);
								$image = $main_product->get_image( array( 50, 50 ) );
								$attribute_html = $price_html = '';
								if ( $main_product->is_type( 'variable' ) ) {
									$attributes = $main_product->get_variation_attributes();
									$variable   = wp_unslash( isset($val['variable']) ? $val['variable'] : null );
									if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
										$variation_id = $main_product->get_matching_variation( $variable );
									} else {
										$data_store   = WC_Data_Store::load( 'product' );
										$variation_id = $data_store->find_matching_product_variation( $main_product, $variable ? $variable : [] );

									}

									if ( $variation_id ) {
										$available_variation = $main_product->get_available_variation( $variation_id );
										$selected_variations = $variable;
									}

									if ( $attributes ) {
										$available_variations = $main_product->get_available_variations();

										$attribute_html = '<div class="variations_form" data-product_id="' . $main_product->get_id() . '" data-product_variations="' . htmlspecialchars( json_encode( $available_variations ) ) . '"><div class="variations">';

										foreach ( $attributes as $attribute => $options ) {
											if ( !empty( $options ) ) {
												$selected = isset( $selected_variations[ 'attribute_' . $attribute ] ) ? $selected_variations[ 'attribute_' . $attribute ] : '';

												$attribute_html .= '<select data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '"' . '" name="wpa_wcpb[' . $main_product->get_id() . '][variable][attribute_' . esc_attr( $attribute ) . ']">';

												// $attribute_html .= '<option value="">' . __( 'Choose an option', 'wcpb' ) . '</option>';

												if ( $main_product && taxonomy_exists( $attribute ) ) {
													// Get terms if this is a taxonomy - ordered. We need the names too.
													$terms = wc_get_product_terms($main_product->get_id(), $attribute, array('fields' => 'all'));

													foreach ($terms as $term) {
														if ( in_array( $term->slug, $options ) ) {
															$attribute_html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $selected ), $term->slug, false ) . '>' . esc_html($term->name) . '</option>';
														}
													}
												} else {
													foreach ( $options as $option ) {
														// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
														$selected = sanitize_title( $selected ) === $selected ? selected( $selected, sanitize_title( $option ), false ) : selected( $selected, $option, false );

														$attribute_html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $option ) . '</option>';
													}
												}
												$attribute_html .= '</select>';
											}
										}
										$attribute_html .= '</div></div>';
									}

									if( isset( $available_variation['display_price'] ) ) {
										$price = $available_variation['display_price'];
										$price_html = wc_price( $price );
									}

								} else {
									$price = $main_product->get_price();
									$price_html = wc_price( $price );
								}
								$html = '
									<tr data-link="' . $main_product->get_ID() . '">
										<td class="name">' . esc_html( $main_product->get_title() ) . '</td>
										<td class="image">' . $image . '</td>
										<td class="attrs"> ' . $attribute_html . '</td>
										<td class="price">' . $price_html . '</td>
										<td class="remove"></td>
									</tr>
								';
								echo $html;
							}
						?>
					</tbody></table>
				</div>
			</div>

			<div class="group <?php if( ! $data ) { echo 'hidden'; } ?>">
				<div class="title"><?php esc_html_e( 'Bundle saved', 'wcpb' ); ?></div>
				<div class="des"><?php esc_html_e( 'You can only specify a discount percentage. The discount percent will be applied to total price', 'wcpb' ); ?></div>
				<div class="wrap">
					<table class="saved">
						<tr class="saved-img">
							<th><?php esc_html_e( 'Bundle', 'wcpb' ); ?></th>
							<td>
								<div class="img flx alc">
									<?php
										$image = ! empty( $post->ID ) ?  get_the_post_thumbnail( intval( $post->ID ), array( 50, 50 ) ) : NULL;

										if ( ! $image ) {
											$image = '<img width="50" height="50" src="' . wc_placeholder_img_src() . '" class="attachment-70x70 size-70x70 wp-post-image" />';
										}
										echo $image;
									?>
									<span class="plus">+</span>
								</div>
								<div class="name"><?php esc_html_e( 'This product', 'wcpb' ); ?></div>
							</td>

							<?php
								if( $saved ) {
									foreach( $saved as $link_id => $val ){
							?>
										<td data-link="<?php echo $link_id; ?>">
											<div class="img flx alc">
												<?php echo $val['image']; ?>
												<span class="plus">+</span>
											</div>
											<div class="name"><?php echo $val['name']; ?></div>
										</td>
							<?php
									}
								}
							?>

						</tr>
						<tr class="saved-percent">
							<th><?php 
								if ( WPA_WCPB_Settings::get_product_bundle_type() == 'total-discount' ) {
									esc_html_e( 'Enter Discount on total product ', 'wcpb' );
								} else {
									esc_html_e( 'Enter Discount per item', 'wcpb' );
								}
							?> (%)</th>
							<td></td>
						   <?php
								if ( $saved ) {
									foreach( $saved as $link_id => $val ){
							?>
									   <td data-link="<?php echo intval( $link_id ); ?>"><input data-price="<?php echo esc_html( $val['price'] ); ?>" name="wpa_wcpb[<?php echo intval( $link_id ); ?>][percent]" step="any" type="number" value="<?php echo esc_html( $val['percent'] ); ?>" /></td>
							<?php
									}
								}
							?>
						</tr>
						<?php if ( WPA_WCPB_Settings::get_product_bundle_type() == 'discount-per-item' ) :?>
							<tr class="saved-amount">
								<th><?php esc_html_e( 'Amount off', 'wcpb' ); ?> ($)</th>
								<td></td>
								<?php
									if ( $saved ) {
										foreach( $saved as $link_id => $val ) {
								?>
										   <td data-link="<?php echo $link_id; ?>"><input step="any" type="number" value="<?php $percent = $val['percent'] * $val['price']/100; if( $percent ) { echo $percent; } ?>" /></td>
								<?php
										}
									}
								?>
							</tr>
						<?php endif;?>
					</table>
				</div>
			</div>

			<div class="group">
				<div class="title"><?php esc_html_e( 'Edit bundle title and description', 'wcpb'); ?></div>
				<div class="wrap">
					<div class="bundle-title-desc-opt">
						<input type="checkbox" <?php checked( $check_enable, 'on' )?> name="wpa_ebw[check_enable]" id="wpa_ebw_enable" class="check-title-desc" /> <label style="float: none; width: auto; margin: 0;" for="wpa_ebw_enable"><?php esc_html_e( 'When you enable this option. This setting will orverride global setting of plugin at Settings > Product Bundle', 'wcpb'); ?></label>
					</div>
				</div>
			</div>	

			<div class="group bundle-title-desc-edit <?php echo ( $check_enable == 'off' || !isset( $check_enable ) ) ? 'hidden' : '';?>">
				<div class="wrap">
					<table width="100%">
						<tr>
							<td width="30%"><strong><?php esc_html_e( 'Bundles Widget Title', 'wcpb'); ?></strong></td>
							<td><input type="text" value="<?php echo isset( $widget_edit['title'] ) ? $widget_edit['title'] : ''; ?>" class="bundle-title short" name="wpa_ebw[title]" placeholder="<?php esc_html_e( 'Buy this bundle and get 25% off', 'wcpb' ); ?>" /></td>
						</tr>
						<tr>
							<td width="30%"><strong><?php esc_html_e( 'Bundles Description', 'wcpb'); ?><strong></td>
							<td><textarea class="bundle-desc short" name="wpa_ebw[description]" placeholder="<?php esc_html_e( 'Buy more save more. Save 15% when you purchase 4 products, save 10% when you purchase 3 products', 'wcpb' ); ?>"><?php echo isset( $widget_edit['description'] ) ? $widget_edit['description'] : ''; ?></textarea></td>
						</tr>
					</table>
				</div>
			</div>	

		<?php do_action('woocommerce_product_options_wpa_wcpb'); ?>

		</div>
		<?php
	}
}

if ( is_admin() ) {
	WPA_WCPB_Admin::init();
}