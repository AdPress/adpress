<?php

add_action( 'init', 'wp_adpress_register_notifications' );
function wp_adpress_register_notifications() {
	// Notification Class
	$notify = new wpplex\WP_Notify\WP_Notify( 'wpad' );

	// Missing License Notification
	$mlc_txt = '<h3>' . __( 'AdPress License', 'wp-adpress' ) . '</h3><p>' 
		. __( 'To get automatic updates, you should enter your AdPress License information.', 'wp-adpress' ) . '</p>' . 
		'<p>' . __( 'Enter your details, from the <a href="admin.php?page=adpress-settings&tab=license">License Settings</a> page.', 'wp-adpress' ) . '</p>';
	// Incorrect License Notification
	$ilc_txt = '<h3>' . __( 'AdPress License', 'wp-adpress' ) . '</h3><p>'
		. __( 'The License details your entered are not correct.', 'wp-adpress' ) . '</p>' . 
		'<p>' . __( 'Please verify the details, from the <a href="admin.php?page=adpress-settings&tab=license">License Settings</a> page.', 'wp-adpress' ) . '</p>';
	// Server Connection Problem
	$scp_txt = '<p>' . __( 'There was a problem contacting the AdPress Server. Please contact Support about this incident', 'wp-adpress' ) . '</p>';


	// Register Notifications
	$notify->update_notification( 'mlc', $mlc_txt, 'success', true );
	$notify->update_notification( 'ilc', $ilc_txt, 'error', true );
	$notify->update_notification( 'scp', $scp_txt, 'error', true );

	// Display Notifications
	if ( get_option( 'wp_adpress_run_once', false ) === false ) {
		$notify->display_notification( 'mlc', 0 );
		update_option( 'wp_adpress_run_once', true );
	}
}
