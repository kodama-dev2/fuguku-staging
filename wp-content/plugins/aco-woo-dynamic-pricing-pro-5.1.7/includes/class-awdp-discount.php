<?php

if (!defined('ABSPATH'))
    exit;

class AWDP_Discount
{

    /**
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $_instance = null;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */ 
    public $_version;
    public $product_lists               = false;
    public $awdp_cart_rules             = false;
    public $apply_wdp_coupon            = false;
    public $discountProductPrice        = '';
    public $discountProductMaxPrice     = '';
    public $discountProductMinPrice     = '';
    public $pricing_table               = [];
    public $productvariations           = [];
    public $couponLabel                 = '';
    public $wdp_discounted_price        = [];
    public $wdp_order_meta              = [];
    public $wdp_cart_meta_dp            = [];
    public $wdpPrice                    = [];
    public $wdpRuleIDs                  = [];
    public $wdpGifts                    = [];
    public $wdpBogoIDs                  = [];
    public $wdpGiftsinCart              = [];
    public $wdpBogoIDsinCart            = [];
    public $tieredIDsinCart             = [];
    public $wdpQNitems                  = [];
    public $wdpCartDicount              = [];
    public $awdp_cart_rule_ids          = [];
    public $awdp_discount_applied       = [];
    public $variations                  = [];
    public $variation_prods             = [];
    public $wdp_saleBadges              = [];
    public $actual_price                = [];
    public $giftItems                   = [];
    public $bogoItems                   = [];
    public $offer_message               = '';
    public $offer_prods                 = '';
    public $offer_all                   = '';
    public $priceFlag                   = false;
    public $ct_product_ids              = []; // Bogo 
    public $discounts                   = array();

    private $_active                    = false;
    private $types                      = array();
    private $discount_rules             = false;
    private $conversion_unit            = false;
    private $converted_rate             = '';
    private $cartDisplay                = array();

    public function __construct()
    {

        $this->types = Array(
            'percent_total_amount'  => __('Percentage of cart total amount', 'aco-woo-dynamic-pricing'),
            'percent_product_price' => __('Percentage of product price', 'aco-woo-dynamic-pricing'),
            'fixed_product_price'   => __('Fixed price of product price', 'aco-woo-dynamic-pricing'),
            'fixed_cart_amount'     => __('Fixed price of cart total amount', 'aco-woo-dynamic-pricing'),
            'cart_quantity'         => __('Quantity based discount', 'aco-woo-dynamic-pricing')
        );

    }

