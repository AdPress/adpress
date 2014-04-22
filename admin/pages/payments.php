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



if ( isset( $_GET['view'] ) && 'view-order-details' == $_GET['view'] ) {

    require_once( ADPRESS_ABSPATH . 'admin/pages/payments/view-order-details.php' );

} else {

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

// Initialize the history table
$payments_table = new wp_adpress_Payment_History_Table();
$payments_table->prepare_items();
?>

<div class="wrap">
        <h2><?php _e( 'Payment History', 'edd' ); ?></h2>
        <?php do_action( 'wp_adpress_payments_page_top' ); ?>
        <form id="edd-payments-filter" method="get" action="<?php echo admin_url( 'admin.php?page=adpress-payments' ); ?>">
            <input type="hidden" name="page" value="adpress-payments" />

            <?php $payments_table->views() ?>

            <?php $payments_table->advanced_filters(); ?>
            
            <?php $payments_table->display() ?>
        </form>
        <?php do_action( 'wp_adpress_payments_page_bottom' ); ?>
    </div>
<?php
}
?>
