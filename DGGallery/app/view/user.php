<?php

/**
 * Класс: view_user
 *
 * @author Dark Ghost
 * @copyright 2011
 * @package
 */
class view_user extends view_template {

    protected $_user;

    public function __construct() {
        parent::__construct();
        $this->_db = model_gallery::getRegistry('module_db');
    }

    public function render($mode) {
        if (1 === $mode)
            return $this->renderModeOne();
        elseif (2 === $mode)
            return $this->renderModeTwo();
        return 'error';
    }

    /**
     *
     * @global null $gallery_cat
     * @return string
     */
    public function renderModeOne() {
        $form = new module_form(array());
        $albom = array();
        $allowCat = model_gallery::getClass('model_category')->getAccessGranted();
        if (null === $allowCat) {
            $allowCat = array(0 => array('id' => 0, 'title' => $this->_lang['error']['err_18']));
        }
        global $gallery_cat;
        $gallery_cat = model_gallery::getClass('model_category')->getAccessGranted(); #model_cache_file::get('tree_category');
        $gallery_cat [0] ['title'] = '-------------';
        $gallery_cat [0] ['id'] = '0';


        $this->setView('user_mode1.tpl');
        $input = '<input type="hidden" name="user_id" value="' . $this->_user['user_id'] . '" />';

        $this->_db->query('SELECT id,title FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE author_id='{$this->_user['user_id']}'");
        while ($row = $this->_db->get_row()) {
            $albom[$row['id']]['id'] = $row['id'];
            $albom[$row['id']]['title'] = $row['title'];
        }

        $this->_tpl->set('{albom}', $form->selectCategories($albom));
        $this->_tpl->set('{categoryes}', $form->selectCategories($allowCat));
        $this->_tpl->set('{editor}', model_gallery::getRegistry('model_user')->getEditor());
        $this->_tpl->copy_template = preg_replace("#\\[file-list\\](.*?)\\[/file-list\\]#ies", "\$this->createFileLits('\$1')", $this->_tpl->copy_template);


        if (stripos($tpl->copy_template, '{total-file}') !== false) {
            $count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE author='{$this->_user['name']}'");
            $this->_tpl->set('{total-file}', $count['count']);
        }
        $this->_tpl->set_block("'\\[edit\\](.*?)\\[/edit\\]'si", '');
        $this->_tpl->set('[profile]', '');
        $this->_tpl->set('[/profile]', '');
        $this->_tpl->compile('user');
        $action = HOME_URL . 'gallery/user/addalbom/';
        return <<<HTML
<div id="content"><div id="user-work-area" class="_block b-10 view-images">
<div id="work-area-top-bar"><ul class="nav"><li class="logo"><a class="copy" target="_blank" href="http://dg-dev.ru/gallery/index.html">D.G. Gallery</a></li></ul></div><div id="work-area-side-bar"><ul class="nav"></ul></div>
<div id="work-area"><form method="post" action="{$action}" name="gallery_add_form" id="gallery_add_form">
{$this->_tpl->result['user']}
{$input}
</form></div></div></div>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
gallery.ajax.root = window.location.protocol + '//' + window.location.host+ '/' + 'gallery/ajax/';
gallery.ass._selectDecorator = false;
});
//]]>
</script>
HTML;
    }

    /**
     *
     * @global null $gallery_cat
     * @return string
     */
    public function renderModeTwo() {
        $form = new module_form(array());
        $allowCat = model_gallery::getClass('model_category')->getAccessGranted();
        if (null === $allowCat) {
            $allowCat = array(0 => array('id' => 0, 'title' => $this->_lang['error']['err_18']));
        }
        global $gallery_cat;
        $gallery_cat = model_gallery::getClass('model_category')->getAccessGranted(); #model_cache_file::get('tree_category');
        $gallery_cat [0] ['title'] = '-------------';
        $gallery_cat [0] ['id'] = '0';

        $this->setView('user_mode2.tpl');
        #$tpl->set('{messages}', $msg);
        $this->_tpl->set('{categoryes}', $form->selectCategories($allowCat));
        $this->_tpl->copy_template = preg_replace("#\\[file-list\\](.*?)\\[/file-list\\]#ies", "\$this->createFileLits('\$1')", $this->_tpl->copy_template);
        $upload = new model_upload ();
        $json = $upload->setPlugin($this->_config['uploadifyMaxFile'], 'category', 'all', 0);
        $inlineJs = "gallery.upload.pluginVar={$json};\r";
        $form = new module_form(require ROOT_DIR . '/DGGallery/app/config/adminForms/massLoad.php');
        $form->setconfig['parent_id'] = 0;
        $form->setValue = true;
        $html = $form->getForm('upload', 'upload', true, true);
        $html .= <<<HTML
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
        <td height="440" class="tbl" colspan="2"><div id="fileQueue" class="uploadifyQueue"></div></td>
      </tr>
      <tr valign="top">
        <td valign="top" class="tbl"><input width="120" type="file" height="30" id="uploadify" name="uploadify"></td>
        <td valign="top" class="tbl"><a href="javascript:void(0)" class="youtube-add">youtube-add</a></td>
      </tr>
    </tbody>
  </table>
</fieldset>
HTML;
        $_content = $html . <<<JSS
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
gallery.ajax.root = window.location.protocol + '//' + window.location.host+ '/' + 'gallery/ajax/';
{$inlineJs}
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
});});
//]]>
</script>
JSS;

        if (!in_array($this->_user['user_group'], explode(',', $this->_config['accessCreate']))) {
            $this->_tpl->set('{add-form}', 'Access Denied');
        } else {
            $this->_tpl->set('{add-form}', $_content);
        }
        $this->_tpl->set_block("'\\[edit\\](.*?)\\[/edit\\]'si", '');
        $this->_tpl->set('[profile]', '');
        $this->_tpl->set('[/profile]', '');
        $this->_tpl->compile('user');
        return $this->_tpl->result['user'];
    }

    /**
     * Формирование списка файлов.
     * @param string $tpl
     * @return string
     */
    public function createFileLits($_tpl) {
        $view = new view_template ();
        $tpl = $view->getView();
        $tpl->template = stripslashes($_tpl);
        $tpl->copy_template = $tpl->template;
        $mysqlId = model_gallery::getClass('model_file')->getUserFile($this->_user['name'], 20);
        while ($row = $this->_db->get_row($mysqlId)) {
            #$tpl->set('{name}',$row['path']);
            $tpl->set('{id-file}', $row['id']);
            $tpl->set('{preview-path}', model_file::getThumb($row['path']));
            $tpl->set('{name}', substr($row['path'], strrpos($row['path'], '/') + 1));
            $tpl->compile('list');
        }
        return $tpl->result['list'];
    }

}

