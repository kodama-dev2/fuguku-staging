<?php
$enable_shadow = (isset($wpmm_custom_theme['general']['enable_shadow']) && $wpmm_custom_theme['general']['enable_shadow'] == 1)?1:0;
$shadow_color = (isset($wpmm_custom_theme['general']['shadow_color']))?$wpmm_custom_theme['general']['shadow_color']:'#ffffff';
$zindex = (isset($wpmm_custom_theme['general']['zindex']))?$wpmm_custom_theme['general']['zindex']:'999';
$line_height = (isset($wpmm_custom_theme['general']['line_height']))?$wpmm_custom_theme['general']['line_height']:'1.5';
/* menu bar settings*/
$enable_menu_background = (isset($wpmm_custom_theme['menu_bar']['enable_menu_background']) && $wpmm_custom_theme['menu_bar']['enable_menu_background'] == 1)?1:0;
$menu_background_from = (isset($wpmm_custom_theme['menu_bar']['menu_background_from']))?$wpmm_custom_theme['menu_bar']['menu_background_from']:'#5ec073';
$font_color = (isset($wpmm_custom_theme['menu_bar']['font_color']))?$wpmm_custom_theme['menu_bar']['font_color']:'#ffffff';
$font_hover_color = (isset($wpmm_custom_theme['menu_bar']['font_hover_color']))?$wpmm_custom_theme['menu_bar']['font_hover_color']:'#000000';
$font_family = (isset($wpmm_custom_theme['menu_bar']['font_family']))?$wpmm_custom_theme['menu_bar']['font_family']:'Open Sans';
$font_weight = (isset($wpmm_custom_theme['menu_bar']['font_weight']))?$wpmm_custom_theme['menu_bar']['font_weight']:'normal';
$padding_top = (isset($wpmm_custom_theme['menu_bar']['padding_top']))?$wpmm_custom_theme['menu_bar']['padding_top']:'20px';
$padding_bottom = (isset($wpmm_custom_theme['menu_bar']['padding_bottom']))?$wpmm_custom_theme['menu_bar']['padding_bottom']:'25px';
$padding_left = (isset($wpmm_custom_theme['menu_bar']['padding_left']))?$wpmm_custom_theme['menu_bar']['padding_left']:'20px';
$padding_right = (isset($wpmm_custom_theme['menu_bar']['padding_right']))?$wpmm_custom_theme['menu_bar']['padding_right']:'22px';
$width = (isset($wpmm_custom_theme['menu_bar']['width']))?$wpmm_custom_theme['menu_bar']['width']:'600px';
$border_radius_topleft = (isset($wpmm_custom_theme['menu_bar']['border_radius_topleft']))?$wpmm_custom_theme['menu_bar']['border_radius_topleft']:'0px';
$border_radius_topright = (isset($wpmm_custom_theme['menu_bar']['border_radius_topright']))?$wpmm_custom_theme['menu_bar']['border_radius_topright']:'0px';
$border_radius_bottomright = (isset($wpmm_custom_theme['menu_bar']['border_radius_bottomright']))?$wpmm_custom_theme['menu_bar']['border_radius_bottomright']:'0px';
$border_radius_bottomleft = (isset($wpmm_custom_theme['menu_bar']['border_radius_bottomleft']))?$wpmm_custom_theme['menu_bar']['border_radius_bottomleft']:'0px';
$border_color = (isset($wpmm_custom_theme['menu_bar']['border_color']))?$wpmm_custom_theme['menu_bar']['border_color']:'#5ec073';
$alignment = (isset($wpmm_custom_theme['menu_bar']['alignment']))?$wpmm_custom_theme['menu_bar']['alignment']:'left';
$margin_top = (isset($wpmm_custom_theme['menu_bar']['margin_top']))?$wpmm_custom_theme['menu_bar']['margin_top']:'0px';
$margin_bottom = (isset($wpmm_custom_theme['menu_bar']['margin_bottom']))?$wpmm_custom_theme['menu_bar']['margin_bottom']:'0px';

/* top menu settings*/
$enable_background_hover1 = (isset($wpmm_custom_theme['top_menu']['enable_background_hover']))?1:0;
$background_hover_from1 = (isset($wpmm_custom_theme['top_menu']['background_hover_from']))?$wpmm_custom_theme['top_menu']['background_hover_from']:'#47a35b';
$bg_active_color1 = (isset($wpmm_custom_theme['top_menu']['bg_active_color']))?$wpmm_custom_theme['top_menu']['bg_active_color']:'';
$font_color_active1 = (isset($wpmm_custom_theme['top_menu']['font_color_active']))?$wpmm_custom_theme['top_menu']['font_color_active']:'#ffffff';
$font_size = (isset($wpmm_custom_theme['top_menu']['font_size']) && $wpmm_custom_theme['top_menu']['font_size'] != '' && $wpmm_custom_theme['top_menu']['font_size'] != '0px')?$wpmm_custom_theme['top_menu']['font_size']:'13px';

$font_weight_hover1 = (isset($wpmm_custom_theme['top_menu']['font_weight_hover']) && $wpmm_custom_theme['top_menu']['font_weight_hover'] != '' && $wpmm_custom_theme['top_menu']['font_weight_hover'] != 'default')?$wpmm_custom_theme['top_menu']['font_weight_hover']:'normal';
$transform = (isset($wpmm_custom_theme['top_menu']['transform']))?$wpmm_custom_theme['top_menu']['transform']:'initial';
$font_decoration1 = (isset($wpmm_custom_theme['top_menu']['font_decoration']))?$wpmm_custom_theme['top_menu']['font_decoration']:'none';
$font_decoration_hover1 = (isset($wpmm_custom_theme['top_menu']['font_decoration_hover']))?$wpmm_custom_theme['top_menu']['font_decoration_hover']:'none';


$enable_menu_divider = (isset($wpmm_custom_theme['top_menu']['enable_menu_divider']) && $wpmm_custom_theme['top_menu']['enable_menu_divider'] == 1)?1:0;
$disable_menu_divider = (isset($wpmm_custom_theme['top_menu']['disable_menu_divider']) && $wpmm_custom_theme['top_menu']['disable_menu_divider'] == 1)?1:0;
$menu_divider_color = (isset($wpmm_custom_theme['top_menu']['menu_divider_color']))?$wpmm_custom_theme['top_menu']['menu_divider_color']:'rgb(255,255,255)';
$opacity_glow = (isset($wpmm_custom_theme['top_menu']['opacity_glow']) && $wpmm_custom_theme['top_menu']['opacity_glow'] != '')?$wpmm_custom_theme['top_menu']['opacity_glow']:1;

$enable_menu_label_bgcolor = (isset($wpmm_custom_theme['top_menu']['enable_menu_label_bgcolor']) && $wpmm_custom_theme['top_menu']['enable_menu_label_bgcolor'] == 1)?1:0;
$menu_label_bgcolor = (isset($wpmm_custom_theme['top_menu']['menu_label_bgcolor']))?$wpmm_custom_theme['top_menu']['menu_label_bgcolor']:'';
$menu_label_fontcolor = (isset($wpmm_custom_theme['top_menu']['menu_label_fontcolor']))?$wpmm_custom_theme['top_menu']['menu_label_fontcolor']:'';
$menu_label_fontsize = (isset($wpmm_custom_theme['top_menu']['menu_label_fontsize']))?$wpmm_custom_theme['top_menu']['menu_label_fontsize']:'';
$menu_label_font_weight = (isset($wpmm_custom_theme['top_menu']['menu_label_font_weight']))?$wpmm_custom_theme['top_menu']['menu_label_font_weight']:'';
$menu_label_font_transform = (isset($wpmm_custom_theme['top_menu']['menu_label_font_transform']))?$wpmm_custom_theme['top_menu']['menu_label_font_transform']:'initial';
$menu_label_font_family = (isset($wpmm_custom_theme['top_menu']['menu_label_font_family']))?$wpmm_custom_theme['top_menu']['menu_label_font_family']:'';


