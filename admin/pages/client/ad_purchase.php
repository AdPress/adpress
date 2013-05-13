<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

$current_user = wp_get_current_user();
global $pid;
$pid = $current_user->ID;
if (isset($_POST['submit_purchase'])) {
    $blog_switch = get_option('adpress_blogswitch_' . $pid);
    if ($blog_switch === 'true') {
        $blog_id = get_option('adpress_blogswitch_newid_' . $pid);
        switch_to_blog($blog_id);
    }
    // Redirect.php
    // Update options
    update_option('adpress_temp_purchase_' . $pid, $_POST);
    update_option('adpress_temp_valid_' . $pid, 'no');
    // Detect PayPal Settings
    $settings = get_option('adpress_settings');
    $paypal = isset($settings['paypal']);
    // Create redirect URL
    if ($paypal && !current_user_can('manage_options')) {
        // Create PayPal Payment link
        $gateway = array(
            'username' => $settings['paypal_username'],
            'password' => $settings['paypal_password'],
            'signature' => $settings['paypal_signature'],
            'version' => '84.0',
            'payment_action' => 'Sale',
            'payment_amount' => $_POST['price'],
            'currency' => $settings['currency'],
            'return_url' => ADPRESS_ADMINPATH . 'admin.php?page=adpress-paypal&action=success',
            'cancel_url' => ADPRESS_ADMINPATH . 'admin.php?page=adpress-paypal&action=cancel'
        );
        update_option('adpress_temp_gateway_' . $pid, $gateway);
        if (isset($settings['paypal_testmode'])) {
            $test_mode = true;
        } else {
            $test_mode = false;
        }

        $paypal = new wp_adpress_paypal($gateway, $test_mode);
        $redirect_url = $paypal->doExpressCheckout();
    } else {
        // No Payment required
        update_option('adpress_temp_valid_' . $pid, 'yes');
        $redirect_url = 'admin.php?page=adpress-register_ad';
    }
    $content = '
        <p>
                Your request is being processed. If you are not redirected in
                few seconds, click this <a id="redirect_url" href="' . $redirect_url . '">link</a>.
            </p>';
    wp_adpress::display_message('Purchase Ad', 'Processing your request', $content, 'adpress-icon-purchase', 'adpress-icon-purchase_loader');
} else {
    if (isset($_GET['cid'])) {
        if (isset($_GET['blogid']) && is_plugin_active_for_network(ADPRESS_BASENAME)) {
            // Save the old id
            global $blog_id;
            $old_id = $blog_id;
           update_option('adpress_blogswitch_oldid_' . $pid, $old_id);
            // Save the new id
            $new_id = (int)$_GET['blogid'];
            update_option('adpress_blogswitch_newid_' . $pid, $new_id);
            // Switch to the new blog
            update_option('adpress_blogswitch_' . $pid, 'true');
            switch_to_blog($new_id);
        }
        $cid = (int)$_GET['cid'];
        $ad_purchase = new wp_adpress_adpurchase($cid);
        echo $ad_purchase->display_page();
    } else {
        wp_adpress::display_message('Purchase Ad', 'Campaign not found', '<p>Campaign was not found or is not available.</p>', 'adpress-icon-purchase', 'adpress-icon-flag');
    }
}
?>