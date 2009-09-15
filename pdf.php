<?php

/* Calls remote PDF file locally (automates remote fetch)

  USAGE: pdf.php?
                 file=file.pdf (the name of the remote file, required)
                 download=1 (force save dialog rather than display in browser, optional)
*/

$str = $_SERVER['QUERY_STRING'];
parse_str($str);

if (!$file) {
  echo "<html>No filename specified, action cancelled.</html>\n";
  exit;
}  elseif (!preg_match("/.pdf$/i",$file)) {
  # invalid filename or hack attempt
  echo "<html>Invalid filename, action cancelled.</html>\n";
  exit;
}

$file = "http://".$file;
$handle = fopen("$file", "rb");

if (!$handle) {
  # file doesn't exist
  echo "<html>Nonexistant filename, action cancelled.</html>\n";
  fclose($handle);
  exit;
}

$contents = '';
while (!feof($handle)) {
  $contents .= fread($handle, 8192);
}
fclose($handle);


// We'll be outputting a PDF
header('Content-type: application/pdf');

// force save dialog with given name
if ($dl) {
  header('Content-Disposition: attachment; filename="'.$fn.'"');
}

// spit it out
echo $contents;
?>