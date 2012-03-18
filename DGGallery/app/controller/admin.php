<?php

/**
 * Класс: admin
 *
 * @author Dark Ghost
 * @copyright 2011
 * @package
 */
class controller_admin extends controller_gallery {

    /**
     * @var array
     */
    protected $_lang;

    /**
     * @var array
     */
    protected $_config_cms;

    /**
     * @var array
     */
    protected $_config;

    /**
     * @return void
     */
    public function __construct() {
        parent::__construct(false);
        $this->_lang = model_gallery::getRegistry('lang');
    }

    /**
     *
     * @return array
     */
    public function indexAction() {
        $page = array();
        $page ['add_cat'] = $this->setRowTbl($this->_lang ['index_page'] ['add_cat'] ['title'], $this->_lang ['index_page'] ['add_cat'] ['descr'], 'add_cat', FALSE);
        $page ['tpl_edit'] = $this->setRowTbl($this->_lang ['index_page'] ['edit-template'] ['title'], $this->_lang ['index_page'] ['edit-template'] ['descr'], 'edit-template', null);
        $page ['add_albom'] = $this->setRowTbl($this->_lang ['index_page'] ['add_alb'] ['title'], $this->_lang ['index_page'] ['add_alb'] ['descr'], 'add_alb', FALSE);
        $page ['setting'] = $this->setRowTbl($this->_lang ['index_page'] ['setting'] ['title'], $this->_lang ['index_page'] ['setting'] ['descr'], 'setting', FALSE);
        $page ['mass_load'] = $this->setRowTbl($this->_lang ['index_page'] ['mass'] ['title'], $this->_lang ['index_page'] ['mass'] ['descr'], 'mass', '');

        $page ['moder'] = $this->setRowTbl($this->_lang ['index_page'] ['moder'] ['title'], $this->_lang ['index_page'] ['moder'] ['descr'], 'moder', '');
        if (GALLERY_MODE === 2) {
            $page ['add_albom'] = $this->setRowTbl($this->_lang ['index_page'] ['add_alb'] ['title'], $this->_lang ['index_page'] ['add_alb'] ['descr'], 'disable', FALSE);
        }
        if (GALLERY_MODE === 1) {
            $page ['mass_load'] = $this->setRowTbl($this->_lang ['index_page'] ['mass'] ['title'], $this->_lang ['index_page'] ['mass'] ['descr'], 'disable', '');
        }
        $count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_comments WHERE approve='0'");
        $_count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom  WHERE approve='0'");
        if (!$count['count'] && !$_count['count']) {
            $page ['moder'] = $this->setRowTbl($this->_lang ['index_page'] ['moder'] ['title'], $this->_lang ['index_page'] ['moder'] ['descr'], 'disable', '');
        }
//add user db
        $this->_user = assistant::$_user;
        $check = $this->_db->super_query('SELECT * FROM ' . DBNAME . '.' . PREFIX .
                "_dg_gallery_user WHERE user_id='{$this->_user['user_id']}'");
        if (null === $check['id']) {
            $this->_db->query('INSERT INTO ' . DBNAME . '.' . PREFIX . '_dg_gallery_user (user_id) VALUES ' . "('{$this->_user['user_id']}')");
        }
        return array(
            'content' => '<table width="100%" border="0" cellpadding="50">
<tr>' . $page['add_cat'] . $page['tpl_edit'] . '</tr>
<tr>' . $page['add_albom'] . $page['setting'] . '</tr>
<tr>' . $page['add_tender'] . $page['debug'] . '</tr>
<tr>' . $page['mass_load'] . $page['moder'] . '</tr>
</table>',
        );
    }

    /**
     * Настройки скрипта
     * @return array
     */
    public function settingAction() {
//sidebar
        $sidebar = array('setting', 'upload', 'access', 'view', 'video', 'perf', 'bugs');
//javascript
        $inlineJs .= 'var sidebar =' . module_json::getJson($sidebar) . ";\r";
        $inlineJs .= "gallery.ass.selectDecorator($('select'));\r";
        $inlineJs .= "gallery.ass.checkBoxWrap($('input[type=\"checkbox\"]'));\r";
        $inlineJs .= "gallery.ass.initSideBar(sidebar,'setting');\r";

        return array(
            'content' => $this->_settingForm(),
            'inlineJs' => $inlineJs
        );
    }

