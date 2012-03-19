<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

/**
 *
 */
class controller_ajax_admin extends controller_gallery {

    /**
     * Получение обложки категории
     * @return json
     */
    public function getCatCoverAction() {
        $cat = model_gallery::getClass('model_category');
        return module_json::getJson($cat->getCover($_REQUEST ['id']));
    }

    /**
     * Генерация описания
     * @return json
     */
    public function addDescrAction() {
        $descr = array();
        if ($_REQUEST ['text'] != '') {
            $descr ['descr'] = module_json::convertToCp(model_metaTag::getDescr());
        } else {
            $descr ['descr'] = $this->_lang ['error'] ['err_15'];
        }
        return module_json::getJson($descr);
    }

    /**
     * Генерация ключевых слов
     * @return json
     */
    public function addKeywordAction() {
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
     * Редактирование шаблонов
     * @return mixed json -|- string
     */
    public function edit_templateAction() {
        $editor = new module_editFile ();
        return $editor->go();
    }

    /**
     * Просмотр альбома
     * @return string
     */
    public function openAction() {
        $alb = model_gallery::getClass('model_albom');
        return $alb->getParentAlbon();
    }

    /**
     * Добавление отметки
     * @return json
     */
    public function addLabelAction() {
        $label = model_gallery::getClass('model_label');
        return module_json::getJson($label->add());
    }

    /**
     * Обновление кеша альбома
     * @return void
     */
    public function updateAlbomAction() {
        $alb = model_gallery::getClass('model_albom');
        $ans = array();
        $ans = $alb->updateAlbom(model_request::getPost('id'));
        $alb = model_gallery::getClass('model_albom');
        $ans['tpl'] = $alb->getFileListTable();
        return module_json::getJson($ans);
    }

    /**
     * Разрешить -|- запретить комментарии для определенного файла.
     * @return void
     */
    public function comm_accessAction() {
        $this->_setAccess('comm_access');
    }

    /**
     * Разрешить -|- запретить рейтинг для определенного файла.
     * @return void
     */
    public function rating_accessAction() {
        $this->_setAccess('rating_access');
    }

    /**
     * Удаление отметки
     * @return void
     */
    public function deleteLabelAction() {
        $label = model_gallery::getClass('model_label');
        $label->delete();
    }

    /**
     * Удаление файла
     * @return string
     */
    public function deleteFileAction() {
        return module_json::getJson(model_gallery::getClass('model_file')->deleteFile());
    }

    /**
     * Сортировка файлов в альбоме
     * @return string
     */
    public function sortFileAction() {
        return module_json::getJson(model_gallery::getClass('model_file')->sortFile());
    }

    /**
     * Включение -|- отключние вывода панелей отметок
     * @return string
     */
    public function labelFileAction() {
        return module_json::getJson(model_gallery::getClass('model_file')->label());
    }

    /**
     * Назначение -|- обновление заголовка
     * @return string
     */
    public function settitleAction() {
        return module_json::getJson(model_gallery::getClass('model_file')->setTitle());
    }

    /**
     * Назначение -|- обновление описания
     * @return string
     */
    public function setdescriptionAction() {
        return module_json::getJson(model_gallery::getClass('model_file')->setDescr());
    }

    /**
     * @return string
     */
    public function settagAction() {
        return module_json::getJson(model_gallery::getClass('model_search')->addKeywordsFile());
    }

    /**
     * @return string
     */
    public function addYoutubeAction() {
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
        $_clear = array('www.', '.com', '.ru', 'video.');
        if (is_array($link)) {
            foreach ($link as $feed) {
                if (null != $feed and $feed != '') {
                    $url = parse_url($feed);
                    $url['host'] = str_replace($_clear, '', $url['host']);
                    switch ($url['host']) {
                        case 'smotri':
                            if ($this->_config['allow_smotri_com'])
                                $upload->videoServiceFile('smotri', $url);
                            break;
                        case 'vimeo':
                            if ($this->_config['allow_vimeo_com'])
                                $upload->videoServiceFile('vimeo', $url);
                            break;
                        case 'rutube':
                            if ($this->_config['allow_rutube_ru'])
                                $upload->videoServiceFile('rutube', $url);
                            break;
                        case 'gametrailers':
                            $upload->videoServiceFile('gametrailers', $url);
                            break;
                        case 'youtube':
                            $f = $file->tubeParse($url);
                            parse_str($url['query']);
                            if ($v)
                                $upload->youTubeImages($v, $f);
                            break;
                    }
                }
            }
        }
        if (GALLERY_MODE === 1) {
            $alb = model_gallery::getClass('model_albom');
            return module_json::getJson($alb->updateAlbom($id));
        }
    }

    /**
     * @return string
     */
    public function setplayerAction() {
        return module_json::getJson(model_gallery::getClass('model_file')->setDefaultPlayer());
    }

    /**
     * @return string
     */
    public function deletecoverAction() {
        return module_json::getJson(model_gallery::getClass('model_file')->deleteCover());
    }

    /**
     * @return string
     */
    public function setvideocoverAction() {
        return module_json::getJson(model_gallery::getClass('model_file')->setCover());
    }

    /**
     * Удаление альбома
     * @return void
     */
    public function deletealbomAction() {
        model_gallery::getClass('model_albom')->deleteAlbum();
    }

    /** Изменение прав доступа к альбому.
     * @return void
     */
    public function changepermalbumAction() {
        model_gallery::getClass('model_albom')->setPermission();
    }

    /**
     * @return string
     */
    public function setcoverAction() {
        return module_json::getJson(model_gallery::getClass('model_albom')->setCoverAlbom());
    }

    /**
     *
     */
    public function deletecatAction() {
        model_gallery::getClass('model_category')->deleteCat();
    }

    /**
     *
     */
    public function approvecommentsAction() {
        $id = (int) model_request::getPost('id');
        if ($id) {
            $comm = $this->_db->super_query('SELECT id FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_comments WHERE id='{$id}' AND approve='0' LIMIT 1");
            if ($comm) {
                $this->_db->query('UPDATE ' . DBNAME . '.' . PREFIX . "_dg_gallery_comments SET approve='1' WHERE id='{$comm['id']}' LIMIT 1");
            }
        }
    }

    /**
     * @param $name
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
     * @return string
     */
    public function loadfileAction() {
        $html = '';
        $cat = null;
        $result = model_gallery::getClass('model_file')->loadUserFile(false);
        $category = model_gallery::getClass('model_category')->getAllCategory();
        global $config;
        if ($result['mysqlId'])
            while ($row = $this->_db->get_row($result['mysqlId'])) {
                $cat = (isset($category[$row['parent_id']]) && is_array($category[$row['parent_id']])) ? $category[$row['parent_id']] : null;
                $link = $config['http_home_url'] . 'gallery/full/' . $row['parent_id'] . '-' . $cat['meta_data']['meta_title'] . '.' . $row['id'];
                $preview = model_file::getThumb($row['path']);
                $name = substr($row['path'], strrpos($row['path'], '/') + 1);
                $html .=<<<HTML
         <tr class="file-item" style="padding-top:10px;">
                <td>#{$row['id']}&nbsp;<a href="{$link}"  target="_blank" class="links" > $name</a></td>
                <td width="100">&nbsp;
                  <a href="javascript:void(0)" onclick="deleteFile('{$row['id']}'); $(this).parent().parent().hide()" class="delete" >delete</a>
                  <a href="{$preview}" onclick="return hs.expand(this)" target="_blank" class="preview" rel="{$preview}">preview</a>
                  <a href="javascript:void(0)" onclick="editFile('{$row['id']}'); return false;"  class="setting-tbl">setting</a>
                  </td>
              </tr>
HTML;
            }
        return module_json::getJson(array('tpl' => $html, 'count' => $result['count'], 'page' => model_request::getRequest('page')));
    }

    /**
     * @return string
     */
    public function getfileAction() {
        $_id = model_request::getPost('id');
        return module_json::getJson(model_gallery::getClass('model_file')->getFile($_id));
    }

    /**
     * @return mixed
     */
    public function setfileparamAction() {
        return model_gallery::getClass('model_file')->setParam();
    }

}

