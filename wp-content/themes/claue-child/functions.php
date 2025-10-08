<?php
/**
 * Claue Child Theme Functions
 * 
 * @package Claue Child
 * @since   1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Enqueue parent and child theme styles
 */
add_action('wp_enqueue_scripts', function() {
	// Parent theme styles
	wp_enqueue_style('claue-parent-style', get_template_directory_uri() . '/style.css', [], null);
	
	// Child theme styles
	wp_enqueue_style('claue-child-style', get_stylesheet_uri(), ['claue-parent-style'], '1.0.0');
}, 20);

/**
 * Force disable Claue flip image to use our carousel
 */
add_filter('cs_get_option', function($value, $option_name) {
    if ($option_name === 'wc-flip-thumb') {
        return false; // Force disable flip
    }
    return $value;
}, 10, 2);

/**
 * Initialize carousel on product archive hover
 */
add_action('wp_footer', function() {
	if (!is_shop() && !is_product_category() && !is_product_tag()) {
		return;
	}
	?>
	<script>
	jQuery(document).ready(function($) {
		// Initialize carousel on first hover for each product
		$('.product').one('mouseenter', function() {
			var $carousel = $(this).find('.product-image-carousel .jas-carousel');
			
			if ($carousel.length && !$carousel.hasClass('slick-initialized')) {
				$carousel.slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					arrows: true,
					dots: false,
					infinite: true,
					speed: 300,
					fade: false,
					cssEase: 'ease',
					prevArrow: '<button type="button" class="slick-prev"><i class="fa fa-chevron-left"></i></button>',
					nextArrow: '<button type="button" class="slick-next"><i class="fa fa-chevron-right"></i></button>'
				});
			}
		});
	});
	</script>
	<?php
}, 99);

/**
 * Direct DOM manipulation to replace flip with carousel
 */
add_action('wp_footer', function() {
	if (!is_shop() && !is_product_category() && !is_product_tag()) {
		return;
	}
	?>
	<script>
	jQuery(document).ready(function($) {
		// Wait for page to fully load
		setTimeout(function() {
			$('.product-image-flip').each(function() {
				var $flip = $(this);
				var $product = $flip.closest('.product');
				
				// Get all images in this flip container
				var images = [];
				$flip.find('img').each(function() {
					var $img = $(this);
					var src = $img.attr('src') || $img.attr('data-src') || $img.attr('data-lazy');
					var alt = $img.attr('alt') || '';
					
					// Skip if no valid src or already added
					if (!src || src.indexOf('data:image') === 0 || images.some(function(img) { return img.src === src; })) {
						return;
					}
					
					images.push({src: src, alt: alt});
				});
				
				// Only create carousel if multiple images
				if (images.length > 1) {
					var productUrl = $flip.find('a').first().attr('href');
					var carouselHtml = '<div class="product-image-carousel"><div class="jas-carousel">';
					
					images.forEach(function(img) {
						carouselHtml += '<div class="carousel-slide"><a href="' + productUrl + '"><img src="' + img.src + '" alt="' + img.alt + '" loading="lazy" style="width:100%;height:auto;" /></a></div>';
					});
					
					carouselHtml += '</div></div>';
					
					// Add carousel to flip container
					$flip.append(carouselHtml);
					$flip.addClass('has-carousel'); // Mark that this flip has carousel
					var $carousel = $flip.find('.jas-carousel');
					
					// Initialize carousel immediately
					$carousel.slick({
						slidesToShow: 1,
						slidesToScroll: 1,
						arrows: true,
						dots: false,
						infinite: true,
						speed: 300,
						prevArrow: '<button type="button" class="slick-prev"><i class="fa fa-chevron-left"></i></button>',
						nextArrow: '<button type="button" class="slick-next"><i class="fa fa-chevron-right"></i></button>'
					});
					
					// CSS will handle show/hide on hover
				}
			});
		}, 1000); // Wait 1 second for all images to load
	});
	</script>
	<?php
}, 100);

// Maximum quantity options added directly to parent theme framework.config.php

/**
 * Set maximum quantity based on theme options
 */
add_filter('woocommerce_quantity_input_args', function($args, $product) {
	if (cs_get_option('wc-max-quantity-enable')) {
		$max_qty = cs_get_option('wc-max-quantity-limit', 5);
		$args['max_value'] = $max_qty;
	}
	return $args;
}, 10, 2);

/**
 * Validate cart quantity based on theme options
 */
add_filter('woocommerce_add_to_cart_validation', function($passed, $product_id, $quantity) {
	if (!cs_get_option('wc-max-quantity-enable')) {
		return $passed;
	}
	
	$max_qty = cs_get_option('wc-max-quantity-limit', 5);
	if ($quantity > $max_qty) {
		wc_add_notice(sprintf('Maximum %d items allowed per product.', $max_qty), 'error');
		return false;
	}
	return $passed;
}, 10, 3);

/**
 * Check cart total quantity per product based on theme options
 */
add_filter('woocommerce_add_to_cart_validation', function($passed, $product_id, $quantity) {
	if (!cs_get_option('wc-max-quantity-enable')) {
		return $passed;
	}
	
	$max_qty = cs_get_option('wc-max-quantity-limit', 5);
	$cart_item_quantities = WC()->cart->get_cart_item_quantities();
	$existing_quantity = isset($cart_item_quantities[$product_id]) ? $cart_item_quantities[$product_id] : 0;
	$total_quantity = $existing_quantity + $quantity;
	
	if ($total_quantity > $max_qty) {
		$remaining = $max_qty - $existing_quantity;
		if ($remaining > 0) {
			wc_add_notice(sprintf('You can only add %d more of this item (maximum %d total).', $remaining, $max_qty), 'error');
		} else {
			wc_add_notice(sprintf('Maximum %d items already in cart for this product.', $max_qty), 'error');
		}
		return false;
	}
	return $passed;
}, 20, 3);

