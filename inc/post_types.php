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
		'public' 			=> defined( 'WP_DEBUG' ) && WP_DEBUG,
		'query_var' 		=> true,
		'rewrite' 			=> true,
		'map_meta_cap'      => true,
		'supports' 			=> array( 'title' ),
		'can_export'		=> true,
	);

	// Post Type Statuses
	register_post_status( 'refunded', array(
		'label'                     => _x( 'Refunded', 'Refunded payment status', 'wp-adpress' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Refunded <span class="count">(%s)</span>', 'Refunded <span class="count">(%s)</span>', 'wp-adpress' )
	) );

	register_post_status( 'failed', array(
		'label'                     => _x( 'Failed', 'Failed payment status', 'wp-adpress' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'wp-adpress' )
	)  );

register_post_status( 'cancelled', array(
		'label'                     => _x( 'Cancelled', 'Cancelled payment status', 'wp-adpress' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'wp-adpress' )
	)  );

	// Register the post type
	register_post_type( 'wp_adpress_payments', $payment_args );
}

add_action('init', 'wp_adpress_payment_post_type');

/**
 * Ads History post type
 *
 */
function wp_adpress_adshistory_post_type() {

	// Post Type labels
	$adshistory_labels = array(
		'name' 				=> _x( 'Ads History', 'post type general name', 'wp-adpress' ),
		'singular_name' 	=> _x( 'Ad History', 'post type singular name', 'wp-adpress' ),
		'add_new' 			=> __( 'Add New', 'wp-adpress' ),
		'add_new_item' 		=> __( 'Add New History', 'wp-adpress' ),
		'edit_item' 		=> __( 'Edit Ad History', 'wp-adpress' ),
		'new_item' 			=> __( 'New Ad History', 'wp-adpress' ),
		'all_items' 		=> __( 'Complete History', 'wp-adpress' ),
		'view_item' 		=> __( 'View Ad History', 'wp-adpress' ),
		'search_items' 		=> __( 'Search Ads History', 'wp-adpress' ),
		'not_found' 		=> __( 'No Ads History found', 'wp-adpress' ),
		'not_found_in_trash'=> __( 'No Ads History found in Trash', 'wp-adpress' ),
		'parent_item_colon' => '',
		'menu_name' 		=> __( 'Ads History', 'wp-adpress' ),
	);

	// Post Type args
	$adshistory_args = array(
		'labels' 			=> apply_filters( 'wp_adpress_adshistory_labels', $adshistory_labels ),
		'public' 			=> defined( 'WP_DEBUG' ) && WP_DEBUG,
		'query_var' 		=> true,
		'rewrite' 			=> true,
		'map_meta_cap'      => true,
		'supports' 			=> array( 'title' ),
		'can_export'		=> true,
	);

	// Register the post type
	register_post_type( 'wpad_adshistory', $adshistory_args );
}

add_action( 'init', 'wp_adpress_adshistory_post_type' );

/**
 * Campaigns post type
 *
 */
function wp_adpress_campaigns_post_type() {

	// Post Type labels
	$campaigns_labels = array(
		'name' 				=> _x( 'Campaigns', 'post type general name', 'wp-adpress' ),
		'singular_name' 	=> _x( 'Campaign', 'post type singular name', 'wp-adpress' ),
		'add_new' 			=> __( 'Add New', 'wp-adpress' ),
		'add_new_item' 		=> __( 'Add New Campaign', 'wp-adpress' ),
		'edit_item' 		=> __( 'Edit Campaign', 'wp-adpress' ),
		'new_item' 			=> __( 'New Campaign', 'wp-adpress' ),
		'all_items' 		=> __( 'All Campaigns', 'wp-adpress' ),
		'view_item' 		=> __( 'View Campaign', 'wp-adpress' ),
		'search_items' 		=> __( 'Search Campaigns', 'wp-adpress' ),
		'not_found' 		=> __( 'No Campaigns found', 'wp-adpress' ),
		'not_found_in_trash'=> __( 'No Campaigns found in Trash', 'wp-adpress' ),
		'parent_item_colon' => '',
		'menu_name' 		=> __( 'AdPress Campaigns', 'wp-adpress' ),
	);

	// Post Type args
	$campaigns_args = array(
		'labels' 			=> apply_filters( 'wp_adpress_payment_labels', $campaigns_labels ),
		'public' 			=> defined( 'WP_DEBUG' ) && WP_DEBUG,
		'query_var' 		=> false,
		'rewrite' 			=> false,
		'map_meta_cap'      => true,
		'supports' 			=> array( 'title' ),
		'can_export'		=> true,
	);

	// Register the post type
	register_post_type( 'wp_adpress_campaigns', $campaigns_args );
}

add_action('init', 'wp_adpress_campaigns_post_type');

/**
 * Ads post type
 *
 */
function wp_adpress_ads_post_type() {

	// Post Type labels
	$ads_labels = array(
		'name' 				=> _x( 'Ads', 'post type general name', 'wp-adpress' ),
		'singular_name' 	=> _x( 'Ads', 'post type singular name', 'wp-adpress' ),
		'add_new' 			=> __( 'Add New', 'wp-adpress' ),
		'add_new_item' 		=> __( 'Add New Ad', 'wp-adpress' ),
		'edit_item' 		=> __( 'Edit Ad', 'wp-adpress' ),
		'new_item' 			=> __( 'New Ad', 'wp-adpress' ),
		'all_items' 		=> __( 'All Ads', 'wp-adpress' ),
		'view_item' 		=> __( 'View Ad', 'wp-adpress' ),
		'search_items' 		=> __( 'Search Ads', 'wp-adpress' ),
		'not_found' 		=> __( 'No Ads found', 'wp-adpress' ),
		'not_found_in_trash'=> __( 'No Ads found in Trash', 'wp-adpress' ),
		'parent_item_colon' => '',
		'menu_name' 		=> __( 'AdPress Ads', 'wp-adpress' ),
	);

	// Post Type args
	$ads_args = array(
		'labels' 			=> apply_filters( 'wp_adpress_payment_labels', $ads_labels ),
		'public' 			=> defined( 'WP_DEBUG' ) && WP_DEBUG,
		'query_var' 		=> false,
		'rewrite' 			=> false,
		'map_meta_cap'      => true,
		'supports' 			=> array( 'title' ),
		'can_export'		=> true,
	);

	// Register the post type
	register_post_type( 'wp_adpress_ads', $ads_args );
}

add_action('init', 'wp_adpress_ads_post_type');
