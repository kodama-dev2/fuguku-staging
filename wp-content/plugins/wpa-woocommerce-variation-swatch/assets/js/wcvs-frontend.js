;(function ($, window, document, undefined) {
	"use strict";

	var _galleries;
	var _form_data;
	// ======================================================
	// Get images of each attribute
	// ------------------------------------------------------
	function wpa_wcvs_get_images( id ) {
		var galleryKey = '', selectedAttributes = {}, usedImages = [];

		// Get selected size and color
		$( '#product-' + id + ' .swatch select' ).each(function() {
			if ($( this ).parent().parent().hasClass( 'is-color' ) ) {
				galleryKey = '_product_image_gallery_' + $( this ).data( 'attribute_name' ).replace( 'attribute_', '' ) + '-' + $( this ).val().toLowerCase();
			}

			selectedAttributes[$( this ).data( 'attribute_name' ).replace( 'attribute_', '' )] = $( this ).val();
		});

		var images;

		var html = '<div class="woocommerce-product-gallery woocommerce-product-gallery--with-images woocommerce-product-gallery--columns-4 images" data-columns="4" style="opacity: 0; transition: opacity .25s ease-in-out;">';
		html += '<div class="woocommerce-product-gallery__wrapper">';

		if ( typeof( _galleries[galleryKey]) !== "undefined" && _galleries[galleryKey] !== null ) {
			images = _galleries[galleryKey];

			$.each( images, function ( index, image ) {
				if ( image['single'] == undefined ) {
					var img_single = image['thumbnail'];
				} else {
					var img_single = image['single']; 
				}
				
				if ( $.inArray( img_single, usedImages ) === -1 ) {
					html += '<div data-thumb="' + img_single + '" class="woocommerce-product-gallery__image">';
					html += '<a href="' + image['data-src'] + '">';
					html += '<img width="' + image['data-large_image_width'] + '" height="' + image['data-large_image_width'] + '" src="' + image['data-large_image'] + '" class="attachment-shop_single size-shop_single" alt="" title="" data-src="' + image['data-src'] + '" data-large_image="' + image['data-large_image'] + '" data-large_image_width="' + image['data-large_image_width'] + '" data-large_image_height="' + image['data-large_image_height'] + '"/>';
					html += '</a></div>';
					usedImages.push( img_single );
				}
			});
		} else {
			images = _galleries['default_gallery'];

			// Get variation image
			$.each( _form_data, function ( index, variation ) {
				if ( Object.keys(variation['attributes'] ).length === Object.keys( selectedAttributes ).length ) {
					// Flag to check right or wrong variation
					var chooseThisVariation = true;

					$.each( selectedAttributes, function ( attrName, attrValue ) {
						if ( variation['attributes']['attribute_' + attrName + ''] !== '' && variation['attributes']['attribute_' + attrName + ''] !== attrValue) {
							chooseThisVariation = false;
						}
					});
					
					if ( chooseThisVariation ) {
						var image = variation['image'];
						if ( $.inArray( image['thumb_src'], usedImages ) === -1 ) {
							html += '<div data-thumb="' + image['thumb_src'] + '" class="woocommerce-product-gallery__image">';
							html += '<a href="' + image['full_src'] + '">';
							html += '<img width="' + image['src_w'] + '" height="' + image['src_h'] + '" src="' + image['src'] + '" class="attachment-shop_single size-shop_single" alt="" title="" data-src="' + image['full_src'] + '" data-large_image="' + image['src'] + '" data-large_image_width="' + image['src_w'] + '" data-large_image_height="' + image['src_h'] + ' srcset="' + image['srcset'] + '"/>';
							html += '</a></div>';
							usedImages.push( image['thumb_src'] );
							return true;
						}
					}
				}
			});
		}

		html += '</div></div>';
		html = '';
		$( document.body ).trigger( 'wpa_wcvs_update_html', {
			'html': html,
			'pid': id
		});
	};

	// ======================================================
	// Update HTML
	// ------------------------------------------------------
	$( document.body ).bind( 'wpa_wcvs_update_html', function ( event, data ) {
		var html = data.html, pid = data.pid;

		$( '#product-' + pid + ' .images' ).replaceWith( html );

		// Storefront theme trigger gallery
		if ($( '.woocommerce-product-gallery' ).length > 0 ) {
			$( '.woocommerce-product-gallery' ).each( function() {
				$( this ).wc_product_gallery();
			});
		}
	});


	// ======================================================
	// Update HTML
	// ------------------------------------------------------
	function wpa_wcvs_update_images() {
		setTimeout( function() {
			$( '.variations_form select[data-attribute_name]' ).trigger( 'change' );
		});
		
		var selected = [];
		$( 'body' )
			.on( 'click touchstart', '.swatch__list--item', function ( e ) {
				 _form     = $( this ).parents( 'form' );
				_galleries = _form.data( 'galleries' );
				_form_data = _form.data( 'product_variations' );
				_pid       = _form.data( 'product_id' );
				

				var _this      = $( this ),
					_select    = _this.parent().next( '.value' ).find( 'select' ),
					_attr      = _this.parent().data( 'attribute' ),
					_variation = _this.data( 'variation' ),
					_is_color_label = $( this ).parents('.is-color').length;

				_select.trigger( 'focusin' );

				// Check if this combination is available
				if ( ! _select.find( 'option[value="' + _variation + '"]' ).length ) {
					_this.addClass( 'is-selected' );
					_this.siblings( '.swatch__list--item' ).removeClass( 'is-selected' );
					_select.val( '' ).change();
					_form.trigger( 'wpa_wcvs_no_matching_variations', [_this] );
					return;
				}

				if ( selected.indexOf( _attr ) === -1) {
					selected.push( _attr );
				}

				if ( _this.hasClass( 'is-selected' ) ) {
					_select.val( '' );
					_this.removeClass( 'is-selected' );
					delete selected[selected.indexOf( _attr )];
					_select.change();
				} else {
					_this.addClass( 'is-selected' ).siblings( '.is-selected' ).removeClass( 'is-selected' );
					_select.val( _variation );
					_select.change();
				}
				if (_is_color_label > 0) {
					wpa_wcvs_get_images( _pid );	
				}

				
			})
			.on( 'click', '.reset_variations', function() {
				$( this ).closest( '.variations_form' ).find( '.swatch__list--item.is-selected' ).removeClass( 'is-selected' );

				selected = [];
			})
			.on( 'wpa_wcvs_no_matching_variations', function() {
				window.alert( wc_add_to_cart_variation_params.i18n_no_matching_variations_text );
			});
			
		// Update image gallery for default value
		if ( $( '.swatch__list--item' ).hasClass( 'is-selected' ) ) {
			var _this = $( '.swatch__list--item.is-selected' ),
				_form = $( '.variations_form' ),
				_pid = _form.data( 'product_id' ),
				_is_color_label = $( this ).parents('.is-color').length;
			if (_is_color_label > 0) {
				wpa_wcvs_get_images( _pid );
			}
		}

		function isMatch ( variation_attributes, attributes ) {
			var match = true;

			// loop all attributes to see this variation is valid or not
			for ( var attr_name in variation_attributes ) {
				if ( variation_attributes.hasOwnProperty( attr_name ) ) {
					var val1 = variation_attributes[ attr_name ];
					var val2 = attributes[ attr_name.replace('attribute_', '') ];
					// if attribute value of variation is not equal value is selected => not matched
					if ( 
						val1 !== undefined && val2 !== undefined && val1 && val2
						&& val1.length !== 0 && val2.length !== 0 && val1 !== val2 ) {
						match = false;
					}
				}
			}
			return match;
		};

		function findMatchingVariations ( variations, attributes ) {
			var matching = [];
			for ( var i = 0; i < variations.length; i++ ) {
				var variation = variations[i];
				// if this variation is in stock and matched => this variation is valid
				if ( variation.is_in_stock && isMatch( variation.attributes, attributes ) ) {
					matching.push( variation );
				}
			}
			return matching;
		};

		// Refresh options of available variations. 
		$( '.variations_form' ).on( 'change', 'select[data-attribute_name]', function() {
			setTimeout( function() {
				// get info of variations from server
				var attributes = $( '.variations_form' ).data( 'attributes' );
				var variations = $( '.variations_form' ).data( 'product_variations' );
				var selected = {};
				var attributes_info = {};
				$.each( attributes, function( key, attrs_array ) {
					// get selected value from select
					selected[key] = $(`.variations_form #${key}`).val();
					// set all values of attribute are disable, false is disable 
					$.each( attrs_array, function( k2, attr_val ) {
						attributes_info[attr_val] = false;
					});
				});
				
				// check each attribute is enable (true) or disable (false)
				$.each( attributes, function( handling_attr, handling_val ) {
					var compared_attr = 'attribute_' + handling_attr;
					var checkedAttrs = {...selected};
					// only check rest attributes to find matched variations, no check handling_attr
					checkedAttrs[handling_attr] = '';
					var matchedVariations = findMatchingVariations(variations, checkedAttrs);
					// check variations are valid or not 
					$.each( matchedVariations, function( k1, variation ) {
						var variationAttributes = variation.attributes;
						$.each( variationAttributes, function( attr_name, attr_val ) {
							if(attr_name == compared_attr) {
								// enable only this value of attribute
								if(attr_val) {
									attributes_info = {...attributes_info, [attr_val]: true};
								// enable all values of this attribute
								} else {
									$.each( attributes, function( attribute_name, attribute_values ) {
										if('attribute_' + attribute_name == attr_name) {
											$.each( attribute_values, function( key, value ) {
												attributes_info = {...attributes_info, [value]: true};
											});
										}
									});
								}
								
							}
						});
					});
				});

				$.each( attributes_info, function( attr_name, attr_val ) {
					// disable if that attr is not active
					if(!attr_val) {
						$('.variations_form li[data-variation="' + attr_name + '"]').addClass( 'disabled' );
						// $('.variations_form select option[value="' + attr_name + '"]').prop( 'disabled', true );
					} else {
						$('.variations_form li[data-variation="' + attr_name + '"]').removeClass( 'disabled' );
						// $('.variations_form select option[value="' + attr_name + '"]').prop( 'disabled', false );
					}
					
				});
			}, 50 );
		});

	}

	// ======================================================
	// Update images on product list
	// ------------------------------------------------------
	function wpa_wcvs_change_images() {
		$( 'body' ).on( 'click touchstart', 'div.swatch__list span.swatch__value', function() {
			var parent = $(this).parent();
			var _src      = $( parent ).data( 'thumb' ),
				_src_flip = $( parent ).data( 'thumb-flip' ),
				_title = $( parent ).data( 'title' ),
				_product_title = $( parent ).data( 'product-title' );

			if ( typeof _src !== typeof undefined && _src !== false ){
				$( parent ).closest( '.product' ).find( 'img.attachment-shop_catalog, img.attachment-woocommerce_thumbnail' ).attr( 'src', _src ).attr( 'srcset', _src );
			}

			if ( typeof _title !== typeof undefined && _title !== false && typeof _product_title !== typeof undefined && _product_title !== false ){
				$( this ).parents().eq(2).find( '.product-title a' ).text( _product_title + ' ' + _title );
			}

			if ( typeof _src_flip !== typeof undefined && _src_flip !== false ){
				$( parent ).closest( '.product' ).find( '.wp-image-flip' ).attr( 'src', _src_flip ).attr( 'srcset', _src_flip );
			}
		});
	}

	$( document ).ready( function() {
		_galleries = $( '.variations_form' ).data( 'galleries' );
		_form_data = $( '.variations_form' ).data( 'product_variations' );

		wpa_wcvs_update_images();
		wpa_wcvs_change_images();

		$( 'body div.swatch__list' ).each( function(ind, el){
			$(this).find('.selected-span > .swatch__value').first().trigger('click');
		});
	});
})(jQuery, window, document);