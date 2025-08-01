<?php
/**
 * The header layout 2.
 *
 * @since   1.0.0
 * @package Claue
 */
?>
<header id="jas-header" class="header-2" <?php jas_claue_schema_metadata( array( 'context' => 'header' ) ); ?>>
	<div class="header__top fs__12 bgbl pl__15 pr__15">
		<?php if ( cs_get_option( 'header-boxed' ) ) : echo '<div class="jas-container">'; endif; ?>
			<?php if ( cs_get_option( 'header-top-left' ) || cs_get_option( 'header-top-center' ) || cs_get_option( 'header-top-right' ) || cs_get_option( 'header-currency' ) ) : ?>
			<div class="jas-row middle-xs pt__10 pb__10 ">
				<div class="jas-col-md-4 jas-col-sm-6 jas-col-xs-12 start-md start-sm center-xs">
					<?php if ( cs_get_option( 'header-top-left' ) ) : ?>
						<div class="header-text"><?php echo do_shortcode( cs_get_option( 'header-top-left' ) ); ?></div>
					<?php endif; ?>
				</div>
				<div class="jas-col-md-4 jas-col-sm-6 jas-col-xs-12 center-md end-sm center-xs">
					<?php if ( cs_get_option( 'header-top-center' ) ) : ?>
						<div class="header-text"><?php echo do_shortcode( cs_get_option( 'header-top-center' ) ); ?></div>
					<?php endif; ?>
				</div>
				<div class="jas-col-md-4 jas-col-sm-2 jas-col-xs-12 flex hidden-sm hidden-xs end-lg end-sm">
					<?php if ( cs_get_option( 'header-top-right' ) ) : ?>
						<div class="header-text mr__15"><?php echo do_shortcode( cs_get_option( 'header-top-right' ) ); ?></div>
					<?php endif; ?>
					<?php
						if ( class_exists( 'WooCommerce' ) && cs_get_option( 'header-currency' ) ) {
							echo jas_claue_wc_currency();
						}
					?>
				</div>
			</div><!-- .jas-row -->
			<?php endif; ?>
		<?php if ( cs_get_option( 'header-boxed' ) ) : echo '</div>'; endif; ?>
	</div><!-- .header__top -->

	<div class="header__mid pl__15 pr__15<?php echo ( cs_get_option( 'header-transparent' ) ? ' header__transparent pa w__100' : '' ); ?>">
		<?php if ( cs_get_option( 'header-boxed' ) ) : echo '<div class="jas-container">'; endif; ?>
			<div class="jas-row middle-xs">
				<div class="jas-col-md-2 jas-col-sm-4 jas-col-xs-3 flex start-md flex">
					<a href="javascript:void(0);" class="jas-push-menu-btn hide-md visible-sm visible-xs">
						<?php
							if ( cs_get_option( 'mobile-icon' ) ) :
								$icon = wp_get_attachment_image_src( cs_get_option( 'mobile-icon' ), 'full', true );
								echo '<img src="' . esc_url( $icon[0] ) . '" width="30" height="30" alt="Menu" />';
							else :
								echo '<img src="' . JAS_CLAUE_URL . '/assets/images/icons/hamburger-black.svg" width="30" height="16" alt="Menu" />';
							endif;
						?>
					</a>
					<div class="hidden-xs hidden-sm">
						<?php echo jas_claue_social(); ?>
					</div>
				</div>
				<div class="jas-col-md-8 jas-col-sm-4 jas-col-xs-6 flex middle-xs tc">
					<nav class="jas-navigation jas-col-md-5 hidden-sm hidden-xs flex end-xs pd__0">
						<?php
							if ( has_nav_menu( 'left-menu' ) ) {
								wp_nav_menu(
									array(
										'theme_location' => 'left-menu',
										'menu_class'     => 'jas-menu clearfix',
										'menu_id'        => 'jas-left-menu',
										'container'      => false,
										'walker'         => new JAS_Claue_Menu_Walker(),
										'fallback_cb'    => NULL
									)
								);
							} else {
								echo '<ul class="jas-menu clearfix"><li><a target="_blank" href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Add Menu', 'claue' ) . '</a></li></ul>';
							}
						?>
					</nav><!-- .jas-navigation -->
					<div class="jas-col-md-2 jas-col-sm-12 jas-col-xs-12">
						<?php jas_claue_logo(); ?>
					</div>
					<nav class="jas-navigation jas-col-md-5 hidden-sm hidden-xs pd__0">
						<?php
							if ( has_nav_menu( 'right-menu' ) ) {
								wp_nav_menu(
									array(
										'theme_location' => 'right-menu',
										'menu_class'     => 'jas-menu clearfix',
										'menu_id'        => 'jas-right-menu',
										'container'      => false,
										'walker'         => new JAS_Claue_Menu_Walker(),
										'fallback_cb'    => NULL
									)
								);
							} else {
								echo '<ul class="jas-menu clearfix"><li><a target="_blank" href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Add Menu', 'claue' ) . '</a></li></ul>';
							}
						?>
					</nav><!-- .jas-navigation -->
				</div>
				<div class="jas-col-md-2 jas-col-sm-4 jas-col-xs-3 flex end-xs">				
					
					<div class="jas-action flex end-xs middle-xs pd__0">
						<?php if ( cs_get_option( 'header-search-icon' ) ) : ?>
							<a class="sf-open cb chp hidden-xs" href="javascript:void(0);" title="<?php echo esc_html__( 'Search', 'claue' ); ?>"><i class="pe-7s-search"></i></a>
						<?php endif; ?>
						<?php
							if ( class_exists( 'WooCommerce' ) ) {
								echo jas_claue_wc_my_account();

								if ( class_exists( 'YITH_WCWL' ) ) {
									global $yith_wcwl;
									echo '<a class="cb chp hidden-xs" href="' . esc_url( $yith_wcwl->get_wishlist_url() ) . '" title="' .  esc_html__( 'View your Wishlist', 'claue' ) . '"><i class="pe-7s-like"></i></a>';
								}

								echo jas_claue_wc_shopping_cart();
							}
						?>
					</div>
				</div>
			</div><!-- .jas-row -->
		<?php if ( cs_get_option( 'header-boxed' ) ) : echo '</div>'; endif; ?>
	</div><!-- .header__mid -->
	<form class="header__search w__100 dn pf" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" <?php jas_claue_schema_metadata( array( 'context' => 'search_form' ) ); ?>>
		<div class="pa">
			<input class="w__100 jas-ajax-search" type="text" name="s" placeholder="<?php echo esc_html__( 'Search for...', 'claue' ); ?>" />
			<input type="hidden" name="post_type" value="product">
		</div>
		<a id="sf-close" class="pa" href="#"><i class="pe-7s-close"></i></a>
	</form><!-- #header__search -->

	<div class="jas-canvas-menu jas-push-menu">
		<h3 class="mg__0 tc cw bgb tu ls__2"><?php esc_html_e( 'Menu', 'claue' ); ?> <i class="close-menu pe-7s-close pa"></i></h3>
		<div class="hide-md visible-sm visible-xs mt__30 center-xs flex tc">
			<?php if ( cs_get_option( 'header-top-right' ) ) : ?>
				<div class="header-text mr__15"><?php echo do_shortcode( cs_get_option( 'header-top-right' ) ); ?></div>
			<?php endif; ?>
			<?php
				if ( class_exists( 'WooCommerce' ) && cs_get_option( 'header-currency' ) ) {
					echo jas_claue_wc_currency();
				}
			?>
		</div>
		<div class="jas-action flex center-xs middle-xs hide-md hidden-sm visible-xs mt__10">
			<?php if ( cs_get_option( 'header-search-icon' ) ) : ?>
				<a class="sf-open cb chp" href="javascript:void(0);"><i class="pe-7s-search"></i></a>
			<?php endif; ?>
			<?php
				if ( class_exists( 'WooCommerce' ) ) {
					if ( cs_get_option( 'header-my-account-icon' ) ) {
						echo '<a class="cb chp db jas-my-account" href="' . esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '"><i class="pe-7s-user"></i></a>';
					}
					if ( class_exists( 'YITH_WCWL' ) ) {
						global $yith_wcwl;
						echo '<a class="cb chp wishlist-icon" href="' . esc_url( $yith_wcwl->get_wishlist_url() ) . '"><i class="pe-7s-like"></i></a>';
					}
				}
			?>
		</div><!-- .jas-action -->
		<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary-menu',
					'container_id'   => 'jas-mobile-menu',
					'walker'         => new JAS_Claue_Mobile_Menu_Walker(),
					'fallback_cb'    => NULL
				)
			);
		?>
		<div class="hidden-md visible-sm visible-xs tc mt__20 mb__30">
			<?php echo jas_claue_social(); ?>
		</div>
	</div><!-- .jas-canvas-menu -->
	<?php if ( class_exists( 'WooCommerce' ) ) : ?>	
		<div class="jas-mini-cart jas-push-menu">
			<div class="jas-mini-cart-content">
				<h3 class="mg__0 tc cw bgb tu ls__2"><?php esc_html_e( 'Mini Cart', 'claue' );?> <i class="close-cart pe-7s-close pa"></i></h3>
				<div class="widget_shopping_cart_content"></div>
			</div>
		</div><!-- .jas-mini-cart -->
	<?php endif ?>
</header><!-- #jas-header -->