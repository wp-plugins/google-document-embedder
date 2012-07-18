<?php

# This proxy code is a bypass of default browser handling of PDF files, used for the
# "Force Download" option of Google Doc Embedder plug-in for WordPress. If you do not
# wish to use this code, set the Link Behavior option to "Browser Default" in GDE Settings.

// test for allow_url_fopen in php config; try curl for function if disabled
if (ini_get('allow_url_fopen') !== "1") {
	if (function_exists('curl_version')) {
		$curl = 1;
	} else {
		$err = "This function is not supported on your web server. Please add ";
		$err .= "<code>allow_url_fopen = 1</code> to your php.ini or enable cURL library. ";
		$err .= "If you are unable to do this, please change the Link Behavior setting to ";
		$err .= "Browser Default in GDE Options.";
		showErr($err);
		exit;
	}
}

if ((isset($_GET['fn'])) && (isset($_GET['file']))) { 
	// check for invalid file type
	if (!preg_match("/\.pdf$/i",$_GET['fn'])) {
		showErr('Invalid file type; action cancelled.');
	}
	
	// ready file
	$file = urldecode($file);
	$fileParts = parse_url($file);
	if (preg_match("/^https/i", $fileParts['scheme'])) {
		$ssl = true;
	} else {
		$ssl = false;
	}
	
	// get file
	if ($curl) {
		$code = @curl_get_contents($_GET['file'], $ssl);  
	} else {
		$code = @file_get_contents($_GET['file']); 
	}
	
	// output file
	header('Content-type: application/pdf');
	header('Content-Disposition: attachment; filename="'.$_GET['fn'].'"');
	echo $code;
	
} else {
	showErr('No filename specified; action cancelled.');
}

function curl_get_contents($url, $ssl) {
	$ch = curl_init();
	$timeout = 5; // set to zero for no timeout
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	if ($ssl) {
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
	}
	$file_contents = curl_exec($ch);
	curl_close($ch); 
	
	return $file_contents;
}

function showErr($msg) {
	echo "<html>$msg</html>";
	exit;
}
?>