/* megamenu bar settings*/
$enable_megamenu_background2 = (isset($wpmm_custom_theme['megamenu_bar']['enable_megamenu_background']) && $wpmm_custom_theme['megamenu_bar']['enable_megamenu_background'] == 1)?1:0;
$menu_background_from2 = (isset($wpmm_custom_theme['megamenu_bar']['menu_background_from']))?$wpmm_custom_theme['megamenu_bar']['menu_background_from']:'#ffffff';
$width2 = (isset($wpmm_custom_theme['megamenu_bar']['width']))?$wpmm_custom_theme['megamenu_bar']['width']:'100%';
$padding_top2 = (isset($wpmm_custom_theme['megamenu_bar']['padding_top']))?$wpmm_custom_theme['megamenu_bar']['padding_top']:'15px';
$padding_bottom2 = (isset($wpmm_custom_theme['megamenu_bar']['padding_bottom']))?$wpmm_custom_theme['megamenu_bar']['padding_bottom']:'5px';
$padding_left2 = (isset($wpmm_custom_theme['megamenu_bar']['padding_left']))?$wpmm_custom_theme['megamenu_bar']['padding_left']:'8px';
$padding_right2 = (isset($wpmm_custom_theme['megamenu_bar']['padding_right']))?$wpmm_custom_theme['megamenu_bar']['padding_right']:'8px';
$border_color2 = (isset($wpmm_custom_theme['megamenu_bar']['border_color']))?$wpmm_custom_theme['megamenu_bar']['border_color']:'0px';
$border_radius2 = (isset($wpmm_custom_theme['megamenu_bar']['border_radius']))?$wpmm_custom_theme['megamenu_bar']['border_radius']:'#ffffff';
$box_shadow2 = (isset($wpmm_custom_theme['megamenu_bar']['box_shadow']))?$wpmm_custom_theme['megamenu_bar']['box_shadow']:'0 3px 3px';
$box_shadow_color2 = (isset($wpmm_custom_theme['megamenu_bar']['box_shadow_color']))?$wpmm_custom_theme['megamenu_bar']['box_shadow_color']:'rgba(0, 0, 0, 0.2)';

/* widget settings */
$font_color3 = (isset($wpmm_custom_theme['widgets']['font_color']) && $wpmm_custom_theme['widgets']['font_color'] != '' && $wpmm_custom_theme['widgets']['font_color'] != '#00000')?$wpmm_custom_theme['widgets']['font_color']:'#000000';
$font_hover_color3 = (isset($wpmm_custom_theme['widgets']['font_hover_color']) && $wpmm_custom_theme['widgets']['font_hover_color'] != '' && $wpmm_custom_theme['widgets']['font_hover_color'] != '#00000')?$wpmm_custom_theme['widgets']['font_hover_color']:'#000000';
$font_size3 = (isset($wpmm_custom_theme['widgets']['font_size']))?$wpmm_custom_theme['widgets']['font_size']:'14px';
$font_weight3 = (isset($wpmm_custom_theme['widgets']['font_weight']))?$wpmm_custom_theme['widgets']['font_weight']:'bold';
$font_weight_hover3 = (isset($wpmm_custom_theme['widgets']['font_weight_hover']))?$wpmm_custom_theme['widgets']['font_weight_hover']:'bold';
$transform3 = (isset($wpmm_custom_theme['widgets']['transform']))?$wpmm_custom_theme['widgets']['transform']:'bold';
$font_family3 = (isset($wpmm_custom_theme['widgets']['font_family']))?$wpmm_custom_theme['widgets']['font_family']:'bold';
$font_decoration3 = (isset($wpmm_custom_theme['widgets']['font_decoration']))?$wpmm_custom_theme['widgets']['font_decoration']:'none';
$font_decoration_hover3 = (isset($wpmm_custom_theme['widgets']['font_decoration_hover']))?$wpmm_custom_theme['widgets']['font_decoration_hover']:'none';
$content_font_color3 = (isset($wpmm_custom_theme['widgets']['content_font_color']) && $wpmm_custom_theme['widgets']['content_font_color'] != '' && $wpmm_custom_theme['widgets']['content_font_color'] != '#00000')?$wpmm_custom_theme['widgets']['content_font_color']:'#000000';
$content_font_family3 = (isset($wpmm_custom_theme['widgets']['content_font_family']))?$wpmm_custom_theme['widgets']['content_font_family']:'Open Sans';
$margin_top3 = (isset($wpmm_custom_theme['widgets']['margin_top']) && $wpmm_custom_theme['widgets']['margin_top'] != '')?$wpmm_custom_theme['widgets']['margin_top'].'px':'0px';
$margin_bottom3 = (isset($wpmm_custom_theme['widgets']['margin_bottom']) && $wpmm_custom_theme['widgets']['margin_bottom'] != '')?$wpmm_custom_theme['widgets']['margin_bottom'].'px':'10px';

$widget_btnbgcolor = (isset($wpmm_custom_theme['widgets']['button_bgcolor']))?$wpmm_custom_theme['widgets']['button_bgcolor']:'';
$widget_btnbghcolor = (isset($wpmm_custom_theme['widgets']['button_bghcolor']))?$wpmm_custom_theme['widgets']['button_bghcolor']:'';
$widget_btnfcolor = (isset($wpmm_custom_theme['widgets']['button_fcolor']))?$wpmm_custom_theme['widgets']['button_fcolor']:'';
$widget_btnfhcolor = (isset($wpmm_custom_theme['widgets']['button_fhcolor']))?$wpmm_custom_theme['widgets']['button_fhcolor']:'';


/* Top Section settings */
$font_color4 = (isset($wpmm_custom_theme['top_section']['font_color']))?$wpmm_custom_theme['top_section']['font_color']:'#000000';
$font_size4 = (isset($wpmm_custom_theme['top_section']['font_size']))?$wpmm_custom_theme['top_section']['font_size']:'13px';
$font_weight4 = (isset($wpmm_custom_theme['top_section']['font_weight']))?$wpmm_custom_theme['top_section']['font_weight']:'normal';
$transform4 = (isset($wpmm_custom_theme['top_section']['transform']))?$wpmm_custom_theme['top_section']['transform']:'initial';
$font_family4 = (isset($wpmm_custom_theme['top_section']['font_family']))?$wpmm_custom_theme['top_section']['font_family']:'Open Sans';
$image_margin_top4 = (isset($wpmm_custom_theme['top_section']['image_margin_top']))?$wpmm_custom_theme['top_section']['image_margin_top']:'0px';
$image_margin_bottom4 = (isset($wpmm_custom_theme['top_section']['image_margin_bottom']))?$wpmm_custom_theme['top_section']['image_margin_bottom']:'10px';
$image_margin_left4 = (isset($wpmm_custom_theme['top_section']['image_margin_left']))?$wpmm_custom_theme['top_section']['image_margin_left']:'0px';
$image_margin_right4 = (isset($wpmm_custom_theme['top_section']['image_margin_right']))?$wpmm_custom_theme['top_section']['image_margin_right']:'0px';


/* Bottom Section settings */
$font_color5 = (isset($wpmm_custom_theme['bottom_section']['font_color']))?$wpmm_custom_theme['bottom_section']['font_color']:'#000000';
$font_size5 = (isset($wpmm_custom_theme['bottom_section']['font_size']))?$wpmm_custom_theme['bottom_section']['font_size']:'13px';
$font_weight5 = (isset($wpmm_custom_theme['bottom_section']['font_weight']))?$wpmm_custom_theme['bottom_section']['font_weight']:'normal';
$transform5 = (isset($wpmm_custom_theme['bottom_section']['transform']))?$wpmm_custom_theme['bottom_section']['transform']:'initial';
$font_family5 = (isset($wpmm_custom_theme['bottom_section']['font_family']))?$wpmm_custom_theme['bottom_section']['font_family']:'Open Sans';
$image_margin_top5 = (isset($wpmm_custom_theme['bottom_section']['image_margin_top']))?$wpmm_custom_theme['bottom_section']['image_margin_top']:'10px';
$image_margin_bottom5 = (isset($wpmm_custom_theme['bottom_section']['image_margin_bottom']))?$wpmm_custom_theme['bottom_section']['image_margin_bottom']:'0px';
$image_margin_left5 = (isset($wpmm_custom_theme['bottom_section']['image_margin_left']))?$wpmm_custom_theme['bottom_section']['image_margin_left']:'0px';
$image_margin_right5 = (isset($wpmm_custom_theme['bottom_section']['image_margin_right']))?$wpmm_custom_theme['bottom_section']['image_margin_right']:'0px';


