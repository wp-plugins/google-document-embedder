<?php

/*
Plugin Name: Google Doc Embedder
Plugin URI: http://www.davistribe.org/gde/
Description: Lets you embed MS Office, PDF, TIFF, and many other file types in a web page using the Google Docs Viewer (no Flash or PDF browser plug-ins required).
Author: Kevin Davis
Author URI: http://www.davistribe.org/
Text Domain: gde
Domain Path: /languages/
Version: 2.5.1
License: GPLv2
*/

$gde_ver = "2.5.1.96";

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
 * @author     Kevin Davis <wpp@tnw.org>
 * @copyright  Copyright 2012 Kevin Davis
 * @license    http://www.gnu.org/licenses/gpl.txt GPL 2.0
 * @link       http://www.davistribe.org/gde/
 */

// boring init junk
require_once('functions.php');
$pdata 					= gde_get_plugin_data();
$gdeoptions				= get_option('gde_options');
$healthy				= gde_debug_tables('gde_profiles');
global $wp_version;

// get global settings
if ( is_multisite() ) {
	//$gdeglobals			= get_site_option('gde_globals'); // not implemented yet
}

// activate plugin, allow full uninstall
register_activation_hook( __FILE__, 'gde_activate' );
register_uninstall_hook( __FILE__, 'gde_uninstall' );

// allow localisation
load_plugin_textdomain( 'gde', false, basename( dirname( __FILE__ ) ) . '/languages' );

// activate shortcode
add_shortcode( 'gview', 'gde_do_shortcode' );

