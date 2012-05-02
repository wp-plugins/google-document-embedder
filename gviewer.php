<?php

/*
Plugin Name: Google Doc Embedder
Plugin URI: http://www.davistribe.org/gde/
Description: Lets you embed MS Office, PDF, TIFF, and many other file types in a web page using the Google Docs Viewer (no Flash or PDF browser plug-ins required).
Author: Kevin Davis
Author URI: http://www.davistribe.org/
Text Domain: gde
Domain Path: /languages/
Version: 2.4
License: GPLv2
*/

$gde_ver = "2.4.0.98";

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
 * @copyright  Copyright 2012 Kevin Davis
 * @license    http://www.gnu.org/licenses/gpl.txt GPL 2.0
 * @link       http://www.davistribe.org/gde/
 */

if ( ! defined( 'GDE_PLUGIN_DIR' ) )
    define( 'GDE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
if ( ! defined( 'GDE_PLUGIN_URL' ) )
    define( 'GDE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	
include_once('gde-functions.php');
$gdeoptions = get_option('gde_options');
$pUrl = plugins_url(plugin_basename(dirname(__FILE__)));

// note: updates here should also be reflected in js/dialog.js
$supported_exts = array(
	// ext		=>	mime_type
	"ai"		=>	"application/postscript",
	"doc"		=>	"application/msword",
	"docx"		=>	"application/vnd.openxmlformats-officedocument.wordprocessingml",
	"dxf"		=>	"application/dxf",
	"eps"		=>	"application/postscript",
	"pages"		=>	"application/x-iwork-pages-sffpages",
	"pdf"		=>	"application/pdf",
	"ppt"		=>	"application/vnd.ms-powerpoint",
	"pptx"		=>	"application/vnd.openxmlformats-officedocument.presentationml",
	"ps"		=>	"application/postscript",
	"psd"		=>	"image/photoshop",
	"rar"		=>	"application/rar",
	"svg"		=>	"image/svg+xml",
	"tif"		=>	"image/tiff",
	"tiff"		=>	"image/tiff",
	"ttf"		=>	"application/x-font-ttf",
	"xls"		=>	"application/vnd.ms-excel",
	"xlsx"		=>	"application/vnd.openxmlformats-officedocument.spreadsheetml",
	"xps"		=>	"application/vnd.ms-xpsdocument",
	"zip"		=>	"application/zip"
);
$allowed_exts = implode("|",array_keys($supported_exts));

// basic usage: [gview file="http://path.to/file.pdf"]
function gde_gviewer_func($atts) {
	
	// current settings
	global $gdeoptions, $pUrl, $user_ID;
	
	extract(shortcode_atts(array(
		'file' => '',
		'display' => $gdeoptions['default_display'],
		'save' => $gdeoptions['show_dl'],
		'width' => '',
		'height' => '',
		'lang' => $gdeoptions['default_lang'],
		'force' => $gdeoptions['bypass_check'],
		'cache' => $gdeoptions['disable_cache'],
		'authonly' => $gdeoptions['restrict_dl'],
		'page' => ''
	), $atts));
	
	// add base url if needed
	if (!preg_match("/^http/i",$file) && $gdeoptions['base_url']) {
		// deal with potential slash issues
		if (!preg_match("/\/$/", $gdeoptions['base_url'])) {
			$gdeoptions['base_url'] = $gdeoptions['base_url']."/";
		}
		if (preg_match("/^\//", $file)) {
			$file = $file = substr($file, 1);
		}
		$file = $gdeoptions['base_url'].$file;
	}
	
	// set or clean up dimension values
	$width = str_replace("px", "", trim($width));
	if (!$width || !preg_match("/^\d+%?$/", $width)) {
		$width = $gdeoptions['default_width'];
		if ($gdeoptions['width_type'] == "pc") {
			$width .= "%";
		}
	}
	if (!strpos($width, "%")) {
		$width .= "px";
	}
	
	$height = str_replace("px", "", trim($height));
	if (!$height || !preg_match("/^\d+%?$/", $height)) {
		$height = $gdeoptions['default_height'];
		if ($gdeoptions['height_type'] == "pc") {
			$height .= "%";
		}
	}
	if (!strpos($height, "%")) {
		$height .= "px";
	}
	
	// check link for validity
	$status = gde_validTests($file, $force);
	if ($status && !is_array($status)) {
		if (($gdeoptions['disable_hideerrors'] == "no") || !$gdeoptions['disable_hideerrors']) {
			$code = "\n<!-- GDE EMBED ERROR: $status -->\n";
		} else {
			$code = "\n".'<div class="gde-error">Google Doc Embedder '.__('Error', 'gde').": ".$status."</div>\n";
		}
	} else {
		$code = "";
		
		$fn = basename($file);
		$fnp = gde_splitFilename($fn);
		$fsize = gde_formatBytes($status['fsize']);
		
		// translate filenames with spaces and other forms of wickedness
		$fn = rawurlencode($fn);
		
		$code .=<<<HERE
%A%
<iframe src="%U%" class="gde-frame" style="width:%W%; height:%H%; border: none;" scrolling="no"></iframe>\n
%B%
HERE;

		// obfuscate filename if cache disabled
		if ($gdeoptions['disable_caching'] == "yes" || $cache == "no" || $cache == "0") {
			$uefile = $file."%3F".time();
		} else {
			$uefile = $file;
		}
		// check for proxy
		if ($gdeoptions['disable_proxy'] == "no") {
			$lnk = $pUrl."/proxy.php?url=".$uefile."&hl=".$lang;
		} else {
			$lnk = "http://docs.google.com/viewer?url=".$uefile."&hl=".$lang;
		}
		// check for mobile
		if (strstr($gdeoptions['gdet'], 'm') !== false) {
			$lnk .= "&mobile=true";
		} else {
			$lnk .= "&embedded=true";
		}
		// check for page
		if (is_numeric($page)) {
			// jump to selected page - experimental (works on refresh but not initial page load)
			$page = (int) $page-1;
			$lnk = $lnk."#:0.page.".$page;
		}
		// hide download link for anonymous users
		get_currentuserinfo();
		$dlRestrict = $gdeoptions['restrict_dl'];
		if ($user_ID == '') {
			if ($dlRestrict == "yes" || $authonly == "yes" || $authonly == "1") {
				// no user logged in and restrict set; override link setting
				$save = "no";
			}
		}
		
		$linkcode = "";
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
// allow localisation
load_plugin_textdomain('gde', false, basename( dirname( __FILE__ ) ) . '/languages' );

function gde_activate() {
	global $wpdb;
	
	// initial options
	$init = gde_init();
}

// add an option page
add_action('admin_menu', 'gde_option_page');
function gde_option_page() {
	global $gde_settings_page;
	
	$gde_settings_page = add_options_page('GDE '.__('Settings', 'gde'), 'GDE '.__('Settings', 'gde'), 'manage_options', basename(__FILE__), 'gde_options');

	// enable settings jQuery
	add_action( 'admin_enqueue_scripts', 'gde_admin_custom_js' );
}
function gde_options() {
	if ( function_exists('current_user_can') && !current_user_can('manage_options') ) wp_die('You don\'t have access to this page.');
	if (! user_can_access_admin_page()) wp_die( __('You do not have sufficient permissions to access this page', 'gde') );
	
	require(plugin_dir_path(__FILE__).'/options.php');
	add_action('in_admin_footer', 'gde_admin_footer');
}

// add additional links, for convenience
$plugin = plugin_basename(__FILE__);
function gde_actlinks($links) { 
	$settings_link = '<a href="options-general.php?page=gviewer.php">'.__('Settings', 'gde').'</a>'; 
	array_unshift($links, $settings_link); 
	return $links; 
}
function gde_metalinks($links, $file) {
	global $debug;
	$plugin = plugin_basename(__FILE__);
	if ($file == $plugin) {
		$support_link = '<a href="'.GDE_SUPPORT_URL.'">'.__('Support', 'gde').'</a>';
		$links[] = $support_link;
	}
	return $links;
}
add_filter("plugin_action_links_$plugin", 'gde_actlinks');
add_filter("plugin_row_meta", 'gde_metalinks', 10, 2);

// check for beta, if enabled
function gde_checkforBeta($plugin) {
	global $gde_ver, $pUrl, $gdeoptions;
	
	// beta messages
	$beta_msg['avail'] = array(__('Beta version available', 'gde'), __('Please deactivate the plug-in and install the current version if you wish to participate. Otherwise, you can turn off beta version checking in GDE Settings. Testers appreciated!', 'gde'));
	$beta_msg['newer'] = array(__('Updated beta version available', 'gde'), __('A newer beta has been released. Please deactivate the plug-in and install the current version. Thanks for your help!', 'gde'));
	$beta_msg['current'] = array(__('You\'re running a beta version. Please give feedback.', 'gde'), __('Thank you for running a test version of Google Doc Embedder. You are running the most current beta version. Please give feedback on this version using the &quot;Support&quot; link above. Thanks for your help!', 'gde'));
	$beta_msg['link'] = __('more info', 'gde');
	
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
				$titleStr = $beta_msg['newer'][0];
				$msgStr = $beta_msg['newer'][1];
			} else {
				$titleStr = $beta_msg['avail'][0];
				$msgStr = $beta_msg['avail'][1];
			}
			$message = str_replace("%msg", $msgStr, $message);
			
			if ((version_compare(strval($rver), strval($lver), '>') == 1)) {
				$msg = "$titleStr: <strong>v".$rver."</strong> - ".$message;
				echo '<td colspan="5" class="plugin-update" style="line-height:1.2em; font-size:11px; padding:1px;"><div style="background:#A2F099;border:1px solid #4FE23F; padding:2px; font-weight:bold;">'.$titleStr.'. <a href="javascript:void(0);" onclick="jQuery(\'#gde-beta-msg\').toggle();">('.$beta_msg['link'].')</a></div><div id="gde-beta-msg" style="display:none; padding:10px; text-align:center;">'.$msg.'</div></td>';
			} elseif ($isbeta) {
				$msg = $beta_msg['current'][0];
				echo '<td colspan="5" class="plugin-update" style="line-height:1.2em; font-size:11px; padding:1px;"><div style="border:1px solid; padding:2px; font-weight:bold;">'.$beta_msg['current'][1].' <a href="javascript:void(0);" onclick="jQuery(\'#gde-beta-msg\').toggle();">('.$beta_msg['link'].')</a></div><div id="gde-beta-msg" style="display:none; padding:10px; text-align:center;" >'.$msg.'</div></td>';
			} else {
				return;
			}
		} elseif ($isbeta) {
			$msg = $beta_msg['current'][1];
			echo '<td colspan="5" class="plugin-update" style="line-height:1.2em; font-size:11px; padding:1px;"><div style="border:1px solid; padding:2px; font-weight:bold;">'.$beta_msg['current'][0].' <a href="javascript:void(0);" onclick="jQuery(\'#gde-beta-msg\').toggle();">('.$beta_msg['link'].')</a></div><div id="gde-beta-msg" style="display:none; padding:10px; text-align:center;" >'.$msg.'</div></td>';			
		}
	}
}
add_action('after_plugin_row', 'gde_checkforBeta');

// activate shortcode
add_shortcode('gview', 'gde_gviewer_func');

// editor integration
if ($gdeoptions['disable_editor'] !== "yes") {
	// add quicktag
	add_action( 'admin_print_scripts', 'gde_admin_print_scripts' );
	
	// add tinymce button
	add_action('admin_init','gde_mce_addbuttons');
	
	// extend media upload support to natively unsupported mime types
	if ($gdeoptions['ed_extend_upload'] == "yes") {
		add_filter('upload_mimes', 'gde_upload_mimes');
	}
	
	// embed shortcode instead of link from media library for supported types
	if ($gdeoptions['ed_embed_sc'] == "yes") {
		add_filter('media_send_to_editor', 'gde_media_insert', 20, 3);
	}
}

// footer credit
function gde_admin_footer() {
	$pdata = get_plugin_data(__FILE__);
	$plugin_str = __('plugin', 'gde');
	$version_str = __('Version', 'gde');
	printf('%1$s %2$s | %3$s %4$s<br />', $pdata['Title'], $plugin_str, $version_str, $pdata['Version']);
}

?>