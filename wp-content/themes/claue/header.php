<?php
/**
 * The header for our theme.
 *
 * @since   1.0.0
 * @package Claue
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<?php wp_head(); ?>
	</head>
		<body <?php body_class(); ?> <?php jas_claue_schema_metadata( array( 'context' => 'body' ) ); ?>>
			<?php 
				if ( function_exists( 'wp_body_open' ) ) {
				    wp_body_open();
				} else {
				    do_action( 'wp_body_open' );
				}
			?>
		<div id="jas-wrapper">
			<?php jas_claue_header(); ?>
			
			
