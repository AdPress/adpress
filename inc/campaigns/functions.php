<?php
/**
 * Campaigns Related Functions
 *
 * @package     Includes
 * @subpackage  Campaigns 
 * @since       1.2.0
 */

// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Creates a new Campaign 
 * 
 * @since 1.2.0 
 */
function wp_adpress_insert_new_campaign ( $settings, $ad_definition ) {	
	if ( empty ( $settings ) || empty( $ad_definition ) ) {
		return false;
	}	

	// Make sure the campaign is inserted with the correct timezone
	date_default_timezone_set( wp_adpress_get_timezone_id() );

	// Post Args
	$args = array (
		'post_title'    => $settings['name'],
		'post_status'   => 'publish',
		'post_type'     => 'wp_adpress_campaigns',
		'post_parent'   =>  null,
		'post_date'     =>  null,
		'post_date_gmt' =>  null,
	);

	// Insert the Campaign Post
	$campaign = wp_insert_post( $args );

	if ( $campaign ) {
		update_post_meta( $campaign, 'wpad_campaign_name',        $settings['name'] );
		update_post_meta( $campaign, 'wpad_campaign_description', $settings['description'] );
		update_post_meta( $campaign, 'wpad_campaign_state',       $settings['state'] );
		update_post_meta( $campaign, 'wpad_campaign_settings',    serialize( $settings ) );
		update_post_meta( $campaign, 'wpad_campaign_ad_definition',serialize( $ad_definition ) );

		return $campaign; // return the ID
	}

	// return false if no campaign is inserted
	return false;
}

/**
 * Get Campaign by Id 
 * 
 * @since 1.2.0 
 */
function wp_adpress_get_campaign( $id ) {
	// Check if the campaign exists
	if ( ! wp_adpress_post_exists( $id ) ) {
		return false;
	}
	// Campaign Settings
	$settings = get_post_meta( $id, 'wpad_campaign_settings', true );
	$settings = unserialize( $settings );

	// Campaign Ad Definition 
	$ad_definition = get_post_meta( $id, 'wpad_campaign_ad_definition', true );
	$ad_definition = unserialize( $ad_definition );

	$campaign_data = array(
		'name' => '',
		'settings' => $settings,
		'ad_definition' => $ad_definition,
	);

	return $campaign_data;
}

/**
 * Update Campaign by Id 
 * 
 * @since 1.2.0 
 */
function wp_adpress_update_campaign( $id, $settings, $ad_definition ) {
	$current_settings = get_post_meta( $id, 'wpad_campaign_settings', true );
	$new_settings = array_merge_recursive( $current_settings, $settings );
	$serialized_settings = serialize( $new_settings );
	update_post_meta( $id, 'wpad_campaign_settings', $serialized_settings );

	$current_ad_definition = get_post_meta( $id, 'wpad_campaign_ad_definition', true );
	$new_ad_definition = array_merge_recursive( $current_ad_definition, $ad_definition );
	$serialized_ad_definition = serialize( $new_ad_definition );
	update_post_meta( $id, 'wpad_campaign_addefinition', $ad_definition );
}

/**
 * Activate Campaign by Id 
 * 
 * @since 1.2.0 
 */
function wp_adpress_activate_campaign( $id ) {

}

/**
 * Deactivate Campaign by Id 
 * 
 * @since 1.2.0 
 */
function wp_adpress_deactivate_campaign( $id ) {

}

/**
 * Get Campaign Status (active/inactive) by Id 
 * 
 * @since 1.2.0 
 */
function wp_adpress_is_campaign_active( $id ) {

}

/**
 * Delete Campaign by Id 
 * 
 * @since 1.2.0 
 */
function wp_adpress_delete_campaign( $id ) {

}
