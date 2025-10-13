<?php defined('ABSPATH') or die("No script kiddies please!"); 
$enable_bg_image = (isset($wpmmenu_item_meta['upload_image_settings']['enable_bg_image']) && $wpmmenu_item_meta['upload_image_settings']['enable_bg_image'] == 'true')?'true':'false';
$bg_image_type = (isset($wpmmenu_item_meta['upload_image_settings']['bg_image_type']) && $wpmmenu_item_meta['upload_image_settings']['bg_image_type'] != '')?$wpmmenu_item_meta['upload_image_settings']['bg_image_type']:'single_image';
?>
<div class="settings_title"><h4><?php _e('Background Image Settings',APMM_PRO_TD);?></h4></div>
<div class="wpmm_mega_settings">
<table class="widefat">
<tr>
<td class="wpmm_meta_table" style="width: 119px;"><label for="enable_bg_image"><?php _e("Enable Background Image", APMM_PRO_TD) ?></label></td>
<td> 
<div class="wpmm-switch">
<input type='checkbox' class='wpmm_menu_settingss' id="enable_bg_image" name='wpmm_settings[upload_image_settings][enable_bg_image]' value='true' <?php echo checked($enable_bg_image,'true', false ); ?>/>
<label for="enable_bg_image"></label>
</div>
<p class="description"><?php _e("Note: Enable to show Background Image for this menu.", APMM_PRO_TD); ?></p>
</td>
</tr>
<tr>
<td class="wpmm_meta_table">
<?php _e("Background Image Type", APMM_PRO_TD); ?>
</td>
<td>
<select name='wpmm_settings[upload_image_settings][bg_image_type]' class="wpmm_bgimage_type">
<option value='single_image'<?php echo selected( $bg_image_type, 'single_image', false );?>><?php _e("Single Image", APMM_PRO_TD); ?></option>
<option value='double_image' <?php echo selected( $bg_image_type, 'double_image', false );?>><?php _e("Double Image", APMM_PRO_TD); ?></option>
<select>
</td>
</tr>
<tr class="toggle_custom_image" id="wpmm_single_image">
<td class="wpmm_meta_table"><label><?php _e("Choose Bg Image", APMM_PRO_TD) ?></label></td>
<td> 
<div class="wpmm-option-field">
<input type="hidden" class="wpmm-sbgimage" name="wpmm_settings[upload_image_settings][single_bg_image_url]" 
value="<?php echo (isset( $wpmmenu_item_meta['upload_image_settings']['single_bg_image_url']) && $wpmmenu_item_meta['upload_image_settings']['single_bg_image_url'] != '')?esc_url($wpmmenu_item_meta['upload_image_settings']['single_bg_image_url']):'';?>" />

<input type="button" class="wpmm_bgimage_btn button button-primary button-large" 
id="wpmm_single_image" name="wpmm_single_bg_image_url"  
value="Upload Background Image" size="25"/> 
<?php 
$img_url =(isset( $wpmmenu_item_meta['upload_image_settings']['single_bg_image_url']) && $wpmmenu_item_meta['upload_image_settings']['single_bg_image_url'] != '')?esc_url($wpmmenu_item_meta['upload_image_settings']['single_bg_image_url']):'';
if($img_url == ''){
$style = 'style="display:none;"';
}else{
$style = '';
}
?>
<div class="wpmm-option-field wpmm-bgimage-preview" <?php echo $style;?>>
<a class="remove_sbg_image_url" href="#">
	<i class="dashicons dashicons-trash"></i>
</a>
<img style="width: 38%;" class="wpmm-sbg-image" 
src="<?php echo (isset( $wpmmenu_item_meta['upload_image_settings']['single_bg_image_url']) && $wpmmenu_item_meta['upload_image_settings']['single_bg_image_url'] != '')?esc_url($wpmmenu_item_meta['upload_image_settings']['single_bg_image_url']):'';?>" 
alt="">
</div>

</div>
</td>
</tr>

<tr class="toggle_double_image" id="wpmm_double_image">
<td class="wpmm_meta_table"><label><?php _e("Choose Two Image", APMM_PRO_TD) ?></label></td>
<td> 
<div class="wpmm-option-field">
<input type="hidden" class="wpmm-sbgimage1" name="wpmm_settings[upload_image_settings][bg_image_url1]" 
value="<?php echo (isset( $wpmmenu_item_meta['upload_image_settings']['bg_image_url1']) && $wpmmenu_item_meta['upload_image_settings']['bg_image_url1'] != '')?esc_url($wpmmenu_item_meta['upload_image_settings']['bg_image_url1']):'';?>" />

