<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

if (!class_exists('wp_adpress_adview')) {
    /**
     * @package Views
     * @subpackage Ad Page
     */
    class wp_adpress_adview
    {

        public $ad;
        public $view;
        public $id;
        public $mu;

        function __construct()
        {
            /*
            * Check: Ad Parameter
            */
            if (!isset($_GET['id'])) {
                $this->no_ad_set();
                return;
            }

            // Set the Ad Parameter
            $this->id = $_GET['id'];
            /*
            * Check: Ad Exists
            */
            if (!wp_adpress_ads::id_exists($this->id)) {
                $this->ad_no_exists();
                return;
            }
            $this->ad = new wp_adpress_ad($this->id);
            /*
            * Check: Ad running
            */
            if ($this->ad->status != 'running') {
                $this->ad_no_running();
                return;
            }
            /*
            * Check: Access rights
            */
            if (!$this->has_access()) {
                $this->no_ad_access();
                return;
            }
        }

        /**
         * Check: Ad Parameter
         */
        private function no_ad_set()
        {
            wp_adpress::display_message('Ad not set', 'Ad not set', '<p>There is no Ad ID set.</p>', null, null);
        }

        /**
         * Check: Ad Exists
         */
        private function ad_no_exists()
        {
            wp_adpress::display_message('Ad not existant', 'Ad not existant', '<p>There is no Ad with such an ID.</p>', null, null);
        }

        /**
         * Check: Ad running
         */
        private function ad_no_running()
        {
            wp_adpress::display_message('Ad not running', 'Ad not running', '<p>This Ad is currently inactive.</p>', null, null);
        }

        /**
         * Check: Access rights
         */
        private function no_ad_access()
        {
            wp_adpress::display_message('No Access', 'No Access', '<p>you don\'t have the rights to see this ad stats</p>', null, null);
        }

        /**
         * Verify if the current user has access
         * @return boolean
         */
        private function has_access()
        {
            /* Administrator have access to all Ads */
            if (current_user_can('manage_options')) {
                return true;
            }
            /* Users only have access to their running ads */
            if (get_current_user_id() === $this->ad->user_id) {
                return true;
            }
            return false;
        }

        public function generate_view()
        {

			do_action( 'wp_adpress_stats_header', $this->ad  ); 
			do_action( 'wp_adpress_stats_body', $this->ad ); 
			do_action( 'wp_adpress_stats_footer', $this->ad ); 
        }
    }
}

