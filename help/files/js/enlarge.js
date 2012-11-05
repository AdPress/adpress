;
(function($) {
  $(window).load(function() {
      $('.enlarge').each(function() {
        var container = $('<div></div>'),
            height = $(this).height(),
            width = $(this).width()
            zoom = $('<div></div>'),
            self = this;
        container.css({
          'margin': '0 auto',
          'height': height,
          'width': width,
          'position': 'relative',
          'cursor': 'pointer'
        });
        zoom.css({
          'background-image' : 'url(files/img/zoom.png)',
          'height': 40,
          'width': 40,
          'position': 'absolute',
          'top':height/2-20,
          'left': width/2-20,
          'opacity': 0.3
        }).addClass('zoom');
        container.hover(function() {
          $('.zoom', this).stop().animate({
            'opacity': 1
          }, 500);
        }, function() {
          $('.zoom', this).stop().animate({
            'opacity': 0.5
          }, 500);
            
        });
        container.click(function() {
          var zoomed = $('<div></div>'),
              image_url = $('img', this).attr('src').replace('_thumb',''),
              img = $('<img/>'),
              height = $(window).height(),
              width = $(window).width();
          zoomed.css({
            'position': 'fixed',
            'top': 0,
            'left': 0,
            'height': '100%',
            'width': '100%',
            'background-color': 'rgba(0, 0, 0, 0.7)',
            'text-align': 'center',
            'cursor': 'pointer'
          });
          img.attr('src', image_url);
          img.on('load', function() {
            var self_img = $(this),
                img_height = $(this).height()
                img_width = $(this).width();
            $(this).css({
              'position': 'absolute',
              'top': (height-$(this).height())/2,
              'left': (width-$(this).width())/2
            });
            $(window).on('resize', function() {
              self_img.css({
                'top': ($(this).height()-self_img.height())/2,
                'left': ($(this).width()-self_img.width())/2,
                'height': img_height,
                'width': img_width
              });
              if ($(this).height()<self_img.height()) {
                self_img.css({
                  'height': $(this).height(),
                  'width': 'auto'
                });
              }
              if ($(this).width()<self_img.width()) {
                self_img.css({
                  'width': $(this).width(),
                  'height': 'auto'
                });
              }
            });
          });
          img.appendTo(zoomed);
          zoomed.click(function() {
            $(this).remove();
          });
          zoomed.appendTo('body');
        });
        $(this).wrap(container);
        zoom.prependTo($(this).parent());

      });
  });
})(jQuery);