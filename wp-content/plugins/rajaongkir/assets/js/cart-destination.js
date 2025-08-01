jQuery(document).ready(function ($) {
    let isUpdatingDestination = false;
    let isLoadingShipping = false;

    function moveCartDestination() {
        let destinationWrapper = $('#cart-destination-wrapper');
        let shippingField = $('#calc_shipping_country_field');
        if (destinationWrapper.length && shippingField.length) {
            destinationWrapper.insertAfter(shippingField);
        }
    }

    function getSelectedCountry() {
        let selectedCountry = $('#calc_shipping_country').val() || $('#billing_country').val() || $('#shipping_country').val();
        return selectedCountry;
    }

    function initSelect2(selector, action) {
        if ($(selector).hasClass('select2-hidden-accessible')) {
            $(selector).select2('destroy');
        }
        
        $(selector).select2({
            ajax: {
                url: cartDestination.ajax_url,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        action: action,
                        nonce: cartDestination.nonce,
                        country: getSelectedCountry(),
                        query: params.term
                    };
                },
                processResults: function (data) {
                    return { results: data.success ? data.data : [] };
                }
            },
            minimumInputLength: 3,
            placeholder: "Search and select...",
            allowClear: true
        });

        $(selector).on('select2:select', function (e) {
            let selectedOption = e.params.data;
            if (!selectedOption) return;

            let destination_id = selectedOption.id;
            let destination_label = selectedOption.text;

            $('#cart-destination-label').val(destination_label);
            $('#cart-destination')
                .attr('data-destination-id', destination_id)
                .html('<option value="' + destination_id + '" selected>' + destination_label + '</option>');


            // Auto-update on checkout page, but not on cart page
            let isCheckoutPage = $('form.checkout').length > 0;
            if (isCheckoutPage) {
                updateDestinationSequential();
            } else {
                // Show notification on cart page to press update button
                showNotification("Please click the 'Update' button to apply your shipping changes.");
                
                // Update the session in background so the data is ready when Update is clicked
                updateDestinationSession();
            }
        });
    }

    // Function to update session data without triggering cart/checkout refresh
    function updateDestinationSession() {
        let selectedCountry = getSelectedCountry();
        let destination_id = $('#cart-destination').attr('data-destination-id');
        let destination_label = $('#cart-destination-label').val();

        if (!selectedCountry) {
            return;
        }

        if (selectedCountry === 'ID' && (!destination_id || !destination_label)) {
            return;
        }

        showLoadingOverlay();
        isLoadingShipping = true;

        $.ajax({
            type: "POST",
            url: cartDestination.ajax_url,
            data: {
                action: 'update_destination',
                nonce: cartDestination.nonce,
                destination_id: destination_id,
                destination_label: destination_label,
                country: selectedCountry,
                update_totals: 'no' // Tell the server not to update totals
            },
            dataType: 'json'
        }).done(function (response) {
            hideLoadingOverlay();
            isLoadingShipping = false;
        }).fail(function(error) {
            hideLoadingOverlay();
            isLoadingShipping = false;
        });
    }

    // Function to guarantee sequential updates
    function updateDestinationSequential() {
        if (isUpdatingDestination) return;
        isUpdatingDestination = true;
        let selectedCountry = getSelectedCountry();
        let destination_id = $('#cart-destination').attr('data-destination-id');
        let destination_label = $('#cart-destination-label').val();

        if (!selectedCountry) {
            isUpdatingDestination = false;
            return;
        }

        if (selectedCountry === 'ID' && (!destination_id || !destination_label)) {
            isUpdatingDestination = false;
            return;
        }

        $('#billing_country, #shipping_country, #calc_shipping_country')
            .not(this)
            .val(selectedCountry);

        // Show loading overlay before AJAX request
        showLoadingOverlay();
        isLoadingShipping = true;

        // STEP 1: Update the destination in session first
        $.ajax({
            type: "POST",
            url: cartDestination.ajax_url,
            data: {
                action: 'update_destination',
                nonce: cartDestination.nonce,
                destination_id: destination_id,
                destination_label: destination_label,
                country: selectedCountry,
                update_totals: 'no'
            },
            dataType: 'json'
        }).done(function (firstResponse) {
            
            if (firstResponse.success) {
                
                $.ajax({
                    type: "POST",
                    url: cartDestination.ajax_url,
                    data: {
                        action: 'recalculate_shipping',
                        nonce: cartDestination.nonce,
                        country: selectedCountry
                    },
                    dataType: 'json'
                }).done(function (secondResponse) {
                    if (secondResponse.success) {
                        if ($('form.checkout').length > 0) {
                            $(document.body).trigger('update_checkout');
                        }
                    } else {
                        hideLoadingOverlay();
                        isLoadingShipping = false;
                    }
                }).fail(function (error) {
                    hideLoadingOverlay();
                    isLoadingShipping = false;
                }).always(function () {
                    isUpdatingDestination = false;
                });
                
            } else {
                hideLoadingOverlay();
                isLoadingShipping = false;
                isUpdatingDestination = false;
            }
        }).fail(function (error) {
            hideLoadingOverlay();
            isLoadingShipping = false;
            isUpdatingDestination = false;
        });
    }

    function showNotification(message) {
        if ($('.cart-destination-notification').length === 0) {
            $('<div class="cart-destination-notification" style="background-color: #f7f6f7; padding: 10px; margin: 10px 0; border-left: 3px solid #6d6d6d; color: #515151;">' + message + '</div>')
                .insertAfter('#cart-destination-wrapper')
                .fadeIn();
            
            setTimeout(function() {
                $('.cart-destination-notification').fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }
    }

    function refreshCartDestination() {
        let selectedCountry = getSelectedCountry();

        if (selectedCountry !== 'ID') {
            return;
        }

        // Initialize Select2 for cart page
        initSelect2('#cart-destination', 'cart_search_destination');

        let savedDestinationId = cartDestination.selected_destination_id;
        let savedDestinationLabel = cartDestination.selected_destination_label;

        if (savedDestinationId && savedDestinationLabel) {
            $('#cart-destination')
                .attr('data-destination-id', savedDestinationId)
                .html('<option value="' + savedDestinationId + '" selected>' + savedDestinationLabel + '</option>');
            $('#cart-destination-label').val(savedDestinationLabel);
        }
    }

    function showLoadingOverlay() {
        if ($('.shipping-calculator-form').length > 0) {
            if ($('#shipping-loading-overlay').length === 0) {
                $('<div id="shipping-loading-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255,255,255,0.7); z-index: 100; display: flex; justify-content: center; align-items: center;"><div>Calculating shipping costs...</div></div>')
                    .appendTo('.shipping-calculator-form');
            }
        }
        
        if ($('form.checkout').length > 0) {
            $('form.checkout').block({
                message: 'Updating shipping costs...',
                overlayCSS: {
                    backgroundColor: '#fff',
                    opacity: 0.6
                }
            });
        }
    }

    function hideLoadingOverlay() {
        $('#shipping-loading-overlay').remove();
        if ($('form.checkout').length > 0) {
            $('form.checkout').unblock();
        }
    }

    function updateDestination() {
        if (isUpdatingDestination) return;
        isUpdatingDestination = true;

        let selectedCountry = getSelectedCountry();
        let destination_id = $('#cart-destination').attr('data-destination-id');
        let destination_label = $('#cart-destination-label').val();

        let isCartPage = $('form.woocommerce-cart-form').length > 0;
        let isCheckoutPage = $('form.checkout').length > 0;

        if (!selectedCountry) {
            isUpdatingDestination = false;
            return;
        }

        if (selectedCountry === 'ID' && (!destination_id || !destination_label)) {
            isUpdatingDestination = false;
            return;
        }

        console.log("🌎 Setting WooCommerce country to:", selectedCountry);

        $('#billing_country, #shipping_country, #calc_shipping_country')
            .not(this)
            .val(selectedCountry);

        // Show loading overlay before AJAX request
        showLoadingOverlay();
        isLoadingShipping = true;

        $.ajax({
            type: "POST",
            url: cartDestination.ajax_url,
            data: {
                action: 'update_destination',
                nonce: cartDestination.nonce,
                destination_id: destination_id,
                destination_label: destination_label,
                country: selectedCountry
            },
            dataType: 'json',
            beforeSend: function () {
            }
        }).done(function (response) {
            if (response.success) {
                if (isCheckoutPage) {
                    $(document.body).trigger('update_checkout');
                } else if (isCartPage) {
                }
            } else {
                hideLoadingOverlay();
                isLoadingShipping = false;
            }
        }).fail(function (error) {
            hideLoadingOverlay();
            isLoadingShipping = false;
        }).always(function () {
            isUpdatingDestination = false;
        });
    }

    function toggleDestinationDropdown() {
        let selectedCountry = getSelectedCountry();

        if (selectedCountry !== 'ID') {
            $('#cart-destination-field, #cart-destination-wrapper').hide();
            $('#cart-destination').prop('required', false);
        } else {
            $('#cart-destination-field, #cart-destination-wrapper').show();
            $('#cart-destination').prop('required', true); 
    
            refreshCartDestination();
            moveCartDestination();
        }
    }
    

    // Listen for shipping calculation complete to hide loading overlay
    $(document.body).on('updated_shipping_method updated_checkout', function() {
        if (isLoadingShipping) {
            hideLoadingOverlay();
            isLoadingShipping = false;
        }
    });

    // Listen for country changes
    $(document).on('change', '#billing_country, #shipping_country, #calc_shipping_country', function () {
        toggleDestinationDropdown();
        if ($('form.checkout').length > 0) {
            updateDestinationSequential();
        } else if (getSelectedCountry() !== 'ID') {
            updateDestinationSession();
        }
    });

    // Handle cart page specific initialization
    if ($('form.woocommerce-cart-form').length > 0) {
        // Listen for the shipping calculator toggle
        $(document).on('click', '.shipping-calculator-button', function() {
            // Set a small delay to ensure the form is visible
            setTimeout(function() {
                moveCartDestination();
                refreshCartDestination();
            }, 300);
        });
        
        // Additional initialization for when "Calculate shipping" section is already open
        if ($('.shipping-calculator-form').is(':visible')) {
            moveCartDestination();
            refreshCartDestination();
        }
    }

    // Initialize on page load
    toggleDestinationDropdown();
});