<input type="button" class="wpmm_bgimage_btn button button-primary button-large" 
id="wpmm_doubleimage-1" name="wpmm_bg_image_url1"  
value="Upload First Image" size="25"/> 
<?php 
$img_url =(isset( $wpmmenu_item_meta['upload_image_settings']['bg_image_url1']) && $wpmmenu_item_meta['upload_image_settings']['bg_image_url1'] != '')?esc_url($wpmmenu_item_meta['upload_image_settings']['bg_image_url1']):'';
if($img_url == ''){
$style = 'style="display:none;"';
}else{
$style = '';
}
?>
<div class="wpmm-option-field wpmm-bgimage-preview1" <?php echo $style;?>>
<a class="remove_sbg_image_url" href="#">
	<i class="dashicons dashicons-trash"></i>
</a>
<img style="width: 38%;" class="wpmm-sbg-image1" 
src="<?php echo (isset( $wpmmenu_item_meta['upload_image_settings']['bg_image_url1']) && $wpmmenu_item_meta['upload_image_settings']['bg_image_url1'] != '')?esc_url($wpmmenu_item_meta['upload_image_settings']['bg_image_url1']):'';?>" 
alt="">
</div>

</div>

<div class="wpmm-option-field">
<input type="hidden" class="wpmm-sbgimage2" name="wpmm_settings[upload_image_settings][bg_image_url2]" 
value="<?php echo (isset( $wpmmenu_item_meta['upload_image_settings']['bg_image_url2']) && $wpmmenu_item_meta['upload_image_settings']['bg_image_url2'] != '')?esc_url($wpmmenu_item_meta['upload_image_settings']['bg_image_url2']):'';?>" />

<input type="button" class="wpmm_bgimage_btn button button-primary button-large" 
id="wpmm_doubleimage-2" name="wpmm_bg_image_url2"  
value="Upload Second Image" size="25"/> 
<?php 
$img_url =(isset( $wpmmenu_item_meta['upload_image_settings']['bg_image_url2']) && $wpmmenu_item_meta['upload_image_settings']['bg_image_url2'] != '')?esc_url($wpmmenu_item_meta['upload_image_settings']['bg_image_url2']):'';
if($img_url == ''){
$style = 'style="display:none;"';
}else{
$style = '';
}
?>
<div class="wpmm-option-field wpmm-bgimage-preview2" <?php echo $style;?>>
<a class="remove_sbg_image_url" href="#">
	<i class="dashicons dashicons-trash"></i>
</a>
<img class="wpmm-sbg-image2" style="width: 38%;"
src="<?php echo (isset( $wpmmenu_item_meta['upload_image_settings']['bg_image_url2']) && $wpmmenu_item_meta['upload_image_settings']['bg_image_url2'] != '')?esc_url($wpmmenu_item_meta['upload_image_settings']['bg_image_url2']):'';?>" 
alt="">
</div>

</div>

</td>
</tr>

<tr class="toggle_double_image">
<td class="wpmm_meta_table"><label><?php _e("Choose Cross Fading Type", APMM_PRO_TD) ?></label></td>
<td> 
<?php
$cross_fading_type = (isset($wpmmenu_item_meta['upload_image_settings']['cross_fading_type']) && $wpmmenu_item_meta['upload_image_settings']['cross_fading_type'] != '')?esc_attr($wpmmenu_item_meta['upload_image_settings']['cross_fading_type']):'';
$animation_type = (isset($wpmmenu_item_meta['upload_image_settings']['animation_type']) && $wpmmenu_item_meta['upload_image_settings']['animation_type'] != '')?esc_attr($wpmmenu_item_meta['upload_image_settings']['animation_type']):'';
?>
<select name='wpmm_settings[upload_image_settings][cross_fading_type]' class="wpmm_cross_fading_type">
<option value=''><?php _e("No Cross Fading Type", APMM_PRO_TD); ?></option>
<option value='changeonhover'<?php echo selected( $cross_fading_type, 'changeonhover', false );?>><?php _e("Change image to another on hover.", APMM_PRO_TD); ?></option>
 <?php /*<option value='changeontime' <?php echo selected( $wpmmenu_item_meta['upload_image_settings']['cross_fading_type'], 'changeontimer', false );?>><?php _e("One image to another on setup duration time.", APMM_PRO_TD); ?></option>*/ ?>

