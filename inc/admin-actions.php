<?php
/**
 * Admin Actions
 *
 * @package    	Includes 
 * @copyright   Copyright (c) 2015, Abid Omar 
 * @since       1.1.0
 */
// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

/**
 * Processes all WPAD actions sent via POST and GET by looking for the 'wpad-action'
 * request and running do_action() to call the function
 *
 * @since 1.1.0
 * @return void
 */
function wp_adpress_process_actions() {
	if ( isset( $_POST['wpad-action'] ) ) {
		if ( current_user_can( 'manage_options' ) ) {
			do_action( 'wp_adpress_' . $_POST['wpad-action'], $_POST );
		}
	}
	if ( isset( $_GET['wpad-action'] ) ) {
		if ( current_user_can( 'manage_options' ) ) {
			do_action( 'wp_adpress_' . $_GET['wpad-action'], $_GET );
		}
	}
}
add_action( 'admin_init', 'wp_adpress_process_actions' );
