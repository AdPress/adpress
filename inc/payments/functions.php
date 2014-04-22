<?php
/**
 * Payment Functions
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

//add_action('init', 'temp_func');

function temp_func() {
	//    for ($i = 0; $i < 50; $i++) {
	$payment_data = array(
		'price' => 25,
		//'date' => date('now'),
		'user_email' => 'test@test.com',
		'purchase_key' => 'AB54DFC7',
		'currency' => 'USD',
		'ads' => array (),
		'user_info' => array( 'first_name' => 'Abid', 'last_name' => 'Omar', 'id' => ''),
		'status' => 'publish',
		'gateway' => 'PayPal',
	);
	global $wp_rewrite;
	if (!$wp_rewrite) {
		$wp_rewrite = new WP_Rewrite();
	}
	wp_adpress_insert_payment ( $payment_data );
	//    }
}

/**
 * Insert Payment
 *  
 * @since 1.0.0
 * @param array $payment_data
 * @return int|bool Payment ID if payment is inserted, false otherwise
 */
function wp_adpress_insert_payment( $payment_data = array() ) {
	/*
	 * $payment_data = array (
	 * 'price' => '',
	 * 'date' => '',
	 * 'user_email' => '',
	 * 'purchase_key' => '',
	 * 'currency' => '',
	 * 'ads' => array (),
	 * 'user_info' => array( 'first_name' => '', 'last_name' => '', 'id' => ''),
	 * 'status' => '',
	 * 'gateway' => '',
	 * );
	 */
	if ( empty ( $payment_data ) ) {
		return false;
	}

	// Make sure the payment is inserted with the correct timezone
	date_default_timezone_set( wp_adpress_get_timezone_id() );

	// Construct the payment title
	if ( isset( $payment_data['user_info']['first_name'] ) || isset( $payment_data['user_info']['last_name'] ) ) {
		$payment_title = $payment_data['user_info']['first_name'] . ' ' . $payment_data['user_info']['last_name'];
	} else {
		$payment_title = $payment_data['user_email'];
	}

	// Post Args
	$args = array (
		'post_title'    => $payment_title,
		'post_status'   => isset( $payment_data['status'] ) ? $payment_data['status'] : 'pending',
		'post_type'     => 'wp_adpress_payments',
		'post_parent'   => isset( $payment_data['parent'] ) ? $payment_data['parent'] : null,
		'post_date'     => isset( $payment_data['post_date'] ) ? $payment_data['post_date'] : null,
		'post_date_gmt' => isset( $payment_data['post_date'] ) ? $payment_data['post_date'] : null,
	);

	$payment = wp_insert_post( $args );


	if ( $payment ) {
		$payment_meta = array(
			'currency'     => $payment_data['currency'],
			'downloads'    => serialize( $payment_data['ads'] ),
			'user_info'    => serialize( $payment_data['user_info'] ),
		);

		if( ! $payment_data['price'] ) {
			$payment_data['price'] = 0;
		}

		update_post_meta( $payment, '_wpad_payment_meta',         $payment_meta );
		update_post_meta( $payment, '_wpad_payment_user_info',	  $payment_data['user_info'] );
		update_post_meta( $payment, '_wpad_payment_user_id',      $payment_data['user_info']['id'] );
		update_post_meta( $payment, '_wpad_payment_user_email',   $payment_data['user_email'] );
		//update_post_meta( $payment, '_wpad_payment_user_ip',      edd_get_ip() );
		update_post_meta( $payment, '_wpad_payment_purchase_key', $payment_data['purchase_key'] );
		update_post_meta( $payment, '_wpad_payment_total',        $payment_data['price'] );
		//update_post_meta( $payment, '_wpad_payment_mode',         $mode );
		//update_post_meta( $payment, '_wpad_payment_gateway',      $gateway );

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

	$payment = get_post( $payment_id );

	if ( is_wp_error( $payment ) || !is_object( $payment ) ) {
		return;
	}

	$old_status = $payment->post_status;

	if ( $old_status === $new_status ) {
		return; // Don't permit status changes that aren't changes
	}

	do_action( 'wp_adpress_before_payment_status_change', $payment_id, $new_status, $old_status );

	$update_fields = array( 'ID' => $payment_id, 'post_status' => $new_status, 'edit_date' => current_time( 'mysql' ) );

	wp_update_post( apply_filters( 'wp_adpress_update_payment_status_fields', $update_fields ) );

	do_action( 'wp_adpress_update_payment_status', $payment_id, $new_status, $old_status );

}

/**
 * Deletes a Payment
 *
 * @since 1.0.0
 * @param int $payment_id Payment ID (default: 0)
 * @return void
 */
function wp_adpress_delete_payment( $payment_id = 0 ) {
	// Remove the payment
	wp_delete_post( $payment_id, true );

	do_action( 'wp_adpress_payment_deleted', $payment_id );
}

/**
 * Get Payment Status
 *
 * @since 1.0.0
 *
 * @param WP_Post $payment
 * @param bool   $return_label Whether to return the payment status or not
 *
 * @return bool|mixed if payment status exists, false otherwise
 */
function wp_adpress_get_payment_status( $payment, $return_label = false ) {
	if ( ! is_object( $payment ) || !isset( $payment->post_status ) )
		return false;

	$statuses = wp_adpress_get_payment_statuses();
	if ( ! is_array( $statuses ) || empty( $statuses ) )
		return false;

	if ( array_key_exists( $payment->post_status, $statuses ) ) {
		if ( true === $return_label ) {
			return $statuses[ $payment->post_status ];
		} else {
			return array_search( $payment->post_status, $statuses );
		}
	}

	return false;
}

/**
 * Retrieves all available statuses for payments.
 *
 * @since 1.0.0
 * @return array $payment_status All the available payment statuses
 */
function wp_adpress_get_payment_statuses() {
	$payment_statuses = array(
		'publish'   => __( 'Complete', 'wp-adpress' ),
		'refunded'  => __( 'Refunded', 'wp-adpress' ),
		'failed'    => __( 'Failed', 'wp-adpress' ),
	);

	return apply_filters( 'wp_adpress_payment_statuses', $payment_statuses );
}

/**
 * Get the user ID associated with a payment
 *
 * @since 1.0.0
 * @param int $payment_id Payment ID
 * @return string $user_id User ID
 */
function wp_adpress_get_payment_user_id ( $payment_id ) {
	$user_email = get_post_meta( $payment_id, '_wpad_payment_user_email', true );

	$user = get_user_by( 'email', $user_email );
	if ($user) {
		$user_id = $user->ID;
	} else {
		$user_id = 0;
	}

	return apply_filters( 'wp_adpress_payment_user_id', $user_id );
}