/* Flyout settings */
$enable_background6 = (isset($wpmm_custom_theme['flyout']['enable_background']))?1:0;
$menu_bgcurrentcolor6 = (isset($wpmm_custom_theme['flyout']['menu_bgcurrentcolor']))?$wpmm_custom_theme['flyout']['menu_bgcurrentcolor']:'#5ec073';
$menu_bg_hovercolor6 = (isset($wpmm_custom_theme['flyout']['menu_bg_hovercolor']))?$wpmm_custom_theme['flyout']['menu_bg_hovercolor']:'#47a35b';
$font_color6 = (isset($wpmm_custom_theme['flyout']['font_color']))?$wpmm_custom_theme['flyout']['font_color']:'#ffffff';
$font_hover_color6 = (isset($wpmm_custom_theme['flyout']['font_hover_color']))?$wpmm_custom_theme['flyout']['font_hover_color']:'#ffffff';
$font_size6 = (isset($wpmm_custom_theme['flyout']['font_size']))?$wpmm_custom_theme['flyout']['font_size']:'12px';
$font_weight6 = (isset($wpmm_custom_theme['flyout']['font_weight']))?$wpmm_custom_theme['flyout']['font_weight']:'normal';
$font_weight_hover6 = (isset($wpmm_custom_theme['flyout']['font_weight_hover']))?$wpmm_custom_theme['flyout']['font_weight_hover']:'normal';
$transform6 = (isset($wpmm_custom_theme['flyout']['transform']))?$wpmm_custom_theme['flyout']['transform']:'initial';
$font_family6 = (isset($wpmm_custom_theme['flyout']['font_family']))?$wpmm_custom_theme['flyout']['font_family']:'Open Sans';
$font_decoration6 = (isset($wpmm_custom_theme['flyout']['font_decoration']))?$wpmm_custom_theme['flyout']['font_decoration']:'none';
$font_decoration_hover6 = (isset($wpmm_custom_theme['flyout']['font_decoration_hover']))?$wpmm_custom_theme['flyout']['font_decoration_hover']:'none';
$item_margin6 = (isset($wpmm_custom_theme['flyout']['item_margin']))?$wpmm_custom_theme['flyout']['item_margin']:'0px 5px';
$item_padding6 = (isset($wpmm_custom_theme['flyout']['item_padding']))?$wpmm_custom_theme['flyout']['item_padding']:'10px';
$item_width6 = (isset($wpmm_custom_theme['flyout']['item_width']))?$wpmm_custom_theme['flyout']['item_width']:'210px';
/* Mobile settings */
$togglebar_enable_bgcolor = (isset($wpmm_custom_theme['mobile_settings']['togglebar_enable_bgcolor']) && $wpmm_custom_theme['mobile_settings']['togglebar_enable_bgcolor'] == 1)?1:0;
$togglebar_background_from = (isset($wpmm_custom_theme['mobile_settings']['togglebar_background_from']))?$wpmm_custom_theme['mobile_settings']['togglebar_background_from']:'#5ec073';
$togglebar_height = (isset($wpmm_custom_theme['mobile_settings']['togglebar_height']))?$wpmm_custom_theme['mobile_settings']['togglebar_height']:'40px';
$resposive_breakpoint_width = (isset($wpmm_custom_theme['mobile_settings']['resposive_breakpoint_width']) && $wpmm_custom_theme['mobile_settings']['resposive_breakpoint_width'] != '' && $wpmm_custom_theme['mobile_settings']['resposive_breakpoint_width'] != '910px' && $wpmm_custom_theme['mobile_settings']['resposive_breakpoint_width'] != '910')?$wpmm_custom_theme['mobile_settings']['resposive_breakpoint_width']:'910px';
$toggle_bar_content = (isset($wpmm_custom_theme['mobile_settings']['toggle_bar_content']))?$wpmm_custom_theme['mobile_settings']['toggle_bar_content']:'Menu';
$icon_color = (isset($wpmm_custom_theme['mobile_settings']['icon_color']))?$wpmm_custom_theme['mobile_settings']['icon_color']:'#ffffff';
$text_color = (isset($wpmm_custom_theme['mobile_settings']['text_color']))?$wpmm_custom_theme['mobile_settings']['text_color']:'#ffffff';
$togglebar_align = (isset($wpmm_custom_theme['mobile_settings']['togglebar_align']))?$wpmm_custom_theme['mobile_settings']['togglebar_align']:'left';
$submenu_closebtn_position = (isset($wpmm_custom_theme['mobile_settings']['submenu_closebtn_position']))?$wpmm_custom_theme['mobile_settings']['submenu_closebtn_position']:'bottom';
$submenus_retractor_text = (isset($wpmm_custom_theme['mobile_settings']['submenus_retractor_text']))?$wpmm_custom_theme['mobile_settings']['submenus_retractor_text']:'CLOSE';
/* Search Bar settings */
$font_size7 = (isset($wpmm_custom_theme['search_bar']['font_size']))?$wpmm_custom_theme['search_bar']['font_size']:'10px';
$width7 = (isset($wpmm_custom_theme['search_bar']['width']))?$wpmm_custom_theme['search_bar']['width']:'182px';
$text_color7 = (isset($wpmm_custom_theme['search_bar']['text_color']))?$wpmm_custom_theme['search_bar']['text_color']:'#fffff';
$bg_color7 = (isset($wpmm_custom_theme['search_bar']['bg_color']))?$wpmm_custom_theme['search_bar']['bg_color']:'#5ec073';
$text_placholder_color7 = (isset($wpmm_custom_theme['search_bar']['text_placholder_color']))?$wpmm_custom_theme['search_bar']['text_placholder_color']:'#ccc';
$icon_color7 = (isset($wpmm_custom_theme['search_bar']['icon_color']))?$wpmm_custom_theme['search_bar']['icon_color']:'#fffff';
$search_button_bg_color = (isset($wpmm_custom_theme['search_bar']['search_button_bg_color']))?$wpmm_custom_theme['search_bar']['search_button_bg_color']:'';
$search_button_bg_hovercolor = (isset($wpmm_custom_theme['search_bar']['search_button_bg_hovercolor']))?$wpmm_custom_theme['search_bar']['search_button_bg_hovercolor']:'';
$search_button_font_color = (isset($wpmm_custom_theme['search_bar']['search_button_font_color']))?$wpmm_custom_theme['search_bar']['search_button_font_color']:'';
$search_button_font_hovercolor = (isset($wpmm_custom_theme['search_bar']['search_button_font_hovercolor']))?$wpmm_custom_theme['search_bar']['search_button_font_hovercolor']:'';
$search_transform = (isset($wpmm_custom_theme['search_bar']['transform']))?$wpmm_custom_theme['search_bar']['transform']:'initial';
$font_popup_family = (isset($wpmm_custom_theme['search_bar']['font_popup_family']))?$wpmm_custom_theme['search_bar']['font_popup_family']:'';



/* tab menu settings*/
$htab_bgcolor = (isset($wpmm_custom_theme['horizontal_tabbed']['bgcolor']))?$wpmm_custom_theme['horizontal_tabbed']['bgcolor']:'';
$htab_bg_active_color = (isset($wpmm_custom_theme['horizontal_tabbed']['bg_active_color']))?$wpmm_custom_theme['horizontal_tabbed']['bg_active_color']:'';
$htab_font_color = (isset($wpmm_custom_theme['horizontal_tabbed']['font_color']))?$wpmm_custom_theme['horizontal_tabbed']['font_color']:'';
$htab_font_hcolor = (isset($wpmm_custom_theme['horizontal_tabbed']['font_hcolor']))?$wpmm_custom_theme['horizontal_tabbed']['font_hcolor']:'';
$htab_content_bgcolor = (isset($wpmm_custom_theme['horizontal_tabbed']['content_bgcolor']))?$wpmm_custom_theme['horizontal_tabbed']['content_bgcolor']:'';
$htab_content_fcolor = (isset($wpmm_custom_theme['horizontal_tabbed']['content_fcolor']))?$wpmm_custom_theme['horizontal_tabbed']['content_fcolor']:'';

$vtab_bgcolor = (isset($wpmm_custom_theme['vertical_tabbed']['bgcolor']))?$wpmm_custom_theme['vertical_tabbed']['bgcolor']:'';
$vtab_bghcolor = (isset($wpmm_custom_theme['vertical_tabbed']['bghcolor']))?$wpmm_custom_theme['vertical_tabbed']['bghcolor']:'';
$vtab_activebgcolor = (isset($wpmm_custom_theme['vertical_tabbed']['bg_active_color']))?$wpmm_custom_theme['vertical_tabbed']['bg_active_color']:'';
$vtab_fcolor = (isset($wpmm_custom_theme['vertical_tabbed']['font_color']))?$wpmm_custom_theme['vertical_tabbed']['font_color']:'';
$vtab_font_hcolor = (isset($wpmm_custom_theme['vertical_tabbed']['font_hover_color']))?$wpmm_custom_theme['vertical_tabbed']['font_hover_color']:'';

$tab_width = (isset($wpmm_custom_theme['horizontal_tabbed']['tab_width']))?$wpmm_custom_theme['horizontal_tabbed']['tab_width']:'';
$tab_layout = (isset($wpmm_custom_theme['horizontal_tabbed']['tab_layout']) && $wpmm_custom_theme['horizontal_tabbed']['tab_layout'] != '')?$wpmm_custom_theme['horizontal_tabbed']['tab_layout']:'skew_layout';
if($transform == "normal"){
 $transform = "initial";
}
if($menu_label_font_transform == "normal"){
  $menu_label_font_transform = "initial";
}
if($transform3 == "normal"){
    $transform3 = "initial";
}
if($transform4 == "normal"){
    $transform4 = "initial";
}
if($transform5 == "normal"){
    $transform5 = "initial";
}
if($transform6 == "normal"){
    $transform6 = "initial";
}
if($search_transform == "normal"){
	$search_transform = "initial";
}
$maincustom_idwrapper = ".wpmm_megamenu .wp-megamenu-main-wrapper.wpmm-ctheme-wrapper".$theme_slug;

