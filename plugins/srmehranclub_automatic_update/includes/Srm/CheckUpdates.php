<?php

/**
 * Base controller
 */
class Srm_CheckUpdates
{   
    /**
     * Register services
     */
    public static function register(){
           add_action('wp_ajax_srm_update_item', [__CLASS__, 'srm_update_item_callback']);
           add_action('wp_ajax_srm_check_update', [__CLASS__, 'srm_check_update_callback']);
    }

    /**
     * Check for updates for a specific plugin/theme
     */

 public static function srm_check_update_callback() {
    // Validate input data
    $type = sanitize_text_field($_POST['type'] ?? '');
    $file = sanitize_text_field($_POST['file'] ?? '');
    $name = sanitize_text_field($_POST['name'] ?? '');
    
    if (empty($type) || empty($file) || empty($name)) {
        wp_send_json_error([
            'message' => 'Missing required parameters',
            'code'    => 'MISSING_PARAMETERS'
        ]);
    }

    try {
        if ($type === 'plugin') {
            delete_site_transient('update_plugins');
            wp_update_plugins();
            $updates = get_site_transient('update_plugins');

            if (!empty($updates->response[$file])) {
                $update_data = $updates->response[$file];
                return wp_send_json_success([
                    'has_update'  => true,
                    'new_version' => $update_data->new_version,
                    'package'     => $update_data->package ?? '',
                    'message'     => 'Update available (WordPress core)'
                ]);
            }

        } elseif ($type === 'theme') {
            delete_site_transient('update_themes');
            wp_update_themes();
            $updates = get_site_transient('update_themes');

            if (!empty($updates->response[$file])) {
                $update_data = $updates->response[$file];
               return wp_send_json_success([
                    'has_update'  => true,
                    'new_version' => $update_data['new_version'],
                    'package'     => $update_data['package'] ?? '',
                    'message'     => 'Update available (WordPress core)'
                ]);
            }
        }

        // 🔄 If no update found in WordPress core → fallback to custom API
        $api_response = Srm_ApiHandler::get('v2/forcecheck-version', [
            'name' => $name,
            'type' => $type,
        ]);
      

        if (!empty($api_response['success']) && !empty($api_response['data']['data']['prod_version'])) {
           $dataResponse =$api_response['data'];
           return wp_send_json_success([
                'has_update'  => true,
                'new_version' => $dataResponse['data']['prod_version'],
                'package'     => $dataResponse['data']['id'] ?? '',
                'message'     => 'Update available (Custom API)'
            ]);
        }

        // No update anywhere
       return wp_send_json_success([
            'has_update' => false,
            'message'    => 'No updates available'
        ]);

    } catch (Exception $e) {
       return wp_send_json_error([
            'message' => 'Error checking for updates: ' . $e->getMessage(),
            'code'    => 'UPDATE_CHECK_ERROR'
        ]);
    }
}


   public static function srm_update_item_callback() {
        // Validate input data
        $type = sanitize_text_field($_POST['type'] ?? '');
        $file = sanitize_text_field($_POST['file'] ?? '');
        $name = sanitize_text_field($_POST['name'] ?? '');
        $package = esc_url_raw($_POST['package'] ?? '');
        
        if (empty($type) || empty($file) || empty($name)) {
            wp_send_json_error([
                'message' => 'Missing required parameters',
                'code' => 'MISSING_PARAMETERS'
            ]);
            return;
        }
        try {
            if ($type === 'plugin') {
                // Check if plugin exists
                $plugin_path = WP_PLUGIN_DIR . '/' . $file;
                if (!file_exists($plugin_path)) {
                    wp_send_json_error([
                        'message' => 'Plugin file not found: ' . $file,
                        'code' => 'PLUGIN_NOT_FOUND'
                    ]);
                    return;
                }
                
                // Check if plugin is currently active
                $was_active = is_plugin_active($file);
                
                // Deactivate plugin before update
                if ($was_active) {
                    deactivate_plugins($file, true);
                    if (is_plugin_active($file)) {
                        wp_send_json_error([
                            'message' => 'Failed to deactivate plugin before update',
                            'code' => 'DEACTIVATION_FAILED'
                        ]);
                        return;
                    }
                }


                Srm_BackupManager::create_backup('plugin', $file);

                
                // Determine update method based on package URL
                if (strpos($package, 'https://downloads.wordpress.org') === 0) {
                    $result = self::update_free_plugin($file, $package, $name);
                } else {
                    $result = self::update_paid_plugin($file, $package, $name, $type);
                }
                
                if (is_wp_error($result)) {
                    wp_send_json_error([
                        'message' => $result->get_error_message(),
                        'code' => $result->get_error_code()
                    ]);
                    return;
                }
                
                // Reactivate plugin if it was previously active
                if ($was_active) {
                    $activation_result = activate_plugin($file);
                    if (is_wp_error($activation_result)) {
                        wp_send_json_error([
                            'message' => 'Plugin updated successfully but failed to reactivate: ' . $activation_result->get_error_message(),
                            'code' => 'REACTIVATION_FAILED'
                        ]);
                        return;
                    }
                }
                
                wp_send_json_success([
                    'message' => 'Plugin updated successfully' . ($was_active ? ' and reactivated' : ''),
                    'plugin' => $name,
                    'file' => $file,
                    'was_active' => $was_active
                ]);
                
            } else if ($type === 'theme') {
                // Determine update method based on package URL
                if (strpos($package, 'https://downloads.wordpress.org') === 0) {
                    $result = self::update_free_theme($file, $package, $name);
                } else {
                    $result = self::update_paid_theme($file, $package, $name, $type);
                }

               Srm_BackupManager::create_backup('theme', $file);
                
                if (is_wp_error($result)) {
                    wp_send_json_error([
                        'message' => $result->get_error_message(),
                        'code' => $result->get_error_code()
                    ]);
                    return;
                }
                
                wp_send_json_success([
                    'message' => 'Theme updated successfully',
                    'theme' => $name,
                    'file' => $file
                ]);
            }
            
        } catch (Exception $e) {
            wp_send_json_error([
                'message' => 'Unexpected error: ' . $e->getMessage(),
                'code' => 'UNEXPECTED_ERROR'
            ]);
        }
    }

