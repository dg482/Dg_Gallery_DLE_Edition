gallery.ass.editor = null;
gallery.ass.openFile = '',
    gallery.ass.loadFile = function(f,a){
        var aObj ={};
        aObj.action = 'edit-template', aObj.file = f, aObj.cmd = 'open';
        gallery.ajax.sendQuery(aObj, 'json', function(data){
            if(null != data){
                gallery.ass.openFile = f;
                $('li').removeClass('edit');
                $(a).parent().parent().addClass('edit');
                var edtr = $('<form>\
                               <div id="editor-toopbar"><b id="status-file"></b><h4>'+gallery.ass.openFile +'</h4>\
                                 <a href="javascript: gallery.ass.saveTpl(gallery.ass.editor.getValue())" class="btn save" rel="tipsy" title="Ctrl + S"></a></div>\
                                  <textarea name="file" id="file" rows="" cols="" style="display:none"></textarea></form>'),
                mode = 'tags',readOnly = false;
                if(data.fileedit.comment == 'only read'){
                    edtr.attr('readonly',true);
                    readOnly = true;
                    $('#status-file',edtr).addClass('only-read');
                }else{
                    $('#status-file',edtr).addClass('write');
                }
                edtr.css({
                    top: 5,
                    right:5,
                    left:5,
                    position: 'relative',
                    bottom:2
                });
                CodeMirror.defineMode("tags", function(config, parserConfig) {
                    var tagOverlay = {
                        token: function(stream, state) {
                            if (stream.match("{")) {
                                while ((ch = stream.next()) != null)
                                    if (ch == "}") break;
                                return "gallery-tag";
                            }
                            if (stream.match("[")) {
                                while ((ch = stream.next()) != null)
                                    if (ch == "]") break;
                                return "gallery-block";
                            }
                            if (stream.match("[/")) {
                                while ((ch = stream.next()) != null)
                                    if (ch == "]") break;
                                return "gallery-block";
                            }
                            while (stream.next() != null && !stream.match("{", false)) {}
                            return null;
                        }
                    };
                    return CodeMirror.overlayParser(CodeMirror.getMode(config, parserConfig.backdrop || "text/html"), tagOverlay);
                });
                switch (data.fileedit.extension) {
                    case 'tpl':
                    case 'html':
                        mode = 'tags';
                        break;
                    case 'js':
                    case 'json':
                        mode = 'javascript';
                        break;
                    case 'css':
                        mode = 'css';
                        break;
                    default:
                        mode = 'tags';
                        break;
                }
                setTimeout(function(){
                    gallery.ass.editor = CodeMirror.fromTextArea(document.getElementById('file'), {
                        textWrapping: false,
                        mode: mode,
                        tabMode: "indent",
                        lineNumbers: true,
                        indentWithTabs: true,
                        firstLineNumber: 1,
                        readOnly: readOnly,
                        onKeyEvent: function(i, e) {
                            //save
                            if (e.keyCode == 83 && (e.ctrlKey || e.metaKey) && !e.altKey) {
                                e.stop();
                                return gallery.ass.saveTpl(gallery.ass.editor.getValue());
                            }
                            if (e.keyCode == 32 && (e.ctrlKey || e.metaKey) && !e.altKey) {
                                e.stop();
                                return gallery.ass.openTag(data.fileedit.tag);
                            }
                            return false;
                        }
                    });
                    gallery.ass.editor.setValue(data.fileedit.content)
                },100)
                $('#work-area').html(edtr);
                $('*[rel="tipsy"]').tipsy({})
            }
        },gallery.lang.ajax.loadFile);
    };
