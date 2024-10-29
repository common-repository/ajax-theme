=== Plugin Name ===
Contributors: Fractalia - Applications Lab
Tags: theme, plugin, ajax, wordpress, jqueyr, history, fractalia
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: trunk

== Description ==

This plugin help developers to build an ajax-based themes. It uses que jQuery History plugin.

== Installation ==

1. Download the plugin from [here](http://wordpress.org/extend/plugins/ajax-theme/ "Ajax theme").
2. Extract all the files. 
3. Upload everything (keeping the directory structure) to the `/wp-content/plugins/` directory.
4. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= How it works? =

It help you to construct Ajax themes not rendering header.php and footer.ph when making an ajax request. For this pourpuse You should use "ajax_header" and "ajax_footer" instead "get_header" and "get_footer" respectively.

= How do I activate the ajax theme? =

You should include the function "enable_ajax_theme('#')" on functions.php.

= How do It help me on javascript level =

You should exec ajax_init(function(hash){
    jQuery('#content-wrap').load(base_url + hash, function(){
        ...
    });
    /* The function is for reference */
});

For more information you can visit the jQuery history plugin (http://tkyk.github.com/jquery-history-plugin/)


== Changelog ==
- 0.1 Initial release
- 0.1.3 Fixed some bugs
