(function() {
	tinymce.PluginManager.requireLangPack('gde');
	tinymce.create('tinymce.plugins.gde', {
		init : function(ed,url) {
			// fix path
			url = url.replace('/js', '');
			
			ed.addCommand('gde_cmd', function() {
				ed.windowManager.open( {
					file : url + '/libs/gde-dialog.php',
					width : 460 + parseInt(ed.getLang('gde.delta_width',0)),
					height : 540 + parseInt(ed.getLang('gde.delta_height',0)),
					inline : 1}, {
						plugin_url : url
					}
				)}
			);
			ed.addButton('gde', {
				title : 'Google Doc Embedder',
				cmd : 'gde_cmd',
				image : url + '/img/gde-button.png'
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
				authorurl : 'http://www.davistribe.org/gde',
				infourl : 'http://www.davistribe.org/gde',
				version : "1.3"}
		}
	});
	tinymce.PluginManager.add('gde',tinymce.plugins.gde);
})();