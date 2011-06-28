tinyMCEPopup.requireLangPack();
	
var GDEInsertDialog = {
	init : function() {
		var f = document.forms[0];
        var shortcode;
					
				jQuery('.diy').click(function(){
				// diy option selected
					var dis = jQuery('.opt').attr('disabled');
					
					if (dis) {					
					jQuery('.opt').attr('disabled', ''); 
					jQuery('.gray').css('color','black');					
						update_sc();	
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
				});
		
		function update_sc() {
			 shortcode = 'gview';
			 
				 if ((jQuery('#url').val() !=0)&(jQuery('#url').val()) !=null){
					 shortcode = shortcode + '  file="'+jQuery('#url').val()+'"';
				 }
				 if ((jQuery('#height').val() !=0)&(jQuery('#height').val()) !=null){
					 shortcode = shortcode + '  height="'+jQuery('#height').val()+'"';
				 }
				 if ((jQuery('#width').val() !=0)&(jQuery('#width').val() !=null)){
					 shortcode = shortcode + '  width="'+jQuery('#width').val()+'"';
				 }
				
				if (jQuery("input[@name'save']:checked").val() == '1') {
					shortcode = shortcode + '  save="1"';
				}
				else if (jQuery("input[@name='save']:checked").val() == '0') {
					shortcode = shortcode + '  save="0"';
				 }
				 
				 if ( $('.restrict_dl').is(':checked')) {
					 shortcode = shortcode + ' authonly="1"';
				 }
				 if ( $('.disable_cache').is(':checked')) {
					 shortcode = shortcode + ' cache="0"';
				 }
				 if ( $('.bypass_error').is(':checked')) {
					 shortcode = shortcode + ' force="1"';
				 }				
				 
				 var newsc = shortcode.replace(/  /g,' ');
				 
				 jQuery('#shortcode').val('['+newsc+']');

		}
			
				
	},

	insert : function() {
		// Insert the contents from the input into the document

		tinyMCEPopup.editor.execCommand('mceInsertContent', false, jQuery('#shortcode').val());
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(GDEInsertDialog.init, GDEInsertDialog);

