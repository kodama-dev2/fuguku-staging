<?php
/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

$attachment_ids   = $product->get_gallery_image_ids();
$attachment_count = count( $attachment_ids );

// Get page options
$options = get_post_meta( get_the_ID(), '_custom_wc_options', true );

$thumb_position = ( is_array( $options ) && $options['wc-single-style'] == 1 && $options['wc-thumbnail-position'] ) ? $options['wc-thumbnail-position'] : ( cs_get_option( 'wc-thumbnail-position' ) ? cs_get_option( 'wc-thumbnail-position' ) : 'left' );

if ( $thumb_position == 'outside' ) {
	$limit = $attachment_count + 1;
} else {
	$limit = 4;
}

if ( $attachment_ids && has_post_thumbnail() ) {
	?>
	<div class="p-nav oh jas-carousel<?php if ( $thumb_position == 'outside' ) echo ' p-nav-outside'; ?>" data-slick='{"slidesToShow": 4,"slidesToScroll": 1,"asNavFor": ".p-thumb","arrows": false, "focusOnSelect": true, <?php if ( $thumb_position == 'left' || $thumb_position == 'right' ) echo '"vertical": true,'; ?> <?php if ( $thumb_position == 'outside' ) echo ( is_rtl() ? '"rtl":true,' : '' ); ?> "responsive":[{"breakpoint": 736,"settings":{"slidesToShow": 4, "vertical":false<?php echo ( is_rtl() ? ',"rtl":true' : '' ); ?>}}]}'>
		<?php
		    $post_thumb_id = get_post_thumbnail_id();
		    $full_size_image  = wp_get_attachment_image_src( $post_thumb_id, 'full' );
		    $attributes = array(
				'title'	=> get_the_title( $post_thumb_id ),
				'data-src'                => $full_size_image[0],
				'data-large_image'        => $full_size_image[0],
				'data-large_image_width'  => $full_size_image[1],
				'data-large_image_height' => $full_size_image[2],
			);
			$image = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'thumbnail' ), $attributes );
            
			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<div>%s</div>', $image ), $post->ID );

			foreach ( $attachment_ids as $attachment_id ) {
			    if ($post_thumb_id == $attachment_id) continue;
				$full_size_image  = wp_get_attachment_image_src( $attachment_id, 'full' );
				$thumbnail        = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
				$thumbnail_post   = get_post( $attachment_id );
				$image_title      = $thumbnail_post->post_content;

				$attributes = array(
					'title'                   => $image_title,
					'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
					'data-src'                => $full_size_image[0],
					'data-large_image'        => $full_size_image[0],
					'data-large_image_width'  => $full_size_image[1],
					'data-large_image_height' => $full_size_image[2],
				);
                
				$html = '<div>' . wp_get_attachment_image( $attachment_id, 'thumbnail', false, $attributes ) . '</div>';

				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $attachment_id );
			}
		?>
	</div>
	<?php
}
