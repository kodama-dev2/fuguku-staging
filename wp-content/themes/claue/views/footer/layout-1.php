<?php
/**
 * The footer layout 1.
 *
 * @since   1.4.6
 * @package Claue
 */
?>
<footer id="jas-footer" class="bgbl footer-1" <?php jas_claue_schema_metadata( array( 'context' => 'footer' ) ); ?>>
	<?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) || is_active_sidebar( 'footer-4' ) || is_active_sidebar( 'footer-5' ) ) : ?>
		<div class="footer__top pb__80 pt__80">
			<div class="jas-container pr">
				<div class="jas-row">
					<div class="jas-col-md-3 jas-col-sm-6 jas-col-xs-12">
						<?php
							if ( is_active_sidebar( 'footer-1' ) ) {
								dynamic_sidebar( 'footer-1' );
							}
						?>
					</div>
					<div class="jas-col-md-2 jas-col-sm-6 jas-col-xs-12">
						<?php
							if ( is_active_sidebar( 'footer-2' ) ) {
								dynamic_sidebar( 'footer-2' );
							}
						?>
					</div>
					<div class="jas-col-md-2 jas-col-sm-6 jas-col-xs-12">
						<?php
							if ( is_active_sidebar( 'footer-3' ) ) {
								dynamic_sidebar( 'footer-3' );
							}
						?>
					</div>
					<div class="jas-col-md-2 jas-col-sm-6 jas-col-xs-12">
						<?php
							if ( is_active_sidebar( 'footer-4' ) ) {
								dynamic_sidebar( 'footer-4' );
							}
						?>
					</div>
					<div class="jas-col-md-3 jas-col-sm-6 jas-col-xs-12">
						<?php
							if ( is_active_sidebar( 'footer-5' ) ) {
								dynamic_sidebar( 'footer-5' );
							}
						?>
					</div>
				</div><!-- .jas-row -->
			</div><!-- .jas-container -->
		</div><!-- .footer__top -->
	<?php endif; ?>
	<div class="footer__bot pt__20 pb__20 lh__1">
		<div class="jas-container pr tc">
			<?php
				if ( has_nav_menu( 'footer-menu' ) ) {
					echo '<div class="jas-row"><div class="jas-col-md-6 jas-col-sm-12 jas-col-xs-12 start-md center-sm center-xs">';
				}
				echo do_shortcode( cs_get_option( 'footer-copyright' ) );

				if ( has_nav_menu( 'footer-menu' ) ) {
					echo '</div><div class="jas-col-md-6 jas-col-sm-12 jas-col-xs-12 end-md center-sm center-xs flex">';
				}

				wp_nav_menu(
					array(
						'theme_location' => 'footer-menu',
						'menu_class'     => 'clearfix',
						'menu_id'        => 'jas-footer-menu',
						'container'      => false,
						'fallback_cb'    => NULL,
						'depth'          => 1
					)
				);
				if ( has_nav_menu( 'footer-menu' ) ) {
					echo '</div></div>';
				}
			?>
		</div>
	</div><!-- .footer__bot -->
</footer><!-- #jas-footer -->