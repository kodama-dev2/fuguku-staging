<?php
/**
 * The footer layout 5.
 *
 * @since   1.4.6
 * @package Claue
 */
?>
<footer id="jas-footer" class="bgbl footer-5" <?php jas_claue_schema_metadata( array( 'context' => 'footer' ) ); ?>>
	<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
		<div class="footer__top pb__40 pt__40">
			<div class="jas-container pr">
				<div class="jas-row center-xs">
					<div class="jas-col-xs-12">
						<?php
							if ( is_active_sidebar( 'footer-1' ) ) {
								dynamic_sidebar( 'footer-1' );
							}
						?>
					</div>
				</div><!-- .jas-row -->
			</div><!-- .jas-container -->
		</div><!-- .footer__top -->
	<?php endif; ?>
	<div class="footer__bot pt__30 pb__30 lh__1">
		<div class="jas-container pr tc">
			<?php
				if ( has_nav_menu( 'footer-menu' ) ) {
					echo '<div class="jas-row"><div class="jas-col-xs-12 center-xs flex">';
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
					echo '</div><div class="jas-col-xs-12 center-xs mt__20 pt__20">';
				}
				echo do_shortcode( cs_get_option( 'footer-copyright' ) );

				if ( has_nav_menu( 'footer-menu' ) ) {
					echo '</div></div>';
				}
			?>
	</div><!-- .footer__bot -->
</footer><!-- #jas-footer -->