var gallery ={};
var nav = null;

gallery.core = {
    data:{},
    setting: {
        elementRoot: '.images',
        elementRootS: 'part-open',
        mode: 'full',
        httpRoot: window.location.protocol + '//' + window.location.host
    },
    obj:{
        thumbs:[],
        image: [],
        prev: null,
        userArea:[]
    },
    ui: {
        slider:{}
    },
    param: {
        preview:{
            size:{}
        },
        label:{
            panel:true
        }
    },
    agent:{
        chrome: 'http://www.google.com/chrome/',
        opera: 'http://www.opera.com/',
        firefox: 'http://www.mozilla-europe.org/',
        safari: 'http://www.apple.com/safari/'
    },
    currentId: 0,
    currentEff: 'none',
    mode:['tree','list','slider','prev','full'],
    init:function(){
        this.obj.userArea  = this.getSize($('#user-work-area'));
        if(!gallery.core.getDataLeght(this.data)){
            $('#'+this.setting. elementRootS).append('<div class="info b-4"><p>'+gallery.lang.error.empty_albom+'</p></div>');
            return;
        }
        if($.browser.msie && $.browser.version < 9.0){
            $('#'+this.setting. elementRootS).append('<div class="error b-4"><p>'+gallery.lang.error.old_agent+'</p></div>');
            $('#'+this.setting. elementRootS).append('<div class="error b-4"><p>'+gallery.lang.error.new_agent+'</p></div>');
            var  $div = $('<div id="download"/>');
            for(var b in this.agent){
                $div.append('<a href="'+this.agent[b]+'"  target="_blank" class="download-' + b +'"></a>')
            }
            $('#'+this.setting. elementRootS).append($div)
            return;
        }
        //        if(0 === check){
        //
        //        }
        //        this.obj.userArea[3] height
        switch (this.setting.mode) {
            case 'full':
                this.setCss('full', {
                    height: 550
                },400);

                $('#'+this.setting.elementRootS).append(this.createThumbList('bottom'));
                this.setSliderJqueryUi('bottom');
                if(this.data == null){
                    $('#'+this.setting. elementRootS).append('<div class="info b-4"><p>'+gallery.lang.error.empty_albom+'</p></div>');
                    return;
                }
                break;
            default:
                break;
        }
    },
    createThumbList: function(pos){
        function sortNumber(a,b)
        {
            return a - b;
        }
        this.obj.thumbsId = this.getRand();
        var str = '<div id="file-setting"></div>\
		             <div id="view-image"></div>\
                       <ul id="img-list-' + this.obj.thumbsId + '" class="slide-' + pos + '">\
                         <div class="box-ui-slider-horizontal"><div id="ui-slider"></div></div>\
                     <li id="viewport"><ul>';
        if(this.data){
            for(var i in this.data){
                str += '<li id="thumbs-' + this.data[i].id + '" class="loader">\
                         <a href="javascript:" rel="' +  this.data[i].path + '"></a></li>';
            }
        }
        str +='</ul></li></ul>';
        return str;
    },
    getData: function(i){
        var d = null;
        for (var key in this.data){
            if(this.data[key].id == i){
                d = this.data[key];
            }
        }
        return d;
    },
    getRand: function(){
        var m = new Date;
        return  m.getTime();
    },
    setPrevPath: function(str){
        return str.replace('%replace%/', '');
    },
    setThumbPath: function(str){
        return str.replace('%replace%', 'thumbs');
    },
    getDataLeght: function(obj){
        var c = 0;
        for(var k in obj){
            if(obj.hasOwnProperty(k))
                c++;
        }
        return c;
    },
    _setData: function(data){
        this.log('set-data-gallery')
        this.data = data.file ;
    // this.init();
    },
    userAreaResize: function(h, correct, callback){
        this.obj.userArea  = this.getSize($('#user-work-area'));
        h =  (h - this.obj.userArea[3]) + correct;
        $('#'+this.setting.elementRootS).animate(
        {
            height: this.obj.userArea[3] + h
        },100, function(){
            if(typeof callback == 'function'){
                callback();
            }
        });
        $('#user-work-area').animate({
            height: (this.obj.userArea[3] + h  )  + 90
        }, 100, function(){
            if(typeof callback == 'function'){
                callback();
            }
        })
    },
    preload: function(src,id){
        this.currentId = id;
        var data = this.getData(id);
        if(data.status === 'albom'){
            if(typeof this.obj.image[id] == 'object'){
                gallery.core.userAreaResize(this.obj.image[id].height,250);
                setTimeout(function(i){
                    gallery.eff.setCurrentObject(gallery.core.obj.image[id]);
                    gallery.eff.start(gallery.core.currentEff, $('#view-image'));
                },310,id)

            }else{
                this.obj.image[id] = new Image;
                this.obj.image[id].src = src;
                this.preloadProccess(id);
            }
        }else if(data.status === 'youtube' ||
            data.status === 'video'){
            gallery.core.userAreaResize(640,0);
            gallery.player.init(data);
        }else if(data.status === 'smotri.com' || data.status === 'vimeo' ||
            data.status === 'rutube' || data.status === 'gametrailers' ){
            gallery.player.serviceInit(data);
            gallery.core.userAreaResize(640,0);
        }
        if(typeof gallery.ass == 'object'){
            gallery.ass.setToolbar();
        }


    },
    preloadProccess: function(id){
        if(this.obj.image[id].complete){
            this.preloadEnd(id);
        }else{
            setTimeout(function(){
                gallery.core.preloadProccess(id);
            },100);
        }
    },
    preloadEnd: function(id){
        this.param.preview.size.x = this.obj.image[id].width;
        this.param.preview.size.y =   this.obj.image[id].height;
        gallery.core.userAreaResize(gallery.core.param.preview.size.y,250);
        setTimeout(function(id){
            gallery.eff.setCurrentObject(gallery.core.obj.image[id]);
            gallery.eff.start(gallery.core.currentEff, $('#view-image'));
        },310,id);
        //admin
        if(typeof gallery.ass == 'object'){
    //gallery.ass.setToolbar();
    }
    },
    getSize: function(obj){
        var o = $(obj).offset();
        return [o.left, o.top, obj.outerWidth(true), obj.outerHeight(true)];
    },
    setCss:function(c,css,d){
        $('#'+this.setting.elementRootS).addClass(c).animate(css,d);
        $('#user-work-area').animate({
            height: (css.height + 70)
        }, d)
    },
    //thumbs loading
    loadThumb: function(v){
        var w = Math.round( gallery.core.ui.slider.viewport + v );
        var j = Math.round(w / gallery.core.ui.slider.elWidth);
        j = (j <  gallery.core.ui.slider.el ) ? j : gallery.core.ui.slider.el;
        gallery.core.ui.slider.obj.find('.ui-slider-handle').unbind('keydown').mouseup(function(){
            if(typeof(preloadInterval)=='number')
                clearInterval(preloadInterval);
        }).mousedown(function(){
            if(typeof(preloadInterval)=='number')
                clearInterval(preloadInterval);
        });
        var s = (j -  gallery.core.ui.slider.view)- 1;
        var preloadInterval=setInterval(function(){
            for(var i=s;i<j;i++){
                var li= $('li#viewport').children('ul').children('li').eq(i);
                if((typeof(li)=='object')&&li.hasClass('loader')){
                    $(li).removeClass('loader').addClass('loading');
                    var src =  $(li).children('a').attr('rel');
                    src = gallery.core.setThumbPath(src)
                    gallery.core.loadeThumb(src,i);
                }
            }
            if(i==j){
                clearInterval(preloadInterval);
            }
        },200);
    },
    loadeThumb: function(src,i){
        var img = new Image();
        img.src = gallery.core.setting.httpRoot + src;
        gallery.core.obj.thumbs[i] = img;
        gallery.core.loadingThumb(i);
    },
    loadingThumb: function(i){
        if(gallery.core.obj.thumbs[i].complete){
            gallery.core.loadingThumbComplete(i);
        }else{
            setTimeout(function(){
                gallery.core.loadingThumb(i);
            },200)
        }
    },
    loadingThumbComplete: function(i){
        $('li#viewport').children('ul').children('li').eq(i).removeClass('loading')
        .children('a').html(gallery.core.obj.thumbs[i]);

    },
    loadViewImage: function(){
        $('#viewport').children('ul').children('li').children('a').click(function(){
            var src = $(this).attr('rel');
            var id = $(this).parent('li').attr('id');
            id = id.split('-', 2)[1]
            src = gallery.core.setting.httpRoot + gallery.core.setPrevPath(src);
            gallery.core.preload(src, id);
            if(typeof gallery.ass == 'object'){
                gallery.ass.setLabel();
            }
        });
    },
    setSliderJqueryUi: function(o){
        if(this.data == null){
            return;
        }
        this.ui.slider.el = this.getDataLeght(this.data);
        var el = 0;
        $($('li#viewport').children('ul').children('li')).each(function(){
            el += 1;//count children
        })
        if(o == 'bottom' || o == 'top'){
            this.ui.slider.orientation = 'horizontal';
            var w = $('#img-list-'+this.obj.thumbsId).children('li#viewport').children('ul')
            .children('li').outerWidth(true);
            this.ui.slider.viewport = $('li#viewport').outerWidth(true);
            this.ui.slider.max = (w *  el)  - this.ui.slider.viewport + 10;
            this.ui.slider.elWidth = $('li#viewport').children('ul').children('li').outerWidth(true);
            this.ui.slider.view = Math.round(this.ui.slider.viewport / this.ui.slider.elWidth );

            $('#img-list-'+this.obj.thumbsId).children('li#viewport').children('ul').css({
                width: (w *  el)
            });

            $('#img-list-'+this.obj.thumbsId).children('li#viewport').css({
                width: gallery.core.ui.slider.max
            });
        }else{
            this.ui.slider.orientation = 'vertical';
        }

        this.ui.slider.obj = $('#ui-slider').slider({
            step:0.0001,
            max: this.ui.slider.max,
            animate:true,
            orientation:this.ui.slider.orientation,
            slide:function(event,ui){
                gallery.core.viewportSlide(ui.value);
            },
            change:function(event,ui){
                gallery.core.viewportSlide(ui.value);
            }
        });
        this.loadThumb(0);
        this.loadViewImage();
        $('#viewport').children('ul').children('li').children('a').eq(0).click();
        gallery.mouse.initwheel();
        if(typeof gallery.ass === 'object')
            gallery.ass.initThumbHelper();
    },
    viewportSlide: function(v){
        if( this.ui.slider.orientation == 'horizontal'){
            $('li#viewport').children('ul').stop().animate({
                marginLeft:'-'+v+ 'px'
            },300,'linear',function(){
                gallery.core.loadThumb(v);
            })
        }
    },
    /*
     * return  [width , height]
     */
    getPageSize: function(){
        return [$(window).width() , $(window).height() ]
    },
    setCookie:function(name,value,props){
        props=props||{}
        var exp=props.expires
        if(typeof exp=="number"&&exp){
            var d=new Date()
            d.setTime(d.getTime()+exp*1000)
            exp=props.expires=d
        }
        if(exp&&exp.toUTCString){
            props.expires=exp.toUTCString()
        }
        value=encodeURIComponent(value)
        var updatedCookie=name+"="+value
        for(var propName in props){
            updatedCookie+="; "+propName
            var propValue=props[propName]
            if(propValue!==true){
                updatedCookie+="="+propValue
            }
        }
        document.cookie=updatedCookie
    },
    getCookie:function(name){
        var matches=document.cookie.match(new RegExp("(?:^|; )"+name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g,'\\$1')+"=([^;]*)"))
        return matches?decodeURIComponent(matches[1]):undefined
    },
    deleteCookie:function(name){
        gallery.core.setCookie(name,null,{
            expires:-1
        })
    },
    _destroy: function(){
        this.log('_destroy html ')
        $('#part-open').html('');
        $('#view-image').parent().html('');
    },
    log: function(m) {
        window.console && window.console.log && window.console.log(m);
    }
}




//mouse
gallery.mouse = {
    initwheel:function(){
        if(window.addEventListener)
            window.addEventListener('DOMMouseScroll', gallery.mouse.wheel,false);
        window.onmousewheel= document.onmousewheel= gallery.mouse.wheel;
        var _container = document.getElementById('viewport');
        if(_container){
            _container.onmouseover=function(){
                handle=over;
            };
            _container.onmouseout=function(){
                handle=null;
            }
            function over(delta){
                var val =  gallery.core.ui.slider.obj.slider('value')
                gallery.core.ui.slider.obj.slider({
                    value: val + (delta *  gallery.core.ui.slider.elWidth)
                })
            }
        }
    },
    wheel:function(event){
        var delta=0;
        if(!event)event=window.event;
        if(event.wheelDelta){
            delta=event.wheelDelta/120;
        }else if(event.detail){
            delta=-event.detail/3;
        }
        if(delta&&typeof handle=='function'){
            handle(delta);
            if(event.preventDefault)
                event.preventDefault();
            event.returnValue=false;
        }
    }
}
gallery.lang = {
    popup:{},
    setting:{},
    ajax:{},
    error:{}
};

