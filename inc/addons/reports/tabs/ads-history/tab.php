<?php
add_filter( 'wp_adpress_reports_tabs', 'wpad_reports_ads_history_tab' );
function wpad_reports_ads_history_tab( $tabs ) {
	$ads_history_tab = array(
		'ads_history' => __( 'Ads History', 'wp-adpress' ),
	);

	$tabs = array_merge( $tabs, $ads_history_tab );

	return $tabs;
}

add_action( 'wp_adpress_reports_tab', 'wpad_reports_ads_history_body' );
function wpad_reports_ads_history_body( $tab ) {
	if ( $tab === 'ads_history' ) {
		require_once( 'history_table.php' );
		require_once( 'page.php' );		
	}	
}
