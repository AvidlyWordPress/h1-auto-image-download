=== H1 Auto Image Download ===
Contributors: akibjorklund
Tags: development, tool
Requires at least: 3.7
Tested up to: 4.1
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

If an image is not found, this plugin downloads the image from another server. Makes it easy to mirror production for development purposes.

== Description ==

To debug an issue on a production website, you often copy the database over from production. Maybe you do that manually or using WP Migrate Pro. You would also like to have the images to have a complete copy, but the production site might have a huge amount of them and you have no time to waste. So you try to survive without them. But now you don't have to.

H1 Auto Image Download can automatically and seamlessly download the images requested from your production server. You will only have to tell it where to download the images from and now your development site works as if it has all the files it needs. Optionally you can also just redirect to the production files.

== Installation ==

1. Upload plugin folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to the Auto Image Download settings page under Settings and set up the mirrored url

== Changelog ==

= 1.1.1 =
* Fix issue in admin
* Fix typos

= 1.1 =
* Make H1 Auto Image Download work in multisite environments
* Add option to just redirect to file instead of downloading

= 1.0 =
* Initial release