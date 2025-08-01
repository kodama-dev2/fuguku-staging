<?php
/**
 * Add element google map for visual composer.
 *
 * @since   1.0.0
 * @package Claue
 */

function jas_claue_vc_map_google_map() {
	vc_remove_element( 'vc_gmaps' );
	vc_map(
		array(
			'base'            => 'claue_addons_google_maps',
			'name'            => esc_html__( 'Google Maps', 'claue' ),
			'icon'            => 'pe-7s-map-marker',
			'category'        => esc_html__( 'Content', 'claue' ),
			'content_element' => true,
			'params'          => array(
				array(
					'param_name'       => 'address',
					'heading'          => esc_html__( 'Address', 'claue' ),
					'type'             => 'textfield',
					'edit_field_class' => 'vc_col-sm-6 vc_column-with-padding',
				),
				array(
					'param_name'       => 'z',
					'heading'          => esc_html__( 'Zoom Level ( 0 -> 20 )', 'claue' ),
					'type'             => 'textfield',
					'edit_field_class' => 'vc_col-sm-6',
				),
				array(
					'param_name'       => 'lat',
					'heading'          => esc_html__( 'Latitude', 'claue' ),
					'type'             => 'textfield',
					'edit_field_class' => 'vc_col-sm-6 vc_column-with-padding',
				),
				array(
					'param_name'       => 'lon',
					'heading'          => esc_html__( 'Longitude', 'claue' ),
					'type'             => 'textfield',
					'edit_field_class' => 'vc_col-sm-6 vc_column-with-padding',
				),
				array(
					'param_name'       => 'w',
					'heading'          => esc_html__( 'Width', 'claue' ),
					'description'      => esc_html__( 'Numeric value only, Unit is Pixel.', 'claue' ),
					'type'             => 'textfield',
					'edit_field_class' => 'vc_col-sm-6 vc_column-with-padding',
				),
				array(
					'param_name'       => 'h',
					'heading'          => esc_html__( 'Height', 'claue' ),
					'description'      => esc_html__( 'Numeric value only, Unit is Pixel.', 'claue' ),
					'type'             => 'textfield',
					'edit_field_class' => 'vc_col-sm-6 vc_column-with-padding vc_column',
				),
				array(
					'param_name' => 'marker',
					'heading'    => esc_html__( 'Marker', 'claue' ),
					'type'       => 'checkbox',
					'edit_field_class' => 'vc_col-sm-12 vc_column',
					'dependency' => array(
						'element'   => 'address',
						'not_empty' => true,
					),
				),
				array(
					'param_name'  => 'markerimage',
					'heading'     => esc_html__( 'Marker Image', 'claue' ),
					'description' => esc_html__( 'Change default Marker.', 'claue' ),
					'type'        => 'attach_image',
					'dependency' => array(
						'element' => 'marker',
						'value'   => array( 'true' ),
					),
				),
				array(
					'param_name'  => 'infowindow',
					'heading'     => esc_html__( 'Content Info Map', 'claue' ),
					'description' => esc_html__( 'Strong, br are accepted.', 'claue' ),
					'type'        => 'textfield',
					'dependency' => array(
						'element' => 'marker',
						'value'   => array( 'true' ),
					),
				),
				array(
					'param_name' => 'infowindowdefault',
					'heading'    => esc_html__( 'Show content info map', 'claue' ),
					'type'       => 'checkbox',
					'value'      => array(
						'' => 'true',
					),
					'dependency' => array(
						'element' => 'marker',
						'value'   => array( 'true' ),
					),
				),
				array(
					'param_name' => 'traffic',
					'heading'    => esc_html__( 'Show Traffic', 'claue' ),
					'type'       => 'checkbox',
					'value'      => array(
						'' => 'true',
					)
				),
				array(
					'param_name' => 'draggable',
					'heading'    => esc_html__( 'Draggable', 'claue' ),
					'type'       => 'checkbox',
					'value'      => array(
						'' => 'true',
					),
					'dependency' => array(
						'element' => 'marker',
						'value'   => array( 'true' ),
					),
				),
				array(
					'param_name' => 'hidecontrols',
					'heading'    => esc_html__( 'Hide Control', 'claue' ),
					'type'       => 'checkbox',
					'value'      => array(
						'' => 'true',
					)
				),
				array(
					'param_name' => 'scrollwheel',
					'heading'    => esc_html__( 'Scroll wheel zooming', 'claue' ),
					'type'       => 'checkbox',
					'value'      => array(
						'' => 'true',
					)
				),
				array(
					'param_name' => 'maptype',
					'heading'    => esc_html__( 'Map Type', 'claue' ),
					'type'       => 'dropdown',
					'value'      => array(
						esc_html__( 'ROADMAP', 'claue' ) => 'ROADMAP',
						esc_html__( 'SATELLITE', 'claue' ) => 'SATELLITE',
						esc_html__( 'HYBRID', 'claue' ) => 'HYBRID',
						esc_html__( 'TERRAIN', 'claue' ) => 'TERRAIN',
					),
				),
				array(
					'param_name' => 'mapstyle',
					'heading'    => esc_html__( 'Map style', 'claue' ),
					'type'       => 'dropdown',
					'value'      => array(
						esc_html__( 'None', 'claue' ) => '',
						esc_html__( 'Subtle Grayscale', 'claue' ) => 'grayscale',
						esc_html__( 'Blue water', 'claue' ) => 'blue_water',
						esc_html__( 'Pale Dawn', 'claue' ) => 'pale_dawn',
						esc_html__( 'Shades of Grey', 'claue' ) => 'shades_of_grey',
					),
				),
				array(
					'param_name'  => 'class',
					'heading'     => esc_html__( 'Extra class name', 'claue' ),
					'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'claue' ),
					'type' 	      => 'textfield',
				),
			)
		)
	);
}
add_action( 'vc_before_init', 'jas_claue_vc_map_google_map' );