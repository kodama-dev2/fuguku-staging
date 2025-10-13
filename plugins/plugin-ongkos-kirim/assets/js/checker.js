jQuery(function($) {

	// cost checker
	$('.pok-checker').on( 'change', '#select_province', function() {
		var province_id = $(this).val();
		pok_load_city_list( province_id );
	} );
	$('.pok-checker').on( 'change', '#select_city', function() {
		var city_id = $(this).val();
		pok_load_district_list( city_id );
	});
	pok_load_city_list( $('#select_province').val() );
	pok_load_district_list( $('#select_city').val() );
	
	$('.pok-checker').on( 'click', '#check-insurance', function() {
		if ( $(this).is(':checked') ) {
			$('tr.total').addClass('show');
		} else {
			$('tr.total').removeClass('show');
		}
	});

	function pok_load_city_list( province_id ) {
		$('#select_city, #select_district').prop('disabled',true);
		var arrCity  = '<option value="">' + checker.select_city + '</option>';
		var arrDistrict  = '<option value="*">' + checker.select_district + '</option>';
		if ( '*' !== province_id && '' !== province_id ) {
			if ( 'nusantara' === checker.base_api ) {
				if ( checker.provinces[ province_id ] ) {
					$('#select_city').val('').empty().append(arrCity);
					$('#select_district').val('').empty().append(arrDistrict); 
					$.each(checker.provinces[ province_id ].cities, function(key,city_id) {
						var name = checker.cities[ city_id ].type + " " + checker.cities[ city_id ].name;
						arrCity += '<option value='+ city_id + '>'+ name +'</option>';
					});
					$('#select_city, #select_district').prop('disabled',false);
					$('#select_city').html(arrCity).trigger('setvalue').trigger('change');
				}
			} else {
				$.ajax({
					url: ajaxurl,
					type: "POST",
					data: {
						action : 'pok_get_list_city',
						province_id : province_id,
						pok_action : checker.get_list_city
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
			}
		} else {
			$('#select_city').prop('disabled',false).html(arrCity).trigger('change');
			$('#select_district').prop('disabled',false).html(arrDistrict);
		}
	}

	function pok_load_district_list( city_id ) {
		$('#select_district').prop('disabled',true);
		var arrDistrict = '<option value="">' + checker.select_district + '</option>';
		if ( '*' !== city_id && '' !== city_id ) {
			if ( 'nusantara' === checker.base_api ) {
				if ( checker.cities[ city_id ] ) {
					$('#select_district').val('').empty().append(arrDistrict); 
					$.each(checker.cities[ city_id ].districts, function(key,value) {
						arrDistrict += '<option value='+ key + '>'+ value +'</option>';
					});
					$('#select_district').html(arrDistrict).trigger('setvalue').prop('disabled',false);
				}
			} else {
				$.ajax({
					url: ajaxurl,
					type: "POST",
					data: {
						action : 'pok_get_list_district',
						city_id : city_id,
						pok_action : checker.get_list_district
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
			}
		} else {
			$('#select_district').html(arrDistrict).prop('disabled',false);
		}
	}

	$('#form-checker').on('submit', function( e ) {
		console.log( $(this).serialize() );
		$('.submit input').attr('disabled', true);
		$('.result').hide();
		$('.idle').addClass('loading').show();
		$.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				action : 'pok_checker',
				data : $(this).serialize(),
				pok_action : checker.get_checker
			},
			dataType:'json',
			cache: false,
			success: function(result){
				$('.idle').removeClass('loading').hide();
				$('.result').html(result.html).show();
				$('.submit input').attr('disabled', false);
			},
			error: function(err) {
				$('.idle').removeClass('loading').hide();
				$('.result').html(result.html).show();
				console.log(err);
				$('.submit input').attr('disabled', false);
			}
		});
		e.preventDefault();
	});

});