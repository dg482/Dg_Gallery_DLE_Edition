(function () {
    tinymce.create("tinymce.plugins.filedescr", {
        init: function (a, b) {
            var d = this,
                c = tinymce.explode(a.settings.content_css);
            d.editor = a;
            tinymce.each(c, function (f, e) {
                c[e] = a.documentBaseURI.toAbsolute(f)
            });
            a.addCommand("mcefiledescr", function (id) {
											 id = (id === false)?'all':id;
											//	console.log()
											if(!fm.sess){
												alert('ACCESS DENIED');
												return false;}
                a.windowManager.open({
                    file: a.getParam("plugin_filedescr_pageurl", "tiny/plugin/adddescr/?an=" +fm.act+'&fId='+id+'&sessid='+fm.sess),
                    width: parseInt(a.getParam("plugin_filedescr_width", "650")),
                    height: parseInt(a.getParam("plugin_filedescr_height", "450")),
                    resizable: "yes",
                    scrollbars: "yes",
                    popup_css: c ? c.join(",") : a.baseURI.toAbsolute("themes/" + a.settings.theme + "/skins/" + a.settings.skin + "/content.css"),
                    inline: a.getParam("plugin_filedescr_inline", 1)
                }, {
                    base: a.documentBaseURI.getURI()
                })
            });
            a.addButton("filedescr", {
                title: "filedescr.filedescr_desc",
                cmd: "mcefiledescr"
            })
        },
        getInfo: function () {
            return {
                longname: "filedescr",
                author: "Dark Ghost",
                authorurl: " ",
                infourl: " ",
                version: tinymce.majorVersion + "." + tinymce.minorVersion
            }
        }
    });
    tinymce.PluginManager.add("filedescr", tinymce.plugins.filedescr)
})();