    /**
     * Сохранение настроек
     * @return void
     */
    public function save_settingAction() {
        $set = $_REQUEST ['config'];
        if (!$set) {
            return;
        }
        $set ['dle'] = (file_exists(ROOT_DIR . '/engine/data/config.php')) ? 1 : 0;
//empty field (checkbox)
        $int = array('status', 'guest_mode', 'allowYouTube',
            'youTubeThumbAuto', 'youTubeThumbManualLoad', 'vimeoThumbManualLoad',
            'watermark', 'rainbow', 'fileFrame', 'guestMode',
            'statusAlfavit', 'statusAjax', 'statusFilters',
            'allCategory', 'indexCategory', 'ratingAlbom',
            'ratingAlbomType', 'ratingFile', 'ratingFileType',
            'logViewAlbom', 'logViewFile', 'logDownloadFile',
            'highslideType', 'defaultHandler', 'xmlSample',
            'defaultCahe', 'defaultJson', 'debug',
            'altExceptionHandler', 'debugAjax', 'FileHash',
            'watermarkSlider', 'watermarkCover', 'video_setting_dle', 'allow_smotri_com', 'allow_vimeo_com',
            'allow_rutube_ru', 'allow_gametrailers_com', 'autoPlay','watermarkVideo','tube_related'
        );
        foreach ($set as $k => $v) {
            if (is_array($v)) {
                $set [$k] = implode(',', $v);
            }
        }
        foreach ($int as $in) {
            $set [$in] = (isset($set [$in])) ? 1 : 0;
        }
        model_gallery::getRegistry('model_config')->saveToArray($set);
        $page = array();
        $page ['add_cat'] = $this->setRowTbl($this->_lang ['index_page'] ['add_cat'] ['title'], $this->_lang ['index_page'] ['add_cat'] ['descr'], 'add_cat', FALSE);
        $page ['add_albom'] = $this->setRowTbl($this->_lang ['index_page'] ['add_alb'] ['title'], $this->_lang ['index_page'] ['add_alb'] ['descr'], 'add_alb', FALSE);
        if (GALLERY_MODE === 2) {
            $page ['add_albom'] = $this->setRowTbl($this->_lang ['index_page'] ['add_alb'] ['title'], $this->_lang ['index_page'] ['add_alb'] ['descr'], 'disable', FALSE);
        }
        return array(
            'content' => $this->_setInfoSuccess($this->_lang['info']['save_ok']) .
            '<table width="100%" border="0" cellpadding="50">
<tr>' . $page['add_cat'] . $page['add_albom'] . '</tr>

</table>'
        );
    }

    /**
     *
     */
    public function add_catAction() {
        return $this->_category();
    }

    public function editcatAction() {
        return $this->_category();
    }

    public function add_categoryAction() {
        return $this->_addCategory('add_category');
    }

    public function update_categoryAction() {
        return $this->_addCategory('update_category');
    }

    public function add_albAction() {
        if ($this->_config['mode'] == 2) {
            return array(
                'content' => $this->_setWarning($this->_lang['info']['not_support'])
            );
        }
        $edit_id = intval(model_request::getGet('id'));
        $sidebar = array('add', 'access');
        $inlineJs .= 'var sidebar =' . module_json::getJson($sidebar) . ";\r";
        $contextMenu = array(
            'add' => $this->_lang ['contextMenu'] ['create'],
            'update' => $this->_lang ['contextMenu'] ['update']);
        $inlineJs .= 'var contextMenu =' . module_json::getJson($contextMenu) . ";\r";
        $inlineJs .= "gallery.ass.setContextMenu(contextMenu,$('textarea'));\r";
        $inlineJs .= "gallery.ass.initSideBar(sidebar,'add',null);\r";


        $inlineJs .= "gallery.ass.checkBoxWrap($('input[type=\"checkbox\"]'));\r";
        $inlineJs .= "gallery.ass.selectDecorator($('select'));\r";
        $inlineJs .= "gallery.ass.InitWYSIWYG();\r";
        return array(
            'content' => $this->_addAlbumForm($edit_id),
            'inlineJs' => $inlineJs,
            'cssBlock' => 'style="height:700px;"'
        );
    }

