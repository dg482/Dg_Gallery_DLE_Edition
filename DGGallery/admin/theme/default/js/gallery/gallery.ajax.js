gallery.ajax = {
    root: '/dgg/ajax/admin/',
    jCropAction:function(obj,a){
        $('#DvBnMkLpOiUy').remove();
        switch(a){
            case'destroy':
                obj.release();
                break;
            case'add_label':
                if(!gallery.labelEdit.newLabel.newLabel || gallery.labelEdit.newLabel.newLabel == ''){
                    obj.release();
                    return;
                }
                if(!gallery.core.currentId ){
                    obj.release();
                    return;
                }
                var data = gallery.labelEdit.newLabel;
                data.action = 'addLabel';
                data.id = gallery.core.currentId;
                gallery.ajax.sendQuery(data, 'json', function(data){
                    // gallery.core.data = data.file ;
                    gallery.core._setData(data)
                });
                obj.release();
            case'destroyEdit':
                obj.release();

                break;
            case'delete_label':
                obj.release();
                //   Gallery.DeleteLabel();
                break;
            default:
                break;
        }
    },
    beforeSend: null,
    sendQuery: function(obj,type,callback,msg){
        $.ajax({
            beforeSend: function(){
                if(null === gallery.ajax.beforeSend)
                    gallery.ajax.setLoader(msg);
                else if(typeof  gallery.ajax.beforeSend == 'function'){
                    gallery.ajax.beforeSend()
                }
            },
            type:"POST",
            url: gallery.ajax.root,
            data: obj,
            dataType: type ,
            cache: false,
            success:function(data){
                if(typeof callback == 'function'){
                    callback(data);
                }
                gallery.ajax.hideLoader();
            },
            error:function(){
                gallery.ajax.hideLoader();
                gallery.core.log('Error');
            }
        });
    },
    setLoader: function(msg){
        var l = '';
        if(typeof msg == 'string'){
            l = $('<div id="system-ajax" class="b-4">'+msg+'</div>');
        }else{
            if(gallery.lang.ajax.loadingPart)
                l = $('<div id="system-ajax" class="b-4">'+gallery.lang.ajax.loadingPart+'</div>');
            else
                l = $('<div id="system-ajax" class="b-4">loading ... </div>');
        }

        l.css({
            position: 'fixed',
            left: Math.round(($(window).width()  - $('#system-ajax').outerWidth(true)) / 2 ),
            top: Math.round(($(window).height() - $('#system-ajax').outerHeight(true)) / 2 ),
            display: 'none'
        })
        $('body').append(l);
        l.fadeIn('slow');
    },
    hideLoader: function(){
        $('#system-ajax').remove();

    }


}








function activateAjaxDialog(action,id){
    $.ajax({
        beforeSend: function(){
            if(typeof ShowLoading === 'function'){
                ShowLoading();
            }else{
                var a=($(window).width()-$("#loading-layer").width())/2,
                b=($(window).height()-$("#loading-layer").height())/2;
                $("#loading-layer").css({
                    left:a+"px",
                    top:b+"px",
                    position:"fixed",
                    zIndex:"99"
                });
                $("#loading-layer").fadeTo("slow",0.6)
            }
        },
        type:"POST",
        url: 'engine/ajax/tags/index.php',
        data:{
            action: action,
            id: id
        },
        dataType: 'html' ,
        cache: false,
        success:function(data){
            $("#loading-layer").fadeOut("slow")
            var b = [];
            b['close']=function(){
                $(this).dialog("close")
            };

            $("#dlepopup").remove();
            var $div = $("<div id='dlepopup' title='--' style='display:none'></div>");
            $div.html(data)
            $("body").append($div);
            $("#dlepopup").dialog({
                autoOpen:!0,
                width:500,
                buttons:b
            })
        },
        error:function(){
            $("#loading-layer").fadeOut("slow")
            gallery.core.log('Error');
        }
    });
}

$('a.list').click(function(){
    activateAjaxDialog(action,id)
    return false;
})