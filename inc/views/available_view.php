<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

/**
 * @package Views
 * @subpackage Client Available Page
 */

if (!class_exists('wp_adpress_available')) {
    class wp_adpress_available
    {

        private $campaigns;
        public $count;
        public $view = '<p class="info">No active Campaigns</p>';
        private $mu;

        function __construct($mu = false)
        {
            if ($mu) {
                $this->mu = true;
            }
            $this->campaigns = wp_adpress_campaigns::list_campaigns('active');
            $this->count = count($this->campaigns);
            if ($this->count > 0) {
                $this->generate_view();
            }
        }

        private function generate_view()
        {
            $this->view = '';
            foreach ($this->campaigns as $campaign) {
                $available_campaigns = count($campaign->list_ads('available'));
                $this->view .= '
      <div class="campaign c-block">          
        <div class="header c-head">
            <h3>' . $campaign->settings['name'] . '</h3>
        </div>        
        <p class="con">
             ' . $campaign->settings['description'] . '
        </p>' . $this->more_info($campaign);
                if ($available_campaigns > 0) {
                    $this->view .= '
        <p class="info available">
            ' . _n(' There is 1 available Ad spot', ' There are ' . $available_campaigns . ' available Ad spots', $available_campaigns, 'wp-adpress') . $this->purchase_button($campaign->id) .
        '</p>';
                } else {
                    $this->view .= '
        <p class="info notavailable">
            ' . __('There are no available spots for this campaign.', 'wp-adpress') . '
        </p>';
                }
                $this->view .= '</div>';
            }
        }

        private function purchase_button($campaign_id)
        {
            if ($this->mu) {
                global $blog_id;
                $html = '<a href="admin.php?page=adpress-ad_purchase&cid=' . $campaign_id . '&blogid='.$blog_id.'" class="button-primary" style="float:right;margin-right:48px;margin-top:-2px;">' . __('Purchase', 'wp-adpress') . '</a>';
            } else {
                $html = '<a href="admin.php?page=adpress-ad_purchase&cid=' . $campaign_id . '" class="button-primary" style="float:right;margin-right:48px;margin-top:-2px;">' . __('Purchase', 'wp-adpress') . '</a>';
            }
            return $html;

        }

        private function more_info($campaign)
        {
            global $wpadpress;

            /* First Table */
            $html = '<table class="campaign_info info-table"><tbody>';
            if ($campaign->ad_definition['type'] === 'image') {
                $html .= '<tr><td class="title">' . __('Ad Type', 'wp-adpress') . '</td><td>' . __('Image', 'wp-adpress') . '</td></tr>';
                $html .= '<tr><td class="title">' . __('Image Size', 'wp-adpress') . '</td><td>' . $campaign->ad_definition['size']['width'] . ' X ' . $campaign->ad_definition['size']['height'] . '</td></tr>';
                $html .= '<tr><td class="title">' . __('Columns number', 'wp-adpress') . '</td><td>' . $campaign->ad_definition['columns'] . '</td></tr>';
            } else if ($campaign->ad_definition['type'] === 'link') {
                $html .= '<td class="title">' . __('Ad Type', 'wp-adpress') . '</td><td>' . __('Link', 'wp-adpress') . '</td>';
                $html .= '<tr><td class="title">' . __('Max. Link length', 'wp-adpress') . '</td><td>' . $campaign->ad_definition['length'] . '</td></tr>';
            }
            $html .= '</tbody></table>';

            /* Second Table */
            $html .= '<table class="campaign_info info-table"><tbody>';
            $html .= '<tr><td class="title">' . __('Ads number', 'wp-adpress') . '</td><td>' . $campaign->ad_definition['number'] . '</td></tr>';
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

