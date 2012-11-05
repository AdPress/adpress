<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

/**
 * @package Views
 * @subpackage Client Purchases Page
 */

if (!class_exists('wp_adpress_purchases')) {
    class wp_adpress_purchases
    {

        private $user_id;
        private $ads;
        public $count;
        public $ads_view;
        public $empty = true;
        private $mu = false;

        function __construct($mu = false)
        {
            if ($mu) {
                $this->mu = true;
            }
            // Execute Get Commands
            $this->get_commands();
            // Get the current User ID
            $this->user_id = get_current_user_id();
            // Get the user Adlist
            $this->ads = wp_adpress_ads::list_user_ads($this->user_id);
            // Get the ads count
            $this->count = count($this->ads);
            // Generate HTML code if
            if ($this->count > 0) {
                $this->generate_view();
                $this->empty = false;
            }
        }

        /**
         * Execute Get Commands
         */
        private function get_commands()
        {
            if (isset($_GET['blogid'])) {
                switch_to_blog($_GET['blogid']);
            }
            if (isset($_GET['cmd'])) {
                wp_adpress_ads::command($_GET['cmd'], (int)$_GET['id']);
            }
            if (isset($_GET['blogid'])) {
                restore_current_blog();
            }
        }

        /**
         * Generate the view
         */
        private function generate_view()
        {
            $this->ads_view = '
                ' . $this->ads_list_view() . '
            ';
        }

        private function ads_list_view()
        {
            $settings = get_option('adpress_settings');
            $html = '';
            $i = 1;
            foreach ($this->ads as $ad) {
                $campaign = new wp_adpress_campaign($ad->campaign_id);
                $html .= '
                <tr class="active">
                    <th scope="row" class="id-column">' . $i . '</th>';
                if ($this->mu) {
                    global $blog_id;
                    $blog = new wp_adpress_mu($blog_id);
                    $html .= '<td class="plugin-title">' . $blog->blogname . '</td>';
                } else {
                    $blog_id = '';
                }
                $html .= '<td class="plugin-title">' . $campaign->settings['name'] . '</td>
                    <td class="plugin-title">' . $ad->status . '</td>
                    <td class="plugin-title">' . date($settings['time_format'], $ad->time) . '</td>
                    <td class="plugin-title buttons">
                        ' . $this->stats_button($ad->id) . ' <a href="#" class="button-secondary more" id="cc' . $ad->id . $blog_id . '" class="button-secondary" tog_text="Cancel">' . __('Cancel', 'wp-adpress') . '</a>
                    </td>
                </tr>
                <tr class="expand cc' . $ad->id . $blog_id . '">
                        <th></th>
                        <td></td>';
                if ($this->mu) {
                    $html .= '<td></td>';
                }
                $html .= '<td></td><td></td>
                        <td class="buttons">' . __('Are you sure?', 'wp-adpress') . $this->cancel_button($ad->id) .
                        '<a href="#" class="button-secondary less" id="cc' . $ad->id . $blog_id . '" class="button-secondary">' . __('No', 'wp-adpress') . '</a></td>
                </tr>';
                $i++;
            }
            return $html;
        }

        private function stats_button($id)
        {
            global $blog_id;
            if ($this->mu) {
                $html = '<a href="admin.php?page=adpress-ad&id=' . $id . '&blogid=' . $blog_id . '" class="button-secondary">' . __('Stats', 'wp-adpress') . '</a>';
            } else {
                $html = '<a href="admin.php?page=adpress-ad&id=' . $id . '" class="button-secondary">' . __('Stats', 'wp-adpress') . '</a>';
            }
            return $html;
        }

        private function cancel_button($id)
        {
            global $blog_id;
            if ($this->mu) {
                $html = '  <a href="admin.php?page=adpress-purchases&cmd=cancel&id=' . $id . '&blogid='.$blog_id.'" class="button-secondary">' . __('Yes', 'wp-adpress') . '</a>  ';
            } else {
                $html = '  <a href="admin.php?page=adpress-purchases&cmd=cancel&id=' . $id . '" class="button-secondary">' . __('Yes', 'wp-adpress') . '</a>  ';
            }
            return $html;
        }

    }
}

?>
