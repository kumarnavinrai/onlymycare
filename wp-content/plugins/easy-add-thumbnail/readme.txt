=== Easy Add Thumbnail ===
Contributors: samuelaguilera
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8ER3Y2THBMFV6
Tags: thumbnail, thumbnails, featured image, automatic thumbail, automatic feature image
Requires at least: 2.9
Tested up to: 3.5.1
Stable tag: 1.0.2
License: GPL2

Automatically sets the featured image (previously post thumbnail) to the first uploaded image to the post. So easy like that...

== Description ==

Checks if you defined the featured image (previously post thumbnail), and if not it automatically sets the featured image to the first uploaded image for that post. So easy like that...

It does his job in two cases:

1. Dinamically, for old published posts, the featured images are sets only when needed to show them in the frontend. This means that the featured image is set (only first time) when a visitor loads the page where it needs to be shown.

2. For new posts, it sets the featured image just in the publishing process.

No options page to setup, simply install and activate.

The plugin uses only WordPress standard functions to set the featured image (just the same as you set it manually). And this requires to have attached/uploaded at least one image to the post. If there's not any image attached to the post, this plugin can't help you.

= Features =

* Simply avoids you to set the featured image one by one to every post if you uploaded an image when you did the post.

= Requirements =

* WordPress 2.9 or higher.
    	
== Installation ==

* Extract the zip file and just drop the contents in the <code>wp-content/plugins/</code> directory of your WordPress installation (or install it directly from your dashboard) and then activate the Plugin from Plugins page.

<strong>IMPORTANT!</strong> Remember that your theme must support the use of thumbnails, if not, the thumbnails will be added but you'll not see them in your site.
  
== Frequently Asked Questions ==

= Will this plugin works in WordPress older than 2.9? =

No, because this plugin uses the post thumbail function added on WordPress 2.9.

= Can I use this plugin for setting featured image using some image not attached to the post? =

No. This plugin uses only standard WordPress functions to set the featured image. And using this standard (and friendly) method WordPress simply has not any knowing about images not attached to the post.

= My theme is showing big images instead of thumbnail sizes, what happens? =

As stated above this plugin uses standard WordPress method to set the featured image, this does not include any size information. **The size used by your theme for displaying image thumbnails depends totally on how your theme was coded.**

Contact to your theme author for support if you're having this problem.

You can find more information about how to properly show thumbnails in your theme on codex reference for [the_post_thumbnail](http://codex.wordpress.org/Function_Reference/the_post_thumbnail) (check 'Thumbnail Sizes' section) and [set_post_thumbnail_size](http://codex.wordpress.org/Function_Reference/set_post_thumbnail_size) functions.

= Is the post thumbnail and featured image the same? =

Yes. When I released first version of this plugin, this featured was named as [post thumbnails](http://codex.wordpress.org/Post_Thumbnails), but later WordPress team decided to change the name to "featured image".

In fact, WordPress core functions for featured image, still uses original [thumbnail](http://codex.wordpress.org/Function_Reference/the_post_thumbnail) names.

That's because the plugin name (that can't be changed in the Extend directory without having issues) says "thumbnail".

= Why you did this plugin? =

I did it to fullfil the needs of many of my WordPress maintenance service customers.

= Are you planning to add more features? =

At first not. The main and only purpose of this plugins is to do what it already does right now.

== Changelog ==

= 1.0.2 =

* When updating the readme.txt I copied by error another plugin readme to trunk, causing the plugin closed by WordPress.org staff. This release is only to fix the mistake made with readme as requested by WordPress.org staff. Sorry!!

= 1.0.1 =

* Hooks added to set the thumbnail when publishing too.

= 1.0 =

* Initial release.
