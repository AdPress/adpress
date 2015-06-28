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

require_once( 'notifications.php' );

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

add_action( 'wp_adpress_settings_form_update', 'wp_adpress_verify_license_details' );

function wp_adpress_verify_license_details( $var ) {
	if ( isset( $var['license_username'] ) && isset( $var['license_key'] ) ) {
		// Hide the License Notifcation
		$notify = new wpplex\WP_Notify\WP_Notify( 'wpad' );
		$notify->hide_notification( 'mlc' );

		// Verify the new License
		$args = array(
			'body' => array(
				'adpress_validator' => true,
				'envato_username' => $var['license_username'],
				'envato_key' => $var['license_key'],
			),
		);

		$response = wp_remote_post( 'http://wpadpress.com', $args );

		if ( is_wp_error( $response ) ) {
			//something is wrong
			$notify->display_notification( 'scp' );

		} else {
			$body = wp_remote_retrieve_body( $response );
			if ( $body === '1' ) {
				// valid license
				$notify->hide_notification( 'ilc' );
			} else {
				// not-valid license
				$notify->display_notification( 'ilc' );
			}
			$notify->hide_notification( 'scp' );
		}
		$notify->hide_notification( 'mlc' );
	}	
}
