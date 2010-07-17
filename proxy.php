<?php

# This proxy code is a bypass for an existing flaw in Google Docs Viewer that breaks the functionality
# for IE users. If you do not wish to use this code, you can disable it in GDE Advanced Options. Note
# that viewer toolbar customization options depend on this proxy workaround remaining enabled.
# 
# The problem this code addresses is discussed at length on Google's Help Forum:
# http://www.google.com/support/forum/p/Google+Docs/thread?tid=22d92671afd5b9b7&hl=en
# 
# This code is based on the work of Peter Chen. For more information, see:
# http://peterchenadded.herobo.com/gview/

if (isset($_GET['embedded'])) {  
  // get the src page, change relative to absolute and don't remove 'chan' param in get requests  
  $code = file_get_contents("http://docs.google.com/gview?" . $_SERVER['QUERY_STRING']);  
  
  $search = array("/gview/images", "gview/resources_gview/client/js");  
  $replace = array("http://docs.google.com/gview/images", "?jsfile=gview/resources_gview/client/js");  
  
  if (isset($_GET['gdet'])) {
	$gdet = $_GET['gdet'];

	# hide google icon (i)
	if (strstr($gdet, 'i') !== false) { 
		$search[] = ".goog-logo-small {";
		$replace[] = ".goog-logo-small { display: none !important;";
	}
	# hide single/double page view (p)
	if (strstr($gdet, 'p') !== false) { 
		$search[] = ".controlbar-two-up-image {";
		$replace[] = ".controlbar-two-up-image { display: none !important;";
		$search[] = ".controlbar-one-up-image {";
		$replace[] = ".controlbar-one-up-image { display: none !important;";
	}
	# hide zoom in/out (z)
	if (strstr($gdet, 'z') !== false) { 
		$search[] = ".controlbar-minus-image {";
		$replace[] = ".controlbar-minus-image { display: none !important;";
		$search[] = ".controlbar-plus-image {";
		$replace[] = ".controlbar-plus-image { display: none !important;";
	}
	# hide open in new window (n)
	if (strstr($gdet, 'n') !== false) { 
		$search[] = ".controlbar-open-in-viewer-image {";
		$replace[] = ".controlbar-open-in-viewer-image { display: none !important;";
	}
  }
  
  $code = str_replace($search, $replace, $code);  
  
  header('Content-type: text/html');  
  echo $code;  
  
} else if (isset($_GET['a']) && $_GET['a'] == 'gt') {  
  // get text coordinates file, can not redirect because of same origin policy  
  $code = file_get_contents("http://docs.google.com/gview?" . $_SERVER['QUERY_STRING']);  
  header('Content-type: text/xml; charset=UTF-8');  
  echo $code;  
  
} else if (isset($_GET['a']) && $_GET['a'] == 'bi') {  
  // redirect to images  
  header("Location: http://docs.google.com/gview?" . $_SERVER['QUERY_STRING']);  
  header('Content-type: image/png');  
  
} else if (isset($_GET['jsfile'])) {  
  // proxy javascript files and replace navigator.cookieEnabled with false  
  $code = file_get_contents("http://docs.google.com/" . $_GET['jsfile']);  
  
  $search = array("navigator.cookieEnabled");  
  $replace = array("false");  
  $code = str_replace($search, $replace, $code);  
  
  header('Content-type: text/javascript');  
  echo $code;  
  
} else {  
  // everything else, of which there isn't!  
  header("Location: http://docs.google.com/gview?" . $_SERVER['QUERY_STRING']);  
} 
?>