<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
die( '-1' );
}

if ( isset( $_GET['pid'] ) && is_numeric( $_GET['pid'] ) ) {
$payment_id   = absint( $_GET['pid'] );
$item         = get_post( $payment_id );
$payment_date = strtotime( $item->post_date );	
}
?>
<div class="wrap" id="adpress">
	<h2><?php _e( 'Successful Purchase', 'wp-adpress' ); ?></h2>
	<div class="c-block"> 
		<div class="c-head">
			<h3 id="adpress-icon-request_sent"><?php _e('Your request is sent', 'wp-adpress'); ?></h3>
		</div>
		<p>
		<?php _e( 'Thank you for purchasing with <a href="http://wpadpress.com">AdPress.</a>', 'wp-adpress' ); ?>
		</p>
		<p>
		<strong><?php _e( 'An Administrator may need to check your request before it goes live.', 'wp-adpress'); ?></strong>
		</p>
		<p>
		<?php _e('You can now <a href="admin.php?page=adpress-client">purchase more Ads</a> or <a href="admin.php?page=adpress-purchases">check your requests status</a>.', 'wp-adpress'); ?>
		</p>
	</div>
	<div class="c-block"> 
		<div class="c-head">
			<h3 id="adpress-icon-request_sent"><?php _e('Purchase Details', 'wp-adpress'); ?></h3>
		</div>
		<table class="campaign_info info-table">
			<tbody>
				<tr>
					<td class="title"><?php _e( 'Price:', 'wp-adpress' ); ?></td>
					<td><?php echo esc_attr( get_post_meta( $payment_id, 'wpad_payment_total', true ) ); ?> <?php echo wp_adpress_get_currency(); ?></td>
				</tr>
				<tr>
					<td class="title"><?php _e( 'Purchase Key:', 'wp-adpress' ); ?></td>
					<td><?php echo esc_attr( get_post_meta( $payment_id, 'wpad_payment_purchase_key', true ) ); ?></td>
				</tr>
				<tr>
					<td class="title"><?php _e( 'Gateway:', 'wp-adpress' ); ?></td>
					<td><?php echo esc_attr( wp_adpress_get_gateway_label ( get_post_meta( $payment_id, 'wpad_payment_gateway', true ) ) ); ?></td>
				</tr>
				<tr>
					<td class="title"><?php _e( 'Mode:', 'wp-adpress' ); ?></td>
					<td><?php echo esc_attr( wp_adpress_get_mode_label( get_post_meta( $payment_id, 'wpad_payment_mode', true ) ) ); ?></td>
				</tr>
			</tbody>
		</table>
		<div style="clear:both">
		</div>
	</div>
</div>

