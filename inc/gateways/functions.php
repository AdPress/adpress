<?php
/**
 * Gateways Functions 
 * 
 * @package     Includes
 * @subpackage  Gateways
 * @since       1.0.0
 */

// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

/**
 * Returns a list of all available gateways.
 *
 * @since 1.0.0
 * @return array $gateways All the available gateways
 */
function wp_adpress_get_payment_gateways() {
	/*
	 * Gateway Structure
	 *
	 * $gateway = array(
	 *  'id' => 'manual',
	 *  'settings_id' => 'manual',
	 *  'admin_label' => __( 'Manual Payment', 'wp-adpress' ),
	 *  'checkout_label' => __( 'Manual Payment', 'wp-adpress' ),
	 * );
	 */

	$gateways = array();

	return apply_filters( 'wpad_payment_gateways', $gateways );
}

/**
 * Returns an array of all active payment gateways
 *
 * @since 1.0.0
 * @return array $gateways All active gateways
 */
function wp_adpress_get_active_payment_gateways() {
	/*
	 * Gateway Structure
	 *
	 * $gateway = array(
	 *  'id' => 'manual',
	 *  'settings_id' => 'manual',
	 *  'admin_label' => __( 'Manual Payment', 'wp-adpress' ),
	 *  'checkout_label' => __( 'Manual Payment', 'wp-adpress' ),
	 * );
	 */

	$active = array();

	$gateways_settings = get_option( 'adpress_gateways_settings', array( 'active', 'default' ) );
	$active_gateways = array_keys( $gateways_settings['active'] );

	$gateways = wp_adpress_get_payment_gateways();

	foreach( $gateways as $gateway ) {
		if ( in_array( $gateway['id'], $active_gateways ) ) {
			array_push( $active, $gateway );
		}
	}

	return apply_filters( 'wp_adpress_active_payment_gateways', $active );
}

/**
 * Returnthe default payment gateway
 *
 * @since 1.0.0
 * @return array $gateway Gateway metadata
 */
function wp_adpress_get_default_payment_gateway() {
	/*
	 * Gateway Structure
	 *
	 * $gateway = array(
	 *  'id' => 'manual',
	 *  'settings_id' => 'manual',
	 *  'admin_label' => __( 'Manual Payment', 'wp-adpress' ),
	 *  'checkout_label' => __( 'Manual Payment', 'wp-adpress' ),
	 * );
	 */
	$default = '';

	return apply_filters( 'wp_adpress_default_payment_gateway', $default );
}


