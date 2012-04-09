<?php
//require_once("../../../wp-config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Google Doc Embedder</title>
	<script type="text/javascript" src="js/tiny_mce_popup.js"></script>
	<script type="text/javascript" src="js/dialog.js"></script>
	<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>    

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
    I'll insert the shortcode myself
    </p>
  <h2 class="gray">GDE Shortcode Options</h2></td>
  </tr>
  
  <fieldset>
  <legend class="gray dwl_gray">Required</legend>
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td align="right" class="gray dwl_gray"><strong>URL or Filename</strong><br />Full URL or filename to append to File Base URL</td>
    <td valign="top"><input name="url" type="text" class="opt dwl" id="url" style="width:200px;" /><br/>
	<span id="uri-note"></span></td>
  </tr>  
  </table>
  </fieldset>

  <br/>
  <fieldset>
  <legend class="gray dwl_gray">Optional (Override Global Settings)</legend>
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td align="right" class="gray dwl_gray"><strong>Height</strong><br/>(format: 40% or 300px)</td>
    <td valign="top" style="width:200px;"><input name="height" type="text" class="opt dwl" id="height" size="6" /></td>
  </tr>
  <tr>
    <td align="right" class="gray dwl_gray"><strong>Width</strong><br/>(format: 40% or 300px)</td>
    <td valign="top"><input name="width" type="text" class="opt dwl" id="width" size="6" /></td>
  </tr>
  <tr>
    <td align="right" class="gray dwl_gray"><strong>Show Download Link</strong></td>
    <td valign="top" class="gray dwl_gray"><input name="save" type="radio" class="opt dwl save" value="1" /> Yes <input name="save" type="radio" class="opt dwl save" value="0" /> No</td>
  </tr>
  <tr>
    <td colspan="2" class="gray dwl_gray">
      <input name="restrict_dl" type="checkbox" value="-1" class="restrict_dl dwl opt" />
      Show download link only if user is logged in
   </td>
   </tr>
  <tr>
    <td colspan="2" class="gray dwl_gray">
      <input name="disable_cache" type="checkbox" value="-1" class="disable_cache dwl opt" />
      Disable caching (this document is frequently overwritten)
   </td>
   </tr>
     <tr>
    <tr>
    <td colspan="2" class="gray dwl_gray">
      <input name="bypass_error" type="checkbox" value="-1" class="bypass_error opt" />
      Disable internal error checking (try if URL is confirmed good but document doesn't display)
   </td>
   </tr> 
   </table>
   </fieldset>
   
   <table width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
    <td colspan="2">
    <br />
    Shortcode Preview
    <textarea name="shortcode" cols="72" rows="2" id="shortcode"></textarea>
    </td>
  </tr> 
    
</table>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="insert" name="insert" value="{#insert}" onclick="GDEInsertDialog.insert();" />
		</div>

		<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
		</div>
	</div>
</form>

</body>
</html>