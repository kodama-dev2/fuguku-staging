<?php
/**
 * Local Development Configuration (ServBay)
 * This file is git-ignored and only used for local development
 * 
 * SETUP INSTRUCTIONS:
 * 1. Buka ServBay App
 * 2. Ke tab "MySQL" → cek Connection Details
 * 3. Update values di bawah sesuai dengan ServBay settings
 */

// Mode B: Use local ServBay database (safer for development)
define('DB_NAME', 'fuguku_local');
define('DB_USER', 'root');
define('DB_PASSWORD', 'Admin@1234');
define('DB_HOST', 'localhost:/Applications/ServBay/tmp/mysql-8.2.sock');

// Local Development Settings
// Memory limit defined in wp-config.php (256M)
define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', true);
define('WP_ENVIRONMENT_TYPE', 'development');
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Auto-detect site URL (no need to change)
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('WP_HOME', $scheme . '://' . $host);
define('WP_SITEURL', $scheme . '://' . $host);

