<form action="options.php" method="POST">
		<?php settings_fields('adpress_license_settings'); ?>
		<div class="c-block">
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
