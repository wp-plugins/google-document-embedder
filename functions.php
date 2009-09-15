<?php

function getDefaults() {
	// set global default settings
	$defaults = array(
	"gde_default_width" => "600",
	"gde_default_height" => "500",
	"gde_show_dl" => 1,
	"gde_link_text" => "Download (%FT, %FS)",
	"gde_link_pos" => "below",
	"gde_link_func" => "default"
	);
	
	return $defaults;
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

function validUrl($url) {

	// checks for existence and returns filesize
    $handle = curl_init($url);
    if (false === $handle)
    {
        return false;
    }
    curl_setopt($handle, CURLOPT_HEADER, true);
    curl_setopt($handle, CURLOPT_FAILONERROR, true); 
    curl_setopt($handle, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") ); // needed for some sites (such as digg.com)
    curl_setopt($handle, CURLOPT_NOBODY, true);
	curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true); // only useful in case of redirects
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($handle);
    curl_close($handle);
	
	$contentLength = "Unknown";
	if (preg_match('/Content-Length: (\d+)/', $data, $matches)) {
		$contentLength = (int)$matches[1];
	}
    return $contentLength;
}

function splitFilename($filename) {
    $pos = strrpos($filename, '.');
    if ($pos === false)
    { // dot is not found in the filename
        return array($filename, ''); // no extension
    }
    else
    {
        $basename = substr($filename, 0, $pos);
        $extension = strtoupper(substr($filename, $pos+1));
        return array($basename, $extension);
    }
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
  
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
  
    $bytes /= pow(1024, $pow);
  
    return round($bytes, $precision) . '' . $units[$pow];
}

function sanitizeOpt($value) {
	$value = ereg_replace('[^0-9]+', '', $value);
	return $value;
}

function getPluginUrl() {
	// this is a backwards-compatibility function for WP 2.5
	if (!function_exists('plugins_url')) {
		return get_option('siteurl') . '/wp-content/plugins/' . plugin_basename(dirname(__FILE__));
	}

	return plugins_url(plugin_basename(dirname(__FILE__)));
	}


function shortUrl($u) {
	return file_get_contents('http://tinyurl.com/api-create.php?url='.$u);
}

?>