<?php
add_filter( 'wp_adpress_reports_tabs', 'wpad_reports_settings_tab' );
function wpad_reports_settings_tab( $tabs ) {
	$settings_tab = array(
		'settings' => __( 'Settings', 'wp-adpress' ),
	);

	$tabs = array_merge( $tabs, $settings_tab );

	return $tabs;
}

add_action( 'wp_adpress_reports_tab', 'wpad_reports_settings_body' );
function wpad_reports_settings_body( $tab ) {
	if ( $tab === 'settings' ) {
		require_once( 'page.php' );		
	}	
}

add_action( 'admin_init', 'wpad_reports_register_settings' ); 
function wpad_reports_register_settings() {
	register_setting( 'wpad_reports_settings', 'wpad_reports_settings' , 'wp_adpress_forms::validate' );
	add_settings_section('history_section', 'Ads History', 'wp_adpress_forms::description', 'adpress_reports_settings_form_history', 'History Settings');
	add_settings_field('history', 'Enable History', 'wp_adpress_forms::checkbox', 'adpress_reports_settings_form_history', 'history_section', array('history', 'wpad_reports_settings'));
	add_settings_field('history_reset', 'Delete History', 'wp_adpress_forms::button', 'adpress_reports_settings_form_history', 'history_section', array( 'value' => 'Remove History', 'action' => 'reports_remove_history' ) ); 

}

add_action( 'wp_adpress_reports_remove_history', 'wp_adpress_reports_remove_history_fn' ); 
function wp_adpress_reports_remove_history_fn() {
	 wp_adpress_delete_all_adhistory();
}
