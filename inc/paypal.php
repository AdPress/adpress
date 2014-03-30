<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

if (!class_exists('wp_adpress_paypal')) {
    /**
     * PayPal ExpressCheckOut for WordPress
     *
     * This code is not licensed. Feel free to use it in your own open source and
     * commercial projects. The code is provided "AS IS" without any warranty or
     * conditions of any kind.
     *
     * @author Abid Omar
     * @package Includes
     * @subpackage Payment
     */
    class wp_adpress_paypal
    {

        /**
         * Payment instance details
         * @var array
         */
        private $gateway;

        /**
         * PayPal API server
         * @var string
         */
        private $server = 'https://api-3t.paypal.com/nvp';

        /**
         * PayPal Sandbox API server
         * @var string
         */
        private $server_sandbox = 'https://api-3t.sandbox.paypal.com/nvp';

        /**
         * PayPal Payment URL
         * @var string
         */
        private $redirect_url = 'https://www.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=';

        /**
         * PayPal Sandbox Payment URL
         * @var string
         */
        private $redirect_url_sandbox = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=';

        /**
         * Create a new instance of the PayPal class.
         *
         * @param array $param
         * @param boolean $test_mode
         */
        function __construct($param, $test_mode = false)
        {
            /*
            * Set the gateway array variables
            */
            $this->gateway = array(
                'USER' => $param['username'],
                'PWD' => $param['password'],
                'SIGNATURE' => $param['signature'],
                'PAYMENTREQUEST_0_PAYMENTACTION' => $param['payment_action'],
                'PAYMENTREQUEST_0_AMT' => $param['payment_amount'],
                'PAYMENTREQUEST_0_CURRENCYCODE' => $param['currency'],
                'RETURNURL' => $param['return_url'],
                'CANCELURL' => $param['cancel_url'],
                'VERSION' => $param['version'],
                'NOSHIPPING' => 1,
                'ALLOWNOTE' => 1
            );
            /*
            * Change the server and redirect url if we are in a test mode
            */
            if ($test_mode) {
                $this->server = $this->server_sandbox;
                $this->redirect_url = $this->redirect_url_sandbox;
            }
        }

        /**
         * Generate the redirect URL that will ask the user for the payment permission
         *
         * @return string Redirect URL
         */
        public function doExpressCheckout()
        {
            // Request arguments
            $body = $this->gateway;
            $body['METHOD'] = 'SetExpressCheckout';
            $request = array(
                'method' => 'POST',
                'body' => $body,
                'timeout' => 60,
                'sslverify' => apply_filters('https_local_ssl_verify', false),
                'httpversion' => '1.1',
            );

            // Make the HTTP request
            $response = wp_remote_post($this->server, $request);

            // Check that we have a valid response
            if (is_wp_error($response)) {
                wp_adpress::display_log($response);
                return false;
            }
            parse_str(urldecode($response['body']), $response);

            if (strtolower($response['ACK']) === 'success') {
                return ($this->redirect_url . $response['TOKEN']);
            } else {
                wp_adpress::display_log($response);
                return false;
            }
        }

        /**
         * Process the payment.
         *
         * The function returns true if the user completed the payment, and false in the
         * other case.
         *
         * @param string $token
         * @param string $payer_id
         * @return boolean
         */
        public function processPayment($token, $payer_id)
        {
            $body = $this->gateway;
            $body['METHOD'] = 'DoExpressCheckoutPayment';
            $body['PAYERID'] = $payer_id;
            $body['TOKEN'] = $token;
            $request = array(
                'method' => 'POST',
                'body' => $body,
                'timeout' => 60,
                'sslverify' => apply_filters('https_local_ssl_verify', false),
                'httpversion' => '1.1',

            );
            $response = wp_remote_post($this->server, $request);
            wp_adpress::display_log($response);
            if (is_wp_error($response)) {
                wp_adpress::display_log($response);
                return false;
            }

            parse_str(urldecode($response['body']), $response);

            if (strtolower($response['ACK']) === 'success' && strtolower($response['PAYMENTINFO_0_PAYMENTSTATUS']) === 'completed') {
                return $response;
            } else {
                wp_adpress::display_log($response);
                return false;
            }
        }

        /**
         * Issue a complete refund to the specified transaction Id
         * @param string $transactionID
         * @return boolean
         */
        public function issueRefund($transactionID)
        {
            $body = $this->gateway;
            $body['METHOD'] = 'RefundTransaction';
            $body['TRANSACTIONID'] = $transactionID;
            $body['REFUNDTYPE'] = 'Full';
            $request = array(
                'method' => 'POST',
                'body' => $body,
                'timeout' => 60,
                'sslverify' => apply_filters('https_local_ssl_verify', false),
                'httpversion' => '1.1',

            );

            $response = wp_remote_post($this->server, $request);
            if (is_wp_error($response)) {
                wp_adpress::display_log($response);
                return false;
            }

            parse_str(urldecode($response['body']), $response);

            if (strtolower($response['ACK']) === 'success') {
                return true;
            } else {
                wp_adpress::display_log($response);
                return false;
            }
        }

    }
}

