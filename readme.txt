=== iTrasher by Boolex ===
Contributors: Yonanne Remedio
Tags: trasher, unused, images, boolex, remover, delete
Requires at least: 3.5
Tested up to: 4.2.3
Stable tag: 0.1
License: GPLv2 or later

iTrasher allows site admins to delete unwanted or unused images.

== Description ==

iTrasher scans your database for unused images and display them all. The list will then have thumbnails and filenames for admin users to carefully select images and delete permanently. 

== Installation ==

- Download our plugin.
- Upload to your blog.
- Activate it.
- or go to your Dashboard - Plugins - Add new
- search for iTrasher by Boolex and install it
- You're done!

== Frequently Asked Questions ==

= Does this plugin scan the entire database? =

No. It will only scan the posts table. The posts table is where images are stored by default. So it's safe to say that most of your images will be found.

= How does the plugin know that a particular image is unused? =

- firstly, it checks the value of the parent_id - 0 means no parent
- secondly, it checks whether the image is set as featured
- if no parent id and it's not set as featured, then we can add this image to our unused images container
- the checking does not stop here, you might have some other plugins that are using your images, so in this case we need to perform a final check
- this final check ensures that this particular image is not used elsewhere, so the plugin will scan all tables and columns in your database and find every occurence of the image

= Does this plugin support Estatik Real Estate plugin? =

Yes, Estatik plugin stores images differently in a different table. Our plugin is able to find these images.

= What about plugins that stores images differently similar to Estatik? =

If you think images uploaded via these plugins are not found by our plugin, then a customized solution might be a better fit. We can create an extension that suits your needs for only $30/hr. To discuss, just contact us at http://boolex.com/contact-us.

== Screenshots ==
/assets/screenshot-01.jpg
/assets/screenshot-02.jpg
/assets/screenshot-03.jpg

== Changelog ==

= 0.1 =
*Release Date - 3rd July, 2015*

* iTrasher allows site admin to delete unwanted or unused images.