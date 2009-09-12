<?php
include('wpframe.php');

if(isset($_REQUEST['submit']) and $_REQUEST['submit']) {
	if(isset($_POST['gde_show_dl'])) {
		update_option('gde_show_dl', 1);
	} else {
		update_option('gde_show_dl', 0);
	}
	if(isset($_POST['gde_default_width'])) {
		update_option('gde_default_width', sanitizeOpt($_POST['gde_default_width']));
	}
	if(isset($_POST['gde_default_height'])) {
		update_option('gde_default_height', sanitizeOpt($_POST['gde_default_height']));
	}
	if(isset($_POST['gde_link_text'])) update_option('gde_link_text', $_POST['gde_link_text']);
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
<em>To override on individual posts, manually set height= and width= in the post shortcode.</td>
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
<em>To override the display setting on an individual post, use the save= attribute.</em></td>
</tr>
<tr valign="top">
<td colspan="2"><?php showOption('gde_show_dl', t('Show the download link by default')); ?></td>
</tr>
<tr valign="top">
<th scope="row">Link Text</th>
<td><input type="text" name="gde_link_text" value="<?php echo get_option('gde_link_text'); ?>" /></td>
</tr>
</table>
<p class="submit">
<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<span id="autosave"></span>
<input type="submit" name="submit" value="<?php e('Save Options') ?>" style="font-weight: bold;" />
</p>

</form>

</div>

<?php
function showOption($option, $title) {
?>
<input type="checkbox" name="<?php echo $option; ?>" value="1" id="<?php echo $option?>" <?php if(get_option($option)) print " checked='checked'"; ?> />
<label for="<?php echo $option?>"><?php e($title) ?></label><br />

<?php
}

function sanitizeOpt($value) {
	$value = ereg_replace('[^0-9]+', '', $value);
	return $value;
}