<?php

if (!defined('ABSPATH'))
    exit;

class AWDP_Front_End
{

    static $cart_error = array();
    /**
     * The single instance of WordPress_Plugin_Template_Settings.
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $_instance = null;
    public $products = false;
    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version;
    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_token;
    /**
     * The plugin assets URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;
    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    private $discount;
    private $conversion_unit = false;
    /**
     * Check if price has to be display in cart and checkout
     * @var type
     * @var boolean
     * @access private
     * @since 3.4.2
     */
    private $show_price = false;

    function __construct($discount, $file = '', $version = '1.0.0')
    {

        $this->_version     = $version;
        $this->_token       = AWDP_TOKEN;
        $this->discount     = $discount;
        add_action('init', array($this, 'register_awdp_discounts'));

        // Deactivation hook
        add_action( 'deactivate_' . plugin_basename(dirname(AWDP_FILE)), array( $this, 'awdp_plugin_deactivate') );
        
        if ( $this->awdp_check_woocommerce_active() ) {

            // Advanced Settings
            // $addition_settings  = get_option('awdp_addition_settings') ? get_option('awdp_addition_settings') : [];
            // $discount_method    = array_key_exists ( 'discount_method', $addition_settings ) ? $addition_settings['discount_method'] : 'coupon';

            add_action ( 'woocommerce_before_calculate_totals', array($this, 'wdpCalculateDiscount'), 1000, 1 );

            // Change Discount Price HTML View
            add_filter( 'woocommerce_get_price_html', array($this, 'get_product_price_html'), 100, 2 );
            
            // Cart Item Price
            add_filter( 'woocommerce_cart_item_price', array($this, 'cart_price_view'), 1000, 3 );
            add_filter( 'woocommerce_cart_item_price_html', array($this, 'cart_price_view'), 1000, 3 );

            // Enqueue Scripts
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

            // Current Shipment / Payment Session
            add_action( 'wp_ajax_awdpschange', array( $this, 'wdpSetSession' ), 10 );
            add_action( 'wp_ajax_nopriv_awdpschange', array( $this, 'wdpSetSession' ), 10 );

            // Checking Discount Method 
            // if ( $discount_method !== 'cart_item' ) {
                // Cart
                add_action( 'woocommerce_cart_item_subtotal', array( $this, 'wdpCartLoop' ), 8, 3 );
                
                // Coupon Data
                add_filter( 'woocommerce_get_shop_coupon_data', array( $this, 'addVirtualCoupon'), 99, 2 );
                add_action( 'woocommerce_after_calculate_totals', array( $this, 'applyFakeCoupons') );
                add_filter( 'woocommerce_cart_totals_coupon_label', array( $this, 'couponLabel'), 99, 2 );

                // Coupon Message
                add_filter( 'woocommerce_coupon_message', array($this, 'coupon_message'), 10, 3 );
                add_filter( 'woocommerce_coupon_error', array($this, 'coupon_message'), 10, 3 );
            // }

            // Pricing table
            if( false === get_option('awdp_table_position') ){
                $tablePosition = get_option('tableposition');
            } else {
                $tablePosition = get_option('awdp_table_position');
            }
            if ( 'before_product' == $tablePosition ) {
                add_filter( 'woocommerce_before_single_product', array($this, 'show_pricing_table'), 99, 3 );
            } else if ( 'before_product_summary' == $tablePosition ) {
                add_filter( 'woocommerce_before_single_product_summary', array($this, 'show_pricing_table'), 99, 3 );
            } else if ( 'in_product_summary' == $tablePosition ) {
                add_filter( 'woocommerce_single_product_summary', array($this, 'show_pricing_table'), 99, 3 );
            } else if ( 'before_form' == $tablePosition ) {
                add_filter( 'woocommerce_before_add_to_cart_form', array($this, 'show_pricing_table'), 99, 3 );
            } else if ( 'before_variations_form' == $tablePosition ) {
                add_filter( 'woocommerce_before_variations_form', array($this, 'show_pricing_table'), 99, 3 );
            } else if ( 'before_button' == $tablePosition ) {
                add_filter( 'woocommerce_before_add_to_cart_button', array($this, 'show_pricing_table'), 99, 3 );
            } else if ( 'after_button' == $tablePosition ) {
                add_filter( 'woocommerce_after_add_to_cart_button', array($this, 'show_pricing_table'), 99, 3 );
            } else if ( 'after_variations_form' == $tablePosition ) {
                add_filter( 'woocommerce_after_variations_form', array($this, 'show_pricing_table'), 99, 3 );
            } else if ( 'after_form' == $tablePosition ) {
                add_filter( 'woocommerce_after_add_to_cart_form', array($this, 'show_pricing_table'), 99, 3 );
            } else if ( 'meta_start' == $tablePosition ) {
                add_filter( 'woocommerce_product_meta_start', array($this, 'show_pricing_table'), 99, 3 );
            } else if ( 'meta_end' == $tablePosition ) {
                add_filter( 'woocommerce_product_meta_end', array($this, 'show_pricing_table'), 99, 3 );
            } else if ( 'after_product_summary' == $tablePosition ) {
                add_filter( 'woocommerce_after_single_product_summary', array($this, 'show_pricing_table'), 99, 3 );
            } else if ( 'after_product' == $tablePosition ) {
                add_filter( 'woocommerce_after_single_product', array($this, 'show_pricing_table'), 99, 3 );
            } else {
                add_filter( 'woocommerce_before_add_to_cart_button', array($this, 'show_pricing_table'), 99, 3 );
            } 

            // Offer Description
            $offer_desc_config  = get_option('awdp_offer_desc_config') ? get_option('awdp_offer_desc_config') : [];
            $offerMsgPos        = array_key_exists ( 'offer_position', $offer_desc_config ) ? $offer_desc_config['offer_position'] : '';
            if ( 'before_product' == $offerMsgPos ) {
                add_filter( 'woocommerce_before_single_product', array($this, 'show_offer_message'), 99, 3 );
            } else if ( 'before_product_summary' == $offerMsgPos ) {
                add_filter( 'woocommerce_before_single_product_summary', array($this, 'show_offer_message'), 99, 3 );
            } else if ( 'in_product_summary' == $offerMsgPos ) {
                add_filter( 'woocommerce_single_product_summary', array($this, 'show_offer_message'), 99, 3 );
            } else if ( 'before_form' == $offerMsgPos ) {
                add_filter( 'woocommerce_before_add_to_cart_form', array($this, 'show_offer_message'), 99, 3 );
            } else if ( 'before_button' == $offerMsgPos ) {
                add_filter( 'woocommerce_before_add_to_cart_button', array($this, 'show_offer_message'), 99, 3 );
            } else if ( 'after_button' == $offerMsgPos ) {
                add_filter( 'woocommerce_after_add_to_cart_button', array($this, 'show_offer_message'), 99, 3 );
            } else if ( 'after_form' == $offerMsgPos ) {
                add_filter( 'woocommerce_after_add_to_cart_form', array($this, 'show_offer_message'), 99, 3 );
            } else if ( 'meta_start' == $offerMsgPos ) {
                add_filter( 'woocommerce_product_meta_start', array($this, 'show_offer_message'), 99, 3 );
            } else if ( 'meta_end' == $offerMsgPos ) {
                add_filter( 'woocommerce_product_meta_end', array($this, 'show_offer_message'), 99, 3 );
            } else if ( 'after_product_summary' == $offerMsgPos ) {
                add_filter( 'woocommerce_after_single_product_summary', array($this, 'show_offer_message'), 99, 3 );
            } else if ( 'after_product' == $offerMsgPos ) {
                add_filter( 'woocommerce_after_single_product', array($this, 'show_offer_message'), 99, 3 );
            } else {
                add_filter( 'woocommerce_before_add_to_cart_button', array($this, 'show_offer_message'), 99, 3 );
            } 

            // Gift / Bogo Suggestions
            if ( 1 == get_option('awdp_single_free_product') ) {
                $giftPosOptions = get_option('awdp_gifts_position') ? get_option('awdp_gifts_position') : '';
                if ( 'in_product_summary' == $giftPosOptions ) {
                    add_filter( 'woocommerce_single_product_summary', array($this, 'show_offer_products'), 99, 3 );
                } else if ( 'before_form' == $giftPosOptions ) {
                    add_filter( 'woocommerce_before_add_to_cart_form', array($this, 'show_offer_products'), 99, 3 );
                } else if ( 'before_button' == $giftPosOptions ) {
                    add_filter( 'woocommerce_before_add_to_cart_button', array($this, 'show_offer_products'), 99, 3 );
                } else if ( 'after_button' == $giftPosOptions ) {
                    add_filter( 'woocommerce_after_add_to_cart_button', array($this, 'show_offer_products'), 99, 3 );
                } else if ( 'after_form' == $giftPosOptions ) {
                    add_filter( 'woocommerce_after_add_to_cart_form', array($this, 'show_offer_products'), 99, 3 );
                } else if ( 'meta_start' == $giftPosOptions ) {
                    add_filter( 'woocommerce_product_meta_start', array($this, 'show_offer_products'), 99, 3 );
                } else if ( 'meta_end' == $giftPosOptions ) {
                    add_filter( 'woocommerce_product_meta_end', array($this, 'show_offer_products'), 99, 3 );
                } else if ( 'after_product_summary' == $giftPosOptions ) {
                    add_filter( 'woocommerce_after_single_product_summary', array($this, 'show_offer_products'));
                } else if ( 'after_product_summary' == $giftPosOptions ) {
                    add_filter( 'woocommerce_after_single_product', array($this, 'show_offer_products'));
                } else {
                    add_filter( 'woocommerce_after_single_product_summary', array($this, 'show_offer_products'), 99, 3 );
                } 
            }

            // Apply Cart Rules
            // add_action( 'woocommerce_before_calculate_totals', array( $this, 'apply_cart_discounts' ), 99, 2 );

            // Discount Usage Limit 
            // add_filter( 'woocommerce_order_status_completed', array($this, 'couponComplete'), 10, 3 );
            add_action( 'woocommerce_checkout_update_order_meta', array($this, 'wdpSaveOrderMeta'), 10, 3 );

            // Disable QN Change Free Gifts
            // add_filter( 'woocommerce_cart_item_quantity', array($this, 'wdpDisableQNChange'), 10, 3 );

            //Cart Meta
            // add_filter( 'woocommerce_add_cart_item_data', array($this, 'wdpAddCartItemData'), 10, 3 );
            // add_filter( 'woocommerce_get_item_data', array($this, 'wdpGetCartItemData'), 10, 2 );
            // add_filter( 'woocommerce_get_item_data', array($this, 'wdpGetCartItemData'), 10, 2 );

            //Shortcode
            add_shortcode( 'wdpdiscounts', array($this, 'wdpShortcode') );

            // Version 4.0.4
            add_action( 'woocommerce_after_shop_loop_item', array( $this, 'wdpSaleBadge' ), 9999, 3 );

            // CountDown Timer
            $timer_settings = get_option('awdp_timer_settings') ? get_option('awdp_timer_settings') : [];
            $timerposition = array_key_exists ( 'timer_position', $timer_settings ) ? $timer_settings['timer_position'] : '';
               
            if ( $timerposition == 'in_product_summary' ) {
                add_filter( 'woocommerce_single_product_summary', array( $this, 'wdpCountdownTimer') );
            } else if ( $timerposition == 'before_form' ) {
                add_filter( 'woocommerce_before_add_to_cart_form', array( $this, 'wdpCountdownTimer') );
            } else if ( $timerposition == 'before_button' ) {
                add_filter( 'woocommerce_before_add_to_cart_button', array( $this, 'wdpCountdownTimer') );
            } else if ( $timerposition == 'after_button' ) {
                add_filter( 'woocommerce_after_add_to_cart_button', array( $this, 'wdpCountdownTimer') );
            } else if ( $timerposition == 'after_form' ) {
                add_filter( 'woocommerce_after_add_to_cart_form', array( $this, 'wdpCountdownTimer') );
            } else if ( $timerposition == 'meta_start' ) {
                add_filter( 'woocommerce_product_meta_start', array( $this, 'wdpCountdownTimer') );
            } else if ( $timerposition == 'meta_end' ) {
                add_filter( 'woocommerce_product_meta_end', array( $this, 'wdpCountdownTimer') );
            } else if ( $timerposition == 'after_product_summary' ) {
                add_filter( 'woocommerce_after_single_product_summary', array( $this, 'wdpCountdownTimer') );
            } else {
                add_filter( 'woocommerce_before_add_to_cart_button', array( $this, 'wdpCountdownTimer') );
            }

            $badge_settings = get_option('awdp_badge_settings') ? get_option('awdp_badge_settings') : []; 
            $badge_onsale   = array_key_exists( 'badge_onsale', $badge_settings ) ? $badge_settings['badge_onsale'] : '';           
            if ( $badge_onsale ) {
                add_filter( 'woocommerce_sale_flash', array($this, 'wdpHideSaleBadge'));
            }
            
            add_action( 'admin_notices', array($this, 'wdpAdminNotices'));
            
            // Adding Frontend Styles 
            add_action( 'wp_footer', array($this, 'awdp_styles'), 25 );

            // Fee
            $addition_settings  = get_option('awdp_addition_settings') ? get_option('awdp_addition_settings') : [];
            $fee_enable         = ( $addition_settings && array_key_exists ( 'fee_enable', $addition_settings ) ) ? $addition_settings['fee_enable'] : '';
            if ( $fee_enable ) {
                add_action( 'woocommerce_cart_calculate_fees', array($this, 'awdp_fees_custom'), 10, 1 );
            }

            // Subtotal Title
            $subtotal_label     = ( $addition_settings && array_key_exists ( 'subtotal_label', $addition_settings ) ) ? $addition_settings['subtotal_label'] : '';
            if ( $subtotal_label ) {
                add_filter( 'gettext', array($this, 'awdp_subtotal_label'), 10, 1 );
            }

            // WCPA Price
            add_filter( 'wcpa_product_price', array($this, 'wdpWCPAPrice'), 10, 2 );

            // Order Meta @@ Ver 5.0.4
            add_action( 'woocommerce_add_order_item_meta', array( $this, 'wdpOrderMeta'), 10, 3 );
            add_action( 'woocommerce_after_order_itemmeta', array( $this, 'wdpDisplayOrderMeta'), 10, 3 );
            // add_action( 'woocommerce_admin_order_totals_after_discount', array( $this, 'wdpOrderTotal'), 10, 1);

            // Admin Order Page Customization
            add_action( 'woocommerce_admin_order_item_headers', array( $this, 'wdpAdminOrderHeader'), 10, 1 );
            add_action( 'woocommerce_admin_order_item_values', array( $this, 'wdpAdminOrderContent'), 10, 3 );
            // add_action( 'admin_footer', array( $this, 'wdpCustomJS') );
            
            // Dynamic Pricing Table
            add_action( 'wp_ajax_wdpAjax', array( $this, 'wdpDynamicPricingTable') );
            add_action( 'wp_ajax_nopriv_wdpAjax', array( $this, 'wdpDynamicPricingTable') );
            
            // Mini Cart
            add_action( 'woocommerce_widget_shopping_cart_total', array( $this, 'wdpMiniCart'), 15 );

            // Invoice
            // add_filter( 'pdf_template_line_output' , array( $this, 'wdpInvoice'), 10, 2 );
            // add_filter( 'pdf_content_additional_content' , array( $this, 'wdpInvoiceDiscount'), 10, 1 );

            // Dynamic Pricing Table
            add_action( 'wp_ajax_wdpDiscountedPrice', array( $this, 'wdpDiscountedPrice') );
            add_action( 'wp_ajax_nopriv_wdpDiscountedPrice', array( $this, 'wdpDiscountedPrice') );

            add_action( 'woocommerce_after_cart_table', array( $this, 'wdpCartMessage') );

            add_filter ( 'wcpa_discount_rule', array( $this, 'wcpaDiscount' ), 10, 2);

        }
        
    }

