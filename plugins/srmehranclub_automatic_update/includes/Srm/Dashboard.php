<?php

/**
 * Base controller
 */
class Srm_Dashboard
{   
    public static $getInstalled=array();
    private static $allowed_extensions = ['zip'];
    private static $max_file_size = 50 * 1024 * 1024; // 50MB
     /**
     * Register services
     */
    public static function register(){


        add_action( 'wp_ajax_download_item', array(__CLASS__, 'download_item'));
        add_action( 'wp_ajax_activate_item', array(__CLASS__, 'activate_item'));

    }

    public static function download_item(){
          try {
            // Sanitize and validate input
            $item_id = self::validate_item_id($_POST['item_id'] ?? '');
            if (!$item_id) {
                return self::send_error('Invalid item ID');
            }
            
            // Get item info from API
            $item_info = self::get_item_info($item_id);
            if (!$item_info) {
                return self::send_error('Failed to retrieve item information');
            }
            
            // Download and install
            $result = self::process_installation($item_info);
            
            if ($result['success']) {
                return wp_send_json_success($result['message']);
            } else {
                return self::send_error($result['message']);
            }
            
        } catch (Exception $e) {
            error_log('SRM Installer Error: ' . $e->getMessage());
            return self::send_error('Installation failed: ' . $e->getMessage());
        }

    }


    /**
     * Validate item ID
     */
    private static function validate_item_id($item_id) {
        $item_id = sanitize_text_field($item_id);
        return is_numeric($item_id) ? intval($item_id) : false;
    }
    
     /**
     * Get item information from API
     */
    private static function get_item_info($item_id) {
        $response = Srm_ApiHandler::get('products/' . $item_id);
        
        if (empty($response['success']) || $response['success'] != 1) {
            return false;
        }
        
        $data = $response['data']['data'] ?? [];
        
        // Validate required fields
        $required_fields = ['id', 'title', 'type_name', 'version', 'file_path'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }
        
        // Validate type
        if (!in_array($data['type_name'], ['plugin', 'theme'])) {
            throw new Exception('Invalid item type');
        }
        
        return $data;
    }

    public static function  get_first_word($string) {
    // Trim to remove extra spaces
    $string = trim($string);

    // Split by spaces
    $words = explode(' ', $string);

    // Return the first word in lowercase
    return strtolower($words[0] ?? '');
}

     /**
     * Process the installation
     */
    private static function process_installation($item_data) {
        global $wpdb;
        
        $is_plugin = ($item_data['type_name'] === 'plugin');
        $temp_dir = self::get_temp_directory();
        
        try {
            if($is_plugin){


                    // Clean up any existing temp files
                    self::cleanup_temp_directory($temp_dir);
                    
                    // Download the file
                    $download_path = self::download_file($item_data);
                    
                    // Log download
                    self::log_history('download', $item_data, $download_path);
                    
                    // Extract and validate
                    $extracted_path = self::extract_archive($download_path, $temp_dir, $is_plugin);
                    
                    // Create backup if existing installation found
                    $backup_created = self::create_backup_if_needed($extracted_path, $item_data, $is_plugin);
                    
                    // Install the plugin/theme
                    self::install_item($extracted_path, $item_data, $is_plugin);
                    
                    // Cleanup
                    self::cleanup_temp_directory($temp_dir);
                    unlink($download_path);
                    
                    $type_name = $is_plugin ? 'Plugin' : 'Theme';
                    $message = "{$type_name} '{$item_data['title']}' installed successfully!";
                    
                    if ($backup_created) {
                        $message .= " Previous version backed up.";
                    }
                    
                    return ['success' => true, 'message' => $message];
            }else{

                $downloadPath=$item_data['file_path'];

                $theme_slug= self::get_first_word($item_data['title']);

                $result=Srm_RobustThemeInstaller::install_theme($downloadPath, $theme_slug);
                  self::log_history('download', $item_data, $downloadPath);
                 return ['success' => true, 'message' =>'Theme installed successfully'];
            }
            
        } catch (Exception $e) {
            // Cleanup on error
            self::cleanup_temp_directory($temp_dir);
            if (isset($download_path) && file_exists($download_path)) {
                unlink($download_path);
            }
            throw $e;
        }
    }


