(function( $ ) {
	'use strict';
    window.pokProduct = {
    	data: pok,
    	nonces: pok_nonces,
        el: {
            window: $(window),
            document: $(document),
            body: $('body'),
            province: $('#select_province'),
            city: $('#select_city'),
            district: $('#select_district'),
            result_wrapper: $('.pok-shipping-estimation-result-wrapper'),
            product_id: $('.pok_shipping_product'),
			qty: $('.pok_shipping_qty'),
			destination: $('.pok_check_shipping'),
			origin: $('.pok_shipping_origin')
        },

        fn: {

        	load_city_list( province_id ) {
				pokProduct.el.city.prop('disabled',true);
				pokProduct.el.district.prop('disabled',true);
				if ( '0' !== province_id && '' !== province_id ) {
					$.ajax({
						url: pokProduct.data.ajaxurl,
						type: "POST",
						data: {
							action : 'pok_get_list_city',
							province_id : province_id,
							pok_action : pokProduct.nonces.get_list_city
						},
						dataType:'json',
						cache: false,
						success: function(data){
						  	pokProduct.fn.generate_select( pokProduct.el.city, data, pokProduct.data.labelSelectCity );
						  	pokProduct.fn.generate_select( pokProduct.el.district, {}, pokProduct.data.labelSelectDistrict );
							pokProduct.el.city.prop('disabled',false);
							pokProduct.el.district.prop('disabled',false);
							pokProduct.el.city.trigger('setvalue').trigger('change');
						},
						error: function(err) {
							console.log(err);
						}
					});
				} else {
					pokProduct.fn.generate_select( pokProduct.el.city, {}, pokProduct.data.labelSelectCity );
				  	pokProduct.fn.generate_select( pokProduct.el.district, {}, pokProduct.data.labelSelectDistrict );
					pokProduct.el.city.prop('disabled',false);
					pokProduct.el.district.prop('disabled',false);
					pokProduct.el.city.trigger('setvalue').trigger('change');
				}
			},

			load_district_list( city_id ) {
				pokProduct.el.district.prop('disabled',true);
				if ( '0' !== city_id && '' !== city_id ) {
					$.ajax({
						url: pokProduct.data.ajaxurl,
						type: "POST",
						data: {
							action : 'pok_get_list_district',
							city_id : city_id,
							pok_action : pokProduct.nonces.get_list_district
						},
						dataType:'json',
						cache: false,
						success: function(data){
						  	pokProduct.fn.generate_select( pokProduct.el.district, data, pokProduct.data.labelSelectDistrict );
						  	pokProduct.el.district.prop('disabled',false);
							pokProduct.el.district.trigger('setvalue').trigger('change');
					  	}
					});
				} else {
					pokProduct.fn.generate_select( pokProduct.el.district, {}, pokProduct.data.labelSelectDistrict );
				  	pokProduct.el.district.prop('disabled',false);
					pokProduct.el.district.trigger('setvalue').trigger('change');
				}
			},

        	check_shipping_estimation( destination ) {
        		var el          = pokProduct.el;
        		var data        = pokProduct.data;
        		var nonces      = pokProduct.nonces;
				var product_id 	= el.product_id.val();
				var qty 		= el.qty.val();
				var destination = el.destination.val();
				var origin 		= el.origin.val();

				if ( ! qty ) {
					el.result_wrapper.html( '<p>' + data.insertQty + '</p>' );
				} else if ( '0' === destination ) {
					el.result_wrapper.html( '<p>' + data.selectDestination + '</p>' );
				} else {
					el.result_wrapper.html( '<p>' + data.loading + '</p>' );
					$.ajax({
						url: data.ajaxurl,
						type: "POST",
						data: {
							action : 'pok_get_estimated_cost',
							destination : destination,
							product_id : product_id,
							origin : origin,
							qty : qty,
							pok_action : nonces.get_cost
						},
						dataType:'json',
						cache: false,
						success: function(result){
						  	el.result_wrapper.html(result.html);
						},
						error: function(err) {
							console.log(err);
						}
					});
				}
			},

			generate_select: function(selector,options,label) {
				selector.val('').empty().append('<option value="">'+label+'</option>');
				if ( ! $.isEmptyObject(options) ) {
					for ( var o in options ) {
						selector.append('<option value="'+o+'">'+options[o]+'</option>');     
					}
				}
			}

        },

        run: function () {
        	var el = pokProduct.el;
			var local = pokProduct.data;
			var fn = pokProduct.fn;

            $( '.select2-ajax' ).each(function() {
				var action 	= $(this).data('action');
				var phrase	= $(this).val();
				var nonce 	= $(this).data('nonce');
				$(this).select2({
					ajax: {
						url: local.ajaxurl,
						dataType: 'json',
						delay: 250,
						data: function( params ) {
							return {
								pok_action: nonce,
								action: action,
								q: params.term
							}
						},
						processResults: function (data, params) {
							return {
								results: data
							};
						},
		    			cache: true
					},
					minimumInputLength: 3,
					placeholder: $(this).attr('placeholder')
				});
			});

			el.province.on( 'change', function() {
				var province_id = $(this).val();
				fn.load_city_list( province_id, 'custom' );
			} );

			el.city.on( 'change', function() {
				if ( local.enableDistrict ) {
					var city_id = $(this).val();
					fn.load_district_list( city_id, 'custom' );
				}
			});

			$('.pok_check_shipping, .pok_shipping_qty').on('change', function() {
				fn.check_shipping_estimation();
			});

			$('[href="#tab-shipping_estimation"]').on('click', function() {
				fn.check_shipping_estimation();
			});
        }
    };
    pokProduct.run();
})( jQuery );