<?php

/*
* @@ Gift / Bogo Offer Listing 
* @@ Last updated version 4.0.0
*/

class AWDP_viewOfferMessage
{

    public function show_message ( $rule, $productid, $price, $type, $rules, $productlists, $product )
    {  

        $result                 = '';
        $offer_desc_config      = get_option('awdp_offer_desc_config') ? get_option('awdp_offer_desc_config') : [];
        $offer_fontsize         = array_key_exists ( 'offer_fontsize', $offer_desc_config ) ? $offer_desc_config['offer_fontsize'] : 12;
        $offer_paddding_lm      = array_key_exists ( 'offer_paddding_lm', $offer_desc_config ) ? $offer_desc_config['offer_paddding_lm'] : 10;
        $offer_paddding_tp      = array_key_exists ( 'offer_paddding_tp', $offer_desc_config ) ? $offer_desc_config['offer_paddding_tp'] : 10;
        $offer_radius           = array_key_exists ( 'offer_radius', $offer_desc_config ) ? $offer_desc_config['offer_radius'] : 0;
        $offer_color            = array_key_exists ( 'offer_color', $offer_desc_config ) ? $offer_desc_config['offer_color'] : '';
        $offer_background       = array_key_exists ( 'offer_background', $offer_desc_config ) ? $offer_desc_config['offer_background'] : ''; 

        $offer_enabled          = array_key_exists ( 'enable_dismessage', $offer_desc_config ) ? $offer_desc_config['enable_dismessage'] : ''; 
        $offer_rule             = array_key_exists ( 'dismessage_rule', $offer_desc_config ) ? $offer_desc_config['dismessage_rule'] : ''; 
        $offer_desc             = array_key_exists ( 'dismessage', $offer_desc_config ) ? $offer_desc_config['dismessage'] : ''; 

        $border_top_width       = ( array_key_exists ( 'border_top_width', $offer_desc_config ) && $offer_desc_config['border_top_width'] != '' ) ? $offer_desc_config['border_top_width'].'px' : '0px'; 
        $border_right_width     = ( array_key_exists ( 'border_right_width', $offer_desc_config ) && $offer_desc_config['border_right_width'] != '' ) ? ' '.$offer_desc_config['border_right_width'].'px' : ' 0px'; 
        $border_bottom_width    = ( array_key_exists ( 'border_bottom_width', $offer_desc_config ) && $offer_desc_config['border_bottom_width'] != '' ) ? ' '.$offer_desc_config['border_bottom_width'].'px' : ' 0px'; 
        $border_left_width      = ( array_key_exists ( 'border_left_width', $offer_desc_config ) && $offer_desc_config['border_left_width'] != '' ) ? ' '.$offer_desc_config['border_left_width'].'px' : ' 0px'; 
        $offer_border_color     = array_key_exists ( 'offer_border_color', $offer_desc_config ) ? $offer_desc_config['offer_border_color'] : ''; 

        $customStyle            = 'font-size: '.$offer_fontsize.'px;padding: '.$offer_paddding_tp.'px '.$offer_paddding_lm.'px;border-radius: '.$offer_radius.'px;';
        $customStyle           .= $offer_color ? 'color: '.$offer_color.';' : '';
        $customStyle           .= $offer_background ? 'background: '.$offer_background.';' : ''; 
        $customStyle           .= $offer_border_color ? 'border-color: '.$offer_border_color.';' : ''; 
        $customStyle           .= ( $border_top_width || $border_right_width || $border_bottom_width || $border_left_width ) ? 'border-width: '.$border_top_width.$border_right_width.$border_bottom_width.$border_left_width.';' : ''; 

        $msgFlag                = false;

        $offer_desc_status      = array_key_exists ( 'offer_desc_status', $rule ) ? $rule['offer_desc_status'] : '';

        if ( $offer_desc_status ) { // Check Description Status @ 5.1.4
            
            // Gift
            if ( $type == 'gift' && trim ( $rule['gift_off_msg'] ) != '' ) { 

                $gift_type              = $rule['gift_type'];

                $gift_products          = $rule['gift_products']; // Free gift for any product in cart
                $gift_combinations      = $rule['gift_combinations'];

                $ruleID                 = $rule['id'];

                $gift_description       = trim ( $rule['gift_off_msg'] ); 

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
                                $msgFlag = true;
                            }
                        }
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
                                array ( $condition, $category, $cart_products ) 
                            );
                            if ( $checkCategory ) { 
                                $msgFlag = true;
                            }
                        }
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
                                    $msgFlag = true;
                                }
                            }
                        }
                    }
                    // End Gift - On First Order ///////////////////////////////////////

                } else if ( 'gift_combinations' == $gift_type ) { 

                    // Gift Combinations
                    if ( $gift_combinations ) {

                        foreach ( $gift_combinations as $gift_combination ) { 
                            $gifts_purchase = $gift_combination['from_list']; 
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

                            if ( empty ( $gift_from_items ) || in_array ( $productid, $gift_from_items ) ) { 
                                $msgFlag = true;
                            }
                        } 
                    }
                    // End Gift - Combinations ///////////////////////////////////////

                } else if ( 'gift_any_product' == $gift_type ) { 

                    // Free Gift For any Products added to Cart
                    if ( !empty ( $awdp_cart_ids ) && isset( $gift_products ) ) { 
                        $msgFlag = true;
                    }

                } 
                $result     = $msgFlag ? '<div class="awdpOfferMsg" style="display:none;"><span style="'.$customStyle.'">'.$gift_description.'</span></div>' : '';

            } else if  ( $type == 'bogo' && array_key_exists ( 'bogo_message', $rule ) && trim ( $rule['bogo_message'] ) != '' ) {

                /*
                * BOGO Product Single Page Suggestions
                * @@ Version 4.0.0
                */

                $ruleID                 = $rule['id'];

                $bogo_type              = $rule['bogo_type'];
                $bogo_combinations      = $rule['bogo_combinations'];
                $bogo_rule_type         = array_key_exists('bogo_rule_type', $rule) ? $rule['bogo_rule_type'] : '';
                $bogo_x_prods           = array_key_exists('bogo_x_prods', $rule) ? $rule['bogo_x_prods'] : [];
                $bogo_x_repeat          = array_key_exists('bogo_x_repeat', $rule) ? $rule['bogo_x_repeat'] : '';
                $bogo_n_prods           = array_key_exists('bogo_n_prods', $rule) ? $rule['bogo_n_prods'] : [];
                $bogo_n_count           = array_key_exists('bogo_n_count', $rule) ? $rule['bogo_n_count'] : '';
                $bogo_n_repeat          = array_key_exists('bogo_n_repeat', $rule) ? $rule['bogo_n_repeat'] : '';
                $bogo_n_loop            = array_key_exists('bogo_n_loop', $rule) ? $rule['bogo_n_loop'] : '';
                $bogo_payn_prods        = array_key_exists('bogo_payn_prods', $rule) ? $rule['bogo_payn_prods'] : [];
                $bogopayranges          = array_key_exists('bogopayranges', $rule) ? $rule['bogopayranges'] : '';
                $bogo_multitple_item    = array_key_exists('bogo_multitple_item', $rule) ? $rule['bogo_multitple_item'] : '';
                $bogo_cheap_get         = array_key_exists('bogo_cheap_get', $rule) ? $rule['bogo_cheap_get'] : '';
                $bogo_cheap_buy         = array_key_exists('bogo_cheap_buy', $rule) ? $rule['bogo_cheap_buy'] : '';
                $bogo_cheap_prods       = array_key_exists('bogo_cheap_prods', $rule) ? $rule['bogo_cheap_prods'] : '';
                $bogo_cheap_type        = array_key_exists('bogo_cheap_type', $rule) ? $rule['bogo_cheap_type'] : '';
                $bogo_cheap_value       = array_key_exists('bogo_cheap_value', $rule) ? $rule['bogo_cheap_value'] : '';
                $bogo_description       = array_key_exists('bogo_message', $rule) ? trim ( $rule['bogo_message'] ) : '';
                $bogo_x_all             = array_key_exists('bogo_x_all', $rule) ? $rule['bogo_x_all'] : '';

                // Offer products falg
                $offer_flag             = true;

                // All Products
                $all_products = []; 
                $list_key           = 'list_';

                if ( $bogo_rule_type == 'bogo_rule_same' ) { 

                    /*
                    * Buy X Get X (Same Product)
                    */

                    $bogo_x_list    = [];

                    if ( $bogo_x_prods == 'null' || $bogo_x_prods == '' ) { // Any Product
                        $bogo_x_list        = $all_products; // Full products
                    } else if ( strpos ( $bogo_x_prods, $list_key ) === false ) {
                        // get variations
                        $bogo_x_list[]      = $bogo_x_prods;
                    } else { // Check if product list
                        $list_id            = intval ( str_replace ( $list_key, '', $bogo_x_prods ) );
                        $list_prods         = call_user_func_array ( 
                            array ( new AWDP_Discount(), 'wdpProductList' ), 
                            array ( $list_id ) 
                        );
                        $bogo_x_list        = array_unique ( array_merge ( $list_prods, $bogo_x_list ) );
                    }

                    if ( empty ( $bogo_x_list ) || in_array ( $productid, $bogo_x_list ) ) { // in cart
                        $msgFlag = true;
                    }

                } else if ( $bogo_rule_type == 'bogo_rule_nth' ) { 

                    /*
                    * Buy X get discount on nth product
                    */

                    $bogo_n_list = [];

                    if ( $bogo_n_prods == 'null' || $bogo_n_prods == '' ) { // Any Product
                        $bogo_n_list    = $all_products; // Full products
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

                    if ( empty ( $bogo_n_list ) || in_array ( $productid, $bogo_n_list ) ) { // in cart
                        $msgFlag = true;
                    }

                } else if ( $bogo_rule_type == 'bogo_rule_payn' ) { // End Buy X pay for n items
                    
                    // Sort $bogopayranges with min_quantity
                    usort ( $bogopayranges, function ( $item1, $item2 ) { 
                        return $item2['min_qnty'] <=> $item1['min_qnty'];
                    });

                    $bogo_payn_list = [];

                    if ( $bogo_payn_prods == 'null' || $bogo_payn_prods == '' ) { // Any Product
                        $bogo_payn_list = $all_products; // Full products
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

                    if ( empty ( $bogo_payn_list ) || in_array ( $productid, $bogo_payn_list ) ) { // in cart
                        $msgFlag = true;
                    }

                } else if ( $bogo_rule_type == 'bogo_rule_cheapest' ) { 
                    
                    /**
                    * Chepeat Product In Cart
                    **/

                    $cheap_list = []; 

                    if ( $bogo_cheap_prods == '' || empty($bogo_cheap_prods) || ( !empty($bogo_cheap_prods) && ( $bogo_cheap_prods[0] == 'null' || $bogo_cheap_prods[0] == '' ) ) ) { // Any Product
                        $cheap_list = $all_products; // Full products
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

                    $cheap_list = array_values ( array_unique ( $cheap_list ) );

                    if ( empty ( $cheap_list ) || in_array ( $productid, $cheap_list ) ) { // in cart
                        $msgFlag = true;
                    }

                } else { 
                    
                    /*
                    * Buy X get Y Combinations (Default Options)
                    */

                    foreach ( $bogo_combinations as $bogo_combination ) {  

                        $awdp_from_list     = [];
                        $awdp_to_list       = [];

                        if ( empty($bogo_combination['to_list']) || ( !empty( $bogo_combination['to_list']) && ( $bogo_combination['to_list'][0] == 'null' || $bogo_combination['to_list'][0] == '' ) ) ) { // Any Product
                            $awdp_to_list   = $all_products; // Full products
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

                        $awdp_from_list = array_values ( array_unique ( $awdp_from_list ) );

                        if ( empty ( $awdp_from_list ) || in_array ( $productid, $awdp_from_list ) ) { 
                            $msgFlag = true;
                        }

                    }

                } 

                $result     = $msgFlag ? '<div class="awdpOfferMsg" style="display:none;"><span style="'.$customStyle.'">'.$bogo_description.'</span></div>' : '';

            } else if ( $type != 'common') {

                /**
                * ver @ 5.1.5
                * extending offer description to all rules
                **/

                $description            = trim ( $rule['gift_off_msg'] ); 

                $check_in_list          = call_user_func_array ( 
                    array ( new AWDP_Discount(), 'check_in_product_list' ), 
                    array ( $product, $rule ) 
                );

                if ( $description && $check_in_list ) { 
                    $result            .= '<div class="awdpOfferMsg" style="display:none;"><span style="'.$customStyle.'">'.$description.'</span></div>';
                }

            }  

        }
        
        // Common Description (Settings)
        if ( $type == 'common' && $offer_enabled != '' ) {

            $list_products = [];
            if ( $offer_rule == 'all_active' ) {
                foreach ( $rules as $k => $rule ) { 
                    $ruleID     = $rule['id'];
                    $prd_list   = get_post_meta ( $ruleID, 'discount_product_list', true ); 
                    if ( '' == $prd_list || 0 == $prd_list ) {
                        $all_prods  = true;
                        $offer_rule = '';
                        break;
                    }
                    $list_products = array_merge ( $list_products, $productlists[$prd_list] );
                }
                array_values ( array_unique ( $list_products ) ); 
            } else {
                $prd_list           = get_post_meta ( $offer_rule, 'discount_product_list', true );
                if ( $prd_list ) {
                    $list_products  = array_merge ( $list_products, $productlists[$prd_list] );
                } else { // Set to all_products when no product list is selected (pricing rule)
                    $offer_rule     = '';
                }
            }
            
            if ( $offer_desc && ( $offer_rule == '' || ( !empty ( $list_products ) && in_array ( $productid, $list_products ) ) ) ) { 

                $result            .= '<div class="awdpOfferMsg" style="display:none;"><span style="'.$customStyle.'">'.$offer_desc.'</span></div>';

            }

        }

        return $result;

    }

}