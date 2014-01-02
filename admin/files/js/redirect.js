;
(function($) {
    $(document).ready(function() {
        if ($('#redirect_url').length > 0) {
            var redirect_url = $('#redirect_url').attr('href');
            window.location = redirect_url;
        }
    });
})(jQuery)