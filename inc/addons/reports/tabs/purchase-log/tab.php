<?php
add_filter( 'wp_adpress_reports_tabs', 'wpad_reports_purchase_log_tab' );
function wpad_reports_purchase_log_tab( $tabs ) {
	$purchase_log_tab = array(
		'purchase_log' => __( 'Payments Log', 'wp-adpress' ),
	);

	$tabs = array_merge( $tabs, $purchase_log_tab );

	return $tabs;
}

add_action( 'wp_adpress_reports_tab', 'wpad_reports_purchase_log_body' );
function wpad_reports_purchase_log_body( $tab ) {
	if ( $tab === 'purchase_log' ) {
		require_once( 'history_table.php' );
		require_once( 'page.php' );		
	}	
}

// Load CSS and JS files
add_action( 'admin_print_scripts', 'wp_adpress_reports_scripts' );
function wp_adpress_reports_scripts() {
	
	global $current_screen;
	if ( $current_screen->id === 'adpress_page_adpress-reports' ) {
		
	}
}

add_action( 'admin_print_styles', 'wp_adpress_reports_styles' );
function wp_adpress_reports_styles() {
	global $current_screen;
	if ( $current_screen->id === 'adpress_page_adpress-reports' ) {
		wp_enqueue_style( 'wp_adpress_purchase_log', ADPRESS_URLPATH . 'inc/addons/reports/tabs/purchase-log/files/css/purchase_log.css' );	
	}
}
