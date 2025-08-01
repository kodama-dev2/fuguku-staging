<?php
/**
 * The header layout 5.
 *
 * @since   1.0.0
 * @package Claue
 */
?>
<header id="jas-header" class="header-5 pf" <?php jas_claue_schema_metadata( array( 'context' => 'header' ) ); ?>>
	<i class="close-menu hide-md visible-1024 pe-7s-close pa"></i>
	<div class="flex column center-xs pt__60 pb__60">
		<?php jas_claue_logo(); ?>
		<div class="jas-action flex middle-xs center-xs pr mt__20">
			<?php if ( cs_get_option( 'header-search-icon' ) ) : ?>
				<a class="sf-open cb chp" href="javascript:void(0);" title="<?php echo esc_html__( 'Search', 'claue' ); ?>"><i class="pe-7s-search"></i></a>
			<?php endif; ?>
			<?php
				if ( class_exists( 'WooCommerce' ) ) {
					echo jas_claue_wc_my_account();
					if ( class_exists( 'YITH_WCWL' ) ) {
						global $yith_wcwl;
						echo '<a class="cb chp" href="' . esc_url( $yith_wcwl->get_wishlist_url() ) . '" title="' .  esc_html__( 'View your Wishlist', 'claue' ) . '"><i class="pe-7s-like"></i></a>';
					}
					echo jas_claue_wc_shopping_cart();
				}
			?>
		</div><!-- .jas-action -->
		<?php
			if ( class_exists( 'WooCommerce' ) && cs_get_option( 'header-currency' ) ) {
				echo jas_claue_wc_currency();
			}
		?>
		<nav class="jas-navigation center-xs mt__10 mb__30">
			<?php
				wp_nav_menu(
					array(
						'theme_location'  => 'primary-menu',
						'container_id'    => 'jas-mobile-menu',
						'walker'          => new JAS_Claue_Mobile_Menu_Walker(),
						'fallback_cb'     => NULL
					)
				);
			?>
		</nav><!-- .jas-navigation -->
		<?php echo jas_claue_social(); ?>
	</div>
</header><!-- #jas-header -->
<?php if ( class_exists( 'WooCommerce' ) ) : ?>	
	<div class="jas-mini-cart jas-push-menu">
		<div class="jas-mini-cart-content">
			<h3 class="mg__0 tc cw bgb tu ls__2"><?php esc_html_e( 'Mini Cart', 'claue' );?> <i class="close-cart pe-7s-close pa"></i></h3>
			<div class="widget_shopping_cart_content"></div>
		</div>
	</div><!-- .jas-mini-cart -->
<?php endif ?>

<form class="header__search w__100 dn pf" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" <?php jas_claue_schema_metadata( array( 'context' => 'search_form' ) ); ?>>
	<div class="pa">
		<input class="w__100 jas-ajax-search" type="text" name="s" placeholder="<?php echo esc_html__( 'Search for...', 'claue' ); ?>" />
		<input type="hidden" name="post_type" value="product">
	</div>
	<a id="sf-close" class="pa" href="#"><i class="pe-7s-close"></i></a>
</form><!-- #header__search -->
<div class="pl__15 pr__15 pt__10 pb__10 hide-md visible-1024 top-menu pf w__100">
	<div class="jas-row middle-xs">
		<div class="jas-col-sm-4 jas-col-xs-4">
			<a href="javascript:void(0);" class="jas-push-menu-btn">
				<?php
					if ( cs_get_option( 'mobile-icon' ) ) :
						$icon = wp_get_attachment_image_src( cs_get_option( 'mobile-icon' ), 'full', true );
						echo '<img src="' . esc_url( $icon[0] ) . '" width="30" height="30" alt="Menu" />';
					else :
						echo '<img src="' . JAS_CLAUE_URL . '/assets/images/icons/hamburger-black.svg" width="30" height="16" alt="Menu" />';
					endif;
				?>
			</a>
		</div>
		<div class="jas-col-sm-4 jas-col-xs-4 start-md center-sm center-xs">
			<?php jas_claue_logo(); ?>
		</div>
		<div class="jas-col-md-4 jas-col-sm-4 jas-col-xs-4">
			<div class="jas-action flex end-xs middle-xs">
				<a class="sf-open cb chp hidden-xs" href="javascript:void(0);"><i class="pe-7s-search"></i></a>
				<?php
					if ( class_exists( 'WooCommerce' ) ) {
						echo jas_claue_wc_my_account();

						if ( class_exists( 'YITH_WCWL' ) ) {
							global $yith_wcwl;
							echo '<a class="cb chp hidden-xs wishlist-icon" href="' . esc_url( $yith_wcwl->get_wishlist_url() ) . '"><i class="pe-7s-like"></i></a>';
						}
						echo jas_claue_wc_shopping_cart();
		
						if ( cs_get_option( 'header-currency' ) ) {
							echo jas_claue_wc_currency();
						}
					}
				?>
			</div><!-- .jas-action -->

		</div>
	</div><!-- .jas-row -->
</div><!-- .header__mid -->