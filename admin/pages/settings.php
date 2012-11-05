<?php
// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }
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