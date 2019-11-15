=== Wordpress Wordpress Automated Post Author ===
Contributors: Alfred Dagenais
Donate link: http://bit.ly/2OkUF8Y
Tags: Author, Authors, post Author, automatic Author, automatic Author
Requires at least: 4.7
Requires PHP: 7.0
Tested up to: 5.2.2
Stable tag: 1.0.0
License: MIT License

Automatically sets the author into the post (any post type). So easy like that...

== Description ==

**NOTE: Before installing this plugin bear in mind that its only purpose is to ADD the Author ID to your post (in the same way that you would do using WP editor), it does not remove ANYTHING after deactivation.
Before asking for support please read [FAQ](https://github.com/alfreddagenais/automated-post-author) and [this support thread](https://wordpress.org/support/topic/please-read-before-posting-4)**

= How it works? =

Checks if the post (any post type, including pages) has already a author associated, and if not sets it using one of the following methods:

1. Dynamically, for old published posts, the author are set only when needed to show them in the frontend. This means that the author is set (only first time) when a visitor loads the page where it needs to be shown.

2. For new content, the author is set in the publishing process.

**No options page to setup**, simply install and activate.

If you want to exclude certain post type (e.g. pages), you can do it by using a filter. See [FAQ](https://github.com/alfreddagenais/automated-post-author) for more details.

= Requirements =

* WordPress 4.7 or higher.
* PHP 5.6 or higher.
    	
== Installation ==

* Extract the zip file and just drop the contents in the <code>wp-content/plugins/</code> directory of your WordPress installation (or install it directly from your dashboard) and then activate the Plugin from Plugins page.
  
== Frequently Asked Questions ==

= Can I use this plugin for setting featured image using some image not attached to the post? =

No. This plugin uses only standard WordPress functions to set the featured image. And using this standard (and friendly) method WordPress simply has not any knowing about images not attached to the post.

= How can I check if a post has "attached" author? =

In that post edit screen, check the author box, and select your author in the dropdown, you must see at least author.

= How can I exclude pages or other post types ? = 

If you don't want to use Easy Add Author for your pages or any other post type, you can exclude them by simply adding a little snippet of code to your theme functions.php file **before enabling the plugin**.
The following example will exclude pages:

`add_filter ('apa_post_types_exclude', 'my_excluded_types', 10, 1);
function my_excluded_types ( $exclude_types ){
	$exclude_types[] = 'page'; 
	return $exclude_types;
}`

If you want to exclude a custom post type you need to know the value of 'name' used in [register_post_type()](https://codex.wordpress.org/Function_Reference/register_post_type) function for registering that post type.
e.g. If you have a custom post type and its 'name' is 'book' the you'll use:

`add_filter ('apa_post_types_exclude', 'my_excluded_types', 10, 1);
function my_excluded_types ( $exclude_types ){
	$exclude_types[] = 'book'; 
	return $exclude_types;
}`

If you want to exclude more than one post type just duplicate the $exclude_types[] line for each one.

**This snippet must be added to your site BEFORE enabling Easy Add Author in your site**,
if you add it later it will stop assigning the Author for new posts in the excluded types after that moment, previous posts will not be modified.

= Will this plugin works in WordPress older than 4.7? =

Maybe, but WordPress installs older than 4.7 are not supported by me. **Try at your own risk**.

== Changelog ==

= 1.0.0 =

* Initial release.