    /**
     * Cart Message
    */

    public function wdpCartMessage()
    {

        echo $this->discount->wdpCartMessage();

    }

    /**
     * Mini Cart
    */

    public function wdpMiniCart()
    {

        echo $this->discount->wdpMiniCart();

    }

    /**
     * Dynamic Pricing Table
    */

    public function wdpDynamicPricingTable()
    {

        echo $this->discount->wdpDynamicPricingTable();

    }

    /**
     * Addons Products Price
    */

    public function wdpDiscountedPrice()
    {

        echo $this->discount->wdpDiscountedPrice();

    }

    public function wcpaDiscount($response, $product) 
    {

        return $this->discount->wcpaDiscount($response, $product);

    }

    /**
     * Order Total
    */

    // public function wdpOrderTotal( $order )
    // {

    //     echo $this->discount->wdpOrderTotal( $order );

    // }

    /**
     * Admin Order 
    */

    public function wdpAdminOrderHeader($order)
    {
        
        echo $this->discount->wdpAdminOrderHeader($order);

    }

    public function wdpAdminOrderContent($_product, $item, $item_id = null)
    {
        
        echo $this->discount->wdpAdminOrderContent($_product, $item, $item_id = null);

    }

    public function wdpCustomJS()
    {
        
        echo $this->discount->wdpCustomJS();

    }