<select>
</td>
</tr>

<tr class="toggle_double_image">
<td class="wpmm_meta_table"><label><?php _e("Choose Animation Type", APMM_PRO_TD) ?></label></td>
<td> 
<select name='wpmm_settings[upload_image_settings][animation_type]'>
     <option value=''><?php _e("No Animation Type", APMM_PRO_TD); ?></option>
	 <option value='FadeInOut'<?php echo selected( $animation_type, 'FadeInOut', false );?>><?php _e("FadeInOut", APMM_PRO_TD); ?></option>
	  <?php /* <option value='zoom' <?php echo selected( $wpmmenu_item_meta['upload_image_settings']['animation_type'], 'zoom', false );?>><?php _e("Zoom", APMM_PRO_TD); ?></option> */ ?>
	<select>
	</td>
</tr>
<?php 
// $duration_time =(isset( $wpmmenu_item_meta['upload_image_settings']['duration_time']) && $wpmmenu_item_meta['upload_image_settings']['duration_time'] != '')?esc_attr($wpmmenu_item_meta['upload_image_settings']['duration_time']):'10';
?>
<?php /*
<tr class="toggle_double_image">
	<td class="wpmm_meta_table"><label><?php _e("Set Duration In Second", APMM_PRO_TD) ?></label></td>
	<td> 
		<input type="number" name="wpmm_settings[upload_image_settings][duration_time]"  
		value="<?php echo $duration_time;?>"/> 
		<p class="description"><?php _e('Set duration time for each image to be visible for  seconds before fading to the other one.',APMM_PRO_TD);?></p>
	</td>
</tr>
*/

$enable_bg_overlay = (isset($wpmmenu_item_meta['upload_image_settings']['enable_bg_overlay']) && $wpmmenu_item_meta['upload_image_settings']['enable_bg_overlay'] == 'true')?'true':'false';
?>
<tr id="wpmm_single_image1">
<td class="wpmm_meta_table"><label for="enable_bg_overlay"><?php _e("Set Overlay Color", APMM_PRO_TD) ?></label></td>
<td> 
<div class="wpmm-section">
	<div class="left-section-styling">
		<div class="wpmm-switch">
			<input type='checkbox' class='wpmm_menu_settingss' id="enable_bg_overlay" 
			name='wpmm_settings[upload_image_settings][enable_bg_overlay]' 
			value='true' <?php echo checked($enable_bg_overlay,'true', false ); ?>/>
			<label for="enable_bg_overlay"></label>
		</div>
	</div>
	<div class="rt-section-styling">
		<input type='text' name='wpmm_settings[upload_image_settings][setoverlay_color]' class="apmm-color-picker" data-alpha="true" value='<?php echo (isset($wpmmenu_item_meta['upload_image_settings']['setoverlay_color']) && $wpmmenu_item_meta['upload_image_settings']['setoverlay_color'] != '')?esc_attr($wpmmenu_item_meta['upload_image_settings']['setoverlay_color']):'';?>'/>
	</div>
