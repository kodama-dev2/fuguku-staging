<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 *
 * Field: Text
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
class CSFramework_Option_slider extends CSFramework_Options {

public function __construct( $field, $value = '', $unique = '' ) {
	parent::__construct( $field, $value, $unique );
}

  public function output(){

	$choices_default = array(
		'min'  => 10,
		'max'  => 24,
		'step' => 1,
		'unit' => 'px',
	);

	$choices_merge = ( isset( $this->field['choices'] ) && $this->field['choices'] ) ? wp_parse_args( $this->field['choices'], $choices_default ) : $choices_default;

	extract( $choices_merge );

	echo wp_kses_post( $this->element_before() );

	echo '<div id="cs-slider-' . esc_attr( $this->field[ 'id' ] ) . '" class="cs-slider"></div>';

	echo '<input type="hidden" id="input-slider-' . esc_attr( $this->field[ 'id' ] ) . '" name="'. $this->element_name() .'" value="'. $this->element_value() .'"'. $this->element_class() . $this->element_attributes() .'/>';

	echo wp_kses_post( $this->element_after() );

	echo 
	"<script type='text/javascript'>
		jQuery( function($) {
			$(document).ready( function() {
				$( '#cs-slider-" . esc_attr( $this->field[ 'id' ] ) . "' ).slider({
					range: 'min',
					min: " . (int) $min . ",
					max: " . (int) $max . ",
					step: " . (int) $step . ",
					value: " . $this->element_value() . ",
					create: function(event, ui) {
						$(this).children('.ui-slider-handle').html('<span>' + $(this).slider('value') + '" . esc_attr( $unit ) . "' + '</span>');
						$( '#input-slider-" . esc_attr( $this->field[ 'id' ] ) . "' ).val( $(this).slider('value') );
					},
					slide: function(event, ui) {
						$(ui.handle).html('<span>' + ui.value + '" . esc_attr( $unit ) . "' + '</span>');
						$( '#input-slider-" . esc_attr( $this->field[ 'id' ] ) . "' ).val( $(this).slider('value') );
					},
					change: function(event, ui) {
						$(ui.handle).html('<span>' + ui.value + '" . esc_attr( $unit ) . "' + '</span>');
						$( '#input-slider-" . esc_attr( $this->field[ 'id' ] ) . "' ).val( $(this).slider('value') );
					}
				});
			} );
		} )
	</script>";
	}
}
