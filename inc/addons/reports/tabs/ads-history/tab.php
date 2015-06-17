<?php
add_filter( 'wp_adpress_reports_tabs', 'wpad_reports_ads_history_tab' );
function wpad_reports_ads_history_tab( $tabs ) {
	$ads_history_tab = array(
		'ads_history' => __( 'Ads History', 'wp-adpress' ),
	);

	$tabs = array_merge( $tabs, $ads_history_tab );

	return $tabs;
}
