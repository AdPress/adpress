(function ($) {
    /**
     * AdPurchase JS View
     */
    function AdPurchase() {
        this.el = this.elements();
        this.wordpress_hijack();
        this.events();
    }

    /**
     * Store HTML elements
     */
    AdPurchase.prototype.elements = function () {
        var el = {};
        el.upload = {
            button:$('#upload_btn'),
            field:$('#upload_image'),
            textbox:$('#image_link')
        };
        el.flash = {
            button:$('#upload_btn'),
            field:$('#upload_flash'),
            textbox:$('#flash_link')
        };
        el.link_text = $('#link_text');
        el.destination_url = $('#destination_url');
        el.submit = $('#submit_purchase');
        el.form = $('#purchase-form');
        return el;
    };
    /**
     * Bind Events
     */
    AdPurchase.prototype.events = function () {
        var self = this;
        this.el.upload.button.click(function () {
            wp.media.editor.open(this);
            return false;
        });
        this.el.submit.click(function () {
            if (!self.validate()) {
                self.el.form.bind('submit', function () {
                    return false;
                });
            } else {
                self.el.form.unbind('submit');
            }
        });
    };
    /**
     * Change WordPress functions
     */
    AdPurchase.prototype.wordpress_hijack = function () {
        var self = this;

        window.send_to_editor = function (container) {
            if (self.el.upload.field.length > 0) {
                var imgurl = jQuery('img', container).attr('src');
                self.el.upload.field.val(imgurl);
                self.el.upload.textbox.val(imgurl);
                self.el.upload.field.addClass('ready');
            } else {
                var flashurl = jQuery(container).attr('href');
                self.el.flash.field.val(flashurl);
                self.el.flash.textbox.val(flashurl);
                self.el.flash.field.addClass('ready');
            }
        };
    };
    /**
     * Validate UserInput
     */
    AdPurchase.prototype.validate = function () {
        var self = this;
        var validate = true;
        // Validate Image URL
        self.el.upload.field.filter(':visible').each(function () {
            if ($(this).hasClass('ready')) {
                $(this).removeClass('error');
            } else {
                $(this).addClass('error');
                validate = false;
            }
        });
        // Validate Flash URL
        self.el.flash.field.filter(':visible').each(function () {
            if ($(this).hasClass('ready')) {
                $(this).removeClass('error');
            } else {
                $(this).addClass('error');
                validate = false;
            }
        });
        // Validate link Char length
        self.el.link_text.filter(':visible').each(function () {
            if ($(this).val().length < parseInt($(this).attr('length'), 10) + 1 && $(this).val().length > 0) {
                $(this).removeClass('error');
            } else {
                $(this).addClass('error');
                validate = false;
            }
        });
        // Validate Destination URL
        self.el.destination_url.filter(':visible').each(function () {
            //var filter = /http:\/\/[A-Za-z0-9\.-]{3,}\.[A-Za-z]{2}/;
            if ($(this).val() != '') {
                $(this).removeClass('error');
            } else {
                $(this).addClass('error');
                validate = false;
            }
        });
        return validate;
    };
    $(document).ready(function () {
        AdPurchase = new AdPurchase();
    });
})(jQuery);

 