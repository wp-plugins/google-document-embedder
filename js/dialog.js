tinyMCEPopup.requireLangPack();
	
var GDEInsertDialog = {
	init : function() {
		var f = document.forms[0];
        var shortcode;
		
				jQuery('.diy').click(function(){
					// diy option selected
					var dis = jQuery(this).prop("checked");
					
					if (dis == false) {					
						jQuery('.opt').removeAttr('disabled'); 
						jQuery('.gray').css('color','black');					
						jQuery('#shortcode').val('');
						check_uri();
						
					} else {					
						jQuery('.opt').attr('disabled', 'disabled');
						jQuery('.gray').css('color','gray');
						jQuery('#shortcode').val('[gview file=""]');
					}
				
				});
				
				jQuery('.restrict_dl').click(function(){
					 update_sc();
				});	
				jQuery('.disable_cache').click(function(){
					 update_sc();
				});	
				jQuery('.bypass_error').click(function(){
					 update_sc();
				});
				jQuery('.save').click(function(){
					 update_sc();
				});
				
				jQuery('#height').blur(function(){
					update_sc();
				});
				jQuery('#width').blur(function(){
					update_sc();
				});	
				jQuery('#url').blur(function(){
					update_sc();
					check_uri();
				});
		
		function check_uri() {
			var type_regex = /\.(doc|docx|pdf|ppt|pptx|tif|tiff|xls|xlsx|pages|ai|psd|dxf|svg|eps|ps|ttf|xps|zip|rar)$/i
			var path_regex = /^http/i;
			
			if(!(type_regex.test( jQuery('#url').val() )) & ( jQuery('#url').val() !=0 )) {
				// unsupported file type
				jQuery('#uri-note-file').show();
				jQuery('#uri-note-base').hide();
			} else {
				if(!(path_regex.test( jQuery('#url').val() )) & ( jQuery('#url').val() !=0 )) {
					// file base url appended
					jQuery('#uri-note-file').hide();
					jQuery('#uri-note-base').show();
				} else {
					jQuery('#uri-note-file').hide();
					jQuery('#uri-note-base').hide();
				}
			}
		}
		
		function update_sc() {
			 shortcode = 'gview';
			 
				if (( jQuery('#url').val() !=0 ) & ( jQuery('#url').val() ) !=null) {
					check_uri();
					shortcode = shortcode + '  file="'+jQuery('#url').val()+'"';
				} else if ( jQuery('#url').val() == '' ) {
					jQuery('#uri-note').html('');
					shortcode = shortcode + ' file=""';
				}
				if (( jQuery('#height').val() !=0 ) & ( jQuery('#height').val() ) !=null) {
					shortcode = shortcode + '  height="'+jQuery('#height').val()+'"';
				}
				if (( jQuery('#width').val() !=0 ) & ( jQuery('#width').val() ) !=null) {
					shortcode = shortcode + '  width="'+jQuery('#width').val()+'"';
				}
				
				if ( jQuery("input[@name'save']:checked").val() == '1') {
					shortcode = shortcode + '  save="1"';
				}
				else if ( jQuery("input[@name='save']:checked").val() == '0') {
					shortcode = shortcode + '  save="0"';
				}
				 
				if ( jQuery('.restrict_dl').is(':checked') ) {
					shortcode = shortcode + ' authonly="1"';
				}
				if ( jQuery('.disable_cache').is(':checked') ) {
					shortcode = shortcode + ' cache="0"';
				}
				if ( jQuery('.bypass_error').is(':checked') ) {
					shortcode = shortcode + ' force="1"';
				}				
				 
				var newsc = shortcode.replace(/  /g,' ');
				 
				jQuery('#shortcode').val('['+newsc+']');
		}
	},
	insert : function() {
		// insert the contents from the input into the document
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, jQuery('#shortcode').val());
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(GDEInsertDialog.init, GDEInsertDialog);