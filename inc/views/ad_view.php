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
            /*
             * Check Multisite
             */
            /*
            * Display the Stats page
            */
            $this->generate_view();
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

        private function generate_view()
        {
            $fore_front = '
            <h2>' . __('Last Month Stats', 'wp-adpress') . '</h2>
<div id="fore-front">
    <div id="ex-chart" class="c-block">
        <div class="c-head">
           <h3 style="float:left;">' . __('Stats Chart', 'wp-adpress') . '</h3>
           <ul class="toolbar">
            <li><a href="#" class="button-secondary" id="display_all">All</a></li>
            <li><a href="#" class="button-secondary" id="display_month">Month</a></li>
            <li><a href="#" class="button-secondary" id="display_week">Week</a></li>
           </ul>
           <ul class="toolbar" style="float:right; padding-right: 5px;">
            <li><a href="#" class="button-secondary" id="toggle_hits">Hits</a></li>
            <li><a href="#" class="button-secondary" id="toggle_views">Views</a></li>
           </ul>
        </div>
        <div id="chart-placeholder" style="height:260px; width: 680px;margin-left:10px;margin-top:0px;">
            
        </div>
    </div>
    <div id="avg" class="c-block">
        <div class="c-head"><h3>' . __('Averages', 'wp-adpress') . '</h3></div>
        <ul>
            <li><strong>' . __('Views', 'wp-adpress') . '</strong> ' . $this->ad->avg('views') . '</li>
            <li><strong>' . __('Clicks', 'wp-adpress') . '</strong> ' . $this->ad->avg('hits') . '</li>
            <li><strong>' . __('CTR', 'wp-adpress') . '</strong> ' . $this->ad->avg('ctr') . '</li>
        </ul>
    </div>
    <div style="clear:both"></div>
</div>
';
            $stats_table = $this->generate_stats_table();

            $this->view = $fore_front . $stats_table;
        }

        private function generate_stats_table()
        {
            $html = '<h2>' . __('Complete Stats', 'wp-adpress') . '</h2><table id="stats-table" class="wp-list-table widefat plugins" cellspacing="0">';
            $html .= '<caption>' . __('Last Month Stats', 'wp-adpress') . '</caption>';
            $html .= '<thead>';
            $html .= '<tr><th>' . __('Day', 'wp-adpress') . '</th><th>' . __('Views', 'wp-adpress') . '</th><th>' . __('Hits', 'wp-adpress') . '</th><th>' . __('CTR', 'wp-adpress') . '</th></tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            $i = 1;
            foreach ($this->ad->stats['views'] as $date => $views) {
                if (!isset($views)) {
                    $views = 0;
                }
                if (!isset($this->ad->stats['hits'][$date])) {
                    $this->ad->stats['hits'][$date] = 0;
                }
                $html .= '<tr>';
                $html .= '<td>' . date('Y-m-d', strtotime($date)) . '</td>';
                $html .= '<td>' . $views . '</td>';
                $html .= '<td>' . $this->ad->stats['hits'][$date] . '</td>';
                $html .= '<td>' . $this->c_avg($this->ad->stats['hits'][$date], $views) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '<thead>';
            $html .= '<tr><th>Total</th><th>' . $this->ad->total_views() . '</th><th>' . $this->ad->total_hits() . '</th><th>' . $this->ad->avg('ctr') . '</th></tr>';
            $html .= '</thead>';
            $html .= '</table>';
            return $html;
        }

        private function c_avg($hits, $views)
        {
            if ($hits && $hits != 0) {
                return number_format(($hits / $views) * 100, 2) . '%';
            }
            return '0%';
        }

    }
}

