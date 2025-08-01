<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
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
 * @version     3.3.0
 */

global $jassc;

// Get product list style
$class = $data = $sizer = $slider = '';
$attr = array();
$style = $jassc ? $jassc['style'] : apply_filters( 'jas_claue_wc_style', cs_get_option( 'wc-style' ) );

$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
if ( $term ) {
	$term_options = get_term_meta( $term->term_id, '_custom_product_cat_options', true );
}

if ( is_product_category() && isset( $term_options ) && $term_options && $term_options['product-cat-layout'] ) {
	$layout = $term_options['product-cat-layout'];
} else {
	$layout = cs_get_option( 'wc-layout' );
}

// Get pagination style
$pagination = $jassc ? '' : cs_get_option( 'wc-pagination' );

// Get product filter
$filter = !empty($jassc['filter']) ? $jassc['filter'] : '';

if ( $style != 'grid' ) {
	$class = ' jas-masonry';
	$data  = 'data-masonry=\'{"selector":".product","layoutMode":"masonry","columnWidth":".grid-sizer"' . ( is_rtl() ? ',"rtl": false' : ',"rtl": true' ) . '}\'';
	$sizer = '<div class="grid-sizer size-' . cs_get_option( 'wc-column' ) . '"></div>';
	if ( $style == 'metro' ) {
		$class = ' jas-masonry metro';
	}
} else {
	// Slider setting for shortcode products
	if ( $jassc && !empty($jassc['slider']) && !empty($jassc['slider']) ) {
		if ( ! empty( $jassc['items'] ) ) {
			$attr_slider[] = '"slidesToShow": "' . ( int ) $jassc['items'] . '"';
		}
		if ( ! empty( $jassc['autoplay'] ) ) {
			$attr_slider[] = '"autoplay": true';
		}
		if ( ! empty( $jassc['arrows'] ) ) {
			$attr_slider[] = '"arrows": true';
		} else {
			$attr_slider[] = '"arrows": false';
		}
		if ( ! empty( $jassc['dots'] ) ) {
			$attr_slider[] = '"dots": true';
		}

		if ( is_rtl() ) {
			$attr_slider[] = '"rtl": true';
		}

		if ( ! empty( $attr_slider ) ) {
			$attr[] = 'data-slick=\'{' . esc_attr( implode( ', ', $attr_slider ) ) . ',"responsive":[{"breakpoint": 1024,"settings":{"slidesToShow": 3}},{"breakpoint": 480,"settings":{"slidesToShow": 2}}]' . '}\'';
		}
		$slider = ' jas-carousel';
	}
}

// Layout fitRows for pagination is loadmore
if ( $style == 'grid' && $pagination == 'loadmore' ) {
	$class = ' jas-masonry';
	$data  = 'data-masonry=\'{"selector":".product","layoutMode":"fitRows"' . ( is_rtl() ? ',"rtl": false' : ',"rtl": true' ) . '}\'';
}

?>
<?php if ( ! $jassc ) ?>
	<div class="products jas-row<?php echo esc_attr( $class ); ?><?php echo esc_attr( $slider ); ?>" <?php echo wp_kses_post( $data ); ?> <?php echo implode( ' ', $attr ); ?>>
		<?php echo wp_kses_post( $sizer ); ?>
