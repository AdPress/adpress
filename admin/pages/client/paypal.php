<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

$current_user = wp_get_current_user();
global $pid;
$pid = $current_user->ID;
if (isset($_GET['action'])) {

    // WP-MU Blog Switch
    $blog_switch = get_option('adpress_blogswitch_' . $pid);
    if ($blog_switch === 'true') {
        $blog_id = get_option('adpress_blogswitch_newid_' . $pid);
        switch_to_blog($blog_id);
    }

    switch ($_GET['action']) {
        case 'cancel':
        default:
            wp_adpress::display_message('Purchase Ad', 'Payment Canceled', '<p>You have canceled payment.</p>', 'adpress-icon-purchase', 'adpress-icon-flag');
            break;
        case 'success':
            $settings = get_option('adpress_settings');
            $gateway = get_option('adpress_temp_gateway_' . $pid);
            if (isset($settings['paypal_testmode'])) {
                $test_mode = true;
            } else {
                $test_mode = false;
            }
            $paypal = new wp_adpress_paypal($gateway, $test_mode);
            $token = $_GET['token'];
            $payer_id = $_GET['PayerID'];
            $result = $paypal->processPayment($token, $payer_id);
            if ($result) {
                update_option('adpress_temp_valid_' . $pid, 'yes');
                update_option('adpress_temp_paypal_' . $pid, $result);
                wp_adpress::display_message('Purchase Ad', 'Payment Processed', '<p>Your payment was processed successfully. Click here to <a id="redirect_url" href="admin.php?page=adpress-register_ad">continue</a>.</p>', 'adpress-icon-purchase', 'adpress-icon-request_sent');
            } else {
                wp_adpress::display_message('Purchase Ad', 'Invalid Payment', '<p>Payment could not be processed.</p>', 'adpress-icon-purchase', 'adpress-icon-flag');
            }
            break;
    }
} else {
    wp_adpress::display_message('Purchase Ad', 'Invalid Call', '<p>You cannot call this page directly.</p>', 'adpress-icon-purchase', 'adpress-icon-flag');
}

