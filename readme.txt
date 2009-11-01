=== Google Doc Embedder ===
Contributors: k3davis
Donate link: http://pledgie.com/campaigns/6048
Tags: pdf, ppt, tiff, powerpoint, google, embed, google docs, document
Requires at least: 2.5
Tested up to: 2.9-rare
Stable tag: trunk

Lets you embed PDF, PowerPoint presentations (PPT), and TIFF images in a web page using the Google Docs Viewer.

== Description ==

Google Doc Embedder lets you embed PDF files, PowerPoint presentations, and TIFF images in a web page using the Google Docs Viewer.

Similar to services like Scribd, Google Doc Embedder will allow you to embed a PDF, PowerPoint (PPT), or TIFF file directly into your page or post, not requiring the user to have Adobe Reader, PowerPoint, or other software installed to view the contents. Unlike Scribd, the files do not need to be uploaded to any service first - including Google Documents - but can exist anywhere accessible on your site or the internet.

Note: While previously the functionality of this plug-in relied upon an undocumented feature of the Google Docs Viewer, this feature is now "official" and presumably reliable in the long term. However, use of this plug-in now does imply your agreement with Google's published <a href="http://docs.google.com/viewer/TOS?hl=en" target="_blank">Terms of Service</a>.

== Installation ==

1. Upload the entire `google-document-embedder` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Done.

For basic usage, please see the FAQ.

Go to "GDE Settings" (under "Settings" in the admin panel) to change defaults, or override individually using the shortcode syntax in the FAQ.

== Frequently Asked Questions ==

= What file types can be embedded? =
This plug-in can embed PDF, PowerPoint (PPT), or TIFF files only. The file to embed must first be publicly available somewhere on the internet. You can upload it to your WordPress site using the standard techniques, or link to a file on another site.

= How do I embed a file in my page or post? =
Use the custom shortcode `[gview]` to embed the file, as shown:

`[gview file="http://url.to/file.pdf"]`

Note: the `file=` attribute (pointing to the full URL of the file) is **required**.

Optional attributes:

* `save=` : Set to 0 if you wish to suppress the direct download link to the file under the embedded viewer (1 for on, by default)
* `width=` : To override the default width of the viewer, enter a new width value - e.g., "400" (px) or "80%"
* `height=` : To override the default height of the viewer, enter a new height value - e.g., "400" (px) or "80%"

= Will it embed files that are password-protected  or stored in protected folders/sites? =
Most likely, no. If your file requires a login to view - such as being saved in a password-protected directory, or behind a firewall (on your intranet, etc.), the viewer will probably not be able to access the file. For files stored on Google Docs, the viewer will prompt you to log in first, which most users presumably couldn't do. This is what is meant above, that the document should be "publicly available." Please save the file in a publicly accessible location for best results.

= Nothing is showing up! What do I do? =
View the source on the web page where you've embedded the viewer. In order to degrade gracefully in case an error occurs, error messages will be inserted as HTML comments in these pages at the spot the viewer is called.

= I wish the plug-in had feature XYZ... =
That's not a question ;) but if you have any particular ideas on further development of this plug-in, please post <a href="http://wordpress.org/tags/google-document-embedder?forum_id=10#postform">on the forum</a> or privately using the <a href="http://www.davismetro.com/gde/support/">support form</a> and I'll see what I can do.

== Screenshots ==

1. Appearance of embedded viewer
2. Settings page

== Changelog ==

= 1.8.2 = 
* Fixed: Fatal PHP error if cURL library not active (WAMPServer, etc.)

= 1.8.1 =
* Added: Temporary (I hope) workaround option to insert help statement for users of IE8 - <a href="http://davismetro.com/gde/ie8" target="_blank">more info</a>

= 1.8 =
* Added: Ability to set height/width to percentage (thanks eturfboer)
* Fixed: Compatibility with PHP 5.3+, various function tuning

= 1.7.3 =
* Fixed: File URL containing tilde (~) considered invalid (thanks mjurek)

= 1.7.2 =
* Removed: toolbar button options (Google prevents this from working, sorry)

= 1.7.1 =
* Fixed: Misleading error message if file= attribute not used (thanks ersavla)
* Fixed: Bug in cURL header may cause false "not found" error

= 1.7 =
* Added: Ability to hide selected viewer toolbar buttons
* Fixed: Mask URL option ignored on non-PDF file types

= 1.6 =
* Added: Additional class names for optional stylesheet use
* Added: Support to embed TIFF images
* Fixed: Invalid settings link in plugin list (WordPress MU)
* Changed: Embed path to conform to Google published guidelines

= 1.5.1 =
* Fixed: Viewer not hidden if linked file not found
* Fixed: Divide by zero error if file size can't be determined
* Fixed: PHP error if file not found (force download link)
* Fixed: File not found falsely reported on some web servers

= 1.5 =
* Improved error checking.
* Improved customization options for download text.
* Added option to override default browser behavior for PDF links (force download).
* Added option to reset options to defaults.

= 1.0.3 =
* Installation bug fix. If you installed 1.0, please completely delete and reinstall plugin. Sorry :(

= 1.0 =
* Added options page.

= 0.3 =
* Error checking added.

= 0.2 =
* Initial beta release. Fully functional, but no niceties...

== License ==

This plugin is free for everyone. Since it's released under the GPL, you can use it free of charge on your personal or commercial blog.