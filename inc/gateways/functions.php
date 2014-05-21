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