// basic usage: [gview file="http://path.to/file.pdf"]
function gde_do_shortcode( $atts ) {
	
	// current settings
	global $gdeoptions, $healthy; //$gdeglobals
	
	// check profile table health
	if ( ! $healthy ) {
		delete_option('gde_db_version');
		return gde_show_error( __('Unable to load profile settings', 'gde') );
	}
	
	// handle global setting overrides - not active in this release
	/*
	if ($gdeglobals['enforce_viewer'] == "std") {
		$gdeoptions['disable_proxy'] = "yes";
	}
	if ($gdeglobals['enforce_lang']) {
		$gdeoptions['default_lang'] = $gdeglobals['enforce_lang'];
	}
	*/
	
	extract(shortcode_atts(array(
		'file' => '',
		'profile' => 1, // default profile is always ID 1
		'save' => '',
		'width' => '',
		'height' => '',
		'cache' => '',
		'title' => '', // not yet implemented
		'page' => '',
		
		// backwards compatibility < gde 2.5 (still work but make undocumented as shortcode opts)
		'authonly' => '',
		'lang' => ''
	), $atts));
	
	// get requested profile data (or default if doesn't exist)
	$term = $profile;
	if ( is_numeric( $term ) ) {
		// id-based lookup
		if ( ! $profile = gde_get_profiles( $term ) ) {
			gde_dx_log("Loading default profile instead");
			if ( ! $profile = gde_get_profiles( 1 ) ) {
				$code = gde_show_error( __('Unable to load requested profile.', 'gde') );
			} else {
				$pid = 1;
			}
		} else {
			$pid = $term;
		}
	} else {
		// name-based lookup
		if ( ! $profile = gde_get_profiles( strtolower( $term ) ) ) {
			gde_dx_log("Loading default profile instead");
			if ( ! $profile = gde_get_profiles( 1 ) ) {
				$code = gde_show_error( __('Unable to load requested profile.', 'gde') );
			} else {
				$pid = 1;
			}
		} else {
			$pid = $profile['profile_id'];
		}
	}
	
	// use profile defaults if shortcode override not defined
	if ( empty( $save ) ) {
		$save = $profile['link_show'];
	}
	if ( empty( $width ) ) {
		$width = $profile['default_width'];
	}
	if ( empty( $height ) ) {
		$height = $profile['default_height'];
	}
	if ( $cache !== "0" ) {
		if ( empty( $cache ) ) {
			$cache = $profile['cache'];
		}
	}
	if ( empty( $lang ) ) {
		if ( $profile['language'] !== "en_US" ) {
			$lang =  $profile['language'];
		}
	}
	
	// tweak the dimensions if necessary
	$width = gde_sanitize_dims( $width );
	$height = gde_sanitize_dims( $height );
	
	// add base url if needed
	if ( ! preg_match( "/^http/i", $file ) ) {
		if ( substr( $file, 0, 2 ) == "//" ) {
			// append dynamic protocol
			if ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ) {
				$file = "https:" . $file;
			} else {
				$file = "http:" . $file;
			}
		} elseif ( isset( $profile['base_url'] ) ) {
			// not a full link, add base URL if available
			if ( substr( $file, 0, 1 ) == "/" ) {
				// remove any preceding slash from doc (base URL adds it)
				$file = ltrim( $file, '/' );
			}
			$file = $profile['base_url'].$file;
		}
	}
	
	// capture file details
	$fn = basename( $file );
	$fnp = gde_split_filename( $fn );
	
	// file validation
	if ( $gdeoptions['error_check'] == "no" ) {
		$force = true;
	} else {
		$force = false;
	}
	$status = gde_validate_file( str_replace( " ", "%20", $file ), $force );
	
	if ( ! isset( $code ) && ! is_array( $status ) && $status !== -1 ) {
		// validation failed
		$code = gde_show_error( $status );
	} elseif ( ! isset( $code ) ) {
		// validation passed or was skipped
		
		// check for max filesize
		$viewer = true;
		if ( $gdeoptions['file_maxsize'] > 0 && isset( $status['fsize'] ) ) {
			$maxbytes = (int) $gdeoptions['file_maxsize'] * 1024 * 1024;
			if ( $status['fsize'] > $maxbytes ) {
				$viewer = false;
			}
		}
		
		// generate links (embed, download)
		$links = array( $file, $file );
		if ( $profile['link_block'] == "yes" && gde_is_blockable( $profile ) ) {
			if ( $secure = gde_get_secure_url( $file ) ) {
				$links[0] = $secure;
			} else {
				$links[0] = '';
			}
			$links[1] = '';
		} elseif ( $profile['link_show'] !== "none" ) {
			if ( $profile['link_force'] == "yes" && $profile['link_mask'] == "no" ) {
				$links[1] = GDE_PLUGIN_URL . "load.php?d=" . urlencode( $links[1] );
			} elseif ( $profile['link_force'] == "no" && $profile['link_mask'] == "yes" ) {
				$short = gde_get_short_url( $links[0] );
				$links[0] = $short;
				$links[1] = $short;
			} elseif ( $profile['link_force'] == "yes" && $profile['link_mask'] == "yes" ) {
				$short = gde_get_short_url( GDE_PLUGIN_URL . "load.php?d=" . urlencode( $links[0] ) );
				$links[0] = $short;
				$links[1] = $short;
			}
		}
		
		// obfuscate filename if cache disabled (globally or via shortcode)
		if ( ! empty( $links[0] ) && ( $cache == "off" || $cache == "0" ) ) {
			$links[0] .= "?" . time();
		}
		
		// check for failed secure doc
		if ( empty( $links[0] ) && empty( $links[1] ) ) {
			$code = gde_show_error( __('Unable to secure document', 'gde') );
		} else {
		
			// which viewer?
			if ( $profile['viewer'] == "enhanced" ) {
				$lnk = GDE_PLUGIN_URL . "view.php?url=" . urlencode( $links[0] ) . "&hl=" . $lang . "&gpid=" . $pid;
			} else {
				$lnk = "http://docs.google.com/viewer?url=" . urlencode( $links[0]  ) . "&hl=" . $lang;
			}
			
			// what mode?
			if ( $profile['tb_mobile'] == "always" ) {
				$lnk .= "&mobile=true";
			} else {
				$lnk .= "&embedded=true";
			}
			
			// build viewer
			if ( $viewer == false ) {
				// exceeds max filesize
				$vwr = '';
			} else {
				$vwr = '<iframe src="%U%" class="gde-frame" style="width:%W%; height:%H%; border: none;"%ATTRS%></iframe>';
				$vwr = str_replace("%U%", $lnk, $vwr);
				$vwr = str_replace("%W%", $width, $vwr);
				$vwr = str_replace("%H%", $height, $vwr);
				
				// frame attributes
				$vattr[] = ' scrolling="no"';						// iphone scrolling bug
				if ( ! empty( $page ) && is_numeric( $page ) ) {	// selected starting page
					$page = (int) $page - 1;
					$vattr[] = ' onload="javascript:this.contentWindow.location.hash=\':0.page.' . $page . '\';"';
				}
				$vwr = str_replace( "%ATTRS%", implode( '', $vattr ), $vwr );
			}
			
			// show download link?
			$allow_save = false;
			if ( ! empty( $links[1] ) ) {	// link empty = secure document; ignore any other save attribute
				if ( $save == "all" || $save == "1" ) {
					$allow_save = true;
				} elseif ( ( $save == "users" || $authonly == "1" ) && is_user_logged_in() ) {
					$allow_save = true;
				}
			}
			
			if ( $allow_save ) {
				// build download link
				$linkcode = '<p class="gde-text"><a href="%LINK%" class="gde-link"%ATTRS%>%TXT%</a></p>';
				$linkcode = str_replace( "%LINK%", $links[1], $linkcode );
				
				// fix type
				$ftype = strtoupper( $fnp[1] );
				if ( $ftype == "TIF" ) { 
					$ftype = "TIFF";
				}
				
				// link attributes
				if ( $profile['link_mask'] == "yes" ) {
					$attr[] = ' rel="nofollow"';
				}
				$attr[] = gde_ga_event( $file ); // GA integration
				$linkcode = str_replace("%ATTRS%", implode( '', $attr ), $linkcode);
				
				// link text
				if ( empty( $profile['link_text'] ) ) {
					$profile['link_text'] = __('Download', 'gde');
				}
				$dltext = str_replace( "%TITLE", $title, $profile['link_text'] );
				$dltext = str_replace( "%FILE", $fn, $dltext );
				$dltext = str_replace( "%TYPE", $ftype, $dltext );
				$dltext = str_replace( "%SIZE", gde_format_bytes( $status['fsize'] ), $dltext );
				
				$linkcode = str_replace( "%TXT%", $dltext, $linkcode );
			} else {
				$linkcode = '';
			}
			
			// link position
			if ( $profile['link_pos'] == "above" ) {
				$code = $linkcode . "\n" . $vwr;
			} else {
				$code = $vwr . "\n" . $linkcode;
			}
		}
	}
	
	return $code;
}

