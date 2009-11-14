<?php

/*
Plugin Name: Google Doc Embedder
Plugin URI: http://davismetro.com/gde/
Description: Lets you embed PDF files, PowerPoint presentations, and TIFF images in a web page using the Google Docs Viewer (no Flash or PDF browser plug-ins required).
Author: Kevin Davis
Version: 1.9
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
 * @version    1.9
 * @link       http://davismetro.com/gde/
 */

include_once('wpframe.php');
include_once('functions.php');
$options = get_option('gde_options');

// basic usage: [gview file="http://path.to/file.pdf"]
function gde_gviewer_func($atts) {

	// current settings
	global $options, $exts;
	
	extract(shortcode_atts(array(
		'file' => '',
		'save' => $options['show_dl'],
		'width' => '',
		'height' => '',
		'force' => $options['bypass_check']
	), $atts));
	
	$width = str_replace("px", "", trim($width));
	if (!$width || !preg_match("/^\d+%?$/", $width)) {
	    $width = $options['default_width'];
		if ($options['width_type'] == "pc") {
			$width .= "%";
		}
	}
	$height = str_replace("px", "", trim($height));
	if (!$height || !preg_match("/^\d+%?$/", $height)) {
		$height = $options['default_height'];
		if ($options['height_type'] == "pc") {
			$height .= "%";
		}
	}
	
	// supported file types - list acceptable extensions separated by |
	$exts = "pdf|ppt|tif|tiff";
	
	// check link for validity
	$status = gde_validTests($file, $force);
	if ($status && !is_array($status)) {
		$code = "\n<!-- GDE EMBED ERROR: $status -->\n";
	} else {
		$code = "";
		$pUrl = plugins_url(plugin_basename(dirname(__FILE__)));
	
		$fn = basename($file);
		$fnp = gde_splitFilename($fn);
		$fsize = $status['fsize'];
		$fsize = gde_formatBytes($fsize);
		
		$code .=<<<HERE
%A%
<iframe src="%U%" width="%W%" height="%H%" frameborder="0" style="min-width:305px;" class="gde-frame"></iframe>\n
%B%
HERE;

		$lnk = "http://docs.google.com/viewer?url=".urlencode($file)."&embedded=true";
		$linkcode = "";

		if ($save == "yes" || $save == "1") {
		
			$dlMethod = $options['link_func'];
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
					$dlFile = gde_shortUrl($dlFile);
				}
				
			} elseif ($dlMethod == "force-mask") {
				$dlFile = gde_shortUrl($file);
				$target = "_self";
			} else {
				$dlFile = $file;
				$target = "_self";
			}
			$txt = $options['link_text'];
			$linkcode .= "<p class=\"gde-text\"><a href=\"$dlFile\" target=\"$target\" class=\"gde-link\">$txt</a></p>";
		}

		if ($options['ie8_warn'] == "yes") {
			$warn = gde_warnText($pUrl);
			$linkcode .= "\n<!--[if gte IE 7]>\n<p class=\"gde-iewarn\">".$warn."</p>\n<![endif]-->\n";
		}
		
		if ($options['link_pos'] == "above") {
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
	$init = gde_init();
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
	add_action('in_admin_footer', 'gde_admin_footer');
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
add_shortcode('gview', 'gde_gviewer_func');

// display any conflict warnings (admin)
if (($options['ignore_conflicts'] !== "yes") && (!isset($_REQUEST['submit']))) {
	add_action('plugins_loaded', 'gde_conflict_check');
}

// footer credit
function gde_admin_footer() {
	$plugin_data = get_plugin_data( __FILE__ );
	printf('%1$s plugin | Version %2$s<br />', $plugin_data['Title'], $plugin_data['Version']);
}

?>