<?php
// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
    die( '-1' );
}

/**
 * Campaign Package
 *
 * @package Includes
 * @subpackage Campaign
 */

if ( !class_exists( 'wp_adpress_campaign' ) ) {
    /**
     * Campaign Class
     *
     * Represents a campaing model
     */
    class wp_adpress_campaign
    {

        /**
         * Unique Campain ID, matches the one in the database
         * @var integer Campaign ID
         */
        public $id;

        /**
         * Campaign Settings
         * @var array
         */
        public $settings;

        /**
         * Ad Definition
         * @var array
         */
        public $ad_definition;

        /**
         * This variable holds the campaign state. When the campaign object is fully
         * loaded the variable value is "set"; and "unset" otherwise.
         *
         * @var string set/unset
         */
        private $state = 'unset';

        /**
         * This function unlocks the campaign class instance
         */
        public function unlock()
        {
            $this->state = 'unset';
        }

        /**
         * Creates a new campaign instance. All parameters are optional.
         *
         * @param integer $id Campaign ID
         * @param array $settings Campaign Settings
         * @param array $ad_definition Campaign Ad definition
         */
        function __construct( $id = null, $settings = array(), $ad_definition = array( 'number' => 0 ) )
        {
            /*
            * Creates a new Campaign if no id is provided
            */
            if ($id === null) {
				// The new way of doing things	
				$this->id = $this->insert_new_campaign( $settings, $ad_definition );
            } else { 
                // Retrieve Campaign settings from DataBase
                $this->load_campaign( $id );
            }
        }

		private function insert_new_campaign( $settings, $ad_definition )
		{
			if ( empty ( $settings ) || empty( $ad_definition ) ) {
				return false;
			}	

			// Make sure the campaign is inserted with the correct timezone
			date_default_timezone_set( wp_adpress_get_timezone_id() );

			// Post Args
			$args = array (
				'post_title'    => $settings['name'],
				'post_status'   => 'publish',
				'post_type'     => 'wp_adpress_campaigns',
				'post_parent'   =>  null,
				'post_date'     =>  null,
				'post_date_gmt' =>  null,
			);

			// Insert the Campaign Post
			$campaign = wp_insert_post( $args );

			if ( $campaign ) {
				update_post_meta( $campaign, 'wpad_campaign_name',        $settings['name'] );
				update_post_meta( $campaign, 'wpad_campaign_description', $settings['description'] );
				update_post_meta( $campaign, 'wpad_campaign_state',       $settings['state'] );
				update_post_meta( $campaign, 'wpad_campaign_settings',    serialize( $settings ) );
				update_post_meta( $campaign, 'wpad_campaign_addefinition',serialize( $ad_definition ) );

				return $campaign; // return the ID
			}

			// return false if no campaign is inserted
			return false;
		}

		public function update_campaign()
		{
			$settings = serialize( $this->settings );
			update_post_meta( $this->id, 'wpad_campaign_settings', $settings );	

			$ad_definition = serialize( $this->ad_definition );
			update_post_meta( $this->id, 'wpad_campaign_addefinition', $ad_definition );
		}

		private function load_campaign( $id )
		{
			if ( 'publish' != get_post_status ( $id ) ) {
				return false;
			}

			$this->id = $id;

			$this->state = 'set'; // This should probably get removed
			
			// These are the parameters that should get used
			/*
			$this->settings->state = get_post_meta( $id, 'wpad_campaign_state', true );
			$this->settings->name = get_post_meta( $id, 'wpad_campaign_name', true );
			$this->settings->description = get_post_meta( $id, 'wpad_campaign_description', true );
			 */

			$settings = get_post_meta( $id, 'wpad_campaign_settings', true );
			$this->settings = unserialize( $settings );

			$ad_definition = get_post_meta( $id, 'wpad_campaign_addefinition', true );
			$this->ad_definition = unserialize( $ad_definition );

			return true;
		}

        /**
         * Returns the state of the campaign
         * @return string Campaign state
         */
        public function state()
        {
            return $this->settings['state'];
        }

        /**
         * Activate a campaign
         *
         * @return boolean
         */
        public function activate()
        {
            // No checks are required
            $this->settings['state'] = 'active';

			$this->update_campaign();
            return true;
        }

        /**
         * Deactivate a campaign
         * @return boolean
         */
        public function deactivate()
        {
            if ( $this->is_editable() ) {
                $this->settings['state'] = 'inactive';

				$this->update_campaign();
                return true;
            }

            return false;
        }

        /**
         * Remove the campaign from the database.
         * @global object $wpdb
         */
        public function remove()
        {
			// we should clear children posts (ad posts)
			wp_delete_post( $this->id, true );
        }

        /**
         * Returns an array of the campaign ads objects. It's possible to filter the
         * returned array based on the Ad status.
         * @global object $wpdb
         * @param string $status Ad status. Default is all.
         * @return array
         */
        public function list_ads($status = 'all')
        {
            // Returns the Ads in an Array
            $ads_a = array();
            global $wpdb;

            switch ($status) {
                case 'all':
                    $query = "SELECT id FROM " . wp_adpress_campaigns::ads_table() . " WHERE campaign_id=" . $this->id . ";";
                    break;
                default:
                    $query = 'SELECT id FROM ' . wp_adpress_campaigns::ads_table() . ' WHERE campaign_id=' . $this->id . ' AND status="' . $status . '";';
                    break;
            }
            $result = $wpdb->get_col($query);
            for ($i = 0; $i < count($result); $i++) {
                $ads_a[] = new wp_adpress_ad((int)$result[$i]);
            }
            return $ads_a;
        }

        /**
         * Returns an array with the ads availability
         *
         * @global object $wpdb
         * @return array
         * @deprecated
         * TODO: Remove this function
         */
        public function list_ads_availability()
        {
            // Returns the Ads in an Array
            $ads_a = array();
            global $wpdb;
            $query = "SELECT * FROM " . wp_adpress_campaigns::ads_table() . " WHERE campaign_id=" . $this->id . ";";
            for ($i = 0; $i < $this->ad_definition['number']; $i++) {
                $result = $wpdb->get_row($query, ARRAY_A, $i);
                $ads_a[] = $result['status'];
            }
            return $ads_a;
        }

        /**
         * Returns true if the campaign can be edited, deactivated or removed
         * @return boolean
         */
        public function is_editable()
        {
			return true; // temp
            $running = $this->list_ads( 'running' );
            $waiting = $this->list_ads( 'waiting' );
            if (count($running) > 0 || count($waiting) > 0) {
                return false;
            } else {
                return true;
            }
        }

        /**
         * Register a new Ad spot
         * @param array $param Ad parameters
         * @param string $status
         * @return boolean
         * // TODO: needs refactoring
         */
        public function register_ad($param, $status = 'waiting')
        {
            $current_user = wp_get_current_user();
            global $pid;
            $pid = $current_user->ID;
            $settings = get_option('adpress_settings');
            $current_user = wp_get_current_user();
            $available_ads = $this->list_ads('available');
            if (count($available_ads) > 0) {
                $new_ad = $available_ads[0]; 
                $new_ad->param = $param;
                // User ID
                $new_ad->user_id = $current_user->ID;
                // Purchase/Request Time
                $new_ad->time = time();
                // Approval Status
                if (isset($settings['auto_approve']) || current_user_can('manage_options')) {
                    $new_ad->status = 'running';
                } else {
                    $new_ad->status = $status;
                }
                // Time Expiration Ad
                if ($this->ad_definition['contract'] === 'duration') {
                    $days = (int)$this->ad_definition['duration'];
                    $time = $days * 24 * 60 * 60;
                    wp_schedule_single_event(time() + $time, 'adpress_unregister_ad', array($new_ad->id));
                }

                $new_ad->save();
                // Remove all set options
                //
                // Return the campaign Id
                return $new_ad->id;
            }
            return false;
        }

        /**
         * Renders an image Ad Spot
         *
         * @param integer $id
         * @param url $image_src
         * @return string $ad_loop
         * @deprecated
         */
        private function image_ad_spot($id, $image_src)
        {
            $image_settings = get_option('adpress_image_settings');
            $ad_loop = $image_settings['ad_loop'];
            if (get_option('permalink_structure') != '') {
                $url = site_url() . '/adpress/' . $id;
            } else {
                $url = site_url() . '?adpress=' . $id;
            }
            $ad_loop = str_replace('@url', $url, $ad_loop);
            $ad_loop = str_replace('@image_src', $image_src, $ad_loop);
            return $ad_loop;
        }

        /**
         * Renders a CTA spot
         * @param integer $available Available spots
         * @return string $ad_loop
         */
        private function image_cta_spot($available)
        {
            $image_settings = get_option('adpress_image_settings');
            $ad_loop = $image_settings['ad_loop'];
            $ad_loop = str_replace('@url', $this->ad_definition['cta_url'], $ad_loop);
            $ad_loop = str_replace('@image_src', $this->ad_definition['cta_img'], $ad_loop);
            if (isset($this->ad_definition['cta_fill'])) {
                $ad_loop = str_repeat($ad_loop, $available);
            }
            return $ad_loop;
        }

        /**
         * Render a link Ad Spot
         * @param integer $id
         * @param string $link_text
         * @return string $ad_loop
         * @deprecated
         */
        private function link_ad_spot($id, $link_text)
        {
            $image_settings = get_option('adpress_link_settings');
            $ad_loop = $image_settings['ad_loop'];
            if (get_option('permalink_structure') != '') {
                $url = site_url() . '/adpress/' . $id;
            } else {
                $url = site_url() . '?adpress=' . $id;
            }
            $ad_loop = str_replace('@url', $url, $ad_loop);
            $ad_loop = str_replace('@link_text', $link_text, $ad_loop);
            return $ad_loop;
        }

        /**
         * Renders a link CTA spot
         * @param $available string
         * @return string
         */
        private function link_cta_spot($available)
        {
            $image_settings = get_option('adpress_link_settings');
            $ad_loop = $image_settings['ad_loop'];
            $ad_loop = str_replace('@url', $this->ad_definition['cta_url'], $ad_loop);
            $ad_loop = str_replace('@link_text', $this->ad_definition['cta_text'], $ad_loop);
            if (isset($this->ad_definition['cta_fill'])) {
                $ad_loop = str_repeat($ad_loop, $available);
            }
            return $ad_loop;
        }

        /**
         * Renders a Flash Ad Spot
         *
         * @param integer $id
         * @param url $swf_src
         * @return string $ad_loop
         * @deprecated
         */
        private function flash_ad_spot($id, $swf_src)
        {
            $image_settings = get_option('adpress_flash_settings');
            $ad_loop = $image_settings['ad_loop'];
            if (get_option('permalink_structure') != '') {
                $url = site_url() . '/adpress/' . $id;
            } else {
                $url = site_url() . '?adpress=' . $id;
            }
            $ad_loop = str_replace('@url', $url, $ad_loop);
            $ad_loop = str_replace('@swf_src', $swf_src, $ad_loop);
            $ad_loop = str_replace('@banner_height', $this->ad_definition['size']['height'] . 'px', $ad_loop);
            $ad_loop = str_replace('@banner_width', $this->ad_definition['size']['width'] . 'px', $ad_loop);
            return $ad_loop;
        }

        /**
         * Renders a Flash Ad Spot
         *
         * @param integer $available
         * @return string $ad_loop
         */
        private function flash_cta_spot($available)
        {
            $image_settings = get_option('adpress_flash_settings');
            $ad_loop = $image_settings['ad_loop'];
            $ad_loop = str_replace('@url', $this->ad_definition['cta_url'], $ad_loop);
            $ad_loop = str_replace('@swf_src', $this->ad_definition['cta_banner'], $ad_loop);
            $ad_loop = str_replace('@banner_height', $this->ad_definition['size']['height'] . 'px', $ad_loop);
            $ad_loop = str_replace('@banner_width', $this->ad_definition['size']['width'] . 'px', $ad_loop);
            if (isset($this->ad_definition['cta_fill'])) {
                $ad_loop = str_repeat($ad_loop, $available);
            }
            return $ad_loop;
        }

        /**
         * Display the campaign Ads
         *
         * @param $return bool
         * @param $style string
         * @return string $html
         */
        public function display($return = false, $style = '')
        {
            // Display the Ads
            $to_display = $this->to_display();
            $html = '';
            switch ($this->ad_definition['type']) {
                case 'image':
                    $width = $this->ad_definition['columns'] * ($this->ad_definition['size']['width'] + 15);
                    $html .= '<ul id="campaign-' . $this->id . '" class="image-campaign ' . get_style_el($style, 'list_class') . '" style="max-width:' . $width . 'px;">';
                    break;
                case 'flash':
                    $width = $this->ad_definition['columns'] * ($this->ad_definition['size']['width'] + 15);
                    $html .= '<ul id="campaign-' . $this->id . '" class="flash-campaign ' . get_style_el($style, 'list_class') . '" style="max-width:' . $width . 'px;">';
                    break;
                case 'link':
                    $html .= '<ul id="campaign-' . $this->id . '" class="link-campaign ' . get_style_el($style, 'list_class') . '">';
                    break;
            }
            if (!empty($to_display)) {
                foreach ($to_display as $i => $id) {
                    $ad = new wp_adpress_ad($id);
                    $html .= $ad->get_html();
                    $ad->record_view();
                    $ad->save();
                }
            }

            // Display CTA Spots
            $html .= $this->display_cta();

            // Closes the list
            $html .= '</ul>';

			// Apply any possible filters
			$html = apply_filters( 'wp_adpress_campaign_display_ads', $html, $this );

            // Print or Return the output
            if ($return) {
                return $html;
            } else {
                echo $html;
            }
        }

        /**
         * Display CTA spots
         * @return string
         * TODO: requires refactoring
         */
        private function display_cta()
        {
            if (isset($this->ad_definition['cta_url'])) {
                // Get the number of ads to display
                if (isset($this->ad_definition['rotation'])) {
                    $display = $this->ad_definition['rotation'];
                } else {
                    $display = $this->ad_definition['number'];
                }

                // Get the number of the remaining spots
                $remaining = $display - count($this->list_ads('running'));

                // If no spots remain, quit
                if ($remaining < 1) {
                    return;
                }

                // If the CTA fill is not enabled, just put one CTA Ad
                if (!isset($this->ad_definition['cta_fill']) && $remaining > 0) {
                    $remaining = 1;
                }

                // Display the CTA Ad
                switch ($this->ad_definition['type']) {
                    case 'image':
                        return $this->image_cta_spot($remaining);
                        break;
                    case 'flash':
                        return $this->flash_cta_spot($remaining);
                        break;
                    case 'link':
                        return $this->link_cta_spot($remaining);
                        break;
                }
            }
        }


        /**
         * Returns an Array of running Ads Ids to display
         * @return Array
         */
        private function to_display()
        {
            if (isset($this->ad_definition['rotation'])) {
                $display = $this->ad_definition['rotation'];
            } else {
                $display = $this->ad_definition['number'];
            }
            $number = $this->ad_definition['number'];
			
            $to_display = array();
            switch (true) {
                // Something is wrong
                case ($display > $number):
                case ($display === 0 && $number === 0):
                    return false;
                    break;
                // Ad Rotation
                case ($display <= $number):	
                    $running = $this->list_ads('running');
                    foreach ($running as $ad) {
                        $to_display[] = $ad->id;
                    }
                    // No Ad is running
                    if (empty($to_display)) {
                        break;
                    }
                    // No Ad Rotation
                    if ($display === $number || count($to_display) < $display) {
                        break;
                    }
                    // Rotate Ads
                    shuffle($to_display);
                    $to_display = array_slice($to_display, 0, $display);
                    break;
            }
            return $to_display;
        }
    }
}

