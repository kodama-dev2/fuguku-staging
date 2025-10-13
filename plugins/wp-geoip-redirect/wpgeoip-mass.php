<?php
/**
 * Function to MASS Redirect All Countries without Rules
 */
function wpgeoip_mass_redirect()
{
	global $wpdb;
	$enabled = get_option('wpgeoip_mass_redirect');
	$targetURL = get_option('wpgeoip_mass_url');

	// functionality
    if( 'Yes' != get_option( 'wpgeoip-functionality', 'No' ) ) {
        echo '<script>window.location.href="'.admin_url('admin.php?page=wpgeoip-license').'";</script>';
        exit;
    }
?>
<div id="wrap" class="wpgeoip-wrapper">
	<br />
    <img src="<?= plugin_dir_url(__FILE__) ?>/assets/images/icon32x32.png" style="float:left;"/> 
	<h1 style="float:left;margin-top: 10px;margin-left:10px;">Mass Redirect For Countries Without Rules</h1>
    <div style="clear:both;"></div>
    <hr />
	<br />

	<div class="updated">
		This will redirect all countries that <strong>DO NOT</strong> have a redirect rule applied!
		<br />
		<strong>This must be redirected to EXTERNAL domains not on the same website otherwise you'll get a redirect loop.</strong>
	</div>
	
	<?php
	if(isset($_POST['sbMass']))
	{
		update_option('wpgeoip_mass_redirect', $_POST['mass_redirect']);
		update_option('wpgeoip_mass_url', $_POST['mass_url']);
		print '<div class="updated bellow-h2 updated-red">Settings Updated</div>';
		$enabled = get_option('wpgeoip_mass_redirect');
		$targetURL = get_option('wpgeoip_mass_url');
	}
	?>
	
	<form method="post">
	<dl>
		<dt>Enable This Feature ?</dt>
		<dd>
			<input type="radio" name="mass_redirect" value="0" <?php if($enabled == "0") print 'checked'; ?>/> No 
			<input type="radio" name="mass_redirect" value="1" <?php if($enabled == "1") print 'checked'; ?>/> Yes
	    </dd>
	    <dt>Target URL:</dt>
	    <dd>
	    	<input type="text" name="mass_url" value="<?php print $targetURL; ?>" size="50"/>
	    </dd>
	    <dd>
	    	<input type="submit" name="sbMass" value="Update Settings" class="button"/>
	    </dd>
	</dl>
	</form>
</div>
<?php	
}
