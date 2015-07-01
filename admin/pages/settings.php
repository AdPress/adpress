<?php
// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

/*
 * Check that user roles settings are changed
 * @return bool
 */
function wp_adpress_roles_changed()
{
	if (!isset($_GET['tab']) || $_GET['tab'] === 'general') {
		return true;
	} else {
		return false;
	}
}

/*
 * Settings Updated
 */
if (isset($_GET['settings-updated'])) {
	if (wp_adpress_roles_changed()) {
		wp_adpress_roles::set_permissions();
		wp_adpress_roles::media_filter();
	}
}
?>
<div class="wrap" id="adpress">
	<h2 class="nav-tab-wrapper">
<?php
wp_adpress_settings::render_tabs();
?>
	</h2>
<?php
wp_adpress_settings::render_pages();
?>

</div>
