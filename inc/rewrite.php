<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

if (!class_exists('wp_adpress_paypal')) {
    /**
     * Rewrite Class
     * @package Includes
     * @subpackage Rewrite Engine
     */
    class wp_adpress_rewrite
    {

        function __construct()
        {
            /*
            * Register the rewrite tag
            */
            add_action('init', array(&$this, 'register_tags'));
            add_action('wp_head', array(&$this, 'flush_rules'));

            /*
            * Redirect Template when query var matched
            */
            add_action('template_redirect', array(&$this, 'redirect'));
        }

        /**
         * Register the rewrite rules
         */
        public function register_tags()
        {
            add_rewrite_rule('adpress/([^/]*)', 'index.php?adpress=$matches[1]', 'top');
            add_rewrite_tag('%adpress%', '([^&]+)');
        }

        public function flush_rules()
        {
            global $wp_rewrite;
            if ($wp_rewrite->rules && !array_key_exists('adpress/([^/]*)', $wp_rewrite->rules)) {
                $wp_rewrite->flush_rules();
            }
        }

        /**
         * Redirect function
         *
         * @global object $wp_query
         */
        public function redirect()
        {
            global $wp_query;
            if (isset($wp_query->query_vars['adpress'])) {
                $id = $wp_query->query_vars['adpress'];
                $this->parse_redirect($id);
            }
        }

        /**
         * Checks for Ad Status, record the hit and do the redirect
         * @param integer $id
         */
        public function parse_redirect($id)
        {
            /*
            * Checks if id exists
            */
            if (wp_adpress_ads::id_exists($id)) {
                $ad = new wp_adpress_ad($id);
                if ($ad->status === 'running') {
                    $ad->record_hit();
                    $ad->save();
                    // Check that the Ad didn't expire
                    if (isset($ad->param['url'])) {
                        wp_redirect($ad->param['url']);
                        exit;
                    }
                }
            }
        }

    }
}

