/**
 * Product Revamp Layout JavaScript
 * Version: 2.3.3
 * Description: Disable zoom hover for revamp layout, enable click-only zoom
 */

jQuery(document).ready(function($) {
    // Check if we're on revamp layout
    if ($('.wc-single-revamp').length) {
        
        // Disable zoom hover initialization for revamp layout
        $(document).off('woocommerce_gallery_init_zoom');
        
        // Remove existing zoom functionality on revamp layout
        $('.wc-single-revamp .product-revamp-images-container .woocommerce-product-gallery__image').each(function() {
            var $this = $(this);
            
            // Remove zoom hover event
            $this.off('mouseenter mouseleave');
            
            // Remove zoomImg elements
            $this.find('.zoomImg').remove();
            
            // Disable zoom initialization
            if ($this.data('zoom')) {
                $this.data('zoom', null);
            }
        });
        
        // Override zoom initialization for revamp layout
        $(document).on('woocommerce_gallery_init_zoom', function() {
            if ($('.wc-single-revamp').length) {
                // Prevent zoom initialization on revamp layout
                $('.wc-single-revamp .product-revamp-images-container .woocommerce-product-gallery__image').each(function() {
                    $(this).off('mouseenter mouseleave');
                    $(this).find('.zoomImg').remove();
                });
            }
        });
        
        // Ensure PhotoSwipe still works for click zoom
        if (typeof PhotoSwipe !== 'undefined') {
            console.log('PhotoSwipe available for click zoom');
        }
        
        // Override theme zoom function for revamp layout
        if (typeof JAS_Data_Js !== 'undefined' && JAS_Data_Js['wc-single-zoom']) {
            // Disable zoom for revamp layout specifically
            $('.wc-single-revamp .product-revamp-images-container').off('mouseenter mouseleave');
        }
    }
});

// Additional safety to prevent zoom hover on variation changes
jQuery(document).on('found_variation', function() {
    if (jQuery('.wc-single-revamp').length) {
        setTimeout(function() {
            jQuery('.wc-single-revamp .product-revamp-images-container .woocommerce-product-gallery__image').each(function() {
                jQuery(this).off('mouseenter mouseleave');
                jQuery(this).find('.zoomImg').remove();
            });
        }, 100);
    }
});