    public function massAction() {
        if ($this->_config['mode'] == 1) {
            return array(
                'content' => $this->_setWarning($this->_lang['info']['not_support'])
            );
        }
        $form = new module_form(require ROOT_DIR . '/DGGallery/app/config/adminForms/massLoad.php');
        $form->setValue = true;
        $form->setconfig['parent_id'] = 0;
        $gallery_cat = model_cache_file::get('tree_category');
        $gallery_cat [0] ['title'] = '-------------';
        $gallery_cat [0] ['id'] = '0';
        $sidebar = array('upload', 'search');
        $inlineJs = 'var sidebar =' . module_json::getJson($sidebar) . ";\r";
        $inlineJs .= "gallery.ass.initSideBar(sidebar,'upload',null);\r";
        $html = $form->getForm('upload', 'upload', true, false);
//        $total = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . '_dg_gallery_file ');
//        $total = (int) $total['count'];
        $upload = new model_upload ();
        $json = $upload->setPlugin(100, 'category', 'all', 0);
        $inlineJs .= "gallery.upload.pluginVar={$json};\r";
        $inlineJs .= "
gallery.ass._selectDecorator = false;
gallery.upload.setPlugin(function(){

}, true);
$('select[name=\"config[parent_id]\"]').change(function(){
    gallery.upload.pluginVar.post.id = $(this).val();
    $('#uploadify').uploadifySettings('scriptData',gallery.upload.pluginVar.post)
    if(gallery.upload.pluginVar.post.id > 0){
        $('div#overlay').css({
            right:100 + '%'
        })
    }else{
        $('div#overlay').css({
            right:10 + 'px'
        })
    }
});
gallery.ass.setDatePicker($('.file-item'));

";


        $option = $form->selectCategories($gallery_cat);

        $upload = <<<HTML
<fieldset class="b-4" id="upload-area">
    <div id="overlay"></div>
<table width="100%" height="450" border="0" valign="top" style="margin:10px 0 20px 0; background:#FCFCFC">
    <thead>
      <tr height="20">
        <td align="center" colspan="3"></td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td width="80%" valign="top" style="vertical-align: top;" rowspan="2"><div style="height:500px; overflow:auto; overflow-x: hidden;"></div></td>
        <td height="440" class="tbl" colspan="2">
            <div id="fileQueue" class="uploadifyQueue"></div>
             </td>
      </tr>
      <tr valign="top">
        <td valign="top" class="tbl"><input width="120" type="file" height="30" id="uploadify" name="uploadify"></td>
        <td valign="top" class="tbl"><a href="javascript:void(0)" class="youtube-add">youtube-add</a></td>
      </tr>
    </tbody>
  </table></fieldset></div>
  <div id="part-search" style="display:none; height:650px;">
    <fieldset class="b-4 clear">
            <table width="100%" border="0" class="file-table-profile ">
              <thead>
                <tr>
                  <td colspan="2"><fieldset  class="b-4" style="background:#F1F5F7">
                  <legend>Параметры  показа</legend>
                        <label for="category">Категория</label>
                        <select name="category" id="category" style="width:200px;">
                          {$option}
                              </select>
                        <label for="search1"> за период с: </label>
                        <input type="text" name="date-1" class="date f_input" style="width:80px" id="search1"/>
                        <label for="search2">по: </label>
                        <input type="text" name="date-2"  class="date f_input" style="width:80px" id="search2"/>
                    </fieldset></td>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <td colspan="2" align="center"><div id="pager"></div>&nbsp;</td>
                </tr>
              </tfoot>
              <tbody id="ajax-content-user">
              </tbody>
            </table>
          </fieldset>
</div>
HTML;


        $html .= $form->closeForm($upload);
        return array(
            'content' => $html,
            'inlineJs' => $inlineJs,
            'cssBlock' => 'style="height:750px;"'
        );
    }

    public function add_album_dbAction() {
        return array(
            'content' => model_gallery::getClass('model_albom')->add(model_request::getPost('config')),
            'inlineJs' => '',
            'cssBlock' => ''
        );
    }

    public function edit_templateAction() {
        $editor = new module_editFile ();
        $inlineJs = 'var dirEdit =' . $editor->getStart() . ";\r";
        $inlineJs .= "gallery.ass.setListEdit(dirEdit);\r";
        $content = ( file_exists(ROOT_DIR . '/DGGallery/cache/system/page/editor.tmp')) ? stripslashes(file_get_contents(ROOT_DIR . '/DGGallery/cache/system/page/editor.tmp')) : 'error load page';
        return array(
            'content' => $content,
            'inlineJs' => $inlineJs
        );
    }