if($enable_menu_background == 1 && $menu_background_from != ''){
$content .= $maincustom_idwrapper.','.$maincustom_idwrapper.' ul.wpmm-mega-wrapper,'.$maincustom_idwrapper.'.wpmm-orientation-horizontal,'.$maincustom_idwrapper.'.wpmm-orientation-vertical{
  	  background:'.esc_attr($menu_background_from).';
  	 }';
 /* a tag small line on before tag */
$content .= $maincustom_idwrapper.' .wpmm-sub-menu-wrap ul li h4.wpmm-mega-block-title::before,'.$maincustom_idwrapper.' .wpmm-sub-menu-wrap ul li.wp-mega-menu-header > a.wp-mega-menu-link::before{
  	  background:'.esc_attr($menu_background_from).';}';
}else{
$content .= $maincustom_idwrapper.','.$maincustom_idwrapper.' ul.wpmm-mega-wrapper,'.$maincustom_idwrapper.'.wpmm-orientation-horizontal,'.$maincustom_idwrapper.'.wpmm-orientation-vertical{
      background:#000000;
     }';
}

if($border_radius_topleft != '' || $margin_top !='' || $margin_bottom!=''){
$content .= $maincustom_idwrapper.','.$maincustom_idwrapper.' ul.wpmm-mega-wrapper,'.$maincustom_idwrapper.' ul.wpmm-mega-wrapper > li{
  	  border-radius:'.esc_attr($border_radius_topleft).' '.esc_attr($border_radius_topright).' '.esc_attr($border_radius_bottomright).' '.esc_attr($border_radius_bottomleft).';
  	  border:'.esc_attr($border_color).';
  	  margin-top:'.esc_attr($margin_top).';
  	  margin-bottom:'.esc_attr($margin_bottom).';
  	 }';
}
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper > li,'.$maincustom_idwrapper.' ul.wpmm-mega-wrapper > li{
  	  border-radius:'.esc_attr($border_radius_topleft).' '.esc_attr($border_radius_topright).' '.esc_attr($border_radius_bottomright).' '.esc_attr($border_radius_bottomleft).';
  	 }';
if($alignment !=''){
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper{
  	  text-align:'.esc_attr($alignment).';
  	 }';
}
if($width !=''){
$content .= $maincustom_idwrapper.'.wpmm-orientation-horizontal{
  	  width:'.esc_attr($width).';
  	 }';
}
if($font_color_active1 !=''){
$content .= $maincustom_idwrapper.' .wpmm-mega-wrapper > li.current-menu-item > a{
  	  color:'.esc_attr($font_color_active1).' !important;
  	 }';
}
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper > li > a{
  	  color:'.esc_attr($font_color).';
  	  font-size:'.esc_attr($font_size).';
  	  font-weight:'.esc_attr($font_weight).';
  	  font-family:'.esc_attr($font_family).';
  	  text-transform:'.esc_attr($transform).';
  	  text-decoration:'.esc_attr($font_decoration1).';
   }';
if($padding_top != '' || $padding_right != '' || $padding_bottom != '' || $padding_left != ''){
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper > li > a{
  	  padding:'.esc_attr($padding_top).' '.esc_attr($padding_right).' '.esc_attr($padding_bottom).' '.esc_attr($padding_left).';
  	 }';
}
if($enable_background_hover1 == 1 && $background_hover_from1 != ''){
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper > li:hover{
  	  background:'.esc_attr($background_hover_from1).';
  	 }';
}else{
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper > li:hover{
  	  background:#47a35b;
  	 }';
}
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper > li:hover > a{
  	  font-weight:'.esc_attr($font_weight_hover1).';
  	  text-decoration:'.esc_attr($font_decoration_hover1).';
  	  color:'.esc_attr($font_hover_color).';
  	 }';

if($disable_menu_divider != 1){
  if($enable_menu_divider == 1){
  	if($menu_divider_color != '' || $opacity_glow != ''){
     $content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper > li > a.wp-mega-menu-link::before{
  	  background:'.esc_attr($menu_divider_color).';
  	  opacity:'.esc_attr($opacity_glow).';
  	  content: "";
      height: 100%;
      position: absolute;
      right: 0;
      top: 0;
      width: 1px;
  	 }';
  	}
  }
}
if($bg_active_color1 != ''){
$content .= $maincustom_idwrapper.' .wpmm-mega-wrapper > li.current-menu-item{
  	  background:'.esc_attr($bg_active_color1).';
  	 }';
}
/*Mega menu */
if($enable_megamenu_background2 == 1 && $menu_background_from2 != ''){
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap{
  	  background:'.esc_attr($menu_background_from2).';
  	 }';
}
if($padding_top2 != '' || $padding_bottom2 != '' || $padding_left2 != '' || $padding_right2 != ''){
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li:hover > .wpmm-sub-menu-wrap{
  	  padding-top:'.esc_attr($padding_top2).';
  	  padding-bottom:'.esc_attr($padding_bottom2).';
  	  padding-left:'.esc_attr($padding_left2).';
  	  padding-right:'.esc_attr($padding_right2).';
  	 }';
}
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap{
  	  width:'.esc_attr($width2).';
  	  border-radius:'.esc_attr($border_radius2).';
  	 }';
if($border_color2 != ''){
     $content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap{
      border: 1px solid '.esc_attr($border_color2).';
     }';
}
if($box_shadow2 != '' || $box_shadow_color2 != ''){
     $content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap{
      box-shadow:'.esc_attr($box_shadow2).' '.esc_attr($box_shadow_color2).';
     }';
}
/*Widget section*/
$content .= $maincustom_idwrapper.' .wpmm-sub-menu-wrap ul li h4.wpmm-mega-block-title,'.$maincustom_idwrapper.' .wpmm-sub-menu-wrap ul li.wp-mega-menu-header > a.wp-mega-menu-link span.wpmm-mega-menu-href-title,'.$maincustom_idwrapper.' .wpmm-sub-menu-wrap ul.wp-mega-sub-menu li.wpmm-custom-post-settings.wpmega-image-left .wpmm-custom-postimage span.wpmm-mega-menu-href-title,'.$maincustom_idwrapper.' .wpmm-sub-menu-wrap ul.wp-mega-sub-menu li.wpmm-custom-post-settings.wpmega-image-top .wpmm-custom-postimage span.wpmm-mega-menu-href-title{
  	  color:'.esc_attr($font_color3).';
  	  font-size: '.esc_attr($font_size3).';
  	  font-weight:'.esc_attr($font_weight3).';
  	  text-transform:'.esc_attr($transform3).';
  	  font-family:'.esc_attr($font_family3).';
  	  text-decoration:'.esc_attr($font_decoration3).';
  	  margin-bottom:'.esc_attr($margin_bottom3).';
  	  margin-top:'.esc_attr($margin_top3).';
  	}';
$content .= $maincustom_idwrapper.' .wpmm-sub-menu-wrap ul li h4.wpmm-mega-block-title:hover,'.$maincustom_idwrapper.' .wpmm-sub-menu-wrap ul li.wp-mega-menu-header > a.wp-mega-menu-link span.wpmm-mega-menu-href-title:hover{
  	  color:'.esc_attr($font_hover_color3).';
  	  font-weight:'.esc_attr($font_weight_hover3).';
  	  text-decoration:'.esc_attr($font_decoration_hover3).';
  	}';
$content .= $maincustom_idwrapper.' .wpmm-sub-menu-wrap ul li,'.$maincustom_idwrapper.' ul.wp-mega-sub-menu li a{
  	  color:'.esc_attr($content_font_color3).';
  	  font-family:'.esc_attr($content_font_family3).';
  	}';
if($content_font_color3 != ''){
 $content .= $maincustom_idwrapper.' .wp-mega-sub-menu li .wpmm-sub-menu-wrapper.wpmm_menu_1 li::before,'.$maincustom_idwrapper.' .wp-mega-sub-menu .widget_pages li::before,'.$maincustom_idwrapper.' .wp-mega-sub-menu .widget_categories li::before,'.$maincustom_idwrapper.' .wp-mega-sub-menu .widget_archive li::before,'.$maincustom_idwrapper.' .wp-mega-sub-menu .widget_meta li::before,'.$maincustom_idwrapper.' .wp-mega-sub-menu .widget_recent_comments li::before,'.$maincustom_idwrapper.' .wp-mega-sub-menu .widget_recent_entries li::before,'.$maincustom_idwrapper.' .wp-mega-sub-menu .widget_product_categories ul.product-categories li a::before,'.$maincustom_idwrapper.' .wp-mega-sub-menu .widget_categories li::before,'.$maincustom_idwrapper.' .wp-mega-sub-menu .widget_archive li::before{
  	  color:'.esc_attr($content_font_color3).';
  	}';
  	$content .= $maincustom_idwrapper.' ul li.wpmega-menu-megamenu ul li a:hover,'.$maincustom_idwrapper.' ul li.wpmega-menu-megamenu ul li a,'.$maincustom_idwrapper.' ul li.wpmega-menu-megamenu ul li a:focus,'.$maincustom_idwrapper.' ul li.wpmega-menu-megamenu ul li span.wpmm-mega-menu-href-title{
  	  color:'.esc_attr($content_font_color3).';
  	}';
}
/*
* Top Section Stylesheet
*/
 $content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap span.wpmm_megamenu_topcontent{
  	  font-size:'.esc_attr($font_size4).';
  	  color:'.esc_attr($font_color4).';
  	  font-family:'.esc_attr($font_family4).';
  	  font-weight:'.esc_attr($font_weight4).';
  	  text-transform:'.esc_attr($transform4).';
  	  margin-left:'.esc_attr($image_margin_left4).';
  	  margin-right:'.esc_attr($image_margin_right4).';
  	}';
