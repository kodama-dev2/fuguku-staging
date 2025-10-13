<?php

/**
 * Activities controller
 */
class Srm_Activities
{	
	

	 /**
     * Register services
     */
    public static function register(){
		
	}
	
	public static function init(){
		
		// global $wpdb;
		// $total_bkp=Srm_Dashboard::getBackupCount();
		// $backups = $wpdb->get_results('SELECT * FROM `'.$wpdb->prefix.'srclubplugins_history` WHERE `action`="backup"', ARRAY_A);
		require_once( SRM_PLUGIN_DIR . 'includes/Srm/ActivitiesTab.php');
		$activitiesTab_obj = new ActivitiesTab();
		
		include SRM_PLUGIN_DIR . '/templates/Srm_activities.php';
	}
	 
	 
}