    /**
     * Invoice 
    */
    public function wdpInvoice( $line, $order_id )
    {
        
        echo $this->discount->wdpInvoice( $line, $order_id );

    }
    public function wdpInvoiceDiscount( $content )
    {
        
        echo $this->discount->wdpInvoiceDiscount( $content );

    }

    /**
     * Order Meta Save 
    */

    public function wdpOrderMeta($item_id, $values, $cart_item_key)
    {
        
        echo $this->discount->wdpOrderMeta($item_id, $values, $cart_item_key);

    }

    /**
     * Order Meta Display
    */

    public function wdpDisplayOrderMeta( $item_id, $item, $product )
    {

        echo $this->discount->wdpDisplayOrderMeta( $item_id, $item, $product );

    }

    // Discount Calculation
    public function wdpCalculateDiscount ($cartOject) 
    {

        return $this->discount->wdpCalculateDiscount($cartOject);

    }

    // WCPA Price
    public function wdpWCPAPrice( $default, $product ){

        return $this->discount->wdpWCPAPrice( $default, $product );

    }

    // Fee
    public function awdp_fees_custom( $cart ) {

        return $this->discount->awdp_fees_custom( $cart );

    }

    // Subtotal Label
    public function awdp_subtotal_label( $translated_text ) {

        $addition_settings  = get_option('awdp_addition_settings') ? get_option('awdp_addition_settings') : [];
        $subtotal_label     = ( $addition_settings && array_key_exists ( 'subtotal_label', $addition_settings ) ) ? $addition_settings['subtotal_label'] : '';
        if ( $subtotal_label && ( 'Subtotal' == $translated_text || __( 'Subtotal', 'woocommerce' ) == $translated_text ) ) {
            $translated_text = $subtotal_label;
        }
        return $translated_text;

    }

