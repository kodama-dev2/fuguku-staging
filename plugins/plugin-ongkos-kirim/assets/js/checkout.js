(function( $ ) {
	'use strict';
    window.pokCheckout = {
    	data: pok_checkout_data,
        el: {
            window: $(window),
            document: $(document),
            body: $('body'),
            field: {
            	billing: {
            		wrapper: $('.woocommerce-address-fields, .woocommerce-billing-fields'),
            		country: $('#billing_country'),
            		state: $('#billing_state'),
            		_city: $('#billing_city'),
            		city: $('#billing_pok_city'),
            		district: $('#billing_pok_district'),
            		country_wrapper: $('#billing_country_field'),
            		state_wrapper: $('#billing_state_field'),
            		_city_wrapper: $('#billing_city_field'),
            		city_wrapper: $('#billing_pok_city_field'),
            		district_wrapper: $('#billing_pok_district_field')
            	},
            	shipping: {
            		wrapper: $('.woocommerce-address-fields, .woocommerce-shipping-fields'),
            		country: $('#shipping_country'),
            		state: $('#shipping_state'),
            		_city: $('#shipping_city'),
            		city: $('#shipping_pok_city'),
            		district: $('#shipping_pok_district'),
            		country_wrapper: $('#shipping_country_field'),
            		state_wrapper: $('#shipping_state_field'),
            		_city_wrapper: $('#shipping_city_field'),
            		city_wrapper: $('#shipping_pok_city_field'),
            		district_wrapper: $('#shipping_pok_district_field')
            	}
            }
        },

        fn: {

        	check_country: function(context) {
        		pokCheckout.el.field[context].country.val(pokCheckout.data[context].country).trigger('change');
			},

			load_city: function(context) {
				var field = pokCheckout.el.field[context];
				var local = pokCheckout.data;
				if ( 'ID' !== local[context].country && 'ID' !== field.country.val() ) return;
				if (field.state.val()) {
					field.city_wrapper.prop('title',local.labelLoadingCity).addClass('pok_loading');
					field.district_wrapper.prop('title',local.labelLoadingCity).addClass('pok_loading');
					field.city.prop('disabled', true);
					field.district.prop('disabled', true);

					$.ajax({
						url: local.ajaxurl,
						type: 'post',
						data: {
							action: 'pok_get_list_city',
							pok_action: local.nonce_get_list_city,
							province_id: field.state.val()
						},
						success: function(data) {
							pokCheckout.fn.generate_select( field.city, data, local.labelSelectCity );
							pokCheckout.fn.generate_select( field.district, {}, local.labelSelectDistrict );
							field.city_wrapper.removeAttr('title').removeClass('pok_loading woocommerce-validated');
							field.district_wrapper.removeAttr('title').removeClass('pok_loading woocommerce-validated');
							field.city.prop('disabled', false).trigger('options_loaded', data);
							field.district.prop('disabled', false);
							if (!local.enableDistrict) {
								field.city_wrapper.addClass('update_totals_on_change');
							}
						},
						error: function(err) {
							field.city_wrapper.removeAttr('title').removeClass('pok_loading');
							field.city.prop('disabled', false);
							if (confirm( local.labelFailedCity )) {
								return pokCheckout.fn.load_city(context);
							}
						}
					});
				}
			},

			load_district: function(context) {
				var field = pokCheckout.el.field[context];
				var local = pokCheckout.data;
				if ( 'ID' !== local[context].country && 'ID' !== field.country.val() ) return;
				if (parseInt(field.city.val())) {
					field.district_wrapper.prop('title',local.labelLoadingDistrict).addClass('pok_loading');
					field.district.prop('disabled',true);

					$.ajax({
						url: local.ajaxurl,
						type: 'post',
						data: {
							action: 'pok_get_list_district',
							pok_action: local.nonce_get_list_district,
							city_id: field.city.val()
						},
						success: function(data) {
							pokCheckout.fn.generate_select( field.district, data, local.labelSelectDistrict );
							field.district_wrapper.removeAttr('title').removeClass('pok_loading woocommerce-validated').addClass('update_totals_on_change');
							field.district.prop('disabled', false).trigger('options_loaded', data);
						},
						error: function(err) {
							field.district_wrapper.removeAttr('title').removeClass('pok_loading');
							field.district.prop('disabled', false);
							if (confirm( local.labelFailedDistrict )) {
								return pokCheckout.fn.load_district(context);
							}
						}
					});
				}
			},

			load_returning_user_data: function(context) {
				var el = pokCheckout.el;
	        	var field = el.field[context];
				var local = pokCheckout.data;
				if ( field.country.val() !== 'ID' ) return;
				if ( ! local.is_checkout || ! local.useSimpleAddress ) {
					if (parseInt(local[context].state) ) {
						field.state.val(local[context].state).trigger('change');
						if (parseInt(local[context].city)) {
							field.city.on('options_loaded', function(e, city_list) {
								if (city_list[local[context].city]) {
									if (!local.enableDistrict) field.city.addClass('update_totals_on_change');
									field.city.val(local[context].city).trigger('change');
								}
							});
							if (parseInt(local[context].district) && local.enableDistrict) {
								field.district.on('options_loaded', function(e, district_list) {
									if (district_list[local[context].district]) {
										field.district.addClass('update_totals_on_change').val(local[context].district).trigger('change');
									}
								});
							}
						}
					} else {
						pokCheckout.fn.generate_select( field.city, {}, local.labelSelectCity );
						pokCheckout.fn.generate_select( field.district, {}, local.labelSelectDistrict );
					}
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
        	var el = pokCheckout.el;
        	var billing = el.field.billing;
        	var shipping = el.field.shipping;
			var local = pokCheckout.data;
			var fn = pokCheckout.fn;

            el.document.on('ready', function() {
            	if ( local.loadReturningUserData === 'yes' ) {
					if ( 0 === parseInt(local.billing.state) ) {
						fn.check_country('billing');
					}
					if ( 0 === parseInt(local.shipping.state) ) {
						fn.check_country('shipping');
					}
				} else {
					fn.check_country('billing');
					fn.check_country('shipping');
				}
            });

            if ( ! local.useSimpleAddress ) {
				billing.wrapper.on('change', '#billing_state', function() {
					fn.load_city('billing');
				});
				shipping.wrapper.on('change', '#shipping_state', function() {
					fn.load_city('shipping');
				});
				billing.city.on('change', function() {
					billing._city.val($(this).find('option:selected').text());
				});
				shipping.city.on('change', function() {
					shipping._city.val($(this).find('option:selected').text());
				});
				if (local.enableDistrict) {
					billing.city.on('change', function() {
						fn.load_district('billing');
					});
					shipping.city.on('change', function() {
						fn.load_district('shipping');
					});
				}
			} else {
				billing.city.on('change', function() {
					billing._city.val($(this).find('option:selected').text());
				});
				shipping.city.on('change', function() {
					shipping._city.val($(this).find('option:selected').text());
				});
			}

			$( el.body ).on( 'country_to_state_changing', function( event, country, wrapper ) {
				if ( 'ID' === country ) {
					if ( local.loadReturningUserData === 'yes' ) {
						billing.state.val(local.billing.state);
						shipping.state.val(local.shipping.state);
					}
					if ( local.useSimpleAddress ) {
						wrapper.find('#billing_state_field').hide();
						wrapper.find('#shipping_state_field').hide();
					} else {
						wrapper.find('#billing_state_field').show();
						wrapper.find('#shipping_state_field').show();
					}
				}
			});

			if (local.loadReturningUserData === 'yes') {
				fn.load_returning_user_data('billing');
				fn.load_returning_user_data('shipping');
			}

			$( el.body ).on('wc_address_i18n_ready', function() {
				if (local.loadReturningUserData === 'yes') {
					fn.load_returning_user_data('billing');
					fn.load_returning_user_data('shipping');
				}
			})

			$( '.init-select2 select' ).select2();

			$( '.select2-ajax select' ).each(function() {
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
        }
    };
    pokCheckout.run();
})( jQuery );