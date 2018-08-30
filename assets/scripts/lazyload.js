document.addEventListener('DOMContentLoaded', function() {

  var lazyImages = [].slice.call(document.querySelectorAll('img[data-srcset],img[data-src],iframe[data-src],video[data-src]'));

  if ('IntersectionObserver' in window) {
    var lazyImageObserver = new IntersectionObserver(function(entries, observer) {
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
  } else {
    // @TODO
    // Possibly fall back to a more compatible method here
  }
});