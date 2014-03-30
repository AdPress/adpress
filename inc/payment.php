<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

if (!class_exists('wp_adpress_payment')) {
    /**
     * @package Includes
     * @subpackage Payment
     */
    class wp_adpress_payment
    {

        /**
         * Campaign ID
         * @var integer
         */
        private $cid;

        /**
         * Ad Parameters
         * @var array
         */
        private $param;

        function __construct($post)
        {
            // Process the POST Data
            $this->process_post($post);
        }

        /**
         * Process the POST Data
         * @param array $post
         */
        private function process_post($post)
        {
            /*
            * Analyze the purchase details
            */
            $cid = (int)$post['cid'];
            $campaign = new wp_adpress_campaign($cid);
            $param = array();
            $settings = get_option('adpress_settings');
            switch ($campaign->ad_definition['type']) {
                case 'link':
                    $param = array(
                        'link_text' => $post['link_text'],
                        'url' => $post['destination_url'],
                        'client_message' => $post['client_message']
                    );
                    break;
                case 'image':
                    $param = array(
                        'image_link' => $post['image_link'],
                        'url' => $post['destination_url'],
                        'client_message' => $post['client_message']
                    );
                    break;
            }
            $this->cid = $cid;
            $this->param = $param;
        }

    }
}

/**
 * Inserts payment
 * @since 0.98
 * @param array $payment_data
 * @return bool true if payment inserted, false otherwise
 */
function wp_adpress_insert_payment ($payment_data = array()) {

	if (empty($payment_data)) {
		return false;
	}

	// Set the correct time zone
	date_default_timezone_set( wp_adpress_get_timezone_id() );

}
