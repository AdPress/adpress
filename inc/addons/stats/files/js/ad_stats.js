;
( function( $ ) {
	$( document ).ready( function() {
		//
		// Convert the Data
		//
		var views = [],
		hits = [];

		for ( var key in window.adpress_stats.views ) {
			if ( window.adpress_stats.views.hasOwnProperty( key ) ) {
				views.push( [ parseInt( key ) * 1000 , window.adpress_stats.views[ key ] ] );
			}
		}

		for ( var key in window.adpress_stats.hits ) {
			if ( window.adpress_stats.hits.hasOwnProperty( key ) ) {
				hits.push( [ parseInt( key ) * 1000 , window.adpress_stats.hits[ key ] ] );
			}
		}


		//
		// Charts Theme
		//
		Highcharts.theme = {
			colors: ["#f45b5b", "#8085e9", "#8d4654", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
			"#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
			chart: {
				backgroundColor: null,
				style: {
					fontFamily: "Signika, serif"
				}
			},
			title: {
				style: {
					color: 'black',
					fontSize: '16px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				style: {
					color: 'black'
				}
			},
			tooltip: {
				borderWidth: 0
			},
			legend: {
				itemStyle: {
					fontWeight: 'bold',
					fontSize: '13px'
				}
			},
			xAxis: {
				labels: {
					style: {
						color: '#6e6e70'
					}
				}
			},
			yAxis: {
				labels: {
					style: {
						color: '#6e6e70'
					}
				}
			},
			plotOptions: {
				series: {
					shadow: true
				},
				candlestick: {
					lineColor: '#404048'
				},
				map: {
					shadow: false
				}
			},

			// Highstock specific
			navigator: {
				xAxis: {
					gridLineColor: '#D0D0D8'
				}
			},
			rangeSelector: {
				buttonTheme: {
					fill: 'white',
					stroke: '#C0C0C8',
					'stroke-width': 1,
					states: {
						select: {
							fill: '#D0D0D8'
						}
					}
				}
			},
			scrollbar: {
				trackBorderColor: '#C0C0C8'
			},

			// General
			background2: '#E0E0E8'

		};

		// Apply the theme
		Highcharts.setOptions(Highcharts.theme);

		// Create the chart
		var a = $('#hc-holder').highcharts('StockChart', {


			rangeSelector : {
				selected : 1
			},


			series : [
				{
					name : 'Views',
					data : views,
					tooltip: {
						valueDecimals: 0
					}
				},
				{
					name : 'Clicks',
					data : hits,
					tooltip: {
						valueDecimals: 0
					}
				}
			]
		});

	} );
} )( jQuery );
