<?php
/**
 * Initialize Elementor
 *
 * @since   1.6.2
 * @package Claue
 */
 
/// Used to prevent loading of Google Fonts by Elementor
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

//// Fix for Pin maker plugin shortcodes support
add_action( 'init', 'jas_claue_pin_preview_enable');
function jas_claue_pin_preview_enable(){

    if ( is_admin() && function_exists('WPA_PM') ) {

        $pin_lugin_path = WPA_PM()->plugin_path();

        include_once( $pin_lugin_path . '/includes/pm-template-hooks.php' );
        include_once( $pin_lugin_path . '/includes/pm-template-functions.php' );
        include_once( $pin_lugin_path . '/includes/class-pm-shortcodes.php' );
    }
}

// Add a custom category for panel widgets
add_action( 'elementor/init', 'jas_claue_el_init');
function jas_claue_el_init(){
   
   \Elementor\Plugin::$instance->elements_manager->add_category( 
   	  'claue-elements',
   	  array(
   		'title' => __( 'Claue Theme', 'claue' ),
   		'icon' => 'fa fa-plug', //default icon
   	  ),
      1 // position
   );
   
   // Include custom functions of elementor widgets
   $widgets = 'row, column, heading, service, portfolio, member, blog, product, products, google-maps, wc-categories, instagram, banner, meta-slider, promotion';
   $widgets = array_map( 'trim', explode( ',', $widgets ) );
   foreach ( $widgets as $file ) {
	//include_once JAS_CLAUE_PATH . '/core/libraries/vendors/elementor/widgets/' . $file . '.php';
   }
   include_once JAS_CLAUE_PATH . '/core/libraries/vendors/elementor/widgets/banner.php';
   include_once JAS_CLAUE_PATH . '/core/libraries/vendors/elementor/widgets/portfolio.php';
   include_once JAS_CLAUE_PATH . '/core/libraries/vendors/elementor/widgets/product.php';
   include_once JAS_CLAUE_PATH . '/core/libraries/vendors/elementor/widgets/products.php';
   include_once JAS_CLAUE_PATH . '/core/libraries/vendors/elementor/widgets/promotion.php';
   include_once JAS_CLAUE_PATH . '/core/libraries/vendors/elementor/widgets/blog.php';
   include_once JAS_CLAUE_PATH . '/core/libraries/vendors/elementor/widgets/service.php';
   include_once JAS_CLAUE_PATH . '/core/libraries/vendors/elementor/widgets/heading.php';
   include_once JAS_CLAUE_PATH . '/core/libraries/vendors/elementor/widgets/member.php';
   
   return;
}

// Register widgets
add_action( 'elementor/widgets/widgets_registered', 'jas_claue_el_register_widgets' );
function jas_claue_el_register_widgets(){

    if ( class_exists('Claue_Elementor_Portfolio_Widget') ) {

        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Claue_Elementor_Portfolio_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Claue_Elementor_Blog_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Claue_Elementor_Products_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Claue_Elementor_Product_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Claue_Elementor_Banner_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Claue_Elementor_Promotion_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Claue_Elementor_Service_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Claue_Elementor_Heading_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Claue_Elementor_Member_Widget());

    }
    
}

///////////////////////////////////////////
// Register Widget Styles
add_action( 'elementor/frontend/after_enqueue_styles', 'jas_claue_el_enqueue_styles' );
/**
 * Enqueue stylesheets
 *
 * @return  void
 */
function jas_claue_el_enqueue_styles() {
    
    wp_enqueue_style( 'font-stroke', JAS_CLAUE_URL . '/assets/vendors/font-stroke/css/font-stroke.min.css' );

}

//////////////////////////////

add_filter( 'elementor/icons_manager/native', 'jas_claue_el_icon_stroke_tab', 3, 1);
/**
 * Init icon stroke tab
 *
 * @param array $tabs
 * @return array
 */
