<?php
include_once('functions.php');

// perform cleanup, be a good citizen

$options = getDefaults();
foreach ($options as $opt => $val) {
	delete_option($opt);
}

// remove deprecated options if present

$legacy_options = getObsolete();
foreach ($legacy_options as $lopt => $val) {
	delete_option($lopt);
}

?>