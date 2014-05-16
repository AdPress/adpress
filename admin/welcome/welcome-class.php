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
    }

    /**
     * Register the Tabs pages 
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function admin_menus() {
        // About Page
        add_dashboard_page(
            __( 'Welcome to AdPress', 'wp-adpress' ),
            __( 'Welcome to AdPress', 'wp-adpress' ),
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
    }

    /**
     * Hide the Tabs from the menu
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function admin_head() {
        remove_submenu_page( 'index.php', 'wpadpress-about' );
        remove_submenu_page( 'index.php', 'wpadpress-start' );
    }

    /**
     * Navigation tabs
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function tabs() {
        include_once('page-tabs.php');
    }

    /**
     * Welcome page header
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function page_header() {
        include_once('page-header.php');
    }

    /**
     * Welcome page footer
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function page_footer() {
        include_once('page-footer.php');
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
        require_once('about-page.php');
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
        require_once('start-page.php');
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
        wp_safe_redirect( admin_url( 'index.php?page=wpadpress-about' ) );

        // Exit PHP process
        exit;
    }
}
new wp_adpress_welcome();
