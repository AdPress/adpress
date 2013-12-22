(function ($) {
    window.chart_settings = {
        days:30,
        hits:true,
        views:true
    };

    $(document).ready(function () {
        /*
         * Buttons Binding
         */
        $('#display_all').click(function () {
            window.chart_settings.days = -1;
            display_chart();
        });
        $('#display_month').click(function () {
            window.chart_settings.days = 31;
            display_chart();
        });
        $('#display_week').click(function () {
            window.chart_settings.days = 7;
            display_chart();
        });
        $('#toggle_hits').click(function () {
            window.chart_settings.hits = !window.chart_settings.hits;
            display_chart();
        });
        $('#toggle_views').click(function () {
            window.chart_settings.views = !window.chart_settings.views;
            display_chart();
        });

        /*
         * Display Chart Function
         */
        function display_chart() {
            /*
             * Chart Settings
             */
            var days = window.chart_settings.days,
                hits = window.chart_settings.hits,
                views = window.chart_settings.views;
            /*
             * Chart Options
             */
            var options = {
                colors:["#ee7951", "#afd8f8", "#cb4b4b", "#4da74d", "#9440ed"],
                legend:{
                    show:true,
                    noColumns:2, // number of colums in legend table
                    labelFormatter:null, // fn: string -> string
                    labelBoxBorderColor:"#ccc", // border color for the little label boxes
                    container:null, // container (as jQuery object) to put legend in, null means default on top of graph
                    position:"ne", // position of default legend container within plot
                    margin:[0, 0], // distance from grid edge to default legend container within plot
                    backgroundColor:"#fafafa", // null means auto-detect
                    backgroundOpacity:1 // set to 0 to avoid background
                },
                xaxis:{
                    show:null, // null = auto-detect, true = always, false = never
                    position:"bottom", // or "top"
                    mode:"time", // null or "time"
                    color:null, // base color, labels, ticks
                    tickColor:null, // possibly different color of ticks, e.g. "rgba(0,0,0,0.15)"
                    transform:null, // null or f: number -> number to transform axis
                    inverseTransform:null, // if transform is set, this should be the inverse function
                    min:null, // min. value to show, null means set automatically
                    max:null, // max. value to show, null means set automatically
                    autoscaleMargin:null, // margin in % to add if auto-setting min/max
                    ticks:null, // either [1, 3] or [[1, "a"], 3] or (fn: axis info -> ticks) or app. number of ticks for auto-ticks
                    tickFormatter:null, // fn: number -> string
                    labelWidth:null, // size of tick labels in pixels
                    labelHeight:null,
                    reserveSpace:null, // whether to reserve space even if axis isn't shown
                    tickLength:null, // size in pixels of ticks, or "full" for whole line
                    alignTicksWithAxis:null, // axis number or null for no sync
                    // mode specific options
                    tickDecimals:null, // no. of decimals, null means auto
                    tickSize:null, // number or [number, "unit"]
                    minTickSize:null, // number or [number, "unit"]
                    monthNames:null, // list of names of months
                    timeformat:null, // format string to use
                    twelveHourClock:false // 12 or 24 time in time mode
                },
                yaxis:{
                    autoscaleMargin:0.02,
                    position:"left" // or "right"
                },
                xaxes:[],
                yaxes:[],
                series:{
                    points:{
                        show:true,
                        radius:3,
                        lineWidth:2, // in pixels
                        fill:true,
                        fillColor:"#ffffff",
                        symbol:"circle" // or callback
                    },
                    lines:{
                        // we don't put in show: false so we can see
                        // whether lines were actively disabled
                        show:true,
                        lineWidth:2, // in pixels
                        fill:false,
                        fillColor:null,
                        steps:false
                    },
                    shadowSize:0
                },
                grid:{
                    show:true,
                    aboveData:false,
                    color:"#545454", // primary color used for outline and labels
                    backgroundColor:null, // null for transparent, else color
                    borderColor:"#e3e3e3", // set if different from the grid color
                    tickColor:"#e3e3e3", // color for the ticks, e.g. "rgba(0,0,0,0.15)"
                    labelMargin:1, // in pixels
                    axisMargin:5, // in pixels
                    borderWidth:0, // in pixels
                    minBorderMargin:10, // in pixels, null means taken from points radius
                    markings:null, // array of ranges or fn: axes -> array of ranges
                    markingsColor:"#f4f4f4",
                    markingsLineWidth:2,
                    // interactive stuff
                    clickable:false,
                    hoverable:true,
                    autoHighlight:true, // highlight in case mouse is near
                    mouseActiveRadius:5 // how far the mouse can be away to activate an item
                }
            };

            /*
             * Views Array
             */
            var views = [],
                i = 0;
            for (date in adpress_stats.views) {
                i++;
                var display_date = date * 1000;
                views.push([display_date, adpress_stats.views[date]]);
                if (i === days) {
                    break;
                }
            }

            /*
             * Hits Array
             */
            var hits = [],
                i = 0;
            for (date in adpress_stats.hits) {
                i++;
                var display_date = date * 1000;
                hits.push([display_date, adpress_stats.hits[date]]);
                if (i === days) {
                    break;
                }
            }

            /*
             * Display the chart
             */
            data = [];
            if (window.chart_settings.hits) {
                data.push({label:'Hits', data:hits})
            }
            if (window.chart_settings.views) {
                data.push({label:'Views', data:views})
            }
            $.plot($("#chart-placeholder"), data, options);
        }

        // Display the chart for the last 30 days
        display_chart();
    });
})(jQuery);