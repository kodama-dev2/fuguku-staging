<?php

/*
* @@ Cart Price View
* @@ Last updated version 4.0.0
*/

class AWDP_viewCartPrice
{

    public function cart_price ( $rules, $price, $cart_item, $prodLists, $item_price, $activeDiscounts, $product, $quantity, $wdpGiftsinCart, $wdpBogoIDs, $tieredIDsinCart )
    {

        $parent_id          = $cart_item['product_id'];
        $cart_prod_id       = $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];
        $prod_ID            = $cart_item['data']->get_slug();
        $cartKey            = $cart_item['key'];
        $converted_rate     = 1;
        $discount           = 0; 
        $free_label         = __("Free", "aco-woo-dynamic-pricing");
        $actual_quantity    = $quantity;
        $quantity           = 1; // Set quantity to 1 - individual product price

        /*
        * ver @ 5.0.5
        * Disabled tax check -  $price already includes tax 
        */
        // if ( WC()->cart->display_prices_including_tax() ) {
        //     $price = call_user_func_array ( 
        //                 array ( new AWDP_Discount(), 'wdp_price_including_tax' ), 
        //                 array ( $product, $price , array ( 'qty' => $quantity, 'price' => $price )) 
        //             );
        // } else {
        //     $price = call_user_func_array ( 
        //                 array ( new AWDP_Discount(), 'wdp_price_excluding_tax' ), 
        //                 array ( $product, $price , array ( 'qty' => $quantity, 'price' => $price )) 
        //             );
        // }

        if ( $activeDiscounts ) { 
            foreach ( $activeDiscounts as $discounts ) {  
                // Added tiered pricing
                // if ( $discounts['discount_type'] == 'cart_quantity' && array_key_exists ( 'tieredDiscount', $discounts ) && !empty ( $discounts['tieredDiscount'] ) ) { 

                //     foreach ( $discounts['tieredDiscount']['tierRange'] as $key => $tierRange ) { 
                //         $tierStart      = $tierRange['start'] - 1;
                //         $tierEnd        = $tierRange['end'];
                //         $calc_discount  = $tierRange['value'] * ( $tierEnd - $tierStart ); 
                //         $discount       = $discount + ( wc_remove_number_precision ( $calc_discount ) ); 
                //     }

                // } else if ( array_key_exists ( 'discounts', $discounts ) ) { 
                if ( array_key_exists ( 'discounts', $discounts ) ) { 
                    if ( array_key_exists ( $cartKey, $discounts['discounts'] ) && isset ( $discounts['discounts'][$cartKey]['discount'] ) && isset ( $discounts['discounts'][$cartKey]['displayoncart'] ) ) {
                        $discount += wc_remove_number_precision ( $discounts['discounts'][$cartKey]['discount'] );
                    }
                }
            } 
        } 

        /*
        * ver @ 5.0.5
        * Tax Settings
        */
        $tax_display_mode   = get_option( 'woocommerce_tax_display_shop' );
        // $discount           = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax( $product, array ( 'price' => $discount ) ) : wc_get_price_excluding_tax( $product, array ( 'price' => $discount ) );
        
        $discounted_price   = $price - $discount; 

