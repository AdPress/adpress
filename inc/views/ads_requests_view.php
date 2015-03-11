<?php
// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

/**
 * @package Views
 * @subpackage Ads Requests Page
 */

if (!class_exists('wp_adpress_ads_requests')) {
	/**
	 * Ads Requests Table
	 */
	class wp_adpress_ads_requests
	{

		private $ads;
		public $count;
		public $view = '
<p class="info">
No Requests
</p>';
		private $mu;

		function __construct($mu = false)
		{
			// MU Interface
			if ($mu) {
				$this->mu = true;
			}
			// Execute commands
			$this->execute_commands();
			// Get the Ads list
			$this->ads = wp_adpress_ads::list_ads('waiting', 'object');
			// Get the Ads count
			$this->count = count($this->ads);
			if ($this->count > 0) {
				$this->generate_view();
			}

			// Check for possible Commands
		}

		/**
		 * Execute Get Commands
		 */
		private function execute_commands()
		{
			if (isset($_GET['cmd'])) {
				wp_adpress_ads::command($_GET['cmd'], (int)$_GET['id']);
			}
		}

		private function generate_view()
		{
			$this->view = '
	   <table class="wp-list-table widefat plugins withbuttons" cellspacing="0">
			<thead>
				<tr>
					<th class="id-column">' . __('Id', 'wp-adpress') . '</th>
					<th>' . __('Campaign', 'wp-adpress') . '</th>
					<th>' . __('Client', 'wp-adpress') . '</th>
					<th>' . __('Requested at', 'wp-adpress') . '</th>
					<th>' . __('Action', 'wp-adpress') . '</th>
				</tr>
			</thead>
			<tbody>
				' . $this->generate_ads_list() . '
			</tbody>
			</table>';
		}

		private function generate_ads_list()
		{
			$settings = get_option('adpress_settings');
			$html = '';
			foreach ($this->ads as $ad) {
				$campaign = new wp_adpress_campaign($ad->campaign_id);

				if ( $ad->user_id == 0 ) {
					$user_html = 'Guest';
				} else {
					$user_info = get_userdata($ad->user_id);
					$user_html = '<a href="user-edit.php?user_id=' . $user_info->ID . '">' . $user_info->user_login . '</a>';
				}

				$html .= '
			   <tr class="active">
					<th scope="row" class="id-column">' . $ad->id . '</th>
					<td class="plugin-title">' . $campaign->settings['name'] . '</td>
					<td class="plugin-title">' . $user_html . '</td>
					<td class="plugin-title">' . date($settings['time_format'], (int)$ad->time) . '</td>
					<td class="plugin-title buttons">
						<a href="#" class="button-secondary more" id="ac' . $ad->id . '" class="button-secondary" tog_text="Approve">' . __('Approve', 'wp-adpress') . '</a>
						<a href="#" class="button-secondary more" id="rc' . $ad->id . '" class="button-secondary" tog_text="Reject">' . __('Reject', 'wp-adpress') . '</a>
						<a href="#" class="button-secondary more" id="c' . $ad->id . '">' . __('More', 'wp-adpress') . '</a>
					</td>
				</tr>
				<tr class="expand c' . $ad->id . '">
						<th></th>
						<td>' . self::more_info($ad, $campaign) . '</td>
						<td>' . self::transaction_info($ad) . '</td>
							<td></td>
						<td></td>
					</tr>
					<tr class="expand ac' . $ad->id . '">
						<th></th>
						<td>

						</td>
						<td></td><td></td>
						<td class="buttons">' . __( 'Are you sure? ', 'wp-adpress' ) . $this->approve_button($ad->id) . '
						<a href="#" class="button-secondary less" id="ac' . $ad->id . '" class="button-secondary">' . __('No', 'wp-adpress') . '</a></td>
					</tr>
					<tr class="expand rc' . $ad->id . '">
						<th></th>
						<td>

						</td>
						<td></td><td></td>
						<td class="buttons">' . __( 'Are you sure? ', 'wp-adpress' ) . $this->reject_button($ad->id) . '
						<a href="#" class="button-secondary less" id="rc' . $ad->id . '" class="button-secondary">' . __('No', 'wp-adpress') . '</a></td>
						</tr>';
			}
			return $html;
		}

		private function approve_button($id)
		{
			if ($this->mu) {
				global $blog_id;
				$html = '<a href="admin.php?page=adpress-mu-requests&cmd=approve&id=' . $id . '&blogid=' . $blog_id . '" class="button-secondary">' . __('Yes', 'wp-adpress') . '</a>';
			} else {
				$html = '<a href="admin.php?page=adpress-adsrequests&cmd=approve&id=' . $id . '" class="button-secondary">' . __('Yes', 'wp-adpress') . '</a>';
			}

			return $html;
		}

		private function reject_button($id)
		{
			if ($this->mu) {
				global $blog_id;
				$html = '<a href="admin.php?page=adpress-mu-requests&cmd=reject&id=' . $id . '&blogid=' . $blog_id . '" class="button-secondary">' . __('Yes', 'wp-adpress') . '</a>';
			} else {
				$html = '<a href="admin.php?page=adpress-adsrequests&cmd=reject&id=' . $id . '" class="button-secondary">' . __('Yes', 'wp-adpress') . '</a>';
			}
			return $html;
		}

		static function more_info($ad, $campaign)
		{
			$html = '<table class="campaign_info info-table"><tbody>';
			switch ($campaign->ad_definition['type']) {
			case 'image':
				$html .= '<tr><td class="title">' . __('Image', 'wp-adpress') . '</td><td>';
				$html .= '<img src="' . $ad->param['destination_val'] . '"></td></tr>';
				$html .= '<tr><td class="title">' . __('URL', 'wp-adpress') . '</td><td><a href="' . $ad->param['destination_url'] . '">' . $ad->param['destination_url'] . '</a></td></tr>';
				break;
			case 'flash':
				$html .= '<tr><td class="title">' . __('Banner', 'wp-adpress') . '</td></tr>';
				$html .= '<embed src="' . $ad->param['destination_val'] . '"></td></tr>';
				$html .= '<tr><td class="title">' . __('URL', 'wp-adpress') . '</td><td><a href="' . $ad->param['destination_url'] . '">' . $ad->param['destination_url'] . '</a></td></tr>';
				break;
			case 'link':
				$html .= '<tr><td class="title">' . __('Link Text', 'wp-adpress') . '</td><td>' . $ad->param['destination_val'] . '</td></tr>';
				$html .= '<tr><td class="title">' . __('URL', 'wp-adpress') . '</td><td><a href="' . $ad->param['destination_url'] . '">' . $ad->param['destination_url'] . '</a></td></tr>';
				break;
			}
			if (isset($ad->param['client_message'])) {
				$html .= '<tr><td class="title">' . __('Message', 'wp-adpress') . '</td><td>' . $ad->param['client_message'] . '</p></td></tr>';
			}
			$html .= '</tbody></table>';
			return $html;
		}

		static function transaction_info($ad)
		{
			$html = '';
			if (isset($ad->param['paypal'])) {
				$html .= '<table class="campaign_info info-table"><tbody>';
				$html .= '<tr><td class="title">Transaction ID</td>';
				$html .= '<td>' . $ad->param['paypal']['PAYMENTINFO_0_TRANSACTIONID'] . '</td></tr>';
				$html .= '<tr><td class="title">Order Time</td>';
				$html .= '<td>' . date('Y-m-d H:i', strtotime($ad->param['paypal']['PAYMENTINFO_0_ORDERTIME'])) . '</td></tr>';
				$html .= '<tr><td class="title">Amount</td>';
				$html .= '<td>' . $ad->param['paypal']['PAYMENTINFO_0_AMT'] . ' ' . $ad->param['paypal']['PAYMENTINFO_0_CURRENCYCODE'] . '</td></tr>';
				$html .= '</tbody></table>';
			}
			return $html;
		}

	}

	/**
	 * Running Ads Table
	 */
	class wp_adpress_ads_running
	{

		private $ads;
		public $count;
		public $view = '
<p class="info">        
No Ads Running
</p>';
		private $mu;

		function __construct($mu = false)
		{
			// MU Interface
			if ($mu) {
				$this->mu = true;
			}
			// Get the Ads list
			$this->ads = wp_adpress_ads::list_ads('running', 'object');
			// Get the Ads count
			$this->count = count($this->ads);
			if ($this->count > 0) {
				$this->generate_view();
			}
		}

		private function generate_view()
		{
			$this->view = '
	  <table class="wp-list-table widefat plugins withbuttons" cellspacing="0">
			<thead>
				<tr>
					<th class="id-column">' . __('Id', 'wp-adpress') . '</th>
					<th>' . __('Campaign', 'wp-adpress') . '</th>
					<th>' . __('Client', 'wp-adpress') . '</th>
					<th>' . __('Aprroved at', 'wp-adpress') . '</th>
					<th>' . __('Expire', 'wp-adpress') . '</th>
					<th>' . __('Action', 'wp-adpress') . '</th>
				</tr>
			</thead>
			<tbody>
				' . $this->generate_ads_list() . '
			</tbody>
			</table>';
		}

		private function generate_ads_list()
		{
			$settings = get_option('adpress_settings');
			$html = '';
			foreach ($this->ads as $ad) {
				$campaign = new wp_adpress_campaign($ad->campaign_id);
				$user_info = get_userdata($ad->user_id);
				if ( $ad->user_id == 0 ) {
					$user_html = 'Guest';
				} else {
					$user_info = get_userdata($ad->user_id);
					$user_html = '<a href="user-edit.php?user_id=' . $user_info->ID . '">' . $user_info->user_login . '</a>';
				}
				$html .= '
				<tr class="active">
					<th scope="row" class="id-column">' . $ad->id . '</th>
					<td class="plugin-title">' . $campaign->settings['name'] . '</td>
					<td class="plugin-title">' . $user_html . '</td>
					<td class="plugin-title">' . date($settings['time_format'], (int)$ad->time) . '</td>
					<td class="plugin-title">' . $this->expire_info($ad, $campaign) . '</td>
					<td class="plugin-title buttons">
						' . $this->buy_button($ad->id) . ' <a href="#" id="cancel' . $ad->id . '"  class="button-secondary more" tog_text="Cancel">Cancel</a> <a href="#" class="button-secondary more" id="c' . $ad->id . '">' . __('More', 'wp-adpress') . '</a>
					</td>
				</tr>
				<tr class="expand c' . $ad->id . '">
						<th></th>
						<td>' . wp_adpress_ads_requests::more_info($ad, $campaign) . '</td>
							<td></td>
						<td></td>
				</tr>
				<tr class="expand cancel' . $ad->id . '">
					<th></th>
					<td></td><td></td>
					<td></td><td></td>
					<td class="buttons">' . __('Are you sure?', 'wp-adpress') . $this->cancel_button($ad->id) . '
					<a href="#" class="button-secondary less" id="cancel' . $ad->id . '" class="button-secondary">' . __('No', 'wp-adpress') . '</a></td>
					</tr>';
			}
			return $html;
		}

		private function buy_button($id)
		{
			if ($this->mu) {
				global $blog_id;
				$html = '<a href="admin.php?page=adpress-mu-ad&id=' . $id . '&blogid=' . $blog_id . '" class="button-secondary">' . __('Stats', 'wp-adpress') . '</a>';
			} else {
				$html = '<a href="admin.php?page=adpress-ad&id=' . $id . '" class="button-secondary">' . __('Stats', 'wp-adpress') . '</a>';
			}
			return $html;
		}

		private function cancel_button($id)
		{
			if ($this->mu) {
				global $blog_id;
				$html = ' <a href="admin.php?page=adpress-mu-running&cmd=cancel&id=' . $id . '&blogid=' . $blog_id . '" class="button-secondary">' . __('Yes', 'wp-adpress') . '</a>';
			} else {
				$html = ' <a href="admin.php?page=adpress-adsrequests&cmd=cancel&id=' . $id . '" class="button-secondary">' . __('Yes', 'wp-adpress') . '</a>';
			}

			return $html;
		}

		private function expire_info($ad, $campaign)
		{
			$rm = '';
			switch ($campaign->ad_definition['contract']) {
			case 'clicks':
				// Calc. remaining clicks
				$rm = $campaign->ad_definition['clicks'] - $ad->total_hits();
				$rm = $rm . ' clicks';
				break;
			case 'pageviews':
				// Calc. remaining pageviews
				$rm = $campaign->ad_definition['pageviews'] - $ad->total_views();
				$rm = $rm . ' views';
				break;
			case 'duration':
				// Calc. remaining duration
				$rm = $campaign->ad_definition['duration'] - ceil((time() - $ad->time) / (60 * 60 * 24));
				$rm = $rm . ' days';
				break;
			}
			return $rm;
		}

	}
}
