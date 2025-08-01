<?php
$mlabel_animation_type = (isset($options['mlabel_animation_type']))?$options['mlabel_animation_type']:'none';
$animation_delay = (isset($options['animation_delay']))?$options['animation_delay'].'s':'2s';
$animation_duration = (isset($options['animation_duration']))?$options['animation_duration'].'s':'3s';
$animation_iteration_count = (isset($options['animation_iteration_count']))?$options['animation_iteration_count']:'1';
$enable_custom_css = (isset($options['enable_custom_css']) && $options['enable_custom_css'] == 1)?'1':'0';
$icon_width = (isset($options['icon_width']) && $options['icon_width'] != '')?$options['icon_width']:'';
$custom_css = (isset($options['custom_css']))?$options['custom_css']:'';  
$content .= '/*Main Settings Animation and Custom CSS Start*/';
if($mlabel_animation_type != 'none' && $mlabel_animation_type != ''){
	 $content .= 'span.wpmm-mega-menu-label.wpmm_depth_first,
	 span.wpmm-mega-menu-label.wpmm_depth_last{
	 	animation-duration:'.esc_attr($animation_duration).';
	 animation-delay:'.esc_attr($animation_delay).';
	 animation-iteration-count:'.esc_attr($animation_iteration_count).';
	 -webkit-animation-duration:'.esc_attr($animation_duration).';
	 -webkit-animation-delay:'.esc_attr($animation_delay).';
	 -webkit-animation-iteration-count:'.esc_attr($animation_iteration_count).';
	}';	
}
if($icon_width != ''){
	 $content .= '.wp-megamenu-main-wrapper .wpmm-mega-menu-icon{
	 	font-size:'.intval($icon_width).'px;
	 }';	
}
if($enable_custom_css == 1 && $custom_css != ''){
  $content .= $custom_css;
}
$arr_results = array();
/* CSS Style for Custom Styling Menu Items Per Menu Location */
$menus = get_registered_nav_menus();
$settings = get_option( 'wpmegabox_settings' ); 
$locations = get_nav_menu_locations();

