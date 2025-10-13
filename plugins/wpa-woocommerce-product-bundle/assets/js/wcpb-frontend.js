function wpa_wcpb_add_to_cart( obj ) {
	if ( obj.hasClass( 'wc-variation-selection-needed' ) ) {
		jQuery('.wpa-error').css('opacity', '1');
		return;
	}
	jQuery('.wpa-error').css('opacity', '0');
	var parent 						= obj.parent(),
		list_product_id 			= '',
		variable 					= obj.attr('variation_id'),
		main_pro_meta				= {},
		bundle_variable 			= {};
		bundle_variable_json		= '',
		loader 						= jQuery('.wpa-wcpb-list .loader');
	loader.css('display', 'inline-block');

	jQuery( '.list-select .item', parent ).each(function(){
		var checked = jQuery( 'input[type="checkbox"]:checked', jQuery(this) ).length;
		if ( checked ) {
			list_product_id += jQuery(this).attr('data-product-id') + ',';
		}
	});

	// Get Custom Variable of Bundle
	jQuery('.plt-variations-form').each(function( index1, el1 ){
		if (!jQuery(el1).hasClass('main-product')) {
			var bundle_id 				= jQuery(el1).data('product_id'),
			bundle_item_vari 		= {},
			// check_default 			= jQuery( 'select option:selected', this ).data( 'default' ),
			bundle_price 			= jQuery( el1 ).parent().data('item-price'),
			bundle_variation 		= '',
			attribute_count 		= jQuery( 'select', el1 ).length;
			jQuery( 'select', el1 ).each(function(index, el){
				if (jQuery(el).val() != '') {
					bundle_variation += jQuery(el).data('attribute').replace('pa_', '') + ': ' + jQuery(el).val();
					if (index < attribute_count - 1) {
						bundle_variation += ' + ';
					}
					bundle_item_vari['attribute_'+jQuery(el).data('attribute')] = jQuery(el).val();
				}
			});

			bundle_item_vari['price'] = bundle_price;
			bundle_item_vari['variable'] = bundle_variation;
			bundle_variable[bundle_id] = bundle_item_vari;
		} else {
			jQuery( 'select', el1 ).each(function(index, el){
				if (jQuery(el).val() != '') {
					main_pro_meta['attribute_' + jQuery(el).data('attribute')] = jQuery(el).val();
				}
			});

		}
	});

	bundle_variable_json = JSON.parse(JSON.stringify(bundle_variable));

	if ( list_product_id ) {
		jQuery.ajax( {
			type : "POST",
			url  : wpa_wcpb.ajaxurl,
			data : {
				'action' : 'wpa_wcpb_add_to_cart',
				'list_product_id' : list_product_id,
				'variable': variable,
				'main_pro_meta': main_pro_meta,
				'bundle_variable': bundle_variable_json,
				'_nonce' : wpa_wcpb._nonce,
			},
			success : function( response ) {
				wpa_wcpb_toggleClass_loading('done', loader[0]);
				jQuery('.wpa-message').css('opacity', '1');
				// Update mini cart
				if ( jQuery('.widget_shopping_cart_content').length ){
					jQuery.post(
						wpa_wcpb.ajaxurl,
						{'action': 'wpa_wcpb_update_mini_cart'},
						function(response) {
							jQuery('.widget_shopping_cart_content').html(response);
						}
					);
				}
			}
		} );
	}
}

function math_round(number){
    return (Math.round(number * 100)/100).toFixed(2);
}

function wpa_wcpb_toggleClass_loading(toggleClassName, target) {
	var currentClassName = ' '+target.className+' ';
	if(~currentClassName.indexOf(' '+toggleClassName+' ')) {
		target.className = currentClassName.replace(' '+toggleClassName+' ', ' ').trim();
	} else {
		target.className = (currentClassName+' done').trim();
	}
}

