<?php
/*
Plugin Name: Claue Sample Data
Plugin URI: http://janstudio.net
Description: This plugin allow import sample data of theme.
Version: 1.0.0
Author: JanStudio
Author URI: http://janstudio.net
License: GPLv2 or later
Text Domain: jas-sample
*/
define( 'JAS_SAMPLE_PATH', plugin_dir_path( __FILE__ ) );
define( 'JAS_SAMPLE_URI', plugins_url() . '/claue-sample' );
require JAS_SAMPLE_PATH . '/import/init.php';

// Increase memory limit for hosting limit with low memory
ini_set( 'memory_limit','256M' );