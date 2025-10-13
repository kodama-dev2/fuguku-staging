<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Gift Suggestions Display
if ( !function_exists('awdp_gift_products') ) {

    function awdp_gift_products ( $ruleID, $products, $awdp_cart_ids, $gift_auto_cart, $gift_numbers, $singleListing = false, $suggestionsFlag = false )
    { 

        if ( isset($products) && !defined('AWDP_GIFT_'.$ruleID) ) { 
            
            $products_in_cart   = []; // cart product ids
            $suggestionList     = [];

            if ( WC()->cart ) {
                foreach ( WC()->cart->get_cart() as $cart_item ) {
                    array_push($products_in_cart, $cart_item['product_id']);
                }
            }

            define('AWDP_GIFT_'.$ruleID, true); // define gift variable
            $free_count = 0; // Free products count
            $quantity   = 1;
            $cart_array = ['Price' => 'Free'];

            $gift_config    = get_post_meta ( $ruleID, 'gift_config', true );
            $gift_items     = $gift_config['number_items'] ? $gift_config['number_items'] : 1;

            $gift_elg_msg   = get_option('awdp_gift_desc_cart');

            // Automatically add gifts to cart
            if ( isset($gift_auto_cart) && $gift_auto_cart === true && sizeof($products) == 1 ) { 
                $free_product = $products[0];
                if( isset(WC()->cart) && !in_array ($free_product, $products_in_cart) ) { // Check in cart
                    WC()->cart->add_to_cart( $free_product, $quantity, 0, 0, $cart_array );
                }
            }

            // Remove current product from gifts
            if ( is_single() ) {
                if ( ( $key = array_search( get_the_ID(), $products ) ) !== false ) {
                    unset( $products[$key] );
                }  
            } 

            $liCount    = 1;
            $products   = array_values($products);
            $free_title = get_option('awdp_gifttitle_cart') ? get_option('awdp_gifttitle_cart') : __("Eligible Free Gifts", "aco-woo-dynamic-pricing");
            
            $button = '<div class="awdp-free-gifts">'; 
            $button .= '<h3>'.$free_title.'</h3>';
            $button .= ($gift_elg_msg) ? '<p>'.$gift_elg_msg.'</p><ul class="products columns-4">' : '<ul class="products columns-4">';

            foreach ( $products as $product ) {  
                if( $singleListing || ( !in_array ( $product, $products_in_cart ) && isset ( WC()->cart ) ) ) { // Check in cart
                    $product_id     = $product;
                    $thumbID        = wp_get_post_parent_id ( $product ) != 0 ? wp_get_post_parent_id ( $product ) : $product_id;
                    $image          = get_the_post_thumbnail_url ( $thumbID , 'post-thumbnail' ) ? get_the_post_thumbnail_url ( $thumbID , 'post-thumbnail' ) : wc_placeholder_img_src();
                    $product_name   = get_the_title ( $product_id); 
                    $product_sku    = '';  
                    $getproduct     = wc_get_product($product_id);

                    // Boolean check @ ver 5.0.4
                    if ( ! $getproduct || $getproduct->get_parent_id() != 0 ) continue;
                        
                    // Suggestion List Array - Skip multiple entries
                    if ( in_array ( $product_id, $suggestionList ) ) {
                        continue;
                    } else {
                        $suggestionList[] = $product_id;
                    }

                    if ( ( ! $getproduct->managing_stock() && ! $getproduct->is_in_stock() ) || ( $getproduct->managing_stock() && $getproduct->get_stock_quantity() === 0 ) ) continue;

                    // $class = wc_product_class('wdpProduct', $product);
                    if ($liCount % 4 == 0) { $button .= '<li class="product last">';
                    } else { $button .= '<li class="product">'; }  

                    $button .= '<a href="'.get_the_permalink( $product_id ).'"><img src="'.$image.'" alt="'.$product_name.'" class="wdp-thumb" /><h2 class="woocommerce-loop-product__title">'.$product_name.'</h2></a>';

                    if ( $getproduct && $getproduct->is_type( 'variable' ) ) {
                        $button .= '<a href="'.get_the_permalink( $product_id ).'" class="button product_type_simple add_to_cart_button" rel="nofollow">'.__ ("Select Options", "aco-woo-dynamic-pricing").'</a>';
                    } else {
                        $button .= '<a href="'.get_home_url().'/?post_type='.AWDP_WC_PRODUCTS.'&amp;add-to-cart='.$product_id.'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.$product_id.'" data-product_sku="'.$product_sku.'" aria-label="Add â€œ'.$product_name.'â€ to your cart" rel="nofollow">'.__ ("Add to cart", "aco-woo-dynamic-pricing").'</a>';
                    }
                    
                    $button .= '</li>';

                    $free_count++;
                    $liCount++;
                }
            }

            $button .= '</ul></div>'; 

            echo ( !$suggestionsFlag && ( $free_count == 0 || ( ( $gift_items <= sizeof ( array_unique ( array_intersect ( $products_in_cart, $products ) ) ) ) && !$singleListing ) ) ) ? '' : $button; 
            
        }
    }

}

