<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $cross_sells ) : ?>
	<div class="cross-sells product-extra">

		<h2><?php esc_html_e( 'You may be interested in&hellip;', 'claue' ) ?></h2>

		<div class="jas-carousel" data-slick='{"slidesToShow": 2,"slidesToScroll": 1,"responsive":[{"breakpoint": 1024,"settings":{"slidesToShow": 2}},{"breakpoint": 480,"settings":{"slidesToShow": 2}}]<?php echo ( is_rtl() ? ',"rtl":true' : '' ); ?>}'>

			<?php foreach ( $cross_sells as $cross_sell ) : ?>
				<?php
				 	$post_object = get_post( $cross_sell->get_id() );

					$GLOBALS['post'] =& $post_object;
					setup_postdata( $post_object );

					wc_get_template( 'content-product.php' );
				?>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif;

wp_reset_postdata();
