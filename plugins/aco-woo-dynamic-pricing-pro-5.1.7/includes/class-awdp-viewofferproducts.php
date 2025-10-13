<?php

/*
* @@ Gift / Bogo Offer Listing 
* @@ Last updated version 4.0.0
* Wont work for cart based rules
* Disabled for BOGO rules
*/

class AWDP_viewOfferProducts
{

    public function show_products ( $rule, $productid, $price, $discVariable, $prodLists, $type )
    {  

        $result                     = [];

        if ( $type == 'gift' ) { 

            $products_array         = [];

            $gift_type              = $rule['gift_type'];

            $gift_cart_min          = $rule['gift_cart_min'];
            $gift_dis_cpn           = $rule['gift_dis_cpn'];
            $gift_mltpl             = $rule['gift_mltpl'];
            $gift_products          = $rule['gift_products']; // Free gift for any product in cart
            $gift_combinations      = $rule['gift_combinations'];

            $gift_auto_cart         = $rule['gift_add_cart'];
            $gift_numbers           = $rule['gift_numbers'] ? (int)$rule['gift_numbers'] : 1; // Default set to 1 @@ 3.1.0

            $awdp_free_count        = 1;

            $ruleID                 = $rule['id'];

            $all_flag               = false;
            $gift_description       = $rule['gift_off_msg']; 

            $item_price             = $price;

            $awdp_cart_ids          = $awdp_cart_quanity = $awdp_product_price = []; 

            // All Products
            $all_products           = $awdp_cart_ids ? $awdp_cart_ids : [];

            $cart_price[$productid] = $price;
            $cart_products[]        = $productid;

            // Sort
            if ( 'gift_product_amount' == $gift_type || 'gift_cart_item' == $gift_type || 'gift_cart_amount' == $gift_type ) {
                @array_multisort ( array_column ( $gift_combinations, "no_products" ), SORT_ASC, $gift_combinations );
            }

            // Gift Combinations Loop
            $awdp_cart_product_ids = $awdp_cart_ids;

            // Check
            if ( 'gift_product_amount' == $gift_type ) { 

                // Free Gifts based on Product Price 
                if ( $gift_combinations ) { 

                    foreach ( $gift_combinations as $gift_combination ) { 
                        $condition                  = $gift_combination['condition'];
                        $value                      = $gift_combination['no_products'];
                        $products                   = $gift_combination['products']; 
                        $products_excluding_free    = array_diff($cart_products, $products); 
                        $checkCondition = call_user_func_array ( 
                            array ( new AWDP_Discount(), 'check_condition' ), 
                            array ( $condition, $item_price, $value ) 
                        );
                        $checkGiftPrice = call_user_func_array ( 
                            array ( new AWDP_Discount(), 'check_gift_price' ), 
                            array ( $products_excluding_free, $cart_price, floatval ( $value ), $condition ) 
                        );
                        if ( $checkCondition && $checkGiftPrice ) { 
                            $products_array = array_merge ( $products_array , $products );
                        }
                    }
                    $products_array = array_unique($products_array); 
                } 
                // End Gift - Product Price ///////////////////////////////////////

            } else if ( 'gift_category' == $gift_type ) { 

                // Free Gift if Purchased From a Selected Category
                if ( $gift_combinations ) {
                    foreach ( $gift_combinations as $gift_combination ) {
                        $category       = $gift_combination['category'];
                        $condition      = $gift_combination['condition']; 
                        $products       = $gift_combination['products']; 
                        $checkCategory  = call_user_func_array ( 
                            array ( new AWDP_Discount(), 'check_in_category' ), 
                            array ( $condition, $category, $awdp_cart_ids ) 
                        );
                        if ( $checkCategory ) { 
                            $products_array = array_merge ( $products_array , $products );
                        }
                    }
                    $products_array = array_unique($products_array); 
                }

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
                            $awdp_role  = $gift_combination['user_role'];
                            $user_role  = ( array ) $current_user->roles;
                            $products   = $gift_combination['products']; 
                            if( in_array($awdp_role, $user_role) ) { 
                                $products_array = array_merge ( $products_array , $products );
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
                            $gift_from_items = array_merge ( $gift_from_items, $all_products);
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
                            $products_array = array_merge ( $products_array , $free_gifts );
                        }
                    } 
                    $products_array = array_unique($products_array);
                }
                // End Gift - Combinations ///////////////////////////////////////

            } else if ( 'gift_any_product' == $gift_type ) { 

                // Free Gift For any Products added to Cart
                if ( !empty ( $awdp_cart_ids ) && isset( $gift_products ) ) { 
                    foreach($gift_products as $product) { 
                        $products_array[]   = $product;
                    }
                }

            } 

            $offerPrducts                           = $products_array;
            $offerAll                               = $all_flag ? true : false;

            $result['giftItems']['giftsID']         = $offerPrducts;
            $result['giftItems']['giftsAddtoCart']  = $gift_auto_cart;
            $result['giftItems']['giftsCount']      = $gift_numbers; 