    public function wdpHideSaleBadge()
    {

        return false;

    }

    /**
     * Load frontend CSS.
     * @access  public
     * @since   1.0.0
     * @return void
     */
    public function enqueue_styles()
    {

        wp_register_style('wdp-style', AWDP_FOLDER_PATH . 'assets/css/frontend.css', array(), $this->_version);
        wp_register_style('owl-carousel', AWDP_FOLDER_PATH . 'assets/css/owl.carousel.min.css', array(), $this->_version);
        
        wp_enqueue_style('wdp-style');
        wp_enqueue_style('owl-carousel');

    }

    /**
     * Load frontend Javascript.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function enqueue_scripts()
    {

        wp_register_script('awd-script', AWDP_FOLDER_PATH . 'assets/js/frontend.js', array('jquery'), $this->_version);
        // wp_localize_script('awd-script', 'awdajaxobject', array( 'url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('awdpnonce') ));

        /*
        * Price Group @ version 4.0.5
        */
        $addition_settings  = get_option('awdp_addition_settings') ? get_option('awdp_addition_settings') : [];
        // $addon_price        = array_key_exists ( 'disable_addon', $addition_settings ) ? $addition_settings['disable_addon'] : false;

        $new_config         = get_option('awdp_new_config') ? get_option('awdp_new_config') : []; 

