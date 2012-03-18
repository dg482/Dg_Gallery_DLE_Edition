<?php

/**
 * Работа с файлами.
 *
 * @package gallery
 * @author Dark Ghost
 * @copyright 2011
 * @access public
 * @since 1.5.4 (08.2011)
 */
class model_file extends model_gallery {

    /**
     * reflection $this->_info;
     *
     * @var array
     *
     */
    public $currentFile;

    /**
     *
     * @var array
     */
    protected $_info;

    public function __construct() {
        parent::__construct();
    }

    /**
     *
     * @param int $albom_id
     * @return mysql
     */
    public function getFiles($albom_id) {

        return $this->_db->query('SELECT SQL_NO_CACHE * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE parent_id='{$albom_id}'   ORDER BY  position  ASC ");
    }

    /**
     *
     * @param int $id
     * @return mysql
     */
    public function getFile($id) {
        $this->_info = $this->_db->super_query('SELECT  ' . DBNAME . '.' . PREFIX . "_dg_gallery_file.*," . DBNAME . '.' . PREFIX . '_users.* ' .
                ' FROM ' . DBNAME . '.' . PREFIX . '_dg_gallery_file ' .
                ' LEFT JOIN ' . DBNAME . '.' . PREFIX . '_users ON ' .
                DBNAME . '.' . PREFIX . '_users.name = ' . DBNAME . '.' . PREFIX . '_dg_gallery_file.author ' .
                ' WHERE ' . DBNAME . '.' . PREFIX . "_dg_gallery_file.id='{$id}' LIMIT 1 ");
        $this->_info['other_dat'] = unserialize($this->_info['other_dat']);
        return $this->_info;
    }

    /**
     *
     * @param int $id
     * @param int $parent_id
     * @return array
     */
    public function getNextFile($id, $parent_id) {
        $this->_info = $this->_db->super_query('SELECT  ' . DBNAME . '.' . PREFIX . "_dg_gallery_file.*," . DBNAME . '.' . PREFIX . '_users.* ' .
                ' FROM ' . DBNAME . '.' . PREFIX . '_dg_gallery_file ' .
                ' LEFT JOIN ' . DBNAME . '.' . PREFIX . '_users ON ' .
                DBNAME . '.' . PREFIX . '_users.name = ' . DBNAME . '.' . PREFIX . '_dg_gallery_file.author ' .
                ' WHERE ' . DBNAME . '.' . PREFIX . "_dg_gallery_file.id >'{$id}' AND " .
                DBNAME . '.' . PREFIX . "_dg_gallery_file.parent_id='{$parent_id}' AND status!='folder_cover' AND status!='videocover'
                        ORDER BY " . DBNAME . '.' . PREFIX . "_dg_gallery_file.id ASC LIMIT 1 ");
        $this->_info['other_dat'] = unserialize($this->_info['other_dat']);
        return $this->_info;
    }

    /**
     *
     * @param int $id
     * @param int $parent_id
     * @return array
     */
    public function getPrevFile($id, $parent_id) {
        $this->_info = $this->_db->super_query('SELECT  ' . DBNAME . '.' . PREFIX . "_dg_gallery_file.*," . DBNAME . '.' . PREFIX . '_users.* ' .
                ' FROM ' . DBNAME . '.' . PREFIX . '_dg_gallery_file ' .
                ' LEFT JOIN ' . DBNAME . '.' . PREFIX . '_users ON ' .
                DBNAME . '.' . PREFIX . '_users.name = ' . DBNAME . '.' . PREFIX . '_dg_gallery_file.author ' .
                ' WHERE ' . DBNAME . '.' . PREFIX . "_dg_gallery_file.id <'{$id}' AND " .
                DBNAME . '.' . PREFIX . "_dg_gallery_file.parent_id='{$parent_id}' AND status!='folder_cover' AND status!='videocover'
                        ORDER BY " . DBNAME . '.' . PREFIX . "_dg_gallery_file.id DESC LIMIT 1 ");
        $this->_info['other_dat'] = unserialize($this->_info['other_dat']);
        return $this->_info;
    }

    public function getUserFile($userName, $pageLimit=20) {
        $offset = (int) model_request::getRequest('page');
        $offset = ($offset) ? (($offset - 1) * $pageLimit) : 0;
        return $this->_db->query('SELECT SQL_NO_CACHE * FROM ' . DBNAME . '.'
                        . PREFIX . "_dg_gallery_file WHERE author='{$userName}' AND status!='folder_cover' AND status!='videocover' LIMIT {$offset},{$pageLimit}");
    }

    /**
     *
     * @param int $id
     * @param array $array
     * @return mixed
     */
    public function getFileCache($id, $array, $index = false) {
        if (is_array($array))
            foreach ($array as $key => $value) {
                if ($id == $value['id']) {
                    if ($index)
                        return $key;
                    else
                        return $value;
                }
            }
        return null;
    }

    public function getFileCacheIndex($index, $array) {
        return $array[$index];
    }

    /**
     *
     * @param int $id
     * @param array $info
     * @return void
     */
    private function _deleteFile($id, $info = NULL) {
        if (null === $info) {
            $info = $this->getFile($id);
        }
        if ($info) {
            $this->_deleteImage($info['path']);
            $other = null;
            if ($info['status'] == 'video') {
                $other = unserialize($info['other_dat']);
                if ($other)
                    $this->_delete($other['other_dat']['file_path']);
                $this->_deleteChildren($info['id']);
            }
            $this->_db->query('DELETE FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE id='{$info['id']}' LIMIT 1");
            $this->_db->query('DELETE FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_comments WHERE parent_id='{$info['id']}' AND status='file'");
            $this->_db->query('DELETE FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_tags WHERE parent_id='{$info['id']}' AND status='file'");
        }
    }

    /**
     *
     * @param int $id
     */
    public function deleteCatFiles($id) {
        $this->_db->query('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE parent_id='{$id}' AND status='catfile' ");
        $delete = array();
        while ($row = $this->_db->get_row()) {
            $delete[] = $row['id'];
            $this->_deleteImage($row['path']);
        }
        $_del = implode(',', $delete);
        if ($_del) {
            $this->_db->query('DELETE FROM ' . DBNAME . '.' . PREFIX . '_dg_gallery_file WHERE id  IN (' . $_del . ')');
            $this->_db->query('DELETE FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_comments WHERE  status='file' AND parent_id IN (" . $_del . ')');
            $this->_db->query('DELETE FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_tags WHERE  status='file'  AND parent_id IN (" . $_del . ')');
        }
    }

    /**
     *
     * @param int $id
     * @return void
     */
    public function deleteAlbumFiles($id) {
        $this->_db->query('SELECT SQL_NO_CACHE * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE parent_id='{$id}'");
        $delete = array();
        while ($info = $this->_db->get_row()) {
            $delete[] = $info['id'];
            $file = str_replace('%replace%/', '', $info['path']);
            if (file_exists(ROOT_DIR . $file)) {
                unlink(ROOT_DIR . $file);
            }
            $thumb = str_replace('%replace%/', 'thumbs/', $info['path']);
            if (file_exists(ROOT_DIR . $thumb)) {
                unlink(ROOT_DIR . $thumb);
            }
            $original = str_replace('%replace%/', 'original/', $info['path']);
            if (file_exists(ROOT_DIR . $original)) {
                unlink(ROOT_DIR . $original);
            }
        }

        $_del = implode(',', $delete);
        if ($_del) {
            $this->_db->query('DELETE FROM ' . DBNAME . '.' . PREFIX . '_dg_gallery_file WHERE id  IN (' . $_del . ')');
            $this->_db->query('DELETE FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_comments WHERE  status='file' AND parent_id IN (" . $_del . ')');
            $this->_db->query('DELETE FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_tags WHERE  status='file'  AND parent_id IN (" . $_del . ')');
        }
        model_cache_file::delete($id . '_alb');
    }

    /**
     *
     * @param string $path
     * @return void
     */
    private function _deleteImage($path) {
        if ($path) {
            $file = str_replace('%replace%/', '', $path);
            if (file_exists(ROOT_DIR . $file)) {
                unlink(ROOT_DIR . $file);
            }
            $thumb = str_replace('%replace%', 'thumbs', $path);
            if (file_exists(ROOT_DIR . $thumb)) {
                unlink(ROOT_DIR . $thumb);
            }
            $original = str_replace('%replace%', 'original', $path);
            if (file_exists(ROOT_DIR . $original)) {
                unlink(ROOT_DIR . $original);
            }
        }
    }

    /**
     *
     * @param string $path
     * @return void
     */
    private function _delete($path) {
        $file = str_replace('%replace%/', '', $path);
        if (file_exists(ROOT_DIR . $file)) {
            unlink(ROOT_DIR . $file);
        }
        $thumb = str_replace('%replace%', 'thumbs', $path);
        if (file_exists(ROOT_DIR . $thumb)) {
            unlink(ROOT_DIR . $thumb);
        }
    }

    /**
     *
     * @param int $id
     * @return void
     */
    private function _deleteChildren($id) {
        $this->_db->query('SELECT SQL_NO_CACHE * FROM ' . PREFIX . "_dg_gallery_file WHERE parent_id='{$id}' ");
        while ($row = $this->_db->get_row()) {
            $this->_deleteImage($row['path']);
        }
        $this->_db->query('DELETE FROM ' . PREFIX . "_dg_gallery_file WHERE parent_id='{$id}' ");
    }

    /**
     * Обновление кэша альбома
     * @param int $id
     * @return array
     */
    private function _updateAlbom($id) {
        $alb = model_gallery::getClass('model_albom');
        return $alb->updateAlbom($id);
    }

    /**
     * Обновление данных о файле в б.д.
     * @param int $id
     * @param aray $data
     * @return void
     */
    public function updateFile($id, $data) {
        $up = array();
        foreach ($data as $key => $value) {
            $up[] = $key . "='" . $value . "'";
        }
        $this->_db->query('UPDATE ' . PREFIX . "_dg_gallery_file SET " . implode(',', $up) . " WHERE id='{$id}' LIMIT 1");
    }

    /**
     * Удаление файла.
     * @return array
     */
    public function deleteFile() {
        $this->_config = model_gallery::getRegistry('config');
        $id = model_request::getPost('id');
        $info = $this->getFile($id);
        $this->_deleteFile($id, $info);
        if ($this->_config['countFile']) {// update user file counter
            if ($info['author_id']) {
                $_userInfo = $this->_db->super_query('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_user WHERE user_id='{$info['author_id']}' LIMIT 1");
                if ($_userInfo['files'])
                    $this->_db->query('UPDATE ' . DBNAME . '.' . PREFIX . "_dg_gallery_user SET files=files-1 WHERE id='{$info['author_id']}' LIMIT 1");
            }
        }
        $this->_db->query('UPDATE ' . PREFIX . "_dg_gallery_albom SET images=images-1 WHERE id='{$info['parent_id']}' LIMIT 1");
        if (file_exists(ROOT_DIR . '/DGGallery/cache/json/' . $info['parent_id'] . '_alb.json'))
            @unlink(ROOT_DIR . '/DGGallery/cache/json/' . $info['parent_id'] . '_alb.json');
        if (file_exists(ROOT_DIR . '/DGGallery/cache/system/' . $info['parent_id'] . '_alb.php'))
            @unlink(ROOT_DIR . '/DGGallery/cache/json/' . $info['parent_id'] . '_alb.php');
        return $this->_updateAlbom($info['parent_id']);
    }

    /**
     *
     * @return array
     */
    public function sortFile() {
        $id = (int) model_request::getPost('id');
        $info = $this->getFile($id);
        if ((false === $this->_isAdmin()) && (false === $this->_isAuthor())) {
            die('Access Denied.');
        }
        $pos = (int) model_request::getPost('pos');
        if ($pos) {
            $old = $this->_db->super_query('SELECT * FROM ' . PREFIX . "_dg_gallery_file WHERE parent_id='{$info['parent_id']}' AND position='{$pos}' LIMIT 1");
            $this->updateFile($old['id'], array(
                'position' => $info['position']
            ));
            $this->updateFile($info['id'], array(
                'position' => $pos
            ));

            return $this->_updateAlbom($info['parent_id']);
        }
    }

    /**
     *
     * @return array
     */
    public function label() {
        $id = (int) model_request::getPost('id');
        $info = $this->getFile($id);
        if ((false === $this->_isAdmin()) && (false === $this->_isAuthor())) {
            die('Access Denied.');
        }
        if ($info) {
            $dat = unserialize($info['other_dat']);
            $dat['label_status'] = (int) model_request::getPost('set');

            $this->updateFile($info['id'], array(
                'other_dat' => serialize($dat)
            ));
        }
        return $this->_updateAlbom($info['parent_id']);
    }

    /**
     *
     * @return array
     */
    public function setTitle() {
        $id = (int) model_request::getPost('id');
        $info = $this->getFile($id);
        if ($info) {
            if ((false === $this->_isAdmin()) && (false === $this->_isAuthor())) {
                die('Access Denied.');
            }
            $title = module_json::convertToCp(strip_tags(model_request::getPost('title')));
            $title = strlen($title > 245) ? substr($title, 0, 245) : $title;
            $this->updateFile($info['id'], array(
                'title' => $title
            ));
        }
        return $this->_updateAlbom($info['parent_id']);
    }

    /**
     *
     * @param array $url
     * @return array
     */
    public function tubeParse($url) {

        $sxml = null;
        $v = false;
        $youtube_feed = false;
        $gd = null;
        $result = array();
        $url['host'] = str_replace("www.", "", strtolower($url['host']));
        if ($url['host'] != "youtube") {
            return false;
        }
        parse_str($url['query']);
        $sxml = FALSE;
        if ($v) {
            $youtube_feed = file_get_contents('http://gdata.youtube.com/feeds/api/videos/' . $v);
        }
        if (!$youtube_feed) {
            return false;
        }
        $sxml = simplexml_load_string($youtube_feed, NULL, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NOCDATA);
        $media = $sxml->children('http://search.yahoo.com/mrss/');
        $result['title'] = (string) $media->group->title;
        $result['description'] = (string) $media->group->description;
        $attrs = $media->group->player->attributes(); // get video player URL
        $result['watchUrl'] = (string) $attrs['url'];

        $attrs = $media->group->thumbnail[0]->attributes(); // get video thumbnail
        $result['thumbnailURL'] = (string) $attrs['url'];
        $yt = $media->children('http://gdata.youtube.com/schemas/2007'); // get <yt:duration> node for video length
        $attrs = $yt->duration->attributes();
        $result['length'] = (string) $attrs['seconds'];
        $yt = $sxml->children('http://gdata.youtube.com/schemas/2007'); // get <yt:stats> node for viewer statistics
        $attrs = $yt->statistics->attributes();
        $result['viewCount'] = (string) $attrs['viewCount'];

        $gd = $sxml->children('http://schemas.google.com/g/2005'); // get <gd:rating> node for video ratings
        if ($gd->rating) {
            $attrs = $gd->rating->attributes();
            $result['rating'] = (string) $attrs['average'];
        } else {
            $result['rating'] = 0;
        }
        return $result;
    }

    /**
     *
     * @return array
     */
    public function setDescr() {
        require_once ROOT_DIR . '/engine/classes/parse.class.php';
        $parse = new ParseFilter(array(), array(), 1, 1);
        $id = (int) model_request::getPost('id');
        $info = $this->getFile($id);
        if ($info) {
            if ((false === $this->_isAdmin()) && (false === $this->_isAuthor())) {
                die('Access Denied.');
            }
            $descr = module_json::convertToCp(model_request::getPost('descr'));
            $descr = $parse->process($descr);
            $this->updateFile($info['id'], array(
                'descr' => $descr
            ));
        }
        return $this->_updateAlbom($info['parent_id']);
    }

    /**
     *
     * @return array
     */
    public function setDefaultPlayer() {
        $id = (int) model_request::getPost('id');
        $info = $this->getFile($id);
        if ($info) {
            if ((false === $this->_isAdmin()) && (false === $this->_isAuthor())) {
                die('Access Denied.');
            }
            $dat = unserialize($info['other_dat']);
            $dat['default_player'] = (int) model_request::getPost('default_player');
            $this->updateFile($info['id'], array(
                'other_dat' => serialize($dat)
            ));
        }
        return $this->_updateAlbom($info['parent_id']);
    }

    /**
     *
     * @param string $p
     * @param int $i
     * @return array
     * FIX: add
     * @param int $_file_id
     */
    public function addVideoCover($p, $i, $_file_id) {
        // $id = (int) model_request::getPost('file_id');
        if ($_file_id) {
            $info = $this->_db->super_query('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE id='{$_file_id}' LIMIT 1");

            $other = (is_string($info['other_dat'])) ? unserialize($info['other_dat']) : $info['other_dat'];
            $other['other_dat']['video_preview'][$i] = $p;

            $this->updateFile($info['id'], array(
                'other_dat' => serialize($other)
            ));
            return $this->_updateAlbom($info['parent_id']);
        }
    }

    /**
     *
     * @return mixed
     */
    public function deleteCover() {
        $id = (int) model_request::getPost('id');
        $info = $this->getFile($id);
        if ($info) {
            $parent = $this->getFile($info['parent_id']);
            $other = unserialize($parent['other_dat']);
            if ($other) {
                if (isset($other['other_dat']['video_preview'][$id]) /* && is_array($other['other_dat']['video_preview']) */) {
                    unset($other['other_dat']['video_preview'][$id]);
                    if ($other['other_dat']['preview_id'] == $id) {
                        $this->updateFile($parent['id'], array(
                            'other_dat' => serialize($other),
                            'path' => '/uploads/gallery/assets/videopreview.png'
                        ));
                    } else {
                        $this->updateFile($parent['id'], array(
                            'other_dat' => serialize($other)
                        ));
                    }
                    $this->_deleteImage($info['path']);
                    $this->_db->query('DELETE FROM ' . PREFIX . "_dg_gallery_file WHERE  id='{$id}' ");
                    return $this->_updateAlbom($parent['parent_id']);
                }
            }
        }
    }

    /**
     * обложка ?
     * @return array
     * FIX: 4.09.11
     * метод getFile() возвращает уже распакованные данные,
     * была повтораная распаковка строки unserialize($parent['other_dat']), в результате данные были потеряны.
     */
    public function setCover() {
        $id = (int) model_request::getPost('id');
        $info = $this->getFile($id);
        $parent = $this->getFile($info['parent_id']);

        $other = (is_string($parent['other_dat'])) ? unserialize($parent['other_dat']) : $parent['other_dat'];
        $other['other_dat']['preview_id'] = $id;
        $this->updateFile($parent['id'], array(
            'other_dat' => serialize($other),
            'path' => $info['path']
        ));
        return $this->_updateAlbom($parent['parent_id']);
    }

    public static function getThumb($path, $status='') {
        $path = str_replace('%replace%/', 'thumbs/', $path);
        if (file_exists(ROOT_DIR . $path)) {
            return $path;
        } else {
            $_service = array('rutube', 'vimeo', 'smotri.com', 'gametrailers');
            if (in_array($status, $_service)) {
                return '/uploads/gallery/assets/' . $status . '_preview.png';
            } else {
                return '/uploads/gallery/assets/no-image.png';
            }
        }
    }

    public static function getFilePath($path) {
        $path = str_replace('%replace%/', '', $path);
        if (file_exists(ROOT_DIR . $path)) {
            return $path;
        }
        return null;
    }

    /**
     * Просмотр отдельного файла? перенести в view ..........................
     * @param array $row
     * @param dle_template $tpl
     */
    public function setInfo(array $row, dle_template $tpl) {
        $lang = null;
        $albInfo = null;
        $this->currentFile = $row;
        $this->_info = $row;

        $albInfo = model_gallery::getRegistry('model_albom')->getInfo();
        if (GALLERY_MODE === 2) {
            $albInfo = controller_gallery::$CATEGORY;
        }
        $tpl->set('{file-path}', self::getFilePath($row['path']));
        $tpl->set('{preview-path}', self::getThumb($row['path']));
        if (is_string($row['other_dat'])) {
            $row['other_dat'] = unserialize($row['other_dat']);
        }
        $_video = array('video', 'youtube', 'vimeo', 'smotri.com', 'rutube', 'gametrailers');
        if (in_array($row['status'], $_video)) {
            $pl = 2;
            switch ($row['status']) {
                case 'youtube':
                    if (isset($row ['other_dat']['default_player'])) {
                        $pl = $row ['other_dat']['default_player'];
                    } else {
                        if ('youtube' == $row['status'])
                            $pl = 0;
                    }
                    $tpl->copy_template = preg_replace("#\\{media=(.+?),(.+?)\\}#ies", "\$this->setPlayer('\\1', '\\2', '$pl' )", $tpl->copy_template);
                    break;
                case 'video':
                case 'vimeo':
                case 'smotri.com':
                case 'rutube':
                case 'gametrailers':
                    $tpl->copy_template = preg_replace("#\\{media=(.+?),(.+?)\\}#ies", "\$this->setPlayer('\\1', '\\2', '$pl' )", $tpl->copy_template);
                    break;
                default:

                    break;
            }

            $tpl->set_block("#\[field-label\](.*?)\[\/field-label\]#si", '');
        } else {
            if (isset($row ['other_dat']['label_status']) && (1 === (int) $row ['other_dat']['label_status']) &&
                    is_array($row ['other_dat']['label'])) {// set label
                $tpl->set('[field-label]', '');
                $tpl->set('[/field-label]', '');
                $label = '';
                foreach ($row ['other_dat']['label'] as $key => $info) {
                    $label .='<li><a href="javascript:void(0)" class="dggalleryimagelabel" id="' . $key . '">' . $info['text'] . '</a></li>';
                }
                $tpl->set('{label}', $label);
                $tpl->copy_template = $tpl->copy_template . '
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
 var label = ' . module_json::getJson($row ['other_dat']['label']) . ';
 $(\'#galleryImage\').dggallerylabel({
        label: label
   });
 });
//]]>
</script>';
            } else {
                $tpl->set_block("#\[field-label\](.*?)\[\/field-label\]#si", '');
            }
        }
        $lang = model_gallery::getRegistry('lang');
        $row['title'] = ( $row['title'] != '') ? stripslashes($row['title']) : $lang['title']['title_1'];
        $config = model_gallery::getRegistry('config_cms');


        $tpl->set('{file-title}', $row['title']);
        if (isset($row['other_dat']['tag']) && !empty($row['other_dat']['tag'])) {
            $tpl->set('{file-keyword}', stripslashes($row['other_dat']['tag']));
        } else {
//$albInfo['meta_data']['meta_title']
//$albInfo['meta_data']['meta_keywords']
            $tpl->set('{file-keyword}', '' . $albInfo['title']);
        }

        $tpl->set('{title}', $row['title']);
        if ((empty($row ['other_dat']['info']['originalWidth'])) && empty($row ['other_dat']['info']['originalHeight'])) {
            $tpl->set_block("#\[field-file_size\](.*?)\[\/field-file_size\]#si", '');
        } else {
            $tpl->set('[field-file_size]', '');
            $tpl->set('[/field-file_size]', '');
            $tpl->set('{width}', ($row ['other_dat']['info']['originalWidth']) ? $row ['other_dat']['info']['originalWidth'] : '----');
            $tpl->set('{height}', ($row ['other_dat']['info']['originalHeight']) ? $row ['other_dat']['info']['originalHeight'] : '----');
        }
        $tpl->set('{size}', ($row['other_dat']['info']['size']) ? model_gallery::formatsize($row['other_dat']['info']['size']) : '----');
        $tpl->set('{date}', model_gallery::dateFormat($row['date']));
        $colors .= '';
        if (isset($row['other_dat']['info']['colors'][0]) && is_array($row['other_dat']['info']['colors'][0])) {
            foreach ($row['other_dat']['info']['colors'][0] as $key => $value) {
                $colors .= '<a class="color-block" style="background-color:#' . $key . '" title="' . $value . '%" href="' . $config['http_home_url'] . 'gallery/search/color/' . $key . '">' . $key . '</a>';
            }
            $tpl->set('[field-colors]', '');
            $tpl->set('[/field-colors]', '');
        } else {
            $tpl->set_block("#\[field-colors\](.*?)\[\/field-colors\]#si", '');
        }
        $tpl->set('{colors}', $colors);
        $tag_list = '';
        if (isset($row['other_dat']['tag'])) {
            $tag = explode(',', $row['other_dat']['tag']);
            foreach ($tag as $value) {
                $value = trim(stripslashes($value));
                $tag_list[] = '<a href="' . $config['http_home_url'] . 'gallery/search/keyword/' . urlencode($value) . '">' . $value . '</a>';
            }
            $tpl->set('{tag}', implode(', ', $tag_list));
            $tpl->set('[field-tags]', '');
            $tpl->set('[/field-tags]', '');
        } else {
            $tpl->set('{tag}', '');
            $tpl->set_block("#\[field-tags\](.*?)\[\/field-tags\]#si", '');
        }


        # $hrefPage = $config['http_home_url'] . $_SERVER['REQUEST_URI'];
        if (GALLERY_MODE === 1)
            $hrefPage = $config['http_home_url'] . 'gallery/albom/' . $row['parent_id'] . '-' . $albInfo['meta_data']['meta_title'] . '.' . $row['id'];
        elseif (GALLERY_MODE === 2)
            $hrefPage = $config['http_home_url'] . 'gallery/full/' . $row['parent_id'] . '-' . $albInfo['meta_data']['meta_title'] . '.' . $row['id'];

        $src = $config['http_home_url'] . self::getThumb($row['path']);
        $tpl->set('{blog_html}', htmlentities('<a href="' . $hrefPage . '"><img src="' . $src . '" alt="' . $row['title'] . '" /></a>', ENT_QUOTES, 'cp1251'));
        $tpl->set('{bb_code}', '[url=' . $hrefPage . '][img]' . $src . '[/img][/url]');
        $tpl->set('{url}', $hrefPage);
        if (!empty($row['descr']) && $row['descr'] != '') {
            $tpl->set('{description}', stripslashes($row['descr']));
            $tpl->set('[field-description]', '');
            $tpl->set('[/field-description]', '');
        } else {
            $tpl->set_block("#\\[field-description\\](.*?)\\[/field-description\\]#si", '');
            $tpl->set('{description}', '');
        }

        if ($this->_isAuthor() or $this->_isAdmin()) {
            $tpl->set('[edit-description]', '<a href="javascript:editFile(\'' . $row['id'] . '\')">');
            $tpl->set('[/edit-description]', '</a>');
            $tpl->set('[access_edit]', '');
            $tpl->set('[/access_edit]', '');
        } else {
            $tpl->set_block("#\\[edit-description\\](.*?)\\[/edit-description\\]#si", '');
            $tpl->set_block("#\\[access_edit\\](.*?)\\[/access_edit\\]#si", '');
        }
    }

    /**
     *
     * @param int $width
     * @param int $height
     * @param int $id
     * @return string
     */
    public function setPlayer($width, $height, $id) {
        $row = $this->currentFile;
        if (is_string($row['other_dat'])) {
            $row['other_dat'] = unserialize($row['other_dat']);
        }
        $_service = array('smotri.com', 'rutube', 'vimeo', 'gametrailers');
        if (isset($this->_config['widthPlayer']) && $this->_config['widthPlayer'] != '') {
            $width = $this->_config['widthPlayer'];
        }
        if (isset($this->_config['heightPlayer']) && $this->_config['heightPlayer'] != '') {
            $height = $this->_config['heightPlayer'];
        }
        if (in_array($row['status'], $_service)) {
            if ($row['status'] == 'vimeo') {
                $row['other_dat']['path'] = $row['other_dat']["other_dat"]['path'];
            }
            return $this->getVideoPlayerOtherServicess($row['status'], array(
                        'width' => $width,
                        'height' => $height,
                        'video_link' => $row['other_dat']['path']
                    ));
        }

        $src = ('youtube' == $row['status']) ? 'http://www.youtube.com/v/' . $row ["other_dat"]['other_dat']['name'] . '?version=3' :
                $row['other_dat']['other_dat']['file_path'];

        $video_config = null;
        $config = model_gallery::getRegistry('config_cms');
        include (ENGINE_DIR . '/data/videoconfig.php');
        $this->video_config = $video_config;
        $flashvars = array();


        if (!$this->_config['video_setting_dle']) {
            $this->video_config['flv_watermark'] = $this->_config['watermarkVideo'];
            $this->video_config['flv_watermark_pos'] = $this->_config['flv_watermark_pos'];
            $this->video_config['flv_watermark_al'] = $this->_config['flv_watermark_al'];
            $this->video_config['startframe'] = $this->_config['first_frame_preview'];
            $this->video_config['progressBarColor'] = $this->_config['progressBarColor'];
            $this->video_config['play'] = $this->_config['autoPlay'];
            $this->video_config['buffer'] = $this->_config['buffer'];
            $this->video_config['autohide'] = $this->_config['autohide'];
            $this->video_config['tube_related'] = $this->_config['tube_related'];
            $this->video_config['youtube_q'] = $this->_config['youtube_quality'];
        }


        switch ($id) {
            case 0:
            case '0':
                return <<<HTML
<object style="height: {$height}px; width: {$width}px">
  <param name="movie" value="{$src}">
  <param name="allowFullScreen" value="true">
  <param name="allowScriptAccess" value="always">
  <embed src="{$src}" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="{$width}" height="{$height}">
</object>
HTML;
                break;
            case 2:
            case '2':
                if ($this->video_config['flv_watermark'])
                    $watermark = "&showWatermark=true&watermarkPosition={$this->video_config['flv_watermark_pos']}&watermarkMargin=0&watermarkAlpha={$this->video_config['flv_watermark_al']}&watermarkImageUrl={THEME}/dleimages/flv_watermark.png";
                else
                    $watermark = "&showWatermark=false";
                $preview = "&showPreviewImage=true&previewImageUrl=";
                if ($this->video_config['preview'])
                    $preview = "&showPreviewImage=true&previewImageUrl={THEME}/dleimages/videopreview.jpg";
                $preview = "&showPreviewImage=true&previewImageUrl=" . self::getThumb($row['path']);
                if ($this->video_config['startframe'])
                    $preview = "&showPreviewImage=false";
                if ($this->video_config['play']) {
                    $preview = "&showPreviewImage=false&autoPlays=true";
                }
                $this->video_config['buffer'] = intval($this->video_config['buffer']);
                if ($this->video_config['autohide'])
                    $autohide = "&autoHideNav=true&autoHideNavTime=3";
                else
                    $autohide = "&autoHideNav=false";
                $id_player = md5(microtime());
                $video_url = "&videoUrl=" . $src;


                if ('youtube' == $row['status']) {
                    $src .= '?rel=' . intval($this->video_config['tube_related']);
                    return "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0\" width=\"{$width}\" height=\"{$height}\" id=\"Player-{$id_player}\">
					<param name=\"movie\" value=\"" . $config['http_home_url'] . "engine/classes/flashplayer/media_player_v3.swf?stageW={$width}&stageH={$height}&contentType=video&videoUrl=http://www.youtube.com/watch?v={$video_url}{$watermark}{$preview}&youTubePlaybackQuality={$this->video_config['youtube_q']}&isYouTube=true&rollOverAlpha=0.5&contentBgAlpha=0.8&progressBarColor={$this->video_config['progressBarColor']}&defaultVolume=1&fullSizeView=3&showRewind=false&showInfo=false&showFullscreen=true&showScale=true&showSound=true&showTime=true&showCenterPlay=true{$autohide}&videoLoop=false&defaultBuffer={$this->video_config['buffer']}\" />
					<param name=\"allowFullScreen\" value=\"true\" />
					<param name=\"scale\" value=\"noscale\" />
					<param name=\"quality\" value=\"high\" />
					<param name=\"bgcolor\" value=\"#000000\" />
					<param name=\"wmode\" value=\"opaque\" />
					<embed src=\"" . $config['http_home_url'] . "engine/classes/flashplayer/media_player_v3.swf?stageW={$width}&stageH={$height}&contentType=video&videoUrl=http://www.youtube.com/watch?v={$video_url}{$watermark}{$preview}&youTubePlaybackQuality={$this->video_config['youtube_q']}&isYouTube=true&rollOverAlpha=0.5&contentBgAlpha=0.8&progressBarColor={$this->video_config['progressBarColor']}&defaultVolume=1&fullSizeView=3&showRewind=false&showInfo=false&showFullscreen=true&showScale=true&showSound=true&showTime=true&showCenterPlay=true{$autohide}&videoLoop=false&defaultBuffer={$this->video_config['buffer']}\" quality=\"high\" bgcolor=\"#000000\" wmode=\"opaque\" allowFullScreen=\"true\" width=\"{$width}\" height=\"{$height}\" align=\"middle\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"></embed>
					</object>";
                } else {
                    return "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0\" width=\"{$width}\" height=\"{$height}\" id=\"Player-{$id_player}\">
				<param name=\"movie\" value=\"" . $config['http_home_url'] . "engine/classes/flashplayer/media_player_v3.swf?stageW={$width}&stageH={$height}&contentType=video{$video_url}{$watermark}{$preview}&isYouTube=false&rollOverAlpha=0.5&contentBgAlpha=0.8&progressBarColor={$this->video_config['progressBarColor']}&defaultVolume=1&fullSizeView=3&showRewind=false&showInfo=false&showFullscreen=true&showScale=true&showSound=true&showTime=true&showCenterPlay=true{$autohide}&videoLoop=false&defaultBuffer={$this->video_config['buffer']}\" />
				<param name=\"allowFullScreen\" value=\"true\" />
				<param name=\"scale\" value=\"noscale\" />
				<param name=\"quality\" value=\"high\" />
				<param name=\"bgcolor\" value=\"#000000\" />
				<param name=\"wmode\" value=\"opaque\" />
				<embed src=\"" . $config['http_home_url'] . "engine/classes/flashplayer/media_player_v3.swf?stageW={$width}&stageH={$height}&contentType=video{$video_url}{$watermark}{$preview}&isYouTube=false&rollOverAlpha=0.5&contentBgAlpha=0.8&progressBarColor={$this->video_config['progressBarColor']}&defaultVolume=1&fullSizeView=3&showRewind=false&showInfo=false&showFullscreen=true&showScale=true&showSound=true&showTime=true&showCenterPlay=true{$autohide}&videoLoop=false&defaultBuffer={$this->video_config['buffer']}\" quality=\"high\" bgcolor=\"#000000\" wmode=\"opaque\" allowFullScreen=\"true\" width=\"{$width}\" height=\"{$height}\" align=\"middle\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"></embed>
		    </object>";
                }

                break;
            case 1:
            case '1':
                $id_player = md5(microtime());
                $flashvars['auto_start'] = '&playOnStart=' . ($this->_config['autoPlay']) ? 'true' : 'false';
                $flashvars['watermark'] = ($this->video_config['flv_watermark']) ? "&logo={$config['http_home_url']}templates/{$config['skin']}/dleimages/flv_watermark.png" : '';

                $flashvars['play_btn'] = '&showPlayButton=true';

                $flashvars['image_scale'] = '&imageScaleType=0';
                $flashvars['image'] = '&image=' . self::getThumb($row['path']);
                $flvar = implode('', $flashvars);
                return <<< HTML
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="{$width}" height="{$height}" id="Player-{$id_player}">
<param name="movie" value="{$config['http_home_url']}engine/classes/flashplayer/media_player.swf?MediaLink={$src}{$flvar} />
<param name="allowFullScreen" value="true" />
<param name="quality" value="high" />
<param name="bgcolor" value="#000000" />
<param name="wmode" value="opaque" />
<embed src="{$config['http_home_url']}engine/classes/flashplayer/media_player.swf?MediaLink={$src}{$flvar}" quality="high" bgcolor="#000000" wmode="opaque" allowFullScreen="true" width="{$width}" height="{$height}" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>
</object>
HTML;
                break;
            default:
                break;
        }
    }

    /**
     *
     * @param string $name
     * @param array $options
     * @return type
     */
    public function getVideoPlayerOtherServicess($name, array $options) {

        switch ($name) {
            case 'vimeo':
                #http://vimeo.com/groups/cinema4d/videos/28548031
                ####api http://vimeo.com/api/v2/video/28548031.output[php||json||xml]
                return '<iframe width="' . $options['width'] . '" height="' . $options['height'] . '" src="' . $options['video_link'] . '" frameborder="0" allowfullscreen></iframe>';
            case 'smotri.com':
                #http://smotri.com/video/view/?id=v1828839be0a#

                return '<object id="smotriCom" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="' . $options['width'] . '" height="' . $options['height'] . '">
<param name="movie" value="' . $options['video_link'] . '&bufferTime=3&autoStart=false&str_lang=rus&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" />
<param name="allowScriptAccess" value="always" />
<param name="allowFullScreen" value="true" />
<param name="wmode" value="opaque" />
<embed src="' . $options['video_link'] . '&bufferTime=3&autoStart=false&str_lang=rus&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="opaque"  width="' . $options['width'] . '" height="' . $options['height'] . '" type="application/x-shockwave-flash"></embed>
</object>';
            case 'rutube':
                #
                return '<object width="' . $options['width'] . '" height="' . $options['height'] . '">
<param name="movie" value="' . $options['video_link'] . '" />
<param name="wmode" value="transparent" />
<param name="allowFullScreen" value="true" />
<embed src="' . $options['video_link'] . '" type="application/x-shockwave-flash" wmode="transparent" width="' . $options['width'] . '" height="' . $options['height'] . '" allowFullScreen="true" ></embed>
</object>';
            case 'gametrailers':
                //хз зачем ctlsoft этот сервис добавил
                //http://www.gametrailers.com/video/angry-video-screwattack/719815
                return '<object type="application/x-shockwave-flash" id="mtvn_player" name="mtvn_player" data="' . $options['video_link'] . '" width="' . $options['width'] . '" height="' . $options['height'] . '">
  <param name="allowscriptaccess" value="always" />
  <param name="allowFullScreen" value="true" />
  <param name="wmode" value="opaque" />
  <param name="flashvars" value="autoPlay=false" />
  <embed src="' . $options['video_link'] . '" width="' . $options['width'] . '" height="' . $options['height'] . '" type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always" base="." flashVars=""> </embed>
</object>';
                break;
        }
    }

    /**
     *
     * @param type $pageLimit
     * @return type
     */
    public function getPage($pageLimit = 9, $where = array()) {

        $offset = (int) model_request::getRequest('page');
        $offset = ($offset) ? (($offset - 1) * $pageLimit) : 0;
        $result = null;
        if (model_search::$DEF_ORDER === 'ORDER_DATE')
            $order = DBNAME . '.' . PREFIX . "_dg_gallery_file.date DESC ";
        if (model_search::$DEF_ORDER === 'ORDER_COMMENTS')
            $order = DBNAME . '.' . PREFIX . "_dg_gallery_file.comm_num DESC ";
        if (model_search::$DEF_ORDER === 'ORDER_RATING')
            $order = DBNAME . '.' . PREFIX . "_dg_gallery_file.rating DESC ";
        if (model_search::$DEF_ORDER === 'ORDER_DOWNLOAD')
            $order = DBNAME . '.' . PREFIX . "_dg_gallery_file.download DESC ";
        if (model_search::$DEF_ORDER === 'ORDER_VIEW')
            $order = DBNAME . '.' . PREFIX . "_dg_gallery_file.view DESC ";


        $where['status'] = (GALLERY_MODE === 1) ? DBNAME . '.' . PREFIX . "_dg_gallery_file.status='albom' " :
                DBNAME . '.' . PREFIX . "_dg_gallery_file.status='catfile' OR " . DBNAME . '.' . PREFIX . "_dg_gallery_file.status='video' OR "
                . DBNAME . '.' . PREFIX . "_dg_gallery_file.status='youtube' ";

        $count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . '_dg_gallery_file WHERE ' .
                implode(' AND ', $where));

        if (!$count['count'])
            return;

        if (empty($where['parent_id']))
            $sql = 'SELECT ' . DBNAME . '.' . PREFIX . '_dg_gallery_file.* , '
                    . DBNAME . '.' . PREFIX . '_dg_gallery_albom.meta_data, '
                    . DBNAME . '.' . PREFIX . '_dg_gallery_albom.images, '
                    . DBNAME . '.' . PREFIX . '_dg_gallery_albom.access_data, '
                    . DBNAME . '.' . PREFIX . '_dg_gallery_albom.rating FROM '
                    . DBNAME . '.' . PREFIX . '_dg_gallery_file LEFT JOIN '
                    . DBNAME . '.' . PREFIX . '_dg_gallery_albom ON '
                    . DBNAME . '.' . PREFIX . '_dg_gallery_albom.id = ' . DBNAME . '.' . PREFIX . '_dg_gallery_file.parent_id WHERE '
                    . implode(' AND ', $where) . ' ORDER BY ' . $order . " LIMIT {$offset},{$pageLimit}";
        else
            $sql = 'SELECT * FROM ' . DBNAME . '.' . PREFIX . '_dg_gallery_file WHERE '
                    . implode(' AND ', $where) . ' ORDER BY ' . $order . " LIMIT {$offset},{$pageLimit}";
        $this->_db->query($sql);
        while ($row = $this->_db->get_row()) {
            $result['file'][$row['id']] = $row;
        }
        $result['count'] = $count['count'];
        return $result;
    }

    /**
     *
     * @param int $id
     */
    public function updateViewFile($id) {
        if (null === $this->_config) {
            $this->_config = model_gallery::getRegistry('config');
        }
        if ($this->_config['logViewFile']) {
            $this->_db->query('UPDATE ' . DBNAME . '.' . PREFIX . '_dg_gallery_file SET  view=view+1 WHERE id=\'' . $id . '\' LIMIT 1');
        }
    }

    /**
     *
     * @return bool
     */
    protected function _isAuthor() {
        if (null === $this->_info) {
            die('Access Denied, empty info file');
        }
        return ($this->_info['author'] == self::$user['name']);
    }

    /**
     *
     * @return arrau
     */
    public function loadUserFile($user = true) {
        $offset = (int) model_request::getRequest('page');
        $offset = ($offset) ? (($offset - 1) * 20) : 0;

        $name = model_request::getPost('name');
        $date = model_request::getPost('date');
        if (null != $date) {
            $date = date('Y-m-d H:i:s', strtotime($date));
        }
        $category = (int) model_request::getPost('category');
        $where = array();
        $where['status'] = "status!='videocover' ";
        $where['status2'] = "status!='folder_cover' ";
        if ($user)
            $where['author'] = "author='" . model_gallery::$user['name'] . "' ";
        $history = $_POST['history'];
        if (is_array($history)) {
            foreach ($history as $key => $value) {
                if ($key == 'category') {
                    $parent_id = (int) $value;
                    if ($parent_id) {
                        $where['category'] = "parent_id='{$parent_id}'";
                    }
                }
                if ($key == 'date1') {
                    $_date = strtotime($value);
                    if ($_date) {
                        $_date = date('Y-m-d H:i:s', $_date);
                        $where['date1'] = "date >='{$_date}'";
                    }
                }
                if ($key == 'date2') {
                    $_date = strtotime($value);
                    if ($_date) {
                        $_date = date('Y-m-d H:i:s', $_date);
                        $where['date2'] = "date <='{$_date}'";
                    }
                }
            }
        }
        switch ($name) {
            case 'date-1':
                if ($date)
                    $where['date1'] = "date >='{$date}'";
                break;
            case 'date-2':
                if ($date)
                    $where['date2'] = "date <='{$date}'";
                break;
            case 'category':
                if ($category)
                    $where['category'] = "parent_id='{$category}'";
        }
        $_where = '';
        if (count($where))
            $_where = 'WHERE ' . implode(' AND ', $where);
        $mysqlId = $this->_db->query('SELECT * FROM ' . DBNAME . '.' . PREFIX . '_dg_gallery_file ' . $_where . " ORDER BY date DESC LIMIT {$offset},20");
        $count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . '_dg_gallery_file ' . $_where);
        return array(
            'mysqlId' => $mysqlId,
            'count' => $count['count']
        );
    }

    public function getMinMaxId($patent_id) {
        if (GALLERY_MODE === 1) {
            $this->_db->query('SELECT (id) FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE parent_id='{$patent_id}' ORDER BY position ASC ");
            $id = array();
            while ($row = $this->_db->get_row()) {
                $id[] = $row['id'];
            }
            return array(
                'min' => $id[0],
                'max' => end($id)
            );
        }
        if (GALLERY_MODE === 2)
            return $this->_db->super_query('SELECT MIN(id) AS min, MAX(id) AS max FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE parent_id='{$patent_id}' AND status!='folder_cover '");
    }

    /**
     * Обновление параметров файла с сайта.
     * @return array
     */
    public function setParam() {

        require_once ROOT_DIR . '/engine/classes/parse.class.php';
        $parse = new ParseFilter(array(), array(), 1, 1);
        $id = (int) model_request::getPost('id');
        $info = $this->getFile($id);
        $comm_access = null;
        $rating_access = null;
        $keyword = '';
        if ($info) {
            if ((false === $this->_isAdmin()) && (false === $this->_isAuthor())) {
                die('Access Denied.');
            }
            parse_str($_POST['data']);
            model_gallery::getClass('model_search')->addKeywordsFile($keyword); // add keyword
            $descr = module_json::convertToCp($descr);
            $descr = $parse->process($descr);
            $title = module_json::convertToCp(strip_tags($title));
            $title = strlen($title > 245) ? substr($title, 0, 245) : $title;
            $this->updateFile($info['id'], array(
                'title' => $title,
                'descr' => $descr,
                'comm_access' => ($comm_access == 'on') ? 1 : 0,
                'rating_access' => ($rating_access == 'on') ? 1 : 0
            ));
            return $this->_updateAlbom($info['parent_id']);
        } else {
            die();
        }
    }

    /**
     *
     * @return mixed
     */
    public function ratingFile() {
        $_id = (int) model_request::getPost('id');
        $_IP = ip2long($_SERVER['REMOTE_ADDR']);
        $where = '';
        $insert = array(
            'status' => "'file'",
            'ip' => "'{$_IP}'",
            'parent_id' => $_id
        );
        $this->_config = model_gallery::getRegistry('config');
        if ($_id) {
            $checkFile = $this->_db->super_query('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE id='{$_id}' LIMIT 1");

            if (null === $checkFile)
                return;
            $set = model_request::getPost('set');
            if (in_array($set, array(1, 2))) {// 1 = -1, 2 = +1
                $user = model_gallery::$user;
                if ($set == 1) {
                    $set = '-1';
                } else {
                    $set = '+1';
                }
                $insert['status'] = "'{$set}'";
                $where = " WHERE parent_id='{$_id}' AND ip='" . $_IP . "'";
                if ($user['user_id']) {
                    $insert['user_id'] = "'{$user['user_id']}'";
                    $where = " WHERE  parent_id='{$_id}' AND user_id='" . $user['user_id'] . "'";
                }
                $check = $this->_db->super_query('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_log {$where}");

                if ($this->_config['ratingFileType'] == 1) {// + -
                    if (($check['status'] === '-1' && $set === '-1') ||
                            ($check['status'] === '+1' && $set === '+1')) {
                        return;
                    }
                    if (null === $check) {
                        $this->_db->query('INSERT INTO ' . DBNAME . '.' . PREFIX . '_dg_gallery_log (' . implode(',', array_keys($insert)) . ') VALUES (' .
                                implode(',', $insert) . ")");
                    } else {
                        $this->_db->query('UPDATE ' . DBNAME . '.' . PREFIX . "_dg_gallery_log  SET status='{$set}' WHERE id='{$check['id']}' LIMIT 1");
                    }
                    $this->_db->query('UPDATE ' . DBNAME . '.' . PREFIX . "_dg_gallery_file SET rating=rating{$set} WHERE id='{$_id}' LIMIT 1");
                    $this->_updateAlbom($checkFile['parent_id']);
                } else {
                    if (null === $check['id']) {
                        $this->_db->query('INSERT INTO ' . DBNAME . '.' . PREFIX . '_dg_gallery_log (' . implode(',', array_keys($insert)) . ') VALUES (' .
                                implode(',', $insert) . ') ');
                        $this->_db->query('UPDATE ' . DBNAME . '.' . PREFIX . "_dg_gallery_file SET rating=rating{$set} WHERE id='{$_id}' LIMIT 1");
                        $this->_updateAlbom($checkFile['parent_id']);
                    } else {
                        return array('tpl' => $checkFile['rating']);
                    }
                }
            }
            $rating = $this->_db->super_query('SELECT rating FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE id='{$_id}'");
            return ($rating['rating']) ? array('tpl' => $rating['rating']) : '';
        }
    }

    /**
     *
     * @return mixed
     */
    public function getRelatedFiles() {
        $_id = controller_gallery::$CATEGORY['id'];
        $where = null;
        $result = null;
        if (isset($this->_info["other_dat"]['tag']) && $this->_info["other_dat"]['tag'] != '') {
            $where = explode(',', $this->_info["other_dat"]['tag']);
        }
        $_where = null;
        if (is_array($where))
            foreach ($where as $value) {
                if ($value != '')
                    $_where[] = " other_dat LIKE  '%" . trim($value) . "%' ";
            }

        $w = (is_array($_where)) ? ' AND ' . implode(' OR ', $_where) : '';
        $this->_db->query('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE  id != '{$this->_info['id']}' AND status!='folder_cover' " . $w . " AND  parent_id='{$_id}' LIMIT 5");
        while ($row = $this->_db->get_row()) {
            $result[$row['id']] = $row;
        }
        return $result;
    }

    /**
     *
     * @param int $limit
     * @param array $option
     * @return array
     */
    public function getRandFile($limit = 5, $option= null) {
        $result = null;
        $where = '';
        if (isset($option['where'])) {
            $where = ' WHERE ' . $option['where'];
        }
        $this->_db->query('SELECT * FROM ' . DBNAME . '.' . PREFIX . '_dg_gallery_file ' . $where . ' ORDER BY RAND() LIMIT ' . $limit);
        while ($row = $this->_db->get_row()) {
            $result[$row['id']] = $row;
        }
        return $result;
    }

}

