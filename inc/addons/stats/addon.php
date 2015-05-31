<?php
/**
 * Stats Extended 
 *
 * @package     AdPress
 * @subpackage  Stats 
 * @copyright   Copyright (c) 2014, Abid Omar
 * @since       1.0.0
 */

// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Register the Add-on
add_filter( 'adpress_addons', 'stats_register_addon' );

function stats_register_addon( $addons )
{
	$addon = array(
		'id' => 'stats',
		'title' => __( 'Stats Extended', 'wp-adpress' ),
		'description' => __( 'Improved Analytics Dashboard', 'wp-adpress' ),
		'author' => 'Abid Omar',
		'version' => '1.0',
		'basename' => plugin_basename( __FILE__ ),
		'required' => true,
	);
	array_push( $addons, $addon );

	return $addons;
}

require_once( 'template.php' );

// Remove Old Analytics
add_action( 'wp_adpress_stats_body', 'wp_adpress_extended_stats_rm_old_stats', 1, 1 );

function wp_adpress_extended_stats_rm_old_stats( $ad ) {
	remove_action( 'wp_adpress_stats_body', 'wp_adpress_stats_graph', 10 );
}


add_action( 'admin_print_scripts', 'wp_adpress_extended_stats_scripts' );
function wp_adpress_extended_stats_scripts() {
	global $current_screen;
	global $adpress_page_ad;
	if ( $current_screen->id === $adpress_page_ad ) {
		wp_enqueue_script( 'wp_adpress_high_stock', ADPRESS_URLPATH . 'inc/addons/stats/files/js/highstock.js', array( 'jquery' ) );
		wp_enqueue_script( 'wp_adpress_extended_stats', ADPRESS_URLPATH . 'inc/addons/stats/files/js/ad_stats.js', array( 'wp_adpress_high_stock' ) );
	}
}

add_action( 'admin_print_styles', 'wp_adpress_extended_stats_styles' );
function wp_adpress_extended_stats_styles() {
	global $current_screen;
	global $adpress_page_ad;
	if ( $current_screen->id === $adpress_page_ad ) {
		wp_enqueue_style( 'signika_font', '//fonts.googleapis.com/css?family=Signika:400,700' );
		wp_enqueue_style( 'wp_adpress_extended_stats', ADPRESS_URLPATH . 'inc/addons/stats/files/css/ad_stats.css' );
	}
}
