<?php

/**
 * Base controller
 */
class Srm_Base
{	
	 /**
     * Register services
     */
    public function register(){
		add_action('admin_enqueue_scripts', [$this, 'addScripts']);
        add_action('admin_menu', [$this, 'addMenu']);
	}
	  public static function activate(){
        global $wpdb;
     

        if(!file_exists(self::getBackupDirectory())){
            mkdir(self::getBackupDirectory());
        }
        if(!file_exists(self::getDownloadDirectory())){
            mkdir(self::getDownloadDirectory());
        }

        if(get_option('SRM_soft_whitelebal_activation') && get_option('SRM_soft_whitelebal_activation')=="1"){
            update_option('SRM_plugin_name',null);  
            update_option('SRM_plugin_description',null);  
            update_option('SRM_plugin_author',null);  
            update_option('SRM_plugin_author_URI',null);  
            update_option('SRM_enable_white_label',null);  
            update_option('SRM_soft_whitelebal_activation',null);  
            update_option('SRM_allowed',null); 
        }

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta("CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "srclubplugins_history` (
                `id` bigint(21) unsigned NOT NULL AUTO_INCREMENT,
                `type` VARCHAR(20) NOT NULL,
                `action` VARCHAR(20) NOT NULL,
                `version` VARCHAR(20) NOT NULL,
                `date` VARCHAR(19) NOT NULL,
                `path` VARCHAR(200) NOT NULL,
                `product_name` VARCHAR(100) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `id` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
	  }
	 
	 /**
     * Show plugin version
     *
     * @return string
     */
    public static function showVersion()
    {
        $plugin_data = get_plugin_data(SRM_PLUGIN_DIR . 'srmehranclub_automatic_update.php');

        return "<p style='float: right'>Installed version <b>".$plugin_data['Version']."</b></p>";
    }

   
	  
	 /**
     * This function is used for adding menu and submenus
     *
     *
     * @return  void
     */
    public function addMenu()
    {
		 $capability = apply_filters('ilm_filter_main_permission_check', 'administrator');
        if (!current_user_can($capability)) {
            return;
        }
		
		 if (!SRM_License::validate_license()) {
            add_menu_page(
                SRMA_GLOBAL_PLUGIN_NAME,
                SRMA_GLOBAL_PLUGIN_NAME,
                'administrator',
                'srm-license',
                [SRM_License::class, 'init'],
               'dashicons-plugins-checked'
            );

            return;
        }
		if(!Srm_Setup::isWhiteLabel()){
			add_menu_page(
                SRMA_GLOBAL_PLUGIN_NAME,
                SRMA_GLOBAL_PLUGIN_NAME,
                'administrator',
                'srm-setup',
                [Srm_Setup::class, 'init'],
               'dashicons-plugins-checked'
            );
				return;
		}
		add_menu_page(
            SRMA_GLOBAL_PLUGIN_NAME,
            SRMA_GLOBAL_PLUGIN_NAME,
            'edit_posts',
            SRM_GLOBAL_PLUGIN_LINK,
            [Srm_Dashboard::class, 'init'],
            'dashicons-plugins-checked'
        );

        if(Srm_Whitelebel::isWhiteLebelEnable()){
            add_submenu_page(
                SRM_GLOBAL_PLUGIN_LINK,
                __('Backups', 'ilm'),
                __('Backups', 'ilm'),
                'manage_categories',
                'srm-plugin-backups',
                [SRM_Files::class, 'init']
            );
            
            add_submenu_page(
                SRM_GLOBAL_PLUGIN_LINK,
                __('Activities', 'ilm'),
                __('Activities', 'ilm'),
                'manage_categories',
                'srm-plugin-activities',
                [SRM_Activities::class, 'init']
            );
            return;
        }


        add_submenu_page(
            SRM_GLOBAL_PLUGIN_LINK,
            __('Check Updates', 'ilm'),
            __('Check Updates', 'ilm'),
            'manage_categories',
            'srm-check-updates',
            [Srm_CheckUpdates::class, 'init']
        );

        add_submenu_page(
            SRM_GLOBAL_PLUGIN_LINK,
            __('Backups', 'ilm'),
            __('Backups', 'ilm'),
            'manage_categories',
            'srm-plugin-backups',
            [SRM_Files::class, 'init']
        );

        if(SRM_License::isMembership()){

        add_submenu_page(
            SRM_GLOBAL_PLUGIN_LINK,
            __('White labeling', 'ilm'),
            __('White labeling', 'ilm'),
            'manage_categories',
            'srm-plugin-whitelebel',
            [Srm_Whitelebel::class, 'init']
        );

        add_submenu_page(
            SRM_GLOBAL_PLUGIN_LINK,
            __('Activities', 'ilm'),
            __('Activities', 'ilm'),
            'manage_categories',
            'srm-plugin-activities',
            [SRM_Activities::class, 'init']
        );
    }
        

       
        // add_submenu_page(
        //     SRM_GLOBAL_PLUGIN_LINK,
        //     __('Files', 'ilm'),
        //     __('Files', 'ilm'),
        //     'manage_categories',
        //     'srm-plugin-files',
        //     [SRM_Files::class, 'init']
        // );
		add_submenu_page(
            SRM_GLOBAL_PLUGIN_LINK,
            __('License', 'ilm'),
            __('License', 'ilm'),
            'manage_categories',
            'srm-license',
            [SRM_License::class, 'init']
        );
	}
	
	/**
     * Add scripts to the admin panel
     *
     * @param $hook
     */
    public static function addScripts($hook)
    {
        global $wpdb;
		 $style_path = 'css/sr_admin.css';
        $f_path = SRM_PLUGIN_DIR.$style_path;
        $ver = filemtime($f_path);
 
        wp_register_style('srm_admin_style', SRM_PLUGIN_URL.$style_path, $deps=[], $ver);
        wp_enqueue_style('srm_admin_style'); 

        wp_register_style('srm_admin_fontstyle', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css?ver=4.9.8', $deps=[], $ver);
        wp_enqueue_style('srm_admin_fontstyle'); 

		$js_path = 'js/admin_script.js';
        $fjs_path = SRM_PLUGIN_DIR.$js_path;
        $ver = filemtime($fjs_path); 

        wp_register_script('srm_admin_sweetalert2','https://cdn.jsdelivr.net/npm/sweetalert2@11');
        wp_enqueue_script('srm_admin_sweetalert2');

		wp_register_script('srm_admin_validate', SRM_PLUGIN_URL.'js/jquery.validate.min.js');
        wp_enqueue_script('srm_admin_validate');
		wp_register_script('srm_admin_script', SRM_PLUGIN_URL.$js_path.'?v='.time());
        wp_enqueue_script('srm_admin_script');
		
		
		 $ajax_url = admin_url('admin-ajax.php');

        $script_params = [];
        $script_params['ajax_url'] = $ajax_url;

        wp_localize_script('ilm_admin_script', 'srm_ajax', $script_params);

        wp_register_script('srm_sweetalert2', SRM_PLUGIN_URL.'/js/sweetalert2.min.js');

        wp_register_script('srm_PluginDashboard', SRM_PLUGIN_URL.'/js/PluginDashboard.js'.'?v='.time());

        wp_enqueue_script('srm_PluginDashboard');
        if(!empty($_GET['page']) && $_GET['page']=="srm-automatic-upgrade"){
           $downloads = array();

            if($ret = $wpdb->get_results('SELECT * FROM `'.$wpdb->prefix.'srclubplugins_history` WHERE `action`="download"', ARRAY_A)){
                foreach($ret as $d){
                    $downloads[$d['product_name']] = array('version' => $d['version'], 'id' => $d['id']);
                }
            }  



             $script_params['downloadedItemList'] = $downloads;
                $plugins = get_plugins();
                $themes = wp_get_themes();
                 $script_params['plugins'] = $plugins;
                 $script_params['themes'] = $themes;
                   $script_params['isWhiteLebelEnable'] = Srm_Whitelebel::isWhiteLebelEnable();

        }


         wp_localize_script('srm_PluginDashboard', 'srm_ajax', $script_params);
	}

    public static function formatSize($size){
        if($size < 1024){
            return $size.' bytes';
        } elseif($size < 1024*1024){
            return round($size/1024, 2).' KB';
        } elseif($size < 1024*1024*1024){
            return round($size/1024/1024, 2).' MB';
        }
    }


    public static function sendCurlRequest($requestUrl,$postRequest=array(),$requestType='POST'){
        $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => LICENSE_SERVER_URL.'/'.$requestUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER=>false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $requestType,
                CURLOPT_POSTFIELDS => $postRequest,
                CURLOPT_HTTPHEADER => array(
                'Cookie: digits_countrycode=1'
                ),
            ));
				
				$response = curl_exec($curl);
				
				curl_close($curl);
			return json_decode($response);
    }


    
    public static function getBackupDirectory() {
        return WP_CONTENT_DIR . '/srplugin_backups/';
    }
    
    public static  function getDownloadDirectory() {
        return WP_CONTENT_DIR . '/srplugin_downloads/';
    }
    
    public static function getDownloadUrl() {
        return WP_CONTENT_DIR . '/srplugin_downloads/';
    }
    
    public static function getBackupUrl() {
        return WP_CONTENT_DIR . '/srplugin_backups/';
    }

    public static function mehran_request_ajax($url, $prams = null){

        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, $url);
    
        if( $prams != null ){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $prams ));
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $server_output = curl_exec($ch);
    
        curl_close ($ch);
    
        return $server_output;
    }
    
}