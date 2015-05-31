<?php

add_action( 'wp_adpress_stats_body', 'wp_adpress_stats_graph', 10, 1 );

function wp_adpress_stats_graph( $ad ) {
?>
	<h2><?php _e('Last Month Stats', 'wp-adpress'); ?></h2>
<div id="fore-front">
	<div id="ex-chart" class="c-block">
		
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

add_action( 'wp_adpress_stats_body', 'wp_adpress_complete_stats', 100, 1 );
function wp_adpress_complete_stats( $ad ) {
	$html = '<h2>' . __('Complete Stats', 'wp-adpress') . '</h2><table id="stats-table" class="wp-list-table widefat plugins" cellspacing="0">';
	$html .= '<caption>' . __('Last Month Stats', 'wp-adpress') . '</caption>';
	$html .= '<thead>';
	$html .= '<tr><th>' . __('Day', 'wp-adpress') . '</th><th>' . __('Views', 'wp-adpress') . '</th><th>' . __('Hits', 'wp-adpress') . '</th><th>' . __('CTR', 'wp-adpress') . '</th></tr>';
	$html .= '</thead>';
	$html .= '<tbody>';
	$i = 1;
	foreach ($ad->stats['views'] as $date => $views) {
		if (!isset($views)) {
			$views = 0;
		}
		if (!isset($ad->stats['hits'][$date])) {
			$ad->stats['hits'][$date] = 0;
		}
		$html .= '<tr>';
		$html .= '<td>' . date('Y-m-d', strtotime($date)) . '</td>';
		$html .= '<td>' . $views . '</td>';
		$html .= '<td>' . $ad->stats['hits'][$date] . '</td>';
		$html .= '<td>' . wp_adpress_calculate_average($ad->stats['hits'][$date], $views) . '</td>';
		$html .= '</tr>';
	}
	$html .= '</tbody>';
	$html .= '<thead>';
	$html .= '<tr><th>Total</th><th>' . $ad->total_views() . '</th><th>' . $ad->total_hits() . '</th><th>' . wp_adpress_calculate_average( $ad->total_hits(),$ad->total_views() ) . '</th></tr>';
	$html .= '</thead>';
	$html .= '</table>';
	echo $html;
}

function wp_adpress_calculate_average( $hits, $views ) {
	if ($hits && $hits != 0) {
		return number_format(($hits / $views) * 100, 2) . '%';
	}
	return '0%';
}
