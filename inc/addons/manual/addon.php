<?php
/**
 * Manual Gateway
 *
 * @package     AdPress
 * @subpackage  Gateways
 * @copyright   Copyright (c) 2014, Abid Omar
 * @since       1.0.0
 */

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

// Load the Gateway Class
require_once( 'manual.class.php' );

// Initialize the Gateway
WPAD_Payment_Gateways::init_gateway( 'manual', 'WPAD_Payment_Gateway_Manual' );
