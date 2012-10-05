=== Google Doc Embedder ===
Contributors: k3davis
Tags: doc, docx, pdf, ppt, pptx, xls, psd, zip, rar, tiff, ttf, office, powerpoint, google
Author URI: http://www.davistribe.org/code/
Donate link: http://pledgie.com/campaigns/6048
Requires at least: 3.0
Tested up to: 3.4
Stable tag: trunk
License: GPLv2 or later

Lets you embed MS Office, PDF, and many other file types in a web page using the Google Docs Viewer (no Flash or PDF browser plug-ins required).

== Description ==

<i><a href="http://www.davistribe.org/gde/beta-program/info/"><b>NEW!</b> See what's coming in GDE 2.5</a></i>

Google Doc Embedder lets you embed several types of files into your WordPress pages using the Google Docs Viewer - allowing inline viewing (and optional downloading) of the following file types, with no Flash or PDF browser plug-ins required:

* Adobe Acrobat (PDF)
* Microsoft Word (DOC/DOCX)
* Microsoft PowerPoint (PPT/PPTX)
* Microsoft Excel (XLS/XLSX)
* TIFF Images (TIF, TIFF)
* Apple Pages (PAGES)
* Adobe Illustrator (AI)
* Adobe Photoshop (PSD)
* Autodesk AutoCad (DXF)
* Scalable Vector Graphics (SVG)
* PostScript (EPS/PS)
* OpenType/TrueType Fonts (OTF, TTF)
* XML Paper Specification (XPS)
* Archive Files (ZIP/RAR)

Similar to services like Scribd, Google Doc Embedder will allow you to embed these files directly into your page or post, not requiring the user to have Microsoft Word, Adobe Reader, PowerPoint, or other software installed to view the contents. Unlike Scribd, the files do not need to be uploaded to any service first - including Google Docs - but can exist anywhere publicly accessible on your site or the internet.

Note: Use of this plug-in implies your agreement with Google's published [Terms of Service](http://docs.google.com/viewer/TOS?hl=en "Terms of Service").

