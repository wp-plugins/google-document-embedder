<?php
 /*
  * Called from gde-functions.php gde_debug()
  */
  
  if ( ! defined( 'ABSPATH' ) ) {
	exit;
  }
?>
<div class="wrap">
<h2>Google Doc Embedder <?php _e('Support', 'gde'); ?></h2>

<p><strong><?php _e('Please review the documentation before submitting a request for support:', 'gde'); ?></strong></p>
<ul style="list-style-type:square; padding-left:25px;line-height:1em;">
	<li><a href="<?php echo $pdata['PluginURI']; ?>">Google Doc Embedder</a></li>
	<li><a href="<?php echo GDE_WP_URL; ?>faq/"><?php _e('Plugin FAQ', 'gde'); ?></li>
	<li><a href="<?php echo GDE_FORUM_URL; ?>"><?php _e('Support Forum', 'gde'); ?></a></li>
</ul>

<p><?php _e("If you're still experiencing a problem, please complete the form below.", 'gde'); ?></p>

<form action="<?php echo GDE_PLUGIN_URL;?>libs/post-debug.php" id="debugForm">

<table class="form-table" style="border:1px solid #ccc;">
<tr valign="top">
	<th scope="row"><label for="name" id="name_label"><?php _e('Your Name', 'gde'); ?></label></th>
	<td><input size="50" name="name" id="name" value="" type="text"></td>
</tr>
<tr valign="top">
	<th scope="row"><label for="sender" id="sender_label"><?php _e('Your E-mail', 'gde'); ?>*</label></th>
	<td><input size="50" name="email" id="sender" value="" type="text">
	<div id="err_email" class="err" style="color:red;font-weight:bold;display:none;">A valid email address is required.</div></td>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><label for="sc" id="sc_label"><?php _e('Shortcode', 'gde'); ?></label></th>
	<td><input size="50" name="shortcode" id="sc" value="" type="text"><br/>
	<em><?php _e("If you're having a problem getting a specific document to work, paste the shortcode you're trying to use here.", 'gde'); ?></em></td>
	</select>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><label for="msg" id="msg_label"><?php _e('Message', 'gde'); ?></label></th>
	<td><textarea name="message" id="msg" style="width:75%;min-height:50px;"></textarea></td>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Message Options', 'gde'); ?></th>
	<td>
	<input type="checkbox" name="senddb" id="senddb" checked="checked"> <label for="senddb" id="senddb_label"><?php _e('Send debug information', 'gde'); ?>
	(<a href="javascript:void(0);" id="ta_toggle"><?php _e('View'); ?></a>)</label><br/>
	<input type="checkbox" name="cc" id="cc"> <label for="cc" id="cc_label"><?php _e('Send me a copy', 'gde'); ?></label></td>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div id="debugblock" style="display:none;">
	<p><?php _e('Debug Information', 'gde'); ?>:</p>
	<textarea name="debug" id="debugtxt" style="width:100%;min-height:200px;font-family:monospace;" readonly="readonly">
<?php

	echo "--- GDE Debug Information ---\n\n";
	echo "GDE Version: $gde_ver\n";
	echo "WordPress Version: $wp_version [".get_locale()."]\n";
	echo "PHP Version: ".phpversion()."\n";
	echo "Plugin URL: ".GDE_PLUGIN_URL."\n";
	echo "Server Env: ".$_SERVER['SERVER_SOFTWARE']."\n";
	echo "Browser Env: ".$_SERVER['HTTP_USER_AGENT']."\n\n";

	echo "cURL: ";
	if (function_exists('curl_version')) {
		$curl = curl_version(); echo $curl['version']."\n";
	} else { echo "No\n"; }
	
	echo "allow_url_fopen: ";
	if (ini_get('allow_url_fopen') !== "1") {
		echo "No\n";
	} else { echo "Yes\n"; }
	
	echo "Rich Editing: ";
	if (get_user_option('rich_editing')) {
		echo "Yes\n";
	} else { echo "No\n"; }
	
	echo "Viewer: ";
	if ($gdeoptions['disable_proxy'] == "no") {
		echo "Enhanced\n\n";
	} else { echo "Standard\n\n"; }

	echo "Settings Array:\n";
	print_r($gdeoptions);
	
	//echo "\n";
	//echo "MIME Supported:\n";
	//print_r(get_allowed_mime_types());
?>
	</textarea>
	<br/><br/>
	</div>
</div>
	<div id="debugwarn" style="display:none;color:red;font-weight:bold;">
		<p><?php _e("I'm less likely to be able to help you if you do not include debug information.", 'gde'); ?></p>
	</div>
	<input id="debugsend" class="button-primary" type="submit" value="<?php _e('Send Support Request', 'gde'); ?>" name="submit">
	<span id="formstatus" style="padding-left:20px;display:none;">
		<img src="<?php echo GDE_PLUGIN_URL;?>img/in-proc.gif" alt="">
	</span>
	</td>
</tr>
</table>
</form>

</div>