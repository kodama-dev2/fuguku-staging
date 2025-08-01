<?php
/**
 * Product double image.
 *
 * @since   2.1.9
 * @package Claue
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $jassc;

// Flip thumbnail
$flip_thumb = $jassc ? $jassc['flip'] : cs_get_option( 'wc-flip-thumb' );

// Sale badge
echo woocommerce_show_product_loop_sale_flash();

// Get attachment id
if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
	$attachment_ids = $product->get_gallery_attachment_ids();
} else {
	$attachment_ids = $product->get_gallery_image_ids();
}

// Get image size
$woocommerce_thumbnail = wc_get_image_size( 'woocommerce_thumbnail' );
$woocommerce_thumbnail = [
    (int)$woocommerce_thumbnail['width'],
    (int)$woocommerce_thumbnail['height'],
    (int)$woocommerce_thumbnail['crop'],
];

echo '<div class="product-image-flip">';
	// Get first image
	if ( has_post_thumbnail() ) {

        $post_thumbnail_id = get_post_thumbnail_id( $post );

		$image_title = esc_attr( get_the_title( $post_thumbnail_id ) );

		$image       = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', $woocommerce_thumbnail ), array(
			'title'	=> $image_title,
			'alt'	=> $image_title
		) );

		$link = get_the_permalink();

		echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s">%s</a>', $link, $image ), $post->ID );

	} else {
		echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), esc_html__( 'Placeholder', 'claue' ) ), $post->ID );
	}

	// Get a secondary image
	if ( isset( $attachment_ids[0] ) ) {

		$attachment_id = $attachment_ids[0];

		$title = get_the_title();
		$link  = get_the_permalink();
		$image = wp_get_attachment_image( $attachment_id, $woocommerce_thumbnail, '', array( 'class' => 'wp-image-flip' ) );
        $image2 = apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<a href="%s" class="db pa back" title="%s">%s</a>', $link, $title, $image ), $attachment_id, $post->ID );

		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<a href="%s" class="db pa back" title="%s">%s</a>', $link, $title, $image ), $attachment_id, $post->ID );
	}
echo '</div>';
if ( ! $flip_thumb ) {
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' );
}