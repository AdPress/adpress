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
	 * Contains the Ids of Active Gateways 
	 *
	 * @access private
	 * @static
	 * @since 1.0.3
	 *
	 * @var array
	 */
	private static $active_gateways = array();

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
	 * Initializes Gateways and business logic behind them
	 *
	 * @since 1.0.3
	 * @return void
	 */
	public static function init() {
		// Register the Gateways
		self::register_gateways();

		// Initialize Gateways (Call Active Gateways init function)
		self::initialize_gateways();

		// Handles Callbacks attached to Gateways		
		if ( isset( $_REQUEST['wpad-gateway'] ) && isset( $_REQUEST['wpad-callback'] ) && self::is_registered( $_REQUEST['wpad-gateway'] ) ) {
			add_action( 'init', array( 'WPAD_Payment_Gateways', 'action_process_callbacks' ) );
		}
	}

	/**
	 * Initialize a Payment Gateway Object and register it in the 
	 * active instances.
	 *
	 * @access public
	 * @param object $gateway Payment Gateway Class Reference
	 */
	public static function init_gateway( $gateway_id, $gateway ) {		
		// Initialize the gateway and add it to the instances array
		self::$instances[ $gateway_id ] = new $gateway();	
	}

	/**
	 * Register Gateways hooked into our Gateways Filter
	 *
	 * @access public
	 * @since 1.0.3
	 *
	 * @return void
	 */
	public static function register_gateways() {
		$gateways = array();
		self::$gateways = apply_filters( 'wpad_payment_gateways', $gateways );
		return self::$gateways; 
	}

	/**
	 * Initialize the Active Gateways
	 *
	 * @access public
	 * @since 1.0.3
	 *
	 * @return void
	 */
	public static function initialize_gateways() {
		$active_gateways = self::get_active_gateways();	

		foreach( $active_gateways as $gateway_id ) {
			$gateway_class = 'WPAD_Payment_Gateway_' . ucfirst( $gateway_id );
			self::init_gateway( $gateway_id, $gateway_class );	
		}
	}

	/**
	 * Executes Gateways Callbacks
	 *
	 * @access public
	 * @since 1.0.3
	 *
	 * @return void
	 */
	public static function action_process_callbacks() {
		$gateway = self::get( $_REQUEST['wpad-gateway'] );
		$function_name = "callback_{$_REQUEST['wpad-callback']}";
		$callback = array( $gateway, $function_name );
		if ( is_callable( $callback ) ) {
			$gateway->$function_name();
		}
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
	 * Check to see whether a gateway is registered 
	 *
	 * @access public
	 * @since 1.0.3
	 *
	 * @param string $gateway Gateway name 
	 * @return bool True if it's already registered.
	 */
	public static function is_registered( $gateway ) {	
		if ( in_array( $gateway, self::$gateways ) ) {
			return true;
		}
			return false;	
	}

	/**
	 * Return an array of all gateways ids
	 *
	 * @access public
	 * @return array
	 * @since 1.0
	 */
	public static function get_gateways_ids() {	
		return self::$gateways;
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

	/**
	 * Return an Array of Active Gateways.
	 *
	 * @access public
	 * @since 1.0.3
	 *
	 * @return array
	 */
	public static function get_active_gateways() {
		if ( empty( self::$active_gateways ) ) {
			$settings = get_option( 'adpress_gateways_settings' );			
			
			if ( isset( $settings['active'] ) ) {
				$selected_gateways = array_keys( $settings['active'] );
			} else {
				$selected_gateways = array();
			}

			$registered_gateways = self::get_gateways_ids();	
			self::$active_gateways = array_intersect( $selected_gateways, $registered_gateways );
		}	

		return apply_filters( 'wpad_get_active_gateways', self::$active_gateways );
	}
}
