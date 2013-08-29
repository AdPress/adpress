<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}
if (!class_exists('wp_adpress_muadmin')) {
    class wp_adpress_muadmin
    {
        function __construct()
        {
            /*
            * 1. Admin Menu
            */
            add_action('network_admin_menu', array(&$this, 'admin_menu'));

            /*
            * 2. Load Scripts and Styles
            */
            add_action('admin_print_scripts', array(&$this, 'load_scripts'));
            add_action('admin_print_styles', array(&$this, 'load_styles'));
        }

        public function admin_menu()
        {
            global $adpress_mu_main;
            global $adpress_mu_requests;
            global $adpress_mu_running;
            global $adpress_mu_ad;

            // Main Page
            $adpress_mu_main = add_menu_page('AdPress | Main', 'AdPress', 'manage_options', 'adpress-mu-main', array(&$this, 'menu_router'), ADPRESS_URLPATH . 'admin/files/img/icons/icon.png');

            // Requests Page
            $adpress_mu_requests = add_submenu_page('adpress-mu-main', 'AdPress | Ads Requests', 'Ads Requests', 'manage_options', 'adpress-mu-requests', array(&$this, 'menu_router'));

            // Running Page
            $adpress_mu_running = add_submenu_page('adpress-mu-main', 'AdPress | Running Ads', 'Running Ads', 'manage_options', 'adpress-mu-running', array(&$this, 'menu_router'));

            // Stats Page
            $adpress_mu_ad = add_submenu_page('adpress-pages', 'AdPress | Ad Stats', 'Ad Stats', 'manage_options', 'adpress-mu-ad', array(&$this, 'menu_router'));
        }

        public function menu_router()
        {
            // Current Screen
            global $current_screen;
            $screen_id = str_replace('-network', '', $current_screen->id);

            // Pages
            global $adpress_mu_main;
            global $adpress_mu_requests;
            global $adpress_mu_running;
            global $adpress_mu_ad;

            switch ($screen_id)
            {
                case $adpress_mu_main:
                    require_once('mu-pages/main.php');
                    break;
                case $adpress_mu_requests:
                    require_once('mu-pages/requests.php');
                    break;
                case $adpress_mu_running:
                    require_once('mu-pages/running.php');
                    break;
                case $adpress_mu_ad:
                    require_once('mu-pages/ad.php');
                    break;
            }
        }

        public function load_scripts()
        {
            // Current Screen
            global $current_screen;
            $screen_id = str_replace('-network', '', $current_screen->id);

            // Pages
            global $adpress_mu_main;
            global $adpress_mu_requests;
            global $adpress_mu_running;
            global $adpress_mu_ad;

            switch ($screen_id)
            {
                case $adpress_mu_main:
                    break;
                case $adpress_mu_requests:
                    wp_enqueue_script('wp_adpress_admin', ADPRESS_URLPATH . 'admin/files/js/admin.js');
                    break;
                case $adpress_mu_running:
                    wp_enqueue_script('wp_adpress_admin', ADPRESS_URLPATH . 'admin/files/js/admin.js');
                    break;
                case $adpress_mu_ad:
                    wp_enqueue_script('wp_adpress_ad_stats', ADPRESS_URLPATH . 'admin/files/js/ad_stats.js');
                    wp_enqueue_script('wp_adpress_excanvas', ADPRESS_URLPATH . 'admin/files/js/plugins/excanvas.js');
                    wp_enqueue_script('wp_adpress_flot', ADPRESS_URLPATH . 'admin/files/js/plugins/jquery.flot.js');
                    if (isset($_GET['id'])) {
                        $data = wp_adpress_ads::load_data((int)$_GET['id']);
                        wp_localize_script('wp_adpress_ad_stats', 'adpress_stats', $data);
                    }
                    break;
            }

        }

        public function load_styles()
        {
            // Current Screen
            global $current_screen;
            $screen_id = str_replace('-network', '', $current_screen->id);

            // Pages
            global $adpress_mu_main;
            global $adpress_mu_requests;
            global $adpress_mu_running;
            global $adpress_mu_ad;

            switch ($screen_id)
            {
                case $adpress_mu_main:
                    wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                    wp_enqueue_style('wp_adpress_general', ADPRESS_URLPATH . 'admin/files/css/admin.css');
                    break;
                case $adpress_mu_requests:
                    wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                    wp_enqueue_style('wp_adpress_general', ADPRESS_URLPATH . 'admin/files/css/admin.css');
                    break;
                case $adpress_mu_running:
                    wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                    wp_enqueue_style('wp_adpress_general', ADPRESS_URLPATH . 'admin/files/css/admin.css');
                    break;
                case $adpress_mu_ad:
                    wp_enqueue_style('wp_adpress_reset', ADPRESS_URLPATH . 'admin/files/css/reset.css');
                    wp_enqueue_style('wp_adpress_ad_stats', ADPRESS_URLPATH . 'admin/files/css/ad_stats.css');
                    break;
            }
        }
    }
}