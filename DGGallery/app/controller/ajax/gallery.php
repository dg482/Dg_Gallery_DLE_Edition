<?php

/**
 * Контроллер AJAX для выполнения запросов с сайта.
 * Подразумевается что методы данного класса будут вызваны в соответствие со схемой маршрутизации,
 * тоесть будет запрошен один метод в model_route::route(),
 * при принятои схеме url должен выглядеть следующим образом: http://gallery.ru/gallery/[ACTION_NAME]/ajax/
 *
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 *
 */

class controller_ajax_gallery extends controller_gallery
{

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
     * @var module_db
     */
    protected $_db;

    /**
     * Добавление комментариев
     * @return string
     */
    public function add_commentAction()
    {
        model_gallery::setRegistry('view_template', new view_template());
        return model_gallery::getClass('model_comments')->add();
    }

    /**
     * @return string
     */
    public function adddescrAction()
    {
        $descr = array();
        if ($_REQUEST ['text'] != '') {
            $descr ['descr'] = module_json::convertToCp(model_metaTag::getDescr());
        } else {
            $descr ['descr'] = $this->_lang ['error'] ['err_15'];
        }
        return module_json::getJson($descr);
    }

    /**
     * @return string
     */
    public function addkeywordAction()
    {
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
     * Быстрое редактирование комментариев
     * @return string
     */
    public function editcommentsAction()
    {
        return model_gallery::getClass('model_comments')->edit();
    }

    /**
     * Сохранение комментария при быстром редактирование
     * @return string
     */
    public function savecommentsAction()
    {
        return model_gallery::getClass('model_comments')->save();
    }

    /**
     * Удаление комментария
     * @return type
     */
    public function deletecommentsAction()
    {
        model_gallery::getClass('model_comments')->delete();
        return null;
    }

    /**
     *  Изменение прав доступа к альбому.
     * @return void
     */
    public function changepermalbumAction()
    {
        model_gallery::getClass('model_albom')->setPermission();
    }

    /**
     * Разрешить -|- запретить комментарии для определенного файла.
     * @return void
     */
    public function comm_accessAction()
    {
        $this->_setAccess('comm_access');
    }

    /**
     * Разрешить -|- запретить рейтинг для определенного файла.
     * @return string
     */
    public function rating_accessAction()
    {
        $this->_setAccess('rating_access');
    }

    /**
     * Удаление файла
     * @return string
     */
    public function deletefileAction()
    {
        return module_json::getJson(model_gallery::getClass('model_file')->deleteFile());
    }

    /**
     * Сортировка файлов в альбоме
     * @return string
     */
    public function sortfileAction()
    {
        return module_json::getJson(model_gallery::getClass('model_file')->sortFile());
    }

    /**
     * Включение -|- отключние вывода панелей отметок
     * @return string
     */
    public function labelfileAction()
    {
        return module_json::getJson(model_gallery::getClass('model_file')->label());
    }

    /**
     * Назначение -|- обновление заголовка
     * @return string
     */
    public function settitleAction()
    {
        return module_json::getJson(model_gallery::getClass('model_file')->setTitle());
    }

    /**
     * Назначение -|- обновление описания
     * @return string
     */
    public function setdescriptionAction()
    {
        return module_json::getJson(model_gallery::getClass('model_file')->setDescr());
    }

    public function settagAction()
    {
        return module_json::getJson(model_gallery::getClass('model_search')->addKeywordsFile());
    }

    /**
     * @return string
     */
    public function addyoutubeAction()
    {
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
     * @return string
     */
    public function setplayerAction()
    {
        return module_json::getJson(model_gallery::getClass('model_file')->setDefaultPlayer());
    }

    /**
     * @return string
     */
    public function deletecoverAction()
    {
        return module_json::getJson(model_gallery::getClass('model_file')->deleteCover());
    }

    /**
     * @return string
     */
    public function setvideocoverAction()
    {
        return module_json::getJson(model_gallery::getClass('model_file')->setCover());
    }

    /**
     *
     * @param string $name
     * @return void
     */
    protected function _setAccess($name)
    {
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
     * Добавление отметки
     * @return json
     */
    public function addlabelAction()
    {
        return module_json::getJson(model_gallery::getClass('model_label')->add());
    }

    /**
     *
     * @return json
     */
    public function setcoverAction()
    {
        return module_json::getJson(model_gallery::getClass('model_albom')->setCoverAlbom());
    }

    /**
     *
     * @return string
     */
    public function updatealbomAction()
    {
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
    public function loadfileAction()
    {
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
     * Удаление файла с сайта.
     * @return string
     */
    public function getfileAction()
    {
        $_id = model_request::getPost('id');
        return module_json::getJson(model_gallery::getClass('model_file')->getFile($_id));
    }

    /**
     * Обновление параметров файла с сайта.
     * @return array
     */
    public function setfileparamAction()
    {
        return model_gallery::getClass('model_file')->setParam();
    }

    /**
     *
     * @return string
     */
    public function setratingAction()
    {
        return model_gallery::getClass('model_file')->ratingFile();
    }

    /**
     *
     */
    public function deletealbomAction()
    {
        if (model_gallery::$user['user_group'] == 1)
            model_gallery::getClass('model_albom')->deleteAlbum();
    }

}

