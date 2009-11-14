<?php
include_once('wpframe.php');
include_once('functions.php');

$ie8_link = GDE_IE8_URL;
global $options;

if(isset($_REQUEST['defaults'])) {

	$set = gde_init('reset');
	$options = get_option('gde_options');
	showMessage("Options reset to defaults");

} elseif(isset($_REQUEST['submit'])) {

	// change user defaults
	if(isset($_POST['show_dl'])) {
		$options['show_dl'] = "yes";
	} else {
		$options['show_dl'] = "no";
	}
	if(isset($_POST['default_width'])) {
		$neww = $_POST['default_width'];
		if (strlen($neww) > 0) $options['default_width'] = gde_sanitizeOpt($neww, $_POST['width_type']);
	}
	if(isset($_POST['width_type'])) {
		$options['width_type'] = $_POST['width_type'];
	}
	if(isset($_POST['default_height'])) {
		$newh = $_POST['default_height'];
		if (strlen($newh) > 0) $options['default_height'] = gde_sanitizeOpt($newh, $_POST['height_type']);
	}
	if(isset($_POST['height_type'])) {
		$options['height_type'] = $_POST['height_type'];
	}
	if(isset($_POST['link_text'])) {
		$newt = $_POST['link_text'];
		if (strlen(utf8_decode($newt))) $options['link_text'] = $newt;
	}
	if(isset($_POST['link_pos'])) {
		$options['link_pos'] = $_POST['link_pos'];
	}
	if(isset($_POST['link_func'])) {
		$options['link_func'] = $_POST['link_func'];
	}
	if(isset($_POST['ie8_warn'])) {
		$options['ie8_warn'] = "yes";
	} else {
		$options['ie8_warn'] = "no";
	}
	if(isset($_POST['bypass_check'])) {
		$options['bypass_check'] = "yes";
	} else {
		$options['bypass_check'] = "no";
	}
	if(isset($_POST['ignore_conflicts'])) {
		$options['ignore_conflicts'] = "yes";
	} else {
		$options['ignore_conflicts'] = "no";
	}
	
	update_option('gde_options', $options);
	showMessage("Options updated");
}
?>
<div class="wrap">
<?php
echo "<h2>".__('Google Doc Embedder Settings')."</h2>";
?>

<form action="" method="post">
<?php wp_nonce_field('update-options'); ?>

<table class="form-table">
<tr valign="top">
<td colspan="2"><strong>Global Viewer Options</strong><br/>
To override size on individual posts, manually set in the post shortcode using (for example) <code>height="400"</code> (px) or <code>width="80%"</code>.</td>
</tr>
<tr valign="top">
<th scope="row">Default Width</th>
<td><input type="text" size="5" name="default_width" value="<?php echo $options['default_width']; ?>" /> <select name="width_type">
<?php gde_showOption('px', 'width_type', t('px')); ?>
<?php gde_showOption('pc', 'width_type', t('%')); ?>
</select></td>
</tr>
<tr valign="top">
<th scope="row">Default Height</th>
<td><input type="text" size="5" name="default_height" value="<?php echo $options['default_height']; ?>" /> <select name="height_type">
<?php gde_showOption('px', 'height_type', t('px')); ?>
<?php gde_showOption('pc', 'height_type', t('%')); ?>
</select></td>
</tr>
<tr valign="top">
<td colspan="2"><strong>Download Link Options</strong><br/>
To override display setting on an individual post, use <code>save="1"</code> (show) or <code>save="0"</code> (hide) in the post shortcode.</em></td>
</tr>
<tr valign="top">
<td colspan="2"><?php gde_showCheck('show_dl', t('Display the download link by default')); ?></td>
</tr>
<tr valign="top">
<th scope="row">Link Text</th>
<td><input type="text" size="50" name="link_text" value="<?php echo $options['link_text']; ?>" /><br/>
<em>You can further customize text using these dynamic replacements:</em><br/>
<code>%FN</code> : filename &nbsp;&nbsp;&nbsp;
<code>%FT</code> : file type &nbsp;&nbsp;&nbsp;
<code>%FS</code> : file size</td>
</tr>
<tr valign="top">
<th scope="row">Link Position</th>
<td><select name="link_pos">
<?php gde_showOption('above', 'link_pos', t('Above Viewer')); ?>
<?php gde_showOption('below', 'link_pos', t('Below Viewer')); ?>
</select>
</td>
</tr>
<r valign="top">
<th scope="row">Link Behavior</th>
<td><select name="link_func">
<?php gde_showOption('default', 'link_func', t('Browser Default')); ?>
<?php gde_showOption('force', 'link_func', t('Force Download')); ?>
<?php gde_showOption('force-mask', 'link_func', t('Force Download (Mask URL)')); ?>
</select>
</td>
</tr>
<tr valign="top">
<td colspan="2"><strong><a style="text-decoration:none;" href="javascript:;" onmousedown="if(document.getElementById('advopt').style.display == 'none'){ document.getElementById('advopt').style.display = 'block'; }else{ document.getElementById('advopt').style.display = 'none'; }">[ + ]</a> Advanced Options</strong><br />
<div id="advopt" style="display:none;">
<?php gde_showCheck('ie8_warn', t('Show help message to IE8 users (<a href="'.$ie8_link.'" target="_blank">more info</a>)')); ?><br />
<?php gde_showCheck('bypass_check', t('Let Google Doc Viewer handle all errors (for individual files, use <code>force="1"</code>)')); ?><br />
<?php gde_showCheck('ignore_conflicts', t('Turn off plugin conflict warnings')); ?>
</div></td>
</tr>
</tr>
</table>
<p class="submit" style="padding-bottom:0;">
<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<span id="autosave"></span>
<input class="button-primary" type="submit" name="submit" value="<?php e('Save Options') ?>" />
&nbsp;&nbsp;&nbsp;
<input class="button-secondary" type="submit" name="defaults" value="<?php e('Reset to Defaults') ?>" onClick="javascript:return confirm('Are you sure you want to reset all settings to defaults?')" />
</p>

</form>

</div>

<?php
function gde_showCheck($option, $title) {
	global $options;
	if ($options[$option] == "yes") { $chk = ' checked="checked"'; }
?>
<input type="checkbox" name="<?php echo $option; ?>" value="1" id="<?php echo $option; ?>"<?php echo $chk; ?> />
<label for="<?php echo $option; ?>"><?php e($title) ?></label>
<?php
}

function gde_showOption($value, $option, $title) {
	global $options;
	if ($options[$option] == $value) { $chk = ' selected="yes"'; }
?>
<option value="<?php echo $value; ?>"<?php echo $chk; ?>><?php echo $title; ?></option>
<?php
}
?>