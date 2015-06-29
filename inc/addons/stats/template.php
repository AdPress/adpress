<?php

function wp_adpress_extended_stats_graph( $ad ) {
?>
	<div id="adpress-icon-analytics" class="icon32"><br></div>
	<h2><?php _e('Ad Analytics', 'wp-adpress'); ?></h2>
	<div id="fore-front">
		<div id="ex-chart" class="c-block">
		<div class="c-head">
		<h3><?php _e('Stats Chart', 'wp-adpress'); ?></h3>

		<div id="hc-holder" style="width:100%; height:400px;">

		</div>
</div>
		</div>
	<div id="avg" class="c-block">
		<div class="c-head"><h3><?php _e('Averages', 'wp-adpress') ?></h3></div>
		<ul>
		<li><strong><?php _e('Views', 'wp-adpress'); ?></strong><?php echo $ad->avg('views'); ?></li>
		<li><strong><?php _e('Clicks', 'wp-adpress'); ?></strong><?php echo $ad->avg('hits'); ?></li>
		<li><strong><?php _e('CTR', 'wp-adpress'); ?></strong><?php echo wp_adpress_calculate_average( $ad->total_hits(),$ad->total_views() ); ?></li>
		</ul>
	</div>
	<div style="clear:both"></div>
</div>
<?php
}
add_action( 'wp_adpress_stats_body', 'wp_adpress_extended_stats_graph', 10, 1 );
