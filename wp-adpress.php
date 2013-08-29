<?php

/**
 * AdPress - WordPress Ads Management Plug-in
 *
 * @author Abid Omar
 * @version 0.9
 * @package Main
 */
/*
  Plugin Name: AdPress | Ads Manager
  Plugin URI: http://wpadpress.com
  Description: AdPress is a fully featured Ads Manager for WordPress with client management, PayPal Integration, analytics and Multi-site support
  Author: Abid Omar
  Author URI: http://omarabid.com
  Version: 0.9.5
  Text Domain: wp-adpress
 */


if (!class_exists('wp_adpress')) {

    /**
     * AdPress Starter Class
     *
     * This is the plug-in Backbone class. This class is used to Initialize,
     * Install, Activate, Deactivate and Uninstall the plug-in.
     */
    class wp_adpress
    {

        /**
         * Plug-in Version
         * @var string
         */
        public $version = "0.9.5";

        /**
         * Minimal WordPress version required
         * @var string
         */
        public $wp_version = "3.0";

        /**
         * Plugin Settings
         * @var array
         */
        public $settings;

        function __construct()
        {
            /*
             * 1. Check Plug-in requirements
             */
            if (!$this->check_requirements()) {
                return;
            }

            /*
             * 2. Define constants and load dependencies
             */
            $this->define_constants();
            $this->load_dependencies();

            /*
             * 3. Activation Hooks
             */
            register_activation_hook(__FILE__, array(&$this, 'multi_activate'));
            register_deactivation_hook(__FILE__, array(&$this, 'deactivate'));
            register_uninstall_hook(__FILE__, 'wp_adpress::uninstall');
            add_action('wpmu_new_blog', array(&$this, 'activate_new_blog'));

            /*
             * 4. Unregister Ad Hook
             */
            add_action('adpress_unregister_ad', array(&$this, 'unregister_ad'));

            /*
             * 5. Load Translation
             */
            add_action('init', array(&$this, 'i18n'));

            /*
             * 6. Load Widget
             */
            add_action('widgets_init', create_function('', 'register_widget("AdPress_Widget");'));

            /*
             * 7. Load ShortCode
             */
            add_shortcode('adpress', array(&$this, 'register_shortcode'));

            /*
             * 8. Print Ad Styles
             */
            add_action('wp_head', array(&$this, 'print_styles'));

            /*
             * 9. Run the plug-in!
             */
            add_action('plugins_loaded', array(&$this, 'start'));
        }

        /**
         * Checks that the WordPress setup meets the
         * plug-in requirements
         * @global string $wp_version
         * @return boolean
         */
        private function check_requirements()
        {
            global $wp_version;
            if (!version_compare($wp_version, $this->wp_version, '>=')) {
                add_action('admin_notices', create_function('', 'global $wpadpress; printf (\'<div id="message" class="error"><p><strong>\' . __(\'Sorry, AdPress requires WordPress %s or higher\', "ad-press" ) . \'</strong></p></div>\', $wpadpress->wp_version );'));
                return false;
            }
            return true;
        }

        /**
         * Define Global Constants for the plug-in
         */
        private function define_constants()
        {
            $this->settings = get_option('adpress_settings');
            /* [adpress/wp-adpress.php] */
            define('ADPRESS_BASENAME', plugin_basename(__FILE__));
            /* [F:\dev\devpress\wp-content\plugins\adpress] */
            define('ADPRESS_DIR', dirname(__FILE__));
            /* [adpress] */
            define('ADPRESS_FOLDER', plugin_basename(dirname(__FILE__)));
            /* [F:/dev/devpress/wp-content/plugins/adpress/] */
            define('ADPRESS_ABSPATH', trailingslashit(str_replace("\\", "/", WP_PLUGIN_DIR . '/' . plugin_basename(dirname(__FILE__)))));
            /* [http://localhost/devpress/wp-content/plugins/adpress/] */
            define('ADPRESS_URLPATH', trailingslashit(WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__))));
            /* [http://localhost/devpress/wp-admin/] */
            define('ADPRESS_ADMINPATH', get_admin_url());
        }

        /**
         * Load required files for the plug-in
         */
        private function load_dependencies()
        {
            /*
             * Admin Dependencies
             */
            if (is_admin()) {
                require_once('admin/forms.php');
                require_once('admin/admin.php');
                require_once('admin/mu-admin.php');
                require_once('inc/views/campaigns_view.php');
                require_once('inc/views/ads_requests_view.php');
                require_once('inc/views/purchases_view.php');
                require_once('inc/views/available_view.php');
                require_once('inc/views/ad_purchase_view.php');
                require_once('inc/views/settings_view.php');
                require_once('inc/views/ad_view.php');
                require_once('inc/views/mu/main.php');
            }


            /*
             * Plug-in Dependencies
             */
            require_once('inc/install.php');
            require_once('inc/campaign.php');
            require_once('inc/ad.php');
            require_once('inc/rewrite.php');
            require_once('inc/widget.php');
            require_once('inc/paypal.php');
            require_once('inc/payment.php');
            require_once('inc/history.php');
            require_once('inc/import.php');
            require_once('inc/mu.php');
            require_once('inc/roles.php');
            require_once('inc/integration.php');
        }

        /**
         * Multi-Site Activate
         * Checks if WP-MU is enabled, and a network activation is run
         */
        public function multi_activate()
        {
            global $wpdb;
            // check if it is a network activation - if so, run the activation function for each blog id
            if (function_exists('is_multisite') && is_multisite() && isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
                // Get all blog ids
                $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    $this->activate();
                }
                restore_current_blog();
                return;
            }
            $this->activate();
        }

        /**
         * Activate the plug-in
         */
        public function activate()
        {
            // Checks if the plug-in is previously installed, or run the install instance
            if (get_option('adpress_install', false) === false) {
                $this->install();
            }
        }

        /**
         * Activate AdPress for newly added blogs
         * @param integer $blog_id
         */
        public function activate_new_blog($blog_id)
        {
            if (is_plugin_active_for_network('ADPRESS_BASENAME')) {
                switch_to_blog($blog_id);
                $this->activate();
                restore_current_blog();
            }
        }

        /**
         * Deactivate the plug-in
         */
        public function deactivate()
        {

        }

        /**
         * Install the plug-in
         * @return wp_adpress_install
         */
        public function install()
        {
            $setup = new wp_adpress_install();
            return $setup;
        }

        /**
         * Uninstall the plug-in
         * @static
         * @return wp_adpress_uninstall
         */
        public static function uninstall()
        {
            require_once('inc/install.php');
            $setup = new wp_adpress_uninstall();
            return $setup;
        }

        /**
         * Internationalization
         */
        public function i18n()
        {
            load_plugin_textdomain('wp-adpress', false, basename(dirname(__FILE__)) . '/languages/');
        }

        /**
         * Register the shortcode
         * @param array $attr
         * @return string
         */
        public function register_shortcode($attr)
        {
            // Extract shortcode attributes
            shortcode_atts(array(
                'campaign' => '',
            ), $attr);
            // Display the campaign
            $campaign = new wp_adpress_campaign($attr['campaign']);
            return $campaign->display(true);
        }

        /**
         * Print Ad Styles
         */
        public function print_styles()
        {
            // Image Style
            $image_settings = get_option('adpress_image_settings');
            $image_css = $image_settings['ad_css'];
            // Flash Style
            $flash_settings = get_option('adpress_flash_settings');
            $flash_css = $flash_settings['ad_css'];
            // Link Style
            $link_settings = get_option('adpress_link_settings');
            $link_css = $link_settings['ad_css'];
            echo '<style type="text/css">';
            echo $image_css;
            echo $link_css;
            echo $flash_css;
            echo' </style>';
        }

        /**
         * Update the user roles
         */
        static function update_user_roles()
        {

        }

        /**
         * Unregister an Ad for the expire hook
         *
         * @param string $id
         */
        public function unregister_ad($id)
        {
            if (is_int($id)) {
                $id = (int)$id;
                $ad = new wp_adpress_ad($id);
                $ad->unregister_ad();
                $ad->save();
            }
        }

        /**
         * Display a page message
         *
         * @param string $page_title Page Title
         * @param string $message_title Message Title
         * @param string $message_content Message Content
         * @param string $page_icon Page Icon name
         * @param string $message_icon Box Icon name
         * @return string HTML Code
         */
        static function display_message($page_title, $message_title, $message_content, $page_icon, $message_icon)
        {
            $html = '
            <div id="adpress" class="wrap" style="width:600px">
                <div id="' . $page_icon . '" class="icon32"><br></div>
                <h2>' . $page_title . '</h2>
                <div class="c-block" >
                    <div class="c-head">
                        <h3 id="' . $message_icon . '">' . $message_title . '</h3>
                    </div>
                    ' . $message_content . '
                </div>
            </div>';
            echo $html;
            return $html;
        }

        /**
         * Display a notice message
         *
         * @param string $message_title Message Title
         * @param string $message_content Message Content
         * @param string $message_icon Box Icon name
         * @return string HTML Code
         */
        static function display_notice($message_title, $message_content, $message_icon)
        {
            $html = '
            <div id="adpress" class="wrap" style="width:600px">
                <div class="c-block" >
                    <div class="c-head">
                        <h3 id="' . $message_icon . '">' . $message_title . '</h3>
                    </div>
                    ' . $message_content . '
                </div>
            </div>';
            echo $html;
            return $html;
        }

        /**
         * Dump any variable to the screen
         * @param mixed $var Any variable to dump
         * @return string HTML Code
         */
        static function display_log($var)
        {
            $settings = get_option('adpress_settings');
            $html = '
            <div id="adpress" class="wrap" style="width:600px">
                <div class="c-block" >
                    <div class="c-head">
                        <h3 id="adpress-icon-debug">Debugging Information</h3>
                    </div>
                    <p>' . var_export($var, true) . '</p>
                </div>
            </div>';
            if (isset($settings['debug_mode'])) {
                echo $html;
            }
            return $html;
        }

        /**
         * Start the plug-in
         */
        public function start()
        {
            // Rewrite rules for redirects
            new wp_adpress_rewrite();

            // Update roles
            new wp_adpress_roles();

            // Admin Panel
            if (is_admin()) {
                new wp_adpress_admin();
            }
            // Super Admin Panel (for WP-MU)
            if (is_admin() && is_super_admin() && function_exists('is_multisite') && is_multisite()) {
                new wp_adpress_muadmin();
            }
        }

    }

}

// Create a new instance of the main class
$wpadpress = new wp_adpress();

/**
 * ShortHand function
 * @param integer $id Campaign ID
 */
function display_campaign($id)
{
    if (class_exists('wp_adpress_campaign')) {
        $campaign = new wp_adpress_campaign($id);
        $campaign->display();
    }
}
?>
