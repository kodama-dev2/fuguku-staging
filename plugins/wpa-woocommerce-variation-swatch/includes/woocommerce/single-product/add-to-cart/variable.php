<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see    https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $wpdb;
$attribute_keys = array_keys( $attributes );
// Get gallery data 
$galleries = WPA_WCVS_Frontend::image_galleries( $product->get_id(), $available_variations, $attributes );

do_action( 'woocommerce_before_add_to_cart_form' );

// transform attributes data for client 
$data_attributes = [];
// convert 3 types of attributes format: First Second, First_Second, First to first-second, first_second, first
foreach ($attributes as $key => $value) {
    // convert all keys to lowercase
    $new_key = strtolower($key);
    $new_key = str_replace(" ","-", $new_key);
	$data_attributes[$new_key] = array_values($value);
}
?>
	<form class="variations_form cart" method="post" enctype='multipart/form-data'
		  data-attributes="<?php echo htmlspecialchars( wp_json_encode( $data_attributes ) ) ?>"
		  data-product_id="<?php echo absint( $product->get_id() ); ?>"
		  data-galleries="<?php echo htmlspecialchars( wp_json_encode( $galleries ) ); ?>"
		  data-product_variations="<?php echo htmlspecialchars( wp_json_encode( $available_variations ) ) ?>">
		<?php do_action( 'woocommerce_before_variations_form' ); ?>

		<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
			<p class="stock out-of-stock"><?php _e( 'This product is currently out of stock and unavailable.', 'wcvs' ); ?></p>
		<?php else : ?>
			<div class="variations">
				<?php foreach ( $attributes as $attribute_name => $options ) : ?>
					<?php
					$attr = current(
						$wpdb->get_results(
							"SELECT attribute_type FROM {$wpdb->prefix}woocommerce_attribute_taxonomies " .
							"WHERE attribute_name = '" . substr( $attribute_name, 3 ) . "' LIMIT 0, 1;"
						)
					);
					$custom_attr_type = get_post_meta( $product->get_id(), '_display_type_' . sanitize_title($attribute_name), true );

					// Generate request variable name.
					$key = 'attribute_' . sanitize_title( $attribute_name );

					// Get selected attribute value.
					$selected = isset( $_REQUEST[ $key ] )
						? wc_clean( $_REQUEST[ $key ] )
						: $product->get_variation_default_attribute( $attribute_name );
					?>
				   <div class="swatch is-<?php echo esc_attr( isset( $attr->attribute_type ) ? $attr->attribute_type : $custom_attr_type ); ?>">
						<h4 class="swatch__title"><?php echo wc_attribute_label( $attribute_name ); ?></h4>
						<?php if ( isset( $attr->attribute_type ) && ( $attr->attribute_type == 'color' || $attr->attribute_type == 'label' ) ) : ?>
							<ul class="swatch__list is-flex"
								data-attribute="<?php echo esc_attr( $attribute_name ); ?>">
								<?php
								// Get terms if this is a taxonomy - ordered. We need the names too.
								$terms = wc_get_product_terms( $product->get_id(), $attribute_name, array( 'fields' => 'all' ) );

								foreach ( $terms as $term ) {
									$color   = get_term_meta( $term->term_id, 'wpa_color', true );
									$image   = get_term_meta( $term->term_id, 'wpa_image', true );
									$label   = get_term_meta( $term->term_id, 'wpa_label', true );
									$tooltip = get_term_meta( $term->term_id, 'wpa_tooltip', true );

									$enable_tooltip = WPA_WCVS_Settings::data( 'enable_tooltip' );
									$image_custom   = WPA_WCVS_Frontend::get_image( $term->term_id, $product->get_id() );

									if ( ! empty( $image_custom ) ) {
										$image = $image_custom;
									}

									if ( $image ) {
										$style = 'background-image: url( ' . esc_url( $image ) . ' );';
									} else {
										$style = 'background: ' . esc_attr( $color ) . ';';
									}

									if ( in_array( $term->slug, $options ) ) {

										echo '<li data-variation="' . esc_attr( $term->slug ) . '" data-image-id="" class="swatch__list--item is-relative' . ( $term->slug == $selected ? ' is-selected' : '' ) . '">';
										if ( $attr->attribute_type == 'color' ) {
											echo '<span class="swatch__value" style="' . $style . '"></span>';
										} elseif ( $attr->attribute_type == 'label' ) {
											echo '<span class="swatch__value">';
												if ( $label ) {
													echo esc_attr( $label );
												} else {
													echo esc_attr( $term->name );
												}
											echo '</span>';
										}
										if ( $enable_tooltip == 'yes' ) {
											if ( $tooltip ) {
												echo '<span class="swatch__tooltip is-absolute is-block">' . esc_attr( $tooltip ) . '</span>';
											} else {
												echo '<span class="swatch__tooltip is-absolute is-block">' . esc_attr( $term->name ) . '</span>';
											}
										}
										echo '</li>';
									}
								}
								?>
							</ul>
						<?php elseif ( $custom_attr_type == 'color' || $custom_attr_type == 'label' ) : ?>
							<ul class="swatch__list is-flex" data-attribute="<?php echo esc_attr( strtolower( sanitize_title($attribute_name) ) ); ?>">
								<?php
									foreach ( $options as $attr_value ) {
										$attr_color = get_post_meta( $product->get_id(), 'custom_attr_color_' . sanitize_title( $attr_value ), true );
										$attr_img   = get_post_meta( $product->get_id(), 'custom_attr_img_' . sanitize_title( $attr_value ), true );
										$attr_label = get_post_meta( $product->get_id(), 'custom_attr_label_' . sanitize_title( $attr_value ), true );

										if ( $attr_img ) {
											$style = 'background-image: url( ' . esc_url( wp_get_attachment_url( $attr_img ) ) . ' );';
										} else {
											$style = 'background: ' . $attr_color . ';';
										}
										
										echo '<li data-variation="' . esc_attr( $attr_value ) . '" class="swatch__list--item is-relative' . ( $attr_value == $selected ? ' is-selected' : '' ) . '">';

											if ( $custom_attr_type == 'color' ) {
												echo '<span class="swatch__value" style="' . $style . '"></span>';
											} elseif ( $custom_attr_type == 'label' ) {
												echo '<span class="swatch__value">';
													if ( $attr_label ) {
														echo esc_attr( $attr_label );
													} else {
														echo sanitize_title( $attr_value );
													}
												echo '</span>';
											}
										echo '</li>';
									}
								?>
							</ul>
						<?php endif; ?>

						<div class="value">
							<?php
							$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( stripslashes( urldecode( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) ) : $product->get_variation_default_attribute( $attribute_name );
							

							wc_dropdown_variation_attribute_options( array(
								'options'   => $options,
								'attribute' => sanitize_title($attribute_name),
								'product'   => $product,
								'selected'  => $selected
							) );
							echo end( $attribute_keys ) === $attribute_name ? apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'wcvs' ) . '</a>' ) : '';
							?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="single_variation_wrap">
				<?php
				/**
				 * woocommerce_before_single_variation Hook.
				 */
				do_action( 'woocommerce_before_single_variation' );

				/**
				 * woocommerce_single_variation hook. Used to output the cart button and placeholder for variation data.
				 * @since 2.4.0
				 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
				 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
				do_action( 'woocommerce_single_variation' );

				/**
				 * woocommerce_after_single_variation Hook.
				 */
				do_action( 'woocommerce_after_single_variation' );
				?>
			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_after_variations_form' ); ?>
	</form>

<?php
do_action( 'woocommerce_after_add_to_cart_form' );