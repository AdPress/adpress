<?php
/**
 * Weclome Page Class
 *
 * @package     AdPress
 * @subpackage  Admin/Welcome
 * @copyright   Copyright (c) 2014,  Abid Omar
 * @since       1.0.0
 */

// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

/**
 * AdPress Welcome Class
 *
 * A class for the activation welcome screen.
 *
 * @since 1.0.0
 */
class wp_adpress_welcome {

    /**
     * Class Init
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menus') );
        add_action( 'admin_head', array( $this, 'admin_head' ) );
        add_action( 'admin_init', array( $this, 'init'    ) );
        add_action('admin_print_scripts', array(&$this, 'load_scripts'));
        add_action('admin_print_styles', array(&$this, 'load_styles'));
    }

    /**
     * Register the Tabs pages 
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function admin_menus() {
        // New Features Page
        add_dashboard_page(
            __( 'New Features', 'wp-adpress' ),
            __( 'New Features', 'wp-adpress' ),
            'manage_options',
            'wpadpress-new',
            array( $this, 'new_screen' )
        );

        // About Page
        add_dashboard_page(
            __( 'About AdPress', 'wp-adpress' ),
            __( 'About AdPress', 'wp-adpress' ),
            'manage_options',
            'wpadpress-about',
            array( $this, 'about_screen' )
        );

        // Getting Started Page
        add_dashboard_page(
            __( 'Getting started with AdPress', 'wp-adpress' ),
            __( 'Getting started with AdPress', 'wp-adpress' ),
            'manage_options',
            'wpadpress-start',
            array( $this, 'getting_started_screen' )
		);

		// Add-ons Page
		add_dashboard_page(
			__( 'Add-Ons', 'wp-adpress' ),
			__( 'Add-Ons', 'wp-adpress' ),
			'manage_options',
			'wpadpress-addons',
			array( $this, 'addons_screen' )
		);
    }

    public function load_scripts() {
        global $current_screen;

        switch( $current_screen->id ) {
        case 'dashboard_page_wpadpress-new':
            break;
        case 'dashboard_page_wpadpress-start':
            break;
        case 'dashboard_page_wpadpress-about':
			break;
		case 'dashboard_page_wpadpress-addons':
			break;
        }

    }

    public function load_styles() {
        global $current_screen;

        switch( $current_screen->id ) {
        case 'dashboard_page_wpadpress-new':
            wp_enqueue_style( 'wp_adpress_welcome', ADPRESS_URLPATH . 'admin/welcome/assets/css/style.css' );
            break;
        case 'dashboard_page_wpadpress-start':
            wp_enqueue_style( 'wp_adpress_welcome', ADPRESS_URLPATH . 'admin/welcome/assets/css/style.css' );
            break;
        case 'dashboard_page_wpadpress-about':
            wp_enqueue_style( 'wp_adpress_welcome', ADPRESS_URLPATH . 'admin/welcome/assets/css/style.css' );
			break;
		case 'dashboard_page_wpadpress-addons':
			wp_enqueue_style( 'wp_adpress_welcome', ADPRESS_URLPATH . 'admin/welcome/assets/css/style.css' );
			break;
        }
    }

    /**
     * Hide the Tabs from the menu
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function admin_head() {
        remove_submenu_page( 'index.php', 'wpadpress-new' );
        remove_submenu_page( 'index.php', 'wpadpress-about' );
		remove_submenu_page( 'index.php', 'wpadpress-start' );
		remove_submenu_page( 'index.php', 'wpadpress-addons' );
    }

    /**
     * Navigation tabs
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function tabs() {
        include_once( 'tpl/page-tabs.php' );
    }

    /**
     * Welcome page header
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function page_header() {
        include_once( 'tpl/page-header.php' );
    }

    /**
     * Welcome page footer
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function page_footer() {
        include_once( 'tpl/page-footer.php' );
    }

    /**
     * Render About Screen
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function about_screen() {
        $this->page_header();
        $this->tabs();
        require_once( 'pages/about-page.php' );
        $this->page_footer();
    }

    /**
     * Render Getting Started Screen
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function getting_started_screen() {
        $this->page_header();
        $this->tabs();
        require_once( 'pages/start-page.php' );
        $this->page_footer();
    }

    /**
     * Render New Features Screen
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function new_screen() {
        $this->page_header();
        $this->tabs();
        require_once( 'pages/new-page.php' );
        $this->page_footer();
    }

	public function addons_screen() {
		$this->page_header();
        $this->tabs();
        require_once( 'pages/addons-page.php' );
        $this->page_footer();
	}

    /**
     * Sends user to the Welcome page on first activation of EDD as well as each
     * time EDD is upgraded to a new version
     *
     * @access public
     * @since 1.0.0
     * @global $edd_options Array of all the EDD Options
     * @return void
     */
    public function init() {
        global $edd_options;

        // Bail if no activation redirect
        if ( ! get_transient( 'wpad_activation_redirect' ) ) {
            return;
        }

        // Delete the redirect transient
        delete_transient( 'wpad_activation_redirect' );

        // Bail if activating from network, or bulk
        if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
            return;
        }

        // Redirect to the About Page
        wp_safe_redirect( admin_url( 'index.php?page=wpadpress-new' ) );

        // Exit PHP process
        exit;
    }
}
new wp_adpress_welcome();
