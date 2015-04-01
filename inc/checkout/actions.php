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
        if ( strpos( $_POST['user_fullname'], ' ' ) ) {
		list($user_firstname, $user_lastname) = explode( ' ', $_POST['user_fullname'] );
        } else {
            $user_firstname = $_POST['user_fullname'];
            $user_lastname = '';
        }
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

    do_action( 'wp_adpress_set_redirect_urls', $ad_details, $user_details );

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
add_action( 'wp_adpress_redirected_to_cancel_page', 'wp_adpress_cancel_purchase', 100, 1 );

function wp_adpress_failed_purchase( $query_args = array() )
{	
	if ( isset( $query_args['pid'] ) ) {
		$pid = $query_args['pid'];
		wp_adpress_update_payment_status( $pid, 'failed' );
	}	
}
add_action( 'wp_adpress_redirected_to_failure_page', 'wp_adpress_failed_purchase', 100, 1 );

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

add_action( 'template_redirect', 'wp_adpress_checkout_redirect_process' );
function wp_adpress_checkout_redirect_process()
{
	$current_id = get_the_ID();

	$checkout_page_id = url_to_postid( wp_adpress_get_checkout_page_uri() );
	$success_page_id = url_to_postid( wp_adpress_get_success_page_uri() );
	$cancel_page_id = url_to_postid( wp_adpress_get_cancel_page_uri() );
	$failure_page_id = url_to_postid( wp_adpress_get_failure_page_uri() );
	$notify_page_id = url_to_postid( wp_adpress_get_notify_page_uri() );
	$custom_page_id = url_to_postid( wp_adpress_get_custom_page_uri() );

	$query_args = $_GET;

	switch( $current_id ) {
	case $checkout_page_id:
		do_action( 'wp_adpress_redirected_to_checkout_page', $query_args );
		break;
	case $success_page_id:
		do_action( 'wp_adpress_redirected_to_success_page', $query_args );
		break;
	case $cancel_page_id:
		do_action( 'wp_adpress_redirected_to_cancel_page', $query_args );
		break;
	case $failure_page_id:
		do_action( 'wp_adpress_redirected_to_failure_page', $query_args );
		break;
	case $notify_page_id:
		do_action( 'wp_adpress_redirected_to_notify_page', $query_args );
		break;
	case $custom_page_id:
		do_action( 'wp_adpress_redirected_to_custom_page', $query_args );
		break;
	}
}
