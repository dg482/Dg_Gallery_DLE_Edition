<?php

/* *
 * Работа с категориями галереи, добавление, изменение, кэш и т.д.
 *
 * @package gallery
 * @author Dark Ghost
 * @copyright 2011
 * @access public
 * @since 1.5.3 (07.2011)
 *
 */

class model_category {

    /**
     * @var obj
     * */
    private $_db;

    /**
     * @var obj
     * */
    private $_cache;

    /**
     *
     * @var array
     */
    protected $_config;

    /**
     *
     * @var bool
     */
    public static $SITE;

    /**
     *
     * @var type
     */
    private $_data;
    private $_cat;

    /**
     * @return void
     */
    public function __construct() {
        $this->_db = model_gallery::getRegistry('module_db');
        $this->_cache = model_gallery::getRegistry('model_cache_file');
        $this->_config = model_gallery::getRegistry('config');
    }

    /**
     * Венуть все категории.
     * @return array
     */
    public function getAllCategory() {
        if (null === $this->_cat)
            $this->_cat = model_cache_file::get('category');
        if (null === $this->_cat) {
            $this->_cat = $this->setCategory();
        }
        return $this->_cat;
    }

    /**
     * Вернуть категории доступные для создания альбома.
     *
     * @return array
     */
    public function getAccessGranted() {
        $cat = $this->getAllCategory();
        $allow = array();
        foreach ($cat as $data) {
            $access_data = (is_string($data['access_data'])) ? unserialize($data['access_data']) : $data['access_data'];
            $access_data['access'] = explode(',', $access_data["access_load"]);
            if (in_array(model_gallery::$user['user_group'], $access_data['access']))
                $allow[$data['id']] = $data;
        }
        return (is_array($allow) && count($allow)) ? $this->setTree($allow) : null;
    }

    /**
     * Вернуть кэш в формате json
     * @param mixed $id
     * @return
     */
    public function getCategoryJson($id) {
        $cat = model_cache_file::get('category');
        if (is_array($cat [$id])) {
            return module_json::getJson($cat [$id]);
        }
        return null;
    }

    /**
     * Вернуть данные для навигации в админпанели.
     * @return string
     */
    public function getCategoryNavJson() {
        $cat = $this->getAllCategory();
        $cahe = array();
        $cahe ['title'] = 'Категории';
        $k = 0;
        foreach ($cat as $k => $v) {
            $cahe [$k] ['title'] = $v ['title'];
            $cahe [$k] ['parent_id'] = $v ['parent_id'];
        }
        return module_json::getJson($cahe);
    }

    /**
     * @return array
     */
    public function getAllCategoryJson() {
        return model_cache_file::getJson('category');
    }

    /**
     *
     * @param int $id
     * @return
     */
    public function getCategory($id) {
        if (null === $this->_data)
            $this->_data = model_cache_file::get('category');
        return (is_array($this->_data [$id])) ? $this->_data [$id] : null;
    }

    /**
     * model_category::setCategory()
     *
     * @return void
     */
    public function setCategory() {
        $this->_db->query('SELECT SQL_NO_CACHE * FROM ' . DBNAME . '.' . PREFIX . '_dg_gallery');
        $cat = array();
        while ($row = $this->_db->get_row()) {
            $cat [$row ['id']] = $row;
            $cat [$row ['id']]['meta_data'] = unserialize($row['meta_data']);
            $cat [$row ['id']]['access_data'] = unserialize($row['access_data']);
        }
        $tree = $this->setTree($cat);
        $this->_cache->setCache('category', $cat);
        $this->_cache->setCache('tree_category', $tree);
        $this->_cache->setCacheJson('category', $cat);
        $this->_cache->setCacheJson('tree_category', $tree);
    }

    /**
     * Отсортировать категориив дерево
     * @param array $arr
     * @return array
     */
    private function setTree($arr) {
        $tree = array();
        if (!is_array($arr)) {
            return false;
        }
        foreach ($arr as & $rec) {
            if (empty($rec ['parent_id'])) {
                $tree [$rec ['id']] = &$rec;
            } else {
                foreach ($arr as & $parent_rec) {
                    if ($rec ['parent_id'] == $parent_rec ['id']) {
                        if (!is_array($parent_rec ['children'])) {
                            $parent_rec ['children'] = array();
                        }
                        $parent_rec ['children'] [$rec ['id']] = &$rec;
                    }
                }
            }
        }
        return $tree;
    }

