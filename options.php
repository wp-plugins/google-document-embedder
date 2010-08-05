<?php
include_once('functions.php');

global $gdeoptions;
$himg = plugins_url(plugin_basename(dirname(__FILE__))).'/help.png';

if(isset($_REQUEST['defaults'])) {

	$set = gde_init('reset');
	$gdeoptions = get_option('gde_options');
	gde_showMessage("Options reset to defaults");

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
	if(isset($_POST['gdet_i'])) { $newgdet .= "i"; }
	if(isset($_POST['gdet_p'])) { $newgdet .= "p"; }
	if(isset($_POST['gdet_z'])) { $newgdet .= "z"; }
	if(isset($_POST['gdet_n'])) { $newgdet .= "n"; }
	$gdeoptions['restrict_tb'] = $newgdet;
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
	if(isset($_POST['bypass_check'])) {
		$gdeoptions['bypass_check'] = "yes";
	} else {
		$gdeoptions['bypass_check'] = "no";
	}
	if(isset($_POST['ignore_conflicts'])) {
		$gdeoptions['ignore_conflicts'] = "yes";
	} else {
		$gdeoptions['ignore_conflicts'] = "no";
	}
	if(isset($_POST['suppress_beta'])) {
		$gdeoptions['suppress_beta'] = "yes";
	} else {
		$gdeoptions['suppress_beta'] = "no";
	}
	
	update_option('gde_options', $gdeoptions);
	gde_showMessage("Options updated");
}
?>
<div class="wrap">
<?php
echo "<h2>".__('Google Doc Embedder Settings')."</h2>";
?>

<form action="" method="post">
<?php wp_nonce_field('update-options'); ?>

<div id="poststuff" class="metabox-holder">
	<div class="sm-padded" >
		<div id="post-body-content" class="has-sidebar-content">
			<div class="meta-box-sortabless">
				<div id="gde_vieweroptions" class="postbox">

				<h3 class="hndle"><span>Viewer Options</span></h3>
				<div class="inside">
				
				<table class="form-table">
<tr valign="top">
<th scope="row">Viewer Selection</th>
<td><div style="float:right;"><a href="<?php echo GDE_VIEWOPT_URL; ?>" target="_blank" title="Help"><img src="<?php echo $himg; ?>"></a></div><?php gde_showRadio('no', 'dp1', 'disable_proxy', gde_t('Enhanced Viewer')); ?><br />
<em>Use this option to improve IE compatibility and enable toolbar customization.</em><br/>
<?php gde_showRadio('yes', 'dp2', 'disable_proxy', gde_t('Google Standard Viewer')); ?><br />
<em>Use this option if you experience problems with the Enhanced Viewer.</em>
</td>
</tr>
<tr valign="top">
<th scope="row">Default Size</th>
<td><strong>Width </strong><input type="text" size="5" name="default_width" value="<?php echo $gdeoptions['default_width']; ?>" />  <select name="width_type">
<?php gde_showOption('px', 'width_type', gde_t('px')); ?>
<?php gde_showOption('pc', 'width_type', gde_t('%')); ?>
</select> &nbsp;&nbsp;&nbsp;&nbsp;
<strong>Height </strong><input type="text" size="5" name="default_height" value="<?php echo $gdeoptions['default_height']; ?>" /> <select name="height_type">
<?php gde_showOption('px', 'height_type', gde_t('px')); ?>
<?php gde_showOption('pc', 'height_type', gde_t('%')); ?>
</select></td>
</tr>
<tr valign="top">
<th scope="row">Default Language</th>
<td><select name="default_lang">