    public static function runUpdateExtranel($download_file,$plugin_file,$plugin_name,$type){
         global $wpdb;

          if (!function_exists('request_filesystem_credentials')) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }
            if (!class_exists('Plugin_Upgrader')) {
                require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            }
        
         try {
            $upgrader = new Plugin_Upgrader(new Automatic_Upgrader_Skin());

            // Force update using custom package
            $result = $upgrader->run([
                'package'           => $download_file,   // 👈 your custom zip file URL
                'destination'       => WP_PLUGIN_DIR,
                'clear_destination' => true,  // overwrite existing plugin
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
           

          

         } catch (Exception $e) {
            // Cleanup on error
            self::cleanup_temp_directory($temp_dir);
            if (isset($download_path) && file_exists($download_path)) {
                unlink($download_path);
            }
            throw $e;
        }
    }


    /**
     * Download file from URL
     */
    private static function download_file($item_data) {
        $download_dir = Srm_Base::getDownloadDirectory();
        if (!wp_mkdir_p($download_dir)) {
            throw new Exception('Cannot create download directory');
        }
        
        $filename = 'download_' . $item_data['id'] . '_' . time() . '.zip';
        $download_path = $download_dir . $filename;
        
        // Use WordPress HTTP API for better security
        $response = wp_remote_get($item_data['file_path'], [
            'timeout' => 300,
            'stream' => true,
            'filename' => $download_path
        ]);
        
        if (is_wp_error($response)) {
            throw new Exception('Download failed: ' . $response->get_error_message());
        }
        
        if (wp_remote_retrieve_response_code($response) !== 200) {
            throw new Exception('Download failed with HTTP code: ' . wp_remote_retrieve_response_code($response));
        }
        
        // Validate file
        if (!file_exists($download_path) || filesize($download_path) === 0) {
            throw new Exception('Downloaded file is empty or corrupted');
        }
        
        if (filesize($download_path) > self::$max_file_size) {
            unlink($download_path);
            throw new Exception('File size exceeds maximum limit');
        }
        
        return $download_path;
    }
    
    /**
     * Extract archive and validate contents
     */
    private static function extract_archive($archive_path, $temp_dir, $is_plugin) {
        if (!class_exists('ZipArchive')) {
            throw new Exception('ZipArchive class not available');
        }
        
        $zip = new ZipArchive();
        $result = $zip->open($archive_path);
        
        if ($result !== TRUE) {
            throw new Exception('Cannot open ZIP file: ' . $result);
        }
        
        // Security check: scan for malicious files
        self::validate_zip_contents($zip, $is_plugin);
        
        // Extract to temp directory
        if (!$zip->extractTo($temp_dir)) {
            $zip->close();
            throw new Exception('Failed to extract archive');
        }
        
        $zip->close();
        
        // Find the main directory
        $extracted_dir = self::find_main_directory($temp_dir);
        if (!$extracted_dir) {
            throw new Exception('Cannot determine main directory structure');
        }
        
        return $extracted_dir;
    }
    
    /**
     * Validate ZIP contents for security
     */
    private static function validate_zip_contents($zip, $is_plugin) {
        $dangerous_files = ['.php', '.js', '.htaccess'];
        $suspicious_patterns = ['../', '../', '\\..\\', '__MACOSX'];
        
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            
            // Check for directory traversal
            foreach ($suspicious_patterns as $pattern) {
                if (strpos($filename, $pattern) !== false) {
                    throw new Exception('Archive contains suspicious file paths');
                }
            }
            
            // For plugins, check for multiple ZIP files (which could indicate nested malware)
            if ($is_plugin && pathinfo($filename, PATHINFO_EXTENSION) === 'zip') {
                throw new Exception('Plugin archives cannot contain nested ZIP files');
            }
        }
    }
    
    /**
     * Find main directory in extracted files
     */
    private static function find_main_directory($temp_dir) {
        $contents = scandir($temp_dir);
        $directories = array_filter($contents, function($item) use ($temp_dir) {
            return $item !== '.' && $item !== '..' && is_dir($temp_dir . '/' . $item);
        });
        
        if (count($directories) === 1) {
            return $temp_dir . '/' . reset($directories);
        }
        
        // Look for installable file
        foreach ($contents as $item) {
            if (preg_match('/^installable/', $item)) {
                $installable_path = file_get_contents($temp_dir . '/' . $item);
                $installable_path = trim($installable_path, '/');
                
                $full_path = $temp_dir . '/' . $installable_path;
                if (is_dir($full_path)) {
                    return $full_path;
                }
            }
        }
        
        return false;
    }


