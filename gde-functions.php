<?php

// external urls (help, etc.)
@define('GDE_VIEWOPT_URL', 'http://davismetro.com/gde/settings/viewer-options/');
@define('GDE_LINKOPT_URL', 'http://davismetro.com/gde/settings/download-link-options/');
@define('GDE_ADVOPT_URL', 'http://davismetro.com/gde/settings/advanced-options/');
@define('GDE_SUPPORT_URL', 'http://davismetro.com/gde/contact/');
@define('GDE_BETA_URL', 'http://davismetro.com/gde/beta-program/');
@define('GDE_BETA_CHKFILE', 'http://davismetro.com/gde/beta/gde-beta.chk');

if ( ! defined( 'GDE_PLUGIN_URL' ) )  define( 'GDE_PLUGIN_URL', WP_PLUGIN_URL . '/google-document-embedder');

function gde_init($reset = NULL) {
	$baseurl = get_bloginfo('url')."/wp-content/uploads/";
	
	// define global default settings
	$defaults = array(
		'default_width' => '100',
		'width_type' => 'pc',
		'default_height' => '500',
		'height_type' => 'px',
		'default_lang' => 'en_US',
		//'default_display' => 'inline',
		'restrict_tb' => '',
		'base_url' => $baseurl,
		'show_dl' => 'yes',
		'restrict_dl' => 'no',
		'enable_ga' => 'no',
		'link_text' => 'Download (%FT, %FS)',
		'link_pos' => 'below',
		'link_func' => 'default',
		'disable_proxy' => 'yes',
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
    if (preg_match("/($allowed_exts)$/i",$link)) {
        return true;
    } else {
        return false;
    }
}

function gde_validUrl($url) {
	if (!class_exists('WP_Http')) {
		// wp3 compatibility
		include_once( ABSPATH . WPINC . '/class-http.php' );
	}
	$request = new WP_Http;
	$result = $request->request($url);
	if (is_array($result)) {
		$result['response']['fsize'] = $result['headers']['content-length'];
		return $result['response'];
	} else {
		return false;
	}
}

function gde_splitFilename($filename) {
    $pos = strrpos($filename, '.');
    if ($pos === false)
    { // dot is not found in the filename
        return array($filename, ''); // no extension
    }
    else
    {
        $basename = substr($filename, 0, $pos);
        $extension = strtoupper(substr($filename, $pos+1));
		if ($extension == "TIF") { $extension = "TIFF"; }
        return array($basename, $extension);
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
  
		return round($bytes, $precision) . '' . $units[$pow];
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
		$js = GDE_PLUGIN_URL.'/js/gde-quicktags.js';
		wp_enqueue_script("gde_qts", $js, array('quicktags') );
	}
}

function gde_admin_custom_js( $hook ) {
	global $gde_settings_page;
	
	if ( $gde_settings_page == $hook ) {
		wp_enqueue_script( 'gde_jqs', plugins_url('/js/gde-jquery.js', __FILE__) );
	}
}

function gde_mce_addbuttons() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
 
   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "gde_add_tinymce_plugin");
     add_filter('mce_buttons', 'gde_register_mce_button');
   }
}
 
function gde_register_mce_button($buttons) {
   array_push($buttons, "separator", "gde");
   return $buttons;
}

function gde_add_tinymce_plugin($plugin_array) {
	// Load the TinyMCE plugin
   $plugin_array['gde'] = GDE_PLUGIN_URL.'/js/editor_plugin.js';
   return $plugin_array;
}

function gde_e($message) {
	_e($message, basename(dirname(__FILE__)));
}
?>