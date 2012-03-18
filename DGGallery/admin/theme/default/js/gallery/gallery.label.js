gallery.labelEdit = {
    jCropPlugin: null,
    state: false,
    edit: false,
    newLabel: {},
    toolbarCange: true,
    initToolbarLabel: false,
    toolbar:[],
    labels: {},
    scrBlock: null,
    defaultView: 'circle',
    init: function(){
        //delete old object
        gallery.labelEdit.clearLabel();
        gallery.labelEdit._destroy();
        gallery.labelEdit.initToolbarLabel = false;
        $('.label-menu').remove();
        var data = gallery.core.getData(gallery.core.currentId);
        //check image labels
        if(data){
            if(data.other_dat)
                if(typeof data.other_dat.label == 'object'){
                    //create new object
                    console.log(data.other_dat.label_status == 1)
                    gallery.labelEdit.labels = data.other_dat.label ;
                    gallery.labelEdit.setToolbar(function(){
                        gallery.labelEdit.clearLabel();
                    });

                }else{
                    $('.GalleryLabelPanel').remove();
                }
        }
        gallery.labelEdit.setPlugin();
    },
    setPlugin: function(){
        var data = gallery.core.getData(gallery.core.currentId);
        if(data)
            if(data.other_dat)
                if( data.other_dat.label_status == 1){
                    gallery.eff.callback = function(obj){
                        gallery.labelEdit.state = true;
                        gallery.labelEdit.jCropPlugin = $.Jcrop(obj,{
                            aspectRatio:0,
                            onChange:updateCoords,
                            onSelect:updateCoords
                        });
                    }
                }else{
                    gallery.eff.callback = function(obj){
                        return;
                    }
                }
        $('#set_label').change(function(){
            var v =  (document.getElementById('set_label').checked) ? 1 : 0;
            if(v == 1){
                if(false ===  gallery.labelEdit.state){
                    //  gallery.labelEdit._destroy();
                    if(null === gallery.labelEdit.jCropPlugin){
                        gallery.labelEdit.state = true;
                        gallery.labelEdit.jCropPlugin = $.Jcrop(gallery.core.obj.image[gallery.core.currentId],{
                            aspectRatio:0,
                            onChange:updateCoords,
                            onSelect:updateCoords
                        });
                    }
                }
            }else if(v == 0){
                gallery.labelEdit.state = false;
                gallery.labelEdit._destroy();
                $('div#view-image').html(gallery.core.obj.image[gallery.core.currentId]);

            }
            var data = {
                action: 'labelFile',
                id: gallery.core.currentId,
                set: v
            };
            gallery.ajax.sendQuery(data, 'json', null);
        });
    },
    _setPlugin: function(){
        gallery.labelEdit.jCropPlugin = $.Jcrop(gallery.core.obj.image[gallery.core.currentId],{
            aspectRatio:0,
            onChange:updateCoords,
            onSelect:updateCoords
        });
    },
    _destroy: function(){
        if(gallery.labelEdit.jCropPlugin){
            gallery.labelEdit.jCropPlugin.destroy();
            gallery.labelEdit.jCropPlugin = null;
            gallery.labelEdit.state = false;
        }
        $('.ImageLabel').removeClass('ImageLabelActive');
        $('.jcrop-holder').remove();
    //        $('#set_label').val('0');
    //        var p = $($('#set_label').parent('p')) ;
    //        p.children('label.on').removeClass('active');
    //        p.children('label.off').addClass('active');
    },
    setToolbar:function(callback){
        if(false == gallery.core.param.label.panel){
            return;
        }
        if(typeof(callback) == 'function' || gallery.labelEdit.callback == null){
            gallery.labelEdit.callback = callback;
        }
        gallery.labelEdit.callback();
        gallery.labelEdit.initToolbar();
        $(window).scroll(function(){
            $('.tipsy').remove();
        })
    },
    initToolbar:function(){
        var self =  gallery.labelEdit;
        if(false == gallery.core.param.label.panel || self.initToolbarLabel){
            return;
        }

        if(0 ===  gallery.core.getDataLeght(self.labels)){
            return;
        }

        if(gallery.labelEdit.defaultView == 'circle'){
            //self.initToolbarCircle();
            console.log('initToolbarCircle()')
        //   return ;
        }

        var div =  $('<div />'),
        btn   =  div.clone(),
        wrap,
        cont,
        rPos = false;

        self.toolbar['change'] =  $(div.clone()),
        wrap =  $(div.clone());
        self.toolbarPos= 'bottom';
        if(self.toolbarCange){
            rPos = gallery.core.getCookie('toolbpos');
            switch(rPos){
                case'top':
                    self.toolbarPos='top';
                    break;
                case'left':
                    self.toolbarPos='left';
                    break;
                case'right':
                    self.toolbarPos='right';
                    break;
                default:
                    self.toolbarPos= 'bottom';
                    break;
            }
        }
        self.setToollbarCss();
        cont = self.setTollbarLabel();
        wrap.append(cont).attr('id','toolbarWrapper').css(self.toolbar['cssW']);
        div.html(wrap)
        .css(self.toolbar['css'])
        .addClass('GalleryLabelPanel')
        .attr('id','LABEL-PANEL');
        $('body').append(div);
        self.getToolbarParam();
        cont.css(self.toolbar['contCss']);
        $.fn.tipsy.defaults={
            delayIn:0,
            delayOut:10,
            fade:true,
            fallback:'',
            html:false,
            live:false,
            offset:0,
            opacity:0.8,
            title:'title',
            trigger:'hover',
            gravity: self.toolbar['gravity']
        };

        cont.children('span').tipsy({
            title:'rel'
        });
        if(self.toolbarCange){
            div.append(self.setToolbarButton(rPos));
        }
        self.setSlidePanel(div,self.toolbar['contWaxMargin']);
        if(self.addLebel ==true ){
            $(btn).addClass('labelAddEnable').attr('title',gallery.lang.label.label_on).click(function(){
                Gallery.workvar.labelAdd=true;
                Gallery.param.plugin.jCrop.destroy();
                Gallery.param.plugin.jCrop=Gallery.$this.Jcrop(gallery.core.obj.image[gallery.core.currentId],{
                    aspectRatio:0,
                    onChange:updateCoords,
                    onSelect:updateCoords
                });
            });
        }else{
            if(self.addLebel=='nologged'){
                $(btn).addClass('labelAddDisable').attr('title',gallery.lang.label.label_off_);
            }else{
                $(btn).addClass('labelAddDisable').attr('title',gallery.lang.label.label_off);
            }
        }
        btn.css({
            position:'absolute',
            left:3+'px',
            top:5+'px'
        }).tipsy();
        div.append(btn);
        self.showToolbar(div);
        self.toolbar=new Array();
        self.initToolbarLabel = true;
    },
    initToolbarCircle: function(){
        var img = gallery.core.obj.image[gallery.core.currentId];

    },
    setToollbarCss:function(){
        if(false == gallery.core.param.label.panel){
            return ;
        }
        var css=[], self = gallery.labelEdit;
        css['position']='fixed';
        css['opacity']=0.00;
        self.toolbar['css1']=[];
        self.toolbar['css']=[];
        self.toolbar['cssW']=[];
        self.toolbar['css']['opacity']='0.00';
        self.toolbar['css']['position']='fixed';
        switch(self.toolbarPos){
            case'top':
                self.toolbar['css1']['right']=5+'px';
                self.toolbar['css1']['top']=0+'px';
                self.toolbar['css']['top']=0+'px';
                self.toolbar['css']['width']=80+'%';
                self.toolbar['css']['height']=60+'px';
                self.toolbar['css']['left']=10+'%';
                self.toolbar['cssW']['position']='absolute';
                self.toolbar['cssW']['left']=70+'px';
                self.toolbar['cssW']['right']=80+'px';
                self.toolbar['cssW']['top']=5+'px';
                self.toolbar['cssW']['overflow']='hidden';
                break;
            case'bottom':
                self.toolbar['css1']['right']=5+'px';
                self.toolbar['css1']['top']=0+'px';
                self.toolbar['css']['width']=80+'%';
                self.toolbar['css']['height']=60+'px';
                self.toolbar['css']['bottom']=0+'px';
                self.toolbar['css']['left']=10+'%';
                self.toolbar['cssW']['position']='absolute';
                self.toolbar['cssW']['left']=70+'px';
                self.toolbar['cssW']['right']=80+'px';
                self.toolbar['cssW']['top']=5+'px';
                self.toolbar['cssW']['overflow']='hidden';
                break;
            case'left':
                self.toolbar['css1']['right']=5+'px';
                self.toolbar['css1']['bottom']=0+'px';
                self.toolbar['css']['top']=10+'%';
                self.toolbar['css']['width']=60+'px';
                self.toolbar['css']['height']=80+'%';
                self.toolbar['css']['left']=0+'px';
                self.toolbar['cssW']['position']='absolute';
                self.toolbar['cssW']['overflow']='hidden';
                self.toolbar['cssW']['top']=70+'px';
                self.toolbar['cssW']['bottom']=80+'px';
                self.toolbar['cssW']['right']=5+'px';
                break;
            case'right':
                self.toolbar['css1']['right']=5+'px';
                self.toolbar['css1']['bottom']=0+'px';
                self.toolbar['css']['top']=10+'%';
                self.toolbar['css']['width']=60+'px';
                self.toolbar['css']['height']=80+'%';
                self.toolbar['css']['right']=0+'px';
                self.toolbar['css']['backgroundPosition']='100% 0';
                self.toolbar['cssW']['position']='absolute';
                self.toolbar['cssW']['overflow']='hidden';
                self.toolbar['cssW']['top']=70+'px';
                self.toolbar['cssW']['bottom']=80+'px';
                self.toolbar['cssW']['right']=5+'px';
                break;
        }
    },
    setTollbarLabel:function(){
        if(false == gallery.core.param.label.panel ||  gallery.labelEdit.initToolbarLabel){
            return false;
        }

        var Hlabel = $('<span />'),
        cont = $('<div />'),x,y,w,h,rx,ry,
        img =  gallery.core.obj.image[gallery.core.currentId],
        self =  gallery.labelEdit;
        self.toolbar['cont'] = cont;
        self.toolbar['author'] = [];

        if(typeof self.labels == 'object')
            for(var key in self.labels){
                if(self.labels[key].coords){
                    var l  =  $(Hlabel.clone());
                    var cloneImg = $(img).clone();
                    l.attr('id',key);
                    x=parseInt(self.labels[key].coords.x);
                    y=parseInt(self.labels[key].coords.y);
                    w=parseInt(self.labels[key].size.w);
                    h=parseInt(self.labels[key].size.h);
                    rx=50/w;
                    ry=50/h;

                    $(cloneImg).css({
                        height: Math.round(ry * gallery.core.param.preview.size.y)+ 'px',
                        width: Math.round(rx * gallery.core.param.preview.size.x)+ 'px',
                        marginLeft:'-'+Math.round(rx*x) + 'px',
                        marginTop:'-'+Math.round(ry*y) + 'px',
                        display:'block'
                    });
                    l.addClass('ImageLabel')
                    .attr('rel',self.labels[key].text)
                    .html(cloneImg);
                    //Gallery.currentLabel[strL[0]]=[x,y,(w+x),(h+y)];
                    cont.append(l);
                    l.click(function(){
                        var key = $(this).attr('id');
                        if(null == gallery.labelEdit.jCropPlugin){
                            gallery.labelEdit._setPlugin();
                        }
                        gallery.labelEdit.jCropPlugin.setOptions({
                            allowResize:false,
                            allowMove:false
                        });
                        gallery.labelEdit.jCropPlugin.disable();
                        $('span.ImageLabel').not(this).removeClass('ImageLabelActive');
                        $(this).addClass('ImageLabelActive');
                        var l = self.labels[key];

                        gallery.labelEdit.jCropPlugin.animateTo([l.coords.x,l.coords.y,l.coords.x2,l.coords.y2]);
                        $('.jcrop-tracker').one('click',function(){
                            gallery.labelEdit._destroy();
                        })

                        return false;
                    })
                    gallery.labelEdit.setContextMenu(l);
                }

            }
        cont.attr('id','toolBarCont');

        return cont;
    },
    getToolbarParam:function(){
        var id = ['toolBapLabel','#toolbarWrapper','#toolBarCont'], self = gallery.labelEdit;
        self.toolbar['contWidth']=(52*$(id[2]).children('span').length);
        self.toolbar['wrap']=[];
        self.toolbar['contCss']=[];
        switch(self.toolbarPos){
            case'top':
                self.toolbar['wrap']['x'] =$(id[1]).outerWidth(true);
                self.toolbar['contWaxMargin']=self.toolbar['wrap']['x']-self.toolbar['contWidth'];
                self.toolbar['contCss']['width']=self.toolbar['contWidth'];
                self.toolbar['contCss']['height']='50px';
                self.toolbar['gravity']='n';
                break;
            case'bottom':
                self.toolbar['wrap']['x']=$(id[1]).outerWidth(true);
                self.toolbar['contWaxMargin']=self.toolbar['wrap']['x']-self.toolbar['contWidth'];
                self.toolbar['contCss']['width']=self.toolbar['contWidth'];
                self.toolbar['contCss']['height']='50px';
                self.toolbar['gravity']='sw';
                break;
            case'left':
                $('#LABEL-PANEL').addClass('GalleryLabelPanelLR');
                self.toolbar['wrap']['x']=$(id[1]).outerHeight(true);
                self.toolbar['contWaxMargin']=self.toolbar['wrap']['x']-self.toolbar['contWidth'];
                self.toolbar['contCss']['height']=self.toolbar['contWidth'];
                self.toolbar['contCss']['width']='50px';
                self.toolbar['gravity']='w';
                break;
            case'right':
                $('#LABEL-PANEL').addClass('GalleryLabelPanelLR');
                self.toolbar['wrap']['x']=$(id[1]).outerHeight(true);
                self.toolbar['contWaxMargin']=self.toolbar['wrap']['x']-self.toolbar['contWidth'];
                self.toolbar['contCss']['height']=self.toolbar['contWidth'];
                self.toolbar['contCss']['width']='50px';
                self.toolbar['gravity']='e';
                break;
            default:
                break;
        }
    },
    setSlidePanel:function(div,mw){
        var btnPrev = $('<div />'),
        btnNext=  $(btnPrev.clone()),
        css1=new Array(),
        css2=new Array(),
        self =  gallery.labelEdit;
        if(self.toolbarPos=='top' || self.toolbarPos=='bottom'){
            css1['top']=18+'px';
            css1['left']=52+'px';
            css1['backgroundPosition']='0 -16px';
            css2['top']=18+'px';
            css2['right']=62+'px';
            css2['backgroundPosition']='100% -16px';
        }else{
            css1['top']=52+'px';
            css1['right']=25+'px';
            css1['backgroundPosition']='-16px 0';
            css2['right']=25+'px';
            css2['bottom']=62+'px';
            css2['backgroundPosition']='-16px -32px';
        }
        btnPrev.addClass('labelSlidePrev').css(css1).mouseout(function(){
            clearInterval(self.scrBlock);
        }).mouseup(function(){

            clearInterval(self.scrBlock);
        }).mousedown(function(){
            self.scrollBlockR();
        })
        btnNext.addClass('labelSlideNext').css(css2).mouseup(function(){
            clearInterval(self.scrBlock);
        }).mouseout(function(){
            clearInterval(self.scrBlock);
        }).mousedown(function(){
            self.scrollBlock(mw);

        })
        $(div).append(btnPrev,btnNext);
    },
    setToolbarButton:function(r){
        var change =  $('<div />')  ,
        remember = $(change).clone(),
        self =  gallery.labelEdit,
        left= $(change.clone()),
        right=$(change.clone()),
        top= $(change.clone()),
        bottom=$(change.clone());
        top.attr('rel','top').addClass('toolbarTopPosition').css({
            left:16+'px'
        });
        bottom.attr('rel','bottom').addClass('toolbarBottomPosition').css({
            left:16+'px',
            bottom:0+'px'
        });
        right.attr('rel','right').addClass('toolbarRightPosition').css({
            top:16+'px',
            right:0+'px'
        });
        left.attr('rel','left').addClass('toolbarLeftPosition').css({
            top:16+'px'
        });
        if(self.toolbarPos=='left'){
            left.css({
                backgroundPosition:'0 -16px',
                cursor:'none'
            });
        }else{
            left.bind('click',function(){
                self.setToolbalPos($(this));
            }).attr('title',gallery.lang.label.left)
            if(!self.disableTipsyToolbarPos)
                left.tipsy();
        }
        if(self.toolbarPos=='right'){
            right.css({
                backgroundPosition:'100% -16px',
                cursor:'none'
            });
        }else{
            right.bind('click',function(){
                self.setToolbalPos($(this));
            }).attr('title',gallery.lang.label.right)
            if(!self.disableTipsyToolbarPos)
                right.tipsy();
        }
        if(self.toolbarPos=='top'){
            top.css({
                backgroundPosition:'-16px 0',
                cursor:'none'
            });
        }else{
            top.bind('click',function(){
                self.setToolbalPos($(this));
            }).attr('title',gallery.lang.label.top)
            if(!self.disableTipsyToolbarPos)
                top.tipsy();
        }
        if(self.toolbarPos=='bottom'){
            bottom.css({
                backgroundPosition:'-16px -32px',
                cursor:'none'
            });
        }else{
            bottom.attr('title',gallery.lang.label.bottom).bind('click',function(){
                self.setToolbalPos($(this));
            });
            if(!self.disableTipsyToolbarPos)
                bottom.tipsy();
        }
        if(!r){
            remember.addClass('toolbarRemember').css({
                left:16+'px',
                bottom:16+'px'
            }).bind('click',self.rememberPos);
        }else{
            $(remember).addClass('toolbarRememberA').css({
                left:16+'px',
                bottom:16+'px'
            }).bind('click',self.forgetPos);
        }
        $(change).append(right,top,left,bottom,remember).css(self.toolbar['css1']).addClass('toolbar');
        return change;
    },
    setToolbalPos:function(obj){
        if(obj===null){
            var s = gallery.core.getCookie('gallery_toolbar_position');
            if(s!=undefined){
                gallery.labelEdit.toolbarPos = s;
            }
        }else{
            gallery.labelEdit.toolbarPos = $(obj).attr('rel');
            gallery.core.setCookie('toolbpos', gallery.labelEdit.toolbarPos, 365);
            gallery.labelEdit.hideToolbar();
        }
    },
    scrollBlock:function(mw){
        var sw,
        self =  gallery.labelEdit;
        if(self.toolbarPos=='top'|| self.toolbarPos=='bottom'){
            gallery.labelEdit.scrBlock = setInterval(function(){
                var l = parseInt($('#toolBarCont').css('margin-left'),10);
                l= isNaN(l)?0:l;
                sw=l-50;
                if(sw < mw){
                    if(!$("div#toolBarCont").is(':animated')){
                        $('#toolBarCont').stop().animate({
                            marginLeft: mw +'px'
                        },100)
                    }
                    clearInterval(self.scrBlock);
                }else{
                    $('#toolBarCont').stop().animate({
                        marginLeft: sw
                    },10)
                }
            },20);
        }else{
            self.scrBlock=setInterval(function(){
                var l=parseInt($('div#toolBarCont').css('margin-top'));
                l=isNaN(l)?0:l;
                sw=l-50;
                if(sw<mw){
                    if(!$("div#toolBarCont").is(':animated')){
                        $('div#toolBarCont').stop().animate({
                            marginTop:mw+'px'
                        },100)
                    }
                    clearInterval(self.scrBlock);
                }else{
                    if(!$("div#toolBarCont").is(':animated')){
                        $('div#toolBarCont').stop().animate({
                            marginTop:sw+'px'
                        },10)
                    }
                }
            },20);
        }
    },
    scrollBlockR:function(){
        var sw , self =  gallery.labelEdit;
        if(self.toolbarPos=='top'|| self.toolbarPos=='bottom'){
            self.scrBlock=setInterval(function(){
                var l=parseInt($('#toolBarCont').css('margin-left'));
                l=isNaN(l)?0:l;
                sw=l+50;
                if(sw>0){
                    if(!$("div#toolBarCont").is(':animated')){
                        $('#toolBarCont').stop().animate({
                            marginLeft:0+'px'
                        },100)
                    }
                    clearInterval(self.scrBlock);
                }else{
                    if(!$("div#toolBarCont").is(':animated')){
                        $('#toolBarCont').stop().animate({
                            marginLeft:sw+'px'
                        },10)
                    }
                }
            },20);
        }else{
            self.scrBlock=setInterval(function(){
                var l=parseInt($('#toolBarCont').css('margin-top'));
                l=isNaN(l)?0:l;
                sw=l+50;
                if(sw>0){
                    if(!$("div#toolBarCont").is(':animated')){
                        $('#toolBarCont').stop().animate({
                            marginTop:0+'px'
                        },100)
                    }
                    clearInterval(self.scrBlock);
                }else{
                    if(!$("div#toolBarCont").is(':animated')){
                        $('#toolBarCont').stop().animate({
                            marginTop:sw+'px'
                        },10)
                    }
                }
            },20);
        }
    },
    scrollToLabel:function(i){
        var top=$(window).scrollTop();
        var s=top+$(window).height();
        if(i<top||s<(i)){
            $("html:not(:animated)"+(!$.browser.opera?",body:not(:animated)":"")).animate({
                scrollTop:i-150
            },500);
        }
    },
    showToolbar:function(obj){
        $(obj).animate({
            opacity:1.00
        },500,function(){

            });
    },
    hideToolbar:function(){
        $('.tipsy').remove();
        $('.GalleryLabelPanel').animate({
            opacity:0.00
        },500,function(){
            $(this).remove();
            gallery.labelEdit.initToolbarLabel = false;
            gallery.labelEdit.initToolbar();
        })
    },
    clearLabel:function(){
        $('span.img_label').remove();
    },
    destroyTollbar:function(){
        $('#LABEL-PANEL').remove();
        $('.tipsy').remove();
    },
    rememberPos:function(){
        gallery.core.setCookie('toolbpos',gallery.labelEdit.toolbarPos,{
            expires:864000
        });
        $(this).removeClass('toolbarRemember').addClass('toolbarRememberA').bind('click',gallery.labelEdit.forgetPos);
    },
    forgetPos:function(){
        gallery.core.deleteCookie('toolbpos');
        $(this).removeClass('toolbarRememberA').addClass('toolbarRemember').bind('click',gallery.labelEdit.rememberPos);
    },
    setContextMenu: function(elements){
        var id = gallery.core.getRand(),
        obj={
            "deleteLabel":"\u0443\u0434\u0430\u043b\u0438\u0442\u044c"
        }
        $('body').append('<ul id="'+id+'" class="contextMenu label-menu"></ul>');
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
            var id = $(el).attr('id')
            gallery.labelEdit.contextMenuWork(action,id,pos);
        });

        return false;
    },
    contextMenuWork:function(action,id,pos){
        $('#'+id).remove();
        var data = {
            action: 'deleteLabel',
            id: gallery.core.currentId,
            idL: id
        };


        gallery.ajax.sendQuery(data, 'json', null);

    }
}
function updateCoords(c){
    var rx=200/c.w;
    var ry=200/c.h;
    gallery.labelEdit.newLabel.coord = {};
    gallery.labelEdit.newLabel.coord  = c;
    $('#preview').css({
        width:Math.round(rx*800)+'px',
        height:Math.round(ry*600)+'px',
        marginLeft:'-'+Math.round(rx*c.x)+'px',
        marginTop:'-'+Math.round(ry*c.y)+'px'
    });
}