    public static function get_plugin_path_by_slug($slug) {
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugins = get_plugins();

    foreach ($plugins as $plugin_file => $plugin_data) {
        // Matches either "slug/" at start or "slug.php" as main file
        if (strpos($plugin_file, $slug . '/') === 0 || $plugin_file === $slug . '.php') {
            return $plugin_file; // e.g. wordpress-seo-premium/wp-seo-premium.php
        }
    }

    return false; // Not found
}
    
    /**
     * Create backup if existing installation found
     */
    public static function create_backup_if_needed($source_path, $item_data, $is_plugin) {
        global $wpdb;
        if($is_plugin){
           $plugin_name = basename($source_path);
            $pluginFilePath=self::get_plugin_path_by_slug($plugin_name);
            Srm_BackupManager::create_backup('plugin', $pluginFilePath);  
        }
 
      

        // $target_dir = ($is_plugin ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/themes') . '/' . $plugin_name;
        
        // if (!is_dir($target_dir)) {
        //     return false;
        // }
        
        // $backup_dir = Srm_Base::getBackupDirectory();
        // if (!wp_mkdir_p($backup_dir)) {
        //     throw new Exception('Cannot create backup directory');
        // }
        
        // $backup_filename = $plugin_name . '-' . date('Y-m-d-His') . '.zip';
        // $backup_path = $backup_dir . $backup_filename;
        
        // if (!self::create_zip($target_dir, $backup_path)) {
        //     throw new Exception('Failed to create backup');
        // }
        
        // // Log backup
        // self::log_history('backup', $item_data,'srplugin_backups/'.$backup_filename);
        
        return true;
    }

      public static function create_backup_if_neededThemeOnly($source_path, $item_data) {
        global $wpdb;
        
      
        $target_dir = WP_CONTENT_DIR . '/themes/' . $source_path;
    
        
        if (!is_dir($target_dir)) {
            return false;
        }
        
        $backup_dir = Srm_Base::getBackupDirectory();
        if (!wp_mkdir_p($backup_dir)) {
            throw new Exception('Cannot create backup directory');
        }
        
        $backup_filename = $source_path . '-' . date('Y-m-d-His') . '.zip';
        $backup_path = $backup_dir . $backup_filename;
        
        if (!self::create_zip($target_dir, $backup_path)) {
            throw new Exception('Failed to create backup');
        }
        
        // Log backup
        self::log_history('backup', $item_data, $backup_path);
        
        return true;
    }
    
    /**
     * Install the plugin/theme
     */
    private static function install_item($source_path, $item_data, $is_plugin) {
        $plugin_name = basename($source_path);
        $target_dir = ($is_plugin ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/themes') . '/' . $plugin_name;
        
        // Remove existing installation
        if (is_dir($target_dir)) {
            self::remove_directory($target_dir);
        }
        
        // Copy new files
        if (!self::copy_directory($source_path, $target_dir)) {
            throw new Exception('Failed to copy files to target directory');
        }
        
        // Set proper permissions
        self::set_directory_permissions($target_dir);
        
        // Log installation
        self::log_history('install', $item_data, $target_dir);
    }
    
    /**
     * Log activity to history table
     */
    private static function log_history($action, $item_data, $path) {
        global $wpdb;
        
        $history = [
            'type' => $item_data['type_name'],
            'action' => $action,
            'version' => $item_data['version'],
            'date' => current_time('mysql'),
            'path' => $path,
            'product_name' => $item_data['title']
        ];
        
        $wpdb->insert($wpdb->prefix . 'srclubplugins_history', $history);
    }
    
    /**
     * Utility functions
     */
    private static function get_temp_directory() {
        $temp_dir = Srm_Base::getDownloadDirectory() . 'temp/';
        if (!wp_mkdir_p($temp_dir)) {
            throw new Exception('Cannot create temporary directory');
        }
        return $temp_dir;
    }
    
    private static function cleanup_temp_directory($dir) {
        if (is_dir($dir)) {
            self::remove_directory($dir);
        }
    }
    
    private static function remove_directory($dir) {
        if (!is_dir($dir)) return;
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? self::remove_directory($path) : unlink($path);
        }
        rmdir($dir);
    }
    
