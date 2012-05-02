<?php

// access wp functions externally
require_once('bootstrap.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Google Doc Embedder</title>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-includes/js/jquery/jquery.js"></script> 
	<script type="text/javascript" src="js/dialog.js"></script>

    <style type="text/css">
	h2 {
		font-size: 12px;
		color: #000000;
		padding:10px 0;
	}
	.mceActionPanel {
		margin-top:20px;
	}
	.diy{
		margin:5px 5px -5px 10px;
	}
    </style>
    
</head>
<body>
<form onsubmit="GDEInsertDialog.insert();return false;" action="#">
    <p>
    <input name="diy" type="checkbox" value="1" class="diy"/>
    <?php _e('I\'ll insert the shortcode myself', 'gde'); ?>
    </p>
  <h2 class="gray"><?php _e('GDE Shortcode Options', 'gde'); ?></h2></td>
  </tr>
  
  <fieldset>
  <legend class="gray dwl_gray"><?php _e('Required', 'gde'); ?></legend>
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td align="right" class="gray dwl_gray"><strong><?php _e('URL or Filename', 'gde'); ?></strong><br /><?php _e('Full URL or filename to append to File Base URL', 'gde'); ?></td>
    <td valign="top"><input name="url" type="text" class="opt dwl" id="url" style="width:200px;" /><br/>
	<span id="uri-note-base" style="display:none;color:#2B6FB6;"><?php _e('File Base URL will be prefixed', 'gde'); ?></span>
	<span id="uri-note-file" style="display:none;color:red;"><?php _e('Unsupported file type', 'gde'); ?></span>
	</td>
  </tr>  
  </table>
  </fieldset>

  <br/>
  <fieldset>
  <legend class="gray dwl_gray"><?php _e('Optional (Override Global Settings)', 'gde'); ?></legend>
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td align="right" class="gray dwl_gray"><strong><?php _e('Height', 'gde'); ?></strong><br/>(<?php _e('format:', 'gde'); ?> 40% <?php _e('or', 'gde'); ?> 300px)</td>
    <td valign="top" style="width:200px;"><input name="height" type="text" class="opt dwl" id="height" size="6" /></td>
  </tr>
  <tr>
    <td align="right" class="gray dwl_gray"><strong><?php _e('Width', 'gde'); ?></strong><br/>(<?php _e('format:', 'gde'); ?> 40% <?php _e('or', 'gde'); ?> 300px)</td>
    <td valign="top"><input name="width" type="text" class="opt dwl" id="width" size="6" /></td>
  </tr>
  <tr>
    <td align="right" class="gray dwl_gray"><strong><?php _e('Show Download Link', 'gde'); ?></strong></td>
    <td valign="top" class="gray dwl_gray"><input name="save" type="radio" class="opt dwl save" value="1" /> <?php _e('Yes', 'gde'); ?> <input name="save" type="radio" class="opt dwl save" value="0" /> <?php _e('No', 'gde'); ?></td>
  </tr>
  <tr>
    <td colspan="2" class="gray dwl_gray">
      <input name="restrict_dl" type="checkbox" value="-1" class="restrict_dl dwl opt" />
      <?php _e('Show download link only if user is logged in', 'gde'); ?>
   </td>
   </tr>
  <tr>
    <td colspan="2" class="gray dwl_gray">
      <input name="disable_cache" type="checkbox" value="-1" class="disable_cache dwl opt" />
      <?php _e('Disable caching (this document is frequently overwritten)', 'gde'); ?>
   </td>
   </tr>
     <tr>
    <tr>
    <td colspan="2" class="gray dwl_gray">
      <input name="bypass_error" type="checkbox" value="-1" class="bypass_error opt" />
      <?php _e('Disable internal error checking (try if URL is confirmed good but document doesn\'t display)', 'gde'); ?>
   </td>
   </tr> 
   </table>
   </fieldset>
   
   <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
    <td colspan="2">
    <br />
    <?php _e('Shortcode Preview', 'gde'); ?>
    <textarea name="shortcode" cols="72" rows="2" id="shortcode"readonly="readonly"></textarea>
    </td>
  </tr> 
    
</table>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="insert" name="insert" value="<?php _e('Insert', 'gde'); ?>" onclick="GDEInsertDialog.insert();" />
		</div>

		<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="<?php _e('Cancel', 'gde'); ?>" onclick="tinyMCEPopup.close();" />
		</div>
	</div>
</form>

</body>
</html>