    /**
     * Добавление категорий в б.д., валидация данных, обновление кэша.
     * @return void
     */
    public function addCategory() {
        $data = model_request::getPost('config');
        $meta_data = array();
        $access_data = array();
        if (null === $data) {
            return false;
        }
        $insert = array();
        $id = (int) model_request::getPost('id_category');
        $insert ['title'] = $data ['title']; //check title
        if ($insert ['title'] == '') {
            return;
        }
        $insert ['symbol'] = substr($insert ['title'], 0, 1); //get dle parse
        //
        model_search::addLetter($insert ['symbol']);

        $parse = assistant::getRegistry('parse');
        $parse->allow_code = false;
#$parse->safe_mode = true;
        $descr = $this->_db->safesql($parse->process($data ['descr']));
        $descr = str_replace(array('<p>&nbsp;</p>', '\r', '\n'), array('<br />', '', ''), $descr);
        $meta_data ['descr'] = $descr; //metatag
        if ($data ['meta_keywords'] == '') {
            $word = model_metaTag::getKeyword($data ['descr']);
            $meta_data ['meta_keywords'] = (is_array($word)) ? implode(',', $word) : '';
        } else {
            $meta_data ['meta_keywords'] = $data ['meta_keywords'];
        }
        $meta_descr = '';
        if ($data ['meta_descr'] == '') {
            $meta_descr = strip_tags($parse->process($data ['descr']));
        } else {
            $meta_descr = strip_tags($parse->process($data ['meta_descr']));
        }
        if ($meta_descr != '') {
            $meta_descr = substr($meta_descr, 0, 200);
            $meta_data ['meta_descr'] = $meta_descr;
        }
        if ($data ['meta_title'] != '') {
            $meta_data ['meta_title'] = model_metaTag::totranslit($data ['meta_title']);
        } else {
            $meta_data ['meta_title'] = model_metaTag::totranslit($insert ['title']);
        }
        $meta_data['title'] = $insert['title'];
        $access_data ["access_load"] = (isset($data ['accessupload']) and is_array($data ['accessupload'])) ?
                implode(',', $_POST ['config'] ['accessupload']) : '1';
        $access_data ["access"] = (isset($data ['access']) and is_array($data ['access'])) ? implode(',', $_POST ['config'] ['access']) : '1';
        $cover = $this->getCover($id);
        $meta_data ['cover'] = ($cover ['path'] and $cover ['path'] != '') ? $cover ['path'] : '';
        $insert ['parent_id'] = intval($data ['parent_id']);
        $dat = array();
        $update = false;
        if ('update_category' == model_request::getPost('action')) {
            $cover = $this->getCover(model_request::getPost('id_category'));
            $meta_data ['cover'] = ($cover ['path'] and $cover ['path'] != '') ? $cover ['path'] : '';
            $update = true;
            foreach ($insert as $filed => $value) {
                $dat [$filed] = "{$filed}='" . $this->_db->safesql($parse->process($value)) . "'";
            }
            $dat ['meta_data'] = "meta_data='" . serialize($meta_data) . "'";
            $dat ['access_data'] = "access_data='" . serialize($access_data) . "'";
        } else {//insert
            foreach ($insert as $filed => $value) {
                $dat [$filed] = "'" . $this->_db->safesql($parse->process($value)) . "'";
            }
            $dat ['meta_data'] = "'" . serialize($meta_data) . "'";
            $dat ['access_data'] = "'" . serialize($access_data) . "'";
        }

        if (!$update) {//insert
            $this->_db->query('INSERT INTO ' . PREFIX . '_dg_gallery (' . implode(',', array_keys($dat)) . ') VALUES (' . implode(',', $dat) . ')');
            $cat_id = $this->_db->insert_id(); //update file
            if ($cover ['id']) {
                $this->_db->query('UPDATE ' . PREFIX . "_dg_gallery_file SET parent_id='{$cat_id}' WHERE id='{$cover['id']}' LIMIT 1");
            }
        } else {//update
            $this->_db->query('UPDATE  ' . PREFIX . "_dg_gallery SET " . implode(',', $dat) . " WHERE id='{$id}' LIMIT 1");
        }//update cache
        $this->setCategory();
    }

