<?php
/**
 * Initialize openswatch.
 *
 * @since   1.0.0
 * @package Claue
 */

if ( class_exists( 'ColorSwatch' ) && class_exists( 'WooCommerce' ) ) {
	/**
	 * Change default template folder of openswatch.
	 *
	 * @since 1.0.0
	 */
	
	function jas_claue_os_locate_template( $located, $template_name, $args, $template_path, $default_path ) {
		if ( $template_name == 'single-product/add-to-cart/variable.php' ) {
			global $post;

			$tmp = get_post_meta( $post->ID, '_allow_openswatch', true );
			if ( $tmp != 0 ) {
				return JAS_CLAUE_PATH . '/core/libraries/vendors/openswatch/templates/variable.php';
			}
		}
		return $located;
	}
	add_filter( 'wc_get_template','jas_claue_os_locate_template', 20, 5 );

	/**
	 * Change image variable for openswatch.
	 *
	 * @since 1.0.0
	 */
	function jas_claue_os_images( $images, $productId, $attachment_ids ) {
		// Count attachment
		$attachment_ids   = array_filter( $attachment_ids );
		$attachment_count = count( $attachment_ids );

		if ( $attachment_count > 0 ) {

			// Get page options
			$options = get_post_meta( $productId, '_custom_wc_options', true );

			// Get product single style
			$style = ( isset( $options ) && $options['wc-single-style'] ) ? $options['wc-single-style'] : ( cs_get_option( 'wc-single-style' ) ? cs_get_option( 'wc-single-style' ) : '1' );

			$classes        = array( 'single-product-thumbnail pr' );
			$p_thumb        = array( 'p-thumb images' );
			$thumb_position = ( $options['wc-single-style'] == 1 && $options['wc-thumbnail-position'] ) ? $options['wc-thumbnail-position'] : ( cs_get_option( 'wc-thumbnail-position' ) ? cs_get_option( 'wc-thumbnail-position' ) : 'left' );

			if ( $thumb_position && $style == 1 ) {
				$classes[] = $thumb_position;
			}

			if ( $attachment_count == 0 ) {
				$classes[] = 'no-nav';
			}

			if ( $style == 1 ) {
				$attr = 'data-slick=\'{"slidesToShow": 1, "slidesToScroll": 1, "asNavFor": ".p-nav", "fade":true'. ( is_rtl() ? ',"rtl":true' : '' ) .'}\'';
				$p_thumb[] = 'jas-carousel';
			} elseif ( $style == 2 ) {
				$attr = 'data-masonry=\'{"selector":".p-item", "layoutMode":"masonry"}\'';
				$p_thumb[] = 'jas-masonry';

				if ( $attachment_count < 1 ) {
					$p_thumb[] = 'columns-full';
				}
			}

		?>
			<div class="<?php echo esc_attr( implode( ' ', $p_thumb ) ); ?>" <?php echo wp_kses_post( $attr ); ?>>
				<?php
					if ( $attachment_ids ) {
						foreach ( $attachment_ids as $attachment_id ) {
							$image_link = wp_get_attachment_url( $attachment_id );
							if ( ! $image_link )
								continue;

							$image_title   = esc_attr( get_the_title( $attachment_id ) );
							$image_caption = esc_attr( get_post_field( 'post_excerpt', $attachment_id ) );
							$attr          = array(
								'alt'      => $image_title,
								'data-src' => $image_link
							);

							$image = wp_get_attachment_image(
								$attachment_id,
								apply_filters( 'single_product_small_thumbnail_size', 'shop_single' ), 0,
								$attr
							);

							$zoom = '';
							if ( cs_get_option( 'wc-single-zoom' ) ) {
								$zoom = ' jas-image-zoom';
							}

							echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<div class="p-item' . $zoom . '"><a href="%s" itemprop="image" class="zoom" title="%s" data-rel="prettyPhoto[product-gallery]">%s</a></div>', $image_link, $image_caption, $image ), $attachment_id, $productId );
						}
					}
				?>
			</div>

			<?php

			if ( $style == 1 && $thumb_position != 'outside' ) {
				$thumb_position = ( $options['wc-single-style'] == 1 && $options['wc-thumbnail-position'] ) ? $options['wc-thumbnail-position'] : ( cs_get_option( 'wc-thumbnail-position' ) ? cs_get_option( 'wc-thumbnail-position' ) : 'left' );

				if ( $thumb_position == 'left' || $thumb_position == 'right' ) {
					$attr = ',"vertical": true';
				}

				if ( $thumb_position == 'outside' ) {
					$limit = $attachment_count + 1;
				} else {
					$limit = 4;
				}

				if ( $attachment_ids ) {
					?>
					<div class="p-nav oh jas-carousel<?php if ( $thumb_position == 'outside' ) echo ' p-nav-outside'; ?>" data-slick='{"slidesToShow": <?php echo (int) $limit; ?>,"slidesToScroll": 1,"asNavFor": ".p-thumb","arrows": false, "focusOnSelect": true,<?php if ( $thumb_position == 'left' || $thumb_position == 'right' ) echo '"vertical": true,'; ?> "responsive":[{"breakpoint": 736,"settings":{"slidesToShow": 4, "vertical":false}}]<?php echo ( is_rtl() ? ',"rtl":true' : '' ); ?>}'>
						<?php
							foreach ( $attachment_ids as $attachment_id ) {
								$image_link = wp_get_attachment_url( $attachment_id );

								if ( ! $image_link )
									continue;

								$image_title 	= esc_attr( get_the_title( $attachment_id ) );
								$image_caption 	= esc_attr( get_post_field( 'post_excerpt', $attachment_id ) );

								$image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ), 0, $attr = array(
									'title'	   => $image_title,
									'alt'	   => $image_title,
									'data-src' => $image_link
								) );

								echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<div>%s</div>', $image ), $attachment_id, $productId );
							}
						?>
					</div>
					<?php
				}
			}
		}
	}
	add_filter( 'openswatch_image_swatch_html', 'jas_claue_os_images', 20, 3 );
}