<?php
/**
 * The template for displaying maintenance mode.
 *
 * @since   1.0.2
 * @package Claue
 */

// Get the time
$min   = cs_get_option( 'maintenance-min' );
$date  = cs_get_option( 'maintenance-date' );
$month = cs_get_option( 'maintenance-month' );
$year  = cs_get_option( 'maintenance-year' );

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html"/>
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<div id="jas-wrapper">
			<div class="jas-offline-content flex middle-xs center-xs">
				<div class="inner cw">
					<h1><?php echo wp_kses_post( cs_get_option( 'maintenance-title' ) ); ?></h1>
					<h3><?php echo wp_kses_post( cs_get_option( 'maintenance-message' ) ); ?></h3>
					<p><?php echo wp_kses_post( cs_get_option( 'maintenance-content' ) ); ?></p>
					
					<?php if ( cs_get_option( 'maintenance-countdown' ) ) : ?>
						<div class="jas-countdown" data-time='{"min": "<?php echo esc_attr( $min ); ?>", "day": "<?php echo esc_attr( $date ); ?>", "month": "<?php echo esc_attr( $month ); ?>", "year": "<?php echo esc_attr( $year ); ?>"}'></div>
					<?php endif; ?>
				</div>
			</div><!-- .jas-offline -->
		</div><!-- #jas-wrapper -->
		<?php wp_footer(); ?>
	</body>
</html>