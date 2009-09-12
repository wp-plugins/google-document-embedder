=== Google Doc Embedder ===
Contributors: k3davis
Tags: pdf, ppt, powerpoint, google, embed, google docs, document
Requires at least: 2.5
Tested up to: 2.9-rare
Stable tag: trunk

Lets you embed PDF files and PowerPoint presentations in a web page using the Google Document Viewer.

== Description ==

Google Doc Embedder lets you embed PDF files and PowerPoint presentations in a web page using the Google Document Viewer.

Similar to services like Sribd, Google Doc Embedder will allow you to embed a PDF or PPT (PowerPoint) file directly into your page or post, not requiring the user to have Adobe Reader or PowerPoint installed to view the contents. Unlike Scribd, the files do not need to be uploaded to any service first - including Google Documents - but can exist anywhere accessible on your site or the internet.

Note: This plugin utilizes an undocumented feature of the Google Document viewer, and as such it may be subject to hazards if their viewer unexpectedly changes drastically. In this unlikely event, the plugin should degrade nicely and not break your site.

== Installation ==

1. Upload the entire `google-document-embedder` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

You will find 'Contact' menu in your WordPress admin panel.

For basic usage, please see the [FAQ].

== Frequently Asked Questions ==

= What file types can be embedded? =
This plugin can embed PDF or PPT files only. The file to embed must first be available somewhere on the internet. You can upload it to your WordPress site using the standard techniques, or link to a file on another site. **You do not need to save the file in Google Documents first to embed it.**

= How do I embed a file in my page or post? =
Use the custom shortcode `[gview]` to embed the file, as shown:

`[gview file="http://url.to/file.pdf"]`

Optional attributes:

* `save=` : Set to 0 if you wish to suppress the direct download link to the file under the embedded viewer
* `width=` : To override the default width of the viewer, enter a new width value (number in pixels)
* `height=` : To override the default height of the viewer, enter a new height value (number in pixels)

= Nothing is showing up! What do I do? =
View the source on the web page where you've embedded the viewer. In order to degrade gracefully in case an error occurs, error messages will be inserted as HTML comments in these pages at the spot the viewer is called.

== Screenshots ==

1. Appearance of embedded viewer (blank file)

== Changelog ==

= 0.2 =
* Initial beta release. Fully functional, but no niceties...

== License ==

This plugin is free for everyone. Since it's released under the GPL, you can use it free of charge on your personal or commercial blog.