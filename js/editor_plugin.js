(function() {
	tinymce.PluginManager.requireLangPack('gde');
	tinymce.create('tinymce.plugins.GDEPlugin', {
		init : function(ed,url) {
			ed.addCommand('mceGDE', function() {
				ed.windowManager.open( {
					file : url + '/../gde-dialog.php',
					width : 420 + parseInt(ed.getLang('gde.delta_width',0)),
					height : 540 + parseInt(ed.getLang('gde.delta_height',0)),
					inline : 1}, {
						plugin_url : url,
						some_custom_arg : 'custom arg'
					}
				)}
			);
			ed.addButton('gde', {
				title : 'Google Doc Embedder',
				cmd : 'mceGDE',
				image : url + '/../img/gde-button.png'
			});
			ed.onNodeChange.add
				(function(ed,cm,n) {
					cm.setActive('gde',n.nodeName=='IMG')
				})
		},
		createControl : function(n,cm) {
			return null
		},
		getInfo : function() { 
			return { 
				longname : 'Google Doc Embedder',
				author : 'Kevin Davis',
				authorurl : 'http://www.davismetro.com/gde',
				infourl : 'http://www.davismetro.com/gde',
				version : "1.1"}
		}
	});
	tinymce.PluginManager.add('gde',tinymce.plugins.GDEPlugin)
})();