if($image_margin_bottom4 != ''){
 $content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap .top_clearfix{
  	  margin-bottom:'.esc_attr($image_margin_bottom4).';
  	}';
}

if($image_margin_bottom4 != ''){
 $content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap .wpmm-topimage,'.$maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap .wpmm-ctop{
  	  margin-left:'.esc_attr($image_margin_left4).';
  	  margin-right:'.esc_attr($image_margin_right4).';
  	  margin-top:'.esc_attr($image_margin_top4).';
  	}';
}
/*
* Bottom Section stylesheet
*/
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap span.wpmm_megamenu_bottomcontent{
  	  font-size:'.esc_attr($font_size5).';
  	  color:'.esc_attr($font_color5).';
  	  font-family:'.esc_attr($font_family5).';
  	  font-weight:'.esc_attr($font_weight5).';
  	  text-transform:'.esc_attr($transform5).';
  	  margin-left:'.esc_attr($image_margin_left5).';
  	  margin-right:'.esc_attr($image_margin_right5).';
  	}';
if($image_margin_top5 != ''){
	$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap .bottom_clearfix{
  	  margin-top:'.esc_attr($image_margin_top5).';
  	}';
}


$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap .wpmm-bottomimage,'.$maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap .wpmm-cbottom{
  	  margin-left:'.esc_attr($image_margin_left5).';
  	  margin-right:'.esc_attr($image_margin_right5).';
  	  margin-bottom:'.esc_attr($image_margin_bottom5).';
  	}';
/*flyout*/

if($enable_background6 == 1 && $menu_bgcurrentcolor6 != ''){
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li.wpmega-menu-flyout ul{
  	  background:'.esc_attr($menu_bgcurrentcolor6).';
  	}';
}
if($item_width6 != ''){
	$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li.wpmega-menu-flyout ul{
  	  width:'.esc_attr($item_width6).';
  	}';
}
if($menu_bg_hovercolor6 != ''){
	$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li.wpmega-menu-flyout ul.wp-mega-sub-menu li:hover{
  	  background:'.esc_attr($menu_bg_hovercolor6).';
  	}';
}

if($item_margin6 != ''){
	$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li.wpmega-menu-flyout.wpmega-flyout-horizontal-left ul.wp-mega-sub-menu li,'.$maincustom_idwrapper.' ul.wpmm-mega-wrapper li.wpmega-menu-flyout.wpmega-flyout-horizontal-right ul.wp-mega-sub-menu li{
  	  margin:'.esc_attr($item_margin6).';
  	}';
}
if($item_padding6 != ''){
	$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li.wpmega-menu-flyout.wpmega-flyout-horizontal-left ul.wp-mega-sub-menu li a.wp-mega-menu-link,'.$maincustom_idwrapper.' ul.wpmm-mega-wrapper li.wpmega-menu-flyout.wpmega-flyout-horizontal-right ul.wp-mega-sub-menu li a.wp-mega-menu-link{
  	  padding:'.esc_attr($item_padding6).';
  	}';
}

$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li.wpmega-menu-flyout ul.wp-mega-sub-menu li a{
  	  font-size:'.esc_attr($font_size6).';
  	  color:'.esc_attr($font_color6).';
  	  font-family:'.esc_attr($font_family6).';
  	  font-weight:'.esc_attr($font_weight6).';
  	  text-transform:'.esc_attr($font_decoration6).';
  	}';
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li.wpmega-menu-flyout ul.wp-mega-sub-menu li:hover a,'.$maincustom_idwrapper.' ul.wpmm-mega-wrapper li.wpmega-menu-flyout ul.wp-mega-sub-menu li:hover a span{
  	  color:'.esc_attr($font_hover_color6).';
  	  font-weight:'.esc_attr($font_weight_hover6).';
  	  text-decoration:'.esc_attr($font_decoration_hover6).';
  	}';

 /* search bar */
 $content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li.wpmm-menu-align-right.wpmm-search-type .wpmm-sub-menu-wrap
 .megamenu-type-search input.search-submit[type="submit"]{
  	  font-size:'.esc_attr($font_size7).';
  	  color:'.esc_attr($text_color7).';
  	  background:'.esc_attr($bg_color7).';
  	}';
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li.wpmm-menu-align-right.wpmm-search-type .wpmm-sub-menu-wrap .megamenu-type-search input.search-field[type="search"]{
  	  width:'.esc_attr($width7).';
  	  color:'.esc_attr($text_placholder_color7).';
  	}';

 if($icon_color7 != '' ){
  $content .= $maincustom_idwrapper.' .wpmm-search-type  > .wpmm-mega-menu-icon > i.fa-search,'.$maincustom_idwrapper.' .wpmm-search-type  > .wpmm-mega-menu-icon > i.genericon-search,'.$maincustom_idwrapper.' .wpmm-search-type  > .wpmm-mega-menu-icon > i.dashicons-search{
  	  color:'.esc_attr($icon_color7).';
  	}';
 }else{
 	$content .= $maincustom_idwrapper.' a.wpmm-search-type > .wpmm-mega-menu-icon > i.fa-search,'.$maincustom_idwrapper.' .wpmm-search-type  > .wpmm-mega-menu-icon > i.genericon-search,'.$maincustom_idwrapper.' .wpmm-search-type  > .wpmm-mega-menu-icon > i.dashicons-search{
  	  color:'.esc_attr($font_color).';
  	}';
 }

if($width7 != '' ){
$content .= $maincustom_idwrapper.' .wpmm-mega-wrapper .wpmega-searchinline input.search-field{
  	  width:'.esc_attr($width7).';
  	}';
}
/* Popup Search Form */
if($bg_color7 != '' || $width7 != ''){
	$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li div.popup-search-form .wpmm-overlay-search{
  	  background:'.esc_attr($bg_color7).';
  	  width:'.esc_attr($width7).';
  	}';
}
/* Popup Search Form */
if($bg_color7 != ''){
	$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li div.popup-search-form .wpmm-overlay-search form label input.search-field{
  	  border: 1px solid '.esc_attr($bg_color7).';
  	}';
}
if( $font_popup_family != '') {
	$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li div.popup-search-form .wpmm-overlay-search{
  	  font-family:'.esc_attr($font_popup_family).';
  	}';
}
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li div.popup-search-form .wpmm-overlay-search form input[type="submit"]{
  	  font-size:'.esc_attr($font_size7).';
  	  color:'.esc_attr($search_button_font_color).';
  	  text-transform:'.esc_attr($search_transform).';
}';
if($search_button_bg_color != ''){
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li div.popup-search-form .wpmm-overlay-search form input[type="submit"]{
  	  background:'.esc_attr($search_button_bg_color).';
  	  border: 3px solid'.esc_attr($search_button_bg_color).';
}';
}
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li div.popup-search-form .wpmm-overlay-search form input[type="submit"]:hover{
  	  background:'.esc_attr($search_button_bg_hovercolor).';
  	  color:'.esc_attr($search_button_font_hovercolor).';
}';
/* search bar custom css end */
/* top menu label custom css */
if($enable_menu_label_bgcolor == 1){
$content .= $maincustom_idwrapper.' .wpmm-mega-menu-label::before{
  	  border-color:'.esc_attr($menu_label_bgcolor).'  transparent transparent;
}';
$content .= $maincustom_idwrapper.' .wpmm-sub-menu-wrap ul.wp-mega-sub-menu li.wp-mega-menu-header a span.wpmm-mega-menu-label{
  	  font-size:'.esc_attr($menu_label_fontsize).';
  	  color:'.esc_attr($menu_label_fontcolor).';
}';
$content .= $maincustom_idwrapper.' .wpmm-mega-menu-label{
  	  font-size:'.esc_attr($menu_label_fontsize).';
  	  color:'.esc_attr($menu_label_fontcolor).';
  	  background:'.esc_attr($menu_label_bgcolor).';
  	  font-weight:'.esc_attr($menu_label_font_weight).';
  	  text-transform:'.esc_attr($menu_label_font_transform).';
  	  font-family:'.esc_attr($menu_label_font_family).';
}';
}


