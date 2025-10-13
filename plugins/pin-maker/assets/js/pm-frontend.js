;(function ( $, window, document, undefined ) {
	"use strict";

	$.PM = $.PM || {};

	// ======================================================
	// Init
	// ------------------------------------------------------
	$.PM.Init = function() {
		$( '.pin__type' ).on( 'click', function() {
			$( this ).toggleClass( 'pin__opened' );
			$( this ).siblings().removeClass( 'pin__opened' );
		});
		$( '.pin__image' ).on( 'click', function() {
			$( this ).siblings().removeClass( 'pin__opened' );
		});

		$( '.pin__type--area' ).hover( function() {
			$( this ).siblings( '.pin__image' ).toggleClass( 'pm-mask' );
		});
	};

	// ======================================================
	// Init_Cat layout
	// ------------------------------------------------------
	$.PM.Init_Cat = function() {
		var pin = $( '.pin-maker' );

		pin.each( function( i, val ) {
			var data_masonry = $( this ).data( 'masonry' ),
				data_grid    = $( this ).data( 'grid' );

			if ( data_masonry !== undefined ) {
				var selector = data_masonry.selector,
					sizer    = data_masonry.columnWidth,
					gutter   = data_masonry.gutterWidth / 2;

				$( this ).imagesLoaded( function() {
					$( val ).isotope( {
						itemSelector: selector,
						masonry: {
							columnWidth: sizer
						}
					} );
					$( val ).children( '.pin__wrapper' ).css( 'padding', gutter );
				} );
			}

			if ( data_grid !== undefined ) {
				var gutter = data_grid.gutterWidth / 2;

				$( val ).children( '.pin__wrapper' ).css( 'padding', gutter );
			}
		} );
		if ( $( '.pm-slick' ).length > 0 ) {
			$( '.pm-slick' ).slick();
		}
	};

	$( document ).ready( function() {
		$.PM.Init();
		$.PM.Init_Cat();
	} );
})( jQuery, window, document );