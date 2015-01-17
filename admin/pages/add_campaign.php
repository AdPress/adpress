<?php
// Don't load directly
if (!defined('ABSPATH')) {
	die('-1');
}

global $wpadpress;
if (isset($_POST['new_campaign'])) {
	require_once('add_campaign_post.php');
} else {
	$save_button = __('Add Campaign', 'wp-adpress');
	$title = __('Add new Campaign', 'wp-adpress');
	$campaign = false;

	/*
	 * Checks if we are changing an existing campaign
	 */
	if (isset($_GET['cmd']) && $_GET['cmd'] === 'edit') {
		$campaign = new wp_adpress_campaign((int)$_GET['cid']);
		$campaign_id = (int)$_GET['cid'];
		$campaign_meta = unserialize( $campaign->ad_definition['campaign_meta'] );
		
		if ($campaign->state() === 'active') {
			$checked = 'checked';
		}
		if (isset($campaign->ad_definition['cta_url'])) {
			$cta = 'checked';
			$cta_url = $campaign->ad_definition['cta_url'];
		}
		if (isset($campaign->ad_definition['cta_fill'])) {
			$cta_fill = "checked";
		}
		switch ($campaign->ad_definition['type']) {
		case 'image':
			$image = 'selected';
			if (isset($campaign->ad_definition['cta_url'])) {
				$cta_img = $campaign->ad_definition['cta_img'];
			}
			break;
		case 'link':
			$link = 'selected';
			if (isset($campaign->ad_definition['cta_url'])) {
				$cta_text = $campaign->ad_definition['cta_text'];
			}
			break;
		case 'flash':
			$flash = 'selected';
			if (isset($campaign->ad_definition['cta_url'])) {
				$cta_banner = $campaign->ad_definition['cta_banner'];
			}
			break;
		}
		switch ($campaign->ad_definition['contract']) {
		case 'clicks':
			$clicks = 'selected';
			break;
		case 'pageviews':
			$pageviews = 'selected';
			break;
		case 'duration':
			$duration = 'selected';
			break;
		}
		if (isset($campaign->ad_definition['rotation'])) {
			$rotation = 'checked';
			$rotation_number = $campaign->ad_definition['rotation'];
		}
		$save_button = __('Save Campaign', 'wp-adpress');
		$title = __('Edit Campaign', 'wp-adpress');
		$hidden_keys = '<input type="hidden" name="edit_campaign" value="' . $campaign->id . '" />';
	}
	
?>


<div class="wrap" id="adpress">
	<div id="adpress-icon-addcampaign" class="icon32"><br></div>
	<h2><?php echo $title; ?></h2>

	<form method="post" id="addcampaign-form">

		<!-- Campaign Details -->
		<div class="c-block">
			<div class="c-head">
				<h3 id="adpress-icon-campaign_details"><?php _e('Campaign Details', 'wp-adpress'); ?></h3>
			</div>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row">
							<label for="campaign_name"><?php _e('Campaign Name', 'wp-adpress'); ?></label>
						</th>
						<td>
						<input type="textbox" name="campaign_name" id="campaign_name" value="<?php
	if ( $campaign ) {
		echo $campaign->settings['name'];
	}
?>" class="string"/>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="campaign_description"><?php _e('Campaign Description', 'wp-adpress'); ?></label>
						</th>
						<td>
						<textarea name="campaign_description" id="campaign_description"><?php
	if ( $campaign ) {
		echo $campaign->settings['description'];
	}
?></textarea>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="campaign_state"><?php _e('Campaign State', 'wp-adpress'); ?></label>
						</th>
						<td>
						<input type="checkbox" name="campaign_state" id="campaign_state" <?php
	if (isset($checked)) {
		echo $checked;
	}
?>/> Active
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<!-- Ad Designer -->
		<div class="c-block">
			<div class="c-head">
				<h3 id="adpress-icon-ad_designer">Ad Designer</h3>
			</div>
			<table class="form-table">
				<tbody>
					<!-- Select Type -->
					<tr valign="top">
						<th scope="row"><label for="ad_type"><?php _e('Ad Type', 'wp-adpress'); ?></label></th>
						<td>
							<select type="select" name="ad_type" id="ad_type">
							<option <?php
	if (isset($image)) {
		echo $image;
	}
	?> value="for_image"><?php _e('Image', 'wp-adpress'); ?>
	</option>
		<option <?php
	if (isset($link)) {
		echo $link;
	}
	?> value="for_link"><?php _e('Link', 'wp-adpress'); ?>
	</option>
		<option <?php
	if (isset($flash)) {
		echo $flash;
	}
	?> value="for_flash"><?php _e('Flash', 'wp-adpress'); ?>
	</option>
		</select>
		</td>
		</tr>

		<!-- Options for Image Type -->
		<tr class="for_image" valign="top">
		<th scope="row">
		<label for="ad_image_height"><?php _e('Image Size', 'wp-adpress'); ?></label>
		</th>
		<td>
		<input type="textbox" name="ad_image_width" id="ad_image_width" class="ad_width integer text-number"
		value="<?php
	if (isset($campaign->ad_definition['size']['width']) && $campaign->ad_definition['type'] === 'image') {
		echo $campaign->ad_definition['size']['width'];
	}
?>"/> X <input type="textbox" name="ad_image_height" id="ad_image_height"
	class="ad_height integer text-number" value="<?php
	if (isset($campaign->ad_definition['size']['height']) && $campaign->ad_definition['type'] === 'image') {
		echo $campaign->ad_definition['size']['height'];
	}
?>"/>
						</td>
					</tr>
					<tr class="for_image" valign="top">
						<th scope="row">
							<label for="ad_image_columns"><?php _e('Columns Number', 'wp-adpress'); ?></label>
						</th>
						<td>
							<input type="textbox" name="ad_image_columns" id="ad_image_columns" class="ad_columns integer text-number"
							value="<?php
	if (isset($campaign->ad_definition['columns']) && $campaign->ad_definition['type'] === 'image') {
		echo $campaign->ad_definition['columns'];
	}
?>"/>
						</td>
					</tr>

					<!-- Options for Link Type -->
					<tr class="for_link" valign="top">
						<th scope="row">
							<label for="ad_link_length"><?php _e('Maximum link length', 'wp-adpress'); ?></label>
						</th>
						<td>
						<input type="textbox" name="ad_link_length" id="ad_link_length" class="integer text-number" value="<?php
	if (isset($campaign->ad_definition['length'])) {
		echo $campaign->ad_definition['length'];
	}
?>"/>
						</td>
					</tr>

					<!-- Options for Flash Type -->
					<tr class="for_flash" valign="top">
						<th scope="row">
							<label for="ad_banner_width"><?php _e('Banner Size', 'wp-adpress'); ?></label>
						</th>
						<td>
							<input type="textbox" name="ad_banner_width" id="ad_banner_width" class="ad_width integer text-number"
							value="<?php
	if (isset($campaign->ad_definition['size']['width']) && $campaign->ad_definition['type'] === 'flash') {
		echo $campaign->ad_definition['size']['width'];
	}
?>"/> X <input type="textbox" name="ad_banner_height" id="ad_banner_height"
	class="ad_height integer text-number" value="<?php
	if (isset($campaign->ad_definition['size']['height']) && $campaign->ad_definition['type'] === 'flash') {
		echo $campaign->ad_definition['size']['height'];
	}
?>"/>
						</td>
					</tr>
					<tr class="for_flash" valign="top">
						<th scope="row">
							<label for="ad_banner_columns"><?php _e('Columns Number', 'wp-adpress'); ?></label>
						</th>
						<td>
							<input type="textbox" name="ad_banner_columns" id="ad_banner_columns" class="ad_columns integer text-number"
							value="<?php
	if (isset($campaign->ad_definition['columns']) && $campaign->ad_definition['type'] === 'flash') {
		echo $campaign->ad_definition['columns'];
	}
?>"/>
						</td>
					</tr>
					<!-- Common Options -->
					<tr valign="top">
						<th scope="row">
							<label for="ads_number"><?php _e('Ads number', 'wp-adpress'); ?></label>
						</th>
						<td>
						<input type="textbox" name="ads_number" id="ads_number" class="integer text-number" value="<?php
	if ( $campaign ) {
		echo $campaign->ad_definition['number'];
	}
?>"/>
						</td>
					</tr>

				</tbody>
			</table>

			<!-- CTA Slider -->
			<div class="toggle">
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="ad_cta"><?php _e('Ad CTA', 'wp-adpress'); ?></label>
						</th>
						<td>
							<input type="checkbox" class="toggle-check" name="ad_cta" id="ad_cta" <?php if (isset($cta)) {echo $cta;} ?>/>
						</td>
					</tr>
				</table>
				<table class="form-table push_1 toggle-content" id="cta_slider">
					<tbody>
						<tr valign="top" class="for_image">
							<th scope="row">
								<label for="cta_image">CTA Image</label>
							</th>
							<td>
								<a href="" class="button-secondary" id="cta_image">Select Image</a> <span id="cta_image_url"
									class="value_url"
									value="<?php if (isset($cta_img)) {echo $cta_img;} ?>">[no selection]</span>
								<input type="hidden" id="cta_image_input" name="cta_image" value="<?php  if (isset($cta_img)) {echo $cta_img;} ?>"/>
							</td>
						</tr>
						<tr valign="top" class="for_link">
							<th scope="row">
								<label for="cta_text">CTA Text</label>
							</th>

							<td>
								<input type="textbox" name="cta_text" id="cta_text" class="string" value="<?php  if (isset($cta_text)) {echo $cta_text;} ?>"/>
							</td>
						</tr>
						<tr valign="top" class="for_flash">
							<th scope="row">
								<label for="cta_image">CTA banner</label>
							</th>
							<td>
								<a href="" class="button-secondary" id="cta_banner">Select SWF</a> <span id="cta_banner_url"
									value="<?php  if (isset($cta_banner)) {echo $cta_banner;} ?>">[no selection]</span>
								<input type="hidden" id="cta_banner_input" name="cta_banner" value="<?php  if (isset($cta_banner)) {echo $cta_banner;} ?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="target_url">Target URL</label>
							</th>

							<td>
								<input type="textbox" name="target_url" id="target_url" class="url" value="<?php if (isset($cta_url)) {echo $cta_url;} ?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="cta_fill">Fill All spots</label>
							</th>

							<td>
								<input type="checkbox" name="cta_fill" id="cta_fill" <?php if (isset($cta_fill)) {echo $cta_fill;} ?>/>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- Rotation Slider -->
			<div class="toggle">
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="ad_rotation"><?php _e('Ad rotation', 'wp-adpress'); ?></label>
							</th>
							<td>
								<input type="checkbox" class="toggle-check" name="ad_rotation" id="ad_rotation" <?php if (isset($rotation)) {echo $rotation;} ?>/>
							</td>
						</tr>
					</tbody>
				</table>
				<table class="form-table push_1 toggle-content" id="rotation_slider">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="rotation_number">Ads to Display</label>
							</th>
							<td>
								<input type="textbox" name="rotation_number" id="rotation_number" class="text-number integer" value="<?php if (isset($rotation_number)) {echo $rotation_number;} ?>"/>
							</td>
						</tr>
					</tbody>
				</table>
			</div>	

			<?php echo apply_filters( 'wp_adpress_new_campaign_ad_designer', '', $campaign ); ?>

		</div>

		<!-- Contract Details -->
		<div class="c-block">
			<div class="c-head">
				<h3 id="adpress-icon-contract_details"><?php _e('Contract details', 'wp-adpress'); ?></h3>
			</div>
			<table class="form-table">
				<tbody>
					<tr valign="top" valign="top">
						<th scope="row">
							<label for="contract_type"><?php _e('Contract Type', 'wp-adpress'); ?></label>
						</th>
						<td>
							<select name="contract_type" id="contract_type">
							<option <?php
	if (isset($clicks)) {
		echo $clicks;
	}
	?> value="for_clicks"><?php _e('Per Clicks', 'wp-adpress'); ?></option>
		<option <?php
	if (isset($pageviews)) {
		echo $pageviews;
	}
	?> value="for_pageviews"><?php _e('Per Pageviews', 'wp-adpress'); ?></option>
		<option <?php
	if (isset($duration)) {
		echo $duration;
	}
	?> value="for_duration"><?php _e('Duration', 'wp-adpress'); ?></option>
		</select>
		</td>
		</tr>
		<tr class="for_clicks" valign="top">
		<th scope="row">
		<label for="contract_type_clicks"><?php _e('Clicks Number', 'wp-adpress'); ?></label>
		</th>
		<td>
		<input type="textbox" name="contract_type_clicks" id="contract_type_clicks" class="integer text-number"
		value="<?php
	if (isset($campaign->ad_definition['clicks'])) {
		echo $campaign->ad_definition['clicks'];
	}
?>"/>
						</td>
					</tr>
					<tr class="for_pageviews" valign="top">
						<th scope="row">
							<label for="contract_type_pageviews"><?php _e('Pageviews Number', 'wp-adpress'); ?></label>
						</th>
						<td>
							<input type="textbox" name="contract_type_pageviews" id="contract_type_pageviews"
							class="integer text-number" value="<?php
	if (isset($campaign->ad_definition['pageviews'])) {
		echo $campaign->ad_definition['pageviews'];
	}
?>"/>
						</td>
					</tr>
					<tr class="for_duration" valign="top">
						<th scope="row">
							<label for="contract_type_duration"><?php _e('Duration', 'wp-adpress'); ?></label>
						</th>
						<td>
							<input type="textbox" name="contract_type_duration" id="contract_type_duration"
							class="integer text-number" value="<?php
	if (isset($campaign->ad_definition['duration'])) {
		echo $campaign->ad_definition['duration'];
	}
?>"/> Days
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="contract_price"><?php _e('Price', 'wp-adpress'); ?></label>
						</th>
						<td>
						<input type="textbox" name="contract_price" id="contract_price" class="integer text-number" value="<?php
	if ( $campaign ) {
		echo $campaign->ad_definition['price'];
	}
	?>"/> <?php
	if (isset($wpadpress)) {
		echo $wpadpress->settings['currency'];
	}
?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<!-- Submit buttons -->
		<p class="submit">
<?php
	if (isset($hidden_keys)) {
		echo $hidden_keys;
	}
?>
		<input type="submit" name="new_campaign" id="new_campaign" class="button-primary"
		value="<?php echo $save_button; ?>"/>
		</p>
	</form>

	<!-- Preview Ads -->
	<div id="ad_preview" class="c-block">
		<div class="c-head">
			<h3 id="adpress-icon-ad_preview">Ad Preview</h3> <a href="#" id="preview_btn"
				class="button-secondary"><?php _e('Preview', 'wp-adpress'); ?></a>
		</div>
		<div style="clear:both"></div>
		<div id="image_ad" class="for_image for_flash">
			<ul>
			</ul>
		</div>
		<div id="link_ad" class="for_link">
			<ul>
			</ul>
		</div>
	</div>
</div>
<?php
}
?>
