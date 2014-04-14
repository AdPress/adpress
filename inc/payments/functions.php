<?php
/**
 * Payment Functions
 *
 * @package     Includes
 * @subpackage  Payments
 * @copyright   Copyright (c) 2014, Abid Omar
 * @since       1.0.0
 */

// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

/**
 * Get Payments
 *
 * Retrieve payments from the database.
 *
 *
 * @since 1.0.0
 * @param array $args Arguments passed to get payments
 * @return object $payments Payments retrieved from the database
 */
function wp_adpress_get_payments( $args = array() ) {

}

/**
 * Insert Payment
 *  
 * @since 1.0.0
 * @param array $payment_data
 * @return int|bool Payment ID if payment is inserted, false otherwise
 */
function wp_adpress_insert_payment( $payment_data = array() ) {
	if ( empty ( $paymeny_data ) ) {
		return false;
	}

	// Make sure the payment is inserted with the correct timezone
	date_default_timezone_set( wp_adpress_get_timezone_id() );

	$args = array ();

	$payment = wp_insert_post( $args );
	
	if ( $payment ) {
		return $payment; // return the ID
	}

	// return false if no payment is inserted
	return false;

}

/**
 * Updates a payment status.
 *
 * @since 1.0.0
 * @param int $payment_id Payment ID
 * @param string $new_status New Payment Status (default: publish)
 * @return void
 */
function wp_adpress_update_payment_status( $payment_id, $new_status = 'publish' ) {
	if ( $new_status == 'completed' || $new_status == 'complete' ) {
		$new_status = 'publish';
	}

}
