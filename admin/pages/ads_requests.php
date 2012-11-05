<?php
// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }
$ads_requests = new wp_adpress_ads_requests();
$ads_running = new wp_adpress_ads_running();
?>
<div class="wrap" id="adpress">
    <div id="adpress-icon-adsrequests" class="icon32"><br></div><h2><?php _e('Ads Requests', 'wp-adpress'); ?></h2>
    <div id="requests-table" style="margin-bottom: 25px;">
        <div class="tablenav top">
            <div class="tablenav-pages">
                <div class="displaying-num">
                    <?php echo $ads_requests->count; printf(_n(' Ad', ' Ads', $ads_requests->count, 'wp-adpress'));?>
                </div>    
            </div>
            <br class="clear" />
        </div>
        <?php echo $ads_requests->view; ?>
    </div>
    <div id="adpress-icon-runningads" class="icon32"><br></div><h2><?php _e('Running Ads', 'wp-adpress'); ?></h2>
        <div id="running-table">
            <div class="tablenav top">
                <div class="tablenav-pages">
                    <div class="displaying-num">
                        <?php echo $ads_running->count; printf(_n(' Ad', ' Ads', $ads_running->count, 'wp-adpress'));?>
                    </div>    
                </div>
                <br class="clear" />
            </div>
            <?php echo $ads_running->view; ?>
        </div>
</div>