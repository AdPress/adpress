<?php

// Don't load directly
if (!defined('ABSPATH')) {
   die('-1');
}

// Register the Add-on
add_filter('adpress_addons', 'manual_register_addon');

function manual_register_addon($addons)
{
   $addon = array(
	  'id' => 'manual',
	  'title' => 'Manual Payment',
	  'description' => __('Manual Payment Gateway', 'wp-adpress'),
	  'author' => 'Abid Omar',
	  'version' => '1.0',
	  'basename' => plugin_basename(__FILE__),
   );
   array_push($addons, $addon);

   return $addons;
}

// Register Settings
add_action('admin_init', 'manual_gateway_settings');
function manual_gateway_settings() {
   register_setting('adpress_gateway_manual_settings', 'adpress_gateway_manual_settings', 'wp_adpress_forms::validate');
   add_settings_section('gateway_manual_general_section', 'Manual Settings', 'wp_adpress_forms::description', 'adpress_gateway_manual_form_general');		
   add_settings_field('email', 'PayPal Email', 'wp_adpress_forms::textbox', 'adpress_gateway_manual_form_general', 'gateway_manual_general_section', array('email', 'adpress_gateway_manual_settings'));
}
