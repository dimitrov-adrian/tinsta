![](https://raw.githubusercontent.com/dimitrov-adrian/tinsta/master/screenshot.png)

# Tinsta (Tiny Standard)
Tinsta theme aims to provide very standard web site layout with a lot of 
customization options. Anyway, it is not as rich of functions as Headway
or Divi, but is free, fast, not overbloated and... Open Source.


### Requirements
- PHP 5.4 or later
- WordPress 4.4 or later


### Performance
The theme doesn't come with page cache and assets aggregation, it's just not theme's business to do that.
Instead you can consider [WP Super Cache](https://bg.wordpress.org/plugins/wp-super-cache/) for page cache and [Autoptimize](https://bg.wordpress.org/plugins/autoptimize/)
for asset aggregation.


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


### Developers


### Hooks refernce


#### Filters


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


### Debugging
WordPress constant `SCRIPT_DEBUG` also allow forcing stylesheet regeneration, but need to send `no-cache` HTTP header (usually `Shift`+**Refresh** or Chrone's **Disable Cache** option in dev panel)


### Contributing
Have idea or feedback, then open an issue. Have fix, then make a pull request.

If you want to help with translation, then please wait until first stable release.



### TODO
List by priority
- [ ] **[WIP]** Improve strings
- [ ] Gutenberg *(integration)*
- [ ] Sticky Sidebars
- [ ] WooCommerce *(integration)*
- [ ] Allow full width layout
- [ ] SiteOrigin Panels *(integration)*
- [ ] BbPress *(integration)*
- [ ] Beaver Builder *(integration)*
- [ ] Add ability to add icon to menu items
- [ ] Breadcrumbs widget improvement
- [ ] More layouts for post's single/archive views
- [ ] Share Buttons by AddThis *(integration)*
- [ ] Documentation
