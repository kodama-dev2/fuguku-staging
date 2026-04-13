<?php
/**
 * Visitor country shortcode for localization popups and dynamic copy.
 *
 * Usage in popup/HTML content (must run through do_shortcode):
 *   You are visiting Fuguku.com from [fuguku_visitor_country] — would you like to update your localization?
 *
 * Attributes:
 *   default  — Text if country cannot be detected (default: empty).
 *   format   — "name" (default) or "code" (ISO 3166-1 alpha-2).
 *
 * @package Fuguku_Gifts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve ISO 3166-1 alpha-2 country code from request (WooCommerce GeoIP, Cloudflare, etc.).
 *
 * @return string Two-letter code or empty string.
 */
function fuguku_visitor_country_get_code() {
	$code = '';

	if ( ! empty( $_SERVER['HTTP_CF_IPCOUNTRY'] ) && is_string( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) {
		$c = strtoupper( sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) );
		if ( preg_match( '/^[A-Z]{2}$/', $c ) && 'XX' !== $c && 'T1' !== $c ) {
			$code = $c;
		}
	}

	if ( '' === $code && class_exists( 'WC_Geolocation' ) ) {
		$geo = WC_Geolocation::geolocate_ip();
		if ( ! empty( $geo['country'] ) && is_string( $geo['country'] ) ) {
			$c = strtoupper( $geo['country'] );
			if ( preg_match( '/^[A-Z]{2}$/', $c ) ) {
				$code = $c;
			}
		}
	}

	/**
	 * Filter the detected visitor country code before display logic.
	 *
	 * @param string $code ISO 3166-1 alpha-2 or empty.
	 */
	return apply_filters( 'fuguku_visitor_country_code', $code );
}

/**
 * Convert country code to a human-readable country name when possible.
 *
 * @param string $code ISO 3166-1 alpha-2.
 * @return string
 */
function fuguku_visitor_country_code_to_name( $code ) {
	$code = strtoupper( (string) $code );
	if ( '' === $code ) {
		return '';
	}

	if ( function_exists( 'WC' ) && WC()->countries ) {
		$countries = WC()->countries->get_countries();
		if ( isset( $countries[ $code ] ) ) {
			return $countries[ $code ];
		}
	}

	if ( class_exists( 'Locale' ) ) {
		$display = Locale::getDisplayRegion( '-' . $code, get_locale() );
		if ( is_string( $display ) && '' !== $display ) {
			return $display;
		}
	}

	return $code;
}

/**
 * Shortcode callback: [fuguku_visitor_country default="" format="name"]
 *
 * @param array<string,string>|string $atts Shortcode attributes.
 * @return string
 */
function fuguku_visitor_country_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'default' => '',
			'format'  => 'name',
		),
		$atts,
		'fuguku_visitor_country'
	);

	$format = strtolower( (string) $atts['format'] );
	if ( ! in_array( $format, array( 'name', 'code' ), true ) ) {
		$format = 'name';
	}

	$code = fuguku_visitor_country_get_code();

	if ( '' === $code ) {
		$out = (string) $atts['default'];
	} elseif ( 'code' === $format ) {
		$out = $code;
	} else {
		$out = fuguku_visitor_country_code_to_name( $code );
	}

	/**
	 * Filter the final shortcode string (already escaped below).
	 *
	 * @param string $out   Display text.
	 * @param string $code  Raw ISO code (may be empty).
	 * @param array  $atts  Shortcode attributes.
	 */
	$out = apply_filters( 'fuguku_visitor_country_output', $out, $code, $atts );

	return esc_html( $out );
}

add_action(
	'init',
	static function () {
		add_shortcode( 'fuguku_visitor_country', 'fuguku_visitor_country_shortcode' );
	},
	20
);
