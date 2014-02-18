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
   );
   array_push($addons, $addon);

   return $addons;
}

// Register Settings
add_action('admin_init', 'paypal_gateway_settings');
function paypal_gateway_settings() {
   register_setting('adpress_gateway_paypal_settings', 'adpress_gateway_paypal_settings', 'wp_adpress_forms::validate');
   add_settings_section('gateway_paypal_general_section', 'PayPal Settings', 'wp_adpress_forms::description', 'adpress_gateway_paypal_form_general');		
   add_settings_field('email', 'PayPal Email', 'wp_adpress_forms::textbox', 'adpress_gateway_paypal_form_general', 'gateway_paypal_general_section', array('email', 'adpress_gateway_paypal_settings'));
   add_settings_field('sandbox', 'Sandbox Mode', 'wp_adpress_forms::checkbox', 'adpress_gateway_paypal_form_general', 'gateway_paypal_general_section', array('checkbox', 'adpress_gateway_paypal_settings'));
   add_settings_field('ipn', 'Disable IPN', 'wp_adpress_forms::checkbox', 'adpress_gateway_paypal_form_general', 'gateway_paypal_general_section', array('ipn', 'adpress_gateway_paypal_settings'));
}