function wpa_wcpb_onchange_input_check_total_discount(){
	var total_price = 0,
		wpa_wcpb_list = jQuery('.wpa-wcpb-list'),
		product_bundles = jQuery('.px-product-bundles'),
		input_checked_lenght = jQuery('.px-product-bundles input[type=checkbox]:checked').length,
		product_bundle_data = product_bundles.attr('data-total-discount')
		product_bundle_data_arr = product_bundle_data.split(','),
		bundle_percent = product_bundle_data_arr[input_checked_lenght - 1],
		currencySymbol = '<span class="woocommerce-Price-currencySymbol">' + jQuery('.total.price .current-price span.woocommerce-Price-amount .woocommerce-Price-currencySymbol', wpa_wcpb_list).html() + '</span>',
		remove_thousand_separator = '';
		remove_thousand_separator = wpa_wcpb.thousand_separator == '.' ? '\\.' : wpa_wcpb.thousand_separator;
		remove_thousand_separator = new RegExp(remove_thousand_separator, "g");
	if ( ! bundle_percent ) {
		for ( var i = product_bundle_data_arr.length - 1; i >= 0; i-- ) {
			if ( product_bundle_data_arr[i] ) {
				bundle_percent = product_bundle_data_arr[i];
				break;
			}
		};
	}

	jQuery('.px-product-bundles input[type=checkbox]:checked').each(function(){
		var parent = jQuery(this).parent().parent(),
			price = parent.attr('data-item-price-with-filter').replace(remove_thousand_separator, '').replace(',', '.'),
			new_price = math_round(parseFloat( price ) * ( 100 - parseFloat( bundle_percent ) )/ 100);
			// replace decimal seperator
			new_price = new_price.toString().replace('.', wpa_wcpb.decimal_separator).replace(/\B(?=(\d{3})+(?!\d))/g, wpa_wcpb.thousand_separator);
			var htmlSymbol = '<span class="woocommerce-Price-currencySymbol">' + jQuery('.price > span.woocommerce-Price-amount .woocommerce-Price-currencySymbol', parent).html() + '</span>';
		if (wpa_wcpb.currency_pos.indexOf('right') >= 0) {
			var priceHtml = new_price + htmlSymbol;
		} else {
			var priceHtml = htmlSymbol + new_price;
		}
		jQuery('.price > span.woocommerce-Price-amount', parent).html( priceHtml );
		total_price += parseFloat( price );
	});

	if (wpa_wcpb.currency_pos.indexOf('right') >= 0) {
		var currencySymbolLeft = '';
		var currencySymbolRight = currencySymbol;
	} else {
		var currencySymbolLeft = currencySymbol;
		var currencySymbolRight = '';
	}

	jQuery('.total.price .current-price span.woocommerce-Price-amount', wpa_wcpb_list).html(
		currencySymbolLeft + math_round(parseFloat( total_price ) * ( 100 - parseFloat( bundle_percent ) ) / 100 ).toString().replace('.', wpa_wcpb.decimal_separator).replace(/\B(?=(\d{3})+(?!\d))/g, wpa_wcpb.thousand_separator) + currencySymbolRight
	);
	jQuery('.total.price .old-price span.woocommerce-Price-amount', wpa_wcpb_list).html(
		currencySymbolLeft + math_round(total_price).toString().replace('.', wpa_wcpb.decimal_separator).replace(/\B(?=(\d{3})+(?!\d))/g, wpa_wcpb.thousand_separator) + currencySymbolRight
	);
	jQuery('.total.price .save-price span.woocommerce-Price-amount', wpa_wcpb_list).html(
		currencySymbolLeft + math_round( parseFloat( total_price ) * parseFloat( bundle_percent ) / 100  ).toString().replace('.', wpa_wcpb.decimal_separator).replace(/\B(?=(\d{3})+(?!\d))/g, wpa_wcpb.thousand_separator) + currencySymbolRight
	);
	jQuery('.total.price .save-percent', wpa_wcpb_list).html(
		bundle_percent
	);
}

