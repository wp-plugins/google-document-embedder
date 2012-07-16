<?php
include_once('gde-functions.php');

global $gdeoptions;
$himg = plugins_url(plugin_basename(dirname(__FILE__))).'/img/help.png';

// check for debug
if (isset($_GET['debug'])) {
	$debug = true;
	echo gde_debug();
} else {
	$debug = false;
}

// get initial tbedit status (prevents FOUC)
if ($gdeoptions['disable_proxy'] == "no") {
	// enhanced viewer is selected, show opts by default
	$tbdisplay = "";
} else {
	// hide opts by default
	$tbdisplay = "display:none;";
}

if(isset($_REQUEST['defaults'])) {

	$set = gde_init('reset');
	$gdeoptions = get_option('gde_options');
	gde_showMessage(__('Options reset to defaults', 'gde'));

} elseif(isset($_REQUEST['submit'])) {

	// change user defaults
	if(isset($_POST['show_dl'])) {
		$gdeoptions['show_dl'] = "yes";
	} else {
		$gdeoptions['show_dl'] = "no";
	}
	if(isset($_POST['restrict_dl'])) {
		$gdeoptions['restrict_dl'] = "yes";
	} else {
		$gdeoptions['restrict_dl'] = "no";
	}
	if(isset($_POST['enable_ga'])) {
		$gdeoptions['enable_ga'] = "yes";
	} else {
		$gdeoptions['enable_ga'] = "no";
	}
	if(isset($_POST['default_width'])) {
		$neww = $_POST['default_width'];
		if (strlen($neww) > 0) $gdeoptions['default_width'] = gde_sanitizeOpt($neww, $_POST['width_type']);
	}
	if(isset($_POST['width_type'])) {
		$gdeoptions['width_type'] = $_POST['width_type'];
	}
	if(isset($_POST['default_height'])) {
		$newh = $_POST['default_height'];
		if (strlen($newh) > 0) $gdeoptions['default_height'] = gde_sanitizeOpt($newh, $_POST['height_type']);
	}
	if(isset($_POST['height_type'])) {
		$gdeoptions['height_type'] = $_POST['height_type'];
	}
	if(isset($_POST['default_lang'])) {
		$gdeoptions['default_lang'] = $_POST['default_lang'];
	}
	//if(isset($_POST['default_display'])) {
	//	$gdeoptions['default_display'] = $_POST['default_display'];
	//}
	
	// custom toolbar
	$newgdet = "";
	if(isset($_POST['gdet_z'])) { $newgdet .= "z"; }
	if(isset($_POST['gdet_n'])) { $newgdet .= "n"; }
	if(isset($_POST['gdet_m'])) { $newgdet .= "m"; }
	if(isset($newgdet)) {
		$gdeoptions['restrict_tb'] = $newgdet;
	}
	
	if(isset($_POST['base_url'])) {
		$gdeoptions['base_url'] = $_POST['base_url'];
	}
	if(isset($_POST['link_text'])) {
		$newt = $_POST['link_text'];
		if (strlen(utf8_decode($newt))) $gdeoptions['link_text'] = $newt;
	}
	if(isset($_POST['link_pos'])) {
		$gdeoptions['link_pos'] = $_POST['link_pos'];
	}
	if(isset($_POST['link_func'])) {
		$gdeoptions['link_func'] = $_POST['link_func'];
	}
	if(isset($_POST['disable_proxy'])) {
		$gdeoptions['disable_proxy'] = $_POST['disable_proxy'];
	}
	if(isset($_POST['disable_hideerrors'])) {
		$gdeoptions['disable_hideerrors'] = "yes";
	} else {
		$gdeoptions['disable_hideerrors'] = "no";
	}
	if(isset($_POST['disable_editor'])) {
		$gdeoptions['disable_editor'] = "yes";
	} else {
		$gdeoptions['disable_editor'] = "no";
	}
	if(isset($_POST['ed_extend_upload'])) {
		$gdeoptions['ed_extend_upload'] = "yes";
	} else {
		$gdeoptions['ed_extend_upload'] = "no";
	}
	if(isset($_POST['ed_embed_sc'])) {
		$gdeoptions['ed_embed_sc'] = "yes";
	} else {
		$gdeoptions['ed_embed_sc'] = "no";
	}
	if(isset($_POST['disable_caching'])) {
		$gdeoptions['disable_caching'] = "yes";
	} else {
		$gdeoptions['disable_caching'] = "no";
	}
	if(isset($_POST['bypass_check'])) {
		$gdeoptions['bypass_check'] = "yes";
	} else {
		$gdeoptions['bypass_check'] = "no";
	}
	if(isset($_POST['suppress_beta'])) {
		$gdeoptions['suppress_beta'] = "yes";
	} else {
		$gdeoptions['suppress_beta'] = "no";
	}
	
	update_option('gde_options', $gdeoptions);
	gde_showMessage(__('Options updated', 'gde'));
}

