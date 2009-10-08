<?php

/*
Plugin Name: Google Doc Embedder
Plugin URI: http://davismetro.com/gde/
Description: Lets you embed PDF files, PowerPoint presentations, and TIFF images in a web page using the Google Docs Viewer.
Author: Kevin Davis
Version: 1.7.1
*/

/*  Copyright 2009 Kevin Davis. E-mail: kev@tnw.org
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include_once('wpframe.php');
include_once('functions.php');

// usage: [gview file="http://path.to/file.pdf" save="1" width="600" height="500"]
function gviewer_func($atts) {

	// current settings
	$dl = get_option('gde_show_dl');
	$wd = get_option('gde_default_width');
	$ht = get_option('gde_default_height');
	$txt = get_option('gde_link_text');
	$xlogo = get_option('gde_xlogo');
	$xfull = get_option('gde_xfull');
	$xpgup = get_option('gde_xpgup');
	$xzoom = get_option('gde_xzoom');
	extract(shortcode_atts(array(
		'file' => '',
		'save' => $dl,
		'width' => $wd,
		'height' => $ht
	), $atts));

	// supported file types - list acceptable extensions separated by |
	$exts = "pdf|ppt|tif|tiff";
	
	// check link for validity
	if (!$file) {
		$code = "\n<!-- GDE EMBED ERROR: file attribute not found (check syntax) -->\n";
	} elseif (!validLink($file)){
		$code = "\n<!-- GDE EMBED ERROR: invalid URL, please use fully qualified URL -->\n";
	} elseif (!validType($file,$exts)) {
		$code = "\n<!-- GDE EMBED ERROR: unsupported file type -->\n";
	} elseif (!$fsize = validUrl($file)) {
		$code = "\n<!-- GDE EMBED ERROR: file not found -->\n";
	} else {
		$pUrl = getPluginUrl();
	
		$fn = basename($file);
		$fnp = splitFilename($fn);
		$fsize = formatBytes($fsize);
		
		$code=<<<HERE
<iframe src="%U%" style="width:%W%px; height:%H%px;" frameborder="0" class="gde-frame"></iframe>\n
HERE;

		$lnk = "http://docs.google.com/viewer?url=".urlencode($file)."&embedded=true";
		if ($xlogo || $xfull || $xpgup || $xzoom) {
			$lnk = $pUrl."/altview.php?loc=".urlencode($lnk);
			if ($xlogo == "1") { $lnk .= "&logo=no"; }
			if ($xfull == "1") { $lnk .= "&full=no"; }
			if ($xpgup == "1") { $lnk .= "&pgup=no"; }
			if ($xzoom == "1") { $lnk .= "&zoom=no"; }
		}

		if ($save == "1") {
		
			$dlMethod = get_option('gde_link_func');
			if ($fnp[1] == "PDF") {
				if ($dlMethod == "force" or $dlMethod == "force-mask") {
					$dlFile = $pUrl;
					$fileParts = parse_url($file);
					$fileStr = str_replace($fileParts['scheme']."://","",$file);
					$dlFile .= "/pdf.php?file=".$fileStr."&dl=1&fn=".$fn;
					$target = "_self";
				} elseif ($dlMethod == "default") {
					$dlFile = $file;
					$target = "_blank";
				}
				if ($dlMethod == "force-mask") {
					$dlFile = shortUrl($dlFile);
				}
				
			} elseif ($dlMethod == "force-mask") {
				$dlFile = shortUrl($file);
				$target = "_self";
			} else {
				$dlFile = $file;
				$target = "_self";
			}
			$linkcode = "<p class=\"gde-text\"><a href=\"$dlFile\" target=\"$target\" class=\"gde-link\">$txt</a></p>";
			
			if (get_option('gde_link_pos') == "above") {
				$code = $linkcode . '' . $code;
			} else {
				$code = $code . '' . $linkcode;
			}
		}

		$code = str_replace("%U%", $lnk, $code);
		$code = str_replace("%W%", $width, $code);
		$code = str_replace("%H%", $height, $code);
		$code = str_replace("%FN", $fn, $code);
		$code = str_replace("%FT", $fnp[1], $code);
		$code = str_replace("%FS", $fsize, $code);
		
	}
	
	return $code;
}

// activate plugin
register_activation_hook( __FILE__, 'gde_activate');
function gde_activate() {
	global $wpdb;
	
	// initial options
	$defaults = getDefaults();
	foreach($defaults as $set => $val) {
		add_option($set, $val);
	}
}

// add an option page
add_action('admin_menu', 'gde_option_page');
function gde_option_page() {
	add_options_page(t('GDE Settings'), t('GDE Settings'), 'administrator', basename(__FILE__), 'gde_options');
}
function gde_options() {
	if ( function_exists('current_user_can') && !current_user_can('manage_options') ) die(t('An error occurred.'));
	if (! user_can_access_admin_page()) wp_die( t('You do not have sufficient permissions to access this page') );

	require(ABSPATH. '/wp-content/plugins/google-document-embedder/options.php');
}

// add additional settings link, for convenience
$plugin = plugin_basename(__FILE__); 
function my_plugin_actlinks( $links ) { 
 $settings_link = '<a href="options-general.php?page=gviewer.php">Settings</a>'; 
 array_unshift( $links, $settings_link ); 
 return $links; 
}
add_filter("plugin_action_links_$plugin", 'my_plugin_actlinks' );

// activate shortcode
add_shortcode('gview', 'gviewer_func');

?>