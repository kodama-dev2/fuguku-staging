<?php
/**
 * SRClub Backup Manager
 * Static class for creating backups of WordPress plugins and themes
 * 
 * @package SRClub
 * @version 1.0.1
 */

class Srm_BackupManager {
    
    /**
     * Backup base directory name
     */
    const BACKUP_DIR = 'srplugin_backups';
    
    /**
     * Database table suffix
     */
    const TABLE_SUFFIX = 'srclubplugins_history';
    
    /**
     * Create backup for WordPress plugins or themes
     * 
     * @param string $type Type of backup ('plugin' or 'theme')
     * @param string $file Plugin file path (e.g., 'folder/file.php') or theme slug
     * @return array|WP_Error Result array with backup info or WP_Error on failure
     */
    public static function create_backup( $type, $file ) {
        global $wpdb;
        
        // Validate type
        if ( ! in_array( $type, array( 'plugin', 'theme' ) ) ) {
            return new WP_Error( 'invalid_type', 'Type must be "plugin" or "theme"' );
        }
        
        // Sanitize inputs
        $type = sanitize_text_field( $type );
        $file = sanitize_text_field( $file );
        
        // Extract folder name from plugin file path
        if ( $type === 'plugin' ) {
            // If file contains slash, extract folder name
            if ( strpos( $file, '/' ) !== false ) {
                $folder_name = dirname( $file );
            } else {
                // Single file plugin (remove .php extension if exists)
                $folder_name = str_replace( '.php', '', $file );
            }
        } else {
            $folder_name = $file;
        }
        
        // Determine source path (the folder to backup)
        $source_path = self::get_source_path( $type, $folder_name );
        
        // Check if source exists
        if ( ! file_exists( $source_path ) ) {
            return new WP_Error( 'source_not_found', "Source {$type} not found: {$source_path}" );
        }
        
        // Initialize backup directories
        $backup_dir = self::init_backup_directories( $type );
        if ( is_wp_error( $backup_dir ) ) {
            return $backup_dir;
        }
        
        // Get version info (using original file path for plugins)
        $version = self::get_version( $type, $file );
        
        // Get product name (using original file path for plugins)
        $product_name = self::get_product_name( $type, $file );
        
        // Generate backup filename with timestamp
        $timestamp = current_time( 'Y-m-d_H-i-s' );
        $backup_name = sanitize_file_name( $folder_name ) . '_v' . $version . '_' . $timestamp . '.zip';
        $backup_path = $backup_dir . '/' . $backup_name;
        
        // Create ZIP archive
        $zip_result = self::create_zip( $source_path, $backup_path );
        
        if ( is_wp_error( $zip_result ) ) {
            return $zip_result;
        }
        
        // Verify backup was created
        if ( ! file_exists( $backup_path ) || filesize( $backup_path ) === 0 ) {
            return new WP_Error( 'backup_failed', 'Backup file was not created or is empty' );
        }
        
        // Get relative path for database storage
        $relative_path = str_replace( WP_CONTENT_DIR . '/', '', $backup_path );
        
        // Insert into database
        $insert_result = self::save_to_database( $type, $version, $relative_path, $product_name );
        
        if ( is_wp_error( $insert_result ) ) {
            // Delete the backup file if database insert fails
            if ( file_exists( $backup_path ) ) {
                unlink( $backup_path );
            }
            return $insert_result;
        }
        
        return array(
            'success'      => true,
            'backup_id'    => $insert_result,
            'type'         => $type,
            'product_name' => $product_name,
            'version'      => $version,
            'backup_path'  => $backup_path,
            'backup_size'  => size_format( filesize( $backup_path ) ),
            'date'         => current_time( 'Y-m-d H:i:s' ),
            'file'         => $file,
            'folder_name'  => $folder_name
        );
    }
    
    /**
     * Get source path based on type and file
     * 
     * @param string $type Type ('plugin' or 'theme')
     * @param string $folder_name Folder name to backup
     * @return string Full path to source
     */
    private static function get_source_path( $type, $folder_name ) {
        if ( $type === 'plugin' ) {
            return WP_PLUGIN_DIR . '/' . $folder_name;
        } else {
            return WP_CONTENT_DIR . '/themes/' . $folder_name;
        }
    }
    
