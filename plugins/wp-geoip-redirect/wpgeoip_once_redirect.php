<?php

function wpgeoip_once_redirect() {
// functionality
if( 'Yes' != get_option( 'wpgeoip-functionality', 'No' ) ) {
    echo '<script>window.location.href="'.admin_url('admin.php?page=wpgeoip-license').'";</script>';
    exit;
}
?>
<div id="wrap" class="wpgeoip-wrapper">
	<br />
    <img src="<?= plugin_dir_url(__FILE__) ?>/assets/images/icon32x32.png" style="float:left;"/> 
	<h1 style="float:left;margin-top: 10px;margin-left:10px;">WP GeoIP Once Redirect</h1>
    <div style="clear:both;"></div>
    <hr />
	
	<?php
	if(isset($_POST['sbNoredirect']))
	{
		update_option('wpgeoip_once_redirect', $_POST['once_redirect']);
		update_option('wpgeoip_cookie_duration', $_POST['wpgeoip_cookie_duration']);

		print '<div class="updated bellow-h2 updated-red">Once redirect settings successfully updated. <br/><small>Please note, cookie length only affects new cookies ( not existent ones, which will keep the length priorly defined ).</small></div>';

		if( $_POST['once_redirect'] == 0 ) {
			print '<div class="updated below-h2 updated-red">Attention, if you previously enabled this feature, this will only affect users that DO NOT have a cookie already (i.e. new users only). Please clear your browser COOKIES instead. </div>';
		}

	}

	$enabled = get_option('wpgeoip_once_redirect');

	?>

	<div class="updated below-h2">
	Redirect user only once, then allow him come back to the same page unrestricted ( without getting redirected again )
	</div>
	<br />

	<form method="post">
	
	<h3>Enable <strong>once redirect</strong> feature?</h3>
	<hr>

	<input type="radio" name="once_redirect" value="0" <?php if($enabled == "0") print 'checked'; ?>/> No, don't enable <br />
	<input type="radio" name="once_redirect" value="1" <?php if($enabled == "1") print 'checked'; ?>/> Yes, enable this feature

	<h3>Cookie duration ( how long should the system avoid redirecting the user after first redirect )</h3>
	<hr />
	<small>Please note, cookie length only affects new cookies ( not existent ones, which will keep the length defined earlier).</small><br/><br/>
	<select name="wpgeoip_cookie_duration" class="chosen-select">
	<?php 
	foreach( array( 'Days', 'Minutes', 'Seconds' ) as $length ) {
		for( $i = 1; $i <= 365; $i++ ) {
			if( get_option('wpgeoip_cookie_duration', 0) == $i . ' ' . $length )
				printf('<option value="%d %s" selected>%d %s</option>', $i, $length, $i, $length);
			else
				printf('<option value="%d %s">%d %s</option>', $i, $length, $i, $length);
		}
	}
	?>
	</select>
	
	<hr />
	<input type="submit" name="sbNoredirect" value="Update Settings" class="button"/>
	</form>
</div>
<?php	


}