<?php

// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

// Register the Add-on
add_filter('adpress_addons', 'paypal_register_addon');

function paypal_register_addon($addons)
{
	$addon = array(
		'id' => 'paypal',
		'title' => 'PayPal',
		'description' => __('PayPal', 'wp-adpress'),
		'author' => 'Abid Omar',
		'version' => '1.0',
		'basename' => plugin_basename(__FILE__),
		'required' => true,
	);
	array_push($addons, $addon);

	return $addons;
}


class WPAD_Payment_Gateway_PayPal_Standard extends WPAD_Payment_Gateway {
	public $settings = array(
		'id' => 'paypal_standard',
		'settings_id' => 'paypal_standard',
		'short_label' => 'PayPal Standard',
		'admin_label' => 'PayPal Standard',
		'checkout_label' => 'PayPal Standard',
	);

	public function setup_settings_form() {
		register_setting('adpress_gateway_paypal_standard_settings', 'adpress_gateway_paypal_standard_settings', 'wp_adpress_forms::validate');
		add_settings_section('gateway_paypal_standard_general_section', 'PayPal Settings', 'wp_adpress_forms::description', 'adpress_gateway_paypal_standard_form_general');		
		add_settings_field('email', 'PayPal Email', 'wp_adpress_forms::textbox', 'adpress_gateway_paypal_standard_form_general', 'gateway_paypal_standard_general_section', array('email', 'adpress_gateway_paypal_standard_settings')); 
		add_settings_field('ipn', 'Disable IPN', 'wp_adpress_forms::checkbox', 'adpress_gateway_paypal_standard_form_general', 'gateway_paypal_standard_general_section', array('ipn', 'adpress_gateway_paypal_standard_settings'));
	}

	public function process() {

	}
}

WPAD_Payment_Gateways::init_gateway( 'paypal_standard', 'WPAD_Payment_Gateway_PayPal_Standard' );
