<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}
/**
 * Ad Sub-Package
 *
 * To create a new Ad Unit, you need the campaign id it'll be linked too as well
 * as the parameters. You need to be knowledgeable of the campaign ad definition.
 * If the params don't respect the campaign ad definition, the creation will
 * fail and no ad unit is created. You should use the save() function to save
 * your ad unit to the database.
 *
 *
 * @package Includes
 * @subpackage Ad
 */

if (!class_exists('wp_adpress_ad')) {
    /**
     * Ad Class
     *
     * Represents an Ad Model
     */
    class wp_adpress_ad
    {
        /**
         * Campaign ID
         * @var integer
         */
        public $campaign_id;

        /**
         * Ad parameters
         * @var array
         */
        public $param;

        /**
         * Ad Status. Possible values are "waiting", "running" and "available"
         * @var string
         */
        public $status;

        /**
         * ID of the user that did register the Ad.
         * @var integer
         */
        public $user_id;

        /**
         * Campaign Ad Definition
         * @var array
         */
        public $ad_definition;

        /**
         * Class State
         * @var string
         */
        private $state = 'unset';

        public function unlock()
        {
            $this->state = 'unset';
        }

        /**
         * Ad Stats
         * @var array
         */
        public $stats = array(
            'views' => array(),
            'hits' => array(),
        );

        /**
         * Time when the Ad is registered or purchased
         * @var string
         */
        public $time;

        /**
         *
         * @param integer $id Ad id
         * @param integer $campaign_id Campaign id
         * @param array $param Ad parameters
         * @param string $status Ad Status
         */
        function __construct($id = null, $campaign_id = null, $param = null, $status = 'available')
        {
            // Load the ad defintion
            // TODO: why is this needed? and does it work?
            $this->load_ad_definition();
            $this->status = $status;

            if ($id === null) {
                // new ad unit
                $this->id = wp_adpress_ads::new_ad_id();
                $this->campaign_id = $campaign_id;
                $this->param = $param;
            } else {
                // load ad unit
                $this->id = $id;
                $this->retrieve_ad();
            }
        }

        /**
         * Load the campaign Ad defintion
         */
        private function load_ad_definition()
        {
            $campaign = new wp_adpress_campaign($this->campaign_id);
            $this->ad_definition = $campaign->ad_definition;
        }

        /**
         * Load the Ad object from the database
         * @global object $wpdb
         */
        private function retrieve_ad()
        {
            global $wpdb;
            $query = "SELECT * FROM " . wp_adpress_ads::ads_table() . " WHERE id=" . $this->id . ";";
            $result = $wpdb->get_row($query, ARRAY_A);
            if ($result != false) {
                $this->campaign_id = $result['campaign_id'];
                $this->param = unserialize($result['ad_settings']);
                $this->status = $result['status'];
                $this->user_id = (int)$result['user_id'];
                $this->stats = unserialize($result['ad_stats']);
                $this->state = 'set';
                $this->time = $result['time'];
                // Campaign Related
                $campaign = new wp_adpress_campaign($this->campaign_id);
                $this->ad_definition = $campaign->ad_definition;
            }
        }

        /**
         * Save the Ad Object to the database
         */
        public function save()
        {
            if ($this->state === 'unset') {
                $this->insert_new();
            } else {
                $this->update_db();
            }
        }

        /**
         * Insert a new Ad record to the database
         * @global object $wpdb
         * @return boolean
         */
        private function insert_new()
        {
            global $wpdb;
            //TODO: check that the data is valid


            /* Ad data */
            $data = array(
                'id' => $this->id,
                'campaign_id' => $this->campaign_id,
                'ad_settings' => serialize($this->param),
                'status' => $this->status,
                'ad_stats' => serialize($this->stats),
                'user_id' => $this->user_id,
                'time' => $this->time
            );

            /* Columns Format */
            $format = array(
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s'
            );

            // Insert Ad to DB
            $result = $wpdb->insert( wp_adpress_ads::ads_table(), $data, $format );

            /* Set the Class state */
            if ($result !== false) {
                $this->state = 'set';
            }

            return $result;
        }

        /**
         * Update an existing Ad record
         * @global object $wpdb
         * @return boolean
         */
        private function update_db()
        {
            global $wpdb;

            /* Ad data */
            $data = array(
                'campaign_id' => $this->campaign_id,
                'ad_settings' => serialize($this->param),
                'status' => $this->status,
                'ad_stats' => serialize($this->stats),
                'user_id' => $this->user_id,
                'time' => $this->time
            );
            /* Row */
            $row = array('id' => $this->id);
            /* Columns format */
            $format = array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s'
            );

            // Update Ad to DB
            $result = $wpdb->update(wp_adpress_ads::ads_table(), $data, $row, $format);

            return $result;
        }


        /**
         * Approve Command
         */
        public function approve()
        {
            // Change status to running
            $this->status = 'running';
            // Time approved
            $this->time = time();

			do_action( 'wp_adpress_register_ad_accepted', $this->id );
        }

        /**
         * Reject Command
         */
        public function reject()
        {
            // issue a refund if applicable
            //$this->issue_refund();
            // Unregister the Ad
            $this->unregister_ad( 'refused' );

			do_action( 'wp_adpress_register_ad_refused', $this->id );
        }

        /**
         * Cancel Command
         */
        public function cancel()
        {
            // issue a refund if applicable
            //$this->issue_refund();
            // Unregister the Ad
            $this->unregister_ad( 'cancelled' );

			do_action( 'wp_adpress_register_ad_cancelled', $this->id );
        }

        /**
         * Record a view
         */
        public function record_view()
        {
			/*
			 * Check for Bots
			 */
			if(!empty($_SERVER['HTTP_USER_AGENT']) and preg_match('~(bot|crawl|slurp|spider|archiver|facebook|curl)~i', $_SERVER['HTTP_USER_AGENT'])){
				return;
			}

            /*
             * Record the view
             */
            if (isset($this->stats['views'][date('Ymd')])) {
                $this->stats['views'][date('Ymd')] += 1;
            } else {
                $this->stats['views'][date('Ymd')] = 1;
            }
            /*
             * Check if the Ad expired
             */
            $campaign = new wp_adpress_campaign($this->campaign_id);
            if ($campaign->ad_definition['contract'] === 'pageviews') {
                $views_limit = (int)$campaign->ad_definition['pageviews'];
                if ($this->total_views() === $views_limit) {
                    $this->unregister_ad( 'expired' );
                }
            }
        }

        /**
         * Record a hit
         */
        public function record_hit()
        {
			/*
			 * Check for Bots
			 */
			if(!empty($_SERVER['HTTP_USER_AGENT']) and preg_match('~(bot|crawl|slurp|spider|archiver|facebook|curl)~i', $_SERVER['HTTP_USER_AGENT'])){
				return;
			}

            /*
             * Record the hit
             */
            if (isset($this->stats['hits'][date('Ymd')])) {
                $this->stats['hits'][date('Ymd')] += 1;
            } else {
                $this->stats['hits'][date('Ymd')] = 1;
            }
            /*
             * Check if the Ad expired
             */
            $campaign = new wp_adpress_campaign($this->campaign_id);
            if ($campaign->ad_definition['contract'] === 'clicks') {
                $clicks_limit = (int)$campaign->ad_definition['clicks'];
                if ($this->total_hits() === $clicks_limit + 1) {
                    $this->unregister_ad( 'expired' );
                }
            }
        }

        /**
         * Calculate the total ad clicks
         * @return int Total Hits
         */
        public function total_hits()
        {
            $sum = array_sum($this->stats['hits']);
            if (!$sum) {
                $sum = 0;
            }
            return $sum;
        }

        /**
         * Calculate the total ad views
         * @return int Total Views
         */
        public function total_views()
        {
            $sum = array_sum($this->stats['views']);
            if (!$sum) {
                $sum = 0;
            }
            return $sum;
        }

        /**
         * Calculate the average Hits, Views and CTR
         * @param $type string
         * @return int|string
         */
        public function avg($type)
        {
            switch ($type) {
            case 'views':
                $count = count($this->stats['views']);
                if (!$count) {
                    $count = 1;
                }
                $avg_views = (int)number_format(($this->total_views() / $count), 0);
                return $avg_views;
                break;
            case 'hits':
                $count = count($this->stats['hits']);
                if (!$count) {
                    $count = 1;
                }
                $avg_hits = (int)number_format(($this->total_hits() / $count), 0);
                return $avg_hits;
                break;
            case 'ctr':
                if ($this->avg('hits') === 0) {
                    $avg_ctr = 0;
                } else {
                    $avg_ctr = number_format($this->avg('views') / $this->avg('hits'), 1) . '%';
                }
                return $avg_ctr;
                break;
            }
        }

        /**
         * Destroy the Ad record
         * @global object $wpdb
         * @return boolean
         */
        public function destroy()
        {
            global $wpdb;
            $query = "DELETE FROM " . wp_adpress_ads::ads_table() . " WHERE id=" . $this->id . ";";
            $result = $wpdb->query($query);
            return $result;
        }

        /**
         * Unregister the Ad
         * @param string $ref
         */
        public function unregister_ad( $ref = null )
        {
            $settings = get_option('adpress_settings');
            // Record the history
            if (isset($settings['history'])) {
                wp_adpress_history::record_history($this);
            }
            // Empty the Ad
            $this->param = array();
            $this->status = 'available';
            $this->stats = array(
                'views' => array(),
                'hits' => array()
            );
            $this->user_id = null;
            $this->time = null;
            $this->post_id = null;

			do_action( 'wp_adpress_unregister_ad', $this->id, $ref );
        }

        /**
         * Issue a PayPal Refund
         * @return boolean|null
         */
        public function issue_refund()
        {
            // TODO: requires refactoring (control and views mixed)
            $settings = get_option('adpress_settings');
            if (isset($settings['paypal']) && isset($this->param['paypal']['PAYMENTINFO_0_TRANSACTIONID']) && isset($settings['paypal_refund']) && $this->status != 'running') {
                $transactionID = $this->param['paypal']['PAYMENTINFO_0_TRANSACTIONID'];
                $gateway = array(
                    'username' => $settings['paypal_username'],
                    'password' => $settings['paypal_password'],
                    'signature' => $settings['paypal_signature'],
                    'version' => '84.0',
                    'payment_action' => 'Sale',
                    'payment_amount' => '',
                    'currency' => '',
                    'return_url' => '',
                    'cancel_url' => ''
                );
                $paypal = new wp_adpress_paypal($gateway, isset($settings['paypal_testmode']));
                if ($paypal->issueRefund($transactionID)) {
                    wp_adpress::display_notice(__('Refund issued', 'wp-adpress'), '<p>' . __('A refund was issued', 'wp-adpress') . '</p>', 'adpress-icon-flag');
                    return true;
                } else {
                    wp_adpress::display_notice(__('Error issuing refund', 'wp-adpress'), '<p>' . __('There was an error while trying to issue the refund. Please Contact the Administrator to issue the refund manually.', 'wp-adpress') . '</p>', 'adpress-icon-flag');
                    return false;
                }
            }
        }

        /**
         * Return the Ad URL
         *
         * @return string URL
         */
        private function get_url()
        {
            $settings = get_option('adpress_settings');
            if (get_option('permalink_structure') != '' && isset($settings['smart_rewrite'])) {
                $url = home_url() . '/adpress/' . $this->id;
            } else {
                $url = home_url() . '?adpress=' . $this->id;
            }
            return $url;
        }

        /**
         * Return the Ad Spot HTML Code
         *
         * @return mixed
         */
        public function get_html()
        {
            switch ($this->ad_definition['type']) {
            case 'image':
                $image_settings = get_option('adpress_image_settings');
                $ad_loop = $image_settings['ad_loop'];
                $ad_loop = str_replace('@url', $this->get_url(), $ad_loop);
                $ad_loop = str_replace('@image_src', $this->param['destination_val'], $ad_loop);
                break;
            case 'flash':
                $flash_settings = get_option('adpress_flash_settings');
                $ad_loop = $flash_settings['ad_loop'];
                $ad_loop = str_replace('@url', $this->get_url(), $ad_loop);
                $ad_loop = str_replace('@swf_src', $this->param['destination_val'], $ad_loop);
                $ad_loop = str_replace('@banner_height', $this->ad_definition['size']['height'] . 'px', $ad_loop);
                $ad_loop = str_replace('@banner_width', $this->ad_definition['size']['width'] . 'px', $ad_loop);
                break;
            case 'link':
                $link_settings = get_option('adpress_link_settings');
                $ad_loop = $link_settings['ad_loop'];
                $ad_loop = str_replace('@url', $this->get_url(), $ad_loop);
                $ad_loop = str_replace('@link_text', $this->param['destination_val'], $ad_loop);
                break;
            }
            return $ad_loop;
        }

    }
}

