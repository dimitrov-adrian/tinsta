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

  /**
   * Legacy browsers supports.
   */
  (function () {

    // Flex.
    if (!cssSupports('display', 'flex')) {
      ( function () {
        var script = document.createElement('script');
        script.setAttribute('src', tinsta.assetsDir + 'js/flexibility.min.js');
        script.setAttribute('async', 'async');
        document.body.appendChild(script);
      }() );
    }

    // Sticky.
    if (!cssSupports('position', 'sticky')) {
      ( function () {
        var script = document.createElement('script');
        script.setAttribute('src', tinsta.assetsDir + 'js/sticky.min.js');
        script.setAttribute('async', 'async');
        document.body.appendChild(script);
      }() );
    }

  }());

  /**
   * It's more convinion to use classList but it's not supported from some old browsers.
   *
   * @param element
   * @param className
   */
  var elementRemoveClass = function (element, className) {
    var classes = element.className.split(' ').filter(function (localClassName) {
      return localClassName !== className;
    });
    element.className = classes.join(' ');
  };

  /**
   * Add one time closing button.
   */
  var addCloseButton = function (callback, closeText) {
    var button = document.createElement('div');
    button.innerText = closeText || tinsta.strings.close;
    button.className = 'mobile-back-button';
    button.addEventListener('mouseup', function () {
      button.parentNode.removeChild(button);
      elementRemoveClass(document.body, 'no-scroll');
      callback();
    });
    document.body.appendChild(button);
    document.body.className += ' no-scroll';
  };

  /**
   * Fixups and definitions.
   */
  ( function() {
    // Removing no-js class.
    document.documentElement.className += ' js';
    elementRemoveClass(document.documentElement, 'no-js');
    // Workaround for transition touch events.
    // document.addEventListener('touchstart', function () {} );
    // document.body.addEventListener('touchstart', function () {}, false);
  }() );

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

  /**
   * Search forms add focus class when focused element.
   */
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

  /**
   * Auto-grow of comments.
   */
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

  /**
   * Avatar change based on inputed email.
   */
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

  /**
   * Full Height.
   */
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

  /**
   *  Agree accepted.
   */
  (function () {
    var shouldShowAgreeDialog = !localStorage.getItem('agreeAccepted');
    if (window.hasOwnProperty('tinstaCustomized')) {
      shouldShowAgreeDialog = ( window.tinstaCustomized.hasOwnProperty('component_site_agreement_enable') && window.tinstaCustomized.component_site_agreement_enable )
        || window.tinstaCustomized.hasOwnProperty('component_site_agreement_style')
        || window.tinstaCustomized.hasOwnProperty('component_site_agreement_text')
        || window.tinstaCustomized.hasOwnProperty('component_site_agreement_agree_button')
        || window.tinstaCustomized.hasOwnProperty('component_site_agreement_cancel_url')
        || window.tinstaCustomized.hasOwnProperty('component_site_agreement_cancel_title');
    }
    if (shouldShowAgreeDialog) {
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

  /**
   * Scrolltop.
   */
  if (tinsta.scrolltop) {
    ( function () {
      var button = document.createElement('div');
      button.className = 'scrolltop-button';
      button.innerText = tinsta.strings.top;
      button.setAttribute('title', tinsta.strings.top);
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
    }() );

  }

  /**
   * Init smoothscroll if available.
   */
  window.addEventListener('load', function () {
    if ( window.hasOwnProperty('jQuery') && window.jQuery.fn.hasOwnProperty('niceScroll') ) {
      jQuery('body').niceScroll();
    }
  });

  /**
   * Responsive menu.
   */
  (function () {

    var mainWrapper = document.getElementsByClassName('site-container').item(0);
    if (!mainWrapper) {
      return false;
    }

    var menuItemsSelectors = [
      //'.site-header-wrapper .menu-item-type-tinsta-nav-menu-widget-area.depth-0 > .sub-menu',
      '.site-header-wrapper  .menu-item-object-tinsta-nav-menu-object.depth-0 > .sub-menu',
      '.site-header-wrapper .menu-item-has-children.depth-0 > .sub-menu'
    ];

    document.querySelectorAll(menuItemsSelectors.join(','))
      .forEach(function (item) {

        // Mega class.
        var richItemsLen = 0;
        for ( var i = 0; i < item.children.length; i++) {
          if (item.children.item(i).className.match('menu-item-has-children')) {
            richItemsLen++;
          }
        }
        if (item.children.length === richItemsLen || !!item.parentElement.className.match('menu-item-object-tinsta-nav-menu-object')) {
          item.parentNode.className += ' is-mega';
        }

        // Consider replacing window.innerWidth with window.outerWidth
        // innerWidth represent current width, and outerWidth represent the whole window
        item.parentElement.addEventListener('mouseenter', function (event) {
          if ( window.innerWidth >= parseInt(tinsta.breakpoints.tablet) ) {
            item.style.right = 'auto';
            item.style.left = 'auto';
            if (item.offsetWidth + item.offsetLeft > mainWrapper.offsetWidth) {
              item.style.right = 0;
            }
            if (item.offsetLeft < 10) {
              item.style.left = 0;
            }
          }
        });

        // Mobile menu.
        if (item.parentElement.children.item(0)) {
          item.parentElement.children.item(0).addEventListener('click', function (event) {
            if (window.innerWidth < parseInt(tinsta.breakpoints.tablet)) {
              event.preventDefault();
              item.className += ' mobile-menu';
              addCloseButton(function () {
                elementRemoveClass(item, 'mobile-menu');
              });
            }
          });
        }

      });

  }());


  /**
   * Make topline, header sticky.
   */
  ( function () {
    var topline = document.getElementsByClassName('site-topline-wrapper');
    var header = document.getElementsByClassName('site-header-wrapper');
    if (topline.length > 0 && header.length > 0) {
      var setHeaderTop = function () {
        if (window.getComputedStyle(topline[0]).position === 'sticky' && window.getComputedStyle(header[0]).position === 'sticky') {
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