if (!$debug) {
	$event = ""; // init to avoid WP_DEBUG notices
?>

<div class="wrap">
<h2>Google Doc Embedder <?php _e('Settings', 'gde'); ?></h2>

<form action="" method="post">
<?php wp_nonce_field('update-options'); ?>

<div id="poststuff" class="metabox-holder">
	<div class="sm-padded" >
		<div id="post-body-content" class="has-sidebar-content">
			<div class="meta-box-sortabless">
				<div id="gde_vieweroptions" class="postbox">

				<h3 class="hndle"><span><?php _e('Viewer Options', 'gde'); ?></span></h3>
				<div class="inside">
				
				<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Viewer Selection', 'gde'); ?></th>
<td><div style="float:right;"><a href="<?php echo GDE_VIEWOPT_URL; ?>" target="_blank" title="<?php echo __('Help', 'gde'); ?>"><img src="<?php echo $himg; ?>"></a></div>
<?php
	gde_showRadio('yes', 'std-view', 'disable_proxy', __('Google Standard Viewer', 'gde'), $event); ?><br />
<em><?php echo __('Embed the standard Google Viewer.', 'gde'); ?></em><br/>
<?php gde_showRadio('no', 'enh-view', 'disable_proxy', __('Enhanced Viewer', 'gde'), $event); ?><br />
<em><?php echo __('Use this option to enable toolbar customization and fix some display problems (experimental).', 'gde'); ?></em><br/>
</td>
</tr>
<tr valign="top" id="tbedit" style="<?php echo $tbdisplay; ?>">
<th scope="row"><?php _e('Customize Toolbar', 'gde'); ?></th>
<td>
<?php //gde_showCheckTb('gdet_i', __('Google Logo', 'gde')); ?>
<?php //gde_showCheckTb('gdet_p', __('Single/Double Page View', 'gde')); ?>
<?php gde_showCheckTb('gdet_z', __('Hide Zoom In/Out', 'gde')); ?> &nbsp;&nbsp;
<?php gde_showCheckTb('gdet_n', __('Hide Open in New Window', 'gde')); ?> &nbsp;&nbsp;
<?php gde_showCheckTb('gdet_m', __('Always Use Mobile Theme', 'gde')); ?>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Default Size', 'gde'); ?></th>
<td><strong><?php echo __('Width', 'gde'); ?> </strong><input type="text" size="5" name="default_width" value="<?php echo $gdeoptions['default_width']; ?>" />  <select name="width_type" style="padding-right:2px;">
<?php gde_showOption('px', 'width_type', 'px'); ?>
<?php gde_showOption('pc', 'width_type', '%'); ?>
</select> &nbsp;&nbsp;&nbsp;&nbsp;
<strong><?php echo __('Height', 'gde'); ?> </strong><input type="text" size="5" name="default_height" value="<?php echo $gdeoptions['default_height']; ?>" /> <select name="height_type" style="padding-right:2px;">
<?php gde_showOption('px', 'height_type', 'px'); ?>
<?php gde_showOption('pc', 'height_type', '%'); ?>
</select></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Default Language', 'gde'); ?></th>
<td><select name="default_lang">

<?php gde_showOption('cs', 'default_lang', '&#268;esky'); ?>
<?php gde_showOption('sr', 'default_lang', '&#x0421;&#x0440;&#x043F;&#x0441;&#x043A;&#x0438;'); ?>
<?php gde_showOption('uk', 'default_lang', '&#x0423;&#x043A;&#x0440;&#x0430;&#x0457;&#x043D;&#x0441;&#x044C;&#x043A;&#x0430;'); ?>
<?php gde_showOption('el', 'default_lang', '&Epsilon;&lambda;&lambda;&eta;&nu;&iota;&kappa;ά'); ?>
<?php gde_showOption('ar', 'default_lang', 'Arabic'); ?>
<?php gde_showOption('in', 'default_lang', 'Bahasa Indonesia'); ?>
<?php gde_showOption('ca', 'default_lang', 'Catal&agrave;'); ?>
<?php gde_showOption('da', 'default_lang', 'Dansk'); ?>
<?php gde_showOption('de', 'default_lang', 'Deutsch'); ?>
<?php gde_showOption('en_GB', 'default_lang', 'English (UK)'); ?>
<?php gde_showOption('en_US', 'default_lang', 'English (US)'); ?>
<?php gde_showOption('es', 'default_lang', 'Espa&ntilde;ol'); ?>
<?php gde_showOption('fil', 'default_lang', 'Filipino'); ?>
<?php gde_showOption('fr', 'default_lang', 'Fran&ccedil;ais'); ?>
<?php gde_showOption('iw', 'default_lang', 'Hebrew'); ?>
<?php gde_showOption('hr', 'default_lang', 'Hrvatski'); ?>
<?php gde_showOption('it', 'default_lang', 'Italiano'); ?>
<?php gde_showOption('lv', 'default_lang', 'Latvie&scaron;u'); ?>
<?php gde_showOption('lt', 'default_lang', 'Lietuvių'); ?>
<?php gde_showOption('hu', 'default_lang', 'Magyar'); ?>
<?php gde_showOption('nl', 'default_lang', 'Nederlands'); ?>
<?php gde_showOption('no', 'default_lang', 'Norsk (bokmål)'); ?>
<?php gde_showOption('pl', 'default_lang', 'Polski'); ?>
<?php gde_showOption('pt_BR', 'default_lang', 'Portugu&ecirc;s (Brasil)'); ?>
<?php gde_showOption('pt_PT', 'default_lang', 'Portugu&ecirc;s (Portugal)'); ?>
<?php gde_showOption('ro', 'default_lang', 'Rom&acirc;n&#x0103;'); ?>
<?php gde_showOption('sl', 'default_lang', 'Sloven&#x0161;&#x010D;ina'); ?>
<?php gde_showOption('sk', 'default_lang', 'Slovensk&yacute;'); ?>
<?php gde_showOption('fi', 'default_lang', 'Suomi'); ?>
<?php gde_showOption('sv', 'default_lang', 'Svenska'); ?>
<?php gde_showOption('tr', 'default_lang', 'T&uuml;rk&ccedil;e'); ?>
<?php gde_showOption('vi', 'default_lang', 'Tiếng Việt'); ?>
<?php gde_showOption('ru', 'default_lang', 'Русский'); ?>
<?php gde_showOption('bg', 'default_lang', 'български'); ?>
<?php gde_showOption('mr', 'default_lang', 'मराठी'); ?>
<?php gde_showOption('hi', 'default_lang', 'हिन्दी'); ?>
<?php gde_showOption('bn', 'default_lang', 'বাংলা'); ?>
<?php gde_showOption('gu', 'default_lang', 'ગુજરાતી'); ?>
<?php gde_showOption('or', 'default_lang', 'ଓଡିଆ'); ?>
<?php gde_showOption('ta', 'default_lang', 'தமிழ்'); ?>
<?php gde_showOption('te', 'default_lang', 'తెలుగు'); ?>
<?php gde_showOption('kn', 'default_lang', 'ಕನ್ನಡ'); ?>
<?php gde_showOption('ml', 'default_lang', 'മലയാളം'); ?>
<?php gde_showOption('th', 'default_lang', 'ภาษาไทย'); ?>
<?php gde_showOption('zh_CN', 'default_lang', '中文（简体）'); ?>
<?php gde_showOption('zh_TW', 'default_lang', '中文（繁體）'); ?>
<?php gde_showOption('ja', 'default_lang', '日本語'); ?>
<?php gde_showOption('ko', 'default_lang', '한국어'); ?>

</select></td>
</tr>
<!--tr valign="top">
<th scope="row">Default Viewer Display</th>
<td><select name="default_display">

<?php gde_showOption('inline', 'default_display', __('Inline (Default)', 'gde')); ?>
<?php gde_showOption('inline-open', 'default_display', __('Collapsible (Open)', 'gde')); ?>
<?php gde_showOption('inline-close', 'default_display', __('Collapsible (Closed)', 'gde')); ?>

</select></td>
</tr-->
</table>
				</div>
				</div>
				</div>


<div id="gde_linkoptions" class="postbox">

				<h3 class="hndle"><span><?php _e('Download Link Options', 'gde'); ?></span></h3>
				<div class="inside">
<table class="form-table">
<tr valign="top">
<td colspan="2"><div style="float:right;"><a href="<?php echo GDE_LINKOPT_URL; ?>" target="_blank" title="<?php echo __('Help', 'gde'); ?>"><img src="<?php echo $himg; ?>"></a></div>
<?php gde_showCheck('show_dl', __('Display the download link by default', 'gde')); ?><br/>
<?php gde_showCheck('restrict_dl', __('Only display download link to logged in users', 'gde')); ?><br/>
<?php gde_showCheck('enable_ga', __('Track downloads in Google Analytics (tracking script must be installed on your site)', 'gde')); ?></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('File Base URL', 'gde'); ?></th>
<td><input type="text" size="50" name="base_url" value="<?php echo $gdeoptions['base_url']; ?>" /><br/>
<?php echo __('Any file not starting with <em>http</em> will be prefixed by this value', 'gde'); ?></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Link Text', 'gde'); ?></th>
<td><input type="text" size="50" name="link_text" value="<?php echo $gdeoptions['link_text']; ?>" /><br/>
<em><?php echo __('You can further customize text using these dynamic replacements:', 'gde'); ?></em><br/>
<code>%FN</code> : <?php echo __('filename', 'gde'); ?> &nbsp;&nbsp;&nbsp;
<code>%FT</code> : <?php echo __('file type', 'gde'); ?> &nbsp;&nbsp;&nbsp;
<code>%FS</code> : <?php echo __('file size', 'gde'); ?></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Link Position', 'gde'); ?></th>
<td><select name="link_pos">
<?php gde_showOption('above', 'link_pos', __('Above Viewer', 'gde')); ?>
<?php gde_showOption('below', 'link_pos', __('Below Viewer', 'gde')); ?>
</select>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Link Behavior', 'gde'); ?></th>
<td><select name="link_func">
<?php gde_showOption('default', 'link_func', __('Browser Default', 'gde')); ?>
<?php gde_showOption('force', 'link_func', __('Force Download', 'gde')); ?>
<?php gde_showOption('force-mask', 'link_func', __('Force Download (Mask URL)', 'gde')); ?>
</select>
</td>
</tr>
<tr valign="top">
<td colspan="2">
</div></td>
</tr>
</tr>
</table>
				</div>
			</div>

		<div id="gde_linkoptions" class="postbox">
			<h3 class="hndle"><span><?php _e('Advanced Options', 'gde'); ?></span></h3>
			<div class="inside" style="min-height:35px;">
				<div style="float:left;"><a href="javascript:void(0);" id="advopt-plugin"><?php echo __('Plugin Behavior', 'gde'); ?></a><br/> 
				<a href="javascript:void(0);" id="advopt-editor"><?php echo __('Editor Behavior', 'gde'); ?></a>
				</div>
				<div style="float:right;"><a href="<?php echo GDE_ADVOPT_URL; ?>" target="_blank" title="<?php echo __('Help', 'gde'); ?>"><img src="<?php echo $himg; ?>"></a></div>
				<div id="adv-plugin" style="display:none;padding-left: 250px;">
					<?php gde_showCheck('disable_hideerrors', __('Display error messages inline (not hidden)', 'gde')); ?><br />
					<?php gde_showCheck('bypass_check', __('Disable internal error checking', 'gde')); ?><br />
					<?php gde_showCheck('disable_caching', __('Disable document caching', 'gde')); ?><br />
					<?php gde_showCheck('suppress_beta', __('Disable beta version notifications', 'gde')); ?>
				</div>
				<div id="adv-editor" style="display:none;padding-left:250px;">
					<?php gde_showCheck('disable_editor', __('Disable all editor integration', 'gde')); ?><br />
					<?php
						if ($gdeoptions['disable_editor'] == "yes") {
							$disabled = "true";
						} else { $disabled = "false"; }
						gde_showCheck('ed_embed_sc', __('Insert shortcode from Media Library', 'gde'), null, $disabled);
					?><br />
					<?php gde_showCheck('ed_extend_upload', __('Allow uploads of all supported media types', 'gde'), null, $disabled); ?>
				</div>
			</div>
		</div>
			</div>
		</div>
	</div>

<p class="submit" style="padding:0 10px;">
<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<input class="button-primary" type="submit" name="submit" value="<?php _e('Save Options', 'gde') ?>" />
&nbsp;&nbsp;&nbsp;
<input class="button-secondary" type="submit" name="defaults" value="<?php _e('Reset to Defaults', 'gde') ?>" onClick="javascript:return confirm('<?php echo __('Are you sure you want to reset all settings to defaults?', 'gde'); ?>')" />
</p>

</form>

</div>

<?php
} // end if not debug