function wpa_wcpb_onchange_input_check_discount_per_item(){
	var total_price = 0,
		bundles_price = 0,
		wpa_wcpb_list = jQuery('.wpa-wcpb-list'),
		product_bundles = jQuery('.px-product-bundles'),
		currencySymbol = '<span class="woocommerce-Price-currencySymbol">' + jQuery('.total.price .current-price span.woocommerce-Price-amount .woocommerce-Price-currencySymbol', wpa_wcpb_list).html() + '</span>';

	jQuery('.px-product-bundles input[type=checkbox]:checked').each(function(){
		var parent = jQuery(this).parent().parent(),
			item_price = parent.attr('data-item-price-with-filter');
			item_percent = ( parent.attr('data-item-percent') ) ? parent.attr('data-item-percent') : 0,
			new_item_price = parseFloat( item_price ) * ( 100 - parseFloat( item_percent )) / 100;
		bundles_price += parseFloat( new_item_price );
		total_price += parseFloat( item_price );
	});

	jQuery('.total.price .current-price span.woocommerce-Price-amount', wpa_wcpb_list).html(
		currencySymbol + bundles_price
	);
	jQuery('.total.price .old-price span.woocommerce-Price-amount', wpa_wcpb_list).html(
		currencySymbol + total_price
	);
	jQuery('.total.price .save-price span.woocommerce-Price-amount', wpa_wcpb_list).html(
		currencySymbol + ( total_price - bundles_price )
	);
	jQuery('.total.price .save-percent', wpa_wcpb_list).html(
		parseFloat( 100 * ( 1 - bundles_price / total_price ) )
	);
}


