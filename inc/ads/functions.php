<?php
/**
 * Ads Related Functions
 *
 * @package     Includes
 * @subpackage  Ads 
 * @since       1.0.0
 */

// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

/**
 * Insert Ad Historical data 
 *  
 * @since 1.1.0
 * @param object $ad_data
 * @return int|bool Ad Slot ID if payment is inserted, false otherwise
 */
function wp_adpress_insert_adhistory( $ad_data = false ) {
	if ( ! $ad_data || $ad_data->id ) {
		return false;
	}

	// Post Args
	$args = array (
		'post_title'    => __( 'Ad History - ', 'wp-adpress' ) . $ad_data->id,
		'post_status'   => 'publish',
		'post_type'     => 'wp_adpress_adhistory',
	);

	// Insert the Post
	$adhistory = wp_insert_post( $args );

	if ( $adhistory ) {	
		update_post_meta( $adhistory, 'wpad_adhistory_adid',    $ad_data->id );
		update_post_meta( $adhistory, 'wpad_adhistory_approved',      $ad_data->time );
		update_post_meta( $adhistory, 'wpad_adhistory_expired',   time() );
		update_post_meta( $adhistory, 'wpad_adhistory_data',      serialize( $ad ) );
		update_post_meta( $adhistory, 'wpad_adhistory_campaign', serialize( new wp_adpress_campaign( $ad_data->campaign_id ) ) );

		do_action( 'wp_adpress_insert_adhistory', $adhistory, $ad_data );

		return $adhistory; // return the ID
	}

	// return false if no payment is inserted
	return false;
}

/**
 * Get Ad historical data 
 *
 * @since 1.1.0
 * @param int $adhistory_id Ad History ID (default: 0)
 * @return array Ad Slot historical data 
 */
function wp_adpress_get_adhistory( $adhistory_id = 0 ) {
	$adid = get_post_meta( $adhistory_id, 'wpad_adhistory_adid', true );
	$approved = get_post_meta( $adhistory_id, 'wpad_adhistory_approved', true );
	$expired = get_post_meta( $adhistory_id, 'wpad_adhistory_expired', true );
	$data = get_post_meta( $adhistory_id, 'wpad_adhistory_data', true );
	$campaign = get_post_meta( $adhistory_id, 'wpad_adhistory_campaign', true );

	$adhistory_data = array(
		'adhistory_id' => $adhistory_id,
		'adid' => $adid,
		'approved' => $approved,
		'expired' => $expired,
		'data' => unserialize( $data ),
		'campaign' => unserialize( $campaign ),
	);

	return $adhistory_data;
}

/**
 * Deletes an Ad historical data 
 *
 * @since 1.1.0
 * @param int $adhistory_id Ad History ID (default: 0)
 * @return void
 */
function wp_adpress_delete_adhistory( $adhistory_id = 0 ) {
	// Remove the ad history 
	wp_delete_post( $adhistory_id, true );

	do_action( 'wp_adpress_delete_adhistory', $adhistory_id );
}
