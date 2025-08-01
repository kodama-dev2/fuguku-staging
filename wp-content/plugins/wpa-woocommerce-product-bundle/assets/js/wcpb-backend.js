;(function ( $, window, document, undefined ) {
	"use strict";

	$.WCPB = $.WCPB || {};

	// ======================================================
	// Edit Bundles Widget Title and Description
	// ------------------------------------------------------
	$.WCPB.edit_title_desc = function() {
		$('#wpa_wcpb_product_data .bundle-title-desc-opt .check-title-desc').change(function(){
			if ($(this).is(":checked")) {
				$('#wpa_wcpb_product_data .bundle-title-desc-edit').removeClass('hidden');
			}else {
				$('#wpa_wcpb_product_data .bundle-title-desc-edit').addClass('hidden');
			}
		});
	};

	// ======================================================
	// Search product by ajax
	// ------------------------------------------------------
	$.WCPB.search_product = function() {
		var timer_product, last_keyword_product = true;
		$('body').on('keyup', '#wpa_wcpb_product_data .search-product .txt-search', function() {
			var _this         = $(this);
			var container 	  = _this.closest( '.search-product' );

			if ( timer_product ) {
				clearTimeout( timer_product );
			}

			timer_product = setTimeout( function() {

				// Get keyword.
				var keyword = _this.val();

				container.find( '.loading-search' ).remove();
				container.find( '.results-search' ).remove();

				if( last_keyword_product !== true && keyword == last_keyword_product && ! container.find( '.loading-search' ).length ) {
					return;
				}

				last_keyword_product = keyword;

				if ( keyword == '' || keyword.length <= parseInt( _this.attr( 'data-min-characters' ) ) ) {
					return;
				}

				// Show loading indicator.
				container.append( '<img class="loading-search" src="images/spinner.gif">' );

				$.ajax( {
					type : "POST",
					url  : wpa_wcpb.ajaxurl,
					data : {
						action 	: 'wpa_wcpb_search_product',
						keyword : keyword,
						_nonce 	: wpa_wcpb._nonce,
					},
					success  : function( response ) {

						var response = ( response ) ? JSON.parse( response ) : '';

						container.find( '.loading-search' ).remove();
						container.find( '.results-search' ).remove();

						container.append( '<div class="results-search"></div>' );

						// Prepare response.
						if ( response.message ) {
							container.find( '.results-search' ).append( '<div class="no-results">' + response.message + '</div>' );
						} else {
							container.find( '.results-search' ).append( '<div class="list-products"></div>' );

							// Show results.
							$.each( response.list_product, function( key, value ) {
								container.find( '.list-products' ).append( '<div class="item-search flx alc" data-id="' + value.id + '"> <div class="img">' + value.image + '</div> <div class="name-price fl10"> <div class="name-search">' + value.title + '</div> <div class="price-search"' + value.price + '</span> </div> </div> ' ); } );
						}
					}
				} );
			}, 300 );
		});
		$('body').on('focus', '#wpa_wcpb_product_data .search-product .txt-search', function() {
			var parent = $(this).closest('.search-product');

			parent.find('.loading-search').remove();
			parent.find('.results-search').show();
		});

		$('body').on('blur', '#wpa_wcpb_product_data .search-product .txt-search', function() {
			var parent = $(this).closest('.search-product');

			parent.find('.loading-search').remove();
			parent.find('.results-search').hide();
		});
	};

	// ======================================================
	// Add product bundle
	// ------------------------------------------------------
	$.WCPB.add_product_bundle = function() {
		$('body').on('mousedown', '#wpa_wcpb_product_data .search-product .item-search', function() {
			var _this    = $(this),
			parent       = _this.closest( '#wpa_wcpb_product_data' ),
			product_id   = _this.attr( 'data-id' ),
			list_product = parent.find( '.list-prouduct tbody' ),
			link_id      = 1;

			list_product.find( 'tr' ).each( function(){
				var _this = $(this),
				id = _this.attr( 'data-link' );

				if( id >= link_id ) {
					link_id = parseInt( id ) + 1;
				}
			} );

			parent.addClass( 'loading' );
			
			_this.remove();

			parent.find( '.group.hidden' ).removeClass( 'hidden' );

			$.ajax( {
				type : "POST",
				url  : wpa_wcpb.ajaxurl,
				data : {
					action     : 'wpa_wcpb_add_bundle',
					product_id : product_id,
					link_id    : link_id,
					_nonce     : wpa_wcpb._nonce,
				},
				success  : function( response ) {
					var response = ( response ) ? JSON.parse( response ) : '';

					if( response.status = 'true' ) {

						list_product.append( response.data );

						// Add image and name product
						var image = parent.find( '.list-prouduct tr[data-link="' + link_id + '"] .image' ).html(),
							name  = parent.find( '.list-prouduct tr[data-link="' + link_id + '"] .name' ).html();
						parent.find( '.saved .saved-img' ).append( '<td data-link="' + link_id + '"> <div class="img flx alc"> ' + image + '<span class="plus">+</span> </div><div class="name">' + name + '</div></td>' );

						// Add text box percent
						parent.find( '.saved .saved-percent' ).append( '<td data-link="' + link_id + '"><input data-price="' + response.price + '" step="any" name="wpa_wcpb[' + link_id + '][percent]" type="number" /></td>' );

						// Add text box percent
						parent.find( '.saved .saved-amount' ).append( '<td data-link="' + link_id + '"><input step="any" type="number" /></td>' );
					}	

					parent.removeClass( 'loading' );
				}
			} );
		});
	};

	// ======================================================
	// Remove product bundle
	// ------------------------------------------------------
	$.WCPB.remove_product_bundle = function() {
		$('body').on('click', '#wpa_wcpb_product_data .list-prouduct .remove span', function() {
			var _this    = $(this),
			parent       = _this.closest( 'tr' ),
			wpa_wcpb     = _this.closest( '#wpa_wcpb_product_data' ),
			link_id      = parent.attr( 'data-link' );

			wpa_wcpb.find( '.saved [data-link="' + link_id + '"]' ).remove();

			parent.remove();
		} );
	}

	// ======================================================
	// Update attributes of product variable
	// ------------------------------------------------------
	$.WCPB.update_attributes = function() {
		$('body').on('change', '#wpa_wcpb_product_data .saved-percent input', function() {
			var _this   = $(this),
				value   = Number( _this.val() ),
				price   = Number( _this.attr( 'data-price' ) ),
				link_id = _this.closest( 'td' ).attr( 'data-link' ),
				parent  = _this.closest( '.saved' ),
				amount  = parent.find( '.saved-amount td[data-link="' + link_id + '"] input' );

			amount.val( value * price / 100 );
		});

		$('body').on('change', '#wpa_wcpb_product_data .saved-amount input', function() {
			var _this   = $(this),
				value   = Number( _this.val() ),
				link_id = _this.closest( 'td' ).attr( 'data-link' ),
				parent  = _this.closest( '.saved' ),
				percent  = parent.find( '.saved-percent td[data-link="' + link_id + '"] input' ),
				price   = Number( percent.attr( 'data-price' ) );

			percent.val( value / price * 100 );
		});
	};

	// ======================================================
	// Add color picker in admin panel
	// ------------------------------------------------------
	$.WCPB.input_color_picker = function() {
		if( $().wpColorPicker ) {
			$( '.px-color-picker' ).each(function(){
				$(this).wpColorPicker();
			});
		}
	};

	$( document ).ready( function() {
		$.WCPB.search_product();
		$.WCPB.add_product_bundle();
		$.WCPB.remove_product_bundle();
		$.WCPB.update_attributes();
		$.WCPB.input_color_picker();
		$.WCPB.edit_title_desc();
	} );
})( jQuery, window, document );