    /**
     * Initialize backup directories
     * 
     * @param string $type Type ('plugin' or 'theme')
     * @return string|WP_Error Backup directory path or WP_Error on failure
     */
    private static function init_backup_directories( $type ) {
        $backup_base_dir = WP_CONTENT_DIR . '/' . self::BACKUP_DIR;
        
        // Create base backup directory if it doesn't exist
        if ( ! file_exists( $backup_base_dir ) ) {
            if ( ! wp_mkdir_p( $backup_base_dir ) ) {
                return new WP_Error( 'dir_creation_failed', 'Failed to create backup base directory' );
            }
            
            // Create .htaccess to prevent direct access
            self::create_htaccess( $backup_base_dir );
            
            // Create index.php for additional security
            self::create_index_file( $backup_base_dir );
        }
        
        // Create type-specific directory
        $backup_dir = $backup_base_dir . '/' . $type;
        if ( ! file_exists( $backup_dir ) ) {
            if ( ! wp_mkdir_p( $backup_dir ) ) {
                return new WP_Error( 'dir_creation_failed', "Failed to create {$type} backup directory" );
            }
            self::create_index_file( $backup_dir );
        }
        
        return $backup_dir;
    }
    
    /**
     * Create .htaccess file for directory protection
     * 
     * @param string $directory Directory path
     * @return void
     */
    private static function create_htaccess( $directory ) {
        $htaccess_content = "Order deny,allow\nDeny from all\n<Files ~ \"\\.(zip)$\">\n    Deny from all\n</Files>";
        file_put_contents( $directory . '/.htaccess', $htaccess_content );
    }
    
    /**
     * Create blank index.php file
     * 
     * @param string $directory Directory path
     * @return void
     */
    private static function create_index_file( $directory ) {
        $index_content = "<?php\n// Silence is golden\n";
        file_put_contents( $directory . '/index.php', $index_content );
    }
    
    /**
     * Create ZIP archive from source directory or file
     * 
     * @param string $source Source path
     * @param string $destination Destination ZIP file path
     * @return bool|WP_Error True on success, WP_Error on failure
     */
    private static function create_zip( $source, $destination ) {
        if ( ! class_exists( 'ZipArchive' ) ) {
            return new WP_Error( 'zip_not_available', 'ZipArchive class is not available on this server' );
        }
        
        $zip = new ZipArchive();
        
        if ( $zip->open( $destination, ZipArchive::CREATE | ZipArchive::OVERWRITE ) !== true ) {
            return new WP_Error( 'zip_create_failed', 'Cannot create ZIP archive' );
        }
        
        $source = realpath( $source );
        
        if ( ! $source ) {
            $zip->close();
            return new WP_Error( 'invalid_source', 'Source path is invalid' );
        }
        
        if ( is_dir( $source ) ) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator( $source, RecursiveDirectoryIterator::SKIP_DOTS ),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            
            foreach ( $files as $name => $file ) {
                if ( ! $file->isDir() ) {
                    $file_path = $file->getRealPath();
                    $relative_path = substr( $file_path, strlen( $source ) + 1 );
                    
                    // Skip if file is not readable
                    if ( ! is_readable( $file_path ) ) {
                        continue;
                    }
                    
                    $zip->addFile( $file_path, $relative_path );
                }
            }
        } elseif ( is_file( $source ) ) {
            $zip->addFile( $source, basename( $source ) );
        }
        
        $zip->close();
        
