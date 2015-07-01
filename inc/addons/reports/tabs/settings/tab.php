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
