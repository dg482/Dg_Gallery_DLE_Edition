(function() {
	tinymce.create('tinymce.plugins.filedescr', {
		init : function(ed, url) {
			var t = this, css = tinymce.explode(ed.settings.content_css);
			t.editor = ed;
			// Force absolute CSS urls	
			tinymce.each(css, function(u, k) {
				css[k] = ed.documentBaseURI.toAbsolute(u);
			});

			ed.addCommand('mcefiledescr', function(id) {
				ed.windowManager.open({
					file : ed.getParam("plugin_filedescr_pageurl", url + "/filedescr.html"),
					width : parseInt(ed.getParam("plugin_filedescr_width", "500")),
					height : parseInt(ed.getParam("plugin_filedescr_height", "450")),
					resizable : "yes",
					scrollbars : "yes",
					popup_css : css ? css.join(',') : ed.baseURI.toAbsolute("themes/" + ed.settings.theme + "/skins/" + ed.settings.skin + "/content.css"),
					inline : ed.getParam("plugin_filedescr_inline", 1)
				}, {
					base : ed.documentBaseURI.getURI()
				});
			});

			ed.addButton('filedescr', {title : 'filedescr.filedescr_desc', cmd : 'mcefiledescr'});
		},

		getInfo : function() {
			return {
				longname : 'filedescr',
				author : 'Dark Ghost',
				authorurl : '',
				infourl : '',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('filedescr', tinymce.plugins.Preview);
})();