=== Google Doc Embedder ===
Contributors: k3davis
Donate link: http://pledgie.com/campaigns/6048
Tags: doc, docx, pdf, ppt, pptx, xls, psd, svg, tiff, office, powerpoint, google docs, google
Requires at least: 2.8
Tested up to: 3.2
Stable tag: trunk

Lets you embed MS Office, PDF, and many other file types in a web page using the Google Docs Viewer (no Flash or PDF browser plug-ins required).

== Description ==

Google Doc Embedder lets you embed several types of files into your WordPress pages using the Google Docs Viewer - allowing inline viewing (and optional downloading) of the following file types, with no Flash or PDF browser plug-ins required:

* Adobe PDF
* Microsoft Word (DOC/DOCX)
* Microsoft PowerPoint (PPT/PPTX)
* Microsoft Excel (XLS/XLSX)
* TIFF Images
* Apple Pages (PAGES)
* Adobe Illustrator (AI)
* Adobe Photoshop (PSD)
* Autodesk AutoCad (DXF)
* Scalable Vector Graphics (SVG)
* PostScript (EPS/PS)
* TrueType (TTF)
* XML Paper Specification (XPS)
* Archive Files (ZIP/RAR)

Similar to services like Scribd, Google Doc Embedder will allow you to embed these files directly into your page or post, not requiring the user to have Microsoft Word, Adobe Reader, PowerPoint, or other software installed to view the contents. Unlike Scribd, the files do not need to be uploaded to any service first - including Google Documents - but can exist anywhere publicly accessible on your site or the internet.

Note: Use of this plug-in implies your agreement with Google's published <a href="http://docs.google.com/viewer/TOS?hl=en" target="_blank">Terms of Service</a>.

== Installation ==

1. Upload the entire `google-document-embedder` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Done.

Upload the documents to your site using the media upload facility built into WordPress, via FTP, or link to documents on another (public) site. Use the Google Doc Embedder button in the Visual editor to build the appropriate shortcode.

For basic manual instructions, please see the FAQ. For advanced usage (including codes not exposed in the editor), see the <a href="http://www.davismetro.com/gde/usage/">web site</a>.

Go to "GDE Settings" (under "Settings" in the admin panel) to change defaults, or override individually using the shortcode syntax in the FAQ.

== Frequently Asked Questions ==

= What file types can be embedded? =
This plug-in currently can embed the following:

* Adobe PDF
* Microsoft Word (DOC/DOCX)
* Microsoft PowerPoint (PPT/PPTX)
* Microsoft Excel (XLS/XLSX)
* TIFF Images
* Apple Pages (PAGES)
* Adobe Illustrator (AI)
* Adobe Photoshop (PSD)
* Autodesk AutoCad (DXF)
* Scalable Vector Graphics (SVG)
* PostScript (EPS/PS)
* TrueType (TTF)
* XML Paper Specification (XPS)
* Archive Files (ZIP/RAR)

The file to embed must first be publicly available somewhere on the internet. You can upload it to your WordPress site using the standard techniques, or link to a file on another site.

= How do I embed a file in my page or post? =
Use the Google Doc Embedder button in the Visual editor to insert the `[gview]` shortcode as described below. Alternately, you can paste the URL into the HTML editor, select it, and click the "GDE" quicktag button.

To manually insert the `[gview]` shortcode into your page or post to embed the file, use the syntax below (use of the HTML tab in the editor recommended):

`[gview file="http://url.to/file.pdf"]`

Note: the `file=` attribute (pointing to the full URL of the file) is **required**.

Optional attributes:

* `save=` : Set to 0 if you wish to suppress the direct download link to the file under the embedded viewer (1 for on, by default)
* `width=` : To override the default width of the viewer, enter a new width value - e.g., "400" (px) or "80%"
* `height=` : To override the default height of the viewer, enter a new height value - e.g., "400" (px) or "80%"
* `cache=` : Set to 0 to bypass the viewer's internal caching (useful only for frequently updated files)

For a list of all available attributes, see <a href="http://www.davismetro.com/gde/usage/">Usage</a>.

