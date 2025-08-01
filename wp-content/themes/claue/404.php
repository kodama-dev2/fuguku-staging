<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @since   1.0.0
 * @package Claue
 */
get_header(); ?>

<div id="jas-content">
	<div class="jas-container">
		<section class="error-404 not-found">
			<div id="content-wrapper">
				<h1><?php echo esc_html__( '404', 'claue' ); ?></h1>
				<h3 class="page-title"><?php esc_html_e( 'Sorry! Page you are looking can&rsquo;t be found.', 'claue' ); ?></h3>
				<p><?php esc_html_e('Go back to the ','claue'); ?><a href="<?php echo esc_url( home_url( '/' ) ) ;?>" rel="home"><?php esc_html_e('homepage' ,'claue' ); ?></a></p>
			</div>
		</section><!-- .error-404 -->
	</div><!-- #jas-container -->
</div><!-- #jas-content -->

<?php get_footer(); ?>