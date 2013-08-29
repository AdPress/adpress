<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}
// Create a new Main view
$view = new wp_adpress_mu_main();
?>
<div class="wrap" id="adpress" style="width:580px">
    <div id="adpress-icon-access" class="icon32"><br></div>
    <h2><?php _e('Admin Access', 'wp-adpress'); ?></h2>
    <div id="campaings-table">
        <div class="tablenav top">
            <div class="tablenav-pages">
                <div class="displaying-num">
                    <?php echo $view->blogs_count; printf(_n(' Blog', ' Blogs', $view->blogs_count, 'wp-adpress')); ?>
                </div>
            </div>
            <br class="clear" />
        </div>
        <?php echo $view->table; ?>
    </div>
</div>