(function () {

  // It's more convinion to use classList but it's not supported from some old browsers.
  var elementRemoveClass = function (element, className) {
    var classes = element.className.split(' ').filter(function (localClassName) {
      return localClassName !== className;
    });
    element.className = classes.join(' ');
  };

  // Add one time closing button.
  var addCloseButton = function (callback, closeText) {
    var button = document.createElement('div');
    button.innerText = closeText || tinsta.closeLabel;
    button.className = 'mobile-back-button';
    button.addEventListener('click', function () {
      this.parentNode.removeChild(this);
      callback();
    });
    document.body.appendChild(button);
  };

  // Removing no-js class.
  document.documentElement.className += ' js';
  elementRemoveClass(document.documentElement, 'no-js');

  // Workaround for transition touch events.
  document.addEventListener('touchstart', function () {} );

  // Responsive menu.
  document.addEventListener('DOMContentLoaded', function () {
    this.querySelectorAll('.menu > li.menu-item-has-children > a').forEach(function (menuElement) {
      menuElement.addEventListener('click', function (event) {
        if (window.matchMedia('(max-width: ' + tinsta.breakpoints.mobile + ')').matches) {
          event.stopPropagation();
          event.preventDefault();
          menuElement.parentNode.className += ' sub-menu-overlay-active';
          addCloseButton(function () {
            console.log(menuElement);
            elementRemoveClass(menuElement.parentNode, 'sub-menu-overlay-active');
          });
        }
      });
    });
  }, {once: true});

  // Search forms add focus class when focused element.
  document.addEventListener('DOMContentLoaded', function () {
    this.querySelectorAll('.widget_search')
      .forEach(function (widget) {
        var field = widget.querySelectorAll('.search-field');
        if (field.length < 1) {
          return;
        }
        field = field[0];
        field.addEventListener('keydown', function (event) {
          if (event.which === 27) {
            elementRemoveClass(this, 'focus');
            field.blur();
          }
        });
        field.addEventListener('focus', function () {
          widget.className += ' focus';
        });
        field.addEventListener('blur', function () {
          elementRemoveClass(widget, 'focus');
        });
      });
  }, {once: true});

  // Auto-grow of comments.
  document.addEventListener('DOMContentLoaded', function () {
    var commentTextArea = document.getElementById('comment');
    if (commentTextArea && commentTextArea.tagName === 'TEXTAREA') {
      commentTextArea.removeAttribute('rows');
      var recalcTextAreaHeight = function () {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
      };
      commentTextArea.style.overflow = 'hidden';
      commentTextArea.addEventListener('change', recalcTextAreaHeight);
      commentTextArea.addEventListener('keypress', recalcTextAreaHeight);
      commentTextArea.addEventListener('keydown', recalcTextAreaHeight);
      commentTextArea.addEventListener('keyup', recalcTextAreaHeight);
    }
  }, {once: true});

  // Full Height.
  document.addEventListener('DOMContentLoaded', function () {
    var body = this.querySelectorAll('body.full-height');
    if (body.length > 0) {

      var fullHeightRecalc = function () {

        var height = 0;
        var main = document.getElementsByClassName('site-container-wrapper');
        if (main.length < 1) {
          return;
        }
        height = window.innerHeight - (document.body.offsetHeight - main[0].offsetHeight);
        var wpAdminBar = document.getElementById('wpadminbar');
        if (wpAdminBar) {
          height -= wpAdminBar.offsetHeight;
        }

        if (height > 0) {
          main[0].style.minHeight = height + 'px';
        }
      };

      fullHeightRecalc();
      window.addEventListener('load', fullHeightRecalc);
      window.addEventListener('resize', fullHeightRecalc);
      window.addEventListener('orientationchange', fullHeightRecalc);
    }

  }, {once: true});

  // Agree accepted.
  document.addEventListener('DOMContentLoaded', function () {
    if (!localStorage.getItem('agreeAccepted')) {
      var siteAgreementDialog = document.getElementById('site-enter-agreement');
      if (!siteAgreementDialog) {
        return;
      }
      siteAgreementDialog.style.display = 'block';
      document.getElementById('site-enter-agreement-button').addEventListener('click', function (event) {
        event.preventDefault();
        siteAgreementDialog.className += ' agreed';
        setTimeout(function () {
          siteAgreementDialog.style.display = 'none';
          siteAgreementDialog.parentNode.removeChild(siteAgreementDialog);
        }, 150);
        localStorage.setItem('agreeAccepted', true);
      });
    }
  }, {once: true});

  // Scrolltop.
  if (tinsta.scrolltop) {
    (function () {
      var button = document.createElement('div');
      button.className = 'scrolltop-button';
      button.innerText = tinsta.scrolltop;
      button.setAttribute('title', tinsta.top);
      button.addEventListener('click', function () {
        var scrollStep = -window.scrollY / (250 / 15);
        var scrollInterval = setInterval(function () {
          if (window.scrollY !== 0) {
            window.scrollBy(0, scrollStep);
          }
          else {
            clearInterval(scrollInterval)
          }
        }, 15);
      });
      var check = function () {
        if (window.pageYOffset > window.innerHeight) {
          button.style.display = 'block';
        }
        else {
          button.style.display = 'none';
        }
      };
      document.body.appendChild(button);
      window.addEventListener('scroll', check);
      setTimeout(check, 100);
    }());

  }

  /**
   * Makes "skip to content" link work correctly in IE9, Chrome, and Opera
   * for better accessibility.
   *
   * @link http://www.nczonline.net/blog/2013/01/15/fixing-skip-to-content-links/
   */
  (function () {
    var ua = navigator.userAgent.toLowerCase();
    if ((ua.indexOf('webkit') > -1 || ua.indexOf('opera') > -1 || ua.indexOf('msie') > -1) &&
      document.getElementById && window.addEventListener) {
      window.addEventListener('hashchange', function () {
        var element = document.getElementById(location.hash.substring(1));
        if (element) {
          if (!/^(?:a|select|input|button|textarea)$/i.test(element.nodeName)) {
            element.tabIndex = -1;
          }
          element.focus();
        }
      }, false);
    }
  })();

  // Enable masonry.
  // document.addEventListener('DOMContentLoaded', function () {
  //   if (typeof(Masonry) !== 'undefined') {
  //     var elements = document.querySelector('.masonry');
  //     var masonry = new Masonry(elements.parentNode, {
  //       // gutter: 10,
  //       fitWidth: true,
  //       // transitionDuration: 0,
  //       // resize: false,
  //       //percentPosition: true,
  //       itemSelector: '.masonry'
  //     });
  //   }
  // });

  // Init smoothscroll if available.
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof(jQuery) !== 'undefined' && typeof(jQuery.fn.niceScroll) !== 'undefined') {
      jQuery(document).ready(function () {
        jQuery('body').niceScroll();
      });
    }
  });

}());