if (isset($menus) && !empty($menus)) {
foreach ($menus as $key => $value) {
  if ( isset ( $settings[ $key ]['enabled'] ) && $settings[ $key ]['enabled'] == 1 ) {
    $orientation = $settings[ $key ]['orientation'];
       /*
       * Check if wp mega menu pro is enabled or not for specific menu location
      */
       $orientation = (isset($settings[ $key ]['orientation']) && $settings[ $key ]['orientation'] != '')?$settings[ $key ]['orientation']:'horizontal';
       if(!empty( $locations[ $key ])){
        $menu = wp_get_nav_menu_object( $locations[ $key ] );
        $menuitems = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) ); // get all menu items of specific menu location
         if(isset($menuitems) && !empty($menuitems)):
           foreach ($menuitems as $k => $v) {
             $menuID = $v->ID;
             $arr = array(
              'menuid' => $menuID,
              'orientation' => $orientation,
              'location' => $key
              );
             array_push($arr_results, $arr);
           }
           endif;
         }

   }
 }
}
if(isset($arr_results) && !empty($arr_results)){
	foreach ($arr_results as $key => $value) {
	 $get_custom_styling_details = get_post_meta($value['menuid'], '_wpmegamenu' );
	 $check = (isset($get_custom_styling_details[0]['custom_styling']['enable_custom_styling']) && $get_custom_styling_details[0]['custom_styling']['enable_custom_styling'] == true)?true:false;
   //header style php file included here
      $menuid = $value['menuid'];
	  $orientation = (isset($value['orientation']) && $value['orientation'] != '')?esc_attr($value['orientation']):'horizontal';
	  if($orientation == "horizontal"){
	      $oclass = ".wpmm-orientation-horizontal";
	  }else{
	      $oclass = ".wpmm-orientation-vertical";
	  }

 $main_id_wrap = ".wp-megamenu-main-wrapper".$oclass;
 $item_html_id = " ul.wpmm-mega-wrapper li#wp_nav_menu-item-".$menuid;

 $containerwrap = $main_id_wrap.$item_html_id;

 if($check){
	  $enable_menu_bg_color = (isset($get_custom_styling_details[0]['custom_styling']['enable_menu_bg_color']) && $get_custom_styling_details[0]['custom_styling']['enable_menu_bg_color'] != '')?$get_custom_styling_details[0]['custom_styling']['enable_menu_bg_color']:'false';
	  $menu_background_color = (isset($get_custom_styling_details[0]['custom_styling']['menu_background_color']) && $get_custom_styling_details[0]['custom_styling']['menu_background_color'] != '')?$get_custom_styling_details[0]['custom_styling']['menu_background_color']:'';

	  $enable_menu_bg_hover_color = (isset($get_custom_styling_details[0]['custom_styling']['enable_menu_bg_hover_color']) && $get_custom_styling_details[0]['custom_styling']['enable_menu_bg_hover_color'] != '')?$get_custom_styling_details[0]['custom_styling']['enable_menu_bg_hover_color']:'false';
	  $menu_bg_hover_color = (isset($get_custom_styling_details[0]['custom_styling']['menu_bg_hover_color']) && $get_custom_styling_details[0]['custom_styling']['menu_bg_hover_color'] != '')?$get_custom_styling_details[0]['custom_styling']['menu_bg_hover_color']:'';
	  
	  $enable_menu_font_color = (isset($get_custom_styling_details[0]['custom_styling']['enable_menu_font_color']) && $get_custom_styling_details[0]['custom_styling']['enable_menu_font_color'] != '')?$get_custom_styling_details[0]['custom_styling']['enable_menu_font_color']:0;
	  $menu_font_color = (isset($get_custom_styling_details[0]['custom_styling']['menu_font_color']) && $get_custom_styling_details[0]['custom_styling']['menu_font_color'] != '')?$get_custom_styling_details[0]['custom_styling']['menu_font_color']:'';
	  $enable_menu_font_hover_color = (isset($get_custom_styling_details[0]['custom_styling']['enable_menu_font_hover_color']) && $get_custom_styling_details[0]['custom_styling']['enable_menu_font_hover_color'] != '')?$get_custom_styling_details[0]['custom_styling']['enable_menu_font_hover_color']:0;
	  $menu_font_hover_color = (isset($get_custom_styling_details[0]['custom_styling']['menu_font_hover_color']) && $get_custom_styling_details[0]['custom_styling']['menu_font_hover_color'] != '')?$get_custom_styling_details[0]['custom_styling']['menu_font_hover_color']:'';
	  $enable_submenu_megamenu_width = (isset($get_custom_styling_details[0]['custom_styling']['enable_submenu_megamenu_width']) && $get_custom_styling_details[0]['custom_styling']['enable_submenu_megamenu_width'] != '')?$get_custom_styling_details[0]['custom_styling']['enable_submenu_megamenu_width']:'false';

	  $submenu_megamenu_width = (isset($get_custom_styling_details[0]['custom_styling']['submenu_megamenu_width']) && $get_custom_styling_details[0]['custom_styling']['submenu_megamenu_width'] != '')?esc_attr($get_custom_styling_details[0]['custom_styling']['submenu_megamenu_width'].'px'):'';
	  $enable_submenu_bg_color = (isset($get_custom_styling_details[0]['custom_styling']['enable_submenu_bg_color']) && $get_custom_styling_details[0]['custom_styling']['enable_submenu_bg_color'] != '')?$get_custom_styling_details[0]['custom_styling']['enable_submenu_bg_color']:'false';

	  $submenu_bg_color = (isset($get_custom_styling_details[0]['custom_styling']['submenu_bg_color']) && $get_custom_styling_details[0]['custom_styling']['submenu_bg_color'] != '')?$get_custom_styling_details[0]['custom_styling']['submenu_bg_color']:'';

	  $enable_sub_cfont_color = (isset($get_custom_styling_details[0]['custom_styling']['enable_sub_cfont_color']) && $get_custom_styling_details[0]['custom_styling']['enable_sub_cfont_color'] != '')?$get_custom_styling_details[0]['custom_styling']['enable_sub_cfont_color']:0;
	  $submenu_cfont_color = (isset($get_custom_styling_details[0]['custom_styling']['submenu_cfont_color']) && $get_custom_styling_details[0]['custom_styling']['submenu_cfont_color'] != '')?$get_custom_styling_details[0]['custom_styling']['submenu_cfont_color']:'';
	  $enable_sub_heading_font_color = (isset($get_custom_styling_details[0]['custom_styling']['enable_sub_heading_font_color']) && $get_custom_styling_details[0]['custom_styling']['enable_sub_heading_font_color'] != '')?$get_custom_styling_details[0]['custom_styling']['enable_sub_heading_font_color']:'false';
	  $sub_heading_font_color = (isset($get_custom_styling_details[0]['custom_styling']['sub_heading_font_color']) && $get_custom_styling_details[0]['custom_styling']['sub_heading_font_color'] != '')?$get_custom_styling_details[0]['custom_styling']['sub_heading_font_color']:'';
	  $enable_menu_icon_color = (isset($get_custom_styling_details[0]['custom_styling']['enable_menu_icon_color']) && $get_custom_styling_details[0]['custom_styling']['enable_menu_icon_color'] != '')?$get_custom_styling_details[0]['custom_styling']['enable_menu_icon_color']:'false';
	  $menu_icon_color = (isset($get_custom_styling_details[0]['custom_styling']['menu_icon_color']) && $get_custom_styling_details[0]['custom_styling']['menu_icon_color'] != '')?$get_custom_styling_details[0]['custom_styling']['menu_icon_color']:'';
	  $enable_menu_icon_hover_color = (isset($get_custom_styling_details[0]['custom_styling']['enable_menu_icon_hover_color']) && $get_custom_styling_details[0]['custom_styling']['enable_menu_icon_hover_color'] != '')?$get_custom_styling_details[0]['custom_styling']['enable_menu_icon_hover_color']:'false';
	  $menu_icon_hover_color = (isset($get_custom_styling_details[0]['custom_styling']['menu_icon_hover_color']) && $get_custom_styling_details[0]['custom_styling']['menu_icon_hover_color'] != '')?$get_custom_styling_details[0]['custom_styling']['menu_icon_hover_color']:'';

  if($enable_menu_bg_color == 'true' && $menu_background_color != ''){ 
    $content .= $containerwrap.'{background-color:'.esc_attr($menu_background_color).';}';	
  }
  /* on menu hover icon menu bg color change */
  if($enable_menu_bg_hover_color  == 'true' && $menu_bg_hover_color != ''){ 
    $content .= $containerwrap.':hover{background:'.esc_attr($menu_bg_hover_color).';}';	
  }
  /* on menu hover icon color change */
  if($enable_menu_icon_color == 'true' && $menu_icon_color != ''){ 
    $content .= $containerwrap.' .wpmm-mega-menu-icon{color:'.esc_attr($menu_icon_color).';}';	
  }

  if($enable_menu_icon_hover_color == 'true' && $menu_icon_hover_color != ''){ 
    $content .= $containerwrap.':hover .wpmm-mega-menu-icon{color:'.esc_attr($menu_icon_hover_color).';}';	
  }
  if($enable_menu_font_color == 'true' && $menu_font_color != ''){ 
    $content .= $containerwrap.' > a,'.$containerwrap.' > a > i,'.$containerwrap.' > a > img{color:'.esc_attr($menu_font_color).';}';	
  }

  if($enable_menu_font_hover_color == 'true' && $menu_font_hover_color != ''){ 
    $content .= $containerwrap.':hover > a,'.$containerwrap.':hover > a > i,'.$containerwrap.':hover > a > img{color:'.esc_attr($menu_font_hover_color).';}';	
  }

  if($enable_submenu_megamenu_width == 'true' && $submenu_megamenu_width != ''){ 
    $content .= $containerwrap.' > .wpmm-sub-menu-wrap{width:'.esc_attr($submenu_megamenu_width).';}';	
  }

  if($enable_submenu_bg_color == 'true' && $submenu_bg_color != ''){ 
    $content .= $containerwrap.' > .wpmm-sub-menu-wrap,'.$containerwrap.' > ul{background-color:'.esc_attr($submenu_bg_color).';}';	
  }

 if($enable_sub_heading_font_color == 'true' && $sub_heading_font_color != ''){ 
    $content .= $containerwrap.' .wpmm-sub-menu-wrap ul li h4.wpmm-mega-block-title::before,'.$containerwrap.' .wpmm-sub-menu-wrap ul li.wp-mega-menu-header > a.wp-mega-menu-link::before,'.$containerwrap.' .wpmm-sub-menu-wrap ul li h4.wpmm-mega-block-title,'.$containerwrap.' .wpmm-sub-menu-wrapper ul li.menu-item-depth-1 > a > span.wpmm-mega-menu-href-title{color:'.esc_attr($sub_heading_font_color).';}';	
  }

 if($enable_sub_cfont_color == 'true' && $submenu_cfont_color != ''){ 
    $content .= $containerwrap.' .wp-mega-sub-menu li .wpmm-sub-menu-wrapper.wpmm_menu_1 li::before,'.$containerwrap.' .wp-mega-sub-menu .widget_pages li::before,'.$containerwrap.' .wp-mega-sub-menu .widget_categories li::before,'.$containerwrap.' .wp-mega-sub-menu .widget_archive li::before,'.$containerwrap.' .wp-mega-sub-menu .widget_meta li::before,'.$containerwrap.' .wp-mega-sub-menu .widget_recent_comments li::before,'.$containerwrap.'  .wp-mega-sub-menu .widget_recent_entries li::before,'.$containerwrap.'  .wp-mega-sub-menu .widget_product_categories ul.product-categories li a::before,'.$containerwrap.' .wp-mega-sub-menu .widget_categories li::before,'.$containerwrap.' .wp-mega-sub-menu .widget_archive li::before{color:'.esc_attr($submenu_cfont_color).';}';	
   $content .= $containerwrap.' .wpmm-sub-menu-wrap ul li a:focus,'.$containerwrap.' .wpmm-sub-menu-wrap ul li a,'.$containerwrap.' .wpmm-sub-menu-wrap ul li div,'.$containerwrap.' .wpmm-sub-menu-wrap ul li span.wpmm-mega-menu-href-title,'.$containerwrap.' .wpmm-sub-menu-wrapper ul li span.wpmm-mega-menu-href-title{color:'.esc_attr($submenu_cfont_color).';}';

  }
} //end check

//active view more button custom

$activate_view_more_btn = (isset($get_custom_styling_details[0]['general_settings']['activate_view_more_btn']) && $get_custom_styling_details[0]['general_settings']['activate_view_more_btn'] == 'true')?1:0;
$vbtn_bgcolor = (isset($get_custom_styling_details[0]['general_settings']['vbtn_bgcolor']) && $get_custom_styling_details[0]['general_settings']['vbtn_bgcolor'] != '')?esc_attr($get_custom_styling_details[0]['general_settings']['vbtn_bgcolor']):'';
$vbtn_bghcolor = (isset($get_custom_styling_details[0]['general_settings']['vbtn_bghcolor']) && $get_custom_styling_details[0]['general_settings']['vbtn_bghcolor'] != '')?esc_attr($get_custom_styling_details[0]['general_settings']['vbtn_bghcolor']):'';
$vbtn_fcolor = (isset($get_custom_styling_details[0]['general_settings']['vbtn_fcolor']) && $get_custom_styling_details[0]['general_settings']['vbtn_fcolor'] != '')?esc_attr($get_custom_styling_details[0]['general_settings']['vbtn_fcolor']):'';
$vbtn_fhcolor = (isset($get_custom_styling_details[0]['general_settings']['vbtn_fhcolor']) && $get_custom_styling_details[0]['general_settings']['vbtn_fhcolor'] != '')?esc_attr($get_custom_styling_details[0]['general_settings']['vbtn_fhcolor']):'';

if($activate_view_more_btn == 1){

	if($vbtn_bgcolor != ''){ 
      $content .= $containerwrap.'.wpmega-view-more-btn a{background-color:'.esc_attr($vbtn_bgcolor).';border-color:'.esc_attr($vbtn_bgcolor).'}';

	}
	if($vbtn_fcolor != ''){ 
      $content .= $containerwrap.'.wpmega-view-more-btn a i,'.$containerwrap.'.wpmega-view-more-btn a span{color:'.esc_attr($vbtn_fcolor).';}';
	}
	if($vbtn_bghcolor != ''){ 
      $content .= $containerwrap.'.wpmega-view-more-btn a:hover{background-color:'.esc_attr($vbtn_bghcolor).';}';
	}
	if($vbtn_fhcolor != ''){ 
      $content .= $containerwrap.'.wpmega-view-more-btn a:hover i,'.$containerwrap.'.wpmega-view-more-btn a:hover span{color:'.esc_attr($vbtn_fhcolor).';}';
	}
  }
 }
}
if (isset($menus) && !empty($menus)) {
foreach ($menus as $key => $value) {
  if ( isset ( $settings[ $key ]['enabled'] ) && $settings[ $key ]['enabled'] == 1 ) {
             $orientation = $settings[ $key ]['orientation'];
     if(isset($locations[ $key ] )){
     $menu = wp_get_nav_menu_object( $locations[ $key ] );
     $menuitems = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) ); // get all menu items of specific menu location
  if(isset($menuitems) && !empty($menuitems)):
	  foreach ($menuitems as $k1 => $val) {

       $menuID = $val->ID;
       $menu_item_parent = $val->menu_item_parent;
       $get_details = get_post_meta($menuID, '_wpmegamenu' );
       $top_menu_label = (isset($get_details[0]['general_settings']['top_menu_label']))?$get_details[0]['general_settings']['top_menu_label']:'';
       $label_animation = (isset($get_details[0]['general_settings']['label_animation'])?$get_details[0]['general_settings']['label_animation']:'none');
       $menuidwrap = '#wp_nav_menu-item-'.$menuID;

        if($top_menu_label != ''){ 
        	  $duration = (isset($get_details[0]['general_settings']['animaton_duration'])?$get_details[0]['general_settings']['animaton_duration']:$animation_duration);
	          $delay = (isset($get_details[0]['general_settings']['animaton_delay'])?$get_details[0]['general_settings']['animaton_delay']:$animation_delay);
	          $iteration_count = (isset($get_details[0]['general_settings']['animation_iteration_count']) && $get_details[0]['general_settings']['animation_iteration_count'] != '')?esc_attr($get_details[0]['general_settings']['animation_iteration_count']):$animation_iteration_count;
	          if($label_animation != 'none'){
	          	 $content .= $menuidwrap.' span.wpmm-mega-menu-label.wpmm_depth_first{
	          	  animation-iteration-count:'.esc_attr($iteration_count).';
	          	  -webkit-animation-iteration-count:'.esc_attr($iteration_count).';
	          	  animation-duration:'.esc_attr($duration).';
	          	  animation-delay:'.esc_attr($delay).';
	          	  -webkit-animation-duration:'.esc_attr($duration).';
	          	  -webkit-animation-delay:'.esc_attr($delay).';
	          	 }';
	            }
              } // top menu label end

    $enable_bg_image = (isset($get_details[0]['upload_image_settings']['enable_bg_image']) && $get_details[0]['upload_image_settings']['enable_bg_image'] == true)?1:0;
   if($enable_bg_image == 1 && $menu_item_parent == 0){
        $bg_image_type = (isset($get_details[0]['upload_image_settings']['bg_image_type'])?$get_details[0]['upload_image_settings']['bg_image_type']:'');
        $bg_image_url1 = (isset($get_details[0]['upload_image_settings']['bg_image_url1'])?esc_url($get_details[0]['upload_image_settings']['bg_image_url1']):'');
        $bg_image_url2 = (isset($get_details[0]['upload_image_settings']['bg_image_url2'])?esc_url($get_details[0]['upload_image_settings']['bg_image_url2']):'');
        $cross_fading_type = (isset($get_details[0]['upload_image_settings']['cross_fading_type'])?$get_details[0]['upload_image_settings']['cross_fading_type']:'');
        $image_position = (isset($get_details[0]['upload_image_settings']['image_position'])?$get_details[0]['upload_image_settings']['image_position']:'');
        $image_repeat = (isset($get_details[0]['upload_image_settings']['image_repeat'])?$get_details[0]['upload_image_settings']['image_repeat']:'');
        $duration_time = (isset($get_details[0]['upload_image_settings']['duration_time'])?$get_details[0]['upload_image_settings']['duration_time']:'10');
        $animation_type = (isset($get_details[0]['upload_image_settings']['animation_type'])?$get_details[0]['upload_image_settings']['animation_type']:'FadeInOut');
        if($bg_image_type == "double_image"){
          $animate_type = $animation_type;
        }else{
         $single_animation_type = (isset($get_details[0]['upload_image_settings']['single_animation_type'])?$get_details[0]['upload_image_settings']['single_animation_type']:'zoom');
          $animate_type = $single_animation_type;
        }
        $content .= '.first-image,.second-image{background-repeat:'.esc_attr($image_repeat).';background-position:'.esc_attr($image_position).';}';	
                if( $cross_fading_type != "changeonhover"){ 
        	 if($animate_type == "FadeInOut"){
                $content .= '#wpmm_cbg_'.$menuID.' img.top{ 
                	animation-name:'.esc_attr($animate_type).';
                	animation-timing-function:ease-in-out;
                	animation-iteration-count: infinite;
                	animation-duration:'.intval($duration_time).'s;
                	animation-direction: alternate;
                }';	
        	 }else if($animate_type == "zoom"){
               $content .= '#wpmm_cbg_'.$menuID.'.zoom{ 
                	animation:'.intval($duration_time).'s ease-in-out 1s normal none infinite running image;
                	-webkit-animation:'.intval($duration_time).'s ease-in-out 1s normal none infinite running image;
                	 opacity:0.5;
                }';	
        	 }
        }else{
        	$content .= '#wpmm_cbg_'.$menuID.' img{ 
                	-webkit-transition: opacity 1s ease-in-out;
                	-moz-transition: opacity 1s ease-in-out;
                	-o-transition: opacity 1s ease-in-out;
                	transition: opacity 1s ease-in-out;
                }';	
        }   
       }
	 }
	endif;
	}
  	}
  }
}
$content .= '/*Custom CSS End*/';