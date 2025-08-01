<?php

/*
* @@ Product Price
* @@ Last updated version 4.0.0
*/

class AWDP_typeBogo
{

    public function apply_discount_bogo ( $rule, $product, $price, $quantity, $discVariable, $prodLists, $cartContents, $cart_item, $wdpBogoIDsinCart, $wdpBogoIDs, $discPrices, $disc_prod_ID, $dispPrice, $cartView = false )
    { 

        $result                     = [];

        if ( isset(WC()->cart) && WC()->cart->get_cart_contents_count() > 0 ) {

            $prod_ID                    = $cart_item['data']->get_slug();  
            $awdp_prod_id               = $cart_item['data']->get_id();
            $cartKey                    = $cartView ? $prod_ID : $cart_item['key'];
            $actualCartKey              = $cart_item['key'];
            $ruleID                     = $rule['id'];
            $products_array             = [];
            $free_count                 = 1;
            $awdp_cart_bogo_count       = 0;
            $BogoFlag                   = false;

            // Rule details
            $awdp_qn_buy                = (int)$rule['bogo_buy'];
            $awdp_qn_get                = (int)$rule['bogo_get']; 
            $bogo_type                  = $rule['bogo_type'];
            $bogo_value                 = ( 'off_percentage' == $bogo_type || 'fixed' == $bogo_type ) ? (int)$rule['bogo_value'] : 0 ;
            $bogo_combinations          = $rule['bogo_combinations'];
            $bogo_rule_type             = array_key_exists('bogo_rule_type', $rule) ? $rule['bogo_rule_type'] : '';
            $bogo_x_prods               = array_key_exists('bogo_x_prods', $rule) ? $rule['bogo_x_prods'] : [];
            $bogo_n_prods               = array_key_exists('bogo_n_prods', $rule) ? $rule['bogo_n_prods'] : [];
            $bogo_n_count               = array_key_exists('bogo_n_count', $rule) ? $rule['bogo_n_count'] : '';
            $bogo_n_repeat              = array_key_exists('bogo_n_repeat', $rule) ? $rule['bogo_n_repeat'] : '';
            $bogo_n_loop                = array_key_exists('bogo_n_loop', $rule) ? $rule['bogo_n_loop'] : '';
            $bogo_payn_prods            = array_key_exists('bogo_payn_prods', $rule) ? $rule['bogo_payn_prods'] : [];
            $bogopayranges              = array_key_exists('bogopayranges', $rule) ? $rule['bogopayranges'] : '';
            $bogo_multitple_item        = array_key_exists('bogo_multitple_item', $rule) ? $rule['bogo_multitple_item'] : '';
            $bogo_cheap_get             = array_key_exists('bogo_cheap_get', $rule) ? (int)$rule['bogo_cheap_get'] : '';
            $bogo_cheap_buy             = array_key_exists('bogo_cheap_buy', $rule) ? (int)$rule['bogo_cheap_buy'] : '';
            $bogo_cheap_prods           = array_key_exists('bogo_cheap_prods', $rule) ? $rule['bogo_cheap_prods'] : '';
            $bogo_cheap_type            = array_key_exists('bogo_cheap_type', $rule) ? $rule['bogo_cheap_type'] : '';
            $bogo_cheap_value           = array_key_exists('bogo_cheap_value', $rule) ? $rule['bogo_cheap_value'] : '';
            $bogo_description           = array_key_exists('bogo_message', $rule) ? $rule['bogo_message'] : '';
            $bogo_x_all                 = array_key_exists('bogo_x_all', $rule) ? $rule['bogo_x_all'] : '';
            
            $bogo_x_repeat              = array_key_exists('bogo_x_repeat', $rule) ? $rule['bogo_x_repeat'] : '';
            $bogo_x_count               = array_key_exists('bogo_x_count', $rule) ? $rule['bogo_x_count'] : '';

            $bogo_display_sort          = array_key_exists('bogo_display_sort', $rule) ? $rule['bogo_display_sort'] : '';
            $bogo_display_count         = array_key_exists('bogo_display_count', $rule) ? $rule['bogo_display_count'] : '';
            $bogo_consider_individual   = array_key_exists('bogo_consider_individual', $rule) ? $rule['bogo_consider_individual'] : '';

            $bogo_y_individual          = get_post_meta($ruleID, 'discount_bogo_y_individual', true) ? get_post_meta($ruleID, 'discount_bogo_y_individual', true) : false;

            $bogo_cheap_individual      = get_post_meta($ruleID, 'discount_cheapest_check', true) ? get_post_meta($ruleID, 'discount_cheapest_check', true) : false;
            $bogo_cheap_repeat          = get_post_meta($ruleID, 'discount_repeat_cheapest', true) ? get_post_meta($ruleID, 'discount_repeat_cheapest', true) : false;

            // $bogo_y_all                 = array_key_exists('bogo_y_all', $rule) ? $rule['bogo_y_all'] : ''; // repeat disabled @ 3.2.1
            $bogo_y_all                 = '';
            $awdp_in_cart               = [];
            $skip_id                    = [];

            // Offer products falg
            $offer_flag                 = true;
            $all_flag                   = false;

            // Buy X get Y flag
            $awdp_BY_flag               = false;

            $awdp_cart_items            = $cartContents ? $cartContents : WC()->cart->get_cart();
            $awdp_products              = $awdp_actual_products = $awdp_cart_quanity = $awdp_product_price = $awdp_cart_price = [];

            // Buy X Get Y
            // $ofr_applied                = 0;
            // $ofr_remaining              = 0;
            // $awdp_buy_qn                = 0;

            $discountApplied            = [];

            /*
            * ver @ 5.0.5 
            * Tax Settings
            */
            $tax_display_mode           = get_option( 'woocommerce_tax_display_shop' );
            $pirce_include_tax          = get_option( 'woocommerce_prices_include_tax' ); 
            $decimal_points             = get_option( 'woocommerce_price_num_decimals' ) ? get_option( 'woocommerce_price_num_decimals' ) : 2;

            // Cart Message
            $wdp_msg_flag               = false;

            $skipFlag                   = false;

            if ( $awdp_cart_items ) {

                // Sort cart items based on quantity
                /* 
                * @ ver 5.0.3
                * Sort based on price for bogo combinations
                * @ ver 5.1.4
                * added 'bogo_rule_cheapest' for price sort -> $bogo_rule_type == 'bogo_rule_cheapest'
                */
                if ( $bogo_rule_type == 'bogo_rule_different' || $bogo_rule_type == 'bogo_rule_cheapest' ) { 
                    usort ( $awdp_cart_items, function ( $item1, $item2 ) { 
                        return $item1['data']->get_price() <=> $item2['data']->get_price();
                    });
                } else {
                    usort ( $awdp_cart_items, function ( $item1, $item2 ) { 
                        return $item2['quantity'] <=> $item1['quantity'];
                    });
                }
                // End Sort

                foreach ( $awdp_cart_items as $awdp_cart_item ) { 

                    $cartKey = array_key_exists ( 'key', $awdp_cart_item ) ? $awdp_cart_item['key'] : '';
                    /* 
                    * Sale Items Check
                    * @ver 5.0.3
                    */
                    if ( isset($rule['disable_on_sale']) && $rule['disable_on_sale'] && $awdp_cart_item['data']->is_on_sale() ) { 
                        continue;
                    }
                    //

                    
                    /*
                    * ver @ 5.0.5 
                    * Tax Settings
                    */
                    // $tax_display_mode   = get_option( 'woocommerce_tax_display_shop' );
                    // $priceIncTax        = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax( $product, array ( 'price' => $cartprice ) ) : wc_get_price_excluding_tax( $product, array ( 'price' => $cartprice ) );
                    // $cartprice          = $priceIncTax ? $priceIncTax : $cartprice;

                    /*
                    * ver @ 5.0.9 
                    * If more than 2 decimal points, round to 2 decimal points
                    */
                    $cartPriceIncTax            = ( ( strlen ( strrchr ( wc_get_price_including_tax ( $product, array ( 'price' => $awdp_cart_item['data']->get_price() ) ), '.' ) ) -1 ) > 2 ) ? round ( wc_get_price_including_tax ( $product, array ( 'price' => $awdp_cart_item['data']->get_price() ) ), 2 ) : wc_get_price_including_tax ( $product, array ( 'price' => $awdp_cart_item['data']->get_price() ) );
                    $cartPriceExcTax            = ( ( strlen ( strrchr ( wc_get_price_excluding_tax ( $product, array ( 'price' => $awdp_cart_item['data']->get_price() ) ), '.' ) ) -1 ) > 2 ) ? round ( wc_get_price_excluding_tax ( $product, array ( 'price' => $awdp_cart_item['data']->get_price() ) ), 2 ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $awdp_cart_item['data']->get_price() ) );
                    $priceAfterTax              = ( 'incl' === $tax_display_mode ) ? $cartPriceIncTax : ( ( 'yes' === $pirce_include_tax && 'excl' === $tax_display_mode ) ? $cartPriceExcTax : '' ); 
                    $cart_display_price         = $priceAfterTax ? $priceAfterTax : $awdp_cart_item['data']->get_price(); 
                    
                    $cartprice                  = !empty ($discPrices) ? ( ( array_key_exists ( $prod_ID, $discPrices ) && $discPrices[$prod_ID] != '' ) ? wc_remove_number_precision ( $discPrices[$prod_ID] ) : $awdp_cart_item['data']->get_price() ) : $awdp_cart_item['data']->get_price();

                    $awdp_cart_price[$cartKey]  = ( array_key_exists( $cartKey, $discPrices ) && $discPrices[$cartKey] != '' ) ? wc_remove_number_precision ( $discPrices[$cartKey] ) : $cartprice;

                    if ( array_key_exists( 'variation_id', $awdp_cart_item ) && $awdp_cart_item['variation_id'] == 0 ) {

                        array_push($awdp_products, $awdp_cart_item['product_id']);
                        array_push($awdp_actual_products, $awdp_cart_item['product_id']);
                        $awdp_cart_quanity[$awdp_cart_item['product_id']]       = $awdp_cart_item['quantity']; 
                        // $awdp_product_price[$awdp_cart_item['product_id']]      = $cartprice;
                        $awdp_product_price[$awdp_cart_item['product_id']]      = ( array_key_exists( $cartKey, $discPrices ) && $discPrices[$cartKey] != '' ) ? wc_remove_number_precision ( $discPrices[$cartKey] ) : $cartprice;

                    } else {

                    // if ( $awdp_cart_item['variation_id'] != 0 ) {
                        array_push($awdp_products, $awdp_cart_item['variation_id']); 
                        array_push($awdp_actual_products, $awdp_cart_item['variation_id']); 
                        $awdp_cart_quanity[$awdp_cart_item['variation_id']]     = $awdp_cart_item['quantity']; 
                        // $awdp_product_price[$awdp_cart_item['variation_id']]    = $cartprice;
                        $awdp_product_price[$awdp_cart_item['variation_id']]    = ( array_key_exists( $cartKey, $discPrices ) && $discPrices[$cartKey] != '' ) ? wc_remove_number_precision ( $discPrices[$cartKey] ) : $cartprice; 
                        $skip_id[]                                              = $awdp_cart_item['product_id'];
                        // Parent Quantity
                        if ( !in_array($awdp_cart_item['product_id'], $awdp_products) ) {
                            array_push($awdp_products, $awdp_cart_item['product_id']);
                            $awdp_cart_quanity[$awdp_cart_item['product_id']]   = $awdp_cart_item['quantity'];
                            // $awdp_product_price[$awdp_cart_item['product_id']]  = $cartprice;
                            $awdp_product_price[$awdp_cart_item['product_id']]  = ( array_key_exists( $cartKey, $discPrices ) && $discPrices[$cartKey] != '' ) ? wc_remove_number_precision ( $discPrices[$cartKey] ) : $cartprice;
                        } else {
                            $awdp_cart_quanity[$awdp_cart_item['product_id']]   = $awdp_cart_quanity[$awdp_cart_item['product_id']] + 1;
                            // $awdp_product_price[$awdp_cart_item['product_id']]  = $cartprice;
                            $awdp_product_price[$awdp_cart_item['product_id']]  = ( array_key_exists( $cartKey, $discPrices ) && $discPrices[$cartKey] != '' ) ? wc_remove_number_precision ( $discPrices[$cartKey] ) : $cartprice;
                        }

                    }

                } 

            }

            // All Products
            $all_products = $awdp_products ? $awdp_products : []; // Set all products to cart items - 3.1.0

            if ( isset($awdp_cart_quanity[$awdp_prod_id]) ) {  

                $awdp_cart_prod_id  = ( $cart_item['data']->get_parent_id() == 0 ) ? $cart_item['data']->get_id() : $cart_item['data']->get_parent_id();
                $list_key           = 'list_'; 

                if ( $bogo_rule_type == 'bogo_rule_same' ) { 
 
                    /* 
                    * BOGO Rule: Buy X Get X (Same Product)
                    * Discount types: Fixed, Percentage, Free
                    * ver 5.0.7 -> ( $awdp_prod_id == $bogo_variation ) check added - fix - cart amount mismacth when multiple rules activated
                    * ver 5.0.8 -> offer count reset for variable products
                    * ver 5.0.9 -> product id to cartKey
                    */

                    $bogo_x_list    = [];

                    if ( $bogo_x_prods == 'null' || $bogo_x_prods == '' ) { // Any Product
                        $bogo_x_list        = $all_products; // Full products
                        $all_flag           = true;
                    } else if ( strpos ( $bogo_x_prods, $list_key ) === false ) {
                        $checkproduct       = wc_get_product($bogo_x_prods); 
                        $children_ids       = $checkproduct ? $checkproduct->is_type( 'variable' ) : ''; 
                        if ( $children_ids ) {
                            // get variations
                            $bogo_x_list    = call_user_func_array ( 
                                                  array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                                                  array ( $bogo_x_prods )
                                              ); 
                        } else {
                            $bogo_x_list[]  = $bogo_x_prods;
                        }
                    } else { // Check if product list
                        $list_id            = intval ( str_replace ( $list_key, '', $bogo_x_prods ) );
                        $list_prods         = call_user_func_array ( 
                            array ( new AWDP_Discount(), 'wdpProductList' ), 
                            array ( $list_id ) 
                        );
                        $bogo_x_list        = array_unique ( array_merge ( $list_prods, $bogo_x_list ) );
                    }

                    if ( array_intersect ( $bogo_x_list, $awdp_products ) ) { // in cart

                        $bogo_x_cart        = array_values(array_intersect( $bogo_x_list, $awdp_products));
                        $wdpBogoIDs         = array_merge ( $wdpBogoIDs, $bogo_x_list );

                        foreach ( $bogo_x_list as $bogo_x_single ) { 
                            
                            $ofr_count      = $bogo_x_all ? ( array_key_exists($bogo_x_single, $awdp_cart_quanity) ? ( ( $awdp_cart_quanity[$bogo_x_single] % 2 ) == 0 ? ( $awdp_cart_quanity[$bogo_x_single] / 2 ) : floor ( $awdp_cart_quanity[$bogo_x_single] / 2 ) ) : 1 ) : 1; 

                            // $bogo_product = wc_get_product($bogo_x_single); @@ 3.3.0
                            $bogo_product   = call_user_func_array ( 
                                array ( new AWDP_Discount(), 'wdpCheckVariation' ), 
                                array ( $bogo_x_single ) 
                            );

                            // Check Varaible Product
                            if ( $bogo_product ) { 
                                
                                $bogo_variations = $bogo_product;

                                foreach ( $bogo_variations as $bogo_variation ) {

                                    if ( array_key_exists ( $bogo_variation, $awdp_cart_quanity ) ) {

                                        // Reset offer count for variable products
                                        $ofr_count      = $bogo_x_all ? ( array_key_exists($bogo_variation, $awdp_cart_quanity) ? ( ( $awdp_cart_quanity[$bogo_variation] % 2 ) == 0 ? ( $awdp_cart_quanity[$bogo_variation] / 2 ) : floor ( $awdp_cart_quanity[$bogo_variation] / 2 ) ) : 1 ) : 1; 

                                        $bogo_var_prod_slug = get_post_field( 'post_name', $bogo_variation );
                                        $discount           = 0;
                                        // $prod_price         = $price;
                                        $prod_price         = $awdp_product_price[$bogo_variation]; 

                                        // Get product price
                                        $bogo_product_price = $prod_price ? wc_add_number_precision($prod_price) : 0;  

                                        // Calculate Discount
                                        /*
                                        * ver 4.0.5
                                        * Fixed Type Added
                                        */
                                        if ( $bogo_product_price > 0 && array_key_exists ( $bogo_variation, $awdp_cart_quanity ) && $awdp_cart_quanity[$bogo_variation] > 1 ) {

                                            // Skip Flag
                                            $skipFlag = true;

                                            if ( 'off_percentage' == $bogo_type ) { 

                                                $bogo_discount          = $bogo_product_price * ( $bogo_value / 100 ) * $ofr_count; 
                                                $bogo_discount_single   = $bogo_product_price * ( $bogo_value / 100 );
                                                $updated_price          = ( $bogo_product_price - $bogo_discount_single ) * $ofr_count; 
                                                $updated_price_single   = $bogo_product_price - $bogo_discount_single; 

                                                /*
                                                * @ ver 5.0.7
                                                * Added id check - fix cart amount mismatch issue
                                                * Bogo same product
                                                */
                                                if ( in_array ( $bogo_variation, $awdp_products ) && ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1 && ( $awdp_prod_id == $bogo_variation ) ) { 
                                                    // Buy X get X Repeat option added in 3.1.1
                                                    $wdpBogoIDsinCart[] = array ( 
                                                        'product_id'                => $bogo_variation, 
                                                        'discount_type'             => $bogo_type, 
                                                        'discount_percentage'       => $bogo_value, 
                                                        'discounted_price'          => $updated_price, 
                                                        'discount'                  => $bogo_discount, 
                                                        'single_discount'           => $bogo_discount_single, 
                                                        'discounted_price_single'   => $updated_price_single, 
                                                        'quantity'                  => $awdp_qn_get, 
                                                        'price'                     => $bogo_product_price, 
                                                        'offer_items'               => $ofr_count,
                                                        'cartKey'                   => $actualCartKey,
                                                        'actual_qn'                 => $awdp_cart_quanity[$bogo_variation]
                                                    );
                                                    // $discountApplied[]  = $actualCartKey;

                                                }

                                            } else if ( 'fixed' == $bogo_type ) { 

                                                /*
                                                * @ver 5.0.3
                                                * Fix - error when value greater than product amount
                                                */

                                                $bogo_value_pre         = wc_add_number_precision ($bogo_value);
                                                $bogo_discount          = $bogo_value_pre * $ofr_count; 
                                                $bogo_discount_single   = $bogo_value_pre;

                                                if ( $bogo_product_price >= $bogo_discount_single ) {
                                                    $updated_price          = ( $bogo_product_price - $bogo_discount_single ) * $ofr_count;
                                                    $updated_price_single   = $bogo_product_price - $bogo_discount_single;
                                                } else {
                                                    $updated_price          = 0;
                                                    $updated_price_single   = 0;
                                                    $bogo_discount_single   = $bogo_product_price;
                                                    $bogo_discount          = $bogo_product_price * $ofr_count; 
                                                }

                                                /*
                                                * @ ver 5.0.7
                                                * Added id check - fix cart amount mismatch issue
                                                */
                                                if ( in_array ( $bogo_variation, $awdp_products ) && ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1 && ( $awdp_prod_id == $bogo_variation )  ) {
                                                    // Buy X get X Repeat option added in 3.1.1
                                                    $wdpBogoIDsinCart[] = array ( 
                                                        'product_id'                => $bogo_variation, 
                                                        'discount_type'             => $bogo_type, 
                                                        'discount_percentage'       => $bogo_value_pre, 
                                                        'discounted_price'          => $updated_price, 
                                                        'discount'                  => $bogo_discount, 
                                                        'single_discount'           => $bogo_discount_single, 
                                                        'discounted_price_single'   => $updated_price_single, 
                                                        'quantity'                  => $awdp_qn_get, 
                                                        'price'                     => $bogo_product_price, 
                                                        'offer_items'               => $ofr_count,
                                                        'cartKey'                   => $actualCartKey,
                                                        'actual_qn'                 => $awdp_cart_quanity[$bogo_variation]
                                                    );
                                                    // $discountApplied[]  = $actualCartKey;

                                                }

                                            } else if ( 'free' == $bogo_type ) { 

                                                $updated_price          = 0;
                                                $bogo_discount          = $bogo_product_price * $ofr_count;
                                                $bogo_discount_single   = $bogo_product_price; 

                                                /*
                                                * @ ver 5.0.7
                                                * Added id check - fix cart amount mismatch issue
                                                */
                                                if ( in_array ( $bogo_variation, $awdp_products ) && ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1 && ( $awdp_prod_id == $bogo_variation )  ) { 
                                                    // Buy X get X Repeat option added in 3.1.1
                                                    $wdpBogoIDsinCart[] = array ( 
                                                        'product_id'                => $bogo_variation, 
                                                        'discount_type'             => $bogo_type, 
                                                        'discount_percentage'       => $bogo_value, 
                                                        'discounted_price'          => 0, 
                                                        'discount'                  => $bogo_discount, 
                                                        'single_discount'           => $bogo_discount_single, 
                                                        'discounted_price_single'   => 0, 
                                                        'quantity'                  => $awdp_qn_get, 
                                                        'price'                     => $bogo_product_price, 
                                                        'offer_items'               => $ofr_count,
                                                        'cartKey'                   => $actualCartKey,
                                                        'actual_qn'                 => $awdp_cart_quanity[$bogo_variation]
                                                    );
                                                    // $discountApplied[]  = $actualCartKey;

                                                }

                                            }
                                            $free_count++;
                                            
                                        }

                                    }
                                    
                                }

                            } // End Check

                            $bogo_prod_slug     = get_post_field( 'post_name', $bogo_x_single );
                            $discount           = 0;
                            // $prod_price         = $price;
                            // $prod_price         = array_key_exists ( $bogo_x_single, $awdp_product_price ) ? $awdp_product_price[$bogo_x_single] : 0;
                            $prod_price         = array_key_exists ( $actualCartKey, $awdp_cart_price ) ? $awdp_cart_price[$actualCartKey] : 0;

                            // Get product price
                            $bogo_product_price = $prod_price ? wc_add_number_precision($prod_price) : 0; 
                            // $bogo_product_price         = $awdp_product_price[$bogo_x_single];
                            
                            if ( $bogo_product_price > 0 ) {

                                // $$bogo_x_count
                                $bogo_x_compare = $bogo_x_count != '' ? ( ( $bogo_x_count >= $free_count ) ? intval($bogo_x_repeat) : 0 ) : intval($bogo_x_repeat);
                                $bogo_x_index   = array_search($bogo_x_single, $bogo_x_cart); // used to skip second item from list

                                // Offer Flag
                                $offer_flag     = ( $bogo_x_compare === 0 && $bogo_x_index >= 1 ) ? false : true; 

                                // Calculate Discount
                                /*
                                * ver 4.0.5
                                * Fixed Type Added
                                */
                                if ( array_key_exists ( $bogo_x_single, $awdp_cart_quanity ) && ( $awdp_cart_quanity[$bogo_x_single] > 1 ) && ( $bogo_x_compare === 1 || ( $bogo_x_compare === 0 && $bogo_x_index == 0 ) ) ) { 

                                    // Skip Flag
                                    $skipFlag = true;

                                    if ( 'off_percentage' == $bogo_type ) {

                                        $bogo_discount          = $bogo_product_price * ( $bogo_value / 100 ) * $ofr_count; 
                                        $bogo_discount_single   = $bogo_product_price * ( $bogo_value / 100 );
                                        $updated_price          = ( $bogo_product_price - $bogo_discount_single ) * $ofr_count;
                                        $updated_price_single   = $bogo_product_price - $bogo_discount_single; 

                                        /*
                                        * @ ver 5.0.7
                                        * Added id check - fix cart amount mismatch issue
                                        * Bogo same product
                                        */
                                        if ( in_array ( $bogo_x_single, $awdp_products ) && ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1  && ( $awdp_prod_id == $bogo_x_single ) ) { 
                                            // Buy X get X Repeat option added in 3.1.1
                                            $wdpBogoIDsinCart[] = array ( 
                                                'product_id'                => $bogo_x_single, 
                                                'discount_type'             => $bogo_type, 
                                                'discount_percentage'       => $bogo_value, 
                                                'discounted_price'          => $updated_price, 
                                                'discount'                  => $bogo_discount, 
                                                'single_discount'           => $bogo_discount_single, 
                                                'discounted_price_single'   => $updated_price_single, 
                                                'quantity'                  => $awdp_qn_get, 
                                                'price'                     => $bogo_product_price, 
                                                'offer_items'               => $ofr_count,
                                                'cartKey'                   => $actualCartKey,
                                                'actual_qn'                 => $awdp_cart_quanity[$bogo_x_single] 
                                            );
                                            // $discountApplied[]  = $actualCartKey; 

                                        }

                                    } else if ( 'fixed' == $bogo_type ) {

                                        /*
                                        * @ver 5.0.3
                                        * Fix - error when value greater than product amount
                                        */

                                        $bogo_value_pre         = wc_add_number_precision ($bogo_value);
                                        $bogo_discount          = $bogo_value_pre * $ofr_count; 
                                        $bogo_discount_single   = $bogo_value_pre;

                                        if ( $bogo_product_price >= $bogo_discount_single ) {
                                            $updated_price          = ( $bogo_product_price - $bogo_discount_single ) * $ofr_count;
                                            $updated_price_single   = $bogo_product_price - $bogo_discount_single;
                                        } else {
                                            $updated_price          = 0;
                                            $updated_price_single   = 0;
                                            $bogo_discount_single   = $bogo_product_price;
                                            $bogo_discount          = $bogo_product_price * $ofr_count; 
                                        }

                                        /*
                                        * @ ver 5.0.7
                                        * Added id check - fix cart amount mismatch issue
                                        */
                                        if ( in_array ( $bogo_x_single, $awdp_products ) && ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1 && ( $awdp_prod_id == $bogo_x_single ) ) {
                                            // Buy X get X Repeat option added in 3.1.1
                                            $wdpBogoIDsinCart[] = array ( 
                                                'product_id'                => $bogo_x_single, 
                                                'discount_type'             => $bogo_type, 
                                                'discount_percentage'       => $bogo_value_pre, 
                                                'discounted_price'          => $updated_price, 
                                                'discount'                  => $bogo_discount, 
                                                'single_discount'           => $bogo_discount_single, 
                                                'discounted_price_single'   => $updated_price_single, 
                                                'quantity'                  => $awdp_qn_get, 
                                                'price'                     => $bogo_product_price, 
                                                'offer_items'               => $ofr_count,
                                                'cartKey'                   => $actualCartKey,
                                                'actual_qn'                 => $awdp_cart_quanity[$bogo_x_single] 
                                            );
                                            // $discountApplied[]  = $actualCartKey;

                                        }

                                    } else if ( 'free' == $bogo_type ) { 

                                        $updated_price          = 0;
                                        $bogo_discount          = $bogo_product_price * $ofr_count;
                                        $bogo_discount_single   = $bogo_product_price;

                                        /*
                                        * @ ver 5.0.7
                                        * Added id check - fix cart amount mismatch issue
                                        */
                                        if ( in_array ( $bogo_x_single, $awdp_products ) && ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1 && ( $awdp_prod_id == $bogo_x_single ) ) {
                                            // Buy X get X Repeat option added in 3.1.1
                                            $wdpBogoIDsinCart[] = array ( 
                                                'product_id'                => $bogo_x_single, 
                                                'discount_type'             => $bogo_type, 
                                                'discount_percentage'       => $bogo_value, 
                                                'discounted_price'          => 0, 
                                                'discount'                  => $bogo_discount, 
                                                'single_discount'           => $bogo_discount_single, 
                                                'discounted_price_single'   => 0, 
                                                'quantity'                  => $awdp_qn_get, 
                                                'price'                     => $bogo_product_price, 
                                                'offer_items'               => $ofr_count,
                                                'cartKey'                   => $actualCartKey,
                                                'actual_qn'                 => $awdp_cart_quanity[$bogo_x_single] 
                                            );
                                            // $discountApplied[]  = $actualCartKey;

                                        }

                                    }
                                    $free_count++;

                                }

                            }

                        }

                        $wdpBogoIDsinCart   = array_values($wdpBogoIDsinCart);
                        $wdpBogoIDs         = array_unique(array_values($wdpBogoIDs));
                        $products_array     = $wdpBogoIDs; // New Products Array

                    }

                    // End Buy X get X @@ 3.3.0 Fix

                } else if ( $bogo_rule_type == 'bogo_rule_nth' ) { 

                    /* 
                    * BOGO Rule: Buy X get discount on nth product
                    * Discount types: Fixed, Percentage, Free
                    * ver 5.0.7 -> ( $awdp_prod_id == $bogo_variation ) check added - fix - cart amount mismacth when multiple rules activated
                    */

                    $bogo_n_list = [];
 
                    if ( $bogo_n_prods == 'null' || $bogo_n_prods == '' ) { // Any Product
                        $bogo_n_list    = $all_products; // Full products
                        $all_flag       = true;
                    } else if ( strpos ( $bogo_n_prods, $list_key ) === false ) { // get variations
                        $bogo_n_list[]  = $bogo_n_prods;
                    } else { // Check if product list 
                        $list_id        = intval ( str_replace ( $list_key, '', $bogo_n_prods ) );
                        $list_prods     = call_user_func_array ( 
                            array ( new AWDP_Discount(), 'wdpProductList' ), 
                            array ( $list_id ) 
                        );
                        $bogo_n_list    = array_unique ( array_merge ( $list_prods, $bogo_n_list ) );
                    } 

                    if ( array_intersect ( $bogo_n_list, $awdp_products ) ) { // in cart

                        $bogo_n_cart    = array_values ( array_intersect( $awdp_products, $bogo_n_list ) );
                        $wdpBogoIDs     = array_merge ( $wdpBogoIDs, $bogo_n_list ); 

                        foreach ( $bogo_n_list as $bogo_n_single ) { 

                            // $bogo_product = wc_get_product($bogo_n_single);
                            $bogo_product   = call_user_func_array ( 
                                array ( new AWDP_Discount(), 'wdpCheckVariation' ), 
                                array ( $bogo_n_single ) 
                            );
                            $bogo_prod_slug = get_post_field( 'post_name', $bogo_n_single );

                            if ( $bogo_product ) { // Check Variable Product

                                $bogo_variations = $bogo_product;

                                foreach ( $bogo_variations as $bogo_variation ) {

                                    if ( array_key_exists ( $bogo_variation, $awdp_cart_quanity ) ) {

                                        $bogo_var_prod_slug = get_post_field( 'post_name', $bogo_variation );
                                        $discount           = 0;
                                        // $prod_price         = $price;
                                        $prod_price         = $awdp_product_price[$bogo_variation]; 

                                        // Get product price
                                        $bogo_product_price = $prod_price ? wc_add_number_precision($prod_price) : 0; 

                                        if ( $bogo_product_price > 0 ) {

                                            $bogo_n_compare = intval($bogo_n_repeat);
                                            $bogo_n_loop    = intval($bogo_n_loop);
                                            $bogo_n_index   = array_search($bogo_variation, $bogo_n_cart); // used to skip second item from list

                                            $ofr_count      = 1;
                                            if ( $bogo_n_loop == 1 && array_key_exists( $bogo_variation, $awdp_cart_quanity ) && $awdp_cart_quanity[$bogo_variation] > $bogo_n_count ) {
                                                $ofr_count  = floor ( $awdp_cart_quanity[$bogo_variation] / $bogo_n_count );
                                            }

                                            // Offer Flag
                                            $offer_flag     = ( $bogo_n_compare === 0 && $bogo_n_index === 0 ) ? false : true;

                                            // Calculate Discount
                                            /*
                                            * ver 4.0.5
                                            * Fixed Type Added
                                            * Bogo nth
                                            */
                                            if ( array_key_exists ( $bogo_variation, $awdp_cart_quanity ) && ( $awdp_cart_quanity[$bogo_variation] >= $bogo_n_count ) && ( $bogo_n_compare === 1 || ( $bogo_n_compare === 0 && $bogo_n_index == 0 ) ) ) {

                                                // Skip Flag
                                                $skipFlag = true;

                                                if ( 'off_percentage' == $bogo_type ) { 

                                                    $bogo_discount          = $bogo_product_price * ( $bogo_value / 100 ) * $ofr_count; 
                                                    $bogo_discount_single   = $bogo_product_price * ( $bogo_value / 100 ); 
                                                    $updated_price          = $bogo_product_price - $bogo_discount; 
                                                    $updated_price_single   = $bogo_product_price - $bogo_discount_single;

                                                    /*
                                                    * @ ver 5.0.7
                                                    * Added id check - fix cart amount mismatch issue
                                                    */
                                                    if ( in_array ( $bogo_variation, $awdp_products ) && ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1 && ( $awdp_prod_id == $bogo_variation )  ) {

                                                        $wdpBogoIDsinCart[] = array ( 
                                                            'product_id'                => $bogo_variation, 
                                                            'discount_type'             => $bogo_type, 
                                                            'discount_percentage'       => $bogo_value, 
                                                            'discounted_price'          => $updated_price, 
                                                            'discount'                  => $bogo_discount, 
                                                            'single_discount'           => $bogo_discount_single, 
                                                            'discounted_price_single'   => $updated_price_single, 
                                                            'quantity'                  => $awdp_qn_get, 
                                                            'price'                     => $bogo_product_price, 
                                                            'offer_items'               => $ofr_count,
                                                            'cartKey'                   => $actualCartKey,
                                                            'actual_qn'                 => $awdp_cart_quanity[$bogo_variation] 
                                                        );
                                                        $discountApplied[]  = $actualCartKey;

                                                    }

                                                } else if ( 'fixed' == $bogo_type ) { 

                                                    /*
                                                    * @ver 5.0.3
                                                    * Fix - error when value greater than product amount
                                                    */

                                                    $bogo_value_pre         = wc_add_number_precision ($bogo_value);
                                                    $bogo_discount          = $bogo_value_pre * $ofr_count; 
                                                    $bogo_discount_single   = $bogo_value_pre; 

                                                    if ( $bogo_product_price >= $bogo_discount_single ) {
                                                        $updated_price          = $bogo_product_price - $bogo_discount; 
                                                        $updated_price_single   = $bogo_product_price - $bogo_discount_single;
                                                    } else {
                                                        $updated_price          = 0; 
                                                        $updated_price_single   = 0;
                                                        $bogo_discount_single   = $bogo_product_price;
                                                        $bogo_discount          = $bogo_product_price * $ofr_count; 
                                                    }

                                                    /*
                                                    * @ ver 5.0.7
                                                    * Added id check - fix cart amount mismatch issue
                                                    */
                                                    if ( in_array ( $bogo_variation, $awdp_products ) && ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1 && ( $awdp_prod_id == $bogo_variation ) ) {

                                                        $wdpBogoIDsinCart[] = array ( 
                                                            'product_id'                => $bogo_variation, 
                                                            'discount_type'             => $bogo_type, 
                                                            'discount_percentage'       => $bogo_value_pre, 
                                                            'discounted_price'          => $updated_price, 
                                                            'discount'                  => $bogo_discount, 
                                                            'single_discount'           => $bogo_discount_single, 
                                                            'discounted_price_single'   => $updated_price_single, 
                                                            'quantity'                  => $awdp_qn_get, 
                                                            'price'                     => $bogo_product_price, 
                                                            'offer_items'               => $ofr_count,
                                                            'cartKey'                   => $actualCartKey,
                                                            'actual_qn'                 => $awdp_cart_quanity[$bogo_variation] 
                                                        );
                                                        $discountApplied[]  = $actualCartKey;

                                                    }

                                                } else if ( 'free' == $bogo_type ) { 

                                                    $updated_price = 0;
                                                    $bogo_discount = $bogo_product_price * $ofr_count;

                                                    /*
                                                    * @ ver 5.0.7
                                                    * Added id check - fix cart amount mismatch issue
                                                    */
                                                    if ( in_array ( $bogo_variation, $awdp_products ) && ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1  && ( $awdp_prod_id == $bogo_variation ) ) {

                                                        $wdpBogoIDsinCart[] = array ( 
                                                            'product_id'                => $bogo_variation, 
                                                            'discount_type'             => $bogo_type, 
                                                            'discount_percentage'       => $bogo_value, 
                                                            'discounted_price'          => 0, 
                                                            'discount'                  => $bogo_discount, 
                                                            'single_discount'           => $bogo_product_price, 
                                                            'discounted_price_single'   => 0, 
                                                            'quantity'                  => $awdp_qn_get, 
                                                            'price'                     => $bogo_product_price, 
                                                            'offer_items'               => $ofr_count,
                                                            'cartKey'                   => $actualCartKey,
                                                            'actual_qn'                 => $awdp_cart_quanity[$bogo_variation] 
                                                        );
                                                        $discountApplied[]  = $actualCartKey;

                                                    }

                                                }
                                            }
                                            $free_count++;

                                        }
                                    }

                                }

                            } 

                            $bogo_prod_slug     = get_post_field( 'post_name', $bogo_n_single );
                            $discount           = 0;
                            // $prod_price         = $price;
                            $prod_price         = array_key_exists ( $bogo_n_single, $awdp_product_price ) ? $awdp_product_price[$bogo_n_single] : 0;

                            // Get product price
                            $bogo_product_price = $prod_price ? wc_add_number_precision($prod_price) : 0; 
                            
                            if ( $bogo_product_price > 0 ) {

                                $bogo_n_compare = intval($bogo_n_repeat);
                                $bogo_n_loop    = intval($bogo_n_loop);
                                $bogo_n_index   = array_search($bogo_n_single, $bogo_n_cart); // used to skip second item from list

                                $ofr_count = 1;
                                if ( $bogo_n_loop == 1 && array_key_exists( $bogo_n_single, $awdp_cart_quanity ) && $awdp_cart_quanity[$bogo_n_single] > $bogo_n_count ) {
                                    $ofr_count = floor ( $awdp_cart_quanity[$bogo_n_single] / $bogo_n_count );
                                }

                                // Offer Flag
                                $offer_flag = ( $bogo_n_compare === 0 && $bogo_n_index === 0 ) ? false : true;

                                // Calculate Discount
                                /*
                                * ver 4.0.5
                                * Fixed Type Added
                                * Bogo nth
                                */
                                if ( array_key_exists ( $bogo_n_single, $awdp_cart_quanity ) && ( $awdp_cart_quanity[$bogo_n_single] >= $bogo_n_count ) && ( $bogo_n_compare === 1 || ( $bogo_n_compare === 0 && $bogo_n_index == 0 ) ) ) {

                                    // Skip Flag
                                    $skipFlag = true;

                                    if ( 'off_percentage' == $bogo_type ) { 

                                        $bogo_discount          = $bogo_product_price * ( $bogo_value / 100 ) * $ofr_count; 
                                        $bogo_discount_single   = $bogo_product_price * ( $bogo_value / 100 ); 
                                        $updated_price          = $bogo_product_price - $bogo_discount; 
                                        $updated_price_single   = $bogo_product_price - $bogo_discount_single;

                                        /*
                                        * @ ver 5.0.7
                                        * Added id check - fix cart amount mismatch issue
                                        */
                                        if ( in_array ( $bogo_n_single, $awdp_products ) && ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1 && ( $awdp_prod_id == $bogo_n_single ) ) {

                                            $wdpBogoIDsinCart[] = array ( 
                                                'product_id'                => $bogo_n_single, 
                                                'discount_type'             => $bogo_type, 
                                                'discount_percentage'       => $bogo_value, 
                                                'discounted_price'          => $updated_price, 
                                                'discount'                  => $bogo_discount, 
                                                'single_discount'           => $bogo_discount_single, 
                                                'discounted_price_single'   => $updated_price_single, 
                                                'quantity'                  => $awdp_qn_get, 
                                                'price'                     => $bogo_product_price, 
                                                'offer_items'               => $ofr_count,
                                                'cartKey'                   => $actualCartKey,
                                                'actual_qn'                 => $awdp_cart_quanity[$bogo_n_single] 
                                            );
                                            $discountApplied[]  = $actualCartKey;

                                        }

                                    } else if ( 'fixed' == $bogo_type ) { 

                                        /*
                                        * @ver 5.0.3
                                        * Fix - error when value greater than product amount
                                        */

                                        $bogo_value_pre         = wc_add_number_precision ($bogo_value);
                                        $bogo_discount          = $bogo_value_pre * $ofr_count; 
                                        $bogo_discount_single   = $bogo_value_pre; 

                                        if ( $bogo_product_price >= $bogo_discount_single ) {
                                            $updated_price          = $bogo_product_price - $bogo_discount; 
                                            $updated_price_single   = $bogo_product_price - $bogo_discount_single;
                                        } else {
                                            $updated_price          = 0; 
                                            $updated_price_single   = 0;
                                            $bogo_discount_single   = $bogo_product_price;
                                            $bogo_discount          = $bogo_product_price * $ofr_count; 
                                        }

                                        /*
                                        * @ ver 5.0.7
                                        * Added id check - fix cart amount mismatch issue
                                        */
                                        if ( in_array ( $bogo_n_single, $awdp_products ) && ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1 && ( $awdp_prod_id == $bogo_n_single ) ) {

                                            $wdpBogoIDsinCart[] = array ( 
                                                'product_id'                => $bogo_n_single, 
                                                'discount_type'             => $bogo_type, 
                                                'discount_percentage'       => $bogo_value_pre, 
                                                'discounted_price'          => $updated_price, 
                                                'discount'                  => $bogo_discount, 
                                                'single_discount'           => $bogo_discount_single, 
                                                'discounted_price_single'   => $updated_price_single, 
                                                'quantity'                  => $awdp_qn_get, 
                                                'price'                     => $bogo_product_price, 
                                                'offer_items'               => $ofr_count,
                                                'cartKey'                   => $actualCartKey,
                                                'actual_qn'                 => $awdp_cart_quanity[$bogo_n_single] 
                                            );
                                            $discountApplied[]  = $actualCartKey;

                                        }

                                    } else if ( 'free' == $bogo_type ) { 

                                        $updated_price = 0;
                                        $bogo_discount = $bogo_product_price * $ofr_count;

                                        /*
                                        * @ ver 5.0.7
                                        * Added id check - fix cart amount mismatch issue
                                        */
                                        if ( in_array ( $bogo_n_single, $awdp_products ) && ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1 && ( $awdp_prod_id == $bogo_n_single ) ) {

                                            $wdpBogoIDsinCart[] = array ( 
                                                'product_id'                => $bogo_n_single, 
                                                'discount_type'             => $bogo_type, 
                                                'discount_percentage'       => $bogo_value, 
                                                'discounted_price'          => 0, 
                                                'discount'                  => $bogo_discount, 
                                                'single_discount'           => $bogo_product_price, 
                                                'discounted_price_single'   => 0, 
                                                'quantity'                  => $awdp_qn_get, 
                                                'price'                     => $bogo_product_price, 
                                                'offer_items'               => $ofr_count,
                                                'cartKey'                   => $actualCartKey,
                                                'actual_qn'                 => $awdp_cart_quanity[$bogo_n_single] 
                                            );
                                            $discountApplied[]  = $actualCartKey;

                                        }

                                    }
                                }
                                $free_count++;

                            }

                        }

                        $wdpBogoIDsinCart   = array_values($wdpBogoIDsinCart);
                        $wdpBogoIDs         = array_unique(array_values($wdpBogoIDs));
                        $products_array     = $wdpBogoIDs; // New Products Array
                    
                    }
                    // End Buy X get discount on nth

                } else if ( $bogo_rule_type == 'bogo_rule_payn' ) { 
                    
                    /* 
                    * BOGO Rule: Buy X get discount for n items
                    * Discount gets applied to n items / quantities
                    * Discount types: Fixed, Percentage, Free
                    * ver 5.0.7 -> ( $awdp_prod_id == $bogo_payn_single ) check added - fix - cart amount mismacth when multiple rules activated
                    * ver 5.0.8 -> $ofr_count check added
                    */

                    $bogo_payn_list = [];
                    
                    // Sort $bogopayranges with min_quantity
                    usort ( $bogopayranges, function ( $item1, $item2 ) { 
                        return $item2['min_qnty'] <=> $item1['min_qnty'];
                    });
                    // end

                    if ( $bogo_payn_prods == 'null' || $bogo_payn_prods == '' ) { // Any Product
                        $bogo_payn_list = $all_products; // Full products
                        $all_flag = true;
                    } else if ( strpos ( $bogo_payn_prods, $list_key ) === false ) {
                        // get variations
                        $bogo_payn_list[]   = $bogo_payn_prods;
                    } else { // Check if product list
                        $list_id            = intval ( str_replace ( $list_key, '', $bogo_payn_prods ) );
                        $list_prods         = call_user_func_array ( 
                            array ( new AWDP_Discount(), 'wdpProductList' ), 
                            array ( $list_id ) 
                        );
                        $bogo_payn_list     = array_unique ( array_merge ( $list_prods, $bogo_payn_list ) );
                    }

                    if ( array_intersect ( $bogo_payn_list, $awdp_products ) ) { // in cart

                        $bogo_payn_cart = array_values ( array_intersect( $awdp_products, $bogo_payn_list ) );
                        $wdpBogoIDs     = array_merge ( $wdpBogoIDs, $bogo_payn_list );

                        foreach ( $bogo_payn_list as $bogo_payn_single ) {

                            if ( array_key_exists ( $bogo_payn_single, $awdp_cart_quanity ) ) {

                                $bogo_prod_slug     = get_post_field( 'post_name', $bogo_payn_single );
                                // $bogo_product = wc_get_product($bogo_payn_single);
                                $discount           = 0;
                                // $prod_price     = $price;
                                $prod_price         = $awdp_product_price[$bogo_payn_single]; 

                                // Get product price
                                $bogo_product_price = $prod_price ? wc_add_number_precision($prod_price) : 0; 

                                foreach ( $bogopayranges as $bogopayrange ) {

                                    if ( $awdp_cart_quanity[$bogo_payn_single] >= $bogopayrange["min_qnty"] ) { 

                                        $bogo_value     = array_key_exists ( "payn_value", $bogopayrange ) ? floatval($bogopayrange["payn_value"]) : 0; 
                                        $bogo_type      = array_key_exists ( "payn_type", $bogopayrange ) ? $bogopayrange["payn_type"] : ''; 
                                        $ofr_count      = array_key_exists ( "item_cnt", $bogopayrange ) ? floatval($bogopayrange["item_cnt"]) : 0;

                                        // Skip Flag
                                        $skipFlag = true;

                                        // Cross checking Offer count with cart quantity
                                        if ( array_key_exists ( $bogo_payn_single, $awdp_cart_quanity ) ) {
                                            $ofr_count = ( $awdp_cart_quanity[$bogo_payn_single] > $ofr_count ) ? ( ( ( $awdp_cart_quanity[$bogo_payn_single] - $ofr_count ) > $ofr_count ) ? $ofr_count : $awdp_cart_quanity[$bogo_payn_single] - $ofr_count ) : 0;
                                        }

                                        // Calculate Discount
                                        /*
                                        * ver 4.0.5
                                        * Fixed Type Added
                                        * Bogo n items
                                        */
                                        if ( 'off_percentage' == $bogo_type && $bogo_value > 0 ) { 

                                            $bogo_discount          = $bogo_product_price * ( $bogo_value / 100 ) * $ofr_count; 
                                            $bogo_discount_single   = $bogo_product_price * ( $bogo_value / 100 ); 
                                            $updated_price          = $bogo_product_price - $bogo_discount; 
                                            $updated_price_single   = $bogo_product_price - $bogo_discount_single;

                                            /*
                                            * @ ver 5.0.7
                                            * Added id check - fix cart amount mismatch issue
                                            */
                                            if ( ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1 && ( $awdp_prod_id == $bogo_payn_single ) ) {

                                                $wdpBogoIDsinCart[] = array ( 
                                                    'product_id'                => $bogo_payn_single, 
                                                    'discount_type'             => $bogo_type, 
                                                    'discount_percentage'       => $bogo_value, 
                                                    'discounted_price'          => $updated_price, 
                                                    'discount'                  => $bogo_discount, 
                                                    'single_discount'           => $bogo_discount_single, 
                                                    'discounted_price_single'   => $updated_price_single, 
                                                    'quantity'                  => $awdp_qn_get, 
                                                    'price'                     => $bogo_product_price, 
                                                    'offer_items'               => $ofr_count,
                                                    'cartKey'                   => $actualCartKey,
                                                    'actual_qn'                 => $awdp_cart_quanity[$bogo_payn_single] 
                                                );
                                                $discountApplied[]  = $actualCartKey;

                                            }

                                        } else if ( 'fixed' == $bogo_type && $bogo_value > 0 ) { 

                                            /*
                                            * @ver 5.0.3
                                            * Fix - error when value greater than product amount
                                            */

                                            $bogo_value_pre         = wc_add_number_precision ($bogo_value);
                                            $bogo_discount          = $bogo_value_pre * $ofr_count; 
                                            $bogo_discount_single   = $bogo_value_pre; 

                                            if ( $bogo_product_price >= $bogo_discount_single ) {
                                                $updated_price          = $bogo_product_price - $bogo_discount; 
                                                $updated_price_single   = $bogo_product_price - $bogo_discount_single;
                                            } else {
                                                $updated_price          = 0; 
                                                $updated_price_single   = 0;  
                                                $bogo_discount_single   = $bogo_product_price;
                                                $bogo_discount          = $bogo_product_price * $ofr_count; 
                                            }

                                            /*
                                            * @ ver 5.0.7
                                            * Added id check - fix cart amount mismatch issue
                                            */
                                            if ( ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1 && ( $awdp_prod_id == $bogo_payn_single ) ) {

                                                $wdpBogoIDsinCart[] = array ( 
                                                    'product_id'                => $bogo_payn_single, 
                                                    'discount_type'             => $bogo_type, 
                                                    'discount_percentage'       => $bogo_value_pre, 
                                                    'discounted_price'          => $updated_price, 
                                                    'discount'                  => $bogo_discount, 
                                                    'single_discount'           => $bogo_discount_single, 
                                                    'discounted_price_single'   => $updated_price_single, 
                                                    'quantity'                  => $awdp_qn_get, 
                                                    'price'                     => $bogo_product_price, 
                                                    'offer_items'               => $ofr_count,
                                                    'cartKey'                   => $actualCartKey,
                                                    'actual_qn'                 => $awdp_cart_quanity[$bogo_payn_single]  
                                                );
                                                $discountApplied[]  = $actualCartKey;

                                            }

                                        } else if ( 'free' == $bogo_type ) { 

                                            $updated_price = 0;
                                            $bogo_discount          = $bogo_product_price * $ofr_count;
                                            $updated_price          = $bogo_product_price - $bogo_discount; 
                                            $bogo_discount_single   = $bogo_product_price; 
                                            $updated_price_single   = 0;

                                            /*
                                            * @ ver 5.0.7
                                            * Added id check - fix cart amount mismatch issue
                                            */
                                            if ( ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1 && ( $awdp_prod_id == $bogo_payn_single ) ) {

                                                $wdpBogoIDsinCart[] = array ( 
                                                    'product_id'                => $bogo_payn_single, 
                                                    'discount_type'             => $bogo_type, 
                                                    'discount_percentage'       => $bogo_value, 
                                                    'discounted_price'          => 0, 
                                                    'discount'                  => $bogo_discount, 
                                                    'single_discount'           => $bogo_product_price, 
                                                    'discounted_price_single'   => 0, 
                                                    'quantity'                  => $awdp_qn_get, 
                                                    'price'                     => $bogo_product_price, 
                                                    'offer_items'               => $ofr_count,
                                                    'cartKey'                   => $actualCartKey,
                                                    'actual_qn'                 => $awdp_cart_quanity[$bogo_payn_single]  
                                                );
                                                $discountApplied[]  = $actualCartKey;

                                            }

                                        }
                                        // continue;

                                    }

                                }

                            }

                        }

                        $wdpBogoIDsinCart   = array_values($wdpBogoIDsinCart);
                        $wdpBogoIDs         = array_unique(array_values($wdpBogoIDs));
                        $products_array     = $wdpBogoIDs; // New Products Array

                    }
                    
                    // End Buy X pay for n items

                } else if ( $bogo_rule_type == 'bogo_rule_cheapest' ) { 
                    
                    /* 
                    * BOGO Rule: Buy X get discount applied on the cheapest product in the cart
                    * Discount types: Fixed, Percentage, Free
                    * ver 3.2.3 -> Same variation price and variation fix, Modified wdpProductList to get variation ids
                    * ver 5.0.7 -> ( $awdp_prod_id == $cheap_prod_in_cart ) check added - fix - cart amount mismacth when multiple rules activated
                    */
                    
                    // Discount on cheapest product in the cart
                    $cheap_list         = []; 
                    $cheapCount         = 0;
                    $ofr_items_count    = 0;

                    /*
                    * Added array_check for cheapest products in cart rule
                    */
                    if ( $bogo_cheap_prods == '' || empty($bogo_cheap_prods) || ( !empty($bogo_cheap_prods) && ( $bogo_cheap_prods[0] == 'null' || $bogo_cheap_prods[0] == '' ) ) ) { // Any Product
                        $cheap_list     = $all_products; // Full products
                        $all_flag       = true;
                    } else if ( is_array($bogo_cheap_prods) ) {
                        foreach ( $bogo_cheap_prods as $bogo_cheap_prod ) {
                            if ( strpos ( $bogo_cheap_prod, $list_key ) === false ) {
                                // get variations
                                $cheap_list[]       = $bogo_cheap_prod;
                            } else { // Check if product list
                                $list_id            = intval ( str_replace ( $list_key, '', $bogo_cheap_prod ) );
                                $list_prods         = call_user_func_array ( 
                                    array ( new AWDP_Discount(), 'wdpProductList' ), 
                                    array ( $list_id ) 
                                );
                                $cheap_list         = array_unique ( array_merge ( $list_prods, $cheap_list ) );
                            }
                        }
                    } else {  
                        if ( strpos ( $bogo_cheap_prods, $list_key ) === false ) {
                            // get variations
                            $cheap_list[]       = $bogo_cheap_prods;
                        } else { // Check if product list
                            $list_id            = intval ( str_replace ( $list_key, '', $bogo_cheap_prods ) ); 
                            $list_prods         = call_user_func_array ( 
                                array ( new AWDP_Discount(), 'wdpProductList' ), 
                                array ( $list_id ) 
                            );
                            $cheap_list         = array_unique ( array_merge ( $list_prods, $cheap_list ) );
                        }
                    }
                    
                    $cheap_list             = array_values ( array_unique ( $cheap_list ) );
                    $cheap_prods_in_cart    = array_intersect ($cheap_list, $awdp_actual_products); // skipped parent id from cart product list - products in cart count fix
                    
                    // Consider individual quantities
                    if ( $bogo_cheap_individual ) {
                        foreach ( $cheap_prods_in_cart as $cheapKey ) { 
                            if ( array_key_exists ( $cheapKey, $awdp_cart_quanity ) ) {
                                $cheapCount = $cheapCount + $awdp_cart_quanity[$cheapKey];
                            }
                        }
                    }

                    $checkSum               = $bogo_cheap_individual ? $cheapCount : sizeof ( $cheap_prods_in_cart );
                    // $cheap_get              = ( $checkSum > ( $bogo_cheap_get + $bogo_cheap_buy ) ) ? $bogo_cheap_get : ( ( $checkSum - (int)$bogo_cheap_buy ) >= 0 ? ( $checkSum - (int)$bogo_cheap_buy ) : 0 ); // ver @ 5.1.4
                    $cheap_get              = ( ( $checkSum > ( $bogo_cheap_get + $bogo_cheap_buy ) ) || ( $checkSum - $bogo_cheap_buy ) >= 0 ) ? $bogo_cheap_get : 0;

                    $ofr_count              = 1; 
                    
                    if ( in_array( $awdp_cart_prod_id, $cheap_list ) && ( $checkSum > $bogo_cheap_buy ) ) { 
                        
                        foreach ( $cheap_prods_in_cart as $cheap_prod_in_cart ) { 
                            
                            $ofr_get_count_max      = $awdp_cart_quanity[$cheap_prod_in_cart] > $cheap_get ? ( ( $awdp_cart_quanity[$cheap_prod_in_cart] - $cheap_get ) >= $cheap_get ? $cheap_get : $awdp_cart_quanity[$cheap_prod_in_cart] - $cheap_get ) : 0;

                            /*
                            * ver @ 5.1.4 
                            * Checking if individual quantity check is enabled
                            * ver @ 5.1.7
                            * added repeat option 
                            */
                            if ( $bogo_cheap_individual ) {
                                $ofr_count      = $ofr_get_count_max; 
                                $ofr_count      = $ofr_items_count ? ( ( $cheap_get - $ofr_items_count ) < $ofr_count ? ( $cheap_get - $ofr_items_count ) : $ofr_count ) : $ofr_count;

                                // Repeat option
                                $ofr_count      = $bogo_cheap_repeat ? ( $bogo_cheap_buy == 1 ? ( ( $awdp_cart_quanity[$cheap_prod_in_cart] > $cheap_get * floor ( $cheapCount / $bogo_cheap_buy ) ) ? ( ( $cheapCount % 2 ) == 0 ? $ofr_count * ( $cheapCount / 2 ) : $ofr_count * floor ( $cheapCount / 2 ) ) : $awdp_cart_quanity[$cheap_prod_in_cart] ) : ( ( $awdp_cart_quanity[$cheap_prod_in_cart] > $cheap_get * floor ( $cheapCount / $bogo_cheap_buy ) ) ? ( ( $cheapCount % $bogo_cheap_buy ) == 0 ? $ofr_count * ( $cheapCount / $bogo_cheap_buy ) : $ofr_count * floor ( $cheapCount / $bogo_cheap_buy ) ) : $awdp_cart_quanity[$cheap_prod_in_cart] ) ) : $ofr_count; 
                            }
                            // End

                            $awdp_product_price_sorted = $awdp_product_price;
                            sort($awdp_product_price_sorted); // Sort Array - Price
                            
                            // $equalprice = ( count ( array_count_values ( $awdp_product_price_sorted ) ) == 1 ) ? true : false; // Checking if all products have same price

                            $awdp_product_slice = array_slice( $awdp_product_price_sorted, 0, $cheap_get );
                            $awdp_intersect     = array_keys ( array_intersect ( $awdp_product_price, $awdp_product_slice ) ); // Cheapest Applicable Products

                            // $bogo_product = wc_get_product( $cheap_prod_in_cart );
                            $bogo_prod_slug     = get_post_field( 'post_name', $cheap_prod_in_cart );
                            $bogo_product_price = array_key_exists( $cheap_prod_in_cart, $awdp_product_price ) ? wc_add_number_precision ($awdp_product_price[$cheap_prod_in_cart]) : '';

                            if ( $bogo_product_price && ( $ofr_items_count < $cheap_get ) ) { 

                                // $cheapCount++;
                                // $BogoFlag = ( sizeof($wdpBogoIDsinCart) >= $cheap_get ) ? true : ( $equalprice ? ( ( $cheapCount >= $cheap_get ) ? true : false ) : false ); // Setting flag 
                                // Bogo cheapest

                                // Skip Flag
                                $skipFlag = true;

                                // Calculate Discount
                                if ( 'off_percentage' == $bogo_cheap_type ) { 

                                    $bogo_discount          = $bogo_product_price * ( $bogo_cheap_value / 100 ) * $ofr_count; 
                                    $bogo_discount_single   = $bogo_product_price * ( $bogo_cheap_value / 100 ); 
                                    $updated_price          = $bogo_product_price - $bogo_discount; 
                                    $updated_price_single   = $bogo_product_price - $bogo_discount_single;
                                    
                                    /*
                                    * @ ver 5.0.7
                                    * Added id check - fix cart amount mismatch issue
                                    */
                                    if ( in_array ( $cheap_prod_in_cart, $awdp_intersect ) && ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1 && ( $awdp_prod_id == $cheap_prod_in_cart ) ) {

                                        $wdpBogoIDsinCart[] = array ( 
                                            'product_id'                => $cheap_prod_in_cart, 
                                            'discount_type'             => $bogo_cheap_type, 
                                            'discount_percentage'       => $bogo_cheap_value, 
                                            'discounted_price'          => $updated_price, 
                                            'discount'                  => $bogo_discount, 
                                            'single_discount'           => $bogo_discount_single, 
                                            'discounted_price_single'   => $updated_price_single, 
                                            'quantity'                  => $awdp_qn_get, 
                                            'price'                     => $bogo_product_price, 
                                            'offer_items'               => $ofr_count,
                                            'cartKey'                   => $actualCartKey, 
                                            'cheap'                     => true,
                                            'actual_qn'                 => $awdp_cart_quanity[$cheap_prod_in_cart]  
                                        );
                                        $discountApplied[]  = $actualCartKey;

                                    }

                                } else if ( 'fixed' == $bogo_cheap_type ) { 

                                    /*
                                    * @ver 5.0.3
                                    * Fix - error when value greater than product amount
                                    */

                                    $bogo_cheap_value_pre   = wc_add_number_precision ($bogo_cheap_value);
                                    $bogo_discount          = $bogo_cheap_value_pre * $ofr_count; 
                                    $bogo_discount_single   = $bogo_cheap_value_pre; 

                                    if ( $bogo_product_price >= $bogo_discount_single ) {
                                        $updated_price          = $bogo_product_price - $bogo_discount; 
                                        $updated_price_single   = $bogo_product_price - $bogo_discount_single;
                                    } else {
                                        $updated_price          = 0; 
                                        $updated_price_single   = 0;  
                                        $bogo_discount_single   = $bogo_product_price;
                                        $bogo_discount          = $bogo_product_price * $ofr_count; 
                                    }
                                    
                                    /*
                                    * @ ver 5.0.7
                                    * Added id check - fix cart amount mismatch issue
                                    */
                                    if ( in_array ( $cheap_prod_in_cart, $awdp_intersect ) && ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1 && ( $awdp_prod_id == $cheap_prod_in_cart ) ) {

                                        $wdpBogoIDsinCart[] = array ( 
                                            'product_id'                => $cheap_prod_in_cart, 
                                            'discount_type'             => $bogo_cheap_type, 
                                            'discount_percentage'       => $bogo_cheap_value_pre, 
                                            'discounted_price'          => $updated_price, 
                                            'discount'                  => $bogo_discount, 
                                            'single_discount'           => $bogo_discount_single, 
                                            'discounted_price_single'   => $updated_price_single, 
                                            'quantity'                  => $awdp_qn_get, 
                                            'price'                     => $bogo_product_price, 
                                            'offer_items'               => $ofr_count,
                                            'cartKey'                   => $actualCartKey, 
                                            'cheap'                     => true,
                                            'actual_qn'                 => $awdp_cart_quanity[$cheap_prod_in_cart] 
                                        );
                                        $discountApplied[]  = $actualCartKey;

                                    }

                                } else if ( 'free' == $bogo_cheap_type ) { 

                                    $updated_price          = 0;
                                    $bogo_discount          = $bogo_product_price * $ofr_count;
                                    $updated_price          = $bogo_product_price - $bogo_discount; 
                                    $bogo_discount_single   = $bogo_product_price; 
                                    $updated_price_single   = $bogo_product_price - $bogo_discount_single;

                                    /*
                                    * @ ver 5.0.7
                                    * Added id check - fix cart amount mismatch issue
                                    */
                                    if ( in_array ( $cheap_prod_in_cart, $awdp_intersect ) && ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_count >= 1 && ( $awdp_prod_id == $cheap_prod_in_cart ) ) {

                                        $wdpBogoIDsinCart[] = array ( 
                                            'product_id'                => $cheap_prod_in_cart, 
                                            'discount_type'             => $bogo_cheap_type, 
                                            'discount_percentage'       => $bogo_cheap_value, 
                                            'discounted_price'          => 0, 
                                            'discount'                  => $bogo_discount, 
                                            'single_discount'           => $bogo_product_price, 
                                            'discounted_price_single'   => 0, 
                                            'quantity'                  => $awdp_qn_get, 
                                            'price'                     => $bogo_product_price, 
                                            'offer_items'               => $ofr_count,
                                            'cartKey'                   => $actualCartKey, 
                                            'cheap'                     => true,
                                            'actual_qn'                 => $awdp_cart_quanity[$cheap_prod_in_cart] 
                                        );
                                        $discountApplied[]  = $actualCartKey;

                                    }

                                }
                                $free_count++;
                                $ofr_items_count = $ofr_items_count + $ofr_count;

                            }

                        }
                        
                    }
                    // End cheapest product

                } else { 

                    /* 
                    * BOGO Rule: Buy X get Y
                    * Discount types: Fixed, Percentage, Free
                    * ver 3.2.3 -> Same variation price and variation fix, Modified wdpProductList to get variation ids
                    * ver 5.0.7 -> ( $awdp_prod_id == $awdp_to_single ) check added - fix - cart amount mismacth when multiple rules activated
                    */

                    foreach ( $bogo_combinations as $bogo_combination ) {  

                        // $awdp_to_list       = empty ( $bogo_combination['to_list'] ) ? [] : $bogo_combination['to_list']; @ 5.0.5
                        $awdp_to_list       = []; 
                        $buy_limit          = 0;
                        $awdp_BY_count      = 0;
                        $awdp_BY_get_count  = 0;
                        // $awdp_to_list = [];
                        $awdp_from_list     = [];
                        $any_prods          = [];

                        // Buy X Get Y
                        $ofr_applied        = 0;
                        $ofr_remaining      = 0;
                        $awdp_buy_qn        = 0;

                        $awdp_qn            = 0;

                        if ( empty($bogo_combination['to_list']) || ( !empty( $bogo_combination['to_list']) && ( $bogo_combination['to_list'][0] == 'null' || $bogo_combination['to_list'][0] == '' ) ) ) { // Any Product
                            
                            /*
                            * ver @ 5.0.4
                            * any product display options added
                            */
                            $any_prods      = call_user_func_array ( 
                                array ( new AWDP_Discount(), 'wdpAllProducts' ), 
                                array ( $bogo_display_count, $bogo_display_sort, $bogo_combination['from_list'], $awdp_actual_products )
                            ); 
                            // $awdp_to_list   = $all_products; // All Products to Any Products List
                            $awdp_to_list   = $any_prods; // Any products
                            $all_flag       = true; 

                        } else { // List / Product Selection

                            $awdp_to_list_items = $bogo_combination['to_list'];
                            foreach ( $awdp_to_list_items as $key => $value ) { 
                                if ( strpos ( $value, $list_key ) === false ) {
                                    // get variations
                                    $awdp_to_list[] = $value;
                                } else { // Check if product list
                                    $list_id        = intval ( str_replace ( $list_key, '', $value ) ); 
                                    $list_prods     = call_user_func_array ( 
                                        array ( new AWDP_Discount(), 'wdpProductList' ), 
                                        array ( $list_id, false, false, -1, true ) 
                                    );
                                    $awdp_to_list   = array_unique ( array_merge ( $list_prods, $awdp_to_list ) ); 
                                }
                            } 
                            $awdp_to_variations = call_user_func_array ( 
                                array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                                array ( $awdp_to_list )
                            ); 
                            $awdp_to_list       = !empty($awdp_to_variations) ? array_merge ( $awdp_to_list, $awdp_to_variations ) : $awdp_to_list;

                            // Remove variable prodcts parent from list @ 5.0.4
                            $parentIDs            = call_user_func_array ( 
                                array ( new AWDP_Discount(), 'wdpGetVariationParent' ), 
                                array ( $awdp_to_list )
                            ); 
                            $awdp_to_list       = !empty ( $parentIDs ) ? array_diff( $awdp_to_list, $parentIDs ) : $awdp_to_list;

                        }

                        if ( empty($bogo_combination['from_list']) || ( !empty( $bogo_combination['from_list']) && ( $bogo_combination['from_list'][0] == 'null' || $bogo_combination['from_list'][0] == '' ) ) ) { // Any Product
                            
                            $awdp_from_list = $all_products; // Full products
                            $buy_limit      = $awdp_cart_quanity; // Including individual project quantities 

                            /*
                            * @ ver 5.1.4 
                            * Limiting from list size to buy quantity
                            * Compare with to list
                            */
                            if ( sizeof ( array_diff ( $awdp_from_list, $awdp_to_list ) ) >= $awdp_qn_buy ) {
                                $awdp_from_list     = array_diff ( $awdp_from_list, $awdp_to_list );
                            } else {
                                $awdp_from_list     = ( sizeof ( $awdp_from_list ) > $awdp_qn_buy ) ? array_slice ( $awdp_from_list, 0, $awdp_qn_buy ) : $awdp_from_list;
                            }

                        } else { // List / Product Selection

                            $awdp_from_list_items = $bogo_combination['from_list'];
                            foreach ( $awdp_from_list_items as $awdp_from_list_item ) { 
                                if ( strpos ( $awdp_from_list_item, $list_key ) === false ) {
                                    // get variations
                                    $awdp_from_list[]   = $awdp_from_list_item;
                                } else { // Check if product list
                                    $list_id            = intval ( str_replace ( $list_key, '', $awdp_from_list_item ) );
                                    $list_prods         = call_user_func_array ( 
                                        array ( new AWDP_Discount(), 'wdpProductList' ), 
                                        array ( $list_id ) 
                                    );
                                    $awdp_from_list     = array_unique ( array_merge ( $list_prods, $awdp_from_list ) );
                                }
                            }
                            $awdp_from_variations = call_user_func_array ( 
                                                        array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                                                        array ( $awdp_from_list )
                                                    ); 
                            $awdp_from_list       = !empty($awdp_from_variations) ? array_merge ( $awdp_from_list, $awdp_from_variations ) : $awdp_from_list;

                            // Remove variable prodcts parent from list @ 5.0.4
                            $parentIDs            = call_user_func_array ( 
                                                        array ( new AWDP_Discount(), 'wdpGetVariationParent' ), 
                                                        array ( $awdp_from_list )
                                                    ); 
                            $awdp_from_list       = !empty ( $parentIDs ) ? array_diff( $awdp_from_list, $parentIDs ) : $awdp_from_list;

                        } 

                        // Remove repeatations
                        $awdp_to_list   = $awdp_to_list ? array_values ( array_unique ( $awdp_to_list ) ) : [];  
                        $awdp_from_list = $awdp_from_list ? array_values ( array_unique ( $awdp_from_list ) ) : []; 

                        // $awdp_intersect = array_values ( array_intersect ( $awdp_from_list, $awdp_products ) ); 
                        $awdp_intersect = array_values ( array_intersect ( $awdp_from_list, $awdp_actual_products ) ); 
                        $awdp_in_cart   = array_unique ( array_merge ( $awdp_intersect, $awdp_in_cart ) ); 

                        $bogo_count_val = $bogo_y_individual ? sizeof ( $awdp_cart_items ) : sizeof ( $awdp_in_cart );
 
                        /*
                        * ver @ 5.0.4 
                        * Checking from qn - individual count
                        */
                        if ( $awdp_from_list ) { 
                            foreach ( $awdp_from_list as $awdp_from_sgl ) { 
                                if ( $bogo_consider_individual && array_key_exists ( $awdp_from_sgl, $awdp_cart_quanity ) ) { 
                                    $awdp_buy_qn = $awdp_buy_qn + $awdp_cart_quanity[$awdp_from_sgl];
                                } else if ( array_key_exists ( $awdp_from_sgl, $awdp_cart_quanity ) ) {
                                    $awdp_buy_qn = $awdp_buy_qn + 1;
                                }
                            }
                        }
                        
                        // $awdp_qn = ( $bogo_combination['from_list'] == $bogo_combination['to_list'] ) ? ( $awdp_cart_quanity[$awdp_cart_prod_id] - 1 ) : $awdp_cart_quanity[$awdp_cart_prod_id]; // quantity - 1 when buy x get x

                        // $awdp_qn        = ( $bogo_combination['from_list'] == $bogo_combination['to_list'] ) ? ( ( sizeof($awdp_in_cart) >= ( $awdp_qn_get + $awdp_qn_buy ) ) ? $awdp_qn_get : ( ( $awdp_qn_buy - $awdp_qn_get ) >= 0 ? ( $awdp_qn_buy - $awdp_qn_get ) : 0 ) ) : $awdp_cart_quanity[$awdp_cart_prod_id]; // @@@ 3.3.8

                        /*
                        * ver @ 5.0.4 
                        * else - $awdp_cart_quanity[$awdp_cart_prod_id] changed to $awdp_qn_get
                        * offer count issue fix
                        * $awdp_
                        */
                        // $awdp_qn        = ( $bogo_combination['from_list'] == $bogo_combination['to_list'] ) ? ( ( sizeof($awdp_in_cart) >= ( $awdp_qn_get + $awdp_qn_buy ) ) ? $awdp_qn_get : ( ( $awdp_qn_buy - $awdp_qn_get ) >= 0 ? ( $awdp_qn_buy - $awdp_qn_get ) : 0 ) ) : $awdp_qn_get; 
                        // $awdp_qn        = ( $bogo_combination['from_list'] == $bogo_combination['to_list'] ) ? ( ( sizeof($awdp_in_cart) >= ( $awdp_qn_get + $awdp_qn_buy ) ) ? $awdp_qn_get : ( ( $awdp_qn_buy - $awdp_qn_get ) >= 0 ? ( $awdp_qn_buy - $awdp_qn_get ) : 0 ) ) : $awdp_qn_get; 

                        if ( $awdp_to_list ) {
                            if ( $bogo_combination['from_list'] == $bogo_combination['to_list'] ) { 
                                $awdp_qn        = ( sizeof($awdp_in_cart) >= ( $awdp_qn_get + $awdp_qn_buy ) ) ? $awdp_qn_get : ( ( $awdp_qn_buy - $awdp_qn_get ) >= 0 ? ( $awdp_qn_buy - $awdp_qn_get ) : 0 ); 
                            } else {
                                foreach ( $awdp_to_list as $awdp_to_sgl ) { 
                                    if ( $bogo_consider_individual && array_key_exists ( $awdp_to_sgl, $awdp_cart_quanity ) ) { 
                                        $awdp_qn = $awdp_qn + $awdp_cart_quanity[$awdp_to_sgl];
                                    } else if ( array_key_exists ( $awdp_to_sgl, $awdp_cart_quanity ) ) {
                                        $awdp_qn = $awdp_qn + 1;
                                    }
                                }
                            }
                        }
                        
                        /* 
                        * ver @ 5.0.4
                        * Added - cart quantity greater than buy quantity check - sizeof ( $awdp_actual_products ) > $awdp_qn_buy
                        *,Skipping buy items from cart check
                        */
                        $cart_after_skipping_buy    = array_intersect ( $awdp_in_cart, $awdp_from_list ) ? array_diff ( $awdp_in_cart, array_intersect ( $awdp_in_cart, $awdp_from_list ) ) : $awdp_in_cart;
                        $to_list_skipping_buy       = array_intersect ( $awdp_to_list, $awdp_from_list ) ? array_diff ( $awdp_to_list, array_intersect ( $awdp_to_list, $awdp_from_list ) ) : $awdp_to_list;

                        /*
                        * ver 5.0.5
                        * From and To list having same products - removing first $awdp_qn_buy common elements from $to_list_skipping_buy list
                        */
                        if ( sizeof ( array_intersect ( $awdp_to_list, $awdp_from_list ) ) > $awdp_qn_buy ) { 
                            $from_incart            = array_intersect ( $awdp_in_cart, $awdp_from_list );
                            $to_incart              = array_intersect ( $awdp_in_cart, $awdp_to_list );
                            $cmn_incart             = array_intersect ( $from_incart, $to_incart );
                            $sliced                 = ( sizeof ( $cmn_incart ) > $awdp_qn_buy ) ? array_slice ( $cmn_incart, 0, $awdp_qn_buy ) : [];
                            $to_list_skipping_buy   = !empty ( $sliced ) ? array_diff ( $awdp_to_list, $sliced ) : $to_list_skipping_buy;
                        }
                        
                        /*
                        * ver 5.0.4
                        * in_array ( $awdp_cart_prod_id, $awdp_from_list ) to array_intersect ( $awdp_products, $awdp_from_list )
                        * removed $awdp_qn >= $awdp_qn_buy check
                        */
                        // if ( array_intersect ( $awdp_products, $awdp_from_list ) && ( $awdp_qn >= $awdp_qn_buy || sizeof ( $cart_after_skipping_buy ) >= $awdp_qn_buy || $awdp_buy_qn >= $awdp_qn_buy ) ) { 

                        if ( array_intersect ( $awdp_products, $awdp_from_list ) && ( sizeof ( $cart_after_skipping_buy ) >= $awdp_qn_buy || $awdp_buy_qn >= $awdp_qn_buy ) ) { 

                            $wdpBogoIDs = array_merge ( $wdpBogoIDs, $awdp_to_list ); 
                            $ofr_flag   = true;
                            // $wdpBogoIDsinCart;

                            $repeatitems = [];
                            foreach ( $awdp_intersect as $intersect ) {
                                if ( $awdp_cart_quanity[$intersect] >= $awdp_qn_buy ) {
                                    $repeatitems[] = $intersect;
                                }
                            } 
                      
                            foreach ( $to_list_skipping_buy as $awdp_to_single ) {  

                                // if ( array_key_exists ( $awdp_to_single, $awdp_cart_quanity ) && $awdp_cart_quanity[$awdp_to_single] >= $awdp_qn_get ) { // Checking if cart_quantity > buy_count 

                                if ( array_key_exists ( $awdp_to_single, $awdp_cart_quanity ) ) { // @@ 3.1.1
                                    
                                    // $awdp_new_qn = ( $awdp_to_list == $awdp_from_list ) ? ( $awdp_cart_quanity[$awdp_to_single] - 1 ) : $awdp_cart_quanity[$awdp_to_single];
                                    // if ( $awdp_new_qn > 0 ) {

                                        $compare_qn = $awdp_qn_buy;
                                    
                                    // if ( ( sizeof($awdp_intersect) >= $compare_qn ) && ( sizeof($wdpBogoIDsinCart) < $compare_qn ) ) { // @@ 3.2.1 
                                    if ( sizeof($wdpBogoIDsinCart) <= $awdp_qn ) { // @@ 3.2.1 
                                        
                                        /*
                                        * ver 5.0.4
                                        * individual quantity considered for buy x get y
                                        */
                                        $ofr_count      = $bogo_consider_individual ? ( ( $ofr_remaining && $ofr_remaining < $awdp_cart_quanity[$awdp_to_single] ) ? $ofr_remaining :( ( $awdp_qn_get < $awdp_cart_quanity[$awdp_to_single] ) ? $awdp_qn_get : $awdp_cart_quanity[$awdp_to_single] ) ) : 1; // repeat turned off and individual qn not considered // repeat, individual qn needs to be worked on

                                        // $bogo_product = wc_get_product($awdp_to_single);
                                        $bogo_prod_slug = get_post_field( 'post_name', $awdp_to_single ); 

                                        $discount       = 0;
                                        // $prod_price     = $price;
                                        $prod_price     = $awdp_product_price[$awdp_to_single];

                                        // Get product price
                                        $bogo_product_price = wc_add_number_precision($prod_price); 
                                        
                                        // Offer remaining check added
                                        
                                        if ( $bogo_product_price > 0 ) {  
                                            
                                            $ofr_remaining  = $ofr_flag ? $ofr_count : $ofr_remaining;

                                            // Skip Flag
                                            $skipFlag = true;

                                            // Calculate Discount
                                            /*
                                            * ver 4.0.5
                                            * Fixed Type Added
                                            * $free_count moved to inside loop (individual check)
                                            * Bogo x get y
                                            */
                                            // if ( 'off_percentage' == $bogo_type && sizeof($wdpBogoIDsinCart) < $awdp_qn_get ) { 
                                            if ( 'off_percentage' == $bogo_type ) { 
                                                // $bogo_discount = $bogo_product_price * ($bogo_value / 100); 
                                                // $updated_price = $bogo_product_price - $bogo_discount; 
                                                $bogo_discount          = $bogo_product_price * ( $bogo_value / 100 ) * $ofr_count; 
                                                $bogo_discount_single   = $bogo_product_price * ( $bogo_value / 100 ); 
                                                $updated_price          = $bogo_product_price - $bogo_discount; 
                                                $updated_price_single   = $bogo_product_price - $bogo_discount_single;

                                                /*
                                                * ver 5.0.4
                                                * $ofr_count >= 1 chnaged to $ofr_remaining >= 1
                                                * 'offer_items' value changed to $ofr_remaining
                                                * removed - in_array ( $awdp_to_single, $awdp_to_list )
                                                */
                                                /*
                                                * @ ver 5.0.7
                                                * Added id check - fix cart amount mismatch issue
                                                */
                                                if ( ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_remaining >= 1 && ( $awdp_prod_id == $awdp_to_single )  ) { 

                                                    $wdpBogoIDsinCart[] = array ( 
                                                        'product_id'                => $awdp_to_single, 
                                                        'discount_type'             => $bogo_type, 
                                                        'discount_percentage'       => $bogo_value, 
                                                        'discounted_price'          => $updated_price, 
                                                        'discount'                  => $bogo_discount, 
                                                        'single_discount'           => $bogo_discount_single, 
                                                        'discounted_price_single'   => $updated_price_single, 
                                                        'quantity'                  => $awdp_qn_get, 
                                                        'price'                     => $bogo_product_price,
                                                        'cartKey'                   => $actualCartKey, 
                                                        'offer_items'               => $ofr_count,
                                                        'actual_qn'                 => $awdp_cart_quanity[$awdp_to_single] 
                                                    ); 
                                                    $discountApplied[]  = $actualCartKey;
                                                } 

                                            // } else if ( 'fixed' == $bogo_type && sizeof($wdpBogoIDsinCart) < $awdp_qn_get ) { 
                                            } else if ( 'fixed' == $bogo_type ) { 
                                                
                                                /*
                                                * @ver 5.0.3
                                                * Fix - error when value greater than product amount
                                                */

                                                $bogo_value_pre         = wc_add_number_precision ($bogo_value);
                                                $bogo_discount          = $bogo_value_pre * $ofr_count; 
                                                $bogo_discount_single   = $bogo_value_pre; 

                                                if ( $bogo_product_price >= $bogo_discount_single ) {
                                                    $updated_price          = $bogo_product_price - $bogo_discount; 
                                                    $updated_price_single   = $bogo_product_price - $bogo_discount_single;
                                                } else {
                                                    $updated_price          = 0; 
                                                    $updated_price_single   = 0;
                                                    $bogo_discount_single   = $bogo_product_price;
                                                    $bogo_discount          = $bogo_product_price * $ofr_count; 
                                                }

                                                /*
                                                * ver 5.0.4
                                                * $ofr_count >= 1 chnaged to $ofr_remaining >= 1
                                                * 'offer_items' value changed to $ofr_remaining
                                                * removed - in_array ( $awdp_to_single, $awdp_to_list )
                                                */
                                                /*
                                                * @ ver 5.0.7
                                                * Added id check - fix cart amount mismatch issue
                                                */
                                                if ( ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_remaining >= 1  && ( $awdp_prod_id == $awdp_to_single ) ) { 

                                                    $wdpBogoIDsinCart[] = array ( 
                                                        'product_id'                => $awdp_to_single, 
                                                        'discount_type'             => $bogo_type, 
                                                        'discount_percentage'       => $bogo_value_pre, 
                                                        'discounted_price'          => $updated_price, 
                                                        'discount'                  => $bogo_discount, 
                                                        'single_discount'           => $bogo_discount_single, 
                                                        'discounted_price_single'   => $updated_price_single, 
                                                        'quantity'                  => $awdp_qn_get, 
                                                        'price'                     => $bogo_product_price,
                                                        'cartKey'                   => $actualCartKey, 
                                                        'offer_items'               => $ofr_count,
                                                        'actual_qn'                 => $awdp_cart_quanity[$awdp_to_single] 
                                                    );
                                                    $discountApplied[]  = $actualCartKey;

                                                }

                                            // } else if ( 'free' == $bogo_type && sizeof($wdpBogoIDsinCart) < $awdp_qn_get ) { 
                                            } else if ( 'free' == $bogo_type ) {  

                                                // $updated_price = 0;
                                                // $bogo_discount = $bogo_product_price;
                                                $updated_price          = 0;
                                                $bogo_discount          = $bogo_product_price * $ofr_count;
                                                $updated_price          = $bogo_product_price - $bogo_discount; 
                                                $bogo_discount_single   = $bogo_product_price; 
                                                $updated_price_single   = $bogo_product_price - $bogo_discount_single; 

                                                /*
                                                * ver 5.0.4
                                                * $ofr_count >= 1 changed to $ofr_remaining >= 1
                                                * 'offer_items' value changed to $ofr_remaining
                                                * removed - in_array ( $awdp_to_single, $awdp_to_list )
                                                */
                                                /*
                                                * @ ver 5.0.7
                                                * Added id check - fix cart amount mismatch issue
                                                */
                                                if ( ( array_search ( $actualCartKey, array_column ( $wdpBogoIDsinCart, 'cartKey' ) ) === false ) && $ofr_remaining >= 1 && ( $awdp_prod_id == $awdp_to_single ) ) { 

                                                    $wdpBogoIDsinCart[] = array ( 
                                                        'product_id'                => $awdp_to_single, 
                                                        'discount_type'             => $bogo_type, 
                                                        'discount_percentage'       => $bogo_value, 
                                                        'discounted_price'          => 0, 
                                                        'discount'                  => $bogo_discount, 
                                                        'single_discount'           => $bogo_product_price, 
                                                        'discounted_price_single'   => $updated_price_single, 
                                                        'quantity'                  => $awdp_qn_get, 
                                                        'price'                     => $bogo_product_price,
                                                        'cartKey'                   => $actualCartKey, 
                                                        'offer_items'               => $ofr_count,
                                                        'actual_qn'                 => $awdp_cart_quanity[$awdp_to_single] 
                                                    );
                                                    $discountApplied[]  = $actualCartKey;

                                                }  

                                            }

                                            $free_count++;

                                            $ofr_flag       = false; 
                                            $ofr_applied    = $ofr_applied + $ofr_count; 
                                            $ofr_remaining  = ( $ofr_remaining > 0 ) ? ( ( ( $awdp_qn_get - $ofr_applied ) >= 0 ) ? ( $awdp_qn_get - $ofr_applied ) : 0 ) : 0; 
                                            // $ofr_remaining  = $bogo_consider_individual ? $ofr_remaining : 1;  // commented @ 5.1.4 - from any product issue

                                        }

                                    }

                                }
                                
                            }

                            $wdpBogoIDsinCart   = array_values($wdpBogoIDsinCart); 
                            $wdpBogoIDs         = array_unique(array_values($wdpBogoIDs));
                            $products_array     = $wdpBogoIDs; // New Products Array
                        
                        }

                    }

                } 

                $offerMessage   = $bogo_description;
                $offerPrducts   = $products_array; 
                $offerAll       = $all_flag ? true : false;

                // Cart Message
                // if ( $ofr_remaining !== 0 && wc_has_notice( $bogo_description, $notice_type ) === false ) {
                //     add_action( 'woocommerce_before_cart_table', 
                //         function() use ( $bogo_description, $notice_type ) { 
                //             wc_add_notice( apply_filters( 'wc_add_to_cart_message', $bogo_description, false ), $notice_type );
                //         }, 1
                //     );
                // }

            } 

            $result['productDiscount']              = $discVariable; 
            $result['wdpBogoIDsinCart']             = $wdpBogoIDsinCart; 
            $result['offerMessage']                 = $offerMessage;
            $result['offerPrducts']                 = $offerPrducts;
            $result['offerAll']                     = $offerAll; 
            $result['skipFlag']                     = $skipFlag; 
    
            $result['bogoItems']['itemsID']         = $offerPrducts;
            $result['bogoItems']['itemsCount']      = $offerAll; 
            $result['bogoItems']['itemsinCart']     = sizeof($wdpBogoIDsinCart); 
            $result['bogoItems']['itemsType']       = $bogo_type; 
            $result['bogoItems']['itemsValue']      = $bogo_value; 

        } 

        return $result;

    }

}
