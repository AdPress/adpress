<?php

// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

// Register the Add-on
add_filter('adpress_addons', 'wpad_manual_register_addon');

function wpad_manual_register_addon($addons) {
	$addon = array(
		'id' => 'manual',
		'title' => 'Manual Payment',
		'description' => __('Manual Payment Gateway', 'wp-adpress'),
		'author' => 'Abid Omar',
		'version' => '1.0',
		'basename' => plugin_basename(__FILE__),
		'required' => true,
	);
	array_push($addons, $addon);

	return $addons;
}

class WPAD_Payment_Gateway_Manual extends WPAD_Payment_Gateway {
	public $settings = array(
		'id' => 'manual',
		'settings_id' => 'manual',
		'short_label' => 'Manual',
		'admin_label' => 'Manual Payment',
		'checkout_label' => 'Manual Payment',
	);		

	public function setup_settings_form() {
		register_setting('adpress_gateway_manual_settings', 'adpress_gateway_manual_settings', 'wp_adpress_forms::validate');
		add_settings_section('gateway_manual_general_section', 'Manual Settings', 'wp_adpress_forms::description', 'adpress_gateway_manual_form_general');	
		add_settings_field('label', 'No Settings Required', 'wp_adpress_forms::label', 'adpress_gateway_manual_form_general', 'gateway_manual_general_section');
	}	

	public function process() {

	}
}

WPAD_Payment_Gateways::init_gateway( 'manual', 'WPAD_Payment_Gateway_Manual' );

/**
 * Process the Purchase Data and Record the Transaction
 * 
 * @since 1.0.0
 * @param array $purchase_data Purchase Data
 * @return void
 */
function wpad_manual_payment( $purchase_data ) {
	/*
	 * $purchase_data = array(
	 *  
	 * );
	 */
	$payment_data = array(
		'price' => '',
		'user_email' => '',
		'user_id' => '',
		'purchase_key' => '',
		'currency' => '',
		'user_info' => array(
			'first_name' => '',
			'last_name' => '',
			'id' => '',
		),
		'status' => 'pending',
		'ad' => array(
			'cid' => '',
			'post' => '',
		),
		'gateway' => 'manual',
	);

	$payment = wp_adpress_insert_payment( $payment_data );

	if ( $payment ) {
		wp_adpress_update_payment_status( $payment, 'publish' );
	} else {
		//error logging
	}
}
