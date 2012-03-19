gallery.ass = {
    timeOut: 100,
    active: null,
    helper: false,
    curentId: 0,
    keyword: [],
    contestMenu:[],
    popupId: 0,
    msg:[],
    wysiwyg: true,
    label: true,// on || off label images
    init: function(){
    // this.setLabel();
    },
    initThumbHelper: function(){
        $('#viewport').children('ul').children('li').hover(function(e){
            var i = $(this).index();
            gallery.ass.curentId = $(this).attr('id').split('-', 2)[1]
            gallery.ass.active = setTimeout(function(){
                gallery.ass.showThumbHelper(e,i);
            },gallery.ass.timeOut);

        }, function(){
            gallery.ass.helper = false;
            var i = $(this).index();
            clearTimeout(gallery.ass.active);
            gallery.ass.hideThumbHelper(i);
        });

    },
    showThumbHelper: function(e,i){
        var obj = $('#viewport').children('ul').children('li').eq(i);
        $(obj).addClass('active');
        $(obj).append('<div class="thumbHelper" id="thumbHelper" />');
        $('#thumbHelper').click(function(){
            gallery.ass.setPopUp();
        });
    },
    hideThumbHelper: function(i){
        setTimeout(function(){
            if(! gallery.ass.helper){
                $('.infoHelper-block').fadeOut('normal', function(){
                    $(this).remove();
                });
            }
        },200)
        var obj = $('#viewport').children('ul').children('li').eq(i);
        if(obj.hasClass('active')){
            obj.removeClass('active');
            obj.children('.thumbHelper').remove();
        }

    },
    setPopUp: function(){
        var info = gallery.ass.setInfoFile();
        var tpl = $('<div class="infoHelper-block">\
                   <div class="infoHelper-bottom"></div>\
                     <div class="infoHelper-main">'+info+'</div></div>');
        $('body').append(tpl);
        gallery.eff.aHover($('a.sub'));
        var o = $('#thumbHelper').offset();
        tpl.css({
            top: o.top - 200,
            left: o.left,
            display: 'none'
        })
        $(tpl).fadeIn('fast', function(){
            $(this).mousemove(function(){
                gallery.ass.helper = true;
            }).hover(function(){
                gallery.ass.helper = true;
            },function(){
                $('.infoHelper-block').fadeOut('normal', function(){
                    $(this).remove();
                });
            })
        })
    },
    setInfoFile : function(){
        var data = gallery.core.getData(gallery.ass.curentId),
        lang = gallery.lang;
        var tpl = '<ul>';
        tpl +='<li><span>ID '+data.id + '</span></li>';
        tpl +='<li>' +  lang.popup.load + ' ' +data.author + ', (' + data.date +')</li>';
        tpl +='<li>' +  lang.popup.rating + ' ' +data.rating + ', ' + lang.popup.vote + ' ' + data.vote_num +'</li>';
        if(data.other_dat.info && data.other_dat.info.colors){
            if(typeof data.other_dat.info.colors[0] == 'object'){
                tpl +='<li>';
                for(var k in data.other_dat.info.colors[0] ){
                    tpl += '<span rel="tipsy" class="colors-block" style="background:#'+k+'" title="'+data.other_dat.info.colors[0][k]+'"></span>';
                }
                tpl +='</li>';
            }
        }
        tpl +='</ul>';
        return tpl;
    },
    setToolbar: function(){
        // ShowLoading();
        var data = gallery.core.getData(gallery.core.currentId),
        obj = $('#file-setting');
        obj.html('');
        var  $inp = $('<input type="checkbox" id="comments_access" /><label for="comments_access">'+gallery.lang.setting.comm+'</label>');
        obj.append($inp);
        if(data.comm_access == 1){
            $('#comments_access').attr({
                checked: true
            });
        }
        $('#comments_access').button({
            text: true,
            icons: {
                primary: "ui-icon-comment"
            }
        }).click(function() {
            setTimeout(function(){
                var $val = (document.getElementById('comments_access').checked) ? 0 : 1
                gallery.ajax.sendQuery({
                    id: gallery.core.currentId,
                    set: $val,
                    action: 'comm_access'
                }, 'json',function(){
                    $('#comments_access').attr({
                        checked: ($val) ? true : false
                    }).button( "refresh");
                });
            },50)
            return false;
        });

        var $inpR = $('<input type="checkbox" id="rating_access"/><label for="rating_access">'+gallery.lang.setting.rating+'</label>');
        obj.append($inpR);
        if(data.rating_access == 1){
            $('#rating_access').attr('checked',true);
        }
        $('#rating_access').button({
            text: true,
            icons: {
                primary: "ui-icon-star"
            }
        }).click(function() {
            setTimeout(function(){
                var $val =  (document.getElementById('rating_access').checked) ? 0 : 1
                gallery.ajax.sendQuery({
                    id: gallery.core.currentId,
                    set: $val,
                    action: 'rating_access'
                }, 'json',function(){
                    $('#rating_access').attr({
                        checked: ($val) ? true : false
                    }).button( "refresh");
                });
            },50)
            return false;
        });
        if(data.status === 'albom'){
            var $inpC = $('<input type="checkbox" id="cover_albom"/><label for="cover_albom">'+gallery.lang.setting.cover+'</label>');
            obj.append($inpC);
            if(data.other_dat.is_cover){
                $('#cover_albom').attr('checked',true);
            }
            $('#cover_albom').button({
                text: true,
                icons: {
                    primary: "ui-icon-image"
                }
            }).click(function() {
                setTimeout(function(){
                    var $val =  (document.getElementById('cover_albom').checked) ? 0 : 1
                    gallery.ajax.sendQuery({
                        id: gallery.core.currentId,
                        set: $val,
                        parent_id: data.parent_id,
                        action: 'setcover'
                    }, 'json',function(){
                        $('#cover_albom').attr({
                            checked: ($val) ? true : false
                        }).button( "refresh");
                    });
                },50)
                return false;
            });
            if(gallery.ass.label){
                var $inpL = $('<input type="checkbox" id="set_label"/><label for="set_label">'+gallery.lang.setting.label+'</label>');
                obj.append($inpL);
                $('#set_label').attr('checked',(data.other_dat.label_status) ? true : false )
                .button({
                    text: true,
                    icons: {
                        primary: "ui-icon-tag"
                    }
                });
            }
        }
        var  param = {};
        var $inpT = $('<button>'+gallery.lang.setting.title+'</button>');
        obj.append($inpT);
        $($inpT).button({
            text: true,
            icons: {
                primary: "ui-icon-notice"
            }
        }).click(function() {
            param.height = 150;
            param.width = 300;
            gallery.ass.openPopUpBlock('text', obj, param, null,
                function(){
                    //set -|- update title
                    var title = $('#title_' + gallery.core.currentId).val();
                    gallery.ajax.sendQuery({
                        id: gallery.core.currentId,
                        title: title,
                        action: 'settitle'
                    }, 'json', function(data){
                        if(null != data){
                            gallery.core._setData(data);
                        }
                    });
                });
            return false;
        });
        var $inpD = $('<button>'+gallery.lang.setting.descr+'</button>');
        obj.append($inpD);
        $($inpD).button({
            text: true,
            icons: {
                primary: "ui-icon-notice"
            }
        }).click(function() {
            param.height = 380;
            param.width = 750;
            gallery.ass.openPopUpBlock('textarea', obj, param,function(){
                gallery.ass.InitWYSIWYG();
            },function(){
                //set -|- update description
                var descr = (typeof $.fn.tinymce == 'function') ?
                $('textarea#descr_' + gallery.core.currentId).tinymce().getContent() :
                $('textarea#descr_' + gallery.core.currentId).val()
                gallery.ajax.sendQuery({
                    id: gallery.core.currentId,
                    descr: descr,
                    action: 'setdescription'
                }, 'json', function(data){
                    if(null != data){
                        gallery.core._setData(data);
                    }
                });
            });
            return false;
        });

        var $inpTg = $('<button>'+gallery.lang.setting.tags +'</button>');
        obj.append($inpTg);
        $($inpTg).button({
            text: true,
            icons: {
                primary: "ui-icon-notice"
            }
        }).click(function() {
            param.height = 200;
            param.width = 500;
            gallery.ass.openPopUpBlock('tags', obj, param,function(){
                gallery.ass.setListTag();
            },function(){
                //set -|- update description
                var descr =$('textarea#tag_' + gallery.core.currentId).val()
                gallery.ajax.sendQuery({
                    id: gallery.core.currentId,
                    tag: descr,
                    action: 'settag'
                }, 'json', function(data){
                    if(null != data){
                        gallery.core._setData(data);
                    }
                });
            });
            return false;
        });
        if(data.status == 'video' || data.status == 'youtube'){
            //video
            var $ul = $('<ul class="ul-player" />'),
            pl = ['youTube','dleOld','dleNew'];
            if(!data.other_dat.default_player){
                data.other_dat.default_player = '2';
            }
            for(var key in pl){
                var _class = (data.other_dat.default_player == key) ? 'active': '';
                var   $li = $('<li class="b-4 '+_class+' "><a href="javascript:void(0)" rel="'+key+'" class="'+pl[key]+'"></a><p>'+gallery.lang.player[pl[key]]+'</p></li>');
                $ul.append($li);
            }
            $ul.children('li').click(function(){
                //set -|- update default player
                var id = $(this).children('a').attr('rel');
                $ul.children('li').removeClass('active');
                $(this).addClass('active');
                data.other_dat.default_player = id;
                gallery.player.init(data);

            });

            var $inpVp = $('<button>'+ gallery.lang.setting.titlePlayer +'</button>');
            obj.append($inpVp);
            $($inpVp).button({
                text: true,
                icons: {
                    primary: "ui-icon-video"
                }
            }).click(function() {
                param.height = 340;
                param.width = 300;
                gallery.ass.openPopUpBlock('html', obj, param, null,function(){
                    //set -|- update default_player
                    gallery.ajax.sendQuery({
                        id: gallery.core.currentId,
                        default_player: data.other_dat.default_player,
                        action: 'setplayer'
                    }, 'json', function(data){
                        if(null != data){
                            gallery.core._setData(data);
                        }
                    });
                },
                $ul);
                return false;
            });
            var $html = $('<ul class="ul-cover" />');
            var $inpCo = $('<button id="title_">'+gallery.lang.setting.titleCover +'</button>');
            obj.append($inpCo);
            $($inpCo).button({
                text: true,
                icons: {
                    primary: "ui-icon-newwin"
                }
            }).click(function() {
                param.height = 560;
                param.width = 500;
                gallery.ass.openPopUpBlock('html', obj, param,function(){
                    $html = $('<ul class="ul-cover" />');
                    //------------
                    gallery.ass.cover = [];
                    var ID = data.id;
                    if(data.other_dat.other_dat)
                        if(data.other_dat.other_dat.video_preview){
                            _data = gallery.core.getData(ID);
                            for(var key in _data.other_dat.other_dat.video_preview){
                                var  __class = (_data.other_dat.other_dat.preview_id == key) ? 'active ' : '',
                                li = $('<li class="loading b-4 cover '+__class +'"/>');
                                var img = new Image;
                                img.src = gallery.core.setThumbPath(_data.other_dat.other_dat.video_preview[key]);
                                gallery.ass.cover[key] = img;
                                gallery.ass.coverLoad(key,li);
                                $html.append(li);
                                var h = $('<ul class="panel" />');
                                li.hover(function(){
                                    var id = $(this).children('img').attr('rel');
                                    var  l = $('<li />'),
                                    del = $('<a href="javascript:void(0)" class="delete"></a>').click(function(){
                                        $(this).parent().parent().parent().hide('fade')
                                        gallery.ajax.sendQuery({
                                            id: id,
                                            action: 'deletecover'
                                        }, 'json', function(data){
                                            if(null != data){
                                                gallery.core._setData(data);
                                            }
                                        });
                                    }), add = $('<a href="javascript:void(0)" class="add"></a>').click(function(){
                                        $(this).parent().parent().parent().parent().children('li').removeClass('active');
                                        $(this).parent().parent().parent().addClass('active');
                                        gallery.ajax.sendQuery({
                                            id: id,
                                            action: 'setvideocover'
                                        }, 'json', function(data){
                                            if(null != data){
                                                gallery.core._setData(data);
                                                var cdata =  gallery.core.getData(ID);
                                                gallery.player.init(cdata);
                                            }
                                        });
                                    }),ll = l.clone() ;
                                    h.html('');
                                    h.append(l.html(del),ll.html(add));
                                    $(this).append(h);
                                },function(){
                                    h.remove();
                                })
                            }
                        }
                    var $li = $('<li class="uploadify" />'),
                    inp = $('<input type="file" id="uploadifyCover" />'),
                    fq = $('<div id="fileQueueCover" syle="float:right;" />')
                    cdata = data;
                    $li.append(inp,fq);
                    $html.append($li);
                    $('#'+gallery.ass.popupId).html($html);
                    gallery.upload.setPluginVideoCover('uploadifyCover', function(event,data){
                        $('#'+gallery.ass.popupId).remove()
                        gallery.ajax.sendQuery({
                            id: cdata.parent_id,// album id
                            action: 'updateAlbom'
                        }, 'json', function(data){
                            gallery.core._setData(data);

                        });
                    });
                },function(){
                    //set -|- update description
                    gallery.ajax.sendQuery({
                        id: gallery.core.currentId,
                        descr: descr,
                        action: 'setdescription'
                    }, 'json', function(data){
                        if(null != data){
                            gallery.core._setData(data);
                        }
                    });
                }, $html);
                return false;
            });
        }
    },
    coverLoad: function(i,o){
        if(gallery.ass.cover[i].complete){
            $(gallery.ass.cover[i]).attr('rel',i)
            $(o).removeClass('loading').html(gallery.ass.cover[i]);
        }else{
            setTimeout(function(){
                gallery.ass.coverLoad(i,o);
            },200)
        }
    },
    setLabel: function(){
        gallery.labelEdit.init();
    },
    setSwitch:function(l,i,v,obj){
        var ch = (v) ?' checked="checked" ':'';
        var tpl = '<p class="switch">\
                     <label class="label">'+l+'</label>\
                     <label for="'+i+'" class="on"></label>\
                     <label for="'+i+'" class="off"></label>\
                     <input type="checkbox" '+ch+'name="'+i+'" id="'+i+'" value="'+v+'" />\
                  </p>';
        $(obj).append(tpl);
    },
    checkBoxDecorator: function(){
        $('p.switch').each(function(){
            var v = $(this).children('input[type="checkbox"]').val();
            if(v == 1){
                $(this).children('label.on').addClass('active');
            }else{
                $(this).children('label.off').addClass('active');
            }
            $(this).children('label.on, label.off').click(function(){
                var cv = $(this).parent('p.switch').children('input[type="checkbox"]').val();
                if(cv == 1){
                    $(this).removeClass('active').next('label.off').addClass('active')
                    .parent('p.switch').children('input[type="checkbox"]').val(0);
                }else if(cv == 0){
                    $(this).removeClass('active')
                    $(this).parent('p.switch').children('label.on').addClass('active')
                    $(this).parent('p.switch').children('input[type="checkbox"]').val(1);
                }
            });
        });
    },
    setPopupField: function(l,obj,callback){
        var tpl = $('<p class="switchPopup b-4"><label>'+l+'</sabel></p>')
        .click(function(){
            if(typeof callback == 'function'){
                callback(this);
            }
        });
        $(obj).append(tpl)
    },
    openPopUpBlock: function(type,obj,param,callback,callbackButton,html){
        $('#'+this.popupId).remove();
        var data = gallery.core.getData(gallery.core.currentId);
        if(!data){
            data =  gallery.core.getData(gallery.core.currentId);
            console.log('dddd');
            console.log(data);
            return;
        }
        this.popupId = gallery.core.getRand();
        var of = $(obj).offset();
        var element= '<div id="'+this.popupId+'">';
        var title = '';
        switch (type) {
            case 'text':
                title = gallery.lang.setting.titlePopup;
                element +='<input class="popupInputText b-4" type="text" value="'+data.title+'" id="title_'+data.id+'" /></div>';
                break;
            case 'textarea':
                title = gallery.lang.setting.titleDescr;
                element +='<textarea id="descr_'+data.id+'" class="jwysiwyg">'+data.descr+'</textarea>';
                break;
            case 'tags':
                title = gallery.lang.setting.titleTag;
                var tag = (data.other_dat.tag) ? data.other_dat.tag : '';
                element +='<fieldset id="tagList"><legend>'+gallery.lang.setting.titleTags+'</legend>'
                element +='<textarea id="tag_'+data.id+'" class="keywords">'+tag+'</textarea></fieldset>';
                break;
            case 'html':
                if(typeof html == 'string'){
                    element += html;
                }

                break;
            default:
                break;
        }
        var b = {};
        b[gallery.lang.apply] =function(){
            if(callbackButton)
                if(typeof callbackButton == 'function'){
                    callbackButton()
                }
            $(this).dialog("close");
        // $(this).dialog("destroy");
        };
        b[gallery.lang.cancel] = function(){
            $(this).dialog("close");
        // $(this).dialog("destroy");
        };
        $('body').append(element);
        if(typeof html == 'object'){
            $('#'+this.popupId).html(html);
        }
        $('#'+this.popupId).dialog({
            resizable: false,
            buttons: b,
            title: title,
            height: param.height,
            width: param.width,
            open: function(event, ui) {
                if(typeof callback == 'function'){
                    callback();
                }
            },
            close: function(event, ui){}
        })
    },
    closePopup: function(){
        $('#'+this.popupId).fadeOut('fast', function(){
            $(this).remove();
        })
    },
    InitWYSIWYG: function () {
        if(false == this.wysiwyg){
            return;
        }
        $('textarea.jwysiwyg').tinymce({
            theme : "advanced",
            skin : "cirkuit",
            language : "ru",
            width : "100%",
            height : "100",
            plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,iespell,inlinepopups,insertdatetime,preview,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
            relative_urls : false,
            convert_urls : false,
            media_strict : false,
            dialog_type : 'window',
            extended_valid_elements : "div[align|class|style|id|title]",
            theme_advanced_buttons1 : "formatselect,|,fontselect,|,fontsizeselect,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,cut,copy,|,fullscreen",
            theme_advanced_buttons2 :"bullist,numlist,|,forecolor,backcolor,|,removeformat,cleanup,|,link,unlink,|,undo,redo,code,",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : true,
            plugin_insertdate_dateFormat : "%d-%m-%Y",
            plugin_insertdate_timeFormat : "%H:%M:%S",
            theme_advanced_resize_horizontal : false
        });
    },
    setTinymce: function(obj){
        if(false == this.wysiwyg){
            return;
        }
        $(obj).tinymce({
            theme : "advanced",
            skin : "cirkuit",
            language : "ru",
            width : "100%",
            height : "100",
            plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,iespell,inlinepopups,insertdatetime,preview,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
            relative_urls : false,
            convert_urls : false,
            media_strict : false,
            dialog_type : 'window',
            extended_valid_elements : "div[align|class|style|id|title]",
            theme_advanced_buttons1 : "formatselect,|,fontselect,|,fontsizeselect,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,cut,copy,|,fullscreen",
            theme_advanced_buttons2 :"bullist,numlist,|,forecolor,backcolor,|,removeformat,cleanup,|,link,unlink,|,undo,redo,code,",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : true,
            plugin_insertdate_dateFormat : "%d-%m-%Y",
            plugin_insertdate_timeFormat : "%H:%M:%S",
            theme_advanced_resize_horizontal : false
        });
    },
    setListTag: function(obj){
        var data = gallery.core.getData(gallery.core.currentId);
        var a = [],b=[];
        if(typeof data.tag == 'object'){
            for(var k in data.tag){
                a.push('<a href="javascript:" rel="'+data.tag[k]+'">'+data.tag[k]+'</a>');
                this.keyword.push(data.tag[k]);
            }
        }
        if(typeof data.newtag == 'object'){
            for (var j in data.newtag){
                b.push('<a href="javascript:" rel="'+data.newtag[j]+'">'+data.newtag[j]+'</a>');
            }
        }
        $('#newTag').append(b.join(','));
        $('#tagList').append(a.join(','));
    },
    setNav: function(obj){
        if(null == obj){
            return;
        }
        var str = $('<ul />'),help = $('<ul class="help"></ul>');
        str.append('<li class="group"><span>'+obj.group['title']+'</span></li>')
        for(var i in obj.group){
            if(i !='title'){
                str.append('<li><a href="'+obj.group[i].href+'" rel="'+i+'">'+obj.group[i].title+'</a></li>');
            }
        }
        for(var j in obj.info){
            if(j !='title'){
                help.append('<li><a href="javascript:" rel="'+j+'">'+obj.info[j]+'</a></li>');
            }else{
                help.append('<li class="help"><span>'+obj.info[j]+'</span></li>');
            }
        }
        help.children('li').children('a').click(function(){
            var action = $(this).attr('rel');

        });
        var wrap = $('<div ></div>').css({
            display:'none'
        }).append(str,help);
        $('div#sysnav').html('').append(wrap);
        wrap.show("blind",{},1000)
    },
    setCat: function(obj){
        var str = $('<ul class="cat"></ul>');
        str.append('<li class="cat"><span>'+obj['title']+'</span></li>');
        for(var i in obj){
            if(i !='title'){
                str.append('<li><a href="javascript:" rel="'+i+'">'+obj[i].title+'</a></li>');
            }
        }
        var wrap = $('<div ></div>').css({
            display:'none'
        }).append(str);
        $('div#sysnav').html('').append(wrap);
        wrap.show("blind",{},1000)
    },
    setNavPatr: function(){
        $('#side-bar').children('ul.sysnav').children('li').click(function(){
            var action = $(this).children('a').attr('rel');
            gallery.ass.setNavContent(action);
            $('#side-bar').children('ul.sysnav').children('li').removeClass('active');
            $(this).addClass('active');
        });
    },
    setNavContent: function(a) {
        switch (a) {
            case 'system':
                $('#sysnav').removeClass('hidden');
                $('#tree').addClass('hidden');
                gallery.ass.setNav(nav);
                break;
            case 'category':
                $('#tree').removeClass('hidden');
                break;
            case 'tender':
                break;
            default:
                break;
        }

    },
    _selectDecorator: true,
    selectDecorator: function(element){
        if(false ===this._selectDecorator){
            return;
        }
        $(element).each(function(){
            var select=$(this);
            var name=select.attr('name');
            if(select.hasClass('Gselect')){
                return;
            }
            if($(this).parent().parent().parent().parent().parent().parent().parent().css('display') == 'none'){
                return;
            }
            if(select.attr('multiple')){
                gallery.ass.multipleSelectDecorator(this);
                return;
            }
            var labeltext= '';
            var wrap=select.addClass('Gselect').css({
                display:'none'
            }).wrap('<div class="GselectBox"></div>'),
            width = select.width()+ 25
            wrap.parent().css({
                width: width
            });
            wrap.parent().prepend('<span id="label_'+name+'" class="GselectLabel">'+labeltext+'</spn>','<div class="GselectOptions"></div><span class="GselectArrow"></span>');
            var ul=$('<ul class="GselectList"></ul>')
            var selected='';
            $('option',select).each(function(i){
                if($(this).attr('selected')){
                    selected=$(this).html();
                    ul.append('<li><a href="javascript:" id="'+i+'" class="GselectOptionActive">'+$(this).html()+'</a></li>')
                }else{
                    ul.append('<li><a href="javascript:" id="'+i+'">'+$(this).html()+'</a></li>')
                }
            });
            $('.GselectOptions',wrap.parent()).css({
                width: (width + 25)
            }).hide()
            $('.GselectOptions',wrap.parent()).append(ul);
            wrap.parent().append('<span class="GselectOptionSelected">'+selected+'</span>');
            wrap.parent().hover(function(){
                var box=$('.GselectOptions',this);
                box.fadeIn('fast');
            },function(){
                $('.GselectOptions',this).fadeOut('fast')
            });
            $('.GselectOptions > ul > li',wrap.parent()).click(function(){
                var i=$('a',this).attr('id');
                $('option',select).removeAttr('selected');
                var so=$('option',select).eq(i);
                so.attr('selected','selected');
                $('.GselectOptionSelected',$(this).parent().parent().parent()).html(so.html());
                $('li > a',$(this).parent()).removeClass('GselectOptionActive');
                $('li',$(this).parent()).each(function(){
                    var I=$('a',this).attr('id');
                    if(I==i){
                        $('a',this).addClass('GselectOptionActive')
                    }
                })
                $('.GselectOptions',wrap.parent()).fadeOut('fast');
                if(select.attr('onchange'))
                    select.change();
            })
        })
    },
    multipleSelectCallback: null,
    multipleSelectDecorator: function(obj){
        var id = $(obj).attr('name');
        if(document.getElementById(id)){
            return ;
        }
        var aG = $('<div class="access-granted b-4" id="'+id+'"><span></span><div></div></div>'),
        aD = $('<div class="access-denied b-4" id="'+id+'"><span></span><div></div></div>');
        $(obj).children('option').each(function(){
            var idelement = gallery.core.getRand();
            var text = $(this).html(),
            element = $('<a href="javascript:" rel="'+$(this).index()+'" id="'+idelement+'" ><b>'+text+'</b></a>').click(function(){
                //var selected = $(obj).children('option').eq($(this).attr('rel')).attr('selected');
                var opt = $(obj).children('option').eq($(this).attr('rel'));
                if(false == opt[0].selected){
                    if(typeof  gallery.ass.multipleSelectCallback == 'function'){

                    }
                    $(obj).children('option').eq($(this).attr('rel')).attr('selected',true);
                    aG.children('div').append(this);
                }else{
                    if(typeof  gallery.ass.multipleSelectCallback == 'function'){

                    }
                    $(obj).children('option').eq($(this).attr('rel')).attr('selected',false);
                    aD.children('div').append(this);
                }
                if(typeof gallery.ass.multipleSelectCallback == 'function')
                    gallery.ass.multipleSelectCallback($(obj));
            });
            if($(this).attr('selected')){
                aG.children('div').append(element);
            }else{
                aD.children('div').append(element);
            }
        })
        $(obj).parent().show().append(aG,aD);
        $(obj).hide();
        gallery.eff.fadeIn(aG, 1000);
        gallery.eff.fadeIn(aD, 1000);
    },
    checkBoxWrap: function(obj){
        $(obj).each(function(){
            var value = ($(this).attr('checked'))? 1 : 0;
            gallery.ass.setSwitch('', $(this).attr('name'), value, $(this).parent());
            $(this).remove();
        })
        gallery.ass.checkBoxDecorator();
    },
    initSideBar: function(obj,active,callback){
        for(var k in obj){
            var classActive = (obj[k] == active )?'class="selected"':'';
            var item = $('<li '+classActive+' role="'+k+'"><a href="javascript:" class="'+obj[k]+'" rel="'+obj[k]+'"></li>');
            if(typeof callback !='function'){
                item.click(function(){
                    $('#work-area-side-bar').children('ul.nav').children('li').removeAttr('class');
                    $(this).addClass('selected');
                    var index = $(this).attr('role');
                    var element = null,form = null;
                    if(document.getElementById('ajax-content')){
                        form = $('#work-area').children('div').children('form');
                        element = form.children('div').eq(index);
                    }else{
                        form =  $('#work-area').children('form');
                        element = form.children('div').eq(index);
                    }
                    if(element.css('display') === 'block'){
                        return;
                    }
                    form.children('div').hide();
                    element.show();
                    var _height = $(element).outerHeight(true);
                    _height = (_height < 580) ? 580 : _height;
                    gallery.core.userAreaResize(_height, 50,null)
                    //setLabel
                    var attr = $(this).children('a').attr('rel');
                    if('open' == attr){
                        $('#LABEL-PANEL').remove();
                        gallery.ass.setLabel();
                        gallery.core.init()
                    }else{
                        $('#LABEL-PANEL').remove();
                        gallery.core._destroy()
                    }
                    gallery.ass.selectDecorator($('select'))
                });
            }else{
                item.click(function(){
                    callback(this);
                })
            }
            $('#work-area-side-bar').children('ul.nav').append(item)
        }
    },
    setContextMenu: function(obj,elements){
        var id = gallery.core.getRand();
        $('body').append('<ul id="'+id+'" class="contextMenu"></ul>');
        for(var key in obj){
            $('#'+id+'').append( $('<li><a href="#'+key+'"><span class="'+key+'">'+obj[key]+'</span></a></li>'))
        }
        this.contextMenu(elements,id);
    },
    contextMenu:function(obj,id){
        $(obj).contextMenu({
            menu:id,
            leftButton:false
        },function(action,el,pos){
            var id = $(el).attr('id');
            if($(el).parent().hasClass('albom')){
                id = $(el).attr('rel');
                action = 'deleteAlb';
            }
            gallery.ass.contextMenuWork(action,id,pos);
        });

        return false;
    },
    contextMenuWork:function(action,id,pos){
        var aObj = {};
        switch(action){
            case"add":
            case 'update':
                aObj.type = 'json';
                if(id == 'meta_keywords'){
                    aObj.action = 'addKeyword';
                }else if(id == 'meta_descr'){
                    aObj.action = 'addDescr';
                }
                aObj.text = $('textarea[name="config[descr]"]').html();
                gallery.ajax.sendQuery(aObj, 'json', function(data){
                    if(null != data)
                        if(data.descr){
                            $('textarea#'+id).html(data.descr)
                            $('textarea#'+id).val(data.descr)
                        }
                });
                break;
            case 'w':
                gallery.ass. setTinymce($('textarea#'+id));
                break;
            case 'editCat':
                document.location.href = admin +'&action=editCat&id='+id;
                break;
            case 'deleteCat':
                gallery.ajax.sendQuery({
                    action: 'deletecat',
                    id: id
                }, 'html', function(data){
                    document.location.href = admin;
                });
                break;
            case 'deleteAlb':
                gallery.ajax.sendQuery({
                    action: 'deletealbom',
                    id: id
                }, 'html', function(data){
                    document.location.href = admin;
                });
                break;
        }
    },
    setPager: function(total, _page ){
        var page = Math.floor((total / 20)) + 1,
        $ul = $('<ul class="pager" />');
        if(page == 0){
            $('#pager').html('');
        }
        for(var i= 0; i < page; i++){
            var $li = $('<li class="page"/>'),
            p = (i + 1),
            $a = $('<a href="javascript:void(0)" rel="'+p+'" >'+ p + '</a>');
            if(p ==  _page){
                $a.addClass('active');
            }else{
                $a.click(function(){
                    pager(this);
                });
            }
            $ul.append($li.html($a));
        }
        $('#pager').html($ul);
    },
    setDatePicker: function($obj){
        $('.date').click(function(){
            $(this).datepicker();
        })
        $('.date').datepicker($.datepicker.regional['ru']);

    }
}
var pager = function(obj){
    var i = $(obj).attr('rel');
    gallery.ajax.sendQuery({
        action: 'loadfile',
        page: i,
        history: userSearch
    }, 'json',function(data){
        if(data ){
            $('#ajax-content-user').html(data.tpl);
            gallery.ass.setPager(data.count, data.page);
        }else{
            $('#ajax-content-user').html('<div class="error b-4"><p>empty result</p></div>');
        }
    });
}