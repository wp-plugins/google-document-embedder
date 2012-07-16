<?php

// external urls (help, etc.)
@define('GDE_VIEWOPT_URL', 'http://www.davistribe.org/gde/settings/viewer-options/');
@define('GDE_LINKOPT_URL', 'http://www.davistribe.org/gde/settings/download-link-options/');
@define('GDE_ADVOPT_URL', 'http://www.davistribe.org/gde/settings/advanced-options/');
@define('GDE_FORUM_URL', 'http://wordpress.org/tags/google-document-embedder?forum_id=10');
@define('GDE_WP_URL', 'http://wordpress.org/extend/plugins/google-document-embedder/');
@define('GDE_BETA_URL', 'http://www.davistribe.org/gde/beta-program/');
@define('GDE_BETA_CHKFILE', 'http://dev.davismetro.com/beta/gde/beta.chk');

function gde_init($reset = NULL) {
	// set default base url
	$baseurl = get_bloginfo('url')."/wp-content/uploads/";
	
	// check for existing translation for locale
	$default_lang = gde_get_locale();
	
	// define global default settings
	$defaults = array(
		'default_width' => '100',
		'width_type' => 'pc',
		'default_height' => '500',
		'height_type' => 'px',
		'default_lang' => $default_lang,
		//'default_display' => 'inline',
		'restrict_tb' => '',
		'base_url' => $baseurl,
		'show_dl' => 'yes',
		'restrict_dl' => 'no',
		'enable_ga' => 'no',
		'link_text' => __('Download', 'gde').' (%FT, %FS)',
		'link_pos' => 'below',
		'link_func' => 'default',
		'disable_proxy' => 'yes',
		'ed_extend_upload' => 'yes',
		'ed_embed_sc' => 'yes',
		'disable_editor' => 'no',
		'disable_caching' => 'no',
		'disable_hideerrors' => 'no',
		'bypass_check' => 'no',
		'suppress_beta' => 'no'
	);
	
	if (!$exists = get_option('gde_options')) {
		foreach ($defaults as $key => $value) {
			// convert old settings if found
			$currvalue = get_option('gde_' . $key);
			if ($currvalue || $currvalue === "0" || $currvalue === "1") {
				if ($reset !== "reset") { 
					if ($currvalue === "0") { $defaults[$key] = "no"; }
					elseif ($currvalue === "1") { $defaults[$key] = "yes"; }
					else { $defaults[$key] = $currvalue; }
				}
				delete_option('gde_' . $key);
			}
		}
		add_option('gde_options', $defaults);
	} else {
		$gdeoptions = get_option('gde_options');
		if ($reset !== "reset") {
			// maintain existing settings
			foreach ($defaults as $key => $value) {
				if($gdeoptions[$key]) {
					$defaults[$key] = $gdeoptions[$key];
				}
			}
		}
		update_option('gde_options', $defaults);
	}
	return $defaults;
}

function gde_validTests($file = NULL, $force) {
	global $allowed_exts, $gdeoptions;
	
	// error messages
	$nofile = 'file attribute not found (check syntax)';
	$badlink = 'invalid URL, please use fully qualified URL';
	$badtype = '%e is not a supported file type';
	$notfound = 'retrieve error (%e), use force="1" to bypass this check';
	
	if (!$file) {
		return $nofile;
	}
	
	$result = gde_validUrl($file);
	if ($force == "1" || $force == "yes") {
		return $result;
	} else {
		if ($result['code'] !== 200) {
			if (!gde_validLink($file)) {
				return $badlink;
			} else {
				$err = $result['code'].":".$result['message'];
				$notfound = str_replace("%e", $err, $notfound);
				
				return $notfound;
			}
		} else {
			if (!gde_validType($file,$allowed_exts)) {
				$fn = basename($file);
				$fnp = gde_splitFilename($fn);
				$type = $fnp[1];
				$badtype = str_replace("%e", $type, $badtype);
				
				return $badtype;
			} else {
				return $result;
			}
		}
	}
}

function gde_validLink($link) {

    $urlregex = '/^(([\w]+:)?\/\/)(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/i';

    if (preg_match($urlregex, $link)) {
        return true;
    } else {
        return false;
    }
}

function gde_validType($link, $allowed_exts) {
    if (preg_match("/\.($allowed_exts)$/i",$link)) {
        return true;
    } else {
        return false;
    }
}

function gde_validUrl($url) {
	include_once( ABSPATH . WPINC . '/class-http.php' );
	
	$request = new WP_Http;
	$result = $request->request($url);
	if (is_array($result) && isset($result['headers']['content-length'])) {
		$result['response']['fsize'] = $result['headers']['content-length'];
		return $result['response'];
	} else {
		return false;
	}
}

function gde_splitFilename($filename) {
    $pos = strrpos($filename, '.');
    if ($pos === false) {
        return array($filename, ''); // no extension (dot is not found in the filename)
    } else {
        $basename = substr($filename, 0, $pos);
        $ext = substr($filename, $pos+1);
        return array($basename, $ext);
    }
}

