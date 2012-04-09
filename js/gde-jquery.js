//(function($jQuery){
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
	
});