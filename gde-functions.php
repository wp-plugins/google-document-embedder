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
    if ($pos === false) {
        return array($filename, ''); // no extension (dot is not found in the filename)
    } else {
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
	
?>	
<div class="wrap">
<h2>Google Doc Embedder <?php _e('Support', 'gde'); ?></h2>

<p><strong><?php _e('Please review the documentation before submitting a request for support:', 'gde'); ?></strong></p>
<ul style="list-style-type:square; padding-left:25px;line-height:1em;">
	<li><a href="<?php echo $pdata['PluginURI']; ?>">Google Doc Embedder</a></li>
	<li><a href="<?php echo GDE_WP_URL; ?>faq/"><?php _e('Plugin FAQ', 'gde'); ?></li>
	<li><a href="<?php echo GDE_FORUM_URL; ?>"><?php _e('Support Forum', 'gde'); ?></a></li>
</ul>

<p><?php _e("If you're still experiencing a problem, please complete the form below.", 'gde'); ?></p>

<form action="<?php echo GDE_PLUGIN_URL;?>libs/post-debug.php" id="debugForm">

<table class="form-table" style="border:1px solid #ccc;">
<tr valign="top">
	<th scope="row"><label for="name" id="name_label"><?php _e('Your Name', 'gde'); ?></label></th>
	<td><input size="50" name="name" id="name" value="" type="text"></td>
</tr>
<tr valign="top">
	<th scope="row"><label for="sender" id="sender_label"><?php _e('Your E-mail', 'gde'); ?>*</label></th>
	<td><input size="50" name="email" id="sender" value="" type="text">
	<div id="err_email" class="err" style="color:red;font-weight:bold;display:none;">A valid email address is required.</div></td>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><label for="sc" id="sc_label"><?php _e('Shortcode', 'gde'); ?></label></th>
	<td><input size="50" name="shortcode" id="sc" value="" type="text"><br/>
	<em><?php _e("If you're having a problem getting a specific document to work, paste the shortcode you're trying to use here.", 'gde'); ?></em></td>
	</select>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><label for="msg" id="msg_label"><?php _e('Message', 'gde'); ?></label></th>
	<td><textarea name="message" id="msg" style="width:75%;min-height:50px;"></textarea></td>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Message Options', 'gde'); ?></th>
	<td>
	<input type="checkbox" name="senddb" id="senddb" checked="checked"> <label for="senddb" id="senddb_label"><?php _e('Send debug information', 'gde'); ?>
	(<a href="javascript:void(0);" id="ta_toggle"><?php _e('View'); ?></a>)</label><br/>
	<input type="checkbox" name="cc" id="cc"> <label for="cc" id="cc_label"><?php _e('Send me a copy', 'gde'); ?></label></td>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div id="debugblock" style="display:none;">
	<p><?php _e('Debug Information', 'gde'); ?>:</p>
	<textarea name="debug" id="debugtxt" style="width:100%;min-height:200px;font-family:monospace;" readonly="readonly">
<?php
echo "--- GDE Debug Information ---\n\n";
echo "GDE Version: $gde_ver\n";
echo "WordPress Version: $wp_version [".get_locale()."]\n";
echo "PHP Version: ".phpversion()."\n";
echo "Plugin URL: ".GDE_PLUGIN_URL."\n";
echo "Server Env: ".$_SERVER['SERVER_SOFTWARE']."\n";
echo "Browser Env: ".$_SERVER['HTTP_USER_AGENT']."\n\n";

echo "cURL: ";
if (function_exists('curl_version')) {
	$curl = curl_version(); echo $curl['version']."\n";
} else { echo "No\n"; }
echo "allow_url_fopen: ";
if (ini_get('allow_url_fopen') !== "1") {
	echo "No\n";
} else { echo "Yes\n"; }
echo "Rich Editing: ";
if (get_user_option('rich_editing')) {
	echo "Yes\n";
} else { echo "No\n"; }
echo "Viewer: ";
if ($gdeoptions['disable_proxy'] == "no") {
	echo "Enhanced\n\n";
} else { echo "Standard\n\n"; }

echo "Settings Array:\n";
print_r($gdeoptions);
//echo "\n";
//echo "MIME Supported:\n";
//print_r(get_allowed_mime_types());
?>
	</textarea>
	<br/><br/>
	</div>
</div>
	<div id="debugwarn" style="display:none;color:red;font-weight:bold;">
		<p><?php _e("I'm less likely to be able to help you if you do not include debug information.", 'gde'); ?></p>
	</div>
	<input id="debugsend" class="button-primary" type="submit" value="<?php _e('Send Support Request', 'gde'); ?>" name="submit">
	<span id="formstatus" style="padding-left:20px;display:none;">
		<img src="<?php echo GDE_PLUGIN_URL;?>img/in-proc.gif" alt="">
	</span>
	</td>
</tr>
</table>
</form>

</div>
<?php
}

?>