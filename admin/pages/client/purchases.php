<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}
?>
<div class="wrap" id="adpress">
    <div id="adpress-icon-purchases" class="icon32"><br></div>
    <h2><?php _e('My Purchases', 'wp-adpress'); ?> <a href="admin.php?page=adpress-client"
                                                      class="add-new-h2"><?php _e('Purchase Ads', 'wp-adpress'); ?></a>
    </h2>

    <div id="campaings-table">
        <div class="tablenav top">
            <div class="tablenav-pages">
                <div class="displaying-num">

                </div>
            </div>
            <br class="clear"/>
        </div>

        <table class="wp-list-table widefat plugins withbuttons" cellspacing="0">
            <thead>
            <tr>
                <th class="id-column"><?php _e('Id', 'wp-adpress'); ?></th>
                <?php
                if (is_plugin_active_for_network(ADPRESS_BASENAME)) {
                    echo '<th>' . __('Website', 'wp-adpress') . '</th>';
                }
                ?>
                <th><?php _e('Campaign', 'wp-adpress'); ?></th>
                <th><?php _e('Status', 'wp-adpress'); ?></th>
                <th><?php _e('Requested at', 'wp-adpress'); ?></th>
                <th><?php _e('Actions', 'wp-adpress'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php
            if (is_plugin_active_for_network(ADPRESS_BASENAME)) {
                global $blog_id;
                $old_id = $blog_id;
                foreach (wp_adpress_mu::list_blogs() as $blogid) {
                    switch_to_blog($blogid);
                    $purchases_view = new wp_adpress_purchases(true);
                    if (!$purchases_view->empty) {
                        echo $purchases_view->ads_view;
                    }
                }
                switch_to_blog($old_id);

            } else {
                $purchases_view = new wp_adpress_purchases();
                echo $purchases_view->ads_view;
            }

            ?>
            </tbody>
        </table>
    </div>
</div>