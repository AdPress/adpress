<?php
// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

if (!class_exists('wp_adpress_adpurchase')) {
	/**
	 * @package Views
	 * @subpackage Ad Purchase Page
	 */
	class wp_adpress_adpurchase
	{

		private $campaign;
		private $html;
		private $cid;

		function __construct($cid)
		{
			$this->cid = $cid;
			$this->campaign = new wp_adpress_campaign($cid);
			/* Step 1: Check that there is an available campaign */
			if (!$this->is_available()) {
				$this->html = '
					<div id="adpress" class="wrap" style="width:600px">
					<div id="adpress-icon-purchase" class="icon32"><br></div>
					<h2>' . __( 'Purchase Ad', 'wp-adpress' ) .'</h2>
					<div class="c-block">
					<div class="c-head">
					<h3>' . __('No Ads available', 'wp-adpress') . '</h3>
					</div>
					<p>
					' . __('No Ad are available for the moment', 'wp-adpress') . '
					</p>
					</div>
					</div>
					';
				return false;
			}
			/* Step 2: Render the HTML */
			$this->render_html();
		}

		private function is_available()
		{
			if (count($this->campaign->list_ads('available')) > 0) {
				return true;
			}
			return false;
		}

		private function render_html()
		{	
				$this->html = wp_adpress_checkout_form( $this->cid );
		}

		private function render_purchase_button()
		{
			$settings = get_option('adpress_settings');
			$paypal = isset($settings['paypal']);
			if ($paypal) {
				$html = '<input type="submit" name="submit_purchase" id="submit_purchase" class="button-primary" value="' . __('Purchase with PayPal', 'wp-adpress') . '"/>';
			} else {
				$html = '<input type="submit" name="submit_purchase" id="submit_purchase" class="button-primary" value="' . __('Purchase', 'wp-adpress') . '"/>';
			}
			return $html;
		}

		private function render_client_message()
		{
			$settings = get_option('adpress_settings');
			$message = isset($settings['client_message']);
			if ($message) {
				$html = '
					<div class="c-block">
					<div class="c-head">
					<h3>' . __('Message', 'wp-adpress') . '</h3>
					</div>
					<table class="form-table">
					<tbody>
					<tr>
					<th scope="row">
					<label for="client_message">' . __('Put a message to the Admin', 'wp-adpress') . '</label>
					</th>
					<td>
					<textarea id="client_message" name="client_message"></textarea>
					</td>
					</tr>
					</tbody>
					</table>
					</div>';
				return $html;
			}
		}

		private function render_ad_details()
		{
			global $wpadpress;
			$campaign = $this->campaign;
			$html = ' <div class="c-block"><div class="c-head"><h3>' . __('Ad Spot Details', 'wp-adpress') . '</h3></div>';

			/* First Table */
			$html .= '<table class="campaign_info info-table"><tbody>';
			if ($campaign->ad_definition['type'] === 'image' || $campaign->ad_definition['type'] === 'flash') {
				$html .= '<tr><td class="title">' . __('Ad Type', 'wp-adpress') . '</td><td>' . __('Image', 'wp-adpress') . '</td></tr>';
				$html .= '<tr><td class="title">' . __('Banner Size', 'wp-adpress') . '</td><td>' . $campaign->ad_definition['size']['width'] . ' X ' . $campaign->ad_definition['size']['height'] . '</td></tr>';
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

			$html .= '<div style="clear:both;"></div></div>';
			return $html;
		}

		private function render_form()
		{
			switch ($this->campaign->ad_definition['type']) {
			case 'link':
				$html = '
					<div class="c-block">                    
					<div class="c-head">
					<h3>' . __('Link Ad', 'wp-adpress') . '</h3>
					</div>
					<table class="form-table">
					<tbody>
					<tr>
					<th scope="row">
					<label for="link_text">' . __('Link Text', 'wp-adpress') . '</label>
					</th>
					<td>
					<input type="textbox" name="link_text" id="link_text" length="' . $this->campaign->ad_definition['length'] . '"/>
					</td>
					</tr>
					<tr>
					<th scope="row">
					<label for="destination_url">' . __('Destination URL', 'wp-adpress') . '</label>
					</th>
					<td>
					<input type="textbox" name="destination_url" id="destination_url"/>
					</td>
					</tr>
					</tbody>
					</table>
					</div>';
				break;
			case 'image':
				$html = '
					<div class="c-block">                    
					<div class="c-head">
					<h3>' . __('Image Ad', 'wp-adpress') . '</h3>
					</div>
					<table class="form-table">
					<tbody>
					<tr>
					<th scope="row">
					<label for="upload_image">' . __('Upload Image', 'wp-adpress') . '</label>
					</th>
					<td>
					<input type="textbox" name="upload_image" id="upload_image" value="' . __('Upload Image', 'wp-adpress') . '" disabled/> <a href="#" id="upload_btn" class="button-secondary" style="padding: 4px;padding-top: 5px;padding-bottom: 3px;">' . __('Upload Image', 'wp-adpress') . '</a>
					</td>
					</tr>
					<tr>
					<th scope="row">
					<label for="destination_url">' . __('Destination URL', 'wp-adpress') . '</label>
					</th>
					<td>
					<input type="textbox" name="destination_url" id="destination_url"/>
					</td>
					</tr>
					</tbody>

					</table>
					</div>';
				break;
			case 'flash':
			case 'image':
				$html = '
					<div class="c-block">
					<div class="c-head">
					<h3>' . __('Flash Ad', 'wp-adpress') . '</h3>
					</div>
					<table class="form-table">
					<tbody>
					<tr>
					<th scope="row">
					<label for="upload_flash">' . __('Upload SWF', 'wp-adpress') . '</label>
					</th>
					<td>
					<input type="textbox" name="upload_flash" id="upload_flash" value="' . __('Upload SWF', 'wp-adpress') . '" disabled/> <a href="#" id="upload_btn" class="button-secondary" style="padding: 4px;padding-top: 5px;padding-bottom: 3px;">' . __('Upload SWF', 'wp-adpress') . '</a>
					</td>
					</tr>
					<tr>
					<th scope="row">
					<label for="destination_url">' . __('Destination URL', 'wp-adpress') . '</label>
					</th>
					<td>
					<input type="textbox" name="destination_url" id="destination_url"/>
					</td>
					</tr>
					</tbody>

					</table>
					</div>';
				break;
			}
			return $html;
		}

		public function display_page()
		{
			return $this->html;
		}

		static function process_post($post)
		{
			$current_user = wp_get_current_user();
			global $pid;
			$pid = $current_user->ID;

			/*
			 * Get the purchase details
			 */
			$cid = (int)$post['cid'];
			$campaign = new wp_adpress_campaign($cid);
			$param = array();
			$settings = get_option('adpress_settings');

			$param = array(
				'destination_val' => $post['destination_val'],
				'url' => $post['destination_url']
			);

			if (isset($post['client_message'])) {
				$param['client_message'] = $post['client_message'];
			}


			$settings = get_option('adpress_settings');
			$paypal = isset($settings['paypal']);
			$mode = isset($settings['sandbox_mode']);
			$plog = isset($settings['payments_log_enabled']);
			if ($paypal) {
				$gateway = array('paypal' => 'PayPal');
			} else {
				$gateway = array('manual' => 'Manual');
			}

			$currency = $settings['currency']; 

			//$result = $campaign->register_ad($param);


			// Payment Data
			$current_user = wp_get_current_user();

			$payment_data = array(
				'price' => $campaign->ad_definition['price'],
				'user_email' => $current_user->user_email,
				'purchase_key' => 'UUUX',
				'currency' => $currency,
				'user_info' => array(
					'first_name' => $current_user->user_firstname,
					'last_name' => $current_user->user_lastname,
					'id' => $current_user->ID,	
				),
				'status' => 'pending',
				//'ad_id' => $result,
				'ad' => array( 'cid' => $cid, 'post' => $param ),
				'gateway' => $gateway,
			);

			// Insert a new payment
			$payment_id = wp_adpress_insert_payment( $payment_data );

			// Update payment status
			//wp_adpress_update_payment_status( $payment_id, 'publish' ); 

			// show response
			self::display_response();
		}

		static function display_response()
		{
			echo '
				<div class="wrap" id="adpress">
				<h2>Purchase Ad</h2>
				<div class="c-block"> 
				<div class="c-head">
				<h3 id="adpress-icon-request_sent">' . __('Your request is sent', 'wp-adpress') . '</h3>
				</div>
				<p>
				<strong>' . __('An Administrator may need to check your request before going live.', 'wp-adpress') . '</strong>
				</p>
				<p>
				' . __('You can now <a href="admin.php?page=adpress-client">purchase more Ads</a> or <a href="admin.php?page=adpress-purchases">check your requests status</a>.', 'wp-adpress') . '
				</p>
				</div>
				</div>';
		}

	}
}

