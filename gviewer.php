<?php

/*
Plugin Name: Google Doc Embedder
Plugin URI: http://wordpress.org/extend/plugins/google-document-embedder/
Description: Lets you embed PDF files and PowerPoint presentations in a page or post using the Google Document Viewer.
Author: Kevin Davis
Version: 1.0
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

// usage: [gview file="http://path.to/file.pdf" save="1" width="600" height="500"]
function gviewer_func($atts) {

	// defaults
	$dl = get_option('gde_show_dl');
	$wd = get_option('gde_default_width');
	$ht = get_option('gde_default_height');
	$txt = get_option('gde_link_text');
	extract(shortcode_atts(array(
		'file' => '',
		'save' => $dl,
		'width' => $wd,
		'height' => $ht
	), $atts));

	// supported file types - list acceptable extensions separated by |
	$exts = "pdf|ppt";
	
	// check link for validity
	if (!validLink($file)){
		$code = "\n<!-- GDE EMBED ERROR: invalid URL, please use fully qualified URL -->\n";
	} elseif (!validType($file,$exts)) {
		$code = "\n<!-- GDE EMBED ERROR: unsupported file type -->\n";
	} else {
	
		$code=<<<HERE
<iframe src="http://docs.google.com/gview?url=%U%&embedded=true" style="width:%W%px; height:%H%px;" frameborder="0"></iframe>
HERE;

		$code = str_replace("%U%", $file, $code);
		$code = str_replace("%W%", $width, $code);
		$code = str_replace("%H%", $height, $code);
		if ($save == "1") {
		$code .= "<p class=\"gde-dl\"><a href=\"$file\" target=\"_blank\">$txt</a></p>";
		}
		
	}
	
	return $code;
}

function validLink($link) {

$urlregex = "^https?\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";

    if (eregi($urlregex, $link)) {
        return true;
    } else {
        return false;
    }
}

function validType($link, $exts) {

	if(preg_match("/($exts)$/i",$link)){
        return true;
    } else {
        return false;
    }
}

// activate plugin
register_activation_hook( __FILE__, 'gde_activate');
function gde_activate() {
	global $wpdb;
	
	// initial options
	add_option('gde_default_width', '600');
	add_option('gde_default_height', '500');
	add_option('gde_show_dl', 1);
	add_option('gde_link_text', 'download file');
}

// add an option page
add_action('admin_menu', 'gde_option_page');
function gde_option_page() {
	add_options_page(t('GDE Settings'), t('GDE Settings'), 'administrator', basename(__FILE__), 'gde_options');
}
function gde_options() {
	if ( function_exists('current_user_can') && !current_user_can('manage_options') ) die(t('Cheatin&#8217; uh?'));
	if (! user_can_access_admin_page()) wp_die( t('You do not have sufficient permissions to access this page') );

	require(ABSPATH. '/wp-content/plugins/google-document-embedder/options.php');
}

// add additional settings link, for convenience
$plugin = plugin_basename(__FILE__); 
function my_plugin_actlinks( $links ) { 
 // Add a link to this plugin's settings page
 $settings_link = '<a href="/wp-admin/options-general.php?page=gviewer.php">Settings</a>'; 
 array_unshift( $links, $settings_link ); 
 return $links; 
}
add_filter("plugin_action_links_$plugin", 'my_plugin_actlinks' );

// activate shortcode
add_shortcode('gview', 'gviewer_func');

?>