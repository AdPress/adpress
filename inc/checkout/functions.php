<?php
/**
 * Checkout Functions 
 * 
 * @package     Includes
 * @subpackage  Checkout
 * @since       1.0.0
 */

// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

/**
 * Register Ad
 * 
 * @since 1.0.0
 * @param array $ad_details
 * @param array $user_info
 * @return bool
 */
function wp_adpress_register_ad( $ad_details, $user_info ) {
    if ( is_array( $ad_details ) ) {
        return;
    }

    $campaign = new wp_adpress_campaign( $ad_details['cid'] );

    $ad_id = $campaign->register_ad( $ad_details['post'] );

    return $ad_id;
}

/**
 * Return User info
 *
 * @param integer $payment_id Payment ID
 * @return array User Info
 */
function wp_adpress_get_payment_user_info( $payment_id ) {
	/*
	 * $user_info = array(
	 *  'user_id' => '',
	 *  'user_email' => '',
	 *  'user_info' => '',
	 * );
	 */
	$user_info = array();

    $user_info['user_id'] = get_post_meta( $payment_id, 'wpad_payment_user_id', true );
    $user_info['user_email'] = get_post_meta( $payment_id, 'wpad_payment_user_email', true );
    $user_info['user_info'] = get_post_meta( $payment_id, 'wpad_payment_user_info', true );
    
	return $user_info;
}

/**
 * Return Ad Details
 *
 * @param integer $payemnt_id Payment ID
 * @return array Ad Details
 */
function wp_adpress_get_payment_ad_details( $payment_id ) {
	/*
	 * $ad_details = array(
	 *  'cid' => '',
	 *  'post' => array(
	 *   'client_message' => '',
	 *   'destination_url' => '',
	 *   'destination_val' => '',
	 *  ),
	 * );
	 */
	$ad_details = array();

    $ad_details = get_post_meta( $payment_id, 'wpad_payment_ad', true );

	return $ad_details;
}
