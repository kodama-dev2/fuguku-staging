<?php
/*
    Plugin Name: SR Automatic Upgrade
    Description: Easily upgrade downloaded plugins and themes from Srmehranclub.com in just two clicks.
    Version: 5.0.8
    Author: SrmehranClub
    Author URI: https://srmehranclub.com/
*/
 
global $wpdb;

//autoloader
spl_autoload_register( 'srm_autoloader' );
function srm_autoloader( $class_name ) {
	
    if ( false !== strpos( $class_name, 'Srm' ) ) {
        $classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
        $class_file = str_replace( '_', DIRECTORY_SEPARATOR, $class_name ) . '.php';
        require_once $classes_dir . $class_file;
    }
}

define( 'SRM_VERSION_NUMBER', '5.0.7');
define( 'SRM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define( 'SRM_PLUGIN_URL', plugin_dir_url(__FILE__));
define( 'SRM_PLUGIN_NAME', plugin_basename( __FILE__ ));
if(get_option('SRM_plugin_name')){
    define('SRMA_GLOBAL_PLUGIN_NAME',get_option('SRM_plugin_name'));
}else{
    define('SRMA_GLOBAL_PLUGIN_NAME','SR Automatic Upgrade');
}

define('SRM_GLOBAL_PLUGIN_LINK','srm-automatic-upgrade');
define('SRM_OPTION_LICENSE_KEY','sr_license_key');
define('SRM_OPTION_LICENSE_STATUS','sr_license_status');
define('SRM_OPTION_LICENSE_LAST_ERROR','sr_license_last_error');
define('GLBLICENSE_SERVER_URL','https://database.srmehranclub.com/api');

Srm_Init::register_services();
register_activation_hook(__FILE__, [Srm_Base::class, 'activate'] );


add_filter( 'all_plugins', 'srma_all_plugins' );
function srma_all_plugins( $all_plugins ) {
   
    foreach ( $all_plugins as $plugin_file => $plugin_data ) {
    	if ('srmehranclub_automatic_update/srmehranclub_automatic_update.php' ===$plugin_file ) {
            if(get_option('SRM_plugin_name') && get_option('SRM_plugin_description') && get_option('SRM_plugin_author') && get_option('SRM_plugin_author_URI')){

                $all_plugins[$plugin_file]=[
                    'Name'=>get_option('SRM_plugin_name'),
                    'Description'=>get_option('SRM_plugin_description'),
                    'AuthorName'=>get_option('SRM_plugin_author'),
                    'AuthorURI'=>get_option('SRM_plugin_author_URI'),
                    'TextDomain'=>'',
                    'Author'=>get_option('SRM_plugin_author'),
                    'Version'=>SRM_VERSION_NUMBER,
                    'PluginURI'=>''
                 ];
            }else{
                $all_plugins[$plugin_file]=$plugin_data;
            }
    		
    	}
    }

    return $all_plugins;
}

require_once('SrCall.php');

