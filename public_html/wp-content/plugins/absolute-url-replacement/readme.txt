=== EI Replace Absolute URL with Relative URL ===
Contributors: earthlinginteractive
Tags: Relative URL, Replace URL, Search and Replace Absolute URL, Relative URL in Database, Relative
Donate link: https://earthlinginteractive.com/
Requires at least: 4.4.0
Tested up to: 4.9.2
Requires PHP: 5.5
Stable tag: 1.0
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Changes absolute URLs to relative URLs when saving content and adds them back in when displaying/editing content. Ability to search and replace absolute URLs with relative URLs in database also.

== Description ==
Changes absolute URLs to relative URLs when saving content to database and adds them back in when displaying/editing content. Ability to search the database for absolute URLs and replace them with relative URLs.

For example:

`<a href="http://localhost/hello-world">Hello World</a>`

Will be converted to:

`<a href="/hello-world">Hello World</a>`

and

`<img class="alignnone size-full wp-image-99" src="http://localhost/wp-content/uploads/2018/01/sample-image.jpg"/>`

Will be converted to:

`<img class="alignnone size-full wp-image-99" src="/wp-content/uploads/2018/01/sample-image.jpg"/>`


This plugin replaces any of the following patterns src=" src=' url(" url(' url( href=" href=' background_image=" srcset=" followed by the siteurl

== Installation ==
1. In WordPress go to Plugins->Add New and locate the plugin 
2. Click the install button
3. Activate the plugin through the ‘Plugins’ menu


You can now go to the menu option in the admin sidebar and select the database tables you would like to find absolute URLs and replace with relative URLs and do a dry run to see the fields that will get changed before doing an actual search and replace on your database.

WARNING! Make sure you have backed up your database before using search & replace!

After saving new or existing content, including posts, custom post types, pages, and widgets, URLs will be relative in the database but will be displayed as absolute in the editor and in the source of your web page.


== Frequently Asked Questions ==
What if I stop using this plugin?
Everything will still work as it did.

== Screenshots ==
1. admin search and replace

== Changelog ==
= 1.0 = 
* Initial release