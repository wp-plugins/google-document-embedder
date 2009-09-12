<?php

/*
Plugin Name: Google Doc Embedder
Plugin URI: http://wordpress.org/extend/plugins/google-document-embedder/
Description: Lets you embed PDF files and PowerPoint presentations in a page or post using the Google Document Viewer.
Author: Kevin Davis
Version: 0.2.1
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

	$code=<<<HERE
	<iframe src="http://docs.google.com/gview?url=%U%&embedded=true" style="width:%W%px; height:%H%px;" frameborder="0"></iframe>
HERE;

    $code = str_replace("%U%", $file, $code);
	$code = str_replace("%W%", $width, $code);
	$code = str_replace("%H%", $height, $code);
	if ($save == "1") {
	  $code .= "<p><a href=\"$file\" target=\"_blank\">download file</a></p>";
	}

    return $code;
}

add_shortcode('gview', 'gviewer_func');

?>
