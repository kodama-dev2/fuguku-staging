<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $woocommerce, $product;

$attachment_count = count( $product->get_gallery_image_ids() );

// Get page options
$options = get_post_meta( get_the_ID(), '_custom_wc_options', true );

// Get product single style
$style = ( is_array( $options ) && $options['wc-single-style'] ) ? $options['wc-single-style'] : ( cs_get_option( 'wc-single-style' ) ? cs_get_option( 'wc-single-style' ) : '1' );

$classes        = array( 'single-product-thumbnail pr' );
$p_thumb        = array( 'p-thumb images woocommerce-product-gallery' );
$thumb_position = ( is_array( $options ) && $options['wc-single-style'] == 1 && $options['wc-thumbnail-position'] ) ? $options['wc-thumbnail-position'] : ( cs_get_option( 'wc-thumbnail-position' ) ? cs_get_option( 'wc-thumbnail-position' ) : 'left' );

if ( $thumb_position && $style == 1 ) {
	$classes[] = $thumb_position;
}

if ( $attachment_count == 0 ) {
	$classes[] = 'no-nav';
}

if ( $style == 1 ) {
	$attr = 'data-slick=\'{"slidesToShow": 1, "slidesToScroll": 1, "adaptiveHeight":true, "asNavFor": ".p-nav", "fade":true'. ( is_rtl() ? ',"rtl":true' : '' ) .'}\'';
	$p_thumb[] = 'jas-carousel';
} elseif ( $style == 2 ) {
	$attr = 'data-masonry=\'{"selector":".p-item", "layoutMode":"masonry"' .( is_rtl() ? ',"rtl": false' : ',"rtl": true' ) .'}\'';
	$p_thumb[] = 'jas-masonry';

	if ( $attachment_count < 1 ) {
		$p_thumb[] = 'columns-full';
	}
} else {
	if ( wp_is_mobile() ) {
		$attr = 'data-slick=\'{"responsive":[{"breakpoint": 736,"settings":{"slidesToShow": 1, "vertical": false, "arrows": true, "dots":true}}]'. ( is_rtl() ? ',"rtl":true' : '' ) .'}\'';
		$p_thumb[] = 'jas-carousel';
	} else {
		$attr = '';
	}
}
?>
<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="<?php echo esc_attr( implode( ' ', $p_thumb ) ); ?>" <?php echo wp_kses_post( $attr ); ?>>
		<?php
			if ( has_post_thumbnail() ) {
				$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
				$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
				$thumbnail_post    = get_post( $post_thumbnail_id );
				$image_title       = $thumbnail_post->post_content;

				$attributes = array(
					'title'                   => $image_title,
					'data-caption'            => get_post_field( 'post_excerpt', $post_thumbnail_id ),
					'data-src'                => $full_size_image[0],
					'data-large_image'        => $full_size_image[0],
					'data-large_image_width'  => $full_size_image[1],
					'data-large_image_height' => $full_size_image[2],
				);

				$zoom = '';
				if ( cs_get_option( 'wc-single-zoom' ) ) {
					$zoom = ' jas-image-zoom';
				}

				$html  = '<div data-thumb="' . get_the_post_thumbnail_url( $post->ID, 'shop_thumbnail' ) . '" class="p-item woocommerce-product-gallery__image' . $zoom . '"><a href="' . esc_url( $full_size_image[0] ) . '">';
					$html .= get_the_post_thumbnail( $post->ID, 'shop_single', $attributes );
				$html .= '</a></div>';

			} else {
				$html  = '<div class="woocommerce-product-gallery__image">';
					$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'claue' ) );
				$html .= '</div>';
			}

			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, get_post_thumbnail_id( $post->ID ) );

			$attachment_ids = $product->get_gallery_image_ids();

			if ( $attachment_ids ) {
				foreach ( $attachment_ids as $attachment_id ) {
					if ($post_thumbnail_id == $attachment_id) continue;
					$full_size_image  = wp_get_attachment_image_src( $attachment_id, 'full' );
					$thumbnail        = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );
					$thumbnail_post   = get_post( $attachment_id );
					$image_title      = $thumbnail_post->post_content;

					$attributes = array(
						'title'                   => $image_title,
						'data-caption'            => get_post_field( 'post_excerpt', $post_thumbnail_id ),
						'data-src'                => $full_size_image[0],
						'data-large_image'        => $full_size_image[0],
						'data-large_image_width'  => $full_size_image[1],
						'data-large_image_height' => $full_size_image[2],
					);

					$html = '<div class="p-item woocommerce-product-gallery__image' . $zoom . '">';
						$html .= '<a href="' . esc_url( $full_size_image[0] ) . '">';
							$html .= wp_get_attachment_image( $attachment_id, 'shop_single', false, $attributes );
						$html .= '</a>';
					$html .= '</div>';

					echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $attachment_id );
				}
			}
		?>
	</div>

	<?php
		if ( $style == 1 && $thumb_position != 'outside' ) {
			do_action( 'woocommerce_product_thumbnails' );
		}
	?>

	<?php if ( isset( $options ) && ! empty( $options['wc-single-video-upload'] ) || ! empty( $options['wc-single-video-url'] ) ) : ?>
		<div class="p-video pa">
			<?php
				if ( $options['wc-single-video'] == 'url' ) {
					echo '<a href="' . esc_url( $options['wc-single-video-url'] ) . '" class="br__40 pl__30 pr__30 tc db bghp jas-popup-url"><i class="pe-7s-play pr"></i>' . esc_html__( 'View Video', 'claue' ) . '</a>';
				} else {
					echo '<a href="#jas-vsh" class="br__40 pl__20 pr__20 tc db bghp jas-popup-mp4"><i class="pe-7s-play pr"></i>' . esc_html__( 'View Video', 'claue' ) . '</a>';
					echo '<div id="jas-vsh" class="mfp-hide">' . do_shortcode( '[video src="' . esc_url( $options['wc-single-video-upload'] ) . '" width="640" height="320"]' ) . '</div>';
				}
			?>
		</div>
	<?php endif; ?>
</div>