        if ( ( sizeof($wdpGiftsinCart) > 0 ) && awdp_bgcheck ( $wdpGiftsinCart, $cart_prod_id ) ) { 

            foreach ( $wdpGiftsinCart as $key => $value ) { 
 
                if ( array_search ( $cart_prod_id, array_column ( $value, 'product_id' ) ) !== false ) {

                    $item_price         = '<span class="woocommerce-Price-amount amount">'.$free_label.'</span>';
                    $gift_index         = array_search ( $cart_prod_id, array_column ( $value, 'product_id' ) );
                    $gift_actual_price  = $value[$gift_index]['price'];
                    // $other_discounts    = array_key_exists($prod_ID, $this->wdp_discounted_price) ? wc_remove_number_precision($this->wdp_discounted_price[$prod_ID]) : 0;
                    $other_discounts    = $price; 
                    $quantity           = 1; 
                    $product            = wc_get_product( $cart_prod_id );

                    // if ($gift_actual_price > 0) {
                    //     if ( WC()->cart->display_prices_including_tax() ) {
                    //         $gift_actual_price = call_user_func_array ( 
                    //             array ( new AWDP_Discount(), 'wdp_price_including_tax' ), 
                    //             array ( $product, $gift_actual_price, array ( 'qty' => $quantity, 'price' => $gift_actual_price ) ) 
                    //         );
                    //     } else {
                    //         $gift_actual_price = call_user_func_array ( 
                    //             array ( new AWDP_Discount(), 'wdp_price_excluding_tax' ), 
                    //             array ( $product, $gift_actual_price, array ( 'qty' => $quantity, 'price' => $gift_actual_price ) ) 
                    //         );
                    //     }
                    // }

                    if ( $actual_quantity > 1 ) {

                        $gift_updated_qn = $actual_quantity - 1;

                        if ( $gift_updated_qn == 0 ) {
                            $item_price = '<div style="display: inline-block;" class="wdp-cart-item-data-wrap">';
                            $item_price .= '<div style="clear: both;"><div style="float: left;">'.$free_label.'</div><div style="float: right; padding-left: 1em;">× 1</div></div>';
                            $item_price .= '</div>';
                        } else if ( $other_discounts != 0 && $other_discounts < $gift_actual_price ) {
                            $item_price = '<div style="display: inline-block;" class="wdp-cart-item-data-wrap">';
                            $item_price .= '<div style="clear: both;"><div style="float: left;">'.$free_label.'</div><div style="float: right; padding-left: 1em;">× 1</div></div>';
                            $item_price .= '<div style="clear: both;"><div style="float: left;">'.wc_format_sale_price( $gift_actual_price * $converted_rate, $other_discounts * $converted_rate ).'</div><div style="float: right; padding-left: 1em;">× '.$gift_updated_qn.'</div></div>';
                            $item_price .= '</div>';
                        } else {
                            $item_price = '<div style="display: inline-block;" class="wdp-cart-item-data-wrap">';
                            $item_price .= '<div style="clear: both;"><div style="float: left;">'.$free_label.'</div><div style="float: right; padding-left: 1em;">× 1</div></div>';
                            $item_price .= '<div style="clear: both;"><div style="float: left;">'.wc_price( $gift_actual_price * $converted_rate ).'</div><div style="float: right; padding-left: 1em;">× '.$gift_updated_qn.'</div></div>';
                            $item_price .= '</div>';
                        }

                    }
                
                } 
            
            }
            
        } else if ( ( sizeof($wdpBogoIDs) > 0 ) && awdp_bgcheck ( $wdpBogoIDs, $cart_prod_id ) ) { 

            foreach ( $wdpBogoIDs as $key => $value ) { 
 
                if ( array_search ( $cartKey, array_column ( $value, 'cartKey' ) ) !== false ) {
                    
                    $bogo_index         = array_search ( $cartKey, array_column ( $value, 'cartKey' ) );
                    $bogo_type          = $value[$bogo_index]['discount_type'];
                    $bogo_value         = $value[$bogo_index]['discount_percentage'];
                    $ofr_count          = array_key_exists('offer_items', $value[$bogo_index]) ? $value[$bogo_index]['offer_items'] : 1;

                    $bogo_price         = array_key_exists('discounted_price_single', $value[$bogo_index]) ? abs(wc_remove_number_precision($value[$bogo_index]['discounted_price_single'])) : abs(wc_remove_number_precision($value[$bogo_index]['discounted_price'])); 
                    
                    // $actual_quantity    = $quantity;
                    $quantity           = 1; 
                    $product            = wc_get_product( $cart_prod_id );
                    // $other_discounts    = array_key_exists($prod_ID, $this->wdp_discounted_price) ? wc_remove_number_precision($this->wdp_discounted_price[$prod_ID]) : 0;
                    $other_discounts    = $price; 

                    /*
                    * ver @ 5.0.5 
                    * Tax Settings
                    */
                    // $tax_display_mode   = get_option( 'woocommerce_tax_display_shop' );
                    // $bogo_price         = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax( $product, array ( 'price' => $bogo_price ) ) : wc_get_price_excluding_tax( $product, array ( 'price' => $bogo_price ) );
                    // $discounted_price   = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax( $product, array ( 'price' => $discounted_price ) ) : wc_get_price_excluding_tax( $product, array ( 'price' => $discounted_price ) );
                    // $other_discounts    = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax( $product, array ( 'price' => $other_discounts ) ) : wc_get_price_excluding_tax( $product, array ( 'price' => $other_discounts ) ); 

                    if ( $actual_quantity > 1 ) {  

                        $bogo_updated_qn = $actual_quantity - $ofr_count;

                        if($bogo_type == 'free') {

                            if ( $bogo_updated_qn == 0 ) { 
                                $item_price = '<div style="display: inline-block;" class="wdp-cart-item-data-wrap">';
                                $item_price .= '<div style="clear: both;"><div style="float: left;">'.$free_label.'</div><div style="float: right; padding-left: 1em;">× '.$ofr_count.'</div></div>';
                                $item_price .= '</div>';
                            } else if ( $other_discounts != 0 && $other_discounts < $discounted_price ) { 
                                $item_price = '<div style="display: inline-block;" class="wdp-cart-item-data-wrap">';
                                $item_price .= '<div style="clear: both;"><div style="float: left;">'.$free_label.'</div><div style="float: right; padding-left: 1em;">× '.$ofr_count.'</div></div>';
                                $item_price .= '<div style="clear: both;"><div style="float: left;">'.wc_format_sale_price( $discounted_price * $converted_rate, $other_discounts * $converted_rate ).'</div><div style="float: right; padding-left: 1em;">× '.$bogo_updated_qn.'</div></div>';
                                $item_price .= '</div>';
                            } else { 
                                $item_price = '<div style="display: inline-block;" class="wdp-cart-item-data-wrap">';
                                $item_price .= '<div style="clear: both;"><div style="float: left;">'.$free_label.'</div><div style="float: right; padding-left: 1em;">× '.$ofr_count.'</div></div>';
                                $item_price .= '<div style="clear: both;"><div style="float: left;">'.wc_price( $discounted_price * $converted_rate ).'</div><div style="float: right; padding-left: 1em;">× '.$bogo_updated_qn.'</div></div>';
                                $item_price .= '</div>';
                            }

                        } else { 

                            if ( $bogo_updated_qn == 0 ) {  
                                $item_price = '<div style="display: inline-block;" class="wdp-cart-item-data-wrap">';
                                $item_price .= '<div style="clear: both;"><div style="float: left;">'.wc_format_sale_price( $other_discounts * $converted_rate, $bogo_price * $converted_rate ).'</div><div style="float: right; padding-left: 1em;">× '.$ofr_count.'</div></div></div>'; 
                            } else if ( $other_discounts != 0 && $other_discounts < $discounted_price ) {
                                $item_price = '<div style="display: inline-block;" class="wdp-cart-item-data-wrap">';
                                $item_price .= '<div style="clear: both;"><div style="float: left;">'.wc_format_sale_price( $other_discounts * $converted_rate, $bogo_price * $converted_rate ).'</div><div style="float: right; padding-left: 1em;">× '.$ofr_count.'</div></div>';
                                $item_price .= '<div style="clear: both;"><div style="float: left;">'.wc_format_sale_price( $discounted_price * $converted_rate, $other_discounts * $converted_rate ).'</div><div style="float: right; padding-left: 1em;">× '.$bogo_updated_qn.'</div></div>';
                                $item_price .= '</div>';
                            } else { 
                                $item_price = '<div style="display: inline-block;" class="wdp-cart-item-data-wrap">';
                                $item_price .= '<div style="clear: both;"><div style="float: left;">'.wc_format_sale_price( $discounted_price * $converted_rate, $bogo_price * $converted_rate ).'</div><div style="float: right; padding-left: 1em;">× '.$ofr_count.'</div></div>';
                                $item_price .= '<div style="clear: both;"><div style="float: left;">'.wc_price( $discounted_price * $converted_rate ).'</div><div style="float: right; padding-left: 1em;">× '.$bogo_updated_qn.'</div></div>';
                                $item_price .= '</div>';
                            }

                        }

                    } else {

                        if( $bogo_type == 'free' ) {
    
                            $item_price = '<span class="woocommerce-Price-amount amount">'.$free_label.'</span>';
    
                        } else if ( $discounted_price == 0 || $discounted_price == 0.0000000001 ) { 
    
                            $item_price = '<span class="woocommerce-Price-amount amount">'.$free_label.'</span>';
                            
                        } else { 
    
                            $item_price = wc_format_sale_price ( $discounted_price * $converted_rate, $bogo_price * $converted_rate );
    
                        }
    
                    }

                }

            }
            
        } else if ( ( sizeof($tieredIDsinCart) > 0 ) && ( array_search ( $cart_prod_id, array_column ( $tieredIDsinCart, 'product_id' ) ) !== false ) ) {

            $tiered_index = array_search ( $cart_prod_id, array_column ( $tieredIDsinCart, 'product_id' ) );
            // foreach ( $tieredIDsinCart as $key => $value ) {    
            //     if ( $cart_prod_id == $value['product_id'] ) { 
                    
                    if ( isset ( $tieredIDsinCart[$tiered_index]['tieredPricing'] ) && !empty ( $tieredIDsinCart[$tiered_index]['tieredPricing'] ) ) { 
                        $item_price = '<div style="display: inline-block;" class="wdp-cart-item-data-wrap">';
                        foreach ( $tieredIDsinCart[$tiered_index]['tieredPricing'] as $tieredPricing )
                        { 
                            $tiered_qn = ( $tieredPricing['end'] - $tieredPricing['start'] > 0 ) ? ( $tieredPricing['end'] - $tieredPricing['start']  + 1 ) : 1;
                            if ( wc_remove_number_precision ( $tieredPricing['price'] ) < $price ) {
                                $item_price .= '<div style="clear: both;"><div style="float: left;">'.wc_format_sale_price ( $price * $converted_rate, wc_remove_number_precision ( $tieredPricing['price'] ) * $converted_rate ).'</div><div style="float: right; padding-left: 1em;">× '.$tiered_qn.'</div></div>';
                            } else {
                                $item_price .= '<div style="clear: both;"><div style="float: left;">'.wc_price( wc_remove_number_precision ( $tieredPricing['price'] ) * $converted_rate ).'</div><div style="float: right; padding-left: 1em;">× '.$tiered_qn.'</div></div>'; 
                            }
                        }
                        $item_price .= '</div>';
                    }

            //     }
            // }
            
        } else if ( ( $discounted_price < $price ) || $discounted_price == 0 ) { 

            $item_price = wc_format_sale_price ( $price * $converted_rate, $discounted_price * $converted_rate );

        } else if ( $discounted_price > $price ) { 

            $item_price = wc_format_sale_price ( $price * $converted_rate, $discounted_price * $converted_rate );

        }

        $results['item_price']      = $item_price;
        $results['discountedprice'] = $discounted_price * $converted_rate;

        return $results;

    }

}