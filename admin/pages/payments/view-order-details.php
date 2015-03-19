<?php
/**
 * View Order Details
 *
 * @package     Admin
 * @subpackage  Pages/Payments
 * @since       1.0.0
*/

// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

/**
 * View Order Details Page
 *
 * @since 1.0.0
 * @return void
*/
// Stop if no payment id is provided
if ( ! isset( $_GET['id'] ) || ! is_numeric( $_GET['id'] ) ) {
	wp_die( __( 'Payment ID not supplied or incorrect.', 'wp-adpress' ), __( 'Error', 'wp-adpress' ) );
}

// Setup the variables
$payment_id   = absint( $_GET['id'] );
$item         = get_post( $payment_id );
$payment_date = strtotime( $item->post_date );
$user_info = get_post_meta( $payment_id, 'wpad_payment_user_info', true );

if (!$user_info) {
	$user_info = array (
		'first_name' => '',
		'last_name' => '',
		'id' => '',
	);
}

?>
<div class="wrap">
	<h2><?php printf( __( 'Payment #%d', 'wp-adpress' ), $payment_id ); ?></h2>
	<?php do_action( 'wp_adpress_view_order_details_before', $payment_id ); ?>
	<form id="wp-adpress-edit-order-form" method="post">
		<?php do_action( 'wp_adpress_view_order_details_form_top' ); ?>
		<div id="poststuff">
			<div id="dashboard-widgets-wrap">
				<div id="post-body" class="metabox-holder columns-2">

					<!-- BEGIN CONTAINER 1 -->
					<div id="postbox-container-1" class="postbox-container">
						<div id="side-sortables" class="meta-box-sortables ui-sortable">							
	
							<div id="wp-adpress-order-update" class="postbox"> <!-- ORDER UPDATE -->
								
								<h3 class="hndle">
									<span><?php _e( 'Payment Status', 'wp-adpress' ); ?></span>
								</h3>
								<div class="inside">
									<div class="admin-box">	
	
										<div class="admin-box-inside">
											<p>
												<span class="label"><?php _e( 'Status:', 'wp-adpress' ); ?></span>
												<span class="value"><?php _e( wp_adpress_get_payment_status( $item, true ), 'wp-adpress' ); ?></span>
											</p>
										</div>
	
										<div class="admin-box-inside">
											<p>
												<span class="label"><?php _e( 'Date:', 'wp-adpress' ); ?></span>
												<span class="value"><?php echo esc_attr( date( 'm/d/Y', $payment_date ) ); ?></span>
											</p>
										</div>
	
										<div class="admin-box-inside">
											<p>
												<span class="label"><?php _e( 'Time:', 'wp-adpress' ); ?></span>
												<span class="value"><?php echo esc_attr( date_i18n( 'H', $payment_date ) ); ?> : <?php esc_attr( date( 'i', $payment_date ) ); ?></span>
											</p>
										</div>
	
									</div><!-- /.column-container -->
	
								</div><!-- /.inside -->
	
							</div><!-- /#order-data -->
	
						</div><!-- /#side-sortables -->
					</div><!-- /#postbox-container-1 -->
	
					<div id="postbox-container-2" class="postbox-container">
						<div id="normal-sortables" class="meta-box-sortables ui-sortable">
	
							<?php do_action( 'wp_adpress_view_order_details_main_before', $payment_id ); ?>
	
							<div id="customer-details" class="postbox">
								<h3 class="hndle">
									<span><?php _e( 'Customer Details', 'wp-adpress' ); ?></span>
								</h3>
								<div class="inside clearfix">
	
									<div class="column-container">
										<div class="column">
											<strong><?php _e( 'Name:', 'wp-adpress' ); ?></strong>
											<span class="value"><?php echo esc_attr( $user_info['first_name'] . ' ' . $user_info['last_name'] ); ?></span>
										</div>
										<div class="column">
											<strong><?php _e( 'Email:', 'wp-adpress' ); ?></strong>
											<span class="value"><?php echo esc_attr( get_post_meta( $payment_id, 'wpad_payment_user_email', true ) ); ?></span>
										</div>
										<div class="column">
											<strong><?php _e( 'User ID:', 'wp-adpress' ); ?></strong>
											<span class="value"><?php echo esc_attr( wp_adpress_get_payment_user_id( $payment_id ) ); ?></span>
										</div>
										<div class="column-clear">
										</div>
									</div>
	
								</div><!-- /.inside -->
							</div><!-- /#customer-details -->

							<div id="ad-details" class="postbox">
								<h3 class="hndle">
									<span><?php _e( 'Ad Details', 'wp-adpress' ); ?></span>
								</h3>
								<div class="inside clearfix">
	
									<div class="column-container">
										<div class="column">
											<strong><?php _e( 'Ad ID:', 'wp-adpress' ); ?></strong>
											<span class="value"><?php echo esc_attr( get_post_meta( $payment_id, 'wpad_payment_ad_id', true ) ); ?></span>
										</div>
										<div class="column-clear">
										</div>
									</div>

	
								</div><!-- /.inside -->
							</div><!-- /#ad-details -->

							<div id="payment-details" class="postbox">
								<h3 class="hndle">
									<span><?php _e( 'Payment Details', 'wp-adpress' ); ?></span>
								</h3>
								<div class="inside clearfix">
	
									<div class="column-container">	
										<div class="column">
											<strong><?php _e( 'Price:', 'wp-adpress' ); ?></strong>
											<span class="value"><?php echo esc_attr( get_post_meta( $payment_id, 'wpad_payment_total', true ) ); ?></span>
										</div>
										<div class="column">
											<strong><?php _e( 'Purchase Key:', 'wp-adpress' ); ?></strong>
											<span class="value"><?php echo esc_attr( get_post_meta( $payment_id, 'wpad_payment_purchase_key', true ) ); ?></span>
										</div>
										<div class="column">
											<strong><?php _e( 'Gateway:', 'wp-adpress' ); ?></strong>
											<span class="value"><?php echo esc_attr( wp_adpress_get_gateway_label ( get_post_meta( $payment_id, 'wpad_payment_gateway', true ) ) ); ?></span>
										</div>
										<div class="column">
											<strong><?php _e( 'Mode:', 'wp-adpress' ); ?></strong>
											<span class="value"><?php echo esc_attr( wp_adpress_get_mode_label( get_post_meta( $payment_id, 'wpad_payment_mode', true ) ) ); ?></span>
										</div>
										<div class="column-clear">
										</div>
									</div>
	
								</div><!-- /.inside -->
							</div><!-- /#payment-details -->
	
						<?php do_action( 'wp_adpress_view_order_details_main_after', $payment_id ); ?>

						</div><!-- /#normal-sortables -->
					</div><!-- #postbox-container-2 -->
				</div><!-- /#post-body -->
			</div><!-- #dashboard-widgets-wrap -->
		</div><!-- /#post-stuff -->

		<?php do_action( 'wp_adpress_view_order_details_form_bottom', $payment_id ); ?>
		<?php wp_nonce_field( 'wp_adpress_update_payment_details_nonce' ); ?>

		<input type="hidden" name="wpad_payment_id" value="<?php echo esc_attr( $payment_id ); ?>"/>
		<input type="hidden" name="wpad_action" value="update_payment_details"/>
	</form>

	<?php do_action( 'wp_adpress_view_order_details_after', $payment_id ); ?>
</div><!-- /.wrap -->