function gde_showRadio($value, $id, $option, $title, $event = NULL) {
	global $gdeoptions;
	if ($gdeoptions[$option] == $value) { $chk = ' checked="checked"'; } else { $chk = ""; }
?>
<input type="radio" name="<?php echo $option; ?>" value="<?php echo $value; ?>" id="<?php echo $id; ?>"<?php echo $chk; echo $event; ?> />
<label for="<?php echo $id; ?>"><strong><?php echo $title; ?></strong></label>
<?php
}

function gde_showCheck($option, $title, $link = NULL, $disable = false) {
	global $gdeoptions;
	if ($gdeoptions[$option] == "yes") { $chk = ' checked="checked"'; } else { $chk = ""; }
	if ($disable == "true") { $dis = ' disabled="true"'; } else { $dis = ""; }
?>
<input type="checkbox" name="<?php echo $option; ?>" value="1" id="<?php echo $option; ?>"<?php echo $chk; ?><?php echo $dis; ?> />
<label for="<?php echo $option; ?>"><?php echo $title; ?></label>
<?php
	if ($link) {
		echo ' (<a href="'.$link.'" target="_blank">info</a>)';
	}
}

function gde_showCheckTb($option, $title) {
	global $gdeoptions;
	$gdet = $gdeoptions['restrict_tb'];
	
	$option = str_replace("gdet_", "", $option);
	
	if (strstr($gdet, $option) !== false) { $chk = ' checked="checked"'; } else { $chk = ""; }
?>
<input type="checkbox" name="gdet_<?php echo $option; ?>" value="1" id="gdet_<?php echo $option; ?>"<?php echo $chk; ?> />
<label for="gdet_<?php echo $option; ?>"><?php echo $title; ?></label>
<?php
}

function gde_showOption($value, $option, $title) {
	global $gdeoptions;
	if ($gdeoptions[$option] == $value) { $chk = ' selected="yes"'; } else { $chk = ""; }
?>
<option style="padding-right:5px;" value="<?php echo $value; ?>"<?php echo $chk; ?>><?php echo $title; ?></option>
<?php
}

function gde_showMessage($message, $type='updated') {
	if($type == 'updated') $class = 'updated fade';
	elseif($type == 'error') $class = 'updated error';
	else $class = $type;
	
	print '<div id="message" class="'.$class.'"><p>' . $message . '</p></div>';
}

?>