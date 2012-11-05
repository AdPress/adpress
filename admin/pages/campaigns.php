<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}
// Create a new Campaigns page view
$view = new wp_adpress_campaigns_view();
$campaigns_count = wp_adpress_campaigns::campaigns_number();
?>
<div class="wrap" id="adpress">
    <div id="adpress-icon-campaigns" class="icon32"><br></div>
    <h2><?php _e('Campaigns', 'wp-adpress'); ?><a href="admin.php?page=adpress-inccampaign"
                                                  class="add-new-h2"><?php _e('Add New', 'wp-adpress'); ?></a></h2>

    <div id="campaings-table">
        <div class="tablenav top">
            <div class="tablenav-pages">
                <div class="displaying-num">
                    <?php echo $view->count; printf(_n(' Campaign', ' Campaigns', $view->count, 'wp-adpress')); ?>
                </div>
            </div>
            <br class="clear"/>
        </div>
        <?php echo $view->view; ?>
    </div>
</div>