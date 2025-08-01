<?php
/**
 * Initialize framework and libraries.
 *
 * @since   1.0.0
 * @package Claue
 */

// Theme options
include JAS_CLAUE_PATH . '/core/admin/cs-framework.php';

// Vendor libraries
$libs = 'woocommerce, visual-composer, tgmpa, aq-resizer, openswatch, wcvs, elementor';
$libs = array_map( 'trim', explode( ',', $libs ) );

foreach ( $libs as $lib ) {
	include JAS_CLAUE_PATH . '/core/libraries/vendors/' . $lib . '/init.php';
}

// Theme libraries
include JAS_CLAUE_PATH . '/core/libraries/janstudio/hooks/action.php';
include JAS_CLAUE_PATH . '/core/libraries/janstudio/hooks/filter.php';
include JAS_CLAUE_PATH . '/core/libraries/janstudio/hooks/helper.php';
include JAS_CLAUE_PATH . '/core/libraries/janstudio/classes/menu.php';