(function () {

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
    button.addEventListener('mouseup', function () {
      button.parentNode.removeChild(button);
      callback();
    });
    document.body.appendChild(button);
  };

  // Removing no-js class.
  document.documentElement.className += ' js';
  elementRemoveClass(document.documentElement, 'no-js');

  // Workaround for transition touch events.
  document.addEventListener('touchstart', function () {
  });

  // Search forms add focus class when focused element.
  (function () {
    document.querySelectorAll('.widget_search')
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
  }());

  // Auto-grow of comments.
  (function () {
    var commentTextArea = document.getElementById('comment');
    if (commentTextArea && commentTextArea.tagName === 'TEXTAREA') {
      commentTextArea.removeAttribute('rows');
      var recalcTextAreaHeight = function () {
        this.style.minHeight = 'auto';
        this.style.minHeight = this.scrollHeight + 'px';
      };
      commentTextArea.style.overflow = 'hidden';
      commentTextArea.addEventListener('change', recalcTextAreaHeight);
      commentTextArea.addEventListener('keypress', recalcTextAreaHeight);
      commentTextArea.addEventListener('keydown', recalcTextAreaHeight);
      commentTextArea.addEventListener('keyup', recalcTextAreaHeight);
    }
  }());

  // Avatar change based on inputed email.
  (function () {
      var respondForm = document.querySelector('#respond');
      if (respondForm) {
        var emailField = respondForm.querySelector('#email');
        var avatarImg = respondForm.querySelector('img.avatar');
        if (emailField && avatarImg) {
          var avatarSize = avatarImg.naturalWidth || avatarImg.width;
          console.log(avatarSize);
          var emailIsChanged = function () {
            if (avatarImg.hasAttribute('srcset')) {
              avatarImg.removeAttribute('srcset');
            }
            var newUrl = tinsta.siteUrl + '?tinsta-resolve-user-avatar=' + encodeURIComponent(this.value.trim());
            if (avatarSize) {
              avatarImg.srcset = newUrl + '&s=' + avatarSize + ', ' + newUrl + '&s=' + (avatarSize*2) + ' 2x';
              newUrl += '&s=' + avatarSize;
            } else {
              avatarImg.removeAttribute('srcset');
            }
            avatarImg.src = newUrl;
          };
          emailField.addEventListener('change', emailIsChanged);
          emailField.addEventListener('keyup', emailIsChanged);
        }
      }
  }());

  // Full Height.
  if (tinsta.fullHeight) {
    (function () {
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
        if (height > 0 && main[0].scrollHeight < height) {
          main[0].style.minHeight = height + 'px';
        }
      };

      fullHeightRecalc();
      window.addEventListener('load', fullHeightRecalc);
      window.addEventListener('resize', fullHeightRecalc);
      window.addEventListener('orientationchange', fullHeightRecalc);
    }());
  }

  // Agree accepted.
  (function () {
    if (!localStorage.getItem('agreeAccepted')) {
      var siteAgreementDialog = document.getElementById('site-enter-agreement');
      if (!siteAgreementDialog) {
        return;
      }
      siteAgreementDialog.style.display = 'block';
      document.getElementById('site-enter-agreement-button').addEventListener('mouseup', function (event) {
        event.preventDefault();
        siteAgreementDialog.className += ' agreed';
        setTimeout(function () {
          siteAgreementDialog.style.display = 'none';
          siteAgreementDialog.parentNode.removeChild(siteAgreementDialog);
        }, 150);
        localStorage.setItem('agreeAccepted', true);
      });
    }
  }());

  // Scrolltop.
  if (tinsta.scrolltop) {
    (function () {
      var button = document.createElement('div');
      button.className = 'scrolltop-button';
      button.innerText = tinsta.top;
      button.setAttribute('title', tinsta.top);
      button.addEventListener('mouseup', function () {
        var scrollStep = -window.scrollY / (250 / 30);
        var scrollInterval = setInterval(function () {
          if (window.scrollY !== 0) {
            window.scrollBy(0, scrollStep);
          }
          else {
            clearInterval(scrollInterval)
          }
        }, 30);
      });
      var check = function () {
        if (window.pageYOffset > (window.innerHeight / 2)) {
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

  // Init smoothscroll if available.
  (function () {
    if (typeof(jQuery) !== 'undefined' && typeof(jQuery.fn.niceScroll) !== 'undefined') {
      jQuery(document).ready(function () {
        jQuery('body').niceScroll();
      });
    }
  }());

  // Responsive menu.
  (function () {

    var mainWrapper = document.getElementsByClassName('site-container')[0];

    var menuItemsSelectors = [
      '.site-primary-menu-wrapper .root-menu-item.menu-item-type-tinsta-sidebar > .sub-menu',
      '.site-primary-menu-wrapper .root-menu-item.menu-item-has-children > .sub-menu'
    ];

    document.querySelectorAll(menuItemsSelectors.join(','))
      .forEach(function (item) {

        var isMega = true;
        var isMegaFromWidgets = false;

        if (!item.parentNode.className.match('menu-item-type-tinsta-nav-menu-widget-area')) {
          for (var i in item.children) {
            if (
              (
                !item.children.item(i).className.match('menu-item-has-children')
                && !item.children.item(i).className.match('menu-item-type-tinsta-sidebar')
              )
              && !item.children.item(i).className.match('widget')) {
              isMega = false;
            }
          }
        } else {
          isMegaFromWidgets = true;
        }

        if (isMega) {
          item.className += ' is-tinsta-mega';
          if (!isMegaFromWidgets) {
            item.className += ' is-tinsta-mega-submenus';
          }
          item.parentNode.addEventListener('mouseenter', function () {
            item.style.right = 'auto';
            item.style.left = 'auto';
            if (item.offsetWidth + item.offsetLeft > mainWrapper.offsetWidth) {
              item.style.right = 0;
            }
            if (item.offsetLeft < 10) {
              item.style.left = 0;
            }
          });
        }

        item.parentNode.addEventListener('click', function (event) {
          if (window.matchMedia('(max-width: ' + tinsta.breakpoints.tablet + ')').matches) {
            event.preventDefault();
            item.className += ' mobile-menu';
            addCloseButton(function () {
              elementRemoveClass(item, 'mobile-menu');
            });
          }
        });

      });

  }());


  // Legacy supports.
  (function () {

    /**
     * Because CSS.supports() may not be fully supported.
     *
     * @type bool
     */
    var cssSupports = null;
    if (typeof(CSS) && typeof(CSS) === 'function') {
      cssSupports = CSS.supports;
    } else {
      cssSupports = function (prop, value) {
        var d = document.createElement('div');
        d.style[prop] = value;
        return d.style[prop] === value;
      }
    }

    // Flex.
    if (!cssSupports('display', 'flex')) {
      (function () {
        var script = document.createElement('script');
        script.setAttribute('src', tinsta.assetsDir + 'scripts/flexibility.min.js');
        script.setAttribute('async', 'async');
        document.body.appendChild(script);
      }());
    }

    // Sticky.
    if (!cssSupports('position', 'sticky')) {
      (function () {
        var script = document.createElement('script');
        script.setAttribute('src', tinsta.assetsDir + 'scripts/sticky.min.js');
        script.setAttribute('async', 'async');
        document.body.appendChild(script);
      }());
    }

  }());

  ( function () {
    var topline = document.getElementsByClassName('site-topline-wrapper');
    var header = document.getElementsByClassName('site-header-wrapper');
    if (topline.length > 0 && header.length > 0) {
      var setHeaderTop = function () {
        if (window.getComputedStyle(topline[0]).position == 'sticky' && window.getComputedStyle(header[0]).position == 'sticky') {
          header[0].style.setProperty('top', topline[0].offsetHeight + 'px');
        } else {
          header[0].style.setProperty('top', null);
        }
      };
      window.addEventListener('load', setHeaderTop);
      window.addEventListener('resize', setHeaderTop);
      window.addEventListener('orientationchange', setHeaderTop);
      setHeaderTop();
    }
  } () );

}());
