dle_txt = '';
gallery.comments = {
    httpRoot: window.location.protocol + '//' + window.location.host+ '/',
    cache: [],
    editId: 0,
    tinymce: false,
    add: function(){
        var a = document.getElementById("gallery_comment_form"),
        name = $(a.name).val(),
        c = a.sec_code? a.sec_code.value:"";
        if($('#comments').val()=="" || name ==""){
            DLEalert(dle_req_field,dle_info);
            return false;
        }

        if(a.recaptcha_response_field){
            var d=Recaptcha.get_response(),
            e=Recaptcha.get_challenge();
        }
        else{
            e=d="";
        }
        if(typeof ShowLoading == 'function'){
            gallery.ajax.beforeSend = ShowLoading();
        }
        gallery.ajax.root = gallery.comments.httpRoot + 'gallery/addcomments/ajax/';
        gallery.ajax.sendQuery({
            action:'add_comment',
            parent_id:a.parent_id.value,
            comments:$('#comments').val(),
            name:name,
            mail:(typeof(a.mail)=='object')?a.mail.value:'',
            sec_code:c,
            recaptcha_response_field:d,
            recaptcha_challenge_field:e
        }, 'json', function(data){
            if(typeof HideLoading == 'function'){
                HideLoading("");
            }
            if(data.error){
                DLEalert(data.error.join('<br />'),dle_info);
                return;
            }
            a.reset();
            if(a.sec_code){
                a.sec_code.value="";
            }
            if(data.tpl){
                $("#gallery-ajax-comments").html(data.tpl);
                $(data.tpl).css('display','none');
                $("html"+(!$.browser.opera?",body":"")).animate({
                    scrollTop:$("#gallery-ajax-comments").position().top-70
                },1100);
                setTimeout(function(){
                    $("#blind-animation").show("blind",{},1500)
                },1100)
            }
        });
        return false;
    },
    addAnswer: function(parent_id){
        var b = {};
        b[dle_p_send]=function(){
            if(typeof ShowLoading == 'function'){
                gallery.ajax.beforeSend = ShowLoading();
            }
            var a = document.getElementById("gallery_comment_form");
            gallery.ajax.root = gallery.comments.httpRoot + 'gallery/addcomments/ajax/';
            gallery.ajax.sendQuery({
                action:'add_comment',
                ns_parent_id: parent_id,
                parent_id:a.parent_id.value,
                comments: $('#review').val()
            }, 'json', function(data){
                if(typeof HideLoading == 'function'){
                    HideLoading("");
                }
                if(data.error){
                    DLEalert(data.error.join('<br />'),dle_info);
                    return;
                }
                if(data.tpl){
                    $("#answer-"+parent_id).html(data.tpl);
                }
                $("html"+(!$.browser.opera?",body":"")).animate({
                    scrollTop:$("#gallery-ajax-comments").position().top-70
                },1100);
                setTimeout(function(){
                    $("#blind-animation").show("blind",{},1500)
                },1100)
            });
            $(this).dialog("close");

        };
        $("#d-popup").remove();
        var div = $('<div id="d-popup" style="display:none"></div>'),
        frm = $('<form name="answer"><textarea id="review" rows="6" cols="" name="review" style="width:98%"></textarea></form>')
        $("body").append(div);
        $(div).append(frm)
        $("#d-popup").dialog({
            autoOpen: true,
            width: 690,
            height: 270,
            resize:false,
            buttons: b,
            open: function(){
                if(gallery.comments.tinymce)
                    $(function(){
                        $('#review').tinymce({
                            script_url : '/engine/editor/jscripts/tiny_mce/tiny_mce.js',
                            theme : "advanced",
                            skin : "cirkuit",
                            language : "ru",
                            //   width : "98%",
                            height : "160",
                            plugins : "layer,table,style,advhr,spellchecker,advimage,advlist,emotions,inlinepopups,insertdatetime,media,searchreplace,print,contextmenu,paste,fullscreen,nonbreaking,typograf",
                            relative_urls : false,
                            convert_urls : false,
                            media_strict : false,
                            dialog_type : 'window',
                            extended_valid_elements : "noindex,div[align|class|style|id|title]",
                            custom_elements : 'noindex',
                            // Theme options
                            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,undo,redo,|,emotions,|,link,|,forecolor,backcolor,",
                            theme_advanced_buttons2 : "",
                            theme_advanced_buttons3 : "",
                            theme_advanced_toolbar_location : "top",
                            theme_advanced_toolbar_align : "left",
                            theme_advanced_statusbar_location : "bottom",
                            plugin_insertdate_dateFormat : "%d-%m-%Y",
                            plugin_insertdate_timeFormat : "%H:%M:%S",
                            spellchecker_languages : "+Russian=ru,English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv",
                            // Example content CSS (should be your site CSS)
                            content_css : "/engine/editor/css/content.css"
                        });
                    });
            }
        });
    },
    edit: function(id){
        if(this.editId){
        //  this.cancel(this.editId);
        }
        this.editId = id;
        this.cache[id]  =  $('#comm_id_'+id).html();
        if(typeof ShowLoading == 'function'){
            gallery.ajax.beforeSend = ShowLoading();
        }
        gallery.ajax.root = gallery.comments.httpRoot + 'gallery/editcomments/ajax/';
        gallery.ajax.sendQuery({
            id: id,
            action: 'editcomments'
        }, 'json', function(data){
            if(typeof HideLoading == 'function'){
                HideLoading("");
            }
            $('#comm_id_'+id).hide('blind', function(){
                $('#comm_id_'+id).html(data.tpl).show('blind');
            })

        });
    },
    cancel: function(id){
        $('#comm_id_'+id).hide('blind', function(){
            $('#comm_id_'+id).html(gallery.comments.cache[id]).show('blind');
        })
        dle_copy_quote('admin');

    },
    dle_copy_quote: function(a){
        if(typeof  dle_copy_quote == 'function'){
            dle_copy_quote(a)
        }
        dle_txt="";
        if(window.getSelection){
            dle_txt=window.getSelection();
        }
        else if(document.selection){
            dle_txt=document.selection.createRange().text;
        }
        dle_txt!="" && (dle_txt="[quote="+a+"]"+dle_txt+"[/quote]\n")
    },
    copyQuote:function(qname){
        if(dle_txt==""){
            dle_txt="[b]"+qname+"[/b],"+"\n";
        }
        if(dle_txt!=""){
            document.getElementById('comments').value+=dle_txt;
        }
    },
    save: function(id){
        if(typeof ShowLoading == 'function'){
            gallery.ajax.beforeSend = ShowLoading();
        }
        var a = document.getElementById("editcomments");
        gallery.ajax.root = gallery.comments.httpRoot + 'gallery/savecomments/ajax/';
        gallery.ajax.sendQuery({
            id: id,
            comm_txt: $(a.comments).val(),
            action: 'savecomments'
        }, 'json', function(data){
            if(typeof HideLoading == 'function'){
                HideLoading("");
            }
            $('#comm_id_'+id).html(data.tpl);
        });

    },
    _delete: function(id){
        gallery.ajax.root = gallery.comments.httpRoot + 'gallery/deletecomments/ajax/';
        gallery.ajax.sendQuery({
            id: id,
            action: 'deletecomments'
        }, 'json', function(data){
            if(typeof HideLoading == 'function'){
                HideLoading("");
            }
            $('#box_comm_'+id).hide('blind');
        });

    },
    complaint: function(id){

    },
    confirm: function(id){
        gallery.ajax.sendQuery({
            id: id,
            action: 'approvecomments'
        }, 'json', function(data){
            if(typeof HideLoading == 'function'){
                HideLoading("");
            }
            $('#box_comm_'+id).hide('blind');
        });
    }

}