// Bogo Suggestions Display
if ( !function_exists('awdp_bogo_products') ) {

    function awdp_bogo_products( $ruleID, $products, $awdp_cart_ids, $bogo_type, $bogo_value, $singleListing = false )
    { 
        if ( isset($products) && !defined('AWDP_BOGO_'.$ruleID) ) { 

            define('AWDP_BOGO_'.$ruleID, true); // define gift variable
            $free_count     = 0; // Free products count
            $suggestionList = [];

            $bogo_items_nos = get_post_meta( $ruleID, 'discount_bogo_get', true );

            // Remove current product from gifts
            if ( is_single() ) {
                if ( ( $key = array_search( get_the_ID(), $products ) ) !== false ) {
                    unset( $products[$key] );
                }  
            }

            // if($products) { 
                
                $products = array_values($products); 
                $liCount = 1;

                $products_in_cart = []; // cart product ids
                if ( WC()->cart ) {
                    foreach ( WC()->cart->get_cart() as $cart_item ) {
                        // if( in_array( 'wdp_data', $cart_item) ) {
                            array_push($products_in_cart, $cart_item['product_id']);
                        // }
                    }
                }

                if ( get_option('awdp_bogotitle_cart') ) {
                    $title = '<h3>'.get_option('awdp_bogotitle_cart').'</h3>';
                    if ( get_option('awdp_bogo_desc_cart') )
                        $title .= '<p>'.get_option('awdp_bogo_desc_cart').'</p>';
                } else {
                    if ( 'free' == $bogo_type )
                        $title = '<h3>Free Products</h3>';
                    else if ( 'fixed' == $bogo_type )
                        $title = '<h3>Offer on following products</h3><p>'.wc_price($bogo_value) . ' discount on the following products</p>';
                    else 
                        $title = '<h3>Offer on following products</h3><p>'.$bogo_value . ' % discount on the following products</p>';
                } 
                    
                $button  = $title . '<div class="awdp-free-gifts">'; 
                $button .= '<ul class="products columns-4">';
                foreach ( $products as $product ) {  
                    if( $singleListing || ( !in_array ( $product, $products_in_cart ) && isset ( WC()->cart ) ) ) { // Check in cart
                        $product_id     = $product; 
                        $product_id     = wp_get_post_parent_id ( $product ) != 0 ? wp_get_post_parent_id ( $product ) : $product_id;
                        $product_name   = get_the_title($product_id); 
                        $image          = get_the_post_thumbnail_url( $product_id , 'post-thumbnail' ) ? get_the_post_thumbnail_url( $product_id , 'post-thumbnail' ) : wc_placeholder_img_src();
                        $product_sku    = '';   
                        $getproduct     = wc_get_product($product_id); 
                        
                        // Boolean check @ ver 5.0.4
                        if ( ! $getproduct || $getproduct->get_parent_id() != 0 ) continue;

                        // Suggestion List Array - Skip multiple entries
                        if ( in_array ( $product_id, $suggestionList ) ) {
                            continue;
                        } else {
                            $suggestionList[] = $product_id;
                        }
                        
                        if ( ( ! $getproduct->managing_stock() && ! $getproduct->is_in_stock() ) || ( $getproduct->managing_stock() && $getproduct->get_stock_quantity() === 0 ) ) continue;

                        if ($liCount % 4 == 0) { $button .= '<li class="product last">';
                        } else { $button .= '<li class="product">'; }  

                        $button .= '<a href="'.get_the_permalink( $product_id ).'"><img src="'.$image.'" alt="'.$product_name.'" class="wdp-thumb" /><h2 class="woocommerce-loop-product__title">'.$product_name.'</h2></a>';

                        if ( $getproduct->is_type( 'variable' ) ) {
                            $button .= '<a href="'.get_the_permalink( $product_id ).'" class="button product_type_simple add_to_cart_button" rel="nofollow">'.__ ("Select Options", "aco-woo-dynamic-pricing").'</a>';
                        } else {
                            $button .= '<a href="'.get_home_url().'/?post_type='.AWDP_WC_PRODUCTS.'&amp;add-to-cart='.$product_id.'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.$product_id.'" quan data-product_sku="'.$product_sku.'" aria-label="Add â€œ'.$product_name.'â€ to your cart" rel="nofollow">'.__ ("Add to cart", "aco-woo-dynamic-pricing").'</a>';
                        }

                        $button .= '</li>';
                        $free_count++;
                        $liCount++;
                    }
                }
                $button .= '</ul></div>'; 

                echo ( $free_count == 0 || $bogo_items_nos <= sizeof ( array_intersect ( $products_in_cart, $products ) ) ) ? '' : $button; 

            // }
        }
    }
    
}