function gde_formatBytes($bytes, $precision = 2) {
	if ($bytes < 1) {
		return "Unknown";
	} else {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');
		
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
		
		$bytes /= pow(1024, $pow);
		
		if ($units[$pow] == "KB") {
			// less precision for small file sizes
			return round($bytes)."KB";
		} else {
			return round($bytes, $precision) . $units[$pow];
		}
	}
}

function gde_sanitizeOpt($value, $type) {
	$value = preg_replace("/[^0-9]*/", '', $value);
	if (($type == "pc") && ($value > 100)) {
		$value = "100";
	}
	return $value;
}

function gde_shortUrl($u) {
	return wp_remote_fopen('http://tinyurl.com/api-create.php?url='.$u);
}

function gde_admin_print_scripts( $arg ) {
	global $pagenow;
	if (is_admin() && ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) ) {
		$js = GDE_PLUGIN_URL.'js/gde-quicktags.js';
		wp_enqueue_script("gde_qts", $js, array('quicktags') );
	}
}

function gde_admin_custom_js( $hook ) {
	global $gde_settings_page;
	
	if ( $gde_settings_page == $hook ) {
		$js = GDE_PLUGIN_URL.'js/gde-jquery.js';
		wp_enqueue_script( 'gde_jqs', $js );
	}
}

function gde_mce_addbuttons() {
	// don't bother doing this stuff if the current user lacks permissions
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		return;
	
	// add only in Rich Editor mode
	if ( get_user_option('rich_editing') == 'true') {
		add_filter("mce_external_plugins", "gde_add_tinymce_plugin");
		add_filter('mce_buttons', 'gde_register_mce_button');
   }
}

function gde_add_tinymce_plugin($plugin_array) {
	// load the TinyMCE plugin
	$plugin_array['gde'] = GDE_PLUGIN_URL.'js/editor_plugin.js';
	return $plugin_array;
}

function gde_register_mce_button($buttons) {
	array_push($buttons, "separator", "gde");
	return $buttons;
}

// modify the media insertion if requested
function gde_media_insert($html, $id, $attachment) {
	global $supported_exts;
	
	// get the mime type
	$mime_type = get_post_mime_type($id);

	if (in_array($mime_type, $supported_exts)) {
		// insert shortcode instead of link
		$output = '[gview file="'.$attachment['url'].'"]';
		return $output;
	} else {
		// default behavior
		return $html;
	}
}

function gde_upload_mimes ( $existing_mimes=array() ) {
	global $supported_exts;
	
	// add upload support for natively unsupported mimetypes used by this plugin
	foreach ($supported_exts as $ext => $mimetype) {
		if (!array_key_exists($ext, gde_mimes_expanded($existing_mimes))) {
			$existing_mimes[$ext] = $mimetype;
		}
	}
	return gde_mimes_collapsed($existing_mimes);
}

function gde_mimes_expanded(array $types) {
	// expand the supported mime types so that every ext is its own key
	foreach ($types as $k => $v) {
		if (strpos("|", $k)) {
			$subtypes = explode("|", $k);
			foreach ($subtypes as $type) {
				$newtypes[$type] = $v;
				unset($types[$k]);
			}
			$types = array_merge($types, $newtypes);
		}
	}
	return $types;
}

function gde_mimes_collapsed($types) {
	// collapes the supported mime types so that each mime is listed once with combined key (default)
	$newtypes = array();
	
	foreach ($types as $k => $v) {
		if (isset($newtypes[$v])) {
			$newtypes[$v] .= '|' . $k;
		} else {
            $newtypes[$v] = $k;
		}
	}
	return array_flip($newtypes);
}

/**
 * Get plugin data
 *
 * @since   2.4.0.1
 * @return  array Array of plugin data parsed from main plugin file
 */
function gde_get_plugin_data() {
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}
	$plugin_data  = get_plugin_data( GDE_PLUGIN_DIR . 'gviewer.php' );
	
	return $plugin_data;
}

/**
 * Get locale
 *
 * @since   2.4.1.1
 * @return  string Google viewer lang code based on WP_LANG setting, or en_US if not defined
 */
function gde_get_locale() {
	if ( function_exists( 'get_locale' ) ) {
		$locale = get_locale();
		$locale_files = array(
			GDE_PLUGIN_DIR."/languages/gde-$locale.mo",
			GDE_PLUGIN_DIR."/languages/gde-$locale.po" );
		if ( is_readable($locale_files[0]) && is_readable($locale_files[1]) ) {
		
			// enabled languages mapped to Google Viewer language codes
			if ($locale == "es_ES") { $locale = "es"; }
			
			return $locale;
		}
	}
	
	// default language if none can be set
	return "en_US";
}

/**
 * Display debug information
 *
 * @since   2.4.1.1
 * @return  string HTML outputting debug information
 */
function gde_debug() {
	global $gde_ver, $gdeoptions, $wp_version;
	$pdata = gde_get_plugin_data();
	
	include_once("libs/form-debug.php");
}

?>