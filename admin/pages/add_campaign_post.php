<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}
$result = false;
/**
 * Prepare the campaign arguments, we are assuming that validation is worked
 * on the client side and all input is correct.
 */
/**
 * === Settings ===
 */
if (isset($_POST['campaign_state'])) {
    $state = 'active';
} else {
    $state = 'inactive';
}
$campaign_settings = array(
    'name' => stripslashes_deep($_POST['campaign_name']),
    'description' => stripslashes_deep($_POST['campaign_description']),
    'state' => $state
);

/**
 * === Ad Definition ===
 */
$ad_definition = array();
// Shared Properties
$ad_definition['number'] = (int)$_POST['ads_number'];
$ad_definition['price'] = (int)$_POST['contract_price'];

// Ad CTA
if (isset($_POST['ad_cta'])) {
    $ad_definition['cta_url'] = $_POST['target_url'];
    $ad_definition['cta_fill'] = $_POST['cta_fill'];
}

// Ad Type
if ($_POST['ad_type'] === 'for_image') {
    $ad_definition['type'] = 'image';
    $ad_definition['size'] = array('height' => (int)$_POST['ad_image_height'], 'width' => (int)$_POST['ad_image_width']);
    $ad_definition['columns'] = (int)$_POST['ad_image_columns'];
    if (isset($_POST['ad_cta'])) {
        $ad_definition['cta_img'] = $_POST['cta_image'];
    }
} else if ($_POST['ad_type'] === 'for_link') {
    $ad_definition['type'] = 'link';
    $ad_definition['length'] = (int)$_POST['ad_link_length'];
    if (isset($_POST['ad_cta'])) {
        $ad_definition['cta_text'] = $_POST['cta_text'];
    }
} else if ($_POST['ad_type'] === 'for_flash') {
    $ad_definition['type'] = 'flash';
    $ad_definition['size'] = array('height' => (int)$_POST['ad_banner_height'], 'width' => (int)$_POST['ad_banner_width']);
    $ad_definition['columns'] = (int)$_POST['ad_banner_columns'];
    if (isset($_POST['ad_cta'])) {
        $ad_definition['cta_banner'] = $_POST['cta_banner'];
    }
}

// Contract Type
switch ($_POST['contract_type']) {
    case 'for_clicks':
        $ad_definition['contract'] = 'clicks';
        $ad_definition['clicks'] = (int)$_POST['contract_type_clicks'];
        break;
    case 'for_pageviews':
        $ad_definition['contract'] = 'pageviews';
        $ad_definition['pageviews'] = (int)$_POST['contract_type_pageviews'];
        break;
    case 'for_duration':
        $ad_definition['contract'] = 'duration';
        $ad_definition['duration'] = (int)$_POST['contract_type_duration'];
        break;
}

// Ad rotation
if (isset($_POST['ad_rotation'])) {
    $ad_definition['rotation'] = $_POST['rotation_number'];
} else {
    $ad_definition['rotation'] = null;
}
/**
 * Create a new Campaign object
 */
if (isset($_POST['edit_campaign'])) {
    $edit_campaign = new wp_adpress_campaign((int)$_POST['edit_campaign']);
    $edit_campaign->settings = $campaign_settings;
    $edit_campaign->ad_definition = $ad_definition;
    $result = $edit_campaign->save();
} else {
    $new_campaign = new wp_adpress_campaign(null, $campaign_settings, $ad_definition);
    $result = $new_campaign->save();
}
wp_adpress::display_log($_POST);
wp_adpress::display_message('Campaign Saved', 'Campaign Saved', __('<p>You can return to the <a href="admin.php?page=adpress-campaigns">Campaigns page</a> or <a href="admin.php?page=adpress-addcampaign">add another Campaign</a>.</p>', 'wp-adpress'), 'adpress-icon-campaignsaved', 'adpress-icon-request_sent');

