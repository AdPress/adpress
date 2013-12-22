(function($) {
    $(document).ready(function() {
        $('.expand').hide();
        /*
         * More Toggle
         */
        $('.more').toggle(function() {
            var class_name = '.'+$(this).attr('id');
            $(class_name).fadeIn();
            if ($(this).attr('tog_text')) {
                $(this).html($(this).attr('tog_text'));
            } else {
                $(this).html('Less');
            }
            return false;
        }, function() {
            var class_name = '.'+$(this).attr('id');
            $(class_name).fadeOut();
            if ($(this).attr('tog_text')) {
                $(this).html($(this).attr('tog_text'));
            } else {
                $(this).html('More');
            }
            return false;
        });
        
        /*
         * Less Button
         */
        $('.less').click(function() {
            var class_name = '.'+$(this).attr('id');
            $(class_name).fadeOut();
            return false;
        });
        /*
         * Remove Confirm
         */
        $('.remove_confirm', '.remove_tab').hide();
        $('.remove_button').click(function() {
            $('.remove_confirm', $(this).parent()).show();
            $(this).hide();
            return false;
        });
        $('.remove_cancel').click(function() {
            var rmv = $(this).parent();
            rmv.hide();
            $('.remove_button', rmv.parent()).show();   
            return false;
        });
    });
})(jQuery);