            // Gift View End

        } else if  ( $type == 'bogo' ) { 

            /*
            * BOGO Product Single Page Suggestions
            * @@ Version 4.0.0
            */

            $prod_ID                = $cart_item['data']->get_slug();  
            $awdp_prod_id           = $cart_item['data']->get_id();
            $ruleID                 = $rule['id'];
            $products_array         = [];
            $free_count             = 1;
            $awdp_cart_bogo_count   = 0;
            $BogoFlag               = false;

            // Rule details
            $awdp_qn_buy            = (int)$rule['bogo_buy'];
            $awdp_qn_get            = (int)$rule['bogo_get']; 
            $bogo_type              = $rule['bogo_type'];
            $bogo_value             = ( 'off_percentage' == $bogo_type ) ? (int)$rule['bogo_value'] : '' ;
            $bogo_combinations      = $rule['bogo_combinations'];
            $bogo_rule_type         = array_key_exists('bogo_rule_type', $rule) ? $rule['bogo_rule_type'] : '';

            // $bogo_y_all = array_key_exists('bogo_y_all', $rule) ? $rule['bogo_y_all'] : ''; // repeat disabled @ 3.2.1
            $bogo_y_all             = '';
            $awdp_in_cart           = [];
            $skip_id                = [];

            // Offer products falg
            $offer_flag             = true;
            $all_flag               = false;

            // Buy X get Y flag
            $awdp_BY_flag           = false;

            $awdp_cart_items        = $cartContents ? $cartContents : WC()->cart->get_cart();
            $awdp_products          = $awdp_cart_quanity = $awdp_product_price = [];

            // All Products
            $all_products = $awdp_products ? $awdp_products : []; // Set all products to cart items - 3.1.0

            $awdp_cart_prod_id  = ( $cart_item['data']->get_parent_id() == 0 ) ? $cart_item['data']->get_id() : $cart_item['data']->get_parent_id();
            $list_key           = 'list_';

            foreach ( $bogo_combinations as $bogo_combination ) {  

                $awdp_to_list       = empty ( $bogo_combination['to_list'] ) ? [] : $bogo_combination['to_list']; 
                $buy_limit          = 0;
                $awdp_BY_count      = 0;
                $awdp_BY_get_count  = 0;
                // $awdp_to_list = [];
                $awdp_from_list     = [];

                // $bogo_y_individual ? sizeof ( $awdp_cart_items ) : ;

                if ( empty($bogo_combination['to_list']) || ( !empty( $bogo_combination['to_list']) && ( $bogo_combination['to_list'][0] == 'null' || $bogo_combination['to_list'][0] == '' ) ) ) { // Any Product
                    $awdp_to_list   = $all_products; // Full products
                    $all_flag       = true;
                } else { // List / Product Selection
                    $awdp_to_list_items = $bogo_combination['to_list'];
                    foreach ( $awdp_to_list_items as $awdp_to_list_item ) {
                        if ( strpos ( $awdp_to_list_item, $list_key ) === false ) {
                            // get variations
                            $awdp_to_list[] = $awdp_to_list_item;
                        } else { // Check if product list
                            $list_id        = intval ( str_replace ( $list_key, '', $awdp_to_list_item ) );
                            $list_prods     = call_user_func_array ( 
                                array ( new AWDP_Discount(), 'wdpProductList' ), 
                                array ( $list_id ) 
                            );
                            $awdp_to_list   = array_unique ( array_merge ( $list_prods, $awdp_to_list ) );
                        }
                    }
                }

                if ( empty($bogo_combination['from_list']) || ( !empty( $bogo_combination['from_list']) && ( $bogo_combination['from_list'][0] == 'null' || $bogo_combination['from_list'][0] == '' ) ) ) { // Any Product
                    $awdp_from_list = $all_products; // Full products
                    $buy_limit = $awdp_cart_quanity; // Including individual project quantities
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
                } 

                $awdp_to_list   = array_values ( array_unique ( $awdp_to_list ) ); 
                $awdp_from_list = array_values ( array_unique ( $awdp_from_list ) );

                $awdp_intersect = array_values ( array_intersect ( $awdp_from_list, $awdp_products ) ); 
                $awdp_in_cart   = array_unique ( array_merge ( $awdp_intersect, $awdp_in_cart ) ); 
                
                // $awdp_qn = ( $bogo_combination['from_list'] == $bogo_combination['to_list'] ) ? ( $awdp_cart_quanity[$awdp_cart_prod_id] - 1 ) : $awdp_cart_quanity[$awdp_cart_prod_id]; // quantity - 1 when buy x get x
                $awdp_qn = ( $bogo_combination['from_list'] == $bogo_combination['to_list'] ) ? ( ( sizeof($awdp_in_cart) >= ( $awdp_qn_get + $awdp_qn_buy ) ) ? $awdp_qn_get : ( ( $awdp_qn_buy - $awdp_qn_get ) >= 0 ? ( $awdp_qn_buy - $awdp_qn_get ) : 0 ) ) : $awdp_cart_quanity[$awdp_cart_prod_id]; // @@@ 3.3.8

                if ( in_array( $awdp_cart_prod_id, $awdp_from_list ) && ( $awdp_qn >= $awdp_qn_buy || sizeof ( $awdp_in_cart ) >= $awdp_qn_buy ) && $awdp_qn > 0 ) { 

                    $wdpBogoIDs         = array_merge ( $wdpBogoIDs, $awdp_to_list );
                    $wdpBogoIDs         = array_unique(array_values($wdpBogoIDs));
                    $products_array     = $wdpBogoIDs; // New Products Array

                
                }

            }

            $offerMessage   = $bogo_description;
            $offerPrducts   = $products_array;
            $offerAll       = $all_flag ? true : false;

    
            $result['bogoItems']['itemsID']         = $offerPrducts;
            $result['bogoItems']['itemsCount']      = $offerAll; 
            $result['bogoItems']['itemsType']       = $bogo_type; 
            $result['bogoItems']['itemsValue']      = $bogo_value; 

        }

        return $result;

    }

}