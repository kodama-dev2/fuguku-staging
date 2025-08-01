<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( ! WC()->cart->is_empty() ) : ?>

	<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo isset($args['list_class']) ? esc_attr( $args['list_class'] ) : ''; ?>">
		<?php
        do_action( 'woocommerce_before_mini_cart_contents' );

        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                /**
                 * Filter the product name.
                 *
                 * @param string $product_name Name of the product in the cart.
                 */
                $product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
                $thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                $product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                ?>
                <li class="woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
                    <?php
                    echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        'woocommerce_cart_item_remove_link',
                        sprintf(
                            '<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
                            esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                            /* translators: %s is the product name */
                            esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), $product_name ) ),
                            esc_attr( $product_id ),
                            esc_attr( $cart_item_key ),
                            esc_attr( $_product->get_sku() )
                        ),
                        $cart_item_key
                    );
                    ?>
                    <?php if ( empty( $product_permalink ) ) : ?>
                        <?php echo $thumbnail . wp_kses_post( $product_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php else : ?>
                        <a href="<?php echo esc_url( $product_permalink ); ?>">
                            <?php echo $thumbnail . wp_kses_post( $product_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </a>
                    <?php endif; ?>

                    <?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

                    <?php
                    $bundles       = get_post_meta( $product_id, 'wpa_wcpb', true );
                    $bundles_added = explode( ',', ( isset( $cart_item['bundle-products'] ) ? $cart_item['bundle-products'] : '' ) );

                    if ( isset( $cart_item['bundle-products'] ) && $cart_item['bundle-products'] ) {
                        echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], round( $cart_item['custom-price'], 2 ) ) . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    } else {
                        echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    }
                    ?>

                    <?php if ( ! empty( $cart_item['bundle-products'] ) ) : ?>
                        <?php
                        if ( $bundles ) {
                            $custom_variable = $cart_item['bundle-variable'];

                            echo '<ul class="product-bundle fr pd__0">';
                            foreach( $bundles as $key => $val ) {
                                if ( isset($val['product_id']) && in_array( $val['product_id'], $bundles_added ) ) {
                                    $product_item = wc_get_product( intval( $val['product_id'] ) );
                                    echo '<li class="pr">';
                                    echo '<a href="'. $product_item->get_permalink() .'" title="'. $product_item->get_name() .'">'. $product_item->get_name() .'</a>';
                                    // Get variable
                                    if ( ! empty( $val['variable'] ) ) {
                                        $variable = wp_unslash( $val['variable'] );

                                        if ( isset( $custom_variable[$val['product_id']] ) && count( $custom_variable[$val['product_id']] ) > 0 ) {
                                            // Custom variable before add produt bundle to cart
                                            echo '<span class="db" style="text-transform: capitalize;">';
                                            echo wp_kses_post($custom_variable[$val['product_id']]['variable']);
                                            echo '</span>';
                                        } else {
                                            if ( ! empty( $val['variable'] ) ) {
                                                foreach ( $val['variable'] as $kkey => $value ) {
                                                    echo '<span class="db" style="text-transform: capitalize;">';
                                                    echo substr( $kkey, 13 ) . ': ' . $value;
                                                    echo '</span>';
                                                }
                                            }
                                        }
                                    }
                                    echo '</li>';
                                }
                            }
                            echo '</ul>';
                        }
                        ?>
                    <?php endif; ?>
                </li>
                <?php
            }
        }
        do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul><!-- end product list -->

	<p class="woocommerce-mini-cart__total total">
		<?php
		/**
		 * Woocommerce_widget_shopping_cart_total hook.
		 *
		 * @hooked woocommerce_widget_shopping_cart_subtotal - 10
		 */
		do_action( 'woocommerce_widget_shopping_cart_total' );
		?>
	</p>

	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

	<p class="woocommerce-mini-cart__buttons buttons"><?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?> <a class="close-cart button mt__10" href="javascript:void(0)"><?php esc_html_e( 'Continue Shopping', 'claue' );?></a></p>
	<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>

<?php else : ?>

	<p class="woocommerce-mini-cart__empty-message empty"><?php _e( 'Your cart is currently empty. Continue to shopping.', 'claue' ); ?></p>

<?php endif; ?>

<?php jas_claue_minicart_extra_content();?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
