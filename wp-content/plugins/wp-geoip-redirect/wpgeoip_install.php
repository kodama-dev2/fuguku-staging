<?php
/**
 * Add Database Table `grules` / `log`
 */
function wpgeoip_install( $networkwide )
{
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	global $wpdb;
	
	$sql = "CREATE TABLE IF NOT EXISTS `wpgeoip_log` (
                `logID` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `post` VARCHAR( 255 ) NOT NULL ,
                `message` VARCHAR( 255 ) NOT NULL
                )DEFAULT CHARSET=utf8 ;";
     
    dbDelta($sql);
	
	add_option('wpgeoip_mass_redirect', '0');
	add_option('wpgeoip_mass_url', 'http://');

	// multisite?
	if (function_exists( 'is_multisite' ) && is_multisite() ) {
         //check if it is network activation if so run the activation function for each id
         if( $networkwide ) {
            $old_blog =  $wpdb->blogid;
            //Get all blog ids
            $blogids =  $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            foreach ( $blogids as $blog_id ) {
               switch_to_blog($blog_id);
               //Create database table if not exists
               wpgeoip_create_rules_table();
            }
            switch_to_blog( $old_blog );
            return;
         }
      }

      //Create database table if not exists
      wpgeoip_create_rules_table();
}

/**
 * Remove Database Table `grules` | `log`
 */
function wpgeoip_uninstall()
{
	global $wpdb;

   if (function_exists( 'is_multisite' ) && is_multisite() ) {
   
	   global $wpdb;

	   $old_blog =  $wpdb->blogid;

	   //Get all blog ids
	   $blogids =  $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

	   foreach ( $blogids as $blog_id ) {
	      switch_to_blog($blog_id);
	      $wpdb->query("DROP TABLE IF EXISTS `".$wpdb->prefix."grules`");
	   }

	   switch_to_blog( $old_blog );

	} else {
	   	$wpdb->query("DROP TABLE IF EXISTS `".$wpdb->prefix."grules`");
	}


	$wpdb->query("DROP TABLE IF EXISTS `wpgeoip_log`");
	delete_option('wpgeoip_mass_redirect');
	delete_option('wpgeoip_mass_url');
}


/**
 *  Create Rules Table
 */
function wpgeoip_create_rules_table() {
	
	global $wpdb;
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$sql = 'CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'grules` (
			  `ruleID` int(10) unsigned NOT NULL auto_increment,
			  `countryID` varchar(255) NOT NULL,
			  `targetURL` varchar(255) NOT NULL,
			  `catID` int(10) NOT NULL,
			  `postID` int(10) NOT NULL,
			  `home_rule` int(1) NOT NULL,
			  PRIMARY KEY  (`ruleID`)
			)DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
				';
				
	dbDelta($sql);

}

add_action( 'wpmu_new_blog', 'wpgeoip_rules_table_mu' );

//  when a new subsite is created 
function wpgeoip_rules_table_mu( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
    if ( is_plugin_active_for_network( 'wp-geoip-redirect/index.php' ) ) {
        switch_to_blog( $blog_id );
        wpgeoip_create_rules_table();
        restore_current_blog();
    }
}