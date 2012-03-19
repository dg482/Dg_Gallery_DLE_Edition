<?php
/**
 * @package gallery
 * @author Dark Ghost
 * @access public
 * @since 1.5.6 (19.03.12)
 */

class model_upload
{

    /**
     * @var string
     * www/uploars/xxx
     * */
    private $_dir;

    /**
     * @var array
     * (image ext)
     * */
    protected $_image;

    /**
     * @var array
     * (video ext)
     * */
    protected $_video;

    /**
     * @var array
     * */
    protected $_arr;

    /**
     * @var string
     * */
    protected $_allExt;

    /**
     * @var bool
     * @deprecated
     * */
    protected $_shortName;

    /**
     * @var string
     * */
    protected $_fileName;

    /**
     * @var string
     *
     * */
    protected $_fileComplete;

    /**
     * @var string
     * */
    protected $_fileResult;

    /**
     * @var arra
     * */
    protected $_config;

    /**
     * @var obj
     * */
    protected $_zip;

    /**
     * @var obj
     * */
    protected $_db;

    /**
     * @var array
     * */
    private $_inserValue;

    /**
     * @var array
     * */
    private $_user;

    /**
     * @var int
     */
    protected $_lastInsertId;

    /**
     *
     * @var type
     */
    private $_count;

    /**
     *
     * @var type
     */
    private $_status;

    /**
     *
     */
    public function __construct()
    {
        if (!defined('ROOT_DIR')) {
            define('ROOT_DIR', $_SERVER ["DOCUMENT_ROOT"]);
        }
        $this->_count = 0;

        $this->_config = (include ROOT_DIR . '/DGGallery/app/config/config_gallery.php');
        $this->_db = new module_db ();
        global $member_id;
        if (!defined('FOLDER_PREFIX')) {
            if (@ini_get('safe_mode') == 1)
                define('FOLDER_PREFIX', "");
            else
                define('FOLDER_PREFIX', date("Y-m") . "/");
        }
        if (!is_array($member_id)) {
            exit();
            return false;
        }
        $this->_user = $member_id;
        $this->_dir = ROOT_DIR . '/uploads/gallery/' . FOLDER_PREFIX;
        $this->_checkDir($this->_dir);
    }