    /**
     * Update free plugin from WordPress.org repository
     */
   private static function update_free_plugin($plugin_file, $package_url, $plugin_name) {
    // Include necessary WordPress files
    if (!function_exists('request_filesystem_credentials')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
    if (!class_exists('Plugin_Upgrader')) {
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    }

    // Setup filesystem
    $credentials = request_filesystem_credentials('', '', false, WP_PLUGIN_DIR);
    if ($credentials === false) {
        return new WP_Error('FILESYSTEM_ERROR', 'Could not access filesystem');
    }

    if (!WP_Filesystem($credentials)) {
        return new WP_Error('FILESYSTEM_ERROR', 'Could not initialize filesystem');
    }

    // Create upgrader instance
    $upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin());

    // Force update using custom package
    $result = $upgrader->run([
        'package'           => $package_url,
        'destination'       => WP_PLUGIN_DIR,
        'clear_destination' => true,
        'clear_working'     => true,
        'hook_extra'        => [
            'plugin' => $plugin_file,
            'type'   => 'plugin',
            'action' => 'update',
        ],
    ]);

    if (is_wp_error($result)) {
        return $result;
    }

    if ($result === false) {
        return new WP_Error('UPDATE_FAILED', 'Plugin update failed for unknown reason');
    }

    return true;
}

/**
 * Update free theme from WordPress.org repository or custom URL
 */
private static function update_free_theme($theme_slug, $package_url, $theme_name) {
    // Include necessary WordPress files
    if (!function_exists('request_filesystem_credentials')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
    if (!class_exists('Theme_Upgrader')) {
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    }

    // Setup filesystem
    $credentials = request_filesystem_credentials('', '', false, get_theme_root());
    if ($credentials === false) {
        return new WP_Error('FILESYSTEM_ERROR', 'Could not access filesystem');
    }

    if (!WP_Filesystem($credentials)) {
        return new WP_Error('FILESYSTEM_ERROR', 'Could not initialize filesystem');
    }

    // Create upgrader instance
    $upgrader = new Theme_Upgrader(new Automatic_Upgrader_Skin());

    // Force update using custom package
    $result = $upgrader->run([
        'package'           => $package_url,
        'destination'       => get_theme_root(),
        'clear_destination' => true,
        'clear_working'     => true,
        'hook_extra'        => [
            'theme'  => $theme_slug,
            'type'   => 'theme',
            'action' => 'update',
        ],
    ]);

    if (is_wp_error($result)) {
        return $result;
    }

    if ($result === false) {
        return new WP_Error('UPDATE_FAILED', 'Theme update failed for unknown reason');
    }

    return true;
}

   /**
     * Update paid plugin from custom URL
     */
    private static function update_paid_plugin($plugin_file, $package_url, $plugin_name,$type) {
        // Include necessary WordPress files
        if (!function_exists('download_url')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        if (!class_exists('WP_Upgrader')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        }
        
        // Setup filesystem
        $credentials = request_filesystem_credentials('', '', false, WP_PLUGIN_DIR);
        if ($credentials === false) {
            return new WP_Error('FILESYSTEM_ERROR', 'Could not access filesystem');
        }
        
        if (!WP_Filesystem($credentials)) {
            return new WP_Error('FILESYSTEM_ERROR', 'Could not initialize filesystem');
        }
        
        // Download the plugin package
        $download_file = Srm_ApiHandler::get('v2/check-version',[
            'name'=>$plugin_name,
            'type'=>$type
        ]);
     
         $upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin());

            // Force update using custom package
            $result = $upgrader->run([
                'package'           => $download_file['data']['url'],
                'destination'       => WP_PLUGIN_DIR,
                'clear_destination' => true,
                'clear_working'     => true,
                'hook_extra'        => [
                    'plugin' => $plugin_file,
                    'type'   => 'plugin',
                    'action' => 'update',
                ],
            ]);

            if (is_wp_error($result)) {
                return $result;
            }

            if ($result === false) {
                return new WP_Error('UPDATE_FAILED', 'Plugin update failed for unknown reason');
            }
        
        return true;
    }

/**
 * Update paid theme from custom URL
 */
private static function update_paid_theme($theme_slug, $package_url, $theme_name,$type) {
    // Include necessary WordPress files
    if (!function_exists('request_filesystem_credentials')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
    if (!class_exists('Theme_Upgrader')) {
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    }

    // Setup filesystem
    $credentials = request_filesystem_credentials('', '', false, get_theme_root());
    if ($credentials === false) {
        return new WP_Error('FILESYSTEM_ERROR', 'Could not access filesystem');
    }

    if (!WP_Filesystem($credentials)) {
        return new WP_Error('FILESYSTEM_ERROR', 'Could not initialize filesystem');
    }

     // Download the theme package
    $download_file = Srm_ApiHandler::get('v2/check-version',[
        'name'=>$theme_name,
        'type'=>$type
    ]);

   


      $result=Srm_RobustThemeInstaller::install_theme($download_file['data']['url'], $theme_slug);


    



    if (is_wp_error($result)) {
        return $result;
    }

    if ($result === false) {
        return new WP_Error('UPDATE_FAILED', 'Theme update failed for unknown reason');
    }

    return true;
}

    public static function init(){
        $all_plugins = self::getAllInstalledPlugins();
        $all_themes = self::getAllInstalledThemes();
        $available_updates = self::getPluginUpdatesFromCore();
        $themes_updates = self::getThemeUpdatesFromCore();


        include SRM_PLUGIN_DIR . '/templates/Srm_checkUpdates.php';
    }

    /**
     * Get all installed plugins with their information
     */
    public static function getAllInstalledPlugins() {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $all_plugins = get_plugins();
        $available_updates = self::getPluginUpdatesFromCore();
        $plugins_data = [];
        
        foreach ($all_plugins as $plugin_file => $plugin_info) {
            $has_update = isset($available_updates[$plugin_file]);
            $update_info = $has_update ? $available_updates[$plugin_file] : null;
            
            $plugins_data[$plugin_file] = [
                'name' => $plugin_info['Name'],
                'version' => $plugin_info['Version'],
                'description' => $plugin_info['Description'],
                'author' => $plugin_info['Author'],
                'is_active' => is_plugin_active($plugin_file),
                'has_update' => $has_update,
                'new_version' => $has_update ? $update_info['new_version'] : null,
                'package' => $has_update ? $update_info['package'] : '',
                'slug' => $has_update ? $update_info['slug'] : dirname($plugin_file)
            ];
        }
        
        return $plugins_data;
    }

    /**
     * Get all installed themes with their information
     */
    public static function getAllInstalledThemes() {
        $all_themes = wp_get_themes();
        $themes_updates = self::getThemeUpdatesFromCore();
        $themes_data = [];
        
        foreach ($all_themes as $theme_slug => $theme_obj) {
            $has_update = isset($themes_updates[$theme_slug]);
            $update_info = $has_update ? $themes_updates[$theme_slug] : null;
            
            $themes_data[$theme_slug] = [
                'name' => $theme_obj->get('Name'),
                'version' => $theme_obj->get('Version'),
                'description' => $theme_obj->get('Description'),
                'author' => $theme_obj->get('Author'),
                'is_active' => (wp_get_theme()->get_stylesheet() === $theme_slug),
                'has_update' => $has_update,
                'new_version' => $has_update ? $update_info['new_version'] : null,
                'package' => $has_update ? $update_info['package'] : ''
            ];
        }
        
        return $themes_data;
    }

     public static function getPluginUpdatesFromCore() {
        // Get the update transient that WordPress maintains
        $update_plugins = get_site_transient('update_plugins');
        
        $available_updates = [];
        
        if (!empty($update_plugins->response)) {
            foreach ($update_plugins->response as $plugin_file => $plugin_data) {
                 $plugin_info = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_file);

               $available_updates[$plugin_file] = [
                        'new_version'     => $plugin_data->new_version,
                        'current_version' => $plugin_info['Version'] ?? 'Unknown',
                        'plugin_name'     => $plugin_data->plugin ?? 'Unknown',
                        'slug'            => $plugin_data->slug ?? '',
                        'url'             => $plugin_data->url ?? '',
                        'package'         => $plugin_data->package ?? '',
                        'name'            => $plugin_info['Name'] ?? 'Unknown',
                    ];
            }
        }
        
        return $available_updates;
    }

     public static function getThemeUpdatesFromCore() {
        // Get the update transient that WordPress maintains
        $update_themes = get_site_transient('update_themes');
        
        $available_updates = [];
        
        if (!empty($update_themes->response)) {
            foreach ($update_themes->response as $theme_slug => $theme_data) {
                $theme_obj = wp_get_theme($theme_slug);
                $available_updates[$theme_slug] = [
                    'new_version' => $theme_data['new_version'],
                    'current_version' => $theme_obj->get('Version'),
                    'theme_name' => $theme_obj->get('Name'),
                    'url' => $theme_data['url'] ?? '',
                    'package' => $theme_data['package'] ?? ''
                ];
            }
        }
        
        return $available_updates;
    }
}