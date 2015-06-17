<?php
/**
 * Export/Import Settings And Data 
 *
 * @package     AdPress
 * @subpackage  Inc 
 * @copyright   Copyright (c) 2014, Abid Omar
 * @since       1.0.0
 */

// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

// Register the Add-on
add_filter('adpress_addons', 'wpad_export_register_addon');

function wpad_export_register_addon($addons) {
	$addon = array(
		'id' => 'export',
		'title' => 'Export/Import',
		'description' => __('Export/Import your AdPress Settings and Data.', 'wp-adpress'),
		'author' => 'Abid Omar',
		'version' => 'Native',
		'basename' => plugin_basename(__FILE__),
		'required' => true,
	);
	array_push($addons, $addon);

	return $addons;
}

// Add the Export/Import Tab
add_filter( 'wp_adpress_admin_settings_tabs', 'wpad_export_tab' );
function wpad_export_tab( $tabs ) {
	$export_tab = array(
		'export' => __( 'Import/Export', 'wp-adpress' ),	
	);
	$tabs = array_merge( $tabs, $export_tab );
	return $tabs;
}

// Display the Tab
add_action( 'wp_adpress_settings_tabs_display', 'wpad_export_tab_display' ); 
function wpad_export_tab_display ( $tab ) {
	switch( $tab ) {
	case 'export':
		require_once( 'export.class.php' );
wp_adpress_export::display_page();
		break;
	}
}

// Drop-down help
add_action( 'admin_init', 'wpad_export_help_init' ); 
function wpad_export_help_init() {
	global $adpress_page_settings;
	add_action( "load-$adpress_page_settings", 'wpad_export_help' );	
}

function wpad_export_help() {
	require_once( 'help.php' );
}
