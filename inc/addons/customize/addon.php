<?php
/**
 * Customize 
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
add_filter('adpress_addons', 'wpad_customize_register_addon');

function wpad_customize_register_addon($addons) {
	$addon = array(
		'id' => 'customize',
		'title' => 'Customize',
		'description' => __('Customize your Image, Link and Flash campaigns.', 'wp-adpress'),
		'author' => 'Abid Omar',
		'version' => 'Native',
		'basename' => plugin_basename(__FILE__),
		'required' => true,
	);
	array_push($addons, $addon);

	return $addons;
}

// Add the Customize Tabs
add_filter( 'wp_adpress_admin_settings_tabs', 'wpad_customize_tabs' );
function wpad_customize_tabs( $tabs ) {
	$customize_tabs = array(
		'image_ad' => __('Image Ad', 'wp-adpress'),
		'link_ad' => __('Link Ad', 'wp-adpress'),
		'flash_ad' => __('Flash Ad', 'wp-adpress'),
	);
	$tabs = array_merge( $tabs, $customize_tabs );
	return $tabs;
}

add_action( 'admin_init', 'wpad_customize_settings' );
function wpad_customize_settings() {
	// Image Ad
	register_setting('adpress_image_settings', 'adpress_image_settings', 'wp_adpress_forms::validate');
	add_settings_section('image_ad_section', 'Image Ad', 'wp_adpress_forms::description', 'adpress_image_ad_form', 'Image AD');
	add_settings_field('ad_loop', 'HTML Code', 'wp_adpress_forms::textarea', 'adpress_image_ad_form', 'image_ad_section', array('ad_loop', 'adpress_image_settings'));
	add_settings_field('ad_css', 'CSS Style', 'wp_adpress_forms::textarea', 'adpress_image_ad_form', 'image_ad_section', array('ad_css', 'adpress_image_settings'));

	// Link Ad
	register_setting('adpress_link_settings', 'adpress_link_settings', 'wp_adpress_forms::validate');
	add_settings_section('link_ad_section', 'Link Ad', 'wp_adpress_forms::description', 'adpress_link_ad_form');
	add_settings_field('ad_loop', 'HTML Code', 'wp_adpress_forms::textarea', 'adpress_link_ad_form', 'link_ad_section', array('ad_loop', 'adpress_link_settings'));
	add_settings_field('ad_css', 'CSS Style', 'wp_adpress_forms::textarea', 'adpress_link_ad_form', 'link_ad_section', array('ad_css', 'adpress_link_settings'));

	// Flash Ad
	register_setting('adpress_flash_settings', 'adpress_flash_settings', 'wp_adpress_forms::validate');
	add_settings_section('flash_ad_section', 'Flash Ad', 'wp_adpress_forms::description', 'adpress_flash_ad_form');
	add_settings_field('ad_loop', 'HTML Code', 'wp_adpress_forms::textarea', 'adpress_flash_ad_form', 'flash_ad_section', array('ad_loop', 'adpress_flash_settings'));
	add_settings_field('ad_css', 'CSS Style', 'wp_adpress_forms::textarea', 'adpress_flash_ad_form', 'flash_ad_section', array('ad_css', 'adpress_flash_settings'));
}

// Display the Tabs
add_action( 'wp_adpress_settings_tabs_display', 'wpad_customize_tabs_display' ); 
function wpad_customize_tabs_display ( $tab ) {
	switch( $tab ) {
	case 'image_ad':
		require_once( 'tabs/image_ad.php' );	
		break;
	case 'link_ad':
		require_once( 'tabs/link_ad.php' );
		break;
	case 'flash_ad':
		require_once( 'tabs/flash_ad.php' );
		break;
	}
}

// Load CSS and JS files
add_action( 'admin_print_scripts', 'wp_adpress_customize_scripts' );
function wp_adpress_customize_scripts() {
	global $current_screen;
	global $adpress_page_settings;
	if ( $current_screen->id === $adpress_page_settings ) {
		// Code Mirror
		wp_enqueue_script( 'codemirror_plugin', ADPRESS_URLPATH . 'inc/addons/customize/files/js/codemirror.js' );
		wp_enqueue_script( 'codemirror_xml', ADPRESS_URLPATH . 'inc/addons/customize/files/js/xml.js', array( 'codemirror_plugin' ) );
		wp_enqueue_script( 'codemirror_css', ADPRESS_URLPATH . 'inc/addons/customize/files/js/css.js', array( 'codemirror_plugin' ) );	
		wp_enqueue_script( 'wp_adpress_customize', ADPRESS_URLPATH . 'inc/addons/customize/files/js/customize.js', array( 'codemirror_plugin', 'jquery' ) );
	}
}

add_action( 'admin_print_styles', 'wp_adpress_customize_styles' );
function wp_adpress_customize_styles() {
	global $current_screen;
	global $adpress_page_settings;
	if ( $current_screen->id === $adpress_page_settings ) {	
		wp_enqueue_style( 'wp_adpress_customize', ADPRESS_URLPATH . 'inc/addons/customize/files/css/customize.css' );
		wp_enqueue_style( 'codemirror', ADPRESS_URLPATH . 'inc/addons/customize/files/css/codemirror.css' );
	}
}

// Drop-down help
add_action( 'admin_init', 'wpad_customize_help_init' ); 
function wpad_customize_help_init() {
	global $adpress_page_settings;
	add_action( "load-$adpress_page_settings", 'wpad_customize_help' );	
}

function wpad_customize_help() {
	require_once( 'help.php' );
}