    public function moderAction() {
        global $user_group, $is_logged;
        $sidebar = array('comments',);

        $inlineJs .= 'var sidebar =' . module_json::getJson($sidebar) . ";\r";
        $inlineJs .= "gallery.ass.initSideBar(sidebar,'comments',null);\r";
        require_once ROOT_DIR . '/engine/classes/templates.class.php';
        $tpl = new dle_template;
        $param = array();
        $tpl->dir = ROOT_DIR . '/DGGallery/admin/theme/default/';

        $_comm = model_gallery::getClass('model_comments');
        $offset = (int) model_request::getRequest('page');
        $param['start'] = ($offset > 1) ? (($offset - 1) * $param['end']) : 0;
        $count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_comments WHERE approve='0'");
        if ($count['count'])
            $mysqlId = $this->_db->query('SELECT  ' . DBNAME . '.' . PREFIX . '_dg_gallery_comments.*,'
                    . DBNAME . '.' . PREFIX . '_users.name,'
                    . DBNAME . '.' . PREFIX . '_users.reg_date,'
                    . DBNAME . '.' . PREFIX . '_users.reg_date,fullname,'
                    . DBNAME . '.' . PREFIX . '_users.icq,'
                    . DBNAME . '.' . PREFIX . '_users.user_group,'
                    . DBNAME . '.' . PREFIX . '_users.news_num,'
                    . DBNAME . '.' . PREFIX . '_users.comm_num,'
                    . DBNAME . '.' . PREFIX . '_users.signature  FROM '
                    . DBNAME . '.' . PREFIX . '_dg_gallery_comments LEFT JOIN '
                    . DBNAME . '.' . PREFIX . '_users ON '
                    . DBNAME . '.' . PREFIX . '_users.user_id=' . DBNAME . '.' . PREFIX . '_dg_gallery_comments.user_id  WHERE '
                    . DBNAME . '.' . PREFIX . "_dg_gallery_comments.approve='0'  ORDER BY "
                    . DBNAME . '.' . PREFIX . "_dg_gallery_comments.date ASC LIMIT {$param['start']},10 ");
        $_comm->_user = assistant::$_user;
        if ($count['count'])
            $comments = $_comm->load(array(
                'mysqlId' => $mysqlId,
                'count' => $count
                    ), $tpl, true);
        else
            $comments = $this->_setWarning($this->_lang['info']['no_comment']);

        #$_count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE approve='0'");


        $content = '<form name="#" action="#" method="post">';
        $content .= '<div id="part-comments" style="background:#fff; height:600px;overflow:auto">' . $comments . '</div>';
        $content .= '<div id="part-image" style="background:#fff; height:600px;overflow:auto; display:none">';
//        if ($_count['count']) {
//            $this->_db->query('SELECT * FROM '.DBNAME.'.'.PREFIX."_dg_gallery_albom WHERE approve='0' ORDER BY id DESC");
//            while ($row = $this->_db->get_row()) {
//
//            }
//        }
        $content .= '</div>';
        $content.='</form>';
        return array(
            'content' => $content,
            'inlineJs' => $inlineJs,
            'cssBlock' => 'style="height:720px;"'
        );
    }

