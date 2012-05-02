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
	
});