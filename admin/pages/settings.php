<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}
/*
 * Catch Actions
 */
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'del_history':
            if (wp_adpress_history::empty_history()) {
                wp_adpress::display_notice('History Deleted', '<p>History Removed</p>', 'adpress-icon-request_sent');
            }
            break;
        case 'license_save':
            $options = get_option('adpress_license_settings');
            $validity = wp_adpress_license::check_license($options['license_username'], $options['license_key']);
            if ($validity) {
                wp_adpress::remove_notification('license_validity');
            } else {
                wp_adpress::add_notification('license_validity', 'AdPress License is not valid', 'Your AdPress username and license key are not valid. Please check them again.', 'error');
            }
            break;
    }
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
