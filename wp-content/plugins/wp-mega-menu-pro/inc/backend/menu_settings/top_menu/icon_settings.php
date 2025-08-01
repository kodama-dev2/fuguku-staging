<?php defined('ABSPATH') or die("No script kiddies please!"); ?>
<div class="settings_title"><h4><?php _e('Icon Settings',APMM_PRO_TD);?></h4></div>
<div class="wpmm_mega_settings">
<?php  
$attr_class = (isset($wpmmenu_item_meta['icons_settings']['icon_choose']) && $wpmmenu_item_meta['icons_settings']['icon_choose'] != '')?$wpmmenu_item_meta['icons_settings']['icon_choose']:'';
$enable_customimg = (isset($wpmmenu_item_meta['icons_settings']['enable_customimg']) && $wpmmenu_item_meta['icons_settings']['enable_customimg'] == 'true')?'true':'false';

?>
<table class="widefat">
     <tr>
		 <td class="wpmm_meta_table">
		 	<label for="show_top_content"><?php _e("Choose Pre Available Icon", APMM_PRO_TD) ?></label>
		 </td>
		 <td> 
		 	<div data-target="icon-picker" class="button icon-picker <?php if ($attr_class !='') { $v = explode('|', $attr_class); echo $v[0] . ' ' . $v[1]; } ?>"></div>
			<input class="icon-picker-input" type="text" name="wpmm_settings[icons_settings][icon_choose]" value="<?php if ($attr_class !='') { $v = explode('|', $attr_class); echo $v[0] . ' ' . $v[1]; } ?>"/>
		 </td>
	</tr> 
</table> 
</div>
<div class="settings_title"><h4><?php _e('Custom Icon Settings',APMM_PRO_TD);?></h4></div>
	<div class="wpmm_mega_settings">
		<div class="clear"></div>
		<table class="widefat">
			<tr>
				<td class="wpmm_meta_table"><label for="enable_customimg"><?php _e("Enable Custom Icon", APMM_PRO_TD) ?></label></td>
				<td> 
					<div class="wpmm-switch">
						<input type='checkbox' class='wpmm_menu_settingss' id="enable_customimg" name='wpmm_settings[icons_settings][enable_customimg]' value='true' <?php echo checked($enable_customimg,'true', false ); ?>/>
						<label for="enable_customimg"></label>
					</div>
					<p class="description"><?php _e("Note: Enable to show uploaded custom icon for menu. If this option is enable, only uploaded custom icon will be shown on this menu item, the above available icon will not be displayed.So, if you want to display above available choosed menu icon, then please do disable this option.", APMM_PRO_TD); ?></p>
				</td>
			</tr>


			<tr class="toggle_custom_image" id="customimage">
				<td class="wpmm_meta_table"><label><?php _e("Choose Icon", APMM_PRO_TD) ?></label></td>
				<td> 
					<div class="wpmm-option-field">
						<input type="hidden" class="wpmm-customimage-url" name="wpmm_settings[icons_settings][custom_image_url]" 
						value="<?php echo (isset( $wpmmenu_item_meta['icons_settings']['custom_image_url']) && $wpmmenu_item_meta['icons_settings']['custom_image_url'] != '')?esc_url($wpmmenu_item_meta['icons_settings']['custom_image_url']):'';?>" />

						<input type="button" class="wpmm_logo_url_button button button-primary button-large" 
						id="customimage" name="wpmm_custom_image_url"  
						value="Upload Custom Icon" size="25"/> 
						<?php 
						$img_url =(isset( $wpmmenu_item_meta['icons_settings']['custom_image_url']) && $wpmmenu_item_meta['icons_settings']['custom_image_url'] != '')?esc_url($wpmmenu_item_meta['icons_settings']['custom_image_url']):'';
						if($img_url == ''){
							$style = 'style="display:none;"';
						}else{
							$style = '';
						}
						?>
						<div class="wpmm-option-field wpmm-image-preview3" <?php echo $style;?>>
							<a class="remove_custom_image_url" href="#">
								<i class="dashicons dashicons-trash"></i>
							</a>
							<img class="wpmm-custom-image" style="width: 80%;"
							src="<?php echo (isset( $wpmmenu_item_meta['icons_settings']['custom_image_url']) && $wpmmenu_item_meta['icons_settings']['custom_image_url'] != '')?esc_url($wpmmenu_item_meta['icons_settings']['custom_image_url']):'';?>" 
							alt="">
						</div>

					</div>
				</td>
			</tr>

			<tr>
				<td><label><?php _e("Custom Width/Height", APMM_PRO_TD) ?></label></td>
				<td>
					<input type="number" placeholder="E.g., 40" class="wpmm-custom-width custom-cart-icon-size" name="wpmm_settings[icons_settings][custom_width]" 
					value="<?php echo (isset( $wpmmenu_item_meta['icons_settings']['custom_width']) && $wpmmenu_item_meta['icons_settings']['custom_width'] != '')?esc_attr($wpmmenu_item_meta['icons_settings']['custom_width']):'';?>" />
					<label><?php _e("Width(px)", APMM_PRO_TD) ?></label>
					<input type="number" placeholder="E.g., 40" class="wpmm-custom-height custom-cart-icon-size" name="wpmm_settings[icons_settings][custom_height]" 
					value="<?php echo (isset( $wpmmenu_item_meta['icons_settings']['custom_height']) && $wpmmenu_item_meta['icons_settings']['custom_height'] != '')?esc_attr($wpmmenu_item_meta['icons_settings']['custom_height']):'';?>" />
					<label><?php _e("Height(px)", APMM_PRO_TD) ?></label>
					<p class="description"><?php _e('Define image custom width/height in px.',APMM_PRO_TD);?></p>
				</td>
			</tr>
		</table>
</div>