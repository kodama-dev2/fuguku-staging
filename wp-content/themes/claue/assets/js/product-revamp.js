/**
 * Product Revamp Layout JavaScript
 * Version: 2.3.3
 * Description: Disable zoom hover for revamp layout, enable click-only zoom, fix PhotoSwipe issues
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
        
        // Fix PhotoSwipe multiple lightbox issue
        $('.wc-single-revamp .product-revamp-images-container .woocommerce-product-gallery__image a').off('click.prettyphoto click.photoswipe');
        
        // Disable PrettyPhoto for revamp layout
        $('.wc-single-revamp .product-revamp-images-container a[data-rel^="prettyPhoto"]').removeAttr('data-rel');
        $('.wc-single-revamp .product-revamp-images-container a.zoom').removeClass('zoom');
        
        // Initialize proper PhotoSwipe for revamp layout
        initRevampPhotoSwipe();
        
        // Override theme zoom function for revamp layout
        if (typeof JAS_Data_Js !== 'undefined' && JAS_Data_Js['wc-single-zoom']) {
            // Disable zoom for revamp layout specifically
            $('.wc-single-revamp .product-revamp-images-container').off('mouseenter mouseleave');
        }
    }
});

// Initialize PhotoSwipe for revamp layout
function initRevampPhotoSwipe() {
    if (typeof PhotoSwipe === 'undefined') return;
    
    var $ = jQuery;
    var $gallery = $('.wc-single-revamp .product-revamp-images-container');
    
    if (!$gallery.length) return;
    
    // Remove any existing click handlers
    $gallery.find('a').off('click.photoswipe');
    
    // Add single click handler for PhotoSwipe
    $gallery.on('click.photoswipe', 'a', function(e) {
        e.preventDefault();
        
        var $clicked = $(this);
        var $images = $gallery.find('a');
        var items = [];
        var index = 0;
        
        // Build PhotoSwipe items array
        $images.each(function(i) {
            var $img = $(this).find('img');
            var src = $(this).attr('href');
            var width = $img.data('large_image_width') || 1200;
            var height = $img.data('large_image_height') || 800;
            var title = $img.attr('alt') || '';
            
            items.push({
                src: src,
                w: parseInt(width),
                h: parseInt(height),
                title: title
            });
            
            if (this === $clicked[0]) {
                index = i;
            }
        });
        
        // PhotoSwipe options
        var options = {
            index: index,
            bgOpacity: 0.9,
            showHideOpacity: true,
            shareEl: false,
            fullscreenEl: true,
            zoomEl: true,
            tapToClose: false,
            tapToToggleControls: true,
            closeOnScroll: false,
            history: false,
            galleryUID: 'product-revamp-gallery',
            // Make image fill entire lightbox
            imageScriptSrc: false,
            showAnimationDuration: 333,
            hideAnimationDuration: 333,
            // Custom spacing to remove borders
            spacing: 0,
            allowPanToNext: false,
            // Custom UI
            barsSize: {top: 0, bottom: 0},
            captionEl: false,
            counterEl: true,
            arrowEl: true,
            preloaderEl: true
        };
        
        // Create PhotoSwipe gallery element
        var pswpElement = $('.pswp')[0];
        if (!pswpElement) {
            // Create PhotoSwipe HTML if not exists
            $('body').append(`
                <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="pswp__bg"></div>
                    <div class="pswp__scroll-wrap">
                        <div class="pswp__container">
                            <div class="pswp__item"></div>
                            <div class="pswp__item"></div>
                            <div class="pswp__item"></div>
                        </div>
                        <div class="pswp__ui pswp__ui--hidden">
                            <div class="pswp__top-bar">
                                <div class="pswp__counter"></div>
                                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                                <div class="pswp__preloader">
                                    <div class="pswp__preloader__icn">
                                        <div class="pswp__preloader__cut">
                                            <div class="pswp__preloader__donut"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
                            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
                        </div>
                    </div>
                </div>
            `);
            pswpElement = $('.pswp')[0];
        }
        
        // Initialize PhotoSwipe
        var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
        gallery.init();
        
        // Custom styling for fullscreen
        gallery.listen('afterChange', function() {
            $('.pswp__img').css({
                'object-fit': 'contain',
                'width': '100%',
                'height': '100%'
            });
        });
    });
}

// Additional safety to prevent zoom hover on variation changes
jQuery(document).on('found_variation', function() {
    if (jQuery('.wc-single-revamp').length) {
        setTimeout(function() {
            jQuery('.wc-single-revamp .product-revamp-images-container .woocommerce-product-gallery__image').each(function() {
                jQuery(this).off('mouseenter mouseleave');
                jQuery(this).find('.zoomImg').remove();
            });
            
            // Re-initialize PhotoSwipe after variation change
            initRevampPhotoSwipe();
        }, 100);
    }
});