if (!class_exists('wp_adpress_ads')) {
    /**
     * Ads Class
     */
    class wp_adpress_ads
    {

        /**
         * Return the name of the campaigns table
         * @return string
         */
        static function campaigns_table()
        {
            global $wpdb;
            return $wpdb->prefix . 'adpress_campaigns';
        }

        /**
         * Return the name of the campaigns table
         * @return string
         */
        static function ads_table()
        {
            global $wpdb;
            return $wpdb->prefix . 'adpress_ads';
        }

        /*
         * Generate a new Ad id
         * @return integer
         */

        static function new_ad_id()
        {
            global $wpdb;
            $max_id = $wpdb->get_var('SELECT MAX(id) FROM ' . self::ads_table() . ' WHERE id is not null;');
            if ($max_id === NULL) {
                return 1;
            }
            $new_id = (int)$max_id + 1;
            return $new_id;
        }

        /**
         * Verify that an Ad ID exists
         * @param integer $id
         * @return boolean
         */
        static function id_exists($id)
        {
            global $wpdb;
            $result = $wpdb->get_var('SELECT * FROM ' . self::ads_table() . ' WHERE id=' . $id . ';');
            if ($result === NULL) {
                return false;
            } else {
                return true;
            }
        }

        /**
         * Return an array of ids or Ads objects
         *
         * @global object $wpdb;
         * @param string $status Ad status
         * @param string $output Ads output type (ids/objects)
         * @return array
         */
        static function list_ads($status = 'all', $output = 'object')
        {
            global $wpdb;
            switch ($status) {
            case 'all':
                $query = 'SELECT id FROM ' . self::ads_table() . ';';
                break;
            default:
                $query = 'SELECT id FROM ' . self::ads_table() . ' WHERE status="' . $status . '";';
                break;
            }
            $arr = array();
            $result = $wpdb->get_col($query);
            switch ($output) {
            case 'id':
                $arr = $result;
                break;
            case 'object':
                for ($i = 0; $i < count($result); $i++) {
                    $arr[] = new wp_adpress_ad((int)$result[$i]);
                }
                break;
            }
            return $arr;
        }

        /**
         * List purchased ads for a user
         * @param integer $user_id
         * @return array Array of Ads objects
         */
        static function list_user_ads($user_id)
        {
            global $wpdb;
            $query = 'SELECT id FROM ' . self::ads_table() . ' WHERE user_id=' . $user_id . ';';
            $result = $wpdb->get_col($query);
            $arr = array();
            for ($i = 0; $i < count($result); $i++) {
                $arr[] = new wp_adpress_ad((int)$result[$i]);
            }
            return $arr;
        }

        /**
         * Executes an Ad command
         * @param string $cmd
         * @param integer $id
         */
        static function command($cmd, $id)
        {
            $ad = new wp_adpress_ad($id);
            switch ($cmd) {
            case 'approve':
                if ($ad->status === 'waiting') {
                    $ad->approve();
                }
                break;
            case 'reject':
                if ($ad->status === 'waiting') {
                    $ad->reject();
                }
                break;
            case 'cancel':
                $ad->cancel();
                break;
            }
            $ad->save();
        }

        /**
         * @param integer $id
         */
        static function load_data($id)
        {
            $ad = new wp_adpress_ad($id);
            $hits = array();
            $views = array();
            foreach ($ad->stats['hits'] as $key => $value) {
                $time = strtotime($key);
                $hits[$time] = $value;
            }
            foreach ($ad->stats['views'] as $key => $value) {
                $time = strtotime($key);
                $views[$time] = $value;
            }
            return array('hits' => $hits, 'views' => $views);
        }

        /**
         * Remove all ads from the database.
         * @static
         * @return mixed
         */
        static function empty_ads()
        {
            global $wpdb;
            $query = $wpdb->query('TRUNCATE TABLE ' . self::ads_table() . ';');
            return $query;
        }

    }
}
