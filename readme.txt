=== Plugin Name ===
Contributors: ansimation
Tags: wordcamp, badge, widget, attending, sponsoring, wcbadge, wc badge, wc, event badge, event
Requires at least: 2.9
Tested up to: 3.0
Stable tag: 1.0
Donate Link: http://www.visitfloridastateparks.com/donate/


== Description ==

If you're an event organizer this plugin allows you to give attendees and speakers and anyone else for that matter a widget for their blog
that displays the event's "badge". Does require editing the code to fit the intended event but all that is required is adding your background
image to your site, then modifying some variables at the top of the class ( background position, event name, etc ) and you're set to add the
widget to your blog as well as package it and allow users to download it.

== Installation ==

1. Upload `wc_badge/` (the folder) to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Upgrade Notice ==
Thanks

== Screenshots ==

== Frequently Asked Questions ==

= Can users resize badges to fit their sidebars? =
Yes.

= I added the widget to my sidebar but it's not being displayed =
Check the folder permissions on wp-content/plugins/wc_badge/badges/ and make sure it is writable by php.

= I dont have the GD Graphics Library installed, can I still use the widget? =
Sorry but no, in order to create our badges and add dynamic captions we require GD

== Changelog ==

= 1.0 =
* Initial Plugin Release.