Translations are welcome; see [the FAQ](http://wordpress.org/extend/plugins/google-document-embedder/faq/ "FAQ") for instructions.

* English (en\_US), built-in
* Spanish (es\_ES) by [elarequi](http://elarequi.com/propuestastic/ "elarequi"), thanks!
* Czech (cs\_CZ) by Jirka, thanks!
* Hungarian (hu\_HU) by szemcse, thanks!
* Turkish (tr\_TR) by [LettoBlog](http://profiles.wordpress.org/lettoblog "LettoBlog"), thanks!

== Installation ==

1. Upload the entire `google-document-embedder` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Done.

Upload the documents to your site using the media upload facility built into WordPress, via FTP, or link to documents on another (public) site. Use the Google Doc Embedder button in the Visual editor to build the appropriate shortcode, or see the FAQ for other methods.

For basic manual instructions, please see the FAQ. For advanced usage (including codes not exposed in the editor), see the [web site](http://www.davistribe.org/gde/usage/ "web site").

Go to "GDE Settings" (under "Settings" in the admin panel) to change defaults, or override individually using the shortcode syntax in the FAQ.

== Frequently Asked Questions ==

= What file types can be embedded? =
This plug-in currently can embed the following:

* Adobe Acrobat (PDF)
* Microsoft Word (DOC/DOCX)
* Microsoft PowerPoint (PPT/PPTX)
* Microsoft Excel (XLS/XLSX)
* TIFF Images (TIF, TIFF)
* Apple Pages (PAGES)
* Adobe Illustrator (AI)
* Adobe Photoshop (PSD)
* Autodesk AutoCad (DXF)
* Scalable Vector Graphics (SVG)
* PostScript (EPS/PS)
* OpenType/TrueType Fonts (OTF, TTF)
* XML Paper Specification (XPS)
* Archive Files (ZIP/RAR)

The file to embed must first be publicly available somewhere on the internet. You can upload it to your WordPress site using the standard techniques, or link to a file on another site.

= How do I embed a file in my page or post? =
There are several ways you can insert a supported document, depending on your preference:

* Manually enter the shortcode (explained below).
* Upload a supported file type from a page or post, and from the Media Library, click the "Insert" button.
* Use the Google Doc Embedder button in the Visual editor to insert the `[gview]` shortcode.
* Paste the URL into the HTML editor, select it, and click the "GDE" quicktag button (HTML editor).

To manually insert the `[gview]` shortcode into your page or post to embed the file, use the syntax below (use of the HTML tab in the editor recommended):

`[gview file="http://url.to/file.pdf"]`

Note: the `file=` attribute (generally pointing to the full URL of the file) is **required**. If the majority of your files are referenced from the same directory, you can set a File Base URL in GDE Settings and only put the changing portion in the `file=` attribute (or a full URL for a file outside of that base URL). File Base URL will be prepended to the value of `file=` unless `file=` starts with `http`.

Common optional attributes:

* `save=` : Set to 0 if you wish to suppress the direct download link to the file under the embedded viewer (1 for on, by default)
* `width=` : To override the default width of the viewer, enter a new width value - e.g., "400" (px) or "80%"
* `height=` : To override the default height of the viewer, enter a new height value - e.g., "400" (px) or "80%"
* `cache=` : Set to 0 to bypass the viewer's internal caching (useful only for frequently updated files with the same name)

For a list of all available attributes, see [Usage](http://www.davistribe.org/gde/usage/ "Usage").

= Will it embed files that are password-protected  or stored in protected folders/sites? =
Most likely, no. If your file requires a login to view - such as being saved in a password-protected directory, or behind a firewall (on your intranet, etc.), the viewer will probably not be able to access the file. This is what is meant above, that the document should be "publicly available." Please save the file in a publicly accessible location for best results.

= Nothing is showing up! What do I do? =
View the source on the web page where you've embedded the viewer. In order to degrade gracefully in case an error occurs, error messages will be inserted as HTML comments in these pages at the spot the viewer is called. If you don't like/can't cope with this behavior, it can be changed in GDE Settings > Advanced Options > Plugin Behavior.

= Does it work with files saved in Google Docs? =
This plug-in utilizes the viewer from Google Docs in a standalone fashion. There is no direct integration with Google Docs and even those documents stored there and shared publicly do not embed reliably with their viewer (ironically), so at this time that use is not supported by the plug-in. Please store your original documents somewhere on your web site in their native supported formats.

= How can I translate the plugin? =
You can use the [English translation](http://plugins.svn.wordpress.org/google-document-embedder/trunk/languages/gde-en_US.po "English") as a start. After saving the file, you can translate it by using a text editor or [Poedit](http://www.poedit.net/ "Poedit"). Or, you may install and use the [Codestyling Localization](http://wordpress.org/extend/plugins/codestyling-localization/ "Codestyling Localization") plugin.

Please email your translation, along with your name and link for credit, to <em>wpp @ tnw . org</em> for inclusion in the plugin.

= Where can I ask questions, report bug and request features? =
You can open a topic [on the forum](http://wordpress.org/support/plugin/google-document-embedder "forum") and I'll see what I can do. I review all messages posted here regularly. For detailed support on specific documents and uses, please use the "Support" link in your plugin list under Google Doc Embedder.

== Screenshots ==

1. Default appearance of embedded viewer
2. Settings page
3. TinyMCE Editor integration

== Changelog ==

(E) Enhanced Viewer

= 2.4.6 =
* Fixed: Error in Mask URL download link for non-PDF file types

= 2.4.5 =
* Fixed: Regression breaks some files containing spaces (thanks mlautens)
* Fixed: Mask URL 400 error on filenames with spaces (thanks mrhaanraadts)
* Fixed: PDF Force Download option doesn't support SSL

= 2.4.4 =
* Added: PPS and OTF support
* Fixed: Broken support of international filenames in IE (thanks beredim)
* Fixed: More robust file size checking with nonstandard filenames
* Fixed: Global disable cache option not always honored
* Fixed: (E) Mobile theme not loaded if not globally requested
* Changed: Now requires WordPress 3.0+ (mainly for support reasons)

= 2.4.3 =
* Added: (E) Dark theme shortcode option (EXPERIMENTAL)
* Added: Turkish translation (thanks LettoBlog)
* Fixed: Visual editor integration for IIS webhosts (thanks Kristof)
* Changed: Debug information is now a support page from plugin list

= 2.4.2 =
* Fixed: PHP Warning related to MIME type expansion (thanks Adebayo)

= 2.4.1 =
* Added: Spanish translation (thanks elarequi)
* Added: Method to obtain debug information
* Fixed: Insertion of non-GDE file types from Media Library

= 2.4 =
* Added: Allow native upload/insert of all supported file types
* Added: Shortcode inserted from Media Library for supported files
* Added: Localization support (translations welcome)
* Added: (E) Ability to use mobile theme
* Fixed: (E) Toolbar customization on mobile
* Fixed: Editor integration no longer loads its own TinyMCE/jquery libs
* Fixed: URL changes for plugin, help links, beta checking
* Fixed: (E) "Moved Temporarily" error (thanks webmonkeywatts)

= 2.3 =
* Added: Option to set base URL for embedded files (thanks KevEd)
* Added: Option to show error messages inline instead of as HTML comments
* Added: File type check in editor dialog
* Fixed: Download Link setting didn't update shortcode in editor dialog

= 2.2.3 =
* Fixed: (E) Additional bug fixes
* Fixed: jQuery error in editor integration

= 2.2.2 =
* Fixed: (E) Toolbar customizations broken after Google redesign
* Fixed: iPhone scrolling bug (thanks Vayu)
* Changed: Confirmed compatibility with WP 3.3

= 2.2.1 =
* Fixed: HTML syntax bug when custom dimensions provided

= 2.2 =
* Added: ZIP/RAR Archive support (thanks enkerli)

[Full history...](http://www.davistribe.org/gde/changelog/ "Full history")

== Upgrade Notice ==

= 2.4.5 =
Bug fix release