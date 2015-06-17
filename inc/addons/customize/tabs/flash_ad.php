<form action="options.php" method="POST">
			<?php settings_fields('adpress_flash_settings'); ?>
			<div class="c-block">
				<div class="c-head">
					<?php do_settings_sections('adpress_flash_ad_form'); ?>
				</div>
				<input type="hidden" name="_wp_http_referer"
					   value="<?php echo admin_url('admin.php?page=adpress-settings&tab=flash_ad'); ?>"/>

				<p class="submit">
					<input name="Submit" type="submit" class="button-primary"
						   value="<?php esc_attr_e( 'Save Changes', 'wp-adpress' ); ?>"/>
				</p>
		</form>