    /**
     *
     * @param int $maxFile
     * @param string $key
     * @param string $fileType
     * @param int $parent_id
     * @return json
     */
    public function setPlugin($maxFile = null, $key = '', $fileType = 'images', $parent_id = 0)
    {
        if ($this->_count) {
            $count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . '_dg_gallery_file ');
            if ($this->_count <= $count['count']) {
                #   return;
            }
        }
        $this->_image = array("gif", "jpg", "png", "jpe", "jpeg");
        #$this->_video = array("avi", "mp4", "wmv", "mpg", "flv", "mp3", "swf", "m4v", "m4a", "mov", "3gp", "f4v");
        $this->_video = array("mp4", "flv");
        $this->_arr = array('zip');
        switch ($fileType) {
            case 'images' :
                $this->_allExt = $this->_image;
                break;
            case 'all' :
                $this->_allExt = array_merge($this->_image, $this->_video, $this->_arr);
                break;
        }
        $pluginVar = array(
            'path' => array(
                'uploader' => $this->_config ['http'] . 'DGGallery/uploadify/uploadify.swf',
                'script' => $this->_config ['http'] . 'DGGallery/upload.php',
                'cancelImg' => $this->_config ['http'] . 'DGGallery/uploadify/cross-circle-frame.png'
            ),
            'fileExt' => $this->_setFileType($this->_allExt),
            'sizeLimit' => ($this->_config ['maxsize'] * 1024),
            'queueSizeLimit' => (null === $maxFile) ? $this->_config ['uploadifyMaxFile'] : intval($maxFile),
            'post' => array(
                'sessid' => session_id(),
                'area' => $key,
                'id' => $parent_id
            )
        );
        return module_json::getJson($pluginVar);
    }

    /**
     * @param $dat
     * @return string
     */
    private function _setFileType($dat)
    {
        $ext_arr = null;
        $allowed_ext = '';
        if (is_string($dat)) {
            $ext_arr = explode(',', $dat);
        } elseif (is_array($dat)) {
            $ext_arr = $dat;
        }
        $h = count($ext_arr);
        for ($j = 0; $j <= $h; $j++) {
            if ($ext_arr [$j] !== null) {
                $allowed_ext .= '*.' . $ext_arr [$j] . ';';
            }
        }
        return $allowed_ext;
    }

    /**
     * model_upload::MoveFile()
     *
     * @return void
     */
    public function MoveFile()
    {
        if ($this->_count) {
            $count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . '_dg_gallery_file ');
            if ($this->_count <= $count['count']) {
                return '0';
            }
        }
        $this->_image = array("gif", "jpg", "png", "jpe", "jpeg");
        $this->_video = array("mp4", "flv");
        $this->_arr = array('zip');
        $this->_allExt = array_merge($this->_image, $this->_video, $this->_arr);
        $this->_prefix = date('is', time()) . '-';
        $area = strtolower(trim(strip_tags($_POST ['area'])));
        $parent_id = intval(model_request::getRequest('id'));

        if ($area === 'useralbom') {
            $check = $this->_db->super_query('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_albom WHERE id='{$parent_id}' LIMIT 1");
            if (null === $check) {
                die();
            }
            $this->_inserValue['other_dat']['category'] = $check['parent_id'];
            $this->_status = 'albom';
        }
        if ($area === 'category') {
            $check = $this->_db->super_query('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery  WHERE id='{$parent_id}' LIMIT 1");
            if (null === $check) {
                die();
            }
            $this->_inserValue['other_dat']['category'] = $check['parent_id'];
            $this->_status = 'catfile';
        }
        if (!empty($_FILES)) {
            if ($_FILES ['Filedata'] ['name'] == '.htaccess') {
                return;
            }
            $tempFile = $_FILES ['Filedata'] ['tmp_name'];
            $this->_fileName = $this->_prefix . $this->_fixNameFile($_FILES ['Filedata'] ['name']);
            $targetFile = $this->_dir . $this->_fileName;
            $ext = $this->_getFileExt($this->_fileName);
            if (in_array($ext, $this->_allExt)) {
                move_uploaded_file($tempFile, $targetFile);
                if (file_exists($targetFile)) {
                    $this->_fileComplete = $targetFile;
                    //images
                    $img = null;
                    $this->_inserValue['original'] = 1;
                    if (in_array($ext, $this->_image)) {
                        $mode = 0; //( $this->_config['image_handler'] == '') ? 0 : $this->_config['image_handler'];
                        $img = $this->_getHandler($mode);
                        $this->_inserValue ['info'] ['originalWidth'] = $img->getWidth();
                        $this->_inserValue ['info'] ['originalHeight'] = $img->getHeight();
                        //Option => extract color
                        if ($this->_config ['rainbow']) {
                            require_once ROOT_DIR . '/DGGallery/app/model/images/ExtractColors.php';
                            $this->_inserValue ['info'] ['colors'] [] =
                                $img->extractColors($this->_fileComplete, $this->_config ['rainbowColorNum']);
                        }
                        if ($this->_config['FileHash']) { //uin
                            $this->_inserValue['info']['md5_hash'] = md5_file($this->_fileComplete);
                        } else {
                            $this->_inserValue['uid'] = '';
                        }
                        if (is_array($this->_inserValue ['info'] ['colors'][0])) {
                            $this->_inserValue['uid'] = md5(implode('', $this->_inserValue ['info'] ['colors'][0]) . $this->_inserValue['info']['md5_hash']);
                        } else {
                            $this->_inserValue['uid'] = $this->_inserValue['info']['md5_hash'];
                        }
                        $check = $this->_db->super_query('SELECT * FROM ' . DBNAME . '.' . PREFIX . "_dg_gallery_file WHERE hash='{$this->_inserValue['uid']}'");
                        if ($check) {
                            $this->_inserValue['original'] = 0;
                        }
                    }

                    $this->_inserValue ['info'] ['size'] = filesize($this->_fileComplete);
                    switch ($area) {
                        case 'categorycover' :
                            if (in_array($ext, $this->_image)) {
                                $this->_inserValue ['path'] = 'uploads/gallery/' . FOLDER_PREFIX . 'cover/' . $this->_fileName;
                                $watermark = (intval($this->_config ['watermarkCover'])) ? true : false;
                                $img->cteateThumb($this->_config ['maxprop_cover'], $watermark, $this->_config ['resize_mode_cover']);
                                $img->save(true, $this->_dir . 'cover/' . $this->_fileName);
                                $this->_inserValue ['path'] = str_replace(ROOT_DIR, '', $this->_dir . 'cover/' . $this->_fileName);
                                @unlink($this->_fileComplete);
                                $this->_insertDb('folder', $parent_id);
                            }
                            break;
                        case 'albomcover' :
                            break;
                        case 'useralbom' :
                        case 'category':
                            if (in_array($ext, $this->_image)) {
                                $img->checkSize(intval($this->_config ['watermark']) ? true : false); //fix
                                $this->_inserValue ['path'] = '/uploads/gallery/' . FOLDER_PREFIX . '%replace%/' . $this->_fileName;
                                $watermark = (intval($this->_config ['watermarkSlider'])) ? true : false;
                                $img->cteateThumb($this->_config ['maxprop_slider'], $watermark, $this->_config ['resize_mode_thumbs']);
                                $img->save(false, $this->_dir . 'thumbs/' . $this->_fileName);
                                $this->_insertDb('image', $parent_id);
                            }
                            //Video file
                            if (in_array($ext, $this->_video)) {
                                if ($this->_config ['fileFrame']) {
                                    $this->_ffmpegExpotrFrame();
                                }
                                $this->_inserValue['path'] = '/uploads/gallery/' . FOLDER_PREFIX . '%replace%/' . $this->_fileName . '.jpg';

                                $this->_insertDb('video', $parent_id);
                                $this->_db->query('UPDATE ' . PREFIX . "_dg_gallery_file SET parent_id='{$this->_lastInsertId}' WHERE parent_id='0' AND status='videocover'");
                            }
                            //Zip
                            if (in_array($ext, $this->_arr)) {
                                $this->_unZip();
                            }
                            break;
                        case 'videocover':
                            $file_id = model_request::getPost('file_id');
                            if (!$file_id) {
                                unlink($this->_fileComplete);
                                die();
                            }
                            $this->_inserValue['path'] = '/uploads/gallery/' . FOLDER_PREFIX . '%replace%/' . $this->_fileName;
                            $watermark = (intval($this->_config ['watermarkSlider'])) ? true : false;
                            $img->cteateThumb($this->_config ['maxprop_slider'], $watermark, $this->_config ['resize_mode_thumbs']);
                            $img->save(false, $this->_dir . 'thumbs/' . $this->_fileName);
                            $img->checkSize($this->_config ['watermark']);
                            $f = new model_file;
                            $this->_insertDb('image', $file_id);
                            $f->addVideoCover($this->_inserValue['path'], $this->_lastInsertId, $file_id);
                            break;
                    }
                }
                return '1';
            } else {
                return '0';
            }
        } else {
            die();
        }
    }

    /**
     *
     * @param string $name
     * @param array $data
     * @return void
     */
    public function youTubeImages($name, $data)
    {
        $thumb = $this->_dir . $name . '.jpg';
        $other = array();
        $inserValue = array();
        $dat = array();
        if (!$this->_config['youTubeThumbManualLoad']) {
            $data['thumbnailURL'] = ROOT_DIR . '/uploads/gallery/assets/youtube_preview.png';
        }

        if (copy($data['thumbnailURL'], $thumb)) {
            $parent_id = intval(model_request::getRequest('id'));
            $this->_fileComplete = $thumb;
            $img = $this->_getHandler(0);
            $img->youTube = true;

            $img->cteateThumb($this->_config ['maxprop_slider'], true, $this->_config ['resize_mode_thumbs']);
            $img->save(false, $this->_dir . 'thumbs/' . $name . '.jpg');

            $this->_inserValue ['path'] = '/uploads/gallery/' . FOLDER_PREFIX . '%replace%/' . $name . '.jpg';
            $this->_inserValue['title'] = $this->_db->safesql(iconv('utf-8', 'cp1251', $data['title']));
            $this->_inserValue['descr'] = $this->_db->safesql(iconv('utf-8', 'cp1251', $data['description']));
            require_once ROOT_DIR . '/engine/classes/parse.class.php';
            $parse = new ParseFilter(array(), array(), 1, 1);
            $this->_inserValue['descr'] = $parse->process($this->_inserValue['descr']);
            $this->_inserValue['descr'] = str_replace(array('\r', '\n'), array('', '<br />'), $this->_inserValue['descr']);
            $other['viewCount'] = (int)$data['viewCount'];
            $other['length'] = (int)$data['length'];
            $other['rating'] = (int)$data['rating'];
            $other['name'] = $name; //lost
            $inserValue['path'] = '/uploads/gallery/' . FOLDER_PREFIX . '%replace%/' . $name . '.jpg';
            $dat ['date'] = date('Y-m-d H:i:s');


            $this->_inserValue['other_dat'] = serialize($other);
            $this->_insertDb('videotube', $parent_id);
            $id = $this->_lastInsertId;

            $this->_db->query('INSERT INTO ' . PREFIX . "_dg_gallery_file (parent_id,author,author_id,descr, path, other_dat, date,position, status ) VALUES
		('{$id}','{$this->_user['name']}','{$this->_user['user_id']}','','{$inserValue['path']}','" . "','{$dat['date']}','0','videocover' )");
            $insert = $this->_db->insert_id();
            $file = model_gallery::getClass('model_file');
            $parent = $file->getFile($this->_lastInsertId);
            $other = unserialize($parent['other_dat']);
            $other['other_dat']['name'] = $name;
            $other['other_dat']['preview_id'] = $insert;
            $other['other_dat']['video_preview'][$insert] = '/uploads/gallery/' . FOLDER_PREFIX . '%replace%/' . $name . '.jpg';

            $file->updateFile($parent['id'], array(
                'other_dat' => serialize($other)
            ));
        }
    }

    /**
     * @param $name
     * @param $url
     * @return string
     */
    public function videoServiceFile($name, $url)
    {
        if ($this->_count) {
            $count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . DBNAME . '.' . PREFIX . '_dg_gallery_file ');
            if ($count['count'] >= $this->_count) {
                return '0';
            }
        }


        $dat = array();
        if (is_string($url))
            $source = @parse_url($url);
        else
            $source = $url;
        if (empty($source['host'])) {

        }
        $id = model_request::getPost('id');
        $dat ['date'] = date('Y-m-d H:i:s');
        $video_link = false;
        $inserValue = array();
        $count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . PREFIX . "_dg_gallery_file WHERE parent_id='{$id}'");
        $position = ($count['count']) ? $count['count'] + 1 : 1;
        switch ($name) {
            case 'smotri':
                if ($this->_config['allow_smotri_com']) {
                    $a = explode('&', $source['query']);
                    $i = 0;
                    while ($i < count($a)) {
                        $b = explode('=', $a[$i]);
                        if ($b[0] == "id")
                            $video_link = totranslit($b[1], false);
                        $i++;
                    }
                }
                if ($video_link) {
                    $_preview = '/uploads/gallery/assets/smotri.com_preview.png?' . $video_link;
                    $inserValue['path'] = "http://pics.smotri.com/player.swf?file=" . $video_link;
                    $this->_db->query('INSERT INTO ' . PREFIX . "_dg_gallery_file (parent_id,author,author_id,descr, path, other_dat, date,position, status ) VALUES
		('{$id}','{$this->_user['name']}','{$this->_user['user_id']}','','{$_preview}','" . serialize($inserValue) . "','{$dat['date']}','{$position}','smotri.com' )");
                }
                break;
            case 'rutube':
                $a = explode('&', $source['query']);
                $i = 0;

                while ($i < count($a)) {
                    $b = explode('=', $a[$i]);
                    if ($b[0] == "v")
                        $video_link = totranslit($b[1], false);
                    $i++;
                }

                if ($video_link) {
                    $_preview = '/uploads/gallery/assets/rutube_preview.png?' . $video_link;
                    $inserValue['path'] = "http://video.rutube.ru/" . $video_link;
                    $this->_db->query('INSERT INTO ' . PREFIX . "_dg_gallery_file (parent_id,author,author_id,descr, path, other_dat, date,position, status ) VALUES
		('{$id}','{$this->_user['name']}','{$this->_user['user_id']}','','{$_preview}','" . serialize($inserValue) . "','{$dat['date']}','{$position}','rutube' )");
                }
                break;

            case 'gametrailers':
                $video_link = end(explode('/', $source['path']));
                if ($video_link) {
                    $_preview = '/uploads/gallery/assets/gametrailers_preview.png?' . $video_link;
                    $inserValue['path'] = "http://media.mtvnservices.com/mgid:moses:video:gametrailers.com:" . $video_link;
                    $this->_db->query('INSERT INTO ' . PREFIX . "_dg_gallery_file (parent_id,author,author_id,descr, path, other_dat, date,position, status ) VALUES
		('{$id}','{$this->_user['name']}','{$this->_user['user_id']}','','{$_preview}','" . serialize($inserValue) . "','{$dat['date']}','{$position}','gametrailers' )");
                }
                break;
            case 'vimeo':
                $vim_id = end(explode('/', $source['path']));
                $api_answer = file_get_contents('http://vimeo.com/api/v2/video/' . $vim_id . '.php');
                $thumb = $this->_dir . $vim_id . '.jpg';
                $other = array();
                if ($api_answer) {
                    $api_answer = unserialize($api_answer);
                }
                if (is_array($api_answer[0])) {
                    $data = $api_answer[0];
                    $this->_inserValue ['path'] = '/uploads/gallery/' . FOLDER_PREFIX . '%replace%/' . $vim_id . '.jpg';
                    $this->_inserValue['title'] = $this->_db->safesql(iconv('utf-8', 'cp1251', $data['title']));
                    $this->_inserValue['descr'] = $this->_db->safesql(iconv('utf-8', 'cp1251', $data['description']));
                    $this->_inserValue['tags'] = $this->_db->safesql(iconv('utf-8', 'cp1251', $data['tags']));
                    require_once ROOT_DIR . '/engine/classes/parse.class.php';
                    $parse = new ParseFilter(array(), array(), 1, 1);
                    $this->_inserValue['descr'] = $parse->process($this->_inserValue['descr']);
                    $this->_inserValue['descr'] = str_replace(array('\r', '\n'), array('', '<br />'), $this->_inserValue['descr']);

                    $other['name'] = $vim_id; //lost

                    $inserValue['path'] = '/uploads/gallery/' . FOLDER_PREFIX . '%replace%/' . $vim_id . '.jpg';
                    $dat ['date'] = date('Y-m-d H:i:s');


                    $this->_inserValue['other_dat'] = serialize($other);
                    $this->_insertDb('vimeo', $id);
                    $_id = $this->_lastInsertId;
                    $file = model_gallery::getClass('model_file');

                    if (!$this->_config['vimeoThumbManualLoad']) {
                        $data['thumbnail_large'] = ROOT_DIR . '/uploads/gallery/assets/vimeo_preview.png';
                        @copy($data['thumbnail_large'], ROOT_DIR . '/uploads/gallery/' . FOLDER_PREFIX . '/thumbs/' . $vim_id . '.jpg');
                        $other = array();
                        $other['other_dat']['name'] = $vim_id;
                        $other['other_dat']['path'] = 'http://player.vimeo.com/video/' . $vim_id;
                        $file->updateFile($_id, array(
                            'other_dat' => serialize($other)
                        ));
                    } else {
                        if (copy($data['thumbnail_large'], $thumb)) {
                            $this->_fileComplete = $thumb;
                            $img = $this->_getHandler(0);
                            $img->vimeo = true;
                            $img->cteateThumb($this->_config ['maxprop_slider'], true, $this->_config ['resize_mode_thumbs']);
                            $img->save(false, $this->_dir . 'thumbs/' . $vim_id . '.jpg');
                            $this->_db->query('INSERT INTO ' . PREFIX . "_dg_gallery_file (parent_id,author,author_id,descr, path, other_dat, date,position, status ) VALUES
		('{$_id}','{$this->_user['name']}','{$this->_user['user_id']}','','{$inserValue['path']}','" . "','{$dat['date']}','0','videocover' )");
                            $insert = $this->_db->insert_id();

                            $parent = $file->getFile($this->_lastInsertId);
                            $other = unserialize($parent['other_dat']);
                            $other['other_dat']['video_preview'][$insert] = '/uploads/gallery/' . FOLDER_PREFIX . '%replace%/' . $vim_id . '.jpg';
                            $other['other_dat']['preview_id'] = $insert;
                            $other['other_dat']['name'] = $vim_id;

                            $other['other_dat']['path'] = 'http://player.vimeo.com/video/' . $vim_id;

                            $file->updateFile($parent['id'], array(
                                'other_dat' => serialize($other)
                            ));
                        }
                    }
                } else {

                    $this->_insertDb('vimeo', $id);
                }
                break;
            default:
                break;
        }
    }

    /**
     *
     * @param type $mode
     * @return Default_Model_Images_Gd
     */
    private function _getHandler($mode)
    {

        require_once ROOT_DIR . '/DGGallery/app/model/images/Gd.php';
        return new Default_Model_Images_Gd($this->_fileComplete);
        //        if ($mode == 0) {
        //
        //        } elseif ($mode == 1) {
        //            require_once ROOT_DIR . '/DGGallery/app/model/images/Imagick.php';
        //            return new Default_Model_Images_Imagick($this->_fileComplete);
        //        }
    }

    /**
     *
     * @param string $type
     * @param int $parent_id
     * @return viod
     */
    private function _insertDb($type, $parent_id = 0)
    {
        $dat = array();
        $dat ['date'] = date('Y-m-d H:i:s');
        $position = 0;
        $count = array();
        switch ($type) {
            case 'videotube' :
            case 'vimeo':
                if ($type == 'videotube')
                    $dat ['status'] = 'youtube';
                else
                    $dat ['status'] = $type;
                $count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . PREFIX . "_dg_gallery_file WHERE parent_id='{$parent_id}'");
                $position = ($count['count']) ? $count['count'] + 1 : 1;
                $this->_db->query('INSERT INTO ' . PREFIX . "_dg_gallery_file (parent_id,author,title,descr, path, other_dat, date,position, status ) VALUES
		('{$parent_id}','{$this->_user['name']}','{$this->_inserValue['title']}','{$this->_inserValue['descr']}','{$this->_inserValue['path']}','" .
                    $this->_inserValue['other_dat'] . "','{$dat['date']}','{$position}','{$dat['status']}' )");
                $this->_lastInsertId = $this->_db->insert_id();
                $this->_db->query('UPDATE ' . PREFIX . "_dg_gallery_albom SET images=images+1 WHERE id='{$parent_id}' LIMIT 1");

                if (isset($this->_inserValue['tags']) && ($this->_inserValue['tags'] != ''))
                    model_gallery::getClass('model_search')->addKeywordsFile($this->_inserValue['tags']);
                return;
            case 'video':
                $this->_inserValue['other_dat']['file_path'] = '/uploads/gallery/' . FOLDER_PREFIX . $this->_fileName;
                $dat ['status'] = 'video';
                $count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . PREFIX . "_dg_gallery_file WHERE parent_id='{$parent_id}'");
                $position = ($count['count']) ? $count['count'] + 1 : 1;
                $this->_db->query('UPDATE ' . PREFIX . "_dg_gallery_albom SET images=images+1 WHERE id='{$parent_id}' LIMIT 1");
                break;
            case 'videocover':
                $dat ['status'] = 'videocover';
                break;
            case 'image' :
                $dat ['status'] = ($this->_status) ? $this->_status : 'albom';
                $count = $this->_db->super_query('SELECT COUNT(*) AS count FROM ' . PREFIX . "_dg_gallery_file WHERE parent_id='{$parent_id}'");
                $position = ($count['count']) ? $count['count'] + 1 : 1;
                $this->_db->query('UPDATE ' . PREFIX . "_dg_gallery_albom SET images=images+1 WHERE id='{$parent_id}' LIMIT 1");
                break;
            case 'folder' :
                $dat ['status'] = 'folder_cover';
                //del old
                $this->_deleteOld('folder_cover', $parent_id);
                break;
            case 'albom' :
                $dat ['status'] = 'albom_cover';
                //del old
                $this->_deleteOld('albom_cover');
                break;
            case 'zip' :
                $dat ['status'] = 'zip_attachment';
                break;
        }
        $this->_db->query('INSERT INTO ' . PREFIX . "_dg_gallery_file (parent_id,author,author_id,descr, path, other_dat, date,position, status,hash,original ) VALUES
		('{$parent_id}','{$this->_user['name']}','{$this->_user['user_id']}','','{$this->_inserValue['path']}','" .
            serialize($this->_inserValue) . "','{$dat['date']}','{$position}','{$dat['status']}','{$this->_inserValue['uid']}','{$this->_inserValue['original']}')");
        $this->_lastInsertId = $this->_db->insert_id();

        if ($this->_config['countFile']) {
            $this->_db->query('UPDATE ' . PREFIX . "_dg_gallery_user SET files=files+1 WHERE user_id='{$this->_user['user_id']}' LIMIT 1");
        }

        //        if (isset($this->_inserValue ['info'] ['colors'][0]) && is_array($this->_inserValue ['info'] ['colors'][0])) {
        //            $color = array();
        //            $_color = array_keys($this->_inserValue ['info'] ['colors'][0]);
        //            foreach ($_color as $k) {
        //                $color[] = "('{$this->_lastInsertId}','" . $k . "')";
        //            }
        //            $this->_db->query('INSERT INTO ' . PREFIX . "_dg_gallery_color (parent_id,rgba) VALUES " . implode(',', $color));
        //        }
    }

    /**
     * @param $status
     * @param int $id
     */
    private function _deleteOld($status, $id = 0)
    {
        $row = $this->_db->super_query('SELECT id,path FROM ' . PREFIX . "_dg_gallery_file  WHERE parent_id='{$id}' AND status='{$status}' LIMIT 1");
        if ($row ['id']) {
            @unlink(ROOT_DIR . DIRECTORY_SEPARATOR . $row ['path']);
            $this->_db->query('DELETE FROM ' . PREFIX . "_dg_gallery_file WHERE id='{$row['id']}' LIMIT 1");
        }
    }

    /**
     * model_upload::_checkDir()
     *
     * @param mixed $check_dir
     * @return void
     */
    protected function _checkDir($check_dir)
    {
        if (!is_dir($check_dir)) {
            @mkdir($check_dir, 0777);
            @chmod($check_dir,0777);
        }
        $subDir = array('original', 'cover', 'thumbs');
        foreach ($subDir as $value) {
            if (!is_dir($check_dir . '/' . $value)) {
                @mkdir($check_dir . '/' . $value, 0777);
                @chmod($check_dir . '/' . $value, 0777);
            }
        }
    }

    /**
     * model_upload::_getFileExt()
     *
     * @param mixed $path
     * @return string
     */
    protected function _getFileExt($path)
    {
        return strtolower(end(explode('.', end(explode('/', $path)))));
    }

    /**
     * model_upload::_fixNameFile()
     *
     * @param string $str
     * @return string
     */
    protected function _fixNameFile($str)
    {
        $pattern = '/[а-яА-Я]+/';
        $s = null;
        preg_match($pattern, $str, $s);
        if ($s) {
            //FIX: транслитерация имени файла с русским названием
            return totranslit(iconv("UTF-8", 'windows-1251', $str));
        } else {
            return $str;
        }
    }

    /**
     * model_upload::_ffmpegExpotrFrame()
     *
     * @return viod
     */
    protected function _ffmpegExpotrFrame()
    {
        if (!extension_loaded('ffmpeg'))
            return;
        $frames = 5; //($this->_config ['FrameCount'] and $this->_config ['FrameCount'] <= 10) ? $this->_config ['FrameCount'] :
        //$frames += 1;
        $this->movie = new ffmpeg_movie($this->_fileComplete, false);
        $count = $this->movie->getFrameCount();
        $frame = ceil($count / $frames);

        if ($frame) {
            for ($i = 1; $i < $frames; $i++) {
                $exp = ceil($frame * $i);
                $image = $this->movie->getFrame($exp);
                if (is_object($image)) {
                    $show_img = $image->toGDImage();
                    $fileName = ($i == 1) ? $this->_fileName . '.jpg' : $i . '_' . $this->_fileName . '.jpg';

                    imagejpeg($show_img, ROOT_DIR . '/uploads/gallery/' . FOLDER_PREFIX . $fileName, 100);
                    imagedestroy($show_img);
                    $this->_fileComplete = ROOT_DIR . '/uploads/gallery/' . FOLDER_PREFIX . $fileName;
                    $img = $this->_getHandler(0);
                    $img->cteateThumb('480x360', false, $this->_config ['resize_mode_thumbs']);
                    $img->save(true, ROOT_DIR . '/uploads/gallery/' . FOLDER_PREFIX . 'thumbs/' . $fileName);
                    $img->clear();
                    $this->_inserValue['path'] = '/uploads/gallery/' . FOLDER_PREFIX . '%replace%/' . $fileName;
                    $this->_insertDb('videocover', 0);
                    if ($i == 1) {
                        $this->_inserValue['other_dat']['preview_id'] = $this->_lastInsertId;
                    }

                    $this->_inserValue['other_dat']['video_preview'][$this->_lastInsertId] = '/uploads/gallery/' . FOLDER_PREFIX . '%replace%/' . $fileName;
                    //
                }
            }
        }
    }

    /**
     * model_upload::_unZip()
     *
     * @return void
     */
    protected function _unZip()
    {
        if (function_exists('set_time_limit'))
            @set_time_limit(600); //5 min
        if (file_exists(ROOT_DIR . '/DGGallery/app/lib/pclzip.lib.php')) {
            include ROOT_DIR . '/DGGallery/app/lib/pclzip.lib.php';
        } else {
            unlink($this->_fileComplete);
            die();
        }

        $this->_zip = new PclZip($this->_fileComplete);
        /**
         * _postExtract()
         *
         * */
        global $config, $extractFiles;
        $config = $this->_config;
        $_status = $this->_status;
        $_user = $this->_user;

        /**
         * @param $p_event
         * @param $p_header
         * @return int
         */
        function postExtract($p_event, &$p_header)
        {
            //TODO: переименовать файлы после извлечения но до обработки
            global $config, $_user, $_status, $member_id;
            //emty global config gallery
            $parent_id = model_request::getRequest('id');
            $_db = new module_db();

            if (null === $config) {
                // return ?
                return;
            }
            require_once ROOT_DIR . '/DGGallery/app/model/images/Interface.php';
            require_once ROOT_DIR . '/DGGallery/app/model/images/Gd.php';
            switch ($p_header ['status']) {
                case 'ok' :
                    $_file = explode('/', $p_header ['filename']);
                    $file_name = array_pop($_file);
                    $newname = implode('/', $_file) . '/' . date('is', time()) . '-' . $file_name;
                    rename($p_header ['filename'], $newname);
                    $file = str_replace(FOLDER_PREFIX, FOLDER_PREFIX . 'thumbs' . DIRECTORY_SEPARATOR, $newname);
                    $extractFiles [$p_header ['index']]['path'] = str_replace(FOLDER_PREFIX, FOLDER_PREFIX . '%replace%/', $newname);
                    $extractFiles [$p_header ['index']]['path'] = str_replace(ROOT_DIR, '', $extractFiles [$p_header ['index']]['path']);
                    $img = new Default_Model_Images_Gd($newname);
                    //Option => extract color
                    if ($config ['rainbow']) {
                        require_once ROOT_DIR . '/DGGallery/app/model/images/ExtractColors.php';
                        $extractFiles [$p_header ['index']] ['info'] ['colors'] [] =
                            $img->extractColors($newname, $config['rainbowColorNum']);
                    }
                    $extractFiles [$p_header ['index']] ['info'] ['size'] = filesize($newname);
                    $extractFiles [$p_header ['index']]['info'] ['originalWidth'] = $img->getWidth();
                    $extractFiles [$p_header ['index']]['info'] ['originalHeight'] = $img->getHeight();
                    $watermark = (intval($config['watermarkSlider'])) ? true : false;
                    $img->cteateThumb($config ['maxprop_slider'], $watermark, $config['resize_mode_thumbs']);
                    $img->save(false, $file);
                    $img->checkSize($config ['watermark']);
                    $img->clear();

                    $count = $_db->super_query('SELECT COUNT(*) AS count FROM ' . PREFIX . "_dg_gallery_file WHERE parent_id='{$parent_id}'");
                    $position = ($count['count']) ? $count['count'] + 1 : 1;
                    $status = ($config['mode'] == 1) ? 'albom' : 'catfile';
                    $_db->query('INSERT INTO ' . PREFIX . "_dg_gallery_file (parent_id,author,author_id,descr, path, other_dat, date,position, status ) VALUES
		('{$parent_id}','{$member_id['name']}','{$member_id['user_id']}','','{$extractFiles [$p_header ['index']]['path']}','" . serialize($extractFiles [$p_header ['index']]) . "','" . date('Y-m-d H:i:s') .
                        "','{$position}','{$status}' )");
                    $_lastInsertId = $_db->insert_id();
                    if ($config['countFile']) {
                        $_db->query('UPDATE ' . DBNAME . '.' . PREFIX . "_dg_gallery_user SET files=files+1 WHERE user_id='{$member_id['user_id']}' LIMIT 1");
                    }

//                    if (isset($extractFiles [$p_header ['index']]['info'] ['colors'][0]) && is_array($extractFiles [$p_header ['index']]['info'] ['colors'][0])) {
//                        $color = array();
//                        $_color = array_keys($extractFiles [$p_header ['index']]['info'] ['colors'][0]);
//                        foreach ($_color as $k) {
//                            $color[] = "('{$_lastInsertId}','" . $k . "')";
//                        }
//                         $_db->query('INSERT INTO ' . PREFIX . "_dg_gallery_color (parent_id,rgba) VALUES " . implode(',', $color));
//                    }
                    if ($_status == 'albom')
                        $_db->query('UPDATE ' . PREFIX . "_dg_gallery_albom SET images=images+1 WHERE id='{$parent_id}' LIMIT 1");
                    break;
                case 'skipped' :
                    //---|
                    break;
                case 'already_a_directory' :
                    //---|
                    break;
            }
            return 1;
        }

        /**
         * @param $p_event
         * @param $p_header
         * @return bool|string
         */
        function extFilter($p_event, &$p_header)
        {
            $allowed_extensions = array("gif", "jpg", "png", "jpe", "jpeg");
            $info = pathinfo($p_header ['filename']);
            return (in_array($info ['extension'], $allowed_extensions)) ? '1' : false;
        }

        $this->_zip->extract(
            PCLZIP_OPT_PATH, $this->_dir, PCLZIP_OPT_REMOVE_ALL_PATH, PCLZIP_CB_PRE_EXTRACT, "extFilter", PCLZIP_CB_POST_EXTRACT, 'postExtract'
        );
        @unlink($this->_fileComplete);
    }

    /**
     * @param $s
     */
    protected function _log($s)
    {
        $h = fopen(ROOT_DIR . '/log.txt', "a+");
        fwrite($h, $s);
        fclose($h);
    }

}
