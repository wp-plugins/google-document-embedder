jQuery(function ($) {

	/* jQuery library for GDE */
	
	var opt = $('input[name=disable_proxy]:checked').val();
	if (opt == "yes") {
		gdeHideTb();
	} else {
		gdeShowTb();
	}

	$("#std-view").click(function() {
		gdeHideTb();
	});
	
	$("#enh-view").click(function() {
		gdeShowTb();
	});
	
	function gdeShowTb() {
		$("#tbedit").removeAttr("style");
	}
	
	function gdeHideTb() {
		$("#tbedit").attr("style","display:none;");
	}
	
	$("#advopt-plugin").click(function() {
		$("#adv-editor").hide();
		$("#adv-plugin").toggle();
	});
	
	$("#advopt-editor").click(function() {
		$("#adv-plugin").hide();
		$("#adv-editor").toggle();
	});
	
	$("#disable_editor").click(function() {
		var edopt = $(this).prop("checked");
		if (edopt == true) {
			gdeHideEdOpt();
		} else {
			gdeShowEdOpt();
		}
	});
	
	function gdeHideEdOpt() {
		$("#ed_embed_sc").removeAttr("checked");
		$("#ed_extend_upload").removeAttr("checked");
		$("#ed_embed_sc").attr("disabled","true");
		$("#ed_extend_upload").attr("disabled","true");
	}
	
	function gdeShowEdOpt() {
		$("#ed_embed_sc").removeAttr("disabled");
		$("#ed_embed_sc").attr("checked","true");
		$("#ed_extend_upload").removeAttr("disabled");
		$("#ed_extend_upload").attr("checked","true");
	}
	
	/**
	 * Used to handle debug information
	 */
	// show/hide debug info
	$("#ta_toggle").click(function() {
		$("#debugblock").toggle();
	});
	
	// warn on debug deselection
	$('#senddb').click(function() {
		if (this.checked) {
			$("#debugwarn").hide();
		} else {
			$("#debugwarn").show();
		}
	});
	
	
	// validate input
	$("#debugsend").click(function() {
		$(".err").hide();
		
		var em = $("#sender").val();
		if (em == "" || (!validateEmail('sender')) ) {
			$("#err_email").show();
			$("#sender").focus();
			return false;
		}
		
		$("#debugsend").attr("disabled","true");
		$("#debugsend").attr('class', 'button-secondary');
		$("#debugsend").css('outline','none')
		$("#formstatus").show();
	});
	
	// submit handler
	$("#debugForm").submit(function(event) {
	
		// stop normal form submission
		event.preventDefault();
	
		// get form data
		var $form = $(this),
			url = $form.attr("action"),
			name = $form.find("#name").val(),
			debug = $form.find("#debugtxt").val(),
			email = $form.find("#sender").val(),
			sc = $form.find("#sc").val(),
			msg = $form.find("#msg").val();
		
		// check for debug info
		if ($('#senddb').is(':checked')) {
			var senddb = debug;
		} else {
			var senddb = '';
		}
		
		// check for cc
		if ($('#cc').is(':checked')) {
			var sendcc = "yes";
		} else {
			var sendcc = "no";
		}
		
		// post the data
		$.post(url, { 
			name: name,
			email: email,
			sc: sc,
			msg: msg,
			senddb: senddb,
			cc: sendcc
			}, function(data) {
				if (data == "success") {
					var notice = $("#formstatus").html();
					notice = notice.replace('in-proc.gif', 'done.gif');
					$("#formstatus").empty().append(notice);
				} else {
					var notice = $("#formstatus").html();
					notice = notice.replace('in-proc.gif', 'fail.gif');
					$("#formstatus").empty().append(notice);
				}
		});
	});
});

function validateEmail(txtEmail){
   var a = document.getElementById(txtEmail).value;
   var filter = /^((\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*?)\s*;?\s*)+/;
    if (filter.test(a)) {
        return true;
    } else {
        return false;
    }
}