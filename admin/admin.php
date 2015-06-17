<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

if (!class_exists('wp_adpress_admin')) {
    /**
     * Admin Start Class
     * @package Admin
     * @subpackage Starter
     */
    class wp_adpress_admin
    {

        function __construct()
        {
            /*
             * 1. Admin Menu
             */
            add_action('admin_menu', array(&$this, 'admin_menu'), 10);

            /*
             * 2. Load Scripts and Styles
             */
            add_action('admin_print_scripts', array(&$this, 'load_scripts'));
            add_action('admin_print_styles', array(&$this, 'load_styles'));

            /*
             * 3. Generate Settings and Template Forms
             */
            add_action('admin_init', array(&$this, 'settings_form'));

            /*
             * 4. Load Translation
             */
            add_action('admin_init', array(&$this, 'i18n'));

            /*
             * 5. Load Admin bar notifications
             */
            add_action('admin_bar_menu', array(&$this, 'admin_bar'), 100);
        }

        /**
         * Adds the Admin Menu to the WordPress Dashboard
         */
        public function admin_menu()
        {
            // Current Blog Id
            global $blog_id;
            $blog = new wp_adpress_mu($blog_id);

            // Pages Slugs
            global $adpress_page_campaigns;
            global $adpress_page_addcampaign;
            global $adpress_page_adsrequests;
            global $adpress_page_settings;
            global $adpress_page_available;
            global $adpress_page_purchases;
            global $adpress_page_purchase;
            global $adpress_page_ad;
            global $adpress_page_paypal;
            global $adpress_page_register_ad;
            global $adpress_page_payments;
            global $adpress_page_addons;
            global $adpress_page_checkout_success;
            global $adpress_page_checkout_failure;
            global $adpress_page_checkout_cancel;

            /*
             * Admin View
             */
            if ($blog->get_admin_panel_status() === 'false' || is_super_admin()) {
                // Create a TOP Menu
                $adpress_page_campaigns = add_menu_page('AdPress | Campaigns List', 'AdPress', 'manage_options', 'adpress-campaigns', array(&$this, 'menu_router'), ADPRESS_URLPATH . 'admin/files/img/icons/icon.png');

                // Submenus
                $adpress_page_campaigns = add_submenu_page('adpress-campaigns', 'AdPress | Campaigns List', 'Campaigns', 'manage_options', 'adpress-campaigns', array(&$this, 'menu_router'));
                add_action("load-$adpress_page_campaigns", array(&$this, 'help'));

                $adpress_page_addcampaign = add_submenu_page('adpress-campaigns', 'AdPress | Add Campaign', 'Add Campaign', 'manage_options', 'adpress-inccampaign', array(&$this, 'menu_router'));
                add_action("load-$adpress_page_addcampaign", array(&$this, 'help'));

                $adpress_page_adsrequests = add_submenu_page('adpress-campaigns', 'AdPress | Ads Requests', 'Ads Requests', 'manage_options', 'adpress-adsrequests', array(&$this, 'menu_router'));
                add_action("load-$adpress_page_adsrequests", array(&$this, 'help'));

                $adpress_page_settings = add_submenu_page('adpress-campaigns', 'AdPress | Settings', 'Settings', 'manage_options', 'adpress-settings', array(&$this, 'menu_router'));
                add_action("load-$adpress_page_settings", array(&$this, 'help'));
				
				do_action( 'wp_adpress_admin_settings_menu' );

                $adpress_page_addons = add_submenu_page('adpress-campaigns', 'AdPress | Add-ons', 'Add-ons', 'manage_options', 'adpress-addons', array(&$this, 'menu_router'));
                add_action("load-$adpress_page_addons", array(&$this, 'help'));
            }

            /*
             * Client View
             */

            // Create a TOP Menu
            $adpress_page_available = add_menu_page('AdPress Dashboard', 'AdPress', 'adpress_client_menu', 'adpress-client', array(&$this, 'menu_router'), ADPRESS_URLPATH . 'admin/files/img/icons/icon.png');
            add_action("load-$adpress_page_available", array(&$this, 'help'));

            // Submenus
            $adpress_page_available = add_submenu_page('adpress-client', 'AdPress | Available Ads', 'Available Ads', 'adpress_client_menu', 'adpress-client', array(&$this, 'menu_router'));
            add_action("load-$adpress_page_available", array(&$this, 'help'));

            $adpress_page_purchases = add_submenu_page('adpress-client', 'AdPress | Purchased Ads', 'Purchases', 'adpress_client_menu', 'adpress-purchases', array(&$this, 'menu_router'));
            add_action("load-$adpress_page_purchases", array(&$this, 'help'));
            // Independant pages
            $adpress_page_purchase = add_submenu_page('adpress-pages', 'AdPress | Purchase Ad', 'Purchase Ad', 'adpress_client_menu', 'adpress-ad_purchase', array(&$this, 'menu_router'));
            add_action("load-$adpress_page_purchase", array(&$this, 'help'));

            $adpress_page_register_ad = add_submenu_page('adpress-pages', 'AdPress | Processing your purchase', 'Payment', 'adpress_client_menu', 'adpress-register_ad', array(&$this, 'menu_router'));
            $adpress_page_paypal = add_submenu_page('adpress-pages', 'AdPress | Processing your purchase', 'Payment', 'adpress_client_menu', 'adpress-paypal', array(&$this, 'menu_router'));

            // Checkout Pages
            $adpress_page_checkout_success = add_submenu_page('adpress-pages', 'AdPress | Checkout | Success', 'Success', 'adpress_client_menu', 'adpress-checkout-success', array(&$this, 'menu_router'));
            add_action("load-$adpress_page_checkout_success", array(&$this, 'help'));

            $adpress_page_checkout_failure = add_submenu_page('adpress-pages', 'AdPress | Checkout | Failure', 'Failure', 'adpress_client_menu', 'adpress-checkout-failure', array(&$this, 'menu_router'));
            add_action("load-$adpress_page_checkout_failure", array(&$this, 'help'));

            $adpress_page_checkout_cancel = add_submenu_page('adpress-pages', 'AdPress | Checkout | Cancel', 'Cancel', 'adpress_client_menu', 'adpress-checkout-cancel', array(&$this, 'menu_router'));
            add_action("load-$adpress_page_checkout_cancel", array(&$this, 'help'));

            /*
             * Shared View
             */
            $adpress_page_ad = add_submenu_page('adpress-pages', 'AdPress | Purchase Ad', 'Purchase Ad', 'adpress_client_menu', 'adpress-ad', array(&$this, 'menu_router'));
            add_action("load-$adpress_page_ad", array(&$this, 'help'));
        }

        /**
         * Admin pages routing engine
         */
        public function menu_router()
        {
            // Current screen
            global $current_screen;
            // Pages Slugs
            global $adpress_page_campaigns;
            global $adpress_page_addcampaign;
            global $adpress_page_adsrequests;
            global $adpress_page_settings;
            global $adpress_page_payments;
            global $adpress_page_available;
            global $adpress_page_purchases;
            global $adpress_page_purchase;
            global $adpress_page_ad;
            global $adpress_page_register_ad;
            global $adpress_page_paypal;
            global $adpress_page_addons;
            global $adpress_page_checkout_success;
            global $adpress_page_checkout_failure;
            global $adpress_page_checkout_cancel;


            switch ($current_screen->id) {
                /*
                 * Admin View
                 */
                // Campaigns Page
            case $adpress_page_campaigns:
            default:
                require_once('pages/campaigns.php');
                break;
                // Add Campaign Page
            case $adpress_page_addcampaign:
                require_once('pages/add_campaign.php');
                break;
                // Ad Requests and Running Ads page
            case $adpress_page_adsrequests:
                require_once('pages/ads_requests.php');
                break;
                // AdPress Settings
            case $adpress_page_settings:
                require_once('pages/settings.php');
                break;
            case $adpress_page_addons:
                require_once('pages/addons.php');
                break;
                /*
                 * Client View
                 */
                // Available Ads page
            case $adpress_page_available:
                require_once('pages/client/available.php');
                break;
                // Purchases Page
            case $adpress_page_purchases:
                require_once('pages/client/purchases.php');
                break;
                // Make a purchase Page
            case $adpress_page_purchase:
                require_once('pages/client/ad_purchase.php');
                break;
            case $adpress_page_register_ad:
                require_once('pages/client/register_ad.php');
                break;
            case $adpress_page_paypal:
                require_once ('pages/client/paypal.php');
                break;
            case $adpress_page_checkout_success:
                require_once ( ADPRESS_ABSPATH . 'inc/views/checkout/success_page.php' );
                break;
            case $adpress_page_checkout_failure:
                require_once ( ADPRESS_ABSPATH . 'inc/views/checkout/failure_page.php' );
                break;	
            case $adpress_page_checkout_cancel:
                require_once( ADPRESS_ABSPATH . 'inc/views/checkout/cancel_page.php' );
                break;
                /*
                 * Shared View
                 */
            case $adpress_page_ad:
                require_once('pages/ad.php');
                break;
            }
        }

        /**
         * Load Scripts for specific pages
         */
        public function load_scripts()
        {
            // Current screen
            global $current_screen;
            // Pages Slugs
            global $adpress_page_campaigns;
            global $adpress_page_addcampaign;
            global $adpress_page_adsrequests;
            global $adpress_page_settings;
            global $adpress_page_available;
            global $adpress_page_purchases;
            global $adpress_page_purchase;
            global $adpress_page_ad;
            global $adpress_page_paypal;
            global $adpress_page_addons;
            global $adpress_page_checkout_success;
            global $adpress_page_checkout_failure;
            global $adpress_page_checkout_cancel;

            switch ($current_screen->id) {
            case $adpress_page_campaigns:
                wp_enqueue_script('wp_adpress_admin', ADPRESS_URLPATH . 'admin/files/js/admin.js');
                break;
            case $adpress_page_addcampaign:
                wp_enqueue_script('wp_adpress_addesigner', ADPRESS_URLPATH . 'admin/files/js/ad_designer.js');
                wp_enqueue_media();
                break;
            case $adpress_page_purchase:
                wp_enqueue_script('wp_adpress_redirect', ADPRESS_URLPATH . 'admin/files/js/redirect.js');
                wp_enqueue_script('wp_adpress_ad_purchase', ADPRESS_URLPATH . 'admin/files/js/ad_purchase.js');
                wp_enqueue_media();
                break;
            case $adpress_page_paypal:
                wp_enqueue_script('wp_adpress_redirect', ADPRESS_URLPATH . 'admin/files/js/redirect.js');
                break;
            case $adpress_page_adsrequests:
                wp_enqueue_script('wp_adpress_admin', ADPRESS_URLPATH . 'admin/files/js/admin.js');
                break;
            case $adpress_page_settings:
                wp_enqueue_script('textarea_plugin', ADPRESS_URLPATH . 'admin/files/js/plugins/jquery.textarea.js');
                wp_enqueue_script('wp_adpress_settings', ADPRESS_URLPATH . 'admin/files/js/settings.js');
                wp_enqueue_script('wp_adpress_admin', ADPRESS_URLPATH . 'admin/files/js/admin.js');
                break;
            case $adpress_page_addons:
                break;
            case $adpress_page_ad:
                wp_enqueue_script('wp_adpress_ad_stats', ADPRESS_URLPATH . 'admin/files/js/ad_stats.js');
                wp_enqueue_script('wp_adpress_excanvas', ADPRESS_URLPATH . 'admin/files/js/plugins/excanvas.js');
                wp_enqueue_script('wp_adpress_flot', ADPRESS_URLPATH . 'admin/files/js/plugins/jquery.flot.js');
                if (isset($_GET['id'])) {
                    $data = wp_adpress_ads::load_data((int)$_GET['id']);
                    wp_localize_script('wp_adpress_ad_stats', 'adpress_stats', $data);
                }
                break;
            case $adpress_page_purchases:
                wp_enqueue_script('wp_adpress_admin', ADPRESS_URLPATH . 'admin/files/js/admin.js');
                break;
            }
        }

        /**
         * Load Styles for specific pages
         */
        public function load_styles()
        {
            // Current screen
            global $current_screen;
            // Pages Slugs
            global $adpress_page_campaigns;
            global $adpress_page_addcampaign;
            global $adpress_page_adsrequests;
            global $adpress_page_settings;
            global $adpress_page_available;
            global $adpress_page_purchases;
            global $adpress_page_purchase;
            global $adpress_page_ad;
            global $adpress_page_register_ad;
            global $adpress_page_paypal;
            global $adpress_page_addons;
            global $adpress_page_checkout_success;
            global $adpress_page_checkout_failure;
            global $adpress_page_checkout_cancel;

            switch ($current_screen->id) {
            case $adpress_page_campaigns:
                wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                wp_enqueue_style('wp_adpress_general', ADPRESS_URLPATH . 'admin/files/css/admin.css');
                break;
            case $adpress_page_addcampaign:
                wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                wp_enqueue_style('wp_adpress_ad_designer', ADPRESS_URLPATH . 'admin/files/css/ad_designer.css');
                break;
            case $adpress_page_available:
                wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                wp_enqueue_style('wp_adpress_available', ADPRESS_URLPATH . 'admin/files/css/available.css');
                break;
            case $adpress_page_purchases:
                wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                wp_enqueue_style('wp_adpress_general', ADPRESS_URLPATH . 'admin/files/css/admin.css');
                break;
            case $adpress_page_adsrequests:
                wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                wp_enqueue_style('wp_adpress_general', ADPRESS_URLPATH . 'admin/files/css/admin.css');
                break;
            case $adpress_page_purchase:
                wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                wp_enqueue_style('wp_adpress_ad_purchase', ADPRESS_URLPATH . 'admin/files/css/ad_purchase.css');
                break;
            case $adpress_page_settings:
                wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
				wp_enqueue_style('wp_adpress_settings', ADPRESS_URLPATH . 'admin/files/css/settings.css');
				wp_enqueue_style('codemirror_plugin', ADPRESS_URLPATH . 'admin/files/css/plugins/codemirror.css');
                break;
            case $adpress_page_addons:
                wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                wp_enqueue_style('wp_adpress_settings', ADPRESS_URLPATH . 'admin/files/css/settings.css');
                break;
            case $adpress_page_ad:
                wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                wp_enqueue_style('wp_adpress_ad_stats', ADPRESS_URLPATH . 'admin/files/css/ad_stats.css');
                break;
            case $adpress_page_register_ad:
                wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                break;
            case $adpress_page_paypal:
                wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                break;
            case $adpress_page_checkout_success:
                wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                wp_enqueue_style('wp_adpress_general', ADPRESS_URLPATH . 'admin/files/css/admin.css');
                break;
            case $adpress_page_checkout_failure:
                wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                wp_enqueue_style('wp_adpress_general', ADPRESS_URLPATH . 'admin/files/css/admin.css');
                break;
            case $adpress_page_checkout_cancel:
                wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                wp_enqueue_style('wp_adpress_general', ADPRESS_URLPATH . 'admin/files/css/admin.css');
                break;
            }
        }

        /**
         * Register the settings fields
         */
        public function settings_form()
        {
            // AdPress Settings
            register_setting('adpress_settings', 'adpress_settings', 'wp_adpress_forms::validate');
            // -- General Settings
            add_settings_section('general_section', 'General settings', 'wp_adpress_forms::description', 'adpress_settings_form_general', 'General Settings');
            add_settings_field('currency', 'Currency', 'wp_adpress_forms::textbox', 'adpress_settings_form_general', 'general_section', array('currency', 'adpress_settings'));
            add_settings_field('time_format', 'Time Format', 'wp_adpress_forms::textbox', 'adpress_settings_form_general', 'general_section', array('time_format', 'adpress_settings'));
            add_settings_field('adminbar', 'Admin Bar', 'wp_adpress_forms::checkbox', 'adpress_settings_form_general', 'general_section', array('adminbar', 'adpress_settings'));
            add_settings_field('debug_mode', 'Debugging Mode', 'wp_adpress_forms::checkbox', 'adpress_settings_form_general', 'general_section', array('debug_mode', 'adpress_settings'));
            add_settings_field('smart_rewrite', 'Smart Rewrite', 'wp_adpress_forms::checkbox', 'adpress_settings_form_general', 'general_section', array('smart_rewrite', 'adpress_settings'));
            add_settings_field('sandbox_mode', 'Sandbox Mode', 'wp_adpress_forms::checkbox', 'adpress_settings_form_general', 'general_section', array('sandbox_mode', 'adpress_settings'));
            add_settings_field('campaign_edit', 'Edit Active Campaigns', 'wp_adpress_forms::checkbox', 'adpress_settings_form_general', 'general_section', array('campaign_edit', 'adpress_settings'));
            // -- Client Settings
            add_settings_section('client_access', 'Client Access', 'wp_adpress_forms::description', 'adpress_settings_form_client', 'Client Access');
            add_settings_field('client_roles', 'Client Roles', 'wp_adpress_forms::roles_check', 'adpress_settings_form_client', 'client_access', array('client_roles', 'adpress_settings'));
            add_settings_field('auto_approve', 'Auto Approve', 'wp_adpress_forms::checkbox', 'adpress_settings_form_client', 'client_access', array('auto_approve', 'adpress_settings'));
            // -- PayPal Settings
            add_settings_section('paypal_section', 'PayPal Settings', 'wp_adpress_forms::description', 'adpress_settings_form_paypal', 'PayPal Settings');
            add_settings_field('paypal', 'Enable PayPal', 'wp_adpress_forms::checkbox', 'adpress_settings_form_paypal', 'paypal_section', array('paypal', 'adpress_settings'));
            add_settings_field('paypal_testmode', 'SandBox Mode', 'wp_adpress_forms::checkbox', 'adpress_settings_form_paypal', 'paypal_section', array('paypal_testmode', 'adpress_settings'));
            add_settings_field('paypal_refund', 'Enable Refunds', 'wp_adpress_forms::checkbox', 'adpress_settings_form_paypal', 'paypal_section', array('paypal_refund', 'adpress_settings'));
            add_settings_field('paypal_username', 'User Name', 'wp_adpress_forms::textbox', 'adpress_settings_form_paypal', 'paypal_section', array('paypal_username', 'adpress_settings'));
            add_settings_field('paypal_password', 'Password', 'wp_adpress_forms::passwordbox', 'adpress_settings_form_paypal', 'paypal_section', array('paypal_password', 'adpress_settings'));
            add_settings_field('paypal_signature', 'Signature', 'wp_adpress_forms::passwordbox', 'adpress_settings_form_paypal', 'paypal_section', array('paypal_signature', 'adpress_settings'));
            // -- Purchase History
            add_settings_section('history_section', 'Purchase History', 'wp_adpress_forms::description', 'adpress_settings_form_history', 'History Settings');
            add_settings_field('history', 'Enable History', 'wp_adpress_forms::checkbox', 'adpress_settings_form_history', 'history_section', array('history', 'adpress_settings'));
            add_settings_field('history_reset', 'Delete History', 'wp_adpress_forms::button', 'adpress_settings_form_history', 'history_section', array('value' => 'Remove History', 'action' => 'del_history'));
            // -- Payments Log 
            //add_settings_section('payments_section', 'Payments Log', 'wp_adpress_forms::description', 'adpress_settings_form_payments', 'Payments Log Settings');
            //add_settings_field('payments_log_enabled', 'Enable Logging', 'wp_adpress_forms::checkbox', 'adpress_settings_form_payments', 'payments_section', array('payments_log_enabled', 'adpress_settings'));
            //add_settings_field('payments_log_delete', 'Delete Log', 'wp_adpress_forms::button', 'adpress_settings_form_payments', 'payments_section', array('value' => 'Remove Log', 'action' => 'del_payments_log'));

            // Gateways
            register_setting('adpress_gateways_settings', 'adpress_gateways_settings', 'wp_adpress_forms::validate');
            add_settings_section('gateways_general_section', 'General Settings', 'wp_adpress_forms::description', 'adpress_gateways_form_general');
            add_settings_field('installed_gateways', 'Payment Gateways', 'wp_adpress_forms::list_gateways', 'adpress_gateways_form_general','gateways_general_section' );
            add_settings_field('default_gateway', 'Default Gateway', 'wp_adpress_forms::select_default_gateway', 'adpress_gateways_form_general','gateways_general_section' );

            // License Settings
            register_setting('adpress_license_settings', 'adpress_license_settings', 'wp_adpress_forms::validate');
            add_settings_section('license_section', 'License', 'wp_adpress_forms::description', 'adpress_license_form');
            add_settings_field('license_username', 'Username', 'wp_adpress_forms::textbox', 'adpress_license_form', 'license_section', array('license_username', 'adpress_license_settings'));
            add_settings_field('license_key', 'License Key', 'wp_adpress_forms::textbox', 'adpress_license_form', 'license_section', array('license_key', 'adpress_license_settings'));

        }

        /**
         * Internationalization
         */
        public function i18n()
        {
            load_plugin_textdomain('wp-adpress', false, basename(dirname(__FILE__)) . '/languages/');
        }

        /**
         * Draw the Admin Bar
         * @global object $wp_admin_bar
         * @return null
         */
        public function admin_bar()
        {
            global $wp_admin_bar;
            $settings = get_option('adpress_settings');
            if (!is_super_admin() || !is_admin_bar_showing() || !isset($settings['adminbar']))
                return;
            // Get pending requests
            $waiting = count(wp_adpress_ads::list_ads('waiting', 'id'));
            if ($waiting === 0) {
                $waiting = '';
            } else {
                $waiting = ' | ' . $waiting;
            }
            // Root Menu
            $wp_admin_bar->add_menu(array(
                'id' => 'adpress_bar',
                'title' => __('AdPress' . $waiting, 'wp-adpress' ),
                'href' => 'admin.php?page=adpress-adsrequests',
                'meta' => array('html' => '')
            ));
            // New Requests Menu
            $wp_admin_bar->add_menu(array(
                'parent' => 'adpress_bar',
                'id' => 'adpress_new_requests',
                'title' => __('New Requests ' . $waiting, 'wp-adpress' ),
                'href' => 'admin.php?page=adpress-adsrequests'
            ));
            // Add Campaign Menu
            $wp_admin_bar->add_menu(array(
                'parent' => 'adpress_bar',
                'id' => 'adpress_new_campaign',
                'title' => __('New Campaign', 'wp-adpress' ),
                'href' => 'admin.php?page=adpress-addcampaign'
            ));
            // Campaigns Menu
            $wp_admin_bar->add_menu(array(
                'parent' => 'adpress_bar',
                'id' => 'adpress_campaigns',
                'title' => __('Campaigns', 'wp-adpress' ),
                'href' => 'admin.php?page=adpress-campaigns'
            ));
            // Settings Menu
            $wp_admin_bar->add_menu(array(
                'parent' => 'adpress_bar',
                'id' => 'adpress_settings',
                'title' => __('Settings', 'wp-adpress' ),
                'href' => 'admin.php?page=adpress-settings'
            ));
        }

        /**
         * Contextual Help
         */
        public function help()
        {
            require_once( 'help.php' );
        }


    }
}