    /**
     *
     */
    public function openAction() {
        $alb = model_gallery::getRegistry('model_albom');
        $content = '';
        $inlineJs = '';
        $info = null;
        if (model_request::getPost('config')) {
            $alb = model_gallery::getClass('model_albom');
            $alb->add(model_request::getRequest('config'));
        }
        if ($alb->checkAlbom()) {
            $sidebar = array('open', 'upload', 'access', 'setting');
            $inlineJs .= 'var sidebar =' . module_json::getJson($sidebar) . ";\r";
            $inlineJs .= "gallery.ass.initSideBar(sidebar,'open',null);\r";
//$inlineJs .= "gallery.ass.checkBoxWrap($('input[type=\"checkbox\"]'));\r";
            $inlineJs .= "gallery.ass.selectDecorator($('select'));\r";
            $files = $alb->getFileListTable();
            $inlineJs .= 'gallery.core.data =' . module_json::getJson($alb->getFileList()) . ";\r";
            $inlineJs .= "gallery.core.param.label.panel = true;//show toolbar\r";
            $inlineJs .= "gallery.core.init();\r";
            $inlineJs .= "gallery.ass.init();\r";
            $inlineJs .= "gallery.ass.InitWYSIWYG();\r";
            $contextMenu = array(
                'add' => $this->_lang ['contextMenu'] ['create'],
                'update' => $this->_lang ['contextMenu'] ['update']);
            $inlineJs .= 'var contextMenu =' . module_json::getJson($contextMenu) . ";\r";
            $inlineJs .= "gallery.ass.setContextMenu(contextMenu,$('textarea'));\r";

//init upload
            $upload = new model_upload ();
            $json = $upload->setPlugin(100, 'useralbom', 'all', model_request::getRequest('id'));
            $inlineJs .= "gallery.upload.pluginVar={$json};\r";
            $info = $alb->openAlbom();

            $form = new module_form(require ROOT_DIR . '/DGGallery/app/config/adminForms/editAlbom.php');
//start form setting
            $form->setValue = true;
            $form->setconfig = unserialize($info['info']["access_data"]);

            $formAccess = $form->setPatr('contentAccessAlbom', true, true);
            $form->setconfig['title'] = $info['info']['title'];
            $meta_data = $info['info']['meta_data'];
            if (is_string($info['info']['meta_data'])) {
                $meta_data = unserialize($info['info']['meta_data']);
            }
            $form->setconfig['meta_title'] = $meta_data['meta_title'];
            $form->setconfig['gallery_cat'] = $info['info']['parent_id'];
            $form->setconfig['descr'] = $meta_data['description'];
            $form->setconfig['description'] = $meta_data['description'];
            $form->setconfig['meta_descr'] = $meta_data['meta_descr'];
            $form->setconfig['meta_keywords'] = $meta_data['meta_keywords'];

            $formedit = $form->setPatr('editalbum', true, true);


            $inlineJs .= "
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
                            primary: \"ui-icon-trash\"
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
                            placeholder: \"ui-icon ui-icon-arrowreturn-1-e\",
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
//
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
$('input[name=\"config[guestMode]\"]').live('click',function(){
        var data = {
        action: 'changepermalbum',
        id: gallery.upload.pluginVar.post.id,
        set:  $(this).parent().children('input').val(),
        perm: 'guestMode'
    };
    gallery.ajax.sendQuery(data, 'json', null);
});
";

            $content .= '<form action="#" method="post"><div class="images b-2 full" id="part-open"></div>';
            $content .= '<div id="part-upload" class="images b-2 full">
<table border="0" height="450" width="100%" style="margin:10px 0 20px 0;" valign="top">
  <thead>
    <tr height="20">
      <td align="center" colspan="3">загрузка файлов в альбом</td>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td width="80%" rowspan="2" valign="top" style="vertical-align: top;"><div style="height:500px; overflow:auto; overflow-x: hidden;">
          <table width="100%" border="0" class="file-table">
            ' . $files . '
          </table>
        </div></td>
      <td height="440" colspan="2" class="tbl" ><div class="uploadifyQueue" id="fileQueue"></div></td>
    </tr>
    <tr valign="top" >
      <td  class="tbl" valign="top" ><input type="file"  name="uploadify" id="uploadify"></td>
      <td  class="tbl" valign="top"><a class="youtube-add" href="javascript:void(0)">youtube-add</a></td>
    </tr>
  </tbody>
</table>
</div>
' . $formAccess . '
' . $formedit . '

</form>';
        } else {
            $content .= $this->_setErrorInfo($this->_lang['error']['err_3']);
        }
        return array(
            'content' => $content,
            'inlineJs' => $inlineJs,
        );
    }

