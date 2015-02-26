<?php
/**
 * PayPal Gateway
 *
 * @package     AdPress
 * @subpackage  Gateways
 * @copyright   Copyright (c) 2014, Abid Omar
 * @since       1.0.0
 */

// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Register the Add-on
add_filter( 'adpress_addons', 'paypal_register_addon' );

function paypal_register_addon( $addons )
{
	$addon = array(
		'id' => 'paypal',
		'title' => 'PayPal',
		'description' => __( 'PayPal', 'wp-adpress' ),
		'author' => 'Abid Omar',
		'version' => '1.0',
		'basename' => plugin_basename( __FILE__ ),
		'required' => true,
	);
	array_push( $addons, $addon );

	return $addons;
}

// Register the Gateway
add_action( 'plugins_loaded', 'wpad_paypal_register_gateway_fn', 100 );
function wpad_paypal_register_gateway_fn() {	
	add_filter( 'wpad_payment_gateways', 'wpad_paypal_register_gateway' );	
}

function wpad_paypal_register_gateway( $gateways ) {
	$gateways[] = 'paypal';
	return $gateways;	
}

// Load the Gateway Class
require_once( 'paypal.class.php' );
