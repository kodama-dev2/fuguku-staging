<?php
/**
 * Custom column for visual composer.
 *
 * @since   1.0.0
 * @package Claue
 */

function jas_claue_vc_add_params_to_column() {
	vc_map_update( 'vc_column', array( 'icon' => 'pe-7s-graph3' ) );
	vc_add_params(
		'vc_column',
		array(
			array(
				'heading'          => esc_html__( 'Background  Position', 'claue' ),
				'description'      => esc_html__( 'Sets the starting position of a background image.', 'claue' ),
				'group'            => esc_html__( 'Design Options', 'claue' ),
				'type'             => 'dropdown',
				'param_name'       => 'background_position',
				'edit_field_class' => 'vc_col-xs-6',
				'value'            => array(
					esc_html__( 'Left Top', 'claue' )      => 'default',
					esc_html__( 'Left Center', 'claue' )   => 'left center',
					esc_html__( 'Left Bottom', 'claue' )   => 'left bottom',
					esc_html__( 'Right Top', 'claue' )     => 'right top',
					esc_html__( 'Right Center', 'claue' )  => 'right center',
					esc_html__( 'Right Bottom', 'claue' )  => 'right bottom',
					esc_html__( 'Center Top', 'claue' )    => 'center top',
					esc_html__( 'Center Center', 'claue' ) => 'center center',
					esc_html__( 'Center Bottom', 'claue' ) => 'center bottom',
				),
			),
		)
	);

	vc_add_params(
		'vc_column_inner',
		array(
			array(
				'heading'          => esc_html__( 'Background  Position', 'claue' ),
				'description'      => esc_html__( 'Sets the starting position of a background image.', 'claue' ),
				'group'            => esc_html__( 'Design Options', 'claue' ),
				'type'             => 'dropdown',
				'param_name'       => 'background_position',
				'edit_field_class' => 'vc_col-xs-6',
				'value'            => array(
					esc_html__( 'Left Top', 'claue' )      => 'default',
					esc_html__( 'Left Center', 'claue' )   => 'left center',
					esc_html__( 'Left Bottom', 'claue' )   => 'left bottom',
					esc_html__( 'Right Top', 'claue' )     => 'right top',
					esc_html__( 'Right Center', 'claue' )  => 'right center',
					esc_html__( 'Right Bottom', 'claue' )  => 'right bottom',
					esc_html__( 'Center Top', 'claue' )    => 'center top',
					esc_html__( 'Center Center', 'claue' ) => 'center center',
					esc_html__( 'Center Bottom', 'claue' ) => 'center bottom',
				),
			),
		)
	);
}
add_action( 'vc_after_init', 'jas_claue_vc_add_params_to_column' );