    private function _category() {
        $edit_id = intval(model_request::getGet('id'));

//init upload
        $upload = new model_upload ();
//javascript
        $inlineJs .= "gallery.ass.selectDecorator($('select'));\r";
        $inlineJs .= "gallery.ass.InitWYSIWYG();\r";
//setUploadify
        $cover = model_gallery::getRegistry('model_category')->getCover($edit_id);


        $inlineJs .= ( $cover ['path'] == '') ? 'gallery.upload.loadImg = "' . $this->_config ['http'] . "uploads/gallery/assets/no-image.png\"\r" : 'gallery.upload.loadImg = "' . substr($this->_config ['http'], 0, -1) . $cover ['path'] . "\"\r";
        $inlineJs .= "gallery.upload.preloadCover();\r";
        $json = $upload->setPlugin(1, 'categoryCover', 'images', $edit_id);
        $inlineJs .= "gallery.upload.pluginVar={$json};\r";

        $inlineJs .= "gallery.ass.checkBoxWrap($('input[type=\"checkbox\"]'));\r";
        $inlineJs .= "gallery.upload.setPlugin(function(){gallery.upload.loadCatCover({$edit_id})}, true);\r";
        $contextMenu = array(
            'add' => $this->_lang ['contextMenu'] ['create'],
            'update' => $this->_lang ['contextMenu'] ['update'], /* 'w' => $this->_lang ['contextMenu'] ['w'] */);
        $inlineJs .= 'var contextMenu =' . module_json::getJson($contextMenu) . ";\r";
        $inlineJs .= "gallery.ass.setContextMenu(contextMenu,$('textarea'));\r";

        return array(
            'content' => $this->_addCatForm($edit_id),
            'inlineJs' => $inlineJs,
            'cssBlock' => 'style="height:1200px;"'
        );
    }

    /**
     *
     * @param type $action
     * @return type
     */
    private function _addCategory($action) {
        $this->_getDleClass('parse', 'parse');
        $page = null;
        $content = '';

        if ($_POST ['config'] ['title'] != '') {
            model_gallery::getRegistry('model_category')->addCategory();
            if ('add_category' === $action) {
                $content .= $this->_setInfoSuccess($this->_lang ['info'] ['add_cat_ok']);
//$page ['add_tender'] = $this->setRowTbl ( $this->_lang ['index_page'] ['add_tender'] ['title'], $this->_lang ['index_page'] ['add_tender'] ['descr'], 'add_tender', '' );
                $page ['add_albom'] = $this->setRowTbl($this->_lang ['index_page'] ['add_alb'] ['title'], $this->_lang ['index_page'] ['add_alb'] ['descr'], 'add_alb', '');
                $page ['mass_load'] = $this->setRowTbl($this->_lang ['index_page'] ['mass'] ['title'], $this->_lang ['index_page'] ['mass'] ['descr'], 'mass', '');
                if (GALLERY_MODE === 2) {
                    $page ['add_albom'] = $this->setRowTbl($this->_lang ['index_page'] ['add_alb'] ['title'], $this->_lang ['index_page'] ['add_alb'] ['descr'], 'disable', FALSE);
                }
                if (GALLERY_MODE === 1) {
                    $page ['mass_load'] = $this->setRowTbl($this->_lang ['index_page'] ['mass'] ['title'], $this->_lang ['index_page'] ['mass'] ['descr'], 'disable', '');
                }
                $content .= <<< HTML
<table width="100%" border="0" cellpadding="50"><tr>{$page['add_albom']}{$page ['mass_load']}</tr></table>
HTML;
            } elseif ('update_category' === $action) {
                $content .= $this->_setInfoSuccess($this->_lang ['info'] ['update_cat_ok']);
            }
        } else {
            $content .= $this->_setErrorInfo($this->_lang ['error'] ['err_14']);
        }
        return array(
            'content' => $content
        );
    }

    private function _addCatForm($edit_id = 0) {
//init forms
        $form = new module_form(require ROOT_DIR . '/DGGallery/app/config/adminForms/addCat.php');
//start form add_Cat
        if ($edit_id)
            $form->setValue = true;
        $form_action = '';
        if (0 == $edit_id) {
            $form->setconfig = $this->_config;
            $form_action .= '<input type="hidden" name="action" value="add_category" />';
        } else {
            $cat = model_gallery::getRegistry('model_category');
            $form->setconfig = $cat->getCatInfo($edit_id);
            $form_action .= '<input type="hidden" name="action" value="update_category" />';
            $form_action .= '<input type="hidden" name="id_category" value="' . $edit_id . '" />';
        }
        $form->actionPath = model_gallery::getRegistry('admin_path');

        $content = $form->getForm('addcat', 'addcat', false);
        $form_action .= '<input type="hidden" name="mod" value="dg_gallery" />';
        $form_action .= '<input type="submit" value="применить" class="buttons b-6" />';
//end form
        $content .= $form->closeForm($form_action);
        return $content;
    }