function jas_claue_el_icon_stroke_tab( $tabs ) {

    $stroke_icons = array(
        'pe-7s-album',
        'pe-7s-arc',
        'pe-7s-back-2',
        'pe-7s-bandaid',
        'pe-7s-car',
        'pe-7s-diamond',
        'pe-7s-door-lock',
        'pe-7s-eyedropper',
        'pe-7s-female',
        'pe-7s-gym',
        'pe-7s-hammer',
        'pe-7s-headphones',
        'pe-7s-helm',
        'pe-7s-hourglass',
        'pe-7s-leaf',
        'pe-7s-magic-wand',
        'pe-7s-male',
        'pe-7s-map-2',
        'pe-7s-next-2',
        'pe-7s-paint-bucket',
        'pe-7s-pendrive',
        'pe-7s-photo',
        'pe-7s-piggy',
        'pe-7s-plugin',
        'pe-7s-refresh-2',
        'pe-7s-rocket',
        'pe-7s-settings',
        'pe-7s-shield',
        'pe-7s-smile',
        'pe-7s-usb',
        'pe-7s-vector',
        'pe-7s-wine',
        'pe-7s-cloud-upload',
        'pe-7s-cash',
        'pe-7s-close',
        'pe-7s-bluetooth',
        'pe-7s-cloud-download',
        'pe-7s-way',
        'pe-7s-close-circle',
        'pe-7s-id',
        'pe-7s-angle-up',
        'pe-7s-wristwatch',
        'pe-7s-angle-up-circle',
        'pe-7s-world',
        'pe-7s-angle-right',
        'pe-7s-volume',
        'pe-7s-angle-right-circle',
        'pe-7s-users',
        'pe-7s-angle-left',
        'pe-7s-user-female',
        'pe-7s-angle-left-circle',
        'pe-7s-up-arrow',
        'pe-7s-angle-down',
        'pe-7s-switch',
        'pe-7s-angle-down-circle',
        'pe-7s-scissors',
        'pe-7s-wallet',
        'pe-7s-safe',
        'pe-7s-volume2',
        'pe-7s-volume1',
        'pe-7s-voicemail',
        'pe-7s-video',
        'pe-7s-user',
        'pe-7s-upload',
        'pe-7s-unlock',
        'pe-7s-umbrella',
        'pe-7s-trash',
        'pe-7s-tools',
        'pe-7s-timer',
        'pe-7s-ticket',
        'pe-7s-target',
        'pe-7s-sun',
        'pe-7s-study',
        'pe-7s-stopwatch',
        'pe-7s-star',
        'pe-7s-speaker',
        'pe-7s-signal',
        'pe-7s-shuffle',
        'pe-7s-shopbag',
        'pe-7s-share',
        'pe-7s-server',
        'pe-7s-search',
        'pe-7s-film',
        'pe-7s-science',
        'pe-7s-disk',
        'pe-7s-ribbon',
        'pe-7s-repeat',
        'pe-7s-refresh',
        'pe-7s-add-user',
        'pe-7s-refresh-cloud',
        'pe-7s-paperclip',
        'pe-7s-radio',
        'pe-7s-note2',
        'pe-7s-print',
        'pe-7s-network',
        'pe-7s-prev',
        'pe-7s-mute',
        'pe-7s-power',
        'pe-7s-medal',
        'pe-7s-portfolio',
        'pe-7s-like2',
        'pe-7s-plus',
        'pe-7s-left-arrow',
        'pe-7s-play',
        'pe-7s-key',
        'pe-7s-plane',
        'pe-7s-joy',
        'pe-7s-photo-gallery',
        'pe-7s-pin',
        'pe-7s-phone',
        'pe-7s-plug',
        'pe-7s-pen',
        'pe-7s-right-arrow',
        'pe-7s-paper-plane',
        'pe-7s-delete-user',
        'pe-7s-paint',
        'pe-7s-bottom-arrow',
        'pe-7s-notebook',
        'pe-7s-note',
        'pe-7s-next',
        'pe-7s-news-paper',
        'pe-7s-musiclist',
        'pe-7s-music',
        'pe-7s-mouse',
        'pe-7s-more',
        'pe-7s-moon',
        'pe-7s-monitor',
        'pe-7s-micro',
        'pe-7s-menu',
        'pe-7s-map',
        'pe-7s-map-marker',
        'pe-7s-mail',
        'pe-7s-mail-open',
        'pe-7s-mail-open-file',
        'pe-7s-magnet',
        'pe-7s-loop',
        'pe-7s-look',
        'pe-7s-lock',
        'pe-7s-lintern',
        'pe-7s-link',
        'pe-7s-like',
        'pe-7s-light',
        'pe-7s-less',
        'pe-7s-keypad',
        'pe-7s-junk',
        'pe-7s-info',
        'pe-7s-home',
        'pe-7s-help2',
        'pe-7s-help1',
        'pe-7s-graph3',
        'pe-7s-graph2',
        'pe-7s-graph1',
        'pe-7s-graph',
        'pe-7s-global',
        'pe-7s-gleam',
        'pe-7s-glasses',
        'pe-7s-gift',
        'pe-7s-folder',
        'pe-7s-flag',
        'pe-7s-filter',
        'pe-7s-file',
        'pe-7s-expand1',
        'pe-7s-exapnd2',
        'pe-7s-edit',
        'pe-7s-drop',
        'pe-7s-drawer',
        'pe-7s-download',
        'pe-7s-display2',
        'pe-7s-display1',
        'pe-7s-diskette',
        'pe-7s-date',
        'pe-7s-cup',
        'pe-7s-culture',
        'pe-7s-crop',
        'pe-7s-credit',
        'pe-7s-copy-file',
        'pe-7s-config',
        'pe-7s-compass',
        'pe-7s-comment',
        'pe-7s-coffee',
        'pe-7s-cloud',
        'pe-7s-clock',
        'pe-7s-check',
        'pe-7s-chat',
        'pe-7s-cart',
        'pe-7s-camera',
        'pe-7s-call',
        'pe-7s-calculator',
        'pe-7s-browser',
        'pe-7s-box2',
        'pe-7s-box1',
        'pe-7s-bookmarks',
        'pe-7s-bicycle',
        'pe-7s-bell',
        'pe-7s-battery',
        'pe-7s-ball',
        'pe-7s-back',
        'pe-7s-attention',
        'pe-7s-anchor',
        'pe-7s-albums',
        'pe-7s-alarm',
        'pe-7s-airplay',
    );

    $tabs['stroke'] = [
            'name' => 'stroke',
            'label' => __( 'Stroke icons', 'claue' ),
            'url' => get_template_directory_uri().'/assets/vendors/font-stroke/css/font-stroke.min.css',
            'prefix' => '',
            'displayPrefix' => 'stroke',
            'labelIcon' => 'fas fa-edit',
            'ver' => '5.3.2',
            'icons' => $stroke_icons,
        ];

    return $tabs;
}

