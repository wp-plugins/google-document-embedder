<?php

/*
Plugin Name: Google Doc Embedder
Plugin URI: http://davismetro.com/gde/
Description: Lets you embed PDF files, PowerPoint presentations, and TIFF images in a web page using the Google Docs Viewer.
Author: Kevin Davis
Version: 1.8.1
*/

/**
 * LICENSE
 * This file is part of Google Doc Embedder.
 *
 * Google Doc Embedder is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package    google-document-embedder
 * @author     Kevin Davis <kev@tnw.org>
 * @copyright  Copyright 2009 Kevin Davis
 * @license    http://www.gnu.org/licenses/gpl.txt GPL 2.0
 * @version    1.8.1
 * @link       http://davismetro.com/gde/
 */

include_once('wpframe.php');
include_once('functions.php');

// usage: [gview file="http://path.to/file.pdf" save="1" width="600" height="500"]
function gviewer_func($atts) {

	// current settings
	$dl = get_option('gde_show_dl');
	$txt = get_option('gde_link_text');
	$ie8warn = get_option('gde_ie8_warn');
	$bypass = get_option('gde_bypass_check');
	extract(shortcode_atts(array(
		'file' => '',
		'save' => $dl,
		'width' => '',
		'height' => '',
		'force' => $bypass
	), $atts));
	$width = str_replace("px", "", trim($width));
	if (!$width || !preg_match("/^\d+%?$/", $width)) {
	    $width = get_option('gde_default_width');
		if (get_option('gde_width_type') == "pc") {
			$width .= "%";
		}
	}
	$height = str_replace("px", "", trim($height));
	if (!$height || !preg_match("/^\d+%?$/", $height)) {
		$height = get_option('gde_default_height');
		if (get_option('gde_height_type') == "pc") {
			$height .= "%";
		}
	}
	
	// supported file types - list acceptable extensions separated by |
	$exts = "pdf|ppt|tif|tiff";
	
	// check link for validity
	if (!$file) {
		$code = "\n<!-- GDE EMBED ERROR: file attribute not found (check syntax) -->\n";
	} elseif ((!validLink($file)) && ($force !== "1")) {
		$code = "\n<!-- GDE EMBED ERROR: invalid URL, please use fully qualified URL -->\n";
	} elseif ((!validType($file,$exts)) && ($force !== "1")) {
		$code = "\n<!-- GDE EMBED ERROR: unsupported file type -->\n";
	} elseif ((!$fsize = validUrl($file)) && ($force !== "1")) {
		$code = "\n<!-- GDE EMBED ERROR: file not found -->\n";
	} else {
		$pUrl = getPluginUrl();
	
		$fn = basename($file);
		$fnp = splitFilename($fn);
		$fsize = formatBytes($fsize);
		
		$code=<<<HERE
%A%
<iframe src="%U%" width="%W%" height="%H%" frameborder="0" style="min-width:305px;" class="gde-frame"></iframe>\n
%B%
HERE;

		$lnk = "http://docs.google.com/viewer?url=".urlencode($file)."&embedded=true";
		$linkcode = "";

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
			$linkcode .= "<p class=\"gde-text\"><a href=\"$dlFile\" target=\"$target\" class=\"gde-link\">$txt</a></p>";
		}

		if ($ie8warn == "1") {
			$warn = __("IE8 User: If you're having trouble viewing this document, go to Tools -> Internet Options -> Privacy -> Advanced -> Check &quot;Override automatic cookie handling&quot;.");
			$linkcode .= "\n<!--[if gte IE 8]>\n<p class=\"gde-iewarn\">".$warn."</p>\n<![endif]-->\n";
		}
		
		if (get_option('gde_link_pos') == "above") {
			$code = str_replace("%A%", $linkcode, $code);
			$code = str_replace("%B%", '', $code);
		} else {
			$code = str_replace("%A%", '', $code);
			$code = str_replace("%B%", $linkcode, $code);
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
	
	// remove deprecated options if present
	$legacy_options = getObsolete();
	foreach ($legacy_options as $lopt => $val) {
		delete_option($lopt);
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
function gde_actlinks( $links ) { 
 $settings_link = '<a href="options-general.php?page=gviewer.php">Settings</a>'; 
 array_unshift($links, $settings_link); 
 return $links; 
}
add_filter("plugin_action_links_$plugin", 'gde_actlinks' );

// activate shortcode
add_shortcode('gview', 'gviewer_func');

?>