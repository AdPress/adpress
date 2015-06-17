<?php
// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

/**
 * @package Views
 * @subpackage Settings
 */

if (!class_exists('wp_adpress_settings')) {
	class wp_adpress_settings
	{

		static function render_tabs()
		{
			/*
			 * Get the current tab
			 */

			if (isset($_GET['tab'])) {
				$current = $_GET['tab'];
			} else {
				$current = 'general';
			}
			/*
			 * Load the tabs array
			 */
			$tabs = wp_adpress_forms::tabs();
			$links = array();
			/*
			 * Create the links
			 */
			foreach ($tabs as $tab => $name) {
				if ($tab == $current) {
					$links[] = '<a class="nav-tab nav-tab-active" href="?page=adpress-settings&tab=' . $tab . '">' . $name . '</a>';
				} else {
					$links[] = '<a class="nav-tab" href="?page=adpress-settings&tab=' . $tab . '">' . $name . '</a>';
				}
			}
			/*
			 * Draw the links
			 */
			foreach ($links as $link) {
				echo $link;
			}
		}

		static function render_pages()
		{
			// if no tab is set, set it to default
			if (!isset($_GET['tab'])) {
				$_GET['tab'] = 'general';
			}

			// built-in tabs
			switch ($_GET['tab']) {
			case 'general':
				self::general_page();
				break;
			case 'gateways':
				self::gateways_page();
				break;	
			case 'history':
				self::history();
				break;
			case 'import':
				self::import();
				break;
			case 'license':
				self::license();
				break;
			}

			// Extended tabs
			do_action( 'wp_adpress_settings_tabs_display', $_GET['tab'] );
		}

		/**
		 * General Page
		 */
		static function general_page()
		{
?>
		<form action="options.php" method="POST">
			<?php settings_fields('adpress_settings'); ?>
			<div class="c-block" style="width: 650px;">
				<div class="c-head">
					<?php do_settings_sections('adpress_settings_form_general'); ?>
				</div>
				<div class="c-block" style="width: 650px;">
					<div class="c-head">
						<?php do_settings_sections('adpress_settings_form_client'); ?>
					</div>
						<div class="c-block" style="width: 650px;">
							<div class="c-head">
								<?php do_settings_sections('adpress_settings_form_history'); ?>
							</div>
							<input type="hidden" name="_wp_http_referer"
								   value="<?php echo admin_url('admin.php?page=adpress-settings'); ?>"/>

							<p class="submit">
								<input name="Submit" type="submit" class="button-primary"
									   value="<?php esc_attr_e( 'Save Changes', 'wp-adpress' ); ?>"/>
							</p>
		</form>
<?php
		}
		/**
		 * Gateways page
		 */
		static function gateways_page()
		{
?>
<form action="options.php" method="POST">
<?php settings_fields('adpress_gateways_settings'); ?>

			<div class="c-block" style="width: 650px;">
				<div class="c-head">
					<?php do_settings_sections('adpress_gateways_form_general'); ?>
				</div>
							 <p class="submit">
								<input name="Submit" type="submit" class="button-primary"
									   value="<?php esc_attr_e( 'Save Changes', 'wp-adpress' ); ?>"/>
							</p>
</form>
<?php
			// Loop through the gateways and display the
			// Settings for each gateway
			$gateways = WPAD_Payment_Gateways::get_gateways();
			foreach ($gateways as $gateway) {
				$gateway->setup_form();
			}
?>

<?php
		}
		/**
		 * Image Ad Page
		 */
		static function image_ad_page()
		{
		}

		/**
		 * Link Ad Page
		 */
		static function link_ad_page()
		{
		}

		/**
		 * Flash Ad Page
		 */
		static function flash_ad_page()
		{
		}

		/**
		 * History Page
		 */
		static function history()
		{
			wp_adpress_history::generate_view();
		}

		/**
		 * Import/Export Page
		 */
		static function import()
		{
			
		}

		static function license()
		{
?>
		<form action="options.php" method="POST">
			<?php settings_fields('adpress_license_settings'); ?>
			<div class="c-block" style="width: 650px;">
				<div class="c-head">
					<?php do_settings_sections('adpress_license_form'); ?>
				</div>
				<input type="hidden" name="_wp_http_referer"
					   value="<?php echo admin_url('admin.php?page=adpress-settings&tab=license&action=license_save'); ?>"/>

				<p class="submit">
					<input name="Submit" type="submit" class="button-primary"
						   value="<?php esc_attr_e( 'Save Changes', 'wp-adpress' ); ?>"/>
				</p>
		</form>
<?php
		}
	}
}

