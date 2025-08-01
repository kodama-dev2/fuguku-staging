jQuery(function($) {

	// nice selects!
	$(".chosen-select").chosen();

	// license checker!
	$('#wpgeoip_autoupdate_frm').submit(function(ev) {

		ev.preventDefault();

		$.post( wpgeoipajax.ajax_url, { 'action': 'wpgeoip_autoupdate', 'license_code': $('input[name=wpgeoip_license_code]').val() }, function(resp)  {

			if( resp == 'LICENSE_VALID_AUTOUPDATE_ENABLED' ) {
				$('.wpgeoip-status').html('ENABLED');
			}

			$('.wpgeoip-license-result').html(resp);

		});

	    return false;

	});

});