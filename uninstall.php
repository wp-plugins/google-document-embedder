<?php
include_once('functions.php');

// perform cleanup, be a good citizen

$options = getDefaults();
foreach ($options as $opt => $val) {
	delete_option($opt);
}

?>