# Tinsta (Tiny Standard)

![](https://raw.githubusercontent.com/dimitrov-adrian/tinsta/master/screenshot.png)

Tinsta theme aims to provide very standard web site layout with a lot of 
customization options. Anyway, it is not as rich of functions as Headway
or Divi, but is free, fast, not overbloated and... Open Source.

##
### Requirements
- PHP 5.4 or later
- WordPress 4.4 or later

##
### Performance
The theme doesn't come with page cache and assets aggregation, it's just not theme's business to do that.
Instead you can consider [WP Super Cache](https://bg.wordpress.org/plugins/wp-super-cache/) for page cache and [Autoptimize](https://bg.wordpress.org/plugins/autoptimize/)
for asset aggregation.

##
### TODO
List by priority
- [ ] **[WIP]** Improve strings
- [ ] **[WIP]** Gutenberg blocks *(integration)*
- [ ] Sticky Sidebars
- [ ] WooCommerce *(integration)*
- [ ] Allow full width layout
- [ ] SiteOrigin Panels *(integration)*
- [ ] BbPress *(integration)*
- [ ] Add ability to add icon to menu items
- [ ] More layouts for post's single/archive views
- [ ] Share Buttons by AddThis *(integration)*
- [ ] Documentation

##
#### FAQ

##### Why no complete customizeable ability like Headway
Because the theme's purpose is not to be a complete design builder, but
to provide a lot of customization options within the theme's scope.


#####  Why not layout builder like Site-Origin Panels, Divi, ..etc.
The theme is not intendet to be full featured like these commercial themes,
and at current stage cannot beat some big commercial solutions.
Anyway, there are a lot of plugins that provide layout builder for any theme, 
so you are free to use every plugin that you are familiar with.
You can try Site-Origin's Panels or Beaver Builder 

### Want to extend. Plugins recommendation list.
The theme cannot provide a replacement functions for everythink in WordPress's plugins garden,
and cannot make it better than other mature solutions. It's better to use some of the
listed plugins if they match your requirements and is not in the Tinsta's theme core.
- If Menu - https://wordpress.org/plugins/if-menu/
- Widget Logic - https://bg.wordpress.org/plugins/widget-logic/

##
### Developers

#### Hooks refernce

##### Filters

###### tinsta_force_options
Force options to given values, also hide customizer controls

```php
add_filter('tinsta_get_options_defaults', function ($options) {
  $options['typography_font_size'] = 22;
  return $options
});
```

###### tinsta_get_stylesheet_args
Override or add variables exposed to scss scripts.

```php
add_filter('tinsta_get_stylesheet_args', function ($args) {
  $args['variables']['my-custom-font-size'] = '32px';
  return $args;
});
```

##
### Debugging
WordPress constant `SCRIPT_DEBUG` also allow forcing stylesheet regeneration, but need to send `no-cache` HTTP header (usually `Shift`+**Refresh** or Chrone's **Disable Cache** option in dev panel)

##
### Contributing
Have idea or feedback, then open an issue. Have fix, then make a pull request.

If you want to help with translation, then please wait until first stable release.

## 
### License & Terms 

Tinsta is available under the terms of the GPL-v2 or later license See [`COPYING`](http://www.gnu.org/licenses/gpl-2.0.html) for details.

Tinsta theme bundles the following third-party resources:

**HTML5 Shiv**
Licenses: MIT/GPL2
Source: https://github.com/aFarkas/html5shiv

**Normalize.css**
Licenses: MIT License
Source: http://github.com/necolas/normalize.css

**A JavaScript polyfill for Flexbox**
License: MIT License
Source: https://github.com/jonathantneal/flexibility

**-prefix-free**
Licenses: MIT License
Source: https://leaverou.github.io/prefixfree/

**Selectivizr2**
Licenses: MIT License
Source: https://github.com/corysimmons/selectivizr2

**SmoothScroll**
Licenses: MIT License
Source: https://github.com/galambalazs/smoothscroll-for-websites

**position: sticky; The polyfill!**
Licenses: MIT License
Source: https://github.com/matthewp/position--sticky-

**Line Awesome**
Licenses: https://icons8.com/good-boy-license/
Source: https://icons8.com/line-awesome

**scssphp**
Licenses: MIT License
Source: http://leafo.github.io/scssphp/