gallery.ass.openTag = function(tagObj){
    if (this.editor.somethingSelected()) return;
    var cur = this.editor.getCursor(false), token = this.editor.getTokenAt(cur)
    ,str = '',
    tprop = token, pos = this.editor.cursorCoords(), id = gallery.core.getRand(),
    box = $('<div id="'+ id +'" class="templateTag b-4 box-shadow-5-03"></div>');
    for(var t in tagObj){
        box.append('<a href="javascript:void(null);" rel="'+t+'"><span>'+tagObj[t]+'</span></a>');
    }
    box.css({
        left: Math.round(pos.x),
        top: Math.round(pos.y),
        dispaly: 'none'
    });
    $('body').append(box);
    box.show('slide',{},300);
    box.children('a').click(function(){
        insert($(this).attr('rel'))
    });
    $('body').one('click', function(){
        $('#'+id).fadeOut('fast', function(){
            $(this).remove()
        });
    });
    function insert(str){
        if (!/^[\w$_]*$/.test(token.string)) {
            token = tprop = {
                start: cur.ch,
                end: cur.ch,
                string: "",
                state: token.state,
                className: token.string == "." ? "js-property" : null
            };
        }
        gallery.ass.editor.replaceRange(str, {
            line: cur.line,
            ch: token.start
        }, {
            line: cur.line,
            ch: token.end
        });
    }
};
gallery.ass.saveTpl = function(data){
    var aObj ={};
    aObj.action = 'edit-template',
    aObj.data = data,
    aObj.file = gallery.ass.openFile,
    aObj.cmd = 'save';
    gallery.ajax.sendQuery(aObj, 'json', function(dat){
        },gallery.lang.ajax.saveTpl)
};
gallery.ass.setListEdit = function(obj){
    var tpl = $('<ul class="tree categories top-5 left-0">\
          <li class="tree-item-main parent last"> <span class="item box-slide-head">\
            <a href="javascript:void(null)">'+ obj.currentDir + '</a></span>\
              <ul class="box-slide-body"></ul></li></ul>');
    gallery.ass.setListDir(tpl, obj,true);
    gallery.ass.setListFile(tpl, obj);

    $('#tree').html(tpl)
};
gallery.ass.setListDir = function(tpl,obj,n){
    var i =0;
    for(var d in obj.dir){
        i +=1;
        var id = gallery.core.getRand()+i,
        name = obj.dir[d].split('/');
        name = (name.length  > 1) ? name[name.length - 1]: name  = name[0];
        var dir = $('<li class="tree-item"><span class="item box-slide-head">\
                          <a href="javascript:"  onclick="gallery.ass.loadDir(\''+obj.dir[d]+'\',this,\''+id+'\');">'+name+'</a></span>\
                             <ul class="hidden albom_list" id="list-'+id+'"></ul></li>')
        if(n){
            $('.box-slide-body',tpl).append(dir);
        }else{
            $(tpl).append(dir);
        }
    }
};
gallery.ass.setListFile = function(tpl,obj){
    for(var f in obj.file){
        var ext = obj.file[f].split('.'), name = obj.file[f],onC='';
        ext = ext[ext.length - 1];
        name = name.split('/');
        name =(name.length  > 1)? name[name.length - 1] : name[0];
        if(ext == 'tpl' || ext == 'css' || ext == 'js'){
            onC =  'onclick="gallery.ass.loadFile(\''+obj.file[f]+'\',this);"'
        }
        var file = $('<li class="tree-item"><span class="item box-slide-head '+ext+'">\
                          <a href="javascript:void(null);"'+onC+' >'+name+'</a></span></li>')
        $(tpl).append(file);
    }
};
gallery.ass.loadDir = function(f,a,i){
    var aObj ={};
    $(a).addClass('loading');
    aObj.action = 'edit-template', aObj.dir = f, aObj.cmd = 'open';
    gallery.ajax.sendQuery(aObj, 'json', function(data){
        if(null != data){
            $('#list-'+i).removeClass('hidden');
            gallery.ass.setListDir($('#list-'+i), data,false);
            gallery.ass.setListFile($('#list-'+i), data);
        }
        $(a).removeClass('loading').removeAttr('onclick').bind('click', function(){
            $('#list-'+i).toggle('slow');
        })
    });
};




gallery.ass.catShow =  function(obj){
    $(obj).click(function(){
        var id = $(this).attr('id');
        gallery.ajax.sendQuery({
            action: 'open',
            id: id
        }, 'html', function(data){
            $('ul#albom_'+id).html(data).removeClass('hidden');
        });
        return false;

    })
};
