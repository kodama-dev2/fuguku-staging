<?php
/**
 * Description
 *
 * @package WPA_WCVS
 * @version 1.0.0
 * @author  WPAddon
 */

if ( ! defined('ABSPATH' ) ) {
	exit;
}

// Enqueue Color Picker.
wp_enqueue_script( 'wp-color-picker' );
wp_enqueue_style( 'wp-color-picker' );

// Enqueue WordPress's media manager.
wp_enqueue_media();
?>
<div class="form-field wpa-color-wrap">
	<label for="wpa-color"><?php esc_html_e( 'Color', 'wcvs' ); ?></label>
	<input name="wpa_color" id="wpa-color" type="text" value="">
</div>
<div class="form-field wpa-image-wrap">
	<label for="wpa-image"><?php _e( 'Thumbnail', 'wcvs' ); ?></label>

	<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" />

	<a href="javascript:void(0)" class="button wpa-image-upload">
		<?php esc_html_e( 'Upload/Add image', 'wcvs' ); ?>
	</a>

	<a href="javascript:void(0)" class="button wpa-image-remove">
		<?php esc_html_e( 'Remove', 'wcvs' ); ?>
	</a>
	
	<input type="hidden" name="wpa_image" id="wpa-image" type="text" value="">
	<p><?php esc_html_e( 'This option will be overridden color.', 'wcvs' ); ?></p>
</div>
<div class="form-field wpa-tooltip-wrap">
	<label for="wpa-tooltip"><?php esc_html_e( 'Tooltip', 'wcvs' ); ?></label>
	<input name="wpa_tooltip" id="wpa-tooltip" type="text" value="">
</div>
<script type="text/javascript">
	( function( $ ) {
		$( document ).ready( function() {
			$( '#wpa-color' ).wpColorPicker();

			$( '.wpa-image-upload' ).click( function( e ) {
				e.preventDefault();

				// Store clicked element for later reference
				var btn = $( this ), img = btn.prev(), input = btn.next(), manager = btn.data( 'wpa_image_select' );

				if ( ! manager) {
					// Create new media manager.
					manager = wp.media({
						button: {
							text: '<?php esc_html_e( 'Select',  'wcvs' ); ?>',
						},
						states: [
							new wp.media.controller.Library({
								title: '<?php esc_html_e( 'Select an image',  'wcvs' ); ?>',
								library: wp.media.query({type: 'image'}),
								multiple: false,
								date: false,
							})
						]
					});

					// When an image is selected, run a callback
					manager.on( 'select', function() {
						// Grab the selected attachment
						var attachment = manager.state().get( 'selection' ).first();

						// Update the field value
						input.val( attachment.attributes.url ).trigger( 'change' );
						img.attr( 'src', attachment.attributes.url );
					});

					// Store media manager object for later reference
					btn.data( 'wpa_image_select', manager );
				}

				manager.open();
			});
			$( '.wpa-image-remove' ).click( function( e ) {
				e.preventDefault();

				var btn = $( this ), img = btn.prev().prev(), input = btn.next();

				img.attr( 'src', '<?php echo esc_url( wc_placeholder_img_src() ); ?>' );
				input.val( '' );
			});
		});
	})(jQuery);
</script>
