<?php
// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }
global $amount;
$action = $_GET['action'];
$settings = get_option('adpress_settings');
$gateway = array(
    'username' => $settings['paypal_username'],
    'password' => $settings['paypal_password'],
    'signature' => $settings['paypal_signature'],
    'version' => '84.0',
    'payment_action' => 'Sale',
    'payment_amount' => $amount,
    'currency' => $settings['currency'],
    'return_url' => 'http://localhost/devpress/wp-admin/admin.php?page=adpress-paypal_redirect&action=success',
    'cancel_url' => 'http://localhost/devpress/wp-admin/admin.php?page=adpress-paypal_redirect&action=cancel'
);

$paypal = new wp_adpress_paypal($gateway, true);
switch ( $action ) {
    case '':
    default:
        $set = $paypal->doExpressCheckout();
        if ( $set ) {
            $title = 'Redirecting to PayPal';
            $info = 'Redirecting you to the PayPal Website; if you are not redirect, <a href="' . $set . '" id="redirect_url">click here</a>';
        } else {
            $title = 'Processing Problem';
            $info = 'Some problem happened, and we cannot process the payment. Please contact the Website administator';
        }
        break;
    case 'success':
        $token = $_GET['token'];
        $payer_id = $_GET['PayerID'];
        $paypal = new wp_adpress_paypal($gateway, true);
        if ( $paypal->processPayment($token, $payer_id) ) {
            $title = 'Payment Processed';
            $info = 'Your payment was processed, and your Ad request was sent.';
        } else {
            $title = 'Processing Problem';
            $info = 'Some problem happened, and we cannot process the payment. Please contact the Website administator';
        }
        break;
    case 'cancel':
        $title = 'Payment Canceled';
        $info = 'Payment Canceled';
        break;
}
?>
<div clas="wrap">
    <h2><?php echo $title; ?></h2>
    <p class="info">
        <?php echo $info; ?>
    </p>
</div>