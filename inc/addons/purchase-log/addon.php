<?php
/**
 * Purchase Log 
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
add_filter( 'adpress_addons', 'purchase_log_register_addon' );

function purchase_log_register_addon( $addons )
{
	$addon = array(
		'id' => 'stats',
		'title' => __( 'Purchase Log', 'wp-adpress' ),
		'description' => __( 'A table list of all purchases', 'wp-adpress' ),
		'author' => 'Abid Omar',
		'version' => 'Native',
		'basename' => plugin_basename( __FILE__ ),
		'required' => true,
	);
	array_push( $addons, $addon );

	return $addons;
}

add_action( 'wp_adpress_admin_settings_menu', 'wpad_purchase_log_menu' );

function wpad_purchase_log_menu() {
	global $adpress_page_purchase_log;
	$adpress_page_purchase_log = add_submenu_page( 'adpress-campaigns', 'AdPress | Payments History', 'Payments History', 'manage_options', 'adpress-payments', 'wpad_purchase_log_loader' );
}

function wpad_purchase_log_loader() {
	require_once( 'history_table.php' );
	require_once( 'payments.php' );
}

add_action( 'admin_print_styles', 'wp_adpress_purchase_log_styles' );
function wp_adpress_purchase_log_styles() {
	global $current_screen;
	global $adpress_page_purchase_log;
	if ( $current_screen->id === $adpress_page_purchase_log ) {
		wp_enqueue_style( 'wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css' );
		wp_enqueue_style( 'wp_adpress_purchase_log', ADPRESS_URLPATH . 'inc/addons/stats/files/css/ad_stats.css' );
	}
}
