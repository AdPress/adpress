<?php
// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( class_exists( 'WPAD_Payment_Gateway' ) ) {
	/**
	 * WPAD_Payment_Gateway_Manual Class
	 *
	 * @since 1.0.0
	 */
	class WPAD_Payment_Gateway_Manual extends WPAD_Payment_Gateway {
		/**
		 * Gateway Settings
		 *
		 * @var array
		 */
		public $settings = array(
			'id' => 'manual',
			'settings_id' => 'manual',
			'short_label' => 'Manual',
			'admin_label' => 'Manual Payment',
			'checkout_label' => 'Manual Payment',
		);	

		/**
         * Constructor for the gateway.
         *
         * @access public
         * @return void
         */
        public function __construct() {
            parent::__construct();
        }

		public function setup_settings_form() {
			register_setting('adpress_gateway_manual_settings', 'adpress_gateway_manual_settings', 'wp_adpress_forms::validate');
			add_settings_section('gateway_manual_general_section', 'Manual Settings', 'wp_adpress_forms::description', 'adpress_gateway_manual_form_general');	
			add_settings_field('label', 'No Settings Required', 'wp_adpress_forms::label', 'adpress_gateway_manual_form_general', 'gateway_manual_general_section');
		}	

		public function process() {
			// Payment Processed		
			$id = wp_adpress_update_payment_status( $this->log_id, 'publish' );		
			
			// Redirect user to the success page
			if ( $id === $this->log_id ) {	
				wp_adpress_send_to_success_page( array( 'pid' => $id ) );
			} else {
				wp_adpress_send_to_failure_page( array( 'pid' => $id ) );
			}	
		}
	}
}
