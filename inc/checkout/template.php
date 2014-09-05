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
function wp_adpress_checkout_form() {
	$html = '<div class="wrap" id="adpress" style="width:600px;">';
	$html .= wp_adpress_checkout_header();
	$html .= '</div>';
	return $html;
}

/**
 * Renders the checkout form header
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_checkout_header() {
	$html = '<div id="adpress-icon-purchase" class="icon32"><br></div><h2>Purchase Ad</h2>';
	return $html;
}

/**
 * Renders the user details
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_checkout_user_details() {
	$html = '';
	return $html;
}

/**
 * Renders the ad details
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_checkout_ad_details() {
	$html = '';
	return $html;
}

/**
 * Renders the ad form
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_checkout_ad_form() {
	$html = '';
	return $html;
}

/**
 * Renders the payment gateways
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_gateways_form() {
	$html = '';
	return $html;
}

/**
 * Renders the submit button
 *
 * @since 1.0.0
 * @return string
 */
function wp_adpress_submit() {
	$html = '';
	return $html;
}
