=== Visual Recent Posts ===
Contributors: oktoberfive	
Tags: recent posts, magazine, thumbnails, featured post, images, posts
Donate link: http://oktober5.com/donate/
Requires at least: 2.5
Tested up to: 2.7 RC2
Stable tag: trunk

Displays your recent posts with auto-generated thumbnails and excerpts; also includes a featured post.

== Description ==

<p>If the download link here is not work--which it hasn't been as of late... grrrr--then you can download it here: <a href="http://oktober5.com/wp-content/downloads/visual-recent-posts.zip/">VRP Download Link</a></p>

<p>This plugin gives the look of a magazine-style website with thumbnails and excerpts in a clean layout. Thumbnails are generated automatically, and there is a settings page to set an unholy amount of options. (Really, it's out of control and I'm sorry.) The latest release also has a "featured post" add-on so you can specify any post you want or just the most recent one to be put in the featured post box.</p>

<p>Are you using Thesis from DIYThemes.com? Easily place your Recent Visual Posts by simply inserting your desired Thesis hook on the settings page. No code changes needed.</p>

<p>This plugin uses a slightly modified version of the excellent Image Extractor plugin found at <a href="http://wordpress.org/extend/plugins/image-extractor/">wordpress.org/extend/plugins/image-extractor/</a>.</p>

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create thumbnail cache directory `thumb-cache` under the wp-content/ directory.
1. Place `<?php insertVisualRecentPosts(); ?>` in your template where you want things to show up. If you have a nice hook system in your theme (like Thesis theme from diythemes.com), then you don't even have to modify any code. Just enter the hook name in the plugin's option panel.

== Frequently Asked Questions ==

= I created the 'thumb-cache' directory but my images aren't showing up. What do I do? =

Don't panic! OK, panic if you like, but this is probably a permissions problem. Make sure that directory is writable. If you have questions, please visit the plugin homepage or the <a href="http://oktober5.com/forums/">plugin forums</a>

Also, you should know that if you are using images in your posts that are hosted on another site this plugin won't be able to generate the thumbnail. So, you have to upload the images in wordpress when you're editing the post in order for things to go smoothly.

== Screenshots ==

1. Here's how I configured the plugin to run on my own page.

== I need more help! ==

Make sure you check out the <a href="http://oktober5.com/visual-recent-posts-plugin/">plugin homepage</a>. If you need my help, check out the <a href="http://oktober5.com/forums/">plugin forums</a>

== Change Log ==
v1.0 - changed thumbnail cache directory to be `wp-content/thumb-cache/`. Users of the WP super cache plugin might have noticed that deleting their cache also deleted all the thumbnails :)

v1.1 - The plugin was conflicting with other plugins about some session_start() garbage which I really don't understand, but after a wild copy/paste job it appears things are better now.

v1.1.1 - OK, for future reference, please use `<?php` not just `<?` ... OK! Seriously.... Anyway, if you were getting a function not found or something like that, hopefully it's fixed.

v1.1.2 - No you can specify a category or multiple categories to display. And! if you don't want to display posts without images, now you can--just change your setting on the VRP options page.




