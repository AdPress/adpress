(function ($) {
    function AdDesigner() {
        /* Store the Elements */
        this.el = this.elements();
        /* Rows Selectors */
        this.rows_selectors();
        /* Buttons Binding */
        this.binding();
        /* WP Hijack */
        this.init();
    }

    /**
     * DOM Elements
     */
    AdDesigner.prototype.elements = function () {
        var el = {};
        // Form
        el.form = $('#addcampaign-form');

        // Select Boxes
        el.selects = {
            ad_type:$('#ad_type'),
            contract_type:$('#contract_type')
        };

        // Buttons
        el.buttons = {
            preview:$('#preview_btn'),
            submit:$('#new_campaign')
        };

        // CTA action
        el.cta = {
            toggle:$('#ad_cta'),
            slider:$('#cta_slider'),
            img_btn:$('#cta_image'),
            img_url:$('#cta_image_url'),
            img_input:$('#cta_image_input'),
            banner_btn:$('#cta_banner'),
            banner_url:$('#cta_banner_url'),
            banner_input:$('#cta_banner_input')

        };

        // Ad Rotation
        el.rotation = {
            toggle:$('#ad_rotation'),
            slider:$('#rotation_slider')
        };

        // Ad Preview
        el.preview = {
            img:{
                container:$('UL', '#image_ad'),
                boxes:$('.ad_box', '#image_ad')
            },
            link:{
                container:$('UL', '#link_ad')
            }
        };
        // TextBoxes
        el.textboxes = {
            ads_number:$('#ads_number'),
            ads_to_display:$('#rotation_number'),
            columns_number:$('.ad_columns'),
            ad_height:$('.ad_height'),
            ad_width:$('.ad_width'),
            link_length:$('#ad_link_length')
        };
        return el;
    };

    /**
     * Automated Rows Selectors
     */
    AdDesigner.prototype.rows_selectors = function () {
        var self = this;
        // Browse for possible Selectors
        $.each(self.el.selects, function () {
            /* Display the Default row */
            display_row(this.val(), this);
            /* Show on Change */
            this.change(function () {
                display_row($(this).val(), this);
            });
        });
        // Display a particular row
        function display_row(row, select) {
            $('option', select).each(function () {
                $('.' + $(this).val()).hide();
            });
            $('.' + row).show();
        }
    };

    /**
     * Buttons Binding
     */
    AdDesigner.prototype.binding = function () {
        var self = this;
        /* CTA Toggle */
        this.el.cta.toggle.click(function () {
            self.el.cta.slider.toggle();
        });
        this.el.cta.img_btn.click(function (e) {
            tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
            e.preventDefault();
        });
        this.el.cta.banner_btn.click(function (e) {
            tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
            e.preventDefault();
        });
        /* Ad Rotation Toggle */
        this.el.rotation.toggle.click(function () {
            self.el.rotation.slider.toggle();
        });
        /* Preview Button */
        this.el.buttons.preview.click(function () {
            self.preview_ad();
            return false;
        });

        /* Submit Button */
        this.el.buttons.submit.click(function () {
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
     * Preview Ad
     */
    AdDesigner.prototype.preview_ad = function () {
        var self = this,
            current_type = $(self.el.selects.ad_type).val();
        if (self.el.textboxes.ads_to_display.is(':visible')) {
            ads_number = parseInt(self.el.textboxes.ads_to_display.val(), 10);
        } else {
            ads_number = parseInt(self.el.textboxes.ads_number.val(), 10);
        }
        /* Image Ad Preview */
        if (current_type === 'for_image' || current_type === 'for_flash') {
            var ad_height = parseInt(self.el.textboxes.ad_height.filter(':visible').val(), 10),
                ad_width = parseInt(self.el.textboxes.ad_width.filter(':visible').val(), 10),
                columns_number = parseInt(self.el.textboxes.columns_number.filter(':visible').val(), 10);

            // Create the Boxes
            create_boxes(ads_number);
            // Set The container Width
            self.el.preview.img.container.width((ad_width + 12) * columns_number);
            // Set The box width
            $('.ad_box').css({
                'width':ad_width,
                'height':ad_height
            });
            /* Link Ad Preview */
        } else if (current_type === 'for_link') {
            create_links(ads_number);
        }
        function create_boxes(number) {
            $('.ad_box').remove();
            var $box = $('<li class="ad_box"></li>');
            for (var i = 0; i < number; i++) {
                self.el.preview.img.container.append($box.clone());
            }
        }

        function create_links(number) {
            $('.ad_link').remove();
            var $link = $('<li class="ad_link"><a href="#">Link Ad</a></li>');
            for (var i = 0; i < number; i++) {
                self.el.preview.link.container.append($link.clone());
            }
        }
    };

    /**
     * Hijack WordPress function
     */
    AdDesigner.prototype.hijack = function () {
        var self = this;
        window.send_to_editor = function (container) {
            if (self.el.cta.img_url.filter(':visible').length) {
                var imgurl = jQuery('img', container).attr('src');
                save_url(imgurl, self.el.cta.img_url, self.el.cta.img_input);
                tb_remove();
            } else if (self.el.cta.banner_url.filter(':visible').length) {
                var swfurl = $(container).attr('href');
                save_url(swfurl, self.el.cta.banner_url, self.el.cta.banner_input);
                tb_remove();
            }
        };
        // Save URL and displays it
        function save_url(url, dis, hid) {
            self.display_url(url, dis);
            dis.attr('value', url);
            hid.val(url);
        }
    };

    AdDesigner.prototype.display_url = function (url, dis) {
        var display_url = url,
            self = this;
        if (url.length > 20) {
            display_url = url.substr(0, 20) + '...';
        }
        dis.html(display_url);
    };

    /**
     * Validate Forms
     */
    AdDesigner.prototype.validate = function () {
        var validate = true;

        // Integer Validation
        $('.integer:visible').each(function () {
            if (!isNumber($(this).val())) {
                $(this).addClass('error');
                validate = false;
            } else {
                $(this).removeClass('error');
            }
        });

        // String Validation
        $('.string:visible').each(function () {
            if ($(this).val().length < 4) {
                $(this).addClass('error');
                validate = false;
            } else {
                $(this).removeClass('error');
            }
        });

        // URL Validation
        $('.url:visible').each(function () {
            if (isURL($(this).val()) === false) {
                $(this).addClass('error');
                validate = false;
            } else {
                $(this).removeClass('error');
            }
        });

        // Value URL validation
        $('.value_url:visible').each(function () {
            if (isURL($(this).attr('value')) === false) {
                $(this).addClass('error');
                validate = false;
            } else {
                $(this).removeClass('error');
            }
        });

        return validate;
        function isNumber(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        }

        function isURL(url) {
            return true;
        }
    };

    /**
     * Initializes Ad Designer
     */
    AdDesigner.prototype.init = function () {
        var self = this;
        // Hijack WordPress Media Uploader
        self.hijack();
        // Toggle the slider
        if (!self.el.cta.toggle.attr('checked')) {
            self.el.cta.slider.toggle();
        }
        if (!self.el.rotation.toggle.attr('checked')) {
            self.el.rotation.slider.toggle();
        }
        // If Image, display some of the url
        if (self.el.cta.img_input.val()) {
            self.display_url(self.el.cta.img_input.val(), self.el.cta.img_url);
        }
        if (self.el.cta.banner_input.val()) {
            self.display_url(self.el.cta.banner_input.val(), self.el.cta.banner_url);
        }
    };

    $(document).ready(function () {
        window.ad_designer = new AdDesigner();
    });
})(jQuery);