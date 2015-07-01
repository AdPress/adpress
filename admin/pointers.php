<?php

//add_action('admin_enqueue_scripts', 'myHelpPointers');
function myHelpPointers() {
global $current_screen;
	//First we define our pointers 
	$pointers = array(
		array(
			'id' => 'xyz1f2',   // unique id for this pointer
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
	$myPointers = new \wpplex\WP_Pointer\WP_Pointer($pointers);
}
