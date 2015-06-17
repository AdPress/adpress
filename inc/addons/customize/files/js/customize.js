(function($) {
	$(document).ready(function() {
		if ( $( '#ad_loop' ).length > 0 ) {
			CodeMirror.fromTextArea($('#ad_loop')[0], {
				htmlMode: true,
				mode:  "xml"
			});
		}
		if ( $( '#ad_css' ).length > 0 ) {
			CodeMirror.fromTextArea($('#ad_css')[0], {
				mode:  "css"
			});
		}
	});
})(jQuery);
