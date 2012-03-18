<?php

/**
 * ���������� AJAX ��� ���������� �������� � �����.
 * ��������������� ��� ������ ������� ������ ����� ������� � ������������ �� ������ �������������,
 * ������ ����� �������� ���� ����� � model_route::route(),
 * ��� �������� ����� url ������ ��������� ��������� �������: http://gallery.ru/gallery/[ACTION_NAME]/ajax/
 *
 * @package gallery
 * @author Dark Ghost
 * @copyright 2011
 * @access public
 * @since 1.5.3 (07.2011)
 *
 */
class controller_ajax_gallery extends controller_gallery {

    /**
     *
     * @var array
     */
    protected $_config_cms;

    /**
     *
     * @var array
     */
    protected $_config;

    /**
     *
     * @var object
     */
    protected $_request;

    /**
     *
     * @var object
     */
    protected $_view;

    /**
     *
     * @var object
     */
    protected $_tpl;

    /**
     *
     * @var object
     */
    protected $_db;

    /**
     * ���������� ������������
     * @return string
     */
    public function add_commentAction() {
        model_gallery::setRegistry('view_template', new view_template());
        return model_gallery::getClass('model_comments')->add();
    }

    public function adddescrAction() {
        $descr = array();
        if ($_REQUEST ['text'] != '') {
            $descr ['descr'] = module_json::convertToCp(model_metaTag::getDescr());
        } else {
            $descr ['descr'] = $this->_lang ['error'] ['err_15'];
        }
        return module_json::getJson($descr);
    }

    public function addkeywordAction() {
        $descr = array();
        if ($_REQUEST ['text'] != '') {
            $key = model_metaTag::getKeyword($_REQUEST ['text']);
            $k = module_json::convertToCp($key);
            if (is_array($k)) {
                $descr ['descr'] = implode(',', $k);
            }
        } else {
            $descr ['descr'] = $this->_lang ['error'] ['err_15'];
        }
        return module_json::getJson($descr);
    }

    /**
     * ������� �������������� ������������
     * @return string
     */
    public function editcommentsAction() {
        return model_gallery::getClass('model_comments')->edit();
    }

    /**
     * ���������� ����������� ��� ������� ��������������
     * @return string
     */
    public function savecommentsAction() {
        return model_gallery::getClass('model_comments')->save();
    }

    /**
     * �������� �����������
     * @return type
     */
    public function deletecommentsAction() {
        model_gallery::getClass('model_comments')->delete();
        return null;
    }

    /**
     *  ��������� ���� ������� � �������.
     * @return void
     */
    public function changepermalbumAction() {
        model_gallery::getClass('model_albom')->setPermission();
    }

    /**
     * ��������� -|- ��������� ����������� ��� ������������� �����.
     * @return void
     */
    public function comm_accessAction() {
        $this->_setAccess('comm_access');
    }

    /**
     * ��������� -|- ��������� ������� ��� ������������� �����.
     * @return void
     */
    public function rating_accessAction() {
        $this->_setAccess('rating_access');
    }

    /**
     * �������� �����
     * @return json
     */
    public function deletefileAction() {
        return module_json::getJson(model_gallery::getClass('model_file')->deleteFile());
    }

    /**
     * ���������� ������ � �������
     * @return json
     */
    public function sortfileAction() {
        return module_json::getJson(model_gallery::getClass('model_file')->sortFile());
    }

    /**
     * ��������� -|- ��������� ������ ������� �������
     * @return json
     */
    public function labelfileAction() {
        return module_json::getJson(model_gallery::getClass('model_file')->label());
    }

    /**
     * ���������� -|- ���������� ���������
     * @return json
     */
    public function settitleAction() {
        return module_json::getJson(model_gallery::getClass('model_file')->setTitle());
    }

    /**
     * ���������� -|- ���������� ��������
     * @return json
     */
    public function setdescriptionAction() {
        return module_json::getJson(model_gallery::getClass('model_file')->setDescr());
    }

    public function settagAction() {
        return module_json::getJson(model_gallery::getClass('model_search')->addKeywordsFile());
    }

