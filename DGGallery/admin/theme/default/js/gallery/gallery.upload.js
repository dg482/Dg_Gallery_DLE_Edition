gallery.upload = {
    post:{},
    sendMethod: 'POST',
    ajax: gallery.ajax,
    ass: gallery.ass,
    core: gallery.core,
    pluginVar:{
        path: {
            uploader:'',
            script:'',
            cancelImg: ''
        },
        fileExt: '',
        multi: false,
        queueSizeLimit: 1024, // 1mb
        post: {}
    },
    counter: 0,
    loadImg: '',
    images: [],

    setPlugin: function(onAllComplete, wrap ){
        if(wrap){
            $("#fileQueue").wrap('<fieldset class="b-4 upload-box"></fieldset>');
            $("#fileQueue").parent('fieldset').append('<legend>0</legend>');
        }
        $("#uploadify").uploadify({
            uploader: gallery.core.setting.httpRoot+'/' + gallery.upload.pluginVar.path.uploader,
            script:gallery.core.setting.httpRoot +'/'+  gallery.upload.pluginVar.path.script,
            cancelImg: gallery.core.setting.httpRoot +'/'+ gallery.upload.pluginVar.path.cancelImg,
            queueID: 'fileQueue',
            auto: true,
            multi: true,
            fileDesc: gallery.upload.pluginVar.fileExt,
            fileExt : gallery.upload.pluginVar.fileExt,
            queueSizeLimit:  this.pluginVar.queueSizeLimit,// max
            wmode: 'transparent',
            method:gallery.upload.sendMethod,
            scriptData: gallery.upload.pluginVar.post,
            sizeLimit: this.pluginVar.sizeLimit,// size
            removeCompleted: true,
            onSelect: function(){
                gallery.upload.counter += 1;
                $("#fileQueue").parent('fieldset').children('legend').html(gallery.upload.counter);
            },
            onCancel: function() {
                gallery.upload.counter -= 1;
                $("#fileQueue").parent('fieldset').children('legend').html(gallery.upload.counter);
            },
            onComplete: function() {
                gallery.upload.counter -= 1;
                $("#fileQueue").parent('fieldset').children('legend').html(gallery.upload.counter);
            },
            onAllComplete: function(event,data){
                gallery.upload.counter = 0;
                $("#fileQueue").parent('fieldset').children('legend').html(gallery.upload.counter);
                if(typeof onAllComplete){
                    onAllComplete(event,data);
                }
            }
        });
        $('a.youtube-add').live('click', function(){
            var b = {}, id = gallery.core.getRand(),
            $element = $('<div id="' +  id + '" />'),
            $text  = $('<textarea class="youtube" name="youtube" id="youtube-'+id+'"></textarea>');
            //element += 'http://www.youtube.com/watch?v=coLtYvdgMbs\r';
            //element += 'http://www.youtube.com/watch?v=aAftI-tbyxM&feature=related\r';
            //element += 'http://www.youtube.com/watch?v=RTCZoSPEOA0';

            b[gallery.lang.apply] =function(){
                var link = $text.val();
                gallery.ajax.sendQuery({
                    id: gallery.upload.pluginVar.post.id,
                    link: link,
                    action: 'addYoutube'
                }, 'json', function(data){
                    if(null != data){}
                });
                $(this).dialog("close");
                $(this).dialog("destroy");
            };
            b[gallery.lang.cancel] =function(){
                $(this).dialog("close");
                $(this).dialog("destroy");
            };
            $element.html($text)
            $('body').append($element);
            $('#'+id).dialog({
                resizable: false,
                buttons: b,
                title: 'add youtube',
                height: 250,
                width: 450,
                position: 'center',
                open: function(event, ui) {

                }
            })
        })
    },
    setPluginVideoCover: function(id, callback){
        gallery.upload.pluginVar.post.area = 'videocover';
        gallery.upload.pluginVar.post.file_id = gallery.core.currentId;
        if($('#'+id).hasClass('on')){
            return;
        }
        $('#'+id).addClass('on');
        $('#'+id).uploadify({
            uploader:gallery.core.setting.httpRoot+'/' + gallery.upload.pluginVar.path.uploader,
            script: gallery.core.setting.httpRoot+'/' +gallery.upload.pluginVar.path.script,
            cancelImg: gallery.core.setting.httpRoot+'/' +gallery.upload.pluginVar.path.cancelImg,
            queueID: 'fileQueueCover',
            auto: true,
            multi: true,
            fileDesc: 'Images',
            fileExt : '*.gif;*.jpg;*.png;*.jpe;*.jpeg;',
            queueSizeLimit:  1,// max
            wmode: 'transparent',
            method:gallery.upload.sendMethod,
            scriptData: gallery.upload.pluginVar.post,
            sizeLimit: this.pluginVar.sizeLimit,// size
            removeCompleted: true,
            onSelect: function(){
            //------------
            },
            onCancel: function() {
            //------------
            },
            onComplete: function() {
            //------------
            },
            onAllComplete: function(event,data){
                callback(event,data);
            }
        });
    },
    loadCatCover: function(id){
        gallery.ajax.sendQuery({
            action: 'getCatCover',
            type: 'json',
            id: id
        }, 'json', function(data){
            gallery.upload.loadImg = data.path;
            gallery.upload.preloadCover();
        }, gallery.lang.ajax.loadingCover);
    },
    preloadCover: function(){
        this.images[1] = new Image();
        this.images[1].src =  this.loadImg;
        this.preloadProccess(1);
    },
    preloadProccess: function(id){
        if(this.images[id].complete){
            $('#view-image').html(this.images[id])
        }else{
            setTimeout(function(){
                gallery.upload.preloadProccess(id);
            },100);
        }
    }
}

