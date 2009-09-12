<?php

/*
Plugin Name: Google Doc Embedder
Plugin URI: http://wordpress.org/extend/plugins/google-document-embedder/
Description: Lets you embed PDF files and PowerPoint presentations in a page or post using the Google Document Viewer.
Author: Kevin Davis
Version: 0.3
Author URI: http://www.davismetro.com/
*/

// [gview file="http://path.to/file.pdf" save="1" width="600" height="500"]
function gviewer_func($atts) {
	extract(shortcode_atts(array(
		'file' => '',
		'save' => '1',
		'width' => '600',
		'height' => '500'
	), $atts));

	// supported file types - list acceptable extensions separated by |
	$exts = "pdf|ppt";
	
	// check link for validity
	if (!validLink($file)){
		$code = "\n<!-- GVIEW EMBED ERROR: invalid URL, please use fully qualified URL -->\n";
	} elseif (!validType($file,$exts)) {
		$code = "\n<!-- GVIEW EMBED ERROR: unsupported file type -->\n";
	} else {
	
		$code=<<<HERE
<iframe src="http://docs.google.com/gview?url=%U%&embedded=true" style="width:%W%px; height:%H%px;" frameborder="0"></iframe>
HERE;

		$code = str_replace("%U%", $file, $code);
		$code = str_replace("%W%", $width, $code);
		$code = str_replace("%H%", $height, $code);
		if ($save == "1") {
		$code .= "<p><a href=\"$file\" target=\"_blank\">download file</a></p>";
		}
		
	}
	
	return $code;
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

add_shortcode('gview', 'gviewer_func');

?>