<?php defined('ABSPATH') or die("No script kiddies please!");?>
<!----------------Widgets settings  -------------------------->
<p class="description">
		<?php _e('For customization content inside mega menu, toggle open widget settings below and find many custom options to display mega menu content in your way.',APMM_PRO_TD);?>
		</p>
<div class="apmm-slideToggle" id="widgets_settings"  style="cursor:pointer;">
<div class="title_toggle"><?php _e('Widgets Settings',APMM_PRO_TD);?></div>
</div>
<div class="apmm-Togglebox apmm-slideTogglebox_widgets_settings" style="display: none;">

<table cellspacing="0" class="widefat apmm_create_seciton">
<tbody>
<tr>
	<td>
		<label><?php _e('Heading Title Fonts',APMM_PRO_TD);?></label>
		<p class="description">
		<?php _e('Set these font to use Widget headers in the mega menu.For Color[Hover]: set color for header title on hover.',APMM_PRO_TD);?>
		</p>
	</td>
	<td>
		<div class="wpmmpro-box-opt">
			  	 <table>
                     <tr>
			  	 		<td width="140"><span><?php _e('Color',APMM_PRO_TD);?></span></td>
			  	 	    <td><input type="text" name="apmm_theme[widgets][font_color]" 
	class="apmega-menu_bar_padding apmm-color-picker" value="<?php echo (!isset($widgets_font_color))?'':esc_attr($widgets_font_color);?>"/>
		                </td>
			  	    </tr>
			  	    <tr>
			  	 		<td width="140"><span><?php _e('Color [Hover]',APMM_PRO_TD);?></span></td>
			  	 	    <td><input type="text" name="apmm_theme[widgets][font_hover_color]" 
	class="apmega-menu_bar_padding apmm-color-picker" value="<?php echo (!isset($widgets_font_color_active))?'':esc_attr($widgets_font_color_active);?>"/></td>
			  	    </tr>
			  	    <tr>
			  	 		<td width="140"><span><?php _e('Size',APMM_PRO_TD);?></span></td>
			  	 	    <td><input type="text" value="<?php echo (!isset($widgets_font_size))?'':esc_attr($widgets_font_size);?>" name="apmm_theme[widgets][font_size]" class="apmega-menu_bar_padding" placeholder="15px"/><em>in px</em></td>
			  	    </tr>
			  	 </table>
				<label class="ap-mega_container-padding">
					<span><?php _e('Weight',APMM_PRO_TD);?></span>
					   <select name="apmm_theme[widgets][font_weight]" 
						class="apmm_fontweight">
						   <option value="theme_default" <?php echo (isset($widgets_font_weight) && $widgets_font_weight == 'theme_default')?'selected="selected"':'';?>><?php _e('Theme Default',APMM_PRO_TD);?></option>
						   <option value="normal" <?php echo (isset($widgets_font_weight) && $widgets_font_weight == 'normal')?'selected="selected"':'';?>><?php _e('Normal(400)',APMM_PRO_TD);?></option>
						   <option value="bold" <?php echo (isset($widgets_font_weight) && $widgets_font_weight == 'bold')?'selected="selected"':'';?>><?php _e('Bold(600)',APMM_PRO_TD);?></option>
						   <option value="light" <?php echo (isset($widgets_font_weight) && $widgets_font_weight == 'light')?'selected="selected"':'';?>><?php _e('Light(300)',APMM_PRO_TD);?></option>
						</select>
					</label>
					<label class="ap-mega_container-padding">
					<span><?php _e('Weight On Hover',APMM_PRO_TD);?></span>
					   <select name="apmm_theme[widgets][font_weight_hover]" 
						class="apmm_fontweight_hover">
						   <option value="theme_default" <?php echo (isset($widgets_font_weight_hover) && $widgets_font_weight_hover == 'theme_default')?'selected="selected"':'';?>><?php _e('Theme Default',APMM_PRO_TD);?></option>
						   <option value="normal" <?php echo (isset($widgets_font_weight_hover) && $widgets_font_weight_hover == 'normal')?'selected="selected"':'';?>><?php _e('Normal(400)',APMM_PRO_TD);?></option>
						   <option value="bold" <?php echo (isset($widgets_font_weight_hover) && $widgets_font_weight_hover == 'bold')?'selected="selected"':'';?>><?php _e('Bold(600)',APMM_PRO_TD);?></option>
						   <option value="light" <?php echo (isset($widgets_font_weight_hover) && $widgets_font_weight_hover == 'light')?'selected="selected"':'';?>><?php _e('Light(300)',APMM_PRO_TD);?></option>
						</select>
					</label>

					<label class="ap-mega_container-padding">
					<span><?php _e('Transform',APMM_PRO_TD);?></span>
						<select name="apmm_theme[widgets][transform]" 
							class="apmm_transform">
							   <option value="normal" <?php echo (isset($widgets_transform) && $widgets_transform == 'normal')?'selected="selected"':'';?>><?php _e('Normal',APMM_PRO_TD);?></option>
							   <option value="capitalize" <?php echo (isset($widgets_transform) && $widgets_transform == 'capitalize')?'selected="selected"':'';?>><?php _e('Capitalize',APMM_PRO_TD);?></option>
							   <option value="uppercase" <?php echo (isset($widgets_transform) && $widgets_transform == 'uppercase')?'selected="selected"':'';?>><?php _e('Uppercase',APMM_PRO_TD);?></option>
							   <option value="lowercase" <?php echo (isset($widgets_transform) && $widgets_transform == 'lowercase')?'selected="selected"':'';?>><?php _e('Lowercase',APMM_PRO_TD);?></option>
							</select>
					</label>
					<label class="ap-mega_container-padding">
					<span><?php _e('Family',APMM_PRO_TD);?></span>
						<select name="apmm_theme[widgets][font_family]" class="apmm_font_family">
						  <option value="Assistant" <?php if(isset($widgets_font_family) && $widgets_font_family == 'Assistant') echo "selected";?>>Assistant</option>
						  <?php  $apmm_fonts = get_option('apmm_font_family');
						   if(isset($apmm_fonts) && !empty($apmm_fonts)){
						  	foreach ($apmm_fonts as $value) {
						  		?>
						  <option value="<?php echo $value;?>" <?php if(isset($widgets_font_family)) if($value == $widgets_font_family) echo "selected";?>><?php echo $value;?></option>
						   <?php } 
						   }?>
						</select>
					</label>
					<label class="ap-mega_container-padding">
					<span><?php _e('Decoration',APMM_PRO_TD);?></span>
						<select name="apmm_theme[widgets][font_decoration]" class="apmm_font_decoration">
							   <option value="none" <?php echo (isset($widgets_font_decoration) && $widgets_font_decoration == 'none')?'selected="selected"':'';?>><?php _e('None',APMM_PRO_TD);?></option>
							   <option value="underline" <?php echo (isset($widgets_font_decoration) && $widgets_font_decoration == 'underline')?'selected="selected"':'';?>><?php _e('Underline',APMM_PRO_TD);?></option>
							</select>
					</label>
					<label class="ap-mega_container-padding">
					<span><?php _e('Decoration On Hover',APMM_PRO_TD);?></span>
						<select name="apmm_theme[widgets][font_decoration_hover]" class="apmm_font_decoration_hover">
							   <option value="none" <?php echo (isset($widgets_font_decoration_hover) && $widgets_font_decoration_hover == 'none')?'selected="selected"':'';?>><?php _e('None',APMM_PRO_TD);?></option>
							   <option value="underline" <?php echo (isset($widgets_font_decoration_hover) && $widgets_font_decoration_hover == 'underline')?'selected="selected"':'';?>><?php _e('Underline',APMM_PRO_TD);?></option>
							</select>
					</label>
		</div>
	</td>
