<?php
/**
 * Post Type Functions
 *
 * @package     Includes 
 * @subpackage  Functions
 * @copyright   Copyright (c) 2014, Abid Omar 
 * @since       0.9.8 
 */

// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

/**
 * Payments post type
 *
 */
function wp_adpress_payment_post_type() {

	// Post Type labels
	$payment_labels = array(
		'name' 				=> _x('Payments', 'post type general name', 'wp-adpress' ),
		'singular_name' 	=> _x('Payment', 'post type singular name', 'wp-adpress' ),
		'add_new' 			=> __( 'Add New', 'wp-adpress' ),
		'add_new_item' 		=> __( 'Add New Payment', 'wp-adpress' ),
		'edit_item' 		=> __( 'Edit Payment', 'wp-adpress' ),
		'new_item' 			=> __( 'New Payment', 'wp-adpress' ),
		'all_items' 		=> __( 'All Payments', 'wp-adpress' ),
		'view_item' 		=> __( 'View Payment', 'wp-adpress' ),
		'search_items' 		=> __( 'Search Payments', 'wp-adpress' ),
		'not_found' 		=> __( 'No Payments found', 'wp-adpress' ),
		'not_found_in_trash'=> __( 'No Payments found in Trash', 'wp-adpress' ),
		'parent_item_colon' => '',
		'menu_name' 		=> __( 'Payment History', 'wp-adpress' ),
	);

	// Post Type args
	$payment_args = array(
		'labels' 			=> apply_filters( 'wp_adpress_payment_labels', $payment_labels ),
		'public' 			=> false,
		'query_var' 		=> true,
		'rewrite' 			=> true,
		'map_meta_cap'      => true,
		'supports' 			=> array( 'title' ),
		'can_export'		=> true,
	);

	// Register the post type
	register_post_type( 'wp_adpress_payments', $payment_args );
}

add_action('init', 'wp_adpress_payment_post_type');

/**
 * Registered Ads post type
 *
 * @since    1.0.0
 */
function wp_adpress_regads_post_type() {

	// Post Type labels
	$regads_labels = array(
		'name' 				=> _x('Registered Ads', 'post type general name', 'wp-adpress' ),
		'singular_name' 	=> _x('Registered Ad', 'post type singular name', 'wp-adpress' ),
		'add_new' 			=> __( 'Add New', 'wp-adpress' ),
		'add_new_item' 		=> __( 'Add New Ad Log', 'wp-adpress' ),
		'edit_item' 		=> __( 'Edit Ad Log', 'wp-adpress' ),
		'new_item' 			=> __( 'New Ad Log', 'wp-adpress' ),
		'all_items' 		=> __( 'All Ad Logs', 'wp-adpress' ),
		'view_item' 		=> __( 'View Ad Log', 'wp-adpress' ),
		'search_items' 		=> __( 'Search Ad Logs', 'wp-adpress' ),
		'not_found' 		=> __( 'No Ad Logs found', 'wp-adpress' ),
		'not_found_in_trash'=> __( 'No Ad Logs found in Trash', 'wp-adpress' ),
		'parent_item_colon' => '',
		'menu_name' 		=> __( 'Registered Ads', 'wp-adpress' ),
	);

	// Post Type args
	$payment_args = array(
		'labels' 			=> apply_filters( 'wp_adpress_regads_labels', $regads_labels ),
		'public' 			=> true,
		'query_var' 		=> true,
		'rewrite' 			=> true,
		'map_meta_cap'      => true,
		'supports' 			=> array( 'title' ),
		'can_export'		=> true,
	);

	// Register the post type
	register_post_type( 'wp_adpress_ads', $payment_args );
}

add_action('init', 'wp_adpress_regads_post_type');
