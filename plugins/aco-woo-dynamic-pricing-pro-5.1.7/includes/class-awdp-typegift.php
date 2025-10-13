<?php

/*
* @@ Product Price
* @@ Last updated version 4.0.0
* Added Option to select variable product as gift (For cart total, product price, no. of items)
* Cart / Single page gift suggestions call updated
* Removed $product == $product_id check while adding wdpGiftsinCart list (Bug: if gift price is less than discount value - product price rule, discount not getting applied )
*/

class AWDP_typeGift
{

    public function apply_discount_gift ( $rule, $product, $price, $quantity, $discVariable, $prodLists, $cartContents, $cartContent, $wdpGiftsinCart, $discPrices, $disc_prod_ID, $dispPrice, $cartView = false )
    {  

        $result = [];

        if ( isset(WC()->cart) && WC()->cart->get_cart_contents_count() > 0 ) { 

            $gift_variation         = $rule['gift_variation'] ? $rule['gift_variation'] : ''; // Gift Variable Product @@ 4.0.0

            /*
            * Changed from $cartContent['product_id']
            * @@ ver 4.0.0    
            */
            $product_id             = $cartContent['variation_id'] != 0 ? $cartContent['variation_id'] : $cartContent['product_id'];

            $products_array         = [];

            $gift_type              = $rule['gift_type'];

            $gift_cart_min          = $rule['gift_cart_min'];
            $gift_dis_cpn           = $rule['gift_dis_cpn'];
            $gift_mltpl             = $rule['gift_mltpl'];
            $gift_products          = $rule['gift_products']; // Free gift for any product in cart
            $gift_combinations      = $rule['gift_combinations'];

            $gift_auto_cart         = $rule['gift_add_cart'];
            $gift_numbers           = $rule['gift_numbers'] ? (int)$rule['gift_numbers'] : 1; // Default set to 1 @@ 3.1.0
            
            $awdp_cart_items        = $cartContents ? $cartContents : WC()->cart->get_cart();
            // $awdp_cart_items_total  = 0;
            $awdp_cart_items_count  = 0;
            $awdp_free_count        = 1;

            $ruleID                 = $rule['id'];

            $all_flag               = false;
            $gift_description       = $rule['gift_off_msg']; 

            $item_price             = $price;

            $giftSuggestionsFlag    = false;

            $awdp_cart_ids = $awdp_cart_quanity = $awdp_product_price = $awdp_product_slug = $awdp_cart_price = [];

            $skipFlag               = false;

            // Sort cart items based on price
            uasort ( $awdp_cart_items, 
                function($a, $b) {
                    $price_a = !empty ($discPrices) ? ( ( array_key_exists ( $a['data']->get_slug(), $discPrices ) && $discPrices[$a['data']->get_slug()] != '' ) ? wc_remove_number_precision ( $discPrices[$a['data']->get_slug()] ) : $a['data']->get_price() ) : $a['data']->get_price();
                    $price_b = !empty ($discPrices) ? ( ( array_key_exists ( $b['data']->get_slug(), $discPrices ) && $discPrices[$b['data']->get_slug()] != '' ) ? wc_remove_number_precision ( $discPrices[$b['data']->get_slug()] ) : $b['data']->get_price() ) : $b['data']->get_price();
                    return $price_a < $price_b ? 1 : -1;
                }
            );
            
            // Cart Items Loop
            foreach ( $awdp_cart_items as $awdp_cart_item ) { 

                /* 
                * Sale Items Check
                * @ver 5.0.3
                */
                if ( isset($rule['disable_on_sale']) && $rule['disable_on_sale'] && $awdp_cart_item['data']->is_on_sale() ) { 
                    continue;
                }
                //

                $prod_ID    = $awdp_cart_item['data']->get_slug(); 
                $cartprice  = !empty ($discPrices) ? ( ( array_key_exists ( $prod_ID, $discPrices ) && $discPrices[$prod_ID] != '' ) ? wc_remove_number_precision ( $discPrices[$prod_ID] ) : $awdp_cart_item['data']->get_price() ) : $awdp_cart_item['data']->get_price(); 

                if ( $awdp_cart_item['variation_id'] != 0 ) { // Checking variable product
                    array_push($awdp_cart_ids, $awdp_cart_item['variation_id']);
                    $awdp_cart_quanity[$awdp_cart_item['variation_id']]     = $awdp_cart_item['quantity']; 
                    $awdp_product_price[$awdp_cart_item['variation_id']]    = $cartprice; 
                    $awdp_cart_price[$awdp_cart_item['variation_id']]       = $cartprice * $awdp_cart_item['quantity']; 
                    $awdp_product_slug[$awdp_cart_item['variation_id']]     = $prod_ID; 
                } else {
                    array_push($awdp_cart_ids, $awdp_cart_item['product_id']);
                    $awdp_cart_quanity[$awdp_cart_item['product_id']]       = $awdp_cart_item['quantity']; 
                    $awdp_product_price[$awdp_cart_item['product_id']]      = $cartprice;   
                    $awdp_cart_price[$awdp_cart_item['product_id']]         = $cartprice * $awdp_cart_item['quantity'];   
                    $awdp_product_slug[$awdp_cart_item['product_id']]       = $prod_ID;   
                }
                
            }

            $awdp_cart_items_total = !empty ( $awdp_cart_price ) ? array_sum ( $awdp_cart_price ) : 0;

            // All Products
            // $all_products = $this->wdpProductList() ? $this->wdpProductList() : [];
            $all_products = $awdp_cart_ids ? $awdp_cart_ids : [];

            // Sort rules based on value
            // @array_multisort( array_column ( $gift_combinations, "no_products" ), SORT_DESC, $gift_combinations );
            /*
            * @ver 5.0.3
            * multisort chnaged to usort - errror array size issue
            */
            usort ( $gift_combinations, function ( $item1, $item2 ) { 
                if ( array_key_exists ( 'no_products', $item1 ) && $item1['no_products'] != '' && array_key_exists ( 'no_products', $item2 ) && $item2['no_products'] != '' )
                    return $item2['no_products'] <=> $item1['no_products'];
            });
            
            // Gift Combinations Loop
            $awdp_cart_product_ids = $awdp_cart_ids;

            /*
            * ver @ 5.0.4
            * added in_array ( $product_id, $prodsincart ) check to skip non gift items from entering loop
            *
            */

            // Check
            if ( 'gift_cart_item' == $gift_type || 'gift_cart_amount' == $gift_type ) { 

                // Free Gifts based on Number of Items in Cart || Cart Total
                if ( $gift_combinations ) { 

                    $awdp_cart_wt_gfit  = $awdp_cart_price;
                    $gifts_count        = 0;
                    // $gift_numbers = 1; // Setting gift number to 1
                    foreach ( $gift_combinations as $gift_combination ) { 
                        if ( $gift_variation ) {
                            $products   = array_key_exists ( 'variations', $gift_combination ) ? $gift_combination['variations'] : [];
                        } else {
                            $products   = array_key_exists ( 'products', $gift_combination ) ? $gift_combination['products'] : [];
                        } 
                        if ( $products ) { 
                            foreach ( $products as $product ) {
                                $variations = call_user_func_array ( 
                                    array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                                    array ( $product ) 
                                ); 
                                if ( $variations ) { 
                                    foreach ( $variations as $variation ) {
                                        if ( ( $key = array_search ( $variation, $awdp_cart_product_ids ) ) !== false ) { 
                                            unset($awdp_cart_product_ids[$key]); 
                                            unset($awdp_cart_wt_gfit[$variation]);
                                        }
                                    }
                                } else if ( ( $key = array_search ( $product, $awdp_cart_product_ids ) ) !== false ) {
                                    unset($awdp_cart_product_ids[$key]);
                                    unset($awdp_cart_wt_gfit[$product]);
                                }
                            }
                        }
                    }
                    /*
                    * @Fix - FreeGift when only gift product in cart
                    */
                    $awdp_cart_items_count  =  sizeof ( $awdp_cart_product_ids ); 
                    $awdp_cart_items_total  =  array_sum ( $awdp_cart_wt_gfit ); 
                    foreach ( $gift_combinations as $gift_combination ) { 

                        $products_with_vars     = [];
                        $condition              = array_key_exists ( 'condition', $gift_combination ) ? $gift_combination['condition'] : '';
                        $value                  = array_key_exists ( 'no_products', $gift_combination ) ? (int)$gift_combination['no_products'] : '';
                        $compare_value          = ( 'gift_cart_item' == $gift_type ) ? $awdp_cart_items_count : $awdp_cart_items_total; 
                        // $products                   = $gift_combination['products']; 
                        
                        if ( $gift_variation ) {
                            $products   = array_key_exists ( 'variations', $gift_combination ) ? $gift_combination['variations'] : [];
                        } else {
                            $products   = array_key_exists ( 'products', $gift_combination ) ? $gift_combination['products'] : [];
                            /*
                            * ver@ 5.0.3
                            * get all variations
                            */
                            if ( !empty ( $products ) ) {
                                $products_with_vars = call_user_func_array ( 
                                    array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                                    array ( $products ) 
                                );
                            }
                        } 

                        $checkCondition = call_user_func_array ( 
                            array ( new AWDP_Discount(), 'check_condition' ), 
                            array ( $condition, $compare_value, $value ) 
                        ); 

                        if( $checkCondition && array_diff ( $awdp_cart_ids, $products ) ) { 
                            $products_array = array_merge ( $products_array , $products );
                            $prodsincart    = !empty ( $products_with_vars ) ? array_intersect ( $products_with_vars, $awdp_cart_ids ) : array_intersect ( $products, $awdp_cart_ids ); 
                            
                            if ( $prodsincart && in_array ( $product_id, $prodsincart ) ) { 

                                // Skip Flag
                                $skipFlag = true;

                                foreach ( $prodsincart as $product ) { 
                                    $prod_price = ( $product === $product_id ) ? $item_price : $awdp_product_price[$product]; 
                                    $prod_slug  = $awdp_product_slug[$product];
                                    $variations = call_user_func_array ( 
                                        array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                                        array ( $product ) 
                                    );
                                    if ( $variations ) { 
                                        foreach ( $variations as $variation ) { 
                                            if ( isset( $awdp_cart_ids ) && in_array( $variation, $awdp_cart_ids ) && sizeof($wdpGiftsinCart) < $gift_numbers ) {
                                                $wdpGiftsinCart[] = array ( 
                                                    'product_id' => $variation, 
                                                    'discount'  => $prod_price, 
                                                    'quantity'  => $gift_numbers, 
                                                    'price'     => $prod_price, 
                                                    'slug'      => $prod_slug,
                                                    'actual_qn' => $awdp_cart_quanity[$variation] 
                                                );
                                            }
                                        }
                                    } else { 
                                        $occurence = array_count_values($awdp_cart_ids); 
                                        if ( isset( $awdp_cart_ids ) && in_array( $product, $awdp_cart_ids ) && sizeof($wdpGiftsinCart) < $gift_numbers ) { 
                                            $wdpGiftsinCart[] = array ( 
                                                'product_id' => $product, 
                                                'discount'  => $prod_price, 
                                                'quantity'  => $gift_numbers, 
                                                'price'     => $prod_price, 
                                                'slug'      => $prod_slug,
                                                'actual_qn' => $awdp_cart_quanity[$product]  
                                            );
                                        }
                                    }
                                }

                            }
                        }
                    }

                    $products_array = array_unique($products_array); 

                }
                // End Gift - Cart Amount / Items ///////////////////////////////////////

            } else if ( 'gift_product_amount' == $gift_type ) { 

                // Free Gifts based on Product Price 
                if ( $gift_combinations ) {

                    // $gift_numbers       = 1; // Set to 1
                    $current_prod       = $product_id;
                    
                    foreach ( $gift_combinations as $gift_combination ) { 
                        
                        $products_with_vars = [];
                        $condition          = $gift_combination['condition'];
                        $value              = $gift_combination['no_products'];
                        // $products          = $gift_combination['products']; 
                        
                        if ( $gift_variation ) {
                            $products   = array_key_exists ( 'variations', $gift_combination ) ? $gift_combination['variations'] : [];
                        } else {
                            $products   = array_key_exists ( 'products', $gift_combination ) ? $gift_combination['products'] : [];
                            /*
                            * ver@ 5.0.3
                            * get all variations
                            */
                            if ( !empty ( $products ) ) {
                                $products_with_vars = call_user_func_array ( 
                                    array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                                    array ( $products ) 
                                );
                            }
                        }
                        
                        $products_excluding_free    = array_diff ( $awdp_cart_ids, $products ); 

                        $checkCondition = call_user_func_array ( 
                            array ( new AWDP_Discount(), 'check_condition' ), 
                            array ( $condition, $item_price, $value ) 
                        );
                        $checkGiftPrice = call_user_func_array ( 
                            array ( new AWDP_Discount(), 'check_gift_price' ), 
                            array ( $products_excluding_free, $awdp_product_price, floatval ( $value ), $condition ) 
                        ); 

                        // if ( $checkCondition && $checkGiftPrice ) { // 5.0.4 - Check Condition
                        if ( $checkGiftPrice ) { 
                            $products_array = array_merge ( $products_array , $products ); 
                            $prodsincart    = !empty ($products_with_vars) ? array_intersect ( $products_with_vars, $awdp_cart_ids ) : array_intersect ( $products, $awdp_cart_ids );
                            
                            if ( $prodsincart && in_array ( $product_id, $prodsincart ) && ( sizeof($awdp_cart_ids) > $gift_numbers )  ) { 

                                // Skip Flag
                                $skipFlag = true;

                                foreach ( $prodsincart as $product ) { 
                                    $prod_price = $awdp_product_price[$product];
                                    $prod_slug  = $awdp_product_slug[$product];
                                    $variations = call_user_func_array ( 
                                        array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                                        array ( $product ) 
                                    ); 
                                    if ( $variations ) { 
                                        foreach ( $variations as $variation ) {
                                            if ( isset( $awdp_cart_ids ) && in_array( $variation, $awdp_cart_ids ) && sizeof($wdpGiftsinCart) < $gift_numbers ) {
                                                $wdpGiftsinCart[] = array ( 
                                                    'product_id' => $variation, 
                                                    'discount'  => $prod_price, 
                                                    'quantity'  => $gift_numbers, 
                                                    'price'     => $prod_price, 
                                                    'slug'      => $prod_slug,
                                                    'actual_qn' => $awdp_cart_quanity[$variation]  
                                                );
                                            }
                                        }
                                    } else {  
                                        $occurence = array_count_values($awdp_cart_ids); 
                                        // if ( isset( $awdp_cart_ids ) && sizeof($awdp_cart_ids) > $gift_numbers && in_array( $product, $awdp_cart_ids ) && sizeof($wdpGiftsinCart) < $gift_numbers ) { // 5.0.4 - Removed sizeof($awdp_cart_ids) > $gift_numbers check - gifts get applied only if no of items in cart greater than gifts count
                                        if ( isset( $awdp_cart_ids ) && in_array( $product, $awdp_cart_ids ) && sizeof($wdpGiftsinCart) < $gift_numbers ) { 
                                            $wdpGiftsinCart[] = array ( 
                                                'product_id' => $product, 
                                                'discount'  => $prod_price, 
                                                'quantity'  => $gift_numbers, 
                                                'price'     => $prod_price, 
                                                'slug'      => $prod_slug,
                                                'actual_qn' => $awdp_cart_quanity[$product]  
                                            );
                                        }
                                    }
                                }

                            }
                        }

                    }
                    
                    $products_array = array_unique($products_array); 

                }
                // End Gift - Product Price ///////////////////////////////////////

            } else if ( 'gift_category' == $gift_type ) { 

                // Free Gift if Purchased From a Selected Category
                if ( $gift_combinations ) {

                    foreach ( $gift_combinations as $gift_combination ) {

                        $products_with_vars = [];
                        $category           = $gift_combination['category'];
                        $condition          = $gift_combination['condition']; 
                        $products           = array_key_exists ( 'products', $gift_combination ) ? $gift_combination['products'] : []; 

                        /*
                        * ver@ 5.0.3
                        * get all variations
                        */
                        if ( !empty ( $products ) ) {
                            $products_with_vars = call_user_func_array ( 
                                array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                                array ( $products ) 
                            );
                        }

                        $checkCategory  = call_user_func_array ( 
                            array ( new AWDP_Discount(), 'check_in_category' ), 
                            array ( $condition, $category, $awdp_cart_ids ) 
                        ); 

                        if ( $checkCategory ) { 
                            $products_array = array_merge ( $products_array , $products );
                            $prodsincart    = !empty($products_with_vars) ? array_intersect ( $products_with_vars, $awdp_cart_ids ) : array_intersect ( $products, $awdp_cart_ids );
                            
                            if ( $prodsincart && in_array ( $product_id, $prodsincart ) && ( sizeof($awdp_cart_ids) > $gift_numbers ) ) { 

                                // Skip Flag
                                $skipFlag = true;

                                foreach ( $prodsincart as $product ) { 
                                    $prod_price = $awdp_product_price[$product];
                                    $prod_slug  = $awdp_product_slug[$product];
                                    $variations = call_user_func_array ( 
                                        array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                                        array ( $product ) 
                                    );
                                    if ( $variations ) { // Check if variable
                                        foreach ( $variations as $variation ) { 
                                            if ( isset( $awdp_cart_ids ) && in_array( $variation, $awdp_cart_ids ) && sizeof($wdpGiftsinCart) < $gift_numbers ) {
                                                $wdpGiftsinCart[] = array ( 
                                                    'product_id' => $variation, 
                                                    'discount'  => $prod_price, 
                                                    'quantity'  => $gift_numbers, 
                                                    'price'     => $prod_price, 
                                                    'slug'      => $prod_slug,
                                                    'actual_qn' => $awdp_cart_quanity[$variation]  
                                                );
                                            }
                                        }
                                    } else { // Else
                                        $occurence = array_count_values($awdp_cart_ids);
                                        if ( isset( $awdp_cart_ids ) && in_array( $product, $awdp_cart_ids ) && sizeof($wdpGiftsinCart) < $gift_numbers ) { 
                                            $wdpGiftsinCart[] = array ( 
                                                'product_id' => $product, 
                                                'discount'  => $prod_price, 
                                                'quantity'  => $gift_numbers, 
                                                'price'     => $prod_price, 
                                                'slug'      => $prod_slug,
                                                'actual_qn' => $awdp_cart_quanity[$product]  
                                            );
                                        }
                                    }
                                }

                            }
                        }
                    }

                    $products_array = array_unique($products_array); 

                }
                // End Gift - Category Purchase ///////////////////////////////////////

            } else if ( 'gift_first_order' == $gift_type ) { 
                
                // Free Gift For User's First Order
                $checkUserFirstOrder = call_user_func_array ( 
                    array ( new AWDP_Discount(), 'user_first_order' ), 
                    array ( get_current_user_id() ) 
                );
                if ( is_user_logged_in() && $checkUserFirstOrder ) {
                    // get current user id
                    $current_user = wp_get_current_user();

                    if ( $gift_combinations ) {

                        foreach ( $gift_combinations as $gift_combination ) {

                            $products_with_vars = [];
                            $awdp_role          = $gift_combination['user_role'];
                            $user_role          = ( array ) $current_user->roles;
                            $products           = array_key_exists ( 'products', $gift_combination ) ? $gift_combination['products'] : []; 

                            /*
                            * ver@ 5.0.3
                            * get all variations
                            */
                            if ( !empty ( $products ) ) {
                                $products_with_vars = call_user_func_array ( 
                                    array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                                    array ( $products ) 
                                );
                            }

                            if( in_array($awdp_role, $user_role) ) { 
                                $products_array = array_merge ( $products_array , $products );
                                $prodsincart    = !empty($products_with_vars) ? array_intersect ( $products_with_vars, $awdp_cart_ids ) : array_intersect ( $products, $awdp_cart_ids );
                                
                                if ( $prodsincart && in_array ( $product_id, $prodsincart ) ) {

                                    // Skip Flag
                                    $skipFlag = true;

                                    foreach ( $prodsincart as $product ) { 
                                        $prod_price = $awdp_product_price[$product];
                                        $prod_slug  = $awdp_product_slug[$product];
                                        $variations = call_user_func_array ( 
                                            array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                                            array ( $product ) 
                                        );
                                        if ( $variations ) {
                                            foreach ( $variations as $variation ) { 
                                                if ( isset( $awdp_cart_ids ) && in_array( $variation, $awdp_cart_ids ) && sizeof($wdpGiftsinCart) < $gift_numbers ) {
                                                    $wdpGiftsinCart[] = array ( 
                                                        'product_id' => $variation, 
                                                        'discount'  => $prod_price, 
                                                        'quantity'  => $gift_numbers, 
                                                        'price'     => $prod_price, 
                                                        'slug'      => $prod_slug,
                                                        'actual_qn' => $awdp_cart_quanity[$variation]  
                                                    );
                                                }
                                            }
                                        } else {
                                            $occurence = array_count_values($awdp_cart_ids);
                                            if ( isset( $awdp_cart_ids ) && in_array( $product, $awdp_cart_ids ) && sizeof($wdpGiftsinCart) < $gift_numbers ) {
                                                $wdpGiftsinCart[] = array ( 
                                                    'product_id' => $product, 
                                                    'discount'  => $prod_price, 
                                                    'quantity'  => $gift_numbers, 
                                                    'price'     => $prod_price, 
                                                    'slug'      => $prod_slug,
                                                    'actual_qn' => $awdp_cart_quanity[$product]  
                                                );
                                            }
                                        }
                                    }

                                }
                            }
                        }

                        $products_array = array_unique($products_array); 

                    }
                }
                // End Gift - On First Order ///////////////////////////////////////

            } else if ( 'gift_combinations' == $gift_type ) { 

                // Gift Combinations
                if ( $gift_combinations ) { 

                    foreach ( $gift_combinations as $gift_combination ) { 
                        
                        $gifts_purchase = $gift_combination['from_list']; 
                        $free_gifts     = $gift_combination['to_list']; 
                        $list_key       = 'list_';
                        $gift_from_list = $gift_from_items = []; 

                        if( empty($gift_combination['from_list']) || ( !empty($gift_combination['from_list']) && ( $gift_combination['from_list'][0] == 'null' || $gift_combination['from_list'][0] == '' ) ) ) { // Any Product
                            // Skip when from list = null (any prodcut)
                            $gift_from_items = array_merge ( $gift_from_items, $all_products);
                            continue;
                        } else {
                            $gifts_purchase_items = $gift_combination['from_list']; 
                            foreach ( $gifts_purchase_items as $gifts_purchase_item ) {
                                if ( strpos ( $gifts_purchase_item, $list_key ) === false ) {
                                    // get variations
                                    $gift_from_items[] = $gifts_purchase_item;
                                } else { 
                                    $list_id            = intval ( str_replace ( $list_key, '', $gifts_purchase_item ) );
                                    $list_prods         = call_user_func_array ( 
                                        array ( new AWDP_Discount(), 'wdpProductList' ), 
                                        array ( $list_id ) 
                                    );
                                    $gift_from_items    = array_unique ( array_merge ( $list_prods, $gift_from_items ) ); 
                                }
                            }
                        } 

                        $gift_from_items = array_values ( array_unique ( $gift_from_items ) );

                        foreach ( $gift_from_items as $gift_from_item ) { // Get Variations
                            // $gift_cart = wc_get_product($gift_from_item);
                            $variations = call_user_func_array ( 
                                array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                                array ( $gift_from_item ) 
                            );
                            $gift_cart = call_user_func_array ( 
                                array ( new AWDP_Discount(), 'wdpCheckVariation' ), 
                                array ( $gift_from_item ) 
                            ); 

                            if ( $variations ) {
                                $gift_from_list = array_merge ( $gift_from_list, $variations );
                            } else if ( $gift_cart ) {
                                $gift_from_list = array_merge ( $gift_from_list , $gift_cart );
                            } 

                            $gift_from_list[] = $gift_from_item;
                            // }
                        }
                        $gift_from_list = array_values(array_unique($gift_from_list));  

                        // Cart Items
                        if ( array_intersect( $awdp_cart_ids, $gift_from_list ) ) { 

                            $free_gifts_var = call_user_func_array ( 
                                array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                                array ( $free_gifts ) 
                            ); 

                            $free_gifts         = $free_gifts_var ? array_merge ( $free_gifts_var, $free_gifts ) : $free_gifts;
                            $products_array     = array_merge ( $products_array, $free_gifts );
                            $prodsincart        = array_intersect ( $free_gifts, $awdp_cart_ids ); // changed $products array to $free_gifts
                            
                            $giftSuggestionsFlag = sizeof ( array_intersect ( $awdp_cart_ids, array_intersect ( $free_gifts, $gift_from_list ) ) ) >= 1;

                            if ( $prodsincart && in_array ( $product_id, $prodsincart ) && ( sizeof ( array_intersect ( $awdp_cart_ids, array_intersect ( $free_gifts, $gift_from_list ) ) ) > 1 ) ) {

                                // Skip Flag
                                $skipFlag = true;

                                foreach ( $prodsincart as $product ) { 
                                    $prod_price = $awdp_product_price[$product];
                                    $prod_slug  = $awdp_product_slug[$product];
                                    $variations = call_user_func_array ( 
                                        array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                                        array ( $product ) 
                                    );
                                    if ( $variations ) {  // Check for variable products
                                        foreach ( $variations as $variation ) { 
                                            $free_prod_slug = get_post_field( 'post_name', $variation ); 
                                            if ( isset ( $awdp_cart_ids ) && in_array ( $variation, $awdp_cart_ids ) && ( sizeof($wdpGiftsinCart) < $gift_numbers ) ) {
                                                $wdpGiftsinCart[] = array ( 
                                                    'product_id' => $variation, 
                                                    'discount'  => $prod_price, 
                                                    'quantity'  => $gift_numbers, 
                                                    'price'     => $prod_price, 
                                                    'slug'      => $prod_slug,
                                                    'actual_qn' => $awdp_cart_quanity[$variation]  
                                                );
                                            }
                                        }
                                    } 
                                    // else {
                                        $occurence = array_count_values($awdp_cart_ids);
                                        if ( isset ( $awdp_cart_ids ) && in_array ( $product, $awdp_cart_ids ) && ( sizeof ($wdpGiftsinCart) < $gift_numbers ) ) {
                                            $wdpGiftsinCart[] = array ( 
                                                'product_id' => $product, 
                                                'discount'  => $prod_price, 
                                                'quantity'  => $gift_numbers, 
                                                'price'     => $prod_price, 
                                                'slug'      => $prod_slug,
                                                'actual_qn' => $awdp_cart_quanity[$product]  
                                            );
                                        }
                                    // }
                                }

                            }

                        }
                    } 
                
                    $products_array = array_unique($products_array); 

                }
                // End Gift - Combinations ///////////////////////////////////////

            } else if ( 'gift_any_product' == $gift_type ) { 
                // Free Gift For any Products added to Cart
                if ( !empty ( $awdp_cart_ids ) && isset( $gift_products ) ) { 

                    // Skip Flag
                    $skipFlag = true;

                    foreach ( $gift_products as $product ) { 

                        $products_array[]   = $product; 
                        $variations         = call_user_func_array ( 
                            array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                            array ( $product ) 
                        ); 

                        if ( ( !in_array ( $product, $awdp_cart_ids ) && !$variations ) || ( $variations && empty ( array_intersect ( $variations, $awdp_cart_ids ) ) ) ) { 
                            continue;
                        } 

                        $prod_price         = array_key_exists ( $product, $awdp_product_price ) ? $awdp_product_price[$product] : '';
                        $prod_slug          = array_key_exists ( $product, $awdp_product_slug ) ? $awdp_product_slug[$product] : '';

                        if ( $variations ) { 
                            foreach ( $variations as $variation ) { 
                                $free_prod_slug = get_post_field( 'post_name', $variation ); 
                                $prod_price     = array_key_exists ( $variation, $awdp_product_price ) ? $awdp_product_price[$variation] : '';
                                $prod_slug      = array_key_exists ( $variation, $awdp_product_slug ) ? $awdp_product_slug[$variation] : '';
                                if ( isset( $awdp_cart_ids ) && in_array( $variation, $awdp_cart_ids ) && sizeof($wdpGiftsinCart) < $gift_numbers ) {
                                    $wdpGiftsinCart[] = array ( 
                                        'product_id' => $variation, 
                                        'discount'  => $prod_price, 
                                        'quantity'  => $gift_numbers, 
                                        'price'     => $prod_price, 
                                        'slug'      => $prod_slug,
                                        'actual_qn' => $awdp_cart_quanity[$variation]  
                                    );
                                }
                            }
                        } else {
                            $occurence = array_count_values($awdp_cart_ids);
                            if ( isset( $awdp_cart_ids ) && in_array( $product, $awdp_cart_ids ) && sizeof($wdpGiftsinCart) < $gift_numbers ) {
                                $wdpGiftsinCart[] = array ( 
                                    'product_id' => $product, 
                                    'discount'  => $prod_price, 
                                    'quantity'  => $gift_numbers, 
                                    'price'     => $prod_price, 
                                    'slug'      => $prod_slug,
                                    'actual_qn' => $awdp_cart_quanity[$product]  
                                );
                            }
                        }

                    }

                }
                // End Gift - On Any Product ///////////////////////////////////////

            } else if ( 'gift_spent' == $gift_type ) { 
                
                // Free Gifts based on Total Amount Spent By the User
                $current_user = get_current_user_id();
                $amount_spent = wc_get_customer_total_spent($current_user); 

                if ( $gift_combinations ) {

                    foreach ( $gift_combinations as $gift_combination ) {
                        $rule_spent = array_key_exists ( 'spent', $gift_combination ) ? $gift_combination['spent'] : 0;
                        $products   = array_key_exists ( 'products', $gift_combination ) ? $gift_combination['products'] : [];
                        $variations = call_user_func_array ( 
                            array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                            array ( $products ) 
                        ); 
                        if ( $amount_spent > $rule_spent ) { 
                            $products       = $variations ? array_merge ( $products, $variations ) : $products;
                            $products_array = isset ( $products ) ? array_merge ( $products, $products_array ) : $products_array;
                        }
                    }
                    $prodsincart    = array_intersect ( $products_array, $awdp_cart_ids );

                    if ( $prodsincart && in_array ( $product_id, $prodsincart ) && ( sizeof($awdp_cart_ids) > $gift_numbers ) ) { 

                        // Skip Flag
                        $skipFlag = true;

                        foreach ( $products_array as $product ) { 

                            $prod_price = array_key_exists ( $product, $awdp_product_price ) ? $awdp_product_price[$product] : '';
                            $prod_slug  = array_key_exists ( $product, $awdp_product_slug ) ? $awdp_product_slug[$product] : '';
                            $variations = call_user_func_array ( 
                                array ( new AWDP_Discount(), 'wdpGetVariations' ), 
                                array ( $product ) 
                            );

                            if ( $variations ) {
                                foreach ( $variations as $variation ) { 
                                    if ( isset( $awdp_cart_ids ) && in_array( $variation, $awdp_cart_ids ) && sizeof($wdpGiftsinCart) < $gift_numbers ) {
                                        $wdpGiftsinCart[] = array ( 
                                            'product_id' => $variation, 
                                            'discount'  => $prod_price, 
                                            'quantity'  => $gift_numbers, 
                                            'price'     => $prod_price, 
                                            'slug'      => $prod_slug,
                                            'actual_qn' => $awdp_cart_quanity[$variation]  
                                        );
                                    }
                                }
                            } else {
                                $occurence = array_count_values($awdp_cart_ids);
                                if ( isset( $awdp_cart_ids ) && in_array( $product, $awdp_cart_ids ) && sizeof($wdpGiftsinCart) < $gift_numbers ) {
                                    $wdpGiftsinCart[] = array ( 
                                        'product_id' => $product, 
                                        'discount'  => $prod_price, 
                                        'quantity'  => $gift_numbers, 
                                        'price'     => $prod_price, 
                                        'slug'      => $prod_slug,
                                        'actual_qn' => $awdp_cart_quanity[$product]  
                                    );
                                }
                            }

                        }

                    }

                }

            }

            $wdpGiftsinCart                         = array_values ( array_unique ( $wdpGiftsinCart, SORT_REGULAR ) );

            $offerMessage                           = $gift_description;
            $offerPrducts                           = $products_array;
            $offerAll                               = $all_flag ? true : false;

            $result['productDiscount']              = $discVariable;
            $result['wdpGiftsinCart']               = $wdpGiftsinCart;
            $result['offerMessage']                 = $offerMessage;
            $result['offerPrducts']                 = $offerPrducts;
            $result['offerAll']                     = $offerAll;
            $result['skipFlag']                     = $skipFlag;

            $result['giftItems']['giftsID']         = $offerPrducts;
            $result['giftItems']['giftsAddtoCart']  = $gift_auto_cart;
            $result['giftItems']['giftsCount']      = $gift_numbers; 
            $result['giftItems']['giftsinCart']     = sizeof($wdpGiftsinCart); 
            $result['giftItems']['suggestionsFlag'] = $giftSuggestionsFlag; 
        
        }

        return $result;

    } 

}
