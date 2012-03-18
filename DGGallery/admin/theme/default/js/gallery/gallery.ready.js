$('document').ready(function(){
    gallery.ass.setNavPatr();
    gallery.ass.setNav(nav);
    $('div.box').hover(function(){
        $(this).children('p.popup').show( 'fade',{},500);
        $(this).children('p.popup').children('a').click(function(){
            var aObj = {};
            aObj.action = $(this).attr('rel');
            aObj.type = 'html';
            if( aObj.action ){
                gallery.ajax.sendQuery(aObj, 'html', function(data){
                    $('#work-area').html('<div id="ajax-content" style="display:none">'+ data+'</div>' );
                    //  console.log($('#ajax-content').outerHeight(true))
                    $('#ajax-content').show( 'fade',function(){
                        gallery.ass.selectDecorator($('select'))
                    },500);
                });
                gallery.eff.fade($('div.box'),25);
            }

        })
    }, function(){
        $(this).children('p.popup').hide( 'fade',{},500);
        $(this).children('p.popup').children('a').unbind('click');
    });
    $(function() {
        $('a.trash').button({
            text: false,
            icons: {
                primary: "ui-icon-trash"
            }
        }).click(function() {
            $(this).parent().parent().hide('fade');
            var data = {
                action: 'deleteFile',
                id: $(this).attr('rel')
            };
            gallery.ajax.sendQuery(data, 'json', function(data){
                gallery.core._setData(data);
            });
            return false;
        })
        $('.file-table').children('tbody').sortable({
            placeholder: "ui-icon ui-icon-arrowreturn-1-e",
            helper: function(e,ui){
                ui.children().each(function(){
                    $(this).width($(this).width());
                });
                return ui;
            },
            axis:'y',
            cursor:'move',
            start:function(event,ui){

            },
            stop: function(event, ui) {
                $('#save-files').removeAttr('disabled').button();
                var i = 0;
                $(ui.item).prev().parent().children().each(function(){
                    i = ($(this).index() + 1);
                    $(this).children('td').eq(0).children('input').val(i);
                });
                var data = {
                    action: 'sortFile',
                    id: $(ui.item).attr('role'),
                    pos: ($(ui.item).index() + 1)
                };
                gallery.ajax.sendQuery(data, 'json', function(data){
                    gallery.core._setData(data);
                });
            }
        }).disableSelection();
    });


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
                    $('#ajax-content-user').html(data.tpl)
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

    //--- hs setting

    hs.graphicsDir = '/engine/classes/highslide/graphics/';
    hs.outlineType = 'rounded-white';
    hs.numberOfImagesToPreload = 0;
    hs.showCredits = false;
    hs.lang =  gallery.lang.hs;
});
function deleteFile(id){
    gallery.ajax.sendQuery(data = {
        action: 'deleteFile',
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
        if(null === data){
            gallery.core.log('error');
            return;
        }
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
        b[gallery.lang.apply] = function(){
            var data = $form.serialize();
            gallery.ajax.sendQuery(data = {
                action: 'setFileParam',
                id: id,
                data: data
            }, 'json',null);

            $(this).dialog("close");
        };
        b[gallery.lang.cancel] = function(){
            $(this).dialog("close");
        };

        $div.dialog({
            resizable: false,
            buttons: b,
            title: gallery.lang.label.edit_file,
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
function dropdownmenu(a,b,c,d){
    window.event?event.cancelBubble=!0:b.stopPropagation&&b.stopPropagation();
    b=$("#dropmenudiv");
    if(b.is(":visible"))return clearhidemenu(),b.fadeOut("fast"),!1;
    b.remove();
    $("body").append('<div id="dropmenudiv" style="display:none;position:absolute;z-index:100;width:165px;"></div>');
    b=$("#dropmenudiv");
    b.html(c.join(""));
    d&&b.width(d);
    c=$(document).width()-15;
    d=$(a).offset();
    c-d.left<b.width()&&(d.left-=b.width()-a.offsetWidth);
    b.css({
        left:d.left+"px",
        top:d.top+a.offsetHeight+
        "px"
    });
    b.fadeTo("fast",0.9);
    b.mouseenter(function(){
        clearhidemenu()
    }).mouseleave(function(){
        delayhidemenu()
    });
    $(document).one("click",function(){
        hidemenu()
    });
    return!1
}
function hidemenu(){
    $("#dropmenudiv").fadeOut("fast")
}
function delayhidemenu(){
    delayhide=setTimeout("hidemenu()",1E3)
}
function clearhidemenu(){
    typeof delayhide!="undefined"&&clearTimeout(delayhide)
}
function IPMenu(a,b,c,d){
    var e=[];
    e[0]='<a href="https://www.nic.ru/whois/?ip='+a+'" target="_blank">'+b+"</a>";
    e[1]='<a href="'+dle_root+dle_admin+"?mod=iptools&ip="+a+'" target="_blank">'+c+"</a>";
    e[2]='<a href="'+dle_root+dle_admin+"?mod=blockip&ip="+a+'" target="_blank">'+d+"</a>";
    return e
}
var userSearch = new Object;