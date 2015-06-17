<?php

add_action('admin_enqueue_scripts', 'myHelpPointers');
function myHelpPointers() {
	require_once( 'class.wp-help-pointers.php' );
global $current_screen;
//var_dump( $current_screen );
	//First we define our pointers 
	$pointers = array(
		array(
			'id' => 'xyz12',   // unique id for this pointer
			'screen' => 'dashboard_page_wpadpress-new', // this is the page hook we want our pointer to show on
			'target' => '#wpadminbar', // the css selector for the pointer to be tied to, best to use ID's
			'title' => 'My ToolTip',
			'content' => 'My tooltips Description',
			'position' => array( 
				'edge' => 'top', //top, bottom, left, right
				'align' => 'left' //top, bottom, left, right, middle
			)
		)
		// more as needed
	);
	//Now we instantiate the class and pass our pointer array to the constructor 
	$myPointers = new WP_Help_Pointer($pointers);
}