if($htab_content_fcolor != ''){
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap ul.wp-mega-sub-menu li.wpmega-vertical-tabs ul.wpmm-tab-groups > li.wpmm-tabs-section > div.wpmm-sub-menu-wrapper > ul.wpmm-tab-groups-panel > li > a > span.wpmm-mega-menu-href-title,'.$maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap ul.wp-mega-sub-menu li.wpmega-horizontal-tabs ul.wpmm-tab-groups > li.wpmm-tabs-section > div.wpmm-sub-menu-wrapper > ul.wpmm-tab-groups-panel > li > a > span.wpmm-mega-menu-href-title{
  	  color:'.esc_attr($htab_content_fcolor).';
}';
$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap ul.wp-mega-sub-menu li.wpmega-vertical-tabs ul.wpmm-tab-groups > li.wpmm-tabs-section > div.wpmm-sub-menu-wrapper > ul.wpmm-tab-groups-panel > li > a > span.wpmm-mega-menu-href-title:before,'.$maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap ul.wp-mega-sub-menu li.wpmega-horizontal-tabs ul.wpmm-tab-groups > li.wpmm-tabs-section > div.wpmm-sub-menu-wrapper > ul.wpmm-tab-groups-panel > li > a > span.wpmm-mega-menu-href-title:before{
  	  background:'.esc_attr($htab_content_fcolor).';
}';
$content .= $maincustom_idwrapper.' ul.wp-mega-sub-menu li.wpmm-custom-post-settings.wpmega-image-left .wpmm-custom-postimage span.wpmm-mega-menu-href-title,'.$maincustom_idwrapper.' ul.wp-mega-sub-menu li.wpmm-custom-post-settings.wpmega-image-top .wpmm-custom-postimage span.wpmm-mega-menu-href-title{
  	  color:'.esc_attr($htab_content_fcolor).';
}';
}

if($htab_bgcolor != ''){
$content .= $maincustom_idwrapper.' ul li ul.wp-mega-sub-menu > li.wpmega-horizontal-tabs > .wpmm-sub-menu-wrapper > ul > li.wpmm-tabs-section > a.wp-mega-menu-link{
  	  background:'.esc_attr($htab_bgcolor).';
}';
}

if($htab_bg_active_color != '' || $htab_font_hcolor != ''){
	$content .= $maincustom_idwrapper.' ul li ul.wp-mega-sub-menu > li.wpmega-horizontal-tabs > .wpmm-sub-menu-wrapper > ul > li.wpmm-tabs-section.show_tab > a.wp-mega-menu-link{
  	  background:'.esc_attr($htab_bg_active_color).';
  	  color:'.esc_attr($htab_font_hcolor).';
}';
}
if($htab_bg_active_color != ''){
$content .= $maincustom_idwrapper.' ul li ul li.wpmega-horizontal-tabs > div > ul > li.wpmm-tabs-section > .wpmm-sub-menu-wrapper{
  	  border: 2px solid'.esc_attr($htab_bg_active_color).';
}';
}

if($htab_content_bgcolor != ''){
$content .= $maincustom_idwrapper.' ul li ul li.wpmega-horizontal-tabs > div > ul > li.wpmm-tabs-section > .wpmm-sub-menu-wrapper{
  	  background: '.esc_attr($htab_content_bgcolor).';
}';
}

if($htab_font_color != ''){
	$content .= $maincustom_idwrapper.' ul li ul.wp-mega-sub-menu > li.wpmega-horizontal-tabs > .wpmm-sub-menu-wrapper > ul > li.wpmm-tabs-section > a.wp-mega-menu-link{
  	  color: '.esc_attr($htab_font_color).';
}';
}
if($vtab_bgcolor != ''){
		$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap ul.wp-mega-sub-menu li.wpmega-vertical-tabs ul.wpmm-tab-groups > li > a > span{
  	  background: '.esc_attr($vtab_bgcolor).';
}';
}

if($vtab_bghcolor != ''){
	$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap ul.wp-mega-sub-menu li.wpmega-vertical-tabs ul.wpmm-tab-groups > li.show_tab > a > span{
  	  background: '.esc_attr($vtab_bghcolor).';}';
}


if($vtab_activebgcolor != ''){
	$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap ul.wp-mega-sub-menu li.wpmega-vertical-tabs ul.wpmm-tab-groups > li > a:hover:before,'.$maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap ul.wp-mega-sub-menu li.wpmega-vertical-tabs ul.wpmm-tab-groups > li.show_tab > a:before{
  	  background: '.esc_attr($vtab_activebgcolor).';}';
}
if($vtab_fcolor != ''){
	$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap ul.wp-mega-sub-menu li.wpmega-vertical-tabs ul.wpmm-tab-groups > li > a > span{
  	  color: '.esc_attr($vtab_fcolor).';}';
}
if($vtab_font_hcolor != ''){
	$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap ul.wp-mega-sub-menu li.wpmega-vertical-tabs ul.wpmm-tab-groups > li > a:hover span,'.$maincustom_idwrapper.' ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap ul.wp-mega-sub-menu li.wpmega-vertical-tabs ul.wpmm-tab-groups > li > a:hover{
  	  color: '.esc_attr($vtab_font_hcolor).';}';
}

if($tab_width != ''){
	$content .= $maincustom_idwrapper.' ul li ul.wp-mega-sub-menu > li.wpmega-horizontal-tabs > .wpmm-sub-menu-wrapper > ul > li.wpmm-tabs-section > a.wp-mega-menu-link{
  	  width: '.esc_attr($tab_width).'px;}';

}

if($tab_layout == 'flat_layout'){
	$content .= $maincustom_idwrapper.' ul li ul.wp-mega-sub-menu > li.wpmega-horizontal-tabs > .wpmm-sub-menu-wrapper > ul > li.wpmm-tabs-section > a.wp-mega-menu-link,'.$maincustom_idwrapper.' ul li ul.wp-mega-sub-menu > li.wpmega-horizontal-tabs > .wpmm-sub-menu-wrapper > ul > li.wpmm-tabs-section > a.wp-mega-menu-link span{
  	   -ms-transform: none;
	   -webkit-transform: none;
	   transform: none;
  	}';
  	$content .= $maincustom_idwrapper.' ul li ul.wp-mega-sub-menu > li.wpmega-horizontal-tabs > .wpmm-sub-menu-wrapper > ul > li.wpmm-tabs-section:first-child{
  	  margin-left: 0px;
  	}';
}
/* Widget extra options */
if($widget_btnbgcolor != ''){
	$content .= $maincustom_idwrapper.' .wpmm-sub-menu-wrap li .wpmm-text-widgets .wwpmm-linkbtn,'.$maincustom_idwrapper.' .wpmm-sub-menu-wrap li .wpmm-pro-productlist-wrap .all-product-list li .wpmm-second-wrapper .wpmm-woo-content a.button,'.$maincustom_idwrapper.' .wpmm-sub-menu-wrap li .wpmm-pro-recent-product-widget .all-re-product-list li .wpmm-recentpro-right-section a.button{
  	  background: '.esc_attr($widget_btnbgcolor).';
  	  border: 1px solid '.esc_attr($widget_btnbgcolor).';
  	}';
}

if($widget_btnbghcolor != ''){
	$content .= $maincustom_idwrapper.' .wpmm-sub-menu-wrap li .wpmm-text-widgets .wwpmm-linkbtn:hover,'.$maincustom_idwrapper.' .wpmm-sub-menu-wrap li .wpmm-pro-productlist-wrap .all-product-list li .wpmm-second-wrapper .wpmm-woo-content a.button:hover,'.$maincustom_idwrapper.' .wpmm-sub-menu-wrap li .wpmm-pro-recent-product-widget .all-re-product-list li .wpmm-recentpro-right-section a.button:hover{
  	  background: '.esc_attr($widget_btnbgcolor).';
  	  border: 1px solid '.esc_attr($widget_btnbgcolor).';
  	}';
}

if($widget_btnfcolor != ''){
   $content .= $maincustom_idwrapper.' .wpmm-sub-menu-wrap li .wpmm-text-widgets .wwpmm-linkbtn a,'.$maincustom_idwrapper.' .wpmm-sub-menu-wrap li .wpmm-pro-recent-posts-widget a.wpmm-readmore-btn,'.$maincustom_idwrapper.' .wpmm-sub-menu-wrap li .wpmm-pro-productlist-wrap .all-product-list li .wpmm-second-wrapper .wpmm-woo-content a.button,'.$maincustom_idwrapper.' .wpmm-sub-menu-wrap li .wpmm-pro-recent-product-widget .all-re-product-list li .wpmm-recentpro-right-section a.button{
  	  color: '.esc_attr($widget_btnfcolor).' !important;
  	}';
}

