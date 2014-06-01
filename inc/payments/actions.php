<?php
/**
 * Payment Actions
 * 
 * @package     Includes
 * @subpackage  Payments
 * @since       1.0.0
 */

// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

/**
 * Completes a purchase
 *
 * @since 1.0.0
 * @param intger $payment_id Payment ID
 * @param string $new_status New Status
 * @param string $old_status Old Status
 * @return void
 */
function wp_adpress_complete_purchase( $payment_id, $new_status, $old_status ) {
	// Make sure the Ad purchase process is completed once
	if ( $old_status == 'publish' || $old_status == 'complete' ) {
		return;
	}

	// Make sure the Ad purchase is processed only when new status is completed
	if ( $new_status != 'publish' && $new_status != 'complete' ) {
		return;
	}


	$user_info = wp_adpress_get_payment_user_info( $payment_id );	// User Info
	$ad_details = wp_adpress_get_payment_ad_details( $payment_id );	// Ad Details

	do_action( 'wp_adpress_pre_complete_purchase', $payment_id );

	// Register the Ad
	wp_adpress_register_ad( $ad_details, $user_info );

	do_action( 'wp_adpress_complete_purchase', $payment_id );
}

add_action( 'wp_adpress_update_payment_status', 'wp_adpress_complete_purchase', 100, 3 );
