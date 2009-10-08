<?php

$str = $_SERVER['QUERY_STRING'];
parse_str($str);

if (!$src = curl_get_file_contents($loc)) {
  echo "<html><body>\n\n<!-- GDE EMBED ERROR: alt viewer failed to load -->\n\n</body></html>";
  exit;
}

$src = str_replace('<head>','<head><base href="http://docs.google.com/gview" />', $src);

if ($logo == "no") {
  $src = str_replace('.goog-logo-small {','.goog-logo-small { display: none !important;', $src);
}
if ($full == "no") {
  $src = str_replace('.controlbar-open-in-viewer-image {','.controlbar-open-in-viewer-image { display: none !important;', $src);
}
if ($pgup == "no") {
  $src = str_replace('.controlbar-two-up-image {','.controlbar-two-up-image { display: none !important;', $src);
  $src = str_replace('.controlbar-one-up-image {','.controlbar-one-up-image { display: none !important;', $src);
}
if ($zoom == "no") {
  $src = str_replace('.controlbar-minus-image {','.controlbar-minus-image { display: none !important;', $src);
  $src = str_replace('.controlbar-plus-image {','.controlbar-plus-image { display: none !important;', $src);
}

echo $src;

function curl_get_file_contents($URL)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) return $contents;
            else return FALSE;
    }
?>