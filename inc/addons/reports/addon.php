<?php
/**
 * Advanced Reporting 
 *
 * @package     AdPress
 * @subpackage  Inc 
 * @copyright   Copyright (c) 2014, Abid Omar
 * @since       1.0.0
 */

// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Register the Add-on
add_filter( 'adpress_addons', 'reports_register_addon' );

function reports_register_addon( $addons )
{
	$addon = array(
		'id' => 'reports',
		'title' => __( 'Reports', 'wp-adpress' ),
		'description' => __( 'Advanced Reporting and Logging.', 'wp-adpress' ),
		'author' => 'Abid Omar',
		'version' => 'Native',
		'basename' => plugin_basename( __FILE__ ),
		'required' => true,
	);
	array_push( $addons, $addon );

	return $addons;
}

add_action( 'wp_adpress_admin_settings_menu', 'wpad_reports_menu' );

function wpad_reports_menu() {
	global $adpress_page_reports;
	$adpress_page_reports = add_submenu_page( 'adpress-campaigns', 'AdPress | Reports', 'Reports', 'manage_options', 'adpress-reports', 'wpad_reports_loader' );
}

function wpad_reports_loader() {
	// Ads History Reporting
	require_once( 'reports.php' );	
}

// Load CSS and JS files
add_action( 'admin_print_scripts', 'wp_adpress_reports_scripts' );
function wp_adpress_reports_scripts() {
	
	global $current_screen;
	if ( $current_screen->id === 'adpress_page_adpress-reports' ) {
		
	}
}

add_action( 'admin_print_styles', 'wp_adpress_reports_styles' );
function wp_adpress_reports_styles() {
	global $current_screen;
	if ( $current_screen->id === 'adpress_page_adpress-reports' ) {
wp_enqueue_style( 'wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css' );	
		wp_enqueue_style( 'wp_adpress_reports', ADPRESS_URLPATH . 'inc/addons/reports/files/css/reports.css' );	
	}
}

	require_once( 'tabs/purchase-log/tab.php' );
	require_once( 'tabs/ads-history/tab.php' );
	require_once( 'tabs/settings/tab.php' );
