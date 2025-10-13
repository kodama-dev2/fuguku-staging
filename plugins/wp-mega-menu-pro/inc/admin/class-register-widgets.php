<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!!' );
if ( !class_exists( 'WPMM_Register_Widgets' ) ) {
    class WPMM_Register_Widgets extends WPMM_Libary{
        /**
         * Executes all the tasks on Plugin activation
         * 
         * @since 1.0.0
         */
        function __construct() {
             add_action( 'widgets_init',  array( $this,'wpmm_mega_register_widget' ));
             add_filter('widget_text', 'do_shortcode');
             add_filter( 'black_studio_tinymce_enable_pages' , array($this, 'wpmegamenu_blackstudio_tinymce' ) );
        }
         
      function wpmm_mega_register_widget(){
       register_widget( 'WP_Mega_Menu_Widget_PRO' );
       register_widget( 'WP_Mega_Menu_PRO_Contact_Info' );
       register_widget('WP_Mega_Menu_Posts_Heading_Widget');
       register_widget('WP_Mega_Menu_PRO_PostsTimeline');
       register_widget('WP_Mega_Menu_PRO_PostsFormat');
       register_widget('WP_Mega_Menu_PRO_TextImage');
       register_widget('WP_Mega_Menu_PRO_FeatureBox');
       register_widget('WPMMPro_Simple_Recent_Posts');
       register_widget('WP_Mega_Menu_PRO_Posts_Slider_Widget');
       register_widget('WP_Mega_Menu_PRO_LinkImage');
       register_widget('WP_Mega_Menu_PRO_GalleryImageWidget');
       register_widget('WP_Mega_Menu_PRO_HtmlText');
         if(WPMM_Libary::is_woocommerce_activated()){
           register_widget( 'WPMMPro_prodlist_widget_area' );
           register_widget( 'WPMMPro_Recent_Products_widget_area' );
           register_widget( 'WPMMPro_Products_With_Cart_widget_area' );
         }
       } 

     /**
     * Black Studio TinyMCE Compatibility.
     * Load TinyMCE assets on nav-menus.php page.
     */
    public function wpmegamenu_blackstudio_tinymce( $pages ) {
      $pages[] = 'nav-menus.php';
      return $pages;
    }



    }
    new WPMM_Register_Widgets();
}