</tr>
<tr>
	<td>
		<label><?php _e('Content Font Color',APMM_PRO_TD);?></label>
		<p class="description">
		<?php _e('Define any font to use for mega menu widget contents.',APMM_PRO_TD);?>
		</p>
	</td>
	<td>
		<div class="wpmmpro-box-opt">
			  	 <table>
                     <tr>
			  	 		<td width="140"><span><?php _e('COLOR',APMM_PRO_TD);?></span></td>
			  	 	    <td><input type="text" value="<?php echo (!isset($widgets_content_font_color))?'':esc_attr($widgets_content_font_color);?>" name="apmm_theme[widgets][content_font_color]" 
		class="apmega-menu_bar_padding apmm-color-picker"/>
		                </td>
			  	    </tr>
			  	    <tr>
			  	 		<td width="140"><span><?php _e('Family',APMM_PRO_TD);?></span></td>
			  	 	    <td>
			  	 	    <select name="apmm_theme[widgets][content_font_family]" class="apmm_font_family">
							<option value="Assistant" <?php if(isset($widgets_content_font_family) && $widgets_content_font_family == 'Assistant') echo "selected";?>>Assistant</option>
							  <?php  $apmm_fonts = get_option('apmm_font_family');
							   if(isset($apmm_fonts) && !empty($apmm_fonts)){
							  	foreach ($apmm_fonts as $value) {
							  		?>
							  <option value="<?php echo $value;?>" <?php if(isset($widgets_content_font_family)) if($value == $widgets_content_font_family) echo "selected";?>><?php echo $value;?></option>
							   <?php } 
							   }?>
						 </select>
					   </td>
			  	  </tr>
			  </table>
	  </div>
	</td>
