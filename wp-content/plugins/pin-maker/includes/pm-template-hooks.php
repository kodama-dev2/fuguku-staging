<?php
/**
 * PinMaker Template Hooks
 * Action/filter hooks used for PinMaker functions/views.
 *
 * @package PinMaker
 * @version 1.0.0
 * @author  WPAddon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pin Loop Items.
 *
 * @see wpa_pm_pin_attachment()
 */
add_action( 'wpa_pm_pin_content', 'wpa_pm_pin_attachment', 5 );
add_action( 'wpa_pm_pin_content', 'wpa_pm_pin_icon', 10 );
add_action( 'wpa_pm_pin_content', 'wpa_pm_pin_style', 15 );