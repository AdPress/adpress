<?php
// Current screen
global $current_screen;
if ( isset( $_GET['tab'] ) ) {
	$tab = $_GET['tab'];
} else {
	return;
}

switch( $tab ) {
case 'image_ad':
	$current_screen->add_help_tab(array(
		'id' => 'image_help_tab1',
		'title' => __('Image Ad', 'wp-adpress' ),
		'content' => '<h2>' . __('Image Ad', 'wp-adpress') . '</h2>' .
		'<p>' . __('You can customize fully how you ads look like. For example, you can add a border to your images Ads. You can also change or add some text or HTML code.', 'wp-adpress') . '</p>'
	));
	$current_screen->add_help_tab(array(
		'id' => 'image_help_tab2',
		'title' => __('HTML Code', 'wp-adpress'),
		'content' => '<h2>' . __('HTML Code', 'wp-adpress') . '</h2>' .
		'<p>' . __('This is the HTML code for each Ad spot. There some variables that the plug-in fills automatically.', 'wp-adpress') . '</p>' .
		'<li>' . __('<strong>@url</strong> The Ad target URL.', 'wp-adpress') . '</li>' .
		'<li>' . __('<strong>@image_src</strong> The image URL.', 'wp-adpress') . '</li>'
	));
	$current_screen->add_help_tab(array(
		'id' => 'image_help_tab3',
		'title' => __('CSS Style', 'wp-adpress'),
		'content' => '<h2>' . __('CSS Style', 'wp-adpress') . '</h2>' .
		'<p>' . __('The CSS styles added here will be loaded in every page on your blog. They should be targeted for your Ad Widget. For that purpose, AdPress makes use of the <em>image-campaign</em> class. All your styles should be nested beneath this class to avoid conflicts with other CSS rules.', 'wp-adpress')
	));
	break;
case 'link_ad':
	$current_screen->add_help_tab(array(
		'id' => 'link_help_tab1',
		'title' => __('Link Ad', 'wp-adpress' ),
		'content' => '<h2>' . __('Link Ad', 'wp-adpress') . '</h2>' .
		'<p>' . __('You can customize fully how you link ads look like. For example, you can change how your Ads links look like (color, font...). You can also change or add some text or HTML code.', 'wp-adpress') . '</p>'
	));
	$current_screen->add_help_tab(array(
		'id' => 'link_help_tab2',
		'title' => __('HTML Code', 'wp-adpress'),
		'content' => '<h2>' . __('HTML Code', 'wp-adpress') . '</h2>' .
		'<p>' . __('This is the HTML code for each Ad spot. There some variables that the plug-in fills automatically.', 'wp-adpress') . '</p>' .
		'<li>' . __('<strong>@url</strong> The Ad target URL.', 'wp-adpress') . '</li>' .
		'<li>' . __('<strong>@link_text</strong> The link text.', 'wp-adpress') . '</li>'
	));
	$current_screen->add_help_tab(array(
		'id' => 'link_help_tab3',
		'title' => __('CSS Style', 'wp-adpress'),
		'content' => '<h2>' . __('CSS Style', 'wp-adpress') . '</h2>' .
		'<p>' . __('The CSS styles added here will be loaded in every page on your blog. They should be targeted for your Ad Widget. For that purpose, AdPress makes use of the <em>link-campaign</em> class. All your styles should be nested beneath this class to avoid conflicts with other CSS rules.', 'wp-adpress')
	));
	break;
case 'flash_ad':
	$current_screen->add_help_tab(array(
		'id' => 'flash_help_tab1',
		'title' => __('Flash Ad', 'wp-adpress' ),
		'content' => '<h2>' . __('Flash Ad', 'wp-adpress') . '</h2>' .
		'<p>' . __('You can customize fully how you flash ads look like. For example, you can change how your Ads links look like (color, font...). You can also change or add some text or HTML code.', 'wp-adpress') . '</p>'
	));
	$current_screen->add_help_tab(array(
		'id' => 'flash_help_tab2',
		'title' => __('HTML Code', 'wp-adpress'),
		'content' => '<h2>' . __('HTML Code', 'wp-adpress') . '</h2>' .
		'<p>' . __('This is the HTML code for each Ad spot. There some variables that the plug-in fills automatically.', 'wp-adpress') . '</p>' .
		'<li>' . __('<strong>@banner_height</strong> Height of the Flash Banner.', 'wp-adpress') . '</li>' .
		'<li>' . __('<strong>@banner_width</strong> Width of the Flash Banner.', 'wp-adpress') . '</li>' .
		'<li>' . __('<strong>@url</strong> The Ad target URL.', 'wp-adpress') . '</li>' .
		'<li>' . __('<strong>@swf_src</strong> The link text.', 'wp-adpress') . '</li>'
	));
	$current_screen->add_help_tab(array(
		'id' => 'flash_help_tab3',
		'title' => __('CSS Style', 'wp-adpress'),
		'content' => '<h2>' . __('CSS Style', 'wp-adpress') . '</h2>' .
		'<p>' . __('The CSS styles added here will be loaded in every page on your blog. They should be targeted for your Ad Widget. For that purpose, AdPress makes use of the <em>flash-campaign</em> class. All your styles should be nested beneath this class to avoid conflicts with other CSS rules.', 'wp-adpress')
	));
	break;
}
