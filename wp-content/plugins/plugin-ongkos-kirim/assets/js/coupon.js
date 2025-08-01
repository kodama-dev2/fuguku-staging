jQuery(function($) {

	$('.init-select2').each(function() {
		$(this).select2();
	});

	function pok_check_coupon_type() {
		var type = $('#discount_type').val();
		var shipping_type = $('#shipping_discount_type').val();
		if ( 'ongkir' === type ) {
			$('#coupon_options .coupon_amount_field').hide();
			$('#coupon_options .free_shipping_field').hide();
			$('#coupon_options .shipping_discount_type_field').show();
			$('#woocommerce-coupon-data .tab-shipping-restriction').show();
			if ( 'fixed' === shipping_type || 'percent' === shipping_type ) {
				$('#coupon_options .shipping_discount_amount_field').show();
			} else {
				$('#coupon_options .shipping_discount_amount_field').hide();
			}
		} else {
			$('#coupon_options .coupon_amount_field').show();
			$('#coupon_options .free_shipping_field').show();
			$('#woocommerce-coupon-data .tab-shipping-restriction').hide();
			$('#coupon_options .shipping_discount_amount_field').hide();
			$('#coupon_options .shipping_discount_type_field').hide();

		}
	}
	$('#coupon_options .shipping_discount_amount_field').insertAfter('p.discount_type_field');
	$('#coupon_options .shipping_discount_type_field').insertAfter('p.discount_type_field');
	pok_check_coupon_type();

	$('#woocommerce-coupon-data').on('change', '#discount_type', function() {
		pok_check_coupon_type();
	});
	$('#woocommerce-coupon-data').on('change', '#shipping_discount_type', function() {
		pok_check_coupon_type();
	});

	$('#shipping_restriction_coupon_data').on('change', '#shipping_restriction_courier', function() {
		var couriers = $(this).val();
		if ( ! couriers ) {
			couriers = pok_data.couriers;
		}
		var options = {};
		if ( couriers && couriers.length ) {
			couriers.forEach( function( courier, i ) {
				if ( pok_data.services[ courier ] ) {
					for ( var s in pok_data.services[ courier ] ) {
						options[ courier + '-' + s ] = $('#shipping_restriction_courier option[value="' + courier + '"]').text() + ' - ' + pok_data.services[ courier ][ s ]['name'];
					}
				}
			} );
		}
		var html = '';
		for ( var s in options ) {
			html += '<option value="' + s + '">' + options[s] + '</option>';
		}
		$('#shipping_restriction_service').html(html);
	});

	$('.pok-coupon-destination').on( 'change', '.select_province', function() {
		var province_id = $(this).val();
		pok_load_city_list( province_id, $(this).parents('tr') );
	} );

	$('.pok-coupon-destination').on( 'change', '.select_city', function() {
		var city_id = $(this).val();
		pok_load_district_list( city_id, $(this).parents('tr') );
	});

	$('.add-destination').on( 'click', function() {
		var id = randomString();
		var row = $('.pok-coupon-destination .repeater').clone();
		row.removeClass('repeater');
		row.find( '.select_province' ).attr( 'name', 'shipping_restriction[destination]['+id+'][province]' );
		row.find( '.select_city' ).attr( 'name', 'shipping_restriction[destination]['+id+'][city]' );
		row.find( '.select_district' ).attr( 'name', 'shipping_restriction[destination]['+id+'][district]' );
		$('.pok-coupon-destination tbody').append( row );
	} );

	$('.pok-coupon-destination').on('click', '.remove-manual', function() {
		$(this).parents('tr').remove();
	});

	function pok_load_city_list( province_id, target ) {
		target.find( '.select_city, .select_district').prop('disabled',true);
  		var arrCity  = '<option value="">' + pok_translations.all_city + '</option>';
  		var arrDistrict  = '<option value="">' + pok_translations.all_district + '</option>';
		if ( '*' !== province_id && '' !== province_id ) {
			$.ajax({
				url: ajaxurl,
				type: "POST",
				data: {
					action : 'pok_get_list_city',
					province_id : province_id,
					pok_action : pok_nonces.get_list_city
				},
				dataType:'json',
				cache: false,
				success: function(arr){
				  	var selectList = '';
				  	target.find('.select_city').val('').empty().append(arrCity);
					target.find('.select_district').val('').empty().append(arrDistrict); 
				  	$.each(arr, function(key,value) {
						var data = {};
						arrCity += '<option value='+ key + '>'+ value +'</option>';
					});
					target.find('.select_city, .select_district').prop('disabled',false);
					target.find('.select_city').html(arrCity).trigger('setvalue').trigger('change');
				},
				error: function(err) {
					console.log(err);
				}
			});
		} else {
			target.find('.select_city').prop('disabled',false).html(arrCity).trigger('change');
			target.find('.select_district').prop('disabled',false).html(arrDistrict);
		}
	}

	function pok_load_district_list( city_id, target ) {
		target.find('.select_district').prop('disabled',true);
		var arrDistrict = '<option value="">' + pok_translations.all_district + '</option>';
		if ( '*' !== city_id && '' !== city_id ) {
			$.ajax({
				url: ajaxurl,
				type: "POST",
				data: {
					action : 'pok_get_list_district',
					city_id : city_id,
					pok_action : pok_nonces.get_list_district
				},
				dataType:'json',
				cache: false,
				success: function(arr){
				  	var selectList = '';
				  	target.find('.select_district').val('').empty().append(arrDistrict); 
				  	$.each(arr, function(key,value) {
						var data = {};
						arrDistrict += '<option value='+ key + '>'+ value +'</option>';
					});
					target.find('.select_district').html(arrDistrict).trigger('setvalue').prop('disabled',false);
			  	}
			});
		} else {
			target.find('.select_district').html(arrDistrict).prop('disabled',false);
		}
	}

	function randomString() {
		var text = "";
		var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		for (var i = 0; i < 15; i++) {
			text += possible.charAt(Math.floor(Math.random() * possible.length));
		}
		return text;
	}

});