;(function ( $, window, document, undefined ) {
	"use strict";

	$.PLT = $.PLT || {};

	// ======================================================
	// Main Product Variation Select
	// ------------------------------------------------------
	$.PLT.main_product_variation_select = function () {
		if ( !$( '.variations_form' ).length ) return;
		$( '.variations_form .variations select' ).each(function() {
			$(this).blur(function(){
				if ( $( '.variations_form .single_add_to_cart_button' ).hasClass( 'disabled' ) ) {
					$( '.wpa_wcpb_add_to_cart' ).addClass( 'wc-variation-selection-needed' );
					$( '.wpa_wcpb_add_to_cart' ).addClass( 'disabled' );
				}else {
					$( '.wpa_wcpb_add_to_cart' ).removeClass( 'wc-variation-selection-needed' );
					$( '.wpa_wcpb_add_to_cart' ).removeClass( 'disabled' );
				}
				var newattr = $(this).attr( 'name' ),
					newattrval = $(this).val(),
					variation_id = $( 'input[name="variation_id"], input.variation_id', $( '.variations_form' ) ).val();
				$( '.wpa_wcpb_add_to_cart' ).attr( newattr, newattrval );
				$( '.wpa_wcpb_add_to_cart' ).attr( 'variation_id', variation_id );
				$( '.wpa-wcpb-list .item-main select[data-attribute="'+ newattr.replace('attribute_', '') +'"]' ).val(newattrval).change();
			});
		});
	}

	// ======================================================
	// Main Product Variation onload Default
	// ------------------------------------------------------
	$.PLT.main_product_variation_load_default = function () {
		if ( !$( '.variations_form' ).length ) return;
		if ( $( '.variations_form .single_add_to_cart_button' ).hasClass( 'disabled' ) ) {
			$( '.wpa_wcpb_add_to_cart' ).addClass( 'wc-variation-selection-needed' );
			$( '.wpa_wcpb_add_to_cart' ).addClass( 'disabled' );
		}else {
			$( '.wpa_wcpb_add_to_cart' ).removeClass( 'wc-variation-selection-needed' );
			$( '.wpa_wcpb_add_to_cart' ).removeClass( 'disabled' );
		}
		var variation_id = $( 'input[name="variation_id"], input.variation_id', $( '.variations_form' ) ).val();
		$( '.wpa_wcpb_add_to_cart' ).attr( 'variation_id', variation_id );
	}

	// ======================================================
	// Product Bundle Variation Select
	// ------------------------------------------------------
	$.PLT.product_bundle_variation_select = function () {
		if ( !$( '.plt-variations-form select' ).length ) return;
		var remove_thousand_separator = '';
		remove_thousand_separator = wpa_wcpb.thousand_separator == '.' ? '\\.' : wpa_wcpb.thousand_separator;
		remove_thousand_separator = new RegExp(remove_thousand_separator, "g");
		$( '.plt-variations-form select' ).each(function() {
			$(this).change(function(){
				var parent = $(this).parents('.item'),
					selects = parent.find('select'),
					bundle_price = false,
					varations = parent.find('.plt-variations-form').data('variation'),
					wpa_wcpb_list = jQuery('.wpa-wcpb-list'),
					currencySymbol = '<span class="woocommerce-Price-currencySymbol">' + jQuery('.total.price .current-price span.woocommerce-Price-amount .woocommerce-Price-currencySymbol', wpa_wcpb_list).html() + '</span>',
					selected = [],
					varation_id = 0,
				    current_select = $(this),
					current_attribute = $(this).data('attribute');

				if ( selects.hasOwnProperty(1) && $(selects[0]).data('attribute') == current_attribute ){
					//$(selects[1]).find('option').removeAttr('disabled');
					$(selects[1]).find('option').attr('disabled', 'disabled');
				}

				$.each(varations, function(indexv, varation){
					selected[indexv] = 0;
					selects.each(function(indexs, select){
						var value = $(this).val(),
						attribute = $(this).data('attribute');
						if (typeof varation['attributes']['attribute_' + attribute] != 'undefined' && (varation['attributes']['attribute_' + attribute] == value || varation['attributes']['attribute_' + attribute] == '')) {
							selected[indexv] += 1;

							if (
								indexs == 0 && attribute == current_attribute
								&& selects.hasOwnProperty(1)
								&& typeof varation['attributes']['attribute_' + $(selects[1]).data('attribute')] != 'undefined'
							){

								if ( varation['attributes']['attribute_' + $(selects[1]).data('attribute')] != '' ){

									var avalable_value = varation['attributes']['attribute_' + $(selects[1]).data('attribute')];
									$(selects[1]).find('option').removeAttr('selected');
									$(selects[1]).find('option').each(function(indexo, el){
										if ( $(this).attr('value') == avalable_value ){
											$(this).removeAttr('disabled');
											$(this).attr('selected', 'selected');
											$(this).val(avalable_value);
										}
									});

								} else {
									$(selects[1]).find('option').removeAttr('disabled');
								}
							}

						}
						if (typeof varation['attributes']['attribute_' + attribute] != 'undefined' && varation['attributes']['attribute_' + attribute] != value) {
							selected[indexv] -= 1;
						}
					});
				});

				var match_varation = varations[selected.indexOf(Math.max.apply(Math, selected))];
				bundle_price = match_varation['display_price'];
				varation_id = match_varation['variation_id'];

				if ($(this).parents('.plt-variations-form').hasClass('main-product') && varation_id > 0) {
					$( '.wpa_wcpb_add_to_cart' ).attr( 'variation_id', varation_id );
				}

				if (bundle_price == false) {
					$(this).find('option[value="'+ $(this).data('default') +'"]');
				} else {
					parent.attr('data-item-price', bundle_price);
					parent.attr('data-item-price-with-filter', match_varation['price_with_filter']);
					var price = match_varation['price_with_filter'].replace(remove_thousand_separator, '').replace(',', '.');
					price = math_round(parseFloat( price )).toString().replace('.', wpa_wcpb.decimal_separator).replace(/\B(?=(\d{3})+(?!\d))/g, wpa_wcpb.thousand_separator);
					if (wpa_wcpb.currency_pos.indexOf('right') >= 0) {
						price = price + currencySymbol;
					} else {
						price = currencySymbol + price;
					}

					$('.info-item .price del .woocommerce-Price-amount', parent).html( price );
					wpa_wcpb_onchange_input_check_total_discount();
				}
			});
		});
	}

	$( document ).ready( function() {
		$(window).load(function() {
			$.PLT.main_product_variation_select();
			$.PLT.main_product_variation_load_default();
			$.PLT.product_bundle_variation_select();
			$( '.plt-variations-form select' ).trigger('change');
		});
		if ($('.wpa-wcpb-list').length > 0) {
			wpa_wcpb_onchange_input_check_total_discount();
		};

	} );
})( jQuery, window, document );



