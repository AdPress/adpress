<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

/**
 * @package Views
 * @subpackage Campaigns Page
 */

if (!class_exists('wp_adpress_campaigns_view')) {
    class wp_adpress_campaigns_view
    {

        private $campaigns;
        public $count;
        public $view = '<p class="info">No campaigns</p>';

        function __construct()
        {
            // Execute Get Commands
            $this->get_commands();
            // Campaigns count
            $this->count = wp_adpress_campaigns::campaigns_number();
            // Load the campaigns
            $this->campaigns = wp_adpress_campaigns::list_campaigns();
            // Generate the view
            if (count($this->campaigns) > 0) {
                $this->generate_view();
            }
        }

        /**
         * Execute Get Commands
         */
        private function get_commands()
        {
            if (isset($_GET['cmd'])) {
                wp_adpress_campaigns::command($_GET['cmd'], (int)$_GET['cid']);
            }
        }

        /**
         * Generate the view
         */
        private function generate_view()
        {
            $this->view = '
                    <table class="wp-list-table widefat plugins" cellspacing="0">
            <thead>
                <tr>
                    <th class="id-column">' . __('Id', 'wp-adpress') . '</th>
                    <th>' . __('Campaign', 'wp-adpress') . '</th>
                    <th>' . __('Description', 'wp-adpress') . '</th>
                    <th>' . __('Get code', 'wp-adpress') . '</th>
                </tr>
            </thead>
            <tbody class="highlight_rows">
                ' . $this->generate_list_view() . '
            </tbody>
            <tfoot>
                <tr>
                    <th class="id-column">' . __('Id', 'wp-adpress') . '</th>
                    <th>' . __('Campaign', 'wp-adpress') . '</th>
                    <th>' . __('Description', 'wp-adpress') . '</th>
                    <th>' . __('Get code', 'wp-adpress') . '</th>
                </tr>
            </tfoot>
        </table>';
        }

        /**
         * Generate the List view
         */
        private function generate_list_view()
        {
            $html = '';
            foreach ($this->campaigns as $campaign) {
                $html .= '
                <tr class="' . $campaign->state() . '">
                     <th scope="row" class="id-column">' . $campaign->id . '</th>
                        <td class="plugin-title" style="width:340px;">
                            <strong>' . $campaign->settings['name'] . '</strong>
                            <div class="row-actions-visible">
                                <span><a href="#" class="more" id="c' . $campaign->id . '">' . __('More', 'wp-adpress') . '</a> | </span>
                                <span>' . $this->activate_button($campaign) . ' | </span>
                                <span>' . $this->edit_button($campaign) . ' | </span>
                                <span class="remove_tab">' . $this->remove_button($campaign) . ' </span>
                            </div>
                        </td>
                        <td class="column-description desc">
                            <div class="plugin-description">
                                <p>' . $campaign->settings['description'] . '</p>
                            </div>
                        </td>
                        <td>
                        <a href="#" class="button-primary blue more" tog_text="PHP Code" id="phpcode' . $campaign->id . '">PHP Code</a> <a href="#" class="button-primary sea more" tog_text="Short Code" id="shortcode' . $campaign->id . '">Short Code</a>
                        </td>
                    </tr>
                    <tr class="expand c' . $campaign->id . '" >
                        <th></th>
                        <td style="padding:0;">
                            ' . $this->more_info($campaign) . '
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="expand phpcode' . $campaign->id . '" >
                        <th></th>
                        <td style="padding:0;">
                            <h5 style="margin: 5px 7px;">PHP Code</h5>
                            <p class="dis-code">
                            ' . $this->php_code($campaign->id) . '
                            </p>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="expand shortcode' . $campaign->id . '" >
                        <th></th>
                        <td style="padding:0;">
                            <h5 style="margin: 5px 7px;">Short Code</h5>
                            <p class="dis-code">
                            ' . $this->short_code($campaign->id) . '
                            </p>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>';
            }
            return $html;
        }

        /**
         * Activate Button view
         */
        private function activate_button($campaign)
        {
            if ($campaign->state() === 'active' && $campaign->is_editable()) {
                $html = '<a href="admin.php?page=adpress-campaigns&cmd=deactivate&cid=' . $campaign->id . '">' . __('Deactivate', 'wp-adpress') . '</a>';
            } else if ($campaign->state() === 'inactive') {
                $html = '<a href="admin.php?page=adpress-campaigns&cmd=activate&cid=' . $campaign->id . '">' . __('Activate', 'wp-adpress') . '</a>';
            } else {
                $html = __('Deactivate', 'wp-adpress');
            }
            return $html;
        }

        /**
         * Edit Button view
         */
        private function edit_button($campaign)
        {
            if ($campaign->is_editable()) {
                $html = '<a href="admin.php?page=adpress-inccampaign&cmd=edit&cid=' . $campaign->id . '">' . __('Edit', 'wp-adpress') . '</a>';
            } else {
                $html = __('Edit', 'wp-adpress');
            }
            return $html;
        }

        /**
         * Remove button view
         */
        private function remove_button($campaign)
        {
            if ($campaign->is_editable()) {
                $html = '<a href="#" class="remove_button">' . __('Remove', 'wp-adpress') . '</a><span class="remove_confirm">' . __('Are you sure?', 'wp-adpress') . ' <a href="admin.php?page=adpress-campaigns&cmd=remove&cid=' . $campaign->id . '">' . __('Yes', 'wp-adpress') . '</a> | <a href="#" class="remove_cancel">' . __('No', 'wp-adpress') . '</a></span>';
            } else {
                $html = __('Remove', 'wp-adpress');
            }
            return $html;
        }

        /**
         * More Info view
         */
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

        private function php_code($id)
        {
            $html = '&lt;?php <br />
            <span class="push_1"><span class="if">if</span> (function_exists(display_campaign)) {</span>
            <br />
            <span class="push_2">display_campaign(<span class="number">'.$id.'</span>);</span>
            <br />
            <span class="push_1">}</span>
            </br >
            ?&gt;';
            return $html;
        }

        private function short_code($id)
        {
            $html = '[adpress campaign=<span class="number">'.$id.'</span>]';
            return $html;
        }

    }
}

?>
