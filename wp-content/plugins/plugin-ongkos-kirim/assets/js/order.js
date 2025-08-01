(function( $ ) {
	'use strict';
	window.pokOrder = {
		nonces: pok_nonces,
		data: pok_order_data,
		el: {
			window: $(window),
			document: $(document),
			body: $('body'),
			order_data: $('#order_data'),
			field: {
				billing: {
					country: $('#_billing_country'),
					state: $('#_billing_state'),
					_city: $('#_billing_city'),
					city: $('#_billing_pok_city'),
					district: $('#_billing_pok_district'),
					country_wrapper: $('._billing_country_field'),
					state_wrapper: $('._billing_state_field'),
					_city_wrapper: $('._billing_city_field'),
					city_wrapper: $('._billing_pok_city_field'),
					district_wrapper: $('._billing_pok_district_field')
				},
				shipping: {
					country: $('#_shipping_country'),
					state: $('#_shipping_state'),
					_city: $('#_shipping_city'),
					city: $('#_shipping_pok_city'),
					district: $('#_shipping_pok_district'),
					country_wrapper: $('._shipping_country_field'),
					state_wrapper: $('._shipping_state_field'),
					_city_wrapper: $('._shipping_city_field'),
					city_wrapper: $('._shipping_pok_city_field'),
					district_wrapper: $('._shipping_pok_district_field')
				}
			}
		},

		fn: {

			load_city: function(context) {
				var field = pokOrder.el.field[context];
				var local = pokOrder.data;
				if ( 'ID' !== local[context].country && 'ID' !== field.country.val() ) return;
				if ($('#_'+context+'_state').val()) {
					field.city_wrapper.prop('title',local.labelLoadingCity).addClass('pok_loading');
					field.district_wrapper.prop('title',local.labelLoadingCity).addClass('pok_loading');
					field.city.prop('disabled', true);
					field.district.prop('disabled', true);

					$.ajax({
						url: ajaxurl,
						type: 'post',
						data: {
							action: 'pok_get_list_city',
							pok_action: local.nonce_get_list_city,
							province_id: $('#_'+context+'_state').val()
						},
						success: function(data) {
							pokOrder.fn.generate_select( field.city, data, local.labelSelectCity );
							pokOrder.fn.generate_select( field.district, {}, local.labelSelectDistrict );
							field.city_wrapper.removeAttr('title').removeClass('pok_loading');
							field.district_wrapper.removeAttr('title').removeClass('pok_loading');
							field.city.prop('disabled', false).trigger(context+'_city_options_loaded', data);
							field.district.prop('disabled', false);
							if (!local.enableDistrict) {
								field.city_wrapper.addClass('update_totals_on_change');
							}
						},
						error: function(err) {
							field.city_wrapper.removeAttr('title').removeClass('pok_loading');
							field.city.prop('disabled', false);
							if (confirm( local.labelFailedCity )) {
								return pokOrder.fn.load_city(context);
							}
						}
					});
				}
			},

			load_district: function(context) {
				var field = pokOrder.el.field[context];
				var local = pokOrder.data;
				if ( 'ID' !== local[context].country && 'ID' !== field.country.val() ) return;
				if (parseInt(field.city.val())) {
					field.district_wrapper.prop('title',local.labelLoadingDistrict).addClass('pok_loading');
					field.district.prop('disabled',true);

					$.ajax({
						url: ajaxurl,
						type: 'post',
						data: {
							action: 'pok_get_list_district',
							pok_action: local.nonce_get_list_district,
							city_id: field.city.val()
						},
						success: function(data) {
							pokOrder.fn.generate_select( field.district, data, local.labelSelectDistrict );
							field.district_wrapper.removeAttr('title').removeClass('pok_loading');
							field.district.prop('disabled', false).trigger(context+'_district_options_loaded', data);
						},
						error: function(err) {
							console.log(err)
							field.district_wrapper.removeAttr('title').removeClass('pok_loading');
							field.district.prop('disabled', false);
							if (confirm( local.labelFailedDistrict )) {
								return pokOrder.fn.load_district(context);
							}
						}
					});
				}
			},

			set_city: function(context) {
				pokOrder.el.field[context]._city.val(pokOrder.el.field[context].city.find('option:selected').text());
			},

			load_returning_user_data: function(context) {
				var el = pokOrder.el;
	        	var field = el.field[context];
				var local = pokOrder.data;
				if ( field.country.val() !== 'ID' ) return;
				if (parseInt(local[context].state) ) {
					field.state.val(local[context].state).trigger('change');
					if (parseInt(local[context].city)) {
						field.city.on(context+'_city_options_loaded', function(e, city_list) {
							if (city_list[local[context].city]) {
								field.city.val(local[context].city).trigger('change');
							}
						});
						if (parseInt(local[context].district) && local.enableDistrict) {
							field.district.on(context+'_district_options_loaded', function(e, district_list) {
								if (district_list[local[context].district]) {
									field.district.val(local[context].district).trigger('change');
								}
							});
						}
					}
				} else {
					pokOrder.fn.generate_select( field.city, {}, local.labelSelectCity );
					pokOrder.fn.generate_select( field.district, {}, local.labelSelectDistrict );
				}
			},

			generate_select: function(selector,options,label) {
				selector.val('').empty().append('<option value="">'+label+'</option>');
				if ( ! $.isEmptyObject(options) ) {
					for ( var o in options ) {
						selector.append('<option value="'+o+'">'+options[o]+'</option>');     
					}
				}
			},

			load_billing: function( force ) {
				var local = pokOrder.data;
				var field = pokOrder.el.field.billing;
				if ( true === force || window.confirm( woocommerce_admin_meta_boxes.load_billing ) ) {

					pokOrder.el.order_data.addClass('prevent-changes');

					// Get user ID to load data for
					var user_id = $( '#customer_user' ).val();

					if ( ! user_id ) {
						window.alert( woocommerce_admin_meta_boxes.no_customer_selected );
						return false;
					}

					var data = {
						user_id : user_id,
						action  : 'woocommerce_get_customer_details',
						security: woocommerce_admin_meta_boxes.get_customer_details_nonce
					};

					$( this ).closest( 'div.order_data_column' ).block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});

					field.city.val('').empty().append('<option value="">'+local.labelSelectCity+'</option>'); 
					field.district.val('').empty().append('<option value="">'+local.labelSelectDistrict+'</option>'); 

					$.ajax({
						url: woocommerce_admin_meta_boxes.ajax_url,
						data: data,
						type: 'POST',
						success: function( response ) {
							if ( response && response.billing ) {
								if ( response.billing.state_options ) {
									pokOrder.fn.generate_select( field.state, response.billing.state_options, local.labelSelectState );
								}
								if ( response.billing.city_options ) {
									pokOrder.fn.generate_select( field.city, response.billing.city_options, local.labelSelectCity );
								}
								if ( response.billing.district_options ) {
									pokOrder.fn.generate_select( field.district, response.billing.district_options, local.labelSelectDistrict );
								}
								$.each( response.billing, function( key, data ) {
									$( '#_billing_' + key ).val( data );
								});
							}
							$( 'div.order_data_column' ).unblock();
							pokOrder.el.order_data.removeClass('prevent-changes');
						}
					});
				}
				return false;
			},

			load_shipping: function( force ) {
				var local = pokOrder.data;
				var field = pokOrder.el.field.shipping;
				if ( true === force || window.confirm( woocommerce_admin_meta_boxes.load_billing ) ) {
					console.log(pokOrder.el.order_data)
					pokOrder.el.order_data.addClass('prevent-changes');

					// Get user ID to load data for
					var user_id = $( '#customer_user' ).val();

					if ( ! user_id ) {
						window.alert( woocommerce_admin_meta_boxes.no_customer_selected );
						return false;
					}

					var data = {
						user_id : user_id,
						action  : 'woocommerce_get_customer_details',
						security: woocommerce_admin_meta_boxes.get_customer_details_nonce
					};

					$( this ).closest( 'div.order_data_column' ).block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});

					field.city.val('').empty().append('<option value="">'+local.labelSelectCity+'</option>'); 
					field.district.val('').empty().append('<option value="">'+local.labelSelectDistrict+'</option>');

					$.ajax({
						url: woocommerce_admin_meta_boxes.ajax_url,
						data: data,
						type: 'POST',
						success: function( response ) {
							if ( response && response.shipping ) {
								if ( response.shipping.state_options ) {
									pokOrder.fn.generate_select( field.state, response.shipping.state_options, local.labelSelectState );
								}
								if ( response.shipping.city_options ) {
									pokOrder.fn.generate_select( field.city, response.shipping.city_options, local.labelSelectCity );
								}
								if ( response.shipping.district_options ) {
									pokOrder.fn.generate_select( field.district, response.shipping.district_options, local.labelSelectDistrict );
								}
								$.each( response.shipping, function( key, data ) {
									$( '#_shipping_' + key ).val( data );
								});
							}
							$( 'div.order_data_column' ).unblock();
							pokOrder.el.order_data.removeClass('prevent-changes');
						}
					});
				}
				return false;
			},

			copy_billing_to_shipping: function() {
				var billing = pokOrder.el.field.billing;
				var shipping = pokOrder.el.field.shipping;
				if ( window.confirm( woocommerce_admin_meta_boxes.copy_billing ) ) {
					pokOrder.el.order_data.addClass('prevent-changes');
					shipping.state_wrapper.html( billing.state_wrapper.html() );
					shipping.city.html( billing.city.html() );
					shipping.district.html( billing.district.html() );
					$('.order_data_column [name^="_billing_"]').each( function() {
						var input_name = $(this).attr('name');
						input_name     = input_name.replace( '_billing_', '_shipping_' );
						$( '#' + input_name ).val( $(this).val() );
					});
					pokOrder.el.order_data.removeClass('prevent-changes');
				}
				return false;
			},

			add_shipping: function() {
				var local = pokOrder.data;
				var shipping = pokOrder.el.field.shipping;
				if ( 'ID' !== shipping.country.val() ) {
					alert(local.labelOnlyIndonesia);
					return false;
				}
				if ( local.enableDistrict ) {
					var destination = shipping.district.val();
				} else {
					var destination = shipping.city.val();
				}
				if ( destination ) {
					tb_show( local.labelSelectShipping, "#TB_inline?width=500&inlineId=pok-switch-shipping" );
					$('.pok-order-shipping-result .loading').removeClass('hidden');
					$('.pok-order-shipping-result .results').addClass('hidden');
					$('.pok-order-shipping-result .no-result').addClass('hidden');
					$('.pok-order-shipping-result .results tbody').html('');
					var order_id = $(this).data('order-id');
					$.ajax({
						url: ajaxurl,
						type: "POST",
						data: {
							action : 'pok_get_cost',
							destination : destination,
							order_id : order_id,
							pok_action : pokOrder.nonces.get_cost
						},
						dataType:'json',
						cache: false,
						success: function(arr){
							if ( 0 !== arr.length ) {
								var options = '';
							  	$('.pok-order-shipping-result .loading').addClass('hidden');
							  	$('.pok-order-shipping-result .no-result').addClass('hidden');
							  	$.each(arr, function(key,value) {
							  		options += "<tr class='pok-add-shipping-option' data-name='" + value.label + "' data-cost='" + value.cost + "' data-meta='" + JSON.stringify(value.meta) + "'><td class='courier'>" + value.courier_name + "</td><td class='service'>" + value.meta.service + "</td><td class='etd'>" + value.meta.etd + "</td><td class='cost'>" + value.cost_display + "</td></tr>";
								});
								$('.pok-order-shipping-result .results tbody').html(options);
								$('.pok-order-shipping-result .results').removeClass('hidden');
							} else {
								$('.pok-order-shipping-result .loading').addClass('hidden');
								$('.pok-order-shipping-result .no-result').removeClass('hidden');
								$('.pok-order-shipping-result .results').addClass('hidden');
							}
						},
						error: function(err) {
							console.log(err);
						}
					});
				} else {
					tb_remove();
					if ( local.enableDistrict ) {
						alert(local.labelNoDistrict);
					} else {
						alert(local.labelNoCity);
					}
					return false;
				}
			},

			switch_shipping: function() {
				var local = pokOrder.data;
				var shipping = pokOrder.el.field.shipping;
				if ( 'ID' !== shipping.country.val() ) {
					alert(local.labelOnlyIndonesia);
					return false;
				}
				if ( local.enableDistrict ) {
					var destination = shipping.district.val();
				} else {
					var destination = shipping.city.val();
				}
				if ( destination ) {
					tb_show( local.labelSelectShipping, "#TB_inline?width=500&inlineId=pok-switch-shipping" );
					$('.pok-order-shipping-result .loading').removeClass('hidden');
					$('.pok-order-shipping-result .results').addClass('hidden');
					$('.pok-order-shipping-result .no-result').addClass('hidden');
					$('.pok-order-shipping-result .results tbody').html('');
					var order_id = $(this).data('order-id');
					var item_id  = $(this).data('id');
					var weight   = $(this).data('weight');
					var origin   = $(this).data('origin');
					$.ajax({
						url: ajaxurl,
						type: "POST",
						data: {
							action : 'pok_get_cost',
							destination : destination,
							order_id : order_id,
							weight : weight,
							origin : origin,
							pok_action : pokOrder.nonces.get_cost
						},
						dataType:'json',
						cache: false,
						success: function(arr){
							if ( 0 !== arr.length ) {
								var options = '';
							  	$('.pok-order-shipping-result .loading').addClass('hidden');
							  	$('.pok-order-shipping-result .no-result').addClass('hidden');
							  	$.each(arr, function(key,value) {
							  		options += "<tr class='pok-switch-shipping-option' data-name='" + value.label + "' data-id='" + item_id + "' data-cost='" + value.cost + "' data-meta='" + JSON.stringify(value.meta) + "'><td class='courier'>" + value.courier_name + "</td><td class='service'>" + value.meta.service + "</td><td class='etd'>" + value.meta.etd + "</td><td class='cost'>" + value.cost_display + "</td></tr>";
								});
								$('.pok-order-shipping-result .results tbody').html(options);
								$('.pok-order-shipping-result .results').removeClass('hidden');
							} else {
								$('.pok-order-shipping-result .loading').addClass('hidden');
								$('.pok-order-shipping-result .no-result').removeClass('hidden');
								$('.pok-order-shipping-result .results').addClass('hidden');
							}
						},
						error: function(err) {
							console.log(err);
						}
					});
				} else {
					tb_remove();
					if ( local.enableDistrict ) {
						alert(local.labelNoDistrict);
					} else {
						alert(local.labelNoCity);
					}
					return false;
				}
			},

			insert_shipping: function() {
				tb_remove();
				pokOrder.fn.block();
				var label 	= $(this).data('name');
				var cost 	= $(this).data('cost');
				var meta    = $(this).data('meta');
				var order_id = $('#post_ID').val();
				$.ajax({
					url: ajaxurl,
					data: {
						action : 'pok_insert_order_shipping',
						label : label,
						cost : cost,
						order_id : order_id,
						meta : meta,
						pok_action : pokOrder.nonces.set_order_shipping
					},
					type: 'POST',
					success: function( response ) {
						if ( response.success ) {
							$( 'table.woocommerce_order_items tbody#order_shipping_line_items' ).append( response.data.html );
						} else {
							window.alert( response.data.error );
						}
						pokOrder.fn.unblock();
					}
				});
			},

			change_shipping: function() {
				tb_remove();
				pokOrder.fn.block();
				var label 	= $(this).data('name');
				var cost 	= $(this).data('cost');
				var meta    = $(this).data('meta');
				var item_id = $(this).data('id');
				var order_id = $('#post_ID').val();
				$.ajax({
					url: ajaxurl,
					data: {
						action : 'pok_change_order_shipping',
						label : label,
						cost : cost,
						order_id : order_id,
						meta : meta,
						item_id : item_id,
						pok_action : pokOrder.nonces.set_order_shipping
					},
					type: 'POST',
					success: function( response ) {
						if ( response.success ) {
							$( 'table.woocommerce_order_items tbody#order_shipping_line_items [data-order_item_id="'+item_id+'"]' ).replaceWith( response.data.html );
						} else {
							window.alert( response.data.error );
						}
						pokOrder.fn.unblock();
					}
				});
			},

			block: function() {
				$( '#woocommerce-order-items' ).block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
			},

			unblock: function() {
				$( '#woocommerce-order-items' ).unblock();
			},

		},

		run: function () {
			var el = pokOrder.el;
			var billing = el.field.billing;
			var shipping = el.field.shipping;
			var local = pokOrder.data;
			var fn = pokOrder.fn;

			el.body.on( 'country-change.woocommerce', function(event,country,el) {
				if ( 'ID' === country ) {
					el.find('._billing_city_field').hide();
					el.find('._billing_pok_city_field').show();
					el.find('._billing_pok_district_field').show();
					el.find('._shipping_city_field').hide();
					el.find('._shipping_pok_city_field').show();
					el.find('._shipping_pok_district_field').show();
				} else {
					el.find('._billing_city_field').show();
					el.find('._billing_pok_city_field').hide();
					el.find('._billing_pok_district_field').hide();
					el.find('._shipping_city_field').show();
					el.find('._shipping_pok_city_field').hide();
					el.find('._shipping_pok_district_field').hide();
				}
			} );

			fn.load_returning_user_data('billing');
			fn.load_returning_user_data('shipping');

			el.order_data.not('.prevent-changes').on( 'change', '#_billing_state', function() {
				fn.load_city( 'billing' );
			});
			el.order_data.not('.prevent-changes').on( 'change', '#_shipping_state', function() {
				fn.load_city( 'shipping' );
			});
			if (local.enableDistrict) {
				el.order_data.not('.prevent-changes').on( 'change', '#_billing_pok_city', function() {
					fn.load_district('billing');
					fn.set_city( 'billing' );
				});
				el.order_data.not('.prevent-changes').on( 'change', '#_shipping_pok_city', function() {
					fn.load_district('shipping');
					fn.set_city( 'shipping' );
				});
			}

			$( '#customer_user' ).on( 'change', function() {
				$( 'a.edit_address' ).click();
				$( 'div.order_data_column' ).block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
				fn.load_billing( true );
				fn.load_shipping( true );
			} );
			$( '#woocommerce-order-items').on( 'click', '.add-order-ongkir', fn.add_shipping );
			$( '#woocommerce-order-items').on( 'click', '.switch-order-ongkir', fn.switch_shipping );
			$( 'body' ).on( 'click', '.pok-order-shipping-result .pok-add-shipping-option', fn.insert_shipping );
			$( 'body' ).on( 'click', '.pok-order-shipping-result .pok-switch-shipping-option', fn.change_shipping );
			$( '#order_data' ).on('click', 'a.pok_load_customer_billing', fn.load_billing );
			$( '#order_data' ).on('click', 'a.pok_load_customer_shipping', fn.load_shipping );
			$( '#order_data' ).on('click', 'a.pok_billing-same-as-shipping', fn.copy_billing_to_shipping );
			$( 'a.load_customer_billing' ).addClass('pok_load_customer_billing').removeClass('load_customer_billing');
			$( 'a.load_customer_shipping' ).addClass('pok_load_customer_shipping').removeClass('load_customer_shipping');
			$( 'a.billing-same-as-shipping' ).addClass('pok_billing-same-as-shipping').removeClass('billing-same-as-shipping');
		}
	};
	pokOrder.run();
})( jQuery );