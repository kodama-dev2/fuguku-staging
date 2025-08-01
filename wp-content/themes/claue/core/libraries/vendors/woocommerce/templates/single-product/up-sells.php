<?php
/**
 * Single Product Up-Sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/up-sells.php.
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

$limit = cs_get_option( 'wc-other-product-show' ) ? cs_get_option( 'wc-other-product-show' ) : 3;

if ( $upsells ) : ?>
	<div class="upsells product-extra mt__60">
		<div class="product-extra-title tc">
			<h2 class="tu mg__0 fs__24 pr dib fwsb"><?php esc_html_e( 'You may also like', 'claue' ); ?></h2>
		</div>

		<div class="jas-carousel" data-slick='{"slidesToShow": <?php echo ( int ) $limit; ?>,"slidesToScroll": 1,"responsive":[{"breakpoint": 1024,"settings":{"slidesToShow": 3}},{"breakpoint": 480,"settings":{"slidesToShow": 2}}]<?php echo ( is_rtl() ? ',"rtl":true' : '' ); ?>}'>

			<?php
				foreach ( $upsells as $upsell ) :
				 	$post_object = get_post( $upsell->get_id() );
				 	$GLOBALS['post'] =& $post_object;
					setup_postdata( $post_object );
					
					wc_get_template( 'content-product.php' );
				endforeach;
			?>
		</div>
	</div>
<?php endif;

wp_reset_postdata();
