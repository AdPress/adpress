<?php
add_filter( 'wp_adpress_reports_tabs', 'wpad_reports_purchase_log_tab' );
function wpad_reports_purchase_log_tab( $tabs ) {
	$purchase_log_tab = array(
		'purchase_log' => __( 'Payments Log', 'wp-adpress' ),
	);

	$tabs = array_merge( $tabs, $purchase_log_tab );

	return $tabs;
}
