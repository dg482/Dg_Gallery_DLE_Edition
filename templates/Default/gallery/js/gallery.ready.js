
gallery.ajax.root = window.location.protocol + '//' + window.location.host+ '/' + 'gallery/ajax/';

$.fn.dggallerylabel = function(_options){
    gallery.core.obj.image[gallery.core.currentId] = $('img#gallery-image');
    gallery.core.currentId = 0;
    //gallery.core.obj.image[gallery.core.currentId] = $(this);
    if(null == gallery.labelEdit.jCropPlugin){
        gallery.labelEdit._setPlugin();
    }
    gallery.labelEdit.jCropPlugin.setOptions({
        allowResize:false,
        allowMove:false
    });
    gallery.labelEdit.jCropPlugin.disable();
    var _stop = null;
    $('a.dggalleryimagelabel').parent().hover(function(){
        _stop = false;
        var key = $(this).children().attr('id');
        $('span.ImageLabel').not(this).removeClass('ImageLabelActive');
        $(this).addClass('ImageLabelActive');
        var l = _options.label[key];
        gallery.labelEdit.jCropPlugin.animateTo([l.coords.x,l.coords.y,l.coords.x2,l.coords.y2]);
        return false;
    }, function(){
        setTimeout(function(){
            if(_stop)
                gallery.labelEdit.jCropPlugin.release();
        },150);
        _stop = true;
    });
    $(document).click(function(){
        gallery.labelEdit.jCropPlugin.release();
    });
}

var userSearch = new Object;
$('select#category').live('change',function(){
    var $val = $(this).val() ;
    userSearch.category = $val;
    gallery.ajax.sendQuery({
        date: $val,
        action: 'loadfile',
        name: 'category',
        history: userSearch
    }, 'json',function(data){
        if(data)
            if(data.tpl){
                $('#ajax-content-user').html(data.tpl);

            }else{
                $('#ajax-content-user').html('<div class="error b-4"><p>empty result</p></div>');
            }
        gallery.ass.setPager(data.count, 1);
    });
})
$('input[type="text"].date').live('change',function(){
    var $val = $(this).val() ;
    if($(this).attr('name') == 'date-1'){
        userSearch.date1 = $val;
    }else{
        userSearch.date2 = $val;
    }
    gallery.ajax.sendQuery({
        date: $val,
        action: 'loadfile',
        name: $(this).attr('name'),
        history: userSearch
    }, 'json',function(data){
        if(data.tpl){
            $('#ajax-content-user').html(data.tpl);
        }else{
            $('#ajax-content-user').html('<div class="error b-4"><p>empty result</p></div>');
        }
        gallery.ass.setPager(data.count, 1);
    });
});

