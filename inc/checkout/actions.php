<?php
/**
 * Checkout Default Functions 
 * 
 * @package     Includes
 * @subpackage  Checkout
 * @since       1.0
 */

// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

function wp_adpress_action_submit_checkout() {
	// Get the selected Gateway instance
	$gateway = WPAD_Payment_Gateways::get( $_POST['gateway'] );	

	// Collect the posted information
	//

	$current_user = wp_get_current_user();

	if ( $current_user->ID == 0 ) {
		list($user_firstname, $user_lastname) = explode(" ", $_POST['user_fullname']);
		$user_email = $_POST['user_email'];
	} else {
		$user_email = $current_user->user_email;
		$user_firstname = $current_user->user_firstname;
		$user_lastname = $current_user->user_lastname;
	}

	$ad_details = array(
		'cid' => $_POST['cid'],
		'post' => array(
			'destination_url' => $_POST['destination_url'],
			'destination_val' => $_POST['destination_val'],
			'client_message' => '',
		),	
	);
	$user_details = array(
		'user_email' => $user_email,	
		'user_info' => array(
			'first_name' => $user_firstname,
			'last_name' => $user_lastname,
			'id' => $current_user->ID,
		),
	);

	// Set a new purchase log
	$gateway->set_purchase_log( $ad_details, $user_details );

	// Process the payment
	$gateway->process();
}

add_action( 'wp_adpress_submit_checkout', 'wp_adpress_action_submit_checkout', 100, 1 );

function wp_adpress_cancel_purchase( $query_args = array() )
{
	if ( isset( $query_args['pid'] ) ) {
		$pid = $query_args['pid'];
		wp_adpress_update_payment_status( $pid, 'cancelled' );
	}	
}
add_action( 'wp_adpress_redirect_to_cancel_page', 'wp_adpress_cancel_purchase', 100, 1 );

add_action( 'admin_menu', 'admin_menu_submit_checkout', 100 );

function admin_menu_submit_checkout() {
	add_submenu_page( NULL, 'adpress-submit_checkout', 'adpress-submit_checkout', 'manage_options', 'adpress-submit_checkout');
}

function wp_adpress_submit_checkout_fn()
{
	do_action( 'wp_adpress_submit_checkout' );
}

if ( isset( $_POST['submit_checkout'] ) ) {
	add_action( 'wp_loaded', 'wp_adpress_submit_checkout_fn' , 200 );
}
