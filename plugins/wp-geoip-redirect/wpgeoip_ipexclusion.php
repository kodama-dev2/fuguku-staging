<?php

function wpgeoip_ipexclusion() {

global $wpdb;
$excluded = get_option('wpgeoip_excluded', '');

// functionality
if( 'Yes' != get_option( 'wpgeoip-functionality', 'No' ) ) {
    echo '<script>window.location.href="'.admin_url('admin.php?page=wpgeoip-license').'";</script>';
    exit;
}
?>
<div id="wrap" class="wpgeoip-wrapper">
	<br />
    <img src="<?= plugin_dir_url(__FILE__) ?>/assets/images/icon32x32.png" style="float:left;"/> 
	<h1 style="float:left;margin-top: 10px;margin-left:10px;">WP GEoIP Excluded IP Addresses</h1>
    <div style="clear:both;"></div>
    <hr />
	
	<?php
	if(isset($_POST['sbExclude']))
	{
		update_option('wpgeoip_excluded', $_POST['excluded_redirect']);
		print '<div class="updated below-h2 updated-red">IP Exclusion List successfully updated</div>';
		$excluded = get_option('wpgeoip_excluded');
	}
	?>

	<div class="updated below-h2">
	<h3>AVOID REDIRECTS:</h3> <hr />
	Here you can enter as many IP addresses as you want to skip redirection rules.<br />
	Each IP address must be entered on a new line.
	</div>
	<br />

	<form method="post">
	<h4>Enter one IP address per line:</h4>
	
	<textarea name="excluded_redirect" rows="10" cols="142"><?= $excluded ?></textarea>

	<br /><br />
	<input type="submit" name="sbExclude" value="Update Excluded IP Addresses" class="button"/>
	</form>
</div>
<?php	


}