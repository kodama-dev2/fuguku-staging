<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/*
  Plugin Name: WP Mega Menu Pro
  Plugin URI:  https://accesspressthemes.com/wordpress-plugins/wp-mega-menu-pro/
  Description: Horizontal & Vertical layout Mega Menu | Responsive & User friendly | Widgetized, Drag & Drop | Built-in and custom template layouts
  Version:     2.1.7
  Author:      AccessPress Themes
  Author URI:  http://accesspressthemes.com
  License:     GPLv2 or later
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Domain Path: /languages
  Text Domain: wp-mega-menu-pro
 */
defined( 'APMM_PRO_VERSION' ) or define( 'APMM_PRO_VERSION', '2.1.7' ); //plugin version
defined( 'APMM_PRO_SOV' ) or define( 'APMM_PRO_SOV', '2.5.6' ); //siteorigin latest version compatible
defined( 'APMM_PRO_TITLE' ) or define( 'APMM_PRO_TITLE', 'WP MEGA MENU PRO' ); //plugin version
defined( 'APMM_PRO_TD' ) or define( 'APMM_PRO_TD', 'wp-mega-menu-pro' ); //plugin's text domain
defined( 'APMM_PRO_CSS_PREFIX' ) or define( 'APMM_PRO_CSS_PREFIX', 'wpmega-' ); //plugin's text domain
defined( 'APMM_PRO_IMG_DIR' ) or define( 'APMM_PRO_IMG_DIR', plugin_dir_url( __FILE__ ) . 'images' ); //plugin image directory
defined( 'APMM_PRO_JS_DIR' ) or define( 'APMM_PRO_JS_DIR', plugin_dir_url( __FILE__ ) . 'js' );  //plugin js directory
defined( 'APMM_PRO_CSS_DIR' ) or define( 'APMM_PRO_CSS_DIR', plugin_dir_url( __FILE__ ) . 'css' ); // plugin css dir
defined( 'APMM_PRO_DYNAMIC_CSS_DIR' ) or define( 'APMM_PRO_DYNAMIC_CSS_DIR', plugin_dir_url( __FILE__ ) . 'wpmm-dynamic-css/' ); // plugin css dir
defined( 'APMM_PRO_PATH' ) or define( 'APMM_PRO_PATH', plugin_dir_path( __FILE__ ) );
defined( 'APMM_PRO_DYNAMIC_CSS_PATH' ) or define( 'APMM_PRO_DYNAMIC_CSS_PATH', plugin_dir_path( __FILE__ ) . 'wpmm-dynamic-css/');
defined( 'APMM_PRO_URL' ) or define( 'APMM_PRO_URL', plugin_dir_url( __FILE__ ) ); //plugin directory url
if( !defined('AP_MEGAMENU_ITEM_OPTIONS_PRO')){
  define( 'AP_MEGAMENU_ITEM_OPTIONS_PRO', 'ap-mega-menu-item-options' );
}
if( !defined('AP_MEGAMENU_MENU_LOCATION_PRO')){
  define( 'AP_MEGAMENU_MENU_LOCATION_PRO', 'ap-mega-menu-location' );
}
/* library*/
require_once APMM_PRO_PATH . 'inc/admin/libs/wpmm-libary.php';
require_once APMM_PRO_PATH . 'inc/frontend/core/wpmm_walker_class.php';
require_once APMM_PRO_PATH . 'inc/admin/wpmegamenu-widget.php';
if(!class_exists('APMM_Class_Pro')){
/**
* Plugin's main class
*/
class APMM_Class_Pro extends WPMM_Libary
{
  var $apmega_settings;

  function __construct()
  {
    $this->ap_megamenu_includes();
    $this->apmega_settings = get_option('apmega_settings');
    if (is_admin()) {
          new WPMM_Menu_Widget_Manager();
    }
    $ap_theme_settings = new AP_Theme_Settings();
  }

    /*
    * Includes Files
    */
    public function ap_megamenu_includes(){
        include(APMM_PRO_PATH . 'inc/admin/class-wpmm-activation.php');
        include(APMM_PRO_PATH . 'inc/admin/menu_settings_class.php');
        include(APMM_PRO_PATH . 'inc/admin/theme_settings_class.php');
        include(APMM_PRO_PATH . 'inc/admin/class-register-widgets.php');
        include(APMM_PRO_PATH . 'inc/admin/class-wpmm-shortcodes.php');
        include(APMM_PRO_PATH . 'inc/admin/widget-manager_class.php');
        include(APMM_PRO_PATH . 'inc/admin/class-wpmm-enqueue-scripts.php');
        include(APMM_PRO_PATH . 'inc/admin/class-dynamic-css.php');
        //include(APMM_PRO_PATH . 'inc/admin/class-aristath-dynamic-css.php');
        include(APMM_PRO_PATH . 'inc/frontend/WPMegamenuWalker_Class.php');
    }

    public static function wpmm_pro_shopping_cart_ajax_data($woo_cart_display, $cart_display_pattern,$enable_custom_image, $custom_image_url,$custom_width,$custom_height,$nameimage,$icon_type,$icon_class,$customwidth,$customheight,$attr_class,$class){

      $woo_details = array(
        'woo_cart_display' => $woo_cart_display,
        'cart_display_pattern' => $cart_display_pattern,
        'enable_custom_image' => $enable_custom_image,
        'custom_image_url' => $custom_image_url,
        'custom_width' => $custom_width,
        'custom_height' => $custom_height,
        'nameimage' => $nameimage,
        'icon_type' => $icon_type,
        'customwidth' => $customwidth,
        'customheight' => $customheight,
        'icon_class' => $icon_class,
        'attr_class' => $attr_class,
        'class' => $class
        );

      update_option( 'wpmm_woo_settings',  $woo_details );
      $getsettings = get_option('wpmm_woo_settings');
      if(WPMM_Libary::is_woocommerce_activated()){
        $link =  function_exists( 'wc_get_cart_total' ) ? WC()->cart->wc_get_cart_total() : wc_get_cart_url();
        $html_content ='<a class="wpmm-cart-contentsone '.$getsettings['class'].'" href="'.esc_url( $link ).'" title="View your shopping cart">';
        if($getsettings['enable_custom_image'] == "1"){
           //enable custom icon
          $html_content .= "<img src=".$custom_image_url." alt=".$nameimage[0]." width=".$custom_width." height=".$custom_height.">";
        }else{

         if(isset($getsettings['icon_type']) && $getsettings['icon_type'] == "custom"){
          $html_content .= '<span class="wpmm-mega-menu-icon"><img src="'.$getsettings['icon_class'].'" width="'.$getsettings['customwidth'].'" height="'.$getsettings['customheight'].'"/></span>';
        } else{
         $html_content .= '<i class="wpmm-mega-menu-icon  '.$getsettings['icon_class'].'" aria-hidden="true"></i>';
       }
     }

     switch ($getsettings['woo_cart_display']) {
      case 'icon_only':
      # Icon Only
      break;
      case 'item_only':
       # Icon & Items Only
      if(WPMM_Libary::is_woocommerce_activated()){
        $html_content .= "<span class='wpmm-cart-count'>".wp_kses_data( sprintf(  WC()->cart->get_cart_contents_count() ) )."</span>";
      }
      break;
      case 'price_only':
       # Icon & Price Only
      if(WPMM_Libary::is_woocommerce_activated()){
        $html_content .= "<span class='wpmm-cart-amount'>".wp_kses_data( WC()->cart->get_cart_subtotal())."</span>";
      }
      break;
      case 'both_pi':
      # Icon Both Price and Items
        if(WPMM_Libary::is_woocommerce_activated()){
          $itemcount  = wp_kses_data( sprintf(  WC()->cart->get_cart_contents_count() ) );
          $amt = wp_kses_data( WC()->cart->get_cart_subtotal());
          if($getsettings['cart_display_pattern'] != ''){
            $cart_display_pattern = $getsettings['cart_display_pattern'];
            $orginalstr = array("#item_count", "#price");
            $replacestr   = array($itemcount,$amt );
            $total_cart_display = str_replace($orginalstr, $replacestr,$cart_display_pattern );
            $html_content .= "<span class='wpmm-cart-count'>".$total_cart_display."</span>";
          }
        }
        break;
        default:
        # code...

        break;
      }
      $html_content .= "</a>";
    }else{
      $html_content = "";
    }
    return  $html_content;

  }

 }
$wpmmegamenu_pro_object = new APMM_Class_Pro();
$wpmega_menu_pro_library = new WPMM_Libary();
}