// Check in bogo / gift list
if ( !function_exists('awdp_bgcheck') ) {

    function awdp_bgcheck ( $itemsinCart, $productid )
    {

        foreach ( $itemsinCart as $key => $value ) { 
 
            if ( array_search ( $productid, array_column ( $value, 'product_id' ) ) !== false ) {

                return true;

            }

        }

    }

}

// Dynamic Discount Value
if ( !function_exists('awdp_dynamic_value') ) {

    function awdp_dynamic_value( $rule, $item, $price, $quantity, $prodLists, $disc_prod_ID = false )
    {

        if ( isset($rule['rules']) && is_array($rule['rules']) && !empty($rule['rules']) ) {

            $product_lists      = $prodLists ? $prodLists : [];  
            $list_id            = ( array_key_exists ( 'product_list', $rule ) && $rule['product_list'] ) ? $rule['product_list'] : '';
            $rulesArray         = $rule['rules'];

            // Initialise
            $wdp_cart_totals = $wdp_cart_items = $wdp_cart_quantity = $wdp_cart_quantity_pl = $wdp_cart_totals_pl = $wdp_cart_items_pl = 0;

            // Sort Based on discount
            usort ( $rulesArray, function($a, $b) {
                if ( $b['discount'] != '' && $a['discount'] != '' )
                    return $b['discount'] - $a['discount']; 
            } );

            $item               = $disc_prod_ID ? wc_get_product ( $disc_prod_ID ) : $item;
            // $evel_str           = '';

            // Checking if Product List is Active
            if ( isset ( WC()->cart ) && WC()->cart->get_cart_contents_count() > 0 ) {

                // Checkout page ajax loading fix 
                $cart_items = is_checkout() ? ( WC()->session->get('WDP_Cart') ? WC()->session->get('WDP_Cart') : WC()->cart->get_cart() ) : WC()->cart->get_cart(); 
    
                // Product List
                $applicable_products    = ( $list_id && $list_id != 'null' ) ? ( !empty ( $product_lists ) && array_key_exists ( $list_id, $product_lists ) ? $product_lists[$list_id] : [] ) : [];
    
                if ($cart_items) {
                    foreach ( $cart_items as $cart_item ) {
                        // $product_data       = $cart_item['data']->get_data();
                        // $wdp_cart_totals    = $wdp_cart_totals + $product_data['price'] * $cart_item['quantity'];
                        $wdp_cart_totals    = $wdp_cart_totals + $cart_item['data']->get_price() * $cart_item['quantity'];
                        $wdp_cart_items     = $wdp_cart_items + $cart_item['quantity'];
                        $wdp_cart_quantity  = $wdp_cart_quantity + 1;
                        // check Product List
                        if ( !empty ( $applicable_products ) && in_array ( $cart_item['product_id'], $applicable_products ) ) { 
                            $wdp_cart_totals_pl    = $wdp_cart_totals_pl + $cart_item['data']->get_price() * $cart_item['quantity'];
                            $wdp_cart_items_pl     = $wdp_cart_items_pl + $cart_item['quantity'];
                            $wdp_cart_quantity_pl  = $wdp_cart_quantity_pl + 1;
                        }
                    }
                }
    
            }

            $eval_array     = [];
            $eval_flag      = true;
            $eval_index     = 0;
            $eval_max_index = sizeof ( $rule['rules'] );

            foreach ( $rulesArray as $val ) {

                if ( !empty($val['rules']) && is_array($val['rules']) && count($val['rules']) ) {

                    $evel_str   = '';
                    $operator   = '';

                    $val_rules  = array_values ( array_filter( $val['rules'] ) ); 
                    $dynmcDisc  = array_key_exists ( 'discount', $val ) ? $val['discount'] : ''; 

                    if ( !$dynmcDisc ) continue;

                    foreach ( $val_rules as $rul ) { 

                        $evel_str .= '(';

                        if ( $rul['rule']['value'] != '' ) {
                            if ( awdp_combinations ( $rul, $item, $list_id, $product_lists, $wdp_cart_totals, $wdp_cart_items, $wdp_cart_quantity, $wdp_cart_totals_pl, $wdp_cart_items_pl, $wdp_cart_quantity_pl ) ) { 
                                // $evel_str .= ' true ';
                                $eval_flag = ( $operator ) ? ( ( strtolower($operator) == 'and' ) ? ( ( $eval_flag && true ) ? 1 : 0 ) : 1 ) : 1; 
                            } else { 
                                // $evel_str .= ' false ';
                                $eval_flag = ( $operator ) ? ( ( strtolower($operator) == 'or' ) ? ( ( $eval_flag || false ) ? 1 : 0 ) : 0 ) : 0;
                            }
                        } else {
                            // $evel_str .= ' true ';
                            $eval_flag = ( $operator ) ? ( ( strtolower($operator) == 'and' ) ? ( ( $eval_flag && true ) ? 1 : 0 ) : 1 ) : 1; 
                        }

                        // $evel_str .= ') ' . (($rul['operator'] !== false) ? $rul['operator'] : '') . ' ';
                        $operator   = ($rul['operator'] !== false) ? $rul['operator'] : '';
                        
                    }

                    $eval_array[$eval_index]['flag']        = $eval_flag;
                    $eval_array[$eval_index]['operator']    = ( $eval_max_index == ( $eval_index + 1 ) ) ? '' : ( ($val['operator'] !== false) ? $val['operator'] : 'skip' );
                    $eval_index++;

                    // if ( count($val['rules']) > 0 && !empty($val['rules']) ) {
                    //     preg_match_all('/\(.*\)/', $evel_str, $match);
                    //     $evel_str = $match[0][0] . ' ';
                    // }

                    // $evel_str = str_replace(['and', 'or'], ['&&', '||'], strtolower($evel_str));

                    if ( !empty ( $eval_array ) ) {

                        $check_optr = ''; 
                        array_multisort ( array_column ( $eval_array, "operator" ), SORT_ASC, $eval_array ); 
        
                        foreach ( $eval_array as $eval_single ) {  
        
                            if ( $check_optr ) {
                                $rules_flag = ( $check_optr == 'and' ) ? ( ( $rules_flag && $eval_single['flag'] ) ? true : false ) : ( ( $rules_flag || $eval_single['flag'] ) ? true : false ); 
                            } else {
                                $rules_flag = ( $rules_flag || $eval_single['flag'] ) ? true : $eval_single['flag'];
                            }
                            
                            $check_optr = ( strtolower ( $eval_single['operator'] ) == 'and' || strtolower ( $eval_single['operator'] ) == 'or' ) ? strtolower ( $eval_single['operator'] ) : '';
        
                        }
                        
                    }
                            
                    // if ( eval ( 'return ' . $evel_str . ';' ) ) {

                    //     return $dynmcDisc;

                    // }

                    if ( $rules_flag ) {

                        return $dynmcDisc;

                    }

                }
                
            }

        }

    }

}

