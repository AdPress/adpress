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
		 * Renders a Fields Separator
		 *
		 * @return void
		 */
		static function separator()
		{
			echo '';	
		}

		/**
		 * Renders a textbox
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
		 * Renders a Range Element 
		 */
		static function rangebox($param)
		{
			$settings = get_option($param[1]);
			if (isset($settings[$param[0]])) {
				$val = $settings[$param[0]];
			} else {
				$val = '';
			}
			echo '<input type="range" name="' . $param[1] . '[' . $param[0] . ']" id="' . $param[0] . '" value="' . $val . '" /> <output id="'.$param[0].'_output" for="' . $param[0] . '">20</output>%';
		}

		/**
		 * Renders a label
		 *
		 * @param string
		 */
		static function label( $param ) {
			//nothing
			echo '';
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
		 * Renders a collection of Radio Boxes
		 * @param $param array
		 */
		static function radiobox( $param ) {
			$settings = get_option( $param[ 1 ] );
			$selected = ( isset( $settings[ $param[ 0 ] ] ) ? $settings [ $param [ 0 ] ] : '' );
			$options = $param[ 2 ];
			foreach ( $options as $option_id=>$option_label ) {
				if ( $option_id === $selected ) {
					echo ' <input type="radio" id="' . $param[0] . $option_id . '" name="' . $param[1] . '[' . $param[0] . ']" value="' . $option_id . '" checked/> <label for="' . $param[0] . $option_id . '">' . $option_label . ' </label>'; 
				} else {
					echo ' <input type="radio" id="' . $param[0] . $option_id . '" name="' . $param[1] . '[' . $param[0] . ']" value="' . $option_id . '" /> <label for="' . $param[0] . $option_id . '">' . $option_label . ' </label>'; 
				}
			}
		}

		/**
		 * Renders a list checkbox
		 */
		static function list_gateways( ) {
			// All available Gateways
			$gateways = wp_adpress_get_payment_gateways(); 

			// Enable Gateways
			$settings = get_option( 'adpress_gateways_settings' );
			if ( isset( $settings['active'] ) ) {
				$active = $settings['active'];
			} else {
				$active = array();
			}

			echo '<ul>';
		
			foreach ($gateways as $gateway) {
				if (array_key_exists($gateway['id'], $active) && $active[$gateway['id']] == 'on') {
					echo '<li><input type="checkbox" id="'.$gateway['id'].'" name="adpress_gateways_settings[active]['.$gateway['id'].']" checked/> <label for="'.$gateway['id'].'">'. $gateway['short_label'] . '</label></li>';
				} else {
					echo '<li><input type="checkbox" id="'.$gateway['id'].'" name="adpress_gateways_settings[active]['.$gateway['id'].']" /> <label for="'.$gateway['id'].'">'. $gateway['short_label'] . '</label></li>';

				}
			}
			echo '</ul>';
		}

		/**
		 * Renders a list select
		 * @param array $param
		 */
		static function list_select($param) {
			$list = get_option($param[0]);
			if (isset($param[2])) {
				$settings = get_option($param[1]);
				$default = $settings[$param[2]];
			} else {
				$default = get_option($param[1]);
			}
			echo '<select id="" name="'.$param[1].'['.$param[2].']">';
			foreach ($list as $item_id => $item_name) {
				if ($item_id == $default) {
					echo '<option id="" value="'.$item_id.'" selected="selected">'.$item_name.'</option>';
				} else {
					echo '<option id="" value="'.$item_id.'">'.$item_name.'</option>';
				}
			}
			echo '</select>';
		}

		static function select_default_gateway() {
			$list = wp_adpress_get_payment_gateways();
			$settings = get_option( 'adpress_gateways_settings' );
			if ( isset( $settings['default'] ) ) {
				$default = $settings['default'];
			} else {
				$default = 'manual';
			}
			echo '<select id="" name="adpress_gateways_settings[default]">';
			foreach ($list as $gateway) {
				if ($gateway['id'] == $default) {
					echo '<option id="" value="'.$gateway['id'].'" selected="selected">'.$gateway['short_label'].'</option>';
				} else {
					echo '<option id="" value="'.$gateway['id'].'">'.$gateway['short_label'].'</option>';
				}
			}
			echo '</select>';

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
			echo '<a href="' . $_SERVER['REQUEST_URI'] . '&wpad-action=' . $action . '" class="button-secondary">' . $value . '</a>';
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
				'gateways' => __('Gateways', 'wp-adpress'),
			);

			return apply_filters( 'wp_adpress_admin_settings_tabs', $tabs );
		}

		/**
		 * Validates user input
		 * @param array $var User input
		 * @return array User input
		 */
		static function validate($var)
		{
			do_action( 'wp_adpress_settings_form_update', $var );
			return $var;
		}

	}
}
