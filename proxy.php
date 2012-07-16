<?php

// access wp functions externally
require_once('libs/bootstrap.php');

// get settings
$gdeoptions = get_option('gde_options');
$tb = $gdeoptions['restrict_tb'];

# This proxy code was originally intended as a bypass for a longstanding flaw in Google Docs Viewer
# that broke the functionality for some IE users. That use is rarely or not relevant since around the
# release of version 1.9.8 of this plugin. However, it's been commandeered to perform other, more
# groovy functions.
#
# If you do not wish to use this code, select Google Standard Viewer rather than
# Enhanced Viewer in GDE Settings. Note that viewer customization options depend on this
# proxy workaround remaining enabled.
# 
# The problem this code originally addressed was discussed at length on Google's Help Forum:
# http://www.google.com/support/forum/p/Google+Docs/thread?tid=22d92671afd5b9b7&hl=en
# 
# The original proxy code was based on the work of Peter Chen. For more information, see:
# http://peterchenadded.herobo.com/gview/
#
# Peter's code was modified to allow for cURL fallback, viewer customization options, mobile versions,
# and to reflect changes in the viewer since the code was first released.

// test for allow_url_fopen in php config; try curl for function if disabled
if (ini_get('allow_url_fopen') !== "1") {
	if (function_exists('curl_version')) {
		$curl = 1;
	} else {
		$err = _e('This function is not supported on your web server. Please add
			<code>allow_url_fopen = 1</code> to your php.ini or enable cURL library.
			If you are unable to do this, please switch to Google Standard Viewer in GDE Options.', 'gde');
		exit;
	}
}

// check for mobile
if (strstr($_SERVER['QUERY_STRING'], 'mobile=true') !== false) {	// already set
	$mobile = true;
} elseif (strstr($tb, 'm') !== false) { 	// specifically requested mobile version
	$_SERVER['QUERY_STRING'] .= "&mobile=true";
	$mobile = true;
} elseif (gde_mobile_check()) {				// is otherwise a mobile browser, by best guess
	$_SERVER['QUERY_STRING'] .= "&mobile=true";
	$mobile = true;
}

if (isset($_GET['embedded']) || isset($_GET['mobile'])) {
	
	// get the src page, change relative path to absolute
	if (isset($curl)) {
		$code = gde_curl_get_contents("https://docs.google.com/viewer?" . $_SERVER['QUERY_STRING']);
	} else {
		$code = file_get_contents("https://docs.google.com/viewer?" . $_SERVER['QUERY_STRING']);
	}
	
	// fix path to images - full URL in current viewer makes these unnecessary
	//$search[] = "/viewer/images";
	//$replace[] = "https://www.gstatic.com/docs/gview/images/icon_sprites_6.png";
	//$search[] = "/gview/images";
	//$replace[] = "https://docs.google.com/viewer/images";
	
	// proxy the javascript file
	$search[] = "gview/resources_gview/client/js";
	$replace[] = "?jsfile=gview/resources_gview/client/js";
	
	# hide zoom in/out (z)
	if (strstr($tb, 'z') !== false) { 
		if ($mobile) {
			$search[] = ".mobile-button-zoom-out {";
			$replace[] = ".mobile-button-zoom-out { display: none !important;";
			$search[] = ".mobile-button-zoom-in {";
			$replace[] = ".mobile-button-zoom-in { display: none !important;";
		} else {
			$search[] = "#zoomOutToolbarButtonIcon {";
			$replace[] = "#zoomOutToolbarButtonIcon { display: none !important;";
			$search[] = "#zoomInToolbarButtonIcon {";
			$replace[] = "#zoomInToolbarButtonIcon { display: none !important;";
		}
	}
	# hide open in new window (n)
	if (!isset($mobile)) {
		if (strstr($tb, 'n') !== false) { 
			$search[] = "#openInViewerButtonIcon {";
			$replace[] = "#openInViewerButtonIcon { display: none !important;";
		}
	} else {
		# hide mobile footer (always){
		$search[] = "#page-footer {";
		$replace[] = "#page-footer { display: none !important;";
	}
	
	// perform string replacements  
	$code = str_replace($search, $replace, $code);
	
	// perform theme replacement (experimental)
	if (isset($_GET['t'])) {
		if ($_GET['t'] == 'dark') {
			$pattern = '#(<style type="text/css">.view.*</style>)#';
			$replacement = '$1'."\n".'<link rel="stylesheet" type="text/css" href="themes/gde-dark.css">';
			$code = preg_replace($pattern, $replacement, $code);
		}
	}
	
	// output page
	header('Content-type: text/html');
	echo $code;  
	
} elseif (isset($_GET['a']) && $_GET['a'] == 'gt') {
	// get text coordinates file, can not redirect because of same origin policy
	if (isset($curl)) {
		$code = gde_curl_get_contents("https://docs.google.com/viewer?" . $_SERVER['QUERY_STRING']);
	} else {
		$code = file_get_contents("https://docs.google.com/viewer?" . $_SERVER['QUERY_STRING']);
	}
	
	header('Content-type: text/xml; charset=UTF-8');  
	echo $code;  
	
} elseif (isset($_GET['a']) && $_GET['a'] == 'bi') {
	// redirect to images  
	header("Location: https://docs.google.com/viewer?" . $_SERVER['QUERY_STRING']);  
	header('Content-type: image/png');  
	
} elseif (isset($_GET['jsfile'])) {
	// proxy javascript files and replace navigator.cookieEnabled with false
	if (isset($curl)) {
		$code = gde_curl_get_contents("https://docs.google.com/" . $_GET['jsfile']);  
	} else {
		$code = file_get_contents("https://docs.google.com/" . $_GET['jsfile']); 
	}
	
	$search = array("navigator.cookieEnabled");  
	$replace = array("false");  
	$code = str_replace($search, $replace, $code);  
	
	header('Content-type: text/javascript');  
	echo $code;  
	
} else {  
	// everything else, of which there isn't!  
	header("Location: https://docs.google.com/viewer?" . $_SERVER['QUERY_STRING']);  
} 

/**
 * Fetch remote file source
 *
 * @since   1.7.0.0
 * @return  string Contents of remote file
 */
function gde_curl_get_contents($url) {
	$ch = curl_init();
	$timeout = 5; // set to zero for no timeout
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
	$file_contents = curl_exec($ch);
	curl_close($ch); 
	
	return $file_contents;
}

/**
 * Check for mobile browser
 *
 * @since   2.5.0.1
 * @return  bool Browser is detected as mobile, or not
 */
function gde_mobile_check() {
	include_once("libs/mobile-check.php");
	
	if (gde_is_mobile_browser()) {
		return true;
	} else {
		return false;
	}
}

?>