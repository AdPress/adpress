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