</tr>
<tr>
  <td>
	<label><?php _e('Button Custom Options',APMM_PRO_TD);?></label>
	<p class="description"><?php _e('Set custom options for all button inside mega menu as display with the help of widget using our plugin. Custom optionf fo button includes background color, font color, font hover color respectively.',APMM_PRO_TD);?>
	</p>
</td>
<td>
<div class="wpmmpro-box-opt">
	  	 <table>
            <tr>
	  	 	 <td width="140"><span><?php _e('Background Color',APMM_PRO_TD);?></span></td>
	  	 	    <td><input type="text" value="<?php echo (!isset($widgets_button_bgcolor))?'':esc_attr($widgets_button_bgcolor);?>" name="apmm_theme[widgets][button_bgcolor]" class="apmega-menu_bar_padding apmm-color-picker"/>
                </td>
	  	    </tr>
	  	    <tr>
	  	 	 <td width="140"><span><?php _e('Background Color [Hover]',APMM_PRO_TD);?></span></td>
	  	 	    <td><input type="text" value="<?php echo (!isset($widgets_button_bghcolor))?'':esc_attr($widgets_button_bghcolor);?>" name="apmm_theme[widgets][button_bghcolor]" class="apmega-menu_bar_padding apmm-color-picker"/>
                </td>
	  	    </tr>
	  	    <tr>
	  	 	 <td width="140"><span><?php _e('Font Color',APMM_PRO_TD);?></span></td>
	  	 	    <td><input type="text" value="<?php echo (!isset($widgets_button_fcolor))?'':esc_attr($widgets_button_fcolor);?>" name="apmm_theme[widgets][button_fcolor]" class="apmega-menu_bar_padding apmm-color-picker"/>
                </td>
	  	    </tr>
	  	     <tr>
	  	 	 <td width="140"><span><?php _e('Font Color [Hover]',APMM_PRO_TD);?></span></td>
	  	 	    <td><input type="text" value="<?php echo (!isset($widgets_button_fhcolor))?'':esc_attr($widgets_button_fhcolor);?>" name="apmm_theme[widgets][button_fhcolor]" class="apmega-menu_bar_padding apmm-color-picker"/>
                </td>
	  	    </tr>
	  	 </table>
  </div>
 </td>
</tr>
<tr>
	<td>
		<label><?php _e('Widget Heading Margin',APMM_PRO_TD);?></label>
		<p class="description">
			<?php _e('Set the margin for the widget headings.',APMM_PRO_TD);?>
		</p>
	</td>
	<td>
	<label data-validation="px" class="ap-mega_container-padding">
	<span><?php _e('Top',APMM_PRO_TD);?></span>
	<input type="text" value="<?php echo (!isset($widgets_margin_top))?'':esc_attr($widgets_margin_top);?>" name="apmm_theme[widgets][margin_top]" 
	class="apmega-widgets_padding" placeholder="0px">
	</label>
	<label data-validation="px" class="ap-mega_container-padding">
	<span><?php _e('Bottom',APMM_PRO_TD);?></span>
	<input type="text" value="<?php echo (!isset($widgets_margin_bottom))?'':esc_attr($widgets_margin_bottom);?>" name="apmm_theme[widgets][margin_bottom]" 
	class="apmega-widgets_padding" placeholder="0px">
	</label>

 
	</td>
</tr>

</tbody>
</table>
</div>
<!----------------Widgets settings  End-------------------------->