/**
 * Gifts AJAX JavaScript
 * 
 * @package Fuguku_Gifts
 * @version 1.0.0
 */

(function($) {
    'use strict';

    // Gifts AJAX Handler
    var FugukuGifts = {
        
        // Configuration
        config: {
            ajaxUrl: fuguku_gifts_ajax.ajax_url,
            nonce: fuguku_gifts_ajax.nonce,
            currentPage: 1,
            loading: false,
            searchTimeout: null,
        },

        // Initialize
        init: function() {
            this.bindEvents();
            this.initPriceSlider();
            this.initSearchAutocomplete();
            this.initWishlistButtons();
        },

        // Bind Events
        bindEvents: function() {
            var self = this;

            // Filter events
            $(document).on('change', '.gift-filter-select', function() {
                self.filterGifts();
            });

            $(document).on('click', '.filter-dropdown-content a', function(e) {
                e.preventDefault();
                var filterType = $(this).closest('.filter-dropdown').find('.filter-btn').data('filter');
                var filterValue = $(this).data(filterType);
                
                // Update active state
                $(this).closest('.filter-dropdown-content').find('a').removeClass('active');
                $(this).addClass('active');
                
                self.filterGifts();
            });

            // Search events
            $(document).on('input', '.gift-search-input', function() {
                clearTimeout(self.config.searchTimeout);
                self.config.searchTimeout = setTimeout(function() {
                    self.filterGifts();
                }, 500);
            });

            // Load more events
            $(document).on('click', '.load-more-btn', function(e) {
                e.preventDefault();
                self.loadMoreGifts();
            });

            // Clear filters
            $(document).on('click', '.filter-clear', function(e) {
                e.preventDefault();
                self.clearFilters();
            });

            // Wishlist events
            $(document).on('click', '.gift-wishlist-btn', function(e) {
                e.preventDefault();
                var giftId = $(this).data('gift-id');
                self.toggleWishlist(giftId, $(this));
            });
        },

        // Filter Gifts
        filterGifts: function() {
            var self = this;
            var filters = this.getFilters();
            
            if (self.config.loading) return;
            
            self.config.loading = true;
            self.showLoading();

            $.ajax({
                url: self.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'fuguku_filter_gifts',
                    nonce: self.config.nonce,
                    category: filters.category,
                    price_range: filters.price_range,
                    availability: filters.availability,
                    featured: filters.featured,
                    search: filters.search,
                    page: 1,
                    per_page: 12
                },
                success: function(response) {
                    if (response.success) {
                        $('.gifts-grid').html(response.data.html);
                        self.updateResultsCount(response.data.found);
                        self.config.currentPage = 1;
                        
                        // Update load more button
                        if (response.pagination.has_more) {
                            $('.load-more-btn').show();
                        } else {
                            $('.load-more-btn').hide();
                        }
                    }
                },
                error: function() {
                    console.log('Filter request failed');
                },
                complete: function() {
                    self.config.loading = false;
                    self.hideLoading();
                }
            });
        },

        // Load More Gifts
        loadMoreGifts: function() {
            var self = this;
            var filters = this.getFilters();
            
            if (self.config.loading) return;
            
            self.config.loading = true;
            self.showLoadMoreLoading();

            $.ajax({
                url: self.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'fuguku_load_more_gifts',
                    nonce: self.config.nonce,
                    page: self.config.currentPage + 1,
                    per_page: 12
                },
                success: function(response) {
                    if (response.success) {
                        $('.gifts-grid').append(response.data.html);
                        self.config.currentPage++;
                        
                        if (!response.pagination.has_more) {
                            $('.load-more-btn').hide();
                        }
                    }
                },
                error: function() {
                    console.log('Load more request failed');
                },
                complete: function() {
                    self.config.loading = false;
                    self.hideLoadMoreLoading();
                }
            });
        },

        // Get Filters
        getFilters: function() {
            return {
                category: $('.filter-dropdown-content a[data-category].active').data('category') || '',
                price_range: $('.filter-dropdown-content a[data-price].active').data('price') || '',
                availability: $('.filter-dropdown-content a[data-availability].active').data('availability') || '',
                featured: $('.filter-dropdown-content a[data-featured].active').data('featured') || '',
                search: $('.gift-search-input').val() || ''
            };
        },

        // Clear Filters
        clearFilters: function() {
            $('.gift-search-input').val('');
            $('.filter-dropdown-content a').removeClass('active');
            $('.filter-dropdown-content a[data-category=""]').addClass('active');
            $('.filter-dropdown-content a[data-price=""]').addClass('active');
            $('.filter-dropdown-content a[data-availability=""]').addClass('active');
            $('.filter-dropdown-content a[data-featured=""]').addClass('active');
            
            this.filterGifts();
        },

        // Toggle Wishlist
        toggleWishlist: function(giftId, button) {
            var self = this;
            var isInWishlist = button.hasClass('in-wishlist');
            var action = isInWishlist ? 'fuguku_remove_from_wishlist' : 'fuguku_add_to_wishlist';

            $.ajax({
                url: self.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: action,
                    nonce: self.config.nonce,
                    gift_id: giftId
                },
                success: function(response) {
                    if (response.success) {
                        if (response.in_wishlist) {
                            button.addClass('in-wishlist');
                            button.find('.wishlist-text').text('Remove from Wishlist');
                            button.find('i').removeClass('fa-heart-o').addClass('fa-heart');
                        } else {
                            button.removeClass('in-wishlist');
                            button.find('.wishlist-text').text('Add to Wishlist');
                            button.find('i').removeClass('fa-heart').addClass('fa-heart-o');
                        }
                        
                        // Show notification
                        self.showNotification(response.message, 'success');
                    } else {
                        self.showNotification(response.message, 'error');
                    }
                },
                error: function() {
                    self.showNotification('Request failed', 'error');
                }
            });
        },

        // Initialize Price Slider
        initPriceSlider: function() {
            if ($('.price-range-slider').length) {
                $('.price-range-slider').slider({
                    range: true,
                    min: 0,
                    max: 5000000,
                    values: [0, 5000000],
                    slide: function(event, ui) {
                        $('.price-range-min').text('IDR ' + ui.values[0].toLocaleString());
                        $('.price-range-max').text('IDR ' + ui.values[1].toLocaleString());
                    },
                    stop: function(event, ui) {
                        // Trigger filter after slider stops
                        setTimeout(function() {
                            FugukuGifts.filterGifts();
                        }, 500);
                    }
                });
            }
        },

        // Initialize Search Autocomplete
        initSearchAutocomplete: function() {
            var self = this;
            
            $('.gift-search-input').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: self.config.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'fuguku_search_gifts',
                            nonce: self.config.nonce,
                            search_term: request.term
                        },
                        success: function(data) {
                            if (data.success) {
                                var suggestions = [];
                                $.each(data.results, function(index, item) {
                                    suggestions.push({
                                        label: item.title + ' - ' + item.brand,
                                        value: item.title,
                                        url: item.url
                                    });
                                });
                                response(suggestions);
                            }
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    window.location.href = ui.item.url;
                }
            });
        },

        // Initialize Wishlist Buttons
        initWishlistButtons: function() {
            var self = this;
            
            // Check wishlist status on page load
            $('.gift-wishlist-btn').each(function() {
                var button = $(this);
                var giftId = button.data('gift-id');
                
                // Check if gift is in user's wishlist
                $.ajax({
                    url: self.config.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'fuguku_check_wishlist',
                        nonce: self.config.nonce,
                        gift_id: giftId
                    },
                    success: function(response) {
                        if (response.success && response.in_wishlist) {
                            button.addClass('in-wishlist');
                            button.find('.wishlist-text').text('Remove from Wishlist');
                            button.find('i').removeClass('fa-heart-o').addClass('fa-heart');
                        }
                    }
                });
            });
        },

        // Show Loading
        showLoading: function() {
            $('.gifts-grid').append('<div class="gifts-loading"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
        },

        // Hide Loading
        hideLoading: function() {
            $('.gifts-loading').remove();
        },

        // Show Load More Loading
        showLoadMoreLoading: function() {
            $('.load-more-btn').html('<i class="fa fa-spinner fa-spin"></i> Loading...');
        },

        // Hide Load More Loading
        hideLoadMoreLoading: function() {
            $('.load-more-btn').html('Load More');
        },

        // Update Results Count
        updateResultsCount: function(count) {
            $('.gifts-results-count').text(count + ' gifts found');
        },

        // Show Notification
        showNotification: function(message, type) {
            var notification = $('<div class="gifts-notification ' + type + '">' + message + '</div>');
            $('body').append(notification);
            
            setTimeout(function() {
                notification.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        FugukuGifts.init();
    });

})(jQuery); 