<?php gde_showOption('cs', 'default_lang', gde_t('&#268;esky')); ?>
<?php gde_showOption('sr', 'default_lang', gde_t('&#x0421;&#x0440;&#x043F;&#x0441;&#x043A;&#x0438;')); ?>
<?php gde_showOption('uk', 'default_lang', gde_t('&#x0423;&#x043A;&#x0440;&#x0430;&#x0457;&#x043D;&#x0441;&#x044C;&#x043A;&#x0430;')); ?>
<?php gde_showOption('el', 'default_lang', gde_t('&Epsilon;&lambda;&lambda;&eta;&nu;&iota;&kappa;ά')); ?>
<?php gde_showOption('ar', 'default_lang', gde_t('Arabic')); ?>
<?php gde_showOption('in', 'default_lang', gde_t('Bahasa Indonesia')); ?>
<?php gde_showOption('ca', 'default_lang', gde_t('Catal&agrave;')); ?>
<?php gde_showOption('da', 'default_lang', gde_t('Dansk')); ?>
<?php gde_showOption('de', 'default_lang', gde_t('Deutsch')); ?>
<?php gde_showOption('en_GB', 'default_lang', gde_t('English (UK)')); ?>
<?php gde_showOption('en_US', 'default_lang', gde_t('English (US)')); ?>
<?php gde_showOption('es', 'default_lang', gde_t('Espa&ntilde;ol')); ?>
<?php gde_showOption('fil', 'default_lang', gde_t('Filipino')); ?>
<?php gde_showOption('fr', 'default_lang', gde_t('Fran&ccedil;ais')); ?>
<?php gde_showOption('iw', 'default_lang', gde_t('Hebrew')); ?>
<?php gde_showOption('hr', 'default_lang', gde_t('Hrvatski')); ?>
<?php gde_showOption('it', 'default_lang', gde_t('Italiano')); ?>
<?php gde_showOption('lv', 'default_lang', gde_t('Latvie&scaron;u')); ?>
<?php gde_showOption('lt', 'default_lang', gde_t('Lietuvių')); ?>
<?php gde_showOption('hu', 'default_lang', gde_t('Magyar')); ?>
<?php gde_showOption('nl', 'default_lang', gde_t('Nederlands')); ?>
<?php gde_showOption('no', 'default_lang', gde_t('Norsk (bokmål)')); ?>
<?php gde_showOption('pl', 'default_lang', gde_t('Polski')); ?>
<?php gde_showOption('pt_BR', 'default_lang', gde_t('Portugu&ecirc;s (Brasil)')); ?>
<?php gde_showOption('pt_PT', 'default_lang', gde_t('Portugu&ecirc;s (Portugal)')); ?>
<?php gde_showOption('ro', 'default_lang', gde_t('Rom&acirc;n&#x0103;')); ?>
<?php gde_showOption('sl', 'default_lang', gde_t('Sloven&#x0161;&#x010D;ina')); ?>
<?php gde_showOption('sk', 'default_lang', gde_t('Slovensk&yacute;')); ?>
<?php gde_showOption('fi', 'default_lang', gde_t('Suomi')); ?>
<?php gde_showOption('sv', 'default_lang', gde_t('Svenska')); ?>
<?php gde_showOption('tr', 'default_lang', gde_t('T&uuml;rk&ccedil;e')); ?>
<?php gde_showOption('vi', 'default_lang', gde_t('Tiếng Việt')); ?>
<?php gde_showOption('ru', 'default_lang', gde_t('Русский')); ?>
<?php gde_showOption('bg', 'default_lang', gde_t('български')); ?>
<?php gde_showOption('mr', 'default_lang', gde_t('मराठी')); ?>
<?php gde_showOption('hi', 'default_lang', gde_t('हिन्दी')); ?>
<?php gde_showOption('bn', 'default_lang', gde_t('বাংলা')); ?>
<?php gde_showOption('gu', 'default_lang', gde_t('ગુજરાતી')); ?>
<?php gde_showOption('or', 'default_lang', gde_t('ଓଡିଆ')); ?>
<?php gde_showOption('ta', 'default_lang', gde_t('தமிழ்')); ?>
<?php gde_showOption('te', 'default_lang', gde_t('తెలుగు')); ?>
<?php gde_showOption('kn', 'default_lang', gde_t('ಕನ್ನಡ')); ?>
<?php gde_showOption('ml', 'default_lang', gde_t('മലയാളം')); ?>
<?php gde_showOption('th', 'default_lang', gde_t('ภาษาไทย')); ?>
<?php gde_showOption('zh_CN', 'default_lang', gde_t('中文（简体）')); ?>
<?php gde_showOption('zh_TW', 'default_lang', gde_t('中文（繁體）')); ?>
<?php gde_showOption('ja', 'default_lang', gde_t('日本語')); ?>
<?php gde_showOption('ko', 'default_lang', gde_t('한국어')); ?>

</select></td>
</tr>
<?php
if ($gdeoptions['disable_proxy'] == "no") {
?>
<tr valign="top">
<th scope="row">Hide Toolbar Buttons</th>
<td><?php gde_showCheckTb('gdet_i', gde_t('Google Logo')); ?>
<?php gde_showCheckTb('gdet_p', gde_t('Single/Double Page View')); ?>
<?php gde_showCheckTb('gdet_z', gde_t('Zoom In/Out')); ?>
<?php gde_showCheckTb('gdet_n', gde_t('Open in New Window')); ?>
</td>
</tr>
<?php } ?>
</table>
				
				</div>
				</div>
				</div>


<div id="gde_linkoptions" class="postbox">

				<h3 class="hndle"><span>Download Link Options</span></h3>
				<div class="inside">
