> **This theme is under development. It's possible to have breaking changes until
> first public version.**
>
> **Using on production is on Your own risk.**


# Tinsta (Tiny Standard)

![](https://raw.githubusercontent.com/dimitrov-adrian/tinsta/master/screenshot.jpg)

Tinsta (as from **Ti**ny **sta**ndard) is free open source WordPress theme, that aims to provide 
very standard web site layout with a lot of customization options.


##
### Requirements
- PHP 5.4 or later
- WordPress 4.6 or later


##
### Installation
1. Download [Tinsta](https://github.com/dimitrov-adrian/tinsta/archive/master.zip)
2. Unzip `tinsta-master.zip` and rename `tinsta-master` to `tinsta`
3. Upload to your `wp-content/themes`
4. In your admin panel, go to Appearance -> Themes
5. Click on the 'Activate' button to use your new theme right away.
4. Navigate to Appearance > Customize in your admin panel and customize to taste.


##
### wp-config.php constants

##### Directory to store cached css files
Constant should be relative to `WP_CONTENT_DIR` and **must** starts with slash.  
Defaults `WP_CONTENT_DIR`/cache/tinsta/css

```php
define('TINSTA_STYLESHEET_CACHE_DIR', '/cache/tinsta/css');
```

##### Bundled third-party plugin integrations
Defaults is enabled

```php
define('TINSTA_INTEGRATIONS', true);
```


##
### TODO
List by priority
- [ ] **[WIP]** Improve UI strings
- [ ] **[WIP]** Gutenberg blocks *(integration)*
- [ ] **[WIP]** SiteOrigin Panels *(integration)*
- [ ] **[WIP]** WooCommerce *(integration)*
- [ ] Sticky Sidebars
- [ ] Allow full width layout
- [ ] bbPress *(integration)*
- [ ] Add ability to add icon to menu items
- [ ] More layouts for post's single/archive views
- [ ] Documentation


##
### FAQ

#### How to update
Tinsta theme is not in the [theme directory](https://wordpress.org/themes/),
so cannot be updated through WordPress built-in store. The only way to get automatic
updates is via [github-udpater](https://github.com/afragen/github-updater) plugin.

Manual update is always an option. Check **INSTALLATION** for more information.

#### Why no complete customizeable ability like Headway
Because the theme's purpose is not to be a complete design builder,
but to provide a lot of customization options within the theme's scope.

####  Why no layout builder like Site-Origin Panels, Divi, ..etc.
There is a lot of plugins that provide such function, and at current
stage Tinsta theme cannot provide something better than these options.
Anyway, the theme can integrate with some of these plugins very well,
so you can pick the plugin you are familiar with it and do the job.

#### Why no per post type or page sidebars (widget areas), how can manage different widgets in same sidebars
Adding variants to sidebar per post_type or type family will be very limited way
to managing widgets. Using plugin that provide widget logic is more flexible way
to do the goal. There is plenty of good solutions for this, so right now there 
is no reason to reinvent the wheel. Check some of the **Widget Logic** recommentations,
and pick by your choice.


##
### Want to extend. Plugins recommendation list.
The theme cannot provide a replacement functions for everything in 
WordPress's plugins garden.
So here is short list of plugins that can be helpful.

- Menu Logic:
  - [If Menu](https://wordpress.org/plugins/if-menu/)
- Widget Logic:
  - [If Widget](https://wordpress.org/plugins/if-widget/)
  - [Widget Logic](https://bg.wordpress.org/plugins/widget-logic/)
  - [Widget Display Conditions](https://wordpress.org/plugins/widget-display-conditions/)
  - [Widget Visibility Without Jetpack](https://wordpress.org/plugins/widget-visibility-without-jetpack/)
- Performance:
  - [Autoptimize](https://bg.wordpress.org/plugins/autoptimize/)
  - [WP Super Cache](https://bg.wordpress.org/plugins/wp-super-cache/)
- Widgets:
  - [Post Field Widget](https://github.com/dimitrov-adrian/post-field-widget)
  - [PHP Code Widget](https://wordpress.org/plugins/php-code-widget/)
  - [Recent Posts Widget with thumbnails](https://wordpress.org/plugins/recent-posts-widget-with-thumbnails/)
  - [Simple Social Icons](https://wordpress.org/plugins/simple-social-icons/)


##
### Developers

#### Hooks refernce

##### Filters

###### tinsta_force_options
Force options to given values, also hide customizer controls. It is useful when creating child theme and want to force
some theme settings, and hide the controls.

```php
add_filter('tinsta_get_options_defaults', function ($options) {
  $options['typography_font_size'] = 22;

  return $options
});
```

###### tinsta_stylesheet_args
Override or add variables exposed to scss scripts.

```php
add_filter('tinsta_stylesheet_args', function ($args) {
  $args['variables']['my-custom-font-size'] = '32px';

  return $args;
});
```


##
### Debugging
WordPress constant `SCRIPT_DEBUG` also allow forcing stylesheet regeneration,
but need to send `no-cache` HTTP header (usually `Shift`+**Refresh** or
Chrome's **Disable cache** option in dev panel)


##
### Contributing

* Pull requests **are welcome**.

* Have idea or feedback, feel free to open an issue and share.

* If you want to help with translation, then please wait until first stable release.

* **Code changes have to be PSR1/2 compatible, with 2 spaces indention.**

## 
### License & Terms 
Tinsta theme is available under the terms of the
[GPL-v2](http://www.gnu.org/licenses/gpl-2.0.html) or later license. 

The theme come bundled with following third-party resources:

**scssphp**  
Licenses: MIT License  
Source: http://leafo.github.io/scssphp/

**Normalize.css**  
Licenses: MIT License  
Source: http://github.com/necolas/normalize.css

**Line Awesome**  
Licenses: https://icons8.com/good-boy-license/  
Source: https://icons8.com/line-awesome

**SmoothScroll**  
Licenses: MIT License  
Source: https://github.com/galambalazs/smoothscroll-for-websites

**A JavaScript polyfill for Flexbox**  
License: MIT License  
Source: https://github.com/jonathantneal/flexibility

**position: sticky; The polyfill!**  
Licenses: MIT License  
Source: https://github.com/matthewp/position--sticky-

**HTML5 Shiv**  
Licenses: MIT/GPL2  
Source: https://github.com/aFarkas/html5shiv

**-prefix-free**  
Licenses: MIT License  
Source: https://leaverou.github.io/prefixfree/

**Selectivizr2**  
Licenses: MIT License  
Source: https://github.com/corysimmons/selectivizr2

**remPolyfill**  
Licenses: MIT License  
Source: https://github.com/nbouvrette/remPolyfill

**Respond.js min/max-width media query polyfill**  
Licenses: MIT License  
Source: https://github.com/scottjehl/Respond