= Will it embed files that are password-protected  or stored in protected folders/sites? =
Most likely, no. If your file requires a login to view - such as being saved in a password-protected directory, or behind a firewall (on your intranet, etc.), the viewer will probably not be able to access the file. This is what is meant above, that the document should be "publicly available." Please save the file in a publicly accessible location for best results.

= Nothing is showing up! What do I do? =
View the source on the web page where you've embedded the viewer. In order to degrade gracefully in case an error occurs, error messages will be inserted as HTML comments in these pages at the spot the viewer is called.

= Does it work with files saved in Google Docs? =
This plug-in utilizes the viewer from Google Docs in a standalone fashion. There is no direct integration with Google Docs and even those documents stored there and shared publicly do not embed reliably with their viewer (ironically), so at this time that use is not supported by the plug-in. Please store your original documents somewhere on your web site in their native supported formats.

= I wish the plug-in had feature XYZ... =
That's not a question ;) but if you have any particular ideas on further development of this plug-in, please post <a href="http://wordpress.org/tags/google-document-embedder?forum_id=10#postform">on the forum</a> or privately using the <a href="http://www.davismetro.com/gde/support/">support form</a> and I'll see what I can do.

== Screenshots ==

1. Default appearance of embedded viewer
2. Settings page
3. TinyMCE Editor integration

== Changelog ==

= 2.2.1 =
* Fixed: HTML syntax bug when custom dimensions provided

= 2.2 =
* Added: ZIP/RAR Archive support (thanks enkerli)

= 2.1 =
* Added: Ability to track downloads with Google Analytics (thanks omarigil)
* Changed: New editor integration was disabled by default - oops! (thanks Brian)

= 2.0 =
* Added: TinyMCE and Quicktag editor integration (thanks cr.aguila)
* Added: Option to override internal caching of viewer (thanks Brian)
* Added: More individual overrides for global settings
* Fixed: Deprecated HTML output that interfered with some browsers
* Removed: Plugin conflict code and setting; not particularly useful

= 1.9.8 =
* Added: Viewer support for multiple new file types
* Changed: Workaround for NextGEN Gallery incompatibility (thanks alex)
* Changed: Defaults to Standard Viewer (IE problem is fixed!)

= 1.9.7 =
* Fixed: Minor compatibility issue with some PHP versions
* Changed: Confirmed compatibility with WP 3.1

= 1.9.6 =
* Changed: Removed min-width restriction of viewer (thanks Amanda)
* Fixed: Enhanced Viewer failed in hardened PHP configs (thanks Waseem)
* Fixed: Force Download option failed in hardened PHP configs
* Fixed: Options page layout quirks in lower resolutions
* Fixed: Options page PHP parse error on XAMPP (thanks John)

= 1.9.5 =
* Added: Support for Word documents (DOC, DOCX)
* Added: Default language option for viewer
* Added: Revived ability to hide selected viewer toolbar buttons (from 1.7)
* Changed: IE now supported by "enhanced viewer" proxy option (ie-warn removed)
* Changed: Improved options organization
* Fixed: Filenames with spaces wouldn't load
* Fixed: Suppress beta notification option not honored

= 1.9.4 =
* Added: Option to restrict download link to logged in users (thanks kris)
* Added: Compatibility with WP 3.0

= 1.9.3 =
* Added: Support for PPS files (thanks Dan)
* Changed: Simplified default IE Warning text

= 1.9.2 =
* Fixed: "Turn off beta notifications" needlessly still checked for beta version
* Confirmed WP 2.9 compatibility
* Beta delivery test

= 1.9.1 =
* Fixed: Options not saved in some instances due to variable collision (thanks gadgetto)
* Fixed: Options not saved when plugin is reactivated
* Changed: Default width now 100% (existing setting will be preserved)
* Added: Notification of beta versions - <a href="http://davismetro.com/gde/beta-program" target="_blank">more info</a>

= 1.9 =
* Added: Revealed more troubleshooting options (under "Advanced Options")
* Fixed: No longer relies on cURL for any function
* Changed: Function overhaul for general efficiency and reduced database calls
* Removed: WP 2.5 compatibility. Now requires WordPress 2.8+.

<a href="http://davismetro.com/gde/changelog/" target="_blank">Full history...</a>

== Upgrade Notice ==

= 2.2.1 =
Minor bug fix release