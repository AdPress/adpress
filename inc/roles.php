<?php
if (!class_exists('wp_adpress_roles')) {
    class wp_adpress_roles
    {
        /**
         * Determines if all users will have the required permissions
         *
         * @var boolean
         */
        static $all;

        /**
         * An array with the roles which have the required permissions
         *
         * @var array
         */
        static $roles;

        /**
         * An array with the user names which have the required permissions
         *
         * @var array
         */
        static $users;

        function __construct()
        {
            // Quit the roles class if front-end
            if (!is_admin()) {
                return;
            }

            // Create the MetaBox user view
            $this->metabox_user();

            // Make sure the Administrator can access the client dashboard
            $this->admin_privilege();
        }


        /**
         * Set the permission entities
         */
        static function set_entities()
        {
            $settings = get_option('adpress_settings');
            if (isset($settings['client_roles'])) {
                $roles = $settings['client_roles'];
            } else {
                $roles = array();
            }

            // ALL Rule
            if (isset($roles['all']) && $roles['all'] === 'on') {
                self::$all = true;
            } else {
                self::$all = false;
            }

            // Roles Rule
            self::$roles = $roles;
            unset(self::$roles['all']);

            // Users Rule
            self::$users = get_users(array('meta_key' => 'adpress_client', 'meta_value' => true, 'fields' => 'ID'));
        }

        /**
         * User Metabox hooks
         */
        private function metabox_user()
        {
            if (current_user_can('manage_options')) {
                add_action('show_user_profile', array(&$this, 'display_metabox'));
                add_action('edit_user_profile', array(&$this, 'display_metabox'));

                add_action('personal_options_update', array(&$this, 'update_metabox'));
                add_action('edit_user_profile_update', array(&$this, 'update_metabox'));
            }
        }

        /**
         * Display the AdPress Form
         *
         * @param object $user
         */
        public function display_metabox($user)
        {
            // AdPress Client
            $user_meta = get_user_meta($user->ID, 'adpress_client', true);
            if ($user_meta) {
                $checked = "checked";
            } else {
                $checked = "";
            }
            print <<<form
<h3>AdPress Client</h3>
<table class="form-table">
    <tr>
        <th><label for="adpress_client">Enable Client Access</label></th>
        <td><input type="checkbox" name="adpress_client" id="adpress_client" $checked/> Enable Access to the AdPress client dashboard</td>
    </tr>
</table>
form;

        }

        /**
         * Update the user metabox
         *
         * @param integer $user_id
         */
        public function update_metabox($user_id)
        {
            if (isset($_POST['adpress_client']) && $_POST['adpress_client'] === 'on') {
                $checked = true;
            } else {
                $checked = false;
            }
            update_user_meta($user_id, 'adpress_client', $checked);
        }

        /**
         * Make sure the Administrator can access the client dashboard
         */
        private function admin_privilege()
        {
            if (current_user_can('manage_options')) {
                $user = wp_get_current_user();
                $user->add_cap('adpress_client_menu');
            }
        }

        /**
         * Set the Menu and Pages access permissions
         */
        static function set_permissions()
        {
            // Set the allowed entities
            self::set_entities();
            self::set_all_permissions();
            if (!self::$all) {
                self::set_roles_permissions();
                self::set_users_permissions();
            }
        }

        /**
         * Set the permissions for ALL users
         */
        static function set_all_permissions()
        {
            $users = get_users();
            foreach ($users as $user) {
                $user = new WP_User($user->ID);
                if (self::$all) {
                    $user->add_cap('adpress_client_menu');
                } else {
                    $user->remove_cap('adpress_client_menu');
                }
            }
        }

        /**
         * Set the permissions for Roles
         */
        static function set_roles_permissions()
        {
            global $wp_roles;
            $roles = $wp_roles->get_names();
            foreach ($roles as $role_id => $role_name) {
                $role = get_role($role_id);
                $role->remove_cap('adpress_client_menu');
            }
            if (!empty(self::$roles)) {
                foreach (self::$roles as $role_id => $role_state) {
                    $role = get_role($role_id);
                    $role->add_cap('adpress_client_menu');
                }
            }
        }

        /**
         * Set the permissions for specific Users
         */
        static function set_users_permissions()
        {
            $users = get_users();
            foreach ($users as $user) {
                $user = new WP_User($user->ID);
                $user->remove_cap('adpress_client_menu');
            }
            if (!empty(self::$users)) {
                foreach (self::$users as $user_id) {
                    $user = new WP_User($user_id);
                    $user->add_cap('adpress_client_menu');
                }
            }
        }

        /**
         * Restrict Media Access
         */
        static function media_filter()
        {
            // Apply the media filter for current AdPress Clients
            $roles = self::filter_roles(array('adpress_client_menu'), array('upload_files'));
            $users = self::filter_users(array('adpress_client_menu'), array('upload_files'));
            self::roles_add_cap($roles, 'upload_files');
            self::roles_add_cap($roles, 'remove_upload_files');
            self::users_add_cap($users, 'upload_files');
            self::users_add_cap($users, 'remove_upload_files');

            // Restrict Media Library access
            add_filter('parse_query', 'wp_adpress_roles::restrict_media_library');

            // For cleaning purposes
            $clean_roles = self::filter_roles(array('remove_upload_files'), array('adpress_client_menu'));
            $clean_users = self::filter_users(array('remove_upload_files'), array('adpress_client_menu'));
            self::roles_remove_cap($clean_roles, 'upload_files');
            self::roles_remove_cap($clean_roles, 'remove_upload_files');
            self::users_remove_cap($clean_users, 'upload_files');
            self::users_remove_cap($clean_users, 'remove_upload_files');
        }

        /**
         * @param $roles
         * @param $cap
         */
        static function roles_add_cap($roles, $cap)
        {
            foreach ($roles as $role) {
                $role = get_role($role);
                $role->add_cap($cap);
            }
        }

        /**
         * @param $users
         * @param $cap
         */
        static function users_add_cap($users, $cap)
        {
            foreach ($users as $user) {
                $user = new WP_User($user);
                $user->add_cap($cap);
            }
        }

        /**
         * @param $roles
         * @param $cap
         */
        static function roles_remove_cap($roles, $cap)
        {
            foreach ($roles as $role) {
                $role = get_role($role);
                $role->remove_cap($cap);
            }
        }

        /**
         * @param $users
         * @param $cap
         */
        static function users_remove_cap($users, $cap)
        {
            foreach ($users as $user) {
                $user = new WP_User($user);
                $user->remove_cap($cap);
            }
        }

        /**
         * Filter all roles of the blog based on capabilities
         *
         * @static
         * @param array $include Array of capabilities to include
         * @param array $exclude Array of capabilities to exclude
         * @return array
         */
        static function filter_roles($include, $exclude)
        {
            $filtered_roles = array();
            global $wp_roles;
            $roles = $wp_roles->get_names();
            foreach ($roles as $role_id => $role_name) {
                $role = get_role($role_id);
                if (self::role_has_caps($role, $include) && !self::role_has_caps($role, $exclude)) {
                    $filtered_roles[] = $role_id;
                }
            }
            return $filtered_roles;
        }

        /**
         * @static
         * @param $role
         * @param $caps
         * @return bool
         */
        static function role_has_caps($role, $caps)
        {
            foreach ($caps as $cap) {
                if (!$role->has_cap($cap)) {
                    return false;
                }
            }
            return true;
        }

        /**
         * Filter all users of the blog based on capabilities
         *
         * @static
         * @param array $include Array of capabilities to include
         * @param array $exclude Array of capabilities to exclude
         * @return array
         */
        static function filter_users($include, $exclude)
        {
            $filtered_users = array();
            $users = get_users();
            foreach ($users as $user) {
                $user = new WP_User($user->ID);
                if (self::user_has_caps($user, $include) && !self::user_has_caps($user, $exclude)) {
                    $filtered_users[] = $user->ID;
                }
            }
            return $filtered_users;
        }


        /**
         * @static
         * @param $user
         * @param $caps
         * @return bool
         */
        static function user_has_caps($user, $caps)
        {
            foreach ($caps as $cap) {
                if (!$user->has_cap($cap)) {
                    return false;
                }
            }
            return true;
        }

        /**
         * @param $wp_query
         */
        static function restrict_media_library($wp_query)
        {
            if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/upload.php')) {
                if (current_user_can('remove_upload_files')) {
                    global $current_user;
                    $wp_query->set('author', $current_user->ID);
                }
            } else if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/media-upload.php')) {
                if (current_user_can('remove_upload_files')) {
                    global $current_user;
                    $wp_query->set('author', $current_user->ID);
                }
            }
        }

    }
}