jQuery(function($) {

	var active_tab;
	var wrapper = "#pok-setting";

	if (window.location.hash) {
		var current = getHash();
		if (current.indexOf('tab-') === 0) {
			set_active_tab(current.replace('tab-',''));
		} else {
			set_active_tab_first();
		}
	} else {
		set_active_tab_first();
	}

	$(window).on('hashchange', function(){
		var current = getHash();
		if (current.indexOf('tab-') === 0) {
			set_active_tab(current.replace('tab-',''));
		}
	});

	function getHash() {
		var hash = window.location.hash;
		if (!hash) return;
		if (hash.indexOf('#') === 0) {
			return hash.replace('#','');
		}
	}

	function set_active_tab(section) {
		$( wrapper + ' .sections-nav .tab').each(function() {
			$(this).removeClass('tab-active');
		});
		$( wrapper + ' .sections-nav .tab-'+section).addClass('tab-active');
		$( wrapper + ' .sections-container .section').each(function() {
			$(this).removeClass('active');
		});
		$(wrapper + ' .sections-container .section-'+section).addClass('active');
	}

	function set_active_tab_first() {
		var first_menu = $(wrapper + ' .sections-nav .tab:first-child a').attr('href');
		if (first_menu) {
			if(window.history.pushState) {
				window.history.pushState(null, null, first_menu);
			} else {
				window.location.hash = first_menu;
			}
			var current = getHash();
			if (current.indexOf('tab-') === 0) {
				set_active_tab(current.replace('tab-',''));
			}
		}
	}


	$('.select2-ajax').each(function() {
		var action 	= $(this).data('action');
		var phrase	= $(this).val();
		var nonce 	= $(this).data('nonce');
		$(this).select2({
			ajax: {
				url: ajaxurl,
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

	$('.init-select2').each(function() {
		$(this).select2();
	});

	$('.rajaongkir2-store-location').on('change', function() {
		var selected = $(this).find(':selected');
		$('[name="pok_setting[store_location][1]"]').val( selected.text() );
	})

	// toggle base api
	$('input[name="pok_setting[base_api]"]').on('click', function() {
		var selectedValue = $(this).val();
		var currentValue  = pok_settings.base_api;
		if ( selectedValue !== currentValue ) {
			if ( 'rajaongkir' === selectedValue && ! pok_settings.rajaongkir_status[0] ) {
				window.alert( pok_translations.switch_base_api_rajaongkir );
				$( '.rajakongkir-api-fields' ).addClass( 'show' );
				$( '.rajakongkir2-api-fields' ).removeClass( 'show' );
			} else if ( 'rajaongkir2' === selectedValue && ! pok_settings.rajaongkir2_status[0] ) {
				window.alert( pok_translations.switch_base_api_rajaongkir2 );
				$( '.rajakongkir-api-fields' ).removeClass( 'show' );
				$( '.rajakongkir2-api-fields' ).addClass( 'show' );
			} else {
				var confirm = window.confirm( pok_translations.confirm_change_base_api );
				if ( confirm ) {
					$('.pok-setting-content').block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});
					if ( 'nusantara' === selectedValue ) {
						window.location = pok_urls.switch_base_api_tonjoo;	
					} else if ( 'rajaongkir' === selectedValue ) {
						window.location = pok_urls.switch_base_api_rajaongkir;
					} else if ( 'rajaongkir2' === selectedValue ) {
						window.location = pok_urls.switch_base_api_rajaongkir2;
					}
				} else {
					return false;
				}
			}
		} else {
			if ( 'rajaongkir' === selectedValue ) {
				$( '.rajakongkir-api-fields' ).addClass( 'show' );
				$( '.rajakongkir2-api-fields' ).removeClass( 'show' );
			} else if ( 'rajaongkir' === selectedValue ) {
				$( '.rajakongkir-api-fields' ).removeClass( 'show' );
				$( '.rajakongkir2-api-fields' ).addClass( 'show' );
			} else {
				$( '.rajakongkir-api-fields' ).removeClass( 'show' );
				$( '.rajakongkir2-api-fields' ).removeClass( 'show' );
			}
		}
	});

	// toggle base api
	// $('input[name="pok_setting[base_api]"]').on('click', function() {
	// 	var value = $(this).val();
	// 	if ( 'nusantara' === pok_settings.base_api ) {
	// 		if ( 'rajaongkir' === value ) {
	// 			if ( ! pok_settings.rajaongkir_status[0] ) {
	// 				window.alert( pok_translations.switch_base_api_rajaongkir );
	// 				$( '.rajakongkir-api-fields' ).addClass( 'show' );
	// 				$( '.rajakongkir2-api-fields' ).removeClass( 'show' );
	// 			} else {
	// 				var confirm = window.confirm( pok_translations.confirm_change_base_api );
	// 				if ( confirm ) {
	// 					$('.pok-setting-content').block({
	// 						message: null,
	// 						overlayCSS: {
	// 							background: '#fff',
	// 							opacity: 0.6
	// 						}
	// 					});
	// 					window.location = pok_urls.switch_base_api_rajaongkir;
	// 				} else {
	// 					return false;
	// 				}
	// 			}
	// 		} else if ( 'rajaongkir2' === value ) {
	// 			if ( ! pok_settings.rajaongkir2_status[0] ) {
	// 				window.alert( pok_translations.switch_base_api_rajaongkir2 );
	// 				$( '.rajakongkir-api-fields' ).removeClass( 'show' );
	// 				$( '.rajakongkir2-api-fields' ).addClass( 'show' );
	// 			} else {
	// 				var confirm = window.confirm( pok_translations.confirm_change_base_api );
	// 				if ( confirm ) {
	// 					$('.pok-setting-content').block({
	// 						message: null,
	// 						overlayCSS: {
	// 							background: '#fff',
	// 							opacity: 0.6
	// 						}
	// 					});
	// 					window.location = pok_urls.switch_base_api_rajaongkir2;
	// 				} else {
	// 					return false;
	// 				}
	// 			}
	// 		} else {
	// 			$( '.rajakongkir-api-fields' ).removeClass( 'show' );
	// 			$( '.rajakongkir2-api-fields' ).removeClass( 'show' );
	// 		}
	// 	} else if ( 'rajaongkir' === pok_settings.base_api ) {
	// 		if ( 'nusantara' === value ) {
	// 			var confirm = window.confirm( pok_translations.confirm_change_base_api );
	// 			if ( confirm ) {
	// 				$('.pok-setting-content').block({
	// 					message: null,
	// 					overlayCSS: {
	// 						background: '#fff',
	// 						opacity: 0.6
	// 					}
	// 				});
	// 				window.location = pok_urls.switch_base_api_tonjoo;
	// 			} else {
	// 				return false;
	// 			}
	// 		} else if ( 'rajaongkir2' === value ) {
	// 			if ( ! pok_settings.rajaongkir2_status[0] ) {
	// 				window.alert( pok_translations.switch_base_api_rajaongkir2 );
	// 				$( '.rajakongkir-api-fields' ).removeClass( 'show' );
	// 				$( '.rajakongkir2-api-fields' ).addClass( 'show' );
	// 			} else {
	// 				var confirm = window.confirm( pok_translations.confirm_change_base_api );
	// 				if ( confirm ) {
	// 					$('.pok-setting-content').block({
	// 						message: null,
	// 						overlayCSS: {
	// 							background: '#fff',
	// 							opacity: 0.6
	// 						}
	// 					});
	// 					window.location = pok_urls.switch_base_api_rajaongkir2;
	// 				} else {
	// 					return false;
	// 				}
	// 			}
	// 		} else {
	// 			$( '.rajakongkir-api-fields' ).addClass( 'show' );
	// 			$( '.rajakongkir2-api-fields' ).removeClass( 'show' );
	// 		}
	// 	} else {
	// 		if ( 'nusantara' === value ) {
	// 			var confirm = window.confirm( pok_translations.confirm_change_base_api );
	// 			if ( confirm ) {
	// 				$('.pok-setting-content').block({
	// 					message: null,
	// 					overlayCSS: {
	// 						background: '#fff',
	// 						opacity: 0.6
	// 					}
	// 				});
	// 				window.location = pok_urls.switch_base_api_tonjoo;
	// 			} else {
	// 				return false;
	// 			}
	// 		} else if ( 'rajaongkir' === value ) {
	// 			if ( ! pok_settings.rajaongkir_status[0] ) {
	// 				window.alert( pok_translations.switch_base_api_rajaongkir );
	// 				$( '.rajakongkir-api-fields' ).addClass( 'show' );
	// 				$( '.rajakongkir2-api-fields' ).removeClass( 'show' );
	// 			} else {
	// 				var confirm = window.confirm( pok_translations.confirm_change_base_api );
	// 				if ( confirm ) {
	// 					$('.pok-setting-content').block({
	// 						message: null,
	// 						overlayCSS: {
	// 							background: '#fff',
	// 							opacity: 0.6
	// 						}
	// 					});
	// 					window.location = pok_urls.switch_base_api_rajaongkir;
	// 				} else {
	// 					return false;
	// 				}
	// 			}
	// 		} else {
	// 			return false;
	// 		}
	// 	}
	// });
	var base_api = $('input[name="pok_setting[base_api]"]:checked').val();
	if ( 'rajaongkir' === base_api ) {
		$( '.rajakongkir-api-fields' ).addClass( 'show' );
	} else if ( 'rajaongkir2' === base_api ) {
		$( '.rajakongkir2-api-fields' ).addClass( 'show' );
	} else {
		$( '.rajakongkir-api-fields' ).removeClass( 'show' );
	}

	$('#set-rajaongkir-key').on('click', function() {
		var api_type = $('select[name="pok_setting[rajaongkir_type]"]').val();
		var api_key = $('input[name="pok_setting[rajaongkir_key]"]').val();
		if ( "" === api_key ) {
			$('.rajaongkir-key-response').text( pok_translations.api_key_empty );
			return;
		}
		$('.rajaongkir-key-response').removeClass('success').addClass('loading').text( pok_translations.connecting_server );
		$(this).prop('disabled',true);
		$.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				pok_action: pok_nonces.set_rajaongkir_api_key,
				action: "pok_set_rajaongkir_api_key",
				api_key: api_key,
				api_type: api_type
			},
			context: this,
			success: function(res) {
				if ( "success" === res ) {
					$('.pok-setting-content').block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});
					location.reload(true);
				} else if ( "" === res ) {
					$('.rajaongkir-key-response').text( 'loading...' );
					$(this).prop('disabled',false);
				} else {
					$('.rajaongkir-key-response').removeClass('loading').removeClass('success').text(res);
					$(this).prop('disabled',false);
				}
			},
			error: function(err, err1) {
				$('.rajaongkir-key-response').removeClass('success').removeClass('loading').text( pok_translations.cant_connect_server );
				$(this).prop('disabled',false);
			}
		});
	});
	$('#set-rajaongkir2-key').on('click', function() {
		var api_key = $('input[name="pok_setting[rajaongkir2_key]"]').val();
		if ( "" === api_key ) {
			$('.rajaongkir2-key-response').text( pok_translations.api_key_empty );
			return;
		}
		$('.rajaongkir2-key-response').removeClass('success').addClass('loading').text( pok_translations.connecting_server );
		$(this).prop('disabled',true);
		$.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				pok_action: pok_nonces.set_rajaongkir2_api_key,
				action: "pok_set_rajaongkir2_api_key",
				api_key: api_key
			},
			context: this,
			success: function(res) {
				if ( "success" === res ) {
					$('.pok-setting-content').block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});
					location.reload(true);
				} else if ( "" === res ) {
					$('.rajaongkir2-key-response').text( 'loading...' );
					$(this).prop('disabled',false);
				} else {
					$('.rajaongkir2-key-response').removeClass('loading').removeClass('success').text(res);
					$(this).prop('disabled',false);
				}
			},
			error: function(err, err1) {
				$('.rajaongkir2-key-response').removeClass('success').removeClass('loading').text( pok_translations.cant_connect_server );
				$(this).prop('disabled',false);
			}
		});
	});

	// select courier
	function pok_get_selected_courier() {
		var couriers = [];
		$('.options-specific-service > div').hide();
		$('[name="pok_setting[couriers][]"]:checked').each(function() {
			$('.options-specific-service .options-specific-service-'+$(this).val()).show();
		});
	}
	pok_get_selected_courier();

	$('[name="pok_setting[couriers][]"]').on('click', function() {
		pok_get_selected_courier();
	});

	// toggle filter specific courier
	$('input[name="pok_setting[specific_service]"]').on('click', function() {
		var value = $(this).val();
		if ( 'yes' === value ) {
			$( '.options-specific-service' ).addClass( 'show' );
		} else {
			$( '.options-specific-service' ).removeClass( 'show' );
		}
	});
	var specific_service = $('input[name="pok_setting[specific_service]"]:checked').val();
	if ( 'yes' === specific_service ) {
		$( '.options-specific-service' ).addClass( 'show' );
	} else {
		$( '.options-specific-service' ).removeClass( 'show' );
	}

	// accordion filter specific courier
	$('.options-specific-service').on( 'click', '.service-options h5, .service-options p', function() {
		var parent = $(this).parent();
		parent.toggleClass('expand');
		$('.options-specific-service .service-options').not(parent).removeClass('expand');
	} );
	$('.options-specific-service input[type=checkbox]').on( 'change', function() {
		$('.options-specific-service .service-options').each( function() {
			var checked = [];
			var courier = $(this);
			courier.find('input[type=checkbox]').each(function() {
				if ( $(this).is(':checked') ) {
					checked.push($(this).data('short'));
				}
			});
			if ( checked.length ) {
				courier.find('p').html(checked.join(', '));
			} else {
				courier.find('p').html(pok_translations.no_service_selected);
			}
		});
	});

	// toggle rounding
	$('input[name="pok_setting[round_weight]"]').on('click', function() {
		var value = $(this).val();
		if ( 'auto' === value ) {
			$( '.options-round-weight' ).addClass( 'show' );
		} else {
			$( '.options-round-weight' ).removeClass( 'show' );
		}
	});
	var round_weight = $('input[name="pok_setting[round_weight]"]:checked').val();
	if ( 'auto' === round_weight ) {
		$( '.options-round-weight' ).addClass( 'show' );
	} else {
		$( '.options-round-weight' ).removeClass( 'show' );
	}

	// toggle insurance fee
	$('select[name="pok_setting[enable_insurance]"]').on('change', function() {
		var value = $(this).val();
		if ( 'yes' === value || 'set' === value ) {
			$( '.options-enable-insurance' ).addClass( 'show' );
		} else {
			$( '.options-enable-insurance' ).removeClass( 'show' );
		}
	});
	var enable_insurance = $('select[name="pok_setting[enable_insurance]"]').val();
	if ( 'yes' === enable_insurance || 'set' === enable_insurance ) {
		$( '.options-enable-insurance' ).addClass( 'show' );
	} else {
		$( '.options-enable-insurance' ).removeClass( 'show' );
	}

	// toggle timber packing fee
	$('select[name="pok_setting[enable_timber_packing]"]').on('change', function() {
		var value = $(this).val();
		if ( 'yes' === value || 'set' === value ) {
			$( '.options-enable-timber_packing' ).addClass( 'show' );
		} else {
			$( '.options-enable-timber_packing' ).removeClass( 'show' );
		}
	});
	var enable_timber_packing = $('select[name="pok_setting[enable_timber_packing]"]').val();
	if ( 'yes' === enable_timber_packing || 'set' === enable_timber_packing ) {
		$( '.options-enable-timber_packing' ).addClass( 'show' );
	} else {
		$( '.options-enable-timber_packing' ).removeClass( 'show' );
	}

	// toggle unique number
	$('input[name="pok_setting[unique_number]"]').on('click', function() {
		var value = $(this).val();
		if ( 'yes' === value ) {
			$( '.options-unique-number' ).addClass( 'show' );
		} else {
			$( '.options-unique-number' ).removeClass( 'show' );
		}
	});
	var unique_number = $('input[name="pok_setting[unique_number]"]:checked').val();
	if ( 'yes' === unique_number ) {
		$( '.options-unique-number' ).addClass( 'show' );
	} else {
		$( '.options-unique-number' ).removeClass( 'show' );
	}

	// toggle markup fee
	$('input[name="pok_setting[markup_fee]"]').on('click', function() {
		var value = $(this).val();
		if ( 'yes' === value ) {
			$( '.options-markup-fee' ).addClass( 'show' );
		} else {
			$( '.options-markup-fee' ).removeClass( 'show' );
		}
	});
	var markup_fee = $('input[name="pok_setting[markup_fee]"]:checked').val();
	if ( 'yes' === markup_fee ) {
		$( '.options-markup-fee' ).addClass( 'show' );
	} else {
		$( '.options-markup-fee' ).removeClass( 'show' );
	}

	// toggle debug mode
	$('input[name="pok_setting[debug_mode]"]').on('click', function() {
		var value = $(this).val();
		if ( 'yes' === value ) {
			$( '.options-debug-mode' ).addClass( 'show' );
		} else {
			$( '.options-debug-mode' ).removeClass( 'show' );
		}
	});
	var debug_mode = $('input[name="pok_setting[debug_mode]"]:checked').val();
	if ( 'yes' === debug_mode ) {
		$( '.options-debug-mode' ).addClass( 'show' );
	} else {
		$( '.options-debug-mode' ).removeClass( 'show' );
	}

	// toggle currency conversion
	$('select[name="pok_setting[currency_conversion]"]').on('change', function() {
		var value = $(this).val();
		$( '.options-currency' ).removeClass('show');
		$( '.options-currency-'+value ).addClass('show');
	});
	var currency_conversion = $('select[name="pok_setting[currency_conversion]"]').val();
	$( '.options-currency' ).removeClass('show');
	$( '.options-currency-'+currency_conversion ).addClass('show');

	// check fixer
	$('#check-fixer-api').on('click', function() {
		var api_key = $('input[name="pok_setting[currency_fixer_api_key]"]').val();
		var result = $(this).parent().find('.api-response');
		if ( "" === api_key ) {
			result.html( '<span class="error">' + pok_translations.api_key_empty + '</span>' );
			return;
		}
		$(this).prop('disabled',true);
		result.html('');
		$.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				pok_action: pok_nonces.check_fixer_api,
				action: "pok_check_fixer_api",
				api_key: api_key
			},
			context: this,
			success: function(res) {
				if ( 'API Active' === res ) {
					result.html( '<span class="success">API Active</span>' );
				} else {
					result.html( '<span class="error">' + res + '</span>' );
				}
				$(this).prop('disabled',false);
			},
			error: function(err, err1) {
			result.html( '<span class="error">' + pok_translations.cant_connect_server + '</span>' );
				$(this).prop('disabled',false);
			}
		});
	});

	// check currencylayer
	$('#check-currencylayer-api').on('click', function() {
		var api_key = $('input[name="pok_setting[currency_currencylayer_api_key]"]').val();
		var result = $(this).parent().find('.api-response');
		if ( "" === api_key ) {
			result.html( '<span class="error">' + pok_translations.api_key_empty + '</span>' );
			return;
		}
		$(this).prop('disabled',true);
		result.html('');
		$.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				pok_action: pok_nonces.check_currencylayer_api,
				action: "pok_check_currencylayer_api",
				api_key: api_key
			},
			context: this,
			success: function(res) {
				if ( res.indexOf('API Active') ) {
					result.html( '<span class="success">API Active</span>' );
				} else {
					result.html( '<span class="error">' + res + '</span>' );
				}
				$(this).prop('disabled',false);
			},
			error: function(err, err1) {
			result.html( '<span class="error">' + pok_translations.cant_connect_server + '</span>' );
				$(this).prop('disabled',false);
			}
		});
	});

	// custom service & cost markup.
	$('.add-repeater-row').on('click', function() {
		var key = pok_repeaterKey();
		var wrapper = $(this).parents('.setting-repeater-wrapper');
		wrapper.removeClass('empty');
		var base = wrapper.find('.repeater-base').clone();
		base.addClass('repeater-row').removeClass('repeater-base').attr( 'data-id', key );
		base.find( '[disabled]' ).attr( 'disabled', false );
		base.find('[name]').each(function() {
			var oldname = $(this).attr('name');
			$(this).attr('name', oldname.replace('{id}', key));
		});
		wrapper.find('.repeater-container').append( base );
	});
	$('.setting-repeater-wrapper').on( 'click', '.delete-repeater-row', function() {
		var wrapper = $(this).parents('.setting-repeater-wrapper');
		$(this).parents('.repeater-row').remove();
		if ( 0 == wrapper.find( '.repeater-container .repeater-row' ).length ) {
			wrapper.addClass( 'empty' );
		}
	});
	$('.setting-repeater-wrapper').on('change', '.markup-courier', function() {
		var courier = $(this).val();
		var row = $(this).parents('.repeater-row');
		var arrService = '<option value="">' + pok_translations.all_service + '</option>';
		row.find('.markup-service').prop('disabled',true);
		$.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				action : 'pok_get_list_service',
				courier : courier,
				pok_action : pok_nonces.get_list_service
			},
			dataType:'json',
			cache: false,
			success: function(arr){
			  	var selectList = '';
			  	row.find('.markup-service').val('').empty().append(arrService);
			  	$.each(arr, function(key,value) {
					var data = {};
					arrService += '<option value='+ key + '>'+ value +'</option>';
				});
				row.find('.markup-service').prop('disabled',false);
				row.find('.markup-service').html(arrService).trigger('change');
			},
			error: function(err) {
				console.log(err);
			}
		});
	});
	$('.setting-repeater-wrapper').on('change', '.custom-service-courier', function() {
		var courier = $(this).val();
		var row = $(this).parents('.repeater-row');
		var arrService = '<option value="">' + pok_translations.select_service + '</option>';
		row.find('.custom-service-service').prop('disabled',true);
		$.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				action : 'pok_get_list_service',
				courier : courier,
				pok_action : pok_nonces.get_list_service
			},
			dataType:'json',
			cache: false,
			success: function(arr){
			  	var selectList = '';
			  	row.find('.custom-service-service').val('').empty().append(arrService);
			  	$.each(arr, function(key,value) {
					var data = {};
					arrService += '<option value='+ key + '>'+ value +'</option>';
				});
				row.find('.custom-service-service').prop('disabled',false);
				row.find('.custom-service-service').html(arrService).trigger('change');
			},
			error: function(err) {
				console.log(err);
			}
		});
	});
	function pok_repeaterKey() {
		var text = "";
		var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		for (var i = 0; i < 15; i++) {
			text += possible.charAt(Math.floor(Math.random() * possible.length));
		}
		return text;
	}

	//debugger
	$('#debug_passphrase').on('change keyup', function() {
		var pass = $(this).val();
		var base = $(this).data('base');
		$(this).next('.helper').find('a').attr( 'href', base + pass );
		$(this).next('.helper').find('a').text( base + pass );
	})

	// custom costs
	$('.pok-custom-cost').on( 'change', '#select_province', function() {
		var province_id = $(this).val();
		pok_load_city_list( province_id, 'custom' );
	} );

	$('.pok-custom-cost').on( 'change', '#select_city', function() {
		var city_id = $(this).val();
		pok_load_district_list( city_id, 'custom' );
	});

	// add custom cost
	$('.pok-custom-cost').on( 'click', '#add-cost', function() {
		var row_id 			= randomString( 'row_', {} );
		var province_id 	= $('#select_province').find(":selected").val();
		var province_text 	= $('#select_province').find(":selected").text();
		var city_id 		= $('#select_city').find(":selected").val();
		var city_text 		= $('#select_city').find(":selected").text();
		var district_id 	= $('#select_district').find(":selected").val();
		var district_text 	= $('#select_district').find(":selected").text();
		var courier 		= $('#select_courier').find(":selected").val();
		if ( 'custom' === courier ) {
			var courier_text = $('input[name="pok_setting[custom_cost_courier]"]').val();
		} else {
			var courier_text = $('#select_courier').find(":selected").text();
		}		
		var package_name 	= '' === $('#input_package_name').val() ? '-' : $('#input_package_name').val();
		var cost 			= '' === $('#input_cost').val() ? 0 : $('#input_cost').val();
		var min 			= '' === $('#input_min').val() ? 0 : $('#input_min').val();
		var max 			= 0 === parseInt( $('#input_max').val() ) ? '&infin;' : $('#input_max').val();
		var html = '<tr>';
			html += '<td><input type="hidden" value="' + province_id + '" name="custom_cost[' + row_id + '][province_id]"><input type="hidden" value="' + province_text + '" name="custom_cost[' + row_id + '][province_text]">' + province_text + '</td>';
			html += '<td><input type="hidden" value="' + city_id + '" name="custom_cost[' + row_id + '][city_id]"><input type="hidden" value="' + city_text + '" name="custom_cost[' + row_id + '][city_text]">' + city_text + '</td>';
			html += '<td><input type="hidden" value="' + district_id + '" name="custom_cost[' + row_id + '][district_id]"><input type="hidden" value="' + district_text + '" name="custom_cost[' + row_id + '][district_text]">' + district_text + '</td>';
			html += '<td><input type="hidden" value="' + courier + '" name="custom_cost[' + row_id + '][courier]">' + courier_text + '</td>';
			html += '<td><input type="hidden" value="' + package_name + '" name="custom_cost[' + row_id + '][package_name]">' + package_name + '</td>';
			html += '<td><input type="hidden" value="' + cost + '" name="custom_cost[' + row_id + '][cost]">' + currencyFormat( parseFloat( cost ) ) + '</td>';
			html += '<td><input type="hidden" value="' + min + '" name="custom_cost[' + row_id + '][min]">' + min + '</td>';
			html += '<td><input type="hidden" value="' + max + '" name="custom_cost[' + row_id + '][max]">' + max + '</td>';
			html += '<td><a class="remove-manual">' + pok_translations.delete + '</a></td></tr>';
		$('.pok-custom-cost tbody').append(html);
	});

	// remove custom cost.
	$('.pok-custom-cost').on( 'click', '.remove-manual', function() {
		$(this).closest('tr').remove();
	});

	function pok_load_city_list( province_id, context ) {
		$('#select_city, #select_district').prop('disabled',true);
		if ( 'custom' === context ) {
	  		var arrCity  = '<option value="*">' + pok_translations.all_city + '</option>';
	  		var arrDistrict  = '<option value="*">' + pok_translations.all_district + '</option>';
	  	} else {
	  		var arrCity  = '<option value="">' + pok_translations.select_city + '</option>';
	  		var arrDistrict  = '<option value="*">' + pok_translations.select_district + '</option>';
	  	}
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
				  	$('#select_city').val('').empty().append(arrCity);
					$('#select_district').val('').empty().append(arrDistrict); 
				  	$.each(arr, function(key,value) {
						var data = {};
						arrCity += '<option value='+ key + '>'+ value +'</option>';
					});
					$('#select_city, #select_district').prop('disabled',false);
					$('#select_city').html(arrCity).trigger('setvalue').trigger('change');
				},
				error: function(err) {
					console.log(err);
				}
			});
		} else {
			$('#select_city').prop('disabled',false).html(arrCity).trigger('change');
			$('#select_district').prop('disabled',false).html(arrDistrict);
		}
	}

	function pok_load_district_list( city_id, context ) {
		$('#select_district').prop('disabled',true);
		if ( 'custom' === context ) {
	  		var arrDistrict = '<option value="*">' + pok_translations.all_district + '</option>';
	  	} else {
	  		var arrDistrict = '<option value="">' + pok_translations.select_district + '</option>';
	  	}
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
				  	$('#select_district').val('').empty().append(arrDistrict); 
				  	$.each(arr, function(key,value) {
						var data = {};
						arrDistrict += '<option value='+ key + '>'+ value +'</option>';
					});
					$('#select_district').html(arrDistrict).trigger('setvalue').prop('disabled',false);
			  	}
			});
		} else {
			$('#select_district').html(arrDistrict).prop('disabled',false);
		}
	}

	function randomString(prefix,exists) {
		var text = "";
		var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		for (var i = 0; i < 15; i++) {
			text += possible.charAt(Math.floor(Math.random() * possible.length));
		}
		return prefix+text;
	}

	Number.prototype.formatMoney = function(c, d, t){
		var n = this, 
			c = isNaN(c = Math.abs(c)) ? 2 : c, 
			d = d == undefined ? "." : d, 
			t = t == undefined ? "," : t, 
			s = n < 0 ? "-" : "", 
			i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
			j = (j = i.length) > 3 ? j % 3 : 0;
		return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	};

	function currencyFormat( num ) {
		if ( isNaN(num) ) {
			num = 0;
		}
		var result = num.formatMoney(wc_currency.num_decimal, wc_currency.sep_decimal, wc_currency.sep_thousand);
		if ( 'left_space' ) {
			result = wc_currency.currency + " " + result;
		} else if ( 'right_space' ) {
			result = result + " " + wc_currency.currency;
		} else if ( 'left' ) {
			result = wc_currency.currency + "" + result;
		} else if ( 'right' ) {
			result = result + "" + wc_currency.currency;
		}
		// if ( num < 0 ) {
		// 	result = '- ' + result;
		// }
		return result;
	}

	// Debugger
	$('#disable-api-tonjoo').on('click', function() {
		if ( confirm( pok_translations.confirm_disable_api ) ) {
			pok_simulate_api( 'disable', 'tonjoo', $(this) );
		}
	});
	$('#disable-api-rajaongkir').on('click', function() {
		if ( confirm( pok_translations.confirm_disable_api ) ) {
			pok_simulate_api( 'disable', 'rajaongkir', $(this) );
		}
	});
	$('#enable-api-tonjoo').on('click', function() {
		pok_simulate_api( 'enable', 'tonjoo', $(this) );
	});
	$('#enable-api-rajaongkir').on('click', function() {
		pok_simulate_api( 'enable', 'rajaongkir', $(this) );
	});
	$('#check-ip').on('click', function() {
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				action: 'pok_check_ip'
			},
			context: this,
			beforeSend: function() {
				$(this).prop('disabled',true);
			},
			success: function(response) {
				$('.ip-result').html( response );
				$(this).prop('disabled',false);
			},
			error: function(data) {
				$(this).prop('disabled',false);
				console.log( data );
			}
		});
	});

	function pok_simulate_api( doing, context, ini ) {
		ini.prop('disabled', true);
		$.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				action : 'pok_simulate_api',
				do : doing + '_' + context,
				pok_action : pok_nonces.simulate_disable_api
			},
			cache: false,
			success: function(arr){
				location.reload();
		  	},
		  	error: function(err) {
		  		ini.prop('disabled', false);
		  		console.log(err);
		  	}
		});
	}

});