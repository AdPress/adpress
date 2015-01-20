<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

if (!class_exists('wp_adpress_history')) {
    /**
     * History Manager
     *
     * @package Includes
     * @subpackage Settings
     */
    class wp_adpress_history
    {

        /**
         * History record id
         * @var integer
         */
        public $id;

        /**
         * Ad Id
         * @var integer
         */
        public $ad_id;

        /**
         * Campaign object
         * @var object
         */
        public $campaign;

        /**
         * Ad Object
         * @var object
         */
        public $ad;

        /**
         * Ad Approval time
         * @var timestamp
         */
        public $approved_at;

        /**
         * Ad Expiry time
         * @var timestamp
         */
        public $expired_at;

        /**
         * Create a new Ad History object
         * @param integer $id
         */
        function __construct($id)
        {
            $this->populate_object($this->query_table($id));
        }

        /**
         * Query the history table
         * @param integer $id
         * @return array
         */
        private function query_table($id)
        {
            global $wpdb;
            $query = 'SELECT * FROM ' . self::history_table() . ';';
            $arr = $wpdb->get_row($query, ARRAY_A, $id - 1);
            return $arr;
        }

        /**
         * Populate the History Object from the query
         * @param array $query
         */
        private function populate_object($query)
        {
            $settings = get_option('adpress_settings');
            $time_format = $settings['time_format'];
            $this->id = $query['id'];
            $this->ad_id = $query['ad_id'];
            $this->ad = unserialize($query['ad_info']);
            $this->campaign = unserialize($query['campaign_info']);
            $this->approved_at = date($time_format, $query['approved_at']);
            $this->expired_at = date($time_format, $query['expired_at']);
        }
       //TODO seperate these functions to a histories table
        /**
         * Records the history of an Ad
         * @global object $wpdb
         * @param object $ad
         */
        static function record_history($ad)
        {
            global $wpdb;
            $campaign = new wp_adpress_campaign($ad->campaign_id);
            $record = array(
                'id' => self::new_history_id(),
                'ad_id' => $ad->id,
                'approved_at' => $ad->time,
                'expired_at' => time(),
                'ad_info' => serialize($ad),
                'campaign_info' => serialize($campaign)
            );
            $format = array(
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s'
            );
            $wpdb->insert(self::history_table(), $record, $format);
        }


        static function empty_history()
        {
            global $wpdb;
            $query = $wpdb->query('TRUNCATE TABLE ' . self::history_table() . ';');
            return $query;
        }

        /**
         * Return a new history id
         * @global object $wpdb
         * @return integer
         */
        static function new_history_id()
        {
            global $wpdb;
            $max_id = $wpdb->get_var('SELECT MAX(id) FROM ' . self::history_table() . ' WHERE id is not null;');
            if ($max_id === NULL) {
                return 1;
            }
            $new_id = (int)$max_id + 1;
            return $new_id;
        }


        /**
         * Returns the name of the history table
         * @return string
         */
        static function history_table()
        {
            global $wpdb;
            return $wpdb->prefix . 'adpress_history';
        }

        static function list_history()
        {
            global $wpdb;
            // Query
            $arr = array();
            $query = 'SELECT id FROM ' . self::history_table() . ';';
            $result = $wpdb->get_col($query);
            // History Objects
            for ($i = 0; $i < count($result); $i++) {
                $arr[] = new wp_adpress_history((int)$result[$i]);
            }
            // Return the array
            return $arr;
        }

        /// TODO: Remove the view from the history class
        /**
         * Generate the history view
         */
        static function generate_view()
        {
            echo '
        <div class="wrap" id="adpress">
    <div id="adpress-icon-history" class="icon32"><br></div>
    <h2>Purchase History</h2>
    <div id="campaings-table">
        <div class="tablenav top">
            <div class="tablenav-pages">
                <div class="displaying-num">
                   
                </div>    
            </div>
            <br class="clear" />
        </div>
        <table class="wp-list-table widefat plugins" cellspacing="0">
            <thead>
                <tr>
                    <th class="id-column">' . __('Id', 'wp-adpress') . '</th>
                    <th>' . __('Ad', 'wp-adpress') . '</th>
                    <th>' . __('Campaign', 'wp-adpress') . '</th>
                    <th>' . __('Approved at', 'wp-adpress') . '</th>
                    <th>' . __('Expired at', 'wp-adpress') . '</th>
                    <th>' . __('Buyer', 'wp-adpress') . '</th>
                    <th>' . __('Actions', 'wp-adpress') . '</th>
                </tr>
            </thead>
            <tbody>
                ' . self::table_loop() . '
            </tbody>
            <tfoot>
                <tr>
                    <th class="id-column">' . __('Id', 'wp-adpress') . '</th>
                    <th>' . __('Ad', 'wp-adpress') . '</th>
                    <th>' . __('Campaign', 'wp-adpress') . '</th>
                    <th>' . __('Approved at', 'wp-adpress') . '</th>
                    <th>' . __('Expired at', 'wp-adpress') . '</th>
                    <th>' . __('Buyer', 'wp-adpress') . '</th>
                    <th>' . __('Action', 'wp-adpress') . '</th>
                </tr>
            </tfoot>
        </table>       
    </div>    
</div>    
        ';
        }

        static function table_loop()
        {
            // TODO: Replace the loop with history list function
            $html = '';
            $rows = self::new_history_id();
            for ($i = 1; $i < $rows; $i++) {
                $history = new wp_adpress_history($i);
if ( $history->ad->user_id == 0 ) {
					$user_html = 'Guest';
				} else {
					$user_info = get_userdata($history->ad->user_id);
					$user_html = '<a href="user-edit.php?user_id=' . $user_info->ID . '">' . $user_info->user_login . '</a>';
				}
                $html .= '<tr class="active">';
                $html .= '<th scope="row" class="check-column" style="padding: 3px 7px !important;">' . $history->id . '</th>';
                $html .= '<th scope="row" class="check-column" >' . $history->ad_id . '</th>';
                $html .= '<td class="plugin-title">' . $history->campaign->settings['name'] . '</td>';
                $html .= '<td class="plugin-title">' . $history->approved_at . '</td>';
                $html .= '<td class="plugin-title">' . $history->expired_at . '</td>';
                $html .= '<td class="plugin-title">' . $user_html . '</td>';
                $html .= '<td class="plugin-title buttons"><a href="#" class="button-secondary more" id="c' . $history->id . '">' . __('More', 'wp-adpress') . '</a></td>';
                $html .= '</tr>';
                $html .= '<tr class="expand c' . $history->id . '">';
                $html .= '<th></th><th></th>';
                $html .= '<td>' . self::more_info($history) . '</td>';
                $html .= '<td></td><td></td>';
                $html .= '<td></td><td></td>';
                $html .= '</tr>';
            }
            return $html;
        }

        static function more_info($history)
        {
            $campaign = $history->campaign;
            $ad = $history->ad;
            global $wpadpress;

            /* First Table */
            $html = '<table class="campaign_info info-table"><tbody>';
            if ($campaign->ad_definition['type'] === 'image') {
                $html .= '<tr><td class="title">' . __('Ad Type', 'wp-adpress') . '</td><td>' . __('Image', 'wp-adpress') . '</td></tr>';
                $html .= '<tr><td class="title">' . __('Image Size', 'wp-adpress') . '</td><td>' . $campaign->ad_definition['size']['width'] . ' X ' . $campaign->ad_definition['size']['height'] . '</td></tr>';
            } else if ($campaign->ad_definition['type'] === 'link') {
                $html .= '<td class="title">' . __('Ad Type', 'wp-adpress') . '</td><td>' . __('Link', 'wp-adpress') . '</td>';
                $html .= '<tr><td class="title">' . __('Max. Link length', 'wp-adpress') . '</td><td>' . $campaign->ad_definition['length'] . '</td></tr>';
            }
            $html .= '</tbody></table>';

            /* Second Table */
            $html .= '<table class="campaign_info info-table"><tbody>';
            switch ($campaign->ad_definition['contract']) {
                case 'clicks':
                    $html .= '<tr><td class="title">' . __('Clicks', 'wp-adpress') . '</td><td>' . $campaign->ad_definition['clicks'] . '</td></tr>';
                    break;
                case 'pageviews':
                    $html .= '<tr><td class="title">' . __('Pageviews', 'wp-adpress') . '</td><td>' . $campaign->ad_definition['pageviews'] . '</td></tr>';
                    break;
                case 'duration':
                    $html .= '<tr><td class="title">' . __('Duration', 'wp-adpress') . '</td><td>' . $campaign->ad_definition['duration'] . ' ' . __('days', 'wp-adpress') . '</td></tr>';
                    break;
            }
            $html .= '<tr><td class="title">' . __('Price', 'wp-adpress') . '</td><td>' . $campaign->ad_definition['price'] . ' ' . $wpadpress->settings['currency'] . '</td></tr>';
            $html .= '<tbody></table>';

            $html .= '<div style="clear:both;"></div>';
            return $html;
        }

    }
}

