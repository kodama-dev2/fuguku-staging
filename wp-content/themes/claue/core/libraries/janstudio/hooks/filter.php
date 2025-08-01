<?php
/**
 * Filter hooks.
 *
 * @since   1.0.0
 * @package Claue
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function jas_claue_body_class( $classes ) {
	// Add class for header left
	if ( cs_get_option( 'header-layout' ) == 5 ) {
		$classes[] = 'header-lateral';
	}

	// Add class for boxed layout
	if ( cs_get_option( 'boxed' ) ) {
		$classes[] = 'boxed';
	}

	// Add body class when enable sticky add to cart
	if ( cs_get_option( 'wc-sticky-atc' ) ) {
		$classes[] = 'has-btn-sticky';
	}

	if ( cs_get_option( 'maintenance' ) ) {
		$class[] = 'offline';
	}

	// Add class to handle mobile layout
	if ( wp_is_mobile() ) {
		$classes[] = 'jas-mobile';
	}

	$atc_behavior = cs_get_option( 'wc-atc-behavior' ) ? cs_get_option( 'wc-atc-behavior' ) : 'slide';

	$classes[] = 'jan-atc-behavior-'. $atc_behavior;

	return $classes;
}
add_filter( 'body_class', 'jas_claue_body_class' );

/**
 * Filter portfolio limit per page.
 *
 * @since 1.0.0
 */
function jas_claue_portfolio_per_page( $query ) {
	if ( ! is_post_type_archive( 'portfolio' ) ) return;

	// Get portfolio number per page
	$limit = cs_get_option( 'portfolio-number-per-page' );
  	if ( $query->query_vars['post_type'] == 'portfolio' && !is_admin() ) $query->query_vars['posts_per_page'] = $limit;

  	return $query;
}
add_filter( 'pre_get_posts', 'jas_claue_portfolio_per_page' );