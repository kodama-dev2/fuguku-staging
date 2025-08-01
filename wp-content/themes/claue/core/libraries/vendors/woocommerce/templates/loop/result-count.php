<?php
/**
 * Result Count
 *
 * Shows text: Showing x - x of x results.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/result-count.php.
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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wp_query;

if ( ! woocommerce_products_will_display() )
	return;

$columns = cs_get_option( 'wc-column' );
$enable  = cs_get_option( 'wc-sidebar-filter' );
$swicher = cs_get_option( 'wc-col-switch' );
if ( $enable ) : ?>
	<a class="filter-trigger mr__50"><i class="fa fa-sliders fwb"></i></a>
<?php endif; ?>

<?php if ( $swicher ) : ?>
	<div class="wc-col-switch flex">
		<a href="#" class="pr one hide-md hidden-sm visible-xs mr__10<?php if ($columns == '12') echo ' active'; ?>" data-col="12"></a>
		<a href="#" class="pr two mr__10<?php if ($columns == '6') echo ' active'; ?>" data-col="6"></a>
		<a href="#" class="pr hidden-xs three mr__10<?php if ($columns == '4') echo ' active'; ?>" data-col="4"></a>
		<a href="#" class="pr hidden-sm four mr__10<?php if ($columns == '3') echo ' active'; ?>" data-col="3"></a>
		<a href="#" class="pr hidden-sm six<?php if ($columns == '2') echo ' active'; ?>" data-col="2"></a>
	</div>
<?php endif; ?>