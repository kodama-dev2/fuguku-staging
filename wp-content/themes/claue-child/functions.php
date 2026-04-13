<?php
/**
 * Claue Child Theme Functions
 * 
 * @package Claue Child
 * @since   1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Exclude out-of-stock products from Elementor product queries so sliders never receive OOS slides.
 * (Client-side slickFilter is unreliable for width calculation.)
 *
 * @param array<string,mixed> $args Query args.
 * @return array<string,mixed>
 */
function fuguku_meta_query_exclude_outofstock(array $args): array {
	if (! isset($args['meta_query']) || ! is_array($args['meta_query'])) {
		$args['meta_query'] = [];
	}
	$args['meta_query'][] = [
		'key'     => '_stock_status',
		'value'   => 'outofstock',
		'compare' => '!=',
	];
	return $args;
}

/**
 * Elementor Pro (and similar) product loop query.
 *
 * @param array<string,mixed> $args
 * @param mixed               $widget
 */
add_filter('elementor/query/query_args', function ($args, $widget) {
	if (is_admin()) {
		return $args;
	}
	if (! apply_filters('fuguku_exclude_oos_from_elementor_queries', true, $args, $widget)) {
		return $args;
	}
	$pt = $args['post_type'] ?? null;
	$is_product = ($pt === 'product') || (is_array($pt) && in_array('product', $pt, true));
	if (! $is_product) {
		return $args;
	}
	return fuguku_meta_query_exclude_outofstock($args);
}, 20, 2);

/**
 * WooCommerce [products] shortcode and similar.
 *
 * @param array<string,mixed> $query_args
 */
add_filter('woocommerce_shortcode_products_query', function ($query_args) {
	if (is_admin()) {
		return $query_args;
	}
	if (! apply_filters('fuguku_exclude_oos_from_shortcode_queries', true, $query_args)) {
		return $query_args;
	}
	return fuguku_meta_query_exclude_outofstock($query_args);
}, 20, 1);

/**
 * Enqueue parent and child theme styles
 */
add_action('wp_enqueue_scripts', function() {
	// Parent theme styles
	wp_enqueue_style('claue-parent-style', get_template_directory_uri() . '/style.css', [], null);
	
	// Child theme styles
	wp_enqueue_style('claue-child-style', get_stylesheet_uri(), ['claue-parent-style'], '1.0.1');
}, 20);

/**
 * New Collections: Slick + OOS cleanup — hanya di halaman depan.
 * Script ini cuma menyentuh node di bawah .new-available (bukan popup Elementor).
 * Memuatnya hanya di front page mengurangi bebas konflik di halaman lain.
 */
add_action('wp_enqueue_scripts', function () {
	if ( is_admin() || ! is_front_page() ) {
		return;
	}
	if ( ! apply_filters( 'fuguku_enqueue_new_available_slick', true ) ) {
		return;
	}
	wp_enqueue_script(
		'claue-new-available-slick',
		get_stylesheet_directory_uri() . '/js/new-available-slick.js',
		['jquery'],
		'1.0.2',
		true
	);
}, 9999);

/**
 * Force disable Claue flip image to use our carousel
 */
add_filter('cs_get_option', function($value, $option_name) {
    if ($option_name === 'wc-flip-thumb') {
        return false; // Force disable flip
    }
    // Force add-to-cart behavior to popup to match design
    if ($option_name === 'wc-atc-behavior') {
        return 'popup';
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

// Force body class to popup behavior if theme option drifted
add_filter('body_class', function($classes){
    if (!in_array('jan-atc-behavior-popup', $classes, true)) {
        $classes[] = 'jan-atc-behavior-popup';
    }
    return $classes;
}, 99);

// Fallback JS: bind + / - even if parent script fails to load
add_action('wp_footer', function() {
    ?>
    <script>
    jQuery(function($){
        // Ensure popup behavior fires after default add_to_cart ajax
        $(document).ajaxComplete(function(event, xhr, settings){
            try {
                if (!settings || !settings.url) return;
                if (settings.url.indexOf('add_to_cart') === -1) return;
                if (typeof JASAjaxURL === 'undefined' || !$.magnificPopup) return;
                $.ajax({
                    url: JASAjaxURL,
                    type: 'POST',
                    data: { action: 'jas_claue_popup_content_ajax' },
                    success: function(response){
                        $.magnificPopup.open({
                            items: { src: '<div class="product-quickview cart__popup pr">' + response + '</div>', type: 'inline' },
                            mainClass: 'mfp-fade',
                            removalDelay: 800
                        });
                    }
                });
            } catch(e) { /* noop */ }
        });

        // Intercept single product non-AJAX submit and convert to AJAX to show popup
        $(document).on('submit', 'form.cart', function(e){
            if (!$('body').hasClass('jan-atc-behavior-popup')) return; // respect theme option
            e.preventDefault();
            var $form = $(this);
            var data = $form.serializeArray();
            var addToCart = $form.find('input[name="add-to-cart"]').val();
            if (addToCart) {
                data.push({name: 'product_id', value: addToCart});
            }
            var url = (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.wc_ajax_url)
                ? wc_add_to_cart_params.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart')
                : ($form.attr('action') || window.location.href);
            $.ajax({
                type: 'POST',
                url: url,
                data: $.param(data),
                success: function(response){
                    try {
                        if (response && response.fragments) {
                            $.each(response.fragments, function(k, v){ $(k).replaceWith(v); });
                            $(document.body).trigger('wc_fragments_refreshed');
                        }
                    } catch(err) { /* noop */ }
                    if (typeof JASAjaxURL !== 'undefined' && $.magnificPopup) {
                        $.post(JASAjaxURL, {action: 'jas_claue_popup_content_ajax'}, function(html){
                            $.magnificPopup.open({
                                items: { src: '<div class="product-quickview cart__popup pr">' + html + '</div>', type: 'inline' },
                                mainClass: 'mfp-fade',
                                removalDelay: 800
                            });
                        });
                    }
                }
            });
        });

        $(document.body).on('click', '.quantity .plus', function(e){
            e.preventDefault();
            var $qty  = $(this).closest('.quantity').find('input.input-text, input.qty');
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
            var $qty  = $(this).closest('.quantity').find('input.input-text, input.qty');
            var step  = parseFloat($qty.attr('step')) || 1;
            var min   = parseFloat($qty.attr('min')) || 1;
            var val   = parseFloat($qty.val()) || min;
            var next  = val - step;
            if (next < min) next = min;
            $qty.val(next).trigger('change');
            $('.quantity .plus').css('pointer-events','auto');
        });

        // Re-affirm after Woo fragments or content updates
        $(document.body).on('wc_fragments_refreshed wc_fragments_loaded updated_wc_div', function(){
            // no-op: delegated handlers above already cover future nodes
        });
    });
    </script>
    <?php
});

