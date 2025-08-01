jQuery(document).ready(function($) {
    // Quick view functionality
    $('.gift-card').on('click', '.quick-view-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var giftId = $(this).data('gift-id');
        
        $.ajax({
            url: fuguku_gift_frontend.ajax_url,
            type: 'POST',
            data: {
                action: 'fuguku_gift_quick_view',
                gift_id: giftId,
                nonce: fuguku_gift_frontend.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Create modal
                    var modal = $('<div class="gift-quick-view-modal">' +
                        '<div class="modal-overlay"></div>' +
                        '<div class="modal-content">' +
                        '<button class="modal-close">×</button>' +
                        response.data.html +
                        '</div>' +
                        '</div>');
                    
                    $('body').append(modal);
                    modal.fadeIn();
                }
            }
        });
    });
    
    // Close modal
    $(document).on('click', '.modal-close, .modal-overlay', function() {
        $('.gift-quick-view-modal').fadeOut(function() {
            $(this).remove();
        });
    });
    
    // Wishlist functionality
    $('.wishlist-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $btn = $(this);
        var giftId = $btn.data('gift-id');
        
        $.ajax({
            url: fuguku_gift_frontend.ajax_url,
            type: 'POST',
            data: {
                action: 'fuguku_gift_toggle_wishlist',
                gift_id: giftId,
                nonce: fuguku_gift_frontend.nonce
            },
            success: function(response) {
                if (response.success) {
                    if (response.data.in_wishlist) {
                        $btn.addClass('in-wishlist').text('♥');
                    } else {
                        $btn.removeClass('in-wishlist').text('♡');
                    }
                }
            }
        });
    });
    
    // Filter functionality
    $('.gift-filter').on('change', function() {
        var category = $('#gift-category-filter').val();
        var price = $('#gift-price-filter').val();
        var availability = $('#gift-availability-filter').val();
        
        $('.gift-card').each(function() {
            var $card = $(this);
            var cardCategory = $card.data('category');
            var cardPrice = parseFloat($card.data('price'));
            var cardAvailability = $card.data('availability');
            
            var showCard = true;
            
            // Category filter
            if (category && cardCategory !== category) {
                showCard = false;
            }
            
            // Price filter
            if (price) {
                var priceRange = price.split('-');
                var minPrice = parseFloat(priceRange[0]);
                var maxPrice = parseFloat(priceRange[1]);
                
                if (cardPrice < minPrice || (maxPrice && cardPrice > maxPrice)) {
                    showCard = false;
                }
            }
            
            // Availability filter
            if (availability && cardAvailability !== availability) {
                showCard = false;
            }
            
            if (showCard) {
                $card.show();
            } else {
                $card.hide();
            }
        });
    });
    
    // Sort functionality
    $('.gift-sort').on('change', function() {
        var sortBy = $(this).val();
        var $container = $('.gift-grid');
        var $cards = $container.find('.gift-card').get();
        
        $cards.sort(function(a, b) {
            var $a = $(a);
            var $b = $(b);
            
            switch (sortBy) {
                case 'price-low':
                    return parseFloat($a.data('price')) - parseFloat($b.data('price'));
                case 'price-high':
                    return parseFloat($b.data('price')) - parseFloat($a.data('price'));
                case 'name':
                    return $a.find('.gift-title').text().localeCompare($b.find('.gift-title').text());
                case 'newest':
                    return new Date($b.data('date')) - new Date($a.data('date'));
                default:
                    return 0;
            }
        });
        
        $container.empty().append($cards);
    });
    
    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
});
