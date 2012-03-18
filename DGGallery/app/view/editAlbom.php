<?php

/**
 * Класс: view_editAlbom
 *
 * @author Dark Ghost
 * @copyright 2011
 * @package
 */
class view_editAlbom extends view_template {

    public function __construct() {
        parent::__construct();
    }

    public function render(array $info) {
        $alb = model_gallery::getRegistry('model_albom');
        $alb->setId($info['id']);
        $config = null;
        require_once ROOT_DIR . '/engine/classes/parse.class.php';
        $this->_config['allow_wysiwyg'] = 1; //TOFO: добавить в настройки
        if ($this->_config['allow_wysiwyg']) {
            $this->_parse = new ParseFilter(Array('div', 'span', 'p', 'br', 'strong', 'em', 'ul', 'li', 'ol'), Array(), 0, 1);
            $this->_parse->wysiwyg = true;
            $this->_parse->safe_mode = true;
        } else {
            $this->_parse = new ParseFilter(array(), array(), 1, 1);
            $this->_parse->safe_mode = true;
        }
        $config = model_gallery::getRegistry('config_cms');

        global $gallery_cat;
        $gallery_cat = model_gallery::getClass('model_category')->getAccessGranted(); #model_cache_file::get('tree_category');
        $gallery_cat [0] ['title'] = 'без категории';
        $gallery_cat [0] ['id'] = '0';
        $this->setView('edit_albom.tpl');
        $files = $alb->getFileListTable();
        $this->_tpl->set('{files}', $files);
        $form = new module_form(require ROOT_DIR . '/DGGallery/app/config/adminForms/editAlbomSite.php');
        //start form setting
        $form->setValue = true;
        $form->setconfig = unserialize($info["access_data"]);
        $form->setconfig['id'] = $info['id'];
        $formAccess = $form->setPatr('contentAccessAlbom', true, true);
        $form->setconfig['title'] = $info['title'];
        $meta_data = $info['meta_data'];
        if (is_string($info['meta_data'])) {
            $meta_data = unserialize($info['meta_data']);
        }
        $form->setconfig['descr'] = base64_decode($meta_data['description']);
        $form->setconfig['meta_title'] = $meta_data['meta_title'];
        $form->setconfig['gallery_cat'] = $info['parent_id'];
        $this->_tpl->set('{editor}', model_gallery::getRegistry('model_user')->getEditor());
        $form->setconfig['description'] = $form->setconfig['descr'];


        $form->setconfig['meta_descr'] = $meta_data['meta_descr'];
        $form->setconfig['meta_keywords'] = $meta_data['meta_keywords'];
        $formedit = $form->setPatr('editalbum', true, true);
        $this->_tpl->set('{form-access}', $formAccess);
        $this->_tpl->set('{form-edit}', $formedit);
        //-----------
        $upload = new model_upload ();
        $json = $upload->setPlugin($this->_config['uploadifyMaxFile'], 'useralbom', 'all', $info['id']);
        $inlineJs = "gallery.upload.pluginVar={$json};\r";
        $inlineJs .= "gallery.core.param.label.panel = true;//show toolbar\r";
        $inlineJs .= "gallery.core.init();\r";
        $inlineJs .= "gallery.ass.init();\r";
        // description albom
        $inlineJs .= "gallery.ass.InitWYSIWYG();\r";
        $contextMenu = array(
            'add' => $this->_lang ['contextMenu'] ['create'],
            'update' => $this->_lang ['contextMenu'] ['update']);
        $inlineJs .= 'var contextMenu =' . module_json::getJson($contextMenu) . ";\r";
        $inlineJs .= "gallery.ass.setContextMenu(contextMenu,$('textarea'));\r";

        $lang = module_json::getJson($this->_lang ['javaScript']);
        $data = module_json::getJson($alb->getFileList());

        if (!$this->_config['allowYouTube']) {
            $this->_tpl->set_block("'\\[youtube\\](.*?)\\[/youtube\\]'si", '');
        } else {
            $this->_tpl->set('[youtube]', '');
            $this->_tpl->set('[/youtube]', '');
        }


        $content = $this->compile('user');
        $action = HOME_URL . 'gallery/user/savealbom/' . $info['id'];
        return <<<JSS
<div id="content"><div id="user-work-area" class="_block b-10 view-images">
<div id="work-area-top-bar"><ul class="nav"><li class="logo"><a class="copy" target="_blank" href="http://dg-dev.ru/gallery/index.html">D.G. Gallery</a></li></ul></div><div id="work-area-side-bar"><ul class="nav"></ul></div>
    <div id="work-area">
      <form method="post" action="{$action}" name="gallery_add_form" id="gallery_add_form" >
{$content}
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
gallery.lang = {$lang};
gallery.ajax.root = window.location.protocol + '//' + window.location.host+ '/' + 'gallery/ajax/';
var sidebar = ["open","upload","access","setting"];
gallery.ass.initSideBar(sidebar,'open',null);
gallery.core.data = {$data};
gallery.ass.wysiwyg = true;
tinyMCE_GZ.init({
            plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
            themes : 'advanced',
            skin : "cirkuit",
            disk_cache : true,
            languages:"ru",
            debug : false
});
{$inlineJs}
gallery.upload.setPlugin(function(){
    gallery.ajax.sendQuery({
        action: 'updateAlbom',
        type: 'json',
        id: gallery.upload.pluginVar.post.id
    }, 'json',
    function(data){
    $('.file-table').html(data.tpl);
       gallery.core._setData(data);
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
                    $(function() {
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
                            start:function(event,ui){},
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
                                gallery.ajax.sendQuery(data, 'json', null);
                            }
                        }).disableSelection();
                    });
    })
}, true);\r
gallery.ass.multipleSelectCallback = function(el){
    var name = el.attr('name');
    if(name == 'config[accessView][]'){
        name = 'accessView';
    }
    if(name == 'config[accessComments][]'){
        name = 'accessComments';
    }
    if(name == 'config[accessCommentsFile][]'){
        name = 'accessCommentsFile';
    }
    var data = {
        action: 'changepermalbum',
        id: gallery.upload.pluginVar.post.id,
        set:   el.serializeArray(),
        perm: name
    };

    gallery.ajax.sendQuery(data, 'json', null);
};\r
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
});
//]]>
</script>
JSS;
    }

}