    /**
     *
     * @return json
     */
    public function addyoutubeAction() {
        $id = model_request::getPost('id');
        if (!$id) {
            return 'error';
        }
        $upload = model_gallery::getClass('model_upload');
        $link = model_request::getPost('link');
        $link = explode("\n", $link);
        $file = model_gallery::getClass('model_file');
        $f = array();
        $v = '';
        if (is_array($link)) {
            foreach ($link as $feed) {
                if (null != $feed and $feed != '') {
                    $url = parse_url($feed);
                    $f = $file->tubeParse($url);
                    parse_str($url['query']);
                    if ($v)
                        $upload->youTubeImages($v, $f);
                }
            }
        }
        $alb = model_gallery::getClass('model_albom');
        return module_json::getJson($alb->updateAlbom($id));
    }

    /**
     *
     * @return json
     */
    public function setplayerAction() {
        return module_json::getJson(model_gallery::getClass('model_file')->setDefaultPlayer());
    }

    /**
     *
     * @return json
     */
    public function deletecoverAction() {
        return module_json::getJson(model_gallery::getClass('model_file')->deleteCover());
    }

    /**
     *
     * @return json
     */
    public function setvideocoverAction() {
        return module_json::getJson(model_gallery::getClass('model_file')->setCover());
    }

    /**
     *
     * @param string $name
     * @return void
     */
    protected function _setAccess($name) {
        $id = model_request::getPost('id');
        $set = model_request::getPost('set');
        $file = model_gallery::getClass('model_file');
        $info = $file->getFile($id);
        if ($info)
            $file->updateFile($id, array(
                $name => $set
            ));
        if ($info['parent_id']) {
            $alb = model_gallery::getClass('model_albom');
            $alb->updateAlbom($info['parent_id']);
        }
    }

    /**
     * ���������� �������
     * @return json
     */
    public function addlabelAction() {
        return module_json::getJson(model_gallery::getClass('model_label')->add());
    }

    /**
     *
     * @return json
     */
    public function setcoverAction() {
        return module_json::getJson(model_gallery::getClass('model_albom')->setCoverAlbom());
    }

    /**
     *
     * @return string
     */
    public function updatealbomAction() {
        $alb = model_gallery::getClass('model_albom');
        $ans = array();
        $ans = $alb->updateAlbom(model_request::getPost('id'));
        $alb = model_gallery::getClass('model_albom');
        $ans['tpl'] = $alb->getFileListTable();
        return module_json::getJson($ans);
    }

    /**
     *
     * @return array
     */
    public function loadfileAction() {
        $view = new view_template ();
        $tpl = $view->getView();
        $view->setView('user_mode2.tpl');
        $copy_template = null;

        preg_match("#\\[file-list\\](.*?)\\[/file-list\\]#si", $tpl->copy_template, $copy_template);
        if ($copy_template[1]) {
            $tpl->template = stripslashes($copy_template[1]);
            $tpl->copy_template = $tpl->template;
            $result = model_gallery::getClass('model_file')->loadUserFile();
            if ($result['mysqlId'])
                while ($row = $this->_db->get_row($result['mysqlId'])) {
                    #$tpl->set('{name}',$row['path']);
                    $tpl->set('{id-file}', $row['id']);
                    $tpl->set('{preview-path}', model_file::getThumb($row['path']));
                    $tpl->set('{name}', substr($row['path'], strrpos($row['path'], '/') + 1));
                    $tpl->compile('list');
                }
            return array('tpl' => $tpl->result['list'], 'count' => $result['count']);
        }
    }

    /**
     * �������� ����� � �����.
     * @return string
     */
    public function getfileAction() {
        $_id = model_request::getPost('id');
        return module_json::getJson(model_gallery::getClass('model_file')->getFile($_id));
    }

    /**
     * ���������� ���������� ����� � �����.
     * @return array
     */
    public function setfileparamAction() {
        return model_gallery::getClass('model_file')->setParam();
    }

    /**
     *
     * @return string
     */
    public function setratingAction() {
        return model_gallery::getClass('model_file')->ratingFile();
    }

    public function deletealbomAction() {
        if (model_gallery::$user['user_group'] == 1)
            model_gallery::getClass('model_albom')->deleteAlbum();
    }

}

