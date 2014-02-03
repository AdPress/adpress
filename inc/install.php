<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

/**
 * Install Package
 *
 * @package Includes
 * @subpackage Install
 */

if (!class_exists('wp_adpress_install')) {
    /**
     * Install Class
     */
    class wp_adpress_install
    {

        /**
         * Campaigns Table schema
         * @var string SQL Query
         */
        private $campaigns_table_sql = '
        CREATE TABLE %table_name% (
            id INT(3) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            settings TEXT,
            ad_definition TEXT
        );';

        /**
         * Ads Table schema
         * @var string SQL Query
         */
        private $ads_table_sql = '
        CREATE TABLE %table_name% (
            id INT(3) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            campaign_id INT(3) NOT NULL,
            ad_settings TEXT,
            ad_stats TEXT,
            status TEXT,
            time TEXT,
            user_id INT(8)
        );';

        /**
         * History Table schema
         * @var string SQL Query
         */
        private $history_table_sql = '
        CREATE TABLE %table_name% (
            id INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            ad_id INT(3) NOT NULL,
            approved_at TEXT,
            expired_at TEXT,
            ad_info TEXT,
            campaign_info TEXT
        );';

        /**
         * Plug-in default settings
         * @var array
         */
        static $defaults = array(
            'currency' => 'USD',
            'adminbar' => 'on',
            'smart_rewrite' => '',
            'client_role' => 'administrator',
            'client_transfer_role' => 'administrator',
            'client_old_role' => '',
            'client_roles' => array('all' => 'on'),
            'time_format' => 'Y-m-d h:i',
            'history' => 'on'
        );

		/**
		 * List of installed gateways
		 * @var array
		 */
		static $installed_gateways = array(
			'manual' => 'Manual',
			'paypal' => 'PayPal',
		);

		/**
		 * Array of active gateways
		 * @var array
		 */
		static $active_gateways = array(
		   'manual' => 'on',
		);

		static $default_gateway = 'paypal';

        /**
         * Image Ad Default Settings
         * @var array
         */
        static $img_defaults = array(
            'ad_loop' => '
<li>
	<a href="@url" target="_blank">
		<img src="@image_src"/>
	</a>
</li>',
            'ad_css' => '
.image-campaign
{
	list-style: none;
}
.image-campaign li
{
	display: list-item;
	float: left;
	padding: 0;
	margin: 5px;
}'
        );

        /**
         * Flash Ad Default Settings
         * @var array
         */
        static $flash_defaults = array(
            'ad_loop' => '
            <li>
            	<div style="height: @banner_height; width: @banner_width; position: relative;">
            		<a target="_blank" style="height: @banner_height; width: @banner_width;" href="@url"></a>
            		<embed style="position: absolute;" type="application/x-shockwave-flash" src="@swf_src" quality="high" wmode="transparent" height="@banner_height" width="@banner_width">
            	</div>
            </li>',
            'ad_css' => '
            .flash-campaign
            {
            	list-style: none;
            }

            .flash-campaign li
            {
            	display: list-item;
            	float: left;
            	padding: 0;
            	margin: 5px;
            }

            .flash-campaign li div
            {
              position: relative;
            }

            .flash-campaign li div a
            {
              position: absolute;
              z-index: 9999;
              display: block;
            }'
        );

        /**
         * Link Ad Default Settings
         * @var array
         */
        static $link_defaults = array(
            'ad_loop' => '
<li>
	<a href="@url" target="_blank">@link_text</a>
</li>',
            'ad_css' => '
.link-campaign
{

}

.link-campaign li
{
	color: #777;
	font-size: 15px;
}'
        );

        function __construct()
        {
            /*
            * Required file to execute dbDelta queries
            */
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            /*
            * Create the Tables
            */
            $this->create_Table('adpress_campaigns', $this->campaigns_table_sql);
            $this->create_Table('adpress_ads', $this->ads_table_sql);
            $this->create_Table('adpress_history', $this->history_table_sql);

            /*
            * Set the Default Settings
            */
            self::$defaults['client_role'] = __('administrator');
            self::$defaults['client_transfer_role'] = __('administrator');
            update_option('adpress_settings', self::$defaults);
            update_option('adpress_image_settings', self::$img_defaults);
            update_option('adpress_flash_settings', self::$flash_defaults);
            update_option('adpress_link_settings', self::$link_defaults);

			// Gateways Settings
			$adpress_settings = array (
				'active' => self::$active_gateways,
				'default' => self::$default_gateway,
			);
			update_option('adpress_gateways', self::$installed_gateways);
			update_option('adpress_gateways_settings', $adpress_settings);


            /*
            * Set the install option
            */
            update_option('adpress_install', 'installed');

            /*
            * Update the user roles
            */
            // TODO: Check this line of code
            //wp_adpress::update_user_roles();
        }

        /**
         * Create a table in the WordPress database
         * @global object $wpdb
         * @param string $table_name Table name
         * @param string $sql Table schema
         */
        private function create_Table($table_name, $sql)
        {
            global $wpdb;
            /*
            * Make sure that the table doesn't exist, if it does drop the current
            * one. This is to make sure that plugin updates re-construct the
            * database
            */
            if (wp_adpress_uninstall::check_table_exists($table_name)) {
                wp_adpress_uninstall::remove_Table($table_name);
            }
            /*
            * Create the Table
            */
            $this->table_sql = str_replace('%table_name%', $wpdb->prefix . $table_name, $sql);
            dbDelta($this->table_sql);
        }

    }
}

if (!class_exists('wp_adpress_uninstall')) {
    /**
     * Uninstall Class
     */
    class wp_adpress_uninstall
    {

        function __construct()
        {
            global $wpdb;
            /* Remove the Tables */
            if (self::check_table_exists('adpress_campaigns')) {
                self::remove_Table('adpress_campaigns');
            }
            if (self::check_table_exists('adpress_ads')) {
                self::remove_Table('adpress_ads');
            }
            if (self::check_table_exists('adpress_history')) {
                self::remove_Table('adpress_history');
            }
            /* Remove the plugin Settings */
            delete_option('adpress_settings');

            /* Remove the Install Variable */
            delete_option('adpress_install');

            /* Flush rewrite rules */
            if (function_exists('flush_rewrite_rules')) {
                flush_rewrite_rules();
            }
        }

        /**
         * Removes a table from the WordPress database
         * @global object $wpdb
         * @param string $table Table name
         */
        public static function remove_Table($table)
        {
            global $wpdb;
            $wpdb->query('DROP TABLE ' . $wpdb->prefix . $table . ' ;');
        }

        /**
         * Check if a mysql Table Exists
         * @param string $table Table name
         * @return boolean
         */
        public static function check_table_exists($table)
        {
            global $wpdb;
            $result = $wpdb->get_var('SELECT (CASE WHEN EXISTS ((SELECT * FROM information_schema.TABLES WHERE table_name = "' . $wpdb->prefix . $table . '")) THEN 1 ELSE 0 END) AS TABLE_EXIST;');
            if ($result === '1') {
                return true;
            }
            return false;
        }

    }
}

