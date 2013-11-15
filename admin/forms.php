<?php
// Don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}

if (!class_exists('wp_adpress_forms')) {
    /**
     * Forms Rendering Class
     * @package Admin
     * @subpackage Starter
     */
    class wp_adpress_forms
    {

        /**
         * Renders a section description
         * @param $param
         */
        static function description($param)
        {
            echo '</div>';
        }

        /**
         * Renders a textbox
         * @param string $id Option name
         */
        static function textbox($param)
        {
            $settings = get_option($param[1]);
            if (isset($settings[$param[0]])) {
                $val = $settings[$param[0]];
            } else {
                $val = '';
            }
            echo '<input type="text" name="' . $param[1] . '[' . $param[0] . ']" id="' . $param[0] . '" value="' . $val . '" />';
        }

        /**
         * Renders a password textbox
         * @static
         * @param $param
         */
        static function passwordbox($param)
        {
            $settings = get_option($param[1]);
            if (isset($settings[$param[0]])) {
                $val = $settings[$param[0]];
            } else {
                $val = '';
            }
            echo '<input type="password" name="' . $param[1] . '[' . $param[0] . ']" id="' . $param[0] . '" value="' . $val . '" />';
        }

        /**
         * Renders a Checkbox
         * @param integer $id
         */
        static function checkbox($param)
        {
            $settings = get_option($param[1]);
            $val = isset($settings[$param[0]]);
            if ($val) {
                $checked = 'checked';
            } else {
                $checked = '';
            }
            echo '<input type="checkbox" name="' . $param[1] . '[' . $param[0] . ']" id="' . $param[0] . '" ' . $checked . ' />';
        }

        /**
         * Renders a text area
         * @param array $param
         */
        static function textarea($param)
        {
            $settings = get_option($param[1]);
            if (isset($settings[$param[0]])) {
                $val = $settings[$param[0]];
            } else {
                $val = '';
            }
            echo '<textarea name="' . $param[1] . '[' . $param[0] . ']" id="' . $param[0] . '">' . $val . '</textarea>';
        }

        static function button($param)
        {


            $value = $param['value'];
            $action = $param['action'];
            echo '<a href="' . $_SERVER['REQUEST_URI'] . '&action=' . $action . '" class="button-secondary">' . $value . '</a>';
        }

        /**
         * Renders a SELECT element with roles
         * @global objet $wp_roles
         * @static
         * @param array $param
         */
        static function roles_select($param)
        {
            // Control Parameters
            $settings = get_option($param[1]);
            if (isset($settings[$param[0]])) {
                $val = $settings[$param[0]];
            } else {
                $val = '';
            }

            // Get WP Roles
            global $wp_roles;
            $roles = $wp_roles->get_names();

            // Generate HTML code
            $html = '<select name="' . $param[1] . '[' . $param[0] . ']" id="' . $param[0] . '">';
            if ($val === 'all') {
                $html .= '<option selected value="all">All</option>';
            } else {
                $html .= '<option value="all">All</option>';
            }
            foreach ($roles as $key => $value) {
                if ($key === $val) {
                    $html .= '<option selected value="' . $key . '">';
                } else {
                    $html .= '<option value="' . $key . '">';
                }
                $html .= $value;
                $html .= "</option>";
            }
            $html .= "</select>";
            // Update roles
            echo $html;
        }

        /**
         * Renders a list of roles checkboxes
         * @global object $wp_roles
         * @static
         * @param array $param
         */
        static function roles_check($param)
        {
            // Roles list
            $settings = get_option($param[1]);
            if (isset($settings[$param[0]])) {
                $val = $settings[$param[0]];
            } else {
                $val = '';
            }

            // Generate HTML Code
            // Get WP Roles
            global $wp_roles;
            $roles = $wp_roles->get_names();
            unset($roles['administrator']);
            // Generate HTML code
            if (isset($val['all']) && $val['all'] === 'on') {
                echo '<input type="checkbox" name="' . $param[1] . '[' . $param[0] . '][all]" id="' . $param[0] . '[all]" checked/>  All<br />';
            } else {
                echo '<input type="checkbox" name="' . $param[1] . '[' . $param[0] . '][all]" id="' . $param[0] . '[all]" />  All<br />';
            }

            foreach ($roles as $key => $value) {
                if (isset($val[$key]) && $val[$key] === 'on') {
                    echo '<input type="checkbox" name="' . $param[1] . '[' . $param[0] . '][' . $key . ']" id="' . $param[0] . '[' . $key . ']" checked />  ' . $value . '<br />';
                } else {
                    echo '<input type="checkbox" name="' . $param[1] . '[' . $param[0] . '][' . $key . ']" id="' . $param[0] . '[' . $key . ']" />  ' . $value . '<br />';
                }

            }
        }

        /**
         * Settings Tabs
         * @return array Tabs
         */
        static function tabs()
        {
            $tabs = array(
                'general' => __('General', 'wp-adpress'),
                'image_ad' => __('Image Ad', 'wp-adpress'),
                'link_ad' => __('Link Ad', 'wp-adpress'),
                'flash_ad' => __('Flash Ad', 'wp-adpress'),
                'history' => __('History', 'wp-adpress'),
                'import' => __('Import/Export', 'wp-adpress'),
                'license' => __('License', 'wp-adpress')
            );
            return $tabs;
        }

        /**
         * Validates user input
         * @param array $var User input
         * @return array User input
         */
        static function validate($var)
        {
            return $var;
        }

    }
}

