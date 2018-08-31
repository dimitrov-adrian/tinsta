document.addEventListener('DOMContentLoaded', function() {

  // Images.
  var lazyImages = [].slice.call(document.querySelectorAll('img[data-srcset],img[data-src],iframe[data-src],video[data-src]'));

  // IntersectionObserver version.
  if ('IntersectionObserver' in window) {
    var lazyImageObserver = new IntersectionObserver( function (entries, observer) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          var lazyImage = entry.target;
          if (lazyImage.dataset.hasOwnProperty('src') && lazyImage.dataset.src) {
            lazyImage.src = lazyImage.dataset.src;
          }
          if (lazyImage.dataset.hasOwnProperty('srcset') && lazyImage.dataset.srcset) {
            lazyImage.srcset = lazyImage.dataset.srcset;
          }
          lazyImageObserver.unobserve(lazyImage);
        }
      });
    });

    lazyImages.forEach(function(lazyImage) {
      lazyImage.className += ' lazy';
      lazyImage.addEventListener('load', function () {
        this.className += ' lazy-loaded';
      });
      lazyImageObserver.observe(lazyImage);
    });

  }

  // Simple fallback version.
  else {

    var checkImages = function () {
      for (var i = 0; i < lazyImages.length; i++) {
        if ( lazyImages[i].offsetTop >= window.scrollY * 0.9 && lazyImages[i].offsetTop <= ( window.scrollY + window.outerHeight ) * 1.1 ) {
          if (lazyImages[i].getAttribute('data-src')) {
            lazyImages[i].setAttribute('src', lazyImages[i].getAttribute('data-src'));
          }
          if (lazyImages[i].getAttribute('data-srcset')) {
            lazyImages[i].setAttribute('srcset', lazyImages[i].getAttribute('data-srcset'));
          }
          lazyImages.splice(i, 1);
        }
      }
    };

    lazyImages.forEach( function (lazyImage) {
      lazyImage.className += ' lazy';
      lazyImage.addEventListener('load', function () {
        this.className += ' lazy-loaded';
      });
    });

    window.addEventListener('scroll', checkImages);
    window.addEventListener('load', checkImages);
    window.addEventListener('orientationchange', checkImages);
    document.addEventListener('DOMContentLoaded', checkImages);
  }
});