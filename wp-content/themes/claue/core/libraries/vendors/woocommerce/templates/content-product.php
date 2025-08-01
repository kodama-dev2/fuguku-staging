<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see     http://docs.woothemes.com/document/template-structure/
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product, $post, $jassc;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

// Get product options
$options = get_post_meta( get_the_ID(), '_custom_wc_thumb_options', true );

// Get wc style
$style = $jassc ? $jassc['style'] : apply_filters( 'jas_claue_wc_style', cs_get_option( 'wc-style' ) );

$metro = '';

if ( isset( $options['wc-thumbnail-size'] ) && $options['wc-thumbnail-size'] && $style == 'metro' ) {
	$large = 2;
	$metro = ' metro-item';
} else {
	$large = 1;
}

// Extra post classes
$classes = array();
$classes[] = $jassc ? 'jas-col-md-' . (int) $jassc['columns'] * $large . $metro . ' jas-col-sm-4 jas-col-xs-6 mt__30' : 'jas-col-md-' . (int) cs_get_option( 'wc-column' ) * $large . $metro . ' jas-col-sm-4 jas-col-xs-6 mt__30';

// Flip thumbnail
$flip_thumb = $jassc ? $jassc['flip'] : cs_get_option( 'wc-flip-thumb' );

// Countdown for sale product
$start = get_post_meta( get_the_ID(), '_sale_price_dates_from', true );
$end   = get_post_meta( get_the_ID(), '_sale_price_dates_to', true );

if( $product->is_type('variable') ) {
    $variation_ids = $product->get_visible_children();
    foreach( $variation_ids as $variation_id ) {
        $variation = wc_get_product( $variation_id );

        if ( $variation->is_on_sale() ) {
            $variation_date_on_sale_to = $variation->get_date_on_sale_to();

            if( !empty($variation_date_on_sale_to) ) {
                $end = $variation_date_on_sale_to->getTimestamp();
                break;
            }
        }
    }
}

$now   = date( 'd-m-y' );
$attributes = $product->get_attributes();
$date_now = new DateTime();
$date_start = new DateTime(date( 'Y-m-d', (int)$start));
$date_end = new DateTime(date( 'Y-m-d', (int)$end));
$date_end->modify('+1 day');

// Enable add to cart button
$enable_atc = cs_get_option( 'wc-atc-on-product-list' );

// Enable quick shop button
$enable_quickshop = cs_get_option( 'wc-quick-view-btn' );

if( isset($attributes['pa_color']) ){
    /** @var WC_Product_Attribute $pa_color */
    $pa_color = $attributes['pa_color'];
    $get_options = $pa_color->get_options();
    if ( count($get_options) > 1 ){
        $flip_thumb = false;
    }
}

?>
<div <?php post_class( $classes ); ?>>
	<div class="product-inner pr<?php if ( ! empty( $jassc['countdown'] ) && ! empty( $end ) && ! empty ( $now ) && $date_end > $date_now ) : ?> product-countdown<?php endif; ?>">
		<div class="product-image pr">
			<?php
				/**
				 * woocommerce_before_shop_loop_item hook.
				 *
				 * @hooked woocommerce_template_loop_product_link_open - 10
				 */
				do_action( 'woocommerce_before_shop_loop_item' );
			?>
			<?php
				if ( $flip_thumb ) {
					wc_get_template_part( 'content', 'product-image' );
				} else {
					echo '<a class="db" href="' . esc_url( get_permalink() ) . '">';
						/**
						 * woocommerce_before_shop_loop_item_title hook.
						 *
						 * @hooked woocommerce_show_product_loop_sale_flash - 10
						 * @hooked woocommerce_template_loop_product_thumbnail - 10
						 */        
                        remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 ); 
						do_action( 'woocommerce_before_shop_loop_item_title' );
                        echo woocommerce_get_product_thumbnail();
                        
					echo '</a>';
				}
			?>

			<div class="product-btn pa flex column ts__03">
				<?php
					if ( $enable_quickshop && ! cs_get_option( 'wc-catalog' ) ) {
						echo '<a class="btn-quickview cd br__40 pl__25 pr__25 bgw tc dib" href="javascript:void(0);" data-prod="' . esc_attr( $post->ID ) . '">' . esc_html__( 'Quick Shop', 'claue' ) . '</a>';
					}
				?>

				<?php
					if ( $enable_atc ) {
						woocommerce_template_loop_add_to_cart();
					}
				?>
			</div>
			
			<?php if ( ! empty( $jassc['countdown'] ) && ( $end && $date_start <= $date_now ) ) : ?>
				<div class="countdown-time pa">
					<div class="jas-countdown flex tc" data-time='{"day": "<?php echo $date_end->format('d'); ?>", "month": "<?php echo $date_end->format('m'); ?>", "year": "<?php echo $date_end->format('Y'); ?>"}'></div>
				</div>
			<?php endif; ?>
			<?php
				$attrs = cs_get_option( 'wc-attr' );
				if ( $attrs ) {
					echo '<div class="product-attr pa ts__03 cw">';
						foreach ( $attrs as $attr ) {
							$attr_op = 'pa_' . $attr;
							foreach ( $attributes as $attribute ) {

								if ( $attribute && isset( $attribute['name'] ) ) {
									$values = wc_get_product_terms( absint( $product->get_id() ), $attribute['name'], array( 'fields' => 'names' ) );
									if ( $attr_op == $attribute['name'] ) {
										echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );
									}
								}
							}
						}
					echo '</div>';
				}
			?>
		</div><!-- .product-image -->
		<div class="product-info mt__15">
			<?php
				/**
				 * woocommerce_shop_loop_item_title hook.
				 *
				 * @hooked woocommerce_template_loop_product_title - 10
				 */
				do_action( 'woocommerce_shop_loop_item_title' );

				/**
				 * woocommerce_after_shop_loop_item_title hook.
				 *
				 * @hooked woocommerce_template_loop_rating - 5 #removed
				 * @hooked woocommerce_template_loop_price - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item_title' );
			?>
		</div><!-- .product-info -->
	</div><!-- .product-inner -->
</div>