</div>
<p class="description"><?php _e("Set Overlay Color for Background Image in Mega Menu.", APMM_PRO_TD); ?></p>
</td>
</tr>
<?php 
$imageposition = (isset($wpmmenu_item_meta['upload_image_settings']['image_position']) && $wpmmenu_item_meta['upload_image_settings']['image_position'] != '')?esc_attr($wpmmenu_item_meta['upload_image_settings']['image_position']):'left top';
?>
<tr>
<td class="wpmm_meta_table"><label><?php _e("Image Position", APMM_PRO_TD) ?></label></td>
<td> 
<select name='wpmm_settings[upload_image_settings][image_position]' class="wpmm_image_position">
	<option value='left top'<?php echo selected( $imageposition, 'left top', false );?>><?php _e("Left Top", APMM_PRO_TD); ?></option>
	<option value='left center' <?php echo selected( $imageposition, 'left center', false );?>><?php _e("Left Center", APMM_PRO_TD); ?></option>
	<option value='left bottom' <?php echo selected( $imageposition, 'left bottom', false );?>><?php _e("Left Bottom", APMM_PRO_TD); ?></option>
	<option value='right top' <?php echo selected( $imageposition, 'right top', false );?>><?php _e("Right Top", APMM_PRO_TD); ?></option>
	<option value='right center' <?php echo selected( $imageposition, 'right center', false );?>><?php _e("Right Center", APMM_PRO_TD); ?></option>
	<option value='right bottom' <?php echo selected( $imageposition, 'right bottom', false );?>><?php _e("Right Bottom", APMM_PRO_TD); ?></option>
	<option value='center top' <?php echo selected( $imageposition, 'center top', false );?>><?php _e("Center Top", APMM_PRO_TD); ?></option>
	<option value='center center' <?php echo selected( $imageposition, 'center center', false );?>><?php _e("Center Center", APMM_PRO_TD); ?></option>
	<option value='center bottom' <?php echo selected( $imageposition, 'center bottom', false );?>><?php _e("Center Bottom", APMM_PRO_TD); ?></option>
	<option value='50% 50%' <?php echo selected( $imageposition, '50% 50%', false );?>><?php _e("50% 50%", APMM_PRO_TD); ?></option>
	<option value='10px 200px' <?php echo selected( $imageposition, '10px 200px', false );?>><?php _e("10px 200px", APMM_PRO_TD); ?></option>
	<option value='50px 50px' <?php echo selected( $imageposition, '50px 50px', false );?>><?php _e("50px 50px", APMM_PRO_TD); ?></option>
	<option value='initial' <?php echo selected( $imageposition, 'initial', false );?>><?php _e("Initial", APMM_PRO_TD); ?></option>
	<option value='inherit' <?php echo selected( $imageposition, 'inherit', false );?>><?php _e("Inherit", APMM_PRO_TD); ?></option>
	<select>
	</td> 
</tr>
<?php 
$image_repeat = (isset($wpmmenu_item_meta['upload_image_settings']['image_repeat']) && $wpmmenu_item_meta['upload_image_settings']['image_repeat'] != '')?esc_attr($wpmmenu_item_meta['upload_image_settings']['image_repeat']):'no-repeat';
?>
<tr>
	<td class="wpmm_meta_table"><label><?php _e("Image repeat", APMM_PRO_TD) ?></label></td>
	<td> 
		<select name='wpmm_settings[upload_image_settings][image_repeat]' class="wpmm_image_repeat">

			<option value='repeat'<?php echo selected( $image_repeat, 'repeat', false );?>><?php _e("Repeat", APMM_PRO_TD); ?></option>
			<option value='repeat-x' <?php echo selected( $image_repeat, 'repeat-x', false );?>><?php _e("Repeat-X", APMM_PRO_TD); ?></option>
			<option value='repeat-y' <?php echo selected( $image_repeat, 'repeat-y', false );?>><?php _e("Repeat-Y", APMM_PRO_TD); ?></option>
			<option value='no-repeat' <?php echo selected( $image_repeat, 'no-repeat', false );?>><?php _e("No-Repeat", APMM_PRO_TD); ?></option>
			<option value='initial' <?php echo selected( $image_repeat, 'initial', false );?>><?php _e("Initial", APMM_PRO_TD); ?></option>
			<option value='inherit' <?php echo selected( $image_repeat, 'inherit', false );?>><?php _e("Inherit", APMM_PRO_TD); ?></option>
			<select>
			</td>
		</tr>
		<tr>
			<?php 
			$bgsize = (isset( $wpmmenu_item_meta['upload_image_settings']['bgsize']) &&  $wpmmenu_item_meta['upload_image_settings']['bgsize'] != '')?esc_attr( $wpmmenu_item_meta['upload_image_settings']['bgsize']):'cover';
			?>
			<td class="wpmm_meta_table"><label><?php _e("Background Size", APMM_PRO_TD) ?></label></td>
			<td> 
			  <select name='wpmm_settings[upload_image_settings][bgsize]' class="wpmm_image_position">
				<option value='cover' <?php echo selected( $bgsize, 'cover' , false );?>><?php _e("Cover", APMM_PRO_TD); ?></option>
				<option value='contain' <?php echo selected( $bgsize, 'contain', false );?>><?php _e("Contain", APMM_PRO_TD); ?></option>
			  <select>
			  <p class="description">Default is set to background-size as cover.</p>
			</td> 
		</tr>
</table>
</div>