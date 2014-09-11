<?php
/**
 * AdPress Payment Gateways Global class
 *
 * This class provides a global wrapper for the AdPress gateways
 *
 * @package		Includes
 * @subpackage	Gateways
 * @copyright	Copyright (c) 2014, Abid Omar 
 * @since		1.0
 */

// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

final class WPAD_Payment_Gateways {
	/**
	 * Contain a key-value array of gateway names and gateway class names
	 *
	 * @access private
	 * @static
	 * @var array
	 * @since 1.0
	 */
	private static $gateways = array();

	/**
	 * Contain an array of payment gateway objects
	 *
	 * @access private
	 * @static
	 * @var array
	 * @since 1.0
	 */
	private static $instances = array();

	/**
 	 * Initializes a Payment Gateway Object and register it in the 
	 * active instances.
	 *
	 * @access public
	 * @param object $gateway Payment Gateway Class Reference
	 */
	public static function init_gateway( $gateway_id, $gateway ) {	
		// Add the gateway class reference to the gateways array
		self::$gateways[ $gateway_id ] = $gateway;

		// Initialize the gateway and add it to the instances array
		self::$instances[ $gateway_id ] = new $gateway();
	}

	/**
	 * Return a particular payment gateway object
	 *
	 * @access public
	 * @param string $gateway Name of the payment gateway you want to get
	 *
	 * @return object
	 * @since 1.0
	 */
	public static function &get( $gateway ) {
		return self::$instances[ $gateway ];
	}

	/**
 	 * Return an array of all initialized gateway objects
	 *
	 * @access public
	 * @return array
	 * @since 1.0
	 */
	public static function get_gateways() {
		return self::$instances;
	}
}
