<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

if (!class_exists('wp_adpress_mu')) {
    class wp_adpress_mu
    {
        public $blogid;
        public $blogname;
        public $blogurl;

        /**
         * Create a new MU Blog object
         * @param $blogid
         */
        function __construct($blogid)
        {
            $this->blogid = (int)$blogid;
            if (function_exists('is_multisite') && is_multisite()) {
                $blog_details = get_blog_details($blogid);
                $this->blogname = $blog_details->blogname;
                $this->blogurl = $blog_details->siteurl;
            }
        }

        public function get_admin_panel_status()
        {
            if (function_exists('is_multisite') && is_multisite()) {
                switch_to_blog($this->blogid);
                $status = get_option('adpress_restrict_access');
                restore_current_blog();
            } else {
                $status = get_option('adpress_restrict_access');
            }
            return $status;
        }

        public function restrict_admin_panel($update)
        {
            if (function_exists('is_multisite') && is_multisite()) {
                switch_to_blog($this->blogid);
                update_option('adpress_restrict_access', $update);
                restore_current_blog();
            } else {
                update_option('adpress_restrict_access', $update);
            }
        }

        /**
         * Returns an array of Blogs ids
         * @static
         * @return array
         */
        public static function list_blogs()
        {
            global $wpdb;
            $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
            return $blogids;
        }

        /**
         * Run a method for each blog
         * @static
         * @param $func
         */
        public static function map_blogs($func)
        {
            global $wpdb;
            $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
            foreach ($blogids as $blogid)
            {
                switch_to_blog($blogid);
                call_user_func($func);
            }
            restore_current_blog();
        }
    }
}
