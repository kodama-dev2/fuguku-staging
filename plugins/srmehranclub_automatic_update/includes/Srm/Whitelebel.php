<?php

/**
 * Whitelebel controller
 */
class Srm_Whitelebel
{


		 /**
     * Register services
     */
    public static function register(){
		add_action( 'wp_ajax_nopriv_enablewhitelebel',array(__CLASS__, 'enablewhitelebel'));
		add_action( 'wp_ajax_enablewhitelebel', array(__CLASS__, 'enablewhitelebel'));
	}

	public static function init(){
		$file_path=SRM_PLUGIN_DIR.'/srmehranclub_automatic_update.php';
		$plugin_data = get_plugin_data($file_path);

		if(get_option('SRM_plugin_name') && get_option('SRM_plugin_description') && get_option('SRM_plugin_author') && get_option('SRM_plugin_author_URI')){
			
			 $PluginName=(get_option('SRM_plugin_name'))?get_option('SRM_plugin_name'):'';
			$PluginDescription=(get_option('SRM_plugin_description'))?strip_tags(get_option('SRM_plugin_description')):'';
			$PluginAuthor=(get_option('SRM_plugin_author'))?get_option('SRM_plugin_author'):'';
			$PluginAuthorURI=(get_option('SRM_plugin_author_URI'))?get_option('SRM_plugin_author_URI'):'';
			$enable_white_label=(get_option('SRM_enable_white_label'))?get_option('SRM_enable_white_label'):'';
			$soft_whitelebal_activation=(get_option('SRM_soft_whitelebal_activation'))?get_option('SRM_soft_whitelebal_activation'):'';
		}else{
			$PluginName=($plugin_data['Name'])?$plugin_data['Name']:'';
			$PluginDescription=($plugin_data['Description'])?strip_tags($plugin_data['Description']):'';
			$PluginAuthor=($plugin_data['AuthorName'])?$plugin_data['AuthorName']:'';
			$PluginAuthorURI=($plugin_data['AuthorURI'])?$plugin_data['AuthorURI']:'';
			
		}
			
		include SRM_PLUGIN_DIR . '/templates/Srm_whiteLebel.php';

    }

	public static function isWhiteLebelEnable(){
			if(get_option('SRM_enable_white_label') && get_option('SRM_enable_white_label')=="on"){
				return true;
			}else{
				return false;
			}
	}

	public static function enablewhitelebel(){
		
			if(isset($_POST['plugin_name'])){
			update_option('SRM_plugin_name', $_POST['plugin_name']);  
			}
			if(isset($_POST['plugin_description'])){
			update_option('SRM_plugin_description', $_POST['plugin_description']);  
			}
			if(isset($_POST['plugin_author'])){
			update_option('SRM_plugin_author', $_POST['plugin_author']);  
			}
			if(isset($_POST['plugin_author_URI'])){
			update_option('SRM_plugin_author_URI', $_POST['plugin_author_URI']);  
			}

		
			if(isset($_POST['enable_white_label'])){
				update_option('SRM_enable_white_label', $_POST['enable_white_label']); 
				
			}
			if(isset($_POST['soft_whitelebal_activation'])){
				update_option('SRM_soft_whitelebal_activation', $_POST['soft_whitelebal_activation']); 
				
			}
			
			if(isset($_POST['allowed'])){
				update_option('SRM_allowed', $_POST['allowed']); 
				
			}

	
		echo "sucess";
		die();
	}
	
}