<table class="form-table">
<tr valign="top">
<td colspan="2"><div style="float:right;"><a href="<?php echo GDE_LINKOPT_URL; ?>" target="_blank" title="Help"><img src="<?php echo $himg; ?>"></a></div><?php gde_showCheck('show_dl', gde_t('Display the download link by default')); ?><br/>
<?php gde_showCheck('restrict_dl', gde_t('Only display download link to logged in users')); ?></td>
</tr>
<tr valign="top">
<th scope="row">Link Text</th>
<td><input type="text" size="50" name="link_text" value="<?php echo $gdeoptions['link_text']; ?>" /><br/>
<em>You can further customize text using these dynamic replacements:</em><br/>
<code>%FN</code> : filename &nbsp;&nbsp;&nbsp;
<code>%FT</code> : file type &nbsp;&nbsp;&nbsp;
<code>%FS</code> : file size</td>
</tr>
<tr valign="top">
<th scope="row">Link Position</th>
<td><select name="link_pos">
<?php gde_showOption('above', 'link_pos', gde_t('Above Viewer')); ?>
<?php gde_showOption('below', 'link_pos', gde_t('Below Viewer')); ?>
</select>
</td>
</tr>
<r valign="top">
<th scope="row">Link Behavior</th>
<td><select name="link_func">
<?php gde_showOption('default', 'link_func', gde_t('Browser Default')); ?>
<?php gde_showOption('force', 'link_func', gde_t('Force Download')); ?>
<?php gde_showOption('force-mask', 'link_func', gde_t('Force Download (Mask URL)')); ?>
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
			</div>
		</div>
	</div>
				
<p class="submit" style="padding-bottom:10px;">
<div style="font-size:11px;margin-top:-40px;padding-bottom:20px;padding-left:5px;">
<strong><a style="text-decoration:none;" href="javascript:;" onmousedown="if(document.getElementById('advopt').style.display == 'none'){ document.getElementById('advopt').style.display = 'block'; }else{ document.getElementById('advopt').style.display = 'none'; }">[ + ]</a> Advanced Options</strong> <a href="<?php echo GDE_ADVOPT_URL; ?>" target="_blank"><img src="<?php echo $himg; ?>"></a><br />
<div id="advopt" style="display:none;">
<?php gde_showCheck('bypass_check', gde_t('Disable internal error checking')); ?><br />
<?php gde_showCheck('ignore_conflicts', gde_t('Disable plugin conflict warnings')); ?><br />
<?php gde_showCheck('suppress_beta', gde_t('Disable beta version notifications')); ?>
</div>
</div>

<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<span id="autosave"></span>
<input class="button-primary" type="submit" name="submit" value="<?php gde_e('Save Options') ?>" />
&nbsp;&nbsp;&nbsp;
<input class="button-secondary" type="submit" name="defaults" value="<?php gde_e('Reset to Defaults') ?>" onClick="javascript:return confirm('Are you sure you want to reset all settings to defaults?')" />
</p>

</form>

</div>

<?php

function gde_showRadio($value, $id, $option, $title) {
	global $gdeoptions;
	if ($gdeoptions[$option] == $value) { $chk = ' checked="checked"'; }
?>
<input type="radio" name="<?php echo $option; ?>" value="<?php echo $value; ?>" id="<?php echo $id; ?>"<?php echo $chk; ?> />
<label for="<?php echo $id; ?>"><strong><?php gde_e($title) ?></strong></label>
<?php
}

function gde_showCheck($option, $title, $link = NULL) {
	global $gdeoptions;
	if ($gdeoptions[$option] == "yes") { $chk = ' checked="checked"'; }
?>
<input type="checkbox" name="<?php echo $option; ?>" value="1" id="<?php echo $option; ?>"<?php echo $chk; ?> />
<label for="<?php echo $option; ?>"><?php gde_e($title) ?></label>
<?php
	if ($link) {
		echo ' (<a href="'.$link.'" target="_blank">info</a>)';
	}
}

function gde_showCheckTb($option, $title) {
	global $gdeoptions;
	$gdet = $gdeoptions['restrict_tb'];
	
	$option = str_replace("gdet_", "", $option);
	
	if (strstr($gdet, $option) !== false) { $chk = ' checked="checked"'; }
?>
<input type="checkbox" name="gdet_<?php echo $option; ?>" value="1" id="gdet_<?php echo $option; ?>"<?php echo $chk; ?> />
<label for="gdet_<?php echo $option; ?>"><?php gde_e($title) ?></label>
<?php
}

function gde_showOption($value, $option, $title) {
	global $gdeoptions;
	if ($gdeoptions[$option] == $value) { $chk = ' selected="yes"'; }
?>
<option value="<?php echo $value; ?>"<?php echo $chk; ?>><?php echo $title; ?></option>
<?php
}

function gde_showMessage($message, $type='updated') {
	if($type == 'updated') $class = 'updated fade';
	elseif($type == 'error') $class = 'updated error';
	else $class = $type;
	
	print '<div id="message" class="'.$class.'"><p>' . __($message, basename(dirname(__FILE__))) . '</p></div>';
}

?>