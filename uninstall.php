<?php

// perform cleanup, be a good citizen

$options = array('gde_default_width','gde_default_height','gde_show_dl','gde_link_text');

foreach ($options as $opt) {
	delete_option($opt);
}

?>