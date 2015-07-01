<?php
/**
 * Payment History Table Page 
 *
 * @package     Admin
 * @subpackage  Admin/Pages
 * @copyright   Copyright (c) 2014, Abid Omar 
 * @since       1.0.0
 */

// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

// Initialize the history table
$adshistory_table = new wp_adpress_ads_history_table();
?>
<div id="adpress-icon-ads_history" class="icon32"><br></div>
<h2><?php _e( 'Ads History', 'wp-adpress' ); ?></h2>
        <form id="wp-adpress-adshistory-filter" method="get" action="<?php echo admin_url( 'admin.php?page=adpress-reports&tab=ads_history' ); ?>">
			<input type="hidden" name="page" value="adpress-reports" />
			<input type="hidden" name="tab" value="ads_history" />

            <?php $adshistory_table->views() ?>

            <?php $adshistory_table->advanced_filters(); ?>
            
            <?php $adshistory_table->display() ?>
        </form>
