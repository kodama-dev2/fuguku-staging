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

// Max quantity is now configured in parent theme options (framework.config.php)

// Ensure quantity input has sane defaults and respects parent options
add_filter('woocommerce_quantity_input_args', function($args, $product) {
    $enabled = function_exists('cs_get_option') ? (bool) cs_get_option('wc-max-quantity-enable') : true;
    if ($enabled && function_exists('cs_get_option')) {
        $max = (int) cs_get_option('wc-max-quantity-limit', 5);
        if ($max > 0) {
            $args['max_value'] = $max;
        }
    }
    // Hard defaults to avoid 0/NaN on some setups
    $args['min_value'] = isset($args['min_value']) && (int)$args['min_value'] > 0 ? (int)$args['min_value'] : 1;
    $args['step']      = isset($args['step']) && (int)$args['step'] > 0 ? (int)$args['step'] : 1;
    if (empty($args['input_value']) || (int)$args['input_value'] < $args['min_value']) {
        $args['input_value'] = $args['min_value'];
    }
    return $args;
}, 10, 2);

// Fallback JS: bind + / - even if parent script fails to load
add_action('wp_footer', function() {
    ?>
    <script>
    jQuery(function($){
        $(document.body).on('click', '.quantity .plus', function(e){
            e.preventDefault();
            var $qty  = $(this).closest('.quantity').find('input.qty');
            var step  = parseFloat($qty.attr('step')) || 1;
            var max   = parseFloat($qty.attr('max'));
            var val   = parseFloat($qty.val()) || 0;
            var next  = val + step;
            $('.quantity .plus').css('pointer-events','auto');
            if (!isNaN(max) && max > 0 && next > max) return;
            $qty.val(next).trigger('change');
        });
        $(document.body).on('click', '.quantity .minus', function(e){
            e.preventDefault();
            var $qty  = $(this).closest('.quantity').find('input.qty');
            var step  = parseFloat($qty.attr('step')) || 1;
            var min   = parseFloat($qty.attr('min')) || 1;
            var val   = parseFloat($qty.val()) || min;
            var next  = val - step;
            if (next < min) next = min;
            $qty.val(next).trigger('change');
            $('.quantity .plus').css('pointer-events','auto');
        });
    });
    </script>
    <?php
});