    private function setRowTbl($title, $descr, $cat = '', $js = '') {
        $admin = '/admin/index.php?';
        if ($this->_config_cms) {
            $admin = $this->_config_cms ['http_home_url'] . $this->_config_cms ['admin_path'] . '?mod=dg_gallery';
        }
        $link = '<p class="popup b-4">';
        if ($js != '') {
            $link .= '<a href="javascript:" rel="' . $js . '">' . $this->_lang ['index_page'] ['links'] ['ajax'] . '</a>';
        }
        $link .= '<a href="' . $admin . '&action=' . $cat . '">' . $this->_lang['index_page']['links']['gocat'] . '</a></p>';
        if ($cat === 'disable') {
            $link = '';
        }
        return <<< HTML
<td width="50%"><div class="box box-shadow-5-03 b-4">
<h2 class="{$cat}">{$title}</h2>
<p>{$descr}</p>
{$link}
</div></td>
HTML;
    }

    private function _settingForm() {//init forms
        $form = new module_form (); //start form setting
        $form->setValue = true;
        $form->setconfig = $this->_config;

        $form->actionPath = model_gallery::getRegistry('admin_path');
        $content = $form->getForm('setting', 'setting', true);
        $content .= $form->setPatr('meta', false, true);
        $content .= $form->setPatr('upload', true, false); //upload setting
        $content .= $form->setPatr('fileWork', false, false);
        $content .= $form->setPatr('uploadify', false, true);
//pay system
#$content .= $form->setPatr ( 'paysystemSC', true, false );
#$content .= $form->setPatr ( 'paysystemWM', false, false );
#$content .= $form->setPatr ( 'paysystemA1A', false, false );
#$content .= $form->setPatr ( 'paysystemRC', false, true );
//access content, function
        $content .= $form->setPatr('contentAccess', true, true);
        $content .= $form->setPatr('viewTools', true, false); //output setting





        $content .= $form->setPatr('pagination', false, true);
        $content .= $form->setPatr('videoSetting', true, false);
        $content .= $form->setPatr('videoSettingPlayerGlobal', false, false);
        $content .= $form->setPatr('videoSettingPlayer', false, true);
        $content .= $form->setPatr('performance', true, false); //setting performance
        $content .= $form->setPatr('performanceExtend', false, true); //debug setting
        $content .= $form->setPatr('debug', true, true);
        $form_action = '<input type="hidden" name="mod" value="dg_gallery" />';
        $form_action .= '<input type="hidden" name="action" value="save_setting" />';
        $form_action .= '<input type="submit" value="применить" class="buttons b-6" />';
        //end form setting
        $content .= $form->closeForm($form_action);
        return $content;
    }

    private function _setErrorInfo($msg) {
        return '<div class="error b-4"><p>' . $msg . '</p><a class="close" href="javascript:">close</a></div>';
    }

    private function _setInfoSuccess($msg) {
        return '<div class="success b-4"><p>' . $msg . '</p><a class="close" href="javascript:">close</a></div>';
    }

    private function _setWarning($msg) {
        return '<div class="warning b-4 ">
<p>' . $msg . '</p>
<a class="close" href="javascript:">close</a>
</div>';
    }

    private function _addAlbumForm($edit_id = null) {
//init forms
        $form = new module_form(require ROOT_DIR . '/DGGallery/app/config/adminForms/addAlbum.php');
//start form add_Cat
        $form->setValue = true;
        $form_action = '';
        if (null === $edit_id) {
            $form->setconfig = $this->_config;
            $form_action .= '<input type="hidden" name="action" value="update_album" />';
        } else {
            $cat = model_gallery::getRegistry('model_category');
            $form->setconfig = $cat->getCatInfo($edit_id);
            $form_action .= '<input type="hidden" name="action" value="add_album_db" />';
            $form_action .= '<input type="hidden" name="id_category" value="' . $edit_id . '" />';
        }
        $form->actionPath = model_gallery::getRegistry('admin_path');
        $content = $form->getForm('addalbum', 'addalbum', true, true);
        $content .= $form->setPatr('contentAccess', true, true);

        $form_action .= '<input type="hidden" name="mod" value="dg_gallery" />';
        $form_action .= '<input type="submit" value="применить" class="buttons b-6" />';
//end form
        $content .= $form->closeForm($form_action);
        return $content;
    }

}