// Dynamic Discount Combinations
if ( !function_exists('awdp_combinations') ) {

    function awdp_combinations ( $rul, $item, $list_id, $product_lists, $wdp_cart_totals, $wdp_cart_items, $wdp_cart_quantity, $wdp_cart_totals_pl, $wdp_cart_items_pl, $wdp_cart_quantity_pl )
    {

        $ruleItem       = $rul['rule']['item'];
        $ruleVal        = $rul['rule']['value'];
        $rulCond        = $rul['rule']['condition'];
        $selectValue    = $rul['rule']['selectValue'];

        // $operator = $rul["operator"];

        if ( 'cart_total_amount' == $ruleItem ) { 

            // Check if cart is empty
            if ( !isset (WC()->cart) || $wdp_cart_quantity_pl == 0 || !did_action('woocommerce_before_calculate_totals') ) 
                return false;

            $item_val   = $wdp_cart_totals_pl;
            $rel_val    = (float)$ruleVal;

        } else if ( 'cart_total_amount_all_prods' == $ruleItem ) { 

            // Check if cart is empty
            if ( !isset (WC()->cart) || $wdp_cart_totals == 0 || !did_action('woocommerce_before_calculate_totals') ) 
                return false;

            $item_val   = $wdp_cart_totals;
            $rel_val    = (float)$ruleVal;

        } else if ( 'product_price' == $ruleItem ) {

            if(is_object($item)){
                $item_val = (float)$item->get_price();
            } else {
                $item_val = (float)$item['data']->get_price();
            }
            $rel_val    = (float)$ruleVal;

        } else if ( 'cart_items' == $ruleItem ) {

            // Check if cart is empty
            if ( !isset ( WC()->cart ) || $wdp_cart_quantity_pl == 0 || !did_action('woocommerce_before_calculate_totals') ) return false;

            $item_val   = $wdp_cart_items_pl;
            $rel_val    = (float)$ruleVal;

        } else if ( 'cart_items_all_prods' == $ruleItem ) {

            // Check if cart is empty
            if ( !isset ( WC()->cart ) || $wdp_cart_quantity == 0 || !did_action('woocommerce_before_calculate_totals') ) return false;

            $item_val   = $wdp_cart_items;
            $rel_val    = (float)$ruleVal;

        } else if ( 'cart_products' == $ruleItem ) {

            // Check if cart is empty
            if ( !isset ( WC()->cart ) || $wdp_cart_quantity == 0 || !did_action('woocommerce_before_calculate_totals') ) return false;

            $item_val   = $wdp_cart_quantity;
            $rel_val    = (float)$ruleVal;

        } else if ( 'cart_products_list' == $ruleItem ) {

            // Check if cart is empty
            if ( !isset ( WC()->cart ) || $wdp_cart_quantity_pl == 0 || !did_action('woocommerce_before_calculate_totals') ) return false;

            $item_val   = $wdp_cart_quantity_pl;
            $rel_val    = (float)$ruleVal;

        } else if ( 'number_orders' == $ruleItem ) { 

            if ( !is_user_logged_in() ) return false; // check if user logged in

            // Customer Orders
            $customer_orders = get_posts( array(
                'numberposts' => -1,
                'fields'      => 'ids',
                'meta_key'    => '_customer_user',
                'meta_value'  => get_current_user_id(),
                'post_type'   => wc_get_order_types(),
                'post_status' => array_keys( wc_get_order_statuses() ),
            ) );
            $number_of_orders = count($customer_orders);

            $item_val = $number_of_orders;
            $rel_val = (float)$ruleVal; 

        } else if ( 'amount_spent' == $ruleItem ) { 

            if ( !is_user_logged_in() ) return false; // check if user logged in

            $item_val = wc_get_customer_total_spent(get_current_user_id());
            $rel_val = (float)$ruleVal; 

        } else if ( 'last_order' == $ruleItem ) { 

            if ( !is_user_logged_in() ) return false; // check if user logged in

            // Customer Orders
            $customer_order = get_posts( array(
                'numberposts' => 1,
                'meta_key'    => '_customer_user',
                'meta_value'  => get_current_user_id(),
                'post_type'   => wc_get_order_types(),
                'post_status' => array( 'wc-completed' )
            ) );

            // Last Order Amount
            if ( $customer_order ) {
                $order = wc_get_order( $customer_order[0] );
                $last_order_amount = $order ? $order->get_total() : 0;
            } else {
                $last_order_amount = 0;
            }

            $item_val = $last_order_amount;
            $rel_val = (float)$ruleVal; 

        } else if ( 'previous_order' == $ruleItem ) { // Previous order contains / not contains any product

            if ( !is_user_logged_in() ) return false; // check if user logged in

            // Customer Orders
            $customer_orders = get_posts( array(
                'numberposts' => -1,
                'meta_key'    => '_customer_user',
                'meta_value'  => get_current_user_id(),
                'post_type'   => wc_get_order_types(),
                'post_status' => array( 'wc-processing', 'wc-completed' )
            ) );

            $rel_val = $selectValue;
            $item_val = [];

            if ($customer_orders) {
                foreach ( $customer_orders as $customer_order ) {
                    $order = wc_get_order( $customer_order ); 
                    foreach ( $order->get_items() as $item_id => $item_data ) {
                        $product = $item_data->get_product();
                        $item_val[] = $product->get_id();
                    }
                }
            }

            $item_val = array_values(array_unique($item_val));
            if ( 'contains' == $rulCond && in_array($rel_val, $item_val) ) {
                return true;
            } else if ( 'not_contains' == $rulCond && !in_array($rel_val, $item_val) ) {
                return true;
            }

            return false;

        } else if ( 'cart_user_role' == $ruleItem ) {   

            if ( $selectValue == '0' && 'equal_to' == $rulCond ) { 
                return true; 
            } else if ( $selectValue == '0' && 'not_equal' == $rulCond ) {
                return false;
            }
            
            /*
            * @ver 5.0.0 - Disabled user logged in check
            */
            // if ( !is_user_logged_in() ) return false; // check if user logged in

            $current_user = wp_get_current_user();
            $user_roles = ( array ) $current_user->roles;

            $item_val = $user_roles;
            $rel_val = $selectValue; 

            if ( 'equal_to' == $rulCond && in_array ( $rel_val, $item_val ) ) {
                return true;
            } else if ( 'not_equal' == $rulCond && !in_array ( $rel_val, $item_val ) ) {
                return true;
            }

            return false;

        } else if ( 'cart_user_selection' == $ruleItem ) { 

            if ( !is_user_logged_in() ) return false; // check if user logged in

            $current_user = wp_get_current_user();

            $item_val = $current_user->ID;
            $rel_val = $selectValue; 

            if ( intval($rel_val) == 0 ) {
                if ( 'equal_to' == $rulCond ) {
                    return true;
                } else if ( 'not_equal' == $rulCond ) {
                    return false;
                }
            } else {
                if ( 'equal_to' == $rulCond && $rel_val == $item_val ) {
                    return true;
                } else if ( 'not_equal' == $rulCond && $rel_val != $item_val ) {
                    return true;
                }
            }

            return false;

        } else if ( 'payment_method' == $ruleItem ) { 

            $item_val = WC()->session->get('awdpwcpaymentmethod') ? WC()->session->get('awdpwcpaymentmethod') : WC()->session->get('chosen_payment_method'); 
            $rel_val = $ruleVal; 

            if ( 'equal_to' == $rulCond && in_array( $item_val, $rel_val ) ) {
                return true;
            } else if ( 'not_equal' == $rulCond && !in_array( $item_val, $rel_val ) ) {
                return true;
            }

            return false;

        } else if ( 'shipment_method' == $ruleItem ) { 

            $item_val = WC()->session->get('awdpwcshippingmethod') ? WC()->session->get('awdpwcshippingmethod') : WC()->session->get('chosen_shipping_methods');  
            $rel_val = $ruleVal; 

            if ( 'equal_to' == $rulCond && $item_val != NULL && in_array( $item_val, $rel_val ) ) {
                return true;
            } else if ( 'not_equal' == $rulCond && $item_val != NULL && !in_array( $item_val, $rel_val ) ) {
                return true;
            }

            return false;

        } else if ( 'product_in_cart' == $ruleItem ) { // check product in cart

            if ( !isset ( WC()->cart ) || $wdp_cart_quantity == 0 || !did_action('woocommerce_before_calculate_totals') ) return false;

            $selectedProduct = $selectValue;
            $awdp_cart_items = WC()->cart->get_cart();
            $awdp_cart_ids = [];

            foreach( $awdp_cart_items as $awdp_cart_item ) { 
                // $cart_prod = $awdp_cart_item['variation_id'] ? $awdp_cart_item['variation_id'] : $awdp_cart_item['product_id'];
                $cart_prod = $awdp_cart_item['product_id'];
                array_push( $awdp_cart_ids, $cart_prod );
            } 

            if ( $rulCond == 'equals' && in_array( $selectedProduct, $awdp_cart_ids ) ) {
                return true;
            } else if ( $rulCond == 'not_equals' && !in_array( $selectedProduct, $awdp_cart_ids ) ) {
                return true;
            }

        } else if ( 'product_stock' == $ruleItem  ) {

            if ( is_object ( $item ) ) {
                $item_data  = $item->get_data();
                // $item_id    = $item->get_id();
                $item_val   = $item_data['stock_quantity'];
            } else if ( is_array ( $item ) ) {
                $item_data  = $item['data']->get_data();
                $item_val   = $item_data['stock_quantity'];
            }

            $rel_val    = (float)$rule['value'];

        } else {

            return false;

        }

        switch ($rulCond) {
            case 'equal_to':
                if ( $item_val > 0 && @abs(($item_val - $rel_val) / $item_val) < 0.00001 ) {
                    return true;
                }
                break;
            case 'less_than':
                if ($item_val < $rel_val) {
                    return true;
                }
                break;
            case 'less_than_eq':
                if ($item_val < $rel_val || ( $item_val > 0 && abs(($item_val - $rel_val) / $item_val) < 0.0001 ) ) {
                    return true;
                }
                break;
            case 'greater_than': 
                if ($item_val > $rel_val) { 
                    return true;
                }
                break;
            case 'greater_than_eq':
                if ($item_val > $rel_val || ( $item_val > 0 && abs(($item_val - $rel_val) / $item_val) < 0.0001 ) ) {
                    return true;
                }
                break;
        }

    }

}