if($widget_btnfhcolor != ''){
	$content .= $maincustom_idwrapper.' .wpmm-sub-menu-wrap li .wpmm-text-widgets .wwpmm-linkbtn:hover a,'.$maincustom_idwrapper.' .wpmm-sub-menu-wrap li .wpmm-pro-recent-posts-widget a.wpmm-readmore-btn:hover,'.$maincustom_idwrapper.' .wpmm-sub-menu-wrap li .wpmm-pro-productlist-wrap .all-product-list li .wpmm-second-wrapper .wpmm-woo-content a.button:hover,'.$maincustom_idwrapper.' .wpmm-sub-menu-wrap li .wpmm-pro-recent-product-widget .all-re-product-list li .wpmm-recentpro-right-section a.button:hover{
  	  color: '.esc_attr($widget_btnfhcolor).' !important;
  	}';
}
if($enable_mobile == 1 && $resposive_breakpoint_width != '910' && $resposive_breakpoint_width != '910px'){
    $content .= '/*Responsive Custom Theme Integration CSS Start*/';
	$content .= '
	@media(max-width:'.$resposive_breakpoint_width.'){';
    $content .= '
    .wp-megamenu-main-wrapper.wpmm-ctheme-wrapper .wpmegamenu-toggle{
        display:block;
	}
	.wpmm-ctheme-wrapper .wpmegamenu-toggle .wpmega-closeblock,.wpmm-ctheme-wrapper .wpmegamenu-toggle .menutoggle{
		display:none;
	}
   .wp-megamenu-main-wrapper.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper {
    	overflow: hidden;
    	z-index: 999;
    	display:none;
    }
    .wp-megamenu-main-wrapper.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper.wpmm-show-menu{
		display:block;
	}
    .wp-megamenu-main-wrapper.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper > li {
		width: 100%;
		border-bottom: 1px solid #ccc;
		text-align: left;
		position: relative;
	}
	.wp-megamenu-main-wrapper.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li:last-child {
		border-bottom: none;
	}
	.wp-megamenu-main-wrapper.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li .dropdown-toggle {
		display: none;
	}
	.wp-megamenu-main-wrapper.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper > li > a,
	.wp-megamenu-main-wrapper.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper > li > a.wpmega-searchdown,
	.wp-megamenu-main-wrapper.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper > li > a.wpmega-searchinline {
		padding: 15px 10px;
	}
	.wpmm_megamenu .wp-megamenu-main-wrapper.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper > li > a.wpmega-searchinline,
	.wpmm_megamenu .wp-megamenu-main-wrapper.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper > li > a.wpmm-csingle-menu {
		padding: 15px 10px;
	}
	.wp-megamenu-main-wrapper.wpmega-midnightblue-sky-white.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper > li > a::before {
		display: none;
	}
	.wp-megamenu-main-wrapper.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper > li.menu-item-has-children a {
		margin-right: 0;
	}
	.wpmm-ctheme-wrapper .wpmegamenu-toggle .wpmega-openblock,
	.wpmm-ctheme-wrapper .wpmegamenu-toggle .wpmega-closeblock {
		padding: 10px 10px 13px;
		color: #000;
	}

	.wpmm-ctheme-wrapper .wpmegamenu-toggle .wpmega-openblock,
	.wpmm-ctheme-wrapper .wpmegamenu-toggle .wpmega-closeblock {
		padding: 10px 10px 13px;
		color: #fff;
	}
    .wpmm-ctheme-wrapper.wpmega-clean-white .wpmegamenu-toggle .wpmega-openblock,
	.wpmm-ctheme-wrapper.wpmega-clean-white .wpmegamenu-toggle .wpmega-closeblock {
		color: #000;
	}
	.wpmm-ctheme-wrapper.wpmega-clean-white .wpmegamenu-toggle{
	    border: 1px solid #ccc;
     }

	.wpmm-ctheme-wrapper.wpmm-orientation-vertical.wpmega-clean-white .wpmegamenu-toggle .wpmega-openblock,
	.wpmm-ctheme-wrapper.wpmm-orientation-vertical.wpmega-clean-white .wpmegamenu-toggle .wpmega-closeblock{
      color: #000;
	}

	.wp-megamenu-main-wrapper.wpmm-ctheme-wrapper .wpmm-mega-menu-label {
		top: 50%;
		transform: translateY(-50%);
		-webkit-transform: translateY(-50%);
		-ms-transform: translateY(-50%);
		left: 23%;
	}
	.wp-megamenu-main-wrapper.wpmm-ctheme-wrapper .wpmm-mega-menu-label::before {
		border-color: #d500fb transparent transparent;
		border-style: solid;
		border-width: 7px 4.5px 0;
		bottom: -6px;
		content: "";
		height: 0;
		left: -6px;
		margin-left: auto;
		margin-right: auto;
		position: absolute;
		right: auto;
		top: 50%;
		transform: rotate(90deg) translateX(-50%);
		width: 0;
	}
	.wp-megamenu-main-wrapper.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap {
		transition: none;
		-webkit-transition: none;
		-ms-transition: none;
	}
	.wpmm-ctheme-wrapper .wpmega-responsive-closebtn {
		color: #fff;
		border-top: 1px solid #fff;
		padding: 15px 10px;
		font-weight: 600;
		position: relative;
		padding-left: 30px;
		cursor: pointer;
		z-index: 999999;
		overflow: hidden;
		clear: both;
	}

	.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmm-menu-align-right.wpmm-search-type:hover .wpmm-sub-menu-wrap {
		top: 0;
	}
	.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li .wpmm-search-form .wpmm-search-icon.inline-toggle-right.inline-search.searchbox-open {
		left: auto;
		opacity: 1;
		right: 10px;
	}
	.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout ul {
		width: 100%;
	}
	.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout div,
	.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout div ul li div {
		width: 100%;
		position: relative;
		max-height: 0;
	}
	.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout.active-show > div {
		max-height: 1000px;
	}
	.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout.active-show > div ul li.active-show > div {
    	max-height: 1000px;
    }
    .wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout li.menu-item-has-children > a::after {
    	top: 12px;
    }
    .wpmm_megamenu .wp-megamenu-main-wrapper.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper > li > a.wpmega-searchdown {
    	padding: 15px 10px;
    }
    .wp-megamenu-main-wrapper.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap {
    	position: relative;
    	max-height: 0;
    	transition: all ease 0.1s;
    	-webkit-transition: all ease 0.1s;
    	-ms-transition: all ease 0.1s;
    	padding: 0 8px 0;
    }
    .wp-megamenu-main-wrapper.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.active-show .wpmm-sub-menu-wrap {
    	position: relative;
    	max-height: 10000px;
    	transition: all ease 0.3s;
    	-webkit-transition: all ease 0.3s;
    	-ms-transition: all ease 0.3s;
    	padding: 15px 8px 5px;
    }
    .wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout.wpmega-flyout-horizontal-right ul.wp-mega-sub-menu li.wpmm-submenu-align-left.menu-item-has-children a:after,
    .wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout.wpmega-flyout-horizontal-left ul.wp-mega-sub-menu li.wpmm-submenu-align-left.menu-item-has-children a:after {
    	left: auto;
    	right: 10px;
    	transform: rotate(180deg) !important;
	    -webkit-transform: rotate(180deg) !important;
	    -ms-transform: rotate(180deg) !important;
    }
    .wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout.wpmega-flyout-horizontal-right ul.wp-mega-sub-menu li.wpmm-submenu-align-left.menu-item-has-children a.wp-mega-menu-link {
    	padding-left: 10px;
    }
    .wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout ul.wp-mega-sub-menu li a {
    	padding-left: 20px !important;
    }
    .wp-megamenu-main-wrapper.wpmm-onclick.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout > div {
    	overflow: hidden;
    	height: 0;
    }
    .wp-megamenu-main-wrapper.wpmm-onclick.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout > div.wpmm-open-fade {
    	height: 100%;
    	z-index: 999;
    }
    .wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout.wpmega-flyout-horizontal-left div ul li div {
    	right: 0;
    }
    .wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout div {
    	z-index: 999;
    }
    .wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout.wpmega-flyout-horizontal-left div ul li.wpmm-submenu-align-right div {
    	left: 0;
    }
    .wpmm_megamenu ul.wpmm-mega-wrapper.wpmm-ctheme-wrapper li.wpmega-hide-on-mobile {
		display: none;
	}
	.wp-megamenu-main-wrapper.wpmm-orientation-vertical.wpmm-ctheme-wrapper {
		width: 100%;
	}
	.wp-megamenu-main-wrapper.wpmm-orientation-vertical.wpmm-ctheme-wrapper .wp-mega-toggle-block {
		color: #fff;
	}
	.wp-megamenu-main-wrapper.wpmm-orientation-vertical.wpmm-ctheme-wrapper .wp-mega-toggle-block .wpmega-openblock,
	.wp-megamenu-main-wrapper.wpmm-orientation-vertical.wpmm-ctheme-wrapper .wp-mega-toggle-block .wpmega-closeblock {
		padding: 10px 10px 13px;
	}
	.wp-megamenu-main-wrapper.wpmm-orientation-vertical.wpmm-ctheme-wrapper .wp-mega-toggle-block .dashicons {
		font-size: 26px;
	}
	.wp-megamenu-main-wrapper.wpmm-orientation-vertical.wpmm-ctheme-wrapper .wp-mega-toggle-block .menutoggle {
		display: none;
	}
	.wpmm-orientation-vertical.wpmm-ctheme-wrapper .wpmega-responsive-closebtn {
	    color: #fff;
	    border-top: 1px solid #fff;
	    padding: 10px;
	    font-weight: 600;
	    position: relative;
	    padding-left: 10px;
	    cursor: pointer;
	    z-index: 999999;
	}
	.wp-megamenu-main-wrapper.wpmm-orientation-vertical.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap {
    	<!-- position: relative; -->
    	max-height: 0;
    	transition: all ease 0.1s;
    	-webkit-transition: all ease 0.1s;
    	-ms-transition: all ease 0.1s;
    	padding: 0 8px 0;
    	left: 0;
    	width: 100% !important;
    	right: 0;
    }
    .wp-megamenu-main-wrapper.wpmm-orientation-vertical.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.active-show .wpmm-sub-menu-wrap {
    	position: relative;
    	max-height: 10000px;
    	transition: all ease 0.3s;
    	-webkit-transition: all ease 0.3s;
    	-ms-transition: all ease 0.3s;
    	padding: 15px 8px 5px;
    }
    .wpmm-orientation-vertical.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout div {
    	left: 0;
    }
    .wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li .wpmm-search-form .wpmm-search-icon.inline-toggle-left.inline-search.searchbox-open {
    	left: 40px;
    	top: 27px;
    }
    .wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap ul.wp-mega-sub-menu li.wpmega-vertical-tabs ul.wpmm-tab-groups > li.wpmm-tabs-section > div.wpmm-sub-menu-wrapper > ul.wpmm-tab-groups-panel > li {
        width: 49%;
        padding: 0;
        margin: 0 0 10px;
    }
    .wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap ul.wp-mega-sub-menu li.wpmega-vertical-tabs ul.wpmm-tab-groups > li.wpmm-tabs-section > div.wpmm-sub-menu-wrapper > ul.wpmm-tab-groups-panel > li:nth-child(even) {
        margin-left: 1%;
    }
     .wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout.wpmega-flyout-horizontal-right div ul li.wpmm-submenu-align-left div{
        right:0;
    }
    .wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout.wpmega-flyout-horizontal-right div ul li div{
        left:0;
    }
    /*=============
    slide on click for responsive
    ==============*/
    .wp-megamenu-main-wrapper.wpmm-ctheme-wrapper.wpmm-slide ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap,
    .wp-megamenu-main-wrapper.wpmm-ctheme-wrapper.wpmm-slide ul.wpmm-mega-wrapper li.wpmega-horizontal-left-edge .wpmm-sub-menu-wrap,
    .wp-megamenu-main-wrapper.wpmm-ctheme-wrapper.wpmm-slide ul.wpmm-mega-wrapper li.wpmega-horizontal-center .wpmm-sub-menu-wrap {
    	left: 0;
    }
    .wp-megamenu-main-wrapper.wpmm-ctheme-wrapper.wpmm-slide ul.wpmm-mega-wrapper li .wpmm-sub-menu-wrap {
    	position: static;
    	padding: 0 8px;
    }
    .wp-megamenu-main-wrapper.wpmm-ctheme-wrapper.wpmm-slide ul.wpmm-mega-wrapper li:hover .wpmm-sub-menu-wrap {
		opacity: 0;
		visibility: hidden;
		max-height: 0;
		padding: 0 8px;
    }
    .wp-megamenu-main-wrapper.wpmm-ctheme-wrapper.wpmm-slide.wpmm-onclick ul.wpmm-mega-wrapper li.active-show .wpmm-sub-menu-wrap {
		opacity: 1;
		visibility: visible;
		max-height: 10000px;
		z-index: 999;
		transition: all 0.4s ease-in;
		-webkit-transition: all 0.4s ease-in;
		-ms-transition: all 0.4s ease-in;
		padding: 15px 8px 5px;
    }
    .wp-megamenu-main-wrapper.wpmm-onclick.wpmm-ctheme-wrapper ul.wpmm-mega-wrapper li.wpmega-menu-flyout.active-show > div {
    	overflow: visible;
    }
    .main-navigation button.menu-toggle{
			display: none;
	}';

 if($togglebar_enable_bgcolor == 1){
 	$content .= $maincustom_idwrapper.' .wpmegamenu-toggle{
  	  height: '.esc_attr($togglebar_height).';
  	  background: '.esc_attr($togglebar_background_from).';
  	  text-align: '.esc_attr($togglebar_align).';
  	}';
  }

 	$content .= $maincustom_idwrapper.' .wpmegamenu-toggle .wpmega-openblock,'.$maincustom_idwrapper.' .wpmegamenu-toggle .wpmega-closeblock{
  	  color: '.esc_attr($icon_color).';
  	  background: '.esc_attr($togglebar_background_from).';
  	}';
   $content .= $maincustom_idwrapper.' .close-primary{
  	  color: '.esc_attr($text_color).';
  	}';
  	$content .= $maincustom_idwrapper.',.wpmm-orientation-horizontal,'.$maincustom_idwrapper.' ul.wpmm-mega-wrapper{
  	  background: '.esc_attr($menu_background_from).';
  	}';
  	$content .= $maincustom_idwrapper.' .wpmm-orientation-horizontal ul.wpmm-mega-wrapper > li > a,.wpmm-orientation-horizontal,'.$maincustom_idwrapper.' .wpmm-orientation-vertical ul.wpmm-mega-wrapper > li > a{
  	  color: '.esc_attr($font_color).';
  	}';
  	$content .= $maincustom_idwrapper.' ul.wpmm-mega-wrapper > li:hover > a,'.$maincustom_idwrapper.' .wpmm-orientation-vertical ul.wpmm-mega-wrapper > li > a{
  	  font-weight: '.esc_attr($font_weight_hover1).';
  	  text-decoration: '.esc_attr($font_decoration_hover1).';
  	  color: '.esc_attr($font_hover_color).';
  	}';
    $content .= $maincustom_idwrapper.' .wpmm-sub-menu-wrap ul li > a{
  	  padding: 0px;
  	}';
	$content .= '}';

   $content .= '
	@media screen and (max-width:'.$resposive_breakpoint_width.'){';
   $content .= '.wpmm-sub-menu-wrap li.wpmega-1columns-1total, .wpmm-sub-menu-wrap li.wpmega-1columns-2total, .wpmm-sub-menu-wrap li.wpmega-1columns-3total, .wpmm-sub-menu-wrap li.wpmega-1columns-4total, .wpmm-sub-menu-wrap li.wpmega-1columns-5total, .wpmm-sub-menu-wrap li.wpmega-1columns-6total, .wpmm-sub-menu-wrap li.wpmega-1columns-7total, .wpmm-sub-menu-wrap li.wpmega-1columns-8total, .wpmm-sub-menu-wrap li.wpmega-2columns-2total, .wpmm-sub-menu-wrap li.wpmega-2columns-3total, .wpmm-sub-menu-wrap li.wpmega-3columns-3total, .wpmm-sub-menu-wrap li.wpmega-2columns-4total, .wpmm-sub-menu-wrap li.wpmega-3columns-4total, .wpmm-sub-menu-wrap li.wpmega-4columns-4total, .wpmm-sub-menu-wrap li.wpmega-2columns-5total, .wpmm-sub-menu-wrap li.wpmega-3columns-5total, .wpmm-sub-menu-wrap li.wpmega-4columns-5total, .wpmm-sub-menu-wrap li.wpmega-5columns-5total, .wpmm-sub-menu-wrap li.wpmega-2columns-6total, .wpmm-sub-menu-wrap li.wpmega-3columns-6total, .wpmm-sub-menu-wrap li.wpmega-4columns-6total, .wpmm-sub-menu-wrap li.wpmega-5columns-6total, .wpmm-sub-menu-wrap li.wpmega-6columns-6total, .wpmm-sub-menu-wrap li.wpmega-2columns-7total, .wpmm-sub-menu-wrap li.wpmega-3columns-7total, .wpmm-sub-menu-wrap li.wpmega-4columns-7total, .wpmm-sub-menu-wrap li.wpmega-5columns-7total, .wpmm-sub-menu-wrap li.wpmega-5columns-7total, .wpmm-sub-menu-wrap li.wpmega-6columns-7total, .wpmm-sub-menu-wrap li.wpmega-7columns-7total, .wpmm-sub-menu-wrap li.wpmega-2columns-8total, .wpmm-sub-menu-wrap li.wpmega-3columns-8total, .wpmm-sub-menu-wrap li.wpmega-4columns-8total, .wpmm-sub-menu-wrap li.wpmega-5columns-8total, .wpmm-sub-menu-wrap li.wpmega-6columns-8total, .wpmm-sub-menu-wrap li.wpmega-7columns-8total, .wpmm-sub-menu-wrap li.wpmega-8columns-8total{
      width:100%;
      padding: 0 10px;}';
   $content .= '}';
}
