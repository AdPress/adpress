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
  Version: 0.9.9
  Text Domain: wp-adpress
 */


if (!class_exists('wp_adpress')) {

	/**
	 * AdPress Starter Class
	 *
	 * This is the plug-in Backbone class. This class is used to Initialize,
	 * Install, Activate, Deactivate and Uninstall the plug-in.
	 */
	final class wp_adpress
{

	private static $instance;

	/**
	 * Plug-in Version
	 * @var string
	 */
	public $version = "0.9.9";

	/**
	 * Minimal WordPress version required
	 * @var string
	 */
	public $wp_version = "3.8";

	/**
	 * Plugin Settings
	 * @var array
	 */
	public $settings;

	public static function instance()
	{
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof wp_adpress ) ) {
			self::$instance = new wp_adpress;
			self::$instance->preload();
		}
		return self::$instance;
	}

	public function preload()
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
		add_action('wp_head', 'wp_adpress::print_styles');

		/*
		 * 9. Run the plug-in!
		 */
		add_action('plugins_loaded', array(&$this, 'start'));

		/*
		 * 10. Usage Tracking
		 */
		add_action('admin_init', array(&$this, 'usage_tracking'));

		/*
		 * 11. Display notices
		 */
		add_action('admin_notices', array(&$this, 'display_notifications'));

		/*
		 * 12. Media Access (TODO: should be moved from here)
		 */
		add_action('pre_get_posts', 'wp_adpress_roles::restrict_ajax_library');
		add_action('parse_query', 'wp_adpress_roles::restrict_media_library');
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

	/** * Load required files for the plug-in
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
		require_once('inc/misc.php');
		require_once('inc/post_types.php');
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
		require_once('inc/license.php');

		/*
		 * Payments Functionality
		 */
		require_once('inc/payments/functions.php');
		require_once('inc/payments/history_table.php');

		/*
		 * Required Add-ons
		 */
		require_once('inc/addons/manual.php');
		require_once('inc/addons/paypal.php');
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
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
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

		// Check if the license settings are entered
		$license = get_option('adpress_license_settings');
		if (!isset($license['license_username']) || $license['license_username'] === '') {
			wp_adpress::add_notification('license_missing', 'AdPress License', 'Please enter AdPress license details to enable all of the plugin features', 'updated');
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
		if (class_exists('wp_adpress_install')) {
			$setup = new wp_adpress_install();
			return $setup;
		}
	}

	/**
	 * Uninstall the plug-in
	 * @static
	 * @return wp_adpress_uninstall
	 */
	public static function uninstall()
	{
		require_once('inc/install.php');
		if (class_exists('wp_adpress_uninstall')) {
			$setup = new wp_adpress_uninstall();
			return $setup;
		}
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
	static function print_styles($return = false)
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
		if ($return) {
			$css = $image_css . "\n" . $link_css . "\n" . $flash_css;
			return $css;
		} else {
			echo '<style type="text/css">';
			echo $image_css;
			echo $link_css;
			echo $flash_css;
			echo' </style>';
		}
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
	 *
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
	 * Displays a notification in all of the plugin pages
	 *
	 * @param $id string notification unique identifier
	 * @param $title string title of notification
	 * @param $content string content of notification
	 * @param $type string warning|error
	 */
	static function add_notification($id, $title, $content, $type)
	{
		$notifications = get_option('adpress_notifications');

		$notification = array(
			'id' => $id,
			'title' => $title,
			'content' => $content,
			'type' => $type,
		);

		$notifications[$id] = $notification;

		update_option('adpress_notifications', $notifications);
	}

	/**
	 * Removes a notification
	 *
	 * @param $id string notification unique identifier
	 */
	static function remove_notification($id)
	{
		$notifications = get_option('adpress_notifications');

		unset($notifications[$id]);

		update_option('adpress_notifications', $notifications);
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
		if (is_admin() && class_exists('wp_adpress_admin')) {
			new wp_adpress_admin();
		}
		// Super Admin Panel (for WP-MU)
		if (is_admin() && is_super_admin() && function_exists('is_multisite') && is_multisite() && class_exists('wp_adpress_muadmin')) {
			new wp_adpress_muadmin();
		}

	}

	/**
	 * Usage Tracking
	 *
	 */
	public function usage_tracking()
	{
		// PressTrends Account API Key
		$api_key = 'ghzz893juaz1urmyn0om4uckhxz1r93p1';
		$auth = '';
		// Start of Metrics
		global $wpdb;
		$data = get_transient('presstrends_cache_data');
		if (!$data || $data == '') {
			$api_base = 'http://api.presstrends.io/index.php/api/pluginsites/update/auth/';
			$url = $api_base . $auth . '/api/' . $api_key . '/';
			$count_posts = wp_count_posts();
			$count_pages = wp_count_posts('page');
			$comments_count = wp_count_comments();
			if (function_exists('wp_get_theme')) {
				$theme_data = wp_get_theme();
				$theme_name = urlencode($theme_data->Name);
			} else {
				$theme_data = get_theme_data(get_stylesheet_directory() . '/style.css');
				$theme_name = $theme_data['Name'];
			}
			$plugin_name = '&';
			foreach (get_plugins() as $plugin_info) {
				$plugin_name .= $plugin_info['Name'] . '&';
			}
			// CHANGE __FILE__ PATH IF LOCATED OUTSIDE MAIN PLUGIN FILE
			$plugin_data = get_plugin_data(__FILE__);
			$posts_with_comments = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type='post' AND comment_count > 0");
			$data = array(
				'url' => stripslashes(str_replace(array('http://', '/', ':'), '', site_url())),
				'posts' => $count_posts->publish,
				'pages' => $count_pages->publish,
				'comments' => $comments_count->total_comments,
				'approved' => $comments_count->approved,
				'spam' => $comments_count->spam,
				'pingbacks' => $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_type = 'pingback'"),
				'post_conversion' => ($count_posts->publish > 0 && $posts_with_comments > 0) ? number_format(($posts_with_comments / $count_posts->publish) * 100, 0, '.', '') : 0,
				'theme_version' => $plugin_data['Version'],
				'theme_name' => $theme_name,
				'site_name' => str_replace(' ', '', get_bloginfo('name')),
				'plugins' => count(get_option('active_plugins')),
				'plugin' => urlencode($plugin_name),
				'wpversion' => get_bloginfo('version'),
			);
			foreach ($data as $k => $v) {
				$url .= $k . '/' . $v . '/';
			}
			wp_remote_get($url);
			set_transient('presstrends_cache_data', $data, 60 * 60 * 24);
		}
	}

	/**
	 * Display notifications in the AdPress notification system
	 */
	public function display_notifications()
	{
		$notifications = get_option('adpress_notifications');
		if (!is_array($notifications)) {
			return;
		}

		if (empty($notifications)) {
			return;
		}

		foreach ($notifications as $notification) {
			$this->display_notification($notification);
		}
	}

	/**
	 * @param $notification
	 */
	private function display_notification($notification)
	{
			/*
			echo '<div class="notification ' . $notification['type'] . '">
				  <h3>' . $notification['title'] . '</h3>
				  <p>' . $notification['content'] . '</p>
				  </div>';
			 */
	}

}

}

// Create a new instance of the main class
/*
if (class_exists('wp_adpress')) {
	$wpadpress = new wp_adpress();
}
 */

function WPAD() {
	return wp_adpress::instance();
}
global $wpadpress;
$wpadpress = WPAD();

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


