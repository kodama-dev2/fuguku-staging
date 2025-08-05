/**
 * Product Revamp Layout JavaScript
 * Version: 2.3.8
 * Description: Disable zoom hover for revamp layout, enable click-only zoom, fix multiple lightbox issue with simple modal, update button styling, pill shape buttons, replace text with icons
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
        
        // AGGRESSIVE FIX: Remove ALL existing click handlers
        $('.wc-single-revamp .product-revamp-images-container').off();
        $('.wc-single-revamp .product-revamp-images-container a').off();
        $('.wc-single-revamp .product-revamp-images-container .woocommerce-product-gallery__image').off();
        
        // Remove all lightbox attributes
        $('.wc-single-revamp .product-revamp-images-container a').removeAttr('data-rel');
        $('.wc-single-revamp .product-revamp-images-container a').removeClass('zoom');
        $('.wc-single-revamp .product-revamp-images-container a').removeAttr('rel');
        
        // Disable PrettyPhoto completely for revamp
        if (typeof $.fn.prettyPhoto !== 'undefined') {
            $('.wc-single-revamp .product-revamp-images-container a').off('click.prettyphoto');
        }
        
        // Initialize simple click handler for revamp layout
        initSimpleImageClick();
        
        // Override theme zoom function for revamp layout
        if (typeof JAS_Data_Js !== 'undefined' && JAS_Data_Js['wc-single-zoom']) {
            // Disable zoom for revamp layout specifically
            $('.wc-single-revamp .product-revamp-images-container').off('mouseenter mouseleave');
        }
    }
});

// Simple click handler for revamp layout (no fullscreen, fix multiple lightbox)
function initSimpleImageClick() {
    var $ = jQuery;
    var $gallery = $('.wc-single-revamp .product-revamp-images-container');
    
    if (!$gallery.length) return;
    
    // Remove ALL existing click handlers completely
    $gallery.find('a').off();
    
    // Add simple, single click handler
    $gallery.on('click.revamp-simple', 'a', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        var $clicked = $(this);
        var imageUrl = $clicked.attr('href');
        var $img = $clicked.find('img');
        var title = $img.attr('alt') || '';
        
        // Close any existing lightboxes first
        if (typeof $.prettyPhoto !== 'undefined') {
            $.prettyPhoto.close();
        }
        
        // Simple modal overlay
        createSimpleModal(imageUrl, title);
        
        return false;
    });
}

// Create simple modal for image viewing
function createSimpleModal(imageUrl, title) {
    var $ = jQuery;
    
    // Remove existing modal if any
    $('.revamp-image-modal').remove();
    
    // Create simple modal HTML
    var modalHtml = `
        <div class="revamp-image-modal">
            <div class="revamp-modal-overlay"></div>
            <div class="revamp-modal-content">
                <button class="revamp-modal-close">&times;</button>
                <img src="${imageUrl}" alt="${title}" />
                <div class="revamp-modal-title">${title}</div>
            </div>
        </div>
    `;
    
    // Add to body
    $('body').append(modalHtml);
    
    // Show modal
    setTimeout(function() {
        $('.revamp-image-modal').addClass('show');
    }, 10);
    
    // Close handlers
    $('.revamp-modal-close, .revamp-modal-overlay').on('click', function(e) {
        e.preventDefault();
        closeSimpleModal();
    });
    
    // ESC key close
    $(document).on('keydown.revamp-modal', function(e) {
        if (e.keyCode === 27) {
            closeSimpleModal();
        }
    });
}

// Close simple modal
function closeSimpleModal() {
    var $ = jQuery;
    $('.revamp-image-modal').removeClass('show');
    setTimeout(function() {
        $('.revamp-image-modal').remove();
        $(document).off('keydown.revamp-modal');
    }, 300);
}

// Additional safety to prevent zoom hover on variation changes
jQuery(document).on('found_variation', function() {
    if (jQuery('.wc-single-revamp').length) {
        setTimeout(function() {
            jQuery('.wc-single-revamp .product-revamp-images-container .woocommerce-product-gallery__image').each(function() {
                jQuery(this).off('mouseenter mouseleave');
                jQuery(this).find('.zoomImg').remove();
            });
            
            // Re-initialize simple click handler after variation change
            initSimpleImageClick();
        }, 100);
    }
});