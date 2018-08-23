(function($, d, w) {
  var alreadyLoaded = false;

  $(document).on('ready', function() {
    if (alreadyLoaded) {
      return;
    }
    alreadyLoaded = true;

    var timeThreshold = 200;
    var distanceThreshold = parseInt($(w).height()/2);
    var distanceThresholdPlayVideo = $('.header-container').is(':visible') ? $('.header-container').outerHeight() : $('#header-container').outerHeight();

    var elementSelector = 'img[data-srcset],img[data-src],iframe[data-src],video[data-src]';

    // Gray image
    // var placeholderImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC';

    // Transparent gif
    // var placeholderImg = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAAAAAA6fptVAAAACklEQVQYV2P4DwABAQEAWk1v8QAAAABJRU5ErkJggg==';

    var $w = $(w);
    var $elements = $([]);
    var videos = [];

    /**
     * Set element brllTop and brllBottom
     *
     * @param element
     */
    var setElementPosition = function(element) {
      var $element = $(element);
      // @TODO refactor widget templates and not render both mobile and default.
      // we should turn this on because have bad implementation for product matrix and widgets,
      // that render both mobile and default templates and show/hide acording viewport size.
      if (false && !$element.is(':visible')) {
        element.brllTop = $element.closest(':visible').offset().top;
        element.brllHeight = $element.closest(':visible').outerHeight();
        element.brllBottom = element.brllTop + element.brllHeight;
      }
      else {
        element.brllTop = $element.offset().top;
        element.brllHeight = $element.outerHeight();
        element.brllBottom = element.brllTop + element.brllHeight;
      }
    };

    /**
     * Update elements from the list
     */
    var updateElements = function() {
      $elements = $(elementSelector, d)
        .filter(function() {
          // use same callback to save iterations.
          // It have delay because accelerated scroll in some browsers,
          // need to do it on iteration.
          // setElementPosition($(this));

          // filters
          return !this.hasOwnProperty('lazyLoadProcessed');
        });
    };

    /**
     * Element loader
     *
     * @param $element
     */
    var elementLoader = function($element) {
      if ($element.prop('tagName') === 'VIDEO') {
        if ($element.length) {
          var sources = $element.data('src').split(',');
          $.each(sources, function(index, value){
            $element.append('<source src="' + decodeURIComponent(value) + '">');
          });
        }
      } else {
        $element
          .attr('src', $element.attr('data-src') || '')
          .attr('src-set', $element.attr('data-srcset') || '')
          .one("load", function() {
            if ($(this).hasClass('clr')) {
              $(this).animate({opacity: 1}, 'fast');
            }
          });

      }

    };

    /**
     * Elements processor
     *
     * @param viewportTop
     */
    var processElements = function(viewportTop) {

      if (!viewportTop) {
        viewportTop = $w.scrollTop();
      }
      var viewportHeight = $w.outerHeight();
      var viewportBottom = viewportTop + viewportHeight;

      // Lazy load of objects files (images, videos, iframes).
      if ($elements.length > 0) {
        $elements
          .each(function() {
            setElementPosition(this);
            if (viewportTop <= this.brllBottom + distanceThreshold && viewportBottom >= this.brllTop - distanceThreshold) {
              $this = $(this);
              this.lazyLoadProcessed = true;
              if ($this.prop('tagName') === 'VIDEO') {
                // Add video in the queue for lazy play
                videos.push(this);
              }

              elementLoader($this);
            }
          });
      }

      // Only videos loaded in previous check, will be processed.
      $(videos).each(function() {
        setElementPosition(this);
        // local threshold to avoid unable to fit video in screen.
        var localThreshold = this.brllHeight * 1.6 >= viewportHeight ? this.brllHeight / 10 : this.brllHeight/1.8;
        if (viewportTop + distanceThresholdPlayVideo <= this.brllBottom - localThreshold && viewportBottom >= this.brllTop + localThreshold) {
          if (this.paused) {
            // $(this).attr('status', 'play');
            var autoplay = !! $(this).attr('data-autoplay') || $(this).attr('autoplay');
            setTimeout(function() {
              if(autoplay !== 'false'){
                if (!(this.currentTime > 0 && this.paused && this.ended && this.readyState >= 2)) {
                  this.play();
                }
              }
              // This should be enough to prevent conflicts in fast attempt to play/pause for regular users.
            }.bind(this), 100);
          }
        }
        else if (!this.paused) {
          // $(this).attr('status', 'pause');
          if (this.currentTime > 0 && !this.paused && !this.ended && this.readyState > 2) {
            this.pause();
          }
        }
      });

      updateElements();
    };

    // Update elements.
    updateElements();

    // Set placeholders for elements.
    // $elements
    //   .filter('video')
    //   .not('[poster]')
    //   .attr('poster', placeholderImg)
    //   .attr('preload', 'none');

    // Run processor on every timeThreshold while scrolling.
    var scrollTimer = null;
    var scrollTimerControl = null;
    $w
      .on('scroll', function() {
        if (!scrollTimer) {
          scrollTimer = setInterval(processElements, timeThreshold);
        }
        if (scrollTimerControl) {
          clearTimeout(scrollTimerControl);
        }
        scrollTimerControl = setTimeout(function() {
          clearInterval(scrollTimer);
          scrollTimer = null;
        }, timeThreshold);
      })
      .trigger('scroll');

    // Predict anchors behaviours.
    var el = document.getElementById(window.location.hash.substr(1));
    if (!el) {
      el = $('[data-anchor="' + window.location.hash.substr(1) + '"]');
      if (!el.length) {
        el = null;
      }
    }
    else {
      el = $(el);
    }
    if (el) {
      processElements(el.offset().top);
    }

  });

}(jQuery, document, window));
