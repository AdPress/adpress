<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}
$current_user = wp_get_current_user();
global $pid;
$pid = $current_user->ID;
$blog_switch = get_option('adpress_blogswitch_' . $pid);
if ($blog_switch === 'true') {
    $blog_id = get_option('adpress_blogswitch_newid_' . $pid);
    switch_to_blog($blog_id);
}

$valid = get_option('adpress_temp_valid_' . $pid);
if ($valid === 'yes') {
    $post = get_option('adpress_temp_purchase_' . $pid);
    wp_adpress_adpurchase::process_post($post);
} else {
    wp_adpress::display_message('Purchase Ad', 'Request not valid', '<p>This request is no longer valid, please restart the purchase again.</p>', 'adpress-icon-purchase', 'adpress-icon-flag');
}
?>