        return true;
    }
    
    /**
     * Get version of plugin or theme
     * 
     * @param string $type Type ('plugin' or 'theme')
     * @param string $file Plugin/theme identifier
     * @return string Version number or 'unknown'
     */
    private static function get_version( $type, $file ) {
        if ( $type === 'plugin' ) {
            return self::get_plugin_version( $file );
        } else {
            return self::get_theme_version( $file );
        }
    }
    
    /**
     * Get plugin version
     * 
     * @param string $file Plugin file path (e.g., 'folder/file.php' or 'single-file.php')
     * @return string Version number
     */
    private static function get_plugin_version( $file ) {
        // Ensure file has .php extension
        if ( strpos( $file, '.php' ) === false ) {
            // Try to find the main plugin file
            if ( strpos( $file, '/' ) !== false ) {
                // Already has a path
                $plugin_file = $file;
            } else {
                // Single file or folder name
                $folder_path = WP_PLUGIN_DIR . '/' . $file;
                if ( is_dir( $folder_path ) ) {
                    // Find main plugin file in folder
                    $files = scandir( $folder_path );
                    foreach ( $files as $potential_file ) {
                        if ( substr( $potential_file, -4 ) === '.php' ) {
                            $plugin_file = $file . '/' . $potential_file;
                            break;
                        }
                    }
                } else {
                    $plugin_file = $file . '.php';
                }
            }
        } else {
            $plugin_file = $file;
        }
        
        if ( ! function_exists( 'get_plugin_data' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
        
        if ( ! file_exists( $plugin_path ) ) {
            return 'unknown';
        }
        
        $plugin_data = get_plugin_data( $plugin_path, false, false );
        return ! empty( $plugin_data['Version'] ) ? $plugin_data['Version'] : 'unknown';
    }
    
    /**
     * Get theme version
     * 
     * @param string $file Theme identifier
     * @return string Version number
     */
    private static function get_theme_version( $file ) {
        $theme = wp_get_theme( $file );
        return $theme->exists() ? $theme->get( 'Version' ) : 'unknown';
    }
    
    /**
     * Get product name of plugin or theme
     * 
     * @param string $type Type ('plugin' or 'theme')
     * @param string $file Plugin/theme identifier
     * @return string Product name
     */
    private static function get_product_name( $type, $file ) {
        if ( $type === 'plugin' ) {
            return self::get_plugin_name( $file );
        } else {
            return self::get_theme_name( $file );
        }
    }
    
    /**
     * Get plugin name
     * 
     * @param string $file Plugin file path
     * @return string Plugin name
     */
    private static function get_plugin_name( $file ) {
        // Ensure file has .php extension
        if ( strpos( $file, '.php' ) === false ) {
            if ( strpos( $file, '/' ) !== false ) {
                $plugin_file = $file;
            } else {
                $folder_path = WP_PLUGIN_DIR . '/' . $file;
                if ( is_dir( $folder_path ) ) {
                    $files = scandir( $folder_path );
                    foreach ( $files as $potential_file ) {
                        if ( substr( $potential_file, -4 ) === '.php' ) {
                            $plugin_file = $file . '/' . $potential_file;
                            break;
                        }
                    }
                } else {
                    $plugin_file = $file . '.php';
                }
            }
        } else {
            $plugin_file = $file;
        }
        
        if ( ! function_exists( 'get_plugin_data' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
        
        if ( ! file_exists( $plugin_path ) ) {
            return $file;
        }
        
        $plugin_data = get_plugin_data( $plugin_path, false, false );
        return ! empty( $plugin_data['Name'] ) ? $plugin_data['Name'] : $file;
    }
    
    /**
     * Get theme name
     * 
     * @param string $file Theme identifier
     * @return string Theme name
     */
    private static function get_theme_name( $file ) {
        $theme = wp_get_theme( $file );
        return $theme->exists() ? $theme->get( 'Name' ) : $file;
    }
    
    /**
     * Save backup information to database
     * 
     * @param string $type Type ('plugin' or 'theme')
     * @param string $version Version number
     * @param string $path Relative path to backup file
     * @param string $product_name Product name
     * @return int|WP_Error Insert ID on success, WP_Error on failure
     */
    private static function save_to_database( $type, $version, $path, $product_name ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::TABLE_SUFFIX;
        
        $insert_result = $wpdb->insert(
            $table_name,
            array(
                'type'         => $type,
                'action'       => 'backup',
                'version'      => $version,
                'date'         => current_time( 'Y-m-d H:i:s' ),
                'path'         => $path,
                'product_name' => $product_name
            ),
            array( '%s', '%s', '%s', '%s', '%s', '%s' )
        );
        
        if ( $insert_result === false ) {
            return new WP_Error( 'db_insert_failed', 'Failed to save backup info to database: ' . $wpdb->last_error );
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Get all backups from database
     * 
     * @param string $type Optional. Filter by type ('plugin' or 'theme')
     * @param int $limit Optional. Number of records to retrieve
     * @return array|WP_Error Array of backups or WP_Error on failure
     */
    public static function get_backups( $type = null, $limit = 50 ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::TABLE_SUFFIX;
        $where = "WHERE action = 'backup'";
        
        if ( $type && in_array( $type, array( 'plugin', 'theme' ) ) ) {
            $where .= $wpdb->prepare( " AND type = %s", $type );
        }
        
        $limit = absint( $limit );
        
        $query = "SELECT * FROM {$table_name} {$where} ORDER BY date DESC LIMIT {$limit}";
        $results = $wpdb->get_results( $query, ARRAY_A );
        
        if ( $results === null ) {
            return new WP_Error( 'db_query_failed', 'Failed to retrieve backups: ' . $wpdb->last_error );
        }
        
        return $results;
    }
    
    /**
     * Delete a backup file and its database record
     * 
     * @param int $backup_id Backup ID from database
     * @return bool|WP_Error True on success, WP_Error on failure
     */
    public static function delete_backup( $backup_id ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::TABLE_SUFFIX;
        $backup_id = absint( $backup_id );
        
        // Get backup info
        $backup = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $backup_id ),
            ARRAY_A
        );
        
        if ( ! $backup ) {
            return new WP_Error( 'backup_not_found', 'Backup record not found' );
        }
        
        // Delete file if it exists
        $file_path = WP_CONTENT_DIR . '/' . $backup['path'];
        if ( file_exists( $file_path ) ) {
            if ( ! unlink( $file_path ) ) {
                return new WP_Error( 'file_delete_failed', 'Failed to delete backup file' );
            }
        }
        
        // Delete database record
        $delete_result = $wpdb->delete( $table_name, array( 'id' => $backup_id ), array( '%d' ) );
        
        if ( $delete_result === false ) {
            return new WP_Error( 'db_delete_failed', 'Failed to delete backup record from database' );
        }
        
        return true;
    }
    
    /**
     * Get backup directory path
     * 
     * @param string $type Optional. Type ('plugin' or 'theme')
     * @return string Directory path
     */
    public static function get_backup_dir( $type = null ) {
        $backup_base_dir = WP_CONTENT_DIR . '/' . self::BACKUP_DIR;
        
        if ( $type && in_array( $type, array( 'plugin', 'theme' ) ) ) {
            return $backup_base_dir . '/' . $type;
        }
        
        return $backup_base_dir;
    }
}

/**
 * Example Usage:
 * 
 * // Backup a plugin using folder/file.php format
 * $result = Srm_BackupManager::create_backup('plugin', 'wordpress-seo-premium/wp-seo-premium.php');
 * 
 * // Or just the folder name
 * $result = Srm_BackupManager::create_backup('plugin', 'wordpress-seo-premium');
 * 
 * // Backup a theme
 * $result = Srm_BackupManager::create_backup('theme', 'twentytwentyfour');
 * 
 * if (is_wp_error($result)) {
 *     echo 'Error: ' . $result->get_error_message();
 * } else {
 *     echo 'Success! Backup created<br>';
 *     echo 'Backup ID: ' . $result['backup_id'] . '<br>';
 *     echo 'Product: ' . $result['product_name'] . '<br>';
 *     echo 'Version: ' . $result['version'] . '<br>';
 *     echo 'Size: ' . $result['backup_size'] . '<br>';
 *     echo 'Path: ' . $result['backup_path'] . '<br>';
 * }
 * 
 * // Get all backups
 * $backups = Srm_BackupManager::get_backups();
 * 
 * // Get only plugin backups
 * $plugin_backups = Srm_BackupManager::get_backups('plugin', 20);
 * 
 * // Delete a backup
 * Srm_BackupManager::delete_backup(123);
 * 
 * // Get backup directory
 * $plugin_dir = Srm_BackupManager::get_backup_dir('plugin');
 */
?>