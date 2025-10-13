<?php
/**
 * Robust Theme Installer Class with Static Methods
 * Handles various theme package structures including nested directories and mixed content
 */
class Srm_RobustThemeInstaller {

    /**
     * Main theme installation function that handles various package structures
     * 
     * @param string $package_url URL to the theme package
     * @param string $theme_slug Expected theme slug
     * @return array Installation result with status and message
     */
    public static function install_theme($package_url, $theme_slug) {
        // Include necessary WordPress files
        if (!function_exists('wp_filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';
        require_once ABSPATH . 'wp-admin/includes/class-theme-upgrader.php';

        // Initialize WordPress filesystem
        WP_Filesystem();

        try {

            $theme = wp_get_theme($theme_slug);

            if ( $theme->exists() ) {
                 $themeName=$theme->get( 'Name' );
                 $themeVersion=$theme->get( 'Version' );

                // Srm_Dashboard::create_backup_if_neededThemeOnly($theme_slug,[
                //     'type_name'=>'theme',
                //     'action'=>'backup',
                //     'title'=>$themeName,
                //     'version'=>$themeVersion
                // ]);
                   
            }
            // Step 1: Try direct installation first
            // $result = self::try_direct_installation($package_url, $theme_slug);
            // if ($result['success']) {
            //     return $result;
            // }

            // Step 2: If direct installation fails, try manual extraction and installation
            return self::try_manual_extraction($package_url, $theme_slug);

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Theme installation failed: ' . $e->getMessage(),
                'error_type' => 'exception'
            ];
        }
    }

    /**
     * Try direct theme installation using WordPress upgrader
     */
    public static function try_direct_installation($package_url, $theme_slug) {
        try {
            $upgrader = new Theme_Upgrader(new Automatic_Upgrader_Skin());
            
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

            if ($result && !is_wp_error($result)) {
                // Verify theme was installed correctly
                if (wp_get_theme($theme_slug)->exists()) {
                    return [
                        'success' => true,
                        'message' => 'Theme installed successfully via direct method',
                        'method'  => 'direct',
                        'theme_slug' => $theme_slug
                    ];
                }

                // Check if theme was installed with different slug
                $installed_theme = self::find_installed_theme($result);
                if ($installed_theme) {
                    return [
                        'success' => true,
                        'message' => 'Theme installed successfully with different slug',
                        'method'  => 'direct',
                        'theme_slug' => $installed_theme,
                        'original_slug' => $theme_slug
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Direct installation failed',
                'error'   => is_wp_error($result) ? $result->get_error_message() : 'Unknown error',
                'error_type' => 'direct_install_failed'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Direct installation exception: ' . $e->getMessage(),
                'error_type' => 'direct_install_exception'
            ];
        }
    }




    /**
     * Try manual extraction and theme installation
     */
    public static function try_manual_extraction($package_url, $theme_slug) {
        global $wp_filesystem;

        if (!function_exists('unzip_file')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        
        // Create temporary directory
        $temp_dir = wp_tempnam() . '_theme_extract';
        if (!$wp_filesystem->mkdir($temp_dir, 0755)) {
            return [
                'success' => false,
                'message' => 'Failed to create temporary directory',
                'error_type' => 'temp_dir_creation_failed'
            ];
        }

        try {
            // Download the package
            $download_result = download_url($package_url);
            if (is_wp_error($download_result)) {
                self::cleanup_directory($temp_dir);
                return [
                    'success' => false,
                    'message' => 'Failed to download theme package: ' . $download_result->get_error_message(),
                    'error_type' => 'download_failed'
                ];
            }

            // Extract the package using unzip_file
            $extract_result = unzip_file($download_result, $temp_dir);
            
            if (is_wp_error($extract_result)) {
                unlink($download_result);
                self::cleanup_directory($temp_dir);
                return [
                    'success' => false,
                    'message' => 'Failed to extract theme package: ' . $extract_result->get_error_message(),
                    'error_type' => 'extraction_failed'
                ];
            }

            // Clean up downloaded file
            unlink($download_result);

            // Find the correct theme directory
            $theme_dir = self::find_theme_directory($temp_dir, $theme_slug);
          
            if (!$theme_dir) {
                self::cleanup_directory($temp_dir);
                return [
                    'success' => false,
                    'message' => 'Could not locate valid theme directory in package',
                    'error_type' => 'theme_dir_not_found'
                ];
            }

            // Install the theme
            $install_result = self::install_from_directory($theme_dir, $theme_slug);
            
            // Clean up temporary directory
            self::cleanup_directory($temp_dir);

            return $install_result;

        } catch (Exception $e) {
            // Clean up on exception
            self::cleanup_directory($temp_dir);
            return [
                'success' => false,
                'message' => 'Manual extraction failed: ' . $e->getMessage(),
                'error_type' => 'manual_extraction_exception'
            ];
        }
    }

    /**
     * Find the correct theme directory within extracted files
     */
    public static function find_theme_directory($base_dir, $expected_slug) {
        global $wp_filesystem;

        // Get all files and directories in the base directory
        $files = $wp_filesystem->dirlist($base_dir);

        if (!$files) {
            return false;
        }

          

        $directories = [];
        $zip_files = [];

        foreach ($files as $file) {

            if ($file['type'] === 'd') {
                $directories[] = $file['name'];
            } elseif ($file['type'] === 'f' && pathinfo($file['name'], PATHINFO_EXTENSION) === 'zip') {
                $zip_files[] = $file['name'];
            }
        }

     

         
        // Strategy 1: Look for directory with expected slug name
        foreach ($directories as $dir) {
            if (stripos($dir, $expected_slug) !== false) {
                $potential_dir = trailingslashit($base_dir) . $dir;
                if (self::is_valid_theme_directory($potential_dir)) {
                    return $potential_dir;
                }
            }
        }




        // Strategy 2: Look for any directory containing style.css
        foreach ($directories as $dir) {
            $potential_dir = trailingslashit($base_dir) . $dir;
            if (self::is_valid_theme_directory($potential_dir)) {
                return $potential_dir;
            }
        }



        // Strategy 3: Look recursively in subdirectories
        foreach ($directories as $dir) {

            $potential_dir = trailingslashit($base_dir) . $dir;
            $nested_theme = self::find_theme_directory($potential_dir, $expected_slug);
            if ($nested_theme) {
                return $nested_theme;
            }
        }




        // Strategy 4: Look for .zip files that might contain the theme
        foreach ($zip_files as $zip_file) {
            if (stripos($zip_file, '-child') !== false) {
                continue;
            }
            $zip_path = trailingslashit($base_dir) . $zip_file;
            $theme_dir = self::extract_and_find_theme_in_zip($zip_path, $expected_slug);
            if ($theme_dir) {
                return $theme_dir;
            }
        }

        // Strategy 5: Check if base directory itself is a valid theme
        if (self::is_valid_theme_directory($base_dir)) {
            return $base_dir;
        }

        return false;
    }

    /**
     * Extract zip file and find theme directory within it
     */
    public static function extract_and_find_theme_in_zip($zip_path, $expected_slug) {
        global $wp_filesystem;

        if (!function_exists('unzip_file')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $zip_extract_dir = dirname($zip_path) . '_zip_extract_' . basename($zip_path, '.zip');
        
        if (!$wp_filesystem->mkdir($zip_extract_dir, 0755)) {
            return false;
        }

        $extract_result = unzip_file($zip_path, $zip_extract_dir);
        if (is_wp_error($extract_result)) {
            self::cleanup_directory($zip_extract_dir);
            return false;
        }

        $theme_dir = self::find_theme_directory($zip_extract_dir, $expected_slug);
        
        if (!$theme_dir) {
            self::cleanup_directory($zip_extract_dir);
            return false;
        }

        return $theme_dir;
    }

    /**
     * Check if a directory contains a valid WordPress theme
     */
public static function is_valid_theme_directory($dir) {
    global $wp_filesystem;

    // Build paths
    $style_css = trailingslashit($dir) . 'style.css';
    $index_php = trailingslashit($dir) . 'index.php';

    // Check if style.css and index.php exist
    if (
        !$wp_filesystem->exists($style_css) || 
        !$wp_filesystem->exists($index_php)
    ) {
        return false;
    }

    // Read and validate style.css content
    $style_content = $wp_filesystem->get_contents($style_css);
    if (!$style_content) {
        return false;
    }

    // Check for required theme header: Theme Name
    if (preg_match('/Theme Name:\s*(.+)/i', $style_content)) {
        return true;
    }

    return false;
}


    /**
     * Install theme from a directory
     */
    public static function install_from_directory($source_dir, $theme_slug) {
        global $wp_filesystem;

        // Get actual theme info from style.css
        $theme_info = self::get_theme_info($source_dir);
        $actual_slug = $theme_info['slug'] ?: $theme_slug;

        // Get theme root directory
        $theme_root = get_theme_root();
        $destination_dir = trailingslashit($theme_root) . $actual_slug;

        // Remove existing theme if it exists
        if ($wp_filesystem->exists($destination_dir)) {
            if (!$wp_filesystem->rmdir($destination_dir, true)) {
                return [
                    'success' => false,
                    'message' => 'Failed to remove existing theme directory',
                    'error_type' => 'existing_theme_removal_failed'
                ];
            }
        }

        // Copy theme files
        $copy_result = copy_dir($source_dir, $destination_dir);
        if (is_wp_error($copy_result)) {
            return [
                'success' => false,
                'message' => 'Failed to copy theme files: ' . $copy_result->get_error_message(),
                'error_type' => 'file_copy_failed'
            ];
        }

        // Set proper permissions
        self::set_theme_permissions($destination_dir);

        // Verify installation
        if (!wp_get_theme($actual_slug)->exists()) {
            return [
                'success' => false,
                'message' => 'Theme files copied but theme not recognized by WordPress',
                'error_type' => 'theme_not_recognized'
            ];
        }

        return [
            'success' => true,
            'message' => 'Theme installed successfully via manual extraction',
            'method'  => 'manual',
            'theme_slug' => $actual_slug,
            'theme_name' => $theme_info['name'],
            'original_slug' => $theme_slug
        ];
    }

    /**
     * Get theme information from style.css
     */
    public static function get_theme_info($theme_dir) {
        global $wp_filesystem;

        $style_css = trailingslashit($theme_dir) . 'style.css';
        $style_content = $wp_filesystem->get_contents($style_css);

        if (!$style_content) {
            return ['name' => '', 'slug' => ''];
        }

        // Extract theme name
        preg_match('/Theme Name:\s*(.+)/i', $style_content, $name_matches);
        $theme_name = isset($name_matches[1]) ? trim($name_matches[1]) : '';

        // Generate slug from theme name or directory name
        $slug = $theme_name ? sanitize_title($theme_name) : basename($theme_dir);

        return [
            'name' => $theme_name,
            'slug' => $slug
        ];
    }

    /**
     * Set proper permissions for theme files and directories
     */
    public static function set_theme_permissions($theme_dir) {
        global $wp_filesystem;

        // Set directory permissions
        $wp_filesystem->chmod($theme_dir, 0755);

        // Set file permissions recursively
        self::chmod_recursive($theme_dir);
    }

    /**
     * Recursively set file permissions
     */
    public static function chmod_recursive($dir) {
        global $wp_filesystem;

        $files = $wp_filesystem->dirlist($dir);
        if (!$files) {
            return;
        }

        foreach ($files as $file) {
            $path = trailingslashit($dir) . $file['name'];
            
            if ($file['type'] === 'd') {
                $wp_filesystem->chmod($path, 0755);
                self::chmod_recursive($path);
            } else {
                $wp_filesystem->chmod($path, 0644);
            }
        }
    }

    /**
     * Find installed theme from upgrader result
     */
    public static function find_installed_theme($result) {
        if (!is_array($result) || !isset($result['destination_name'])) {
            return false;
        }

        $theme_slug = $result['destination_name'];
        return wp_get_theme($theme_slug)->exists() ? $theme_slug : false;
    }

    /**
     * Clean up directory and all its contents
     */
    public static function cleanup_directory($dir) {
        global $wp_filesystem;
        
        if ($wp_filesystem->exists($dir)) {
            $wp_filesystem->rmdir($dir, true);
        }
    }

    /**
     * Validate theme package before installation
     */
    public static function validate_package($package_url) {
        // Check if URL is accessible
        $response = wp_remote_head($package_url);
        if (is_wp_error($response)) {
            return [
                'valid' => false,
                'message' => 'Package URL is not accessible: ' . $response->get_error_message()
            ];
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            return [
                'valid' => false,
                'message' => 'Package URL returned HTTP ' . $response_code
            ];
        }

        // Check content type
        $content_type = wp_remote_retrieve_header($response, 'content-type');
        $valid_types = ['application/zip', 'application/x-zip-compressed', 'application/octet-stream'];
        
        if ($content_type && !in_array($content_type, $valid_types)) {
            return [
                'valid' => false,
                'message' => 'Invalid content type: ' . $content_type
            ];
        }

        return [
            'valid' => true,
            'message' => 'Package URL is valid'
        ];
    }

    /**
     * Get installation status and information
     */
    public static function get_theme_status($theme_slug) {
        $theme = wp_get_theme($theme_slug);
        
        if (!$theme->exists()) {
            return [
                'exists' => false,
                'message' => 'Theme not found'
            ];
        }

        return [
            'exists' => true,
            'name' => $theme->get('Name'),
            'version' => $theme->get('Version'),
            'author' => $theme->get('Author'),
            'description' => $theme->get('Description'),
            'is_active' => (get_stylesheet() === $theme_slug),
            'path' => $theme->get_stylesheet_directory()
        ];
    }
}
