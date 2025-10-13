jQuery(document).ready(function ($) {

    var $wdpTable           = $('.wdp_table ');
    var $dynamicPricing     = awdajaxobject.dynamicPricing;
    var $variablePricing    = awdajaxobject.variablePricing;

    // Shortcode Listing Page Carousel
    $('.wdp-carousel').owlCarousel({
        loop: false,
        margin: 10,
        dots: true,
        nav: false,
        responsiveClass: true,
        responsive: {
            0: {
                items:1,
                nav:true
            },
            600: {
                items:2
            },
            800: {
                items:3
            },
            1000: {
                items:4
            }
        }
    });
    $('.wdp-slider').owlCarousel({
        loop: false,
        dots: true,
        nav: false,
        items: 1
    });

    // Timer
    function makeTimer() {

        var selectedDate  = $("#timer").data('time');
        // Format 25 December 2020 23:59:00 GMT+05:30
        // Safari timer fix - pregreplace dateformat
        var endTime = new Date(selectedDate.replace(/-/g, "/"));			
        endTime = (Date.parse(endTime) / 1000);

        var now = new Date();
        now = (Date.parse(now) / 1000);

        var timeLeft = endTime - now;

        var days = Math.floor(timeLeft / 86400); 
        var hours = Math.floor((timeLeft - (days * 86400)) / 3600);
        var minutes = Math.floor((timeLeft - (days * 86400) - (hours * 3600 )) / 60);
        var seconds = Math.floor((timeLeft - (days * 86400) - (hours * 3600) - (minutes * 60)));

        var label_days = $("#days").data('label');
        var label_hrs = $("#hours").data('label');
        var label_mins = $("#minutes").data('label');
        var label_secs = $("#seconds").data('label');

        if (hours < "10") { hours = "0" + hours; }
        if (minutes < "10") { minutes = "0" + minutes; }
        if (seconds < "10") { seconds = "0" + seconds; }

        $("#days").html(days + "<span>"+label_days+"</span>");
        $("#hours").html(hours + "<span>"+label_hrs+"</span>");
        $("#minutes").html(minutes + "<span>"+label_mins+"</span>");
        $("#seconds").html(seconds + "<span>"+label_secs+"</span>");

    }
    
    // checking if timer is enabled
    if ( $("#timer").length > 0 ) {
        setInterval(function() { makeTimer(); }, 1000);
    }

    //Refresh Cart on Payment Mode Change
    $( document.body ).on( 'change', 'input[name="payment_method"], input.shipping_method', function (e) {
        let data = { 
            'action': 'awdpschange', 
            'nonce': awdajaxobject.nonce, 
            'awbcompvalue': e.target.value, 
            'awbcompname': e.target.name 
        };
        $.post(awdajaxobject.url, data, function(response) { $( 'body' ).trigger( 'update_checkout' ); });
    });

    // if ( typeof wp.hooks !== 'undefined' ) {
    //     wp.hooks.addFilter('wcpa_product_price', 'wcpa', (productPrice, product_id, variation_id) => {
    //         $.ajax({ url: awdajaxobject.url, data: {action: 'wdpDiscountedPrice', prodID: product_id, varID: variation_id} }).done(function(data) {
    //             productPrice = data ? data : productPrice;
    //             return productPrice;
    //         }).fail(function(data){
    //             return productPrice;
    //         });
    //     }, 10);
    //     document.dispatchEvent(new Event("wcpaTrigger", {bubbles: true}));
    //     $( '.variations_form' ).on( 'woocommerce_variation_select_change', function() {
    //         wp.hooks.addFilter('wcpa_product_price', 'wcpa', (productPrice, product_id, variation_id) => {
    //             $.ajax({ url: awdajaxobject.url, data: {action: 'wdpDiscountedPrice', prodID: product_id, varID: variation_id} }).done(function(data) {
    //                 productPrice = data ? data : productPrice;
    //                 return productPrice;
    //             }).fail(function(data){
    //                 return productPrice;
    //             });
    //         }, 10);
    //         document.dispatchEvent(new Event("wcpaTrigger", {bubbles: true}));
    //     });
    //     // $('form.cart').find('[name=quantity]').on('change input', function(){ 
    //     //     let qn = $(this).parents('form.cart').find('input[name="quantity"]').val();
    //     //     wp.hooks.addFilter('wcpa_product_price', 'wcpa', (productPrice, product_id, variation_id) => {
    //     //         $.ajax({ url: awdajaxobject.url, data: {action: 'wdpDiscountedPrice', prodID: product_id, varID: variation_id} }).done(function(data) {
    //     //             productPrice = data ? qn * data : qn * productPrice;
    //     //             return productPrice;
    //     //         }).fail(function(data){
    //     //             return qn * productPrice;
    //     //         });
    //     //     }, 10);
    //     //     document.dispatchEvent(new Event("wcpaTrigger", {bubbles: true}));
    //     // });
    // }

    if ( $wdpTable.length > 0 ) {

        if ( $variablePricing ) {
            $( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
                let attributes = variation.attributes;
                let price = variation.display_price;
                let price_html = variation.price_html;
                let variation_id = variation.variation_id;
                let loader = '<div class="wdpLoader"><span></span><span></span><span></span></div>';
                $wdpTable.find('tbody td').html(loader);
                $('.wdpHiddenPrice').html(price_html); // hidden variation price
                let varPrice = $('.wdpHiddenPrice del').length ? $('.wdpHiddenPrice ins .amount').text() : $('.wdpHiddenPrice .amount').text();
                varPrice = varPrice ? varPrice.replace(/[^\d\.]/g, '') : price;
                let data = {
                    'action': 'wdpAjax', 
                    'nonce': awdajaxobject.nonce, 
                    'type': 'change',
                    'attributes': attributes,
                    'price': varPrice,
                    'variation_id': variation_id,
                    'DisData': $wdpTable.attr('data-table'),
                    'Rule': $wdpTable.attr('data-rule'),
                }
                $.post(awdajaxobject.url, data, function(response) { 
                    if ( response ) { 
                        $wdpTable.attr('data-price', varPrice);
                        $wdpTable.find('tbody').html(response); 
                    }
                });
            });
        }

        if ( $dynamicPricing ) {
            let loader = '<div class="wdpLoader"><span></span><span></span><span></span></div>';
            $('form.cart').find('[name=quantity]').on('change input', function(){ 
                if ( $wdpTable.attr('data-product') === '' ) { return; }
                let data = { 
                    'action': 'wdpAjax', 
                    'nonce': awdajaxobject.nonce, 
                    'ProdID': $wdpTable.attr('data-product'), 
                    'DisData': $wdpTable.attr('data-table'), 
                    'ProdPrice': $wdpTable.attr('data-price'), 
                    'ProdVarPrice': $wdpTable.attr('data-var-price'), 
                    'ProdQty': $(this).parents('form.cart').find('input[name="quantity"]').val() 
                };
                $(".wdpDynamicValue .wdpPrice").html(loader);
                $(".wdpDynamicValue .wdpTotal").html(loader);
                $.post(awdajaxobject.url, data, function(response) { 
                    if ( response ) { 
                        response = JSON.parse(response); 
                        // $(".wdpDynamicValue .wdpPrice").removeClass('wdpLoader');
                        // $(".wdpDynamicValue .wdpTotal").removeClass('wdpLoader');
                        $(".wdpDynamicValue .wdpPrice").html(response.currency + response.price); 
                        $(".wdpDynamicValue .wdpTotal").html(response.currency + response.total); 
                        $(".wdpDynamicValue").show();
                    }
                });
            });
        }

    }
  
});  