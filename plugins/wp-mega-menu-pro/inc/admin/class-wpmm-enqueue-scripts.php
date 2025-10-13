<?php defined('ABSPATH') or die('No script kiddies please!!');
if ( !class_exists('WPMM_Enqueue_Scripts') ) {
    class WPMM_Enqueue_Scripts extends WPMM_Libary{

        /**
         * Enqueue all the necessary JS and CSS
         *
         * since @1.0.0
         */
        function __construct() {
         add_action('wp_enqueue_scripts',array($this,'wpmm_megamenu_frontend_scripts') , 100);
         add_action('wp_head',array($this,'prefix_add_header_scripts'));
         add_action('wp_footer',array($this,'prefix_add_footer_scripts'));
         add_action( 'admin_enqueue_scripts', array( $this, 'wp_admin_enqueue_scripts'),11 ); 
        } 

        public function prefix_add_header_scripts(){
            $options = get_option( 'apmega_settings' );  
            $enable_mobile = (isset($options['enable_mobile']) && $options['enable_mobile'] == 1)?1:0; 
            $fonts_final_arr = array();
            $menu_settings = get_option( 'wpmegabox_settings' );
            $content = '';
            $wpmm_custom_theme = array();

           if(isset($menu_settings) && !empty($menu_settings)):
               foreach ($menu_settings as $lkey => $val) {
                $enabled_mm =  (isset($val['enabled']) && $val['enabled'] == 1)?1:0;
                $theme_type =  (isset($val['theme_type']) && $val['theme_type'] == 'available_skins')?'available_skins':'custom_themes';
                $themeid =  (isset($val['theme']) && $val['theme'] != '')?intval($val['theme']):1;
                $orientation =  (isset($val['orientation']) && $val['orientation'] == 'horizontal')?'horizontal':'vertical';
                 if($enabled_mm == 1 && $theme_type == 'custom_themes'){
                   $menuthemes = (array) AP_Theme_Settings::get_custom_theme_rowdata($themeid);
                   $theme_slug = '.wpmega-'.$menuthemes['slug'];  
                   if(isset($menuthemes['theme_settings']) && !empty($menuthemes['theme_settings'])){
                        $wpmm_custom_theme = unserialize($menuthemes['theme_settings']);
                    }else{
                        $wpmm_custom_theme = array();
                    }
                    $font_family = (isset($wpmm_custom_theme['menu_bar']['font_family']))?$wpmm_custom_theme['menu_bar']['font_family']:'Open Sans';
                    $menu_label_font_family = (isset($wpmm_custom_theme['top_menu']['menu_label_font_family']))?$wpmm_custom_theme['top_menu']['menu_label_font_family']:'';
                    $font_family3 = (isset($wpmm_custom_theme['widgets']['font_family']))?$wpmm_custom_theme['widgets']['font_family']:'bold';
                    $content_font_family3 = (isset($wpmm_custom_theme['widgets']['content_font_family']))?$wpmm_custom_theme['widgets']['content_font_family']:'Open Sans';
                    $font_family4 = (isset($wpmm_custom_theme['top_section']['font_family']))?$wpmm_custom_theme['top_section']['font_family']:'Open Sans';
                    $font_family5 = (isset($wpmm_custom_theme['bottom_section']['font_family']))?$wpmm_custom_theme['bottom_section']['font_family']:'Open Sans';
                    $font_family6 = (isset($wpmm_custom_theme['flyout']['font_family']))?$wpmm_custom_theme['flyout']['font_family']:'Open Sans';
                    $font_popup_family = (isset($wpmm_custom_theme['search_bar']['font_popup_family']))?$wpmm_custom_theme['search_bar']['font_popup_family']:'';
                    $fonts  =  $font_family;
                    $fonts5 = $menu_label_font_family;
                    $fonts1 = $font_family3; 
                    $fonts2 = $font_family4; 
                    $fonts3 = $font_family5; 
                    $fonts4 = $font_family6; 

                    $fonts_final = str_replace(' ', '+', $fonts);         
                    $fonts_final1 = str_replace(' ', '+', $fonts1);         
                    $fonts_final2 = str_replace(' ', '+', $fonts2);         
                    $fonts_final3 = str_replace(' ', '+', $fonts3);         
                    $fonts_final4 = str_replace(' ', '+', $fonts4); 
                    $fonts_final5 = str_replace(' ', '+', $fonts5); 
                    $rkey = WPMM_Libary::generateRandomIndex();
                    $fonts_final_arr =
                     array(
                      '0' => $fonts_final,
                      '1' => $fonts_final1,
                      '2' => $fonts_final2,
                      '3' => $fonts_final3,
                      '4' => $fonts_final4,
                      '5' => $fonts_final5
                      );
                     $result_fonts = array_unique($fonts_final_arr);
                     if(!empty($result_fonts)){
                      foreach ($result_fonts as $key => $value) { 
                        if($value !='Assistant' || $value !='' || $value !='default'){?>
                      <link rel='stylesheet' href='//fonts.googleapis.com/css?family=<?php echo $value;?>' type='text/css' media='all' />
                      <?php  }else{ ?>
                      <link href="https://fonts.googleapis.com/css?family=Assistant:200,300,400,600,700,800&amp;subset=hebrew" rel="stylesheet"/>
                      <?php } 
                      }
                     }
                  }
               }
            endif;
        }
        
       /*
        * Enqueue Back-end Scripts
       */
        function wp_admin_enqueue_scripts($hooks){
          if ( 'nav-menus.php' == $hooks ) {
            do_action("wp_megamenu_nav_menus_scripts", $hooks );
          }
        }

        /*
        * Load script to footer
        */
        public function prefix_add_footer_scripts(){
          $options = get_option( 'apmega_settings' );  
          $enable_custom_js = (isset($options['enable_custom_js']) && $options['enable_custom_js'] == 1)?'1':'0';
          $custom_js = (isset($options['custom_js']))?$options['custom_js']:'';    
          if($enable_custom_js == 1){
            if($custom_js != ''){ ?>
            <script type="text/javascript">
            <?php echo $custom_js; ?>
            </script>
            <?php  
            }
          }
        }

      function wpmm_megamenu_frontend_scripts(){
       $options = get_option( 'apmega_settings' ); // Variables for JS scripts
       $enable_mobile = (isset($options['enable_mobile']) && $options['enable_mobile'] == 1)?'1':'0';
       $enable_rtl = (isset($options['enable_rtl']) && $options['enable_rtl'] == 1)?'1':'0';
      if( $enable_mobile == 1){
         wp_enqueue_style( 'wpmm-responsive-stylesheet', APMM_PRO_CSS_DIR . '/responsive.css',APMM_PRO_VERSION);
      } 
      if( is_rtl() && $enable_rtl == 1){
       wp_enqueue_style( 'wpmm-style-rtl', APMM_PRO_CSS_DIR . '/style-rtl.css',APMM_PRO_VERSION );
      }
     wp_enqueue_style( 'wpmm-animate-css', APMM_PRO_CSS_DIR . '/animate.css', false, APMM_PRO_VERSION );
     wp_enqueue_style( 'wpmm-colorbox', APMM_PRO_CSS_DIR . '/colorbox.css', false, APMM_PRO_VERSION );
     wp_enqueue_style( 'wpmm-frontwalker-stylesheet', APMM_PRO_CSS_DIR . '/frontend_walker.css', true, APMM_PRO_VERSION );
     wp_enqueue_style('wpmm-google-fonts-style', "//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700");

     wp_enqueue_style('wpmm-bxslider-style',APMM_PRO_CSS_DIR.'/jquery.bxslider.css',false,APMM_PRO_VERSION);
     wp_enqueue_script('wpmm-jquery-bxslider-min',APMM_PRO_JS_DIR.'/jquery.bxslider.min.js',array('jquery'),APMM_PRO_VERSION);

     wp_enqueue_script( 'wp_megamenu_actual_scripts', APMM_PRO_JS_DIR . '/jquery.actual.js',array('jquery') , APMM_PRO_VERSION );
     wp_enqueue_script( 'wp_megamenu_colorbox', APMM_PRO_JS_DIR . '/jquery.colorbox.js',array('jquery') , APMM_PRO_VERSION );
     wp_enqueue_script( 'wp_megamenu-frontend_scripts', APMM_PRO_JS_DIR . '/frontend.js',array('jquery') , APMM_PRO_VERSION, true );
     wp_enqueue_script( 'wp_megamenu_validate_scripts', APMM_PRO_JS_DIR . '/jquery.validate.js',array('jquery') , APMM_PRO_VERSION );
     
     wp_register_script('wpmm_ajax-auth-script', APMM_PRO_JS_DIR . '/ajax-auth-script.js', array('jquery') , APMM_PRO_VERSION); 
     wp_enqueue_script('wpmm_ajax-auth-script');

    if(WPMM_Libary::is_woocommerce_activated()){
          $wooenabled = "true";
        }else{
         $wooenabled = "false";
       }
       $mlabel_animation_type = (isset($options['mlabel_animation_type']))?$options['mlabel_animation_type']:'none';
       $animation_delay = (isset($options['animation_delay']))?$options['animation_delay']:'2';
       $animation_duration = (isset($options['animation_duration']))?$options['animation_duration']:'3';
       $animation_iteration_count = (isset($options['animation_iteration_count']))?$options['animation_iteration_count']:'1';
       wp_localize_script('wp_megamenu-frontend_scripts', 'wp_megamenu_params', array(
        'wpmm_mobile_toggle_option'      => esc_attr($options['mobile_toggle_option']),
        'wpmm_enable_rtl'                => $enable_rtl,
              'wpmm_event_behavior'            => esc_attr($options['advanced_click']), //click_submenu or follow_link
              'wpmm_ajaxurl'                   => admin_url('admin-ajax.php'),
              'wpmm_ajax_nonce'                => wp_create_nonce('wpm-ajax-nonce'),
              'check_woocommerce_enabled'      => $wooenabled,
              'wpmm_mlabel_animation_type'     => esc_attr($mlabel_animation_type),
              'wpmm_animation_delay'           => esc_attr($animation_delay),
              'wpmm_animation_duration'        => esc_attr($animation_duration),
              'wpmm_animation_iteration_count'      => esc_attr($animation_iteration_count),
              'enable_mobile'                     => $enable_mobile,
              'wpmm_sticky_opacity'            => (isset($options['sticky_opacity'])?esc_attr($options['sticky_opacity']):''), 
              'wpmm_sticky_offset'             => (isset($options['sticky_offset'])?esc_attr($options['sticky_offset']):''),
              'wpmm_sticky_zindex'             => (isset($options['sticky_zindex'])?esc_attr($options['sticky_zindex']):'')
              ));
          wp_localize_script( 'wpmm_ajax-auth-script', 'wp_megamenu_ajax_auth_object', array( 
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'redirecturl' => home_url(),
            'loadingmessage' => __('Sending user info, please wait...')
            ));

          wp_enqueue_style('wpmegamenu-linecon-css', APMM_PRO_CSS_DIR.'/wpmm-icons/linecon.css', array(), APMM_PRO_VERSION);
          wp_enqueue_style( 'dashicons' );
          wp_enqueue_style( 'wpmegamenu-genericons', APMM_PRO_CSS_DIR . '/wpmm-icons/genericons.css', APMM_PRO_VERSION );
          wp_enqueue_style( 'wpmegamenu-icomoon', APMM_PRO_CSS_DIR . '/wpmm-icons/icomoon.css', APMM_PRO_VERSION );
          wp_enqueue_script( 'wpmegamenu-linearicons', 'https://cdn.linearicons.com/free/1.0.0/svgembedder.min.js' );
          wp_enqueue_style( 'wpmegamenu-icon-picker-fontawesome', APMM_PRO_CSS_DIR . '/wpmm-icons/fontawesome.css', APMM_PRO_VERSION );
          wp_enqueue_style( 'wpmegamenu-icon-picker-fa-solid', APMM_PRO_CSS_DIR . '/wpmm-icons/fa-solid.css', APMM_PRO_VERSION );
          wp_enqueue_style( 'wpmegamenu-icon-picker-fa-regular', APMM_PRO_CSS_DIR . '/wpmm-icons/fa-regular.css', APMM_PRO_VERSION );
          wp_enqueue_style( 'wpmegamenu-icon-picker-fa-brands', APMM_PRO_CSS_DIR . '/wpmm-icons/fa-brands.css', APMM_PRO_VERSION );
          wp_enqueue_style( 'wpmegamenu-font-awesome-style', APMM_PRO_CSS_DIR . '/wpmm-icons/font-awesome.min.css', array(), APMM_PRO_VERSION );
          wp_enqueue_style( 'wpmegamenu-linearicons', 'https://cdn.linearicons.com/free/1.0.0/icon-font.min.css');
          wp_enqueue_style( 'wpmegamenu-themify', APMM_PRO_CSS_DIR . '/wpmm-icons/themify-icons.css', APMM_PRO_VERSION );
/*         wp_dequeue_style('wpmegamenu-icon-picker-fontawesome');
          wp_dequeue_style('wpmegamenu-icon-picker-fa-solid');
          wp_dequeue_style('wpmegamenu-icon-picker-fa-regular');
          wp_dequeue_style('wpmegamenu-icon-picker-fa-regular');
          wp_dequeue_style('wpmegamenu-icon-picker-fa-brands');
          wp_dequeue_style('wpmegamenu-font-awesome-style');*/
        }
    }
    new WPMM_Enqueue_Scripts();
}