    /**
     *
     * Ensures only one instance of AWDP is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WordPress_Plugin_Template()
     * @return Main AWDP instance
     */
    public static function instance($file = '', $version = '1.0.0')
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($file, $version);
        }
        return self::$_instance;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->_active;
    }

    /*
    * @change get_price to woocommerce_before_calculate_totals
    * @since 4.0
    * @fix compatibility and quantity discount issues
    */
    /*
    * Changing Product slug check to cart item key - to be done
    * 
    */
    public function wdpCalculateDiscount ( $cartObject ) { 
        
        if ( $cartObject ) { 

            $cartContents   = $cartObject->cart_contents; 
            $result         = [];
            $inCartItemIDs  = [];

            //Gifts suggestions cart
            $suggetsionsPos     = get_option('awdp_gifts_cart_position') ? get_option('awdp_gifts_cart_position') : '';

            // Advanced Settings
            // $addition_settings  = get_option('awdp_addition_settings') ? get_option('awdp_addition_settings') : [];
            // $discount_method    = array_key_exists ( 'discount_method', $addition_settings ) ? $addition_settings['discount_method'] : 'coupon';

            // Disable Discount if any other gets added to the cart
            $disable_discount   = get_option('awdp_disable_discount') ? get_option('awdp_disable_discount') : '';
            $coupon             = get_option('awdp_fee_label') ? get_option('awdp_fee_label') : 'Discount';
            $coupon_code        = apply_filters('woocommerce_coupon_code', $coupon);
            if ( $disable_discount && !empty ( WC()->cart->get_applied_coupons() ) && !in_array ( $coupon_code, WC()->cart->get_applied_coupons() ) ) {
                return $cartObject;
            }

            // Load discount rules
            $this->load_rules();
            $this->apply_wdp_coupon = true; 

            // Check if discount is active
            if ( $this->discount_rules == null )
                return $cartObject; // Exit if no rules 

            /*
            * ver @ 5.0.5 
            * Tax Settings
            */
            $decimal_points     = get_option( 'woocommerce_price_num_decimals' ) ? get_option( 'woocommerce_price_num_decimals' ) : 2;
            $tax_display_mode   = get_option( 'woocommerce_tax_display_shop' );
            $pirce_include_tax  = get_option( 'woocommerce_prices_include_tax' ); 

            // CartContents Loop
            foreach ( $cartContents as $cartContent ) { 

                $prod_ID            = $cartContent['product_id'];
                $product            = wc_get_product ( $prod_ID );
                // $price          = $cartContent['data']->get_price();
                // $product_slug   = $product->get_data()['slug'];
                $product_slug       = $cartContent['data']->get_slug();
                $quantity           = $cartContent['quantity'];
                $variationID        = $cartContent['variation_id'];
                $variations         = $cartContent['variation'];
                $inCartItemIDs[]    = $cartContent['product_id'];

                $cartKey            = $cartContent['key'];
                $disc_prod_ID       = $variationID == 0 ? $prod_ID : $variationID;  

                $cartPrice          = $cartContent['data']->get_price(); 
                // Checking for addons price
                $addonPrice         = apply_filters('wcpa_cart_addon_data', false, $cartContent); 
                $dispPrice          = $addonPrice ? ( $addonPrice['totalPrice'] - $addonPrice['excludeFromDiscount'] ) : $cartPrice; 

                $BGFlag             = false;
                
                /*
                * ver @ 5.0.5
                * Get conversion rate 
                * Remove conversion from the coupon total
                */
                $this->converted_rate   = $this->get_con_unit($product, $cartContent['data']->get_price());

                foreach ( $this->discount_rules as $k => $rule ) { 

                    // Disable Discount for Deposit Items @ 5.1.7
                    $depositCheck       = get_post_meta($rule['id'], 'deposit_check', true) ? get_post_meta($rule['id'], 'deposit_check', true) : 0; 
                    if ( array_key_exists ( 'awcdp_deposit', $cartContent ) && $cartContent['awcdp_deposit'] && $depositCheck ) {
                        continue;
                    }

                    // Get Product List
                    if ( !$this->get_items_to_apply_discount ( $product, $rule, $disc_prod_ID, false, $product_slug ) ) {
                        continue;
                    } 
                    
                    // Check if User if Logged-In
                    if ( ( intval ( $rule['discount_reg_customers'] ) === 1 && !is_user_logged_in() && empty ( array_filter ( $rule['discount_reg_user_roles'] ) ) ) || ( intval ( $rule['discount_reg_customers'] ) === 1 && is_user_logged_in() && ( !empty ( array_filter ( $rule['discount_reg_user_roles'] ) ) && empty ( array_intersect ( $rule['discount_cur_user_roles'], $rule['discount_reg_user_roles'] ) ) ) ) ) { 
                        continue;
                    }

                    // Check Usage Limits
                    if ( !$this->check_usage_restrctions($rule) ) { 
                        continue;
                    }
    
                    // Validate Rules
                    if( 'cart_quantity' != $rule['type'] ) { // Skipping cart_quantity rule // 
                        if ( !$this->validate_discount_rules( $product, $rule, ['product_price', 'cart_total_amount', 'cart_total_amount_all_prods', 'cart_items', 'cart_items_all_prods', 'cart_products', 'cust_prev_order_count', 'cart_user_role', 'cart_user_selection', 'payment_method', 'shipment_method', 'number_orders', 'amount_spent', 'last_order', 'previous_order', 'product_in_cart', 'product_stock'], $cartContent ) ) { 
                            continue;
                        }
                    }
    
                    // Discounts Default Values
                    if ( !isset ( $this->discounts[$rule['id']] ) ) { 
                        $this->discounts[$rule['id']] = [ 'label' => $rule['label'], 'discount_type' => $rule['type'], 'discount_remainder' => -1, 'taxable' => false ];
                    } 
      
                    /*-----------------------------------------------------------------------------------*/
                    /*
                    * ver @ 5.0.9 
                    * If more than 2 decimal points, round to 2 decimal points
                    */
                    $priceIncTax        = ( ( strlen ( strrchr ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > $decimal_points ) ? round ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), $decimal_points ) : wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) );
                    $priceExcTax        = ( ( strlen ( strrchr ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > $decimal_points ) ? round ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), $decimal_points ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );
            
                    // $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );
                    $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? $priceIncTax : ( ( 'yes' === $pirce_include_tax && 'excl' === $tax_display_mode ) ? $priceExcTax : '' ); 
            
                    // $priceNew           = $priceAfterTax ? $priceAfterTax : $cartPrice; 
                    $disPrice           = $priceAfterTax ? $priceAfterTax : $cartPrice; 
                    /*-----------------------------------------------------------------------------------*/

                    $priceNew           = $cartPrice;

                    // Get Price
                    $discPrices         = $this->wdp_discounted_price;
                    $price              = !empty ($discPrices) ? ( ( array_key_exists ( $cartKey, $discPrices ) && $discPrices[$cartKey] != '' ) ? wc_remove_number_precision ( $discPrices[$cartKey] ) : $priceNew ) : $priceNew;  
        
                    $discVariable       = $this->discounts[$rule['id']]; 
                    $prodLists          = $this->product_lists;
                    $wdpGiftsinCart     = array_key_exists ( $rule['id'], $this->wdpGiftsinCart ) ? $this->wdpGiftsinCart[$rule['id']] : [];
                    $wdpBogoIDsinCart   = array_key_exists ( $rule['id'], $this->wdpBogoIDsinCart ) ? $this->wdpBogoIDsinCart[$rule['id']]: [];
                    $wdpBogoIDs         = $this->wdpBogoIDs;

                    // Disable Double Discount
                    if ( array_key_exists ( 'discounts', $this->discounts[$rule['id']] ) && array_key_exists ( $cartKey, $this->discounts[$rule['id']]['discounts'] ) ) {
                        continue;
                    }

                    // Discount Types
                    if ( 'percent_product_price' == $rule['type'] )
                        $result = call_user_func_array ( 
                            array ( new AWDP_typeProductPrice(), 'apply_discount_percent_product_price' ), 
                            array ( $rule, $product, $price, $quantity, $discVariable, $cartContent, $disc_prod_ID, $prodLists, $dispPrice, $disPrice ) 
                        );
                    else if ( 'fixed_product_price' == $rule['type'] )
                        $result = call_user_func_array ( 
                            array ( new AWDP_typeProductPrice(), 'apply_discount_fixed_product_price' ), 
                            array ( $rule, $product, $price, $quantity, $discVariable, $cartContent, $disc_prod_ID, $prodLists, $dispPrice, $disPrice ) 
                        );
                    else if ( 'percent_total_amount' == $rule['type'] )
                        $result = call_user_func_array ( 
                            array ( new AWDP_typeTotalAmount(), 'apply_discount_percent_total_amount' ), 
                            array ( $rule, $product, $price, $quantity, $discVariable, $cartContent, $disc_prod_ID, $dispPrice ) 
                        );
                    else if ( 'fixed_cart_amount' == $rule['type'] )
                        $result = call_user_func_array ( 
                            array ( new AWDP_typeTotalAmount(), 'apply_discount_fixed_price_total_amount' ), 
                            array ( $rule, $product, $price, $quantity, $discVariable, $cartContent, $disc_prod_ID, $dispPrice ) 
                        );
                    else if ( 'cart_quantity' == $rule['type'] )
                        $result = call_user_func_array ( 
                            array ( new AWDP_typeCartQuantity(), 'apply_discount_cart_quantity' ), 
                            array ( $rule, $product, $price, $quantity, $discVariable, $prodLists, $cartContents, $cartContent, $disc_prod_ID, $dispPrice, $disPrice ) 
                        );
                    else if ( 'bogo' == $rule['type'] ) 
                        $result = call_user_func_array ( 
                            array ( new AWDP_typeBogo(), 'apply_discount_bogo' ), 
                            array ( $rule, $product, $price, $quantity, $discVariable, $prodLists, $cartContents, $cartContent, $wdpBogoIDsinCart, $wdpBogoIDs, $discPrices, $disc_prod_ID, $dispPrice )
                        );
                    else if ( 'gift' == $rule['type'] )
                        $result = call_user_func_array ( 
                            array ( new AWDP_typeGift(), 'apply_discount_gift' ), 
                            array ( $rule, $product, $price, $quantity, $discVariable, $prodLists, $cartContents, $cartContent, $wdpGiftsinCart, $discPrices, $disc_prod_ID, $dispPrice ) 
                        );

                    if ( !empty($result) ) { 

                        $this->discounts[$rule['id']]                   = array_key_exists ( 'productDiscount', $result ) ? $result['productDiscount'] : $this->discounts[$rule['id']] ;
                        $this->discounted_products[]                    = $cartKey;
                        $this->wdp_discounted_price[$cartKey]           = array_key_exists ( 'discountedprice', $result ) ? $result['discountedprice'] : ( array_key_exists ( $cartKey, $discPrices ) ? $discPrices[$cartKey] : '' );

                        // Order Meta
                        $coupon                                         = get_option('awdp_fee_label') ? get_option('awdp_fee_label') : 'Discount';
                        $coupon_code                                    = apply_filters('woocommerce_coupon_code', $coupon);
                        $orderMetaData                                  = [];
                        $orderMetaData['type']                          = $rule['type'];
                        $orderMetaData['discount']                      = array_key_exists ( 'productDiscount', $result ) ? $result['productDiscount'] : [];
                        $orderMetaData['coupon']                        = $coupon_code;
                        $orderMetaData['discountedPrice']               = array_key_exists ( 'discountedprice', $result ) ? $result['discountedprice'] : ( array_key_exists ( $cartKey, $discPrices ) ? $discPrices[$cartKey] : '' );

                        // Gift / Bogo Rules
                        if ( 'gift' == $rule['type'] ) {
                            $this->wdpGiftsinCart[$rule['id']]          = array_key_exists ( 'wdpGiftsinCart', $result ) ? $result['wdpGiftsinCart'] : [];
                            $this->giftItems[$rule['id']]               = array_key_exists ( 'giftItems', $result ) ? $result['giftItems'] : []; 
                            $orderMetaData['gift']                      = array_key_exists ( 'wdpGiftsinCart', $result ) ? $result['wdpGiftsinCart'] : [];
                            $BGFlag                                     = array_key_exists ( 'skipFlag', $result ) ? $result['skipFlag'] : false;
                        } else if ( 'bogo' == $rule['type'] ) {
                            $this->wdpBogoIDsinCart[$rule['id']]        = array_key_exists ( 'wdpBogoIDsinCart', $result ) ? $result['wdpBogoIDsinCart'] : [];
                            $this->bogoItems[$rule['id']]               = array_key_exists ( 'bogoItems', $result ) ? $result['bogoItems'] : []; 
                            $orderMetaData['bogo']                      = array_key_exists ( 'wdpBogoIDsinCart', $result ) ? $result['wdpBogoIDsinCart'] : [];
                            $BGFlag                                     = array_key_exists ( 'skipFlag', $result ) ? $result['skipFlag'] : false;
                        } else if ( 'cart_quantity' == $rule['type'] && $rule['quantity_type'] == 'type_product' && $rule['tiered_pricing'] ) {
                            $tr_index = array_search ( $disc_prod_ID, array_column ( $result['productDiscount']['tieredDiscount'], 'product_id' ) );
                            if ( $tr_index !== false ) {
                                // $this->tieredIDsinCart[$rule['id']][]   = array_key_exists ( 'tieredDiscount', $result['productDiscount'] ) ? 
                                // array ( 'product_id' => $disc_prod_ID, 'tieredPricing' => $result['productDiscount']['tieredDiscount'][$tr_index]['tierRange'] )
                                // : [];
                                $this->tieredIDsinCart[]                = array_key_exists ( 'tieredDiscount', $result['productDiscount'] ) ? 
                                array ( 'product_id' => $disc_prod_ID, 'tieredPricing' => $result['productDiscount']['tieredDiscount'][$tr_index]['tierRange'] )
                                : [];
                            }
                        }

                        $this->wdp_order_meta[$cartKey][]               = $orderMetaData; 

                        /*
                        * ver 5.0.4
                        * Fix: Admin order - Discount mismatch
                        */
                        // if ( $discount_method === 'cart_item' && $this->wdp_discounted_price[$cartKey] != '' ) { // Switch to set_price if method is set
                        //     $cartContent['data']->set_price(wc_remove_number_precision($this->wdp_discounted_price[$cartKey]));
                        // }
                        
                        //Skip Rule
                        if ( ( !in_array ( $product_slug, $this->awdp_discount_applied ) && ( $rule['type'] == 'percent_product_price' || $rule['type'] == 'fixed_product_price' || $rule['type'] == 'percent_total_amount' || $rule['type'] == 'fixed_cart_amount' || $rule['type'] == 'cart_quantity' ) ) || ( !in_array ( $product_slug, $this->awdp_discount_applied ) && $BGFlag ) ) { 
                            $this->awdp_discount_applied[] = $product_slug;
                        } 

                    } 

                    if ( !in_array ( $rule['id'], $this->wdpRuleIDs ) )
                        $this->wdpRuleIDs[] = $rule['id'];
    
                }

            }  

            /* 
            * Gifts Cart View
            * @@ Ver 4
            */
            if ( !empty ( $this->giftItems ) ) { 
                foreach ( $this->giftItems as $key => $value ) { 
                    if ( empty ( $value ) ) continue;
                    $giftruleID         = $key; 
                    $giftsID            = $value['giftsID'];
                    $giftsAddtoCart     = $value['giftsAddtoCart'];
                    $giftsCount         = $value['giftsCount'];
                    $giftsinCart        = $value['giftsinCart'];
                    $suggestionsFlag    = $value['suggestionsFlag'];
                    // if ( 1 == get_option('awdp_cart_free_product') && !array_intersect ( $giftsID, $inCartItemIDs ) && ( $giftsinCart < $giftsCount ) ) {  
                    /*
                    * @ver 5.0.3
                    * removed array_intersect - fix - suggestions not appearing when there is more than 1 gift
                    */
                    if ( 1 == get_option('awdp_cart_free_product') && ( $giftsinCart < $giftsCount ) ) {  
                        if ( $suggetsionsPos == 'before_cart' ) {
                            add_action( 'woocommerce_before_cart', 
                                function() use ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, $suggestionsFlag ) { 
                                    awdp_gift_products ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, false, $suggestionsFlag ); 
                                } 
                            );
                        } if ( $suggetsionsPos == 'before_cart_table' ) {
                            add_action( 'woocommerce_before_cart_table', 
                                function() use ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, $suggestionsFlag ) { 
                                    awdp_gift_products ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, false, $suggestionsFlag ); 
                                } 
                            );
                        } if ( $suggetsionsPos == 'cart_contents' ) {
                            add_action( 'woocommerce_cart_contents', 
                                function() use ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, $suggestionsFlag ) { 
                                    awdp_gift_products ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, false, $suggestionsFlag ); 
                                } 
                            );
                        } if ( $suggetsionsPos == 'after_cart_contents' ) {
                            add_action( 'woocommerce_after_cart_contents', 
                                function() use ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, $suggestionsFlag ) { 
                                    awdp_gift_products ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, false, $suggestionsFlag ); 
                                } 
                            );
                        } if ( $suggetsionsPos == 'before_cart_totals' ) {
                            add_action( 'woocommerce_before_cart_totals', 
                                function() use ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, $suggestionsFlag ) { 
                                    awdp_gift_products ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount ); 
                                } 
                            );
                        } if ( $suggetsionsPos == 'before_order_total' ) {
                            add_action( 'woocommerce_cart_totals_before_order_total', 
                                function() use ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, $suggestionsFlag  ) { 
                                    awdp_gift_products ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, false, $suggestionsFlag ); 
                                } 
                            );
                        } if ( $suggetsionsPos == 'after_order_total' ) {
                            add_action( 'woocommerce_cart_totals_after_order_total', 
                                function() use ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, $suggestionsFlag ) { 
                                    awdp_gift_products ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, false, $suggestionsFlag ); 
                                } 
                            );
                        } if ( $suggetsionsPos == 'proceed_to_checkout' ) {
                            add_action( 'woocommerce_proceed_to_checkout', 
                                function() use ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, $suggestionsFlag ) { 
                                    awdp_gift_products ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, false, $suggestionsFlag ); 
                                } 
                            );
                        } else {
                            add_action( 'woocommerce_after_cart_table', 
                                function() use ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, $suggestionsFlag ) { 
                                    awdp_gift_products ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, false, $suggestionsFlag ); 
                                } 
                            );
                        }
                    }                  
                }
            }

            /* 
            * Bogo Products Cart View
            * @@ Ver 4
            */
            if ( !empty ( $this->bogoItems ) ) {                             
                foreach ( $this->bogoItems as $key => $value ) { 
                    if ( empty ( $value ) ) continue;
                    $bogoruleID     = $key;
                    $itemsID        = $value['itemsID'];
                    $itemsCount     = $value['itemsCount'];
                    $itemsinCart    = $value['itemsinCart'];
                    $bogo_type      = $value['itemsType'];
                    $bogo_value     = $value['itemsValue'];
                    if( 1 == get_option('awdp_cart_free_product')){
                        if ( $suggetsionsPos == 'before_cart' ) {
                            add_action( 'woocommerce_before_cart', 
                                function() use ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ) { 
                                    awdp_bogo_products ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ); 
                                } 
                            );
                        } if ( $suggetsionsPos == 'before_cart_table' ) {
                            add_action( 'woocommerce_before_cart_table', 
                                function() use ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ) { 
                                    awdp_bogo_products ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ); 
                                } 
                            );
                        } if ( $suggetsionsPos == 'cart_contents' ) {
                            add_action( 'woocommerce_cart_contents', 
                                function() use ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ) { 
                                    awdp_bogo_products ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ); 
                                } 
                            );
                        } if ( $suggetsionsPos == 'after_cart_contents' ) {
                            add_action( 'woocommerce_after_cart_contents', 
                                function() use ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ) { 
                                    awdp_bogo_products ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ); 
                                } 
                            );
                        } if ( $suggetsionsPos == 'before_cart_totals' ) {
                            add_action( 'woocommerce_before_cart_totals', 
                                function() use ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ) { 
                                    awdp_bogo_products ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ); 
                                } 
                            );
                        } if ( $suggetsionsPos == 'before_order_total' ) {
                            add_action( 'woocommerce_cart_totals_before_order_total', 
                                function() use ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ) { 
                                    awdp_bogo_products ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ); 
                                } 
                            );
                        } if ( $suggetsionsPos == 'after_order_total' ) {
                            add_action( 'woocommerce_cart_totals_after_order_total', 
                                function() use ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ) { 
                                    awdp_bogo_products ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ); 
                                } 
                            );
                        } if ( $suggetsionsPos == 'proceed_to_checkout' ) {
                            add_action( 'woocommerce_proceed_to_checkout', 
                                function() use ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ) { 
                                    awdp_bogo_products ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ); 
                                } 
                            );
                        } else {
                            add_action( 'woocommerce_after_cart_table', 
                                function() use ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ) { 
                                    awdp_bogo_products ( $bogoruleID, $itemsID, $inCartItemIDs, $bogo_type, $bogo_value ); 
                                } 
                            );
                        }
                    }
                }
            }

        }

    }

    // Cart Price View
    public function cart_discount_items ( $item_price, $cart_item )
    {

        // Disable Discount if any other gets added to the cart
        $disable_discount   = get_option('awdp_disable_discount') ? get_option('awdp_disable_discount') : '';
        $coupon             = get_option('awdp_fee_label') ? get_option('awdp_fee_label') : 'Discount';
        $coupon_code        = apply_filters('woocommerce_coupon_code', $coupon);
        if ( $disable_discount && !empty ( WC()->cart->get_applied_coupons() ) && !in_array ( $coupon_code, WC()->cart->get_applied_coupons() ) ) {
            return $item_price;
        }

        // Load discount rules 
        $this->load_rules();
        
        // Check if discount is active
        if ( $this->discount_rules == null )
            return $item_price; // Exit if no rules 

        // Load Product List
        $this->set_product_list();
        
        $post_id            = $cart_item['product_id'];
        $quantity           = $cart_item['quantity'];
        $product            = wc_get_product ( $post_id );
        $rules              = $this->discount_rules;
        // $price      = $product->get_sale_price() ? $product->get_sale_price() : $product->get_price(); // get cart price
        $product_slug       = $product->get_data()['slug'];
        $prodLists          = $this->product_lists;
        $variations         = $this->variations;
        $cartContents       = WC()->cart->get_cart();
        
        $variationID        = $cart_item['variation_id'];
        // $variations     = $cart_item['variation'];
        $disc_prod_ID       = $variationID == 0 ? $post_id : $variationID;
        $cartKey            = $cart_item['key'];

        $BGFlag             = false;
        
        /*
        * ver @ 5.0.5 
        * Tax Settings
        */
        $decimal_points     = get_option( 'woocommerce_price_num_decimals' ) ? get_option( 'woocommerce_price_num_decimals' ) : 2;
        $tax_display_mode   = get_option( 'woocommerce_tax_display_shop' );
        $pirce_include_tax  = get_option( 'woocommerce_prices_include_tax' ); 
        $cartPrice          = $cart_item['data']->get_price();

        /*-----------------------------------------------------------------------------------*/
        /*
        * ver @ 5.0.9 
        * If more than 2 decimal points, round to 2 decimal points
        */
        $priceIncTax        = ( ( strlen ( strrchr ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > $decimal_points ) ? round ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), $decimal_points ) : wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) );
        $priceExcTax        = ( ( strlen ( strrchr ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > $decimal_points ) ? round ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), $decimal_points ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );

        // $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );
        $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? $priceIncTax : ( ( 'yes' === $pirce_include_tax && 'excl' === $tax_display_mode ) ? $priceExcTax : '' ); 
        $price              = $priceAfterTax ? $priceAfterTax : $cartPrice; // get cart price
        /*-----------------------------------------------------------------------------------*/

        // $price              = $cartPrice; // get cart price

        // $price              = $cart_item['data']->get_price(); // get cart price
        // if( $this->converted_rate == '' && $item->get_ID() != '' ) {
        //     $this->converted_rate = $this->get_con_unit($item, $price, true);
        // }

        foreach ( $this->discount_rules as $k => $rule ) {
            
            // Disable Discount for Deposit Items @5.1.7
            $depositCheck       = get_post_meta($rule['id'], 'deposit_check', true) ? get_post_meta($rule['id'], 'deposit_check', true) : 0; 
            if ( array_key_exists ( 'awcdp_deposit', $cart_item ) && $cart_item['awcdp_deposit'] && $depositCheck ) {
                continue;
            }

            // Get Product List
            if ( !$this->get_items_to_apply_discount ( $product, $rule, $disc_prod_ID, false, $product_slug ) ) {
                continue;
            }
            
            // Check if User if Logged-In
            if ( ( intval ( $rule['discount_reg_customers'] ) === 1 && !is_user_logged_in() && empty ( array_filter ( $rule['discount_reg_user_roles'] ) ) ) || ( intval ( $rule['discount_reg_customers'] ) === 1 && is_user_logged_in() && ( !empty ( array_filter ( $rule['discount_reg_user_roles'] ) ) && empty ( array_intersect ( $rule['discount_cur_user_roles'], $rule['discount_reg_user_roles'] ) ) ) ) ) { 
                continue;
            }

            // Check Usage Limits
            if ( !$this->check_usage_restrctions($rule) ) { 
                continue;
            }

            // Validate Rules
            if( 'cart_quantity' != $rule['type'] ) { // Skipping cart_quantity rule // 
                if ( !$this->validate_discount_rules( $product, $rule, ['product_price', 'cart_total_amount', 'cart_total_amount_all_prods', 'cart_items', 'cart_items_all_prods', 'cart_products', 'cust_prev_order_count', 'cart_user_role', 'cart_user_selection', 'payment_method', 'shipment_method', 'number_orders', 'amount_spent', 'last_order', 'previous_order', 'product_in_cart', 'product_stock'], $cart_item ) ) {
                    continue;
                }
            } 

            // Discounts Default Values
            if ( !isset ( $this->discounts[$rule['id']] ) ) { 
                $this->discounts[$rule['id']] = [ 'label' => $rule['label'], 'discount_type' => $rule['type'], 'discount_remainder' => -1, 'taxable' => false ];
            } 

            /* 
            * Disable Double Discount
            * @ver 5.0.4
            * $product_slug added to awdp_discount_applied list is discount already applied
            */ 
            if ( array_key_exists ( 'discounts', $this->discounts[$rule['id']] ) && array_key_exists ( $cartKey, $this->discounts[$rule['id']]['discounts'] ) ) { 
                $this->awdp_discount_applied[] = $product_slug;
                continue;
            }

            // Get Price @ ver 5.0.4
            $discPrices         = $this->wdp_discounted_price; 
            // $price              = !empty ($discPrices) ? ( ( array_key_exists ( $cartKey, $discPrices ) && $discPrices[$cartKey] != '' ) ? wc_remove_number_precision ( $discPrices[$cartKey] ) : $cart_item['data']->get_price() ) : $cart_item['data']->get_price();

            $cartPrice          = $cart_item['data']->get_price();
            // Checking for addons price
            $addonPrice         = apply_filters('wcpa_cart_addon_data', false, $cart_item); 
            $dispPrice          = $addonPrice ? ( $addonPrice['totalPrice'] - $addonPrice['excludeFromDiscount'] ) : $cartPrice; 

            /*-----------------------------------------------------------------------------------*/
            /*
            * ver @ 5.0.9 
            * If more than 2 decimal points, round to 2 decimal points
            */
            $priceIncTax        = ( ( strlen ( strrchr ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > $decimal_points ) ? round ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), $decimal_points ) : wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) );
            $priceExcTax        = ( ( strlen ( strrchr ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > $decimal_points ) ? round ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), $decimal_points ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );

            // $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );
            $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? $priceIncTax : ( ( 'yes' === $pirce_include_tax && 'excl' === $tax_display_mode ) ? $priceExcTax : '' ); 
            $price              = $priceAfterTax ? $priceAfterTax : $cartPrice; // commenting $discPrices - fix - cart price mismatch when multiples rules are applied
            $disPrice           = $priceAfterTax ? $priceAfterTax : $cartPrice; // commenting $discPrices - fix - cart price mismatch when multiples rules are applied
            /*-----------------------------------------------------------------------------------*/
            
            // $price              = $cartPrice; // commenting $discPrices - fix - cart price mismatch when multiples rules are applied

            $discVariable       = $this->discounts[$rule['id']]; 
            $prodLists          = $this->product_lists;
            $wdpGiftsinCart     = array_key_exists ( $rule['id'], $this->wdpGiftsinCart ) ? $this->wdpGiftsinCart[$rule['id']] : [];
            $wdpBogoIDsinCart   = array_key_exists ( $rule['id'], $this->wdpBogoIDsinCart ) ? $this->wdpBogoIDsinCart[$rule['id']]: [];
            $wdpBogoIDs         = $this->wdpBogoIDs; 

            // Discount Types
            if ( 'percent_product_price' == $rule['type'] )
                $result = call_user_func_array ( 
                    array ( new AWDP_typeProductPrice(), 'apply_discount_percent_product_price' ), 
                    array ( $rule, $product, $price, $quantity, $discVariable, $cart_item, $disc_prod_ID, $prodLists, $disPrice, $dispPrice ) 
                );
            else if ( 'fixed_product_price' == $rule['type'] )
                $result = call_user_func_array ( 
                    array ( new AWDP_typeProductPrice(), 'apply_discount_fixed_product_price' ), 
                    array ( $rule, $product, $price, $quantity, $discVariable, $cart_item, $disc_prod_ID, $prodLists, $disPrice, $dispPrice ) 
                );
            else if ( 'cart_quantity' == $rule['type'] )
                $result = call_user_func_array ( 
                    array ( new AWDP_typeCartQuantity(), 'apply_discount_cart_quantity' ), 
                    array ( $rule, $product, $price, $quantity, $discVariable, $prodLists, $cartContents, $cart_item, $disc_prod_ID, $disPrice, $dispPrice ) 
                );
            else if ( 'bogo' == $rule['type'] )
                $result = call_user_func_array ( 
                    array ( new AWDP_typeBogo(), 'apply_discount_bogo' ), 
                    array ( $rule, $product, $price, $quantity, $discVariable, $prodLists, $cartContents, $cart_item, $wdpBogoIDsinCart, $wdpBogoIDs, $discPrices, $disc_prod_ID, $dispPrice ) 
                );
            else if ( 'gift' == $rule['type'] )
                $result = call_user_func_array ( 
                    array ( new AWDP_typeGift(), 'apply_discount_gift' ), 
                    array ( $rule, $product, $price, $quantity, $discVariable, $prodLists, $cartContents, $cart_item, $wdpGiftsinCart, $discPrices, $disc_prod_ID, $dispPrice ) 
                );

            if ( !empty($result) ) { 

                $this->discounts[$rule['id']]               = array_key_exists ( 'productDiscount', $result ) ? $result['productDiscount'] : $this->discounts[$rule['id']];
                $this->discounted_products[]                = $cartKey;
                $this->wdp_discounted_price[$cartKey]       = array_key_exists ( 'discountedprice', $result ) ? $result['discountedprice'] : ( array_key_exists ( $cartKey, $discPrices ) ? $discPrices[$cartKey] : '' ); 

                // Gift / Bogo Rules
                if ('gift' == $rule['type'] ) {
                    $this->wdpGiftsinCart[$rule['id']]      = array_key_exists ( 'wdpGiftsinCart', $result ) ? $result['wdpGiftsinCart'] : [];
                    $this->giftItems[$rule['id']]           = array_key_exists ( 'giftItems', $result ) ? $result['giftItems'] : []; 
                    $BGFlag                                 = array_key_exists ( 'skipFlag', $result ) ? $result['skipFlag'] : false;
                } else if ( 'bogo' == $rule['type'] ) {
                    $this->wdpBogoIDsinCart[$rule['id']]    = array_key_exists ( 'wdpBogoIDsinCart', $result ) ? $result['wdpBogoIDsinCart'] : [];
                    $this->bogoItems[$rule['id']]           = array_key_exists ( 'bogoItems', $result ) ? $result['bogoItems'] : []; 
                    $BGFlag                                 = array_key_exists ( 'skipFlag', $result ) ? $result['skipFlag'] : false;
                } else if ( 'cart_quantity' == $rule['type'] && $rule['quantity_type'] == 'type_product' && $rule['tiered_pricing'] ) {
                    $tr_index = array_search ( $disc_prod_ID, array_column ( $result['productDiscount']['tieredDiscount'], 'product_id' ) );
                    if ( $tr_index !== false ) {
                        // $this->tieredIDsinCart[$rule['id']]     = array_key_exists ( 'tieredDiscount', $result['productDiscount'] ) ? 
                        // array ( 'product_id' => $disc_prod_ID, 'tieredPricing' => $result['productDiscount']['tieredDiscount'][$tr_index]['tierRange'] )
                        // : [];
                        $this->tieredIDsinCart[]                = array_key_exists ( 'tieredDiscount', $result['productDiscount'] ) ? 
                        array ( 'product_id' => $disc_prod_ID, 'tieredPricing' => $result['productDiscount']['tieredDiscount'][$tr_index]['tierRange'] )
                        : [];
                    }
                }

                //Skip Rule
                // if ( !in_array ( $product_slug, $this->awdp_discount_applied ) ) {
                //     $this->awdp_discount_applied[] = $product_slug;
                // }
                //Skip Rule
                if ( ( !in_array ( $product_slug, $this->awdp_discount_applied ) && ( $rule['type'] == 'percent_product_price' || $rule['type'] == 'fixed_product_price' || $rule['type'] == 'percent_total_amount' || $rule['type'] == 'fixed_cart_amount' || $rule['type'] == 'cart_quantity' ) ) || ( !in_array ( $product_slug, $this->awdp_discount_applied ) && $BGFlag ) ) { 
                    $this->awdp_discount_applied[] = $product_slug;
                } 
                
            }

            if ( !in_array ( $rule['id'], $this->wdpRuleIDs ) )
                $this->wdpRuleIDs[] = $rule['id'];

        } 

        $activeDiscounts    = $this->discounts; 
        $wdpGiftsinCart     = $this->wdpGiftsinCart;
        $wdpBogoIDs         = $this->wdpBogoIDsinCart; 
        $tieredIDsinCart    = $this->tieredIDsinCart; 

        $viewPrice = call_user_func_array ( 
            array ( new AWDP_viewCartPrice(), 'cart_price' ), 
            array ( $rules, $price, $cart_item, $prodLists, $item_price, $activeDiscounts, $product, $quantity, $wdpGiftsinCart, $wdpBogoIDs, $tieredIDsinCart ) 
        ); 

        $cart_item['awdp']['discountedprice'] = array_key_exists ( 'discountedprice', $viewPrice ) ? $viewPrice['discountedprice'] : $price;

        return array_key_exists ( 'item_price', $viewPrice ) ? $viewPrice['item_price'] : $item_price;

    }
    
    // Cart - Before Cart Totals Hook
    public function wdpCartLoop ( $wc, $cart_content, $cart_item_key ) { 

        if ( $this->discount_rules != false && sizeof($this->discount_rules) >= 1 ) { 

            // $QNitems        = $this->wdpQNitems;
            $discounts          = $this->discounts;
            $wdpGifts           = $this->wdpGifts;
            $wdpGiftsinCart     = $this->wdpGiftsinCart;
            $wdpBogoIDs         = $this->wdpBogoIDsinCart;
            $tieredIDsinCart    = $this->tieredIDsinCart;
            // $decimalPoints      = wc_get_price_decimals();

            $product_id         = ( $cart_content['variation_id'] == 0 || $cart_content['variation_id'] == '' ) ? $cart_content['product_id'] : $cart_content['variation_id'];
            // $actual_product_id = $cart_content['product_id'];

            $product            = wc_get_product ( $product_id );
            $pSlug              = $cart_content['data']->get_slug();
            $cartKey            = $cart_content['key'];

            /*
            * ver @ 5.0.5 
            * Tax Settings
            */
            $tax_display_mode   = get_option( 'woocommerce_tax_display_shop' );
            $pirce_include_tax  = get_option( 'woocommerce_prices_include_tax' ); 
            $decimal_points     = get_option( 'woocommerce_price_num_decimals' ) ? get_option( 'woocommerce_price_num_decimals' ) : 2;
            $cartPrice          = $cart_content['data']->get_price();

            // Checking for addons price
            $addonPrice         = apply_filters('wcpa_cart_addon_data', false, $cart_content); 
            $dispPrice          = $addonPrice ? ( $addonPrice['totalPrice'] - $addonPrice['excludeFromDiscount'] ) : $cartPrice; 

            /*-----------------------------------------------------------------------------------*/
            /*
            * ver @ 5.0.9 
            * If more than 2 decimal points, round to set (woocommerce) decimal points
            */
            $priceIncTax        = ( ( strlen ( strrchr ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > $decimal_points ) ? round ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), $decimal_points ) : wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) );
            $priceExcTax        = ( ( strlen ( strrchr ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > $decimal_points ) ? round ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), $decimal_points ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );

            // $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax( $product, array ( 'price' => $cartPrice ) ) : wc_get_price_excluding_tax( $product, array ( 'price' => $cartPrice ) );
            $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? $priceIncTax : ( ( 'yes' === $pirce_include_tax && 'excl' === $tax_display_mode ) ? $priceExcTax : '' ); 
            $price              = $priceAfterTax ? $priceAfterTax : $cartPrice; 
            /*-----------------------------------------------------------------------------------*/

            // $price              = $cartPrice; 

            $quantity           = $cart_content['quantity'];
            $discount           = 0;

            // if( $this->converted_rate == '' && $product->get_ID() != '' && array_key_exists ( $product_id, $this->wdp_discounted_price ) ) {
            //     $this->converted_rate = $this->get_con_unit($product, wc_remove_number_precision($this->wdp_discounted_price[$product_id]));
            // }

            // if ( $price > 0 ) {
            //     if (WC()->cart->display_prices_including_tax()) {
            //         $price = $this->wdp_price_including_tax ( $product, $price, array(
            //             'qty' => $quantity,
            //             'price' => $price,
            //         ) );
            //     } else {
            //         $price = $this->wdp_price_excluding_tax ( $product, $price, array(
            //             'qty' => $quantity,
            //             'price' => $price
            //         ) );
            //     }
            // }

            if ( ( sizeof ( $wdpGiftsinCart ) > 0 ) && awdp_bgcheck ( $wdpGiftsinCart, $product_id ) ) { 

                foreach ( $wdpGiftsinCart as $key => $value ) {
                    
                    // gifts
                    $gift_index      = array_search ( $product_id, array_column ( $value, 'product_id' ) );
                    $gift_value      = $value[$gift_index]['discount'] ? $value[$gift_index]['discount'] : 0;
                    $gift_quantity   = $value[$gift_index]['quantity'];

                    $actual_value    = $value[$gift_index]['price'] ? $value[$gift_index]['price'] : 0;
                    $actual_quantity = $cart_content['quantity']; 

                    $gift_cart_value = ( $actual_value * $actual_quantity ) - ( $gift_value ); // dy default gift quantity is 1 (individual product)

                    // $quantity = 1;

                    // if ( $gift_cart_value > 0 && $this->check_discount($pSlug) && false === $this->awdp_cart_rules ) { // disable tax when no product discount rules applied
                    //     if (WC()->cart->display_prices_including_tax()) {
                    //         $gift_cart_value = $this->wdp_price_including_tax($product, array(
                    //             'qty' => $quantity,
                    //             'price' => $gift_cart_value,
                    //         ), $gift_cart_value);
                    //     } else {
                    //         $gift_cart_value = $this->wdp_price_excluding_tax($product, array(
                    //             'qty' => $quantity,
                    //             'price' => $gift_cart_value
                    //         ), $gift_cart_value);
                    //     }
                    // }

                    // $product_subtotal = wc_price ( $gift_cart_value * $this->converted_rate );
            
                    /*
                    * ver @ 5.0.9 
                    * If more than 2 decimal points, round to 2 decimal points
                    */
                    $priceIncTax        = ( ( strlen ( strrchr ( wc_get_price_including_tax ( $product, array ( 'price' => $gift_cart_value ) ), '.' ) ) -1 ) > 2 ) ? round ( wc_get_price_including_tax ( $product, array ( 'price' => $gift_cart_value ) ), 2 ) : wc_get_price_including_tax ( $product, array ( 'price' => $gift_cart_value ) );
                    $priceExcTax        = ( ( strlen ( strrchr ( wc_get_price_excluding_tax ( $product, array ( 'price' => $gift_cart_value ) ), '.' ) ) -1 ) > 2 ) ? round ( wc_get_price_excluding_tax ( $product, array ( 'price' => $gift_cart_value ) ), 2 ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $gift_cart_value ) );
            
                    // $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );
                    $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? $priceIncTax : ( ( 'yes' === $pirce_include_tax && 'excl' === $tax_display_mode ) ? $priceExcTax : '' ); 
            
                    $gift_cart_value    = $priceAfterTax ? $priceAfterTax : $gift_cart_value; 

                    $product_subtotal   = wc_price ( $gift_cart_value );

                    if ( $product->is_taxable() && get_option('woocommerce_tax_display_cart') == 'incl' ) {
                        if( !wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
                            $product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                        }
                    }
                    
                    return $product_subtotal;

                }

            } else if ( ( sizeof ( $wdpBogoIDs ) > 0 ) && awdp_bgcheck ( $wdpBogoIDs, $product_id ) ) { 

                foreach ( $wdpBogoIDs as $key => $value ) { 
        
                    if ( !empty ( $value ) ) {

                        // $bogo_index         = array_search ( $product_id, array_column ( $value, 'product_id' ) );
                        $bogo_index         = array_search ( $cartKey, array_column ( $value, 'cartKey' ) ); 
                        $bogo_type          = is_array ( $value[$bogo_index] ) ? ( array_key_exists ( 'discount_type', $value[$bogo_index] ) ? $value[$bogo_index]['discount_type'] : '' ) : '';
                        $discount_quantity  = 1;

                        if( !empty($value[$bogo_index]) && array_key_exists ( 'offer_items', $value[$bogo_index] ) && $value[$bogo_index]['offer_items'] > 0 ) {
                            $bogo_value         = ( $value[$bogo_index]['single_discount'] == 0 ) ? wc_remove_number_precision($value[$bogo_index]['price']) : wc_remove_number_precision( $value[$bogo_index]['single_discount'] );
                            $discount_quantity  = $value[$bogo_index]['offer_items'];
                        } else {
                            $bogo_value         = ( $value[$bogo_index]['discount'] == 0 ) ? wc_remove_number_precision($value[$bogo_index]['price']) : wc_remove_number_precision( $value[$bogo_index]['discount'] );
                        }
                        // Fix discounted_price changed to discount - 3.0.7

                        // $quantity           = 1;
                        $actual_quantity    = $cart_content['quantity'];
                        $actual_value       = wc_remove_number_precision($value[$bogo_index]['price']);                        

                        // Check if quantity rules are active
                        // if ( ( sizeof($QNitems) > 0 ) && array_search ( $product_id, array_column ( $QNitems, 'product_id' ) ) !== false ) {
                        //     $qn_index = array_search ( $product_id, array_column ( $QNitems, 'product_id' ) );
                        //     $qn_price = wc_remove_number_precision ( $QNitems[$qn_index]['discounted_price'] );
                        //     // if ($qn_price > 0) {
                        //     //     if (WC()->cart->display_prices_including_tax()) {
                        //     //         $qn_price = $this->wdp_price_including_tax($product, array(
                        //     //             'qty' => $quantity,
                        //     //             'price' => $qn_price,
                        //     //         ), $qn_price);
                        //     //     } else {
                        //     //         $qn_price = $this->wdp_price_excluding_tax($product, array(
                        //     //             'qty' => $quantity,
                        //     //             'price' => $qn_price
                        //     //         ), $qn_price);
                        //     //     }
                        //     // }
                        //     $new_discount_price = $qn_price;
                        //     $actual_value = $qn_price;
                        // } else {
                            $new_discount_price = $bogo_value;
                            $actual_value       = $actual_value;
                        // }
                        // End Check
                        
                        $bogo_cart_value        = ( $actual_value * $actual_quantity ) - ( $new_discount_price * $discount_quantity );

                        if( !defined( "WDP_BOGO_SET_".$cart_content['product_id']) ) {
                            define( "WDP_BOGO_SET_".$cart_content['product_id'], true );
                        }
                
                        /*
                        * ver @ 5.0.9 
                        * If more than 2 decimal points, round to 2 decimal points
                        */
                        $priceIncTax            = ( ( strlen ( strrchr ( wc_get_price_including_tax ( $product, array ( 'price' => $bogo_cart_value ) ), '.' ) ) -1 ) > 2 ) ? round ( wc_get_price_including_tax ( $product, array ( 'price' => $bogo_cart_value ) ), 2 ) : wc_get_price_including_tax ( $product, array ( 'price' => $bogo_cart_value ) );
                        $priceExcTax            = ( ( strlen ( strrchr ( wc_get_price_excluding_tax ( $product, array ( 'price' => $bogo_cart_value ) ), '.' ) ) -1 ) > 2 ) ? round ( wc_get_price_excluding_tax ( $product, array ( 'price' => $bogo_cart_value ) ), 2 ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $bogo_cart_value ) );

                        $priceAfterTax          = ( 'incl' === $tax_display_mode ) ? $priceIncTax : ( ( 'yes' === $pirce_include_tax && 'excl' === $tax_display_mode ) ? $priceExcTax : '' ); 
                
                        $bogo_cart_value        = $priceAfterTax ? $priceAfterTax : $bogo_cart_value; 

                        // $product_subtotal = wc_price ( $bogo_cart_value * $this->converted_rate );
                        $product_subtotal       = wc_price ( $bogo_cart_value );

                        if ( $product->is_taxable() && get_option('woocommerce_tax_display_cart') == 'incl' ) {
                            if ( !wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
                                $product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                            }
                        }

                        return $product_subtotal;

                    }

                }

            } else if ( $discounts ) { 

                $tieredDiscount = 0;

                foreach ( $discounts as $cartdiscounts ) { 
                    // Added tiered pricing
                    if ( $cartdiscounts['discount_type'] == 'cart_quantity' && array_key_exists ( 'tieredDiscount', $cartdiscounts ) && !empty ( array_filter ( $cartdiscounts['tieredDiscount'] ) ) ) { 

                        $tr_index = array_search ( $product_id, array_column ( $cartdiscounts['tieredDiscount'], 'product_id' ) );
                        if ( $tr_index !== false ) {
                            foreach ( $cartdiscounts['tieredDiscount'][$tr_index]['tierRange'] as $key => $tierRange ) { 
                                $tierStart      = $tierRange['start'] - 1;
                                $tierEnd        = $tierRange['end'];
                                $calc_discount  = $tierRange['value'] * ( $tierEnd - $tierStart ); 
                                $tieredDiscount = $tieredDiscount + ( wc_remove_number_precision ( $calc_discount ) ); 
                            }
                        }

                        // Since we are multiplying the discount by quantity - dividing the discount by quantity for tiered discount
                        // $discount       = ( $discount > 0 ) ? ( $discount / $quantity ) : $discount; 

                    } else if ( array_key_exists ( 'discounts', $cartdiscounts ) ) { 

                        if ( array_key_exists ( $cartKey, $cartdiscounts['discounts'] ) && isset ( $cartdiscounts['discounts'][$cartKey]['discount'] ) && isset ( $cartdiscounts['discounts'][$cartKey]["displayoncart"] ) ) { 
                            $discount += wc_remove_number_precision ( $cartdiscounts['discounts'][$cartKey]['discount'] );
                        } 

                    } 
                } 
                
                // $tax_display_mode   = get_option( 'woocommerce_tax_display_shop' );
                // $priceIncTax        = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax( $product ) : wc_get_price_excluding_tax( $product );
                // $discount           = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax( $product, array ( 'price' => $discount ) ) : wc_get_price_excluding_tax( $product, array ( 'price' => $discount ) ); 
        
                /*
                * ver @ 5.0.9 
                * If more than 2 decimal points, round to 2 decimal points
                */
                // $decimal_points     = get_option( 'woocommerce_price_num_decimals' ) ? get_option( 'woocommerce_price_num_decimals' ) : 2;
                // $priceIncTax        = ( ( strlen ( strrchr ( wc_get_price_including_tax ( $product, array ( 'price' => $discount ) ), '.' ) ) -1 ) > 2 ) ? round ( wc_get_price_including_tax ( $product, array ( 'price' => $discount ) ), 2 ) : wc_get_price_including_tax ( $product, array ( 'price' => $discount ) );
                // $priceExcTax        = ( ( strlen ( strrchr ( wc_get_price_excluding_tax ( $product, array ( 'price' => $discount ) ), '.' ) ) -1 ) > 2 ) ? round ( wc_get_price_excluding_tax ( $product, array ( 'price' => $discount ) ), 2 ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $discount ) );
        
                // // $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );
                // $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? $priceIncTax : ( ( 'yes' === $pirce_include_tax && 'excl' === $tax_display_mode ) ? $priceExcTax : '' ); 
        
                // $discount           = $priceAfterTax ? $priceAfterTax : $discount;  
                
                /*
                * ver @ 5.0.9 
                * Round Decimal Values
                */
                $decimal_val        = $discount - floor($discount); 
                // Check decimal length
                if ( ( strlen ( strrchr ( $decimal_val, '.' ) ) -1 ) > 2 ) {
                    $discount       = round ( $discount, 2 ); 
                } else {
                    $discount       = $discount;
                }
                // End Check
    
                $discounted_price   = ( $price - $discount ) * $quantity;  
                $discounted_price   = $tieredDiscount > 0 ? ( $discounted_price - $tieredDiscount ) : $discounted_price;  // Tiered discount
                $price              = round ( $discounted_price, $decimal_points ); 
                $product_subtotal   = wc_price ( $price );  
    
                if ( $product->is_taxable() && get_option('woocommerce_tax_display_cart') == 'incl' ) {
                    if( !wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
                        $product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                    }
                }
    
                return $product_subtotal;

            } else {

                return $wc;

            }

        } else {

            return $wc;

        }

    }

    // Show Pricing Table
    public function show_pricing_table(){

        // Load discount rules
        $this->load_rules();
        
        // Check if discount is active
        if ( $this->discount_rules == null )
            return ''; // Exit if no rules 

        // Load Product List
        $this->set_product_list();
        
        $post_id                    = get_the_ID();
        $product                    = wc_get_product ( $post_id );

        // Divi Theme Page Builder Loading issue - Fix
        if(!$product){
			return;
		}

        /*
        * ver @ 5.0.5 
        * Tax Settings
        */
        $tax_display_mode           = get_option( 'woocommerce_tax_display_shop' );
        $pirce_include_tax          = get_option( 'woocommerce_prices_include_tax' ); 
        $cartPrice                  = $product->get_sale_price() ? $product->get_sale_price() : $product->get_price();

        /*
        * ver @ 5.0.9 
        * If more than 2 decimal points, round to 2 decimal points
        */
        $decimal_points             = get_option( 'woocommerce_price_num_decimals' ) ? get_option( 'woocommerce_price_num_decimals' ) : 2;
        $priceIncTax                = ( ( strlen ( strrchr ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > 2 ) ? round ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), 2 ) : wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) );
        $priceExcTax                = ( ( strlen ( strrchr ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > 2 ) ? round ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), 2 ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );

        // $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );
        $priceAfterTax              = ( 'incl' === $tax_display_mode ) ? $priceIncTax : ( ( 'yes' === $pirce_include_tax && 'excl' === $tax_display_mode ) ? $priceExcTax : '' ); 

        $price                      = $priceAfterTax ? $priceAfterTax : $cartPrice; 
        
        $rules                      = $this->discount_rules;
        $prodLists                  = $this->product_lists;
        $variations                 = $this->variations;
        $discountedPrice            = $this->discountProductPrice;
        $discountProductMaxPrice    = $this->discountProductMaxPrice;
        $discountProductMinPrice    = $this->discountProductMinPrice;
        $product_slug               = $product->get_data()['slug'];

        // if( $this->converted_rate == '' && $item->get_ID() != '' ) {
        //     $this->converted_rate = $this->get_con_unit($item, $price, true);
        // }

        $pricing_table = call_user_func_array ( 
            array ( new AWDP_viewPricingTable(), 'pricin_table' ), 
            array ( $rules, $product, $price, $post_id, $prodLists, $variations, $discountedPrice, $discountProductMaxPrice, $discountProductMinPrice ) 
        ); 

        //Skip Rule
        if ( $pricing_table != '' && !in_array ( $product_slug, $this->awdp_discount_applied ) ) { 
            $this->awdp_discount_applied[] = $product_slug;
        }

        echo $pricing_table;

    }

    // Price View HTML
    public function get_product_price_html ( $item_price, $product )
    {

        if ( !$product ) return $item_price;

        $updatedPrice       = '';
        $product_slug       = $product->get_data()['slug'];
        $post_id            = $product->get_id();

        /*
        * Set / Reset Attribute Value 
        * Support for Feed Plugin - Set sale price attribute key as 'acowdp_sale_price'
        * ver @ 5.1.4
        */
        if ( metadata_exists ( 'post', $post_id, AWDP_Feed_Attribute ) ) { 
            update_post_meta ( $post_id, AWDP_Feed_Attribute, '' );
        } else {
            add_post_meta ( $post_id, AWDP_Feed_Attribute, '' );
        }
        // End

        if ( is_admin() && !wp_doing_ajax() ) // wp_doing_ajax - Quick view fix
            return $item_price;

        // Load discount rules
        $this->load_rules();
        
        // Check if discount is active
        if ( $this->discount_rules == null )
            return $item_price; // Exit if no rules 

        // Load Product List
        $this->set_product_list();
        
        // $post_id        = get_the_ID();
        // $product        = wc_get_product( $post_id ); 


        /*
        * ver @ 5.0.5 
        * Tax Settings
        */
        $tax_display_mode   = get_option( 'woocommerce_tax_display_shop' );
        $pirce_include_tax  = get_option( 'woocommerce_prices_include_tax' ); 
        $cartPrice          = $product->get_sale_price() ? $product->get_sale_price() : $product->get_price();

        /*
        * ver @ 5.0.9 
        * If more than 2 decimal points, round to 2 decimal points
        */
        $decimal_points     = get_option( 'woocommerce_price_num_decimals' ) ? get_option( 'woocommerce_price_num_decimals' ) : 2;
        $priceIncTax        = ( ( strlen ( strrchr ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > 2 ) ? round ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), 2 ) : wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) );
        $priceExcTax        = ( ( strlen ( strrchr ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > 2 ) ? round ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), 2 ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );

        // $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );
        $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? $priceIncTax : ( ( 'yes' === $pirce_include_tax && 'excl' === $tax_display_mode ) ? $priceExcTax : '' ); 

        $price              = $priceAfterTax ? $priceAfterTax : $cartPrice; 

        $rules              = $this->discount_rules;
        // $price              = $priceIncTax ? $priceIncTax : $cartPrice;
        $prodLists          = $this->product_lists;
        $variations         = $this->variations;
        $cartRules          = $this->awdp_cart_rules;

        // Display regular price instead of sale price @ ver 4.3.3
        // $displayPrcIncTax   = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax( $product, array('price' => $product->get_regular_price() ) ) : wc_get_price_excluding_tax( $product, array('price' => $product->get_regular_price() ) );
        $displayPrcIncTax   = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax( $product, array('price' => $product->get_regular_price() ) ) : '';

        $addition_settings  = get_option('awdp_addition_settings') ? get_option('awdp_addition_settings') : [];
        // $use_regular        = array_key_exists ( 'use_regular', $addition_settings ) ? $addition_settings['use_regular'] : false;
        $use_regular        = false;
        $display_price      = $use_regular ? ( $displayPrcIncTax ? $displayPrcIncTax : $product->get_regular_price() ) : '';

        // if( $this->converted_rate == '' && $item->get_ID() != '' ) {
        //     $this->converted_rate = $this->get_con_unit($item, $price, true);
        // }

        $skip_list          = $this->awdp_discount_applied ? $this->awdp_discount_applied : [];

        $viewPrice = call_user_func_array ( 
            array ( new AWDP_viewProductPrice(), 'product_price' ), 
            array ( $rules, $price, $post_id, $product, $prodLists, $cartRules, $item_price, $display_price, $skip_list ) 
        ); 

        if ( is_array ( $viewPrice ) ) {
            $updatedPrice                   = $viewPrice['itemPrice'];
            $this->discountProductPrice     = $viewPrice['discountedPrice'];
            $this->discountProductMaxPrice  = $viewPrice['discountedMaxPrice'];
            $this->discountProductMinPrice  = $viewPrice['discountedMinPrice'];

            //Skip Rule
            if ( !in_array ( $product_slug, $this->awdp_discount_applied ) ) { 
                $this->awdp_discount_applied[] = $product_slug;
            }
        }

        return $updatedPrice ? $updatedPrice : $item_price;

    }

    // WCPA Get Variation Price
    public function wdpWCPAVariationPrice ( )
    {

        // global $product;
        // $product->get_id();

        // Reset Query
        wp_reset_query(); 
        
        $post_id            = get_the_ID();
        $product            = wc_get_product ( $post_id ); 

        if ( !$product ) return '';

        /*
        * ver @ 5.0.5
        * Tax Settings
        */        
        $tax_display_mode   = get_option( 'woocommerce_tax_display_shop' );
        $pirce_include_tax  = get_option( 'woocommerce_prices_include_tax' ); 
        $cartPrice          = $product->get_sale_price() ? $product->get_sale_price() : $product->get_price();

        /*
        * ver @ 5.0.9 
        * If more than 2 decimal points, round to 2 decimal points
        */
        $decimal_points     = get_option( 'woocommerce_price_num_decimals' ) ? get_option( 'woocommerce_price_num_decimals' ) : 2;
        $priceIncTax        = ( ( strlen ( strrchr ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > 2 ) ? round ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), 2 ) : wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) );
        $priceExcTax        = ( ( strlen ( strrchr ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > 2 ) ? round ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), 2 ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );

        // $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );
        $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? $priceIncTax : ( ( 'yes' === $pirce_include_tax && 'excl' === $tax_display_mode ) ? $priceExcTax : '' ); 

        $price              = $priceAfterTax ? $priceAfterTax : $cartPrice;

        if ( is_admin() ) 
            return $price;

        // Load discount rules
        $this->load_rules();
        
        // Check if discount is active
        if ( $this->discount_rules == null )
            return $price; // Exit if no rules 

        // Load Product List
        $this->set_product_list();

        $updatedPrice       = '';
        $product_slug       = $product->get_data()['slug'];

        $rules              = $this->discount_rules;
        $prodLists          = $this->product_lists;
        $variations         = $this->variations;
        $cartRules          = $this->awdp_cart_rules;
        $item_price         = $price;

        // if( $this->converted_rate == '' && $item->get_ID() != '' ) {
        //     $this->converted_rate = $this->get_con_unit($item, $price, true);
        // }

        $priceGroup = call_user_func_array ( 
            array ( new AWDP_productGroup(), 'product_group' ), 
            array ( $rules, $price, $post_id, $product, $prodLists, $cartRules, $item_price ) 
        ); 

        if ( is_array ( $priceGroup ) ) {

            return new WP_REST_Response($priceGroup, 200);
            
        }

        return $price;

    }

    public function wcpaDiscount($response, $product) 
    {

        $result = [];

        if ( !$product ) return $response;

        // Load discount rules
        $this->load_rules();
        
        // Check if discount is active
        if ( $this->discount_rules == null ) return $response; 

        // Load Product List
        $this->set_product_list();

        $rules              = $this->discount_rules;
        $prodLists          = $this->product_lists;
        $variations         = $this->variations;
        $cartRules          = $this->awdp_cart_rules;

        $wcpaDisc = call_user_func_array ( 
            array ( new AWDP_productGroup(), 'wcpa_discount' ), 
            array ( $rules, $product ) 
        ); 

        if ( !empty ($wcpaDisc) ) {
            // Considering only single discount value as multiple values / group currently not supported with WCPA
            foreach ( $wcpaDisc as $disc ) {
                if ( $disc['type']  == 'percentage' ) {
                    $value                  = $disc['value'] ? $disc['value'] / 100 : 0;
                    $result['percentage']   = $value;
                } elseif ( $disc['type'] == 'fixed' ) {
                    $value                  = $disc['value'] ? $disc['value'] : 0;
                    $result['fixed']        = $value;
                }
            }
        }

        return $result;

    }

    // WCPA Get Discounted Price
    public function wdpDiscountedPrice ()
    {

        // Reset Query
        wp_reset_query(); 
        
        $post_id            = $_GET['prodID'] ? $_GET['prodID'] : get_the_ID();
        $variation_id       = $_GET['varID'] ? $_GET['varID'] : ''; 
        $product            = $variation_id ? wc_get_product ( $variation_id ) : wc_get_product ( $post_id ); 

        if ( !$product ) return '';

        /*
        * ver @ 5.0.5
        * Tax Settings
        */        
        $tax_display_mode   = get_option( 'woocommerce_tax_display_shop' );
        $pirce_include_tax  = get_option( 'woocommerce_prices_include_tax' ); 
        $cartPrice          = $product->get_sale_price() ? $product->get_sale_price() : $product->get_price();

        /*
        * ver @ 5.0.9 
        * If more than 2 decimal points, round to 2 decimal points
        */
        $decimal_points     = get_option( 'woocommerce_price_num_decimals' ) ? get_option( 'woocommerce_price_num_decimals' ) : 2;
        $priceIncTax        = ( ( strlen ( strrchr ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > 2 ) ? round ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), 2 ) : wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) );
        $priceExcTax        = ( ( strlen ( strrchr ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > 2 ) ? round ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), 2 ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );

        // $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );
        $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? $priceIncTax : ( ( 'yes' === $pirce_include_tax && 'excl' === $tax_display_mode ) ? $priceExcTax : '' ); 

        $price              = $priceAfterTax ? $priceAfterTax : $cartPrice;

        if ( is_admin() ) 
            return $price;

        // Load discount rules
        $this->load_rules();
        
        // Check if discount is active
        if ( $this->discount_rules == null )
            return $price; // Exit if no rules 

        // Load Product List
        $this->set_product_list();

        $updatedPrice       = '';
        $product_slug       = $product->get_data()['slug'];

        $rules              = $this->discount_rules;
        $prodLists          = $this->product_lists;
        $variations         = $this->variations;
        $cartRules          = $this->awdp_cart_rules;
        $item_price         = $price;

        if ( $discountedPrice ) {
            $product_id     = $_REQUEST['product_id']; 
        }

        // if( $this->converted_rate == '' && $item->get_ID() != '' ) {
        //     $this->converted_rate = $this->get_con_unit($item, $price, true);
        // }

        $priceGroup = call_user_func_array ( 
            array ( new AWDP_productGroup(), 'product_price' ), 
            array ( $rules, $price, $post_id, $product, $prodLists, $cartRules, $item_price ) 
        ); 

        if ( is_array ( $priceGroup ) ) {

            return new WP_REST_Response($priceGroup, 200);
            
        }

        return $price;

    }

    // WCPA Price
    public function wdpWCPAPrice($default, $product){

        // Load discount rules
        $this->load_rules();
        
        // Check if discount is active
        if ( $this->discount_rules == null )
            return $default; // Exit if no rules 

        // Load Product List
        $this->set_product_list();

        // if ( ! is_a( $product, 'WC_Product' ) ) return $default;
        if ( !$product ) return $default;
        
        $updatedPrice       = '';
        $result             = [];
        $product_slug       = $product->get_data()['slug'];
        $post_id            = $product->get_id();

        /*
        * ver @ 5.0.5 
        * Tax Settings
        */       
        $decimal_points     = get_option( 'woocommerce_price_num_decimals' ) ? get_option( 'woocommerce_price_num_decimals' ) : 2;
        $tax_display_mode   = get_option( 'woocommerce_tax_display_shop' );
        $pirce_include_tax  = get_option( 'woocommerce_prices_include_tax' ); 
        $cartPrice          = $product->get_sale_price() ? $product->get_sale_price() : $product->get_price();

        /*
        * ver @ 5.0.9 
        * If more than 2 decimal points, round to 2 decimal points
        */
        $priceIncTax        = ( ( strlen ( strrchr ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > $decimal_points ) ? round ( wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ), $decimal_points ) : wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) );
        $priceExcTax        = ( ( strlen ( strrchr ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), '.' ) ) -1 ) > $decimal_points ) ? round ( wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) ), $decimal_points ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );

        // $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax ( $product, array ( 'price' => $cartPrice ) ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $cartPrice ) );
        $priceAfterTax      = ( 'incl' === $tax_display_mode ) ? $priceIncTax : ( ( 'yes' === $pirce_include_tax && 'excl' === $tax_display_mode ) ? $priceExcTax : '' ); 

        $price              = $priceAfterTax ? $priceAfterTax : $cartPrice;

        $rules              = $this->discount_rules;
        // $price              = $priceIncTax ? $priceIncTax : $cartPrice;
        $prodLists          = $this->product_lists;
        $variations         = $this->variations;
        $cartRules          = $this->awdp_cart_rules;
        $item_price         = $price;

        // Display regular price instead of sale price @ ver 4.3.3
        // $displayPrcIncTax   = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax( $product, array('price' => $product->get_regular_price() ) ) : wc_get_price_excluding_tax( $product, array('price' => $product->get_regular_price() ) );
        // $displayPrcIncTax   = ( 'incl' === $tax_display_mode ) ? wc_get_price_including_tax( $product, array('price' => $product->get_regular_price() ) ) : '';
        $displayPrcIncTax   = ( ( strlen ( strrchr ( wc_get_price_including_tax ( $product, array ( 'price' => $product->get_regular_price() ) ), '.' ) ) -1 ) > $decimal_points ) ? round ( wc_get_price_including_tax ( $product, array ( 'price' => $product->get_regular_price() ) ), $decimal_points ) : wc_get_price_including_tax ( $product, array ( 'price' => $product->get_regular_price() ) );
        $displayPrcExcTax   = ( ( strlen ( strrchr ( wc_get_price_excluding_tax ( $product, array ( 'price' => $product->get_regular_price() ) ), '.' ) ) -1 ) > $decimal_points ) ? round ( wc_get_price_excluding_tax ( $product, array ( 'price' => $product->get_regular_price() ) ), $decimal_points ) : wc_get_price_excluding_tax ( $product, array ( 'price' => $product->get_regular_price() ) );
        $displayPrcIncTax   = ( 'incl' === $tax_display_mode ) ? $displayPrcIncTax : ( ( 'yes' === $pirce_include_tax && 'excl' === $tax_display_mode ) ? $displayPrcExcTax : '' );
        
        $addition_settings  = get_option ('awdp_addition_settings') ? get_option ('awdp_addition_settings') : [];
        // $use_regular        = array_key_exists ( 'use_regular', $addition_settings ) ? $addition_settings['use_regular'] : false;
        $use_regular        = false;
        $display_price      = $use_regular ? ( $displayPrcIncTax ? $displayPrcIncTax : $product->get_regular_price() ) : '';

        $skip_list          = $this->awdp_discount_applied ? $this->awdp_discount_applied : [];

        $viewPrice = call_user_func_array ( 
            array ( new AWDP_viewProductPrice(), 'product_price' ), 
            array ( $rules, $price, $post_id, $product, $prodLists, $cartRules, $item_price, $display_price, $skip_list ) 
        ); 

        if ( is_array ( $viewPrice ) ) {

            // $updatedPrice   = $viewPrice['discountedPrice'] ? $viewPrice['discountedPrice'] ( $viewPrice['discountedMaxPrice'] ? $viewPrice['discountedMaxPrice'] : $viewPrice['discountedMinPrice']); // Fix - Returning null for variable products
            // $updatedPrice   = $viewPrice['discountedPrice'];

            $result['price']            = $viewPrice['discountedPrice'];
            $result['originalPrice']    = $price;
            return $result;
            
        }

        return $default;

    }

    // Currency convertor
    public function get_con_unit( $product, $price = false, $insideloop = false )
    {

        if ( $this->conversion_unit === false && $insideloop === false ) {

            global $WOOCS; // checking WooCommerce Currency Switcher (WOOCS) is enabled
            $from_currency  = get_option('woocommerce_currency');
            $to_currency    = get_woocommerce_currency();

            if ( $from_currency === $to_currency || $WOOCS !== null ) return 1; 
                        
            $view_price     = $product->get_price('view');
            $edit_price     = ( $price ) ? $price : $product->get_price('edit'); 

            /* 
            * ver @ 5.0.8
            *Commenting wcml_raw_price_amount - wpml conversion issue fix
            */
            // if ( $this->conversion_unit == 1) {
            //     $this->conversion_unit = apply_filters('wcml_raw_price_amount', 1);
            // }

            if ($this->conversion_unit == 1) { // Aelia Currency Switcher
                if(wc_get_price_decimals() == 0 ){
                    $converted_amount   = apply_filters('wc_aelia_cs_convert', 1, $from_currency, $to_currency,2);
                }else{
                    $converted_amount   = apply_filters('wc_aelia_cs_convert', 1, $from_currency, $to_currency);
                }
                $this->conversion_unit  = $converted_amount;
            }

            if ( ( $this->conversion_unit == 1 || $this->conversion_unit == false ) && class_exists('WOOMULTI_CURRENCY') ) { // WooCommerce Multi Currency Plugin
                $data                   = WOOMULTI_CURRENCY_Data::get_ins(); 
                $currency_array         = $data->get_list_currencies();
                $rate                   = (float)$currency_array[$to_currency]['rate']; 
                $this->conversion_unit  = $rate;
            }

            if ( ( $this->conversion_unit == 1 || $this->conversion_unit == false ) && class_exists('WOOMULTI_CURRENCY_F') ) { // WooCommerce Multi Currency Free Plugin
                $data                   = WOOMULTI_CURRENCY_F_Data::get_ins(); 
                $currency_array         = $data->get_list_currencies();
                $rate                   = (float)$currency_array[$to_currency]['rate']; 
                $this->conversion_unit  = $rate;
            }

            if ( ( $this->conversion_unit == 1 || $this->conversion_unit == false ) && function_exists('wcpbc_the_zone') ) {
                $wcpbc                  = wcpbc_the_zone();
                $converted_amount       = 1;
                if (is_callable($wcpbc, 'get_exchange_rate_price')) {
                    $converted_amount   = $wcpbc->get_exchange_rate_price(1);
                }
                $this->conversion_unit  = $converted_amount;
            } 

            if ( $view_price && $edit_price && $edit_price > 0 && $view_price > 0 && $this->conversion_unit == false ) {
                $this->conversion_unit  = $view_price / $edit_price;
            } else if ( $this->conversion_unit == false ) {
                $this->conversion_unit  = 1;
            } 

            // global $WOOCS;
            // if ($this->conversion_unit == 1 && $WOOCS!==null) {
            //     if (method_exists($WOOCS, 'woocs_exchange_value')) {
            //         $res=$WOOCS->woocs_exchange_value(1);
            //         $this->conversion_unit = $res;
            //     }
            // }

            return $this->conversion_unit;

        } else if ( $this->conversion_unit === false && $insideloop === true ) { // Pricing Table

            $from_currency      = get_option('woocommerce_currency');
            $to_currency        = get_woocommerce_currency(); 

            if ( $from_currency === $to_currency ) return 1;

            $converted_price    = $price;
            $unit_price         = $product->get_price('edit');

            $this->conversion_unit = $converted_price / $unit_price;

            // if ($this->conversion_unit == 1) { // Aelia Currency Switcher
            //     if(wc_get_price_decimals() == 0 ){
            //         $converted_amount = apply_filters('wc_aelia_cs_convert', 1, $from_currency, $to_currency,2);
            //     }else{
            //         $converted_amount = apply_filters('wc_aelia_cs_convert', 1, $from_currency, $to_currency);
            //     }
            //     $this->conversion_unit = $converted_amount;
            // }

            if ( $this->conversion_unit == 1 && class_exists('WOOMULTI_CURRENCY') ) { // WooCommerce Multi Currency Plugin
                $data                   = WOOMULTI_CURRENCY_Data::get_ins(); 
                $currency_array         = $data->get_list_currencies();
                $rate                   = (float)$currency_array[$to_currency]['rate']; 
                $this->conversion_unit  = $rate;
            }

            if ( $this->conversion_unit == 1 && class_exists('WOOMULTI_CURRENCY_F') ) { // WooCommerce Multi Currency Free Plugin
                $data                   = WOOMULTI_CURRENCY_F_Data::get_ins(); 
                $currency_array         = $data->get_list_currencies();
                $rate                   = (float)$currency_array[$to_currency]['rate']; 
                $this->conversion_unit  = $rate;
            }

            if ($this->conversion_unit == 1 && function_exists('wcpbc_the_zone')) {
                $wcpbc                  = wcpbc_the_zone();
                $converted_amount       = 1;
                if (is_callable($wcpbc, 'get_exchange_rate_price')) {
                    $converted_amount   = $wcpbc->get_exchange_rate_price(1);
                }
                $this->conversion_unit  = $converted_amount;
            }

            // global $WOOCS;
            // if ($this->conversion_unit == 1 && $WOOCS!==null) {
            //     if (method_exists($WOOCS, 'woocs_exchange_value')) { 
            //         $res=$WOOCS->woocs_exchange_value(1); 
            //         $this->conversion_unit = $res;
            //     }
            // } 

            return $this->conversion_unit;

        } else {

            return $this->conversion_unit;

        }

    }

    // Show Pricing Table
    public function show_pricing_table_old(){
        
        $pricing_table = $this->pricing_table;
        $id = get_the_ID();
        if ( is_array($pricing_table) && array_key_exists ( $id, $pricing_table ) ) {
            $pr_tables = $pricing_table[$id];
            foreach ( $pr_tables as $pr_table ) {
                echo $pr_table;
            }
        }
 
    }

    /* 
    * Applying Discount to Fee
    * @ver 4.0.5
    */
    public function awdp_fees_custom( $cart_obj ) {

        global $woocommerce;

        $totals         = $cart_obj->get_totals();
        $sub_total      = $totals['subtotal'];
        $total_discount = 0;
        $total_fees     = 0;
        $label          = get_option('awdp_fee_label') ? get_option('awdp_fee_label') : 'Discount';
        $cart_fees      = $cart_obj->get_fees();

        if ( !empty ( $cart_fees ) ) {

            // Claculate Fees
            foreach ( $cart_fees as $fee ) {
                $total_fees = $total_fees + $fee->amount;
            }

            // Coupons
            foreach ($cart_obj->get_coupon_discount_totals() as $coupon => $value) { 
                $coupon_obj = new WC_Coupon($coupon); 
                if ($coupon_obj) {
                    $curr_coupon_code = $coupon_obj->get_code();
                    // Checking if wdp coupon applied
                    if ( ( $label == $curr_coupon_code ) || ( mb_strtolower($label, 'UTF-8') == mb_strtolower($curr_coupon_code, 'UTF-8') ) || ( preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $label) && mb_strtolower($label, 'UTF-8') == mb_strtolower(htmlspecialchars_decode ($curr_coupon_code), 'UTF-8') ) ) {

                        $percentage     = ( $value / $sub_total ) * 100;
                        $total_discount = $percentage > 0 ? ( $percentage * $total_fees ) / 100 : 0;

                        if ( $total_discount > 0 ) {
                            //apply coupon discount on the fee amount
                            $woocommerce->cart->add_fee( $label . __(' (Fee)', 'aco-woo-dynamic-pricing'), -$total_discount, false, '');
                        }

                    }
                }
            }

        }

    }

    public function wdpCartMessage(){
        
        global $woocommerce; 
        $coupon         = get_option('awdp_fee_label') ? get_option('awdp_fee_label') : 'Discount'; 
        $coupon_code    = apply_filters('woocommerce_coupon_code', $coupon);
        $customStyle    = '';
        $result         = '';
        $total          = 0;    

        if ( in_array ( $coupon_code, $woocommerce->cart->get_applied_coupons() ) ) {

            if  ( $this->discounts ) {

                foreach ( $this->discounts as $ruleid => $discounts ) { 

                    $discount_type = array_key_exists ( 'discount_type', $discounts ) ? $discounts['discount_type'] : '';       
                    $qn_type       = ( $discount_type == 'cart_quantity' ) ? get_post_meta ( $ruleid, 'discount_quantity_type', true ) : '';      

                    if ( $discount_type == 'cart_quantity' && array_key_exists ( 'tieredDiscount', $discounts ) && !empty ( array_filter ( $discounts['tieredDiscount'] ) ) ) { 

                        $tierd_discs = $discounts['tieredDiscount']; 
                        if (!empty ( $tierd_discs ) ) { 
                            foreach ( $tierd_discs as $tierd_disc ) { 
                                $tierRange = $tierd_disc['tierRange'];
                                if ( !empty ( $tierRange ) ) {
                                    if ( sizeof ( $tierRange ) > 1 ) { 

                                        foreach ( $tierRange as $tierRangesingle ) { 

                                            $tierStart      = $tierRangesingle['start'] - 1; 
                                            $tierEnd        = $tierRangesingle['end']; 
        
                                            $calc_discount  = $tierRangesingle['value'] * ( $tierEnd - $tierStart ); 
                                            $total          = $total + ( wc_remove_number_precision ( $calc_discount ) ); 

                                        }

                                    } else {

                                        $tierStart      = $tierRange[0]['start'] - 1; 
                                        $tierEnd        = $tierRange[0]['end']; 

                                        $calc_discount  = $tierRange[0]['value'] * ( $tierEnd - $tierStart ); 
                                        $total          = $total + ( wc_remove_number_precision ( $calc_discount ) ); 

                                    }

                                }
                            }
                        }

                    } else if ( ( $discount_type == 'percent_product_price' || $discount_type == 'fixed_product_price' || $discount_type == 'percent_total_amount' || $discount_type == 'fixed_cart_amount' || $discount_type == 'cart_quantity' ) && array_key_exists ( 'discounts', $discounts ) ) {  

                        foreach ( $discounts['discounts'] as $key => $discount ) { 

                            if ( $discount['discount'] != '' ) { 
                                // Decimal Round
                                $decimal_val    = $discount['discount'] - floor($discount['discount']); 

                                // Check decimal length 
                                if ( ( strlen ( strrchr ( $decimal_val, '.' ) ) -1 ) > 2 ) { 
                                    $calc_discount  = ( $decimal_val == 0 ) ? $discount['discount'] : ( ( $decimal_val > 0.5 ) ? ceil ( $discount['discount'] ) : floor ( $discount['discount'] ) ); 
                                    // $calc_discount  = round ( $discount['discount'], 2 ); 
                                } else { 
                                    $calc_discount  = $discount['discount']; 
                                } 

                                if ( $discount_type == 'fixed_product_price' || $discount_type == 'percent_product_price' || ( $discount_type == 'cart_quantity' && $qn_type == 'type_product' ) ) { 
                                    $calc_discount = $calc_discount * $discount['quantity']; 
                                }  
                                
                                $total                  = $total + ( wc_remove_number_precision ( $calc_discount ) ); 

                            }

                        }

                    } if ( $discount_type == 'bogo' && array_key_exists ( $ruleid, $this->wdpBogoIDsinCart ) ) { 

                        $wdpBogoIDs         = $this->wdpBogoIDsinCart;
                        $wdpBOGODiscounts   = $wdpBogoIDs[$ruleid]; 
                        if ( !empty ( $wdpBOGODiscounts ) ) { 
                            $bogo_discount = 0;
                            foreach ( $wdpBOGODiscounts as $wdpBOGODisc ) {
                                $bogo_discount += wc_remove_number_precision ( $wdpBOGODisc['discount'] );
                            }
                            $total         += $bogo_discount;
                        }

                    } else if ( $discount_type == 'gift' && array_key_exists ( $ruleid, $this->wdpGiftsinCart ) ) { 

                        $wdpGiftsinCart     = $this->wdpGiftsinCart;
                        $wdpGIFTDiscounts   = $wdpGiftsinCart[$ruleid];
                        if ( !empty ($wdpGIFTDiscounts) ) { 
                            $gift_value = 0;
                            foreach ($wdpGIFTDiscounts as $wdpGIFTDisc ) { 
                                $gift_value += $wdpGIFTDisc['discount'];
                            }
                            $total     += $gift_value;
                        }

                    }

                }

            }
                
            // $coupons_obj                = new WC_Coupon($coupon_code);
            // $coupons_amount             = $coupons_obj->get_amount();
            $coupons_amount             = $total;
            $custom_message_settings    = get_option('awdp_custom_msg_settings') ? get_option('awdp_custom_msg_settings') : [];
            $custom_message_status      = array_key_exists ( 'custom_message_status', $custom_message_settings ) ? $custom_message_settings['custom_message_status'] : false;

            if ( $coupons_amount > 0 && $custom_message_status ) {

                $custom_message                       = array_key_exists ( 'custom_message', $custom_message_settings ) ? $custom_message_settings['custom_message'] : '';
                $custom_message_linheight             = array_key_exists ( 'custom_message_linheight', $custom_message_settings ) ? $custom_message_settings['custom_message_linheight'] : '';
                $custom_message_fontsize              = array_key_exists ( 'custom_message_fontsize', $custom_message_settings ) ? $custom_message_settings['custom_message_fontsize'] : '';
                $custom_message_position              = array_key_exists ( 'custom_message_position', $custom_message_settings ) ? $custom_message_settings['custom_message_position'] : '';
                $custom_message_paddding_lm           = array_key_exists ( 'custom_message_paddding_lm', $custom_message_settings ) ? $custom_message_settings['custom_message_paddding_lm'] : '';
                $custom_message_paddding_tp           = array_key_exists ( 'custom_message_paddding_tp', $custom_message_settings ) ? $custom_message_settings['custom_message_paddding_tp'] : '';
                $custom_message_border_radius         = array_key_exists ( 'custom_message_border_radius', $custom_message_settings ) ? $custom_message_settings['custom_message_border_radius'] : '';
                $custom_message_border_top_width      = array_key_exists ( 'custom_message_border_top_width', $custom_message_settings ) ? $custom_message_settings['custom_message_border_top_width'].'px ' : '';
                $custom_message_border_right_width    = array_key_exists ( 'custom_message_border_right_width', $custom_message_settings ) ? $custom_message_settings['custom_message_border_right_width'].'px ' : '';
                $custom_message_border_bottom_width   = array_key_exists ( 'custom_message_border_bottom_width', $custom_message_settings ) ? $custom_message_settings['custom_message_border_bottom_width'].'px ' : '';
                $custom_message_border_left_width     = array_key_exists ( 'custom_message_border_left_width', $custom_message_settings ) ? $custom_message_settings['custom_message_border_left_width'].'px' : '';
                $custom_message_border_color          = array_key_exists ( 'custom_message_border_color', $custom_message_settings ) ? $custom_message_settings['custom_message_border_color'] : '';
                $custom_message_background            = array_key_exists ( 'custom_message_background', $custom_message_settings ) ? $custom_message_settings['custom_message_background'] : '';
                $custom_message_color                 = array_key_exists ( 'custom_message_color', $custom_message_settings ) ? $custom_message_settings['custom_message_color'] : '';
                
                $customStyle           .= 'font-size: '.$custom_message_fontsize.'px;padding: '.$custom_message_paddding_tp.'px '.$custom_message_paddding_lm.'px;border-radius: '.$custom_message_border_radius.'px;';
                $customStyle           .= $custom_message_color ? 'color: '.$custom_message_color.';' : '';
                $customStyle           .= $custom_message_background ? 'background: '.$custom_message_background.';' : ''; 
                $customStyle           .= $custom_message_border_color ? 'border-color: '.$custom_message_border_color.';' : ''; 
                $customStyle           .= $custom_message_position ? 'text-align: '.$custom_message_position.';' : ''; 
                $customStyle           .= ( $custom_message_border_top_width || $custom_message_border_right_width || $custom_message_border_bottom_width || $custom_message_border_left_width ) ? 'border-width: '.$custom_message_border_top_width.$custom_message_border_right_width.$custom_message_border_bottom_width.$custom_message_border_left_width.';' : ''; 

                $message                = $custom_message ? str_replace('[discount]', wc_price($coupons_amount), $custom_message ) : __("You'he saved ", "aco-woo-dynamic-pricing").wc_price($coupons_amount).__(" on this order", "aco-woo-dynamic-pricing");
                $result                 = '<div class="wdp_save_text" style="'.$customStyle.'">'.$message.'</div>';
                echo $result;

            }
        }

    }

    // Show Offer Message
    public function show_offer_message(){

        // Load discount rules
        $this->load_rules();
        
        // Check if discount is active
        if ( $this->discount_rules == null )
            return ''; // Exit if no rules 

        // Load Product List
        $this->set_product_list();

        Global $product;

        $productid              = $product->get_id();
        $price                  = $product->get_price();
        $result                 = '';

        
        $rules                  = $this->discount_rules;
        $productlists           = $this->product_lists;

        /*
        * Common Offer Settings 
        * ver @ 5.0.6
        */
        $offer_desc_config      = get_option('awdp_offer_desc_config') ? get_option('awdp_offer_desc_config') : [];
        $offer_enabled          = array_key_exists ( 'enable_dismessage', $offer_desc_config ) ? $offer_desc_config['enable_dismessage'] : ''; 
        $offer_rule             = array_key_exists ( 'dismessage_rule', $offer_desc_config ) ? $offer_desc_config['dismessage_rule'] : ''; 
        $list_products          = [];
        $all_prods              = false; 

        if ( $offer_enabled && $offer_rule != '' ) { 

            // $customKey  = array_keys ( array_column ( $this->discount_rules, 'id' ), $offer_rule );
            $customRule = []; 
            $type       = 'common';
            $result    .= call_user_func_array ( 
                array ( new AWDP_viewOfferMessage(), 'show_message' ), 
                array ( $customRule, $productid, $price, $type, $rules, $productlists, $product ) 
            );

        } 
        /***********/

        foreach ( $this->discount_rules as $k => $rule ) { 

            // Exit if not Gift 
            // if ( 'gift' == $rule['type'] || 'bogo' == $rule['type'] ) {

            // if ( 'bogo' == $rule['type'] )
            //     $result .= call_user_func_array ( 
            //         array ( new AWDP_viewOfferMessage(), 'show_message' ), 
            //         array ( $rule, $productid, $price, $rule['type'], $rules, $productlists ) 
            //     );
            // else 
                $result .= call_user_func_array ( 
                    array ( new AWDP_viewOfferMessage(), 'show_message' ), 
                    array ( $rule, $productid, $price, $rule['type'], $rules, $productlists, $product ) 
                );

            // }

        }

        echo $result;
 
    }

    // Show Offer Products
    public function show_offer_products() { 
        
        // Load discount rules
        $this->load_rules();
        
        // Check if discount is active
        if ( $this->discount_rules == null )
            return ''; // Exit if no rules 

        // Load Product List
        $this->set_product_list();

        Global $product;

        $productid      = $product->get_id();
        $product_slug   = get_post_field( 'post_name', $productid ); 
        $price          = $product->get_price();
        $result         = '';

        foreach ( $this->discount_rules as $k => $rule ) { 

            // Exit if not Gift 
            if ( 'gift' == $rule['type'] ) {

                // Get Product List
                if ( !$this->get_items_to_apply_discount ( $product, $rule, false, false, $product_slug ) ) {
                    continue;
                }

                // Check if User if Logged-In
                if ( ( intval ( $rule['discount_reg_customers'] ) === 1 && !is_user_logged_in() && empty ( array_filter ( $rule['discount_reg_user_roles'] ) ) ) || ( intval ( $rule['discount_reg_customers'] ) === 1 && is_user_logged_in() && ( !empty ( array_filter ( $rule['discount_reg_user_roles'] ) ) && empty ( array_intersect ( $rule['discount_cur_user_roles'], $rule['discount_reg_user_roles'] ) ) ) ) ) { 
                    continue;
                }

                // Check Usage Limits
                if ( !$this->check_usage_restrctions($rule) ) { 
                    continue;
                }

                // Validate Rules
                if ( !$this->validate_discount_rules( $product, $rule, ['product_price', 'cart_total_amount', 'cart_total_amount_all_prods', 'cart_items', 'cart_items_all_prods', 'cart_products', 'cust_prev_order_count', 'cart_user_role', 'cart_user_selection', 'payment_method', 'shipment_method', 'number_orders', 'amount_spent', 'last_order', 'previous_order', 'product_in_cart', 'product_stock'] ) ) {
                    continue;
                }

                // Discounts Default Values
                if ( !isset ( $this->discounts[$rule['id']] ) ) { 
                    $this->discounts[$rule['id']] = [ 'label' => $rule['label'], 'discount_type' => $rule['type'], 'discount_remainder' => -1, 'taxable' => false ];
                }

                $discPrices         = $this->wdp_discounted_price;
                $discVariable       = $this->discounts[$rule['id']];
                $prodLists          = $this->product_lists;

                $result = call_user_func_array ( 
                    array ( new AWDP_viewOfferProducts(), 'show_products' ), 
                    array ( $rule, $productid, $price, $discVariable, $prodLists, $rule['type'] ) 
                );

                if ( !empty($result) ) {

                    $this->giftItems[$rule['id']]   = array_key_exists ( 'giftItems', $result ) ? $result['giftItems'] : [];

                }

            }

        }

        /* 
        * Gift Items Single Page Suggestions
        * @@ Ver 4
        */
        if ( !empty ( $this->giftItems ) ) {

            foreach ( $this->giftItems as $key => $value ) {

                $giftruleID     = $key;
                $giftsID        = $value['giftsID']; 
                $giftsAddtoCart = $value['giftsAddtoCart'];
                $giftsCount     = $value['giftsCount'];
                $inCartItemIDs  = []; 
                
                awdp_gift_products ( $giftruleID, $giftsID, $inCartItemIDs, $giftsAddtoCart, $giftsCount, true );

            }

        }

        return $result;
 
    }

    // Unset Discounts Array
    public function apply_cart_discounts( $cart_object ){

        $rules = $this->awdp_cart_rule_ids;

        if ( $this->awdp_cart_rules && $this->validate_discount_rules ( $cart_object, $rules, ['cart_total_amount', 'cart_total_amount_all_prods', 'cart_items', 'cart_items_all_prods', 'cart_products'] ) ) { 
            foreach ( $rules as $rule ) { 
                unset($this->discounts[$rule['id']]);
            }
        }

        // Saving Cart in Session
        WC()->session->__unset( 'WDP_Cart' );
        WC()->session->set( 'WDP_Cart', WC()->cart->get_cart() );

    }

    // Discount Check
    public function check_discount ( $slug )
    {
        $_discounts = array(); 

        foreach ($this->discounts as $discounts) { 
            if ( array_key_exists ( 'discount_type', $discounts ) && ( $discounts['discount_type'] == 'percent_product_price' || $discounts['discount_type'] == 'fixed_product_price' || $discounts['discount_type'] == 'cart_quantity' || $discounts['discount_type'] == 'gift' || $discounts['discount_type'] == 'bogo' ) ) { 
                if(array_key_exists('discounts', $discounts)) { 
                    if(!array_key_exists('type', $discounts['discounts'])) {
                        foreach ($discounts['discounts'] as $key => $discount) { 
                            if (!isset($_discounts[$key])) { 
                                $_discounts[$key] = 0.0;
                            }
                            if( $discount != '') 
                                $_discounts[$key] += $discount;

                        }
                    }
                }
            }   
        }

        if(isset($_discounts[$slug]) && $_discounts[$slug] > 0)
            return true;
        else
            return false;
    }

    // Shop Check Discount
    public function check_discount_shop ( $slug )
    {
        $_discounts = array(); 

        foreach ($this->discounts as $discounts) { 
            if ( array_key_exists ( 'discount_type', $discounts ) && ( $discounts['discount_type'] == 'percent_product_price' || $discounts['discount_type'] == 'fixed_product_price' ) ) { 
                if(array_key_exists('discounts', $discounts)) { 
                    if(!array_key_exists('type', $discounts['discounts'])) {
                        foreach ($discounts['discounts'] as $key => $discount) { 
                            if (!isset($_discounts[$key])) { 
                                $_discounts[$key] = 0.0;
                            }
                            if( $discount != '') 
                                $_discounts[$key] += $discount;

                        }
                    }
                }
            }   
        }

        if(isset($_discounts[$slug]) && $_discounts[$slug] > 0)
            return true;
        else
            return false;
    }

    // Validate Rules
    public function validate_discount_rules($cart_obj, $rule, $rules_to_validate = array(), $item = false, $single = false)
    {

        $list_id = ( array_key_exists( 'product_list', $rule ) && $rule['product_list'] ) ? $rule['product_list'] : '';

        $evel_str   = '';
        $result     = true;// if no rules, the validation must be true
        $rules_flag = true;
        
        // Disabling Quantity Rules for Discount Type -> Cart Quantity
        if ( array_key_exists( 'type', $rule ) && 'cart_quantity' == $rule['type'] && 'cart_quantity' == $rule['quantity_type'] ) {
            $qn_flag = true;
        } else {
            $qn_flag = false;
        }
        
        if ( isset($rule['rules'] ) && is_array( $rule['rules'] ) && !empty( $rule['rules'] ) ) { 

            $eval_array     = [];
            $eval_flag      = true;
            $eval_index     = 0;
            $eval_max_index = sizeof ( $rule['rules'] );
             
            foreach ( $rule['rules'] as $val ) { 
                
                if ( $val == NULL || $val == '' ) continue; // Version 4.0.4
                
                $operator   = '';
                
                if ( !empty( $val['rules'] && array_key_exists( 'rules', $val ) && is_array( $val['rules'] ) && count( $val['rules'] ) ) ) {  
                    $evel_str .= '(';
                    $val_rules = array_values ( array_filter( $val['rules'] ) ); // Remove null elements @@ 3.1.1

                    foreach ( $val_rules as $rul ) { 
                        
                        $evel_str .= '('; 

                        if ( in_array($rul['rule']['item'], $rules_to_validate) && ( 
                            ( $rul['rule']['item'] == 'product_in_cart' && $rul['rule']['selectValue'] != '' ) || 
                            ( $rul['rule']['item'] == 'cart_user_selection' && $rul['rule']['selectValue'] != '' ) || 
                            ( $rul['rule']['item'] == 'cart_user_role' && $rul['rule']['selectValue'] != '' ) || 
                            ( $rul['rule']['item'] == 'previous_order' && $rul['rule']['selectValue'] != '' ) || 
                            ( $rul['rule']['item'] == 'payment_method' && $rul['rule']['value'] != '' ) || 
                            ( $rul['rule']['item'] == 'shipment_method' && $rul['rule']['value'] != '' ) || 
                            ( $rul['rule']['item'] == 'product_price' && $rul['rule']['value'] != '' ) || 
                            ( $rul['rule']['item'] == 'number_orders' && $rul['rule']['value'] != '' ) || 
                            ( $rul['rule']['item'] == 'amount_spent' && $rul['rule']['value'] != '' ) || 
                            ( $rul['rule']['item'] == 'last_order' && $rul['rule']['value'] != '' ) || 
                            ( $rul['rule']['item'] == 'cart_total_amount' && $rul['rule']['value'] != '' ) || 
                            ( $rul['rule']['item'] == 'cart_total_amount_all_prods' && $rul['rule']['value'] != '' ) || 
                            ( $rul['rule']['item'] == 'cart_items_all_prods' && $rul['rule']['value'] != '' ) || 
                            ( $rul['rule']['item'] == 'cart_items' && $rul['rule']['value'] != '' ) || 
                            ( $rul['rule']['item'] == 'cart_products' && $rul['rule']['value'] != '' ) || 
                            ( $rul['rule']['item'] == 'product_stock' && $rul['rule']['value'] != '' ) 
                        ) ) { 

                            if ( $this->eval_rule ( $rul['rule'], $cart_obj, $rule, $list_id, $qn_flag, $single, $item ) ) { 
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

                        // $evel_str  .= ') ' . (($rul['operator'] !== false) ? $rul['operator'] : '') . ' ';
                        $operator   = ($rul['operator'] !== false) ? $rul['operator'] : '';

                    }

                    $eval_array[$eval_index]['flag']        = $eval_flag;
                    $eval_array[$eval_index]['operator']    = ( $eval_max_index == ( $eval_index + 1 ) ) ? '' : ( ($val['operator'] !== false) ? $val['operator'] : 'skip' );
                    $eval_index++;
               
                    // if ( count($val['rules']) > 0 && !empty($val['rules']) ) {
                    //     preg_match_all('/\(.*\)/', $evel_str, $match);
                    //     $evel_str = $match[0][0] . ' ';
                    // }
                    // $evel_str .= ') ' . (($val['operator'] !== false) ? $val['operator'] : '') . ' ';

                }
            }
            
            if ( !empty ( $eval_array ) ) {

                $check_optr = ''; 
                // array_multisort ( array_column ( $eval_array, "operator" ), SORT_ASC, $eval_array ); @ 5.1.6
                array_multisort ( array_column ( $eval_array, "operator" ), SORT_DESC, $eval_array );

                foreach ( $eval_array as $key => $value ) { 

                    if ( $check_optr ) {
                        $rules_flag = ( $check_optr == 'and' ) ? ( ( $rules_flag && $value['flag'] ) ? true : false ) : ( ( $rules_flag || $value['flag'] ) ? true : false ); 
                    } else {
                        // $rules_flag = ( $rules_flag || $eval_single['flag'] === 1 ) ? true : $eval_single['flag'];
                        $rules_flag = $key === 0 ? $value['flag'] : ( ( $rules_flag || $value['flag'] === 1 ) ? true : $value['flag'] );
                    }

                    $check_optr = ( strtolower ( $value['operator'] ) == 'and' || strtolower ( $value['operator'] ) == 'or' ) ? strtolower ( $value['operator'] ) : '';

                } 

            } 

            // if (count($rule['rules']) > 0 && !empty($rule['rules']) && $evel_str != '') {
            //     preg_match_all('/\(.*\)/', $evel_str, $match);
            //     $evel_str = $match[0][0] . ' ';
            // }

            // $evel_str = str_replace(['and', 'or'], ['&&', '||'], strtolower($evel_str));

            // if ($evel_str !== '') {
            //     $result = eval('return ' . $evel_str . ';');
            // }

            return $rules_flag; 

        }

        return $rules_flag; 

    }

    public function eval_rule ( $rule, $cart_obj, $discount_rule, $list_id, $qn_flag, $single = false, $item = false )
    {
        
        $product_lists = $this->product_lists ? $this->product_lists : [];
        $wdp_cart_totals = $wdp_cart_items = $wdp_cart_quantity = 0;  

        // Checking Product List
        if ( $list_id && $list_id != 'null' && !empty($product_lists) && isset ( $product_lists[$list_id] ) && WC()->cart && WC()->cart->get_cart_contents_count() > 0 && ( 'cart_total_amount' == $rule['item'] || 'product_price' == $rule['item'] || 'cart_items' == $rule['item'] || 'cart_products' == $rule['item'] || 'number_orders' == $rule['item'] || 'amount_spent' == $rule['item'] || 'last_order' == $rule['item'] || 'previous_order' == $rule['item'] || 'cart_user_role' == $rule['item'] || 'cart_user_selection' == $rule['item'] || 'payment_method' == $rule['item'] || 'shipment_method' == $rule['item'] || 'product_in_cart' == $rule['item'] || 'product_stock' == $rule['item'] ) ) {

            $applicable_products = $product_lists[$list_id];
            $cart_items = WC()->cart->get_cart();
            if ( $cart_items ) {
                foreach ( $cart_items as $cart_item ) {
                    if ( in_array( $cart_item['product_id'], $applicable_products ) ) {
                        $product_data       = $cart_item['data']->get_data();
                        // $wdp_cart_totals    = $wdp_cart_totals + $product_data['price'] * $cart_item['quantity'];
                        $wdp_cart_totals    = $wdp_cart_totals + $cart_item['data']->get_price() * $cart_item['quantity'];
                        $wdp_cart_items     = $wdp_cart_items + $cart_item['quantity'];
                        $wdp_cart_quantity  = $wdp_cart_quantity + 1;

                    }
                }
            }

        } else if ( isset ( WC()->cart ) && WC()->cart->get_cart_contents_count() > 0 ) {

            // Checkout page ajax loading fix 
            $cart_items = is_checkout() ? ( WC()->session->get('WDP_Cart') ? WC()->session->get('WDP_Cart') : WC()->cart->get_cart() ) : WC()->cart->get_cart(); 

            if ($cart_items) {
                foreach ($cart_items as $cart_item) {
                    $product_data       = $cart_item['data']->get_data();
                    // $wdp_cart_totals    = $wdp_cart_totals + $product_data['price'] * $cart_item['quantity'];
                    $wdp_cart_totals    = $wdp_cart_totals + $cart_item['data']->get_price() * $cart_item['quantity'];
                    $wdp_cart_items     = $wdp_cart_items + $cart_item['quantity'];
                    $wdp_cart_quantity  = $wdp_cart_quantity + 1;
                }
            }
        }

        if ( 'cart_total_amount' == $rule['item'] || 'cart_total_amount_all_prods' == $rule['item'] ) {

            // cart based rule : true
            $this->awdp_cart_rules = true;
            $this->apply_wdp_coupon = true;
            // $this->awdp_cart_rule_ids[] = $discount_rule;

            // Check if cart is empty
            if ( !isset (WC()->cart) || $wdp_cart_totals == 0 || !did_action('woocommerce_before_calculate_totals') ) 
                return false;

            $item_val = $wdp_cart_totals;
            $rel_val = (float)$rule['value'];

        } else if ( 'product_price' == $rule['item'] ) { 

            $this->apply_wdp_coupon = true; 

            // if ($single) {
                if(is_object($item)){
                    $item_val = (float)$item->get_price();
                } else {
                    $item_val = (float)$item['data']->get_price();
                } 
            // } else {
            //     $item_val = (float)$item->get_price();
            // }

            // if($single)
            //     $item_val = (float)$item['data']->get_data()['price'];
            // else
            //     $item_val = (float)$item->get_data()['price']; 

            $rel_val = (float)$rule['value'];

        } else if ( ( 'cart_items' == $rule['item'] || 'cart_items_all_prods' == $rule['item'] ) && false == $qn_flag ) {

            // cart based rule : true
            $this->awdp_cart_rules      = true;
            $this->apply_wdp_coupon     = true;
            $this->awdp_cart_rule_ids[] = $discount_rule;

            // Check if cart is empty
            if ( !isset ( WC()->cart ) || $wdp_cart_quantity == 0 || !did_action('woocommerce_before_calculate_totals') ) return false;

            $item_val = $wdp_cart_items;
            $rel_val = (float)$rule['value'];

        } else if ( 'cart_products' == $rule['item'] && false == $qn_flag ) { 

            // cart based rule : true
            $this->awdp_cart_rules = true;
            $this->apply_wdp_coupon = true;
            $this->awdp_cart_rule_ids[] = $discount_rule;

            // Check if cart is empty
            if ( !isset ( WC()->cart ) || $wdp_cart_quantity == 0 || !did_action('woocommerce_before_calculate_totals') ) return false;

            $item_val = $wdp_cart_quantity;
            $rel_val = (float)$rule['value']; 

        } else if ( 'number_orders' == $rule['item'] ) { 

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
            $rel_val = (float)$rule['value']; 

        } else if ( 'amount_spent' == $rule['item'] ) { 

            if ( !is_user_logged_in() ) return false; // check if user logged in

            $item_val = wc_get_customer_total_spent(get_current_user_id());
            $rel_val = (float)$rule['value']; 

        } else if ( 'last_order' == $rule['item'] ) { 

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
            $rel_val = (float)$rule['value']; 

        } else if ( 'previous_order' == $rule['item'] ) { // Previous order contains / not contains any product

            if ( !is_user_logged_in() ) return false; // check if user logged in

            // Customer Orders
            $customer_orders = get_posts( array(
                'numberposts' => -1,
                'meta_key'    => '_customer_user',
                'meta_value'  => get_current_user_id(),
                'post_type'   => wc_get_order_types(),
                'post_status' => array( 'wc-processing', 'wc-completed' )
            ) );

            $rel_val = $rule['selectValue'];
            $item_val = [];

            if ($customer_orders) {
                foreach ( $customer_orders as $customer_order ) {
                    $order = wc_get_order( $customer_order ); 
                    foreach ( $order->get_items() as $item_id => $item_data ) {
                        $product = $item_data->get_product();
                        if ( $product ) {
                            $item_val[] = $product->get_id();
                        }
                    }
                }
            }

            $item_val = array_values(array_unique($item_val));
            if ( 'contains' == $rule['condition'] && in_array($rel_val, $item_val) ) {
                return true;
            } else if ( 'not_contains' == $rule['condition'] && !in_array($rel_val, $item_val) ) {
                return true;
            }

            return false;

        } else if ( 'cart_user_role' == $rule['item'] ) {  

            if ( $rule['selectValue'] == '0' && 'equal_to' == $rule['condition'] ) 
                return true; 
            else if ( $rule['selectValue'] == '0' && 'not_equal' == $rule['condition'] ) 
                return false;

            /*
            * @ver 5.0.0 - Disabled user logged in check
            */
            // if ( !is_user_logged_in() ) return false; // check if user logged in

            $current_user = wp_get_current_user();
            $user_roles = ( array ) $current_user->roles;

            $item_val = $user_roles;
            $rel_val = $rule['selectValue']; 

            if ( 'equal_to' == $rule['condition'] && in_array ( $rel_val, $item_val ) ) {
                return true;
            } else if ( 'not_equal' == $rule['condition'] && !in_array ( $rel_val, $item_val ) ) {
                return true;
            }

            return false;

        } else if ( 'cart_user_selection' == $rule['item'] ) { 

            if ( !is_user_logged_in() ) return false; // check if user logged in

            $current_user = wp_get_current_user();

            $item_val = $current_user->ID;
            $rel_val = $rule['selectValue']; 

            if ( intval($rel_val) == 0 ) {
                if ( 'equal_to' == $rule['condition'] ) {
                    return true;
                } else if ( 'not_equal' == $rule['condition'] ) {
                    return false;
                }
            } else {
                if ( 'equal_to' == $rule['condition'] && $rel_val == $item_val ) {
                    return true;
                } else if ( 'not_equal' == $rule['condition'] && $rel_val != $item_val ) {
                    return true;
                }
            }

            return false;

        } else if ( 'payment_method' == $rule['item'] ) { 

            $item_val = WC()->session->get('awdpwcpaymentmethod') ? WC()->session->get('awdpwcpaymentmethod') : WC()->session->get('chosen_payment_method'); 
            $rel_val = $rule['value']; 

            if ( 'equal_to' == $rule['condition'] && in_array( $item_val, $rel_val ) ) {
                return true;
            } else if ( 'not_equal' == $rule['condition'] && !in_array( $item_val, $rel_val ) ) {
                return true;
            }

            return false;

        } else if ( 'shipment_method' == $rule['item'] ) { 

            $item_val   = WC()->session->get('awdpwcshippingmethod') ? WC()->session->get('awdpwcshippingmethod') : WC()->session->get('chosen_shipping_methods')['0'];  
            $rel_val    = $rule['value']; 

            if ( 'equal_to' == $rule['condition'] && $item_val != NULL && in_array( $item_val, $rel_val ) ) {
                return true;
            } else if ( 'not_equal' == $rule['condition'] && $item_val != NULL && !in_array( $item_val, $rel_val ) ) {
                return true;
            }

            return false;

        } else if ( 'product_in_cart' == $rule['item'] ) { // check product in cart

            if ( !isset ( WC()->cart ) || $wdp_cart_quantity == 0 || !did_action('woocommerce_before_calculate_totals') ) return false;

            $selectedProduct = $rule['selectValue'];
            $awdp_cart_items = WC()->cart->get_cart();
            $awdp_cart_ids = [];

            foreach( $awdp_cart_items as $awdp_cart_item ) { 
                // $cart_prod = $awdp_cart_item['variation_id'] ? $awdp_cart_item['variation_id'] : $awdp_cart_item['product_id'];
                $cart_prod = $awdp_cart_item['product_id'];
                array_push( $awdp_cart_ids, $cart_prod );
            } 

            if ( $rule['condition'] == 'equals' && in_array( $selectedProduct, $awdp_cart_ids ) ) {
                return true;
            } else if ( $rule['condition'] == 'not_equals' && !in_array( $selectedProduct, $awdp_cart_ids ) ) {
                return true;
            }

        } else if ( 'product_stock' == $rule['item'] ) {

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
        
        switch ($rule['condition']) { 
            case 'equal_to':
                if ( $item_val > 0 && @abs(($item_val - $rel_val) / $item_val) < 0.00001 ) {
                    return true;
                }
                break;
            case 'less_than':
                if ( $item_val < $rel_val ) {
                    return true;
                }
                break;
            case 'less_than_eq':
                if ( $item_val < $rel_val || ( $item_val > 0 && abs(($item_val - $rel_val) / $item_val) < 0.0001 ) ) {
                    return true;
                }
                break;
            case 'greater_than':
                if ( $item_val > $rel_val ) { 
                    return true;
                }
                break;
            case 'greater_than_eq':
                if ( $item_val > $rel_val || ( $item_val > 0 && abs(($item_val - $rel_val) / $item_val) < 0.0001 ) ) {
                    return true;
                }
                break;
        }

        return false;
    }

    // Price
    public function get_product_price( $price, $product )
    {
        if($product) {
            $id = $product->get_id();
            if (isset($this->product_lists[$id])) { 
                return $this->product_lists[$id]['price'];
            }
            return $this->calculate_discount($price, $product);
        }
    }

    public function load_rules()
    {
        
        if ($this->discount_rules === false) { 

            /* 
            * Wordpress Time Zone Settings
            * @ Ver 5.0.1
            */
            $wp_tz_stngs    = get_option('awdp_time_zone_config') ? get_option('awdp_time_zone_config') : []; 
            $wp_tz          = array_key_exists ( 'wordpress_timezone', $wp_tz_stngs ) ? $wp_tz_stngs['wordpress_timezone'] : '';

            if ( $wp_tz ) {

                $timezone   = new DateTimeZone( wp_timezone_string() );
                $datenow    = wp_date("Y-m-d H:i:s", null, $timezone );
                $crntTime   = wp_date("H:i", null, $timezone );

            } else {

                // Get wordpress timezone settings
                $gmt_offset = get_option('gmt_offset');
                $timezone_string = get_option('timezone_string');
                if( $timezone_string ) { 
                    $datenow = new DateTime(current_time('mysql'), new DateTimeZone($timezone_string));
                } else { 
                    $min    = 60 * get_option('gmt_offset'); 
                    $sign   = $min < 0 ? "-" : "+";
                    $absmin = abs($min); 
                    $tz     = sprintf("%s%02d%02d", $sign, $absmin/60, $absmin%60);  
                    $datenow = new DateTime(current_time('mysql'), new DateTimeZone($tz)); 
                }
                // Converting to UTC+000 (moment isoString timezone)
                $datenow->setTimezone(new DateTimeZone('+000'));
                $datenow    = $datenow->format('Y-m-d H:i:s');

                $crntTime   = gmdate('H:i');

            }

            // End Timezone

            // wp_reset_query();
            // wp_reset_postdata();

            $stop_date      = date('Y-m-d H:i:s', strtotime($datenow . ' +1 day'));
            $day            = date("l");

            $awdp_discount_args = array(
                'post_type'         => AWDP_POST_TYPE,
                'fields'            => 'ids',
                'post_status'       => 'publish',
                'posts_per_page'    => -1,
                'meta_key'          => 'discount_priority', 
                'orderby'           => 'meta_value_num',
                'order'             => 'ASC', 
                'meta_query'        => array(
                    'relation'          => 'AND',
                    array(
                        'key'           => 'discount_status',
                        'value'         => 1,
                        'compare'       => '=',
                        'type'          => 'NUMERIC'
                    ),
                    array(
                        'key'           => 'discount_start_date',
                        'value'         => $datenow,
                        'compare'       => '<=',
                        'type'          => 'DATETIME'
                    ),
                    array(
                        'relation'      => 'OR',
                        array(
                            'key'       => 'discount_end_date',
                            'value'     => $datenow,
                            'compare'   => '>=',
                            'type'      => 'DATETIME'
                        ),
                        array(
                            'key'       => 'discount_end_date',
                            'compare'   => 'NOT EXISTS',
                        ),
                        array(
                            'key'       => 'discount_end_date',
                            'value'     => '',
                            'compare'   => '=',
                        ),
                    )
                )
            );
            // $awdp_discount_args['tax_query'] = [];

            $awdp_discount_rules    = get_posts($awdp_discount_args); 

            $current_user           = is_user_logged_in() ? wp_get_current_user() : '';
            $user_roles             = $current_user ? ( array ) $current_user->roles : [];

            $discount_rules         = $check_rules = array(); 

            if ( $awdp_discount_rules ) { 

                foreach ( $awdp_discount_rules as $awdpID ) { 

                    // Discount Value Check
                    if ( ( ( get_post_meta($awdpID, 'discount_type', true) == 'fixed_product_price' || get_post_meta($awdpID, 'discount_type', true) == 'percent_product_price' ) && get_post_meta($awdpID, 'discount_value', true) == '' && get_post_meta($awdpID, 'dynamic_value', true) == '' ) || ( ( get_post_meta($awdpID, 'discount_type', true) == 'percent_total_amount' || get_post_meta($awdpID, 'discount_type', true) == 'fixed_cart_amount' ) && get_post_meta($awdpID, 'discount_value', true) == '' ) ) 
                        continue;
                    // End

                    $schedules          = unserialize(get_post_meta($awdpID, 'discount_schedules', true));
                    $weekday_status     = get_post_meta($awdpID, 'discount_schedule_weekday', true);
                    $weekdays           = unserialize(get_post_meta($awdpID, 'discount_schedule_days', true));
                    
                    if ( $schedules ) {

                        foreach ( $schedules as $schedule ) { 
                            $mn_start_time      = date('H:i' , strtotime($schedule['start_date']));
                            $mn_end_time        = date('H:i' , strtotime($schedule['end_date']));
                            // $current_time       = strtotime(gmdate('H:i'));  
                            $current_time       = strtotime($crntTime);  
                            $awdp_start_date    = $schedule['start_date'];
                            $awdp_end_start     = $schedule['end_date'] ? $schedule['end_date'] : $stop_date; 
                            if ( ( $awdp_start_date <= $datenow ) && ( $awdp_end_start >= $datenow ) && !in_array( $awdpID, $check_rules ) ) { 
                                $check_rules[] = $awdpID; // remove repeated entry - single rule
                                if ( $weekday_status == 1 ) { 

                                    $wk_start_time  = strtotime(get_post_meta($awdpID, 'discount_start_time', true));
                                    $wk_end_time    = strtotime(get_post_meta($awdpID, 'discount_end_time', true));

                                    if ( ( in_array($day, $weekdays) && ( $current_time >= $wk_start_time && $current_time <= $wk_end_time ) ) || ( empty($weekdays) || ( !empty($weekdays) && ( $weekdays[0] == 'null' || $weekdays[0] == '' ) ) ) ){ 

                                        $rule_type          = get_post_meta($awdpID, 'discount_type', true);
                                        $discount_config    = get_post_meta($awdpID, 'discount_config', true);
                                        $gift_config        = get_post_meta($awdpID, 'gift_config', true) ? get_post_meta($awdpID, 'gift_config', true) : [];
                                        $user_config        = get_post_meta($awdpID, 'user_config', true) ? get_post_meta($awdpID, 'user_config', true) : []; 
                                        $bogo_config        = get_post_meta($awdpID, 'wdp_bogo_config', true) ? get_post_meta($awdpID, 'wdp_bogo_config', true) : [];

                                        $discount_rules[]   = array(
                                            'id'                        => $awdpID,
                                            'priority'                  => get_post_meta($awdpID, 'discount_priority', true),
                                            'label'                     => ($discount_config['label'] != '') ? $discount_config['label'] : ( get_option('awdp_fee_label') ? get_option('awdp_fee_label') : get_the_title($awdpID) ),
                                            'discount'                  => get_post_meta($awdpID, 'discount_value', true),
                                            'inc_tax'                   => $discount_config['inc_tax'],
                                            'disable_on_sale'           => $discount_config['disable_on_sale'],
                                            'apply_rule_once'           => array_key_exists ( 'apply_rule_once', $discount_config ) ? $discount_config['apply_rule_once'] : false,
                                            'discount_reg_customers'    => get_post_meta($awdpID, 'discount_reg_customers', true),
                                            'discount_reg_user_roles'   => get_post_meta($awdpID, 'discount_reg_user_roles', true) ? get_post_meta($awdpID, 'discount_reg_user_roles', true) : [],
                                            'discount_cur_user_roles'   => $user_roles,
        
                                            'sequentially'              => $discount_config['sequentially'],
                                            'product_list'              => get_post_meta($awdpID, 'discount_product_list', true),
                                            'rules'                     => $discount_config['rules'] ? unserialize(base64_decode($discount_config['rules'])) : '',
                                            'type'                      => $rule_type,
                                            'quantity_rules'            => get_post_meta($awdpID, 'discount_quantityranges', true) ? unserialize(get_post_meta($awdpID, 'discount_quantityranges', true)) : '',
                                            'bogopayranges'             => get_post_meta($awdpID, 'discount_bogopayranges', true) ? unserialize(get_post_meta($awdpID, 'discount_bogopayranges', true)) : '',
                                            'bogo_payn_prods'           => get_post_meta($awdpID, 'discount_bogo_payn_prods', true),
                                            'quantity_type'             => get_post_meta($awdpID, 'discount_quantity_type', true),
                                            'disc_calc_type'            => get_post_meta($awdpID, 'discount_calc_type', true),
                                            'pricing_table'             => get_post_meta($awdpID, 'discount_pricing_table', true),
                                            'table_layout'              => get_post_meta($awdpID, 'discount_table_layout', true),
                                            'variation_check'           => get_post_meta($awdpID, 'discount_variation_check', true),
                                            'tiered_pricing'            => get_post_meta($awdpID, 'discount_tiered_pricing', true) ? get_post_meta($awdpID, 'discount_tiered_pricing', true) : 0,

                                            'dynamic_value'             => get_post_meta($awdpID, 'dynamic_value', true),

                                            'bogo_buy'                  => get_post_meta($awdpID, 'discount_bogo_buy', true),
                                            'bogo_rule_type'            => get_post_meta($awdpID, 'discount_bogo_rule_type', true),
                                            'bogo_x_prods'              => get_post_meta($awdpID, 'discount_bogo_x_prods', true),
                                            'bogo_x_repeat'             => get_post_meta($awdpID, 'discount_bogo_x_repeat', true),
                                            'bogo_n_prods'              => get_post_meta($awdpID, 'discount_bogo_n_prods', true),
                                            'bogo_n_count'              => get_post_meta($awdpID, 'discount_bogo_n_count', true),
                                            'bogo_n_repeat'             => get_post_meta($awdpID, 'discount_bogo_n_repeat', true),
                                            'bogo_n_loop'               => get_post_meta($awdpID, 'discount_bogo_n_loop', true),
                                            'bogo_multitple_item'       => get_post_meta($awdpID, 'discount_bogo_multitple_item', true),
                                            'bogo_get'                  => get_post_meta($awdpID, 'discount_bogo_get', true),
                                            'bogo_type'                 => get_post_meta($awdpID, 'discount_bogo_type', true),
                                            'bogo_value'                => get_post_meta($awdpID, 'discount_bogo_value', true),
                                            'bogo_combinations'         => get_post_meta($awdpID, 'discount_bogo_combinations', true) ? unserialize(get_post_meta($awdpID, 'discount_bogo_combinations', true)) : '',
                                            'bogo_cheap_buy'            => get_post_meta($awdpID, 'discount_bogo_cheapest_buy', true),
                                            'bogo_x_all'                => get_post_meta($awdpID, 'discount_bogo_x_all', true),
                                            'bogo_y_all'                => get_post_meta($awdpID, 'discount_bogo_y_all', true),
                                            'bogo_cheap_get'            => get_post_meta($awdpID, 'discount_bogo_cheapest_get', true),
                                            'bogo_cheap_prods'          => get_post_meta($awdpID, 'discount_bogo_cheapest_prods', true),
                                            'bogo_cheap_type'           => get_post_meta($awdpID, 'discount_bogo_cheapest_type', true),
                                            'bogo_cheap_value'          => get_post_meta($awdpID, 'discount_bogo_cheapest_value', true),
                                            'bogo_message'              => get_post_meta($awdpID, 'discount_bogo_message', true),

                                            'bogo_x_count'              => get_post_meta($awdpID, 'discount_offer_single_count', true),

                                            'bogo_display_count'        => isset ( $bogo_config['count'] ) ? $bogo_config['count'] : '',
                                            'bogo_display_sort'         => isset ( $bogo_config['sort'] ) ? $bogo_config['sort'] : '',
                                            'bogo_consider_individual'  => isset ( $bogo_config['consider_individual'] ) ? $bogo_config['consider_individual'] : '',

                                            'gift_type'                 => array_key_exists('type', $gift_config) ? $gift_config['type'] : '',
                                            'gift_numbers'              => array_key_exists('number_items', $gift_config) ? $gift_config['number_items'] : '',
                                            'gift_cart_min'             => array_key_exists('gift_cart_min', $gift_config) ? $gift_config['gift_cart_min'] : '',
                                            'gift_off_msg'              => array_key_exists('offer_message', $gift_config) ? $gift_config['offer_message'] : '',
                                            'gift_products'             => array_key_exists('gift_products', $gift_config) ? $gift_config['gift_products'] : '',
                                            'gift_add_cart'             => array_key_exists('gift_auto_cart', $gift_config) ? $gift_config['gift_auto_cart'] : '',
                                            'gift_dis_cpn'              => array_key_exists('disable_gift_coupon', $gift_config) ? $gift_config['disable_gift_coupon'] : '',
                                            'gift_mltpl'                => array_key_exists('mulitple_gifts', $gift_config) ? $gift_config['mulitple_gifts'] : '',
                                            'gift_products'             => array_key_exists('gift_products', $gift_config) ? $gift_config['gift_products'] : '',
                                            'gift_combinations'         => array_key_exists('combinations', $gift_config) ? unserialize($gift_config['combinations']) : '',
                                            'gift_variation'            => array_key_exists('gift_variation', $gift_config) ? $gift_config['gift_variation'] : '',

                                            'usage_limit'               => array_key_exists('usage_limit', $user_config) ? $user_config['usage_limit'] : '',
                                            'user_roles'                => array_key_exists('user_roles', $user_config) ? $user_config['user_roles'] : '',
                                            'users_limit'               => array_key_exists('users_limit', $user_config) ? unserialize($user_config['users_limit']) : '',

                                            'custom_pl_status'          => get_post_meta($awdpID, 'discount_custom_pl', true) ? get_post_meta($awdpID, 'discount_custom_pl', true) : '',
                                            'custom_pl'                 => get_post_meta($awdpID, 'custom_product_list', true) ? get_post_meta($awdpID, 'custom_product_list', true) : '',

                                            'offer_desc_status'         => array_key_exists ( 'offer_desc_status', $discount_config ) ? $discount_config['offer_desc_status'] : true

                                        );
                                    }

                                } else { 

                                    $rule_type          = get_post_meta($awdpID, 'discount_type', true);
                                    $discount_config    = get_post_meta($awdpID, 'discount_config', true);
                                    $gift_config        = get_post_meta($awdpID, 'gift_config', true) ? get_post_meta($awdpID, 'gift_config', true) : [];
                                    $user_config        = get_post_meta($awdpID, 'user_config', true) ? get_post_meta($awdpID, 'user_config', true) : []; 
                                    $bogo_config        = get_post_meta($awdpID, 'wdp_bogo_config', true) ? get_post_meta($awdpID, 'wdp_bogo_config', true) : [];

                                    $discount_rules[]   = array(
                                        'id'                        => $awdpID,
                                        'priority'                  => get_post_meta($awdpID, 'discount_priority', true),
                                        'label'                     => ($discount_config['label'] != '') ? $discount_config['label'] : get_the_title($awdpID),
                                        'discount'                  => get_post_meta($awdpID, 'discount_value', true),
                                        'inc_tax'                   => $discount_config['inc_tax'],
                                        'disable_on_sale'           => $discount_config['disable_on_sale'],
                                        'apply_rule_once'           => array_key_exists ( 'apply_rule_once', $discount_config ) ? $discount_config['apply_rule_once'] : false,
                                        'discount_reg_customers'    => get_post_meta($awdpID, 'discount_reg_customers', true),
                                        'discount_reg_user_roles'   => get_post_meta($awdpID, 'discount_reg_user_roles', true) ? get_post_meta($awdpID, 'discount_reg_user_roles', true) : [],
                                        'discount_cur_user_roles'   => $user_roles,

                                        'sequentially'              => $discount_config['sequentially'],
                                        'product_list'              => get_post_meta($awdpID, 'discount_product_list', true),
                                        'rules'                     => $discount_config['rules'] ? unserialize(base64_decode($discount_config['rules'])) : '',
                                        'type'                      => $rule_type,
                                        'quantity_rules'            => get_post_meta($awdpID, 'discount_quantityranges', true) ? unserialize(get_post_meta($awdpID, 'discount_quantityranges', true)) : '',
                                        'bogopayranges'             => get_post_meta($awdpID, 'discount_bogopayranges', true) ? unserialize(get_post_meta($awdpID, 'discount_bogopayranges', true)) : '',
                                        'bogo_payn_prods'           => get_post_meta($awdpID, 'discount_bogo_payn_prods', true),
                                        'quantity_type'             => get_post_meta($awdpID, 'discount_quantity_type', true),
                                        'pricing_table'             => get_post_meta($awdpID, 'discount_pricing_table', true),
                                        'table_layout'              => get_post_meta($awdpID, 'discount_table_layout', true),
                                        'variation_check'           => get_post_meta($awdpID, 'discount_variation_check', true),
                                        'tiered_pricing'            => get_post_meta($awdpID, 'discount_tiered_pricing', true) ? get_post_meta($awdpID, 'discount_tiered_pricing', true) : 0,

                                        'dynamic_value'             => get_post_meta($awdpID, 'dynamic_value', true),

                                        'bogo_buy'                  => get_post_meta($awdpID, 'discount_bogo_buy', true),
                                        'bogo_rule_type'            => get_post_meta($awdpID, 'discount_bogo_rule_type', true),
                                        'bogo_x_prods'              => get_post_meta($awdpID, 'discount_bogo_x_prods', true),
                                        'bogo_x_repeat'             => get_post_meta($awdpID, 'discount_bogo_x_repeat', true),
                                        'bogo_n_prods'              => get_post_meta($awdpID, 'discount_bogo_n_prods', true),
                                        'bogo_n_count'              => get_post_meta($awdpID, 'discount_bogo_n_count', true),
                                        'bogo_n_repeat'             => get_post_meta($awdpID, 'discount_bogo_n_repeat', true),
                                        'bogo_n_loop'               => get_post_meta($awdpID, 'discount_bogo_n_loop', true),
                                        'bogo_multitple_item'       => get_post_meta($awdpID, 'discount_bogo_multitple_item', true),
                                        'bogo_get'                  => get_post_meta($awdpID, 'discount_bogo_get', true),
                                        'bogo_type'                 => get_post_meta($awdpID, 'discount_bogo_type', true),
                                        'bogo_value'                => get_post_meta($awdpID, 'discount_bogo_value', true),
                                        'bogo_combinations'         => get_post_meta($awdpID, 'discount_bogo_combinations', true) ? unserialize(get_post_meta($awdpID, 'discount_bogo_combinations', true)) : '',
                                        'bogo_cheap_buy'            => get_post_meta($awdpID, 'discount_bogo_cheapest_buy', true),
                                        'bogo_x_all'                => get_post_meta($awdpID, 'discount_bogo_x_all', true),
                                        'bogo_y_all'                => get_post_meta($awdpID, 'discount_bogo_y_all', true),
                                        'bogo_cheap_get'            => get_post_meta($awdpID, 'discount_bogo_cheapest_get', true),
                                        'bogo_cheap_prods'          => get_post_meta($awdpID, 'discount_bogo_cheapest_prods', true),
                                        'bogo_cheap_type'           => get_post_meta($awdpID, 'discount_bogo_cheapest_type', true),
                                        'bogo_cheap_value'          => get_post_meta($awdpID, 'discount_bogo_cheapest_value', true),
                                        'bogo_message'              => get_post_meta($awdpID, 'discount_bogo_message', true),

                                        'bogo_x_count'              => get_post_meta($awdpID, 'discount_offer_single_count', true),

                                        'bogo_display_count'        => isset ( $bogo_config['count'] ) ? $bogo_config['count'] : '',
                                        'bogo_display_sort'         => isset ( $bogo_config['sort'] ) ? $bogo_config['sort'] : '',
                                        'bogo_consider_individual'  => isset ( $bogo_config['consider_individual'] ) ? $bogo_config['consider_individual'] : '',
                                        
                                        'gift_type'                 => array_key_exists('type', $gift_config) ? $gift_config['type'] : '',
                                        'gift_numbers'              => array_key_exists('number_items', $gift_config) ? $gift_config['number_items'] : '',
                                        'gift_cart_min'             => array_key_exists('gift_cart_min', $gift_config) ? $gift_config['gift_cart_min'] : '',
                                        'gift_off_msg'              => array_key_exists('offer_message', $gift_config) ? $gift_config['offer_message'] : '',
                                        'gift_products'             => array_key_exists('gift_products', $gift_config) ? $gift_config['gift_products'] : '',
                                        'gift_add_cart'             => array_key_exists('gift_auto_cart', $gift_config) ? $gift_config['gift_auto_cart'] : '',
                                        'gift_dis_cpn'              => array_key_exists('disable_gift_coupon', $gift_config) ? $gift_config['disable_gift_coupon'] : '',
                                        'gift_mltpl'                => array_key_exists('mulitple_gifts', $gift_config) ? $gift_config['mulitple_gifts'] : '',
                                        'gift_products'             => array_key_exists('gift_products', $gift_config) ? $gift_config['gift_products'] : '',
                                        'gift_combinations'         => array_key_exists('combinations', $gift_config) ? unserialize($gift_config['combinations']) : '',
                                        'gift_variation'            => array_key_exists('gift_variation', $gift_config) ? $gift_config['gift_variation'] : '',

                                        'usage_limit'               => array_key_exists('usage_limit', $user_config) ? $user_config['usage_limit'] : '',
                                        'user_roles'                => array_key_exists('user_roles', $user_config) ? $user_config['user_roles'] : '',
                                        'users_limit'               => array_key_exists('users_limit', $user_config) ? unserialize($user_config['users_limit']) : '',

                                        'custom_pl_status'          => get_post_meta($awdpID, 'discount_custom_pl', true) ? get_post_meta($awdpID, 'discount_custom_pl', true) : '',
                                        'custom_pl'                 => get_post_meta($awdpID, 'custom_product_list', true) ? get_post_meta($awdpID, 'custom_product_list', true) : '',

                                        'offer_desc_status'         => array_key_exists ( 'offer_desc_status', $discount_config ) ? $discount_config['offer_desc_status'] : true
                                    );

                                }
                            }
                        }

                    }
                }

            }

            // Moving Cart based rules to least priority
            // Sorting result based on rule types - bogo, gift, quantity, cart total rules moved to last - least priority
            $cart_rules = [];
            foreach ( $discount_rules as $key => $val ) {
                if ( isset($val) && ( 'cart_quantity' == $val['type'] || 'fixed_cart_amount' == $val['type'] || 'percent_total_amount' == $val['type'] || 'bogo' == $val['type'] || 'gift' == $val['type'] ) ) { 
                    
                    $cart_rules[] = $discount_rules[$key];
                    unset($discount_rules[$key]);
                    
                }
            }
            $discount_rules = array_merge($discount_rules, $cart_rules);
            $discount_rules = array_values($discount_rules);
            // End sort

            $this->discount_rules = $discount_rules;
        }
    }

    public function get_items_to_apply_discount( $product, $rule, $disc_prod_ID = false, $cartRule = false, $product_slug = false )
    {

        $items = array();
        global $woocommerce; 

        /*
        * @ver 5.0.4
        * Fix - Disable discount on onsale variations
        */
        $newProduct = $disc_prod_ID ? wc_get_product ( $disc_prod_ID ) : $product;

        //validate with $rule
        if ( !$this->check_in_product_list ( $product, $rule ) ) { 
            return false;
        }
        if ( !$this->validate_discount_rules ( $product, $rule, ['product_price', 'product_stock', 'cart_user_role', 'cart_user_selection'], $newProduct ) ) {  
            return false;
        }
        if ( isset($rule['disable_on_sale']) && $rule['disable_on_sale'] && $newProduct->is_on_sale('edit') ) { 
            return false;
        }
        if ( $cartRule && $this->awdp_cart_rules ) // For Product Price View // Check cart rules active
            return false;

        if ( $product_slug && isset($rule['apply_rule_once']) && $rule['apply_rule_once'] && in_array ( $product_slug, $this->awdp_discount_applied ) && $rule['type'] != 'gift' ) 
             return false;

        return true;

    }

    public function check_in_product_list($product, $rule)
    { 
        /*
        * version 4.0.0
        * $rule['product_list'] = '' check added
        */
        if ( ( ( '' == $rule['product_list'] || 0 == $rule['product_list'] ) && !$rule['custom_pl_status'] ) || 'bogo' == $rule['type'] || 'gift' == $rule['type'] ) {

            return true;

        } else if ( $rule['custom_pl_status'] ) { 

            // Custom Product List
            $customPL   = $rule['custom_pl'];
            $pro_id     = ( $product->get_parent_id() == 0 ) ? $product->get_id() : $product->get_parent_id(); 
            $prodIDs    = [];   
            
            if ( !empty ( $customPL ) ) {

                $wdp_tax_query = $wdp_prod_ids = $prodIDs = []; $taxcnt = 1;
                foreach ( $customPL as $singlePL ) { 
                    foreach ( $singlePL['rules'] as $val ) {
                        if ( is_array ( $val ) && $val['rule']['value'] ) {
                            if ( $val['rule']['item'] == 'product_selection') {
                                $wdp_prod_ids = array_merge ( $wdp_prod_ids, $val['rule']['value'] );
                            } else {
                                if ( $taxcnt === 1 ) { $wdp_tax_query = array('relation' => 'OR'); }
                                $taxoperator = ( $val['rule']['condition'] === 'notin' ) ? 'NOT IN' : 'IN'; 
                                $wdp_tax_query[] = array(
                                    'taxonomy'  => $val['rule']['item'],
                                    'field'     => 'term_id',
                                    'terms'     => $val['rule']['value'],
                                    'operator'  => $taxoperator
                                );
                                $taxcnt++;
                            }
                        }
                    } 
                }

                if ( !empty($wdp_tax_query) ) {
                    $args = array(
                        'post_type'         => AWDP_WC_PRODUCTS,
                        'fields'            => 'ids',
                        'post_status'       => array( 'publish', 'draft' ),
                        'posts_per_page'    => -1,
                        'tax_query'         => $wdp_tax_query
                    );
                    $prodIDs    = get_posts ( $args );
                }
                $prodIDs	= !empty ( $wdp_prod_ids ) ? array_merge ( $wdp_prod_ids, $prodIDs ) : $prodIDs; 

                return isset($prodIDs) && in_array($pro_id, $prodIDs);

            } else {

                return false; // Return false if selection is empty
                
            }

        } else {
            $this->set_product_list(); 
            $pro_id = $product->get_parent_id(); // in case of variation
            if ($pro_id == 0) {
                $pro_id = $product->get_id();
            } 
            return isset($this->product_lists[$rule['product_list']]) &&
                in_array($pro_id, $this->product_lists[$rule['product_list']]);
        }
    }

    public function set_product_list()
    {

        if ( false == $this->product_lists ) {

            $checkML                = call_user_func ( array ( new AWDP_ML(), 'is_default_lan' ), '' );
            $currentLang            = !$checkML ? call_user_func ( array ( new AWDP_ML(), 'current_language' ), '' ) : 'default';

            if ( false === ( $product_lists = get_transient(AWDP_PRODUCTS_TRANSIENT_KEY) ) || get_transient(AWDP_PRODUCTS_LANG_TRANSIENT_KEY) != $currentLang ) {

                $post_type = AWDP_PRODUCT_LIST;
                global $wpdb;

                $product_lists = array();
                $lists  = array_values ( array_diff ( array_filter ( $wpdb->get_col ( $wpdb->prepare ( "
                        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
                        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                        WHERE pm.meta_key = '%s' 
                        AND p.post_status = '%s' 
                        AND p.post_type = '%s'
                        ", 'discount_product_list', 'publish', AWDP_POST_TYPE ) ) ), array("null") ) );

                $post_ids = array_map ( function($value) { return (int)$value; }, $lists ); 

                foreach ( $post_ids as $id ) { 

                    $list_type      = get_post_meta($id, 'list_type', true);
                    $other_config   = get_post_meta($id, 'product_list_config', true) ? get_post_meta($id, 'product_list_config', true) : [];

                    $product_lists[$id] = array();

                    if ( 'dynamic_request' == $list_type ) {

                        $tax_rules          = array_key_exists ( 'rules', $other_config ) ? ($other_config['rules']) : [];
                        $tax_rules          = ($tax_rules && is_array($tax_rules) && !empty($tax_rules)) ? $tax_rules : false;
                        $excludedProducts   = ($other_config['excludedProducts']);
                        $tax_query          = [];

                        $args = array(
                            'post_type'         => AWDP_WC_PRODUCTS,
                            'fields'            => 'ids',
                            'post_status'       => array( 'publish', 'draft' ),
                            'posts_per_page'    => -1,
                        );

                        if ( $excludedProducts ) {
                            $args['post__not_in'] = $excludedProducts;
                        }

                        if ( false !== $tax_rules ) { 

                            if ( isset($tax_rules[0]['rules']) && is_array($tax_rules[0]['rules']) ) {
                                $selected_tax = array_filter($tax_rules[0]['rules']);
                                if ( ( sizeof ( $selected_tax ) ) > 1 ) {
                                    $tax_query = array(
                                        'relation' => ('or' == strtolower($other_config['taxRelation'])) ? 'OR' : 'AND'
                                    );
                                }
                                foreach ( $selected_tax as $tr ) { 
                                    $taxoperator = ( $tr['rule']['condition'] === 'notin' ) ? 'NOT IN' : 'IN'; 
                                    $tax_query[] = array(
                                        'taxonomy'  => $tr['rule']['item'],
                                        'field'     => 'term_id',
                                        'terms'     => $tr['rule']['value'],
                                        'operator'  => $taxoperator
                                    );
                                }
                                $args['tax_query'] = $tax_query;
                            }

                        }

                        $product_lists[$id] = get_posts ( $args );

                    } else {

                        $product_lists[$id] = array_key_exists ( 'selectedProducts', $other_config ) ? ($other_config['selectedProducts']) : [];

                    }  

                    if ( $product_lists[$id] && class_exists('SitePress') ) { // Get WPML Product ids @@ 3.3.1
                        $wpmlPosts = [];
                        foreach ( $product_lists[$id] as $product_list_id ) { 
                            $transID = apply_filters( 'wpml_object_id', $product_list_id, 'product' );
                            if ( $transID ) {
                                $wpmlPosts[] = $transID;
                            }
                        }
                        $product_lists[$id] = array_values ( array_unique ( array_merge ( $product_lists[$id], $wpmlPosts ) ) );
                    }

                }

                set_transient(AWDP_PRODUCTS_TRANSIENT_KEY, $product_lists, 7 * 24 * HOUR_IN_SECONDS);
                set_transient(AWDP_PRODUCTS_LANG_TRANSIENT_KEY, $currentLang, 7 * 24 * HOUR_IN_SECONDS);

            }

            $this->product_lists = $product_lists;

        }

    }

    public function set_custom_list ( $product_list ) 
    {
        $customPL           = get_post_meta ( $product_list, 'custom_product_list', true ) ? get_post_meta ( $product_list, 'custom_product_list', true ) : [];
        $customProds        = []; 
        $pr_cnt             = 1; 
        $tax_flag           = false;

        if ( !empty ( $customPL ) ) { 
            $args = array(
                'post_type'         => AWDP_WC_PRODUCTS,
                'fields'            => 'ids',
                'post_status'       => array( 'publish', 'draft' ),
                'posts_per_page'    => -1,
            );
            $tax_query = array(
                'relation' => 'OR'
            );
            foreach ( $customPL as $singlePL ) { 
                foreach ( $singlePL['rules'] as $val ) { 
                    if ( is_array ( $val ) && $val['rule']['value'] ) {
                        if ( $val['rule']['item'] == 'product_selection') {
                            $customProds = array_merge ( $customProds, $val['rule']['value'] ); 
                            $pr_cnt++;
                        } else {
                            $tax_flag    = true;
                            $taxoperator = ( $val['rule']['condition'] === 'notin' ) ? 'NOT IN' : 'IN'; 
                            $tax_query[] = array(
                                'taxonomy'  => $val['rule']['item'],
                                'field'     => 'term_id',
                                'terms'     => $val['rule']['value'],
                                'operator'  => $taxoperator
                            );
                        }
                    }
                } 
            }
            if ( $tax_flag ) {
                $args['tax_query']  = $tax_query;
                $custom_ids         = get_posts ( $args );
                $customProds        = array_merge ( $customProds, $custom_ids );
            }
        }

        return $customProds;

    }

    public function check_gift_price ( $products, $cart_price, $compare, $condition )
    {

        if ( $products ) {
            foreach ( $products as $product ) {
                $value = floatval ( $cart_price[$product] );
                switch ( $condition ) { 
                    case 'equal_to':
                        if ($value == $compare) {
                            return true;
                        }
                        break;
                    case 'less_than':
                        if ($value < $compare) {
                            return true;
                        }
                        break;
                    case 'less_than_eq':
                        if ($value < $compare || $value == $compare) {
                            return true;
                        }
                        break;
                    case 'greater_than':
                        if ($value > $compare) {
                            return true;
                        }
                        break;
                    case 'greater_than_eq':
                        if ($value > $compare || $value == $compare) {
                            return true;
                        }
                        break;
                }
            }
        }
        return false;
    }

    public function check_condition ( $condition, $value, $compare )
    {

        switch ( $condition ) { 
            case 'equal_to':
                if ($value == $compare) {
                    return true;
                }
                break;
            case 'less_than':
                if ($value < $compare) {
                    return true;
                }
                break;
            case 'less_than_eq':
                if ($value < $compare || $value == $compare) {
                    return true;
                }
                break;
            case 'greater_than':
                if ($value > $compare) { 
                    return true;
                }
                break;
            case 'greater_than_eq':
                if ($value > $compare || $value == $compare) {
                    return true;
                }
                break;
        }
        return false;

    }

    public function check_in_category ( $condition, $category, $ids )
    {

        $prod_cats = [];
        foreach ( $ids as $id ) {
            $parent     = wp_get_post_parent_id( $id );
            $cats       = ( $parent  != 0 ) ? wp_get_post_terms( $parent, 'product_cat' ) : wp_get_post_terms( $id, 'product_cat' );
            $cat_ids    = array_column ( $cats, 'term_id' );
            $prod_cats  = array_merge( $prod_cats, $cat_ids );
        }
        $prod_cats = array_values(array_unique($prod_cats));
        
        if ( 'not_in' == $condition && !in_array ( $category, $prod_cats ) ) {
            return true;
        } else if ( 'in' == $condition && in_array ( $category, $prod_cats ) ) {
            return true;
        } else {
            return false;
        }

    }

    public function get_discounted_price_in_cents($item, $include_tax = true, $sequential = false)
    {

        $actual_price = $item->get_data()['price'];
        $excluding_tax = get_option('woocommerce_tax_display_shop');
        if ($include_tax && $excluding_tax == 'incl') {
            $price = $this->wdp_price_including_tax($item, $actual_price, array(
                'price' => $actual_price,
            ));
        } else {
            $price = $this->wdp_price_excluding_tax($item, $actual_price, array(
                'price' => $actual_price,
            ));
        }

        // if($sequential) 
        //     return absint(round($price - wc_remove_number_precision($this->get_discount($item->get_id(), true))));
        // else
            return $price;
    }

    // Check Users First Order
    public function user_first_order( $id ) {
        // Get all customer orders
        $customer_orders = get_posts( array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $id,
            'fields'      => 'ids',
            'post_type'   => 'shop_order', // WC orders post type
            // 'post_status' => 'wc-completed', // Only orders with status "completed"
            'post_status' => array_keys( wc_get_is_paid_statuses() ) // Orders with status "completed + processing"
        ) );

       return count($customer_orders) == 0 ? true : false; 
    }

    // Individaul Price
    public function get_individual_discounted_price_in_cents($item, $include_tax = true, $sequential = false, $price = false)
    {

        $latest_price = '';
        $excluding_tax = get_option('woocommerce_tax_display_shop');
        $prodPrice = $price ? $price : $item->get_data()['price'];
        if ($excluding_tax == 'incl') {
            $price = $this->wdp_price_including_tax($item, $prodPrice, array(
                'price' => $prodPrice,
            ));
        } else {
            $price = $this->wdp_price_excluding_tax($item, $prodPrice, array(
                'price' => $prodPrice,
            ));
        }

        // if($sequential) 
        //     return absint(round($price - $this->get_discount($item->get_id(), true)));
        // else
            return wc_add_number_precision($price);
    }

    public function get_discount($key, $in_cents = false)
    {
        $item_discount_totals = $this->get_discounts_by_item($in_cents);
        return isset($item_discount_totals[$key]) ? $item_discount_totals[$key] : 0;
    }

    public function get_discounts_by_item($in_cents = false)
    {
        $discounts = $this->discounts;
        $item_discount_totals = array(); 

        foreach ($discounts as $item_discounts) {
            if ($item_discounts['discounts']) {
                foreach ($item_discounts['discounts'] as $item_key => $item_discount) {
                    if (!isset($item_discount_totals[$item_key])) {
                        $item_discount_totals[$item_key] = 0.0;
                    }
                    $item_discount_totals[$item_key] += $item_discount;
                }
            }
        }

        return $in_cents ? $item_discount_totals : $item_discount_totals;
    }

    public function check_in_rule ( $selectedRule, $productID ) {

        $this->load_rules();

        if ( $this->discount_rules == null ) return false; // Skip if no active rules

        $product        = wc_get_product ( $productID ); 
        $discount_index = array_search ( $selectedRule, array_column ( $this->discount_rules, 'id' ) );

        if ( $discount_index !== false ) {

            $rules  = $this->discount_rules;
            $rule   = $rules[$discount_index];

            // Get Product List
            if ( !$this->get_items_to_apply_discount ( $product, $rule ) ) { 
                return false;
            } 
            
            // Check if User if Logged-In
            if ( ( intval ( $rule['discount_reg_customers'] ) === 1 && !is_user_logged_in() && empty ( array_filter ( $rule['discount_reg_user_roles'] ) ) ) || ( intval ( $rule['discount_reg_customers'] ) === 1 && is_user_logged_in() && ( !empty ( array_filter ( $rule['discount_reg_user_roles'] ) ) && empty ( array_intersect ( $rule['discount_cur_user_roles'], $rule['discount_reg_user_roles'] ) ) ) ) ) { 
                return false;
            }

            return true;

        }

        return false;

    }

    protected function apply_discount_remainder($rule, $items_to_apply, $amount)
    {
        $total_discount = 0;

        if ( $items_to_apply ) {
            foreach ($items_to_apply as $item) {
                for ($i = 0; $i < $item->quantity; $i++) {
                    // Find out how much price is available to discount for the item.
                    $discounted_price = $this->get_discounted_price_in_cents($item);

                    // $price_to_discount = (false) ? $discounted_price : $item->price;// check if apply_ sequential

                    $discount = min($discounted_price, 1);

                    // Store totals.
                    $total_discount += $discount;

                    // Store code and discount amount per item.
                    $this->discounts[$rule['id']]['discounts'][$item->key] += $discount;

                    if ($total_discount >= $amount) {
                        break 2;
                    }
                }
                if ($total_discount >= $amount) {
                    break;
                }
            }
        }

        return $total_discount;
    }
    
    // Coupon
    public function addVirtualCoupon($response, $curr_coupon_code)
    {

        if ( $this->discounts && WC()->cart ) {

            global $woocommerce;
            $cart_contents          = $woocommerce->cart->get_cart();
            $prod_QNT               = [];
            $prod_IDs               = [];
            $ct_cart_price_array    = [];
            $this->ct_product_ids   = [];
            $total                  = 0;
            $ct_total               = $this->wdpCartDicount;
            $ct_total_new           = 0;
            $converted_rate         = $this->converted_rate ? $this->converted_rate : 1;
            $label                  = get_option('awdp_fee_label') ? get_option('awdp_fee_label') : 'Discount';
            $this->couponLabel      = $label;
            $cart_item_price        = [];

            $discountItemsCount     = 0; // @5.1.7
            $discountItemsActPrice  = 0; // @5.1.7

            // $cart_contents = $cart_obj->get_cart();

            foreach ($cart_contents as $cart_content) {
                $prod_QNT[$cart_content['data']->get_data()['slug']] = $cart_content['quantity'];
                $ct_total_new = $ct_total_new + ($cart_content['data']->get_price() * $cart_content['quantity']);
                $ct_cart_price_array[] = array ( 'id' => $cart_content['data']->get_slug(), 'price' => ( $cart_content['data']->get_price() * $cart_content['quantity'] ) );
                if ( $cart_content['variation_id'] == 0 ) {
                    $this->ct_product_ids[]                         = $cart_content['product_id'];
                    $cart_item_price[$cart_content['product_id']]   = $cart_content['data']->get_price();
                } else {
                    $this->ct_product_ids[]                         = $cart_content['variation_id'];
                    $cart_item_price[$cart_content['variation_id']] = $cart_content['data']->get_price();
                }
            }

            foreach ( $this->discounts as $ruleid => $discounts ) { 

                $discount_type = array_key_exists ( 'discount_type', $discounts ) ? $discounts['discount_type'] : '';       
                $qn_type       = ( $discount_type == 'cart_quantity' ) ? get_post_meta ( $ruleid, 'discount_quantity_type', true ) : '';      
                    
                if ( ( $label == $curr_coupon_code ) || ( mb_strtolower($label, 'UTF-8') == mb_strtolower($curr_coupon_code, 'UTF-8') ) || ( addslashes(mb_strtolower($label, 'UTF-8')) == mb_strtolower($curr_coupon_code, 'UTF-8') ) || ( preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $label) && mb_strtolower($label, 'UTF-8') == mb_strtolower(htmlspecialchars_decode ($curr_coupon_code), 'UTF-8') ) ) { 

                    // Added tiered pricing
                    if ( $discount_type == 'cart_quantity' && array_key_exists ( 'tieredDiscount', $discounts ) && !empty ( array_filter ( $discounts['tieredDiscount'] ) ) ) { 
                        // $tr_disc = array_column($discounts['tieredDiscount'], 'tierRange');

                        // if (!empty ( $tr_disc ) ) { 

                        //     foreach ( $tr_disc as $key => $tierRange ) {  

                        //         if ( !empty ( $tierRange ) && sizeof ( $tierRange ) > 1 ) { 

                        //             foreach ( $tierRange as $tierRangesingle ) { 

                        //                 $tierStart      = $tierRangesingle['start'] - 1; 
                        //                 $tierEnd        = $tierRangesingle['end']; 
    
                        //                 $calc_discount  = $tierRangesingle['value'] * ( $tierEnd - $tierStart ); 
                        //                 $total          = $total + ( wc_remove_number_precision ( $calc_discount ) ); 

                        //                 // Discounted Items Count
                        //                 $discountItemsCount = $tierRangesingle['value'] ? ( $discountItemsCount + ( $tierEnd - $tierStart ) ) : $discountItemsCount;

                        //             }

                        //         } else if ( !empty ( $tierRange ) ) {

                        //             $tierStart      = $tierRange[0]['start'] - 1; 
                        //             $tierEnd        = $tierRange[0]['end']; 

                        //             $calc_discount  = $tierRange[0]['value'] * ( $tierEnd - $tierStart ); 
                        //             $total          = $total + ( wc_remove_number_precision ( $calc_discount ) ); 

                        //             // Discounted Items Count
                        //             $discountItemsCount = $tierRange[0]['value'] ? ( $discountItemsCount + ( $tierEnd - $tierStart ) ) : $discountItemsCount;

                        //         }

                        //         // if ( !in_array ( $discount["productid"], $prod_IDs ) ) { 
                        //         //     $prod_IDs[] = $discount["productid"]; 
                        //         // } 

                        //     } 
                            
                        // }

                        // @ 5.1.7
                        $tierd_discs = $discounts['tieredDiscount']; 
                        if (!empty ( $tierd_discs ) ) { 
                            foreach ( $tierd_discs as $tierd_disc ) { 
                                $tierRange = $tierd_disc['tierRange'];
                                if ( !empty ( $tierRange ) ) {
                                    if ( sizeof ( $tierRange ) > 1 ) { 

                                        foreach ( $tierRange as $tierRangesingle ) { 

                                            $tierStart      = $tierRangesingle['start'] - 1; 
                                            $tierEnd        = $tierRangesingle['end']; 
        
                                            $calc_discount  = $tierRangesingle['value'] * ( $tierEnd - $tierStart ); 
                                            $total          = $total + ( wc_remove_number_precision ( $calc_discount ) ); 

                                            // Discounted Items Count
                                            // $discountItemsCount = $tierRangesingle['value'] ? ( $discountItemsCount + ( $tierEnd - $tierStart ) ) : $discountItemsCount;

                                        }

                                    } else {

                                        $tierStart      = $tierRange[0]['start'] - 1; 
                                        $tierEnd        = $tierRange[0]['end']; 

                                        $calc_discount  = $tierRange[0]['value'] * ( $tierEnd - $tierStart ); 
                                        $total          = $total + ( wc_remove_number_precision ( $calc_discount ) ); 

                                        // Discounted Items Count
                                        // $discountItemsCount = $tierRange[0]['value'] ? ( $discountItemsCount + ( $tierEnd - $tierStart ) ) : $discountItemsCount;

                                    }
                                    
                                    // Adding to product list
                                    if ( !in_array ( $tierd_disc["product_id"], $prod_IDs ) ) { 
                                        $prod_IDs[] = $tierd_disc["product_id"]; 
                                    } 

                                    // Discounted Items Count
                                    $discountItemsCount     = $tierd_disc["quantity"] ? ( $discountItemsCount + $tierd_disc["quantity"] )  : $discountItemsCount;
                                    $discountItemsActPrice += $tierd_disc["quantity"] ? ( $cart_item_price[$tierd_disc["product_id"]] * $tierd_disc["quantity"] ) : $cart_item_price[$tierd_disc["product_id"]];
                                }
                            }
                        }

                    } else if ( ( $discount_type == 'percent_product_price' || $discount_type == 'fixed_product_price' || $discount_type == 'percent_total_amount' || $discount_type == 'fixed_cart_amount' || $discount_type == 'cart_quantity' ) && array_key_exists ( 'discounts', $discounts ) ) {  

                        foreach ( $discounts['discounts'] as $key => $discount ) { 

                            if ( $discount['discount'] != '' ) { 
                                // Decimal Round
                                $decimal_val    = $discount['discount'] - floor($discount['discount']); 

                                // Check decimal length 
                                if ( ( strlen ( strrchr ( $decimal_val, '.' ) ) -1 ) > 2 ) { 
                                    $calc_discount  = ( $decimal_val == 0 ) ? $discount['discount'] : ( ( $decimal_val > 0.5 ) ? ceil ( $discount['discount'] ) : floor ( $discount['discount'] ) ); 
                                    // $calc_discount  = round ( $discount['discount'], 2 ); 
                                } else { 
                                    $calc_discount  = $discount['discount']; 
                                } 

                                if ( $discount_type == 'fixed_product_price' || $discount_type == 'percent_product_price' || ( $discount_type == 'cart_quantity' && $qn_type == 'type_product' ) ) { 
                                    $calc_discount = $calc_discount * $discount['quantity']; 
                                } 

                                if ( !in_array ( $discount["productid"], $prod_IDs ) ) { 
                                    $prod_IDs[] = $discount["productid"]; 
                                } 
                                
                                $total                  = $total + ( wc_remove_number_precision ( $calc_discount ) ); 
                                $discountItemsCount     = $discountItemsCount + $discount['quantity'];
                                $discountItemsActPrice += $discount["quantity"] ? ( $cart_item_price[$discount["productid"]] * $discount["quantity"] ) : $cart_item_price[$discount["productid"]];

                            }

                        }

                    } if ( $discount_type == 'bogo' && array_key_exists ( $ruleid, $this->wdpBogoIDsinCart ) ) { 

                        $wdpBogoIDs         = $this->wdpBogoIDsinCart;
                        $wdpBOGODiscounts   = $wdpBogoIDs[$ruleid]; 
                        if ( !empty ( $wdpBOGODiscounts ) ) { 
                            $bogo_discount = 0;
                            foreach ( $wdpBOGODiscounts as $wdpBOGODisc ) {
                                $bogo_discount += wc_remove_number_precision ( $wdpBOGODisc['discount'] );
                                if ( !in_array ( $wdpBOGODisc["product_id"], $prod_IDs ) ) { 
                                    $prod_IDs[] = $wdpBOGODisc["product_id"]; 
                                } 
                                $discountItemsCount     = ( $wdpBOGODisc['actual_qn'] && $wdpBOGODisc['discount'] > 0 ) ? $discountItemsCount + $wdpBOGODisc['actual_qn'] : $discountItemsCount;
                                $discountItemsActPrice += $wdpBOGODisc['actual_qn'] ? ( $cart_item_price[$wdpBOGODisc["product_id"]] * $wdpBOGODisc['actual_qn'] ) : $cart_item_price[$wdpBOGODisc["product_id"]];
                            }
                            // Before $discountItemsCount
                            // $bogo_discount  = array_sum ( 
                            //                         array_map ( function ( $item ) { 
                            //                             if ( in_array ( $item['product_id'], $this->ct_product_ids ) ) { 
                            //                                 return wc_remove_number_precision($item['discount']); 
                            //                             } else {
                            //                                 return 0;
                            //                             }
                            //                         }, $wdpBOGODiscounts ) 
                            //                     ); 
                            $total         += $bogo_discount;
                        }
                        
                        // $wdpBogoIDs = $this->wdpBogoIDsinCart; 
                        // if ( $wdpBogoIDs ) { 
                        //     foreach ( $wdpBogoIDs as $key => $value ) { 
                        //         // if ( in_array ( $value['product_id'], $ct_product_ids ) ) { 
                        //             // if($wdpBogoID['discount_type'] == 'free'){
                        //             //     $bogo_discount = wc_remove_number_precision($wdpBogoID['single_discount']);
                        //             //     $total += $bogo_discount;
                        //             // } else { 
                        //                 // $bogo_discount  = wc_remove_number_precision($value['discount']);
                        //                 $bogo_discount  = array_sum ( 
                        //                     array_map ( function ( $item ) { 
                        //                         if ( in_array ( $item['product_id'], $this->ct_product_ids ) ) { 
                        //                             return wc_remove_number_precision($item['discount']); 
                        //                         } else {
                        //                             return 0;
                        //                         }
                        //                     }, $value ) 
                        //                 ); 
                        //                 $total         += $bogo_discount;
                        //             // }
                        //         // }
                        //     }
                        // }

                    } else if ( $discount_type == 'gift' && array_key_exists ( $ruleid, $this->wdpGiftsinCart ) ) { 

                        $wdpGiftsinCart     = $this->wdpGiftsinCart;
                        $wdpGIFTDiscounts   = $wdpGiftsinCart[$ruleid];
                        if ( !empty ($wdpGIFTDiscounts) ) { 
                            $gift_value = 0;
                            foreach ($wdpGIFTDiscounts as $wdpGIFTDisc ) { 
                                // $gift_value += $wdpGIFTDisc[$wdpGIFTDiscount];
                                $gift_value += $wdpGIFTDisc['discount'];
                                if ( !in_array ( $wdpGIFTDisc["product_id"], $prod_IDs ) ) { 
                                    $prod_IDs[] = $wdpGIFTDisc["product_id"]; 
                                } 
                                $discountItemsCount     = ( $wdpGIFTDisc['actual_qn'] && $wdpGIFTDisc['discount'] > 0 ) ? $discountItemsCount + $wdpGIFTDisc['actual_qn'] : $discountItemsCount;
                                $discountItemsActPrice += $wdpGIFTDisc['actual_qn'] ? ( $cart_item_price[$wdpGIFTDisc["product_id"]] * $wdpGIFTDisc['actual_qn'] ) : $cart_item_price[$wdpGIFTDisc["product_id"]];
                            }
                            // Before $discountItemsCount
                            // $gift_value = array_sum ( array_map ( function ( $item ) { 
                            //                 return $item['discount']; 
                            //             }, $wdpGIFTDiscounts ) );
                            // $gift_value = $value['discount'];
                            $total     += $gift_value;
                        }

                        // $wdpGiftsinCart = $this->wdpGiftsinCart; 
                        // if ( $wdpGiftsinCart ) {
                        //     foreach ( $wdpGiftsinCart as $key => $value ) {
                        //         $gift_value = array_sum ( array_map ( function ( $item ) { 
                        //             return $item['discount']; 
                        //         }, $value ) );
                        //         // $gift_value = $value['discount'];
                        //         $total     += $gift_value;
                        //     }
                        // }

                    }
                }
            } 

            if ($total > 0) { 

                // Skip tax
                // $total = wc_get_price_including_tax (); 

                // if ( $this->converted_rate > 1 && class_exists('WC_Aelia_CurrencySwitcher') ) { // Checking if Aelia Currency Plugin is active
                if ( $converted_rate > 0 && $converted_rate != 1 ) { // Removing conversion from coupon total
                    $total = $total / $converted_rate;
                } 

                if ( !$discount_type ) return false;

                /* 
                * @ ver 5.0.4 
                * Coupon Product Ids
                */

                /*
                * @ ver 5.1.7 
                * swicthing coupon discount type to 'fixed_product' from 'fixed_cart'
                * dividing by number of products (offer items)
                */
                // $total          = $discountItemsCount ? ( $total / $discountItemsCount ) : $total;
                // $coupnDiscType  = $discountItemsCount ? 'fixed_product' : 'fixed_cart';

                // Changing to percent
                $total          = $discountItemsActPrice ? ( $total / $discountItemsActPrice ) * 100 : $total;
                $coupnDiscType  = $discountItemsActPrice ? 'percent' : 'fixed_cart';

                $coupon_array = array(
                    'code'                      => mb_strtolower($label, 'UTF-8'),
                    'id'                        => 99999999 + rand(1000, 9999),
                    'amount'                    => $total,
                    'individual_use'            => false,
                    'product_ids'               => $prod_IDs,
                    'exclude_product_ids'       => array(),
                    'usage_limit'               => '',
                    'usage_limit_per_user'      => '',
                    'limit_usage_to_x_items'    => '',
                    'usage_count'               => '',
                    'expiry_date'               => '',
                    'apply_before_tax'          => 'yes',
                    'free_shipping'             => false,
                    'product_categories'        => array(),
                    'exclude_product_categories' => array(),
                    'exclude_sale_items'        => false,
                    'minimum_amount'            => '',
                    'maximum_amount'            => '',
                    'customer_email'            => '',
                    'discount_type'             => $coupnDiscType, // fixed_cart, fixed_product, percent
                    // 'virtual'                   => true,
                );

                //@@ AWDP_CART_NOTICE variable changed to session  => ver 3.1.5
                if ( !WC()->session->get( 'AWDP_CART_NOTICE' ) && get_option( 'awdp_message_status' ) == 1 && !isset( $_POST['update_cart'] ) && !is_checkout() ) {

                    // wc_clear_notices();  // Clear Woocommerce notices

                    // define('AWDP_CART_NOTICE', true);
                    WC()->session->set( 'AWDP_CART_NOTICE', true ); 

                    $notice = (get_option('awdp_message_status') == 1) ? (get_option('awdp_discount_message') ? str_replace('[label]', $label, get_option('awdp_discount_message')) : (('discount' == mb_strtolower($label, 'UTF-8')) ? $label . __(" has been applied!", "aco-woo-dynamic-pricing") : __("Discount '", "aco-woo-dynamic-pricing") . $label . __("' has been applied!", "aco-woo-dynamic-pricing"))) : (('discount' == mb_strtolower($label, 'UTF-8')) ? $label . __(" has been applied!", "aco-woo-dynamic-pricing") : __("Discount '", "aco-woo-dynamic-pricing") . $label . __("' has been applied!", "aco-woo-dynamic-pricing"));

                    if (false === wc_has_notice($notice, "awdpcoupon")) {
                        wc_add_notice($notice, "awdpcoupon");
                    }
                }

                return $coupon_array;

            } 

        }

        return $response;

    }

    public function couponLabel($label, $coupon)
    {

        if ($coupon) {
            $coupon_label = $this->couponLabel;
            $code = $coupon->get_code();
            if ($code == $coupon_label || mb_strtolower($code, 'UTF-8') == mb_strtolower($coupon_label, 'UTF-8')) {
                return ucfirst($coupon_label);
            }
        }
        return $label;
    }

    public function applyFakeCoupons()
    {

        global $woocommerce;  //apply_filters('woocommerce_applied_coupon');
        $coupon = get_option('awdp_fee_label') ? get_option('awdp_fee_label') : 'Discount'; 
        $coupon_code = apply_filters('woocommerce_coupon_code', $coupon);

        if ( !in_array($coupon_code, $woocommerce->cart->get_applied_coupons()) && $this->discounts && true == $this->apply_wdp_coupon && WC()->cart ) {
            $coupons_obj = new WC_Coupon($coupon_code);
            $coupons_amount = $coupons_obj->get_amount();
            if ($coupons_amount > 0) {
                $woocommerce->cart->add_discount($coupon_code);
                // wc_clear_notices(); // Clear Woocommerce notices
            }
        } else if ( in_array($coupon_code, $woocommerce->cart->get_applied_coupons())) {
            $coupons_obj = new WC_Coupon($coupon_code);
            $coupons_amount = $coupons_obj->get_amount();
            if ($coupons_amount == 0) {
                WC()->cart->remove_coupon($coupon_code);
                // wc_clear_notices(); // Clear Woocommerce notices
            }
        }

        $applied_coupons = WC()->cart->get_applied_coupons();

        return true;
        
    }

    // Saving Discount Details as Order Meta
    public function wdpSaveOrderMeta ( $order_id )
    {

        $order      = $order_id ? wc_get_order($order_id) : null; 
        $user_id    = get_current_user_id();
        $user_meta  = get_userdata ( $user_id );
        $wdpRuleIDs = $this->wdpRuleIDs;

        if ( !empty($wdpRuleIDs) && false === get_post_meta( $order_id, 'wdpDiscountRules', true ) ) {
            add_post_meta($order_id, 'wdpDiscountRules', $wdpRuleIDs); 
        } else if ( !empty($wdpRuleIDs) ) {
            update_post_meta($order_id, 'wdpDiscountRules', $wdpRuleIDs);
        }

        foreach ($wdpRuleIDs as $wdpRuleID) {

            $usage_config = get_post_meta($wdpRuleID, 'user_config', true);

            if ( false === get_post_meta( $wdpRuleID, 'wdpUsageLimit', true ) && ( true === $usage_config['user_restrictions'] ) ) {
                add_post_meta($wdpRuleID, 'wdpUsageLimit', 1); 
            } else if ( true === $usage_config['user_restrictions'] ) {
                $numb = intval ( get_post_meta( $wdpRuleID, 'wdpUsageLimit', true ) ) + 1;
                update_post_meta($wdpRuleID, 'wdpUsageLimit', $numb);
            }

            if ( $user_id && $user_id != 0 && ( true === $usage_config['user_restrictions'] || $usage_config['per_user_limit'] > 0 ) ) {
                
                if ( false === get_user_meta ( $user_id, 'wdpUserData', true ) ) {

                    $wdpUserData = array(
                        $wdpRuleID => 1
                    );
    
                    add_user_meta ( $user_id, 'wdpUserData', serialize ( $wdpUserData ) ); 

                } else {

                    $wdpUserData = get_user_meta ( $user_id, 'wdpUserData', true ) ? unserialize ( get_user_meta ( $user_id, 'wdpUserData', true ) ) : [];
                    if ( array_key_exists ( $wdpRuleID, $wdpUserData ) ) {
                        $temp = intval ( $wdpUserData[$wdpRuleID] );
                        $temp = $temp + 1;
                        $wdpUserData[$wdpRuleID] = $temp;
                    } else {
                        $wdpUserData[$wdpRuleID] = 1;
                    }

                    // $numb = get_user_meta ( $user_id, 'wdpUsageLimit', true ) + 1;
                    update_user_meta ( $user_id, 'wdpUserData', serialize ( $wdpUserData ) );

                }

            } 

            // Daily Limit
            if ( $user_id && $user_id != 0 && ( true === $usage_config['daily_limit_switch'] ) ) { 

                $curr_date = date ( "Y-m-d" );

                // global $wpdb;
                // $date_string = "> '$date'";

                // $daily_order = $wpdb->get_var( "SELECT DISTINCT count(p.ID) FROM {$wpdb->prefix}posts as p WHERE p.post_type = 'shop_order' AND p.post_date $date_string AND p.post_status IN ('wc-on-hold','wc-processing','wc-completed')" );

                if ( false === get_post_meta( $wdpRuleID, 'wdpDailyUsageLimit', true ) ) {

                    add_post_meta($wdpRuleID, 'wdpDailyUsageLimit', 1); 
                    add_post_meta($wdpRuleID, 'wdpDailyUsageLimitResetDate', $curr_date);

                } else if ( true === $usage_config['user_restrictions'] ) {

                    // Reset Daily Limit 
                    $reset_date = get_post_meta ( $wdpRuleID, 'wdpDailyUsageLimitResetDate', true );
                    if ( $reset_date === $curr_date ) {
                        $numb = intval ( get_post_meta ( $wdpRuleID, 'wdpDailyUsageLimit', true ) ) + 1;
                    } else {
                        $numb = 1;
                        update_post_meta ( $wdpRuleID, 'wdpDailyUsageLimitResetDate', $curr_date );
                    }
                    update_post_meta ( $wdpRuleID, 'wdpDailyUsageLimit', $numb );

                }

                if ( false === get_user_meta ( $user_id, 'wdpDailyUserData', true ) ) {

                    $wdpDailyUserData[] = array( 'rule' => $wdpRuleID, 'count' => 1, 'date' => $curr_date ); 
                    add_user_meta ( $user_id, 'wdpDailyUserData', serialize ( $wdpDailyUserData ) ); 

                } else {

                    $wdpDailyUserData = unserialize ( get_user_meta ( $user_id, 'wdpDailyUserData', true ) );
                    $match = array_search ( $wdpRuleID, array_column ( $wdpDailyUserData, 'id' ) );

                    if ( $match >= 0 && $match != false ) { 
                        $user_reset_date = $wdpDailyUserData[$match]['date'];
                        if ( $user_reset_date === $curr_date ) {
                            $temp = intval ( $wdpDailyUserData[$match]['count'] );
                            $temp = $temp + 1;
                            $wdpDailyUserData[$match]['count'] = $temp;
                        } else {
                            $wdpDailyUserData[$match]['count'] = 1;
                        }
                    } else {
                        $wdpDailyUserData[] = array( 'rule' => $wdpRuleID, 'count' => 1, 'date' => $curr_date ); 
                    }
                    update_user_meta ( $user_id, 'wdpDailyUserData', serialize ( $wdpDailyUserData ) );

                }

            }

            if ( $user_id && $user_id != 0 && true === $usage_config['user_role_restriction'] && ( $usage_config['per_user_limit'] > 0 || $usage_config['per_user_limit'] == '' ) ) {
                $user_roles_limit = unserialize($usage_config['users_limit']);
                foreach ( $user_roles_limit as $user_role_limit ) {
                    if ( in_array( $user_role_limit['user_role'], $user_meta->roles ) ) {
                        if ( false === get_post_meta( $wdpRuleID, 'wdpRoleLimit_'.$user_role_limit['user_role'], true ) ) {
                            add_post_meta ( $wdpRuleID, 'wdpRoleLimit_'.$user_role_limit['user_role'], 1 ); 
                        } else {
                            $numb = intval ( get_post_meta( $wdpRuleID, 'wdpRoleLimit_'.$user_role_limit['user_role'], true ) ) + 1;
                            update_post_meta ( $wdpRuleID, 'wdpRoleLimit_'.$user_role_limit['user_role'], $numb );
                        }
                    }
                }
            }

        }

    }

    // User Restrictions
    public function check_usage_restrctions ( $rule )
    {   

        $id = $rule['id'];
        $usage_config = get_post_meta ( $id, 'user_config', true );

        if ( is_user_logged_in() && $usage_config != '') {

            $user_id    = get_current_user_id();
            $user_meta  = get_userdata ( $user_id ); 
            $userflag   = false;

            if ( true === $usage_config['user_restrictions'] ) {

                // return true;

                $wdpSingleUserLimit = $usage_config['per_user_limit'];
                $wdpUsageLimit      = get_post_meta( $id, 'wdpUsageLimit', true ) ? get_post_meta( $id, 'wdpUsageLimit', true ) : 0;                                                                         
                $wdpUserData        = get_user_meta ( $user_id, 'wdpUserData', true ) ? unserialize ( get_user_meta ( $user_id, 'wdpUserData', true ) ) : [];;

                if ( ( $usage_config['usage_limit'] != '' && ( $wdpUsageLimit >= $usage_config['usage_limit'] ) ) || ( ( $usage_config['user_role_restriction'] == true ) && $wdpUsageLimit && array_key_exists ( $id, $wdpUserData ) && ( $wdpUserData[$id] >= $wdpSingleUserLimit ) ) || ( $wdpSingleUserLimit != '' && ( array_key_exists ( $id, $wdpUserData ) && $wdpUserData[$id] >= $wdpSingleUserLimit ) ) ) {

                    return false;

                } else if ( true === $usage_config['daily_limit_switch']  ) { // Daily Limit

                    $curr_date              = date ( "Y-m-d" );

                    $wdpDailyLimit          = get_post_meta( $id, 'wdpDailyUsageLimit', true ) ? (int)get_post_meta( $id, 'wdpDailyUsageLimit', true ) : false;
                    $wdpDailyUserData       = get_user_meta ( $user_id, 'wdpDailyUserData', true ) ? unserialize ( get_user_meta ( $user_id, 'wdpDailyUserData', true ) ) : [];
                    $wdpuindex              = array_search ( $id, array_column ( $wdpDailyUserData, 'id' ) );
                    $wdpDailyLimitSet       = $usage_config['daily_usage_limit'] ? (int)$usage_config['daily_usage_limit'] : '';   
                    $wdpDailyUserLimit      = $wdpuindex ? (int)$wdpDailyUserData[$wdpuindex]['count'] : 0; 
                    $wdpDailyULDate         = $wdpuindex ? $wdpDailyUserData[$wdpuindex]['date'] : $curr_date;
                    $wdpDailyUserLimitSet   = $usage_config['daily_user_limit'] ? (int)$usage_config['daily_user_limit'] : '';   

                    if ( false === $wdpDailyLimit || ( $wdpDailyUserLimitSet == '' && ( $wdpDailyLimitSet == '' || ( $wdpDailyLimitSet >= 0 && $wdpDailyLimitSet > $wdpDailyLimit ) ) ) || ( $wdpDailyUserLimitSet != '' && ( $wdpDailyUserLimitSet >= 0 && ( $wdpDailyUserLimitSet > $wdpDailyLimit ) ) || ( $wdpDailyULDate != $curr_date ) ) ) { 
                        
                        $userflag = true;
    
                    } else {

                        return false;

                    }

                } else if ( true === $usage_config['user_role_restriction'] ) { 

                    $user_roles_limit = unserialize($usage_config['users_limit']); 
                    foreach ( $user_roles_limit as $user_role_limit ) { 

                        if ( in_array( $user_role_limit['user_role'], $user_meta->roles ) && $userflag === false ) { 
                            if( get_post_meta( $id, 'wdpRoleLimit_'.$user_role_limit['user_role'], true ) >= $user_role_limit['limit'] ) {
                                $userflag = false;
                            } else {
                                $userflag = true;
                            }
                        } else if ( !in_array( $user_role_limit['user_role'], $user_meta->roles ) && $userflag === false ) { 
                            $userflag = false;
                        }

                    } 

                    return $userflag;

                }
            }

            return true;

        } else if ( $usage_config == '' || false === $usage_config['user_restrictions'] || $usage_config['user_restrictions'] == '' ) {

            return true;

        } else {

            return false;
            
        }

    }

    // Shortcode
    function wdpShortcode ( $atts ) { 
  
        // Trigger Get Price @@ 3.3.4
        // if ( WC()->cart->cart_contents_count == 0 ) { // Load recent product
        //     global $wpdb;
        //     $wdpRandID      = $wpdb->get_col( "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'product' AND post_status = 'publish' LIMIT 1" );
        //     $wdpRandID      = $wdpRandID[0];
        //     $wdpRandProd    = wc_get_product ( $wdpRandID ); 
        //     $wdpRandProd->get_price(); 
        // }
        // End

        // Load discount rules
        $this->load_rules();

        $all_products = [];
        
        if ( $this->discount_rules != false && sizeof($this->discount_rules) >= 1 ) { 

            $shortcode_listing  = get_option('awdp_shortcode_listing') ? get_option('awdp_shortcode_listing') : 'carousel';
            $listing_pagination = get_option('awdp_listing_pagination') ? get_option('awdp_listing_pagination') : false;
            $listing_limit      = get_option('awdp_listing_limit') ? get_option('awdp_listing_limit') : 3;
            $listing_columns    = get_option('awdp_listing_columns') ? get_option('awdp_listing_columns') : 9;
            $result             = '';

            extract(shortcode_atts(
                array(
                    'id' => '',
                    'thumb' => '',
                ), $atts)
            );

            /*
            * Carousel / Product Listing (New)
            * Form version 4
            */
            if ( $shortcode_listing == 'carousel' ) {

                $result             = '<div class="wdp_container">'; // Listing Container
                $set_rules          = [];
                $set_type           = '';
                $awdp_uploadPath    = wp_get_upload_dir(); 
                $awdp_wcThumb       = $awdp_uploadPath['baseurl'].'/woocommerce-placeholder-300x300.png'; 
                $discount_rules     = $this->discount_rules;

                array_multisort( array_column($discount_rules, "type"), SORT_ASC, $discount_rules );

                if ( $id == '' ) {
                    $set_rules = $discount_rules;
                } else {
                    $rule_ids = explode ( ',', $id );
                    $set_rules = $discount_rules;
                    $temp = [];
                    foreach ( $rule_ids as $rule_id ) {
                        $match = array_search ( $rule_id, array_column ( $discount_rules, 'id' ) );
                        if ( $match >= 0 && $match != false ) { 
                            $temp[] = $set_rules[$match];
                        }
                    }
                    $set_rules = array_values ( $temp );
                }

                if ( $set_rules && !is_admin() ) { 
                    
                    foreach ( $set_rules as $rule ) { 

                        $wdp_short = get_post_meta($rule['id'], 'wdp_shortcode', true); 
                        $wdp_title = $wdp_short['title'] ? $wdp_short['title'] : get_the_title ( $rule['id'] );
                        $wdp_desc = $wdp_short['desc'];
                        $wdp_picture = $wdp_short['picture'];

                        $cont = '<div class="wdpGiftWrap woocommerce">';
                        $cont .= '<h3>'.$wdp_title.'</h3>';
                        $cont .= $wdp_desc ? wpautop ( $wdp_desc ) : '';

                        if ( $rule['type'] == 'gift' ) {

                            $gift_combinations = $rule['gift_combinations'];
                            foreach ( $gift_combinations as $gift_combination ) {
                                $purchases  = isset ( $gift_combination['from_list'] ) ? $gift_combination['from_list'] : [];
                                $get        = isset ( $gift_combination['to_list'] ) ? $gift_combination['to_list'] : [];
                                $cont      .= '<div class="wdpSingle">'; 
                                $buy        = '<div class="owl-carousel owl-theme wdp-slider">'; 
                                foreach ($purchases as $purchase) {
                                    $product_id     = $purchase;
                                    $image          = has_post_thumbnail ( $product_id ) ? get_the_post_thumbnail_url ( $product_id , 'post-thumbnail' ) : $awdp_wcThumb;
                                    $product_name   = get_the_title ( $purchase ); 
                                    $product_sku    = ''; 
                                    $buy .= '<div class="item">';  
                                    $buy .= '<a href="'.get_the_permalink ( $product_id ).'"><img src="'.$image.'" alt="'.$product_name.'" class="wdp-thumb" /><h2 class="woocommerce-loop-product__title">'.$product_name.'</h2></a>';
                                    // $buy .= '<a href="'.get_home_url().'/?post_type='.AWDP_WC_PRODUCTS.'&amp;add-to-cart='.$product_id.'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.$product_id.'" data-product_sku="'.$product_sku.'" aria-label="Add â€œ'.$product_name.'â€ to your cart" rel="nofollow">Add to cart</a>';
                                    $buy .= '</div>';
                                }
                                $buy  .= '</div>';
                                $cont .= $buy;
                                $cont .= '<div class="wdpSlides">';
                                $cont .= '<div class="owl-carousel owl-theme wdp-slider">';
                                foreach ( $get as $product ) {  
                                    $product_cart_id = WC()->cart->generate_cart_id ( $product );
                                    $in_cart = WC()->cart->find_product_in_cart ( $product_cart_id ); 
                                    if( $in_cart == '' && isset(WC()->cart) ) { // Check in cart
                                        $product_id     = $product;
                                        $image          = get_the_post_thumbnail_url( $product_id , 'post-thumbnail' );
                                        $product_name   = get_the_title($product); 
                                        $product_sku    = '';   
                                        $cont .= '<div class="item">';
                                        $cont .= '<a href="'.get_the_permalink( $product_id ).'"><img src="'.$image.'" alt="'.$product_name.'" class="wdp-thumb" /><h2 class="woocommerce-loop-product__title">'.$product_name.'</h2></a>';
                                        // $cont .= '<a href="'.get_home_url().'/?post_type='.AWDP_WC_PRODUCTS.'&amp;add-to-cart='.$product_id.'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.$product_id.'" data-product_sku="'.$product_sku.'" aria-label="Add â€œ'.$product_name.'â€ to your cart" rel="nofollow">Add to cart</a>';
                                        $cont .= '</div>';
                                    }
                                }
                                $cont .= '</div>';
                                $cont .= '</div>';
                                $cont .= '</div>';
                            }

                        } else if ( $rule['type'] == 'bogo' ) {  // @@ 3.3.1

                            $bogo_buy = $rule['bogo_buy'];
                            $bogo_get = $rule['bogo_get'];
                            $bogo_discount_type = $rule['bogo_type'];
                            $bogo_type = $rule['bogo_rule_type'];
                            $bogo_value = $rule['bogo_value'];
                            $list_key = 'list_'; 

                            if ( $bogo_type == 'bogo_rule_same' ) {

                                $purchases = $rule['bogo_x_prods'];
                                $purchase_ids = [];
                                // Buy
                                if ( $purchases == 'null' || $purchases == '' ) { // Any Product
                                    $purchase_ids = $all_products; // Full products
                                } else if ( strpos ( $purchases, $list_key ) !== false ) { 
                                    $list_id = intval ( str_replace ( $list_key, '', $purchases ) ); 
                                    $list_prods = $this->wdpProductList ( $list_id, false, true, 10 );
                                    $purchase_ids = array_unique ( array_merge ( $list_prods, $purchase_ids ) );
                                } else {
                                    $purchase_ids = [$rule['bogo_x_prods']];
                                }

                                $bogo_cont = '';

                                if ( $rule['bogo_type'] == 'off_percentage' ) {
                                    $bogo_cont .= '<p class="wdp_dis">Buy 1 get 1 at ' . $rule['bogo_value'] . '% discount</p>';
                                } else {
                                    $bogo_cont .= '<p class="wdp_dis">Buy 1 get 1</p>';
                                }

                                $cont .= '<div class="wdpCarousel">'; 
                                $cont .= '<div class="owl-carousel owl-theme wdp-carousel">';
                                foreach ($purchase_ids as $purchase) {
                                    $product_id = $purchase;
                                    $image = has_post_thumbnail ( $product_id ) ? get_the_post_thumbnail_url ( $product_id , 'post-thumbnail' ) : $awdp_wcThumb;
                                    $product_name = get_the_title($purchase); 
                                    $product_sku = ''; 
                                    $cont .= '<div class="item">';
                                    $cont .= '<a href="'.get_the_permalink( $product_id ).'"><img src="'.$image.'" alt="'.$product_name.'" class="wdp-thumb" /></a>';
                                    $cont .= '<h2 class="woocommerce-loop-product__title">'.$product_name.'</h2>';
                                    $cont .= $bogo_cont;
                                    $cont .= '<a href="'.get_home_url().'/?post_type='.AWDP_WC_PRODUCTS.'&amp;add-to-cart='.$product_id.'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.$product_id.'" data-product_sku="'.$product_sku.'" aria-label="Add â€œ'.$product_name.'â€ to your cart" rel="nofollow">Add to cart</a>';
                                    $cont .= '</div>';
                                }
                                $cont .= '</div>';
                                $cont .= '</div>';  

                            } else if ( $bogo_type == 'bogo_rule_nth' ) {

                                $purchases = $rule['bogo_n_prods'];
                                $purchase_ids = [];
                                // Buy
                                if ( $purchases == 'null' || $purchases == '' ) { // Any Product
                                    $purchase_ids = $all_products; // Full products
                                } else if ( strpos ( $purchases, $list_key ) !== false ) { 
                                    $list_id = intval ( str_replace ( $list_key, '', $purchases ) ); // 
                                    $list_prods = $this->wdpProductList ( $list_id, false, true, 10 );
                                    $purchase_ids = array_unique ( array_merge ( $list_prods, $purchase_ids ) );
                                } else {
                                    $purchase_ids = [$rule['bogo_n_prods']];
                                }

                                $payAppend = ( $rule['bogo_n_count'] == 1 ) ? 'st' : ( ( $rule['bogo_n_count'] == 2 ) ? 'nd' : ( ( $rule['bogo_n_count'] == 3 ) ? 'rd' : 'th' ) );
                                $payQnt = (int)$rule['bogo_n_count'] - 1;
                                $bogo_cont = '';

                                if ( $rule['bogo_type'] == 'off_percentage' ) {
                                    $bogo_cont .= '<p class="wdp_dis">Buy ' . $payQnt . ' get ' . $rule['bogo_value'] . '%  discount on ' . $rule['bogo_n_count'] . $payAppend . ' item</p>';
                                } else {
                                    $bogo_cont .= '<p class="wdp_dis">Buy ' . $payQnt . ' get ' . $rule['bogo_n_count'] . $payAppend . ' item FREE</p>';
                                }

                                $cont .= '<div class="wdpCarousel">'; 
                                $cont .= '<div class="owl-carousel owl-theme wdp-carousel">';
                                foreach ($purchase_ids as $purchase) {
                                    $product_id = $purchase;
                                    $image = has_post_thumbnail ( $product_id ) ? get_the_post_thumbnail_url ( $product_id , 'post-thumbnail' ) : $awdp_wcThumb;
                                    $product_name = get_the_title($purchase); 
                                    $product_sku = ''; 
                                    $cont .= '<div class="item">';
                                    $cont .= '<a href="'.get_the_permalink( $product_id ).'"><img src="'.$image.'" alt="'.$product_name.'" class="wdp-thumb" /></a>';
                                    $cont .= '<h2 class="woocommerce-loop-product__title">'.$product_name.'</h2>';
                                    $cont .= $bogo_cont;
                                    $cont .= '<a href="'.get_home_url().'/?post_type='.AWDP_WC_PRODUCTS.'&amp;add-to-cart='.$product_id.'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.$product_id.'" data-product_sku="'.$product_sku.'" aria-label="Add â€œ'.$product_name.'â€ to your cart" rel="nofollow">Add to cart</a>';
                                    $cont .= '</div>';
                                }
                                $cont .= '</div>';
                                $cont .= '</div>';  

                            } else if ( $bogo_type == 'bogo_rule_payn' ) {

                                $purchases = $rule['bogo_payn_prods'];
                                $purchase_ids = [];
                                // Buy
                                if ( $purchases == 'null' || $purchases == '' ) { // Any Product
                                    $purchase_ids = $all_products; // Full products
                                } else if ( strpos ( $purchases[0], $list_key ) !== false ) { 
                                    $list_id = intval ( str_replace ( $list_key, '', $purchases[0] ) ); // 
                                    $list_prods = $this->wdpProductList ( $list_id, false, true, 10 );
                                    $purchase_ids = array_unique ( array_merge ( $list_prods, $purchase_ids ) );
                                } else {
                                    $purchase_ids = $rule['bogo_payn_prods'];
                                }

                                $payText = ( $rule['item_cnt'] > 1 ) ? 'items' : 'item';
                                $bogo_cont = '';

                                foreach ( $rule['bogopayranges'] as $payRange ) {
                                    if ( $payRange['payn_type'] == 'off_percentage' ) {
                                        $bogo_cont .= '<p class="wdp_dis">Buy ' . $payRange['min_qnty'] . ' and get ' . $payRange['payn_value'] . '%  discount on ' . $payRange['item_cnt'] . ' ' . $payText . '</p>';
                                    } else {
                                        $bogo_cont .= '<p class="wdp_dis">Buy ' . $payRange['min_qnty'] . ' and get ' . $payRange['item_cnt'] . ' FREE ' . $payText . '</p>';
                                    }
                                }

                                $cont .= '<div class="wdpCarousel">'; 
                                $cont .= '<div class="owl-carousel owl-theme wdp-carousel">';
                                foreach ($purchase_ids as $purchase) {
                                    $product_id = $purchase;
                                    $image = has_post_thumbnail ( $product_id ) ? get_the_post_thumbnail_url ( $product_id , 'post-thumbnail' ) : $awdp_wcThumb;
                                    $product_name = get_the_title($purchase); 
                                    $product_sku = ''; 
                                    $cont .= '<div class="item">';
                                    $cont .= '<a href="'.get_the_permalink( $product_id ).'"><img src="'.$image.'" alt="'.$product_name.'" class="wdp-thumb" /></a>';
                                    $cont .= '<h2 class="woocommerce-loop-product__title">'.$product_name.'</h2>';
                                    $cont .= $bogo_cont;
                                    $cont .= '<a href="'.get_home_url().'/?post_type='.AWDP_WC_PRODUCTS.'&amp;add-to-cart='.$product_id.'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.$product_id.'" data-product_sku="'.$product_sku.'" aria-label="Add â€œ'.$product_name.'â€ to your cart" rel="nofollow">Add to cart</a>';
                                    $cont .= '</div>';
                                }
                                $cont .= '</div>';
                                $cont .= '</div>';  

                            } else if ( $bogo_type == 'bogo_rule_cheapest' ) {

                                $purchases = $rule['bogo_cheap_prods'];
                                $purchase_ids = [];
                                // Buy
                                if ( $purchases == 'null' || $purchases == '' ) { // Any Product
                                    $purchase_ids = $all_products; // Full products
                                } else if ( strpos ( $purchases[0], $list_key ) !== false ) { 
                                    $list_id = intval ( str_replace ( $list_key, '', $purchases[0] ) ); // 
                                    $list_prods = $this->wdpProductList ( $list_id, false, true, 10 );
                                    $purchase_ids = array_unique ( array_merge ( $list_prods, $purchase_ids ) );
                                } else {
                                    $purchase_ids = $rule['bogo_cheap_prods'];
                                }

                                $getText = ( $rule['bogo_cheap_get'] > 1 ) ? 'products' : 'product';
                                $buyText = ( $rule['bogo_cheap_buy'] > 1 ) ? 'products' : 'product';

                                if ( $rule['bogo_cheap_type'] == 'off_percentage' ) {
                                    $bogo_cont = '<p class="wdp_dis">Buy ' . $rule['bogo_cheap_buy'] . ' or more ' . $buyText . '  and get the cheapest ' . $rule['bogo_cheap_get'] . ' ' . $getText . ' at ' . $rule['bogo_cheap_value'] . '% OFF</p>';
                                } else {
                                    $bogo_cont = '<p class="wdp_dis">Buy ' . $rule['bogo_cheap_buy'] . ' or more ' . $buyText . '  and get the cheapest ' . $rule['bogo_cheap_get'] . ' ' . $getText . ' FREE</p>';
                                }

                                $cont .= '<div class="wdpCarousel">'; 
                                $cont .= '<div class="owl-carousel owl-theme wdp-carousel">';
                                foreach ($purchase_ids as $purchase) {
                                    $product_id = $purchase;
                                    $image = has_post_thumbnail ( $product_id ) ? get_the_post_thumbnail_url ( $product_id , 'post-thumbnail' ) : $awdp_wcThumb;
                                    $product_name = get_the_title($purchase); 
                                    $product_sku = ''; 
                                    $cont .= '<div class="item">';
                                    $cont .= '<a href="'.get_the_permalink( $product_id ).'"><img src="'.$image.'" alt="'.$product_name.'" class="wdp-thumb" /></a>';
                                    $cont .= '<h2 class="woocommerce-loop-product__title">'.$product_name.'</h2>';
                                    $cont .= $bogo_cont;
                                    $cont .= '<a href="'.get_home_url().'/?post_type='.AWDP_WC_PRODUCTS.'&amp;add-to-cart='.$product_id.'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.$product_id.'" data-product_sku="'.$product_sku.'" aria-label="Add â€œ'.$product_name.'â€ to your cart" rel="nofollow">Add to cart</a>';
                                    $cont .= '</div>';
                                }
                                $cont .= '</div>';
                                $cont .= '</div>';   

                            } else {

                                $bogo_combinations = $rule['bogo_combinations'];
                                foreach ( $bogo_combinations as $bogo_combination ) {
                                    $purchases = $bogo_combination['from_list']; 
                                    $get = $bogo_combination['to_list'];
                                    // @@ Shortcode Fix
                                    // Buy
                                    if ( $purchases == 'null' || $purchases == '' ) { // Any Product
                                        $purchases = $all_products; // Full products
                                    } else if ( strpos ( $purchases[0], $list_key ) === true ) {
                                        $list_id = intval ( str_replace ( $list_key, '', $purchases[0] ) ); // 
                                        $list_prods = $this->wdpProductList ( $list_id, false, true, 10 );
                                        $purchases = array_unique ( array_merge ( $list_prods, $purchases ) );
                                    }
                                    // Get
                                    if ( $get == 'null' || $get == '' ) { // Any Product
                                        $get = $all_products; // Full products
                                    } else if ( strpos ( $get[0], $list_key ) === true ) {
                                        $list_id = intval ( str_replace ( $list_key, '', $get[0] ) );
                                        $list_prods = $this->wdpProductList ( $list_id, false, true, 10 );
                                        $get = array_unique ( array_merge ( $list_prods, $get ) );
                                    } 
                                    //  End
                                    $cont .= '<div class="wdpSingle">'; 
                                    $buy = '<div class="owl-carousel owl-theme wdp-slider">';
                                    foreach ($purchases as $purchase) {
                                        $product_id = $purchase;
                                        $image = has_post_thumbnail ( $product_id ) ? get_the_post_thumbnail_url ( $product_id , 'post-thumbnail' ) : $awdp_wcThumb;
                                        $product_name = get_the_title($purchase); 
                                        $product_sku = ''; 
                                        $buy .= '<div class="item">';
                                        $buy .= '<a href="'.get_the_permalink( $product_id ).'"><img src="'.$image.'" alt="'.$product_name.'" class="wdp-thumb" /><h2 class="woocommerce-loop-product__title">'.$product_name.'</h2></a>';
                                        // $buy .= '<a href="'.get_home_url().'/?post_type='.AWDP_WC_PRODUCTS.'&amp;add-to-cart='.$product_id.'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.$product_id.'" data-product_sku="'.$product_sku.'" aria-label="Add â€œ'.$product_name.'â€ to your cart" rel="nofollow">Add to cart</a>';
                                        $buy .= '</div>';
                                    }
                                    $buy .= '</div>';
                                    $cont .= $buy ;
                                    // if ( $bogo_type == 'off_percentage' ) {
                                    //     $cont .= $bogo_value. '% off on </p>';
                                    // } else {
                                    //     $cont .= $bogo_value. ' off on </p>'; 
                                    // }
                                    $cont .= '<div class="wdpSlides">';
                                    $cont .= '<div class="owl-carousel owl-theme wdp-slider">';
                                    foreach ( $get as $product ) {  
                                        $product_cart_id = WC()->cart->generate_cart_id( $product );
                                        $in_cart = WC()->cart->find_product_in_cart( $product_cart_id ); 
                                        if( $in_cart == '' && isset(WC()->cart) ) { // Check in cart
                                            $product_id = $product;
                                            $image = get_the_post_thumbnail_url( $product_id , 'post-thumbnail' );
                                            $product_name = get_the_title($product); 
                                            $product_sku = '';   
                                            $cont .= '<div class="item">';
                                            $cont .= '<a href="'.get_the_permalink( $product_id ).'"><img src="'.$image.'" alt="'.$product_name.'" class="wdp-thumb" /><h2 class="woocommerce-loop-product__title">'.$product_name.'</h2></a>';
                                            // $cont .= '<a href="'.get_home_url().'/?post_type='.AWDP_WC_PRODUCTS.'&amp;add-to-cart='.$product_id.'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.$product_id.'" data-product_sku="'.$product_sku.'" aria-label="Add â€œ'.$product_name.'â€ to your cart" rel="nofollow">Add to cart</a>';
                                            $cont .= '</div>';
                                        }
                                    }
                                    $cont .= '</div>';
                                    $cont .= '</div>';
                                    $cont .= '</div>';
                                }

                            } 

                        } else if ( $rule['type'] == 'cart_quantity') { 

                            $cont .= '';
                            $prod_list = $this->get_list_products($rule);

                            $quantity_rules = $rule['quantity_rules'];
                            $qn_max = max ( array_column ( $quantity_rules, 'dis_value') );
                            $max_desc = '<p class="wdp_max">Up to ' . $qn_max . '% OFF</p>';
                            $qn_cont = '';
                            // $rule['quantity_type'] == 'type_cart'
                            foreach ( $quantity_rules as $quantity_rule ) { 
                                if ( $quantity_rule['dis_type'] == 'percentage' ) {
                                    if ( $quantity_rule['start_range'] == $quantity_rule['end_range'] ) {
                                        $qn_cont .= '<p class="wdp_dis">Buy ' . $quantity_rule['start_range'] . ' Get ' . $quantity_rule['dis_value'] . '% OFF</p>';
                                    } else {
                                        $qn_cont .= '<p class="wdp_dis">Buy ' . $quantity_rule['start_range'] . ' to ' . $quantity_rule['end_range'] . ' Get ' . $quantity_rule['dis_value'] . '% OFF</p>';
                                    }
                                } else {
                                    if ( $quantity_rule['start_range'] == $quantity_rule['end_range'] ) {
                                        $qn_cont .= '<p class="wdp_dis">Buy ' . $quantity_rule['start_range'] . ' Get Flat ' . $quantity_rule['dis_value'] . ' OFF</p>';
                                    } else {
                                        $qn_cont .= '<p class="wdp_dis">Buy ' . $quantity_rule['start_range'] . ' to ' . $quantity_rule['end_range'] . ' Get Flat ' . $quantity_rule['dis_value'] . ' OFF</p>';
                                    }
                                }
                            }
                            if($prod_list) {
                                $cont .= '<div class="wdpCarousel">'; 
                                $cont .= '<div class="owl-carousel owl-theme wdp-carousel">';
                                foreach($prod_list as $prod){
                                    $product_id = $prod;
                                    $image = has_post_thumbnail ( $product_id ) ? get_the_post_thumbnail_url ( $product_id , 'post-thumbnail' ) : $awdp_wcThumb;
                                    $product_name = get_the_title($product_id); 
                                    $product_sku = '';   
                                    $cont .= '<div class="item">';  
                                    $cont .= '<a href="'.get_the_permalink( $product_id ).'"><img src="'.$image.'" alt="'.$product_name.'" class="wdp-thumb" /></a>';
                                    $cont .= $max_desc;
                                    $cont .= '<h2 class="woocommerce-loop-product__title">'.$product_name.'</h2>';
                                    $cont .= $qn_cont;
                                    $cont .= '<a href="'.get_home_url().'/?post_type='.AWDP_WC_PRODUCTS.'&amp;add-to-cart='.$product_id.'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.$product_id.'" data-product_sku="'.$product_sku.'" aria-label="Add â€œ'.$product_name.'â€ to your cart" rel="nofollow">Add to cart</a>';
                                    $cont .= '</div>';
                                } 
                                $cont .= '</div>';
                                $cont .= '</div>';   
                            }       

                        } else if ( $rule['type'] == 'percent_product_price' || $rule['type'] == 'fixed_product_price' ) { 

                            // $rule['type'] == 'percent_total_amount';
                            $val = $rule['discount'];
                            $prod_list = $this->get_list_products($rule);

                            if($prod_list) {
                                $discount_desc = ( $rule['type'] == 'percent_total_amount' ) ? 'Flat ' . $val . '% OFF' : 'Flat ' . $val . ' OFF';
                                $cont .= '<div class="wdpCarousel">'; 
                                $cont .= '<div class="owl-carousel owl-theme wdp-carousel">';
                                foreach($prod_list as $prod){
                                    $product_id = $prod;
                                    $image = has_post_thumbnail ( $product_id ) ? get_the_post_thumbnail_url ( $product_id , 'post-thumbnail' ) : $awdp_wcThumb;
                                    $product_name = get_the_title($product_id); 
                                    $product_sku = '';   
                                    $cont .= '<div class="item">';
                                    $cont .= '<a href="'.get_the_permalink( $product_id ).'"><img src="'.$image.'" alt="'.$product_name.'" class="wdp-thumb" /></a>';
                                    $cont .= '<p class="wdp_max">'.$discount_desc.'</p>';
                                    $cont .= '<h2 class="woocommerce-loop-product__title">'.$product_name.'</h2>';
                                    $cont .= '<a href="'.get_home_url().'/?post_type='.AWDP_WC_PRODUCTS.'&amp;add-to-cart='.$product_id.'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.$product_id.'" data-product_sku="'.$product_sku.'" aria-label="Add â€œ'.$product_name.'â€ to your cart" rel="nofollow">Add to cart</a>';
                                    $cont .= '</div>';
                                } 
                                $cont .= '</div>';
                                $cont .= '</div>';   
                            } 


                        } 
                        $cont .= '<div class="clearfix"></div>';
                        $cont .= '</div>';
                        echo $cont;

                    }

                }
                
                $result = '</div>'; // Container End

            } else { 

                $set_rules          = [];
                $set_type           = ''; 
                $discount_rules     =  $this->discount_rules;

                array_multisort( array_column($discount_rules, "type"), SORT_ASC, $discount_rules );

                if ( $id == '' ) {
                    $set_rules = $discount_rules;
                } else {
                    $rule_ids = explode ( ',', $id );
                    $set_rules = $discount_rules;
                    $temp = [];
                    if ( is_array($rule_ids) ) {
                        foreach ( $rule_ids as $rule_id ) { 
                            $match = array_search ( $rule_id, array_column ( $discount_rules, 'id' ) );
                            if ( $match !== false ) {  
                                $temp[] = $set_rules[$match];
                            } 
                        }
                        $set_rules = array_values ( $temp );
                    } else {
                        $set_rules = [$id]; 
                    }
                } 

                if ( $set_rules && !is_admin() ) { 

                    $prodids = [];

                    foreach ( $set_rules as $rule ) {

                        $prodids = array_merge ( $prodids, $this->get_list_products($rule) );

                    }  

                    if ( !empty ( $prodids ) ) { 

                        $prodids = array_values ( array_unique ( $prodids, SORT_REGULAR ) );
                        $prodids = implode ( ", ", $prodids ); 
                        echo do_shortcode('[products limit="'.$listing_limit.'" columns="'.$listing_columns.'" ids="'.$prodids.'" paginate="'.$listing_pagination.'"]');

                    }

                }

            }

            return $result;
        }

    }

    public function get_list_products($rule)
    { // For Shortcode Listing

        if ($rule) { 

            $product_lists = [];

            if ( (int)$rule['product_list'] != 0 ) {

                $this->set_product_list();
                $listID = $rule['product_list'];
                $product_lists = $this->product_lists;  
                $product_lists = $product_lists[$listID]; 

            } else {

                $prod_args = array(
                    'posts_per_page'   => 10,
                    'offset'           => 0,
                    'orderby'          => 'date',
                    'fields'           => 'ids',
                    'order'            => 'DESC',
                    'post_type'        => AWDP_WC_PRODUCTS
                );
                $prod_list = get_posts($prod_args);
                foreach( $prod_list as $prod ){ 
                    $product_lists[] = $prod;
                }

            }

            return array_values ( $product_lists );

        }

    }
    
    // Get Product List
    public function wdpProductList ( $list = false, $prods_in_cart = false, $hide_variations = false, $limit = -1, $skip_out_stock = false ) { 

        // if ( !in_array ( $list, $this->bogo_product_lists ) {

            $product_lists = [];

            if( $list == false ) {

                if ( $limit > 0 ) {

                    global $wpdb;
                    $selected_products = $wpdb->get_col( "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type IN ('product', 'product_variaton') AND post_status = 'publish' LIMIT {$limit}" );
                    $product_lists = $selected_products;
                    
                } else {
                    if ( WC()->session->get( 'wdp_list_products' ) ) {

                        $product_lists = WC()->session->get( 'wdp_list_products' );

                    } else {

                        global $wpdb;
                        $selected_products = $wpdb->get_col( "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type IN ('product', 'product_variaton') AND post_status = 'publish'" );

                        $product_list_variations = ( $hide_variations == false ) ? ( $this->wdpGetVariations ( $selected_products, $list ) ? $this->wdpGetVariations ( $selected_products, $list ) : [] ) : [];
                        $product_lists = array_merge ( $selected_products, $product_list_variations );

                        WC()->session->set('wdp_list_products', $product_lists);

                    }
                }

            } else { 
                
                $post_ids = get_posts(
                                array(
                                    'fields' => 'ids',
                                    'post_type' => AWDP_PRODUCT_LIST,
                                    'post__in' => array($list),
                                    'posts_per_page' => -1,
                                )
                            );

                foreach ($post_ids as $id) {

                    $selected_products = [];

                    $list_type = get_post_meta($id, 'list_type', true);
                    $other_config = get_post_meta($id, 'product_list_config', true);
                
                    $product_lists[$id] = array();
                    
                    if ( 'dynamic_request' == $list_type ) {

                        $tax_rules = ($other_config['rules']);
                        $tax_rules = ($tax_rules && is_array($tax_rules) && !empty($tax_rules)) ? $tax_rules : false;
                        $excludedProducts = ($other_config['excludedProducts']);
                        
                        if ( $skip_out_stock ) {
                            $args = array(
                                'post_type'         => AWDP_WC_PRODUCTS,
                                'fields'            => 'ids',
                                'post_status'       => 'publish',
                                'posts_per_page'    => $limit,
                                'meta_query'        => array(
                                                            array(
                                                                'key'     => '_stock_status',
                                                                'value'   => 'outofstock',
                                                                'compare' => '!='
                                                            )
                                                        )
                            );
                        } else {
                            $args = array(
                                'post_type'         => AWDP_WC_PRODUCTS,
                                'fields'            => 'ids',
                                'post_status'       => 'publish',
                                'posts_per_page'    => $limit
                            );
                        } 
                        
                        if ( $excludedProducts ) {
                            $args['post__not_in'] = $excludedProducts;
                        }

                        if ( false !== $tax_rules ) { 

                            if ( isset($tax_rules[0]['rules']) && is_array($tax_rules[0]['rules']) ) {
                                $tax_query = array(
                                    'relation' => ('or' == strtolower($other_config['taxRelation'])) ? 'OR' : 'AND'
                                );
                                foreach ( array_filter($tax_rules[0]['rules']) as $tr ) { 
                                    $taxoperator = ( $tr['rule']['condition'] === 'notin' ) ? 'NOT IN' : 'IN'; 
                                    $tax_query[] = array(
                                        'taxonomy' => $tr['rule']['item'],
                                        'field' => 'term_id',
                                        'terms' => $tr['rule']['value'],
                                        'operator' => $taxoperator
                                    );
                                }
                                $args['tax_query'] = $tax_query;
                            }

                        }

                        $selected_products = get_posts ( $args );
                        $product_list_variations = ( $hide_variations == false ) ? ( $this->wdpGetVariations ( $selected_products, $list ) ? $this->wdpGetVariations ( $selected_products, $list ) : [] ) : [];
                        $product_lists = array_merge ( $selected_products, $product_list_variations );

                    // } else if ( 'variation_selection' == $list_type ) {

                    //     $variatns =[];
                    //     $variationrules = array_key_exists ( 'variationrules', $other_config ) ? $other_config['variationrules'] : '';
                    //     if ( $variationrules ) { 
                    //         foreach ( $variationrules as $variationrule ) { 
                    //             if ( $variationrule['variations'] ) { 
                    //                 $variatns = array_merge ( $variatns, $variationrule['variations'] ); 
                    //             }
                    //         } 
                    //         $variatns = array_values ( array_unique ( $variatns ) );
                    //     }

                    //     $product_lists = $variatns;

                    } else {
                        
                        $selected_products = $other_config['selectedProducts'];
                        $product_list_variations = ( $hide_variations == false ) ? ( $this->wdpGetVariations ( $selected_products, $list ) ? $this->wdpGetVariations ( $selected_products, $list ) : [] ) : [];
                        $product_lists = array_merge ( $selected_products, $product_list_variations );

                    }  

                }
                
            }

        //     $this->bogo_product_lists[] = 

        // } 

        // $product_lists = $this->bogo_product_lists[];

        return array_values ( $product_lists );

    }

    // Any Products suggestion List
    public function wdpAllProducts ( $count, $sort, $skipList, $includeList ) { 
        
        $sort       = $sort ? ( ( $sort == 'ascending' ) ? 'asc' : ( ( $sort == 'descending' ) ? 'desc' : 'rand' ) ) : 'rand';
        $count      = $count ? $count : 3; 
        $list_key   = 'list_';

        if ( !empty($skipList) && strpos ( $skipList[0], $list_key ) !== false ) { // Check if product list
            $list_id    = intval ( str_replace ( $list_key, '', $skipList[0] ) );
            $skipList   = $this->wdpProductList ( $list_id );
        }

        $orderby = ( $sort == 'rand' ) ? 'rand' : 'meta_value_num';

        $display_prods_args = array(
            'post_type'         => AWDP_WC_PRODUCTS,
            'fields'            => 'ids',
            'post_status'       => 'publish',
            'posts_per_page'    => $count,
            'meta_key'          => '_regular_price', 
            'orderby'           => $orderby,
            'order'             => $sort,
            // 'include'           => $includeList ? $includeList : array(),
            'exclude'           => $skipList ? $skipList : array(),
        ); 

        $display_prods  = get_posts($display_prods_args);  

        /*
        * Ver 5.0.4
        * changed array_merge ( $includeList, $spliced_list ) to array_merge ( $includeList, $display_prods )
        */
        if ( !empty ( $includeList ) ) {
            // $splice         = sizeof ( $includeList ); 
            // $spliced_list   = array_splice ( $display_prods, $splice ); 
            // $list           = array_merge ( $includeList, $spliced_list );  
            $list           = array_merge ( $includeList, $display_prods );  
        } else {
            $list           = $display_prods;
        }

        return $list;

    }

    // Get variations 
    public function wdpGetVariations ( $productID, $list = false ) {

        if ( $productID ) {
            if ( ( !is_array ( $productID ) && array_key_exists ( $productID, $this->productvariations ) ) || ( $list && array_key_exists ( $list, $this->productvariations ) ) ) {
                return $this->productvariations[$productID];
            } else { 
                global $wpdb;
                $productID = is_array ( $productID ) ? implode(',', $productID) : $productID; 
                $PLVariations = $wpdb->get_col("SELECT ID FROM {$wpdb->prefix}posts WHERE post_status = 'publish' AND post_parent IN ($productID) AND post_type = 'product_variation'");
                if ( $PLVariations ) {
                    $PLVariations = array_map('intval', $PLVariations); // Converting to intval @@ ver 3.3.8
                    if ( !is_array ( $productID ) ) $this->productvariations[$productID] = $PLVariations;
                    else if ( $list ) $this->productvariations[$list] = $PLVariations;
                    return $PLVariations;
                } 
            }
        }
        return false;

    }

    // Check variations
    public function wdpCheckVariation ( $productID ) {

        if( $productID ) {
            global $wpdb;
            $ParentID = $wpdb->get_col( "SELECT post_parent FROM {$wpdb->prefix}posts WHERE post_status = 'publish' AND ID = $productID AND post_type = 'product_variation'");
            if ( $ParentID ) {
                return $this->wdpGetVariations ( $ParentID );
            } 
            return false;
        }
        return false;

    }

    // Get variation parents
    public function wdpGetVariationParent ( $productID ) {

        $result = [];
        if( $productID ) {
            global $wpdb;
            // $list_key   = 'list_'; 
            // $listProds  = [];
            // foreach ( $productID as $key => $value ) {
            //     if ( strpos ( $value, $list_key ) === true ) {
            //         $listProds = array_merge ( $listProds, $this->wdpProductList ( $value ) );
            //         unset($productID[$key]);
            //     }
            // }
            // $productID  = !empty ( $listProds ) ? array_values ( array_unique ( array_merge ( $listProds, $productID ) ) ) : $productID;
            $productID  = is_array ( $productID ) ? implode(',', $productID) : $productID; 
            $Query      = $wpdb->get_col( "SELECT DISTINCT post_parent FROM {$wpdb->prefix}posts WHERE post_type = 'product_variation' AND post_status = 'publish' AND ID IN ( $productID ) ORDER BY ID ASC, post_parent ASC" );
            if ( $Query ) {
                $result = $Query;
            } 
        }
        return $result;

    }

    // Sale Badges
    public function wdpSaleBadge () {

        /*
        * @ ver 4.0.4
        */

        global $product;
        $productID      = $product->get_ID();
        $thumbnail      = '';

        // Load discount rules
        $this->load_rules();
        
        // Check if discount is active
        if ( $this->discount_rules == null )
            return ''; // Exit if no rules 

        $discount_rules = $this->discount_rules;
        $product_lists  = $this->product_lists;

        echo call_user_func_array ( 
            array ( new AWDP_badge(), 'awdp_sales_badge' ), 
            array ( $discount_rules, $product_lists, $product, $thumbnail ) 
        );
        
    }

    // CountDown timer
    public function wdpCountdownTimer () {

        if ( is_admin() ) 
            return '';

        // Load discount rules
        $this->load_rules();
        
        // Check if discount is active
        if ( $this->discount_rules == null )
            return ''; // Exit if no rules 

        $discount_rules = $this->discount_rules;
        $product_lists  = $this->product_lists;

        return call_user_func_array ( 
            array ( new AWDP_timer(), 'awdp_countdown_timer' ), 
            array ( $discount_rules, $product_lists ) 
        );

    }

    // Shipping ID
    public function wdpShippingID( $shipping_id ) {
        $packages = WC()->shipping->get_packages();
    
        foreach ( $packages as $i => $package ) {
            if ( isset( $package['rates'] ) && isset( $package['rates'][ $shipping_id ] ) ) {
                $rate = $package['rates'][ $shipping_id ];
                /* @var $rate WC_Shipping_Rate */
                return $rate->get_label();
            }
        }
    
        return '';
    }

    // Save Order Meta
    public function wdpOrderMeta ( $item_id, $values, $cart_item_key ) {

        $wdpDiscount    = $this->wdp_order_meta ? $this->wdp_order_meta : [];
        // $coupon         = get_option('awdp_fee_label') ? get_option('awdp_fee_label') : 'Discount';
        // $coupon_code    = apply_filters('woocommerce_coupon_code', $coupon);

        if( array_key_exists ( $cart_item_key, $wdpDiscount )){
            $_awdp_discounted_price = ( $wdpDiscount[$cart_item_key] ) ? $wdpDiscount[$cart_item_key] : [];
            wc_add_order_item_meta($item_id, '_awdp_discount_details', $_awdp_discounted_price);
            // wc_add_order_item_meta($item_id, '_awdp_coupon', $coupon_code);
            return;
        }

    }

    // Display Order Meta
    public function wdpDisplayOrderMeta( $item_id, $item, $product ) { 

        $wdp_discount_details   = wc_get_order_item_meta ( $item_id, '_awdp_discount_details', true ) ? wc_get_order_item_meta ( $item_id, '_awdp_discount_details', true ) : false;
        // $wdp_discount_label     = wc_get_order_item_meta($item_id, '_awdp_coupon', true);
        if ( $wdp_discount_details ) { 
            $prdID      = $product->get_ID(); 
            $wdp_meta   = '<br/><div class="wc-order-item-wdp" style="color: #888;">';
            foreach ( $wdp_discount_details as $wdp_discount_detail ) { 
                if ( $wdp_discount_detail['type'] === 'bogo' ) {
                    if ( $wdp_discount_detail['bogo'] ) {
                        foreach ( $wdp_discount_detail['bogo'] as $orderBogo ) {
                            if ( $orderBogo['product_id'] == $prdID ){
                                $wdp_meta .= ( $orderBogo['discount_type'] == 'free' ) ? '<strong>' .$orderBogo['offer_items'] .' * Free</strong>' : '<strong>' .$orderBogo['offer_items'] .' * ' .$orderBogo['discounted_price_single'];
                            }
                        }
                    }
                }
                if ( $wdp_discount_detail['type'] === 'gift' ) {
                    if ( $wdp_discount_detail['gift'] ) {
                        foreach ( $wdp_discount_detail['gift'] as $orderGift ) {
                            if ( $orderGift['product_id'] == $prdID ){
                                $wdp_meta .= '<strong>1 * Free</strong>';
                            }
                        }
                    }
                }
                if ( $wdp_discount_detail['discountedPrice'] && !( $wdp_discount_detail['type'] === 'gift' || $wdp_discount_detail['type'] === 'bogo' ) ) { 
                    $wdp_discounted_price = $wdp_discount_detail['discountedPrice'];
                    $wdp_meta .= $wdp_discounted_price == 0 ? '<strong>Free Product</strong><br/>' : '<strong>Discounted Price: </strong>'. wc_price ( round ( wc_remove_number_precision ( $wdp_discounted_price ), wc_get_price_decimals() ) ) .'<br/>';
                }
                // $wdp_meta .= $wdp_discount_label ? '<strong>Discount Label: </strong>'.$wdp_discount_label : '';
            }
            $wdp_meta .= '</div>';
            echo $wdp_meta;
        }

    }

    public function wdpAdminOrderHeader($order) {

        $items              = $order->get_items();
        $discount_status    = false;
        foreach ( $items as $key => $val ) { if ( wc_get_order_item_meta ( $key, '_awdp_discount_details', true ) ) { $discount_status = true; continue; } }
        if ( $discount_status ) {
            echo '<th class="line_wdpdata sortable" data-sort="your-sort-option" style="display:none">Dynamic Pricing Total</th>';
        }

    }
    
    public function wdpAdminOrderContent($_product, $item, $itemid = null) {

        // Exit if NULL
        if ( !$_product || $_product == NULL ) return;

        $item_id  = $item ? $item->get_id() : ''; 
        $data     = $item ? $item->get_data() : []; 
        $subtotal = ( !empty($data) && array_key_exists ( 'subtotal', $data ) ) ? $data['subtotal'] : '';
        $discount = 0;
        $quantity = ( !empty($data) && array_key_exists ( 'quantity', $data ) ) ? $data['quantity'] : '';
        $wdp_discount_details   = wc_get_order_item_meta ( $item_id, '_awdp_discount_details', true ) ? wc_get_order_item_meta ( $item_id, '_awdp_discount_details', true ) : false;
        // $wdp_discount_label     = wc_get_order_item_meta($item_id, '_awdp_coupon', true);
        
        if ( $wdp_discount_details ) {

            $prdID      = $_product->get_ID(); 
            foreach ( $wdp_discount_details as $wdp_discount_detail ) { 
                if ( $wdp_discount_detail['type'] === 'bogo' ) {
                    if ( $wdp_discount_detail['bogo'] ) {
                        foreach ( $wdp_discount_detail['bogo'] as $orderBogo ) {
                            if ( $orderBogo['product_id'] == $prdID ){
                                $discount .= $orderBogo['discount'];
                                $quantity = $quantity - $orderBogo['offer_items'];
                            }
                        }
                    }
                }
                if ( $wdp_discount_detail['type'] === 'gift' ) {
                    if ( $wdp_discount_detail['gift'] ) {
                        foreach ( $wdp_discount_detail['gift'] as $orderGift ) {
                            if ( $orderGift['product_id'] == $prdID ){
                                $discount += $orderGift['discount'];
                                $quantity = $quantity - 1; 
                            }
                        } 
                    }
                }  
                if ( $wdp_discount_detail['discountedPrice'] && !( $wdp_discount_detail['type'] === 'gift' || $wdp_discount_detail['type'] === 'bogo' ) ) { 
                    $wdp_discounts = $wdp_discount_detail['discount']['discounts'];
                    foreach ( $wdp_discounts as $key => $value ) {
                        if ( $value['productid'] == $prdID ) { 
                            $discount += round ( wc_remove_number_precision ( $value['discount'] ), wc_get_price_decimals() ) * $quantity;
                        }
                    }
                    $wdp_discounted_price = $wdp_discount_detail['discountedPrice']; 
                }
            }
            $discountedPrice = $subtotal - $discount;

            $wdp_meta   = '<td class="line_wdpdata" style="display:none"><div class="view">';
            $wdp_meta   .= '<span class="woocommerce-Price-amount amount">'.wc_price($discountedPrice).'</span>';
            $wdp_meta   .= $discount ? '<span class="wc-order-item-discount">'.wc_price($discount).' discount</span>' : '';
            $wdp_meta   .= '</div>';
            // $wdp_meta   .= '<div class="edit" style="display: none;"><input type="text" name="line_total['.absint( $item_id ).']" placeholder="'.esc_attr( wc_format_localized_price( 0 ) ).'" value="'.esc_attr( wc_format_localized_price( $discountedPrice ) ).'" class="line_total wc_input_price" /></div>';
            // $wdp_meta   .= '<div class="refund" style="display: none;"><input type="text" name="refund_line_total['.absint( $item_id ).']" placeholder="'.esc_attr( wc_format_localized_price( 0 ) ).'" class="refund_line_total wc_input_price" /></div>';
            $wdp_meta   .= '</td>';

            echo $wdp_meta;

        } else {

            $wdp_meta   = '<td class="line_wdpdata" style="display:none"><div class="view">';
            $wdp_meta   .= '<span class="woocommerce-Price-amount amount">'.wc_price($subtotal).'</span>';
            $wdp_meta   .= '</div>';
            // $wdp_meta   .= '<div class="edit" style="display: none;"><input type="text" name="line_total['.absint( $item_id ).']" placeholder="'.esc_attr( wc_format_localized_price( 0 ) ).'" value="'.esc_attr( wc_format_localized_price( $discountedPrice ) ).'" class="line_total wc_input_price" /></div>';
            // $wdp_meta   .= '<div class="refund" style="display: none;"><input type="text" name="refund_line_total['.absint( $item_id ).']" placeholder="'.esc_attr( wc_format_localized_price( 0 ) ).'" class="refund_line_total wc_input_price" /></div>';
            $wdp_meta   .= '</td>';

            echo $wdp_meta;

        }

    }

    public function wdpCustomJS() {

        $currentScreen = get_current_screen();
        $screenID = $currentScreen->id; //
        if ( $screenID === 'shop_order' ) { ?>
            <script>
                jQuery(document).ready(function() { 
                    jQuery('.line_wdpdata').each (function(index){
                        $wdpdata = jQuery(this).find('.view').html(); 
                        jQuery(this).parent().find('.line_cost .view').html($wdpdata);
                    });
                });
            </script>
        <?php }
        
    }

    public function wdpInvoice( $line, $order_id ) {

        global $woocommerce;
        $order   = new WC_Order( $order_id );
    
        // Check WC version – changes for WC 3.0.0
        $pre_wc_30      = version_compare( WC_VERSION, '3.0', '<' );
        $order_currency = $pre_wc_30 ? $order->get_order_currency() : $order->get_currency();
                    
        $pdflines  = '<table width="100%">';
        $pdflines .= '<tbody>';
    
        if ( sizeof( $order->get_items() ) > 0 ) {
    
            foreach ( $order->get_items() as $item ) {                  
                
                if ( $item['qty'] ) {
                    
                    $line = '';
                    // $item_loop++;
    
                    $_product   = $order->get_product_from_item( $item );
                    $item_name  = $item['name'];
                    $item_id    = $pre_wc_30 ? $item['variation_id'] : $item->get_id();
    
                    $meta_display = '';
                    if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
                        $item_meta  = new WC_Order_Item_Meta( $item );
                        $meta_display = $item_meta->display( true, true );
                        $meta_display = $meta_display ? ( ' ( ' . $meta_display . ' )' ) : '';
                    } else {
                        foreach ( $item->get_formatted_meta_data() as $meta_key => $meta ) {
                            $meta_display .= '<br /><small>(' . $meta->display_key . ':' . wp_kses_post( strip_tags( $meta->display_value ) ) . ')</small>';
                        }
                    }
    
                    if ( $meta_display ) {
    
                        $meta_output     = apply_filters( 'pdf_invoice_meta_output', $meta_display );
                        $item_name      .= $meta_output;
    
                    }
                    
                    $line =     '<tr>' .
                                '<td valign="top" width="5%" align="right">' . $item['qty'] . ' x</td>' .
                                '<td valign="top" width="10%">' .  1233444 . '</td>' .
                                '<td valign="top" width="40%">' .  stripslashes( $item_name ) . '</td>' .
                                '<td valign="top" width="9%" align="right">'  .  wc_price( $item['line_subtotal'] / $item['qty'], array( 'currency' => $order_currency ) ) . '</td>' .                          
                                '<td valign="top" width="9%" align="right">'  .  wc_price( $item['line_subtotal'], array( 'currency' => $order_currency ) ) . '</td>' . 
                                '<td valign="top" width="7%" align="right">'  .  wc_price( $item['line_subtotal_tax'] / $item['qty'], array( 'currency' => $order_currency ) ). '</td>' .           
                                '<td valign="top" width="10%" align="right">123</td>' .
                                '<td valign="top" width="10%" align="right">345</td>' .
                                '</tr>';
                    
                    $pdflines .= $line;
                }
    
            }
    
        }
    
        $pdflines .=    '</tbody>';
        $pdflines .=    '</table>';
    
        return $pdflines;

    }

    public function wdpInvoiceDiscount( $content ) {

        global $woocommerce;
        $woocommerce_pdf_invoice_options = get_option( 'woocommerce_pdf_invoice_settings' );

        $content = str_replace( '[[CUSTOMFIELD]]', $woocommerce_pdf_invoice_options['custom_text'], $content );

        return $content;

    }

    public function wdpMiniCart() {

        global $woocommerce;  //apply_filters('woocommerce_applied_coupon');
        $coupon         = get_option('awdp_fee_label') ? get_option('awdp_fee_label') : 'Discount';
        $coupon_code    = apply_filters('woocommerce_coupon_code', $coupon); 
        $result         = '';
        if ( in_array($coupon_code, $woocommerce->cart->get_applied_coupons()) ) {
            $coupons_obj    = new WC_Coupon($coupon_code); 
            $coupons_amount = $coupons_obj->get_amount(); 
            $shipping       = $woocommerce->cart->get_cart_shipping_total();
            $taxes          = $woocommerce->cart->get_tax_totals();
            // $cart_total     = $woocommerce->cart->cart_contents_total;
            $total          = $woocommerce->cart->get_totals();
            if ( $coupons_amount ) { 
                $result = '<p class="wdp_miniCart total">';
                $result .= '<span class="wdpLabel">'.$coupon.': </span><span class="woocommerce-Price-amount amount">'.wc_price($coupons_amount).'</span></br>';
                $result .= ( $shipping && ( strpos ( $shipping, 'Free' ) === false) ) ? '<span class="wdpLabel">'.__("Shipping", "aco-woo-dynamic-pricing").': </span><span class="woocommerce-Price-amount amount">'.$shipping.'</span></br>' : '';
                if ($taxes) {
                    foreach ($taxes as $key => $val) { 
                        $result .= '<span class="wdpLabel">'.$val->label.': </span><span class="woocommerce-Price-amount amount">'.wc_price($val->amount).'</span></br>';
                    }
                }
                $result .= ( $total && array_key_exists ( 'total', $total ) ) ? '<span class="wdpLabel">'.__("Total", "aco-woo-dynamic-pricing").': </span><span class="woocommerce-Price-amount amount">'.wc_price($total['total']).'</span>' : '';
                $result .= '</p>';
            }
            echo $result;
        }        

    }

    public function wdpDynamicPricingTable() {
    
        $nonce              = $_POST['nonce']; 
        $type               = $_POST['type']; 
        $ProdID             = $_POST['ProdID'];
        $ProdPrice          = $_POST['ProdPrice'];
        $ProdQty            = $_POST['ProdQty'];
        $DisData            = $_POST['DisData'] ? json_decode ( stripslashes ( str_replace ('\'', '"', $_POST['DisData']) ) ) : '';
        $price              = '';
        $discountprice      = 0;
        $converted_rate     = 1; 
        $result             = [];
        $table              = '';

        if ( $type === 'change' ) { 

            if ( $DisData ) {

                $value_display              = get_option('awdp_table_value') ? get_option('awdp_table_value') : '';
                $value_display_text_hide    = get_option('awdp_table_value_notext') ? get_option('awdp_table_value_notext') : 0; 
                $table_layout               = get_post_meta($_POST['Rule'], 'discount_table_layout', true);
                $var_price                  = $_POST['price'];
                $discounted_new_price_bt    = '';

                $langSettings               = get_option('awdp_settings_lang_options') ? get_option('awdp_settings_lang_options') : [];
                if ( !empty ($langSettings) && array_key_exists ( $currentLang, $langSettings ) ) {
                    $value_display_text         = array_key_exists ( 'tablevaluetext', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['tablevaluetext'] : get_option('awdp_table_value_text');
                } else  {
                    $value_display_text         = get_option('awdp_table_value_text') ? get_option('awdp_table_value_text') : '';
                }

                // Pricing table texts
                $prcn_text      = ( !$value_display_text_hide ) ? ( ( ( $value_display == 'discount_value' || $value_display == 'discount_both' ) && $value_display_text ) ? ' '.$value_display_text : __('% OFF', 'aco-woo-dynamic-pricing') ) : '';
                $fxd_text       = ( !$value_display_text_hide ) ? ( ( ( $value_display == 'discount_value' || $value_display == 'discount_both' ) && $value_display_text ) ? ' '.$value_display_text : __(' OFF on cart value', 'aco-woo-dynamic-pricing') ) : '';
                $fxd_text_two   = ( !$value_display_text_hide ) ? ( ( ( $value_display == 'discount_value' || $value_display == 'discount_both' ) && $value_display_text ) ? ' '.$value_display_text : __(' OFF', 'aco-woo-dynamic-pricing') ) : '';
                $cart_text      = ( !$value_display_text_hide ) ? ( ( ( $value_display == 'discount_value' || $value_display == 'discount_both' ) && $value_display_text ) ? ' '.$value_display_text : __(' will be deducted from cart', 'aco-woo-dynamic-pricing') ) : '';

                if ($table_layout == 'horizontal') {
                    $tr_qn = '<tr><td>' . $awdp_qn_label . '</td>';
                    $tr_pr = '<tr><td>' . $awdp_pc_label . '</td>';
                    if ( $value_display == 'discount_both' ) {
                        $tr_nw = '<tr><td>' . $awdp_nw_label . '</td>';
                    }
                }

                foreach ( $DisData as $quantity_rule ) { 

                    $dis_value  = $quantity_rule->dis_value;
                    $dis_type   = $quantity_rule->dis_type;

                    if ($dis_type == 'percentage') {
                        $discounted_new_price_bt = (float)$dis_value . $prcn_text;
                    } else if ($dis_type == 'fixed') {
                        $discounted_new_price_bt = wc_price((float)$dis_value) . $fxd_text_two;
                    }

                    if ($dis_type == 'percentage') {
                        $discount_pt = $var_price * ((float)$dis_value / 100);
                        $discount_pt = min($var_price, $discount_pt);
                    } else if ($dis_type == 'fixed') {
                        $discount_pt = $dis_value * $converted_rate;
                    }

                    $discounted_new_price = (($var_price - $discount_pt) > 0) ? wc_price ( ( $var_price - $discount_pt ) * $converted_rate ) : 0;

                    if ( $table_layout == 'horizontal' ) {
                        if ($quantity_rule->start_range == $quantity_rule->end_range) {
                            $tr_qn .= '<td>' . $quantity_rule->start_range . '</td>';
                        } else if ($quantity_rule->end_range) {
                            $tr_qn .= '<td>' . $quantity_rule->start_range . ' - ' . $quantity_rule->end_range . '</td>';
                        } else {
                            $tr_qn .= '<td>' . $quantity_rule->start_range . ' +</td>';
                        }
                        $tr_pr .= '<td>' . $discounted_new_price . '</td>';
                        if ( $value_display == 'discount_both' ) {
                            $tr_nw .= '<td>' . $discounted_new_price_bt . '</td>';
                        }
                    } else {
                        if ($quantity_rule->start_range == $quantity_rule->end_range) {
                            if ( $value_display == 'discount_value' ) {
                                $table .= '<tr><td>' . $quantity_rule->start_range . '</td><td>' . $discounted_new_price_bt . '</td></tr>';
                            } else if ( $value_display == 'discount_both' ) {
                                $table .= '<tr><td>' . $quantity_rule->start_range . '</td><td>' . $discounted_new_price_bt . '</td><td>' . $discounted_new_price . '</td></tr>';
                            } else {
                                $table .= '<tr><td>' . $quantity_rule->start_range . '</td><td>' . $discounted_new_price . '</td></tr>';
                            }
                        } else if ($quantity_rule->end_range) {
                            if ( $value_display == 'discount_value' ) {
                                $table .= '<tr><td>' . $quantity_rule->start_range . '</td><td>' . $discounted_new_price_bt . '</td></tr>';
                            } else if ( $value_display == 'discount_both' ) {
                                $table .= '<tr><td>' . $quantity_rule->start_range . ' - ' . $quantity_rule->end_range . '</td><td>' . $discounted_new_price_bt . '</td><td>' . $discounted_new_price . '</td></tr>';
                            } else {
                                $table .= '<tr><td>' . $quantity_rule->start_range . ' - ' . $quantity_rule->end_range . '</td><td>' . $discounted_new_price . '</td></tr>';
                            }
                        } else {
                            if ( $value_display == 'discount_value' ) {
                                $table .= '<tr><td>' . $quantity_rule->start_range . '</td><td>' . $discounted_new_price_bt . '</td></tr>';
                            } else if ( $value_display == 'discount_both' ) {
                                $table .= '<tr><td>' . $quantity_rule->start_range . ' +</td><td>' . $discounted_new_price_bt . '</td><td>' . $discounted_new_price . '</td></tr>';
                            } else {
                                $table .= '<tr><td>' . $quantity_rule->start_range . ' +</td><td>' . $discounted_new_price . '</td></tr>';
                            }
                        }
                    }

                }

                if ($table_layout == 'horizontal') {
                    $tr_qn .= '</tr>';
                    $tr_pr .= '</tr>';
                    if ( $value_display == 'discount_both' ) { 
                        $tr_nw .= '</tr>';
                        $table .= $tr_qn . $tr_pr . $tr_nw;
                    } else {
                        $table .= $tr_qn . $tr_pr;
                    }
                }

                echo $table;

            }

        } else {
        
            $variation_prices   = $_POST['ProdVarPrice'] ? json_decode($_POST['ProdVarPrice']) : [];

            if ( $DisData ) {

                // if ( !empty ( $variation_prices ) ) {

                //     $price_to_discount_max = $variation_prices ? max($variation_prices) : 0;
                //     $price_to_discount_min = $variation_prices ? min($variation_prices) : 0;

                //     // Default value when quantity not in range
                //     $result['price']            = $price_to_discount_min ? $price_to_discount_min : 0;
                //     $result['total']            = $price_to_discount_min ? round ( ( $price_to_discount_min * $ProdQty ), wc_get_price_decimals() ) : 0;
                //     $result['currency']         = get_woocommerce_currency_symbol();

                //     foreach ( $DisData as $discount ) { 

                //         if ( ( $discount->end_range != '' && $ProdQty >= $discount->start_range && $ProdQty <= $discount->end_range ) || ( $discount->end_range == '' && $ProdQty >= $discount->start_range ) || ( $discount->end_range != '' && $ProdQty > $discount->end_range && ( $discount->start_range != $discount->end_range ) ) ) {

                //             if ( $discount->dis_type == 'percentage' ) {
                //                 $discount_max_value     = $price_to_discount_max * ((float)$discount->dis_value / 100);
                //                 $discount_min_value     = $price_to_discount_min * ((float)$discount->dis_value / 100);
                //                 // $discount_max_value = min($price_to_discount, $discount_pt);
                //             } else if ( $discount->dis_type == 'fixed' ) {
                //                 $discount_max_value     = wc_add_number_precision($discount->dis_value);
                //                 $discount_min_value     = wc_add_number_precision($discount->dis_value);
                //             }

                //             $result['price']            = $discounted_new_min_price ? round ( $discounted_new_min_price, wc_get_price_decimals() ) : 0;
                //             $result['total']            = $discounted_new_min_price ? round ( ( $discounted_new_min_price * $ProdQty ), wc_get_price_decimals() ) : 0;
                //             $result['currency']         = get_woocommerce_currency_symbol();
                            
                //             $discounted_new_max_price   = (($price_to_discount_max - $discount_max_value) > 0) ? wc_price ( wc_remove_number_precision ( $price_to_discount_max - $discount_max_value ) ) : 0;
                //             $discounted_new_min_price   = (($price_to_discount_min - $discount_min_value) > 0) ? wc_price ( wc_remove_number_precision ( $price_to_discount_min - $discount_min_value ) ) : 0;
                            
                //             // $result['price']            = wc_format_sale_price ( wc_price ( wc_remove_number_precision ( $price_to_discount_min ) ) . ' - ' . wc_price ( wc_remove_number_precision ( $price_to_discount_max ) ), $discounted_new_min_price . ' - ' . $discounted_new_max_price );

                //             continue;

                //         }

                //     }

                // } else {

                    // Default value when quantity not in range
                    $result['price']    = $ProdPrice ? (int)$ProdPrice : 0;
                    $result['total']    = $ProdPrice ? round ( ( $ProdPrice * $ProdQty ), wc_get_price_decimals() ) : 0;
                    $result['currency'] = get_woocommerce_currency_symbol();

                    foreach ( $DisData as $discount ) { 

                        if ( ( $discount->end_range != '' && $ProdQty >= $discount->start_range && $ProdQty <= $discount->end_range ) || ( $discount->end_range == '' && $ProdQty >= $discount->start_range ) || ( $discount->end_range != '' && $ProdQty > $discount->end_range && ( $discount->start_range != $discount->end_range ) ) ) {

                            $discountprice      = ( $discount->dis_type === 'fixed' ) ? ( $ProdPrice - $discount->dis_value ) : ( $ProdPrice - ( $ProdPrice * ( (float)$discount->dis_value / 100 ) ) );

                            $result['price']    = $discountprice ? round ( $discountprice, wc_get_price_decimals() ) : 0;
                            $result['total']    = $discountprice ? round ( ( $discountprice * $ProdQty ), wc_get_price_decimals() ) : 0;
                            $result['currency'] = get_woocommerce_currency_symbol();
                            continue;

                        }

                    }

                // }

            }

            // $price = wc_format_sale_price($ProdPrice * $converted_rate, $discountprice * $converted_rate);
            echo !empty ( $result ) ? json_encode($result) : '';

        }
        die();

    }

    // Woocommerce functions
    function wdp_price_including_tax ( $product, $prodPrice, $args = array() ) {

        $args = wp_parse_args(
            $args,
            array(
                'qty'   => '',
                'price' => '',
            )
        );
    
        $price = '' !== $args['price'] ? max( 0.0, (float) $args['price'] ) : $prodPrice;
        $qty   = '' !== $args['qty'] ? max( 0.0, (float) $args['qty'] ) : 1;
    
        if ( '' === $price ) {
            return '';
        } elseif ( empty( $qty ) ) {
            return 0.0;
        }

        $line_price   = $price * $qty;
        $return_price = $line_price;
    
        if ( $product->is_taxable() ) {
            if ( ! wc_prices_include_tax() ) {
                $tax_rates = WC_Tax::get_rates( $product->get_tax_class() );
                $taxes     = WC_Tax::calc_tax( $line_price, $tax_rates, false );
    
                if ( 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
                    $taxes_total = array_sum( $taxes );
                } else {
                    $taxes_total = array_sum( array_map( 'wc_round_tax_total', $taxes ) );
                }
    
                $return_price = round( $line_price + $taxes_total, wc_get_price_decimals() );
            } else {
                $tax_rates      = WC_Tax::get_rates( $product->get_tax_class() );
                $base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );
    
                /**
                 * If the customer is excempt from VAT, remove the taxes here.
                 * Either remove the base or the user taxes depending on woocommerce_adjust_non_base_location_prices setting.
                 */
                if ( ! empty( WC()->customer ) && WC()->customer->get_is_vat_exempt() ) { // @codingStandardsIgnoreLine.
                    $remove_taxes = apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ? WC_Tax::calc_tax( $line_price, $base_tax_rates, true ) : WC_Tax::calc_tax( $line_price, $tax_rates, true );
    
                    if ( 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
                        $remove_taxes_total = array_sum( $remove_taxes );
                    } else {
                        $remove_taxes_total = array_sum( array_map( 'wc_round_tax_total', $remove_taxes ) );
                    }
    
                    $return_price = round( $line_price - $remove_taxes_total, wc_get_price_decimals() );
    
                    /**
                 * The woocommerce_adjust_non_base_location_prices filter can stop base taxes being taken off when dealing with out of base locations.
                 * e.g. If a product costs 10 including tax, all users will pay 10 regardless of location and taxes.
                 * This feature is experimental @since 2.4.7 and may change in the future. Use at your risk.
                 */
                } elseif ( $tax_rates !== $base_tax_rates && apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ) {
                    $base_taxes   = WC_Tax::calc_tax( $line_price, $base_tax_rates, true );
                    $modded_taxes = WC_Tax::calc_tax( $line_price - array_sum( $base_taxes ), $tax_rates, false );
    
                    if ( 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
                        $base_taxes_total   = array_sum( $base_taxes );
                        $modded_taxes_total = array_sum( $modded_taxes );
                    } else {
                        $base_taxes_total   = array_sum( array_map( 'wc_round_tax_total', $base_taxes ) );
                        $modded_taxes_total = array_sum( array_map( 'wc_round_tax_total', $modded_taxes ) );
                    }
    
                    $return_price = round( $line_price - $base_taxes_total + $modded_taxes_total, wc_get_price_decimals() );
                }
            }
        }
        return apply_filters( 'woocommerce_get_price_including_tax', $return_price, $qty, $product );
    }
    
    function wdp_price_excluding_tax ( $product, $prodPrice, $args = array() ) {
        
        $args = wp_parse_args(
            $args,
            array(
                'qty'   => '',
                'price' => '',
                'skipcheck' => ''
            )
        );
    
        $price  = '' !== $args['price'] ? max( 0.0, (float) $args['price'] ) : $prodPrice;
        $qty  = '' !== $args['qty'] ? max( 0.0, (float) $args['qty'] ) : 1;
        $skipcheck  = '' !== $args['skipcheck'] ? true : false;
    
        if ( '' === $price ) {
            return '';
        } elseif ( empty( $qty ) ) {
            return 0.0;
        }
    
        $line_price = $price * $qty;
    
        if ( ( $product->is_taxable() && wc_prices_include_tax() ) || $skipcheck ) {
            $tax_rates      = WC_Tax::get_rates( $product->get_tax_class() );
            $base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );
            $remove_taxes   = apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ? WC_Tax::calc_tax( $line_price, $base_tax_rates, true ) : WC_Tax::calc_tax( $line_price, $tax_rates, true );
            $return_price   = $line_price - array_sum( $remove_taxes ); // Unrounded since we're dealing with tax inclusive prices. Matches logic in cart-totals class. @see adjust_non_base_location_price.
        } else {
            $return_price = $line_price;
        }
    
        return apply_filters( 'woocommerce_get_price_excluding_tax', $return_price, $qty, $product );
    }

    // Cloning is forbidden.
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

    // Unserializing instances of this class is forbidden.
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

}