if (!class_exists('wp_adpress_campaigns')) {
    /**
     * Campaigns Class
     *
     * This is a convenience class made to make interacting with
     * the database for campaign calls easier.
     */
    class wp_adpress_campaigns
    {

        /**
         * Return the name of the campaigns table
         * @static
         * @return string
         */
        static function campaigns_table()
        {
            global $wpdb;
            return $wpdb->prefix . 'adpress_campaigns';
        }

        /**
         * Return the name of the campaigns table
         * @static
         * @return string
         */
        static function ads_table()
        {
            global $wpdb;
            return $wpdb->prefix . 'adpress_ads';
        }

        /**
         * This function returns a unique ID for a new campaign
         * @return integer new campaign ID
         */
        static function new_campaign_id()
        {
            global $wpdb;
            $max_id = $wpdb->get_var('SELECT MAX(id) FROM ' . self::campaigns_table() . ' WHERE id is not null;');
            if ($max_id === NULL) {
                return 1;
            }
            $new_id = (int)$max_id + 1;
            return $new_id;
        }

        /**
         * Returns the number of campaigns
         * @return integer Campaign count
         */
        static function campaigns_number()
        {
            global $wpdb;
            $count_id = $wpdb->get_var('SELECT COUNT(id) FROM ' . self::campaigns_table() . ' WHERE id is not null;');
            return $count_id;
        }

        /**
         * Executes a command for a campaign
         * @param string $cmd Command
         * @param integer $cid Campaign Id
         */
        static function command($cmd, $cid)
        {
            $campaign = new wp_adpress_campaign($cid);
            switch ($cmd) {
                case 'activate':
                    $campaign->activate();
                    break;
                case 'deactivate':
                    $campaign->deactivate();
                    break;
                case 'remove':
                    $campaign->remove();
                    break;
            }
            $campaign->save();
        }

        /**
         * Return an array of campaigns objects filtered by status
         * @global object $wpdb
         * @param string $state Possible values are all, active and inactive
         * @return array list of wp_adpress_campaign objects
         */
        static function list_campaigns($state = 'all')
        {
            global $wpdb;
            // Select Query
            $query = 'SELECT id FROM ' . self::campaigns_table() . ';';
            $arr = array();
            $result = $wpdb->get_col($query);

            // Filtering
            if (!empty($result)) {
                for ($i = 0; $i < count($result); $i++) {
                    $campaign = new wp_adpress_campaign((int)$result[$i]);
                    switch ($state) {
                        case 'active':
                        case 'inactive':
                            if ($campaign->state() === $state) {
                                $arr[] = $campaign;
                            }
                            break;
                        case 'all':
                        default:
                            $arr[] = $campaign;
                            break;
                    }
                }
            }
            return $arr;
        }

        /**
         * Return a HTML option list of active campaigns. This function is made for the
         * plug-in widget
         *
         * @static
         * @param string $selected_campaign
         * @return string $html
         */
        static function widget_list_campaigns($selected_campaign)
        {
            $html = '';
            $active = self::list_campaigns('active');
            foreach ($active as $campaign) {
                if ((int)$selected_campaign === $campaign->id) {
                    $html .= '<option selected value="' . $campaign->id . '">';
                } else {
                    $html .= '<option value="' . $campaign->id . '">';
                }
                $html .= $campaign->settings['name'];
                $html .= '</option>';
            }
            return $html;
        }

		static function array_list_campaigns()
		{
			$html = array();
			$active = self::list_campaigns('active');
			foreach ($active as $campaign) {
				$html[ $campaign->id ] = $campaign->settings['name'];	
			}
			return $html;
		}

        /**
         * Remove all campaigns from the database
         *
         * @static
         * @return boolean $query
         */
        static function empty_campaigns()
        {
            global $wpdb;
            $query = $wpdb->query('TRUNCATE TABLE ' . self::campaigns_table() . ';');
            return $query;
        }

    }
}

