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
	// Purchase Log Reporting
	require_once( 'tabs/purchase-log/tab.php' );
	require_once( 'tabs/ads-history/tab.php' );
	require_once( 'tabs/settings/tab.php' );

	// Ads History Reporting
	require_once( 'reports.php' );	
}
