<?php
/**
 * The template for displaying product content in the content-quickview-product.php template
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $quickview;
$quickview = true;
?>
<div id="product-<?php the_ID(); ?>" <?php post_class( 'product-quickview pr' ); ?>>
	<div class="jas-row">
		<div class="jas-col-md-7 jas-col-sm-6 jas-col-xs-12 pr">
			<?php do_action( 'woocommerce_quickview_before_thumbnail' ); ?>
			<?php echo woocommerce_show_product_loop_sale_flash();?>
			<div class="single-product-thumbnail pr">
				<div class="p-thumb images jas-carousel" data-slick='{"slidesToShow": 1,"slidesToScroll": 1,"dots": true,"fade":true<?php echo ( is_rtl() ? ',"rtl":true' : '' ); ?>}'>
					<?php
						if ( has_post_thumbnail() ) {
							$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
							$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
							$thumbnail_post    = get_post( $post_thumbnail_id );
							$image_title       = $thumbnail_post->post_content;

							$attributes = array(
								'title'                   => $image_title,
								'data-src'                => $full_size_image[0],
								'data-large_image'        => $full_size_image[0],
								'data-large_image_width'  => $full_size_image[1],
								'data-large_image_height' => $full_size_image[2],
							);

							$html  = '<div class="p-item woocommerce-product-gallery__image">';
								$html .= get_the_post_thumbnail( $post->ID, 'shop_single', $attributes );
							$html .= '</div>';

						} else {
							$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
								$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'claue' ) );
							$html .= '</div>';
						}

						echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, get_post_thumbnail_id( $post->ID ) );

						if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
							$attachment_ids = $product->get_gallery_attachment_ids();
						} else {
							$attachment_ids = $product->get_gallery_image_ids();
						}

						if ( $attachment_ids ) {
							foreach ( $attachment_ids as $attachment_id ) {
								$full_size_image  = wp_get_attachment_image_src( $attachment_id, 'full' );
								$thumbnail        = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );
								$thumbnail_post   = get_post( $attachment_id );
								$image_title      = $thumbnail_post->post_content;

								$attributes = array(
									'title'                   => $image_title,
									'data-src'                => $full_size_image[0],
									'data-large_image'        => $full_size_image[0],
									'data-large_image_width'  => $full_size_image[1],
									'data-large_image_height' => $full_size_image[2],
								);

								$html = '<div>';
									$html .= '<a href="' . esc_url( $full_size_image[0] ) . '">';
										$html .= wp_get_attachment_image( $attachment_id, 'shop_single', false, $attributes );
									$html .= '</a>';
								$html .= '</div>';

								echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $attachment_id );
							}
						}
					?>
				</div>
			</div>
			<?php do_action( 'woocommerce_quickview_after_thumbnail' ); ?>
		</div><!-- .jas-col-md-6 -->
		
		<div class="jas-col-md-5 jas-col-sm-6 jas-col-xs-12">
			<?php do_action( 'woocommerce_quickview_before_summary' ); ?>
			<div class="content-quickview entry-summary">
				<?php
					/**
					 * woocommerce_single_product_summary hook
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
				<a class="btn fwsb detail_link" href="<?php echo get_permalink( $product_id ) ?>"><?php echo esc_html__( 'View details', 'claue' ); ?> <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
			</div><!-- .summary -->
			<?php do_action( 'woocommerce_quickview_after_summary' ); ?>
		</div><!-- .jas-col-md-6 -->
	</div><!-- .row -->
</div><!-- .product-quickview -->