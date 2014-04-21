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
		'name' 				=> _x('Payments', 'post type general name', 'edd' ),
		'singular_name' 	=> _x('Payment', 'post type singular name', 'edd' ),
		'add_new' 			=> __( 'Add New', 'edd' ),
		'add_new_item' 		=> __( 'Add New Payment', 'edd' ),
		'edit_item' 		=> __( 'Edit Payment', 'edd' ),
		'new_item' 			=> __( 'New Payment', 'edd' ),
		'all_items' 		=> __( 'All Payments', 'edd' ),
		'view_item' 		=> __( 'View Payment', 'edd' ),
		'search_items' 		=> __( 'Search Payments', 'edd' ),
		'not_found' 		=> __( 'No Payments found', 'edd' ),
		'not_found_in_trash'=> __( 'No Payments found in Trash', 'edd' ),
		'parent_item_colon' => '',
		'menu_name' 		=> __( 'Payment History', 'edd' ),
	);

	// Post Type args
	$payment_args = array(
		'labels' 			=> apply_filters( 'wp_adpress_payment_labels', $payment_labels ),
		'public' 			=> true,
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
