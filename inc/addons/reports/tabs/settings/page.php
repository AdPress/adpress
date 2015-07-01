<?php
/**
* Reports - Settings Page 
*
* @package    	Addons 
* @subpackage 	Reports 
* @since       1.1.0
*/

// Don't load directly
if (!defined('ABSPATH')) {
die('-1');
}
?>
<div id="adpress-icon-settings" class="icon32"><br></div>
<h2><?php _e( 'Settings', 'wp-adpress' ); ?></h2>
<form action="options.php" method="POST">
	<?php settings_fields( 'wpad_reports_settings' ); ?>
	<div class="c-block">
		<div class="c-head">
			<?php do_settings_sections( 'adpress_reports_settings_form_history' ); ?>
		</div>
		<input type="hidden" name="_wp_http_referer"
		value="<?php echo admin_url( 'admin.php?page=adpress-reports&tab=settings' ); ?>"/>

		<p class="submit">
		<input name="Submit" type="submit" class="button-primary"
		value="<?php esc_attr_e( 'Save Changes', 'wp-adpress' ); ?>"/>
		</p>
	</form>
