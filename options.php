<?php
include_once('wpframe.php');
include_once('functions.php');

if(isset($_REQUEST['defaults'])) {

	// reset to plug-in defaults
	$set = getDefaults();
	update_option('gde_default_width', $set['gde_default_width']);
	update_option('gde_default_height', $set['gde_default_height']);
	update_option('gde_show_dl', $set['gde_show_dl']);
	update_option('gde_link_text', $set['gde_link_text']);
	update_option('gde_link_pos', $set['gde_link_pos']);
	update_option('gde_link_func', $set['gde_link_func']);
	
	showMessage("Options reset to defaults");
} elseif(isset($_REQUEST['submit'])) {

	// change user defaults
	if(isset($_POST['gde_show_dl'])) {
		update_option('gde_show_dl', 1);
	} else {
		update_option('gde_show_dl', 0);
	}
	if(isset($_POST['gde_default_width'])) {
		$neww = $_POST['gde_default_width'];
		if (strlen($neww) > 0) update_option('gde_default_width', sanitizeOpt($neww));
	}
	if(isset($_POST['gde_default_height'])) {
		$newh = $_POST['gde_default_height'];
		if (strlen($newh) > 0) update_option('gde_default_height', sanitizeOpt($newh));
	}
	if(isset($_POST['gde_link_text'])) {
		$newt = $_POST['gde_link_text'];
		if (strlen(utf8_decode($newt))) update_option('gde_link_text', $newt);
	}
	if(isset($_POST['gde_link_pos'])) {
		update_option('gde_link_pos', $_POST['gde_link_pos']);
	}
	if(isset($_POST['gde_link_func'])) {
		update_option('gde_link_func', $_POST['gde_link_func']);
	}
	
	showMessage("Options updated");
}
?>
<div class="wrap">
<h2>Google Doc Embedder Settings</h2>

<form action="" method="post">
<?php wp_nonce_field('update-options'); ?>

<table class="form-table">
<tr valign="top">
<td colspan="2"><strong>Global Viewer Size</strong><br/>
To override on individual posts, manually set <code>height=</code> and <code>width=</code> (number in px) in the post shortcode.</td>
</tr>
<tr valign="top">
<th scope="row">Default Width</th>
<td><input type="text" size="5" name="gde_default_width" value="<?php echo get_option('gde_default_width'); ?>" /> px</td>
</tr>
<tr valign="top">
<th scope="row">Default Height</th>
<td><input type="text" size="5" name="gde_default_height" value="<?php echo get_option('gde_default_height'); ?>" /> px</td>
</tr>
<tr valign="top">
<td colspan="2"><strong>Download Link Options</strong><br/>
To override display setting on an individual post, use <code>save="1"</code> (show) or <code>save="0"</code> (hide) in the post shortcode.</em></td>
</tr>
<tr valign="top">
<td colspan="2"><?php showCheck('gde_show_dl', t('Display the download link by default')); ?></td>
</tr>
<tr valign="top">
<th scope="row">Link Text</th>
<td><input type="text" size="50" name="gde_link_text" value="<?php echo get_option('gde_link_text'); ?>" /><br/>
<em>You can further customize text using these dynamic replacements:</em><br/>
<code>%FN</code> : filename &nbsp;&nbsp;&nbsp;
<code>%FT</code> : file type &nbsp;&nbsp;&nbsp;
<code>%FS</code> : file size</td>
</tr>
<tr valign="top">
<th scope="row">Link Position</th>
<td><select name="gde_link_pos">
<?php showOption('above', 'gde_link_pos', t('Above Viewer')); ?>
<?php showOption('below', 'gde_link_pos', t('Below Viewer')); ?>
</select>
</td>
</tr>
<r valign="top">
<th scope="row">Link Behavior</th>
<td><select name="gde_link_func">
<?php showOption('default', 'gde_link_func', t('Browser Default')); ?>
<?php showOption('force', 'gde_link_func', t('Force Download')); ?>
<?php showOption('force-mask', 'gde_link_func', t('Force Download (Mask URL)')); ?>
</select>
</td>
</tr>
</table>
<p class="submit">
<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<span id="autosave"></span>
<input type="submit" name="submit" value="<?php e('Save Options') ?>" />
&nbsp;&nbsp;&nbsp;
<input type="submit" name="defaults" value="<?php e('Reset to Defaults') ?>" onClick="javascript:return confirm('Are you sure you want to reset all settings to defaults?')" />
</p>

</form>

</div>

<?php
function showCheck($option, $title) {
?>
<input type="checkbox" name="<?php echo $option; ?>" value="1" id="<?php echo $option?>" <?php if(get_option($option)) print " checked='checked'"; ?> />
<label for="<?php echo $option?>"><?php e($title) ?></label><br />

<?php
}
function showOption($value, $option, $title) {
?>
<option value="<?php echo $value; ?>"<?php if((get_option($option)) == $value) print " selected='yes'"; ?>><?php echo $title; ?></option>
<?php
}
?>