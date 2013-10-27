=== H1 Auto Image Download ===
Contributors: akibjorklund
Tags: development, tool
Requires at least: 3.7
Tested up to: 3.7
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

If an image is not found, this plugins downloads the image from another server. Makes it easy to mirror production for development purposes.

== Description ==

To debug an issue on a production website, you often copy the database over from production. Maybe you do that manually or using WP Migrate Pro. You would also like to have the images to have a complete copy, but the production site might have a huge amount of them and you have no time to waste. So you try to survive without them. But now you don't have to.

H1 Auto Image Download is a 404 handler that can automatically and seamlessly download the images requested from your production server. You will only have to tell it where to download the images and now your development site works as if it has all the files it needs.

== Installation ==

1. Upload plugin folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to the Auto Image Download settings page under Settings and set up the mirrored url

== Changelog ==

= 1.0 =
* Initial release