if ( is_admin() ) {
	// add quick settings link to plugin list
	add_filter("plugin_action_links_" . plugin_basename(__FILE__), 'gde_actlinks');
	
	// beta notification (if enabled)
	if ( gde_check_for_beta( __FILE__ ) ) {
		// override plugin update text
		add_action( 'admin_enqueue_scripts', 'gde_admin_beta_js_update' );
	} else {
		// no update available, but notify if currently using a beta
		add_action( 'after_plugin_row', 'gde_warn_on_plugin_page' );
	}
	
	// editor integration
	if ( ! isset( $gdeoptions['ed_disable'] ) || $gdeoptions['ed_disable'] == "no" ) {
		// add quicktag
		add_action( 'admin_print_scripts', 'gde_admin_print_scripts' );
		
		// add tinymce button
		add_action( 'admin_init','gde_mce_addbuttons' );
		
		// extend media upload support to natively unsupported mime types
		if ( $gdeoptions['ed_extend_upload'] == "yes" ) {
			add_filter( 'upload_mimes', 'gde_upload_mimes' );
		}
		
		if ( version_compare( $wp_version, "3.5", "<" ) ) {
			// embed shortcode instead of link from media library for supported types
			add_filter( 'attachment_fields_to_edit', 'gde_attachment_fields_to_edit', null, 2 );
			add_filter( 'media_send_to_editor', 'gde_media_insert', 20, 3 );
		}
	}
	
	// add local settings page
	add_action( 'admin_menu', 'gde_option_page' );
	
	//if ( is_multisite() ) {
		// add global settings page
		//add_action( 'network_admin_menu', 'gde_site_option_page' );	// not present in this release
	//}
}

/**
 * Activate the plugin
 *
 * @since   0.2
 * @return  void
 * @note	This function must remain in this file
 */
function gde_activate() {
	require_once('libs/lib-setup.php');
	gde_setup();
}

/**
 * Uninstall the plugin
 *
 * @since   2.5.0.1
 * @return  void
 * @note	This function must remain in this file. Was using uninstall.php in prior versions.
 */
function gde_uninstall() {
	global $wpdb;
	
	// delete all options
	delete_option('gde_options');
	delete_option('gde_db_version');
	delete_site_option('gde_globals');
	
	// remove beta cache, if any
	delete_option('external_updates-google-document-embedder');
	delete_transient('gde_beta_version');
	
	// drop db tables
	$table = $wpdb->prefix . 'gde_profiles';
	$wpdb->query( "DROP TABLE IF EXISTS $table" );
	$table = $wpdb->prefix . 'gde_secure';
	$wpdb->query( "DROP TABLE IF EXISTS $table" );
	$table = $wpdb->prefix . 'gde_dx_log';
	$wpdb->query( "DROP TABLE IF EXISTS $table" );
}

?>