function deleteFile(id){
    gallery.ajax.sendQuery({
        action: 'deleteFile',
        id: id
    }, 'json',null);
}
function deleteAlbom(id){
    gallery.ajax.sendQuery(data = {
        action: 'deleteAlbom',
        id: id
    }, 'json',null);
}
function editFile(id){
    var wysiwyg = true, $div = $('<div />'), b = {},
    $form = $('<form name="#" action="#" method="post"/>'),
    $fieldset = $('<fieldset />'), $title = $fieldset.clone(),
    $descr = $fieldset.clone(), $keyword = $fieldset.clone(),
    $panel = $fieldset.clone();
    gallery.ajax.sendQuery(data = {
        action: 'getFile',
        id: id
    }, 'json',function(data){
        var $comm = $('<input type="checkbox" id="comm_access" name="comm_access" /><label for="comm_access">'+gallery.lang.setting.comm+'</label>'),
        $rating = $('<input type="checkbox" id="rating_access" name="rating_access" /><label for="rating_access">'+gallery.lang.setting.rating+'</label>');
        $panel.append($comm,$rating);
        $title.append('<legend>'+gallery.lang.setting.title+'</legeng>');
        $title.append('<input type="text" name="title" value="'+data.title+'" />');
        $descr.append('<legend>'+gallery.lang.setting.descr+'</legeng>');
        if(wysiwyg){
            $descr.append('<textarea rows="" name="descr" cols="" class="jwysiwyg">'+data.descr+'</textarea>');
            gallery.ass.InitWYSIWYG();
        }
        $keyword.append('<legend>'+gallery.lang.setting.tags +'</legeng>');
        var tag = (data.other_dat.tag)?data.other_dat.tag:'';
        $keyword.append('<textarea rows="" name="keyword" cols="" >'+tag+'</textarea>');
        $form.append($panel,$title,$descr,  $keyword);
        $('body').append($div.html($form));
        b[dle_p_send] = function(){
            var data = $form.serialize();
            gallery.ajax.sendQuery(data = {
                action: 'setFileParam',
                id: id,
                data: data
            }, 'json',null);

            $(this).dialog("close");
        };
        b[dle_act_lang[3]] = function(){
            $(this).dialog("close");
        };

        $div.dialog({
            resizable: false,
            buttons: b,
            title: 'Редактироание параметров файла',
            height: 650,
            width: 700,
            modal: true,
            position: 'center',
            open: function(event, ui) {
                if(data.comm_access == 1){
                    $('#comm_access').attr({
                        checked: true
                    });
                }
                if(data.rating_access == 1){
                    $('#rating_access').attr('checked',true);
                }
                $('#rating_access').button({
                    text: true,
                    icons: {
                        primary: "ui-icon-star"
                    }
                })
                $('#comm_access').button({
                    text: true,
                    icons: {
                        primary: "ui-icon-comment"
                    }
                })
                if(wysiwyg){
                    gallery.ass.InitWYSIWYG();
                }
            },
            close: function(event, ui){
                $div.remove();
            }
        })
    });
}

function setRating(id,set){
    $.ajax({
        beforeSend: function(){
            ShowLoading("")
        },
        type: "POST",
        url: window.location.protocol + '//' + window.location.host+ '/' + 'gallery/ajax/',
        data: {
            action: 'setrating',
            id: id,
            set: set
        },
        dataType: 'json' ,
        cache: false,
        success:function(data){
            if(data)
                if(data.tpl){
                    $('#rating-'+id).html(data.tpl);
                }
            HideLoading();
        },
        error:function(){
            HideLoading();
            gallery.core.log('Error');
        }
    });
}
gallery.lang = {
    "setting":{
        "comm":"\u041a\u043e\u043c\u043c\u0435\u043d\u0442\u0430\u0440\u0438\u0438",
        "rating":"\u0420\u0435\u0439\u0442\u0438\u043d\u0433",
        "descr":"\u041e\u043f\u0438\u0441\u0430\u043d\u0438\u0435",
        "title":"\u0417\u0430\u0433\u043e\u043b\u043e\u0432\u043e\u043a",
        "tags":"\u0422\u0435\u0433\u0438",
        "titlePopup":"\u0412\u0432\u0435\u0434\u0438\u0442\u0435 \u0437\u0430\u0433\u043e\u043b\u043e\u0432\u043e\u043a.",
        "titleDescr":"\u041e\u043f\u0438\u0441\u0430\u043d\u0435 \u0444\u0430\u0439\u043b\u0430.",
        "titleTag":"\u0412\u0432\u0435\u0434\u0438\u0442\u0435 \u043a\u043b\u044e\u0447\u0435\u0432\u044b\u0435 \u0441\u043b\u043e\u0432\u0430.",
        "titleTags":"\u041a\u043b\u044e\u0447\u0435\u0432\u044b\u0435 \u0441\u043b\u043e\u0432\u0430"
    },

    "ajax":{
        "loadingPart":"\u041f\u043e\u0436\u0430\u043b\u0443\u0439\u0441\u0442\u0430 \u043f\u043e\u0434\u043e\u0436\u0434\u0438\u0442\u0435, \u0438\u0434\u0435\u0442 \u0432\u044b\u043f\u043e\u043b\u043d\u0435\u043d\u0438\u0435 \u0437\u0430\u043f\u0440\u043e\u0441\u0430."
    }
};

