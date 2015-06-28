<?php


// Get the Tabs
$tabs = apply_filters( 'wp_adpress_reports_tabs', array() );

// Current Selected Tab
if (isset($_GET['tab'])) {
	$current = $_GET['tab'];
} else {
	$tabs_k = array_keys( $tabs );
	$current = $tabs_k[0];
}
?>
<div class="wrap" id="adpress">
	<h2 class="nav-tab-wrapper">
<?php
// Tabs Selector
foreach ($tabs as $tab => $name) {
	if ($tab == $current) {
		echo '<a class="nav-tab nav-tab-active" href="?page=adpress-reports&tab=' . $tab . '">' . $name . '</a>';
	} else {
		echo '<a class="nav-tab" href="?page=adpress-reports&tab=' . $tab . '">' . $name . '</a>';
	}
}
?>
</h2>

<?php

// Display Selected Tab
do_action( 'wp_adpress_reports_tab', $current );

?>
</div>
