<?php
/**
 * License Checker and Auto-Updates Enabler 
 *
 * @package     AdPress
 * @subpackage  Settings 
 * @copyright   Copyright (c) 2014, Abid Omar
 * @since       1.0.0
 */

// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

// Register the Add-on
add_filter('adpress_addons', 'wpad_license_register_addon');

function wpad_license_register_addon($addons) {
	$addon = array(
		'id' => 'license',
		'title' => 'License Checker',
		'description' => __('Validates your AdPress License and enables Auto-Updates.', 'wp-adpress'),
		'author' => 'Abid Omar',
		'version' => 'Native',
		'basename' => plugin_basename(__FILE__),
		'required' => true,
	);
	array_push($addons, $addon);

	return $addons;
}

// Add the License Checker Tab
add_filter( 'wp_adpress_admin_settings_tabs', 'wpad_license_tab' );
function wpad_license_tab( $tabs ) {
	$license_tab = array(
		'license' => __( 'License', 'wp-adpress' ),	
	);
	$tabs = array_merge( $tabs, $license_tab );
	return $tabs;
}

add_action( 'admin_init', 'wpad_license_settings' );
function wpad_license_settings() {
	// License Settings
	register_setting('adpress_license_settings', 'adpress_license_settings', 'wp_adpress_forms::validate');
	add_settings_section('license_section', 'License', 'wp_adpress_forms::description', 'adpress_license_form');
	add_settings_field('license_username', 'Username', 'wp_adpress_forms::textbox', 'adpress_license_form', 'license_section', array('license_username', 'adpress_license_settings'));
	add_settings_field('license_key', 'License Key', 'wp_adpress_forms::textbox', 'adpress_license_form', 'license_section', array('license_key', 'adpress_license_settings'));
}

// Display the Tab
add_action( 'wp_adpress_settings_tabs_display', 'wpad_license_tab_display' ); 
function wpad_license_tab_display ( $tab ) {
	switch( $tab ) {
	case 'license':
		require_once( 'form.php' );
		break;
	}
}

// Drop-down help
add_action( 'admin_init', 'wpad_license_help_init' ); 
function wpad_license_help_init() {
	global $adpress_page_settings;
	add_action( "load-$adpress_page_settings", 'wpad_license_help' );	
}

function wpad_license_help() {
	require_once( 'help.php' );
}

add_action( 'admin_init', function() {
	$s = get_option( 'adpress_license_settings' );
	var_dump( wp_adpress_license::check_license( $s['license_username'], $s['license_key'] ) );
} );
