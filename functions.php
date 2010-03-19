<?php

// external urls (help, etc.)
@define('GDE_IE8_URL', 'http://davismetro.com/gde/ie8/');
@define('GDE_CONFLICT_URL', 'http://davismetro.com/gde/conflicts/');
@define('GDE_SUPPORT_URL', 'http://davismetro.com/gde/contact/');
@define('GDE_BETA_URL', 'http://davismetro.com/gde/beta-program/');
@define('GDE_BETA_CHKFILE', 'http://davismetro.com/gde/beta/gde-beta.chk');

function gde_init($reset = NULL) {
	// define global default settings
	$defaults = array(
		'default_width' => '100',
		'width_type' => 'pc',
		'default_height' => '500',
		'height_type' => 'px',
		'show_dl' => 'yes',
		'link_text' => 'Download (%FT, %FS)',
		'link_pos' => 'below',
		'link_func' => 'default',
		'ie8_warn' => 'no',
		'bypass_check' => 'no',
		'ignore_conflicts' => 'no',
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
	global $exts;
	
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
			if (!gde_validType($file,$exts)) {
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

function gde_validType($link, $exts) {

    if(preg_match("/($exts)$/i",$link)) {
        return true;
    } else {
        return false;
    }
}

function gde_validUrl($url) {
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

function gde_warnText($u) {
	return wp_remote_fopen($u."/ie-warn.txt");
}

function gde_conflict_check() {
	global $gde_conflict_list;
	
	// Markdown
	if (function_exists('mdwp_add_p')) {
		$gde_conflict_list = "markdown";
		add_action('admin_notices', 'gde_admin_warning');
	}
	return;
}

function gde_admin_warning() {
	global $gde_conflict_list;
	$gde_link = GDE_CONFLICT_URL."#$gde_conflict_list";
	
	echo "
		<div id='gde-warning' class='updated fade'><p><strong>".__('Google Doc Embedder Warning:')."</strong> ".sprintf(__('You have an active plugin that may conflict with GDE. See <a href="%1$s">more info</a> or <a href="%2$s">turn off this warning</a>.'), "$gde_link", "options-general.php?page=gviewer.php")."</p></div>
	";
}

?>