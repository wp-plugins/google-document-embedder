<?php

# This proxy code is a bypass for an existing flaw in Google Docs Viewer that breaks the functionality
# for IE users. If you do not wish to use this code, select Google Standard Viewer rather than
# Enhanced Viewer in GDE Settings. Note that viewer toolbar customization options depend on this
# proxy workaround remaining enabled.
# 
# The problem this code addresses is discussed at length on Google's Help Forum:
# http://www.google.com/support/forum/p/Google+Docs/thread?tid=22d92671afd5b9b7&hl=en
# 
# This code is based on the work of Peter Chen. For more information, see:
# http://peterchenadded.herobo.com/gview/
#
# Peter's code is modified below to allow for cURL fallback and viewer toolbar customization.

// test for allow_url_fopen in php config; try curl for function if disabled
if (ini_get('allow_url_fopen') !== "1") {
  if (function_exists('curl_version')) {
    $curl = 1;
  } else {
	$err = "This function is not supported on your web server. Please add ";
	$err .= "<code>allow_url_fopen = 1</code> to your php.ini or enable cURL library. ";
	$err .= "If you are unable to do this, please switch to Google Standard ";
	$err .= "Viewer in GDE Options.";
	echo $err;
	exit;
  }
}

if (isset($_GET['embedded'])) { 
  // get the src page, change relative path to absolute
  if (isset($curl)) {
	$code = curl_get_contents("http://docs.google.com/gview?" . $_SERVER['QUERY_STRING']);
  } else {
    $code = file_get_contents("http://docs.google.com/gview?" . $_SERVER['QUERY_STRING']);
  }
  
  $search = array("/gview/images", "gview/resources_gview/client/js");  
  $replace = array("http://docs.google.com/gview/images", "?jsfile=gview/resources_gview/client/js");  
  
  if (isset($_GET['gdet'])) {
	$gdet = $_GET['gdet'];

	# hide google icon (i)
	/* This is no longer visible by default - not necessary
	if (strstr($gdet, 'i') !== false) { 
		$search[] = ".goog-logo-small {";
		$replace[] = ".goog-logo-small { display: none !important;";
	} */
	# hide single/double page view (p)
	/* no longer visible by default
	if (strstr($gdet, 'p') !== false) { 
		$search[] = ".controlbar-two-up-image {";
		$replace[] = ".controlbar-two-up-image { display: none !important;";
		$search[] = ".controlbar-one-up-image {";
		$replace[] = ".controlbar-one-up-image { display: none !important;";
	}
	*/
	# hide zoom in/out (z)
	if (strstr($gdet, 'z') !== false) { 
		$search[] = "#zoomOutToolbarButtonIcon {";
		$replace[] = "#zoomOutToolbarButtonIcon { display: none !important;";
		$search[] = "#zoomInToolbarButtonIcon {";
		$replace[] = "#zoomInToolbarButtonIcon { display: none !important;";
	}
	# hide open in new window (n)
	if (strstr($gdet, 'n') !== false) { 
		$search[] = "#openInViewerButtonIcon {";
		$replace[] = "#openInViewerButtonIcon { display: none !important;";
	}
	# fix remaining toolbar images
		$search[] = "/viewer/images/icon_sprites_6.png";
		$replace[] = "http://docs.google.com/viewer/images/icon_sprites_6.png";
  }
  
  $code = str_replace($search, $replace, $code);  
  
  header('Content-type: text/html');  
  echo $code;  
  
} else if (isset($_GET['a']) && $_GET['a'] == 'gt') {  
  // get text coordinates file, can not redirect because of same origin policy
  if (isset($curl)) {
    $code = curl_get_contents("http://docs.google.com/gview?" . $_SERVER['QUERY_STRING']);
  } else {
    $code = file_get_contents("http://docs.google.com/gview?" . $_SERVER['QUERY_STRING']);
  }
  
  header('Content-type: text/xml; charset=UTF-8');  
  echo $code;  
  
} else if (isset($_GET['a']) && $_GET['a'] == 'bi') {  
  // redirect to images  
  header("Location: http://docs.google.com/gview?" . $_SERVER['QUERY_STRING']);  
  header('Content-type: image/png');  
  
} else if (isset($_GET['jsfile'])) {  
  // proxy javascript files and replace navigator.cookieEnabled with false
  if (isset($curl)) {
    $code = curl_get_contents("http://docs.google.com/" . $_GET['jsfile']);  
  } else {
    $code = file_get_contents("http://docs.google.com/" . $_GET['jsfile']); 
  }
  
  $search = array("navigator.cookieEnabled");  
  $replace = array("false");  
  $code = str_replace($search, $replace, $code);  
  
  header('Content-type: text/javascript');  
  echo $code;  
  
} else {  
  // everything else, of which there isn't!  
  header("Location: http://docs.google.com/gview?" . $_SERVER['QUERY_STRING']);  
} 

function curl_get_contents($url) {
  $ch = curl_init();
  $timeout = 5; // set to zero for no timeout
  curl_setopt ($ch, CURLOPT_URL, $url);
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  $file_contents = curl_exec($ch);
  curl_close($ch); 
  
  return $file_contents;
}
?>