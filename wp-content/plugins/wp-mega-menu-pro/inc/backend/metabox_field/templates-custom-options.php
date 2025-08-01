<?php
/*echo "<pre>";
print_r($menu_general_settings);*/

$enable_cdesigns = (isset($menu_general_settings[$mlocation]['enable_cdesigns']) && $menu_general_settings[$mlocation]['enable_cdesigns'] == '1')?'1':'0';
$bgcolor = (isset($menu_general_settings[$mlocation]['bgcolor']) && $menu_general_settings[$mlocation]['bgcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['bgcolor']):'';
$menu_bordercolor = (isset($menu_general_settings[$mlocation]['menu_bordercolor']) && $menu_general_settings[$mlocation]['menu_bordercolor'] != '')?esc_attr($menu_general_settings[$mlocation]['menu_bordercolor']):'';
$topmenubordercolor = (isset($menu_general_settings[$mlocation]['topmenubordercolor']) && $menu_general_settings[$mlocation]['topmenubordercolor'] != '')?esc_attr($menu_general_settings[$mlocation]['topmenubordercolor']):'';
$divider_color = (isset($menu_general_settings[$mlocation]['divider_color']) && $menu_general_settings[$mlocation]['divider_color'] != '')?esc_attr($menu_general_settings[$mlocation]['divider_color']):'';
$menu_fontsize = (isset($menu_general_settings[$mlocation]['menu_fontsize']) && $menu_general_settings[$mlocation]['menu_fontsize'] != '')?esc_attr($menu_general_settings[$mlocation]['menu_fontsize']):'';
$menu_font_family = (isset($menu_general_settings[$mlocation]['menu_font_family']) && $menu_general_settings[$mlocation]['menu_font_family'] != '')?esc_attr($menu_general_settings[$mlocation]['menu_font_family']):'';
$menu_font_weight = (isset($menu_general_settings[$mlocation]['menu_font_weight']) && $menu_general_settings[$mlocation]['menu_font_weight'] != '')?esc_attr($menu_general_settings[$mlocation]['menu_font_weight']):'';
$menu_fcolor = (isset($menu_general_settings[$mlocation]['menu_fcolor']) && $menu_general_settings[$mlocation]['menu_fcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['menu_fcolor']):'';
//active bg color
$active_bgcolor = (isset($menu_general_settings[$mlocation]['active_bgcolor']) && $menu_general_settings[$mlocation]['active_bgcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['active_bgcolor']):'';
$active_fcolor = (isset($menu_general_settings[$mlocation]['active_fcolor']) && $menu_general_settings[$mlocation]['active_fcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['active_fcolor']):'';

$padding_top = (isset($menu_general_settings[$mlocation]['padding_top']) && $menu_general_settings[$mlocation]['padding_top'] != '')?esc_attr($menu_general_settings[$mlocation]['padding_top']):'';
$padding_left = (isset($menu_general_settings[$mlocation]['padding_left']) && $menu_general_settings[$mlocation]['padding_left'] != '')?esc_attr($menu_general_settings[$mlocation]['padding_left']):'';
$padding_right = (isset($menu_general_settings[$mlocation]['padding_right']) && $menu_general_settings[$mlocation]['padding_right'] != '')?esc_attr($menu_general_settings[$mlocation]['padding_right']):'';
$padding_bottom = (isset($menu_general_settings[$mlocation]['padding_bottom']) && $menu_general_settings[$mlocation]['padding_bottom'] != '')?esc_attr($menu_general_settings[$mlocation]['padding_bottom']):'';
$margin_top = (isset($menu_general_settings[$mlocation]['margin_top']) && $menu_general_settings[$mlocation]['margin_top'] != '')?esc_attr($menu_general_settings[$mlocation]['margin_top']):'';
$margin_left = (isset($menu_general_settings[$mlocation]['margin_left']) && $menu_general_settings[$mlocation]['margin_left'] != '')?esc_attr($menu_general_settings[$mlocation]['margin_left']):'';
$margin_right = (isset($menu_general_settings[$mlocation]['margin_right']) && $menu_general_settings[$mlocation]['margin_right'] != '')?esc_attr($menu_general_settings[$mlocation]['margin_right']):'';
$margin_bottom = (isset($menu_general_settings[$mlocation]['margin_bottom']) && $menu_general_settings[$mlocation]['margin_bottom'] != '')?esc_attr($menu_general_settings[$mlocation]['margin_bottom']):'';
$padding_top_noicon = (isset($menu_general_settings[$mlocation]['padding_top_noicon']) && $menu_general_settings[$mlocation]['padding_top_noicon'] != '')?esc_attr($menu_general_settings[$mlocation]['padding_top_noicon']):'';
$padding_left_noicon = (isset($menu_general_settings[$mlocation]['padding_left_noicon']) && $menu_general_settings[$mlocation]['padding_left_noicon'] != '')?esc_attr($menu_general_settings[$mlocation]['padding_left_noicon']):'';
$padding_right_noicon = (isset($menu_general_settings[$mlocation]['padding_right_noicon']) && $menu_general_settings[$mlocation]['padding_right_noicon'] != '')?esc_attr($menu_general_settings[$mlocation]['padding_right_noicon']):'';
$padding_bottom_noicon = (isset($menu_general_settings[$mlocation]['padding_bottom_noicon']) && $menu_general_settings[$mlocation]['padding_bottom_noicon'] != '')?esc_attr($menu_general_settings[$mlocation]['padding_bottom_noicon']):'';
//social icon menu type
$sicon_margin_top = (isset($menu_general_settings[$mlocation]['sicon_margin_top']) && $menu_general_settings[$mlocation]['sicon_margin_top'] != '')?esc_attr($menu_general_settings[$mlocation]['sicon_margin_top']):'';
$sicon_margin_left = (isset($menu_general_settings[$mlocation]['sicon_margin_left']) && $menu_general_settings[$mlocation]['sicon_margin_left'] != '')?esc_attr($menu_general_settings[$mlocation]['sicon_margin_left']):'';
$sicon_margin_right = (isset($menu_general_settings[$mlocation]['sicon_margin_right']) && $menu_general_settings[$mlocation]['sicon_margin_right'] != '')?esc_attr($menu_general_settings[$mlocation]['sicon_margin_right']):'';
$sicon_margin_bottom = (isset($menu_general_settings[$mlocation]['sicon_margin_bottom']) && $menu_general_settings[$mlocation]['sicon_margin_bottom'] != '')?esc_attr($menu_general_settings[$mlocation]['sicon_margin_bottom']):'';
$sicon_padding_top = (isset($menu_general_settings[$mlocation]['sicon_padding_top']) && $menu_general_settings[$mlocation]['sicon_padding_top'] != '')?esc_attr($menu_general_settings[$mlocation]['sicon_padding_top']):'';
$sicon_padding_left = (isset($menu_general_settings[$mlocation]['sicon_padding_left']) && $menu_general_settings[$mlocation]['sicon_padding_left'] != '')?esc_attr($menu_general_settings[$mlocation]['sicon_padding_left']):'';
$sicon_padding_right = (isset($menu_general_settings[$mlocation]['sicon_padding_right']) && $menu_general_settings[$mlocation]['sicon_padding_right'] != '')?esc_attr($menu_general_settings[$mlocation]['sicon_padding_right']):'';
$sicon_padding_bottom = (isset($menu_general_settings[$mlocation]['sicon_padding_bottom']) && $menu_general_settings[$mlocation]['sicon_padding_bottom'] != '')?esc_attr($menu_general_settings[$mlocation]['sicon_padding_bottom']):'';
?>
<div class="wpmm-custom-options-wrap">
	<div class="wpmm-template-tab-leftsection">
	     <div class="widgetss_header">
	     	<div class="wpmm-label clearfix">
          	<label for="apmegamenu_enabledcdesign_<?php echo $mlocation;?>">Enable Custom Designs?</label>
          	<div class="wpmm-switch">
		        <input type='checkbox' class='apmegamenu_enabled' 
		        name='apmegamenu_custommeta[<?php echo $mlocation;?>][enable_cdesigns]' id="apmegamenu_enabledcdesign_<?php echo $mlocation;?>" value='1' <?php checked( $enable_cdesigns ); ?>/>
		        <label for="apmegamenu_enabledcdesign_<?php echo $mlocation;?>"></label>
		      </div>
          	</div>
	     </div>
	      <ul>
          <li><div class="wpmm_tabss active" id="menubar"><?php _e("Menu Bar Settings", APMM_PRO_TD) ?></div></li>
          <li><div class="wpmm_tabss" id="icons"><?php _e("Menu Icons Settings", APMM_PRO_TD) ?></div></li>
	        <li><div class="wpmm_tabss" id="flyout"><?php _e("Flyout Menu Settings", APMM_PRO_TD) ?></div></li>
	        <li><div class="wpmm_tabss" id="megamenu"><?php _e("Mega Menu Settings", APMM_PRO_TD) ?></div></li>
	        <li><div class="wpmm_tabss" id="menu_label"><?php _e("Menu Label Settings", APMM_PRO_TD) ?></div></li> 
	      </ul>
	</div>
	<div class="widget_right_section">
      <div class="wpmm-btn_close_me btn_close_me">
        <div class="title_widget_add">
	        <div class="rt-header">
	     	<?php _e("Menu Location - ", APMM_PRO_TD) ?> <?php 
	     	$menulocation = str_replace('_', ' ', $mlocation);
	     	$menulocation = str_replace('-', ' ', $menulocation);
	     	echo ucwords($menulocation); ?>
	       	<i class="fa fa-wrench" aria-hidden="true"></i><?php _e("Customize Pre Available Template Options", APMM_PRO_TD); ?></div>          
	        </div>
        <div class="wpmm-rt-side-action">
          <span class="wpmm-load-data" style="display: none;"><span class="wpmm_saving_message"></span><img src='<?php echo APMM_PRO_IMG_DIR;?>/ajaxloader.gif'/></span>
          <span class="wpmm-save-data"><i class="fa fa-save" aria-hidden="true"></i><?php _e("SAVE CHANGES", APMM_PRO_TD) ?></span>
          <span class="wpmm-close-wrap"><i class="fa fa-close" aria-hidden="true"></i><?php _e("CLOSE", APMM_PRO_TD) ?></span>
        </div>
       </div>
      <div class="right_middle_widgets">
        <div class="tab-panes" id="tabs_menubar" style="display: block;">
          <p class="description"><?php _e("<b>Note:</b> Some of the options will only work as per pre available templates design layout setup. So, few custom option selected might not work for some pre available templates.", APMM_PRO_TD) ?> </p>
           <table>
             <tr>
               <td width="400"><?php _e("Menu Background Color", APMM_PRO_TD) ?> 
                 <p class="description">
                  <?php _e("Set overall background color for menu bar.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][bgcolor]' 
                value="<?php echo $bgcolor;?>" class="apmm-color-picker" data-alpha="true"/>
              </td>
             </tr>
              <tr>
               <td width="400"><?php _e("Border Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set overall border color for menu bar.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][menu_bordercolor]' 
                value="<?php echo $menu_bordercolor;?>" class="apmm-color-picker"/>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Menu Top Border Color", APMM_PRO_TD) ?> 
                 <p class="description">
                  <?php _e("Set overall top border color of menu bar. For Unique Mega Menu template only.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][topmenubordercolor]' 
                value="<?php echo $topmenubordercolor;?>" class="apmm-color-picker"/>
              </td>
             </tr>
               <tr>
               <td width="400"><?php _e("Divider Color", APMM_PRO_TD) ?>
                  <p class="description">
                  <?php _e("Set overall divider color for menu bar.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][divider_color]' 
                value="<?php echo $divider_color;?>" class="apmm-color-picker"/>
              </td>
             </tr>
               <tr>
               <td width="400"><?php _e("Font Family", APMM_PRO_TD) ?>
                  <p class="description">
                  <?php _e("Choose font family for overall menu.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <select name="apmegamenu_custommeta[<?php echo $mlocation;?>][menu_font_family]">
                    <option value="theme-default">Default</option>
                    <option value="Assistant" <?php if(isset($menu_font_family) && $menu_font_family == 'Assistant') echo "selected";?>>Assistant</option>
                    <?php  $apmm_fonts = get_option('apmm_font_family');
                     if(isset($apmm_fonts) && !empty($apmm_fonts)){
                      foreach ($apmm_fonts as $value) {
                        ?>
                    <option value="<?php echo $value;?>" <?php if(isset($menu_font_family)) if($value == $menu_font_family ) echo "selected";?>><?php echo $value;?></option>
                     <?php } 
                     }?>
                  </select>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Font Weight", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Choose font weight for overall menu.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <select name="apmegamenu_custommeta[<?php echo $mlocation;?>][menu_font_weight]">
                     <option value="theme_default" <?php echo (isset($menu_font_weight) && $menu_font_weight == 'theme_default')?'selected="selected"':'';?>><?php _e('Template Default',APMM_PRO_TD);?></option>
                     <option value="normal" <?php echo (isset($menu_font_weight) && $menu_font_weight == 'normal')?'selected="selected"':'';?>><?php _e('Normal(400)',APMM_PRO_TD);?></option>
                     <option value="bold" <?php echo (isset($menu_font_weight) && $menu_font_weight == 'bold')?'selected="selected"':'';?>><?php _e('Bold(700)',APMM_PRO_TD);?></option>
                     <option value="light" <?php echo (isset($menu_font_weight) && $menu_font_weight == 'light')?'selected="selected"':'';?>><?php _e('Light(300)',APMM_PRO_TD);?></option>
                  </select>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Menu Font Size", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set font size for all menu items.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <input type='number'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][menu_fontsize]' 
                value="<?php echo $menu_fontsize;?>"/>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Menu Font Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set font color for all menu items.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][menu_fcolor]' 
                value="<?php echo $menu_fcolor;?>" class="apmm-color-picker"/>
              </td>
             </tr>
            <tr>
               <td width="400"><?php _e("Active Background Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set Active menu item Background color.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][active_bgcolor]' 
                value="<?php echo $active_bgcolor;?>" class="apmm-color-picker" data-alpha="true"/>
              </td>
             </tr>
              <tr>
               <td width="400"><?php _e("Active Font Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set Active menu item font color.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][active_fcolor]' 
                value="<?php echo $active_fcolor;?>" class="apmm-color-picker"/>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Menu Item Margin", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set margin for overall main menu bar's items. Not applicable for social custom menu item type.", APMM_PRO_TD); ?>
                 </p>
               </td>
               <td>  
                <label data-validation="px" class="ap-mega_container-padding">
                <span><?php _e('Top',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($margin_top))?'':esc_attr($margin_top);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][margin_top]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                 <span><?php _e('Right',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($margin_right))?'':esc_attr($margin_right);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][margin_right]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                <span><?php _e('Bottom',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($margin_bottom))?'':esc_attr($margin_bottom);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][margin_bottom]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                <span><?php _e('Left',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($margin_left))?'':esc_attr($margin_left);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][margin_left]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                </label>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Menu Item Padding With Icon", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set padding for overall main menu bar's items which have menu icon. Not applicable for social custom menu item type.", APMM_PRO_TD); ?>
                 </p>
               </td>
               <td>  
                <label data-validation="px" class="ap-mega_container-padding">
                <span><?php _e('Top',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($padding_top))?'':esc_attr($padding_top);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][padding_top]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                 <span><?php _e('Right',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($padding_right))?'':esc_attr($padding_right);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][padding_right]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                <span><?php _e('Bottom',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($padding_bottom))?'':esc_attr($padding_bottom);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][padding_bottom]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                <span><?php _e('Left',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($padding_left))?'':esc_attr($padding_left);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][padding_left]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                </label>
              </td>
             </tr>

          <tr>
               <td width="400"><?php _e("Menu Item Padding Without Icon ", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set padding for overall main menu bar's items which doesn't have menu icon. Not applicable for social custom menu item type.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <label data-validation="px" class="ap-mega_container-padding">
                <span><?php _e('Top',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($padding_top_noicon))?'':esc_attr($padding_top_noicon);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][padding_top_noicon]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                 <span><?php _e('Right',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($padding_right_noicon))?'':esc_attr($padding_right_noicon);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][padding_right_noicon]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                <span><?php _e('Bottom',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($padding_bottom_noicon))?'':esc_attr($padding_bottom_noicon);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][padding_bottom_noicon]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                <span><?php _e('Left',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($padding_left_noicon))?'':esc_attr($padding_left_noicon);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][padding_left_noicon]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                </label>
              </td>
             </tr>

          <tr>
               <td width="400"><?php _e("Social Menu Item's Margin", APMM_PRO_TD) ?>
                 <p class="description">
                 <?php _e("Set margin for menu items assigned as custom social links menu item type. Social Icon Menu Type is set by enabling General Settings > `Active Single Menu` on click blue button which opens a popup form for each menu item individually.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <label data-validation="px" class="ap-mega_container-padding">
                <span><?php _e('Top',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($sicon_margin_top))?'':esc_attr($sicon_margin_top);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][sicon_margin_top]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                 <span><?php _e('Right',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($sicon_margin_right))?'':esc_attr($sicon_margin_right);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][sicon_margin_right]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                <span><?php _e('Bottom',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($sicon_margin_bottom))?'':esc_attr($sicon_margin_bottom);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][sicon_margin_bottom]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                <span><?php _e('Left',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($sicon_margin_left))?'':esc_attr($sicon_margin_left);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][sicon_margin_left]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                </label>
              </td>
             </tr>

            <tr>
               <td width="400"><?php _e("Social Menu Item's Padding", APMM_PRO_TD) ?>
                <p class="description">
                 <?php _e("Set padding for menu items assigned as custom social links menu item type. Social Icon Menu Type is set by enabling General Settings > `Active Single Menu` on click blue button which opens a popup form for each menu item individually.", APMM_PRO_TD); ?> 
                 </p>
               </td>
               <td>  
                <label data-validation="px" class="ap-mega_container-padding">
                <span><?php _e('Top',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($sicon_padding_top))?'':esc_attr($sicon_padding_top);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][sicon_padding_top]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                 <span><?php _e('Right',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($sicon_padding_right))?'':esc_attr($sicon_padding_right);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][sicon_padding_right]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                <span><?php _e('Bottom',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($sicon_padding_bottom))?'':esc_attr($sicon_padding_bottom);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][sicon_padding_bottom]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                <span><?php _e('Left',APMM_PRO_TD);?></span>
                <input type="text" value="<?php echo (!isset($sicon_padding_left))?'':esc_attr($sicon_padding_left);?>" name="apmegamenu_custommeta[<?php echo $mlocation;?>][sicon_padding_left]" 
                class="apmega-menu_bar_padding" placeholder="23px">
                </label>
              </td>
             </tr>
        </table>
        <?php
        $eachmenu_bgcolor = (isset($menu_general_settings[$mlocation]['eachmenu_bgcolor']) && $menu_general_settings[$mlocation]['eachmenu_bgcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['eachmenu_bgcolor']):'';
        $eachmenu_fhcolor = (isset($menu_general_settings[$mlocation]['eachmenu_fhcolor']) && $menu_general_settings[$mlocation]['eachmenu_fhcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['eachmenu_fhcolor']):'';
        $eachmenu_topbordercolor = (isset($menu_general_settings[$mlocation]['eachmenu_topbordercolor']) && $menu_general_settings[$mlocation]['eachmenu_topbordercolor'] != '')?esc_attr($menu_general_settings[$mlocation]['eachmenu_topbordercolor']):'';
        $megamenu_topborder = (isset($menu_general_settings[$mlocation]['megamenu_topborder']) && $menu_general_settings[$mlocation]['megamenu_topborder'] != '')?esc_attr($menu_general_settings[$mlocation]['megamenu_topborder']):'';
        ?>
        <div class="wpmm-title-header-opt">Hover Effect On Top Level Menu Items</div>
        <table> 
              <tr>
               <td width="400"><?php _e("Background Hover Color", APMM_PRO_TD) ?>
                  <p class="description">
                  <?php _e("Set background hover color for top level menu items .", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][eachmenu_bgcolor]' 
                value="<?php echo $eachmenu_bgcolor;?>" class="apmm-color-picker" data-alpha="true"/>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Font Hover Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set font hover color for all menu items.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][eachmenu_fhcolor]' 
                value="<?php echo $eachmenu_fhcolor;?>" class="apmm-color-picker"/>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Top Border Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set top border color on hover menu items. Only for specific template type - Modern Mega Menu.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][eachmenu_topbordercolor]' 
                value="<?php echo $eachmenu_topbordercolor;?>" class="apmm-color-picker"/>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Border Circle Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set border circle color on hover each menu items. Only for specific template type - Advanced Magazine Mega Menu and Mini Sized Mega Menu.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][eachmenu_border_circlecolor]' 
                value="<?php echo $eachmenu_border_circlecolor;?>" class="apmm-color-picker"/>
              </td>
             </tr>
              <tr>
               <td width="400"><?php _e("Mega Menu's Top Border Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set mega menu wrapper's top border color on hover for each menu items. Only for specific template type -Simple Mega Menu,Advanced Magazine Mega Menu and Highlighted Border Mega Menu.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][megamenu_topborder]' 
                value="<?php echo $megamenu_topborder;?>" class="apmm-color-picker"/>
              </td>
             </tr>
           </table>

        </div>
        <?php
        $iconcolor = (isset($menu_general_settings[$mlocation]['iconcolor']) && $menu_general_settings[$mlocation]['iconcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['iconcolor']):'';
        $icon_hcolor = (isset($menu_general_settings[$mlocation]['icon_hcolor']) && $menu_general_settings[$mlocation]['icon_hcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['icon_hcolor']):'';
        $icon_bgcolor = (isset($menu_general_settings[$mlocation]['icon_bgcolor']) && $menu_general_settings[$mlocation]['icon_bgcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['icon_bgcolor']):'';
        $icon_bghcolor = (isset($menu_general_settings[$mlocation]['icon_bghcolor']) && $menu_general_settings[$mlocation]['icon_bghcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['icon_bghcolor']):'';
        ?>
        <div class="tab-panes" id="tabs_icons" style="display:none;">
          <table> 
              <tr>
               <td width="400"><?php _e("Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set menu icon color for top level menu items.", APMM_PRO_TD) ?> 
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][iconcolor]' 
                value="<?php echo $iconcolor;?>" class="apmm-color-picker"/>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Hover Color", APMM_PRO_TD) ?>
                  <p class="description">
                  <?php _e("Set icon hover color for top level menu items.", APMM_PRO_TD) ?>
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][icon_hcolor]' 
                value="<?php echo $icon_hcolor;?>" class="apmm-color-picker"/>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Background Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set icon background color for top level menu items. Only used for pre available template type Box Sized Mega Menu", APMM_PRO_TD) ?>
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][icon_bgcolor]' 
                value="<?php echo $icon_bgcolor;?>" class="apmm-color-picker" data-alpha="true"/>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Background Hover Color", APMM_PRO_TD) ?>
                  <p class="description">
                  <?php _e("Set icon background hover color for top level menu items. Only used for pre available template type Box Sized Mega Menu", APMM_PRO_TD) ?>
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][icon_bghcolor]' 
                value="<?php echo $icon_bghcolor;?>" class="apmm-color-picker"/>
              </td>
             </tr>
           </table>
        </div>

        <div class="tab-panes" id="tabs_megamenu" style="display:none;">
        <?php
        $headertitle_fcolor = (isset($menu_general_settings[$mlocation]['headertitle_fcolor']) && $menu_general_settings[$mlocation]['headertitle_fcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['headertitle_fcolor']):'';
        $underline_color = (isset($menu_general_settings[$mlocation]['underline_color']) && $menu_general_settings[$mlocation]['underline_color'] != '')?esc_attr($menu_general_settings[$mlocation]['underline_color']):'';
        $header_title_bgcolor = (isset($menu_general_settings[$mlocation]['header_title_bgcolor']) && $menu_general_settings[$mlocation]['header_title_bgcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['header_title_bgcolor']):'';
        $header_title_fsize = (isset($menu_general_settings[$mlocation]['header_title_fsize']) && $menu_general_settings[$mlocation]['header_title_fsize'] != '')?intval($menu_general_settings[$mlocation]['header_title_fsize']):'';

        $submenu_fsize = (isset($menu_general_settings[$mlocation]['submenu_fsize']) && $menu_general_settings[$mlocation]['submenu_fsize'] != '')?intval($menu_general_settings[$mlocation]['submenu_fsize']):'';
        $submenu_fcolor = (isset($menu_general_settings[$mlocation]['submenu_fcolor']) && $menu_general_settings[$mlocation]['submenu_fcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['submenu_fcolor']):'';
        $submenu_fhcolor = (isset($menu_general_settings[$mlocation]['submenu_fhcolor']) && $menu_general_settings[$mlocation]['submenu_fhcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['submenu_fhcolor']):'';
        $submenu_border_ucolor = (isset($menu_general_settings[$mlocation]['submenu_border_ucolor']) && $menu_general_settings[$mlocation]['submenu_border_ucolor'] != '')?esc_attr($menu_general_settings[$mlocation]['submenu_border_ucolor']):'';
        $submenu_bghcolor = (isset($menu_general_settings[$mlocation]['submenu_bghcolor']) && $menu_general_settings[$mlocation]['submenu_bghcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['submenu_bghcolor']):'';
        $megamenu_bgcolor = (isset($menu_general_settings[$mlocation]['megamenu_bgcolor']) && $menu_general_settings[$mlocation]['megamenu_bgcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['megamenu_bgcolor']):'';
        ?>
          <table>
              <tr>
               <td width="400"><?php _e("Header Title Font Color", APMM_PRO_TD) ?><p class="description">
                  <?php _e("Set mega menu's content header title font color. Useful for simple-mm-template", APMM_PRO_TD) ?>
                 </p></td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][headertitle_fcolor]' 
                value="<?php echo $headertitle_fcolor;?>" class="apmm-color-picker"/>
              </td>
             </tr>

             <tr>
               <td width="400"><?php _e("Underline Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set mega menu's content header title below underline color.", APMM_PRO_TD) ?>
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][underline_color]' 
                value="<?php echo $underline_color;?>" class="apmm-color-picker"/>
              </td>
             </tr>
              <tr>
               <td width="400"><?php _e("Header Title Background Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set mega menu's content header title background color. Only for template - Modern Mega Menu,Advanced Sporty Mega Menu and Orange Bar Mega Menu.", APMM_PRO_TD) ?>
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][header_title_bgcolor]' 
                value="<?php echo $header_title_bgcolor;?>" class="apmm-color-picker" data-alpha="true" />
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Header Title Font Size", APMM_PRO_TD) ?>
                  <p class="description">
                  <?php _e("Set mega menu's content header title font size", APMM_PRO_TD) ?>
                 </p>
               </td>
               <td>  
                <input type='number'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][header_title_fsize]' 
                value="<?php echo $header_title_fsize;?>"/>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Sub Menu Font Size", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set mega menu's content all sub menu items font size.", APMM_PRO_TD) ?>
                 </p>
               </td>
               <td>  
                <input type='number'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][submenu_fsize]' 
                value="<?php echo $submenu_fsize;?>"/>
              </td>
             </tr>
           
            <tr>
               <td width="400"><?php _e("Sub Menu Font Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set mega menu's content sub menu item's font color.", APMM_PRO_TD) ?>
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][submenu_fcolor]' 
                value="<?php echo $submenu_fcolor;?>" class="apmm-color-picker"/>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Sub Menu Hover Font Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set mega menu's content sub menu item's font hover color.", APMM_PRO_TD) ?>
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][submenu_fhcolor]' 
                value="<?php echo $submenu_fhcolor;?>" class="apmm-color-picker"/>
                
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Sub Menu Underline Color", APMM_PRO_TD) ?>
                  <p class="description">
                  <?php _e("Set mega menu's content sub menu item's on hover underline color.For template type - Simple Mega Menu,Highlighted Border Mega Menu", APMM_PRO_TD) ?>
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][submenu_border_ucolor]' 
                value="<?php echo $submenu_border_ucolor;?>" class="apmm-color-picker"/>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Background Hover Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set mega menu's content sub menu item's on hover background color. For template type - Dark Orchid Mega Menu,Box Sized Mega Menu", APMM_PRO_TD) ?>
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][submenu_bghcolor]' 
                value="<?php echo $submenu_bghcolor;?>" class="apmm-color-picker" data-alpha="true"/>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Mega Menu Background Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set mega menu's background color for all menu items having mega menu wrapper except flyout menu.", APMM_PRO_TD); ?>
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][megamenu_bgcolor]' 
                value="<?php echo $megamenu_bgcolor;?>" class="apmm-color-picker" data-alpha="true"/>
              </td>
             </tr>
          </table>
        </div>

        <div class="tab-panes" id="tabs_flyout" style="display:none;">
        <?php
        $flyoutmenu_fcolor = (isset($menu_general_settings[$mlocation]['flyoutmenu_fcolor']) && $menu_general_settings[$mlocation]['flyoutmenu_fcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['flyoutmenu_fcolor']):'';
        $flyoutmenu_fhcolor = (isset($menu_general_settings[$mlocation]['flyoutmenu_fhcolor']) && $menu_general_settings[$mlocation]['flyoutmenu_fhcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['flyoutmenu_fhcolor']):'';
        $flyoutmenu_ucolor = (isset($menu_general_settings[$mlocation]['flyoutmenu_ucolor']) && $menu_general_settings[$mlocation]['flyoutmenu_ucolor'] != '')?esc_attr($menu_general_settings[$mlocation]['flyoutmenu_ucolor']):'';
        $flyoutmenu_bghcolor = (isset($menu_general_settings[$mlocation]['flyoutmenu_bghcolor']) && $menu_general_settings[$mlocation]['flyoutmenu_bghcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['flyoutmenu_bghcolor']):'';
        $flyoutmenu_bgcolor = (isset($menu_general_settings[$mlocation]['flyoutmenu_bgcolor']) && $menu_general_settings[$mlocation]['flyoutmenu_bgcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['flyoutmenu_bgcolor']):'';
        ?> 
         <table>
            <tr>
               <td width="400"><?php _e("Font Color", APMM_PRO_TD) ?>
                  <p class="description">
                  <?php _e("Set all flyout sub menu item's font color except top level menu item.", APMM_PRO_TD); ?>
                 </p>
               </td>
               <td>  
                 <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][flyoutmenu_fcolor]' 
                value="<?php echo $flyoutmenu_fcolor;?>" class="apmm-color-picker"/>
               </td>
            </tr>
            <tr>
               <td width="400"><?php _e(" Font Hover Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set all flyout sub menu item's font hover color except top level menu item.", APMM_PRO_TD); ?>
                 </p>
               </td>
               <td>  
                 <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][flyoutmenu_fhcolor]' 
                value="<?php echo $flyoutmenu_fhcolor;?>" class="apmm-color-picker"/>
               </td>
            </tr>
            <tr>
               <td width="400"><?php _e("Underline Hover Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set all flyout sub menu item's on hover underline color except top level menu item.", APMM_PRO_TD); ?>
                 </p>
               </td>
               <td>  
                 <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][flyoutmenu_ucolor]' 
                value="<?php echo $flyoutmenu_ucolor;?>" class="apmm-color-picker"/>
               </td>
            </tr>
             <tr>
               <td width="400"><?php _e("Background Hover Color", APMM_PRO_TD) ?>
                  <p class="description">
                  <?php _e("Set all flyout sub menu item's on hover background color except top level menu item.", APMM_PRO_TD); ?>
                 </p>
               </td>
               <td>  
                 <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][flyoutmenu_bghcolor]' 
                value="<?php echo $flyoutmenu_bghcolor;?>" class="apmm-color-picker"/>
               </td>
            </tr>
            <tr>
               <td width="400"><?php _e("Background Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set all flyout sub menu item's custom background color except top level menu item.", APMM_PRO_TD); ?>
                 </p>
               </td>
               <td>  
                 <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][flyoutmenu_bgcolor]' 
                value="<?php echo $flyoutmenu_bgcolor;?>" class="apmm-color-picker"/>
               </td>
            </tr>
          </table>
        </div>
        <div class="tab-panes" id="tabs_menu_label" style="display:none;">
        <?php
          $menu_label_bradius = (isset($menu_general_settings[$mlocation]['menu_label_bradius']) && $menu_general_settings[$mlocation]['menu_label_bradius'] != '')?intval($menu_general_settings[$mlocation]['menu_label_bradius']):'';
          $menu_label_fcolor = (isset($menu_general_settings[$mlocation]['menu_label_fcolor']) && $menu_general_settings[$mlocation]['menu_label_fcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['menu_label_fcolor']):'';
          $menu_label_bgcolor = (isset($menu_general_settings[$mlocation]['menu_label_bgcolor']) && $menu_general_settings[$mlocation]['menu_label_bgcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['menu_label_bgcolor']):'';
          $menu_label_text_transform = (isset($menu_general_settings[$mlocation]['menu_label_text_transform']) && $menu_general_settings[$mlocation]['menu_label_text_transform'] != '')?esc_attr($menu_general_settings[$mlocation]['menu_label_text_transform']):'';
          $menu_label_arrowcolor = (isset($menu_general_settings[$mlocation]['menu_label_arrowcolor']) && $menu_general_settings[$mlocation]['menu_label_arrowcolor'] != '')?esc_attr($menu_general_settings[$mlocation]['menu_label_arrowcolor']):'';
          ?>
          <table>
            <tr>
               <td width="400"><?php _e("Border Radius (in px)", APMM_PRO_TD) ?>
                  <p class="description">
                  <?php _e("Set all menu label's common border radius in pixel.", APMM_PRO_TD); ?>
                 </p>
               </td>
               <td>  
                <input type='number'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][menu_label_bradius]' 
                value="<?php echo $menu_label_bradius;?>"/>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Font Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set all menu label's common font color.", APMM_PRO_TD); ?>
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][menu_label_fcolor]' 
                value="<?php echo $menu_label_fcolor;?>" class="apmm-color-picker"/>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Background Color", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set all menu label's common background color.", APMM_PRO_TD); ?>
                 </p>
               </td>
               <td>  
                <input type='text'
                name='apmegamenu_custommeta[<?php echo $mlocation;?>][menu_label_bgcolor]' 
                value="<?php echo $menu_label_bgcolor;?>" class="apmm-color-picker" data-alpha="true"/>
              </td>
             </tr>
             <tr>
               <td width="400"><?php _e("Text Transform", APMM_PRO_TD) ?>
                 <p class="description">
                  <?php _e("Set all menu label's common text transfrom.", APMM_PRO_TD); ?>
                 </p>
               </td>
               <td>  
                <select name="apmegamenu_custommeta[<?php echo $mlocation;?>][menu_label_text_transform]">
                       <option value="normal" <?php echo (isset($menu_label_text_transform) && $menu_label_text_transform == 'normal')?'selected="selected"':'';?>><?php _e('Normal',APMM_PRO_TD);?></option>
                       <option value="capitalize" <?php echo (isset($menu_label_text_transform) && $menu_label_text_transform == 'capitalize')?'selected="selected"':'';?>><?php _e('Capitalize',APMM_PRO_TD);?></option>
                       <option value="uppercase" <?php echo (isset($menu_label_text_transform) && $menu_label_text_transform == 'uppercase')?'selected="selected"':'';?>><?php _e('Uppercase',APMM_PRO_TD);?></option>
                       <option value="lowercase" <?php echo (isset($menu_label_text_transform) && $menu_label_text_transform == 'lowercase')?'selected="selected"':'';?>><?php _e('Lowercase',APMM_PRO_TD);?></option>
                </select>
              </td>
             </tr> <tr>
               <td width="400"><?php _e("Arrow Background Color", APMM_PRO_TD) ?>
                  <p class="description">
                  <?php _e("Set all menu label's down arrow background color. For Advanced Magazine Mega Menu template layout, this option works for both menu label's border and its down arrow type custom color.", APMM_PRO_TD); ?>
                 </p>
               </td>
               <td>  
               <input type='text' name='apmegamenu_custommeta[<?php echo $mlocation;?>][menu_label_arrowcolor]' value="<?php echo $menu_label_arrowcolor;?>" class="apmm-color-picker"/>
              </td>
             </tr>
          </table>
        </div>
      </div>
   </div>
</div>