    /**
     * model_category::getCover()
     *
     * @param integer $id
     * @return
     */
    public function getCover($id = 0) {
        $id = intval($id);
        return $this->_db->super_query('SELECT SQL_NO_CACHE id, path FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE parent_id='{$id}' AND status='folder_cover' LIMIT 1 ");
    }

    /**
     * model_category::catTree()
     *
     * @param bool $t
     * @param mixed $a
     * @param int $sss
     * @return string
     */
    public function catTree($t = false, $a = null, $sss = 0) {
        $html = '';
        $s = '';
        $this->param ['cat'] = model_cache_file::get('tree_category');
        if (null === $this->param ['cat']) {
            $this->setCategory();
            $this->param ['cat'] = model_cache_file::get('tree_category');
        }
        if (is_array($this->param ['cat'])) {
            if (!$t) {
                $html .= '<ul class="tree categories">';
                foreach ($this->param ['cat'] as $key => $val) {
                    if (self::$SITE)
                        $val['meta'] = is_string($val['meta_data']) ? unserialize($val['meta_data']) : $val['meta_data'];



                    if (strlen($val ['title']) > 35)
                        $val ['title'] = substr(trim($val ['title']), 0, 35) . '...';
                    if (!empty($this->param ['cat'] [$key] ['children'])) {
                        $r = count($this->param ['cat'] [$key] ['children']);
                        if ($sss <= 1) {
                            $s = 'tree-item-main parent last';
                        } else {
                            $s = 'tree-item-main parent';
                        }
                        if (null === self::$SITE) {
                            $html .= '<li class="' . $s . '">
                        <span class="item box-slide-head">
<a href="javascript:void(0)" id="' . $val ['id'] . '"  class="category" >' . $val ['title'] . '</a></span>
<ul id="albom_' . $val ['id'] . '" class="hidden albom_list"></ul>';
                        } else {
                            $html .= '<li class="' . $s . '">
                        <span class="item box-slide-head">
<a href="' . HOME_URL . 'gallery/show/' . $val['id'] . '-' . $val['meta']['meta_title'] . '" class="category" >' . $val ['title'] . '</a></span>';
                        }
                        $html .= $this->catTree(true, $this->param ['cat'] [$key] ['children'], $r);
                        $html .= '</li>';
                    } else {
                        if ($sss <= 1) {
                            $s = 'tree-item-main  last';
                        } else {
                            $s = 'tree-item-main  ';
                        }
                        if (null === self::$SITE) {
                            $html .= '<li class="' . $s . '"><span class="item">
<a href="javascript:void(0)" id="' . $val ['id'] . '" class="category" >' . $val ['title'] . '</a></span>
<ul id="albom_' . $val ['id'] . '" class="hidden albom_list"></ul></li>';
                        } else {
                            $html .= '<li class="' . $s . '"><span class="item">
<a href="' . HOME_URL . 'gallery/show/' . $val['id'] . '-' . $val['meta']['meta_title'] . '" class="category" >' . $val ['title'] . '</a></span></li>';
                        }
                    }
                }
                $html .= '</ul>';
            } else {
                if (is_array($a)) {
                    $f = 0;
                    $df = count($a);
                    $html .= '<ul class="box-slide-body ">';
                    foreach ($a as $k => $v) {
                        if (self::$SITE) {
                            $v['meta'] = (is_string($v['meta_data'])) ? unserialize($v['meta_data']) : $v['meta_data'];
                        }

                        $f++;
                        if (strlen($v ['title']) > 35)
                            $v ['title'] = substr(trim($v ['title']), 0, 35) . '...';
                        if (!empty($a [$k] ['children'])) {
                            $d = count($a [$k] ['children']);
                            if ($sss == $f) {
                                $s = 'tree-item parent last';
                            } else {
                                $s = 'tree-item parent';
                            }
                            if (null === self::$SITE) {
                                $html .= ' <li class="' . $s . '"> <span class="item box-slide-head">
<a href="javascript:void(0)" id="' . $v ['id'] . '"  class="category" >' . $v ['title'] . '</a></span>
<ul id="albom_' . $v ['id'] . '"  class="hidden albom_list"></ul>';
                            } else {
                                $html .= ' <li class="' . $s . '"> <span class="item box-slide-head">
<a href="' . HOME_URL . 'gallery/show/' . $v['id'] . '-' . $v['meta']['meta_title'] . '"  class="category" >' . $v ['title'] . '</a></span>';
                            }

                            $html .= $this->catTree(true, $a [$k] ['children'], $d);
                            $html .= '</li>';
                        } else {
                            if ($df == $f) {
                                $s = 'tree-item last';
                            } else {
                                $s = 'tree-item ';
                            }
                            if (null === self::$SITE) {
                                $html .= '<li class="' . $s . '"><span class="item box-slide-head">
                                <a href="javascript:void(0)" id="' . $v ['id'] . '"  class="category" >' . $v ['title'] . '</a></span>
<ul id="albom_' . $v ['id'] . '" class="hidden albom_list"></ul></li>';
                            } else {
                                $html .= '<li class="' . $s . '"><span class="item box-slide-head">
                                <a href="' . HOME_URL . 'gallery/show/' . $v['id'] . '-' . $v['meta']['meta_title'] . '"  class="category" >' . $v ['title'] . '</a></span></li>';
                            }
                        }
                    }
                    $html .= '</ul>';
                }
            }
        }
        return $html;
    }

    /**
     * model_category::getCatInfo()
     *
     * @param int $id
     * @return
     */
    public function getCatInfo($id) {
        $info = model_cache_file::get('category');
        $info_cat = null;
        foreach ($info as $value) {
            if ($value['id'] == $id) {
                $info_cat = $value;
            }
        }
        $access_data = (is_string($info_cat['access_data'])) ? unserialize($info_cat['access_data']) : $info_cat['access_data'];
        $meta_data = (is_string($info_cat['meta_data'])) ? unserialize($info_cat['meta_data']) : $info_cat['meta_data'];
        $info_cat ['accessupload'] = $access_data ['access_load'];
        $info_cat ['access'] = $access_data ['access'];
        $info_cat ['descr'] = stripslashes(stripslashes($meta_data['descr']));
        $info_cat ['meta_descr'] = stripslashes($meta_data['meta_descr']);
        $info_cat ['meta_keywords'] = stripslashes($meta_data['meta_keywords']);
        $info_cat ['guestMode'] = $meta_data ['guest_mode'];
        $info_cat['meta_title'] = $meta_data['meta_title'];
        return $info_cat;
    }

    /**
     *
     * @param int $parent_id
     * @param int $limit
     * @return obj
     */
    public function getCatAlbom($parent_id, $limit = 0) {
        $limit = ( $limit) ? ' LIMIT 0,' . $limit : '';
        return $this->_db->query('SELECT id, title FROM ' . PREFIX . "_dg_gallery_albom WHERE parent_id='{$parent_id}' {$limit}");
    }

    /**
     *
     * @return array
     */
    public function getCatPage() {
        $view_cat = array();
        $offset = (int) model_request::getRequest('page');
        $offset = ($offset > 1) ? (($offset - 1) * $this->_config['indexPage']) : 0;
        $cat = $this->getAllCategory();
        foreach ($cat as $value) {
            $access_data = (is_string($value['access_data'])) ? unserialize($value['access_data']) : $value['access_data'];
            $access_data['access'] = explode(',', $access_data['access']);
            $view_cat[$value['id']] = $value;
            $view_cat[$value['id']]['access_cat'] = 0;
            if (in_array(model_gallery::$user['user_group'], $access_data['access'])) {
                $view_cat[$value['id']]['access_cat'] = 1;
            }
        }
        return array(
            'cat' => array_slice($view_cat, $offset, $this->_config['indexPage']),
            'count' => count($view_cat)
        );
    }

    /**
     *
     * @param int $id
     * @return mixed
     */
    public function getAccessCat($id, $data = null) {
        $value = (null === $data) ? $this->getCategory($id) : $data;
        $access_data = (is_string($value['access_data'])) ? unserialize($value['access_data']) : $value['access_data'];
        $access_data['access'] = explode(',', $access_data['access']);
        return (is_array($access_data['access'])) ?
                in_array(model_gallery::$user['user_group'], $access_data['access']) : false;
    }

    /**
     *
     * @param int $id
     * @return void
     */
    public function deleteCat($id = null) {
        if (null === $id)
            $id = (int) model_request::getPost('id');
        if ($id) {
            $row = $this->_db->super_query('SELECT SQL_NO_CACHE * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery WHERE id='{$id}'");
            #   $meta = unserialize($row['mata_data']);
            if ($row) {
                $cover = $this->getCover($row['id']);
                $cover = str_replace('%replace%', 'cover', $cover['path']);
                if (file_exists(ROOT_DIR . $cover)) {
                    unlink(ROOT_DIR . $cover);
                }
                $this->_db->query('DELETE FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE id='{$cover['id']}'");
            }
            $this->_db->query('SELECT SQL_NO_CACHE * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery WHERE parent_id='{$id}'");
            while ($row = $this->_db->get_row()) {
                $this->deleteCat($row['id']);
            }
            $alb = model_gallery::getClass('model_albom');
            $user = model_gallery::getClass('model_user');
            $_id = array();
            $this->_db->query('SELECT SQL_NO_CACHE * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE parent_id='{$id}'");
            while ($row = $this->_db->get_row()) {
                $_id[] = $row;
            }
            foreach ($_id as $var) {
                $id = $var['id'];
                model_gallery::getClass('model_file')->deleteAlbumFiles($id);
                $userInfo = $user->getInfo($row['author_id']);
                if ($userInfo['albom']) {
                    $user->updateAlbom('-1', $var['author_id']);
                }
                $this->_db->query('DELETE FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE id='{$id}' LIMIT 1");
            }
            model_gallery::getClass('model_file')->deleteCatFiles($id);


            $this->_db->query('DELETE FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery WHERE id='{$id}'");
            $this->setCategory();
        }
    }

}

