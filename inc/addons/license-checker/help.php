<?php
// Current screen
global $current_screen;
if ( isset( $_GET['tab'] ) ) {
	$tab = $_GET['tab'];
} else {
	return;
}

switch( $tab ) {
case 'license':
	$current_screen->add_help_tab(array(
		'id' => 'history_help_license',
		'title' => __( 'License', 'wp-adpress' ),
		'content' => '<h2>' . __( 'License', 'wp-adpress' ) . '</h2>' .
		'<p>' . __( '', 'wp-adpress' ) . '</p>',	
	) );
	break;
}
