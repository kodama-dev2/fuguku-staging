<?php
/**
 * Product Carousel Image (Child Theme Override)
 * Shows product gallery as a carousel on hover
 *
 * @package Claue Child
 * @since   1.0.0
 */

defined('ABSPATH') || exit;

global $product;

// Sale badge
echo woocommerce_show_product_loop_sale_flash();

// Get gallery image IDs
$attachment_ids = method_exists($product, 'get_gallery_image_ids') 
	? $product->get_gallery_image_ids() 
	: [];

// Get thumbnail size
$woocommerce_thumbnail = wc_get_image_size('woocommerce_thumbnail');
$thumb_size = [
	(int)$woocommerce_thumbnail['width'],
	(int)$woocommerce_thumbnail['height'],
	(int)$woocommerce_thumbnail['crop'],
];

$link = get_the_permalink();
$post_id = get_the_ID();

echo '<div class="product-image-carousel">';
echo '<div class="jas-carousel">';

// Featured image slide
if (has_post_thumbnail()) {
	$post_thumbnail_id = get_post_thumbnail_id($post_id);
	$image_title = esc_attr(get_the_title($post_thumbnail_id));
	$image = get_the_post_thumbnail($post_id, $thumb_size, [
		'title' => $image_title,
		'alt'   => $image_title
	]);
	
	echo '<div class="carousel-slide">';
	echo sprintf('<a href="%s">%s</a>', esc_url($link), $image);
	echo '</div>';
}

// Gallery slides
if (!empty($attachment_ids)) {
	foreach ($attachment_ids as $att_id) {
		$image = wp_get_attachment_image($att_id, $thumb_size);
		
		echo '<div class="carousel-slide">';
		echo sprintf('<a href="%s">%s</a>', esc_url($link), $image);
		echo '</div>';
	}
}

echo '</div>'; // .jas-carousel
echo '</div>'; // .product-image-carousel

