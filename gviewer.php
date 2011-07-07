<?php

/*
Plugin Name: Google Doc Embedder
Plugin URI: http://davismetro.com/gde/
Description: Lets you embed MS Office, PDF, TIFF, and many other file types in a web page using the Google Docs Viewer (no Flash or PDF browser plug-ins required).
Author: Kevin Davis
Version: 2.1
*/

$gde_ver = "2.1.0.98";

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
 * @copyright  Copyright 2011 Kevin Davis
 * @license    http://www.gnu.org/licenses/gpl.txt GPL 2.0
 * @link       http://davismetro.com/gde/
 */

include_once('gde-functions.php');
$gdeoptions = get_option('gde_options');
$pUrl = plugins_url(plugin_basename(dirname(__FILE__)));

// basic usage: [gview file="http://path.to/file.pdf"]
function gde_gviewer_func($atts) {

	// current settings
	global $gdeoptions, $exts, $pUrl, $user_ID;
	
	extract(shortcode_atts(array(
		'file' => '',
		'save' => $gdeoptions['show_dl'],
		'width' => '',
		'height' => '',
		'lang' => $gdeoptions['default_lang'],
		'force' => $gdeoptions['bypass_check'],
		'cache' => $gdeoptions['disable_cache'],
		'authonly' => $gdeoptions['restrict_dl'],
		'page' => ''
	), $atts));
	
	// translate nasty filenames with spaces
	if (strpos($file, " ")) {
		$file = str_replace(" ", "%20", $file);
	}
	
	$width = str_replace("px", "", trim($width));
	if (!$width || !preg_match("/^\d+%?$/", $width)) {
	    $width = $gdeoptions['default_width'];
		if ($gdeoptions['width_type'] == "pc") {
			$width .= "%";
		} else {
			$width .= "px";
		}
	}
	$height = str_replace("px", "", trim($height));
	if (!$height || !preg_match("/^\d+%?$/", $height)) {
		$height = $gdeoptions['default_height'];
		if ($gdeoptions['height_type'] == "pc") {
			$height .= "%";
		} else {
			$height .= "px";
		}
	}
	
	// supported file types - list acceptable extensions separated by |
	$exts = "doc|docx|pdf|ppt|pptx|pps|tif|tiff|xls|xlsx|pages|ai|psd|dxf|svg|eps|ps|ttf|xps";
	
	// check link for validity
	$status = gde_validTests($file, $force);
	if ($status && !is_array($status)) {
		$code = "\n<!-- GDE EMBED ERROR: $status -->\n";
	} else {
		$code = "";
	
		$fn = basename($file);
		$fnp = gde_splitFilename($fn);
		$fsize = $status['fsize'];
		$fsize = gde_formatBytes($fsize);
		
		$code .=<<<HERE
%A%
<iframe src="%U%" class="gde-frame" style="width:%W%; height:%H%; border: none;"></iframe>\n
%B%
HERE;

		// obfuscate filename if cache disabled
		if ($gdeoptions['disable_caching'] == "yes" || $cache == "no" || $cache == "0") {
			$uefile = urlencode($file)."%3F".time();
		} else {
			$uefile = urlencode($file);
		}
		if ($gdeoptions['disable_proxy'] == "no") {
			$gdet = $gdeoptions['restrict_tb'];
			$lnk = $pUrl."/proxy.php?url=".$uefile."&hl=".$lang."&gdet=".$gdet."&embedded=true";
		} else {
			$lnk = "http://docs.google.com/viewer?url=".$uefile."&hl=".$lang."&embedded=true";
		}
		if (is_numeric($page)) {
			// jump to selected page
			$page = (int) $page-1;
			$lnk = $lnk."#:0.page.".$page;
		}
		$linkcode = "";
		
		// hide download link for anonymous users
		get_currentuserinfo();
		$dlRestrict = $gdeoptions['restrict_dl'];
		if ($user_ID == '') {
			if ($dlRestrict == "yes" || $authonly == "yes" || $authonly == "1") {
				// no user logged in and restrict set; override link setting
				$save = "no";
			}
		}

		if ($save == "yes" || $save == "1") {
			
			$dlMethod = $gdeoptions['link_func'];
			if ($fnp[1] == "PDF") {
				if ($dlMethod == "force" or $dlMethod == "force-mask") {
					$dlFile = $pUrl;
					$fileParts = parse_url($file);
					$fileStr = str_replace($fileParts['scheme']."://","",$file);
					$dlFile .= "/pdf.php?file=".$fileStr."&fn=".$fn;
					$target = "_self";
					$gaTag = 'onclick="var that=this;_gaq.push([\'_trackEvent,\'Download\',\'PDF\',this.href]);setTimeout(function(){location.href=that.href;},200);return false;"';
				} elseif ($dlMethod == "default") {
					$dlFile = $file;
					$target = "_blank";
					$gaTag = 'onclick="_gaq.push([\'_trackEvent\',\'Download\',\'PDF\',this.href]);"';
				}
				if ($dlMethod == "force-mask") {
					$dlFile = gde_shortUrl($dlFile);
					$gaTag = 'onclick="var that=this;_gaq.push([\'_trackEvent,\'Download\',\'PDF\',this.href]);setTimeout(function(){location.href=that.href;},200);return false;"';
				}
				
			} elseif ($dlMethod == "force-mask") {
				$dlFile = gde_shortUrl($file);
				$target = "_self";
				$gaTag = 'onclick="var that=this;_gaq.push([\'_trackEvent,\'Download\',\''.$fnp[1].'\',this.href]);setTimeout(function(){location.href=that.href;},200);return false;"';
			} else {
				$dlFile = $file;
				$target = "_self";
				$gaTag = 'onclick="var that=this;_gaq.push([\'_trackEvent,\'Download\',\''.$fnp[1].'\',this.href]);setTimeout(function(){location.href=that.href;},200);return false;"';
			}
			$txt = $gdeoptions['link_text'];
			if ($gdeoptions['enable_ga'] == "yes") {
				$gaLink = " $gaTag";
			}
			$linkcode .= "<p class=\"gde-text\"><a href=\"$dlFile\" target=\"$target\" class=\"gde-link\"$gaLink>$txt</a></p>";
		}
		
		if ($gdeoptions['link_pos'] == "above") {
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
	add_options_page(gde_t('GDE Settings'), gde_t('GDE Settings'), 'administrator', basename(__FILE__), 'gde_options');
}
function gde_options() {
	if ( function_exists('current_user_can') && !current_user_can('manage_options') ) die(t('An error occurred.'));
	if (! user_can_access_admin_page()) wp_die( gde_t('You do not have sufficient permissions to access this page') );

	require(ABSPATH. '/wp-content/plugins/google-document-embedder/options.php');
	add_action('in_admin_footer', 'gde_admin_footer');
}

// add additional links, for convenience
$plugin = plugin_basename(__FILE__);
function gde_actlinks($links) { 
	$settings_link = '<a href="options-general.php?page=gviewer.php">Settings</a>'; 
	array_unshift($links, $settings_link); 
	return $links; 
}
function gde_metalinks($links, $file) {
	global $debug;
	$plugin = plugin_basename(__FILE__);
	if ($file == $plugin) {
		$support_link = '<a href="'.GDE_SUPPORT_URL.'">Support</a>';
		$links[] = $support_link;
	}
	return $links;
}
add_filter("plugin_action_links_$plugin", 'gde_actlinks');
add_filter("plugin_row_meta", 'gde_metalinks', 10, 2);

// check for beta, if enabled
function gde_checkforBeta($plugin) {
	global $gde_ver, $pUrl, $gdeoptions;
	
	$pdata = get_plugin_data(__FILE__);
	if (preg_match('/-dev$/i', $pdata['Version'])) { $isbeta = 1; }
	
	if (strpos($pUrl.'/gviewer.php', $plugin) !== false) {
		if ($gdeoptions['suppress_beta'] !== "yes") {
			$vcheck = wp_remote_fopen(GDE_BETA_CHKFILE);
		}
		if ($vcheck) {
			$lver = $gde_ver;
			
			$status = explode('@', $vcheck);
			$rver = $status[1];
			$message = $status[3];
			
			if ($isbeta) {
				$titleStr = "Updated beta";
				$msgStr = "A newer beta has been released. Please deactivate the plug-in and install the current version. Thanks for your help!";
			} else {
				$titleStr = "Beta";
				$msgStr = "Please deactivate the plug-in and install the current version if you wish to participate. Otherwise, you can turn off beta version checking in GDE Settings. Testers appreciated!";
			}
			$message = str_replace("%msg", $msgStr, $message);
			
			if ((version_compare(strval($rver), strval($lver), '>') == 1)) {
				$msg = __("$titleStr version available: ", "gde").'<strong>v'.$rver.'</strong> - '.$message;
				echo '<td colspan="5" class="plugin-update" style="line-height:1.2em; font-size:11px; padding:1px;"><div style="background:#A2F099;border:1px solid #4FE23F; padding:2px; font-weight:bold;">'.__("$titleStr version available.", "gde").' <a href="javascript:void(0);" onclick="jQuery(\'#gde-beta-msg\').toggle();">'.__("(more info)", "gde").'</a></div><div id="gde-beta-msg" style="display:none; padding:10px; text-align:center;" >'.$msg.'</div></td>';
			} elseif ($isbeta) {
				$msg = __("Thank you for running a test version of Google Doc Embedder. You are running the most current beta version. Please give feedback on this version using the &quot;Support&quot; link above. Thanks for your help!", "gde");
				echo '<td colspan="5" class="plugin-update" style="line-height:1.2em; font-size:11px; padding:1px;"><div style="border:1px solid; padding:2px; font-weight:bold;">'.__("You're running a beta version. Please give feedback.", "gde").' <a href="javascript:void(0);" onclick="jQuery(\'#gde-beta-msg\').toggle();">'.__("(more info)", "gde").'</a></div><div id="gde-beta-msg" style="display:none; padding:10px; text-align:center;" >'.$msg.'</div></td>';
			} else {
				return;
			}
		} elseif ($isbeta) {
			$msg = __("Thank you for running a test version of Google Doc Embedder. You are running the most current beta version. Please give feedback on this version using the &quot;Support&quot; link above. Thanks for your help!", "gde");
			echo '<td colspan="5" class="plugin-update" style="line-height:1.2em; font-size:11px; padding:1px;"><div style="border:1px solid; padding:2px; font-weight:bold;">'.__("You're running a beta version. Please give feedback.", "gde").' <a href="javascript:void(0);" onclick="jQuery(\'#gde-beta-msg\').toggle();">'.__("(more info)", "gde").'</a></div><div id="gde-beta-msg" style="display:none; padding:10px; text-align:center;" >'.$msg.'</div></td>';			
		}
	}
}
add_action('after_plugin_row', 'gde_checkforBeta');

// activate shortcode
add_shortcode('gview', 'gde_gviewer_func');

// editor integration (experimental)
if ($gdeoptions['disable_editor'] !== "yes") {
	// add quicktag
	add_action( 'admin_print_scripts', 'gde_admin_print_scripts' );
	
	// add tinymce button
	add_action('admin_init','gde_mce_addbuttons');
}

// footer credit
function gde_admin_footer() {
	$pdata = get_plugin_data(__FILE__);
	printf('%1$s plugin | Version %2$s<br />', $pdata['Title'], $pdata['Version']);
}

// temporarily move certain functions here to workaround NGG incompatibility
function gde_t($message) {
	return __($message, basename(dirname(__FILE__)));
}

?>