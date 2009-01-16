=== Visual Recent Posts ===
Contributors: oktoberfive	
Tags: recent posts, magazine, thumbnails, featured post, images, posts
Donate link: http://oktober5.com/donate/
Requires at least: 2.5
Tested up to: 2.7
Stable tag: 1.2.3

Displays your recent posts with auto-generated thumbnails and excerpts; also includes a featured post and popup box option.

== Description ==

<p>This plugin gives the look of a magazine-style website with thumbnails and excerpts in a clean layout. Thumbnails are generated automatically, and there is a settings page to set an unholy amount of options. (Really, it's out of control and I'm sorry.) The latest release also has a "featured post" add-on so you can specify any post you want or just the most recent one to be put in the featured post box. You can also add a css popup for the image that includes the post title and </p>

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

= My css popup boxes are not working. Are you a sorry excuse for a coder? =

Yes and no. I know enough to break things. But I'm really good at copy/paste :) If your css popups aren't showing, it's probably because you're using IE6...? The code I copy and pasted for this was supposed to work in IE6, but my experience has shown that it doesn't work. If you like you can write Bill Gates about it.

== Screenshots ==

1. Here's how I configured the plugin to run on my own page.

== I need more help! ==

Make sure you check out the <a href="http://oktober5.com/visual-recent-posts-plugin/">plugin homepage</a>. If you need my help, check out the <a href="http://oktober5.com/forums/">plugin forums</a>

== Change Log ==
v1.0 - changed thumbnail cache directory to be `wp-content/thumb-cache/`. Users of the WP super cache plugin might have noticed that deleting their cache also deleted all the thumbnails :)

v1.1 - The plugin was conflicting with other plugins about some session_start() garbage which I really don't understand, but after a wild copy/paste job it appears things are better now.

v1.1.1 - OK, for future reference, please use `<?php` not just `<?` ... OK! Seriously.... Anyway, if you were getting a function not found or something like that, hopefully it's fixed.

v1.1.2 - No you can specify a category or multiple categories to display. And! if you don't want to display posts without images, now you can--just change your setting on the VRP options page.

v1.1.3 - Added parameter to insertVisualRecentPosts() function. You can now pass $number_of_posts to specify how many posts to display for that VRP box. Helpful if you're displaying a small number of posts on the front page but want to display more posts on your category archive pages.

v1.1.4 - Added more style options, like changing font sizes and background colors; also, I removed some hard-coded styles so that you may set them yourself in the css file. Hope that doesn't break things....

v1.2 - Added CSS popup boxes; fixed number of posts with images issue thingy; took out hard coded margins, padding, colors, etc., so that you can make your own changes, although this makes things look ugly initially; upped the thumbnail image quality to 85%

v1.2.1 - Yes, another release... Fixed a bug that I created in the last release. When you specify a number of posts and say images only, it should give you that many posts with images, assuming you have that many. If you don't have that many, it will return what it can find. 

v1.2.2 - Afraid of CSS? This version adds padding options for image box, title, and excerpt. It also fixes the problem where each CSS popup excerpt is the same.

v1.2.3 - Fixed bug where images were not displaying in Opera browser. Also removed some ugly and unnecessary styles in the css, which probably messed things up for you and I'm sorry. It was for the greater good. Also added option to put title after the image.




