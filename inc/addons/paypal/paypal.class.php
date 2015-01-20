<?php
// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( class_exists( 'WPAD_Payment_Gateway' ) ) {
	/**
	 * WPAD_Payment_Gateway_PayPal_Standard Class
	 *
	 * @since 1.0.0
	 */
	class WPAD_Payment_Gateway_PayPal_Standard extends WPAD_Payment_Gateway {
		/**
		 * Gateway Settings
		 *
		 * @var array
		 */
		public $settings = array(
			'id' => 'paypal_standard',
			'settings_id' => 'paypal_standard',
			'short_label' => 'PayPal Standard',
			'admin_label' => 'PayPal Standard',
			'checkout_label' => 'PayPal Standard',
		);

		private $live_url = 'https://www.paypal.com/cgi-bin/webscr?';

		private $sandbox_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?';

		/**
		 * Constructor for the gateway.
		 *
		 * @access public
		 * @return void
		 */
		public function __construct() {
			parent::__construct(); 

            add_action( 'wp_loaded', array( &$this, 'listen_ipn' ) );  
		}

		/**
		 * Register the gateway settings fields 
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function setup_settings_form() {
			register_setting('adpress_gateway_paypal_standard_settings', 'adpress_gateway_paypal_standard_settings', 'wp_adpress_forms::validate');
			add_settings_section('gateway_paypal_standard_general_section', 'PayPal Settings', 'wp_adpress_forms::description', 'adpress_gateway_paypal_standard_form_general');		
			add_settings_field('email', 'PayPal Email', 'wp_adpress_forms::textbox', 'adpress_gateway_paypal_standard_form_general', 'gateway_paypal_standard_general_section', array('email', 'adpress_gateway_paypal_standard_settings')); 
			add_settings_field('ipn', 'Disable IPN', 'wp_adpress_forms::checkbox', 'adpress_gateway_paypal_standard_form_general', 'gateway_paypal_standard_general_section', array('ipn', 'adpress_gateway_paypal_standard_settings'));
		}

		/**
		 * Get PayPal Args for passing to PayPal
		 *
		 * @access public
		 * @param array $order
		 * @return array
		 */
		private function get_paypal_args( $order = array() ) {

			$campaign = new wp_adpress_campaign( $this->ad_details['cid'] );

			$args = array(
				'cmd'           => '_cart',
				'upload'        => '1',
				'business'      => $this->get_option( 'email' ), 
				'return'        => wp_adpress_get_success_page_uri( array( 'pid' => $this->log_id, 'wpad-gateway' => 'paypal-standard', 'wpad-callback' => 'custom' ) ),
				'cancel_return' => wp_adpress_get_cancel_page_uri( array( 'pid' => $this->log_id, 'wpad-gateway' => 'paypal-standard' ) ), 
				'notify_url'    => wp_adpress_get_notify_page_uri( array( 'pid' => $this->log_id, 'wpad-gateway' => 'paypal-standard' ) ),
				'no_shipping'   => '1',
				'item_name_1'   => $campaign->settings['name'],
				'quantity_1'    => '1',
				'amount_1'      => $campaign->ad_definition['price'],
				'currency_code' => wp_adpress_get_currency(),
				'charset'       => 'UTF-8',
				'lc'            => 'US', 
				'bn'            => 'WP_ADPRESS',
				'invoice'       => get_post_meta( $this->log_id, 'wpad_payment_purchase_key', true ),
				'custom'        => $this->log_id,
			);

			$args = apply_filters( 'wp_adpress_paypal_standard_args', $args );

			return $args;
		}

		/**
		 * Return the PayPal Redirect URL
		 *
		 * @access private
		 * @since 1.0.0
		 * @return string
		 */
		private function get_paypal_redirect() {
			if ( wp_adpress_sandbox_mode() ) {
				return $this->sandbox_url;
			} else {
				return $this->live_url;
			}
		}

		/**
		 * Process PayPal Purchase
		 *
		 * @since 1.0 
		 * @return void
		 */
		public function process() {
			$paypal_args = $this->get_paypal_args();

			// Build Query
			$paypal_redirect = $this->get_paypal_redirect() . http_build_query( $paypal_args );

			// Redirect Buyer
			wp_redirect( $paypal_redirect );

			// Exit
			wp_adpress_die();
		}

		/**
		 * Confirm payment by updating the purchase log status 
		 *
		 * @param $data array
		 * @since 1.0 
		 * @return void
		 */
		private function confirm_payment( $data ) { 
			if ( isset( $data['invoice'] ) && isset( $data['custom'] ) ) { 
				if ( get_post_meta( intval( $data['custom'] ), 'wpad_payment_purchase_key', true ) == $data['invoice'] ) {
					// payment is verified	
					wp_adpress_update_payment_status( $data['custom'], 'publish' );
				}
			}
		}

		/**
		 * IPN Listener 
		 *
		 * @since 1.0 
		 * @return void
		 */
		public function listen_ipn() { 
			if ( isset( $_GET['wpad-callback'] ) && $_GET['wpad-callback'] == 'notify' && isset( $_GET['wpad-gateway'] ) && $_GET['wpad-gateway'] == 'paypal-standard' ) {
				$this->process_ipn();	
				wp_adpress_die();
			}
		}

		/**
		 * Process PayPal IPN
		 *
		 * @since 1.0 
		 * @return void
		 */
		public function process_ipn() { 
			// Check the request method is POST
			if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] != 'POST' ) {
				return;
			}

			$data = array(
				'cmd' => '_notify-validate',
			);

			// Check if POST is empty
			if ( empty( $_POST ) ) {
				// Nothing to do
				return;
			} else {
				// Loop through each POST
				foreach ( $_POST as $key => $value ) {
					// Encode the value and append the data
					$data[$key] = $value;
				}
			}

			// Validate the IPN
			$remote_post_vars      = array(
				'method'           => 'POST',
				'timeout'          => 60,
				'redirection'      => 5,
				'httpversion'   => '1.1',
				'compress'      => false,
				'decompress'    => false,
				'blocking'         => true,
				'headers'          => array(
					'host'         => 'www.paypal.com',
					'connection'   => 'close',
					'content-type' => 'application/x-www-form-urlencoded',
					'post'         => '/cgi-bin/webscr HTTP/1.1',

				),
				'sslverify'        => false,
				'body'             => $data,
			);

			// Get response 
			$api_response = wp_remote_post( $this->get_paypal_redirect(), $remote_post_vars );

			if ( is_wp_error( $api_response ) ) {
				// report error
				return;
			}

			if ( $api_response['body'] !== 'VERIFIED' ) {
				// report error
				return; 
			} 

			// Confirm payment
			$this->confirm_payment( $data );
		}	
	}
}
