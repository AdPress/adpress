<?php
// Current screen
global $current_screen;
if ( isset( $_GET['tab'] ) ) {
	$tab = $_GET['tab'];
} else {
	return;
}

switch( $tab ) {
case 'export':
	$current_screen->add_help_tab(array(
		'id' => 'history_help_export',
		'title' => __('Export', 'wp-adpress' ),
		'content' => '<h2>' . __('Export', 'wp-adpress') . '</h2>' .
		'<p>' . __('Use this option to export your settings and data to a back-up file. After clicking the "Export" button, a message will appear and asks you to download the back-up file. Download this file and store it in a safe location.', 'wp-adpress') . '</p>' .
		'<ul>' .
		'<li>' . __('<strong>Settings Data</strong> Export only your current settings parameters', 'wp-adpress') . '</li>' .
		'<li>' . __('<strong>Campaign Data</strong> Export all campaigns and ads (running or pending)', 'wp-adpress') . '</li>' .
		'<li>' . __('<strong>Settings Data</strong> Export the purchase history', 'wp-adpress') . '</li>' .
		'</ul>'
	));
	$current_screen->add_help_tab(array(
		'id' => 'history_help_import',
		'title' => __('Import', 'wp-adpress' ),
		'content' => '<h2>' . __('Import', 'wp-adpress') . '</h2>' .
		'<h3>' . __('Warning', 'wp-adpress') . '</h3>' .
		'<p>' . __('This function will erase all your current data. It doesn\'t do a merge. Your settings, campaign or history data will be fully replaced. Make sure you do a back-up of your current configuration and data before doing an import.', 'wp-adpress') . '</p>' .
		'<h3>' . __('Back-up file', 'wp-adpress') . '</h3>' .
		'<p>' . __('Your back-up file should contain the data you are looking to import. For example, if you want to import history data, the back-up file must have this data.', 'wp-adpress') . '</p>' .
		'<h3>' . __('Select items', 'wp-adpress') . '</h3>' .
		'<p>' . __('You can have a full back-up data, but only import one or a couple of items. For example, if you want to import plug-in settings, just check the settings data box. Your campaign and history data won\'t be changed.', 'wp-adpress') . '</p>'

	));
	$current_screen->add_help_tab(array(
		'id' => 'history_help_reset',
		'title' => __('Reset Plug-in', 'wp-adpress' ),
		'content' => '<h2>' . __('Reset the plug-in', 'wp-adpress') . '</h2>' .
		'<p>' . __('You may find it useful to reset your plug-in settings. You can pick which elements to reset and others to preserve.', 'wp-adpress') . '</p>' .
		'<ul>' .
		'<li>' . __('<strong>Settings</strong> Reset the default plug-in settings', 'wp-adpress') . '</li>' .
		'<li>' . __('<strong>Campaign Data</strong> Remove all campaigns and ads', 'wp-adpress') . '</li>' .
		'<li>' . __('<strong>History Data</strong> Remove all the history', 'wp-adpress') . '</li>' .
		'</ul>'
	));
	break;
}
