<?php defined('ABSPATH') or die("No script kiddies please!");?>
<div class="menu-notice-board-doclink">
    Check Out <a href="https://demo.accesspressthemes.com/wordpress-plugins/wp-mega-menu-pro/docs/" target="_blank"><?php _e('Our Plugin Documentation',APMM_PRO_TD);?></a>
</div>
<?php 
// echo "<pre>";
// print_r($menu_general_settings);
?>
<h2 class="wpmmenu-header-option" style="margin-bottom: 15px;margin-top: 0px;"><?php _e("General Settings", APMM_PRO_TD); ?></h2>
  <table class="wpmm-settings-box">
    <tr>
      <td><label for="apmegamenu_enabled_<?php echo $location;?>"><?php _e("Enable", APMM_PRO_TD) ?></label></td>
      <td> 
       <div class="wpmm-switch">
        <input type='checkbox' class='apmegamenu_enabled' 
        name='apmegamenu_meta[<?php echo $location ?>][enabled]' id="apmegamenu_enabled_<?php echo $location;?>" value='1' <?php checked( isset( $menu_general_settings[$location]['enabled'] ) ); ?>/>
        <label for="apmegamenu_enabled_<?php echo $location;?>"></label>
      </div>
    </td>
  </tr>
  <tr>
      <td><label for="apmegamenu_hideicons_<?php echo $location;?>"><?php _e("Hide All Icons?", APMM_PRO_TD) ?></label></td>
      <td> 
       <div class="wpmm-switch">
        <input type='checkbox' class='apmegamenu_enabled' 
        name='apmegamenu_meta[<?php echo $location ?>][hide_all_icons]' id="apmegamenu_hideicons_<?php echo $location;?>" value='1' <?php checked( isset( $menu_general_settings[$location]['hide_all_icons'] ) ); ?>/>
        <label for="apmegamenu_hideicons_<?php echo $location;?>"></label>
      </div>
    </td>
  </tr>

  <tr>
    <td class='apmega-name'>
      <?php _e("Orientation", APMM_PRO_TD); ?>
    </td>
    <td class='apmega-value'>
      <select name='apmegamenu_meta[<?php echo $location ?>][orientation]' class="select_fields_wpmm wpmm-orientation">
        <option value='horizontal' <?php selected( isset( $menu_general_settings[$location]['orientation'] ) && $menu_general_settings[$location]['orientation'] == 'horizontal'); ?>><?php _e("Horizontal", APMM_PRO_TD); ?></option>
        <option value='vertical' <?php selected( isset( $menu_general_settings[$location]['orientation'] ) && $menu_general_settings[$location]['orientation'] == 'vertical'); ?>><?php _e("Vertical", APMM_PRO_TD); ?></option>
        <select>

        </td>
      </tr>

      <tr class="wpmm_show_valigntype" style="display:none;">
        <td class='apmega-name'>
          <?php _e("Vertical Alignment Type", APMM_PRO_TD); ?>
        </td>
        <td class='apmega-value'>
          <select name='apmegamenu_meta[<?php echo $location ?>][vertical_alignment_type]' class="select_fields_wpmm">
            <option value='left' <?php selected( isset( $menu_general_settings[$location]['vertical_alignment_type'] ) && $menu_general_settings[$location]['vertical_alignment_type'] == 'left'); ?>><?php _e("Left", APMM_PRO_TD); ?></option>
            <option value='right' <?php selected( isset( $menu_general_settings[$location]['vertical_alignment_type'] ) && $menu_general_settings[$location]['vertical_alignment_type'] == 'right'); ?>><?php _e("Right", APMM_PRO_TD); ?></option>
            <select>

            </td>
          </tr>
          <tr>
            <td class='apmega-name'>
              <?php _e("Trigger Effect", APMM_PRO_TD); ?>
            </td>
            <td class='apmega-value'>
              <select name='apmegamenu_meta[<?php echo $location ?>][trigger_option]' class="select_fields_wpmm">
                <option value='onhover' <?php selected( isset( $menu_general_settings[$location]['trigger_option'] ) && $menu_general_settings[$location]['trigger_option'] == 'onhover'); ?>><?php _e("Hover", APMM_PRO_TD); ?></option>
                <option value='onclick' <?php selected( isset( $menu_general_settings[$location]['trigger_option'] ) && $menu_general_settings[$location]['trigger_option'] == 'onclick'); ?>><?php _e("Click", APMM_PRO_TD); ?></option>
                <select>
                </td>
              </tr>
              <tr>
                <td class='apmega-name'>
                  <?php _e("Transition", APMM_PRO_TD); ?>
                </td>
                <td class='apmega-value'>
                  <select name='apmegamenu_meta[<?php echo $location ?>][effect_option]' class="select_fields_wpmm">
                    <option value='fade' <?php selected( isset( $menu_general_settings[$location]['effect_option'] ) && $menu_general_settings[$location]['effect_option'] == 'fade'); ?>><?php _e("Fade", APMM_PRO_TD); ?></option>
                    <option value='slide' <?php selected( isset( $menu_general_settings[$location]['effect_option'] ) && $menu_general_settings[$location]['effect_option'] == 'slide'); ?>><?php _e("Slide Left", APMM_PRO_TD); ?></option>
                    <option value='slide-left' <?php selected( isset( $menu_general_settings[$location]['effect_option'] ) && $menu_general_settings[$location]['effect_option'] == 'slide-left'); ?>><?php _e("Slide Right", APMM_PRO_TD); ?></option>
                    <option value='slide-down' <?php selected( isset( $menu_general_settings[$location]['effect_option'] ) && $menu_general_settings[$location]['effect_option'] == 'slide-down'); ?>><?php _e("Slide Down", APMM_PRO_TD); ?></option>
                    <option value='slide-up' <?php selected( isset( $menu_general_settings[$location]['effect_option'] ) && $menu_general_settings[$location]['effect_option'] == 'slide-up'); ?>><?php _e("Slide Up", APMM_PRO_TD); ?></option>
                    <option value='slide-up-fade' <?php selected( isset( $menu_general_settings[$location]['effect_option'] ) && $menu_general_settings[$location]['effect_option'] == 'slide-up-fade'); ?>><?php _e("Slide Up With Fade", APMM_PRO_TD); ?></option> 
                    <option value='slide-down-fade' <?php selected( isset( $menu_general_settings[$location]['effect_option'] ) && $menu_general_settings[$location]['effect_option'] == 'slide-down-fade'); ?>><?php _e("Slide Down With Fade", APMM_PRO_TD); ?></option> 
                    <option value='super-slidedown' <?php selected( isset( $menu_general_settings[$location]['effect_option'] ) && $menu_general_settings[$location]['effect_option'] == 'super-slidedown'); ?>><?php _e("Super SlideDown", APMM_PRO_TD); ?></option>
                    <option value='zoom-inout' <?php selected( isset( $menu_general_settings[$location]['effect_option'] ) && $menu_general_settings[$location]['effect_option'] == 'zoom-inout'); ?>><?php _e("Zoom In/Out", APMM_PRO_TD); ?></option>
                    <option value='flip-effect' <?php selected( isset( $menu_general_settings[$location]['effect_option'] ) && $menu_general_settings[$location]['effect_option'] == 'flip-effect'); ?>><?php _e("Flip Effect", APMM_PRO_TD); ?></option>
                    <select>
                    </td>
                  </tr>
                  <tr class="themetype">
                    <td><?php _e("Choose Theme Type", APMM_PRO_TD); ?></td>
                    <?php $available_skin_themes = get_option('apmm_pro_register_skin'); ?>
                    <td>
                     <select name="apmegamenu_meta[<?php echo $location;?>][theme_type]" class="wpmm_theme_type">
                      <option value="available_skins" <?php selected( isset( $menu_general_settings[$location]['theme_type'] ) && $menu_general_settings[$location]['theme_type'] == 'available_skins'); ?>><?php _e('Available Skins',APMM_PRO_TD);?></option>
                      <option value="custom_themes" <?php selected( isset( $menu_general_settings[$location]['theme_type'] ) && $menu_general_settings[$location]['theme_type'] == 'custom_themes'); ?>><?php _e('Custom Themes',APMM_PRO_TD);?></option>  
                    </select>
                  </td>
                </tr>
                <tr class="wpmm_show_themes" style="display:none;">
                  <td><?php _e("Custom Theme", APMM_PRO_TD); ?></td>
                  <td>
                    <?php $ap_theme_object = new AP_Theme_Settings();
                    $themes = $ap_theme_object->get_custom_theme_data('');
                    ?>
                    <select name='apmegamenu_meta[<?php echo $location ?>][theme]'
                      class="select_fields_wpmm">
                      <?php
                      $selected_theme = isset( $menu_general_settings[$location]['theme'] ) ? $menu_general_settings[$location]['theme'] : 'default';
                      foreach ( $themes as $key => $theme ) {
                        $theme_id = $theme->theme_id;
                        $theme_title = $theme->title;
                        ?>
                        <option value='<?php echo $theme_id;?>' <?php echo selected( $selected_theme, $theme_id );?>><?php echo $theme_title;?></option>
                        <?php
                      }
                      ?>                         
                    </select>
                  </td>
                </tr>
                <tr class="wpmm_show_skins" style="display:none;">
                 <td><?php _e("Available Skins/Templates", APMM_PRO_TD); ?></td>
                 <td>
                  <?php 
                  $selected_skin = isset( $menu_general_settings[$location]['available_skin'] ) ? $menu_general_settings[$location]['available_skin'] : '';
                  ?>
                  <select name="apmegamenu_meta[<?php echo $location;?>][available_skin]" 
                   class="select_fields_wpmm">
                   <optgroup label="Available Skins">
                    <?php if(isset($available_skin_themes) && !empty($available_skin_themes)){
                      foreach ($available_skin_themes as $key => $value) {?>
                       <option value="<?php echo $value['id'];?>" <?php echo selected( $selected_skin, $value['id'] );?>><?php _e($value['title'],APMM_PRO_TD);?></option>
                     <?php }
                   } ?>
                 </optgroup>
                 <optgroup label="Available Templates">
                   <option value='simple-mm-template' <?php echo selected( $selected_skin, 'simple-mm-template' );?>><?php _e('Simple Mega Menu',APMM_PRO_TD);?></option>
                   <option value='dark-orchid-mm-template' <?php echo selected( $selected_skin, 'dark-orchid-mm-template' );?>><?php _e('Dark Orchid Mega Menu',APMM_PRO_TD);?></option>
                   <option value='modern-mm-template' <?php echo selected( $selected_skin, 'modern-mm-template' );?>><?php _e('Modern Mega Menu',APMM_PRO_TD);?></option>
                   <option value='highlighted-border-mm-template' <?php echo selected( $selected_skin, 'highlighted-border-mm-template' );?>><?php _e('Highlighted Border Mega Menu',APMM_PRO_TD);?></option>
                   <option value='advanced-magazine-mm-template' <?php echo selected( $selected_skin, 'advanced-magazine-mm-template' );?>><?php _e('Advanced Magazine Mega Menu',APMM_PRO_TD);?></option>
                   <option value='sporty-mm-template' <?php echo selected( $selected_skin, 'sporty-mm-template' );?>><?php _e('Advanced Sporty Mega Menu',APMM_PRO_TD);?></option>
                   <option value='unique-mm-template' <?php echo selected( $selected_skin, 'unique-mm-template' );?>><?php _e('Unique Mega Menu',APMM_PRO_TD);?></option>
                   <option value='box-sized-mm-template' <?php echo selected( $selected_skin, 'box-sized-mm-template' );?>><?php _e('Box Sized Mega Menu',APMM_PRO_TD);?></option>
                   <option value='mini-mm-template' <?php echo selected( $selected_skin, 'mini-mm-template' );?>><?php _e('Mini Sized Mega Menu',APMM_PRO_TD);?></option>
                   <option value='orangebar-mm-template' <?php echo selected( $selected_skin, 'orangebar-mm-template' );?>><?php _e('Orange Bar Mega Menu',APMM_PRO_TD);?></option>
                 </optgroup>
               </select>
             </td>
           </tr>
           <?php 
           $enabled_check = (isset($menu_general_settings[$location]['enabled']) && $menu_general_settings[$location]['enabled'] == 1)?1:0;
           $theme_type = (isset($menu_general_settings[$location]['theme_type']) && $menu_general_settings[$location]['theme_type'] == 'available_skins')?'available_skins':'custom_themes';
           // if($enabled_check == 1 && $theme_type == "available_skins" ){
            ?>
           <tr class="wpmm_show_skins" style="display:none;">
             <td><?php _e("Customized Options", APMM_PRO_TD); ?></td>
             <td>
               <span class="wpmm_available_custom" data-enablemegamenu="<?php echo $enabled_check;?>" data-location="<?php echo $location;?>" data-templatetype="<?php echo $selected_skin;?>"><?php _e("Set Custom Options", APMM_PRO_TD); ?></span>
             </td>
           </tr>
         <?php 
          // }
           ?>
         </table>
         <h2 class="wpmmenu-header-option"><?php _e("Mobile Settings", APMM_PRO_TD); ?></h2>
         <p class="description"><?php _e('Set above option for Mobile Menu for this menu location.',APMM_PRO_TD);?></p>
         <table>
           <tr>
            <td><label for="apmegamenu_mobile_mm_<?php echo $location;?>"><?php _e("Enable Mega Menu On Mobile?", APMM_PRO_TD) ?></label></td>
            <td> 
             <div class="wpmm-switch">
              <input type='checkbox' class='apmegamenu_enabled' 
              name='apmegamenu_meta[<?php echo $location ?>][enabled_on_mobile]' id="apmegamenu_mobile_mm_<?php echo $location;?>" value='1' <?php checked( isset( $menu_general_settings[$location]['enabled_on_mobile'] ) ); ?>/>
              <label for="apmegamenu_mobile_mm_<?php echo $location;?>"></label>
            </div>
          </td>
        </tr>
         <tr>
            <td><label for="apmegamenu_hideiconmobile_mm_<?php echo $location;?>"><?php _e("Hide All Icons On Mobile?", APMM_PRO_TD) ?></label></td>
            <td> 
             <div class="wpmm-switch">
              <input type='checkbox' class='apmegamenu_enabled' 
              name='apmegamenu_meta[<?php echo $location ?>][hide_allicons_mobile]' id="apmegamenu_hideiconmobile_mm_<?php echo $location;?>" value='1' <?php checked( isset( $menu_general_settings[$location]['hide_allicons_mobile'] ) ); ?>/>
              <label for="apmegamenu_hideiconmobile_mm_<?php echo $location;?>"></label>
            </div>
          </td>
        </tr>
        <tr>
          <td class='apmega-name'>
            <?php _e("Mobile Menu", APMM_PRO_TD); ?>

          </td>
          <td class='apmega-value'>
            <?php $menus = get_registered_nav_menus(); ?>
            <select name="apmegamenu_meta[<?php echo $location;?>][mobile_menu_location]" 
             class="apmmpro-menu-locations-lists">
             <?php  if ( count ( $menus ) ) {
               $selected_mobile_menu = isset( $menu_general_settings[$location]['mobile_menu_location'] ) ? $menu_general_settings[$location]['mobile_menu_location'] : '';
               foreach ( $menus as $location_key => $description ) { ?>
                <option value="<?php echo $location_key;?>" <?php echo selected( $selected_mobile_menu, $location_key );?>><?php _e($description,APMM_PRO_TD);?></option>
              <?php }
            } ?>
          </select>
          
        </td>
      </tr>
      <tr>
        <td><label for="apmegamenu_disable_toggle_<?php echo $location;?>"><?php _e("Disable Menu Toggle?", APMM_PRO_TD) ?></label></td>
        <td> 
         <div class="wpmm-switch">
          <input type='checkbox' class='apmegamenu_enabled' 
          name='apmegamenu_meta[<?php echo $location ?>][disabled_menu_toggle]' id="apmegamenu_disable_toggle_<?php echo $location;?>" value='1' <?php checked( isset( $menu_general_settings[$location]['disabled_menu_toggle'] ) ); ?>/>
          <label for="apmegamenu_disable_toggle_<?php echo $location;?>"></label>
        </div>
      </td>
    </tr>
  </table>
  <p class="description"><?php _e('Disable this option for mobile if menu toggle is already exist from theme side.',APMM_PRO_TD);?></p>