<?php

if (!defined('ABSPATH'))
    exit;

class AWDP_Api
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
    private $_active = false;

    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route('awdp/v1', '/rules/', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_rules'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/rules/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_rules'),
                'permission_callback' => array($this, 'get_permission'),
            ));
            register_rest_route('awdp/v1', '/rules/(?P<filterRule>\w+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_rules'),
                'permission_callback' => array($this, 'get_permission'),
            ));
            register_rest_route('awdp/v1', '/rules/', array(
                'methods' => 'POST',
                'callback' => array($this, 'post_rule'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/delete/', array(
                'methods' => 'POST',
                'callback' => array($this, 'action_delete'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/statusChange/', array(
                'methods' => 'POST',
                'callback' => array($this, 'status_change'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/bulkAction/', array(
                'methods' => 'POST',
                'callback' => array($this, 'bulk_action'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/orderChange/', array(
                'methods' => 'POST',
                'callback' => array($this, 'order_change'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/sortList/', array(
                'methods' => 'POST',
                'callback' => array($this, 'sorting_list'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/userroles/', array(
                'methods' => 'POST',
                'callback' => array($this, 'user_roles'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/productlist/', array(
                'methods' => 'GET',
                'callback' => array($this, 'product_list'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/productlist/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'product_list'),
                'permission_callback' => array($this, 'get_permission'),
                // 'args' => ['id']
            ));
            register_rest_route('awdp/v1', '/product_rule/', array(
                'methods' => 'POST',
                'callback' => array($this, 'product_rule'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/awdp_settings/', array(
                'methods' => 'POST',
                'callback' => array($this, 'awdp_settings'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/awdp_settings/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'awdp_settings'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/awdp_activation/', array(
                'methods' => 'POST',
                'callback' => array($this, 'awdp_activation'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/data/products', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_products'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/productsandlistsearch', array(
                'methods' => 'GET',
                'callback' => array($this, 'product_nd_list_search'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/productsearch', array(
                'methods' => 'GET',
                'callback' => array($this, 'products_search'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/productlistsearch', array(
                'methods' => 'GET',
                'callback' => array($this, 'product_list_search'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/taxsearch', array(
                'methods' => 'GET',
                'callback' => array($this, 'taxonomy_search'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/usersearch', array(
                'methods' => 'GET',
                'callback' => array($this, 'user_search'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/variationlist/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'variation_list'),
                'permission_callback' => array($this, 'get_permission')
            ));
            //New @@ 3.4.0 Varaiable Product List
            register_rest_route('awdp/v1', '/variableproductsearch', array(
                'methods' => 'GET',
                'callback' => array($this, 'variable_product_search'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/variationsearch', array(
                'methods' => 'GET',
                'callback' => array($this, 'variation_search'),
                'permission_callback' => array($this, 'get_permission')
            ));
            // @ver 5.0.4 - Duplicate Rule
            register_rest_route('awdp/v1', '/duplicateRule/', array(
                'methods' => 'POST',
                'callback' => array($this, 'action_duplicate'),
                'permission_callback' => array($this, 'get_permission')
            ));
        });
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

    function action_delete($data)
    {
        $data = $data->get_params();
        if ($data['id']) {
            $pt = get_post_type($data['id']);
            if ($pt == AWDP_POST_TYPE && wp_delete_post($data['id'], true)) {
                return admin_url('admin.php?page=awdp_admin_ui');
            } else if ($pt == AWDP_PRODUCT_LIST && wp_delete_post($data['id'], true)) {
                return admin_url('admin.php?page=awdp_admin_product_lists');
            }
        }
    }

    function action_duplicate($data) 
    {
        $data   = $data->get_params();
        $id     = $data['id']; 
        if ( $id ) {
            $title      = get_the_title($id).' - Copy';
            $author_id  = get_post_field ( 'post_author', $id );
            $badge_args = array (
                'post_title'    => $title,
                'post_status'   => 'publish',
                'post_type'     => AWDP_POST_TYPE,
                'post_author'   => $author_id
            );
            $newRuleID = wp_insert_post ( $badge_args );

            // Copy post metadata
            $data = get_post_custom ( $id );
            foreach ( $data as $key => $values ) {
                foreach ( $values as $value ) { 
                    if ( $key === 'bogo_combinations' || $key === 'gift' || $key === 'discount_schedule_days' || $key === 'discount_schedules' || $key === 'discount_bogopayranges' || $key === 'discount_quantityranges' || $key === 'discount_cartamount' || $key === 'discount_bogo_combinations' || $key === 'discount_gift' || $key === 'gift_config' || $key === 'discount_gift_cart_item' || $key === 'discount_config' || $key === 'user_config' || $key === 'wdp_shortcode' || $key === 'wdp_bogo_config' || $key === 'custom_product_list' ) {
                        $value = unserialize ( $value );
                    } else if ( $key === 'discount_status' ) {
                        $value = 0;
                    }
                    if ( metadata_exists ( 'post', $newRuleID, $key ) ) { 
                        update_post_meta ( $newRuleID, $key, $value );
                    } else {
                        add_post_meta ( $newRuleID, $key, $value );
                    }
                }
            }

        }

        // Get all badges
        // $all_listings = get_posts ( array ( 'fields' => 'ids', 'posts_per_page' => -1, 'post_type' => AWDP_POST_TYPE ) );
        // Listing discounts based on priority
        $all_listings = get_posts(array('fields' => 'ids','posts_per_page' => -1, 'post_type' => AWDP_POST_TYPE, 'meta_key' => 'discount_priority', 'orderby' => 'meta_value_num','meta_type' => 'NUMBER', 'order' => 'ASC'));
        $result = array();
        $discount_type_name = Array(
            'percent_total_amount'  => __('Percentage of cart total amount', 'aco-woo-dynamic-pricing'),
            'percent_product_price' => __('Percentage of product price', 'aco-woo-dynamic-pricing'),
            'fixed_product_price'   => __('Fixed price of product price', 'aco-woo-dynamic-pricing'),
            'fixed_cart_amount'     => __('Fixed price of cart total amount', 'aco-woo-dynamic-pricing'),
            'cart_quantity'         => __('Quantity based discount', 'aco-woo-dynamic-pricing'),
            'bogo'                  => __('Buy X Get X', 'aco-woo-dynamic-pricing'),
            'gift'                  => __('Gift Product', 'aco-woo-dynamic-pricing'),
            'pay_method'            => __('Payment method', 'aco-woo-dynamic-pricing'),
            'ship_method'           => __('Shipping method', 'aco-woo-dynamic-pricing')
        );

        $wp_tz_stngs    = get_option('awdp_time_zone_config') ? get_option('awdp_time_zone_config') : []; 
        $wp_tz          = array_key_exists ( 'wordpress_timezone', $wp_tz_stngs ) ? $wp_tz_stngs['wordpress_timezone'] : '';
        $order_index    = 0; 

        if ( $wp_tz ) {

            $timezone   = new DateTimeZone( wp_timezone_string() );
            $datenow    = wp_date("Y-m-d H:i:s", null, $timezone );

        } else {

            // Get wordpress timezone settings
            $gmt_offset         = get_option('gmt_offset');
            $timezone_string    = get_option('timezone_string');
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

        }

        foreach ($all_listings as $listID) {

            $date1          = get_post_meta($listID, 'discount_start_date', true);
            $date2          = get_post_meta($listID, 'discount_end_date', true);
            $date2Frmt      = $date2 ? date_format(date_create($date2),"Y-m-d H:i:s") : '';
            $scheduleStatus = get_post_meta($listID, 'discount_status', true) ? ( ( ( strtotime($datenow) < strtotime($date2Frmt) ) || $date2Frmt == '' ) ? 'Active' : 'Inactive' ) : ''; 

            if (!isset($date2) || $date2 == '') {
                $discount_schedule = 'Starts '.date_format(date_create($date1), 'jS M Y');
            } else if (date_format(date_create($date1), 'j M Y') == date_format(date_create($date2), 'j M Y')) {
                $discount_schedule = date_format(date_create($date1), 'jS M Y');
            } else if (date_format(date_create($date1), 'M Y') == date_format(date_create($date2), 'M Y')) {
                $discount_schedule = date_format(date_create($date1), 'jS') . ' - '. date_format(date_create($date2), 'jS M Y');
            } else if (date_format(date_create($date1), 'Y') == date_format(date_create($date2), 'Y')) {
                $discount_schedule = date_format(date_create($date1), 'jS M') . ' - '. date_format(date_create($date2), 'jS M Y');
            } else {
                $discount_schedule = date_format(date_create($date1), 'j M Y') . ' - '. date_format(date_create($date2), 'j M Y');
            }

            $result[] = Array(
                'checkbox'          => '',
                'order_index'       => $order_index,                
                'discount_id'       => $listID,
                'discount_title'    => html_entity_decode ( get_the_title ( $listID ) ),
                'discount_status'   => get_post_meta($listID, 'discount_status', true),
                'discount_schedule' => $discount_schedule ? $discount_schedule : '',
                'discount_type'     => get_post_meta($listID, 'discount_type', true),
                'discount_value'    => ( get_post_meta($listID, 'discount_type', true) == 'percent_product_price' || get_post_meta($listID, 'discount_type', true) == 'fixed_product_price' || get_post_meta($listID, 'discount_type', true) == 'percent_total_amount' || get_post_meta($listID, 'discount_type', true) == 'fixed_cart_amount' ) ? get_post_meta($listID, 'discount_value', true) : '' ,
                'discount_date'     => get_the_date('d M Y', $listID),
                'discount_priority' => get_post_meta($listID, 'discount_priority', true),
                'discount_type_name' => array_key_exists ( get_post_meta($listID, 'discount_type', true), $discount_type_name ) ? $discount_type_name[get_post_meta($listID, 'discount_type', true)] : '',
                'discount_expiry'   => $scheduleStatus
            );
            $order_index++;
        }
        return new WP_REST_Response($result, 200);

        // }
    }

    function order_change($data) 
    {

        $data           = $data->get_params();
        $newIndex       = $data['newIndex'];
        $oldIndex       = $data['oldIndex'];
        $ruleID         = $data['ruleID'];
        $items          = $data['items'];
        $firstChange    = false;
        // Setting flag for first time index change
        if ( get_option ( 'awdp_orderChange' ) === false ) { 
            add_option ( 'awdp_orderChange', 1 );
            $firstChange = true;
        }
        // Change the index
        if ( $firstChange ) { 
            foreach ( $items as $key => $value ) {
                $priority = $key + 1;
                update_post_meta( $value['discount_id'], 'discount_priority', $priority );
            }
        } else { 
            // itterating upto max index change
            $changeIndex = $oldIndex > $newIndex ? $oldIndex : $newIndex;
            $splitItems = array_slice ( $items, 0, $changeIndex + 1 );
            foreach ( $splitItems as $key => $value ) {
                $priority = $key + 1;
                update_post_meta( $value['discount_id'], 'discount_priority', $priority ); 
            }
        }
        // End index change

        $wp_tz_stngs    = get_option('awdp_time_zone_config') ? get_option('awdp_time_zone_config') : []; 
        $wp_tz          = array_key_exists ( 'wordpress_timezone', $wp_tz_stngs ) ? $wp_tz_stngs['wordpress_timezone'] : '';
        $order_index    = 0;

        if ( $wp_tz ) {

            $timezone   = new DateTimeZone( wp_timezone_string() );
            $datenow    = wp_date("Y-m-d H:i:s", null, $timezone );

        } else {

            // Get wordpress timezone settings
            $gmt_offset         = get_option('gmt_offset');
            $timezone_string    = get_option('timezone_string');
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

        }
        
        // Listing discounts based on priority
        $result         = array();
        $all_listings   = get_posts(array('fields' => 'ids','posts_per_page' => -1, 'post_type' => AWDP_POST_TYPE, 'meta_key' => 'discount_priority', 'orderby' => 'meta_value_num','meta_type' => 'NUMBER', 'order' => 'ASC'));
        $discount_type_name = Array(
            'percent_total_amount'  => __('Percentage of cart total amount', 'aco-woo-dynamic-pricing'),
            'percent_product_price' => __('Percentage of product price', 'aco-woo-dynamic-pricing'),
            'fixed_product_price'   => __('Fixed price of product price', 'aco-woo-dynamic-pricing'),
            'fixed_cart_amount'     => __('Fixed price of cart total amount', 'aco-woo-dynamic-pricing'),
            'cart_quantity'         => __('Quantity based discount', 'aco-woo-dynamic-pricing'),
            'bogo'                  => __('Buy X Get X', 'aco-woo-dynamic-pricing'),
            'gift'                  => __('Gift Product', 'aco-woo-dynamic-pricing'),
            'pay_method'            => __('Payment method', 'aco-woo-dynamic-pricing'),
            'ship_method'           => __('Shipping method', 'aco-woo-dynamic-pricing')
        );
        foreach ($all_listings as $listID) {
            $date1          = get_post_meta($listID, 'discount_start_date', true);
            $date2          = get_post_meta($listID, 'discount_end_date', true);
            $date2Frmt      = $date2 ? date_format(date_create($date2),"Y-m-d H:i:s") : '';
            $scheduleStatus = get_post_meta($listID, 'discount_status', true) ? ( ( ( strtotime($datenow) < strtotime($date2Frmt) ) || $date2Frmt == '' ) ? 'Active' : 'Inactive' ) : '';
            if (!isset($date2) || $date2 == ''){
                $discount_schedule = 'Starts '.date_format(date_create($date1), 'jS M Y');
            } else if (date_format(date_create($date1), 'j M Y') == date_format(date_create($date2), 'j M Y')){
                $discount_schedule = date_format(date_create($date1), 'jS M Y');
            } else if (date_format(date_create($date1), 'M Y') == date_format(date_create($date2), 'M Y')){
                $discount_schedule = date_format(date_create($date1), 'jS') . ' - '. date_format(date_create($date2), 'jS M Y');
            } else if (date_format(date_create($date1), 'Y') == date_format(date_create($date2), 'Y')){
                $discount_schedule = date_format(date_create($date1), 'jS M') . ' - '. date_format(date_create($date2), 'jS M Y');
            } else {
                $discount_schedule = date_format(date_create($date1), 'j M Y') . ' - '. date_format(date_create($date2), 'j M Y');
            }
            $result[] = Array(
                'checkbox'          => '',
                'order_index'       => $order_index,
                'discount_id'       => $listID,
                'discount_title'    => html_entity_decode ( get_the_title ( $listID ) ),
                'discount_status'   => get_post_meta($listID, 'discount_status', true),
                'discount_schedule' => $discount_schedule ? $discount_schedule : '',
                'discount_type'     => get_post_meta($listID, 'discount_type', true),
                'discount_value'    => get_post_meta($listID, 'discount_type', true) != 'cart_quantity' ? get_post_meta($listID, 'discount_value', true) : '',
                'discount_date'     => get_the_date('d M Y', $listID),
                'discount_priority' => get_post_meta($listID, 'discount_priority', true),
                'discount_type_name' => array_key_exists ( get_post_meta($listID, 'discount_type', true), $discount_type_name ) ? $discount_type_name[get_post_meta($listID, 'discount_type', true)] : ''
            );
            $order_index++;
        }
        return new WP_REST_Response($result, 200);

    }

    function sorting_list($data) 
    {

        $data           = $data->get_params();
        $value          = $data['value'];
        $type           = $data['type'];
        $firstChange    = false;

        $wp_tz_stngs    = get_option('awdp_time_zone_config') ? get_option('awdp_time_zone_config') : []; 
        $wp_tz          = array_key_exists ( 'wordpress_timezone', $wp_tz_stngs ) ? $wp_tz_stngs['wordpress_timezone'] : '';
        $order_index    = 0;

        if ( $wp_tz ) {

            $timezone   = new DateTimeZone( wp_timezone_string() );
            $datenow    = wp_date("Y-m-d H:i:s", null, $timezone );

        } else {

            // Get wordpress timezone settings
            $gmt_offset         = get_option('gmt_offset');
            $timezone_string    = get_option('timezone_string');
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

        }
        
        // Listing discounts based on priority
        $result         = array();
        $all_args       = array ( 
            'fields'        => 'ids',
            'posts_per_page' => -1, 
            'post_type'     => AWDP_POST_TYPE, 
            'meta_key'      => 'discount_priority', 
            'orderby'       => 'meta_value_num',
            'meta_type'     => 'NUMBER', 
            'order'         => 'ASC' 
        );
        if ( $value != '') {
            if ( $value === 'title') {
                $all_args['orderby']    = 'title';
                $all_args['order']      = $type;
            } else {
                $all_args['meta_key']   = $value;
                $all_args['order']      = $type;
            }
        }
        $all_listings   = get_posts ( $all_args );
        $discount_type_name = Array(
            'percent_total_amount'  => __('Percentage of cart total amount', 'aco-woo-dynamic-pricing'),
            'percent_product_price' => __('Percentage of product price', 'aco-woo-dynamic-pricing'),
            'fixed_product_price'   => __('Fixed price of product price', 'aco-woo-dynamic-pricing'),
            'fixed_cart_amount'     => __('Fixed price of cart total amount', 'aco-woo-dynamic-pricing'),
            'cart_quantity'         => __('Quantity based discount', 'aco-woo-dynamic-pricing'),
            'bogo'                  => __('Buy X Get X', 'aco-woo-dynamic-pricing'),
            'gift'                  => __('Gift Product', 'aco-woo-dynamic-pricing'),
            'pay_method'            => __('Payment method', 'aco-woo-dynamic-pricing'),
            'ship_method'           => __('Shipping method', 'aco-woo-dynamic-pricing')
        );
        foreach ($all_listings as $listID) {
            $date1          = get_post_meta($listID, 'discount_start_date', true);
            $date2          = get_post_meta($listID, 'discount_end_date', true);
            $date2Frmt      = $date2 ? date_format(date_create($date2),"Y-m-d H:i:s") : '';
            $scheduleStatus = get_post_meta($listID, 'discount_status', true) ? ( ( ( strtotime($datenow) < strtotime($date2Frmt) ) || $date2Frmt == '' ) ? 'Active' : 'Inactive' ) : '';
            if (!isset($date2) || $date2 == ''){
                $discount_schedule = 'Starts '.date_format(date_create($date1), 'jS M Y');
            } else if (date_format(date_create($date1), 'j M Y') == date_format(date_create($date2), 'j M Y')){
                $discount_schedule = date_format(date_create($date1), 'jS M Y');
            } else if (date_format(date_create($date1), 'M Y') == date_format(date_create($date2), 'M Y')){
                $discount_schedule = date_format(date_create($date1), 'jS') . ' - '. date_format(date_create($date2), 'jS M Y');
            } else if (date_format(date_create($date1), 'Y') == date_format(date_create($date2), 'Y')){
                $discount_schedule = date_format(date_create($date1), 'jS M') . ' - '. date_format(date_create($date2), 'jS M Y');
            } else {
                $discount_schedule = date_format(date_create($date1), 'j M Y') . ' - '. date_format(date_create($date2), 'j M Y');
            }
            $result[] = Array(
                'checkbox'          => '',
                'order_index'       => $order_index,
                'discount_id'       => $listID,
                'discount_title'    => html_entity_decode ( get_the_title ( $listID ) ),
                'discount_status'   => get_post_meta($listID, 'discount_status', true),
                'discount_schedule' => $discount_schedule ? $discount_schedule : '',
                'discount_type'     => get_post_meta($listID, 'discount_type', true),
                'discount_value'    => get_post_meta($listID, 'discount_type', true) != 'cart_quantity' ? get_post_meta($listID, 'discount_value', true) : '',
                'discount_date'     => get_the_date('d M Y', $listID),
                'discount_priority' => get_post_meta($listID, 'discount_priority', true),
                'discount_type_name' => array_key_exists ( get_post_meta($listID, 'discount_type', true), $discount_type_name ) ? $discount_type_name[get_post_meta($listID, 'discount_type', true)] : ''
            );
            $order_index++;
        }
        return new WP_REST_Response($result, 200);

    }

    function status_change($data)
    {
        $data       = $data->get_params();
        $wdp_status = ( $data['status'] ) ? 1 : 0;
        $id         = $data['id'];

        if ( $id ) {

            update_post_meta($id, 'discount_status', $wdp_status);

            $wp_tz_stngs    = get_option('awdp_time_zone_config') ? get_option('awdp_time_zone_config') : []; 
            $wp_tz          = array_key_exists ( 'wordpress_timezone', $wp_tz_stngs ) ? $wp_tz_stngs['wordpress_timezone'] : '';
            $order_index    = 0;

            if ( $wp_tz ) {

                $timezone   = new DateTimeZone( wp_timezone_string() );
                $datenow    = wp_date("Y-m-d H:i:s", null, $timezone );

            } else {

                // Get wordpress timezone settings
                $gmt_offset         = get_option('gmt_offset');
                $timezone_string    = get_option('timezone_string');
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

            }

            // $all_listings = get_posts(array('fields' => 'ids','posts_per_page' => -1, 'post_type' => AWDP_POST_TYPE));
            // Listing discounts based on priority
            $result         = array();
            $all_args       = array(
                'fields'            => 'ids',
                'posts_per_page'    => -1, 
                'post_type'         => AWDP_POST_TYPE, 
                'meta_key'          => 'discount_priority', 
                'orderby'           => 'meta_value_num',
                'meta_type'         => 'NUMBER', 
                'order'             => 'ASC'
            );
            $all_listings   = get_posts ( $all_args );
            $discount_type_name = Array(
                'percent_total_amount'  => __('Percentage of cart total amount', 'aco-woo-dynamic-pricing'),
                'percent_product_price' => __('Percentage of product price', 'aco-woo-dynamic-pricing'),
                'fixed_product_price'   => __('Fixed price of product price', 'aco-woo-dynamic-pricing'),
                'fixed_cart_amount'     => __('Fixed price of cart total amount', 'aco-woo-dynamic-pricing'),
                'cart_quantity'         => __('Quantity based discount', 'aco-woo-dynamic-pricing'),
                'bogo'                  => __('Buy X Get X', 'aco-woo-dynamic-pricing'),
                'gift'                  => __('Gift Product', 'aco-woo-dynamic-pricing'),
                'pay_method'            => __('Payment method', 'aco-woo-dynamic-pricing'),
                'ship_method'           => __('Shipping method', 'aco-woo-dynamic-pricing')
            );
            foreach ($all_listings as $listID) {
                $date1          = get_post_meta($listID, 'discount_start_date', true);
                $date2          = get_post_meta($listID, 'discount_end_date', true);
                $date2Frmt      = $date2 ? date_format(date_create($date2),"Y-m-d H:i:s") : '';
                $scheduleStatus = get_post_meta($listID, 'discount_status', true) ? ( ( ( strtotime($datenow) < strtotime($date2Frmt) ) || $date2Frmt == '' ) ? 'Active' : 'Inactive' ) : ''; 
                if (!isset($date2) || $date2 == ''){
                    $discount_schedule = 'Starts '.date_format(date_create($date1), 'jS M Y');
                } else if (date_format(date_create($date1), 'j M Y') == date_format(date_create($date2), 'j M Y')){
                    $discount_schedule = date_format(date_create($date1), 'jS M Y');
                } else if (date_format(date_create($date1), 'M Y') == date_format(date_create($date2), 'M Y')){
                    $discount_schedule = date_format(date_create($date1), 'jS') . ' - '. date_format(date_create($date2), 'jS M Y');
                } else if (date_format(date_create($date1), 'Y') == date_format(date_create($date2), 'Y')){
                    $discount_schedule = date_format(date_create($date1), 'jS M') . ' - '. date_format(date_create($date2), 'jS M Y');
                } else {
                    $discount_schedule = date_format(date_create($date1), 'j M Y') . ' - '. date_format(date_create($date2), 'j M Y');
                }
                $result[] = Array(
                    'checkbox'          => '',
                    'order_index'       => $order_index,                    'discount_id'       => $listID,
                    'discount_title'    => html_entity_decode ( get_the_title ( $listID ) ),
                    'discount_status'   => get_post_meta($listID, 'discount_status', true),
                    'discount_schedule' => $discount_schedule ? $discount_schedule : '',
                    'discount_type'     => get_post_meta($listID, 'discount_type', true),
                    'discount_value'    => ( get_post_meta($listID, 'discount_type', true) == 'percent_product_price' || get_post_meta($listID, 'discount_type', true) == 'fixed_product_price' || get_post_meta($listID, 'discount_type', true) == 'percent_total_amount' || get_post_meta($listID, 'discount_type', true) == 'fixed_cart_amount' ) ? get_post_meta($listID, 'discount_value', true) : '' ,
                    'discount_date'     => get_the_date('d M Y', $listID),
                    'discount_priority' => get_post_meta($listID, 'discount_priority', true),
                    'discount_type_name' => array_key_exists ( get_post_meta($listID, 'discount_type', true), $discount_type_name ) ? $discount_type_name[get_post_meta($listID, 'discount_type', true)] : '',
                    'discount_expiry'   => $scheduleStatus
                );
                $order_index++;            
            }
            return new WP_REST_Response($result, 200);

        }
    }

    function user_roles($data)
    {
        $result     = array();
        $aw_index   = 0;
        global $wp_roles;
        if (!isset($wp_roles)) $wp_roles = new WP_Roles();
        $result[$aw_index]['label'] = 'All Roles';
        $result[$aw_index]['text']  = 0;
        $aw_index++;
        foreach ($wp_roles->get_names() as $key => $value) {
            $result[$aw_index]['label'] = $value;
            $result[$aw_index]['text']  = $key;
            $aw_index++;
        }
        return new WP_REST_Response($result, 200);
    }

    function awdp_activation($data){

        $data = $data->get_params();
        $license = trim(sanitize_text_field($data[0]));
        $api_params = array(
            'edd_action'    => 'activate_license',
            'license'       => $license,
            'item_id'       => AWDP_ITEM_ID, // The ID of the item in EDD
            'url'           => home_url()
        );
        $homeURL = ( ( strpos ( get_home_url(), 'localhost' ) !== false || strpos ( get_home_url(), '127.0.0.1' ) !== false ) ) ? 'localhost' : get_home_url(); 

        //Saving license key
        if ( false === get_option('awdp_license_key') )
            add_option('awdp_license_key', $license, '', 'yes');
        else
            update_option('awdp_license_key', $license);

        // Call the custom API.
        $response = wp_remote_post(AWDP_STORE_URL, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

        // make sure the response came back okay
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            if (is_wp_error($response)) {
                $temp = $response->get_error_message();
                if (empty($temp)) {
                    $message = $response->get_error_message();
                } else {
                    $message = __('An error occurred, please try again.');
                }
            }
        } else {
            $license_data = json_decode(wp_remote_retrieve_body($response));

            if (false === $license_data->success) {
                switch ($license_data->error) {
                    case 'expired' :
                        $message = sprintf(
                            __('Your license key expired on %s.'), date_i18n(get_option('date_format'), strtotime($license_data->expires, current_time('timestamp')))
                        );
                        break;
                    case 'revoked' :
                        $message = __('Your license key has been disabled.');
                        break;
                    case 'missing' :
                        $message = __('Invalid license.');
                        break;
                    case 'invalid' :
                    case 'site_inactive' :
                        $message = __('Your license is not active for this URL.');
                        break;
                    case 'item_name_mismatch' :
                        $message = sprintf(__('This appears to be an invalid license key for %s.'), AWDP_PLUGIN_NAME);
                        break;
                    case 'no_activations_left':
                        $message = __('Your license key has reached its activation limit.');
                        break;
                    default :
                        $message = __('An error occurred, please try again.');
                        break;
                }
            } else if ( true === $license_data->success ) {
                $message = __('Your license key has been activated.');
                if ( $license_data->license == 'valid' ) {
                    if ( false === get_option('awdp_plugin_license_url') )
                        add_option('awdp_plugin_license_url', $homeURL, '', 'yes');
                    else
                        update_option('awdp_plugin_license_url', $homeURL);
                }
            }
        }

        if ( $license_data->expires != 'lifetime' && $license_data->expires != '' ) {
            $expiryDate = date_create ( $license_data->expires );
            $expiryDate = date_format ( $expiryDate, "Y-m-d" );
        } else {
            $expiryDate = $license_data->expires;
        }

        if ( false === get_option('awdp_plugin_license_expiry') )
            add_option('awdp_plugin_license_expiry', $expiryDate, '', 'yes');
        else
            update_option('awdp_plugin_license_expiry', $expiryDate);

        if ( false === get_option('awdp_license_status') )
            add_option('awdp_license_status', $license_data->license, '', 'yes');
        else
            update_option('awdp_license_status', $license_data->license);

        $result['message']          = $message;
        $result['response']         = '';
        $result['status']           = $license_data->license;
        $result['licenseactive']    = get_option('awdp_license_status') ? get_option('awdp_license_status') : 0;
        $result['licensekey']       = get_option('awdp_license_key') ? get_option('awdp_license_key') : '';
        $result['licenseexpiry']    = get_option('awdp_plugin_license_expiry') ? get_option('awdp_plugin_license_expiry') : '';
        $result['licenseurl']       = get_option('awdp_plugin_license_url') ? get_option('awdp_plugin_license_url') : '';

        return new WP_REST_Response($result, 200);

    }

    function bulk_action($data)
    {
        global $wpdb;
        $data           = $data->get_params();
        $action         = $data['action'];
        $selectedItems  = array_column($data['selectedItems'], 'discount_id'); 
        $priority       = 1; 

        if ( $action === 'delete' ) { 
            // Delete rules
            foreach ( $selectedItems as $item ) { 
                $status = wp_delete_post( $item, true );
            }
        } else if ( $action === 'activate' ) { 
            // Activate rules
            // foreach ( $selectedItems as $item ) {
            //     update_post_meta( $item, 'discount_status', true );
            // }  
            $selectedItems = implode ( ',', $selectedItems );
            $status = $wpdb->query ( $wpdb->prepare ( "UPDATE {$wpdb->prefix}postmeta SET meta_value = true WHERE meta_key like 'discount_status' AND post_id IN ( $selectedItems )" ) );     
        } else if ( $action === 'deactivate' ) { 
            // Deactivate rules
            // foreach ( $selectedItems as $item ) {
            //     update_post_meta( $item, 'discount_status', false );
            // }  
            $selectedItems = implode ( ',', $selectedItems );
            $status = $wpdb->query ( $wpdb->prepare ( "UPDATE {$wpdb->prefix}postmeta SET meta_value = false WHERE meta_key like 'discount_status' AND post_id IN ( $selectedItems )" ) ); 
        }
        
        $wp_tz_stngs    = get_option('awdp_time_zone_config') ? get_option('awdp_time_zone_config') : []; 
        $wp_tz          = array_key_exists ( 'wordpress_timezone', $wp_tz_stngs ) ? $wp_tz_stngs['wordpress_timezone'] : '';
        $order_index    = 0;

        if ( $wp_tz ) {

            $timezone   = new DateTimeZone( wp_timezone_string() );
            $datenow    = wp_date("Y-m-d H:i:s", null, $timezone );

        } else {

            // Get wordpress timezone settings
            $gmt_offset         = get_option('gmt_offset');
            $timezone_string    = get_option('timezone_string');
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

        }

        // $all_listings = get_posts(array('fields' => 'ids','posts_per_page' => -1, 'post_type' => AWDP_POST_TYPE));
        // Listing discounts based on priority
        $result         = array();
        $all_args       = array(
            'fields'            => 'ids',
            'posts_per_page'    => -1, 
            'post_type'         => AWDP_POST_TYPE, 
            'meta_key'          => 'discount_priority', 
            'orderby'           => 'meta_value_num',
            'meta_type'         => 'NUMBER', 
            'order'             => 'ASC'
        );

         // Checking filter parameters
         if ( isset ( $data['filterRule'] ) && $data['filterRule'] != '' && $action !== 'delete' ) { 
            $all_args['meta_query'][] = array (
                'key'       => 'discount_type',
                'value'     => $data['filterRule'],
                'compare'   => '='
            );
        }

        $all_listings   = get_posts($all_args);

        $discount_type_name = Array(
            'percent_total_amount'  => __('Percentage of cart total amount', 'aco-woo-dynamic-pricing'),
            'percent_product_price' => __('Percentage of product price', 'aco-woo-dynamic-pricing'),
            'fixed_product_price'   => __('Fixed price of product price', 'aco-woo-dynamic-pricing'),
            'fixed_cart_amount'     => __('Fixed price of cart total amount', 'aco-woo-dynamic-pricing'),
            'cart_quantity'         => __('Quantity based discount', 'aco-woo-dynamic-pricing'),
            'bogo'                  => __('Buy X Get X', 'aco-woo-dynamic-pricing'),
            'gift'                  => __('Gift Product', 'aco-woo-dynamic-pricing'),
            'pay_method'            => __('Payment method', 'aco-woo-dynamic-pricing'),
            'ship_method'           => __('Shipping method', 'aco-woo-dynamic-pricing')
        );

        $wp_tz_stngs    = get_option('awdp_time_zone_config') ? get_option('awdp_time_zone_config') : []; 
        $wp_tz          = array_key_exists ( 'wordpress_timezone', $wp_tz_stngs ) ? $wp_tz_stngs['wordpress_timezone'] : '';
        $order_index    = 0;

        if ( $wp_tz ) {

            $timezone   = new DateTimeZone( wp_timezone_string() );
            $datenow    = wp_date("Y-m-d H:i:s", null, $timezone );

        } else {

            // Get wordpress timezone settings
            $gmt_offset         = get_option('gmt_offset');
            $timezone_string    = get_option('timezone_string');
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

        }

        foreach ($all_listings as $listID) { 
            $date1      = get_post_meta($listID, 'discount_start_date', true);
            $date2      = get_post_meta($listID, 'discount_end_date', true);
            $date2Frmt  = $date2 ? date_format(date_create($date2),"Y-m-d H:i:s") : '';
            $scheduleStatus = get_post_meta($listID, 'discount_status', true) ? ( ( ( strtotime($datenow) < strtotime($date2Frmt) ) || $date2Frmt == '' ) ? 'Running' : 'Expired' ) : ''; 

            if (!isset($date2) || $date2 == ''){
                $discount_schedule = 'Starts '.date_format(date_create($date1), 'jS M Y');
            } else if (date_format(date_create($date1), 'j M Y') == date_format(date_create($date2), 'j M Y')){
                $discount_schedule = date_format(date_create($date1), 'jS M Y');
            } else if (date_format(date_create($date1), 'M Y') == date_format(date_create($date2), 'M Y')){
                $discount_schedule = date_format(date_create($date1), 'jS') . ' - '. date_format(date_create($date2), 'jS M Y');
            } else if (date_format(date_create($date1), 'Y') == date_format(date_create($date2), 'Y')){
                $discount_schedule = date_format(date_create($date1), 'jS M') . ' - '. date_format(date_create($date2), 'jS M Y');
            } else {
                $discount_schedule = date_format(date_create($date1), 'j M Y') . ' - '. date_format(date_create($date2), 'j M Y');
            }
            $result[] = Array(
                'checkbox'          => '',
                'order_index'       => $order_index,
                'discount_id'       => $listID,
                'discount_title'    => html_entity_decode ( get_the_title ( $listID ) ),
                'discount_status'   => get_post_meta($listID, 'discount_status', true),
                'discount_schedule' => $discount_schedule ? $discount_schedule : '',
                'discount_type'     => get_post_meta($listID, 'discount_type', true),
                'discount_value'    => ( get_post_meta($listID, 'discount_type', true) == 'percent_product_price' || get_post_meta($listID, 'discount_type', true) == 'fixed_product_price' || get_post_meta($listID, 'discount_type', true) == 'percent_total_amount' || get_post_meta($listID, 'discount_type', true) == 'fixed_cart_amount' ) ? get_post_meta($listID, 'discount_value', true) : '' ,
                'discount_date'     => get_the_date('d M Y', $listID),
                'discount_priority' => get_post_meta($listID, 'discount_priority', true),
                'discount_type_name' => array_key_exists ( get_post_meta($listID, 'discount_type', true), $discount_type_name ) ? $discount_type_name[get_post_meta($listID, 'discount_type', true)] : '',
                'discount_expiry'   => $scheduleStatus,
                'disableDrag'       => isset ( $data['filterRule'] ) ? true : false
            );
            $order_index++;
        }
        return new WP_REST_Response($result, 200);

    }

    function awdp_settings($data)
    {

        $checkML            = call_user_func ( array ( new AWDP_ML(), 'is_default_lan' ), '' );
        $currentLang        = !$checkML ? call_user_func ( array ( new AWDP_ML(), 'current_language' ), '' ) : '';

        if( ! $data['id'] ) {
            
            $data = $data->get_params();

            $pricing_title          = isset ( $data['pricing_title'] ) ? $data['pricing_title'] : '';
            $pricing_price_label    = isset ( $data['pricing_price_label'] ) ? $data['pricing_price_label'] : '';
            $pricing_quantity_label = isset ( $data['pricing_quantity_label'] ) ? $data['pricing_quantity_label'] : '';
            $pricing_new_label      = isset ( $data['pricing_new_label'] ) ? $data['pricing_new_label'] : '';
            $default_fee_label      = isset ( $data['default_fee_label'] ) ? $data['default_fee_label'] : '';
            $dismessagestatus       = isset ( $data['discount_message_status'] ) ? $data['discount_message_status'] : 0;
            $message                = isset ( $data['discount_message'] ) ? $data['discount_message'] : '';
            $variation_display      = isset ( $data['variation_display'] ) ? $data['variation_display'] : 'default';
            $tableposition          = isset ( $data['tableposition'] ) ? $data['tableposition'] : '';
            $pricing_carts_gtitle   = isset ( $data['pricing_carts_gtitle'] ) ? $data['pricing_carts_gtitle'] : '';
            $pricing_carts_gdesc    = isset ( $data['pricing_carts_gdesc'] ) ? $data['pricing_carts_gdesc'] : '';
            $pricing_carts_btitle   = isset ( $data['pricing_carts_btitle'] ) ? $data['pricing_carts_btitle'] : '';
            $pricing_carts_bdesc    = isset ( $data['pricing_carts_bdesc'] ) ? $data['pricing_carts_bdesc'] : '';
            $single_free_product    = isset ( $data['single_free_product'] ) ? $data['single_free_product'] : '';
            $giftsposition          = isset ( $data['giftsposition'] ) ? $data['giftsposition'] : '';
            $cartsuggestionsposition = isset ( $data['cartsuggestionsposition'] ) ? $data['cartsuggestionsposition'] : '';
            $cart_free_product      = isset ( $data['cart_free_product'] ) ? $data['cart_free_product'] : '';
            $disdescription         = isset ( $data['discount_description'] ) ? $data['discount_description'] : '';
            $disitemdescription     = isset ( $data['discount_item_description'] ) ? $data['discount_item_description'] : '';
            $tablesort              = isset ( $data['tablesort'] ) ? $data['tablesort'] : '';
            $tablevalue             = isset ( $data['tablevalue'] ) ? $data['tablevalue'] : '';
            $tablevaluetext         = isset ( $data['tablevaluetext'] ) ? $data['tablevaluetext'] : '';
            $tablevaluetextdisable  = isset ( $data['tablevaluetextdisable'] ) ? $data['tablevaluetextdisable'] : 0;
            $tablefontsize          = isset ( $data['tablefontsize'] ) ? $data['tablefontsize'] : 0;
            $tableborder            = isset ( $data['table_border_color'] ) ? $data['table_border_color'] : '';
            $badge_label_color      = isset ( $data['badge_label_color'] ) ? $data['badge_label_color'] : '';
            $badge_color            = isset ( $data['badge_color'] ) ? $data['badge_color'] : '';
            $badge_position         = isset ( $data['badge_position'] ) ? $data['badge_position'] : '';
            $badge_rule             = isset ( $data['badge_rule'] ) ? $data['badge_rule'] : '';
            $badge_style            = isset ( $data['badge_style'] ) ? $data['badge_style'] : '';
            $badge_label            = isset ( $data['badge_label'] ) ? $data['badge_label'] : '';
            $badge_enable           = isset ( $data['badge_enable'] ) ? $data['badge_enable'] : '';
            $badge_free             = isset ( $data['badge_free'] ) ? $data['badge_free'] : '';
            $badge_onsale           = isset ( $data['badge_onsale'] ) ? $data['badge_onsale'] : '';
            $timer_enable           = isset ( $data['timer_enable'] ) ? $data['timer_enable'] : '';
            $timer_start_enable     = isset ( $data['timer_start_enable'] ) ? $data['timer_start_enable'] : '';
            $timer_rule             = isset ( $data['timer_rule'] ) ? $data['timer_rule'] : '';
            $timer_style            = isset ( $data['timer_style'] ) ? $data['timer_style'] : '';
            $timer_start_time       = isset ( $data['timer_start_time'] ) ? $data['timer_start_time'] : '';
            $timer_end_time         = isset ( $data['timer_end_time'] ) ? $data['timer_end_time'] : '';
            $timer_prefix_text      = isset ( $data['timer_prefix_text'] ) ? $data['timer_prefix_text'] : '';
            $timer_position         = isset ( $data['timer_position'] ) ? $data['timer_position'] : '';
            $timer_label_days       = isset ( $data['timer_label_days'] ) ? $data['timer_label_days'] : '';
            $timer_label_hours      = isset ( $data['timer_label_hours'] ) ? $data['timer_label_hours'] : '';
            $timer_label_minutes    = isset ( $data['timer_label_minutes'] ) ? $data['timer_label_minutes'] : '';
            $timer_label_seconds    = isset ( $data['timer_label_seconds'] ) ? $data['timer_label_seconds'] : '';
            $timer_color            = isset ( $data['timer_color'] ) ? $data['timer_color'] : '';
            $timer_label_color      = isset ( $data['timer_label_color'] ) ? $data['timer_label_color'] : '';
            $timer_text             = isset ( $data['timer_text'] ) ? $data['timer_text'] : '';
            $offer                  = isset ( $data['offer'] ) ? $data['offer'] : '';
            
            $subtotal_label         = isset ( $data['subtotal_label'] ) ? $data['subtotal_label'] : '';
            $fee_enable             = isset ( $data['fee_enable'] ) ? $data['fee_enable'] : '';
            $disable_addon          = isset ( $data['disable_addon'] ) ? $data['disable_addon'] : '';
            $discount_method        = isset ( $data['discount_method'] ) ? $data['discount_method'] : '';
            $use_regular            = isset ( $data['use_regular'] ) ? $data['use_regular'] : '';

            $hide_coupon_box        = isset ( $data['hide_coupon_box'] ) ? $data['hide_coupon_box'] : '';
            $disable_discount       = isset ( $data['disable_discount'] ) ? $data['disable_discount'] : '';
            // $apply_coupon_discount = isset ( $data['apply_coupon_discount'] ) ? $data['apply_coupon_discount'] : '';

            $shortcode_listing      = isset ( $data['shortcode_listing'] ) ? $data['shortcode_listing'] : '';
            $listing_pagination     = isset ( $data['listing_pagination'] ) ? $data['listing_pagination'] : '';
            $listing_limit          = isset ( $data['listing_limit'] ) ? $data['listing_limit'] : '';
            $listing_columns        = isset ( $data['listing_columns'] ) ? $data['listing_columns'] : '';

            $enable_dismessage      = isset ( $data['enable_dismessage'] ) ? $data['enable_dismessage'] : '';
            $dismessage             = isset ( $data['dismessage'] ) ? $data['dismessage'] : '';
            $dismessage_rule        = isset ( $data['dismessage_rule'] ) ? $data['dismessage_rule'] : '';

            $offer_position         = isset ( $data['offer_position'] ) ? $data['offer_position'] : '';
            $offer_fontsize         = isset ( $data['offer_fontsize'] ) ? $data['offer_fontsize'] : '';
            $offer_paddding_lm      = isset ( $data['offer_paddding_lm'] ) ? $data['offer_paddding_lm'] : '';
            $offer_paddding_tp      = isset ( $data['offer_paddding_tp'] ) ? $data['offer_paddding_tp'] : '';
            $offer_radius           = isset ( $data['offer_radius'] ) ? $data['offer_radius'] : '';
            $offer_color            = isset ( $data['offer_color'] ) ? $data['offer_color'] : '';
            $offer_background       = isset ( $data['offer_background'] ) ? $data['offer_background'] : '';

            $border_top_width       = isset ( $data['border_top_width'] ) ? $data['border_top_width'] : '';
            $border_right_width     = isset ( $data['border_right_width'] ) ? $data['border_right_width'] : '';
            $border_bottom_width    = isset ( $data['border_bottom_width'] ) ? $data['border_bottom_width'] : '';
            $border_left_width      = isset ( $data['border_left_width'] ) ? $data['border_left_width'] : '';
            $offer_border_color     = isset ( $data['offer_border_color'] ) ? $data['offer_border_color'] : '';

            // TimeZone
            $wordpress_timezone     = isset ( $data['wordpress_timezone'] ) ? $data['wordpress_timezone'] : '';

            // New Settings         
            $dynamicpricing         = isset ( $data['dynamicpricing'] ) ? $data['dynamicpricing'] : '';
            $variablepricing        = isset ( $data['variablepricing'] ) ? $data['variablepricing'] : '';
            
            // You've Saved Text Settings
            $custom_message                     = isset ( $data['custom_message'] ) ? $data['custom_message'] : '';
            $custom_message_status              = isset ( $data['custom_message_status'] ) ? $data['custom_message_status'] : '';
            $custom_message_linheight           = isset ( $data['custom_message_linheight'] ) ? $data['custom_message_linheight'] : '';
            $custom_message_fontsize            = isset ( $data['custom_message_fontsize'] ) ? $data['custom_message_fontsize'] : '';
            $custom_message_position            = isset ( $data['custom_message_position'] ) ? $data['custom_message_position'] : '';
            $custom_message_paddding_lm         = isset ( $data['custom_message_paddding_lm'] ) ? $data['custom_message_paddding_lm'] : '';
            $custom_message_paddding_tp         = isset ( $data['custom_message_paddding_tp'] ) ? $data['custom_message_paddding_tp'] : '';
            $custom_message_border_radius       = isset ( $data['custom_message_border_radius'] ) ? $data['custom_message_border_radius'] : '';
            $custom_message_border_top_width    = isset ( $data['custom_message_border_top_width'] ) ? $data['custom_message_border_top_width'] : '';
            $custom_message_border_right_width  = isset ( $data['custom_message_border_right_width'] ) ? $data['custom_message_border_right_width'] : '';
            $custom_message_border_bottom_width = isset ( $data['custom_message_border_bottom_width'] ) ? $data['custom_message_border_bottom_width'] : '';
            $custom_message_border_left_width   = isset ( $data['custom_message_border_left_width'] ) ? $data['custom_message_border_left_width'] : '';
            $custom_message_border_color        = isset ( $data['custom_message_border_color'] ) ? $data['custom_message_border_color'] : '';
            $custom_message_background          = isset ( $data['custom_message_background'] ) ? $data['custom_message_background'] : '';
            $custom_message_color               = isset ( $data['custom_message_color'] ) ? $data['custom_message_color'] : '';
            /*
            * WPML Label
            * Version 4.0.5
            */
            // Lang Settings
            $langSettings               = get_option('awdp_settings_lang_options') ? get_option('awdp_settings_lang_options') : [];
            if ( $currentLang ) { 

                // if ( $langSettings && !array_key_exists ( $currentLang, $langSettings ) ) { 
                    $langSettings[$currentLang]['pricing_title']                = $pricing_title;
                    $langSettings[$currentLang]['pricing_price_label']          = $pricing_price_label;
                    $langSettings[$currentLang]['pricing_quantity_label']       = $pricing_quantity_label;
                    $langSettings[$currentLang]['pricing_new_label']            = $pricing_new_label;
                    $langSettings[$currentLang]['tablevaluetext']               = $tablevaluetext;
                    $langSettings[$currentLang]['discount_description']         = $disdescription;
                    $langSettings[$currentLang]['discount_item_description']    = $disitemdescription;
                // } else if ( $langSettings && array_key_exists ( $currentLang, $langSettings ) ) { 
                //     $langSettings[$currentLang]['pricing_title']                = $pricing_title;
                //     $langSettings[$currentLang]['pricing_price_label']          = $pricing_price_label;
                //     $langSettings[$currentLang]['pricing_quantity_label']       = $pricing_quantity_label;
                //     $langSettings[$currentLang]['pricing_new_label']            = $pricing_new_label;
                //     $langSettings[$currentLang]['tablevaluetext']               = $tablevaluetext;
                //     $langSettings[$currentLang]['discount_description']         = $disdescription;
                //     $langSettings[$currentLang]['discount_item_description']    = $disitemdescription;
                // }

                if ( false === get_option('awdp_settings_lang_options') )
                    add_option('awdp_settings_lang_options', $langSettings, '', 'yes');
                else
                    update_option('awdp_settings_lang_options', $langSettings);

            } 
            /*End*/


            $badge_config = array(
                'badge_label_color'     => $badge_label_color,
                'badge_color'           => $badge_color,
                'badge_position'        => $badge_position,
                'badge_style'           => $badge_style,
                'badge_label'           => $badge_label,
                'badge_enable'          => $badge_enable,
                'badge_free'            => $badge_free,
                'badge_onsale'          => $badge_onsale,
                'badge_rule'            => $badge_rule,
            );

            $timer_config = array(
                'timer_enable'          => $timer_enable,
                'timer_rule'            => $timer_rule,
                'timer_style'           => $timer_style,
                'timer_start_time'      => $timer_start_time,
                'timer_end_time'        => $timer_end_time,
                'timer_prefix_text'     => $timer_prefix_text,
                'timer_position'        => $timer_position,
                'timer_label_days'      => $timer_label_days,
                'timer_label_hours'     => $timer_label_hours,
                'timer_label_minutes'   => $timer_label_minutes,
                'timer_label_seconds'   => $timer_label_seconds,
                'timer_color'           => $timer_color,
                'timer_label_color'     => $timer_label_color,
                'timer_text'            => $timer_text,
                'timer_start_enable'    => $timer_start_enable
            );

            $addition_settings = array (
                'fee_enable'            => $fee_enable,
                'discount_method'       => $discount_method,
                'disable_addon'         => $disable_addon,
                'use_regular'           => $use_regular,
                'subtotal_label'        => $subtotal_label
            );

            $custom_message_settings = array (
                'custom_message_status'                 => $custom_message_status,
                'custom_message'                        => $custom_message,
                'custom_message_linheight'              => $custom_message_linheight,
                'custom_message_fontsize'               => $custom_message_fontsize,
                'custom_message_position'               => $custom_message_position,
                'custom_message_paddding_lm'            => $custom_message_paddding_lm,
                'custom_message_paddding_tp'            => $custom_message_paddding_tp,
                'custom_message_border_radius'          => $custom_message_border_radius,
                'custom_message_border_top_width'       => $custom_message_border_top_width,
                'custom_message_border_right_width'     => $custom_message_border_right_width,
                'custom_message_border_bottom_width'    => $custom_message_border_bottom_width,
                'custom_message_border_left_width'      => $custom_message_border_left_width,
                'custom_message_border_color'           => $custom_message_border_color,
                'custom_message_background'             => $custom_message_background,
                'custom_message_color'                  => $custom_message_color,
            );

            $offer_desc_config = array(
                'offer_position'        => $offer_position,
                'offer_fontsize'        => $offer_fontsize,
                'offer_paddding_lm'     => $offer_paddding_lm,
                'offer_paddding_tp'     => $offer_paddding_tp,
                'offer_radius'          => $offer_radius,
                'offer_color'           => $offer_color,
                'offer_background'      => $offer_background,
                'enable_dismessage'     => $enable_dismessage,
                'dismessage_rule'       => $dismessage_rule,
                'dismessage'            => $dismessage,
                'offer_border_color'    => $offer_border_color,
                'border_left_width'     => $border_left_width,
                'border_bottom_width'   => $border_bottom_width,
                'border_right_width'    => $border_right_width,
                'border_top_width'      => $border_top_width,
            ); 

            $time_zone_config = array(
                'wordpress_timezone'    => $wordpress_timezone
            );

            $new_config = array(
                'dynamicpricing'        => $dynamicpricing,
                'variablepricing'       => $variablepricing
            );

            if ( false === get_option('awdp_pc_title') )
                add_option('awdp_pc_title', $pricing_title, '', 'yes');
            else
                update_option('awdp_pc_title', $pricing_title);

            if ( false === get_option('awdp_pc_label') )
                add_option('awdp_pc_label', $pricing_price_label, '', 'yes');
            else
                update_option('awdp_pc_label', $pricing_price_label);

            if ( false === get_option('awdp_qn_label') )
                add_option('awdp_qn_label', $pricing_quantity_label, '', 'yes');
            else
                update_option('awdp_qn_label', $pricing_quantity_label);

            if ( false === get_option('awdp_new_label') )
                add_option('awdp_new_label', $pricing_new_label, '', 'yes');
            else
                update_option('awdp_new_label', $pricing_new_label);

            if ( false === get_option('awdp_fee_label') )
                add_option('awdp_fee_label', $default_fee_label, '', 'yes');
            else
                update_option('awdp_fee_label', $default_fee_label);

            if ( false === get_option('awdp_message_status') )
                add_option('awdp_message_status', $dismessagestatus, '', 'yes');
            else
                update_option('awdp_message_status', $dismessagestatus);

            if ( false === get_option('awdp_discount_message') )
                add_option('awdp_discount_message', $message, '', 'yes');
            else
                update_option('awdp_discount_message', $message);

            if ( false === get_option('awdp_variation_display') )
                add_option('awdp_variation_display', $variation_display, '', 'yes');
            else
                update_option('awdp_variation_display', $variation_display);

            if ( false === get_option('tableposition') && false === get_option('awdp_table_position') ) {
                add_option('awdp_table_position', $tableposition, '', 'yes');
            } else if ( false != get_option('tableposition') && false === get_option('awdp_table_position') ) {
                add_option('awdp_table_position', $tableposition, '', 'yes');
                update_option('tableposition', $tableposition);
            } else {
                update_option('awdp_table_position', $tableposition);
            }

            if ( false === get_option('awdp_offer_desc_config') )
                add_option('awdp_offer_desc_config', $offer_desc_config, '', 'yes');
            else
                update_option('awdp_offer_desc_config', $offer_desc_config);

            if ( false === get_option('awdp_gifttitle_cart') )
                add_option('awdp_gifttitle_cart', $pricing_carts_gtitle, '', 'yes');
            else
                update_option('awdp_gifttitle_cart', $pricing_carts_gtitle);

            if ( false === get_option('awdp_gift_desc_cart') )
                add_option('awdp_gift_desc_cart', $pricing_carts_gdesc, '', 'yes');
            else
                update_option('awdp_gift_desc_cart', $pricing_carts_gdesc);

            if ( false === get_option('awdp_bogotitle_cart') )
                add_option('awdp_bogotitle_cart', $pricing_carts_btitle, '', 'yes');
            else
                update_option('awdp_bogotitle_cart', $pricing_carts_btitle);

            if ( false === get_option('awdp_bogo_desc_cart') )
                add_option('awdp_bogo_desc_cart', $pricing_carts_bdesc, '', 'yes');
            else
                update_option('awdp_bogo_desc_cart', $pricing_carts_bdesc);

            if ( false === get_option('awdp_cart_free_product') )
                add_option('awdp_cart_free_product', $cart_free_product, '', 'yes');
            else
                update_option('awdp_cart_free_product', $cart_free_product);

            if ( false === get_option('awdp_single_free_product') )
                add_option('awdp_single_free_product', $single_free_product, '', 'yes');
            else
                update_option('awdp_single_free_product', $single_free_product);

            if ( false === get_option('awdp_gifts_position') )
                add_option('awdp_gifts_position', $giftsposition, '', 'yes');
            else
                update_option('awdp_gifts_position', $giftsposition);

            if ( false === get_option('awdp_gifts_cart_position') )
                add_option('awdp_gifts_cart_position', $cartsuggestionsposition, '', 'yes');
            else
                update_option('awdp_gifts_cart_position', $cartsuggestionsposition);

            if ( false === get_option('awdp_table_sort') )
                add_option('awdp_table_sort', $tablesort, '', 'yes');
            else
                update_option('awdp_table_sort', $tablesort);

            if ( false === get_option('awdp_table_value') )
                add_option('awdp_table_value', $tablevalue, '', 'yes');
            else
                update_option('awdp_table_value', $tablevalue);

            if ( false === get_option('awdp_table_value_text') )
                add_option('awdp_table_value_text', $tablevaluetext, '', 'yes');
            else
                update_option('awdp_table_value_text', $tablevaluetext);

            if ( false === get_option('awdp_table_value_notext') )
                add_option('awdp_table_value_notext', $tablevaluetextdisable, '', 'yes');
            else
                update_option('awdp_table_value_notext', $tablevaluetextdisable);

            if ( false === get_option('awdp_tablefontsize') )
                add_option('awdp_tablefontsize', $tablefontsize, '', 'yes');
            else
                update_option('awdp_tablefontsize', $tablefontsize);

            if ( false === get_option('awdp_table_border') )
                add_option('awdp_table_border', $tableborder, '', 'yes');
            else
                update_option('awdp_table_border', $tableborder);
                
            if ( false === get_option('awdp_badge_settings') )
                add_option('awdp_badge_settings', $badge_config, '', 'yes');
            else
                update_option('awdp_badge_settings', $badge_config);

            if ( false === get_option('awdp_timer_settings') )
                add_option('awdp_timer_settings', $timer_config, '', 'yes');
            else
                update_option('awdp_timer_settings', $timer_config);

            if ( false === get_option('awdp_addition_settings') )
                add_option('awdp_addition_settings', $addition_settings , '', 'yes');
            else
                update_option('awdp_addition_settings', $addition_settings );

            if ( false === get_option('awdp_discount_description') )
                add_option('awdp_discount_description', $disdescription, '', 'yes');
            else
                update_option('awdp_discount_description', $disdescription);

            if ( false === get_option('awdp_discount_item_description') )
                add_option('awdp_discount_item_description', $disitemdescription, '', 'yes');
            else
                update_option('awdp_discount_item_description', $disitemdescription);

            if ( false === get_option('awdp_hide_coupon_box') )
                add_option('awdp_hide_coupon_box', $hide_coupon_box, '', 'yes' );
            else
                update_option('awdp_hide_coupon_box', $hide_coupon_box );

            if ( false === get_option('awdp_disable_discount') )
                add_option('awdp_disable_discount', $disable_discount, '', 'yes' );
            else
                update_option('awdp_disable_discount', $disable_discount );

            // if ( false === get_option('awdp_apply_coupon_discount') )
            //     add_option('awdp_apply_coupon_discount', $apply_coupon_discount, '', 'yes' );
            // else
            //     update_option('awdp_apply_coupon_discount', $apply_coupon_discount );

            if ( false === get_option('awdp_shortcode_listing') )
                add_option('awdp_shortcode_listing', $shortcode_listing, '', 'yes' );
            else
                update_option('awdp_shortcode_listing', $shortcode_listing );

            if ( false === get_option('awdp_listing_pagination') )
                add_option('awdp_listing_pagination', $listing_pagination, '', 'yes' );
            else
                update_option('awdp_listing_pagination', $listing_pagination );

            if ( false === get_option('awdp_listing_limit') )
                add_option('awdp_listing_limit', $listing_limit, '', 'yes' );
            else
                update_option('awdp_listing_limit', $listing_limit );

            if ( false === get_option('awdp_listing_columns') )
                add_option('awdp_listing_columns', $listing_columns, '', 'yes' );
            else
                update_option('awdp_listing_columns', $listing_columns );

            if ( false === get_option('awdp_time_zone_config') )
                add_option('awdp_time_zone_config', $time_zone_config, '', 'yes');
            else
                update_option('awdp_time_zone_config', $time_zone_config);

            if ( false === get_option('awdp_new_config') )
                add_option('awdp_new_config', $new_config, '', 'yes');
            else
                update_option('awdp_new_config', $new_config);

            if ( false === get_option('awdp_custom_msg_settings') )
                add_option('awdp_custom_msg_settings', $custom_message_settings, '', 'yes');
            else
                update_option('awdp_custom_msg_settings', $custom_message_settings);

        }

        $langSettings               = get_option('awdp_settings_lang_options') ? get_option('awdp_settings_lang_options') : [];
        if ( !empty ($langSettings) && array_key_exists ( $currentLang, $langSettings ) ) {
            $pricing_title              = array_key_exists ( 'pricing_title', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['pricing_title'] : get_option('awdp_pc_title');
            $pricing_price_label        = array_key_exists ( 'pricing_price_label', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['pricing_price_label'] : get_option('awdp_pc_label');
            $pricing_quantity_label     = array_key_exists ( 'pricing_quantity_label', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['pricing_quantity_label'] : get_option('awdp_qn_label');
            $pricing_new_label          = array_key_exists ( 'pricing_new_label', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['pricing_new_label'] : get_option('awdp_new_label');
            $tablevaluetext             = array_key_exists ( 'tablevaluetext', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['tablevaluetext'] : get_option('awdp_table_value_text');
            $discount_description       = array_key_exists ( 'discount_description', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['discount_description'] : get_option('awdp_discount_description');
            $discount_item_description  = array_key_exists ( 'discount_item_description', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['discount_item_description'] : get_option('awdp_discount_item_description');  
        } else  {
            $pricing_title              = get_option('awdp_pc_title') ? get_option('awdp_pc_title') : '';
            $pricing_price_label        = get_option('awdp_pc_label') ? get_option('awdp_pc_label') : '';
            $pricing_quantity_label     = get_option('awdp_qn_label') ? get_option('awdp_qn_label') : '';
            $pricing_new_label          = get_option('awdp_new_label') ? get_option('awdp_new_label') : '';
            $tablevaluetext             = get_option('awdp_table_value_text') ? get_option('awdp_table_value_text') : '';
            $discount_description       = get_option('awdp_discount_description') ? get_option('awdp_discount_description') : '';
            $discount_item_description  = get_option('awdp_discount_item_description') ? get_option('awdp_discount_item_description') : '';
        }

        /* 
        * Wordpress Time Zone Settings
        * @ Ver 5.0.1
        */
        $wp_tz_stngs    = get_option('awdp_time_zone_config') ? get_option('awdp_time_zone_config') : []; 
        $wp_tz          = array_key_exists ( 'wordpress_timezone', $wp_tz_stngs ) ? $wp_tz_stngs['wordpress_timezone'] : '';
        if ( $wp_tz ) {
            $timezone  = new DateTimeZone( wp_timezone_string() );
            $schd_time = wp_date("F d, Y H:i", null, $timezone );
            $srvr_time = wp_date("H:i", null, $timezone );
        } else {
            $schd_time = gmdate('F d, Y H:i');
            $srvr_time = gmdate('H:i');
        }
        /**/

        $result['pricing_title']                = $pricing_title;
        $result['pricing_price_label']          = $pricing_price_label;
        $result['pricing_quantity_label']       = $pricing_quantity_label;
        $result['pricing_new_label']            = $pricing_new_label;
        $result['tablevaluetext']               = $tablevaluetext;
        $result['discount_description']         = $discount_description;
        $result['discount_item_description']    = $discount_item_description;
        $result['default_fee_label']            = get_option('awdp_fee_label') ? get_option('awdp_fee_label') : '';
        $result['discount_message_status']      = get_option('awdp_message_status') ? get_option('awdp_message_status') : '';
        $result['discount_message']             = get_option('awdp_discount_message') ? get_option('awdp_discount_message') : '';
        $result['variation_display']            = get_option('awdp_variation_display') ? get_option('awdp_variation_display') : 'default';
        // $result['discount_description']      = get_option('awdp_discount_description') ? get_option('awdp_discount_description') : '';
        // $result['discount_item_description'] = get_option('awdp_discount_item_description') ? get_option('awdp_discount_item_description') : '';
        $result['tableposition']                = get_option('awdp_table_position') ? get_option('awdp_table_position') : ( get_option('tableposition') ? get_option('tableposition') : '' );
        $result['pricing_carts_gtitle']         = get_option('awdp_gifttitle_cart') ? get_option('awdp_gifttitle_cart') : '';
        $result['pricing_carts_gdesc']          = get_option('awdp_gift_desc_cart') ? get_option('awdp_gift_desc_cart') : '';
        $result['pricing_carts_btitle']         = get_option('awdp_bogotitle_cart') ? get_option('awdp_bogotitle_cart') : '';
        $result['pricing_carts_bdesc']          = get_option('awdp_bogo_desc_cart') ? get_option('awdp_bogo_desc_cart') : '';
        $result['single_free_product']          = get_option('awdp_single_free_product') ? get_option('awdp_single_free_product') : '';
        $result['giftsposition']                = get_option('awdp_gifts_position') ? get_option('awdp_gifts_position') : '';
        $result['cartsuggestionsposition']      = get_option('awdp_gifts_cart_position') ? get_option('awdp_gifts_cart_position') : '';
        $result['cart_free_product']            = get_option('awdp_cart_free_product') ? get_option('awdp_cart_free_product') : '';
        $result['awdp_license_key']             = get_option('awdp_license_key') ? get_option('awdp_license_key') : '';
        $result['awdp_license_status']          = get_option('awdp_license_status') ? get_option('awdp_license_status') : '';
        $result['tablesort']                    = get_option('awdp_table_sort') ? get_option('awdp_table_sort') : '';
        $result['tablevalue']                   = get_option('awdp_table_value') ? get_option('awdp_table_value') : '';
        // $result['tablevaluetext']            = get_option('awdp_table_value_text') ? get_option('awdp_table_value_text') : '';
        $result['tablevaluetextdisable']        = get_option('awdp_table_value_notext') ? get_option('awdp_table_value_notext') : '';
        $result['tablefontsize']                = get_option('awdp_tablefontsize') ? get_option('awdp_tablefontsize') : '';
        $result['tableborder']                  = get_option('awdp_table_border') ? get_option('awdp_table_border') : '';

        // Badge Settings
        $badge_settings                         = get_option('awdp_badge_settings') ? get_option('awdp_badge_settings') : [];
        $result['badge_label_color']            = $badge_settings['badge_label_color'];
        $result['badge_color']                  = $badge_settings['badge_color'];
        $result['badge_position']               = $badge_settings['badge_position'];
        $result['badge_style']                  = $badge_settings['badge_style'];
        $result['badge_label']                  = $badge_settings['badge_label'];
        $result['badge_enable']                 = $badge_settings['badge_enable'];
        $result['badge_free']                   = $badge_settings['badge_free'];
        $result['badge_onsale']                 = $badge_settings['badge_onsale'];
        $result['badge_rule']                   = $badge_settings['badge_rule'];

        // Timer Settings
        $timer_settings                     = get_option('awdp_timer_settings') ? get_option('awdp_timer_settings') : [];
        $result['timer_enable']             = $timer_settings['timer_enable'];
        $result['timer_start_enable']       = $timer_settings['timer_start_enable'];
        $result['timer_rule']               = $timer_settings['timer_rule'];
        $result['timer_style']              = $timer_settings['timer_style'];
        $result['timer_start_time']         = $timer_settings['timer_start_time'];
        $result['timer_end_time']           = $timer_settings['timer_end_time'];
        $result['timer_prefix_text']        = $timer_settings['timer_prefix_text'];
        $result['timer_position']           = $timer_settings['timer_position'];
        $result['timer_label_days']         = $timer_settings['timer_label_days'];
        $result['timer_label_hours']        = $timer_settings['timer_label_hours'];
        $result['timer_label_minutes']      = $timer_settings['timer_label_minutes'];
        $result['timer_label_seconds']      = $timer_settings['timer_label_seconds'];
        $result['timer_color']              = $timer_settings['timer_color'];
        $result['timer_label_color']        = $timer_settings['timer_label_color'];
        $result['timer_text']               = $timer_settings['timer_text'];

        // Additional Settings
        $addition_settings                  = get_option('awdp_addition_settings') ? get_option('awdp_addition_settings') : [];
        $result['fee_enable']               = array_key_exists ( 'fee_enable', $addition_settings ) ? $addition_settings['fee_enable'] : false;
        $result['disable_addon']            = array_key_exists ( 'disable_addon', $addition_settings ) ? $addition_settings['disable_addon'] : false;
        $result['discount_method']          = array_key_exists ( 'discount_method', $addition_settings ) ? $addition_settings['discount_method'] : '';
        $result['use_regular']              = array_key_exists ( 'use_regular', $addition_settings ) ? $addition_settings['use_regular'] : false;
        $result['subtotal_label']           = array_key_exists ( 'subtotal_label', $addition_settings ) ? $addition_settings['subtotal_label'] : '';
        
        
        $custom_message_settings                        = get_option('awdp_custom_msg_settings') ? get_option('awdp_custom_msg_settings') : [];
        $result['custom_message_status']                = array_key_exists ( 'custom_message_status', $custom_message_settings ) ? $custom_message_settings['custom_message_status'] : false;
        $result['custom_message']                       = array_key_exists ( 'custom_message', $custom_message_settings ) ? $custom_message_settings['custom_message'] : '';
        $result['custom_message_linheight']             = array_key_exists ( 'custom_message_linheight', $custom_message_settings ) ? $custom_message_settings['custom_message_linheight'] : '';
        $result['custom_message_fontsize']              = array_key_exists ( 'custom_message_fontsize', $custom_message_settings ) ? $custom_message_settings['custom_message_fontsize'] : '';
        $result['custom_message_position']              = array_key_exists ( 'custom_message_position', $custom_message_settings ) ? $custom_message_settings['custom_message_position'] : '';
        $result['custom_message_paddding_lm']           = array_key_exists ( 'custom_message_paddding_lm', $custom_message_settings ) ? $custom_message_settings['custom_message_paddding_lm'] : '';
        $result['custom_message_paddding_tp']           = array_key_exists ( 'custom_message_paddding_tp', $custom_message_settings ) ? $custom_message_settings['custom_message_paddding_tp'] : '';
        $result['custom_message_border_radius']         = array_key_exists ( 'custom_message_border_radius', $custom_message_settings ) ? $custom_message_settings['custom_message_border_radius'] : '';
        $result['custom_message_border_top_width']      = array_key_exists ( 'custom_message_border_top_width', $custom_message_settings ) ? $custom_message_settings['custom_message_border_top_width'] : '';
        $result['custom_message_border_right_width']    = array_key_exists ( 'custom_message_border_right_width', $custom_message_settings ) ? $custom_message_settings['custom_message_border_right_width'] : '';
        $result['custom_message_border_bottom_width']   = array_key_exists ( 'custom_message_border_bottom_width', $custom_message_settings ) ? $custom_message_settings['custom_message_border_bottom_width'] : '';
        $result['custom_message_border_left_width']     = array_key_exists ( 'custom_message_border_left_width', $custom_message_settings ) ? $custom_message_settings['custom_message_border_left_width'] : '';
        $result['custom_message_border_color']          = array_key_exists ( 'custom_message_border_color', $custom_message_settings ) ? $custom_message_settings['custom_message_border_color'] : '';
        $result['custom_message_background']            = array_key_exists ( 'custom_message_background', $custom_message_settings ) ? $custom_message_settings['custom_message_background'] : '';
        $result['custom_message_color']                 = array_key_exists ( 'custom_message_color', $custom_message_settings ) ? $custom_message_settings['custom_message_color'] : '';

        $offer_desc_config_saved            = get_option('awdp_offer_desc_config') ? get_option('awdp_offer_desc_config') : []; 
        $result['offer_position']           = array_key_exists ( 'offer_position', $offer_desc_config_saved ) ? $offer_desc_config_saved['offer_position'] : '';
        $result['offer_fontsize']           = array_key_exists ( 'offer_fontsize', $offer_desc_config_saved ) ? $offer_desc_config_saved['offer_fontsize'] : '';
        $result['offer_paddding_lm']        = array_key_exists ( 'offer_paddding_lm', $offer_desc_config_saved ) ? $offer_desc_config_saved['offer_paddding_lm'] : '';
        $result['offer_paddding_tp']        = array_key_exists ( 'offer_paddding_tp', $offer_desc_config_saved ) ? $offer_desc_config_saved['offer_paddding_tp'] : '';
        $result['offer_radius']             = array_key_exists ( 'offer_radius', $offer_desc_config_saved ) ? $offer_desc_config_saved['offer_radius'] : '';
        $result['offer_color']              = array_key_exists ( 'offer_color', $offer_desc_config_saved ) ? $offer_desc_config_saved['offer_color'] : '';
        $result['offer_background']         = array_key_exists ( 'offer_background', $offer_desc_config_saved ) ? $offer_desc_config_saved['offer_background'] : '';

        $result['border_top_width']         = array_key_exists ( 'border_top_width', $offer_desc_config_saved ) ? $offer_desc_config_saved['border_top_width'] : '';
        $result['border_right_width']       = array_key_exists ( 'border_right_width', $offer_desc_config_saved ) ? $offer_desc_config_saved['border_right_width'] : '';
        $result['border_bottom_width']      = array_key_exists ( 'border_bottom_width', $offer_desc_config_saved ) ? $offer_desc_config_saved['border_bottom_width'] : '';
        $result['border_left_width']        = array_key_exists ( 'border_left_width', $offer_desc_config_saved ) ? $offer_desc_config_saved['border_left_width'] : '';
        $result['offer_border_color']       = array_key_exists ( 'offer_border_color', $offer_desc_config_saved ) ? $offer_desc_config_saved['offer_border_color'] : '';
        
        $result['enable_dismessage']        = array_key_exists ( 'enable_dismessage', $offer_desc_config_saved ) ? $offer_desc_config_saved['enable_dismessage'] : '';
        $result['dismessage_rule']          = array_key_exists ( 'dismessage_rule', $offer_desc_config_saved ) ? $offer_desc_config_saved['dismessage_rule'] : '';
        $result['dismessage']               = array_key_exists ( 'dismessage', $offer_desc_config_saved ) ? $offer_desc_config_saved['dismessage'] : '';

        $result['hide_coupon_box']          = get_option('awdp_hide_coupon_box') ? get_option('awdp_hide_coupon_box') : '';
        $result['disable_discount']         = get_option('awdp_disable_discount') ? get_option('awdp_disable_discount') : '';
        // $result['apply_coupon_discount'] = get_option('awdp_apply_coupon_discount') ? get_option('awdp_apply_coupon_discount') : '';

        $result['shortcode_listing']        = get_option('awdp_shortcode_listing') ? get_option('awdp_shortcode_listing') : 'carousel';
        $result['listing_pagination']       = get_option('awdp_listing_pagination') ? get_option('awdp_listing_pagination') : '';
        $result['listing_limit']            = get_option('awdp_listing_limit') ? get_option('awdp_listing_limit') : '';
        $result['listing_columns']          = get_option('awdp_listing_columns') ? get_option('awdp_listing_columns') : '';

        $time_zone_config_saved             = get_option('awdp_time_zone_config') ? get_option('awdp_time_zone_config') : []; 
        $result['wordpress_timezone']       = array_key_exists ( 'wordpress_timezone', $time_zone_config_saved ) ? $time_zone_config_saved['wordpress_timezone'] : '';

        $new_config                         = get_option('awdp_new_config') ? get_option('awdp_new_config') : []; 
        $result['dynamicpricing']           = array_key_exists ( 'dynamicpricing', $new_config ) ? $new_config['dynamicpricing'] : '';
        $result['variablepricing']          = array_key_exists ( 'variablepricing', $new_config ) ? $new_config['variablepricing'] : '';

        $result['server_date_time']         = $schd_time;

        return new WP_REST_Response($result, 200);
    }

    function post_rule($data)
    {
        $this->delete_transient();
        $data = $data->get_params();
        if ($data['id']) {
            $my_post = array(
                'ID' => $data['id'],
                'post_title' => wp_strip_all_tags($data['name']),
                'post_content' => '',
            );
            wp_update_post($my_post);
            $this->rule_update_meta($data, $data['id']);
            return $data['id'];
            
        } else {
            $my_post = array(
                'post_type' => AWDP_POST_TYPE,
                'post_title' => wp_strip_all_tags($data['name']),
                'post_content' => '',
                'post_status' => 'publish',
            );
            $id = wp_insert_post($my_post);
            $this->rule_update_meta($data, $id);
            return $id;
        }
    }

    public function delete_transient()
    {
        delete_transient(AWDP_PRODUCTS_TRANSIENT_KEY);
    }

    function rule_update_meta($data, $id)
    {

        $wdp_start_date     = isset($data['start_date']) ? $data['start_date'] : '';
        $wdp_end_date       = isset($data['end_date']) ? $data['end_date'] : '';
        $wdp_discount_type  = isset($data['discount_type']) ? $data['discount_type'] : '';
        $wdp_discount       = isset($data['discount']) ? $data['discount'] : '';
        $wdp_status         = isset($data['status']) ? $data['status'] : '';
        $wdp_reg_customers  = isset($data['reg_customers']) ? $data['reg_customers'] : '';
        $wdp_reg_user_roles = isset($data['reg_user_roles']) ? $data['reg_user_roles'] : '';
        $wdp_pricing_table  = isset($data['pricing_table']) ? $data['pricing_table'] : '';
        $wdp_priority       = isset($data['priority']) ? $data['priority'] : '';
        $wdp_product_list   = isset($data['product_list']) ? $data['product_list'] : '';
        $wdp_inc_tax        = isset($data['inc_tax']) ? $data['inc_tax'] : '';
        $wdp_label          = isset($data['label']) ? $data['label'] : '';
        $wdp_sequentially   = isset($data['sequentially']) ? $data['sequentially'] : '';
        $wdp_usage_limit    = isset($data['usage_limit']) ? $data['usage_limit'] : '';
        $wdp_per_user_limit = isset($data['per_user_limit']) ? $data['per_user_limit'] : '';
        $wdp_users_limit    = isset($data['userlimits']) ? $data['userlimits'] : '';
        $wdp_user_roles     = isset($data['user_roles']) ? $data['user_roles'] : '';
        $wdp_disable_on_sale = isset($data['disable_on_sale']) ? $data['disable_on_sale'] : '';
        $wdp_apply_rule_once = isset($data['apply_rule_once']) ? $data['apply_rule_once'] : '';
        $wdp_show_in_loop   = isset($data['show_in_loop']) ? $data['show_in_loop'] : '';
        $wdp_rules          = isset($data['rules']) ? $data['rules'] : '';
        $wdp_quantity_type  = isset($data['quantity_type']) ? $data['quantity_type'] : '';
        $wdp_weekday        = isset($data['discount_schedule_weekday']) ? $data['discount_schedule_weekday'] : '';

        $bogo_buy           = isset($data['bogo_buy']) ? $data['bogo_buy'] : '';
        $bogo_rule_type     = isset($data['bogo_rule_type']) ? $data['bogo_rule_type'] : '';
        $bogo_x_prods       = isset($data['bogo_x_prods']) ? $data['bogo_x_prods'] : '';
        $bogo_x_repeat      = isset($data['bogo_x_repeat']) ? $data['bogo_x_repeat'] : '';
        $bogo_x_all         = isset($data['bogo_x_all']) ? $data['bogo_x_all'] : '';
        $offer_single_count = isset($data['offer_single_count']) ? $data['offer_single_count'] : '';
        $bogo_y_all         = isset($data['bogo_y_all']) ? $data['bogo_y_all'] : '';
        $bogo_y_individual  = isset($data['bogo_y_individual']) ? $data['bogo_y_individual'] : '';
        $bogo_n_prods       = isset($data['bogo_n_prods']) ? $data['bogo_n_prods'] : '';
        $bogo_n_count       = isset($data['bogo_n_count']) ? $data['bogo_n_count'] : '';
        $bogo_n_repeat      = isset($data['bogo_n_repeat']) ? $data['bogo_n_repeat'] : '';
        $bogo_n_loop        = isset($data['bogo_n_loop']) ? $data['bogo_n_loop'] : '';
        $bogo_cheapest_prods = isset($data['bogo_cheapest_prods']) ? $data['bogo_cheapest_prods'] : '';
        $bogo_cheapest_buy  = isset($data['bogo_cheapest_buy']) ? $data['bogo_cheapest_buy'] : '';
        $bogo_cheapest_get  = isset($data['bogo_cheapest_get']) ? $data['bogo_cheapest_get'] : '';
        $bogo_cheapest_type = isset($data['bogo_cheapest_type']) ? $data['bogo_cheapest_type'] : '';
        $bogo_cheapest_value = isset($data['bogo_cheapest_value']) ? $data['bogo_cheapest_value'] : '';
        $bogo_get           = isset($data['bogo_get']) ? $data['bogo_get'] : '';
        $bogo_type          = isset($data['bogo_type']) ? $data['bogo_type'] : '';
        $bogo_value         = isset($data['bogo_value']) ? $data['bogo_value'] : '';
        $bogo_combinations  = isset($data['bogo_combinations']) ? serialize($data['bogo_combinations']) : '';

        $gift_type          = isset($data['gift_type']) ? $data['gift_type'] : '';
        $gift               = isset($data['gift']) ? serialize($data['gift']) : '';
        $gifts_number       = isset($data['gifts_number']) ? $data['gifts_number'] : '';
        $gift_cart_item     = isset($data['gift_cart_item']) ? $data['gift_cart_item'] : '';
        $gift_auto_cart     = isset($data['add_gift_cart']) ? $data['add_gift_cart'] : '';
        $gift_variation     = isset($data['gift_variation']) ? $data['gift_variation'] : '';
        $disable_gift       = isset($data['disable_gift']) ? $data['disable_gift'] : '';
        $mulitple_gifts     = isset($data['mulitple_gifts']) ? $data['mulitple_gifts'] : '';
        $gift_cart_min      = isset($data['gift_cart_min']) ? $data['gift_cart_min'] : '';
        // $eligible_message = isset($data['eligible_message']) ? $data['eligible_message'] : '';
        $gift_products      = ( isset($data['gift_products']) && is_array($data['gift_products']) ) ? array_values ( array_filter ( $data['gift_products'] ) ) : '';
        
        $offer_message      = isset($data['offer_message']) ? $data['offer_message'] : '';
        $bogo_message       = isset($data['bogo_message']) ? $data['bogo_message'] : '';
        $offer_desc_status  = isset($data['offer_desc_status']) ? $data['offer_desc_status'] : '';
        
        $wdp_weekday        = isset($data['discount_schedule_weekday']) ? $data['discount_schedule_weekday'] : '';

        $wdp_user_restrictions      = isset($data['discount_user_restriction']) ? $data['discount_user_restriction'] : '';
        $wdp_user_role_restrictions = isset($data['discount_user_role_restriction']) ? $data['discount_user_role_restriction'] : '';

        $wdp_daily_limit_swicth = isset($data['discount_daily_limit']) ? $data['discount_daily_limit'] : '';
        $wdp_daily_usage_limit  = isset($data['daily_usage_limit']) ? $data['daily_usage_limit'] : '';
        $wdp_daily_user_limit   = isset($data['daily_user_limit']) ? $data['daily_user_limit'] : '';

        $wdp_short_desc         = isset($data['short_desc']) ? $data['short_desc'] : '';
        $wdp_short_title        = isset($data['short_title']) ? $data['short_title'] : '';
        $wdp_short_picture      = isset($data['short_picture']) ? $data['short_picture'] : '';
        
        $payment_type           = isset($data['payment_type']) ? $data['payment_type'] : '';

        $start_time             = isset($data['startTime']) ? date('H:i', strtotime( $data['startTime'] ) ) : '';
        $end_time               = isset($data['endTime']) ? date('H:i', strtotime( $data['endTime'] ) ) : '';
        
        $table_layout           = isset($data['table_layout']) ? $data['table_layout'] : '';

        $disc_calc_type         = isset($data['disc_calc_type']) ? $data['disc_calc_type'] : '';
        $discount_schedule_days = isset($data['discount_schedule_days']) ? serialize($data['discount_schedule_days']) : '';
        $bogo_payn_prods        = isset($data['bogo_payn_prods']) ? $data['bogo_payn_prods'] : '';

        $schedules              = isset($data['schedules']) ? $data['schedules'] : '';
        $schedule_array         = [];
        $key                    = 0;

        // Dynamic Value
        $dynamic_value          = isset($data['dynamic_value']) ? $data['dynamic_value'] : ''; 
        $customPL               = isset($data['customPL']) ? $data['customPL'] : '';
        $wdp_custom_pl          = isset($data['custom_pl']) ? $data['custom_pl'] : '';

        // Bogo Display Products Settings
        $bogo_sort                  = isset($data['bogo_sort']) ? $data['bogo_sort'] : ''; 
        $bogo_count                 = isset($data['bogo_count']) ? $data['bogo_count'] : ''; 
        $bogo_consider_individual   = isset($data['bogo_consider_individual']) ? $data['bogo_consider_individual'] : ''; 

        foreach($schedules as $schedule){ 
            // Start Date
            if($schedule['start_date']){
                $start_date = $schedule['start_date'];
                $start_date = date("Y-m-d H:i:s", strtotime($start_date));
                if( ( strtotime(get_post_meta($id, 'discount_start_date', true)) > strtotime($start_date) ) || $key == 0 ) {
                    update_post_meta($id, 'discount_start_date', $start_date);
                } 
            } else {
                $start_date = '';
            }
            // End Date
            if($schedule['end_date']){
                $end_date = $schedule['end_date'];
                $end_date = date("Y-m-d H:i:s", strtotime($end_date));
                if( ( strtotime(get_post_meta($id, 'discount_end_date', true)) < strtotime($end_date) ) || $key == 0 ) {
                    update_post_meta($id, 'discount_end_date', $end_date);
                } 
            } else {
                update_post_meta($id, 'discount_end_date', '');
                $end_date = '';
            }
            $schedule_array[$key]['start_date'] = $start_date;
            $schedule_array[$key]['end_date']   = $end_date;
            $key++;
        }

        $serialize_data     = array_values($schedule_array);
        $schedule_serialize = serialize($serialize_data);
        $quantityranges     = isset($data['quantityranges']) ? serialize($data['quantityranges']) : '';
        $variation_check    = isset($data['quantity_variation_check']) ? $data['quantity_variation_check'] : '';
        $tiered_pricing     = isset($data['quantity_tiered_pricing']) ? $data['quantity_tiered_pricing'] : '';
        $cheapest_check     = isset($data['quantity_cheapest_check']) ? $data['quantity_cheapest_check'] : '';
        $repeat_cheapest    = isset($data['repeat_cheapest']) ? $data['repeat_cheapest'] : '';
        $bogopayranges      = isset($data['bogopayranges']) ? serialize($data['bogopayranges']) : '';
        $cartamount         = isset($data['cartamount']) ? serialize($data['cartamount']) : '';

        $deposit_check      = isset($data['depositCheck']) ? $data['depositCheck'] : '';

        update_post_meta($id, 'discount_schedules', $schedule_serialize);
        update_post_meta($id, 'discount_quantityranges', $quantityranges);
        update_post_meta($id, 'discount_variation_check', $variation_check);
        update_post_meta($id, 'discount_tiered_pricing', $tiered_pricing);
        update_post_meta($id, 'discount_cheapest_check', $cheapest_check);
        update_post_meta($id, 'discount_repeat_cheapest', $repeat_cheapest);
        update_post_meta($id, 'discount_bogopayranges', $bogopayranges);
        update_post_meta($id, 'discount_cartamount', $cartamount);
        update_post_meta($id, 'discount_start_time', $start_time);
        update_post_meta($id, 'discount_end_time', $end_time);
        update_post_meta($id, 'discount_schedule_days', $discount_schedule_days);
        update_post_meta($id, 'discount_quantity_type', $wdp_quantity_type);
        update_post_meta($id, 'discount_schedule_weekday', $wdp_weekday);
        update_post_meta($id, 'discount_table_layout', $table_layout);
        update_post_meta($id, 'discount_calc_type', $disc_calc_type);

        update_post_meta($id, 'discount_type', $wdp_discount_type);
        update_post_meta($id, 'discount_value', $wdp_discount);
        update_post_meta($id, 'discount_status', $wdp_status);
        update_post_meta($id, 'discount_reg_customers', $wdp_reg_customers);
        update_post_meta($id, 'discount_reg_user_roles', $wdp_reg_user_roles);
        update_post_meta($id, 'discount_pricing_table', $wdp_pricing_table);
        update_post_meta($id, 'discount_priority', $wdp_priority);
        update_post_meta($id, 'discount_product_list', $wdp_product_list);

        update_post_meta($id, 'discount_payment_type', $payment_type);

        update_post_meta($id, 'discount_bogo_buy', $bogo_buy);
        update_post_meta($id, 'discount_bogo_rule_type', $bogo_rule_type);
        update_post_meta($id, 'discount_bogo_x_prods', $bogo_x_prods);
        update_post_meta($id, 'discount_bogo_x_repeat', $bogo_x_repeat);
        update_post_meta($id, 'discount_bogo_x_all', $bogo_x_all);
        update_post_meta($id, 'discount_offer_single_count', $offer_single_count);
        update_post_meta($id, 'discount_bogo_y_all', $bogo_y_all);
        update_post_meta($id, 'discount_bogo_y_individual', $bogo_y_individual);
        update_post_meta($id, 'discount_bogo_n_prods', $bogo_n_prods);
        update_post_meta($id, 'discount_bogo_n_count', $bogo_n_count);
        update_post_meta($id, 'discount_bogo_n_repeat', $bogo_n_repeat);
        update_post_meta($id, 'discount_bogo_n_loop', $bogo_n_loop);
        update_post_meta($id, 'discount_bogo_cheapest_prods', $bogo_cheapest_prods);
        update_post_meta($id, 'discount_bogo_cheapest_buy', $bogo_cheapest_buy);
        update_post_meta($id, 'discount_bogo_cheapest_get', $bogo_cheapest_get);
        update_post_meta($id, 'discount_bogo_cheapest_type', $bogo_cheapest_type);
        update_post_meta($id, 'discount_bogo_cheapest_value', $bogo_cheapest_value);
        update_post_meta($id, 'discount_bogo_get', $bogo_get);
        update_post_meta($id, 'discount_bogo_type', $bogo_type);
        update_post_meta($id, 'discount_bogo_value', $bogo_value);
        update_post_meta($id, 'discount_bogo_combinations', $bogo_combinations);
        update_post_meta($id, 'discount_bogo_message', $bogo_message);

        update_post_meta($id, 'discount_bogo_payn_prods', $bogo_payn_prods);
        // update_post_meta($id, 'discount_bogo_payn_prods', $bogo_payn_prods);

        update_post_meta($id, 'discount_gift_type', $gift_type);
        // update_post_meta($id, 'discount_gift_type', $gift_type);
        update_post_meta($id, 'discount_gift', $gift);
        // update_post_meta($id, 'discount_gift', $gift);
        update_post_meta($id, 'discount_gift_cart_item', $gift_cart_item);
        // update_post_meta($id, 'discount_gift_cart_item', $gift_cart_item);

        update_post_meta($id, 'dynamic_value', $dynamic_value);

        update_post_meta($id, 'deposit_check', $deposit_check);

        $other_config = array(
            'inc_tax'               => $wdp_inc_tax,
            'label'                 => $wdp_label,
            'sequentially'          => $wdp_sequentially,
            'sequentially'          => $wdp_sequentially,
            'disable_on_sale'       => $wdp_disable_on_sale,
            'disable_on_sale'       => $wdp_disable_on_sale,
            'show_in_loop'          => $wdp_show_in_loop,
            'show_in_loop'          => $wdp_show_in_loop,
            'rules'                 => base64_encode(serialize($wdp_rules)),
            'rules'                 => base64_encode(serialize($wdp_rules)),
            'apply_rule_once'       => $wdp_apply_rule_once,
            'offer_desc_status'     => $offer_desc_status,
        );

        $user_config = array(
            'user_restrictions'     => $wdp_user_restrictions,
            'user_restrictions'     => $wdp_user_restrictions,
            'usage_limit'           => $wdp_usage_limit,
            'usage_limit'           => $wdp_usage_limit,
            'per_user_limit'        => $wdp_per_user_limit,
            'per_user_limit'        => $wdp_per_user_limit,
            'user_roles'            => $wdp_user_roles,
            'user_role_restriction' => $wdp_user_role_restrictions,
            'user_role_restriction' => $wdp_user_role_restrictions,
            'users_limit'           => serialize($wdp_users_limit),
            'users_limit'           => serialize($wdp_users_limit),
            'daily_limit_switch'    => $wdp_daily_limit_swicth,
            'daily_usage_limit'     => $wdp_daily_usage_limit,
            'daily_user_limit'      => $wdp_daily_user_limit
        );

        $gift_config = array(
            'type'                  => $gift_type,
            'combinations'          => $gift,
            'number_items'          => $gifts_number,
            'gift_auto_cart'        => $gift_auto_cart,
            'disable_gift_coupon'   => $disable_gift,
            'mulitple_gifts'        => $mulitple_gifts,
            'gift_cart_min'         => $gift_cart_min,
            // 'eligible_message' => $eligible_message,
            'gift_products'         => $gift_products,
            'offer_message'         => $offer_message,
            'gift_cart_item'        => $gift_cart_item,
            'gift_variation'        => $gift_variation,
        );

        $bogo_config = array(
            'count'                 => $bogo_count,
            'sort'                  => $bogo_sort,
            'consider_individual'   => $bogo_consider_individual,
        );

        $wdp_shortcode = array(
            'title'                 => $wdp_short_title,
            'desc'                  => $wdp_short_desc,
            'picture'               => $wdp_short_picture
        );

        update_post_meta($id, 'discount_config', $other_config);
        update_post_meta($id, 'gift_config', $gift_config);
        update_post_meta($id, 'user_config', $user_config);
        update_post_meta($id, 'wdp_shortcode', $wdp_shortcode);

        if ( ! add_post_meta( $id, 'wdp_bogo_config', $bogo_config, true ) ) { 
            update_post_meta ( $id, 'wdp_bogo_config', $bogo_config );
        }

        update_post_meta($id, 'custom_product_list', $customPL);
        update_post_meta($id, 'discount_custom_pl', $wdp_custom_pl);

    }

    function get_rules($data)
    {

        if (isset($data['id'])) {
            $result             = array();
            $discount_rule      = get_post($data['id']);
            $discount_config    = get_post_meta($discount_rule->ID, 'discount_config', true) ? get_post_meta($discount_rule->ID, 'discount_config', true) : [];
            $gift_config        = get_post_meta($discount_rule->ID, 'gift_config', true) ? get_post_meta($discount_rule->ID, 'gift_config', true) : [];
            $user_config        = get_post_meta($discount_rule->ID, 'user_config', true) ? get_post_meta($discount_rule->ID, 'user_config', true) : [];
            $wdp_shortcode      = get_post_meta($discount_rule->ID, 'wdp_shortcode', true) ? get_post_meta($discount_rule->ID, 'wdp_shortcode', true) : [];
            $bogo_config        = get_post_meta($discount_rule->ID, 'wdp_bogo_config', true) ? get_post_meta($discount_rule->ID, 'wdp_bogo_config', true) : [];
            $restrictions       = array_values(array_filter(unserialize(base64_decode($discount_config['rules'])))); // remove empty values
            $restrictUsers      = [];
            $restrictProds      = [];

            // Scheduling dates
            if(get_post_meta($discount_rule->ID, 'discount_schedules', true)){
                $schedules = unserialize(get_post_meta($discount_rule->ID, 'discount_schedules', true));
            } else if(get_post_meta($discount_rule->ID, 'discount_start_date', true) && get_post_meta($discount_rule->ID, 'discount_end_date', true)){ // data before scheduling
                $schedules[0]['start_date'] = get_post_meta($discount_rule->ID, 'discount_start_date', true);
                $schedules[0]['end_date']   = get_post_meta($discount_rule->ID, 'discount_end_date', true);
            }

            // Rules and Restrictions
            if ( $restrictions ) {
                global $wpdb;
                foreach ( $restrictions as $restriction ) {
                    if ( $restriction['rules'] ) {
                        foreach ( $restriction['rules'] as $restriction_rule ) { 
                            if ( $restriction_rule['rule']['item'] === 'cart_user_selection' && ( ( !empty ( $restrictUsers ) && !in_array ( $restriction_rule['rule']['selectValue'], $restrictUsers ) ) || empty ( $restrictUsers ) ) ) {
                                $uID    = $restriction_rule['rule']['selectValue'];
                                $userNm = $wpdb->get_row ( "SELECT user_login FROM {$wpdb->prefix}users WHERE ID = $uID" );
                                $restrictUsers[] = array ( 'label' => $userNm->user_login, 'value' => $restriction_rule['rule']['selectValue'] ); 
                            } else if ( ( $restriction_rule['rule']['item'] === 'previous_order' || $restriction_rule['rule']['item'] === 'product_in_cart' ) && ( ( !empty ( $restrictProds ) && !in_array ( $restriction_rule['rule']['selectValue'], $restrictProds ) ) || empty ( $restrictProds ) ) ) {
                                $restrictProds[] = array ( 'label' => html_entity_decode ( get_the_title( $restriction_rule['rule']['selectValue'] ) ), 'value' => $restriction_rule['rule']['selectValue'] ); 
                            } 
                        }
                    }
                } 
            }

            global $wp_roles;
            $awdp_user_roles = $wp_roles->roles; 
            $awdp_stats = [];
            if ( $awdp_user_roles ) {
                foreach ( $awdp_user_roles as $key => $value ) {
                    $awdp_stats[$key] = get_post_meta( $discount_rule->ID, 'wdpRoleLimit_'.$key, true );
                }
            }

            $bogo_rules = unserialize ( get_post_meta ( $discount_rule->ID, 'discount_bogo_combinations', true ) );
            $single_rule = $bogo_array_values = [];
            $bogo_array[] = array ( 'label' => 'Any Product', 'value' => '' ); 
            if ( $bogo_rules ) {
                foreach ( $bogo_rules as $bogo_rule ) {
                    $single_rule = array_unique ( array_merge ( $single_rule,  array_merge ( $bogo_rule['from_list'] , $bogo_rule['to_list'] ) ) );
                }
            }
            // @@ Bogo Products Selected List @@
            // Bogo Combination
            if ( $single_rule ) {
                foreach ( $single_rule as $single_rul ) { 
                    // if ( array_search ( $single_rul, array_map ( function ( $bogo_array ) { return $bogo_array->value; }, $bogo_array_values ) ) === false ) {
                    if ( array_search ( $single_rul, array_column ( $bogo_array, 'value' ) ) === false ) {
                        if ( strpos( $single_rul, "list_" ) !== false ) {
                            $act_id         = str_replace( "list_", "", $single_rul ); 
                            $title          = get_the_title ( $act_id ) ? html_entity_decode ( get_the_title ( $act_id ) ) . ' - Product List' : 'Product List';
                            $bogo_array[]   = array ( 'label' => $title, 'value' => 'list_' . $act_id ); 
                        } else {
                            $bogo_array[]   = array ( 'label' => get_the_title ( $single_rul ), 'value' => $single_rul );
                        }
                    }
                } 
            }
            // Bogo same rule
            // if( get_post_meta ( $discount_rule->ID, 'discount_bogo_x_prods', true ) && array_search ( get_post_meta ( $discount_rule->ID, 'discount_bogo_x_prods', true ), array_map ( function ( $bogo_array ) { return $bogo_array->value; }, $bogo_array_values ) ) === false ) {
            if( !is_array ( get_post_meta ( $discount_rule->ID, 'discount_bogo_x_prods', true ) ) && get_post_meta ( $discount_rule->ID, 'discount_bogo_x_prods', true ) && array_search ( get_post_meta ( $discount_rule->ID, 'discount_bogo_x_prods', true ), array_column ( $bogo_array, 'value' ) ) === false ) {
                $bg_id = get_post_meta ( $discount_rule->ID, 'discount_bogo_x_prods', true );
                if ( strpos( $bg_id, "list_" ) !== false ) {
                    $act_id         = str_replace( "list_", "", $bg_id ); 
                    $title          = get_the_title ( $act_id ) ? html_entity_decode ( get_the_title ( $act_id ) ) . ' - Product List' : 'Product List';
                    $bogo_array[]   = array ( 'label' => $title, 'value' => 'list_' . $act_id ); 
                } else {
                    $bogo_array[]   = array ( 'label' => html_entity_decode ( get_the_title ( $bg_id ) ), 'value' => $bg_id );
                }
            }
            // Bogo get discount on nth item
            // if( get_post_meta ( $discount_rule->ID, 'discount_bogo_n_prods', true ) && array_search ( get_post_meta ( $discount_rule->ID, 'discount_bogo_n_prods', true ), array_map ( function ( $bogo_array ) { return $bogo_array->value; }, $bogo_array_values ) ) === false ) {
            if( !is_array ( get_post_meta ( $discount_rule->ID, 'discount_bogo_n_prods', true ) ) && get_post_meta ( $discount_rule->ID, 'discount_bogo_n_prods', true ) && array_search ( get_post_meta ( $discount_rule->ID, 'discount_bogo_n_prods', true ), array_column ( $bogo_array, 'value' ) ) === false ) {
                $bg_id = get_post_meta ( $discount_rule->ID, 'discount_bogo_n_prods', true );
                if ( strpos( $bg_id, "list_" ) !== false ) {
                    $act_id         = str_replace( "list_", "", $bg_id );
                    $title          = get_the_title ( $act_id ) ? html_entity_decode ( get_the_title ( $act_id ) ) . ' - Product List' : 'Product List'; 
                    $bogo_array[]   = array ( 'label' => $title, 'value' => 'list_' . $act_id ); 
                } else {
                    $bogo_array[]   = array ( 'label' => html_entity_decode ( get_the_title ( $bg_id ) ), 'value' => $bg_id );
                }
            }
            // Bogo discount on n items
            // if( get_post_meta ( $discount_rule->ID, 'discount_bogo_payn_prods', true ) && array_search ( get_post_meta ( $discount_rule->ID, 'discount_bogo_payn_prods', true ), array_map ( function ( $bogo_array ) { return $bogo_array->value; }, $bogo_array_values ) ) === false ) {
            if( !is_array ( get_post_meta ( $discount_rule->ID, 'discount_bogo_payn_prods', true ) ) && get_post_meta ( $discount_rule->ID, 'discount_bogo_payn_prods', true ) && array_search ( get_post_meta ( $discount_rule->ID, 'discount_bogo_payn_prods', true ), array_column ( $bogo_array, 'value' ) ) === false ) {
                $bg_id = get_post_meta ( $discount_rule->ID, 'discount_bogo_payn_prods', true );
                if ( strpos( $bg_id, "list_" ) !== false ) {
                    $act_id         = str_replace( "list_", "", $bg_id );
                    $title          = get_the_title ( $act_id ) ? html_entity_decode ( get_the_title ( $act_id ) ) . ' - Product List' : 'Product List'; 
                    $bogo_array[]   = array ( 'label' => $title, 'value' => 'list_' . $act_id ); 
                } else {
                    $bogo_array[]   = array ( 'label' => html_entity_decode ( get_the_title ( $bg_id ) ), 'value' => $bg_id );
                }
            }

            // Bogo discount on cheapest item
            if( !is_array ( get_post_meta ( $discount_rule->ID, 'discount_bogo_cheapest_prods', true ) ) && get_post_meta ( $discount_rule->ID, 'discount_bogo_cheapest_prods', true ) && array_search ( get_post_meta ( $discount_rule->ID, 'discount_bogo_cheapest_prods', true ), array_column ( $bogo_array, 'value' ) ) === false ) {
                $bg_id = get_post_meta ( $discount_rule->ID, 'discount_bogo_cheapest_prods', true ) ? get_post_meta ( $discount_rule->ID, 'discount_bogo_cheapest_prods', true ) : ''; 
                if ( strpos( $bg_id, "list_" ) !== false ) {
                    $act_id         = str_replace( "list_", "", $bg_id );
                    $title          = get_the_title ( $act_id ) ? html_entity_decode ( get_the_title ( $act_id ) ) . ' - Product List' : 'Product List'; 
                    $bogo_array[]   = array ( 'label' => $title, 'value' => 'list_' . $act_id ); 
                } else {
                    $bogo_array[]   = array ( 'label' => html_entity_decode ( get_the_title ( $bg_id ) ), 'value' => $bg_id );
                }
            } 
            // @@ End List @@

            $giftCombs = array_key_exists('combinations', $gift_config) ? unserialize($gift_config['combinations']) : '';
            $gifts_selected = $gifts_cat_selected = [];
            if ( $giftCombs ) {
                foreach ( $giftCombs as $giftComb ) {
                    if ( array_key_exists ( 'from_list', $giftComb ) && ( is_array ( $giftComb['from_list'] ) || is_array ( $giftComb['to_list'] ) ) ) 
                        $gifts_selected = array_merge ( $gifts_selected, $giftComb['from_list'], $giftComb['to_list'] );
                    // Checks Products
                    if ( array_key_exists ( 'products', $giftComb ) && is_array ( $giftComb['products'] ) ) 
                        $gifts_selected = array_merge ( $gifts_selected, $giftComb['products'] );
                    // Variable Products
                    if ( array_key_exists ( 'variableProduct', $giftComb ) && isset ( $giftComb['variableProduct'] ) ) 
                        array_push ( $gifts_selected, $giftComb['variableProduct'] );
                    // Variable Gifts
                    if ( array_key_exists ( 'variations', $giftComb ) && is_array ( $giftComb['variations'] ) ) 
                        $gifts_selected = array_merge ( $gifts_selected, $giftComb['variations'] );
                    // Check Cat
                    if ( array_key_exists ( 'category', $giftComb ) && $giftComb['category'] != '' ) 
                        array_push ( $gifts_cat_selected, $giftComb['category'] );
                }
            }
            if ( array_key_exists ( 'gift_products', $gift_config ) && is_array ( $gift_config['gift_products']) )
                $gifts_selected = array_merge ( $gifts_selected, $gift_config['gift_products'] );

            // Get Details
            $gifts_selected     = $this->get_products ($gifts_selected);
            $gifts_cat_selected = $this->get_categories ($gifts_cat_selected);

            // Variation @@ ver 3.4.0
            $variationrules = $gift_config['gift_variation'] ? '' : '';
            // if ( $variationrules && !empty ( $giftvariations ) ) {
            //     foreach ( $variationrules as $variationrule ) { 
            //         if ( $variationrule['variations'] ) { 
            //             $variatns = array_merge ( $variatns, $variationrule['variations'] ); 
            //         }
            //         if ( !in_array( $variationrule['variableProduct'], $prds) ) array_push ( $prds, $variationrule['variableProduct'] );
            //     } 
            //     $variatns = array_values ( array_unique ( $variatns ) );

            //     foreach ( $other_config['variationrules'] as $key => $val ) { 
            //         $other_config['variationrules'][$key]['variations'] =  array_values(array_filter($other_config['variationrules'][$key]['variations'])); 
            //     }
            // }

            /* 
            * Wordpress Time Zone Settings
            * @ Ver 5.0.1
            */
            $wp_tz_stngs    = get_option('awdp_time_zone_config') ? get_option('awdp_time_zone_config') : []; 
            $wp_tz          = array_key_exists ( 'wordpress_timezone', $wp_tz_stngs ) ? $wp_tz_stngs['wordpress_timezone'] : '';
            if ( $wp_tz ) {
                $timezone  = new DateTimeZone( wp_timezone_string() );
                $schd_time = wp_date("F d, Y H:i", null, $timezone );
                $srvr_time = wp_date("H:i", null, $timezone );
            } else {
                $schd_time = gmdate('F d, Y H:i');
                $srvr_time = gmdate('H:i');
            }
            /**/

            $customPL           = get_post_meta($discount_rule->ID, 'custom_product_list', true) ? get_post_meta($discount_rule->ID, 'custom_product_list', true) : [];
            $defaultTax         = [];
            $defaultProducts    = [];

            if ( !empty ( $customPL ) ) {
                global $wpdb; $taxvalues = $prodvalues = ''; $tx_cnt = $pr_cnt = 1;
                foreach ( $customPL as $singlePL ) { 
                    foreach ( $singlePL['rules'] as $val ) { 
                        if ( is_array ( $val ) && $val['rule']['value'] ) {
                            if ( $val['rule']['item'] == 'product_selection') {
                                if ( $pr_cnt != 1 ) $prodvalues .= ',';
                                $prodvalues .= implode ( ',', $val['rule']['value'] ); 
                                $pr_cnt++;
                            } else {
                                if ( $tx_cnt != 1 ) $taxvalues .= ',';
                                $taxvalues .= implode ( ',', $val['rule']['value'] ); 
                                $tx_cnt++;
                            }
                        }
                    } 
                    if( $taxvalues != '' ) { 
                        $defaultTax = $wpdb->get_results ( "SELECT DISTINCT cat.term_id as value, cat.name as label FROM {$wpdb->prefix}terms cat LEFT JOIN {$wpdb->prefix}term_taxonomy cattax ON cat.term_id = cattax.term_id WHERE cattax.term_id IN (" . $taxvalues . ")" ); 
                        foreach ( $defaultTax as $dtax ) {
                            $dtax->label = html_entity_decode ( $dtax->label );
                        }
                    } 
                    if( $prodvalues != '' ) { 
                        $defaultProducts = $wpdb->get_results ( "SELECT DISTINCT ID as value, post_title as label FROM {$wpdb->prefix}posts WHERE ID IN (" . $prodvalues . ")" ); 
                        foreach ( $defaultProducts as $dprod ) { 
                            $status = get_post_status ( $dprod->value );
                            if ( $status === 'draft' ) {
                                $dprod->label = html_entity_decode ( $dprod->label ) . ' - Draft';
                            } else {
                                $dprod->label = html_entity_decode ( $dprod->label );
                            }
                        }
                    } 
                }
            }

            // Deposit Plugin Check
            $depositInstalled   = (class_exists('AWCDP_Deposits')) ? true : false;
            $depositCheck       = get_post_meta($discount_rule->ID, 'deposit_check', true) ? get_post_meta($discount_rule->ID, 'deposit_check', true) : 0;

            $result = Array(
                'name'                      => $discount_rule->post_title,
                'id'                        => $discount_rule->ID,
                'status'                    => get_post_meta($discount_rule->ID, 'discount_status', true),
                'reg_customers'             => get_post_meta($discount_rule->ID, 'discount_reg_customers', true),
                'reg_user_roles'            => get_post_meta($discount_rule->ID, 'discount_reg_user_roles', true),
                'pricing_table'             => get_post_meta($discount_rule->ID, 'discount_pricing_table', true),
                'discount_type'             => get_post_meta($discount_rule->ID, 'discount_type', true),

                'server_date_time'          => $schd_time,
                'start_date'                => get_post_meta($discount_rule->ID, 'discount_start_date', true),
                'end_date'                  => get_post_meta($discount_rule->ID, 'discount_end_date', true),
                // 'start_time' => date('Y-m-d H:i:s', strtotime(get_post_meta($discount_rule->ID, 'discount_start_time', true))), // @@ ver 3.1.6
                // 'end_time' => date('Y-m-d H:i:s', strtotime(get_post_meta($discount_rule->ID, 'discount_end_time', true))), // @@ ver 3.1.6
                'start_time'                => get_post_meta($discount_rule->ID, 'discount_start_time', true),
                'end_time'                  => get_post_meta($discount_rule->ID, 'discount_end_time', true),
                'server_time'               => $srvr_time, // @@ ver 3.1.6
                'discount_schedule_days'    => unserialize(get_post_meta($discount_rule->ID, 'discount_schedule_days', true)),

                'product_list'              => (int)get_post_meta($discount_rule->ID, 'discount_product_list', true),
                'priority'                  => get_post_meta($discount_rule->ID, 'discount_priority', true),
                'discount'                  => get_post_meta($discount_rule->ID, 'discount_value', true),
                'table_layout'              => get_post_meta($discount_rule->ID, 'discount_table_layout', true),
                'disc_calc_type'            => get_post_meta($discount_rule->ID, 'discount_calc_type', true),

                'bogo_buy'                  => get_post_meta($discount_rule->ID, 'discount_bogo_buy', true),
                'bogo_rule_type'            => get_post_meta($discount_rule->ID, 'discount_bogo_rule_type', true),
                'bogo_x_prods'              => get_post_meta($discount_rule->ID, 'discount_bogo_x_prods', true),
                'bogo_x_repeat'             => get_post_meta($discount_rule->ID, 'discount_bogo_x_repeat', true),
                'bogo_x_all'                => get_post_meta($discount_rule->ID, 'discount_bogo_x_all', true),
                'offer_single_count'        => get_post_meta($discount_rule->ID, 'discount_offer_single_count', true),
                'bogo_y_all'                => get_post_meta($discount_rule->ID, 'discount_bogo_y_all', true),
                'bogo_y_individual'         => get_post_meta($discount_rule->ID, 'discount_bogo_y_individual', true),
                'bogo_n_prods'              => get_post_meta($discount_rule->ID, 'discount_bogo_n_prods', true),
                'bogo_n_count'              => get_post_meta($discount_rule->ID, 'discount_bogo_n_count', true),
                'bogo_payn_prods'           => get_post_meta($discount_rule->ID, 'discount_bogo_payn_prods', true),
                'bogo_n_repeat'             => get_post_meta($discount_rule->ID, 'discount_bogo_n_repeat', true),
                'bogo_n_loop'               => get_post_meta($discount_rule->ID, 'discount_bogo_n_loop', true),
                'bogo_cheapest_prods'       => get_post_meta($discount_rule->ID, 'discount_bogo_cheapest_prods', true),
                'bogo_cheapest_buy'         => get_post_meta($discount_rule->ID, 'discount_bogo_cheapest_buy', true),
                'bogo_cheapest_get'         => get_post_meta($discount_rule->ID, 'discount_bogo_cheapest_get', true),
                'bogo_cheapest_type'        => get_post_meta($discount_rule->ID, 'discount_bogo_cheapest_type', true),
                'bogo_cheapest_value'       => get_post_meta($discount_rule->ID, 'discount_bogo_cheapest_value', true),
                'bogo_get'                  => get_post_meta($discount_rule->ID, 'discount_bogo_get', true),
                'bogo_type'                 => get_post_meta($discount_rule->ID, 'discount_bogo_type', true),
                'bogo_value'                => get_post_meta($discount_rule->ID, 'discount_bogo_value', true),
                'bogo_message'              => get_post_meta($discount_rule->ID, 'discount_bogo_message', true),
                'bogo_combinations'         => $bogo_rules,
                'bogo_productlist'          => $bogo_array,
                'bogopayranges'             => unserialize(get_post_meta($discount_rule->ID, 'discount_bogopayranges', true)),

                'bogo_count'                => (!empty($bogo_config) && array_key_exists('count', $bogo_config)) ? $bogo_config['count'] : '',
                'bogo_sort'                 => (!empty($bogo_config) && array_key_exists('sort', $bogo_config)) ? $bogo_config['sort'] : '',
                'bogo_consider_individual'  => (!empty($bogo_config) && array_key_exists('consider_individual', $bogo_config)) ? $bogo_config['consider_individual'] : 0,

                'gift'                      => $giftCombs,
                'gift_type'                 => (!empty($gift_config) && array_key_exists('type', $gift_config)) ? $gift_config['type'] : '',
                'gifts_number'              => (!empty($gift_config) && array_key_exists('number_items', $gift_config)) ? $gift_config['number_items'] : '',
                'add_gift_cart'             => (!empty($gift_config) && array_key_exists('gift_auto_cart', $gift_config)) ? $gift_config['gift_auto_cart'] : '',
                'disable_gift'              => (!empty($gift_config) && array_key_exists('disable_gift_coupon', $gift_config)) ? $gift_config['disable_gift_coupon'] : '',
                'mulitple_gifts'            => (!empty($gift_config) && array_key_exists('mulitple_gifts', $gift_config)) ? $gift_config['mulitple_gifts'] : '',
                'gift_cart_min'             => (!empty($gift_config) && array_key_exists('gift_cart_min', $gift_config)) ? $gift_config['gift_cart_min'] : '',
                // 'eligible_message' => $gift_config['eligible_message'],
                'gift_products'             => (!empty($gift_config) && array_key_exists('gift_products', $gift_config)) ? $gift_config['gift_products'] : '',
                'offer_message'             => (!empty($gift_config) && array_key_exists('offer_message', $gift_config)) ? $gift_config['offer_message'] : '',
                'gift_cart_item'            => (!empty($gift_config) && array_key_exists('gift_cart_item', $gift_config)) ? $gift_config['gift_cart_item'] : '',
                'gifts_selected'            => $gifts_selected,
                'gifts_cat_selected'        => $gifts_cat_selected,
                
                'gift_variation'            => $gift_config['gift_variation'],
                
                'quantityranges'            => unserialize(get_post_meta($discount_rule->ID, 'discount_quantityranges', true)),
                'quantity_variation_check'  => get_post_meta($discount_rule->ID, 'discount_variation_check', true),
                'quantity_tiered_pricing'   => get_post_meta($discount_rule->ID, 'discount_tiered_pricing', true) ? get_post_meta($discount_rule->ID, 'discount_tiered_pricing', true) : 0,
                'quantity_cheapest_check'   => get_post_meta($discount_rule->ID, 'discount_cheapest_check', true),
                'repeat_cheapest'           => get_post_meta($discount_rule->ID, 'discount_repeat_cheapest', true),
                'cartamount'                => unserialize(get_post_meta($discount_rule->ID, 'discount_cartamount', true)),
                'quantity_type'             => get_post_meta($discount_rule->ID, 'discount_quantity_type', true),
                'schedule_weekday'          => get_post_meta($discount_rule->ID, 'discount_schedule_weekday', true),
                'schedules'                 => $schedules,

                'payment_type'              => get_post_meta($discount_rule->ID, 'discount_payment_type', true),

                'disable_on_sale'           => $discount_config['disable_on_sale'],
                'apply_rule_once'           => array_key_exists ( 'apply_rule_once', $discount_config ) ? $discount_config['apply_rule_once'] : '',
                'inc_tax'                   => $discount_config['inc_tax'],
                'label'                     => $discount_config['label'],
                'label'                     => $discount_config['label'],
                'sequentially'              => $discount_config['sequentially'],
                'show_in_loop'              => $discount_config['show_in_loop'],
                'offer_desc_status'         => array_key_exists ( 'offer_desc_status', $discount_config ) ? $discount_config['offer_desc_status'] : 1,
                'rules'                     => $restrictions, 
                'restrictUsers'             => $restrictUsers,
                'restrictProds'             => $restrictProds,

                'usage_limit'               => (!empty($user_config) && array_key_exists('usage_limit', $user_config)) ? $user_config['usage_limit'] : '',
                'user_roles'                => (!empty($user_config) && array_key_exists('user_roles', $user_config)) ? $user_config['user_roles'] : '',
                'per_user_limit'            => (!empty($user_config) && array_key_exists('per_user_limit', $user_config)) ? $user_config['per_user_limit'] : '',
                'users_limit'               => (!empty($user_config) && array_key_exists('users_limit', $user_config)) ? unserialize($user_config['users_limit']) : '',
                'user_restriction'          => (!empty($user_config) && array_key_exists('user_restrictions', $user_config)) ? $user_config['user_restrictions'] : '',
                'user_role_restriction'     => (!empty($user_config) && array_key_exists('user_role_restriction', $user_config)) ? $user_config['user_role_restriction'] : '',
                'discount_used_count'       => get_post_meta($discount_rule->ID, 'wdpUsageLimit', true),
                'discount_daily_limit'      => (!empty($user_config) && array_key_exists('daily_limit_switch', $user_config)) ?  $user_config['daily_limit_switch'] : '',
                'daily_usage_limit'         => (!empty($user_config) && array_key_exists('daily_usage_limit', $user_config)) ?  $user_config['daily_usage_limit'] : '',
                'daily_user_limit'          => (!empty($user_config) && array_key_exists('daily_user_limit', $user_config)) ?  $user_config['daily_user_limit'] : '',
                'daily_discount_used_count' => get_post_meta($discount_rule->ID, 'wdpDailyUsageLimit', true),

                'short_title'               => (!empty($wdp_shortcode) && array_key_exists('title', $wdp_shortcode)) ? $wdp_shortcode['title'] : '',
                'short_desc'                => (!empty($wdp_shortcode) && array_key_exists('desc', $wdp_shortcode)) ? $wdp_shortcode['desc'] : '',
                'short_picture'             => (!empty($wdp_shortcode) && array_key_exists('picture', $wdp_shortcode)) ? $wdp_shortcode['picture'] : '',
                'usagestats'                => $awdp_stats,

                'dynamic_value'             => get_post_meta($discount_rule->ID, 'dynamic_value', true),

                'listUrl'                   => admin_url('admin.php?page=awdp_admin_product_lists#/'),
                
                'custom_pl'                 => get_post_meta($discount_rule->ID, 'discount_custom_pl', true) ? get_post_meta($discount_rule->ID, 'discount_custom_pl', true) : 0,
                'customPL'                  => $customPL,

                'defaultTax'                => $defaultTax,
                'defaultProducts'           => $defaultProducts,

                'depositInstalled'          => $depositInstalled,
                'depositCheck'              => $depositCheck

            );
            return new WP_REST_Response($result, 200);
        }

        // $all_listings = get_posts ( array ( 'fields' => 'ids', 'posts_per_page' => -1, 'post_type' => AWDP_POST_TYPE ) );
        // $result = array();

        $result         = array();
        $all_args       = array(
            'fields'            => 'ids',
            'posts_per_page'    => -1, 
            'post_type'         => AWDP_POST_TYPE, 
            'meta_key'          => 'discount_priority', 
            'orderby'           => 'meta_value_num',
            'meta_type'         => 'NUMBER', 
            'order'             => 'ASC'
        );

        // Checking filter parameters
        if ( isset ( $data['filterRule'] ) && $data['filterRule'] != '' ) { 
            $all_args['meta_query'][] = array (
                'key'       => 'discount_type',
                'value'     => $data['filterRule'],
                'compare'   => '='
            );
        }

        $all_listings   = get_posts($all_args);

        $discount_type_name = Array(
            'percent_total_amount'  => __('Percentage of cart total amount', 'aco-woo-dynamic-pricing'),
            'percent_product_price' => __('Percentage of product price', 'aco-woo-dynamic-pricing'),
            'fixed_product_price'   => __('Fixed price of product price', 'aco-woo-dynamic-pricing'),
            'fixed_cart_amount'     => __('Fixed price of cart total amount', 'aco-woo-dynamic-pricing'),
            'cart_quantity'         => __('Quantity based discount', 'aco-woo-dynamic-pricing'),
            'bogo'                  => __('Buy X Get X', 'aco-woo-dynamic-pricing'),
            'gift'                  => __('Gift Product', 'aco-woo-dynamic-pricing'),
            'pay_method'            => __('Payment method', 'aco-woo-dynamic-pricing'),
            'ship_method'           => __('Shipping method', 'aco-woo-dynamic-pricing')
        );

        $wp_tz_stngs    = get_option('awdp_time_zone_config') ? get_option('awdp_time_zone_config') : []; 
        $wp_tz          = array_key_exists ( 'wordpress_timezone', $wp_tz_stngs ) ? $wp_tz_stngs['wordpress_timezone'] : '';
        $order_index    = 0;

        if ( $wp_tz ) {

            $timezone   = new DateTimeZone( wp_timezone_string() );
            $datenow    = wp_date("Y-m-d H:i:s", null, $timezone );

        } else {

            // Get wordpress timezone settings
            $gmt_offset         = get_option('gmt_offset');
            $timezone_string    = get_option('timezone_string');
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

        }

        foreach ($all_listings as $listID) {
            $date1      = get_post_meta($listID, 'discount_start_date', true);
            $date2      = get_post_meta($listID, 'discount_end_date', true);
            $date2Frmt  = $date2 ? date_format(date_create($date2),"Y-m-d H:i:s") : '';
            $scheduleStatus = get_post_meta($listID, 'discount_status', true) ? ( ( ( strtotime($datenow) < strtotime($date2Frmt) ) || $date2Frmt == '' ) ? 'Running' : 'Expired' ) : ''; 

            if (!isset($date2) || $date2 == ''){
                $discount_schedule = 'Starts '.date_format(date_create($date1), 'jS M Y');
            } else if (date_format(date_create($date1), 'j M Y') == date_format(date_create($date2), 'j M Y')){
                $discount_schedule = date_format(date_create($date1), 'jS M Y');
            } else if (date_format(date_create($date1), 'M Y') == date_format(date_create($date2), 'M Y')){
                $discount_schedule = date_format(date_create($date1), 'jS') . ' - '. date_format(date_create($date2), 'jS M Y');
            } else if (date_format(date_create($date1), 'Y') == date_format(date_create($date2), 'Y')){
                $discount_schedule = date_format(date_create($date1), 'jS M') . ' - '. date_format(date_create($date2), 'jS M Y');
            } else {
                $discount_schedule = date_format(date_create($date1), 'j M Y') . ' - '. date_format(date_create($date2), 'j M Y');
            }
            $result[] = Array(
                'checkbox'          => '',
                'order_index'       => $order_index,
                'discount_id'       => $listID,
                'discount_title'    => html_entity_decode ( get_the_title ( $listID ) ),
                'discount_status'   => get_post_meta($listID, 'discount_status', true),
                'discount_schedule' => $discount_schedule ? $discount_schedule : '',
                'discount_type'     => get_post_meta($listID, 'discount_type', true),
                'discount_value'    => ( get_post_meta($listID, 'discount_type', true) == 'percent_product_price' || get_post_meta($listID, 'discount_type', true) == 'fixed_product_price' || get_post_meta($listID, 'discount_type', true) == 'percent_total_amount' || get_post_meta($listID, 'discount_type', true) == 'fixed_cart_amount' ) ? get_post_meta($listID, 'discount_value', true) : '' ,
                'discount_date'     => get_the_date('d M Y', $listID),
                'discount_priority' => get_post_meta($listID, 'discount_priority', true),
                'discount_type_name' => array_key_exists ( get_post_meta($listID, 'discount_type', true), $discount_type_name ) ? $discount_type_name[get_post_meta($listID, 'discount_type', true)] : '',
                'discount_expiry'   => $scheduleStatus,
                'disableDrag'       => isset ( $data['filterRule'] ) ? true : false
            );
            $order_index++;
        }
        return new WP_REST_Response($result, 200);
    }

    function product_list($data)
    {
        if (isset($data['id'])) {
            global $wpdb;
            $result                 = array();
            $list_item              = get_post($data['id']);
            $result['list_name']    = $list_item->post_title;
            $result['list_id']      = $list_item->ID;
            $result['list_type']    = get_post_meta($list_item->ID, 'list_type', true);
            $other_config           = get_post_meta($list_item->ID, 'product_list_config', true);

            $rules = $other_config['rules']; $tax = $prds = $variatns = $attributes = []; $values = ''; $attr_values= ''; $ar_cnt = 1;
            if($rules) {
                foreach ( $rules as $rule ) { 
                    foreach ( $rule['rules'] as $val ) { 
                        if ( is_array ( $val ) && $val['rule']['value'] ) {
                            if ( $ar_cnt != 1 ) { $values .= ','; $attr_values .= ','; }
                            if ( $val['rule']['item'] == 'product_attribute' ) 
                                $attr_values .= implode ( ',', $val['rule']['value'] ); 
                            else
				                $values .= implode ( ',', $val['rule']['value'] ); 
                        }
                        $ar_cnt++;
                    } 
                    if( $values != '' ) { 
                        $tax = $wpdb->get_results ( "SELECT DISTINCT cat.term_id as value, cat.name as label FROM {$wpdb->prefix}terms cat LEFT JOIN {$wpdb->prefix}term_taxonomy cattax ON cat.term_id = cattax.term_id WHERE cattax.term_id IN (" . $values . ")" ); 
                    }
                    /* @ver 5.0.1
                    * Attribute List
                    */
                    // if( $attr_values != '' ) { 
                    //     $attributes = $wpdb->get_results ( "SELECT DISTINCT attribute_name As value, attribute_label AS label FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name IN (" . $attr_values . ")" );
                    //     array_merge ( $tax, $attributes );
                    // }
                }

                foreach ( $other_config['rules'] as $key => $val ) { 
                    $other_config['rules'][$key]['rules'] =  array_values(array_filter($other_config['rules'][$key]['rules'])); 
                }
            }

            $variationrules = array_key_exists ( 'variationrules', $other_config ) ? $other_config['variationrules'] : '';
            if($variationrules) {
                foreach ( $variationrules as $variationrule ) { 
                    if ( $variationrule['variations'] ) { 
                        $variatns = array_merge ( $variatns, $variationrule['variations'] ); 
                    }
                    if ( !in_array( $variationrule['variableProduct'], $prds) ) array_push ( $prds, $variationrule['variableProduct'] );
                } 
                $variatns = array_values ( array_unique ( $variatns ) );

                foreach ( $other_config['variationrules'] as $key => $val ) { 
                    $other_config['variationrules'][$key]['variations'] =  array_values(array_filter($other_config['variationrules'][$key]['variations'])); 
                }
            }

            $result['selectedProducts']     = ($other_config['selectedProducts']);
            $result['productAuthor']        = ($other_config['productAuthor']);
            $result['excludedProducts']     = ($other_config['excludedProducts']);
            $result['taxRelation']          = ($other_config['taxRelation']);
            $result['sku_search']           = ($other_config['sku_search']);
            $result['rules']                = ($other_config['rules']);
            $result['variationrules']       = $variationrules;
            $defaultProducts                = array_merge ( is_array($result['excludedProducts']) ? $result['excludedProducts'] : [], is_array($result['selectedProducts']) ? $result['selectedProducts'] : [], $prds );
            $result['defaultProducts']      = empty($defaultProducts) ? [] : $this->get_products($defaultProducts);  // will be using for product list suggestion dropdown

            $result['defaultTax']           = $tax;

            return new WP_REST_Response($result, 200);
        }

        $all_listings = get_posts ( array ( 'fields' => 'ids', 'numberposts' => -1, 'post_type' => AWDP_PRODUCT_LIST ) );
        $result = array();
        foreach ($all_listings as $listID) {
            $typ = '';
            if ( get_post_meta($listID, 'list_type', true) == 'products_selection' ) {
                $typ = 'Product Selection';
            } else if ( get_post_meta($listID, 'list_type', true) == 'dynamic_request' ) {
                $typ = 'Dynamic Request';
            }
            $result[] = array(
                'list_id'   => $listID,
                'list_name' => get_the_title($listID) ? html_entity_decode ( get_the_title ( $listID ) ) : 'No Label',
                'list_type' => $typ,
                'list_date' => get_the_date('d M Y', $listID)
            );
        }
        return new WP_REST_Response($result, 200);
    }

    /**
     * @search parameter - title
     */
    public function product_nd_list_search($arg)
    {
        global $wpdb;
        $params     = $arg->get_params();
        $search     = $params['search'];

        $results    = $wpdb->get_results ( "SELECT post_title as label, ID as value, post_type as type FROM {$wpdb->prefix}posts WHERE post_type in ( 'product', 'awdp_pt_products' ) AND post_status in ( 'publish', 'draft' ) AND ( post_title LIKE '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "' ) GROUP BY ID, post_title" );

        foreach ( $results as $result ) { 
            $result->value = (int)$result->value; 
            if ( $result->type == 'awdp_pt_products' ) { 
                $result->label = $result->label . ' - Product List'; 
                $result->value = 'list_'.$result->value; 
            } else {
                $status = get_post_status ( $result->value );
                if ( $status === 'draft' ) {
                    $result->label = $result->label . ' - Draft';
                }
            }
        } 

        return new WP_REST_Response($results, 200);
    }

    /**
     * @search parameter - title
     */
    public function product_list_search($arg)
    {
        global $wpdb;
        $params     = $arg->get_params();
        $search     = $params['search'];

        $results    = $wpdb->get_results ( "SELECT post_title as label, ID as value, post_type as type FROM {$wpdb->prefix}posts WHERE post_type in ( 'awdp_pt_products' ) AND post_status = 'publish' AND ( post_title LIKE '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "' ) GROUP BY ID, post_title" );

        foreach ( $results as $result ) { 
            // $result->value = (int)$result->value; 
            // $result->label = $result->label; 
            $result->value = 'list_'.$result->value; 
        } 

        // $results[] = { label: 'All Products', value: ''};

        return new WP_REST_Response($results, 200);
    }

    /**
     * @search parameter - title
     * @ver 5.0.9 - added search by sku
     */
    public function products_search($arg)
    {
        global $wpdb, $post;
        $params         = $arg->get_params();
        $search         = $params['search'];
        $skuSearch      = array_key_exists ( 'sku_search', $params ) ? $params['sku_search'] : '';

        if ( $skuSearch ) {

            $results    = $wpdb->get_results ( "SELECT post_title as label, ID as value, post_type as type FROM {$wpdb->prefix}posts pt LEFT JOIN {$wpdb->prefix}postmeta pm ON pt.ID = pm.post_id WHERE pm.meta_key='_sku' AND pm.meta_value LIKE '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%'" );

        } else {

            $results    = $wpdb->get_results ( "SELECT post_title as label, ID as value, post_type as type FROM {$wpdb->prefix}posts WHERE post_type in ( 'product' ) AND post_status in ( 'publish', 'draft' ) AND ( post_title LIKE '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "' ) GROUP BY ID, post_title" );

        }

        foreach ( $results as $result ) { 
            $result->value = (int)$result->value;
            $status = get_post_status ( $result->value );
            if ( $status === 'draft' ) {
                $result->label = $result->label . ' - Draft';
            }
        } 

        return new WP_REST_Response($results, 200);
    }

    /**
     * @search parameter - title
     * @ver 5.0.1 - attribute selection added
     */
    public function taxonomy_search($arg)
    {
        global $wpdb;
        $params     = $arg->get_params();
        $search     = $params['search'];    

        if ( $params['tax'] == 'attribute' ) {

            $results = $wpdb->get_results ( "SELECT attribute_name As value, attribute_label AS label FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE ( attribute_label LIKE '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR attribute_label LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR attribute_label LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "' )" );

            foreach ( $results as $result ) { 
                $result->value = $result->value;
            } 

        } else if ( $params['tax'] == 'subattribute' ) {

            $attr    = $params['attr'];
            $results = $wpdb->get_results ( "SELECT attribute_name As value, attribute_label AS label FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE ( attribute_label LIKE '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR attribute_label LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR attribute_label LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "' )" );

            foreach ( $results as $result ) { 
                $result->value = $result->value;
            } 

        } else {

            $tax        = ( $params['tax'] == 'tag' ) ? 'product_tag' : 'product_cat';

            $results    = $wpdb->get_results ( "SELECT cat.term_id AS value, cat.name AS label FROM {$wpdb->prefix}terms cat LEFT JOIN {$wpdb->prefix}term_taxonomy cattax ON cat.term_id = cattax.term_id WHERE cattax.taxonomy = '" . $tax . "' AND ( cat.name LIKE '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR cat.name LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR cat.name LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "' )" );

            foreach ( $results as $result ) { 
                $result->value = (int)$result->value;
            } 
        }

        return new WP_REST_Response($results, 200);
    }

    /**
     * @search parameter - title
     */
    public function user_search($arg)
    {
        global $wpdb;
        $params     = $arg->get_params();
        $search     = $params['search'];
        $tax        = ( $params['tax'] == 'tag' ) ? 'product_tag' : 'product_cat';

        $results    = $wpdb->get_results ( "SELECT ID AS value, user_login AS label FROM {$wpdb->prefix}users WHERE ( user_login LIKE '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR user_login LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR user_login LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "' )" );

        foreach ( $results as $result ) { 
            $result->value = (int)$result->value;
        } 

        return new WP_REST_Response($results, 200);
    }

    /**
     * @search parameter - product id
     */
    public function variation_list($data)
    {
        $params     = $arg->get_params();
        $id         = $params['id'];
        $results    = [];

        if ( $id ) {

            global $wpdb;

            $results = $wpdb->get_results ( "SELECT post_title as label, ID as value FROM {$wpdb->prefix}posts WHERE post_type in ( 'product_variation
            ' ) AND post_status = 'publish' AND post_parent = '" . $id . "' GROUP BY ID, post_title" );

            foreach ( $results as $result ) { 
                $result->value = (int)$result->value;
            } 

        }

        return new WP_REST_Response($results, 200);
    }

    /**
     *
     */
    public function get_products($arg)
    {

        if (is_a($arg, 'WP_REST_Request')) {

            $productslist   = get_posts(array('fields' => 'ids','numberposts' => -1, 'post_type' => 'product'));
            $products       = Array();
            foreach ($productslist as $product) {
                if(  empty($products) || array_search ( $product, array_column ( $products, 'value' ) ) === false ) {
                    $products[] = [
                        'value' => $product,
                        'label' => ( get_post_status ( $product ) === 'draft' ) ? ( html_entity_decode ( get_the_title ( $product ) ) . ' - Draft' ) : html_entity_decode ( get_the_title ( $product ) )
                    ];
                }
            }
            return new WP_REST_Response($products, 200);

        } else {

            $productslist   = $arg;
            $products       = [];
            foreach ( $productslist as $product ) { 
                if ( strpos( $product, "list_" ) !== false ) {
                    $act_id = str_replace( "list_", "", $product ); 
                    $title = get_the_title ( $act_id ) ?  html_entity_decode ( get_the_title ( $act_id ) ) . ' - Product List' : 'Product List';
                    $products[] = [
                        'value' => 'list_' . $act_id,
                        'label' => $title
                    ];
                } else if ( $product === "null" ){
                    $products[] = [
                        'value' => $product,
                        'label' => 'Any Product'
                    ];
                } else {
                    if( empty($products) || array_search ( $product, array_column ( $products, 'value' ) ) === false ) { 
                        $products[] = [
                            'value' => $product,
                            'label' => ( get_post_status ( $product ) === 'draft' ) ? ( html_entity_decode ( get_the_title ( $product ) ) . ' - Draft' ) : html_entity_decode ( get_the_title ( $product ) )
                        ];
                    }
                }
            }
            return $products;

        }
        
    }

    /**
     *
     */
    public function get_variations($arg)
    {
        global $wpdb;
        $variations = array(); 

        if ( !empty ( $arg ) ) { 

            $ids = implode ( ',', $arg );
            $variations = $wpdb->get_results ( "SELECT ID as value, post_title as label FROM {$wpdb->prefix}posts WHERE ID IN (" . $ids . ")" ); 

            foreach ( $variations as $variation ) { 
                $variation->value = (int)$variation->value;
            } 
            
        }
                
        return $variations;
        
    }

    /**
     *
     */
    public function get_categories($arg)
    {

        // if (is_a($arg, 'WP_REST_Request')) {

        //     $categorylist = get_posts(array('fields' => 'ids','numberposts' => -1, 'post_type' => 'product'));
        //     $categories = Array();
        //     foreach ($categorylist as $category) {
        //         if( empty($categories) || array_search ( $category, array_column ( $categories, 'value' ) ) === false ) {
        //             $categories[] = [
        //                 'value' => $category,
        //                 'label' => get_the_title ( $category )
        //             ];
        //         }
        //     }
        //     return $categories;

        // } else {

            $categorylist = $arg;
            $categories = [];
            foreach ( $categorylist as $category ) { 
                if ( $category === "null" ){
                    $categories[] = [
                        'value' => $category,
                        'label' => 'Any Product'
                    ];
                } else {
                    if( empty($categories) || array_search ( $category, array_column ( $categories, 'value' ) ) === false ) { 
                        global $wpdb;
                        $name = $wpdb->get_row ( "SELECT cat.name FROM {$wpdb->prefix}terms cat LEFT JOIN {$wpdb->prefix}term_taxonomy cattax ON cat.term_id = cattax.term_id WHERE cattax.taxonomy = 'product_cat' AND cat.term_id = '". $category ."'" );
                        $categories[] = [
                            'value' => $category,
                            'label' => $name->name
                        ];
                    }
                }
            }
            return $categories;

        // }
        
    }

    function product_rule($data)
    {
        $data = $data->get_params();
        $this->delete_transient();
        if ($data['id']) {
            $my_post = array(
                'ID' => $data['id'],
                'post_title' => $data['name'] ? wp_strip_all_tags($data['name']) : 'Product List',
                'post_content' => '',
            );
            wp_update_post($my_post);
            $this->update_product_rule_meta($data['id'], $data);
            return $data['id'];
        } else {
            $my_post = array(
                'post_type' => AWDP_PRODUCT_LIST,
                'post_title' => $data['name'] ? wp_strip_all_tags($data['name']) : 'Product List',
                'post_content' => '',
                'post_status' => 'publish',
            );
            $id = wp_insert_post($my_post);
            $this->update_product_rule_meta($id, $data);
            return $id;
        }
    }

    function update_product_rule_meta($id, $data)
    {

        update_post_meta($id, 'list_type', $data['list_type']);
        $other_config = array(
            'selectedProducts' => ($data['selectedProducts']),
            'productAuthor' => ($data['productAuthor']),
            'excludedProducts' => ($data['excludedProducts']),
            'taxRelation' => ($data['taxRelation']),
            'rules' => ($data['rules']),
            'variationrules' => ($data['variationrules']),
            'sku_search' => ($data['sku_search']),
        );
        update_post_meta($id, 'product_list_config', $other_config);

    }

    
    /**
     * @search parameter - title 
     * @@ ver 3.4.0
     */
    public function variable_product_search($arg)
    {
        global $wpdb;
        $params = $arg->get_params();
        $search = $params['search'];

        $results = $wpdb->get_results ( "SELECT post_title as label, ID as value, post_type as type FROM {$wpdb->prefix}posts AS posts 
                        INNER JOIN {$wpdb->prefix}term_relationships AS term_relationships ON posts.ID = term_relationships.object_id
                        INNER JOIN {$wpdb->prefix}term_taxonomy AS term_taxonomy ON term_relationships.term_taxonomy_id = term_taxonomy.term_taxonomy_id
                        INNER JOIN {$wpdb->prefix}terms AS terms ON term_taxonomy.term_id = terms.term_id 
                        WHERE term_taxonomy.taxonomy = 'product_type' AND terms.slug = 'variable' AND posts.post_type in ( 'product' ) AND posts.post_status in ( 'publish', 'draft' ) AND ( posts.post_title LIKE '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR posts.post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR posts.post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "' ) GROUP BY ID, post_title" );

        foreach ( $results as $result ) { 
            $result->value = (int)$result->value;
        } 

        return new WP_REST_Response($results, 200);
    }
    
    /**
     * @search parameter - title 
     * @@ ver 3.4.0
     */
    public function variation_search($arg)
    {
        global $wpdb;
        $params = $arg->get_params();
        $id = $params['prodID'];
        $search = $params['search'];
        $results = [];

        if ( $id ) {

            global $wpdb;

            $results = $wpdb->get_results ( "SELECT post_title as label, ID as value FROM {$wpdb->prefix}posts WHERE post_type in ( 'product_variation' ) AND post_status = 'publish' AND post_parent = '" . $id . "' AND ( post_title LIKE '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "' ) GROUP BY ID, post_title" );

            foreach ( $results as $result ) { 
                $result->value = (int)$result->value;
            } 

        }

        return new WP_REST_Response($results, 200);
    }

    /**
     * Permission Callback
     **/
    public function get_permission()
    {
        if (current_user_can('administrator') || current_user_can('manage_woocommerce')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

}