    private static function copy_directory($source, $dest) {
        if (!is_dir($source)) return false;
        
        if (!wp_mkdir_p($dest)) return false;
        
        $files = array_diff(scandir($source), ['.', '..']);
        foreach ($files as $file) {
            $source_path = $source . '/' . $file;
            $dest_path = $dest . '/' . $file;
            
            if (is_dir($source_path)) {
                if (!self::copy_directory($source_path, $dest_path)) {
                    return false;
                }
            } else {
                if (!copy($source_path, $dest_path)) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    private static function create_zip($source, $destination) {
        if (!class_exists('ZipArchive')) return false;
        
        $zip = new ZipArchive();
        if ($zip->open($destination, ZipArchive::CREATE) !== TRUE) {
            return false;
        }
        
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $file_path = $file->getRealPath();
                $relative_path = substr($file_path, strlen($source) + 1);
                $zip->addFile($file_path, $relative_path);
            }
        }
        
        return $zip->close();
    }
    
    private static function set_directory_permissions($dir) {
        // Set appropriate permissions for WordPress
        chmod($dir, 0755);
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir)
        );
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                chmod($file->getPathname(), 0755);
            } else {
                chmod($file->getPathname(), 0644);
            }
        }
    }


     /**
     * Activate plugin or theme
     */
    public static function activate_item() {
        try {
          
             $item_info = self::get_item_info($_POST['item_id']);
            if (!$item_info) {
                return self::send_error('Failed to retrieve item information');
            }


          
            // Sanitize and validate input
            $item_type = $item_info['type_name'];
            
            $item_name = $item_info['title'];
           $item_path= self::get_activation_history_single($item_type,$item_name);
            
            if (!in_array($item_type, ['plugin', 'theme'])) {
                return self::send_error('Invalid item type');
            }
            
            if (empty($item_path) || empty($item_name)) {
                return self::send_error('Missing required parameters');
            }
            
            // Process activation
            $result = self::process_activation($item_type, $item_path, $item_name);
            
            if ($result['success']) {
                return wp_send_json_success($result);
            } else {
                return self::send_error($result['message']);
            }
            
        } catch (Exception $e) {
            error_log('SRM Activation Error: ' . $e->getMessage());
            return self::send_error('Activation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Process the activation based on item type
     */
    private static function process_activation($item_type, $item_path, $item_name) {
        if ($item_type === 'plugin') {
            return self::activate_plugin($item_path, $item_name);
        } else {
            return self::activate_theme($item_path, $item_name);
        }
    }
    
    /**
     * Activate a plugin
     */
    private static function activate_plugin($plugin_path, $plugin_name) {
        // Validate plugin file exists
        $plugin_dir =$plugin_path;
        if (!file_exists($plugin_dir)) {
            throw new Exception('Plugin file not found: ' . $plugin_path);
        }
        // find possible plugin files in this directory
        $plugin_files = glob($plugin_dir . '/*.php');

        if (empty($plugin_files)) {
                throw new Exception('No PHP files found in plugin directory: ' . $plugin_dir);
        }

        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

    


        $valid_plugin = false;
        foreach ($plugin_files as $file) {
            $plugin_data = get_plugin_data($file, false, false);

            if (!empty($plugin_data['Name'])) {
                $valid_plugin = [
                    'file' => $file,
                    'data' => $plugin_data
                ];
                break; // stop at the first valid plugin header
            }
        }

        if (!$valid_plugin) {
            throw new Exception('No valid plugin file found in: ' . $plugin_dir);
        }

        $pluginFilePath=$valid_plugin['file'];

        
        
        // Check if plugin is already active
        if (is_plugin_active($pluginFilePath)) {
            return [
                'success' => true,
                'message' => "Plugin '{$plugin_name}' is already active",
                'status' => 'already_active'
            ];
        }
       
        
        // Check for plugin dependencies if any
        $dependencies_met = self::check_plugin_dependencies($plugin_data);
        if (!$dependencies_met['met']) {
            throw new Exception('Plugin dependencies not met: ' . $dependencies_met['message']);
        }
        
        // Check WordPress and PHP version requirements
        $compatibility = self::check_plugin_compatibility($plugin_data);
        if (!$compatibility['compatible']) {
            throw new Exception('Plugin compatibility requirements not met: ' . $compatibility['message']);
        }
        
        // Activate the plugin
        $result = activate_plugin($pluginFilePath, '', false, true);
        
        if (is_wp_error($result)) {
            throw new Exception('Plugin activation failed: ' . $result->get_error_message());
        }
        
      
        
        // Log activation
        self::log_activation('plugin', $plugin_name, $pluginFilePath);
        
        // Run any post-activation hooks
        do_action('srm_plugin_activated', $pluginFilePath, $plugin_name);
        
        return [
            'success' => true,
            'message' => "Plugin '{$plugin_name}' activated successfully",
            'status' => 'activated',
            'plugin_data' => $plugin_data
        ];
    }
    
    /**
     * Activate a theme
     */
    private static function activate_theme($theme_path, $theme_name) {
        // Validate theme directory exists
        $theme_dir =  $theme_path;
        if (!is_dir($theme_dir)) {
            throw new Exception('Theme directory not found: ' . $theme_path);
        }

      
         $theme_slug = basename($theme_path);
         // Get theme object
        $theme = wp_get_theme($theme_slug);

        if (!$theme->exists()) {
            throw new Exception("Theme does not exist or is invalid: {$theme_path}");
        }

        // Check if theme is already active
        $current_theme = get_option('stylesheet');
        if ($current_theme === $theme_slug) {
            return [
                'success' => true,
                'message' => "Theme '{$theme_name}' is already active",
                'status'  => 'already_active'
            ];
        }

        // (Optional) check theme compatibility
        if (method_exists(__CLASS__, 'check_theme_compatibility')) {
            $compatibility = self::check_theme_compatibility($theme);
            if (!$compatibility['compatible']) {
                throw new Exception('Theme compatibility requirements not met: ' . $compatibility['message']);
            }
        }

        // Backup current theme
        $previous_theme = wp_get_theme();

        // Switch to the new theme
        switch_theme($theme_slug);

        // Verify theme activation
        $activated_theme = get_option('stylesheet');
        if ($activated_theme !== $theme_slug) {
            // Rollback to previous theme
            switch_theme($previous_theme->get_stylesheet());
            throw new Exception('Theme activation verification failed');
        }

        // Log activation
        self::log_activation('theme', $theme_name,  $theme_dir);

        // Run any post-activation hooks
        do_action('srm_theme_activated', $theme_slug, $theme_name, $previous_theme->get_stylesheet());

        return [
            'success'        => true,
            'message'        => "Theme '{$theme_name}' activated successfully",
            'status'         => 'activated',
            'previous_theme' => $previous_theme->get('Name'),
            'theme_data'     => [
                'name'        => $theme->get('Name'),
                'version'     => $theme->get('Version'),
                'description' => $theme->get('Description'),
                'author'      => $theme->get('Author'),
            ]
        ];
    }
    
    /**
     * Check plugin dependencies
     */
    private static function check_plugin_dependencies($plugin_data) {
        // Check for common dependency indicators in plugin data
        $requires_plugins = [];
        
        // Look for dependency information in plugin headers
        if (!empty($plugin_data['RequiresPlugins'])) {
            $required = array_map('trim', explode(',', $plugin_data['RequiresPlugins']));
            foreach ($required as $required_plugin) {
                if (!is_plugin_active($required_plugin)) {
                    $requires_plugins[] = $required_plugin;
                }
            }
        }
        
        // Check for WooCommerce dependency (common case)
        if (strpos(strtolower($plugin_data['Name']), 'woocommerce') !== false && 
            !is_plugin_active('woocommerce/woocommerce.php')) {
            $requires_plugins[] = 'WooCommerce';
        }
        
        if (!empty($requires_plugins)) {
            return [
                'met' => false,
                'message' => 'Required plugins not active: ' . implode(', ', $requires_plugins)
            ];
        }
        
        return ['met' => true];
    }
    
    /**
     * Check plugin compatibility
     */
    private static function check_plugin_compatibility($plugin_data) {
        global $wp_version;
        
        // Check WordPress version requirement
        if (!empty($plugin_data['RequiresWP'])) {
            if (version_compare($wp_version, $plugin_data['RequiresWP'], '<')) {
                return [
                    'compatible' => false,
                    'message' => "Requires WordPress {$plugin_data['RequiresWP']} or higher"
                ];
            }
        }
        
        // Check PHP version requirement
        if (!empty($plugin_data['RequiresPHP'])) {
            if (version_compare(PHP_VERSION, $plugin_data['RequiresPHP'], '<')) {
                return [
                    'compatible' => false,
                    'message' => "Requires PHP {$plugin_data['RequiresPHP']} or higher"
                ];
            }
        }
        
        // Check if plugin has been tested up to current WP version
        if (!empty($plugin_data['TestedUpTo'])) {
            if (version_compare($wp_version, $plugin_data['TestedUpTo'], '>')) {
                // This is just a warning, not a hard requirement
                error_log("Warning: Plugin may not be compatible with WordPress {$wp_version}");
            }
        }
        
        return ['compatible' => true];
    }
    
    /**
     * Check theme compatibility
     */
    private static function check_theme_compatibility($theme) {
        global $wp_version;
        
        // Check WordPress version requirement
        $requires_wp = $theme->get('RequiresWP');
        if ($requires_wp && version_compare($wp_version, $requires_wp, '<')) {
            return [
                'compatible' => false,
                'message' => "Requires WordPress {$requires_wp} or higher"
            ];
        }
        
        // Check PHP version requirement
        $requires_php = $theme->get('RequiresPHP');
        if ($requires_php && version_compare(PHP_VERSION, $requires_php, '<')) {
            return [
                'compatible' => false,
                'message' => "Requires PHP {$requires_php} or higher"
            ];
        }
        
        // Check for required theme features
        $template_files = ['index.php', 'style.css'];
        foreach ($template_files as $file) {
            if (!file_exists($theme->get_stylesheet_directory() . '/' . $file)) {
                return [
                    'compatible' => false,
                    'message' => "Missing required file: {$file}"
                ];
            }
        }
        
        return ['compatible' => true];
    }
    
    /**
     * Log activation activity
     */
    private static function log_activation($type, $name, $path, $previous_item = null) {
        global $wpdb;
        
        $history = [
            'type' => $type,
            'action' => 'activate',
            'version' => '', // Could be enhanced to get version
            'date' => current_time('mysql'),
            'path' => $path,
            'product_name' => $name
        ];
        
        if ($previous_item) {
            $history['notes'] = "Previous {$type}: {$previous_item}";
        }
        
        $wpdb->insert($wpdb->prefix . 'srclubplugins_history', $history);
    }


   private static function get_activation_history_single($type = null, $name = null) {
            global $wpdb;

            $table = $wpdb->prefix . 'srclubplugins_history';

            // Base query
            $sql = "SELECT path FROM {$table} WHERE 1=1";
            $params = [];

            if ($type) {
                $sql .= " AND type = %s";
                $params[] = $type;
            }

            if ($name) {
                $sql .= " AND product_name = %s";
                $params[] = $name;
            }

            $sql .= " AND action = %s";
            $params[] = 'install';
            


            $sql .= " ORDER BY date DESC LIMIT 1";

            if ($params) {
                $query = $wpdb->prepare($sql, $params);
            } else {
                $query = $sql;
            }



           $path=$wpdb->get_row($query, ARRAY_A);
           if($path){
            return $path['path'];   
           }
          return null;
        }

    
    /**
     * Deactivate plugin or theme
     */
    public static function deactivate_item() {
        try {
            // Verify nonce for security
            if (!wp_verify_nonce($_POST['nonce'], 'srm_deactivate_nonce')) {
                return self::send_error('Security check failed');
            }
            
            // Validate user permissions
            if (!current_user_can('activate_plugins') && !current_user_can('switch_themes')) {
                return self::send_error('Insufficient permissions');
            }
            
            // Sanitize and validate input
            $item_type = sanitize_text_field($_POST['item_type'] ?? '');
            $item_path = sanitize_text_field($_POST['item_path'] ?? '');
            $item_name = sanitize_text_field($_POST['item_name'] ?? '');
            
            if (!in_array($item_type, ['plugin', 'theme'])) {
                return self::send_error('Invalid item type');
            }
            
            if (empty($item_path) || empty($item_name)) {
                return self::send_error('Missing required parameters');
            }
            
            // Process deactivation
            if ($item_type === 'plugin') {
                $result = self::deactivate_plugin($item_path, $item_name);
            } else {
                return self::send_error('Theme deactivation requires switching to another theme');
            }
            
            if ($result['success']) {
                return wp_send_json_success($result);
            } else {
                return self::send_error($result['message']);
            }
            
        } catch (Exception $e) {
            error_log('SRM Deactivation Error: ' . $e->getMessage());
            return self::send_error('Deactivation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Deactivate a plugin
     */
    private static function deactivate_plugin($plugin_path, $plugin_name) {
        // Check if plugin is active
        if (!is_plugin_active($plugin_path)) {
            return [
                'success' => true,
                'message' => "Plugin '{$plugin_name}' is already inactive",
                'status' => 'already_inactive'
            ];
        }
        
        // Deactivate the plugin
        deactivate_plugins($plugin_path, false, false);
        
        // Verify deactivation was successful
        if (is_plugin_active($plugin_path)) {
            throw new Exception('Plugin deactivation verification failed');
        }
        
        // Log deactivation
        self::log_deactivation('plugin', $plugin_name, $plugin_path);
        
        // Run any post-deactivation hooks
        do_action('srm_plugin_deactivated', $plugin_path, $plugin_name);
        
        return [
            'success' => true,
            'message' => "Plugin '{$plugin_name}' deactivated successfully",
            'status' => 'deactivated'
        ];
    }
    
    /**
     * Log deactivation activity
     */
    private static function log_deactivation($type, $name, $path) {
        global $wpdb;
        
        $history = [
            'type' => $type,
            'action' => 'deactivate',
            'version' => '',
            'date' => current_time('mysql'),
            'path' => $path,
            'product_name' => $name
        ];
        
        $wpdb->insert($wpdb->prefix . 'srclubplugins_history', $history);
    }
    
    /**
     * Get activation status of item
     */
    public static function get_activation_status() {
        try {
            // Verify nonce for security
            if (!wp_verify_nonce($_POST['nonce'], 'srm_status_nonce')) {
                return self::send_error('Security check failed');
            }
            
            $item_type = sanitize_text_field($_POST['item_type'] ?? '');
            $item_path = sanitize_text_field($_POST['item_path'] ?? '');
            
            if (!in_array($item_type, ['plugin', 'theme'])) {
                return self::send_error('Invalid item type');
            }
            
            if (empty($item_path)) {
                return self::send_error('Missing item path');
            }
            
            if ($item_type === 'plugin') {
                $is_active = is_plugin_active($item_path);
                $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $item_path, false, false);
                
                return wp_send_json_success([
                    'active' => $is_active,
                    'name' => $plugin_data['Name'] ?? 'Unknown Plugin',
                    'version' => $plugin_data['Version'] ?? 'Unknown',
                    'type' => 'plugin'
                ]);
            } else {
                $current_theme = get_option('stylesheet');
                $is_active = ($current_theme === $item_path);
                $theme = wp_get_theme($item_path);
                
                return wp_send_json_success([
                    'active' => $is_active,
                    'name' => $theme->get('Name'),
                    'version' => $theme->get('Version'),
                    'type' => 'theme'
                ]);
            }
            
        } catch (Exception $e) {
            error_log('SRM Status Check Error: ' . $e->getMessage());
            return self::send_error('Status check failed: ' . $e->getMessage());
        }
    }
    
    private static function send_error($message) {
        return wp_send_json_error(['message' => $message]);
    }

    
    public static function getBackupCount(){
        global $wpdb;
        $sql="SELECT count(id)
        FROM `".$wpdb->prefix."srclubplugins_history`
        WHERE `action` = 'backup'";
        $getCount=$wpdb->get_var($sql);
        return $getCount;
    }

    
    public static function init(){
        global $wpdb;
        $membershipInfo=Srm_License::membershipInfo();
            $total_products='37,177';
           $backupCount=self::getBackupCount();
          $downloads = array();

        if($ret = $wpdb->get_results('SELECT * FROM `'.$wpdb->prefix.'srclubplugins_history` WHERE `action`="download"', ARRAY_A)){
            foreach($ret as $d){
                $downloads[$d['product_name']] = array('version' => $d['version'], 'id' => $d['id']);
            }
        }

        $plugins = get_plugins();
        $themes = wp_get_themes();
        $isWhiteLabel=Srm_Setup::isWhiteLabel();
        include SRM_PLUGIN_DIR . '/templates/Srm_dashboard.php';
    }


    public static function getPrevDown($ids){
        return (array) self::send_request(array('action' => 'prev_down_check', 'ids' => $ids));
        
    }

    public static function getMeta(){
        return (array) self::send_request(array('action' => 'meta'));
    }


    



 public static function createString($str){
    $Getstr = explode(" ", $str);
    
    if(count($Getstr)>1){
        return  strtolower($Getstr[0].' '.$Getstr[1]);
    }else{
        return  strtolower($Getstr[0]);
    }
 }

    public static function getInstalledProductByName($name, $type){
        switch($type){
            case 'plugin':
                $plugins = get_plugins();
    
                foreach($plugins as $idx => $p){
                    //if($p['Name'] == $name){
                        $search=self::createString($name);
                        if(preg_match("/{$search}/i", strtolower($p['Name']))) {
                            $p['file'] = /*get_home_path().'wp-content/plugins/'.*/$idx;
                            return $p;
                        }
                }
            break;
            case 'theme':
                $themes = wp_get_themes();
    
                foreach($themes as $idx => $p){
                    //if($p['Name'] == $name){
                        $search=self::createString($name);
                        if(preg_match("/{$search}/i", strtolower($p['Name']))) {
                            $p['file'] = /*get_home_path().'wp-content/plugins/'.*/$idx;
                            return $p;
                        }
                }
            break;
        }
    
        return false;
    }


    

public static function downloadFile($url, $path)
{
    
    $newfname = $path;
    $file = fopen ($url, 'rb');
    if ($file) {
        $newf = fopen ($newfname, 'wb');
        if ($newf) {
            while(!feof($file)) {
                fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
            }
        }
    }
    if ($file) {
        fclose($file);
    }
    if ($newf) {
        fclose($newf);
    }
}


    public static function zip($source, $destination){
        if (!extension_loaded('zip') || !file_exists($source)) {
          return false;
        }
       
        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
          return false;
        }
       
        $source = str_replace('\\', '/', realpath($source));
      
        if (is_dir($source) === true){
            $firstDir = @end(explode('/', $source));
            $zip->addEmptyDir($firstDir . '/');
          $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
           
          foreach ($files as $file){
              $file = str_replace('\\', '/', $file);
       
              // Ignore "." and ".." folders
              if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                  continue;
       
              $file = realpath($file);
              $file = str_replace('\\', '/', $file);
               
              if (is_dir($file) === true){
                  $zip->addEmptyDir(str_replace($source . '/', $firstDir . '/', $file . '/'));
              }else if (is_file($file) === true){
                  $zip->addFromString(str_replace($source . '/', $firstDir . '/', $file), file_get_contents($file));
              }
          }
        }else if (is_file($source) === true){
          $zip->addFromString(basename($source), file_get_contents($source));
        }
        return $zip->close();
      }

    public static function rrmdir($dir) {

        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file)
                if ($file != "." && $file != "..") self::rrmdir("$dir/$file");
            rmdir($dir);
        }
        else if (file_exists($dir)) unlink($dir);
    }

    public static function rcopy($src, $dst) {
        if (file_exists ( $dst ))
            self::rrmdir ( $dst );
        if (is_dir ( $src )) {
            mkdir ( $dst );
            $files = scandir ( $src );
            foreach ( $files as $file )
                if ($file != "." && $file != "..")
                    self::rcopy ( "$src/$file", "$dst/$file" );
        } else if (file_exists ( $src ))
            copy ( $src, $dst );
    }


    public static function deleteDir($dir) {

        if (is_dir($dir)) { 
            $objects = scandir($dir);
            foreach ($objects as $object) { 
              if ($object != "." && $object != "..") { 
                if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
                self::deleteDir($dir. DIRECTORY_SEPARATOR .$object);
                else
                  unlink($dir. DIRECTORY_SEPARATOR .$object); 
              } 
            }
            rmdir($dir); 
          } 

    }




    public static function getInstalledVersion($pluginName){
            $invst=self::$getInstalled;
             $str=self::limit_text($pluginName, 2);
            
    
            foreach($invst as $key=>$value){
                if(preg_match('#^'.strtolower($str).'#i',strtolower($key)) === 1){
                    return $value;
                }
                
            }

        return false;
    }
    public static function limit_text($text, $limit) {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos   = array_keys($words);
            $text  = substr($text, 0, $pos[$limit]);
        }
        return $text;
    }
     
}