        wp_localize_script('awd-script', 'awdajaxobject', 
            array( 
                'url'               => admin_url('admin-ajax.php'), 
                'nonce'             => wp_create_nonce('awdpnonce'),
                'priceGroup'        => $this->discount->wdpWCPAVariationPrice(),
                'dynamicPricing'    => array_key_exists ( 'dynamicpricing', $new_config ) ? $new_config['dynamicpricing'] : '',
                'variablePricing'   => array_key_exists ( 'variablepricing', $new_config ) ? $new_config['variablepricing'] : ''
            )
        );

        wp_register_script('owl-carousel', AWDP_FOLDER_PATH . 'assets/js/owl.carousel.min.js', array('jquery'), $this->_version);

        wp_enqueue_script('jquery');

        wp_enqueue_script('awd-script');
        wp_enqueue_script('owl-carousel');

    }

    /**
     * Set Current Payment / Shipment WC Session variables.
     * @access  public
     * @since   3.3.0
     * @return  void
     */
    public function wdpSetSession() 
    {

        $nonce = $_POST['nonce']; 
        $type = $_POST['awbcompname'];
        $value = $_POST['awbcompvalue'];
        // Verify nonce field passed from javascript code
        if ( ! wp_verify_nonce( $nonce, 'awdpnonce' ) )
            die ( 'Cheating!' );

        if ( $type == 'payment_method' ) {
            WC()->session->set( 'awdpwcpaymentmethod', $value );
        } else {
            WC()->session->set( 'awdpwcshippingmethod', $value );
        }

        die ( '1' );

    }

    /**
     * Adding Sale Badges
    */

    public function wdpSaleBadge()
    {

        if ( !is_admin() ) { 

            return $this->discount->wdpSaleBadge();

        }

    }

    /**
     * Adding CountDown Timer
    */

    public function wdpCountdownTimer()
    {
        
        echo $this->discount->wdpCountdownTimer();

    }
    
    /**
     * Adding Cart Item Meta (Gift / Bogo Rules)
     * @param $cart_item_data, $product_id, $variation_id
    */

    public function wdpCartLoop ( $wc, $cart_content, $cart_item_key ) {

        return $this->discount->wdpCartLoop ( $wc, $cart_content, $cart_item_key );

    }

    /**
     * Adding Cart Item Meta (Gift / Bogo Rules)
     * @param $cart_item_data, $product_id, $variation_id
    */

    public function wdpAddCartItemData ( $cart_item_data, $product_id, $variation_id ) {

        return $this->discount->wdpAddCartItemData( $cart_item_data, $product_id, $variation_id );

    }

    public function wdpGetCartItemData ( $item_data, $cart_item_data ) {

        return $this->discount->wdpGetCartItemData( $item_data, $cart_item_data );

    }

    /**
     * Disable Quantity Change (Gift)
     * @param $product_quantity, $cart_item_key, $cart_item
    */

    // public function wdpDisableQNChange ( $product_quantity, $cart_item_key, $cart_item ) {

    //     return $this->discount->wdpDisableQNChange($product_quantity, $cart_item_key, $cart_item);

    // }
    
    /**
     * Handling Virtual Coupon
     * @param $response, $curr_coupon_code
    */

    public function addVirtualCoupon($response, $curr_coupon_code)
    {

        return $this->discount->addVirtualCoupon($response, $curr_coupon_code);

    }

    public function applyFakeCoupons()
    {

        return $this->discount->applyFakeCoupons();

    }

    public function couponLabel($label, $coupon)
    {

        return $this->discount->couponLabel($label, $coupon);

    }

    public function couponRemoved($coupon)
    {

        return $this->discount->couponRemoved($coupon);

    }

    public function coupon_message($msg, $msg_code, $coupon=null)
    {
        $awdappliedCode = $coupon->get_code();
        $awdpluginLabel = get_option('awdp_fee_label') ? get_option('awdp_fee_label') : 'Discount';
        if ( $awdappliedCode == $awdpluginLabel || mb_strtolower($awdappliedCode, 'UTF-8') == mb_strtolower($awdpluginLabel, 'UTF-8')) {
            return '';
        }
        return $msg;
    }

    // public function couponComplete($order_id, $old_status, $new_status)
    // {

    //     return $this->discount->couponComplete($order_id, $old_status, $new_status);

    // }

    public function wdpSaveOrderMeta($order_id)
    {

        return $this->discount->wdpSaveOrderMeta($order_id);

    }

    /**
     * Listing Shortcode
     * @param $atts
     */

    function wdpShortcode ( $atts ) {

        return $this->discount->wdpShortcode($atts);

    }
    
    /**
     * Apply the discount on payment gateway
     * @param $cart_obj object
     */

    // public function wdp_payment_gateway($cart_obj){

    //     $this->discount->wdp_payment_gateway($cart_obj);
        
    // }

    /**
     * Apply cart discounts
     * @param $cart_obj object
     */

    public function apply_cart_discounts( $cart_object )
    {

        return $this->discount->apply_cart_discounts( $cart_object );

    }

    /**
     * Apply the discount as fee
     * @param $cart_obj object
     */

    public function cart_price_view( $item_price, $cart_item )
    {

        return $this->discount->cart_discount_items( $item_price, $cart_item );

    }

    // public function cart_apply_discount($cart_obj){
    //     // $this->discount->calculate_discount($cart_obj);
    //     $this->discount->show_discounts($cart_obj);
    // }


    // AWPD Prodcut Price After Discount
    public function get_product_price( $price, $product ) 
    {

        if ( is_admin() ) // disable price change on admin
            return $price;
        else if ( $price != '' )
            return $this->discount->get_product_price( $price, $product );
        else
            return $price;

    }

    // Price HTML Display
    public function get_product_price_html( $price, $product ) 
    {

        return $this->discount->get_product_price_html( $price, $product );

    }

    //Pricing table
    public function show_pricing_table() {

        if ( !is_admin() ) 
            return $this->discount->show_pricing_table();
        else
            return '';

    }

    //Offer message
    public function show_offer_message() {

        if ( !is_admin() ) 
            return $this->discount->show_offer_message();
        else
            return '';

    }

    //Offer products
    public function show_offer_products() {

        return $this->discount->show_offer_products();

    }

    /**
     * Check if woocommerce plugin is active
     */
    public function awdp_check_woocommerce_active()
    {

        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            return true;
        }
        if (is_multisite()) {
            $plugins = get_site_option('active_sitewide_plugins');
            if (isset($plugins['woocommerce/woocommerce.php']))
                return true;
        }
        return false;

    }

    // Deactivate plugin
    public function awdp_plugin_deactivate() {
        global $wpdb;
        // $wpdb->query( 
        //     $wpdb->prepare( 
        //         "DELETE pm FROM {$wpdb->prefix}postmeta pm INNER JOIN {$wpdb->prefix}posts wp ON wp.ID = pm.post_id WHERE pm.meta_key = '".AWDP_Feed_Attribute."';" 
        //     )
        // );
        $wpdb->query( 
            $wpdb->prepare( 
                "UPDATE {$wpdb->prefix}postmeta pm INNER JOIN {$wpdb->prefix}posts p on p.ID = pm.post_id SET pm.meta_value = '' WHERE pm.meta_key = '".AWDP_Feed_Attribute."';"
            )
        );
    }

    public function awdp_styles(){ 

        // Hide Coupon Box
        $hideCouponBox = get_option('awdp_hide_coupon_box') ? get_option('awdp_hide_coupon_box') : false;

        $couponLabel = get_option('awdp_fee_label') ? mb_strtolower ( get_option('awdp_fee_label') ) : 'discount';
        // $styleLabel = get_option('awdp_fee_label') ? str_replace(' ', '-', mb_strtolower ( get_option('awdp_fee_label') ) ) : 'discount'; 
        $bordercolor = get_option('awdp_table_border') ? get_option('awdp_table_border') : '';      
        $tablefontsize = get_option('awdp_tablefontsize') ? ( get_option('awdp_tablefontsize') != '0' ? get_option('awdp_tablefontsize') : '' ) : '';  
        // Badge Settings
        $badge_settings = get_option('awdp_badge_settings') ? get_option('awdp_badge_settings') : []; 
        $badge_onsale = array_key_exists( 'badge_onsale', $badge_settings ) ? $badge_settings['badge_onsale'] : '';
        $badge_color = array_key_exists( 'badge_color', $badge_settings ) ? $badge_settings['badge_color'] : '#3498db'; 
        // Timer Settings
        $timer_settings = get_option('awdp_timer_settings') ? get_option('awdp_timer_settings') : []; 
        $timer_color = array_key_exists( 'timer_color', $timer_settings ) ? $timer_settings['timer_color'] : '#3498db';
        $label_color = array_key_exists( 'timer_label_color', $timer_settings ) ? $timer_settings['timer_label_color'] : '#3498db'; 
        ?>

        <style> .wdp_table_outter{padding:10px 0;} .wdp_table_outter h4{margin: 10px 0 15px 0;} table.wdp_table{ border-top-style:solid; border-top-width:1px !important; border-top-color:<?php if ( $bordercolor == '' ) echo 'inherit'; else echo $bordercolor; ?>; border-right-style:solid; border-right-width:1px !important; border-right-color:<?php if ( $bordercolor == '' ) echo 'inherit'; else echo $bordercolor; ?>;border-collapse: collapse; margin-bottom:0px;<?php if ( $tablefontsize ) { echo 'font-size:'.$tablefontsize.'px'; } ?> } table.wdp_table td{border-bottom-style:solid; border-bottom-width:1px !important; border-bottom-color:<?php if ( $bordercolor == '' ) echo 'inherit'; else echo $bordercolor; ?>; border-left-style:solid; border-left-width:1px !important; border-left-color:<?php if ( $bordercolor == '' ) echo 'inherit'; else echo $bordercolor; ?>; padding:10px 20px !important;} <?php if( $bordercolor != '' ) { ?> table.wdp_table td, table.wdp_table tr { border: 1px solid <?php echo $bordercolor; ?> } <?php } ?>table.wdp_table.lay_horzntl td{padding:10px 15px !important;} a[data-coupon="<?php echo $couponLabel; ?>"]{ display: none; } .wdp-ribbon-two:before, .wdp-ribbon-two.left:before { border-color:<?php echo $badge_color; ?>; border-width: 12px 8px; } .wdp-ribbon-one:before, .wdp-ribbon-one:after { border-color:<?php echo $badge_color; ?>; } .wdp-ribbon-seven:before { border-right: 12px solid <?php echo $badge_color; ?>; } .wdp-ribbon-seven.left:before { border-left: 12px solid <?php echo $badge_color; ?>; border-right: none; } .wdp_helpText{ font-size: 12px; top: 5px; position: relative; } <?php if ($badge_onsale) { ?>.onsale{ display: none !important; }<?php } ?> #timer.timerone .timer_blocks, #timer.timertwo .timer_blocks, #timer.timerthree .timer_blocks, #timer.timerfour .timer_blocks, #timer.timerfive .timer_blocks, #timer.timersix .timer_blocks { color: <?php echo $label_color; ?> } #timer.timerone .timer_blocks span, #timer.timertwo .timer_blocks span, #timer.timerthree .timer_blocks span, #timer.timerfour .timer_blocks span, #timer.timerfive .timer_blocks span, #timer.timersix .timer_blocks span { color: <?php echo $label_color; ?> } #timer.timersix, #timer.timerthree .timer_blocks, #timer.timerfour .timer_blocks { background: <?php echo $timer_color; ?> } #timer.timerone .timer_blocks, #timer.timertwo .timer_blocks { border-color: <?php echo $timer_color; ?> } <?php if ( $hideCouponBox )  { ?> .woocommerce-cart-form .coupon, .woocommerce-cart .coupon, .woocommerce-form-coupon-toggle, .woocommerce .checkout_coupon { display:none !important; } {display:none !important;} <?php } ?> .awdpOfferMsg { width: 100%; float: left; margin: 20px 0px; box-sizing: border-box; display: block !important; } .awdpOfferMsg span { display: inline-block; } </style>
        
    <?php }

    /**
     * AWDP Register Custom post types
     */
    public function register_awdp_discounts()
    {

        $post_type = AWDP_POST_TYPE;
        $labels = array(
            'name'                  => __('Pricing Rules', 'aco-woo-dynamic-pricing'),
            'singular_name'         => __('Pricing Rule', 'aco-woo-dynamic-pricing'),
            'add_new'               => _x('Add New Pricing Rule', $post_type, 'aco-woo-dynamic-pricing'),
            'add_new_item'          => sprintf(__('Add New %s', 'aco-woo-dynamic-pricing'), 'Form'),
            'edit_item'             => sprintf(__('Edit %s', 'aco-woo-dynamic-pricing'), 'Form'),
            'new_item'              => sprintf(__('New %s', 'aco-woo-dynamic-pricing'), 'Form'),
            'all_items'             => sprintf(__('Product Rules', 'aco-woo-dynamic-pricing'), 'Form'),
            'view_item'             => sprintf(__('View %s', 'aco-woo-dynamic-pricing'), 'Form'),
            'search_items'          => sprintf(__('Search %s', 'aco-woo-dynamic-pricing'), 'Form'),
            'not_found'             => sprintf(__('No %s Found', 'aco-woo-dynamic-pricing'), 'Form'),
            'not_found_in_trash'    => sprintf(__('No %s Found In Trash', 'aco-woo-dynamic-pricing'), 'Form'),
            'parent_item_colon'     => sprintf(__('Parent %s'), 'Form'),
        );
        $args = array(
            'labels'                => apply_filters($post_type . '_labels', $labels),
            'description'           => '',
            'public'                => false,
            'publicly_queryable'    => false,
            'exclude_from_search'   => true,
            'show_ui'               => false,
            // 'show_in_menu' => 'edit.php?post_type=product',
            'show_in_nav_menus'     => false,
            'query_var'             => false,
            'can_export'            => true,
            'rewrite'               => false,
            'capability_type'       => 'post',
            'has_archive'           => false,
            'rest_base'             => $post_type,
            'hierarchical'          => false,
            'show_in_rest'          => false,
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'supports'              => array('title'),
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-admin-post',
            // 'taxonomies' => array('product_cat')
        );
        register_post_type($post_type, apply_filters($post_type . '_register_args', $args, $post_type));

        // Product Lists
        $post_type = AWDP_PRODUCT_LIST;
        $labels = array(
            'name'                  => __('Product Lists', 'aco-woo-dynamic-pricing'),
            'singular_name'         => __('Product List', 'aco-woo-dynamic-pricing'),
            'add_new'               => _x('Add New Product List', $post_type, 'aco-woo-dynamic-pricing'),
            'add_new_item'          => sprintf(__('Add New %s', 'aco-woo-dynamic-pricing'), 'Form'),
            'edit_item'             => sprintf(__('Edit %s', 'aco-woo-dynamic-pricing'), 'Form'),
            'new_item'              => sprintf(__('New %s', 'aco-woo-dynamic-pricing'), 'Form'),
            'all_items'             => sprintf(__('Product Lists', 'aco-woo-dynamic-pricing'), 'Form'),
            'view_item'             => sprintf(__('View %s', 'aco-woo-dynamic-pricing'), 'Form'),
            'search_items'          => sprintf(__('Search %s', 'aco-woo-dynamic-pricing'), 'Form'),
            'not_found'             => sprintf(__('No %s Found', 'aco-woo-dynamic-pricing'), 'Form'),
            'not_found_in_trash'    => sprintf(__('No %s Found In Trash', 'aco-woo-dynamic-pricing'), 'Form'),
            'parent_item_colon'     => sprintf(__('Parent %s'), 'Form'),
        );
        $args = array(
            'labels'                => apply_filters($post_type . '_labels', $labels),
            'description'           => '',
            'public'                => false,
            'publicly_queryable'    => false,
            'exclude_from_search'   => true,
            'show_ui'               => false,
            // 'show_in_menu' => 'edit.php?post_type=product',
            'show_in_nav_menus'     => false,
            'query_var'             => false,
            'can_export'            => true,
            'rewrite'               => false,
            'capability_type'       => 'post',
            'has_archive'           => false,
            'rest_base'             => $post_type,
            'hierarchical'          => false,
            'show_in_rest'          => false,
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'supports'              => array('title'),
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-admin-post',
            // 'taxonomies' => array('product_cat')
        );
        register_post_type($post_type, apply_filters($post_type . '_register_args', $args, $post_type));

    }

    public function wdpAdminNotices()
    {
        if ($this->wdpLicenseCheck() === FALSE) {
            ?>
            <div class="error">
                <p>You have an invalid or expired license key for<strong> <?php echo AWDP_PLUGIN_NAME; ?></strong>. Please go to the <a href="<?php echo admin_url('admin.php?page=awdp_ui_settings#/'); ?>">Settings Page</a> License Key section to correct this issue.
                </p>
            </div>
            <?php
        }

        if ( 'yes' !== get_option( 'woocommerce_enable_coupons' ) ) { ?>
            <div class="error">
                <p><strong><?php echo AWDP_PLUGIN_NAME; ?></strong> uses virtual coupons for applying discounts. For proper working of our plugin, please enable coupons (WooCommerce -> Settings -> Enable coupons).</p>
            </div>
        <?php }

        // Checking Permalink
        $wdp_permalink = get_option( 'permalink_structure' ); 
        if ( $wdp_permalink === '' ) { ?>
            <div class="error">
                <p>If you are facing any loading issues with <strong><?php echo AWDP_PLUGIN_NAME; ?></strong>, please make sure the permalink settings is not set to plain (Settings -> Permalinks).</p>
            </div>
        <?php }

    }

    public function wdpLicenseCheck()
    {
        $license_status = get_option('awdp_license_status');
        if ($license_status == 'valid') {
            return true;
        } else {
            return FALSE;
        }
    }

}
