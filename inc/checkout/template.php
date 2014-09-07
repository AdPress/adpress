<?php
/**
 * Template Functions 
 * 
 * @package     Includes
 * @subpackage  Checkout
 * @since       1.0.0
 */

// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

/**
 * Renders the checkout form
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_checkout_form( $cid ) {
	$html = '<form>';
	$html .= '<div class="wrap" id="adpress" style="width:600px;">';
	$html .= wp_adpress_checkout_header( $cid );
	$html .= wp_adpress_checkout_user_details( $cid );
	$html .= wp_adpress_checkout_ad_details( $cid );
	$html .= wp_adpress_checkout_ad_form( $cid );
	$html .= wp_adpress_checkout_addon_form( $cid );
	$html .= wp_adpress_checkout_gateways( $cid );
	$html .= wp_adpress_checkout_submit( $cid );
	$html .= '</div>';
	$html .= '</form>';
	return apply_filters( 'wp_adpress_checkout_form', $html, $cid );
}

/**
 * Renders the checkout form header
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_checkout_header( $cid ) {
	$html = '<div id="adpress-icon-purchase" class="icon32"><br></div><h2>Purchase Ad</h2>';
	return apply_filters( 'wp_adpress_checkout_header', $html, $cid );
}

/**
 * Renders the user details
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_checkout_user_details( $cid ) {
	$user_info = get_userdata( get_current_user_id() );	
	$html = '<div class="c-block">';
	$html .= '<div class="c-head"><h3>' . __( 'User Details', 'wp-adpress' ) . '</h3></div>'; 
	$html .= '<table class="campaign_info info-table">';
	$html .= '<tr><td class="title">' . __( 'Logged User', 'wp-adpress' ) . '</td><td>' . $user_info->user_login . '</td></tr>';
	$html .= '</table><div style="clear:both"></div>';
	$html .= '</div>';
	return apply_filters( 'wp_adpress_checkout_user_details', $html, $cid );
}

/**
 * Renders the ad details
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_checkout_ad_details( $cid ) {	
	$campaign = new wp_adpress_campaign( $cid );
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
	$html .= '<tr><td class="title">' . __('Price', 'wp-adpress') . '</td><td>' . $campaign->ad_definition['price'] . ' ' . wp_adpress_get_currency() . '</td></tr>';
	$html .= '<tbody></table>';

	$html .= '<div style="clear:both;"></div></div>';
	return $html;
	return apply_filters( 'wp_adpress_checkout_ad_details', $html, $cid );
}

/**
 * Renders the ad form
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_checkout_ad_form( $cid ) {
	$campaign = new wp_adpress_campaign( $cid );
	switch( $campaign->ad_definition['type'] ) {
	case 'link':
		$html = wp_adpress_checkout_ad_form_link( $cid );
		break;
	case 'image':
		$html = wp_adpress_checkout_ad_form_image( $cid );
		break;
	case 'flash':
		$html = wp_adpress_checkout_ad_form_flash( $cid );
		break;
	}
	return apply_filters( 'wp_adpress_checkout_ad_form', $html, $cid );
}

/**
 * Renders the link form
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_checkout_ad_form_link( $cid ) {
	$campaign = new wp_adpress_campaign( $cid );
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
					<input type="textbox" name="link_text" id="link_text" length="' . $campaign->ad_definition['length'] . '"/>
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

	return apply_filters( 'wp_adpress_checkout_ad_form_link', $html, $cid );
}

/**
 * Renders the image form
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_checkout_ad_form_image( $cid ) {
	$camapign = new wp_adpress_camapign( $cid );
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
				
	return apply_filters( 'wp_adpress_checkout_ad_form_image', $html, $cid );
}

/**
 * Renders the flash form
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_checkout_ad_form_flash( $cid ) {
	$campaign = new wp_adpress_campaign( $cid );
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
	return apply_filters( 'wp_adpress_checkout_ad_form_flash', $html, $cid );
}

/**
 * Renders the addon form
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_checkout_addon_form( $cid ) {
	$html = '';

	return apply_filters( 'wp_adpress_checkout_addon_form', $html, $cid );
}

/**
 * Renders the payment gateways
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_checkout_gateways( $cid ) {
	$gateways = wp_adpress_get_active_payment_gateways();
	$html = '<div class="c-block">';
	$html .= '<div class="c-head"><h3>Payment Gateway</h3></div>';
	$html .= '<table class="form-table">';

	foreach( $gateways as $gateway ) {
		$html .= '<tr><td>';
		$html .= '<input name="gateway" type="radio" id="' . $gateway['id'] . '" />  <label for="' . $gateway['id'] . '">' . $gateway['checkout_label'] . '</label>';
		$html .= '</td></tr>';
	}

	$html .= '</table>';
	$html .= '</div>';

	return apply_filters( 'wp_adpress_checkout_gateways', $html, $cid );
}

/**
 * Renders the submit button
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_checkout_submit( $cid ) {
	$html = '';

	return apply_filters( 'wp_adpress_checkout_submit', $html, $cid );
}
