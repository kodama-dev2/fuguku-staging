<?php
/**
 * Single product layout 3
 */

// Full width layout
$fullwidth = cs_get_option( 'wc-detail-full' );

?>
<div class="jas-wc-single wc-single-3 mb__60">
	<?php
		/**
		 * woocommerce_before_single_product hook.
		 *
		 * @hooked wc_print_notices - 10
		 */
		 do_action( 'woocommerce_before_single_product' );

		if ( post_password_required() ) {
			echo get_the_password_form();
			return;
		}

		echo '<div class="jas-breadcrumb bgbl pt__20 pb__20  pl__15 pr__15 lh__1">';
			woocommerce_breadcrumb();
		echo '</div>';
	?>
	<?php if ( $fullwidth ) echo '<div class="jas-full pl__30 pr__30 jas-row">'; elseif ( ! $fullwidth ) echo '<div class="jas-container">'; ?>			
		<div class="jas-col-md-12 jas-col-xs-12">
			<div id="product-<?php the_ID(); ?>" <?php post_class( 'mt__40' ); ?>>
				<div class="jas-row mb__50">
					<div class="jas-col-md-3 hidden-sm hidden-xs pr pr__0">
						<div class="summary entry-summary jas-sidebar-sticky tc">
							<?php
								remove_action( 'woocommerce_single_product_summary', 'jas_claue_wc_before_price', 5 );
								remove_action( 'woocommerce_single_product_summary', 'jas_claue_wc_after_rating', 15 );

								remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
								add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 3 );
								add_action( 'woocommerce_single_product_summary', 'jas_claue_wc_before_price', 2 );
								add_action( 'woocommerce_single_product_summary', 'jas_claue_wc_after_rating', 4 );

								remove_action( 'woocommerce_single_product_summary', 'jas_claue_wc_add_extra_link_after_cart', 35 );
								remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

								/**
								 * woocommerce_single_product_summary hook.
								 *
								 * @hooked woocommerce_template_single_title - 5
								 * @hooked woocommerce_template_single_rating - 10
								 * @hooked woocommerce_template_single_price - 10
								 * @hooked woocommerce_template_single_excerpt - 20
								 * @hooked woocommerce_template_single_add_to_cart - 30
								 * @hooked woocommerce_template_single_meta - 40
								 * @hooked woocommerce_template_single_sharing - 50
								 */
								do_action( 'woocommerce_single_product_summary' );
							?>
						</div><!-- .summary -->
					</div>
					<div class="jas-col-md-6 jas-col-sm-6 jas-col-xs-12 pr">
						<?php
							/**
							 * woocommerce_before_single_product_summary hook.
							 *
							 * @hooked woocommerce_show_product_sale_flash - 10
							 * @hooked woocommerce_show_product_images - 20
							 */
							do_action( 'woocommerce_before_single_product_summary' );
						?>
					</div>
					<div class="jas-col-md-3 jas-col-sm-6 jas-col-xs-12 pr pl__0">
						<div class="summary entry-summary jas-sidebar-sticky tc">
							<div class="hide-md visible-sm">
								<?php
									woocommerce_template_single_rating();
									woocommerce_template_single_title();
									woocommerce_template_single_price();
									woocommerce_template_single_excerpt();

								?>
							</div>
							<!--  Do not remove this -->
							<div class="cart-moved"></div>
							<?php
								jas_claue_wc_add_extra_link_after_cart();
							?>

							<div class="hide-md visible-sm">
								<?php
									remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
									remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
									remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
									remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
									remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
									remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
									remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
									do_action( 'woocommerce_single_product_summary' );
								?>

							</div>
						</div>
					</div>
				</div>

				<?php
					/**
					 * woocommerce_after_single_product_summary hook.
					 *
					 * @hooked woocommerce_output_product_data_tabs - 10
					 * @hooked woocommerce_upsell_display - 15
					 * @hooked woocommerce_output_related_products - 20
					 */
					do_action( 'woocommerce_after_single_product_summary' );
				?>
			</div><!-- #product-<?php the_ID(); ?> -->
		</div>
	</div>
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>
