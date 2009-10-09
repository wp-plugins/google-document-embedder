<?php
include_once('functions.php');

// perform cleanup, be a good citizen

$options = getDefaults();
foreach ($options as $opt => $val) {
	delete_option($opt);
}

// remove legacy options if present

$legacy_options = array(
	"gde_xlogo" => 0,
	"gde_xfull" => 0,
	"gde_xpgup" => 0,
	"gde_xzoom" => 0
);
foreach ($legacy_options as $lopt => $val) {
	delete_option($lopt);
}

?>