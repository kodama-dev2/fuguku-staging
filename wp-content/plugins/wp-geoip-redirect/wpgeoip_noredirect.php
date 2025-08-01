<?php

function wpgeoip_noredirect() {

global $wpdb;
$enabled = get_option('wpgeoip_no_redirect', 0);

// functionality
if( 'Yes' != get_option( 'wpgeoip-functionality', 'No' ) ) {
    echo '<script>window.location.href="'.admin_url('admin.php?page=wpgeoip-license').'";</script>';
    exit;
}
?>
<div id="wrap" class="wpgeoip-wrapper">
	<br />
    <img src="<?= plugin_dir_url(__FILE__) ?>/assets/images/icon32x32.png" style="float:left;"/> 
	<h1 style="float:left;margin-top: 10px;margin-left:10px;">WP GeoIP NO Redirect</h1>
    <div style="clear:both;"></div>
    <hr />
	
	<?php
	if(isset($_POST['sbNoredirect']))
	{
		update_option('wpgeoip_no_redirect', $_POST['no_redirect']);
		print '<div class="updated bellow-h2 updated-red">NO Redirect settings successfully updated</div>';
		$enabled = get_option('wpgeoip_no_redirect');
	}
	?>

	<div class="updated below-h2">
	Append <strong>?noredirect=true</strong> to any URL to avoid being redirected.<br />
	<em>Example: <?php bloginfo('url') ?>/page/?noredirect=true</em>
	</div>
	<br />

	<form method="post">
	
	<h3>Enable <strong>?noredirect=true</strong> GET parameter?</h3>
	<hr>
		<input type="radio" name="no_redirect" value="0" <?php if($enabled == "0") print 'checked'; ?>/> No, don't enable <br />
		<input type="radio" name="no_redirect" value="1" <?php if($enabled == "1") print 'checked'; ?>/> Yes, enable this feature
		
		<hr />
		<input type="submit" name="sbNoredirect" value="Update Settings" class="button"/>
	</form>
</div>
<?php	


}