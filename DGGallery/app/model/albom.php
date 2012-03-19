<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */


class model_albom extends model_gallery {

    /**
     *
     * @var object
     */
    protected $_cache;

    /**
     *
     * @var object
     */
    protected $_db;

    /**
     *
     * @var int
     */
    protected $_id;

    /**
     *
     * @var string
     */
    protected $_files;

    /**
     *
     * @var mixed
     * @deprecated
     */
    protected $_loadComm;

    /**
     *
     * @var array
     */
    protected $_config;

    /**
     *
     * @var string
     */
    public static $MODE;

    /**
     *
     * @var array
     */
    protected $_info;

    /**
     *
     */
    public function __construct() {
        parent::__construct();
        $this->setId(model_request::getRequest('id'));
        $this->_config = model_gallery::getRegistry('config');
    }

    /**
     * @param $data
     * @param null $redirect
     * @return bool|string
     */
    public function add($data, $redirect = null) {
        if (null === $data) {
            return false;
        }
        if ($this->_config['mode'] == 2) {//archive mode
            return false;
        }
        $meta_data = array();
        $access_data = array();
        $_data = array();
        if (null == $data['id'])
            if ($data['parent_id'] == 0) {
                return '<div class="error b-4"><p>Невыбрана родительская категория.</p></div>';
            }

        if (null == $data['id'])
            $data['id'] = (int) model_request::getRequest('id');
        if (class_exists('assistant')) {
            $_data['user_id'] = assistant::$_user ['user_id'];
            $data ['author'] = $this->_db->safesql(assistant::$_user ['name']);
        } else {
            $_data['user_id'] = model_gallery::$user['user_id'];
            $data ['author'] = $this->_db->safesql(model_gallery::$user ['name']);
        }

        $parse = null;
        require_once ROOT_DIR . '/engine/classes/parse.class.php';
        $this->_config['allow_wysiwyg'] = 1; //TOFO: добавить в настройки
        if ($this->_config['allow_wysiwyg']) {
            $parse = new ParseFilter(Array('div', 'span', 'p', 'br', 'strong', 'em', 'ul', 'li', 'ol'), Array(), 0, 1);
            $parse->wysiwyg = true;
            $parse->safe_mode = true;
        } else {
            $parse = new ParseFilter(array(), array(), 1, 1);
            $parse->safe_mode = true;
        }

        $data ['title'] = str_replace(array('<', '>'), array('&lt;', '&gt;'), $data ['title']);
        $data ['title'] = $parse->process($data ['title']);
        $symbol = strtolower(substr($data['title'], 0, 1));
        if ($data['title'] == '') {
            return '<div class="error b-4"><p>Незаполнено поле "Название".</p></div>';
        }
        $meta_data ['meta_title'] = ($data ['meta_title'] != '') ? totranslit($parse->process($data ['meta_title'])) :
            totranslit($data ['title']);
        $meta_data ['meta_descr'] = ($data ['meta_descr'] != '') ? $parse->process($data ['meta_descr']) : null;
        $meta_data ['meta_keywords'] = ($data ['meta_keywords'] != '') ? $parse->process($data ['meta_keywords']) : null;
        $data ['descr'] = stripslashes($data ['descr']);
        $data ['descr'] = ($data ['descr'] != '') ? $parse->BB_Parse($parse->process($data ['descr'])) : null;
        $data ['descr'] = str_replace(array('<p>&nbsp;</p>', '\r', '\n'), array('<br />', '', ''), $data ['descr']);
        $meta_data ['description'] = base64_encode($data ['descr']);
        $access_data ['guestMode'] = (isset($data['guestMode'])) ? 1 : 0;
        $access_data ['accessView'] = (is_array($data ['accessView'])) ? implode(',', $data ['accessView']) : 1;
        $access_data ['accessComments'] = (is_array($data ['accessComments'])) ? implode(',', $data ['accessComments']) : 1;
        $access_data ['accessCommentsFile'] = (is_array($data ['accessCommentsFile'])) ? implode(',', $data ['accessCommentsFile']) : 1;

        if (null == $data['id']) { //add
            $approve = 1;
            //            if ($this->_config['albomApprove']) {
            //                $this->_config['albomApprove'] = explode(',', $this->_config['albomApprove']);
            //                if (in_array(model_gallery::$user['user_group'], $this->_config['albomApprove'])) {
            //                    $approve = 0;
            //                }
            //            }

            $this->_db->query('INSERT INTO ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom (author,parent_id,author_id,title,meta_data,access_data,data,approve,symbol)
		 VALUES ('{$data['author']}','{$data['parent_id']}','{$_data['user_id']}','{$data['title']}','" . serialize($meta_data) . "',
		 '" . serialize($access_data) . "','" . serialize($_data) . "','{$approve}' ,'{$symbol}' )");
            $insertId = $this->_db->insert_id();
            if ($data ['parent_id']) {
                $this->_db->query('UPDATE ' . PREFIX . "_dg_gallery SET albom_num=albom_num+1 WHERE id ='{$data['parent_id']}'");
            }
            model_search::addLetter($symbol);
            model_gallery::getClass('model_category')->setCategory();
            model_gallery::getClass('model_user')->updateAlbom('+1', $_data['user_id']);
            if ($this->_config['sendEmailalbom']) {
                $msg = 'Автор: ' . $data['author'] . '<br />';
                $msg .='Название: ' . $data['title'];
                model_mail::send('Добавлен новый альбом.', $msg);
            }

            if ($redirect) {
                return $insertId;
            } else {
                //adminpanel
                model_request::_redirect('?mod=dg_gallery&action=open&id=' . $insertId);
            }
        } else {
            $this->_db->query('UPDATE ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom SET  title='{$data['title']}',meta_data='" . serialize($meta_data) . "' WHERE id='{$data['id']}' LIMIT 1");
            $this->updateAlbom($data['id']);

            return true;
        }
    }

    /**
     * @param int $id
     * @return array|mixed|null
     */
    public function openAlbom($id=0) {
        if ($id)
            $this->_id = $id;
        if (null === $this->_id) {
            $this->setId(model_request::getRequest('id'));
        }
        $info = model_cache_file::get($this->_id . '_alb');
        if (null === $info) {
            $info = $this->updateAlbom($this->_id);
        }
        $this->_files = $info['file'];
        $this->_info = $info['info'];
        return $info;
    }

    /**
     * @param null $id
     */
    public function deleteAlbum($id = null) {
        if (null === $id)
            $id = model_request::getRequest('id');
        if ($id) {
            model_gallery::getClass('model_file')->deleteAlbumFiles($id);
            $check = $this->_db->super_query('SELECT author_id,parent_id FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE id='{$id}' LIMIT 1");
            $user = model_gallery::getClass('model_user');
            $userInfo = $user->getInfo($check['author_id']);
            if ($userInfo['albom']) {
                $user->updateAlbom('-1', $check['author_id']);
            }
            $cat = $this->_db->super_query('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery WHERE id='{$check['parent_id']}' LIMIT 1");
            if ($cat['albom_num']) {
                $this->_db->query('UPDATE ' . DBNAME . '.' . PREFIX . "_dg_gallery SET albom_num=albom_num-1 WHERE id='{$cat['id']}' LIMIT 1");
            }
            $this->_db->query('DELETE FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE id='{$id}' LIMIT 1");
        }
        model_gallery::getClass('model_category')->setCategory();
    }

    /**
     *
     * @param int $id
     * @return void
     */
    public function setId($id) {
        $this->_id = (int) $id;
    }

    /**
     *
     * @return array
     */
    public function getFileList() {
        if (!$this->_files) {
            $this->openAlbom();
        }
        return $this->_files;
    }

    /**
     *
     * @param array $file
     * @return void
     */
    public function setFiles($file) {
        $this->_files = $file;
    }

    /**
     *
     * @param array $info
     * @return void
     */
    public function setInfoAlbom($info) {
        $this->_info = $info;
    }

    /**
     * Обновление кэша альбома
     * @param int $id
     * @return array
     */
    public function updateAlbom($id) {
        if (!$id && !$this->checkAlbom($id)) {
            return false;
        }
        if (!$id || $id == '') {
            return false;
        }

        if (null == $this->_db) {
            $this->_db = model_gallery::getRegistry('module_db');
        }
        $id = intval($id);
        if ($id) {
            $data = $this->_getAlbom($id);
            if (null === $data) {
                return false;
            }
            $this->_cache->setCache($id . '_alb', $data);
            $this->_cache->setCacheJson($id . '_alb', $data);
            return $data;
        }
    }

    /**
     *
     * @param int $id
     * @return array
     */
    protected function _getAlbom($id) {
        if (null == $this->_db) {
            $this->_db = model_gallery::getRegistry('module_db');
        }
        $file = array();
        $_file = model_gallery::getClass('model_file');
        $idq = $_file->getFiles($id);
        $_status = array('albom', 'youtube', 'video', 'smotri.com', 'vimeo', 'rutube', 'gametrailers');
        while ($row = $this->_db->get_row($idq)) {
            if (in_array($row['status'], $_status)) {
                if ($row['other_dat']) {
                    $row ['other_dat'] = unserialize($row['other_dat']);
                }
                $row['descr'] = stripslashes($row['descr']);
                if (isset($file[$row['position']])) {
                    $file[$row['id']] = $row;
                }else
                    $file[$row['position']] = $row;
            }
        }
        // sort($file, SORT_NUMERIC);
        $info = $this->_db->super_query('SELECT SQL_NO_CACHE * FROM ' . PREFIX . "_dg_gallery_albom  WHERE id='{$id}'");
        $info['info_author'] = $this->_db->super_query('SELECT SQL_NO_CACHE * FROM ' . DBNAME . '.' . PREFIX . "_users WHERE name='{$info['author']}' LIMIT 1");
        $info ['meta_data'] = unserialize($info['meta_data']);
        $info ['meta_data']['description'] = base64_decode($info ['meta_data']['description']);
        unset($info['info_author']['password']);
        $this->setInfoAlbom($info);
        return array('info' => $info, 'file' => $file);
    }

    /**
     * @return string
     */
    public function getParentAlbon() {
        $html = '';
        if (null == $this->_db) {
            $this->_db = model_gallery::getRegistry('module_db');
        }
        $this->_db->query('SELECT * FROM ' . PREFIX . "_dg_gallery_albom  WHERE parent_id='{$this->_id}'");
        while ($row = $this->_db->get_row()) {
            $html .= '<li><span class="item albom"><a href="javascript:void(0);" class="albom-open" rel="' . $row['id'] . '">' . $row['title'] . '</a></span></li>';
        }
        return $html;
    }

    /**
     * @return string
     */
    public function getFileListTable() {
        $html = '';
        if (null == $this->_db) {
            $this->_db = model_gallery::getRegistry('module_db');
        }
        $file = model_gallery::getClass('model_file');
        $idq = $file->getFiles($this->_id);
        while ($row = $this->_db->get_row($idq)) {
            $row['path'] = substr($row['path'], strrpos($row['path'], '/') + 1);
            if ($row['status'] != 'folder_cover')
                $html .= '<tr role="' . $row['id'] . '">
                  <td width="25">
                  <input type="text" name="file[' . $row['id'] . ']" value="' . $row['position'] . '" /></td>
                  <td>&nbsp;' . $row['path'] . '</td>
                  <td width="80">&nbsp;<a class="trash" rel="' . $row['id'] . '" href="javascript:void(0)">delete</a></td>
                </tr>';
        }
        return $html;
    }

    /**
     *
     * @return string
     */
    public function getTree() {
        $category = null;
        $category = model_cache_file::get('category');
        $modelCat = assistant::getRegistry('model_category');
        $alb = $this->openAlbom();
        if (null == $alb) {
            $alb = $this->updateAlbom($this->_id);
        }
        if (null == $this->_db) {
            $this->_db = model_gallery::getRegistry('module_db');
        }

        $mysqlId = $modelCat->getCatAlbom($alb['info']['parent_id'], 0);
        $li = '';
        $class = '';
        if ($mysqlId)
            while ($row = $this->_db->get_row($mysqlId)) {
                $class = ($row['id'] == $this->_id) ? ' open ' : '';
                $li .='<li><span class="item albom ' . $class . '"><a href="javascript:void(0);" class="albom-open" rel="' . $row['id'] . '">' . $row['title'] . '</a></span></li>';
            }
        return <<<HTML
<ul class="tree categories">
<li class="tree-item-main active"> <span class="item box-slide-head">
<a href="javascript:void(0);">{$category [$alb['info']['parent_id']]['title']}</a></span>
<ul class="albom_list">
    {$li}
</ul>
</li></ul></li></ul>
HTML;
    }

    /**
     * Проверка существования альбома.
     * @param null $id
     * @return bool
     */
    public function checkAlbom($id = null) {
        if (null == $this->_db) {
            $this->_db = model_gallery::getRegistry('module_db');
        }
        if ($id)
            return ($this->_db->super_query('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE id='{$id}' LIMIT 1")) ? true : false;

        if ($this->_id)
            return ($this->_db->super_query('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE id='{$this->_id}' LIMIT 1")) ? true : false;
    }

    /**
     * Обновлениее параметров альбома.
     * @param int $id
     * @param array $data
     * @return void
     */
    public function updateFields($id, $data) {
        $up = array();
        foreach ($data as $key => $value) {
            $up[] = $key . "='" . $value . "'";
        }
        $this->_db->query('UPDATE ' . PREFIX . "_dg_gallery_albom SET " . implode(',', $up) . " WHERE id='{$id}' LIMIT 1");
    }

    /**
     * Изменение прав доступа к альбому
     * @return void
     */
    public function setPermission() {
        $id = (int) model_request::getPost('id');
        $perm = trim(strtolower(model_request::getPost('perm')));
        $key = null;
        $info = null;
        if ($id) {
            $info = $this->_getAlbom($id);
            if (false === $this->_isAuthor() && false === $this->_isAdmin()) {//check access
                die('Access Denied.');
            }
            $permissoin = unserialize($info['info']['access_data']);

            switch ($perm) {
                case 'accessview':
                    $key = 'accessView';
                    break;
                case 'accesscomments':
                    $key = 'accessComments';
                    break;
                case 'accesscommentsfile':
                    $key = 'accessCommentsFile';
                    break;
                case 'guestmode':
                    $key = 'guestMode';
                    $permissoin['guestMode'] = model_request::getPost('set');
                    $this->updateFields($id, array(
                        'access_data' => serialize($permissoin)
                    ));
                    $this->updateAlbom($id);
                    return;
                default:
                    return;
            }
            $_permissoin = array();
            foreach ($_POST['set'] as $v) {
                if ($v['value'] != '' && $v['value'])
                    $_permissoin[] = $v['value'];
            }
            $permissoin[$key] = implode(',', $_permissoin);
            $this->updateFields($id, array(
                'access_data' => serialize($permissoin)
            ));
            $this->updateAlbom($id);
        }
    }

    /**
     * Установить абложку альома.
     * @return array
     */
    public function setCoverAlbom() {
        $id = model_request::getPost('id'); //file id
        $alb_id = model_request::getPost('parent_id'); //alb id
        $set = (int) model_request::getPost('set');
        $info = null;
        if ($id && $alb_id) {
            $info = $this->_getAlbom($alb_id);
            if (false === $this->_isAuthor() && false === $this->_isAdmin()) {
                die('Access Denied.');
            }
            $old = (is_string($info["info"]['data'])) ? unserialize($info["info"]['data']) : $info["info"]['data'];
            if (is_string($info["info"]['meta_data']))
                $meta_data = (is_string($info["info"]['meta_data'])) ? unserialize($info["info"]['meta_data']) : $info["info"]['meta_data'];
            else
                $meta_data = $info["info"]["meta_data"];
            $_file = model_gallery::getClass('model_file');
            if ($old['cover_id']) {
                $_oldInfo = $_file->getFile($old['cover_id']);
                $data = (is_string($_oldInfo['other_dat'])) ? unserialize($_oldInfo['other_dat']) : $_oldInfo['other_dat'];
                $data['is_cover'] = 0;
                $_file->updateFile($_oldInfo['id'], array(
                    'other_dat' => serialize($data),
                ));
            }
            //set statua file
            $_fileInfo = $_file->getFile($id);
            if ($_fileInfo ['id']) {
                $other = (is_string($_fileInfo['other_dat'])) ? unserialize($_fileInfo['other_dat']) : $_fileInfo['other_dat'];
                if ($set == 1) {
                    $other['is_cover'] = 1;
                    $meta_data['cover'] = $_fileInfo['path'];
                    $old['cover_id'] = $_fileInfo ['id'];
                } else {
                    $other['is_cover'] = 0;
                    $meta_data['cover'] = '';
                    $old['cover_id'] = 0;
                }
                $_file->updateFile($_fileInfo ['id'], array(
                    'other_dat' => serialize($other),
                ));
                $this->updateFields($alb_id, array(
                    'data' => serialize($old),
                    'meta_data' => serialize($meta_data)
                ));
                return $this->updateAlbom($alb_id);
            }
        }
    }

    /**
     *
     * @param mixed $id
     * @return mixed
     */
    public function count($id) {
        return ($id) ?
            $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE parent_id='{$id}' LIMIT 1") : 0;
    }

    /**
     * Вернуть постраничную выборку при просмотре альомав с сайта.
     * @param $id
     * @param $pageLimit
     * @return array
     */
    public function getPageCategory($id, $pageLimit) {
        $offset = (int) model_request::getRequest('page');
        $offset = ($offset) ? (($offset - 1) * $pageLimit) : 0;
        #$count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE parent_id='{$id}' ");
        $result = $this->getAlbomResult('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE parent_id='{$id}' LIMIT {$offset},{$pageLimit}");
        #$result['count'] = $count['count'];
        return $result;
    }

    /**
     *
     * @param int $pageLimit
     * @return array
     */
    public function getPage($pageLimit = 9) {
        $offset = (int) model_request::getRequest('page');
        $offset = ($offset) ? (($offset - 1) * $pageLimit) : 0;
        $count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . '_dg_gallery_albom ');
        $result = $this->getAlbomResult('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom  LIMIT {$offset},{$pageLimit}");
        $result['count'] = $count['count'];
        return $result;
    }

    /**
     *
     * @param string  $sql
     * @return array
     */
    public function getAlbomResult($sql) {
        $result = null;
        $id = $this->_db->query($sql);
        while ($row = $this->_db->get_row($id)) {
            $result['alb'][$row['id']] = $row;
            $result['alb'][$row['id']]['meta_data'] = unserialize($result['alb'][$row['id']]['meta_data']);
            $result['alb'][$row['id']]['access_cat'] = 0;
            $result['alb'][$row['id']]['access_albom'] = 0;
            $this->_info = $row;
            if (model_gallery::getClass('model_category')->getAccessCat($row['parent_id']) || $this->_isAuthor()) {//check  permission category}
                $result['alb'][$row['id']]['access_cat'] = 1;
            }

            $access_data = unserialize($row['access_data']);
            $result['alb'][$row['id']]['access_data'] = $access_data;
            $access_data['access'] = explode(',', $access_data['accessView']);
            $result['alb'][$row['id']]['access_data']['accessView'] = $access_data['access'];

            if (in_array(model_gallery::$user['user_group'], $access_data['access']) || $this->_isAuthor()) {//check permission album
                $result['alb'][$row['id']]['access_albom'] = 1;
            }

            if (null === model_cache_file::get($row['id'] . '_alb')) {
                if ($this->checkAlbom($row['id']))
                    $this->updateAlbom($row['id']);
            }
        }
        return $result;
    }

    /**
     * Вернуть все данные о случайном файле в альбоме
     * @param int $id
     * @return string
     */
    public function getRandFile($id) {
        $this->setId($id);
        $file = $this->openAlbom();
        $key = null;
        if (is_array($file['file']))
            $key = array_rand($file['file']);
        if (!$file['file'][$key]['path']) {
            return HOME_URL . 'uploads/gallery/assets/no-image.png';
        }
        $preview = str_replace('%replace%/', 'thumbs/', $file['file'][$key]['path']);

        if (file_exists(ROOT_DIR . $preview)) {
            return $preview;
        } else {
            return HOME_URL . 'uploads/gallery/assets/no-image.png';
        }
    }

    /**
     * Метод возвращает данные о альбоме при разных условиях показа.
     * @return array
     */
    public function openAlbomSite() {
        $id = model_request::getRequest('id');

        if (null === $id) {
            return;
        }
        if (GALLERY_MODE === 1)//albom
            $result = model_cache_file::get($id . '_alb');
        if (null === $result) {
            if (GALLERY_MODE === 1)//albom
                if ($this->checkAlbom($id))
                    $result = $this->updateAlbom($id);
        }

        if (is_string($result['info']['meta_data']))
            $result['info']['meta_data'] = unserialize($result['info']['meta_data']);
        $result['info']['access_data'] = unserialize($result['info']['access_data']);
        $result['info']['data'] = unserialize($result['info']['data']);
        $cat = model_gallery::getRegistry('model_category');
        if ($cat instanceof model_category) {
            controller_gallery::$CATEGORY = $cat->getCatInfo($result['info']['parent_id']);
        }
        $this->_files = $result['file'];
        $this->_info = $result['info'];

        #  model_gallery::setRegistry('albomInfo', $this->_info);
        switch (self::$MODE) {
            case 'preview':
            case 'image':

                return array(
                    'json' => model_cache_file::getJson($id . '_alb'),
                    'file' => $result['file'],
                    'info' => $result['info']
                );
            case 'tile':
                $_file_id = model_request::getRequest('id_file');
                $result['count'] = count($result['file']);
                if (is_array($result['file'])) {
                    foreach ($result['file'] as $k => $file) {
                        if ($file['id'] == $_file_id) {
                            array_unshift($result['file'], $file);
                            unset($result['file'][$k]);
                        }
                    }
                } else {
                    #throw new controller_exception('error. 404.');
                }
                # array_unique($result['file']);
                $offset = (int) model_request::getRequest('page');
                $offset = ($offset > 1) ? (($offset - 1) * $this->_config['albomPage']) : 0;
                if (is_array($result['file']))
                    return array(
                        //'json' => model_cache_file::getJson($id . '_alb'),
                        'file' => array_slice($result['file'], $offset, $this->_config['albomPage']),
                        'count' => $result['count'],
                        'info' => $result['info']
                    );
                break;
            default:
                return null;
                break;
        }
    }

    /**
     * Заполнение строки шаблона данными.
     * Метод возвращает список файлов.
     *
     * @param string $str
     * @param dle_template $tpl
     * @return string
     */
    public function getFileListAlbum($str, dle_template $tpl) {
        if ($this->_files) {
            $tpl->template = stripslashes($str);
            $tpl->copy_template = stripslashes($str);
            $config = model_gallery::getRegistry('config_cms');

            foreach ($this->_files as $val) {

                $title = stripslashes($val ['title']);
                $tpl->set('{preview-alt}', $title);
                $tpl->set('{preview-title}', $title);
                //UPDATE: добавлен {item-id} для прокрутки списка слайдера
                $tpl->set('{item-id}', $val['id']);
                if (empty($this->_info ['meta_data']['meta_title']) || $this->_info ['meta_data']['meta_title'] == '') {
                    $this->_info ['meta_data']['meta_title'] = 'albom';
                }

                $tpl->set('{link-file}', $config['http_home_url'] . 'gallery/albom/' . $this->_info['id'] . '-' . $this->_info ['meta_data']['meta_title'] .
                    '.' . $val['id']);


                $tpl->set('{preview}', model_file::getThumb($val['path'], $val['status']));
                $tpl->set('{path}', model_file::getFilePath($val['path']));
                $tpl->compile('list');
            }
            return $tpl->result['list'];
        }
    }

    /**
     * @param int $id
     * @return array
     */
    public function getInfo($id = 0) {
        if ($this->_info)
            return $this->_info;
        elseif (null === $this->_info)
            $this->openAlbom($id);
        return $this->_info;
    }

    /**
     * @return bool
     */
    public function getAccessAlbon() {
        //FIX: 28.08.11   приведение данных к нужному формату
        //при добавление комм строка вмесо масиива в результате возвращает не вероное значение доступа к альбому
        if (is_string($this->_info ["access_data"])) {
            $this->_info ["access_data"] = unserialize($this->_info ["access_data"]);
        }
        $access = explode(',', $this->_info ["access_data"]["accessView"]);
        if ($this->_isAuthor() /* || $this->_isAdmin() */) {
            return true;
        }

        return(is_array($access)) ?
            in_array(model_gallery::$user['user_group'], $access) : false;
    }

    /**
     * @return bool
     */
    public function getAccessComments() {
        $access = explode(',', $this->_info ["access_data"]["accessCommentsFile"]);
        return(is_array($access)) ?
            in_array(model_gallery::$user['user_group'], $access) : false;
    }

    /**
     * @return bool
     */
    protected function _isAuthor() {
        return ($this->_info['author_id'] == self::$user['user_id']);
    }

    /**
     * @return bool
     */
    public function isAuthor() {
        return ($this->_info['author_id'] == self::$user['user_id']);
    }

}
